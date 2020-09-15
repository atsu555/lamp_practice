<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';
require_once MODEL_PATH . 'cart.php';

function insert_purchase_history($db, $user_id){
  $sql = "
  INSERT INTO
  purchase_history(
    user_id
  )
  VALUES ?
  ";

  return execute_query($db, $sql, [$user_id]);
}

function purchase_history($db, $carts){
  if(purchase_carts($db, $carts) === false){
    return false;
  }

  insert_purchase_history($db, $carts[0]['user_id']);
}