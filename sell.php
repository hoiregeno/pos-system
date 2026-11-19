<?php
require "./includes/db.php";

$sql = "SELECT id, name, price FROM products ORDER BY id";
$result = mysqli_query($conn, $sql);

$page_title = "Sell";
require "./includes/header.php";
?>
<div class="wrapper">
  <div class="product-grid">
    <?php while($row = mysqli_fetch_assoc($result)): ?>
      <div class="product-card">
        <h2><?= htmlspecialchars($row["name"]) ?></h2>
        <p><?= htmlspecialchars($row["price"]) ?></p>
      </div>
    <?php endwhile; ?>
  </div>
  <div class="panel-wrapper">
    <h1>Current sale</h1>
    <p>Total: 0.00</p>

    <form action="sell.php" method="post">
      <button type="submit">Complete Sale</button>
    </form>
  </div>
</div>
<?php require "./includes/footer.php" ?>