<?php
try
{
ini_set("session.cookie_secure", 1);
session_start();
session_regenerate_id(true);
header("X-XSS-Protection: 1; mode=block");
header("Content-Security-Policy: reflected-xss block");
header( "Location: bbs_main.php" ) ;

}

catch (Exception $e)
{
	print 'ただいま障害により大変ご迷惑をお掛けしております。';
	exit();
}
?>