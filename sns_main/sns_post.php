<?php
try
{
ini_set("session.cookie_secure", 1);
session_start();
header("X-XSS-Protection: 1; mode=block");
header("Content-Security-Policy: reflected-xss block");
require_once('../common/common.php');

$post=sanitize($_POST);
$sns_replyno=$post['replyno'];
$sns_comment=$post['comment'];
$sns_photo=$_FILES['photo'];

if($sns_replyno == null)
{
	 $sns_replyno = 0;
}
else
{
	$replyno = mb_convert_kana($sns_replyno, 'n','UTF-8');
	if(is_numeric($replyno))
	{
		//数字なら何もしない
	}
	else
	{
		print "数字を入力してください。";
		print '<a href="sns_main.php">メイン画面に戻る</a><br/>';
		exit();
	}
}

if( $sns_photo != null)
{
	move_uploaded_file($sns_photo['tmp_name'],'./gazou/'.$sns_photo['name']);
	$sns_photo=$sns_photo['name'];
}
else
{
	$sns_photo='';
}

$dsn = 'mysql:dbname=sns;host=localhost;charset=utf8';
$user = 'insertuser';
$password = '';
$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$sql = 'INSERT INTO mst_sns(comment,image,replyno,user_id) VALUES (:comment,:photo,:replyno,:user_id)';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':comment', $sns_comment, PDO::PARAM_STR);
$stmt->bindValue(':photo', $sns_photo, PDO::PARAM_STR);
$stmt->bindValue(':replyno', $sns_replyno, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $_SESSION['member_login'], PDO::PARAM_INT);
$stmt->execute();

$dbh = null;

// ステータスコードを出力
http_response_code( 301 ) ;
// リダイレクト
header( "Location: sns_main.php" );
exit();
}

catch (Exception $e)
{
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
	exit();
}
?>