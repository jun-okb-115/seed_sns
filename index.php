<?php
session_start();
 require('dbconnect.php');

   // ログインチェック
   // 一時間ログインしていない場合、再度ログイン
  if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    // ログインしている
    // ログイン時間の更新
    $_SESSION['time'] = time();

    //ログインしているユーザーの情報を取得
  $login_sql = 'SELECT * FROM `members` WHERE `member_id`=?';
  $login_data = array($_SESSION['id']);
  $login_stmt = $dbh->prepare($login_sql);
  $login_stmt->execute($login_data);

  $login_member = $login_stmt->fetch(PDO::FETCH_ASSOC);


    // ログインしていない時,または時間切れの場合
  }else{
    header('Location: login.php');
    exit;
  }

  if(!empty($_POST)){

  // 入力チェック
    if($_POST['tweet'] == ''){
      $error['tweet'] = 'blank';
    }
    if(!isset($error)){
      // SQL文作成(INSERT INTO)
      // tweet=つぶやいた内容
      // member_id=ログインした人のid
      // reply_tweet_id=-1
      // created=現在日時。now()を使用
      // modified=現在日時。now()を使用
      $sql = 'INSERT INTO `tweets` SET `tweet`=?, `member_id`=?,`reply_tweet_id`=?,`created`=NOW(),`modified`=NOW()';
      $data = array($_POST['tweet'],$_SESSION['id'],-1);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
    }
  }
  
// 一覧用の投稿全件取得
// テーブル結合
// INNER JOIN とOUTER JOIN(left join とright join)
// INNER JOIN = 両方のテーブルに存在するデータのみ取得
// OUTER JOIN(left join とright join) = 複数のテーブルがあり、それらを結合する際に優先テーブルを一つ決め、そこにある情報は全て表示しながら、他のテーブルの情報に対するデータがあれば表示する
//優先テーブルに指定されると、そのテーブルの情報は全て表示される

  $tweet_sql = 'SELECT * FROM `tweets` LEFT JOIN `members` ON `tweets`.`member_id`= `members`.`member_id` ORDER BY `tweets`.`created` DESC';
  $tweet_stmt = $dbh->prepare($tweet_sql);
  $tweet_stmt->execute();
  
  // からの配列を用意
  $tweet_list = array(); //データがない時のエラーを防ぐ

 // 全件取得
  while(true){
   $tweet = $tweet_stmt->fetch(PDO::FETCH_ASSOC);
   if($tweet == false){
    break;
   }
   $tweet_list[] = $tweet;
  }
 echo '<pre>';
  var_dump($tweet_list);
echo '</pre>';
  // var_dump($_SESSION);

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
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
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
      <div class="col-md-4 content-margin-top">
        <legend>ようこそ<?php echo $login_member['nick_name'];?>さん</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"></textarea>
                <?php if(isset($error) && $error['tweet'] == 'blank'){ ?>
                <p class="error">* つぶやき内容を入力してください。</p>

                <?php } ?>
              </div>
            </div>
          <ul class="paging">
            <input type="submit" class="btn btn-info" value="つぶやく">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <li><a href="index.html" class="btn btn-default">前</a></li>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <li><a href="index.html" class="btn btn-default">次</a></li>
          </ul>
        </form>
      </div>

      <div class="col-md-8 content-margin-top">
        <?php foreach ($tweet_list as $tweet ) { ?>
          
        <div class="msg">
          <img src="picture_path/<?php echo $login_member['picture_path'];?>" width="48" height="48">
          <p>
            <?php echo $tweet['tweet']; ?><span class="name"><?php echo $login_member['nick_name'];?></span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.html">
              <?php echo $tweet['modified']; ?>
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div>
         <?php } ?>
        </div>
      

    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </bo
</html>
