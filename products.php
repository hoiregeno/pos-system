<?php
require "./includes/db.php";

function validate_product(string $name, string $price): ?string {
  if($name === ""){
    return "Product name is required.";
  }

  if(mb_strlen($name) > 100){
    return "Product name is too long.";
  }

  if(!is_numeric($price)){
    return "Price must be a number.";
  }

  if((float) $price < 0){
    return "Price cannot be negative.";
  }

  if((float) $price > 99999999.99){
    return "Price is too large.";
  }

  return null;
}

$error = "";
$product_name = "";
$product_price = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){
  $product_name = trim($_POST["product-name"] ?? "");
  $product_price = trim($_POST["product-price"] ?? "");

  $error = validate_product($product_name, $product_price) ?? "";

  if($error === ""){
    $product_name = ucwords($product_name);
    $product_price = (float) $product_price;

    try{
      $sql = "INSERT INTO products (name, price) VALUES (?, ?)";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, "sd", $product_name, $product_price);
      mysqli_stmt_execute($stmt);

      mysqli_stmt_close($stmt);
      header("Location: products.php");
      exit;
    }
    catch(mysqli_sql_exception $e){
      error_log($e->getMessage());
      if($e->getCode() === 1062){
        $error = "Product already exists.";
      }
      else{
        $error = "Could not save the product please try again.";
      }
    }
  }
}

$sql = "SELECT id, name, price FROM products ORDER BY id";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products</title>
</head>
<body>
  <div class="wrapper">
    <div class="form-wrapper">
      <h2>Add product</h2>

      <?php if($error !== ""): ?>
        <p class="error" role="alert"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form action="./products.php" method="post">
        <input type="text" name="product-name" placeholder="Product name" required>
        <input type="number" name="product-price" step="0.01" min="0" placeholder="Price" required>
        <button type="submit">Add product</button>
      </form>
    </div>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>No.</th>
            <th>Name</th>
            <th>Price</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $num = 1;
          while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?= htmlspecialchars($num++) ?></td>
              <td><?= htmlspecialchars($row["name"]) ?></td>
              <td><?= htmlspecialchars($row["price"]) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>