<?php
require "./includes/db.php";
require "./includes/functions.php";

function get_product($conn, $id): ?array {
  $id = filter_var($id, FILTER_VALIDATE_INT);

  if($id === false){
    return null;
  }

  try{
    $sql = "SELECT name, price FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $row;
  }
  catch(mysqli_sql_exception $e){
    error_log($e->getMessage());
    return null;
  }
}

function update_product($conn, $id, $name, $price): ?string {
  
}

if($_SERVER["REQUEST_METHOD"] === "POST"){
  
}

$id = $_GET["id"] ?? "";
$product = get_product($conn, $id);

if($product === null){
  header("Location: products.php");
  exit;
}

$page_title = "Edit product";
require "./includes/header.php";
?>

<div class="wrapper">
  <div class="form-wrapper">
    <h2>Edit product</h2>

    <form action="edit.php" method="post">
      <input type="text" name="product-name" placeholder="Product name" value="<?= htmlspecialchars($product['name']) ?>" required>
      <input type="number" name="product-price" step="0.01" min="0" placeholder="Price" value="<?= htmlspecialchars($product['price']) ?>" required>
      <div class="btn-wrapper">
        <button type="submit">Save changes</button>
        <a href="products.php">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php require "./includes/footer.php" ?>