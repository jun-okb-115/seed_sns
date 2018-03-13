<?php
session_start();
 require('dbconnect.php');

if(isset($_GET)){
  $sql = 'SELECT * FROM `tweets` WHERE `tweet_id`=?';
  $data = array($_GET['tweet_id']);
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);
  $tweet_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// var_dump($tweet_edit);
if (!empty($_POST)) {

	if($_POST['tweet'] == ''){
		$error['tweet'] = 'blank';
	}
if(!isset($error)){


    $sql = 'UPDATE `tweets` SET  `tweet`=?, `modified`=NOW() WHERE `tweet_id`=?';
    $data = array($_POST['tweet'], $_GET['tweet_id']);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

     header('Location: index.php');
    // それ以降の処理を中断
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
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <h4>つぶやき編集</h4>
        <div class="msg">
          <form method="POST" action="" class="form-horizontal" role="form">

          	<textarea name="tweet" cols="50" rows="5" class="form-control" value="<?php echo $tweet_edit['tweet']; ?>"  ></textarea>
             <?php if(isset($error['tweet']) && $error['tweet'] = 'blank') { ?>
              <p class="error">* tweetを入力してください。</p>
              <?php } ?>
            <ul class="paging">
              <input type="submit" class="btn btn-info" value="更新">
            </ul>
          </form>
        </div>
        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>