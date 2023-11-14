<?php

$name = $_POST['name'];
$phone = $_POST['phone'];
$isDuplicate = false;

// 打開CSV檔案
if (($handle = fopen("data.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // 檢查是否有重複的資料
        if ($data[0] == $name && $data[1] == $phone) {
            $isDuplicate = true;
            break;
        }
    }
    fclose($handle);
}

echo json_encode(['isDuplicate' => $isDuplicate]);

?>
