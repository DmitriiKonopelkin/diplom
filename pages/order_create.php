<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require "../config/db.php";

$productsRes = $conn->query("
    SELECT p.id, p.name, p.color, pr.price 
    FROM products p 
    LEFT JOIN prices pr ON pr.product_id = p.id AND pr.price_type_id = 3
    ORDER BY p.name
");
$products = $productsRes->fetch_all(MYSQLI_ASSOC);

$usersRes = $conn->query("SELECT id, username FROM users ORDER BY username");
$users = $usersRes->fetch_all(MYSQLI_ASSOC);

$methodsRes = $conn->query("SELECT id, name FROM payment_methods");
$paymentMethods = $methodsRes->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
    $userId = $_POST['user_id'] !== '' ? intval($_POST['user_id']) : null;
    $customerName = $_POST['customer_name'];
    $customerTel = $_POST['customer_tel'];
    $deliveryAddress = $_POST['delivery_address'];
    $deliveryDate = $_POST['delivery_date'] !== '' ? $_POST['delivery_date'] : null;
    $paymentMethodId = $_POST['payment_method_id'] !== '' ? intval($_POST['payment_method_id']) : null;
    $productsInput = $_POST['products'] ?? [];

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, customer_tel, delivery_address, delivery_date, payment_method_id, total_price) VALUES (?, ?, ?, ?, ?, ?, 0)");
        if (!$stmt) throw new Exception("Ошибка подготовки запроса: " . $conn->error);
        
        $stmt->bind_param("issssi", $userId, $customerName, $customerTel, $deliveryAddress, $deliveryDate, $paymentMethodId);
        $stmt->execute();
        $orderId = $stmt->insert_id;

        $totalOrderPrice = 0;
        foreach ($products as $product) {
            $productId = $product['id'];
            $qty = intval($productsInput[$productId] ?? 0);
            if ($qty <= 0) continue;

            $unitPrice = floatval($product['price'] ?? 0);
            $itemTotal = $unitPrice * $qty;

            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)");
            $stmtItem->bind_param("iiid", $orderId, $productId, $qty, $itemTotal);
            $stmtItem->execute();

            $stmtStock = $conn->prepare("UPDATE stock SET quantity = quantity - ? WHERE product_id = ?");
            $stmtStock->bind_param("ii", $qty, $productId);
            $stmtStock->execute();

            $totalOrderPrice += $itemTotal;
        }

        $stmt = $conn->prepare("UPDATE orders SET total_price = ? WHERE id = ?");
        $stmt->bind_param("di", $totalOrderPrice, $orderId);
        $stmt->execute();

        $conn->commit();
        header("Location: orders.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color:red;'>Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создание заказа</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include "../includes/header.php"; ?>
<main class="container">
    <h1>Создание заказа</h1>
    <form method="post" class="product_add">
        <div>
            <label>Имя клиента:</label>
            <input type="text" name="customer_name" required>
        </div>
        <div>
            <label>Телефон клиента:</label>
            <input type="text" name="customer_tel" required>
        </div>
        <div>
            <label>Адрес доставки:</label>
            <textarea name="delivery_address"></textarea>
        </div>
        <div>
            <label>Дата доставки:</label>
            <input type="date" name="delivery_date">
        </div>
        <div>
            <label>Пользователь:</label>
            <select name="user_id">
                <option value="">-- не выбрано --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Метод оплаты:</label>
            <select name="payment_method_id">
                <option value="">-- не выбрано --</option>
                <?php foreach ($paymentMethods as $method): ?>
                    <option value="<?= $method['id'] ?>"><?= htmlspecialchars($method['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <h3>Товары:</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Цвет</th>
                    <th>Розничная цена</th>
                    <th>Количество</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['id']) ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['color'] ?? '-') ?></td>
                    <td><?= $product['price'] !== null ? htmlspecialchars($product['price']) . ' ₽' : '-' ?></td>
                    <td>
                        <input type="number" name="products[<?= $product['id'] ?>]" min="0" placeholder="0" style="width: 80px;">
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <input type="submit" name="create_order" value="Создать заказ" class="btn btn-green" style="max-width: 200px; margin-top: 20px;">
    </form>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>