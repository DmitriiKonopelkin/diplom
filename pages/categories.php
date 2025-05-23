<?php

require "../config/db.php";

$sql= ("SELECT * FROM categories");

$result= $conn->query($sql);

 $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Категории товаров</title>
    <link rel='stylesheet' href='../css/style.css'/>
    
</head>
<body>
 <?php  include "../includes/header.php"; ?>
<main class='container-fluid'>
 <table>
    <tr>
        <th>Артикул</th>
        <th>Название</th>
        <th>Описание</th>
        <th>Действия</th>
    </tr>
     <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= htmlspecialchars($category['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($category['name'] ?? '') ?></td>
                <td><?= htmlspecialchars($product['description'] ?? '—') ?></td>
                <td class="action-col">
                    <a href="categories_edit.php?id=<?= $category['id'] ?>" class="edit-btn">Редактировать</a>
                </td>
            </tr>
            <?php endforeach; ?>
 </table>
</main>
<?php include "../includes/footer.php"; ?>
</body>
</html>