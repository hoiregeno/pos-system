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

function delete_product(mysqli $conn, $id): ?string {
  $id = filter_var($id, FILTER_VALIDATE_INT);

  if($id === false){
    return "Invalid product.";
  }

  try{
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
  }catch(mysqli_sql_exception $e){
    error_log($e->getMessage());
    return "Could not delete product.";
  }

  return null;
}

function add_product(mysqli $conn, string $name, string $price): ?string {
  $error = validate_product($name, $price);

  if($error !== null){
    return $error;
  }

  $name = ucwords($name);
  $price = (float) $price;

  try{
    $sql = "INSERT INTO products (name, price) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sd", $name, $price);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
  }
  catch(mysqli_sql_exception $e){
    error_log($e->getMessage());
    if($e->getCode() === 1062){
      return "Product already exists.";
    }

    return "Could not save the product. Please try again.";
  }

  return null;
}

$error = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){
  if(isset($_GET["id"])){
    $error = delete_product($conn, $_GET["id"]) ?? "";
  }
  else{
    $error = add_product($conn,
                         trim($_POST["product-name"] ?? ""), 
                         trim($_POST["product-price"] ?? "")) ?? "";
  }

  if($error === ""){
    header("Location: products.php");
    exit;
  }
}

$sql = "SELECT id, name, price FROM products ORDER BY id";
$result = mysqli_query($conn, $sql);

$page_title = "Products";
require "./includes/header.php";
?>

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
          <th>Action</th>
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
            <td><form action="products.php?id=<?= htmlspecialchars($row["id"]) ?>" method="post">
              <button type="submit" onclick="return confirm('Do you want to delete this product?')">Delete</button>
            </form></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require "./includes/footer.php" ?>