<?php

require "../config/db.php";

if($_SERVER['REQUEST_METHOD']== 'POST' && isset($_POST['product_id'])) {
  $product_id= intval($_POST['product_id']);

  $sql= ("DELETE FROM products WHERE id = ?");

  $stmt= $conn->prepare($sql);
  $stmt->bind_param("i", $product_id);
  $stmt->execute();
  $stmt->close();
}

$products=[];

$sql= ("SELECT id, name FROM products");

$result= $conn->query($sql);

while($row= $result->fetch_assoc()) {
  $products[]= $row;
}

$conn->close();

?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удаление товара</title>
    <link rel='stylesheet' href='../css/style.css'/>
</head>
<body>
    <?php include "../includes/header.php"; ?>
    <main class='container-fluid'>
       <table>
        <tr>
        <th>Id</th>
        <th>Название</th>
        <th>Удалить</th>
        </tr>
        <?php

        foreach($products as $product):
        ?>
        <tr>
        <td><?php  echo htmlspecialchars($product['id']); ?></td>
        <td><?php  echo htmlspecialchars($product['name']); ?></td>
        <td><form method='post' onsubmit='return confirm("Вы уверены, что хотите удалить этот товар?");'>
            <div>
              <input type='hidden' name='product_id' value="<?php echo $product['id']; ?>">            
            </div>
            <div>
              <button type='submit'>Удалить</button>
            </div>
        </form></td>
        </tr>
        <?php endforeach; ?>
       </table>
    </main>
    <?php include "../includes/footer.php"; ?>
</body>
</html>