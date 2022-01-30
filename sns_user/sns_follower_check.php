<?php
ini_set("session.cookie_secure", 1);
session_start();
header("X-XSS-Protection: 1; mode=block");
header("Content-Security-Policy: reflected-xss block");
?>
<?php
try
{
require_once('../common/common.php');
$user = sanitize($_SESSION);
$user_id =$user['member_login'];
$post = sanitize($_POST);
$prof_id = $post['prof_id'];

$dsn = 'mysql:dbname=sns;host=localhost;charset=utf8';
$user = 'selectuser';
$password = '';
$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$sql = 'SELECT * FROM mst_follow WHERE user_id=:user_id AND follow_id=:follow_id';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':follow_id', $prof_id, PDO::PARAM_INT);
$stmt->execute();

$dbh = null;

$rec = $stmt->fetch(PDO::FETCH_ASSOC);

if($rec==false)
{
	//フォローをする
	$user = 'insertuser';
	$dbh = new PDO($dsn, $user, $password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$sql = "INSERT INTO mst_follow(user_id,follow_id) VALUES (:user_id,:follow_id)";
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
	$stmt->bindValue(':follow_id', $prof_id, PDO::PARAM_STR);
	$stmt->execute();

	$dbh = null;
	$_SESSION['follower'] = $prof_id;
	header('Location: sns_follower_add.php');
	exit();
}
else
{
	//フォローを外す
	$user = 'deleteuser';
	$dbh = new PDO($dsn, $user, $password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$sql = 'DELETE FROM mst_follow WHERE user_id=:user_id AND follow_id=:follow_id';
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
	$stmt->bindValue(':follow_id', $prof_id, PDO::PARAM_INT);
	$stmt->execute();

	$dbh = null;
	$_SESSION['follower'] = $prof_id;
	header('Location: sns_follower_delete.php');
	exit();
}

}
catch (Exception $e)
{	print $e;
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
	exit();
}
?>