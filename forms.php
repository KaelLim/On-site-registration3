<!DOCTYPE html>
<html>
<head>
    <title>資料管理</title>
    
    <script>
        function confirmDelete(phone) {
            return confirm('您確定要刪除手機號碼為 ' + phone + ' 的資料嗎？');
        }
    </script>

</head>
<body>
    <h1>資料管理</h1>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4caf50;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        a {
            text-decoration: none;
            color: #2196f3;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>

    <?php
    $counts = ['宜蘭' => 0, '花蓮' => 0, '臺東' => 0];
    if (($handle = fopen("data.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $group = $data[3] ?? ''; // 假設地區資訊是在第四個欄位
            if(isset($counts[$group])) {
                $counts[$group]++;
            }
        }
        fclose($handle);
    }
    ?>

    <!-- 地區人數統計顯示在表格上方 -->
    <div>
        <strong>地區人數統計：</strong>
        宜蘭: <?php echo $counts['宜蘭']; ?>,
        花蓮: <?php echo $counts['花蓮']; ?>,
        臺東: <?php echo $counts['臺東']; ?>
    </div>

    <table border="1">
        <thead>
            <tr>
                <th>序號</th>
                <th>姓名</th>
                <th>手機號碼</th>
                <th>合心</th>
                <th>LDAP 帳號</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $counts = ['宜蘭' => 0, '花蓮' => 0, '臺東' => 0];
            $rowNumber = 1;
            if (($handle = fopen("data.csv", "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $group = $data[2] ?? ''; // 假設第三個欄位是 group
                    $counts[$group] = isset($counts[$group]) ? $counts[$group] + 1 : 1;

                    echo "<tr>";
                    echo "<td>" . $rowNumber++ . "</td>";
                    echo "<td>" . htmlspecialchars($data[0]) . "</td>";
                    echo "<td>" . htmlspecialchars($data[1]) . "</td>";
                    echo "<td>" . htmlspecialchars($data[2]) . "</td>";
                    echo "<td>" . htmlspecialchars($data[3]) . "</td>";
                    echo "<td><a href='edit.php?line=" . urlencode(implode(",", $data)) . "'>編輯</a> | <a href='delete.php?phone=" . urlencode($data[1]) . "' onclick='return confirmDelete(\"" . $data[1] . "\")'>刪除</a></td>";
                    echo "</tr>";
                }
                fclose($handle);
            }
            ?>
        </tbody>
    </table>
</body>
</html>
