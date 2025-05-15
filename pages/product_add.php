<?php

require "../config/db.php";

if($_SERVER['REQUEST_METHOD']== 'POST') {
    $name= $_POST['name'];
    $description= $_POST['description'];
    $weight= $_POST['weight'];
    $dimensions= $_POST['dimensions'];
    $color= $_POST['color'];
    $perishable= $_POST['perishable'];
    $fragile= $_POST['fragile'];
    $categories= $_POST['categories'];

$sql= ("INSERT INTO products('id', 'name', 'description', 'weight', 'dimensions', 'color', 'perishable', 'fragile', 'categories_id') 
VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt= $conn->prepare($sql);
$stmt->bind_param('ssdsssiii', $name, $description, $weight, $dimensions, $color, $perishable, $fragile, $categories_id);
$stmt->execute();
$stmt->close();
$conn->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление товара</title>
    <link rel='stylesheet' href='../css/style.css'/>
    <style>
        .product_add input[type='text'] {
            font-size:17.8px;
            margin-bottom:20px;
            padding:15px 10px;
            border:1px solid #ebb217;
            width:20%;
        }

        .product_add input[type='number'] {
            font-size:17.8px;
            margin-bottom:20px;
            padding:15px 10px;
            border:1px solid #ebb217;
            width:20%;
        }

        .product_add textarea {
            font-size:17.8px;
            margin-bottom:20px;
            padding:15px 10px;
            border:1px solid #ebb217;
            width:20%;
        }

        .product_add input[type='chekbox'] {
            font-size:17.8px;
            margin-bottom:20px;
            padding:15px 10px;
            border:1px solid #ebb217;
            width:20%;
        }

        .product_add select {
            font-size:17.8px;
            margin-bottom:20px;
            padding:15px 10px;
            border:1px solid #ebb217;
            width:20%;
        }

        .product_add input[type='submit'] {
            font-size:17.8px;
            margin-bottom:20px;
            padding:15px 10px;
            border:1px solid #ebb217;
            width:20%;
        }
    </style>
</head>
<body>
<?php  include "../includes/header.php"; ?>
<main class='container-fluid'>
  <form class='product_add'action='product_add.php' method='post'>
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
     <!--<div>
        <select name='categories'>
            <option>Горный велосипед</option>
            <option>Городской велосипед</option>
        </select>
     </div>-->
     <div>
        <input type='number' name='categories' value='1'/>
     </div>
    <div>
        <input type='submit' value='Добавить товар'/>
    </div>
  </form>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>