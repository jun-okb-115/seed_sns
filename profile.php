<?php 
 require('function.php');
 login_check();
 require('dbconnect.php');

 if(!empty($_GET)){

$sql = 'SELECT * FROM `members` WHERE `member_id`=?';
$data = array($_GET['member_id']);
$stmt = $dbh->prepare($sql);
$stmt->execute($data);

$iine_user = $stmt->fetch(PDO::FETCH_ASSOC);
 }

// $iine_users[] = $iine_user;

// // 投稿内容
//    $tweet_sql = 'SELECT * FROM `tweets` LEFT JOIN `members`ON `tweets`.`member_id`=`members`.`member_id` WHERE `tweet_id`=?';
//    $tweet_data = array($_GET['tweet_id']);
//    $tweet_stmt = $dbh->prepare($tweet_sql);
//    $tweet_stmt->execute($tweet_data);
// // 一件のみ
//    $tweet = $tweet_stmt->fetch(PDO::FETCH_ASSOC);

//  echo '<br>';
// echo '<br>';
// var_dump($tweet);
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
                <li><a href="logout.html">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 col-md-offset-4 content-margin-top">
        <div class="msg">
         <!--  <a href="like_user.php?tweet_id=<?php echo $tweet['tweet_id']; ?>"> -->
          <img src="picture_path/<?php echo $iine_user['picture_path']; ?>" width="100" height="100">
         <!--  </a> -->
          

          <p>ID : <span class="name"> <?php echo $iine_user['member_id']; ?> </span></p>


          <p>投稿者 : <span class="name"> <?php echo $iine_user['nick_name']; ?> </span></p>
          </p>
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
