
<?php

require "../config/db.php";


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказы поставщика</title>
    <link rel='stylesheet' href='../css/style.css'/>
</head>
<body>
<?php  include "../includes/header.php"; ?>
<main class='container-fluid'>
  <form action='#' method='post'>
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
