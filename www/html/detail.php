<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';


session_start();

if(is_logined() === false){
    redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);
$order_id = get_get('order_id');

// データ作成処理を呼び出す
if(is_admin($user)){
    $details = get_details($db, $order_id);
}else{
    $details = get_details($db, $order_id, $user['user_id']);
}
$histories = array();
if(count($details) > 0){
    $histories = get_purchase_history_detail_view($db, $order_id);
}


include_once VIEW_PATH . 'detail_view.php';