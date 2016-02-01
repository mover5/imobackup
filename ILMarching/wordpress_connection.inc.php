<?php
	$username = "ilmarch_wordpres";
	$password = "rFCMCzeTCBcQ9hNU";
	$host = "localhost";
	$db = "ilmarch_wordpress";
	
	$connect = mysql_connect($host, $username, $password);
	mysql_select_db($db, $connect);

?>
