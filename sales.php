<?php
require "includes/db.php";

$sql = "SELECT id, total, created_at FROM sales
        ORDER BY id DESC";
$result = mysqli_query($conn, $sql);


$page_title = "Sales";
require "./includes/header.php";
?>

<div class="wrapper">
  <div class="table-wrapper">
    <?php if(mysqli_num_rows($result) === 0): ?>
      <p>No sales yet.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Sale No.</th>
            <th>Date</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?= htmlspecialchars($row["id"]) ?></td>
              <td><?= htmlspecialchars($row["created_at"]) ?></td>
              <td><?= htmlspecialchars(number_format($row["total"], 2)) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<?php require "./includes/footer.php" ?>