<?php
try
{
ini_set("session.cookie_secure", 1);
session_start();
header("X-XSS-Protection: 1; mode=block");
header("Content-Security-Policy: reflected-xss block");
require_once('../common/common.php');

$post=sanitize($_POST);
$sns_comment=$post['comment'];
$sns_photo=$_FILES['photo'];

$search_str = "&gt;&gt;";
$result = strpos($sns_comment , $search_str);
$i = $result;
$sns_replyno = "";

if($result === false)
{
	$sns_replyno = 0;
}
else
{
	$i = $i + 8;
	$no = "";
	while(true)
	{
		//i文字目を取り出す
		$reply_judg = mb_substr($sns_comment, $i, 1);
		//数字か判定
		if(is_numeric($reply_judg))
		{
			$no.=$reply_judg;
			$i = $i + 1;
		}
		else
		{
			//数字でなくなったら抜ける
			break;
		}
	}
	$sns_replyno = $no;
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

$sql = 'INSERT INTO mst_sns(comment,image) VALUES (:comment,:photo)';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':comment', $sns_comment, PDO::PARAM_STR);
$stmt->bindValue(':photo', $sns_photo, PDO::PARAM_STR);
$stmt->execute();

$dbh = null;

// ステータスコードを出力
http_response_code( 301 ) ;
// リダイレクト
header( "Location: sns_main.php" ) ;
exit();
}

catch (Exception $e)
{
	print $e;
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
	exit();
}
?>