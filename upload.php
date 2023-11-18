<?php

$csvFile = 'data.csv';

function randomSleep() {
    $min = 100;  // 0.2秒
    $max = 500;  // 1.3秒
    $randomTime = rand($min, $max) * 1000;  // 從微秒轉為秒
    usleep($randomTime);
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

    // 寫入資料到該行
    $lineData = [
        $data['name'],
        $data['phone'],
        $data['group'],  // 添加 group 資訊
        $data['ldapAccount']
    ];

    $fileContent[$firstEmptyRow] = implode(',', $lineData) . "\n";
    file_put_contents($csvFile, implode('', $fileContent));

    // Send data to the API
    $apiUrl = 'https://community.tzuchi.org.tw/api/user';
    // 省略 API 呼叫的其餘部分

    return ['result' => 'success', 'row' => $firstEmptyRow + 1];
}

function checkDuplicate($name, $phone) {
    // 省略檢查重複的函數內容
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $group = $_POST['group'];  // 獲取 group 資訊
    $ldapAccount = "tc" . $phone;
    
    if (!checkDuplicate($name, $phone)) {
        $response = submitToCsv([
            'name' => $name,
            'phone' => $phone,
            'group' => $group,  // 將 group 資訊傳遞給函數
            'ldapAccount' => $ldapAccount
        ]);

        // 省略剩餘部分
    }
    // 省略其他部分
}
?>
