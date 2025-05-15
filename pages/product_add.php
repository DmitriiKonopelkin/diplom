<?php
require "../config/db.php";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получаем и проверяем данные
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $weight = !empty($_POST['weight']) ? floatval($_POST['weight']) : null;
    $dimensions = $_POST['dimensions'] ?? null;
    $color = $_POST['color'] ?? null;
    $perishable = isset($_POST['perishable']) ? 1 : 0;
    $fragile = isset($_POST['fragile']) ? 1 : 0;
    $categories_id = intval($_POST['categories'] ?? 1);


    $sql = "INSERT INTO products (name, description, weight, dimensions, color, perishable, fragile, categories_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }


    $stmt->bind_param('ssdssiii', $name, $description, $weight, $dimensions, $color, $perishable, $fragile, $categories_id);

    if ($stmt->execute()) {
        echo "<p style='color:green'>Товар успешно добавлен!</p>";
    } else {
        echo "<p style='color:red'>Ошибка при добавлении товара: " . $stmt->error . "</p>";
    }

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
            height: 100px;
        }

        .product_add input[type='checkbox'] {
            width: auto;
            margin-right: 10px;
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
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include "../includes/header.php"; ?>
<main class='container-fluid'>
  <form class='product_add' action='product_add.php' method='post'>
    <div>
        <input type='text' name='name' placeholder='Название товара' required/>
    </div>
    <div>
       <textarea name='description' placeholder='Описание товара' required></textarea>
    </div>
    <div>
        <input type='number' name='weight' placeholder='Вес товара' step="0.01"/>
    </div>
    <div>
        <input type='text' name='dimensions' placeholder='Размер (например, 10x20x30)'/>
    </div>
    <div>
        <input type='text' name='color' placeholder='Цвет'/>
    </div>
    <div class="checkbox-label">
        <input type='checkbox' name='perishable' value="1"/>
        <label>Является ли товар скоропортящимся</label>
    </div>
     <div class="checkbox-label">
        <input type='checkbox' name='fragile' value="1"/>
        <label>Является ли товар хрупким</label>
     </div>
     <div>
        <input type='number' name='categories' value='1' min="1" required/>
     </div>
    <div>
        <input type='submit' value='Добавить товар'/>
    </div>
  </form>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>
