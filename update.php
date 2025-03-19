<?php
$dsn = 'mysql:dbname=php_db_app;host=localhost;charset=utf8mb4';
$name = 'root';
$password = 'root';
if (isset($_GET['id'])) {
  try {
    $pdo = new PDO($dsn, $name, $password);
    // idカラムの値をプレースホルダ（:id）に置き換えたSQL文をあらかじめ用意する
    $sql_select_product = 'SELECT * FROM products WHERE id = :id';
    $stmt_select_product = $pdo->prepare($sql_select_product);

    $stmt_select_product->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt_select_product->execute();

    $product = $stmt_select_product->fetch(PDO::FETCH_ASSOC);

    if ($product === FALSE) {
      exit('パラメータの値が不正です');
    }

    $sql_select_vendor_codes = 'SELECT vendor_code FROM vendors';
    $stmt_select_vendor_codes = $pdo->query($sql_select_vendor_codes);

    $vendor_codes = $stmt_select_vendor_codes->fetchAll(PDO::FETCH_COLUMN);
  } catch (PDOException $e) {
    exit($e->getMessage());
  }
} else {
  exit('idパラメータの値が存在しません。');
}

if (isset($_POST['submit'])) {
  try {
    $pdo = new PDO($dsn, $name, $password);
    $sql_update = '
      UPDATE products
      SET product_code = :product_code,
      product_name = :product_name,
      price = :price,
      stock_quantity = :stock_quantity,
      vendor_code = :vendor_code
      WHERE id = :id
    ';

    $stmt_update = $pdo->prepare($sql_update);

    $stmt_update->bindValue(':product_code', $_POST['product_code'], PDO::PARAM_INT);
    $stmt_update->bindValue(':product_name', $_POST['product_name'], PDO::PARAM_STR);
    $stmt_update->bindValue(':price', $_POST['price'], PDO::PARAM_INT);
    $stmt_update->bindValue(':stock_quantity', $_POST['stock_quantity'], PDO::PARAM_INT);
    $stmt_update->bindValue(':vendor_code', $_POST['vendor_code'], PDO::PARAM_INT);
    $stmt_update->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

    $stmt_update->execute();

    $count = $stmt_update->rowCount();

    $message = "商品を{$count}件編集しました。";

    header("Location: read.php?message={$message}");
  } catch (PDOException $e) {
    exit($e->getMessage());
  }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>商品編集</title>
  <link rel="stylesheet" href="css/style.css">

  <!-- Google Fontsの読み込み -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
</head>

<body>
  <?php include 'header.php'; ?>
  <main>
    <article class="registration">
      <h1>商品編集</h1>
      <div class="back">
        <a href="read.php" class="btn">&lt; 戻る</a>
      </div>
      <form action="update.php?id=<?= $_GET['id'] ?>" method="post" class="registration-form">
        <div>
          <label for="product_code">商品コード</label>
          <input type="number" id="product_code" name="product_code" value="<?= $product['product_code'] ?>" min="0" maxlength="100000000" required>

          <label for="product_name">商品名</label>
          <input type="text" id="product_name" name="product_name" value="<?= $product['product_name'] ?>" maxlength="50" required>

          <label for="price">単価</label>
          <input type="number" name="price" id="price" min="0" value="<?= $product['price'] ?>" max="100000000" required>

          <label for="stock_quantity">在庫数</label>
          <input type="number" id="stock_quantity" name="stock_quantity" value="<?= $product['stock_quantity'] ?>" min="0" max="100000000" required>

          <label for="vendor_code">仕入先コード</label>
          <select name="vendor_code" id="vendor_code" required>
            <option disabled selected value>選択してください</option>
            <?php
            foreach ($vendor_codes as $vendor_code) {
              if ($vendor_code === $product['vendor_code']) {
                echo "<option value='{$vendor_code}' selected>{$vendor_code}</option>";
              } else {
                echo "<option value='{$vendor_code}'>{$vendor_code}</option>";
              }
            }
            ?>
          </select>
        </div>
        <button type="submit" class="submit-btn" name="submit" value="update">編集</button>
      </form>
    </article>
  </main>
  <?php include 'footer.php'; ?>
</body>

</html>