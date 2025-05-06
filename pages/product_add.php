<?php

require "../config/db.php";

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление товара</title>
    <link rel='stylesheet' href='../css/style.css'/>
</head>
<body>
<?php  include "../includes/header.php"; ?>
<main class='container-fluid'>
  <form action='#' method='post'>
    <div>
        <input type='text' name='name' placeholder='Название товара'/>
    </div>
    <div>
       <textarea name='description'>Описание товара</textarea>
    </div>
    <div>
        <input type='number' name='weight' placeholder='Вес товара'/>
    </div>
    <div>
        <input type='text' name='dimensions' placeholder='Размер'/>
    </div>
    <div>
        <input type='text' name='color' placeholder='Цвет'/>
    </div>
    <div>
        <label>Является ли товар скоропортиющимся</label>
        <input type='checkbox' name='perishable'/>
    </div>
     <div>
        <label>Является ли товар хрупким</label>
        <input type='checkbox' name='fragile'/>
     </div>
     <div>
        <select name='categories'>
            <option>Горный велосипед</option>
            <option>Городской велосипед</option>
        </select>
     </div>
    <div>
        <input type='submit' value='Добавить товар'/>
    </div>
  </form>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>