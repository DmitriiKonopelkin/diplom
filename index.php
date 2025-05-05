<?php

require "config/db.php";

session_start();

if($_SERVER['REQUEST_METHOD']= 'POST' && isset($_POST['login'])) {
    $username= $_POST['username'];
    $password= $_POST['password'];
    
    if(!empty($username)&& !empty($password)) {
        $stmt=$mysqli->prepare("SELECT id, username, password FROM users WHERE username=?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result= $stmt->get_result();
        $user= $result->fetch_assoc();

        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']= $user['id'];
            $_SESSION['username']= $user['username'];
            header('Location:pages/products.php');
        } exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <link rel='stylesheet' href='css/style.css'/>
</head>
<body>
 <?php  include "includes/header.php"; ?>
<main class='container-fluid'>
    <div class="login-form">
        <h1>Вход в систему</h1>
    <form action='#' method='post'>
    <div class='form-group'>
  <input type='text' name='username' placeholder='Имя пользователя' required/>
    </div>
    <div class='form-group'>
    <input type='password' name='password' placeholder='Пароль' required/>
    </div>
    <button type='submit'class='login-btn'>Войти</button>
</form>
    </div>
</main>
<?php include "includes/footer.php"; ?>
</body>
</html>