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
アカウントの登録が完了しました。 <br />

<a href="..\bbs_login\bbs_login.html"> ログイン画面へ </a>

</body>
</html>