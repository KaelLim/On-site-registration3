<!DOCTYPE html>
<html>
<head>
    <title>資料管理</title>
    <!-- 加入CSS樣式表和JavaScript -->
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
    <table border="1">
        <thead>
            <tr>
                <th>姓名</th>
                <th>手機號碼</th>
                <th>LDAP 帳號</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (($handle = fopen("data.csv", "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($data[0]) . "</td>";
                    echo "<td>" . htmlspecialchars($data[1]) . "</td>";
                    echo "<td>" . htmlspecialchars($data[2]) . "</td>";
                    echo "<td><a href='edit.php?line=" . urlencode(implode(",", $data)) . "'>編輯</a> | <a href='delete.php?phone=" . urlencode($data[1]) . "'>刪除</a></td>";
                    echo "</tr>";
                }
                fclose($handle);
            }
            ?>
        </tbody>
    </table>
</body>
</html>
