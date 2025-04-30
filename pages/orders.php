<?php

require "../config/db.php";


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказы клиентов</title>
    <link rel='stylesheet' href='../css/style.css'/>
</head>
<body>
 <?php  include "../includes/header.php"; ?>
<main class='container-fluid'>
  <table>
    <tr>
     <th>Номер заказа</th>
     <th>Дата заказа</th>
     <th>Дата доставки</th>
     <th>Имя клиента</th>
     <th>Номер телефона клиента</th>
     <th>Общая стоимость</th>
     <th>Позиции в заказе</th>
     <th>Статус заказа</th>
     <th>Оплата заказа</th>
     <th>Адрес доставки</th>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
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