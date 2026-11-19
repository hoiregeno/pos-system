<?php

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