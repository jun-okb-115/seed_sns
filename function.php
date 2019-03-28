<?php
 session_start();
 
 // 関数とは、一定の処理をまとめて名前をつけて置いているプログラムの塊
 // 何度も同じ処理を行うときに便利

//ログインチェックの関数
 function login_check(){
 // 関数内で使う必要がある
 	

    if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
 // ログインしている
 // ログイン時間の更新
    $_SESSION['time'] = time();

 
 // ログインしていない時,または時間切れの場合
  } else{
    header('Location: login.php');
    exit;
  }
 }

// ツイートの削除
 function delete(){

 	if(true){
 	require('dbconnect.php');

 	if(isset($_GET)){
 $sql = 'UPDATE `tweets` SET `delete_flag`=1 WHERE `tweet_id`=?';
 $data = array($_GET['tweet_id']);
 $stmt = $dbh->prepare($sql);
 $stmt->execute($data);

header('Location: index.php');
exit;
}
 }
  }


// いいね押された時の処理
  function like(){
    require('dbconnect.php');

  	if(isset($_GET['like_tweet_id'])){
  		

   $like_sql = 'INSERT INTO `likes` SET `member_id`=?,`tweet_id`=?';
   $like_data = array($_SESSION['id'],$_GET['like_tweet_id']);
   $like_stmt = $dbh->prepare($like_sql);
   $like_stmt->execute($like_data);

    header('Location: index.php?page='.$_GET['page']);
    exit;
  }
}

// BAD押された時の処理(DELETE)
  function dislike(){
    require('dbconnect.php');

    if(isset($_GET['dislike_tweet_id'])){
      

    $dislike_sql = 'DELETE FROM `likes` WHERE `member_id`=? AND`tweet_id`=? LIMIT 1';
    $dislike_data = array($_SESSION['id'],$_GET['dislike_tweet_id']);
    $dislike_stmt = $dbh->prepare($dislike_sql);
    $dislike_stmt->execute($dislike_data);

     header('Location: index.php?page='.$_GET['page']);
    exit;

    }
  }

// フォローを押された時
  

// いいね一覧
  
//   function like_user(){
//     if(isset($_GET)){
// // !!!!!
//     $like_user_sql = 'SELECT `members`.`nick_name`,`members`.`picture_path` FROM `likes` LEFT JOIN `members` ON `likes`.`member_id`=`members`.`member_id`  WHERE `tweet_id`=?';
//     $like_user_data = array($_GET['tweet_id']);
//     $like_user_stmt = $dbh->prepare($like_usersql);
//     $like_user_stmt->execute($like_user_data);

//     $like_user = $like_user_stmt->fetch(PDO::FETCH_ASSOC);


//     }
//   }
 ?>