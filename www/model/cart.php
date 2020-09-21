<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  return fetch_all_query($db, $sql, [$user_id]);
}

function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";

  return fetch_query($db, $sql, [$user_id, $item_id]);

}

function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES (?, ?, ?)
  ";

  return execute_query($db, $sql, [$item_id, $user_id, $amount]);
}

function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  return execute_query($db, $sql, [$amount, $cart_id]);
}

function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  return execute_query($db, $sql, [$cart_id]);
}

function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  $db->beginTransaction();
  foreach($carts as $cart){
    if(update_item_stock(
        $db,
        $cart['item_id'],
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }

  delete_user_carts($db, $carts[0]['user_id']);
  purchase_history_details($db, $carts);

  if(has_error() === true){
    $db->rollback();
  }else{
    $db->commit();
  }
}

function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  execute_query($db, $sql, [$user_id]);
}


function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

function insert_purchase_history($db, $user_id){
  $sql = "
    INSERT INTO
      purchase_history(
        user_id
      )
    VALUES (?);
  ";
  return execute_query($db, $sql, [$user_id]);
}

function purchase_history_details($db, $carts){
  // 購入履歴の追加と明細の追加
  if(insert_purchase_history($db, $carts[0]['user_id']) === false){
    return false;
  }
  $order_id = $db->lastInsertId();
  foreach($carts as $cart){
    insert_details($db, $order_id, $cart['item_id'], $cart['price'], $cart['amount']);
  }
}


function insert_details($db, $order_id, $item_id, $price, $amount){
  $sql = "
    INSERT INTO
      details(
        order_id,
        item_id,
        price,
        amount
      )
    VALUES (?, ?, ?, ?);
  ";

  return execute_query($db, $sql, [$order_id, $item_id, $price, $amount]);
}

function get_purchase_history($db, $user_id = null){
  $params = array();
  $sql = "
    SELECT
      purchase_history.order_id,
      purchase_history.order_date,
      SUM(details.price * details.amount) as total
    FROM
      purchase_history
    JOIN
      details
    ON
      purchase_history.order_id = details.order_id";
    if($user_id !== null){
      $params[] = $user_id;
      $sql .= " WHERE user_id = ?";
    }
    $sql .= " GROUP BY
      details.order_id
  ";

  return fetch_all_query($db, $sql, $params);
}

function get_details($db, $order_id, $user_id = null){
  $params = array($order_id);
  $sql = "
    SELECT
      items.name,
      details.price,
      details.amount,
      details.price*details.amount as subtotal
    FROM
      items
    JOIN
      details
    ON
      items.item_id = details.item_id
    WHERE
      details.order_id = ?
  ";
  if($user_id !== null){
    $sql .= " AND exists(SELECT * FROM purchase_history WHERE order_id = ? AND user_id = ?)";
    $params[] = $order_id;
    $params[] = $user_id;
  }

  return fetch_all_query($db, $sql, $params);
}

function get_purchase_history_detail_view($db, $order_id){
  $sql = "
    SELECT
      purchase_history.order_date,
      SUM(details.price * details.amount) as total
    FROM
      purchase_history
    JOIN
      details
    ON
      purchase_history.order_id = details.order_id
    WHERE
      details.order_id = ".$order_id."
    GROUP BY
      details.order_id
  ";

  return fetch_all_query($db, $sql);
}

