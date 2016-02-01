<?php
// Start session
session_start();
 
// Include required functions file
require_once('functions.inc.php');
 
// If not logged in, redirect to login screen
// If logged in, unset session variable and display logged-out message
if (check_login_status() == false) {
	// Redirect to 
	redirect('../login.php');
} else {
	// Kill session variables
	unset($_SESSION['logged_in']);
	unset($_SESSION['username']);
	unset($_SESSION['role']);
	unset($_SESSION['userid']);
 
	// Destroy session
	session_destroy();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html;charset=utf-8" />
	<title>Illinois Marching Online Console</title>
	<link rel="stylesheet" type="text/css" href="../css/IMOconsole.css" />
	<link rel="stylesheet" type="text/css" href="../css/anylinkcssmenu.css" />
	<script type="text/javascript" src="../js/anylinkcssmenu.js"></script>
	<script type="text/javascript">
	//anylinkcssmenu.init("menu_anchors_class") ////Pass in the CSS class of anchor links (that contain a sub menu)
	anylinkcssmenu.init("anchorclass")
	</script>
	<script src="../js/equalcolumns.js" type="text/javascript"></script> 
</head>
<body>
<div id="maincontainer">
	
	<!--HEADER-->
	<div id="topsection">
		<div class="innertube">
			<center><img src="http://ilmarching.com/images/Main_Banner.jpg" /></center>
		</div>
	</div>

	<!--MAIN CONTENT-->
	<div id="contentwrapper">
		<div id="contentcolumn">
			<div class="innertube">
				<h1>Logged Out</h1>
				<p>You have successfully logged out. Back to <a href="../login.php">login</a> screen.</p>
			</div>
		</div>
	</div>



	<!--MENU-->
	<div id="leftcolumn">
		<div class="innertube">
			<?php include('../menu.php'); ?>
		</div>
	</div>

	<div id="footer"><?php include('../footer.html');?><div>

</div>

</body>
</html>