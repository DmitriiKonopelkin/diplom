<?php
require "../config/db.php";

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_supplier'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tel = trim($_POST['tel'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contact_person = trim($_POST['contact_person'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $error = "Название поставщика обязательно.";
    } else {
        $stmt = $conn->prepare("INSERT INTO suppliers (name, email, tel, address, contact_person, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $tel, $address, $contact_person, $description);
        if ($stmt->execute()) {
            header("Location: suppliers.php");
            exit;
        } else {
            $error = "Ошибка при добавлении поставщика: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Добавить поставщика</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
<?php include "../includes/header.php"; ?>

<main class="container">
    <h1>Добавить поставщика</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" class="product_add">
        <div>
            <label>Название *</label>
            <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div>
            <label>Телефон</label>
            <input type="text" name="tel" value="<?= htmlspecialchars($_POST['tel'] ?? '') ?>">
        </div>
        <div>
            <label>Адрес</label>
            <textarea name="address"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
        </div>
        <div>
            <label>Контактное лицо</label>
            <input type="text" name="contact_person" value="<?= htmlspecialchars($_POST['contact_person'] ?? '') ?>">
        </div>
        <div>
            <label>Описание</label>
            <textarea name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>
        <input type="submit" name="create_supplier" value="Добавить" class="btn btn-green" style="margin-top: 20px;">
    </form>
</main>

<?php include "../includes/footer.php"; ?>
</body>
</html>