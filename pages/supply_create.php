<?php
require "../config/db.php";

$suppliersRes = $conn->query("SELECT id, name FROM suppliers ORDER BY name");
$suppliers = $suppliersRes->fetch_all(MYSQLI_ASSOC);

$productsRes = $conn->query("SELECT id, name FROM products ORDER BY name");
$products = $productsRes->fetch_all(MYSQLI_ASSOC);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_supply'])) {
    $supplierId = intval($_POST['supplier_id'] ?? 0);
    $productsInput = $_POST['products'] ?? [];

    if ($supplierId <= 0) {
        $error = "Выберите поставщика";
    } else {
        $conn->begin_transaction();
        try {
            foreach ($productsInput as $productId => $data) {
                $quantity = intval($data['quantity']);
                $pricePerUnit = floatval($data['price_per_unit']);
                if ($quantity > 0 && $pricePerUnit > 0) {
                    $stmt = $conn->prepare("INSERT INTO supplies (supplier_id, product_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiid", $supplierId, $productId, $quantity, $pricePerUnit);
                    $stmt->execute();
                }
            }
            $conn->commit();
            header("Location: supplies.php");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Ошибка при создании заказа: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Создание заказа у поставщика</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<?php include "../includes/header.php"; ?>

<main class="container">
    <h1>Создание заказа у поставщика</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" class="product_add">
        <div>
            <label>Поставщик:</label>
            <select name="supplier_id" required>
                <option value="">-- выберите поставщика --</option>
                <?php foreach ($suppliers as $sup): ?>
                    <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <h3>Товары для заказа</h3>
        <table>
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Количество</th>
                    <th>Цена за единицу (₽)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?>
                <tr>
                    <td><?= htmlspecialchars($prod['name']) ?></td>
                    <td>
                        <input type="number" min="0" name="products[<?= $prod['id'] ?>][quantity]" value="0" style="width: 80px;">
                    </td>
                    <td>
                        <input type="number" min="0" step="0.01" name="products[<?= $prod['id'] ?>][price_per_unit]" value="0" style="width: 100px;">
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <input type="submit" name="create_supply" value="Создать заказ" class="btn btn-green" style="margin-top: 20px;">
    </form>
</main>

<?php include "../includes/footer.php"; ?>
</body>
</html>