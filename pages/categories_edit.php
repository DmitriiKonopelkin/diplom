<?php
require "../config/db.php";

$category = null;
if (isset($_GET['id'])) {
    $category_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $category_id = intval($_POST['id']);
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
   
    $sql = "UPDATE categories SET 
            name = ?, 
            description = ?, 
            weight = ?, 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }

    $stmt->bind_param('ss', $name, $description);

    if ($stmt->execute()) {
        echo "<p style='color:green'>Категория успешно обновлена!</p>";
        $category = [
            'id' => $product_id,
            'name' => $name,
            'description' => $description,
        ];
    } else {
        echo "<p style='color:red'>Ошибка при обновлении категории: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование категорий</title>
    <link rel='stylesheet' href='../css/style.css'/>
    <style>
        .categories_edit input[type='text'],
        .categories_edit input[type='number'] {
            font-size:17.8px;
            margin-bottom:20px;
            padding:15px 10px;
            border:1px solid #ebb217;
            width:20%;
        }
          

.categories_edit input[type='submit'] {
            font-size:17.8px;
            margin-bottom:20px;
            padding:15px 10px;
            border:1px solid #ebb217;
            width:20%;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            margin-top:30px;
        }
        
    </style>
</head>
<body>
<?php  include "../includes/header.php"; ?>
<main class='container-fluid'>
    <div class="categories_edit">
       <form action='categories_edit.php' method='post'>
        <div>
            <input type='text' name='name' placeholder='Название категории'  value='<?= htmlspecialchars($category['name'] ?? '') ?>'/>
        </div>
        <div>
            <input type='text' name='description' placeholder='Описание категории'  value='<?= htmlspecialchars($category['description'] ?? '') ?>'/>
        </div>
        <div>
            <input type='submit' value='Редактировать категорию'/>
        </div>
    </form>
    </div>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>