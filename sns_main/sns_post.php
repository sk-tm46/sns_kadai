<?php
try
{
ini_set("session.cookie_secure", 1);
session_start();
header("X-XSS-Protection: 1; mode=block");
header("Content-Security-Policy: reflected-xss block");
require_once('../common/common.php');

$post=sanitize($_POST);
$bbs_comment=$post['comment'];
$bbs_photo=$_FILES['photo'];
$bbs_name=$_SESSION['member_name'];

$search_str = "&gt;&gt;";
$result = strpos($bbs_comment , $search_str);
$i = $result;
$bbs_replyno = "";

if($result === false)
{
	$bbs_replyno = 0;
}
else
{
	$i = $i + 8;
	$no = "";
	while(true)
	{
		//i文字目を取り出す
		$reply_judg = mb_substr($bbs_comment, $i, 1);
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
	$bbs_replyno = $no;
}


if( $bbs_photo != null)
{
	move_uploaded_file($bbs_photo['tmp_name'],'./gazou/'.$bbs_photo['name']);
	$bbs_photo=$bbs_photo['name'];
}
else
{
	$bbs_photo='';
}

$dsn = 'mysql:dbname=bbs;host=localhost;charset=utf8';
$user = 'insertuser';
$password = '';
$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$sql = 'INSERT INTO mst_bbs(comment,image,name,replyno) VALUES (:comment,:photo,:name,:replyno)';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':comment', $bbs_comment, PDO::PARAM_STR);
$stmt->bindValue(':photo', $bbs_photo, PDO::PARAM_STR);
$stmt->bindValue(':name', $bbs_name, PDO::PARAM_STR);
$stmt->bindValue(':replyno', $bbs_replyno, PDO::PARAM_STR);
$stmt->execute();

$dbh = null;

// ステータスコードを出力
http_response_code( 301 ) ;
// リダイレクト
header( "Location: bbs_main.php" ) ;
exit();
}

catch (Exception $e)
{
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
	exit();
}
?>