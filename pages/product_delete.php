<?php

require "../config/db.php";

if($_SERVER['REQUEST_METHOD']== 'POST' && isset($_POST['product_id'])) {
  $product_id= intval($_POST['product_id']);

  $sql= ("DELETE FROM products WHERE id = ?");

  $stmt= $conn->prepare($sql);
  $stmt->bind_param("i", $product_id);
  $stmt->execute();
  $stmt->close();
}

?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удаление товара</title>
    <link rel='stylesheet' href='../css/style.css'/>
</head>
<body>
    <?php include "../includes/header.php"; ?>
    <main class='container-fluid'>
       
    </main>
    <?php include "../includes/footer.php"; ?>
</body>
</html>