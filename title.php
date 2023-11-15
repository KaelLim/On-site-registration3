<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pageTitle = $_POST['pageTitle'];
    $lineGroup = $_POST['lineGroup'];

    $settings = [
        'pageTitle' => $pageTitle,
        'lineGroup' => $lineGroup
    ];

    file_put_contents('settings.json', json_encode($settings));
}

$settings = json_decode(file_get_contents('settings.json'), true);
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Page Settings</title>
    <!-- Google Fonts and Material Design Icons -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: auto;
        }

        h2 {
            color: #333;
            font-weight: 400;
            text-align: center;
        }

        label {
            color: #333;
            font-weight: 500;
            display: block;
            margin-bottom: 5px;
        }

        input[type=text], input[type=submit] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type=submit] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        input[type=submit]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <form method="post">
        <label for="pageTitle">Page Title:</label><br>
        <input type="text" id="pageTitle" name="pageTitle" value="<?php echo htmlspecialchars($settings['pageTitle']); ?>"><br>
        <label for="lineGroup">Line Group:</label><br>
        <input type="text" id="lineGroup" name="lineGroup" value="<?php echo htmlspecialchars($settings['lineGroup']); ?>"><br>
        <input type="submit" value="Save Changes">
    </form>
</body>
</html>
