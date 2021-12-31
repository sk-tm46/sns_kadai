<?php
try
{

require_once('../common/common.php');

$post = sanitize($_POST);
$sns_name=$post['name'];
$sns_pass=$post['pass'];

$sns_pass=md5($sns_pass);

$dsn = 'mysql:dbname=sns;host=localhost;charset=utf8';
$user = 'selectuser';
$password = '';
$dbh = new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$sql='SELECT user_id,name FROM mst_user WHERE name=:name AND pass=:pass';
$stmt=$dbh->prepare($sql);
$stmt->bindValue(':name', $sns_name, PDO::PARAM_STR);
$stmt->bindValue(':pass', $sns_pass, PDO::PARAM_STR);
$stmt->execute();

$dbh = null;

$rec = $stmt->fetch(PDO::FETCH_ASSOC);

if($rec==false)
{
print $sns_pass;
	print 'ユーザー名またはパスワードが間違っています。<br />';
	print '<a href="sns_login.html">戻る</a>';
}
else
{
	ini_set("session.cookie_secure", 1);
	session_start();
	$_SESSION['member_login']=$rec['user_id'];
	$_SESSION['member_name']=$rec['name'];
	header("X-XSS-Protection: 1; mode=block");
	header("Content-Security-Policy: reflected-xss block");
	header('Location: ..\sns_main\sns_session.php');
	exit();
}
}

catch (Exception $e)
{
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
	exit();
}
?>