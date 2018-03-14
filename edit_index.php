<?php
  session_start();
  require('dbconnect.php');

  // GET送信で送られてきた値で該当する投稿を取得
  // GET送信はURLを参照する
  $sql = 'SELECT * FROM `tweets` WHERE `tweet_id`=?';
  $data = array($_GET['tweet_id']);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);
  $post = $stmt->fetch(PDO::FETCH_ASSOC); // 一件の表示なので繰り返す必要なし

  // 値の確認
  // var_dump($post);

  // POST送信された時
  if (!empty($_POST)) {
    $sql = 'UPDATE `tweets` SET  `tweet`=?, `created`=NOW() WHERE `tweet_id`=?';
    $data = array($_POST['tweet'], $_GET['tweet_id']);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    // header = 遷移先を指定して、強制的に遷移する
    // header('Location: 遷移先');
    header('Location: index.php');
    // それ以降の処理を中断
    exit;
  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>edit_index.php</title>
  <!-- CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="assets/css/form.css">
    <link rel="stylesheet" href="assets/css/timeline.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
  <!-- ナビゲーションバー -->
  <nav id="top" class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="bbs.php"><span class="strong-title"><i class="fa fa-linux"></i>Oneline bbs</span></a>
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

  <!-- Bootstrapのcontainer -->
  <div class="container">
    <!-- Bootstrapのrow -->
    <div class="row">
      <!-- 画面左側 -->
       <div class="col-xs-12 col-md-12 col-lg-12" style="margin-top: 5%;">
        <div class="post-index">
          <span class="post-title">
            <b style="font-size: 20px;">POST</b>
          </span>
          <div class="content">
            <form action="" method="POST">
              
              <input type="text" name="tweet" value="<?php echo $post['tweet']; ?>">

              <input type="submit" class="btn btn-success" value="更新">
              <a href="bbs.php" class="btn btn-info">戻る</a>
          </form>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>