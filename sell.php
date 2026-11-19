<?php
session_start();
require "./includes/db.php";
require "./includes/functions.php";

function add_to_cart($conn, $id): ?string {
  $id = filter_var($id, FILTER_VALIDATE_INT);

  if($id === false){
    return "Invalid product.";
  }

  if(get_product($conn, $id) === null){
    return "Product not found.";
  }

  if(isset($_SESSION["cart"][$id])){
    $_SESSION["cart"][$id]++;
  }
  else{
    $_SESSION["cart"][$id] = 1;
  }

  return null;
}

function complete_sale($conn, $cart): ?string {
  if(empty($cart)){
    return "Cart is empty.";
  }

  $items = [];
  $total = 0;

  foreach($cart as $id => $qty){
    $product = get_product($conn, $id);

    if($product === null){
      return "Product not found.";
    }

    $total += $product["price"] * $qty;
    $items[] = [
      "product_id" => $id,
      "quantity" => $qty,
      "price" => $product["price"]
    ];
  }

  try{
    // 1. start the transaction
    mysqli_begin_transaction($conn);

    // 2. insert the total into sales
    $sql = "INSERT INTO sales (total) VALUES (?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "d", $total);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // 3. get the new sale id
    $sale_id = mysqli_insert_id($conn);

    // 4. insert one sale_items row for each entry in $items
    $sql = "INSERT INTO sale_items (sale_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    // 5. commit
    foreach($items as $item){
      mysqli_stmt_bind_param($stmt, "iiid", $sale_id, $item["product_id"], $item["quantity"], $item["price"]);
      mysqli_stmt_execute($stmt);
    }

    mysqli_stmt_close($stmt);
    mysqli_commit($conn);
  }
  catch(mysqli_sql_exception $e){
    // roll back, log the error, return a general message
    mysqli_rollback($conn);
    error_log($e->getMessage());
    return "Could not complete the sale. Please try again.";
  }

  // clear the cart, then return null
  unset($_SESSION["cart"]);

  return null;
}

$error = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){
  if(isset($_GET["id"])){
    $error = add_to_cart($conn, $_GET["id"]) ?? "";
  }
  else{
    $error = complete_sale($conn, $_SESSION["cart"] ?? []) ?? "";
  }

  if($error === ""){
    header("Location: sell.php");
    exit;
  }
}

$sql = "SELECT id, name, price FROM products ORDER BY id";
$result = mysqli_query($conn, $sql);

$cart = $_SESSION["cart"] ?? [];
$total = 0;

$page_title = "Sell";
require "./includes/header.php";
?>

<div class="wrapper">
  <div class="product-grid">
    <?php if($error !== ""): ?>
      <p class="error" role="alert"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php while($row = mysqli_fetch_assoc($result)): ?>
      <div class="product-card">
        <h2><?= htmlspecialchars($row["name"]) ?></h2>
        <p><?= htmlspecialchars($row["price"]) ?></p>

        <form action="sell.php?id=<?= htmlspecialchars($row["id"]) ?>" method="post">
          <button type="submit">Add</button>
        </form>
      </div>
    <?php endwhile; ?>
  </div>
  <div class="panel-wrapper">
    <h1>Current sale</h1>

    <?php if(empty($cart)): ?>
      <p>No items yet.</p>  
    <?php else: ?>
      <?php foreach($cart as $id => $qty):
        $product = get_product($conn, $id);

        if($product === null){
          continue;
        }

        $line_total = $product["price"] * $qty;
        $total += $line_total;
      ?>
        <p>
          <?= htmlspecialchars($product["name"]) ?>
          x<?= htmlspecialchars($qty) ?>
          K<?= number_format($line_total, 2) ?>
        </p>
      <?php endforeach; ?>
    <?php endif; ?>

    <p>Total: <?= number_format($total, 2) ?></p>

    <form action="sell.php" method="post">
      <button type="submit" name="complete-sale">Complete Sale</button>
    </form>
  </div>
</div>

<?php require "./includes/footer.php" ?>