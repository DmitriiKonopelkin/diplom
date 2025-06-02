<?php
require "../config/db.php";

if (isset($_GET['delete_order'])) {
    $orderId = intval($_GET['delete_order']);

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        $conn->commit();
        $message = "Заказ и его позиции успешно удалены.";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Ошибка при удалении заказа: " . $e->getMessage();
    }
}

$query = "
    SELECT o.*, u.username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
";
$result = $conn->query($query);
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление заказами</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include "../includes/header.php"; ?>
<main class="container-fluid">
    <h1>Список заказов</h1>

    <?php if (isset($message)): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <a href="order_create.php" class="btn btn-green" style="margin-bottom: 15px;">Создать заказ</a>

    <table class="products">
        <thead>
            <tr>
                <th>ID</th>
                <th>Клиент</th>
                <th>Телефон</th>
                <th>Пользователь</th>
                <th>Дата</th>
                <th>Доставка</th>
                <th>Сумма</th>
                <th>Статус</th>
                <th>Оплата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= htmlspecialchars($order['customer_tel']) ?></td>
                    <td><?= htmlspecialchars($order['username'] ?? '—') ?></td>
                    <td><?= $order['created_at'] ?></td>
                    <td><?= htmlspecialchars($order['delivery_address']) ?></td>
                    <td><?= $order['total_price'] ?> ₽</td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td><?= htmlspecialchars($order['payment_status']) ?></td>
                    <td>
                        <a href="order_edit.php?id=<?= $order['id'] ?>" class="edit-btn">Редактировать</a>
                        <a href="?delete_order=<?= $order['id'] ?>" class="btn btn-red" onclick="return confirm('Удалить заказ?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>
