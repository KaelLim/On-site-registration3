<?php

$phone = $_POST['phone'];
$isDuplicate = false;
$originalName = "";

// 打開CSV檔案
if (($handle = fopen("data.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // 檢查是否有重複的資料
        if ($data[1] == $phone) {
            $isDuplicate = true;
            $originalName = $data[0]; // 保存原始姓名
            break;
        }
    }
    fclose($handle);
}

echo json_encode(['isDuplicate' => $isDuplicate, 'originalName' => $originalName]);

?>
