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
プロフィール編集<br/> <br/>
<?php
try
{
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
<form method="post" name="user_edit" action="sns_prof_edit_post.php" onsubmit="return checkUserInfo()">
ユーザー名を入力してください。<br/><div id="rename"></div>
<input type="text" name="name"><br/><br/>
自己紹介<br/><div id="reintroduction"></div>
<textarea name="introduction" rows="6" cols="70" wrap="hard"></textarea><br/>
<input type="submit" value="更新"><br/><br/>
<input type="button" onclick="history.back()" value="戻る"><br/>

<a href="..\sns_main\sns_main.php">メイン画面へ戻る</a>

<script>
function checkUserInfo()
{
	var name = document.user_edit.name.value;
	var introduction = document.user_edit.introduction.value;
	var name_len = name.length;
	var introduction_len = introduction.length;

if(name_len >= 31)
{
	document.getElementById("rename").innerText = "30字以内で入力してください。";
	return false;
}
else if(name.length != Array.from(name).length)
{
	document.getElementById("rename").innerText = "不正な値が入力されています。";
	return false;
}
else if(introduction_len >= 257)
{
	document.getElementById("reintroduction").innerText = "256字以内で入力してください。";
	return false;
}

}
</script>
</form>
</body>
</html>