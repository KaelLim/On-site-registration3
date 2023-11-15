<?php

$csvFile = 'data.csv';
$importCsvFile = 'import.csv';

function randomSleep() {
    $min = 200;  // 0.2秒
    $max = 1300;  // 1.3秒
    $randomTime = rand($min, $max) * 1000;  // 從微秒轉為秒
    usleep($randomTime);
}

function writeToImportCsv($data) {
    global $importCsvFile;

    $importData = [
        $data['name'],
        "tc" . $data['phone'] . "@gmail.com",
        "資深編輯",
        $data['ldapAccount'],
        $data['ldapAccount']
    ];

    $fp = fopen($importCsvFile, 'a');  // a代表追加模式
    fputcsv($fp, $importData);
    fclose($fp);
}

function submitToCsv($data) {
    global $csvFile;
    randomSleep();

    // 尋找第一個空的行
    $fileContent = file($csvFile);
    $firstEmptyRow = count($fileContent);

    foreach ($fileContent as $index => $line) {
        list($name, ) = str_getcsv($line);
        if (trim($name) === "") {
            $firstEmptyRow = $index;
            break;
        }
    }

    // 寫入資料到該行，暫時不包括 user_id
    $lineData = [
        $data['name'],
        $data['phone'],
        $data['ldapAccount']
    ];

    // 將現有數據寫入 CSV
    $fileContent[$firstEmptyRow] = implode(',', $lineData) . "\n";
    file_put_contents($csvFile, implode('', $fileContent));

    // Send data to the API
    $apiUrl = 'https://community.tzuchi.org.tw/api/user';
    $postData = [
        "account" => "tc" . $data['phone'],
        "ldap_id" => "tc" . $data['phone'],
        "pass" => "tc" . $data['phone'],
        "name" => $data['name'],
        "email" => "tc" . $data['phone'] . "@gmail.com",
        "roles" => ["lv2_admin"]
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: */*',
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        // Handle error
        echo 'Curl error: ' . curl_error($ch);
    } else {
        $responseData = json_decode($response, true);
        if (isset($responseData['user_id'])) {
            // 更新 CSV 文件以包括 user_id
            $lineData[] = $responseData['user_id'];
            $fileContent[$firstEmptyRow] = implode(',', $lineData) . "\n";
            file_put_contents($csvFile, implode('', $fileContent));

            writeToImportCsv($data);

            curl_close($ch);
            return ['result' => 'success', 'row' => $firstEmptyRow + 1, 'user_id' => $responseData['user_id']];
        }
    }

    curl_close($ch);
    return ['result' => 'failure'];
}



function checkDuplicate($name, $phone) {
    global $csvFile;

    $fileContent = file($csvFile);

    foreach ($fileContent as $line) {
        list($storedName, $storedPhone) = str_getcsv($line);
        if ($storedName == $name && $storedPhone == $phone) {
            return true;  // 找到重複的資料
        }
    }

    return false;  // 沒有重複的資料
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $ldapAccount = "tc" . $phone;
    
    if (!checkDuplicate($name, $phone)) {
        $response = submitToCsv([
            'name' => $name,
            'phone' => $phone,
            'ldapAccount' => $ldapAccount
        ]);

        if ($response['result'] === 'success') {
            // 這裡可以加入一些代碼，例如重定向到成功頁面或其他操作。
            header("Location: index.php?success=1&row={$response['row']}");
            exit;
        } else {
            // 處理錯誤情況
        }
    } else {
        // 處理重複資料的情況
        header("Location: index.php?duplicate=1");
        echo "Name: " . $_POST['name'] . "<br>";
        echo "Phone: " . $_POST['phone'] . "<br>";
        exit;
    }
}
?>
