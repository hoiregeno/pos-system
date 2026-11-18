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