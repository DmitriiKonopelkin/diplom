<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require "../config/db.php";

$searchName = ''; 
$minPrice = null; 
$maxPrice = null;
$categoryId = null;
$categories = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_stock'])) {
    $productId = intval($_POST['product_id']);
    $newQuantity = intval($_POST['new_quantity']);

    if ($productId > 0 && $newQuantity >= 0) {
        $check = $conn->prepare("SELECT product_id FROM stock WHERE product_id = ?");
        if (!$check) {
            die("Ошибка подготовки запроса (stock check): " . $conn->error);
        }
        $check->bind_param("i", $productId);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $update = $conn->prepare("UPDATE stock SET quantity = ? WHERE product_id = ?");
            if (!$update) {
                die("Ошибка подготовки запроса (update stock): " . $conn->error);
            }
            $update->bind_param("ii", $newQuantity, $productId);
        } else {
            $update = $conn->prepare("INSERT INTO stock (product_id, quantity) VALUES (?, ?)");
            if (!$update) {
                die("Ошибка подготовки запроса (insert stock): " . $conn->error);
            }
            $update->bind_param("ii", $productId, $newQuantity);
        }
        $update->execute();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_price'])) {
    $productId = intval($_POST['product_id']);
    $priceTypeId = intval($_POST['price_type_id']);
    $newPrice = floatval($_POST['new_price']);

    if ($productId > 0 && $priceTypeId > 0 && $newPrice >= 0) {
        $check = $conn->prepare("SELECT id FROM prices WHERE product_id = ? AND price_type_id = ?");
        if (!$check) {
            die("Ошибка подготовки запроса (prices check): " . $conn->error);
        }
        $check->bind_param("ii", $productId, $priceTypeId);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $update = $conn->prepare("UPDATE prices SET price = ?, updated_at = NOW() WHERE product_id = ? AND price_type_id = ?");
            if (!$update) {
                die("Ошибка подготовки запроса (update price): " . $conn->error);
            }
            $update->bind_param("dii", $newPrice, $productId, $priceTypeId);
        } else {
            $update = $conn->prepare("INSERT INTO prices (product_id, price_type_id, price, created_at) VALUES (?, ?, ?, NOW())");
            if (!$update) {
                die("Ошибка подготовки запроса (insert price): " . $conn->error);
            }
            $update->bind_param("iid", $productId, $priceTypeId, $newPrice);
        }
        $update->execute();
    }
}

$catQuery = "SELECT id, name FROM categories ORDER BY name";
$catResult = $conn->query($catQuery);
while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['update_price']) && !isset($_POST['update_stock'])) {
    $searchName = trim($_POST['search']);
    $minPrice = $_POST['min_price'] !== '' ? floatval($_POST['min_price']) : null;
    $maxPrice = $_POST['max_price'] !== '' ? floatval($_POST['max_price']) : null;
    $categoryId = $_POST['category_filter'] !== '' ? intval($_POST['category_filter']) : null;
}

$sqlConditions = [];
$params = [];
$types = '';

if (!empty($searchName)) {
    $sqlConditions[] = "products.name LIKE ?";
    $params[] = "%{$searchName}%";
    $types .= 's';
}
if ($minPrice !== null) {
    $sqlConditions[] = "prices.price >= ?";
    $params[] = $minPrice;
    $types .= 'd';
}
if ($maxPrice !== null) {
    $sqlConditions[] = "prices.price <= ?";
    $params[] = $maxPrice;
    $types .= 'd';
}
if ($categoryId !== null) {
    $sqlConditions[] = "products.categories_id = ?";
    $params[] = $categoryId;
    $types .= 'i';
}

$whereClause = count($sqlConditions) > 0 ? 'WHERE ' . implode(' AND ', $sqlConditions) : '';

$query = "
    SELECT 
        products.id,
        products.name,
        products.weight,
        products.dimensions,
        products.color,
        categories.name AS category_name,
        stock.quantity AS stock_quantity,
        prices.price,
        price_types.name AS price_type,
        price_types.id AS price_type_id
    FROM products
    LEFT JOIN categories ON products.categories_id = categories.id
    LEFT JOIN stock ON products.id = stock.product_id
    LEFT JOIN prices ON products.id = prices.product_id
    LEFT JOIN price_types ON prices.price_type_id = price_types.id
    $whereClause
    ORDER BY products.id
";

if (count($params) > 0) {
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Ошибка подготовки запроса (products): " . $conn->error);
    }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
    if (!$result) {
        die("Ошибка запроса (products): " . $conn->error);
    }
}

$products = [];
while ($row = $result->fetch_assoc()) {
    $productId = $row['id'];
    if (!isset($products[$productId])) {
        $products[$productId] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'weight' => $row['weight'],
            'dimensions' => $row['dimensions'],
            'color' => $row['color'],
            'category_name' => $row['category_name'],
            'stock_quantity' => $row['stock_quantity'],
            'prices' => []
        ];
    }

    if ($row['price_type_id']) {
        $products[$productId]['prices'][$row['price_type']] = [
            'type_id' => $row['price_type_id'],
            'price' => $row['price']
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление остатками</title>
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
<?php include "../includes/header.php"; ?>
<main class="container-fluid">
    <h1>Управление остатками товаров</h1>

 <form method="post" class="filter-form" style="margin-bottom: 20px;">
    <input type="text" name="search" placeholder="Название" value="<?= htmlspecialchars($searchName) ?>">
    <input type="number" step="any" name="min_price" placeholder="Мин. цена" value="<?= htmlspecialchars($minPrice ?? '') ?>">
    <input type="number" step="any" name="max_price" placeholder="Макс. цена" value="<?= htmlspecialchars($maxPrice ?? '') ?>">
    <select name="category_filter">
        <option value="">Все категории</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $categoryId ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Применить</button>
    <a href="product_add.php" class="btn btn-green" style="margin-left:10px;">Добавить товар</a>
    <a href="product_delete.php" class="btn btn-red" style="margin-left:10px;">Удалить товар</a>
</form>
    <?php if (empty($products)): ?>
        <p>Нет товаров по заданным фильтрам.</p>
    <?php else: ?>
        <table class="products">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Цвет</th>
                    <th>Размеры</th>
                    <th>Вес</th>
                    <th>Остаток</th>
                    <th>Закупочная</th>
                    <th>Со скидкой</th>
                    <th>Розница</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $priceTypes = ['Закупочная', 'Со скидкой', 'Розничная'];
            ?>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['category_name']) ?></td>
                    <td><?= htmlspecialchars($product['color']) ?></td>
                    <td><?= htmlspecialchars($product['dimensions']) ?></td>
                    <td><?= htmlspecialchars($product['weight']) ?> кг</td>

                    <td>
                        <form method="post" class="stock-update-form" style="margin-bottom: 5px;">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="number" name="new_quantity" min="0" value="<?= htmlspecialchars($product['stock_quantity'] ?? 0) ?>">
                            <button type="submit" name="update_stock">Обновить</button>
                        </form>
                    </td>

                    <?php foreach ($priceTypes as $typeName): 
                        $priceInfo = $product['prices'][$typeName] ?? ['type_id' => null, 'price' => ''];
                    ?>
                    <td>
                        <?php if ($priceInfo['type_id'] !== null): ?>
                            <form method="post" class="price-update-form" style="margin-bottom: 5px;">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="hidden" name="price_type_id" value="<?= $priceInfo['type_id'] ?>">
                                <input type="number" step="any" name="new_price" value="<?= htmlspecialchars($priceInfo['price']) ?>" required>
                                <button type="submit" name="update_price">Сохранить</button>
                            </form>
                        <?php else: ?>
                            <em>Нет цены</em>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>

                    <td><a href="product_edit.php?id=<?= $product['id'] ?>" class="edit-btn">Редактировать</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>