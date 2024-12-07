<?php
$directory = 'files/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['filename'])) {
    $filename = $_POST['filename'];
    $filePath = $directory . $filename;

    if (file_exists($filePath)) {
        $jsonData = json_decode(file_get_contents($filePath), true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $newRecord = array(
                'user_id' => $_POST['user_id'],
                'email' => $_POST['email'],
                'name' => $_POST['name'],
                'given_name' => $_POST['given_name'],
                'family_name' => $_POST['family_name'],
                'nickname' => $_POST['nickname'],
                'last_ip' => $_POST['last_ip'],
                'logins_count' => (int)$_POST['logins_count'],
                'email_verified' => filter_var($_POST['email_verified'], FILTER_VALIDATE_BOOLEAN)
            );

            $jsonData[] = $newRecord;

            $jsonString = "[\n";
            foreach ($jsonData as $record) {
                $jsonString .= "    {\n";
                foreach ($record as $key => $value) {
                    $value = is_bool($value) ? ($value ? 'true' : 'false') : '"' . addslashes($value) . '"';
                    $jsonString .= "        \"$key\": $value,\n";
                }
                $jsonString = rtrim($jsonString, ",\n") . "\n    },\n";
            }
            $jsonString = rtrim($jsonString, ",\n") . "\n]";

            if (file_put_contents($filePath, $jsonString)) {
                $message = "Новий запис успішно додано.";
            } else {
                $message = "Не вдалося оновити файл.";
            }
        } else {
            $message = "Помилка зчитування JSON: " . json_last_error_msg();
        }
    } else {
        $message = "Файл не знайдений.";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додавання нового запису</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>Додавання нового запису</h1>

        <form method="POST">
            <label for="filename">Виберіть файл для оновлення:</label>
            <select name="filename" id="filename" required>
                <?php
                $jsonFiles = glob($directory . '*.json');
                if ($jsonFiles) {
                    foreach ($jsonFiles as $file) {
                        $filename = basename($file);
                        echo "<option value='$filename'>$filename</option>";
                    }
                } else {
                    echo "<option disabled>Немає доступних файлів</option>";
                }
                ?>
            </select><br><br>

            <label for="user_id">ID користувача:</label><br>
            <input type="text" name="user_id" id="user_id" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" name="email" id="email" required><br><br>

            <label for="name">Ім'я користувача:</label><br>
            <input type="text" name="name" id="name" required><br><br>

            <label for="given_name">Ім'я:</label><br>
            <input type="text" name="given_name" id="given_name" required><br><br>

            <label for="family_name">Прізвище:</label><br>
            <input type="text" name="family_name" id="family_name" required><br><br>

            <label for="nickname">Псевдонім:</label><br>
            <input type="text" name="nickname" id="nickname" required><br><br>

            <label for="last_ip">Останній IP:</label><br>
            <input type="text" name="last_ip" id="last_ip" required><br><br>

            <label for="logins_count">Кількість входів:</label><br>
            <input type="number" name="logins_count" id="logins_count" required><br><br>

            <label for="email_verified">Email підтверджено:</label><br>
            <select name="email_verified" id="email_verified" required>
                <option value="true">Так</option>
                <option value="false">Ні</option>
            </select><br><br>

            <button type="submit">Додати запис</button>
        </form>

        <?php
        if (isset($message)) {
            echo "<p>$message</p>";
        }
        ?>

        <a href="index.php">Повернутися на головну</a>
    </div>
</body>
</html>
