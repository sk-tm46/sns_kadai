<?php
try
{

require_once('../common/common.php');

$post = sanitize($_POST);
$bbs_name=$post['name'];
$bbs_pass=$post['pass'];

$bbs_pass=md5($bbs_pass);

$dsn = 'mysql:dbname=bbs;host=localhost;charset=utf8';
$user = 'selectuser';
$password = '';
$dbh = new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$sql='SELECT name FROM mst_user WHERE name=:name AND pass=:pass';
$stmt=$dbh->prepare($sql);
$stmt->bindValue(':name', $bbs_name, PDO::PARAM_STR);
$stmt->bindValue(':pass', $bbs_pass, PDO::PARAM_STR);
$stmt->execute();

$dbh = null;

$rec = $stmt->fetch(PDO::FETCH_ASSOC);

if($rec==false)
{
print $bbs_pass;
	print 'ユーザー名またはパスワードが間違っています。<br />';
	print '<a href="bbs_login.html">戻る</a>';
}
else
{
	ini_set("session.cookie_secure", 1);
	session_start();
	$_SESSION['member_login']=1;
	$_SESSION['member_name']=$rec['name'];
	header("X-XSS-Protection: 1; mode=block");
	header("Content-Security-Policy: reflected-xss block");
	header('Location: ..\bbs_main\bbs_session.php');
	exit();
}
}

catch (Exception $e)
{
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
	exit();
}
?>