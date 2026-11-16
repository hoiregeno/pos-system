<?php
require "./includes/db.php";
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
      <form action="./products.php" method="post">
        <input type="text" name="product-name" placeholder="Product name">
        <input type="number" name="product-price" step="0.01" min="0" placeholder="Price">
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
          <tr>
            <td>1</td>
            <td>Product 1</td>
            <td>5.00</td>
          </tr>
          <tr>
            <td>2</td>
            <td>Product 2</td>
            <td>2.50</td>
          </tr>
          <tr>
            <td>3</td>
            <td>Product 3</td>
            <td>10.00</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>