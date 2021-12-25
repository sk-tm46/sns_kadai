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
<title> ふれあい掲示板 </title>
<link rel="stylesheet" href="popapp.css">
</head>
<body>
<?php
if(isset($_SESSION['member_login'])==false)
{
	$_SESSION['member_login']=1;
	$_SESSION['member_name']='ゲスト';

	print $_SESSION['member_name'];
	print 'さん';
	print '<a href="..\sns_login\sns_login.html"> ログイン </a><br/>';
}
else
{
	print $_SESSION['member_name'];
	print 'さん';
	print '<a href="sns_logout.php"> ログイン中</a><br/>';
} ?>
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
print  $e;
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
	exit();
}
?>
TYS掲示板<br>
コメント
<form method="post" enctype="multipart/form-data">
<textarea name="comment" rows="4" cols="50" wrap="hard"></textarea><br/>
<input type="file" name="photo" id="sFiles" style"width:400px">
<input type="submit" formaction="sns_post.php" name="svpost" value="投稿"><div id="photoMess"></div><br/>
<?php if($sns_post_count == 0): ?>
<?php else: ?>
<table border="1" cellpadding="10">
<?php
for($i=0;$i<$sns_post_count;$i++)
{
$sns_kanma = ':';
$sns_result = $sns_post_no[$i] . $sns_kanma . $sns_kanma . $sns_post_date[$i] ;
?>
<tr>
<td><div class="text"><?php print $sns_result; ?>
<br/>
<?php
print $sns_post_comment[$i];
if($sns_post_image[$i] != "")
{
print $sns_post_image[$i];
}
?></div>
</td>
</tr>
<?php 
} ?>
</table>
<?php endif; ?>
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