<?php 
ini_set("session.cookie_secure", 1);
session_start();
session_regenerate_id(true); 
header("X-XSS-Protection: 1; mode=block");
header("Content-Security-Policy: reflected-xss block");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="refresh" content="30">
<title> ふれあい掲示板 </title>
</head>
<body>
<?php
if(isset($_SESSION['member_login'])==false)
{
	print '<a href="..\sns_login\sns_login.html">ユーザー名またはパスワードが間違っています。</a><br/>';
	exit();
}
else
{
	print $_SESSION['member_name'];
	print 'さんログイン中<br/>';
} ?>
<input type="button" onclick="location.href='sns_logout.php'" value="ログアウト"><br/>
<?php print $_SESSION['member_name']; ?>
さん
<a href="..\sns_user\sns_prof.php">プロフィール画面へ</a><br/>
<br/>
<?php
try{
$dsn = 'mysql:dbname=sns;host=localhost;charset=utf8';
$user = 'selectuser';
$password = '';
$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//TLに表示するフォロワーを取得
$sql = 'SELECT follow_id FROM mst_follow WHERE user_id = :user_id';
$user_id = $_SESSION['member_login'];
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
//フォロワーがいない場合
$cnt = 0;
while(true)
{
	$rec = $stmt->fetch(PDO::FETCH_ASSOC);
	if($rec==false)
	{		
		break;
	}
	$follow_id[$cnt] = $rec['follow_id'];
	//フォロワーがいる場合
	$cnt = $cnt + 1;
}
//自分を追加
$follow_id[$cnt] = $user_id;

//ツイートを取得
$sql = 'SELECT * FROM mst_sns WHERE user_id in (';
$sql.=substr(str_repeat(',?',count($follow_id)),1);
$limit = ') ORDER BY no DESC LIMIT 50';
$sql.= $limit;
$stmt = $dbh->prepare($sql);
$stmt->execute($follow_id);

$dbh = null;

$count = 0;
$sns_post_count = 0;

require_once('../common/common.php');

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
	$sns_post_replyno[$count] = $rec['replyno'];
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
	$sns_post_count = count($sns_post_no);
}
}
catch (Exception $e)
{
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
	exit();
}
?>
TYS掲示板<br/>
<input type="button" onclick="location.href='sns_input.php'" value="投稿画面へ"><br/><br/>
<input type="button" onclick="location.href='../sns_search/sns_word_search.php'" value="投稿検索">
　<input type="button" onclick="location.href='../sns_search/sns_prof_search.php'" value="プロフィール検索">
<?php if($sns_post_count == 0): ?>
<?php else: ?>
<table border="1" cellpadding="10">

<?php
for($i=0;$i<$sns_post_count;$i++)
{
?>
<tr>
<td><div class="text">
<?php
	if($sns_post_replyno[$i] != 0)
	{
	$dsn = 'mysql:dbname=sns;host=localhost;charset=utf8';
	$user = 'selectuser';
	$password = '';
	$dbh = new PDO($dsn, $user, $password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

	$sql = 'SELECT user_id FROM mst_sns WHERE no = :no';
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(':no', $sns_post_replyno[$i], PDO::PARAM_INT);
	$stmt->execute();
	$replyid = $stmt->fetch(PDO::FETCH_ASSOC);
	$replyuser_id = $replyid['user_id'];

	$sql = 'SELECT name FROM mst_user WHERE user_id = :user_id';
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(':user_id', $replyuser_id, PDO::PARAM_INT);
	$stmt->execute();
	$replyname = $stmt->fetch(PDO::FETCH_ASSOC);
	$replyname = $replyname['name'];
	$dbh = null;
	print $replyname;
	print 'さんの投稿No';
	print $sns_post_replyno[$i];
	print 'へ返信です。<br/>';
	}//if文終わり
	
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
$sns_result = $sns_post_no[$i] . $sns_kanma . $username. $sns_kanma . $sns_post_date[$i] ;

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
<br/>
<input type="submit" formaction="sns_main.php" name="reload" value="最新を読み込む"><br/>
</body>
</html>