
<?php

require "../config/db.php";

if($_SERVER['REQUEST_METHOD']== 'POST') {
    $product_id= $_POST['product_id'];
    $quantity= $_POST['quantity'];
    $price_rer_unit= $_POST['price_rer_unit'];
}

$sql= ("INSERT INTO supply_add('id', 'supplier_id', 'product_id', 'quantity', 'price_rer_unit') 
VALUES('$id', '$supplier_id', '$product_id', '$quantity', '$price_rer_unit')");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказы поставщика</title>
    <link rel='stylesheet' href='../css/style.css'/>
    <style>
        .supply_add input[type='number'] {
            font-size:17.8px;
            margin-bottom:20px;
        }

        .supply_add input[type='submit'] {
            font-size:17.8px;
            padding:15px 10px;
            border:1px solid #ebb217;
            margin-top:20px;
        }
    </style>
</head>
<body>
<?php  include "../includes/header.php"; ?>
<main class='container-fluid'>
  <form class='supply_add' action='#' method='post'>
    <div>
        <label>id продукта</label>
        <input type='number' name='product_id' value='1'/>
    </div>
    <div>
        <label>Количество</label>
        <input type='number' name='quantity' value='1'/>
    </div>
    <div>
        <label>Цена за штуку</label>
        <input type='number' name='price_per_unit' value='1'/>
    </div>
    <div>
        <input type='submit' value='Заказать у поставщика'/>
    </div>
  </form>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>
