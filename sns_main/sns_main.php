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
<meta http-equiv="refresh" content="300">
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
<br/>
<?php
try{
$dsn = 'mysql:dbname=sns;host=localhost;charset=utf8';
$user = 'selectuser';
$password = '';
$dbh = new PDO($dsn, $user, $password);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$sql = 'SELECT * FROM mst_sns WHERE :num LIMIT :no';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':num', 1, PDO::PARAM_INT);
$stmt->bindValue(':no', 50, PDO::PARAM_INT);
$stmt->execute();

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
<form method="post" enctype="multipart/form-data">
コメント<br/>
<textarea name="comment" rows="4" cols="50" wrap="hard"></textarea><br/>
<input type="file" name="photo" id="sFiles" style"width:400px"><br/><br/>
返信したいレス番を入力してください。<br/>
<input type="text" name="replyno"><br/><br/>
<input type="submit" formaction="sns_post.php" name="svpost" value="投稿"><div id="photoMess"></div><br/>
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
<script>
function checkPhotoInfo()
{
//ファイルサイズ取得
var fileList = document.getElementById("sFiles").files;
var list = "";
for(var i=0; i<fileList.length; i++){
list += "[" + fileList[i].size + " bytes]" + fileList[i].name + "<br/>";
}
if(list != null){
	if( list > 1000000)
	{
	document.getElementById("photoMess").innerText = "画像が大き過ぎます。";
	return false;
	}
}
}
</script>
</form>
</body>
</html>