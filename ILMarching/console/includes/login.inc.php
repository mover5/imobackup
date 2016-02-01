<?php
require_once('connect.inc.php');
require_once('functions.inc.php');

session_start();

if ($_SESSION['logged_in'] == true) {
	redirect('../index.php');
} else {
	if ( (!isset($_POST['username'])) || (!isset($_POST['password'])) OR
	     (!ctype_alnum($_POST['username'])) ) {
		redirect('../login.php');
	}
	
	$connect = connectToDB();
	
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	$query = "SELECT * FROM Users WHERE Name='$username' AND Password=SHA('$password')";
	//echo $query;
	$result = mysql_query($query);
	
	if (mysql_num_rows($result) == 1) {
		// Kill session variables
		unset($_SESSION['logged_in']);
		unset($_SESSION['username']);
		unset($_SESSION['role']);
		unset($_SESSION['userid']);
		$_SESSION['logged_in'] = true;
		$row = mysql_fetch_array($result);
		$_SESSION['role'] = $row['Role'];
		$_SESSION['username'] = $row['Name'];
		$_SESSION['userid'] = $row['UserID'];
		unset($_SESSION['error']);
		redirect('../index.php');
	} else {
		$_SESSION['error'] = "Incorrect username or password. Please try again";
		redirect('../login.php');
	}
}

?>
