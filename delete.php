<?php
session_start();
 require('dbconnect.php');

if(isset($_GET)){
 $sql = 'UPDATE `tweets` SET `delete_flag`=1 WHERE `tweet_id`=?';
 $data = array($_GET['tweet_id']);
 $stmt = $dbh->prepare($sql);
 $stmt->execute($data);

header('Location: index.php');
exit;
}
 ?>

 