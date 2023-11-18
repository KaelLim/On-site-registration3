<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 處理表單提交
    list($originalName, $originalPhone, $originalLdapAccount, $userId) = explode(',', $_POST['originalLine']);
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $ldapAccount = $_POST['ldapAccount'];

    $newLine = "$name,$phone,$ldapAccount,$userId";

    // 更新API資料
    $apiUrl = 'https://community.tzuchi.org.tw/api/user';
    $apiData = [
        "user_id" => $userId,
        "ldap_id" => $ldapAccount,
        "pass" => $ldapAccount,
        "name" => $name,
        "email" => $ldapAccount . "@gmail.com",
        "roles" => ["lv2_admin", "lv3_admin"]
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: */*',
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiData));

    $response = curl_exec($ch);
    curl_close($ch);

    // 讀取並更新CSV文件
    $fileContent = file('data.csv');
    foreach ($fileContent as $key => $line) {
        if (strpos($line, $originalPhone) !== false) {
            $fileContent[$key] = $newLine . "\n";
            break;
        }
    }

    file_put_contents('data.csv', implode('', $fileContent));
    header('Location: forms.php'); // 重定向回表格頁面
    exit;
}

// 從查詢參數獲取行數據
$line = isset($_GET['line']) ? explode(',', $_GET['line']) : null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>編輯資料</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        label {
            font-size: 16px;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($line): ?>
            <form action="edit.php" method="post">
                <input type="hidden" name="originalLine" value="<?= implode(',', $line) ?>">
                <label>姓名: <input type="text" name="name" value="<?= htmlspecialchars($line[0]) ?>"></label><br>
                <label>手機號碼: <input type="text" name="phone" value="<?= htmlspecialchars($line[1]) ?>"></label><br>
                <label>LDAP 帳號: <input type="text" name="ldapAccount" value="<?= htmlspecialchars($line[3]) ?>"></label><br>
                <input type="submit" value="更新">
            </form>
        <?php else: ?>
            <p>無效的請求。</p>
        <?php endif; ?>
    </div>
</body>
</html>
