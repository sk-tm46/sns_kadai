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
<br/>
投稿画面<br/><br/>
<form method="post" name="user_comment" enctype="multipart/form-data" action="sns_post.php" onsubmit="return checkcommentInfo()">
返信したいレス番を入力してください。<br/>
<input type="text" name="replyno"><br/><br/>
投稿内容を入力してください。<br/><div id="comment"></div>
<textarea name="comment" rows="4" cols="50" wrap="hard"></textarea><br/>
<input type="file" name="photo" id="sFiles" style"width:400px"><br/>
<br/>
<input type="submit" name="svpost" value="投稿"><div id="photoMess"></div><br/>

<script>
function checkcommentInfo()
{
var comment = document.user_comment.comment.value;
var comment_len = comment.length;

//投稿の文字数チェック
if(comment_len >= 257)
{
	document.getElementById("comment").innerText = "256字以内で入力してください。";
	return false;
}

//ファイルサイズ取得
var fileList = document.getElementById("sFiles").files;
var list = "";
for(var i=0; i<fileList.length; i++){
list += "[" + fileList[i].size + " bytes]" + fileList[i].name + "<br/>";
}
if(list != null){
	if( list > 10000)
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