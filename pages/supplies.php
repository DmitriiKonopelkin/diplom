<?php

require "../config/db.php";


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказы у поставщиков</title>
    <link rel='stylesheet' href='../css/style.css'/>
</head>
<body>
 <?php  include "../includes/header.php"; ?>
<main class='container-fluid'>
 <table>
  <tr>
    <th>Дата</th>
    <th>Номер заказа</th>
    <th>Поставщик</th>
    <th>Артикул продукта</th>
    <th>Название продукта</th>
    <th>Количество</th>
    <th>Цена за ед.</th>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
 </table>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>