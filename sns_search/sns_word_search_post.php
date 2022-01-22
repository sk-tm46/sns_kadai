<?php
ini_set("session.cookie_secure", 1);
session_start();
header("X-XSS-Protection: 1; mode=block");
header("Content-Security-Policy: reflected-xss block");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title> ふれあい掲示板 </title>
</head>
<body>
<?php
print $_SESSION['member_name'];
print 'さんログイン中<br/>';
?>
<a href="..\sns_main\sns_logout.php">
<input type="button" value="ログアウト"><br/><br/>
</a>
ワード検索結果<br/>
検索ワード：
<?php
require_once('../common/common.php');
$post = sanitize($_POST);
$word = $post['word'];
print $word;
print '<br/>';

try{
$dsn = 'mysql:dbname=sns;host=localhost;charset=utf8';
$user = 'selectuser';
$password = '';
$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$sql = 'SELECT * FROM mst_sns WHERE comment LIKE :word';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':word', '%'. $word .'%', PDO::PARAM_STR);
$stmt->execute();

$dbh = null;
$count = 0;

while(true)
{
	$rec = $stmt->fetch(PDO::FETCH_ASSOC);
	if($rec==false)
	{
		break;
	}
	$rec = sanitize($rec);
	$sns_post_no[$count] = $rec['no'];
	$sns_post_date[$count] = $rec['date'];
	$sns_post_user_id[$count] = $rec['user_id'];
	$rec_comment=sanitize_br($rec['comment']);
	$sns_post_comment[$count] = $rec_comment;
	if($rec['image']=='')
	{
		$sns_post_image[$count]='';
	}
	else
	{
		$sns_post_image[$count]='<img src="gazou/'.$rec['image'].'" width="400" height="250">';
	}
	$count = $count + 1;
}
}
catch (Exception $e)
{
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
	exit();
}
?>
<?php if($count == 0): ?>
<?php else: ?>
<table border="1" cellpadding="10">
<?php
$dsn = 'mysql:dbname=sns;host=localhost;charset=utf8';
$user = 'selectuser';
$password = '';
for($i=0;$i<$count;$i++)
{
?>
<tr>
<td><div class="text">
<?php
$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$sql = 'SELECT name FROM mst_user WHERE user_id = :user_id';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':user_id', $sns_post_user_id[$i], PDO::PARAM_INT);
$stmt->execute();
$getuser = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $getuser['name'];
$dbh = null;

$sns_kanma = ':';
$sns_result = $sns_post_no[$i] . $sns_kanma . $username. $sns_kanma . $sns_post_date[$i];
$prof_id = $sns_post_user_id[$i];
print $sns_result;
print '<br/>';
print $sns_post_comment[$i];
	if($sns_post_image[$i] != "")
	{
	print $sns_post_image[$i];
	}//if文終わり
?>
<form action ="..\sns_user\sns_prof.php" method ="post">
<input type ="hidden" name = "prof_id" value ="<?=$prof_id?>">
<input type ="submit" name ="submit" value ="詳細">
</form>
</div>
</td>
</tr>
<?php
}//for文終わり
?>
</table>
<?php endif; ?>
<input type="button" onclick="history.back()" value="戻る"><br/>
<a href="..\sns_main\sns_main.php">メイン画面へ戻る</a>
</body>
</html>