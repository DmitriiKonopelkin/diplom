<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../config/db.php";

try {
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
        ORDER BY products.id
    ";
    
    $result = $conn->query($query);
    
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
