<?php
require "../config/db.php";

$orderId = intval($_GET['id'] ?? 0);
if ($orderId <= 0) die("Некорректный ID заказа");

$orderRes = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$orderRes->bind_param("i", $orderId);
$orderRes->execute();
$order = $orderRes->get_result()->fetch_assoc();
if (!$order) die("Заказ не найден");

$itemsRes = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE order_id = ?");
$itemsRes->bind_param("i", $orderId);
$itemsRes->execute();
$orderItems = $itemsRes->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $status = $_POST['status'];
    $paymentStatus = $_POST['payment_status'];
    $deliveryAddress = $_POST['delivery_address'];
    $customerName = $_POST['customer_name'] ?? '';
    $customerTel = $_POST['customer_tel'] ?? '';
    $newProducts = $_POST['products'] ?? [];

    $conn->begin_transaction();

    try {
        $oldItems = [];
        foreach ($orderItems as $item) {
            $oldItems[$item['product_id']] = $item['quantity'];
        }

        $totalPrice = 0;

        foreach ($oldItems as $productId => $oldQty) {
            $qty = isset($newProducts[$productId]) ? intval($newProducts[$productId]) : 0;
            $diff = $qty - $oldQty;

            if ($diff !== 0) {
                $stmtStock = $conn->prepare("UPDATE stock SET quantity = quantity - ? WHERE product_id = ?");
                $stmtStock->bind_param("ii", $diff, $productId);
                $stmtStock->execute();
            }

            if ($qty > 0) {
                $stmtPrice = $conn->prepare("SELECT price FROM prices WHERE product_id = ? ORDER BY price_type_id LIMIT 1");
                $stmtPrice->bind_param("i", $productId);
                $stmtPrice->execute();
                $priceRes = $stmtPrice->get_result();
                $priceRow = $priceRes->fetch_assoc();
                $unitPrice = $priceRow ? floatval($priceRow['price']) : 0;
                $itemTotal = $unitPrice * $qty;

                $stmtItem = $conn->prepare("UPDATE order_items SET quantity = ?, total_price = ? WHERE order_id = ? AND product_id = ?");
                $stmtItem->bind_param("idii", $qty, $itemTotal, $orderId, $productId);
                $stmtItem->execute();

                $totalPrice += $itemTotal;
            } else {
                $stmtDel = $conn->prepare("DELETE FROM order_items WHERE order_id = ? AND product_id = ?");
                $stmtDel->bind_param("ii", $orderId, $productId);
                $stmtDel->execute();
            }
        }

        $stmtUpd = $conn->prepare("UPDATE orders SET total_price = ?, status = ?, payment_status = ?, delivery_address = ?, customer_name = ?, customer_tel = ? WHERE id = ?");
        $stmtUpd->bind_param("dsssssi", $totalPrice, $status, $paymentStatus, $deliveryAddress, $customerName, $customerTel, $orderId);
        $stmtUpd->execute();

        $conn->commit();

        header("Location: orders.php");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color:red;'>Ошибка обновления заказа: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Редактирование заказа</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<?php include "../includes/header.php"; ?>
<main class="container">
    <h1>Редактирование заказа №<?= $orderId ?></h1>
    <form method="post" class="product_add">
        <div class="form-group">
            <label for="customer_name">Имя клиента:</label>
            <input type="text" name="customer_name" id="customer_name" class="form-control" value="<?= htmlspecialchars($order['customer_name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="customer_tel">Телефон клиента:</label>
            <input type="text" name="customer_tel" id="customer_tel" class="form-control" value="<?= htmlspecialchars($order['customer_tel'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="status">Статус заказа:</label>
            <select name="status" id="status" class="form-control">
                <?php foreach (["active","shipped","completed","cancelled"] as $st): ?>
                    <option value="<?= $st ?>" <?= $order['status'] === $st ? "selected" : "" ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="payment_status">Статус оплаты:</label>
            <select name="payment_status" id="payment_status" class="form-control">
                <option value="not paid" <?= $order['payment_status'] === "not paid" ? "selected" : "" ?>>not paid</option>
                <option value="paid" <?= $order['payment_status'] === "paid" ? "selected" : "" ?>>paid</option>
            </select>
        </div>

        <div class="form-group">
            <label for="delivery_address">Адрес доставки:</label>
            <textarea name="delivery_address" id="delivery_address" class="form-control" rows="3"><?= htmlspecialchars($order['delivery_address']) ?></textarea>
        </div>

        <h2>Товары в заказе</h2>
        <table>
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><input type="number" min="0" name="products[<?= $item['product_id'] ?>]" value="<?= $item['quantity'] ?>" class="form-control" style="width: 80px;"></td>
                        <td><?= $item['total_price'] ?> ₽</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <input type="submit" name="update_order" value="Сохранить изменения" class="btn btn-green" style="margin-top: 15px;">
    </form>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>
