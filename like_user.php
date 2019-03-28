<?php
 require('function.php');
 require('dbconnect.php');

 login_check();
 

 if(!empty($_GET)){
// 投稿内容
   $tweet_sql = 'SELECT * FROM `tweets` LEFT JOIN `members`ON `tweets`.`member_id`=`members`.`member_id` WHERE `tweet_id`=?';
   $tweet_data = array($_GET['tweet_id']);
   $tweet_stmt = $dbh->prepare($tweet_sql);
   $tweet_stmt->execute($tweet_data);
// 一件のみ
   $tweet = $tweet_stmt->fetch(PDO::FETCH_ASSOC);


// この投稿にいいねしているユーザーの情報が欲しい
   $like_sql = 'SELECT `likes`.*,`members`.`nick_name`,`members`.`picture_path` FROM `likes` LEFT JOIN `members` ON `likes`.`member_id`= `members`.`member_id` WHERE `tweet_id`=?';
   $like_data = array($_GET['tweet_id']);
   $like_stmt = $dbh->prepare($like_sql);
   $like_stmt->execute($like_data);

   $likers = array();

   while (true) {
   	 $liker = $like_stmt->fetch(PDO::FETCH_ASSOC);

   	 if($liker == false){
   	 	break;
   	 }
   	 $likers[] = $liker;
   	}
   	if ($likers == false) {
    	header('Location: index.php');
    	exit;
    }

   	 // var_dump($likers);exit;

   	 foreach ($likers as $like_user) {
   	 	$sql = 'SELECT `members`.`member_id`,`members`.`nick_name`,`members`.`picture_path` FROM `members` WHERE `member_id`=?';
   	 	$data = array($like_user['member_id']);
        $stmt = $dbh->prepare($sql);
        $stmt->execute($data);

        $like = $stmt->fetch(PDO::FETCH_ASSOC);
        $likes[] = $like;
   	 }

// var_dump($likes);
   	 // いいねをしているユーザーの数
   	 $like_user_count = count($likes);

    }else{
    	header('Location: index.php'.$page);
    	exit;
    }

  
 ?>
 <!DOCTYPE html>
 <html lang="ja">
 <head>
 	<meta charset="UTF-8">
 	<title>like_user.php</title>

 	<!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

 </head>
 <body>

 	<div class="container">
    <div class="row">
      <div class="col-md-4 col-md-offset-4 content-margin-top">
        <div class="msg">
    <?php foreach ($likes as $like) { ?>
    <a href="profile.php?member_id=<?php echo $like['member_id']; ?>">
        <p>
          <img src="picture_path/<?php echo $like['picture_path'];?>" width="48" height="48">
      </a>
        </p>

        <p>ID : <span class="name"> <?php echo $like['member_id']; ?> </span></p>

        <p>
            <span class="name"><?php echo $like['nick_name'];?>さんがいいねしました。</span>
        </p>
        <hr>
        <?php }?>
          
      </div>
    </div>
  </div>


   <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
 </body>
 </html>