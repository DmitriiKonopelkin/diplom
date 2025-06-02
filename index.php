<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "config/db.php";
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Пожалуйста, заполните все поля.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        if (!$stmt) {
            die("Ошибка запроса: " . $conn->error);
        }
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            $error = 'Пользователь не найден.';
        } elseif (!password_verify($password, $user['password'])) {
            $error = 'Неверный пароль.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: pages/products.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Вход в систему</title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f4f4;
        }
        .login-form {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .login-form h1 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 24px;
            color: #2c3e50;
        }
        .login-form input[type='text'],
        .login-form input[type='password'] {
            font-size: 16px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }
        .login-form .login-btn {
            background-color: #2c3e50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-form .login-btn:hover {
            background-color: #34495e;
        }
        .login-form p.error {
            text-align: center;
            margin: 0;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
<main class='container-fluid'>
    <div class="login-form">
        <h1>Вход в систему</h1>
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="index.php" method="post">
            <div class="form-group">
                <input type="text" name="username" placeholder="Имя пользователя" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Пароль" required />
            </div>
            <button type="submit" name="login" class="login-btn">Войти</button>
        </form>
    </div>
</main>
</body>
</html>
