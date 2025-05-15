<?php

require "../config/db.php";

if($_SERVER['REQUEST_METHOD']== 'POST') {
    $name= $_POST['name'];
    $description= $_POST['description'];

    $sql= ("INSERT INTO categories('id', 'name', 'description') VALUES('$id', '$name', '$description')");
}


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление категорий</title>
    <link rel='stylesheet' href='../css/style.css'/>
</head>
<body>
<?php  include "../includes/header.php"; ?>
<main class='container-fluid'>
    <form action='#' method='post'>
        <div>
            <input type='text' name='name' placeholder='Название категории'/>
        </div>
        <div>
            <textarea name='description'>Описание категории</textarea>
        </div>
        <div>
            <input type='submit' value='Добавить категорию'/>
        </div>
    </form>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>