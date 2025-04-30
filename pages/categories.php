<?php

require "../config/db.php";


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Категории товаров</title>
    <link rel='stylesheet' href='../css/style.css'/>
    
</head>
<body>
 <?php  include "../includes/header.php"; ?>
<main class='container-fluid'>
 <table>
    <tr>
        <th>Артикул</th>
        <th>Название</th>
        <th>Описание</th>
    </tr>
    <tr>
        <td>1</td>
        <td>Горный велосипед</td>
        <td>Горный велосипед</td>
    </tr>
    <tr>
        <td>2</td>
        <td>Городской велосипед</td>
        <td>Городской велосипед</td>
    </tr>
 </table>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>