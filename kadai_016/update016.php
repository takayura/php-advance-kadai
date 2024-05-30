<?php
$dsn = 'mysql:dbname=php_book_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = '';

if (isset($_POST['submit']) && $_POST['submit'] === 'update') {
  try {
    $pdo = new PDO($dsn, $user, $password);

    $sql_update = "UPDATE books SET book_code = :book_code, book_name = :book_name, price = :price, stock_quantity = :stock_quantity, genre_code = :genre_code WHERE id = :id";
    $stmt_update = $pdo->prepare($sql_update);

    $stmt_update->bindValue(':book_code', $_POST['book_code'], PDO::PARAM_INT);
    $stmt_update->bindValue(':book_name', $_POST['book_name'], PDO::PARAM_STR);
    $stmt_update->bindValue(':price', $_POST['price'], PDO::PARAM_INT);
    $stmt_update->bindValue(':stock_quantity', $_POST['stock_quantity'], PDO::PARAM_INT);
    $stmt_update->bindValue(':genre_code', $_POST['genre_code'], PDO::PARAM_STR);
    $stmt_update->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

    $stmt_update->execute();

    // 更新が完了したらreadページにリダイレクトし、メッセージを表示する
    header("Location: read016.php?message=書籍情報を更新しました。");
    exit;
  } catch (PDOException $e) {
    exit($e->getMessage());
  }
}

// idパラメータの値が存在すれば処理を行う
if (isset($_GET['id'])) {
  try {
    $pdo = new PDO($dsn, $user, $password);

    // idカラムの値をプレースホルダ「:id」に置き換えたSQL文をあらかじめ用意する
    $sql_select_product = 'SELECT * FROM books WHERE id = :id';
    $stmt_select_product = $pdo->prepare($sql_select_product);

    // bindValue()メソッドを使って実際の値をプレースホルダにバインドする割り当てる
    $stmt_select_product->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

    // SQL文を実行する
    $stmt_select_product->execute();

    // SQL文の実行結果を配列で取得する
    $product = $stmt_select_product->fetch(PDO::FETCH_ASSOC);

    // idパラメータの値と同じidのデータが存在しない場合はエラーメッセージを表示して処理を終了する
    if ($product === FALSE) {
      exit('idパラメータの値が不正です。');
    }

    // genresテーブルからvendor_codeカラムのデータを取得するためのSQL文を変数$sql_select_genre_codesに代入する
    $sql_select_genre_codes = 'SELECT genre_code FROM genres';

    // SQL文を実行する
    $stmt_select_genre_codes = $pdo->query($sql_select_genre_codes);

    // SQL文の実行結果を配列で取得する
    $genre_codes = $stmt_select_genre_codes->fetchAll(PDO::FETCH_COLUMN);
  } catch (PDOException $e) {
    exit($e->getMessage());
  }
} else {
  // idパラメータの値が存在しない場合はエラーメッセージを表示して処理を停止する
  exit('idパラメータの値が存在しません。');
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>書籍編集</title>
  <link rel="stylesheet" href="css/style016.css">

  <!-- Google Fontsの読み込み -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
</head>

<body>
  <header>
    <nav>
      <a href="index016.php">書籍管理アプリ</a>
    </nav>
  </header>
  <main>
    <article class="registration">
      <h1>書籍編集</h1>
      <div class="back">
        <a href="read016.php" class="btn">&lt; 戻る</a>
      </div>
      <form action="update016.php?id=<?= $_GET['id'] ?>" method="post" class="registration-form">
        <div>
          <label for="book_code">書籍コード</label>
          <input type="number" id="book_code" name="book_code" value="<?= $product['book_code'] ?>" min="0"
            max="100000000" required>

          <label for="book_name">書籍名</label>
          <input type="text" id="book_name" name="book_name" value="<?= $product['book_name'] ?>" maxlength="50"
            required>

          <label for="price">単価</label>
          <input type="number" id="price" name="price" value="<?= $product['price'] ?>" min="0" max="100000000"
            required>

          <label for="stock_quantity">在庫数</label>
          <input type="number" id="stock_quantity" name="stock_quantity" value="<?= $product['stock_quantity'] ?>"
            min="0" max="100000000" required>

          <label for="genre_code">ジャンルコード</label>
          <select id="genre_code" name="genre_code" required>
            <option disabled selected value>選択してください</option>
            <?php
            // 配列の中身を順番に取り出し、セレクトボックスの選択肢として出力する
            foreach ($genre_codes as $genre_code) {
              // もし変数$genre_codeが商品の仕入先コードの値と一致していれば、selected属性をつけて初期値にする
              if ($genre_code === $product['genre_code']) {
                echo "<option value='{$genre_code}' selected>{$genre_code}</option>";
              } else {
                echo "<option value='{$genre_code}'>{$genre_code}</option>";
              }
            }
            ?>
          </select>
        </div>
        <button type="submit" class="submit-btn" name="submit" value="update">更新</button>
      </form>
    </article>
  </main>
  <footer>
    <p class="copyright">&copy; 書籍管理アプリ All rights reserved.</p>
  </footer>
</body>

</html>