<?php
  require('function.php');
  login_check();
  require('dbconnect.php');
  // ログインしているユーザーの情報取得
  $login_sql = 'SELECT * FROM `members` WHERE `member_id`=?';
  $login_data = array($_SESSION['id']);
  $login_stmt = $dbh->prepare($login_sql);
  $login_stmt->execute($login_data);
  $login_member = $login_stmt->fetch(PDO::FETCH_ASSOC);
  // var_dump($login_member);

  // ログインしているユーザーをフォローしているユーザーの情報取得
  $sql = 'SELECT `follower_id` FROM `follows` WHERE `member_id`=?';
  $data = array($_SESSION['id']);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);
  // データがない時のエラーを防ぐ
  $followings = array();

  // ある件数分だけ処理を回す
  while(true) {
    $following = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($following == false) {
      break;
    }
    $followings[] = $following;
  }
  $following_comp = array();
  foreach ($followings as $following) {
    $fl_sql = 'SELECT * FROM `members` WHERE `member_id`=?';
    $fl_data = array($following['follower_id']);
    $fl_stmt = $dbh->prepare($fl_sql);
    $fl_stmt->execute($fl_data);
    $following = $fl_stmt->fetch(PDO::FETCH_ASSOC);    
    $following_comp[] = $following;
  }
// echo '<br>';
// echo('<pre>');
// var_dump($followings);
// echo('</pre>');
// echo('<pre>');
// echo count($followings);
// echo('</pre>');

// フォロー解除処理
// フォロー解除が押されtら時のデータをfollowsテーブルから削除
if(isset($_GET['unfollow_id'])){
  $unfl_sql = 'DELETE FROM `follows` WHERE `member_id`=? AND `follower_id`=?';
    $unfl_data = array($_SESSION['id'],$_GET['unfollow_id']);
    $unfl_stmt = $dbh->prepare($unfl_sql);
    $unfl_stmt->execute($unfl_data);

     header('Location: following.php');
    exit;
}
 

  // echo '<br>';
  // echo '<br>';
  // var_dump($following_count);


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
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-3 content-margin-top">
        <img src="picture_path/<?php echo $login_member['picture_path'] ?>" width="250" height="250">
        <h3></h3>
        <a href="#" class="btn btn-info col-md-12" style="font-size: 24px">
          <?php echo $login_member['nick_name']; ?>
        </a>
        <br>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>

      <div class="col-md-9 content-margin-top">
        <div class="msg_header">
        <a href="follow.php">Followers<span class="badge badge-pill badge-default"></span></a>
        <a href="following.php">Following<span class="badge badge-pill badge-default"><?php echo count($following_comp);?></span></a>
        </div>

    <?php foreach($following_comp as $followers){ ?>
        <div class="msg">

          <img src="picture_path/<?php echo $followers['picture_path']; ?>" width="48" height="48">
          <p><span class="name"><?php echo $followers['nick_name']; ?>
          </span></p>

          <a href="following.php?unfollow_id=<?php echo $followers['member_id']; ?>">
          <button class="btn btn-default">フォロー解除</button>
          </a>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>