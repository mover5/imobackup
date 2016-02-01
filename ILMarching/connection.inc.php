<?php
	$username = "ilmarch_libraria";
	$password = "ilmarching";
	$host = "localhost";
	$db = "ilmarch_library";
	
	$connect = mysql_connect($host, $username, $password);
	mysql_select_db($db, $connect);

?>
