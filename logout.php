<?php
session_start();
 require('dbconnect.php');

// セッションの中身をからの配列で上書き
 $_SESSION = array();

 // セッションの情報を有効期限切れにする
if(ini_get('session.use_cookies')){
	$params = session_get_cookie_params();
	setcookie(session_name(),'',time() -42000,$params['path'],$params['domain'],$params['secure'],$params['httponly']);
}
 // セッションの情報を破棄
session_destroy();

 // cookieの情報も削除
setcookie('email','',time()-3000);
setcookie('password','',time()-3000);

 // ログイン後の画面に戻る
header('Location: index.php');
exit;

 //ログイン後の画面に行くことによってしっかりログアウト機能が実装されているか確認するため
 ?>