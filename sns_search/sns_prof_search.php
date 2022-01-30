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
プロフィール検索<br/>
検索したいユーザー名を入力してください。
<br/>
<br/>
<form method="post" action="sns_prof_search_post.php">
<input type="text" size="40" name="name" ><br/>
<input type="submit" value="検索"><br/><br/>

<a href="..\sns_main\sns_main.php">メイン画面へ戻る</a>
</form>
</body>
</html>