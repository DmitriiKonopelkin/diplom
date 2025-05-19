<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../config/db.php";

$searchName = ''; 
$minPrice   = null; 
$maxPrice   = null;
$categoryId = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $searchName = trim($_POST['search']);
    $minPrice   = !empty($_POST['min_price']) ? floatval($_POST['min_price']) : null;
    $maxPrice   = !empty($_POST['max_price']) ? floatval($_POST['max_price']) : null;
    $categoryId = !empty($_POST['category_filter']) ? intval($_POST['category_filter']) : null;
}

try {
    $sqlConditions = array();
    $params = array();

    if (!empty($searchName)) {
        $sqlConditions[] = "products.name LIKE ?";
        $params[] = "%{$searchName}%";
    }

    if ($minPrice !== null && is_numeric($minPrice)) {
        $sqlConditions[] = "prices.price >= ?";
        $params[] = $minPrice;
    }

    if ($maxPrice !== null && is_numeric($maxPrice)) {
        $sqlConditions[] = "prices.price <= ?";
        $params[] = $maxPrice;
    }

    if ($categoryId !== null) {
        $sqlConditions[] = "products.categories_id = ?";
        $params[] = $categoryId;
    }

    $whereClause = count($sqlConditions) > 0 ? 'WHERE ' . implode(' AND ', $sqlConditions) : '';

    $query = "
        SELECT 
            products.*,
            prices.price AS base_price,
            categories.name AS category_name,
            stock.quantity AS stock_quantity
        FROM products 
        LEFT JOIN prices ON products.id = prices.product_id 
        LEFT JOIN stock ON products.id = stock.product_id
        LEFT JOIN categories ON products.categories_id = categories.id
        {$whereClause}
        ORDER BY products.id
    ";

    if (count($params) > 0) {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($query);
    }

    if (!$result) {
        throw new Exception("Ошибка выполнения запроса: " . $conn->error);
    }

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    $conn->close();
} catch (Exception $e) {
    die("Произошла ошибка: " . $e->getMessage());
}
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
<?php include "../includes/header.php"; ?>
<main class='container-fluid'>
    <h1>Управление остатками товаров</h1>
    
    <div class="filter-form">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="search">Искать товар:</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchName); ?>"/>
            
            <label for="min_price">Минимальная цена:</label>
            <input type="number" step="any" min="0" id="min_price" name="min_price" placeholder="От..." value="<?php echo htmlspecialchars($minPrice ?: ''); ?>"/>
            
            <label for="max_price">Максимальная цена:</label>
            <input type="number" step="any" min="0" id="max_price" name="max_price" placeholder="До..." value="<?php echo htmlspecialchars($maxPrice ?: ''); ?>"/>
            
            <button type="submit">Применить фильтры</button>
        </form>

    </div>

    <?php if (empty($products)): ?>
        <div class="error-message">
            Нет данных о товарах или произошла ошибка при загрузке.
        </div>
    <?php else: ?>
        <table class='products'>
            <tr>
                <th>Артикул</th>
                <th>Название</th>
                <th>Цвет</th>
                <th>Вес (кг)</th>
                <th>Размер</th>
                <th>Категория</th>
                <th>В наличии (шт.)</th>
                <th>Цена</th>
                <th class="action-col">Действия</th>
            </tr>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($product['name'] ?? '') ?></td>
                <td><?= htmlspecialchars($product['color'] ?? '—') ?></td>
                <td><?= htmlspecialchars($product['weight'] ?? '—') ?></td>
                <td><?= htmlspecialchars($product['dimensions'] ?? '—') ?></td>
                <td><?= htmlspecialchars($product['category_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($product['stock_quantity'] ?? '0') ?></td>
                <td><?= isset($product['base_price']) ? number_format($product['base_price'], 2, '.', ' ') : '—' ?></td>
                <td class="action-col">
                    <a href="product_edit.php?id=<?= $product['id'] ?>" class="edit-btn">Редактировать</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>
