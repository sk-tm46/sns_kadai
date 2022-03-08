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
プロフィール<br/>
<?php
try
{
require_once('../common/common.php');

$prof_id = $_SESSION['member_login'];

//コメント検索から詳細を開いた場合
if(isset($_POST['prof_id']))
{
	$post = sanitize($_POST);
	$prof_id = $post['prof_id'];
}

//ユーザーの情報取得
$dsn = 'mysql:dbname=sns;host=localhost;charset=utf8';
$user = 'selectuser';
$password = '';
$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$sql = 'SELECT name,comment FROM mst_user WHERE user_id=:user_id';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':user_id', $prof_id, PDO::PARAM_INT);
$stmt->execute();

$rec = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = 'SELECT * FROM mst_sns WHERE user_id=:user_id ORDER BY no DESC';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':user_id', $prof_id, PDO::PARAM_INT);
$stmt->execute();

$dbh = null;
$count = 0;

while(true)
{
	$rec2 = $stmt->fetch(PDO::FETCH_ASSOC);
	if($rec2==false)
	{
		break;
	}
	$rec2 = sanitize($rec2);
	$sns_post_no[$count] = $rec2['no'];
	$sns_post_date[$count] = $rec2['date'];
	$sns_post_user_id[$count] = $rec2['user_id'];
	$rec2_comment=sanitize_br($rec2['comment']);
	$sns_post_comment[$count] = $rec2_comment;
	if($rec2['image']=='')
	{
		$sns_post_image[$count]='';
	}
	else
	{
		$sns_post_image[$count]='<img src="../sns_main/gazou/'.$rec2['image'].'" width="400" height="250">';
	}
	$count = $count + 1;
}

print $_SESSION['member_name'];
print 'さんログイン中<br/>';
}
catch (Exception $e)
{
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
	exit();
}
?>
<a href="..\sns_main\sns_logout.php">
<input type="button" value="ログアウト"><br/><br/>
</a>
ユーザー名<br/>
<?php
$rec = sanitize($rec);
$user_name = $rec['name'];
$user_comment = $rec['comment'];

print $user_name;
print '<br/><br/>';
?>
自己紹介<br/>
<?php
print $user_comment;
print '<br/> ';

if($prof_id == $_SESSION['member_login'])
{
	print '<input type="button" onclick="location.href=\'sns_prof_edit.php\'" value="編集"><br/><br/>';
}
else
{
?>
	<form action ="sns_follower_check.php" method ="post">
	<input type ="hidden" name = "prof_id" value ="<?=$prof_id?>">
	<input type ="submit" name ="submit" value ="フォロー">
	</form>
<?php
}
?>
投稿<br/>
<?php if($count == 0): ?>
<?php else: ?>
<table border="1" cellpadding="10">
<?php
for($i=0;$i<$count;$i++)
{
?>
<tr>
<td><div class="text">
<?php
//名前取得する
$dsn = 'mysql:dbname=sns;host=localhost;charset=utf8';
$user = 'selectuser';
$password = '';
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
print $sns_result;
print '<br/>';
print $sns_post_comment[$i];
	if($sns_post_image[$i] != "")
	{
	print $sns_post_image[$i];
	}//if文終わり
?>
</div>
</td>
</tr>
<?php
}//for文終わり
?>
</table>
<?php endif; ?>

<a href="..\sns_main\sns_main.php">メイン画面へ</a>
</body>
</html>