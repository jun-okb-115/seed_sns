<?php

 // function.phpを読み込み
 require('function.php');
 // DB接続
 require('dbconnect.php');

 // ログインチェック
 login_check();

 // delete();

   // ログインチェック
   // 一時間ログインしていない場合、再度ログイン
  // if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
  //   // ログインしている
  //   // ログイン時間の更新
  //   $_SESSION['time'] = time();

  //   //ログインしているユーザーの情報を取得
  $login_sql = 'SELECT * FROM `members` WHERE `member_id`=?';
  $login_data = array($_SESSION['id']);
  $login_stmt = $dbh->prepare($login_sql);
  $login_stmt->execute($login_data);

  $login_member = $login_stmt->fetch(PDO::FETCH_ASSOC);


  //   // ログインしていない時,または時間切れの場合
  // }else{
  //   header('Location: login.php');
  //   exit;
  // }

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
      $data = array($_POST['tweet'],$_SESSION['id'],-1); //???
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
    }
  }
  
  // ページング機能
  // 空の変数を用意
  $page = '';

  // パラメーターが存在していたらページ番号を代入
  if(isset($_GET['page'])){
    $page = $_GET['page'];
  }else{
    $page = 1;
  }
  // 1以下のイレギュラーな数字が入ってたとき、ページを強制的に1とする
  // max カンマ区切りで羅列された数字の中から最大の数字を取得する
  $page = max($page,1);

  // 1ページ分の表示件数を指定
  $page_number = 5;

  // データの件数から最大のページを計算する
  // SQLで計算するデータを取得
  $page_sql = 'SELECT COUNT(*) AS `page_count` FROM `tweets`WHERE `delete_flag`=0';
  $page_stmt = $dbh->prepare($page_sql);
  $page_stmt->execute();

 // 全件取得(論理削除されていないもの)
  $page_count = $page_stmt->fetch(PDO::FETCH_ASSOC);

 // ceil 小数点切り上げ
 // 1~5 1ページ 6~10 2ページ...
  $all_page_number = ceil($page_count['page_count']/ $page_number);

 // パラーメーターのページ番号が最大を超えていれば、強制的に最後のページとする。
 // min カンマ区切りで羅列された数字の中から最小の数字を取得する
  $page = min($page,$all_page_number);

 // 表示するデータの取得開始場所
  $start = ($page - 1) * $page_number;

// 一覧用の投稿全件取得
// テーブル結合
// INNER JOIN とOUTER JOIN(left join とright join)
// INNER JOIN = 両方のテーブルに存在するデータのみ取得
// OUTER JOIN(left join とright join) = 複数のテーブルがあり、それらを結合する際に優先テーブルを一つ決め、そこにある情報は全て表示しながら、他のテーブルの情報に対するデータがあれば表示する
//優先テーブルに指定されると、そのテーブルの情報は全て表示される
// LIMIT = テーブルから取得する範囲を指定
// LIMIT 取得する配列キー

  $tweet_sql = "SELECT `tweets`.*,`members`.`nick_name`,`members`.`picture_path` FROM `tweets` LEFT JOIN `members` ON `tweets`.`member_id`= `members`.`member_id` WHERE `delete_flag`=0 ORDER BY `tweets`.`modified` DESC LIMIT ".$start.",".$page_number;

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


 // like数を取得するSQL文
   $like_sql = 'SELECT COUNT(*) as `like_cnt` FROM `likes` WHERE `tweet_id`=?';
   $like_data = array($tweet['tweet_id']);
   $like_stmt = $dbh->prepare($like_sql);
   $like_stmt->execute($like_data);
// 各投稿ごとのいいね
   $like_count = $like_stmt->fetch(PDO::FETCH_ASSOC);

// 1行分のデータに新しいキーを用意し、$like_countを代入
   $tweet['like_cnt'] = $like_count['like_cnt'];

// ログインしている人がlikeしているかどうかのデータ判定
   $login_like_sql = 'SELECT COUNT(*)as `login_count` FROM `likes` WHERE `member_id`=? AND `tweet_id`=?';
   $login_like_data = array($_SESSION['id'],$tweet['tweet_id']);
   $login_like_stmt = $dbh->prepare($login_like_sql);
   $login_like_stmt->execute($login_like_data);

// フェッチで取得
   $login_like_number = $login_like_stmt->fetch(PDO::FETCH_ASSOC);

// ログインしているユーザーがいいねしているかどうか判定
    $tweet['login_like_flag'] = $login_like_number['login_count'];

   $tweet_list[] = $tweet;
  }

  // 自分がフォローしているユーザーの数
  $following_sql = 'SELECT COUNT(*) as `following_count` FROM `follows` WHERE `member_id`=?';
  $following_data = array($_SESSION['id']);
  $following_stmt = $dbh->prepare($following_sql);
  $following_stmt->execute($following_data);
  $following = $following_stmt->fetch(PDO::FETCH_ASSOC);

  // 自分がフォローされているユーザーの数
  $follower_sql = 'SELECT COUNT(*) as `follower_count` FROM `follows` WHERE `follower_id`=?';
  $follower_data = array($_SESSION['id']);
  $follower_stmt = $dbh->prepare($follower_sql);
  $follower_stmt->execute($follower_data);
  $follower = $follower_stmt->fetch(PDO::FETCH_ASSOC);

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
        <legend>ようこそ<?php echo $login_member['nick_name'];?>さん&nbsp;<img src="picture_path/<?php echo $login_member['picture_path'];?>" width="48" height="45"></legend>
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
<!-- pageing -->
          <ul class="paging">
            <input type="submit" class="btn btn-info" value="つぶやく">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php if($page == 1) { ?>
                <li>前</li>
                <?php } else{ ?>
                <li><a href="index.php?page=<?php echo $page -1 ?>" class="btn btn-default">前</a></li>
                <?php }?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php if($page == $all_page_number){ ?>
                <li>次</li>
                <?php } else { ?>
                <li><a href="index.php?page=<?php echo $page +1 ?>" class="btn btn-default">次</a></li>
                <?php } ?>
                <li><?php echo $page;?> / <?php echo $all_page_number;?></li>
                
          </ul>
        </form>
      </div>
<!-- 全件取得 -->
      <div class="col-md-8 content-margin-top">
<!-- フォロー、フォロワーの表示 -->
        <div class="msg_header">
          <a href="follow.php">Followers<span class="badge badge-pill badge-default"><?php echo $follower['follower_count']; ?></span></a>
          <a href="following.php">Followings<span class="badge badge-pill badge-default"><?php echo $following['following_count']; ?></span></a>
        </div>

        <?php foreach ($tweet_list as $tweet ) { ?>
        <div class="msg">
          <img src="picture_path/<?php echo $tweet['picture_path'];?>" width="48" height="48">
          <p>
            <?php echo $tweet['tweet']; ?><span class="name"> 『<?php echo $tweet['nick_name'];?>』</span>
            <?php if($_SESSION['id'] !== $tweet['member_id'] ) {?>
            [<a href="reply.php?tweet_id=<?php echo $tweet['tweet_id']; ?>">Re</a>]
            <?php }?>
<!-- いいね -->
            <?php if($tweet['login_like_flag'] == 0){ ?>
            <a href="like.php?like_tweet_id=<?php echo $tweet['tweet_id']; ?>&page=<?php echo $page; ?>"><i class="fa fa-thumbs-o-up"></i>いいね</a>
            <?php } else { ?>

            <a href="like.php?dislike_tweet_id=<?php echo $tweet['tweet_id']; ?>&page=<?php echo $page; ?>"><i class="fa fa-thumbs-o-down">BAD</i></a>
            <?php } ?>
<!-- いいね数の表示 -->
            <a href="like_user.php?tweet_id=<?php echo $tweet['tweet_id']; ?>&page=<?php echo $page?>" style="color: green;">いいね: <?php echo $tweet['like_cnt']; ?> 個</a>

          </p>
          <p class="day">
            <a href="view.php?tweet_id=<?php echo $tweet['tweet_id']; ?>">
              <?php echo $tweet['modified']; ?>
            </a>
<!-- パラメーター -->
<?php if($_SESSION['id'] == $tweet['member_id'] ) {?>
            [<a href="edit.php?tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: #00994C;">編集</a>]
            [<a href="delete.php?tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: #F33;" onclick="return confirm('本当に削除しますか？');">削除</a>]
             <?php } ?>
             <!-- 返信もとのメッセージの詳細 -->
             <?php if($tweet['reply_tweet_id'] >=1) {?>
            <a href="view.php?tweet_id=<?php echo $tweet['reply_tweet_id']; ?>" style="color: #a9a9a9">返信もとのメッセージを表示</a>
           <?php }?>
            
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
  </body>
</html>
