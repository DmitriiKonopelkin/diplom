
<?php
require "../config/db.php";

$categories = [];
$categories_result = $conn->query("SELECT id, name FROM categories");
if ($categories_result) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[$row['id']] = $row['name'];
    }
}

// Получаем данные товара для редактирования
$product = null;
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

// Обработка формы обновления
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $product_id = intval($_POST['id']);
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $weight = !empty($_POST['weight']) ? floatval($_POST['weight']) : null;
    $dimensions = $_POST['dimensions'] ?? null;
    $color = $_POST['color'] ?? null;
    $perishable = isset($_POST['perishable']) ? 1 : 0;
    $fragile = isset($_POST['fragile']) ? 1 : 0;
    $categories_id = intval($_POST['categories_id'] ?? 1);

    $sql = "UPDATE products SET 
            name = ?, 
            description = ?, 
            weight = ?, 
            dimensions = ?, 
            color = ?, 
            perishable = ?, 
            fragile = ?, 
            categories_id = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }

    $stmt->bind_param('ssdssiiii', $name, $description, $weight, $dimensions, $color, $perishable, $fragile, $categories_id, $product_id);

    if ($stmt->execute()) {
        echo "<p style='color:green'>Товар успешно обновлен!</p>";
        // Обновляем данные товара для отображения
        $product = [
            'id' => $product_id,
            'name' => $name,
            'description' => $description,
            'weight' => $weight,
            'dimensions' => $dimensions,
            'color' => $color,
            'perishable' => $perishable,
            'fragile' => $fragile,
            'categories_id' => $categories_id
        ];
    } else {
        echo "<p style='color:red'>Ошибка при обновлении товара: " . $stmt->error . "</p>";
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
    <title><?= isset($product) ? 'Редактирование товара' : 'Товар не найден' ?></title>
    <link rel='stylesheet' href='../css/style.css'/>
    <style>
        .product_edit input[type='text'],
        .product_edit input[type='number'] {
            font-size:17.8px;
            margin-bottom:20px;
            padding:15px 10px;
            border:1px solid #ebb217;
            width:20%;
        }

        .product_edit textarea {
            font-size:17.8px;
            margin-bottom:20px;
            padding:15px 10px;
            border:1px solid #ebb217;
            width:20%;
            height: 100px;
        }

        .product_edit input[type='checkbox'] {
            width: auto;
            margin-right: 10px;
        }

        .product_edit select {
            font-size:17.8px;
            margin-bottom:20px;
            padding:15px 10px;
            border:1px solid #ebb217;
            width:20%;
        }

.product_edit input[type='submit'] {
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
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #0066cc;
            text-decoration: none;
        }
    </style>
</head>
<body>
<?php include "../includes/header.php"; ?>
<main class='container-fluid'>
    <?php if (isset($product)): ?>
        <a href="product_list.php" class="back-link">← Вернуться к списку товаров</a>
        <form class='product_edit' action='product_edit.php' method='post'>
            <input type='hidden' name='id' value='<?= htmlspecialchars($product['id']) ?>'>
            
            <div>
                <input type='text' name='name' placeholder='Название товара' 
                       value='<?= htmlspecialchars($product['name'] ?? '') ?>' required/>
            </div>
            <div>
               <textarea name='description' placeholder='Описание товара' required><?= 
                   htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>
            <div>
                <input type='number' name='weight' placeholder='Вес товара' step="0.01"
                       value='<?= htmlspecialchars($product['weight'] ?? '') ?>'/>
            </div>
            <div>
                <input type='text' name='dimensions' placeholder='Размер (например, 10x20x30)'
                       value='<?= htmlspecialchars($product['dimensions'] ?? '') ?>'/>
            </div>
            <div>
                <input type='text' name='color' placeholder='Цвет'
                       value='<?= htmlspecialchars($product['color'] ?? '') ?>'/>
            </div>
            <div class="checkbox-label">
                <input type='checkbox' name='perishable' value="1" 
                       <?= (isset($product['perishable']) && $product['perishable']) ? 'checked' : '' ?>/>
                <label>Является ли товар скоропортящимся</label>
            </div>
             <div class="checkbox-label">
                <input type='checkbox' name='fragile' value="1"
                       <?= (isset($product['fragile']) && $product['fragile']) ? 'checked' : '' ?>/>
                <label>Является ли товар хрупким</label>
             </div>
             <div>
                <select name='categories_id' required>
                    <option value="">-- Выберите категорию --</option>
                    <?php foreach ($categories as $id => $name): ?>
                        <option value="<?= htmlspecialchars($id) ?>" 
                            <?= (isset($product['categories_id']) && $product['categories_id'] == $id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
             </div>
            <div>
                <input type='submit' value='Сохранить изменения'/>
            </div>
        </form>
    <?php else: ?>
        <p style='color:red'>Товар не найден или не указан ID.</p>
        <a href="product_list.php" class="back-link">← Вернуться к списку товаров</a>
    <?php endif; ?>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>
