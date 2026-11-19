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

$error = "";

if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET["id"])){
  $error = add_to_cart($conn, $_GET["id"]) ?? "";

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
      <button type="submit">Complete Sale</button>
    </form>
  </div>
</div>
<?php require "./includes/footer.php" ?>