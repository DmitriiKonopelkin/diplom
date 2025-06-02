<?php
require "../config/db.php";

$categories = [];
$categories_result = $conn->query("SELECT id, name FROM categories");
if ($categories_result) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[$row['id']] = $row['name'];
    }
}

$price_types = [
    1 => 'Закупочная',
    2 => 'Розничная',
    3 => 'Со скидкой'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $weight = !empty($_POST['weight']) ? floatval($_POST['weight']) : null;
    $dimensions = $_POST['dimensions'] ?? null;
    $color = $_POST['color'] ?? null;
    $perishable = isset($_POST['perishable']) ? 1 : 0;
    $fragile = isset($_POST['fragile']) ? 1 : 0;
    $categories_id = intval($_POST['categories_id'] ?? 1);

    $purchase_price = !empty($_POST['purchase_price']) ? floatval($_POST['purchase_price']) : null;
    $retail_price = !empty($_POST['retail_price']) ? floatval($_POST['retail_price']) : null;
    $discount_price = !empty($_POST['discount_price']) ? floatval($_POST['discount_price']) : null;

    $conn->begin_transaction();

    try {
        $sql = "INSERT INTO products (name, description, weight, dimensions, color, perishable, fragile, categories_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Ошибка подготовки запроса: " . $conn->error);
        }
        $stmt->bind_param('ssdssiii', $name, $description, $weight, $dimensions, $color, $perishable, $fragile, $categories_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Ошибка при добавлении товара: " . $stmt->error);
        }
        
        $product_id = $stmt->insert_id;
        $stmt->close();

        // Insert prices
        if ($purchase_price !== null) {
            $sql = "INSERT INTO prices (product_id, price, price_type_id, description) VALUES (?, ?, 1, 'Закупка')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('id', $product_id, $purchase_price);
            $stmt->execute();
            $stmt->close();
        }

        if ($retail_price !== null) {
            $sql = "INSERT INTO prices (product_id, price, price_type_id, description) VALUES (?, ?, 2, 'Базовая цена')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('id', $product_id, $retail_price);
            $stmt->execute();
            $stmt->close();
        }

        if ($discount_price !== null) {
            $sql = "INSERT INTO prices (product_id, price, price_type_id, description) VALUES (?, ?, 3, 'С максимальной скидкой')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('id', $product_id, $discount_price);
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit();
        echo "<p style='color:green'>Товар и цены успешно добавлены!</p>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color:red'>Ошибка: " . $e->getMessage() . "</p>";
    }

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
</head>
<body>
<?php include "../includes/header.php"; ?>
<main class="container-fluid">
  <form class="product_add" action="product_add.php" method="post">
    <div class="form-group">
        <input type="text" name="name" placeholder="Название товара" required/>
    </div>
    <div class="form-group">
       <textarea name="description" placeholder="Описание товара" required></textarea>
    </div>
    
    <div class="price-inputs">
        <div class="price-input">
            <label for="purchase_price">Закупочная цена</label>
            <input type="number" name="purchase_price" id="purchase_price" placeholder="0.00" step="0.01" min="0" />
        </div>
        <div class="price-input">
            <label for="retail_price">Розничная цена</label>
            <input type="number" name="retail_price" id="retail_price" placeholder="0.00" step="0.01" min="0" required />
        </div>
        <div class="price-input">
            <label for="discount_price">Цена со скидкой</label>
            <input type="number" name="discount_price" id="discount_price" placeholder="0.00" step="0.01" min="0" />
        </div>
    </div>
    
    <div class="form-group">
        <input type="number" name="weight" placeholder="Вес товара" step="0.01" />
    </div>
    <div class="form-group">
        <input type="text" name="dimensions" placeholder="Размер (например, 10x20x30)" />
    </div>
    <div class="form-group">
        <input type="text" name="color" placeholder="Цвет" />
    </div>
    
    <div class="checkbox-label">
        <input type="checkbox" name="perishable" value="1" id="perishable"/>
        <label for="perishable">Является ли товар скоропортящимся</label>
    </div>
    
    <div class="checkbox-label">
        <input type="checkbox" name="fragile" value="1" id="fragile"/>
        <label for="fragile">Является ли товар хрупким</label>
    </div>
    
    <div class="form-group">
        <select name="categories_id" required>
            <option value="">-- Выберите категорию --</option>
            <?php foreach ($categories as $id => $name): ?>
                <option value="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($name) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <input type="submit" value="Добавить товар" />
    </div>
  </form>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>