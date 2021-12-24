<?php
ini_set("session.cookie_secure", 1);
session_start();
header("X-XSS-Protection: 1; mode=block");
header("Content-Security-Policy: reflected-xss block");
$_SESSION=array();
if(isset($_COOKIE[session_name()])==true)
{
	setcookie(session_name(),'',time()-42000,'/');
}
session_destroy();

// ステータスコードを出力
http_response_code( 301 ) ;
// リダイレクト
header( "Location: ..\bbs_login\bbs_login.html" ) ;
exit();
?>