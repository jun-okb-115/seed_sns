<?php
session_start();
 require('dbconnect.php'); 

if(!empty($_POST)){
 
 if($_POST['tweet'] == ''){
 	$error['tweet'] = 'blank';
 }
 if(!isset($error)){
 	$sql = 'INSERT INTO `tweets` SET `tweet`=?,`member_id`=?,`reply_tweet_id`=?,`created`=NOW(),`modified`=NOW()';
 	$data = array($_POST['tweet'],$_SESSION['id'],-1);
 	$stmt = $dbh->prepare($sql);
 	$stmt->execute($data);
 }

}


 ?>
