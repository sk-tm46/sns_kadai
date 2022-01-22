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

//名前の変更がない場合は同じ名前を入れる
if($_POST['name'] == null)
{
	$_POST['name'] = ($_SESSION['member_name']);
}

$post = sanitize($_POST);
$name = $post['name'];
$comment= $post['comment'];

$dsn = 'mysql:dbname=sns;host=localhost;charset=utf8';
$user = 'updateuser';
$password = '';
$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$sql = "UPDATE mst_user SET name=:name,comment=:comment WHERE user_id=:user_id";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue('comment', $comment, PDO::PARAM_STR);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$dbh = null;

// ステータスコードを出力
http_response_code( 301 );

// リダイレクト
header( "Location: sns_prof.php");
}
catch (Exception $e)
{
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
	exit();
}
?>
