<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴画面</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'admin.css'); ?>">
</head>
<body>
  <?php
  include VIEW_PATH . 'templates/header_logined.php'; 
  ?>

  <div class="container">
    <h1>購入履歴</h1>

    <?php include VIEW_PATH . 'templates/messages.php'; ?>

      <table class="table table-bordered text-center">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($histories as $history){ ?>
          <tr class="<?php print(is_open($item) ? '' : 'close_item'); ?>">
            <td><?php print(h($history['order_id']));?></td>
            <td><?php print($history['order_date']); ?></td>
            <td><?php print(number_format(h($history['SUM(details.price)']))); ?>円</td>
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