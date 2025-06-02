<?php
require "../config/db.php";

if (isset($_GET['accept_order'])) {
    $acceptOrderId = intval($_GET['accept_order']);
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("SELECT product_id, quantity, accepted FROM supplies WHERE id = ?");
        $stmt->bind_param("i", $acceptOrderId);
        $stmt->execute();
        $orderData = $stmt->get_result()->fetch_assoc();

        if (!$orderData) throw new Exception("Заказ не найден");
        if ($orderData['accepted']) throw new Exception("Заказ уже принят");

        $stmt = $conn->prepare("UPDATE stock SET quantity = quantity + ? WHERE product_id = ?");
        $stmt->bind_param("ii", $orderData['quantity'], $orderData['product_id']);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE supplies SET accepted = 1 WHERE id = ?");
        $stmt->bind_param("i", $acceptOrderId);
        $stmt->execute();

        $conn->commit();
        header("Location: supplies.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color:red;'>Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

$query = "
    SELECT s.created_at, 
           s.id AS supply_id, 
           sup.name AS supplier_name,
           p.id AS product_id,
           p.name AS product_name,
           s.quantity,
           s.price_per_unit,
           s.accepted
    FROM supplies s
    JOIN suppliers sup ON sup.id = s.supplier_id
    JOIN products p ON p.id = s.product_id
    ORDER BY s.created_at DESC, s.id DESC
";
$result = $conn->query($query);
$supplies = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Заказы у поставщиков</title>
    <link rel="stylesheet" href="../css/style.css" />
    <style>
        tr.accepted {
            background-color: #d4edda !important;
        }
    </style>
</head>
<body>
<?php include "../includes/header.php"; ?>

<main class="container-fluid">
    <h1>Заказы у поставщиков</h1>

    <div style="margin-bottom: 20px;">
        <a href="supply_create.php" class="btn btn-green" style="margin-right: 10px;">Добавить заказ у поставщика</a>
        <a href="supplier_create.php" class="btn btn-green">Добавить поставщика</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Дата</th>
                <th>Номер заказа</th>
                <th>Поставщик</th>
                <th>ID продукта</th>
                <th>Название продукта</th>
                <th>Количество</th>
                <th>Цена за ед.</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($supplies) === 0): ?>
                <tr><td colspan="8" style="text-align:center;">Нет данных</td></tr>
            <?php else: ?>
                <?php foreach ($supplies as $row): ?>
                    <tr <?= $row['accepted'] ? 'class="accepted"' : '' ?>>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td><?= htmlspecialchars($row['supply_id']) ?></td>
                        <td><?= htmlspecialchars($row['supplier_name']) ?></td>
                        <td><?= htmlspecialchars($row['product_id']) ?></td>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= htmlspecialchars($row['quantity']) ?></td>
                        <td><?= htmlspecialchars($row['price_per_unit']) ?> ₽</td>
                        <td>
                            <?php if (!$row['accepted']): ?>
                                <a href="supplies.php?accept_order=<?= $row['supply_id'] ?>" class="btn btn-green">Принять заказ</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php include "../includes/footer.php"; ?>
</body>
</html>