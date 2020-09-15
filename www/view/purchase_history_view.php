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

    <?php if(count($items) > 0){ ?>
      <table class="table table-bordered text-center">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>user_id</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($items as $item){ ?>
          <tr class="<?php print(is_open($item) ? '' : 'close_item'); ?>">
            <td><img src="<?php print(IMAGE_PATH . h($items['image']));?>" class="item_image"></td>
            <td><?php print(h($item['name'])); ?></td>
            <td><?php print(number_format(h($item['price']))); ?>円</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>商品はありません。</p>
    <?php } ?>
  </div>
  <script>
    $('.delete').on('click', () => confirm('本当に削除しますか？'))
  </script>
</body>
</html>