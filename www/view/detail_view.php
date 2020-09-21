<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>商品管理</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'admin.css'); ?>">
</head>
<body>
<?php
  include VIEW_PATH . 'templates/header_logined.php';
  ?>

  <div class="container">
    <h1>購入明細</h1>
    <p>注文番号:<?php print $order_id; ?>
    <?php foreach($histories as $history){ ?>
    <p>購入日時:<?php print(h($history['order_date']));?></p>
    <p>合計金額:<?php print(h($history['total']));?></p>
    <?php } ?>
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

      <table class="table table-bordered text-center">
        <thead class="thead-light">
          <tr>
            <th>商品名</th>
            <th>商品価格</th>
            <th>購入数</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($details as $detail){ ?>
          <tr class="<?php print(is_open($item) ? '' : 'close_item'); ?>">
            <td><?php print(h($detail['name']));?></td>
            <td><?php print($detail['price']); ?></td>
            <td><?php print($detail['amount']); ?></td>
            <td><?php print(number_format(h($detail['subtotal']))); ?>円</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
  </div>
  <script>
    $('.delete').on('click', () => confirm('本当に削除しますか？'))
  </script>
</body>
</html>