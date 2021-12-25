<?php
try
{
ini_set("session.cookie_secure", 1);
session_start();
header("X-XSS-Protection: 1; mode=block");
header("Content-Security-Policy: reflected-xss block");

require_once('../common/common.php');

$post = sanitize($_POST);
$sns_pass = $_POST['pass'];
$_POST['pass'] = md5($sns_pass);

$post=$_POST;
$sns_name=$post['name'];
$sns_pass=$post['pass'];

$dsn = 'mysql:dbname=sns;host=localhost;charset=utf8';
$user = 'insertuser';
$password = '';
$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$sql = "INSERT INTO mst_user(name,pass) VALUES (:name,:pass)";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':name', $sns_name, PDO::PARAM_STR);
$stmt->bindValue(':pass', $sns_pass, PDO::PARAM_STR);
$stmt->execute();

$dbh = null;

// ステータスコードを出力
http_response_code( 301 ) ;

// リダイレクト
header( "Location: sns_post_done.php" ) ;
exit();
}

catch (Exception $e)
{
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
	exit();
}
?>