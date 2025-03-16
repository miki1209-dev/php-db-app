<?php
$dsn = 'mysql:dbname=yvuxfgw17qqisayl;host=nkpl8b2jg68m87ht.cbetxkdyhwsb.us-east-1.rds.amazonaws.com;charset=utf8mb4';
$name = 'vy7g5a3wqyoxuvk2';
$password = 'bzfgl6ua6h3t9cwd';

try {
  $pdo = new PDO($dsn, $name, $password);
  $sql_delete = 'DELETE FROM products WHERE id = :id';

  $stmt_delete = $pdo->prepare($sql_delete);
  $stmt_delete->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

  $stmt_delete->execute();

  $count = $stmt_delete->rowCount();
  $message = "商品を{$count}件削除しました。";

  header("Location: read.php?message={$message}");
} catch (PDOException $e) {
  exit($e->getMessage());
}
