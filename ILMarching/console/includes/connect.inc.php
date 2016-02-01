<?php


function connectToDB() {
	$username = "ilmarch_libraria";
	$password = "ilmarching";
	$database = "ilmarch_library";
	$host = "localhost";
	$connection = mysql_connect($host, $username, $password);
	
	mysql_select_db($database, $connection);
	
	return $connection;
}
?>
