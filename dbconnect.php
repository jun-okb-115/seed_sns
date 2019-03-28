<?php 
echo 'a';
echo 'b';
    $dsn = 'mysql:dbname=seed_sns;host=localhost';
  
    $user = 'root';
    $password = '';

    $dbh = new PDO($dsn,$user,$password);
    $dbh->query('SET NAMES utf8');
 ?>
