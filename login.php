<?php
  session_start();
  require('dbconnect.php');

  // COOKIEにログイン情報が保存されていたら自動でログイン
  if (isset($_COOKIE['email']) && !empty($_COOKIE['email'])) {
    $_POST['email'] = $_COOKIE['email'];
    $_POST['password'] = $_COOKIE['password'];

    // COOKIEが保存されていたらログインした時間を更新する = COOKIEの保存期間を更新する
    $_POST['save'] = 'on';
  }

  // ログインボタンが押された時
  // $_POSTはarray型なのでPOST送信されていない時は要素が0個の配列
  // その場合、isset関数ではnullではなく、0個の配列があるということでtrueになる
  if (!empty($_POST)) {
    // ログイン認証処理
    // sha1で入力されたものをまた暗号化し、暗号化したものと一致するものを探す
    $sql = 'SELECT * FROM `members` WHERE `email`=? AND `password`=?';
    $data = array($_POST['email'], sha1($_POST['password']));
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($member == false) {
      // 認証失敗
      $error['login'] = 'failed';
    } else {
      // 認証成功
      // 1.セッション変数にmember_idを保存
      // ログインしているユーザーの判定のため定義する必要がある
      $_SESSION['id'] = $member['member_id'];

      // 2.ログインした時間をセッションに保存
      // time = 今の時間
      $_SESSION['time'] = time();

      // 3.自動ログイン
      if ($_POST['save'] == 'on') {
        // setcookie(保存したいカラム名、保存したい値、今の時間から保存したい時間まで : 秒数) = $_COOKIEに値をセットする
        setcookie('email', $_POST['email'], time()+60*60*24*14);
        setcookie('password', $_POST['password'], time()+60*60*24*14);
      }

      // 4.ログイン後の画面に遷移
      header('Location: index.php');
      exit;
    }
  }
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.html"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <legend>ログイン</legend>
        <form method="post" action="" class="form-horizontal" role="form">
          <!-- メールアドレス -->
          <div class="form-group">
            <label class="col-sm-4 control-label">メールアドレス</label>
            <div class="col-sm-8">
              <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com">
            </div>
          </div>
          <!-- パスワード -->
          <div class="form-group">
            <label class="col-sm-4 control-label">パスワード</label>
            <div class="col-sm-8">
              <input type="password" name="password" class="form-control" placeholder="">
            </div>
          </div>
          <!-- 自動ログイン -->
          <div class="form-group">
            <label class="col-sm-4 control-label">自動ログイン</label>
            <div class="col-sm-8">
              <input type="checkbox" name="save" value="on">オンにする
            </div>
          </div>
          <?php if(isset($error['login']) && $error['login'] == 'failed') { ?>
            <p class="error">* emailかパスワードが間違っています。</p>
          <?php } ?>
          <input type="submit" class="btn btn-default" value="ログイン">
        </form>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>