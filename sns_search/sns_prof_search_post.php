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
$name = $post['name'];
print $name;
print '<br/>';

try{
$dsn = 'mysql:dbname=sns;host=localhost;charset=utf8';
$user = 'selectuser';
$password = '';
$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$sql = 'SELECT user_id,name,comment FROM mst_user WHERE name LIKE :name';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':name', '%'. $name .'%', PDO::PARAM_STR);
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
	$sns_post_no[$count] = $rec['user_id'];
	$sns_post_name[$count] = $rec['name'];
	$rec_comment=sanitize_br($rec['comment']);
	$sns_post_comment[$count] = $rec_comment;

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
$prof_id = $sns_post_no[$i];
print $sns_post_name[$i];
print '<br/>';
print $sns_post_comment[$i];
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