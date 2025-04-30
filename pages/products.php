<?php

require "../config/db.php";

$result= $conn->query("SELECT products.*, prices.price, stock.quantity FROM products LEFT JOIN prices ON products.id=prices.product_id LEFT JOIN stock ON products.id=stock.product_id;");


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление остатками</title>
    <link rel='stylesheet' href='../css/style.css'/>
</head>
<body>
 <?php  include "../includes/header.php"; ?>
<main class='container-fluid'>
<table class='products'>
    <tr>
    <th>артикул</th>
    <th>название</th>
    <th>цвет</th>
    <th>вес</th>
    <th>размер</th>
    <th>категория товара</th>
    <th>в наличие шт.</th>
    <th>заказано у поставщика шт.</th>
    <th>цена 1</th>
    <th>цена 2</th>
    <th>цена 3</th>
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
    <td></td>
    </tr>
    
</table>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>