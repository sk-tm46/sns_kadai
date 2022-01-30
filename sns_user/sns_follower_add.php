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
フォロー完了 <br />
<br />
フォローが完了しました。 <br />
<?php	
$prof_id = $_SESSION['follower'];
?>
<form action ="sns_prof.php" method ="post">
<input type ="hidden" name = "prof_id" value ="<?=$prof_id?>">
<input type ="submit" name ="submit" value ="戻る"><br/><br/>
</form>
</body>
</html>