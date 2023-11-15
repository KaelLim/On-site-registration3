<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $phoneToDelete = $_GET['phone'];

    // 讀取CSV文件
    $fileContent = file('data.csv');
    $userIdToDelete = null;

    // 過濾並查找對應的 user_id
    $newContent = array_filter($fileContent, function ($line) use ($phoneToDelete, &$userIdToDelete) {
        $data = str_getcsv($line);
        if (trim($data[1]) === trim($phoneToDelete)) {
            $userIdToDelete = trim($data[3]); // 假設 user_id 存放在第四個欄位
            return false;
        }
        return true;
    });

    // 更新CSV文件
    file_put_contents('data.csv', implode('', $newContent));

    // 如果找到相應的 user_id，則發送 DELETE 請求
    if ($userIdToDelete) {
        $apiUrl = "https://community.tzuchi.org.tw/api/user/delete/$userIdToDelete";

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['accept: */*']);

        $response = curl_exec($ch);
        curl_close($ch);
    }
}

header('Location: forms.php'); // 重定向回表格頁面
exit;
?>
