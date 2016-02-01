<?php
session_start();

require_once('includes/functions.inc.php');

if (check_login_status() == false) {
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/IMOconsole.css" />
<link rel="stylesheet" type="text/css" href="css/anylinkcssmenu.css" />
<script src="js/equalcolumns.js" type="text/javascript"></script> 
<script type="text/javascript" src="js/anylinkcssmenu.js"></script>
<script type="text/javascript">
//anylinkcssmenu.init("menu_anchors_class") ////Pass in the CSS class of anchor links (that contain a sub menu)
anylinkcssmenu.init("anchorclass")
</script>
</head>

<body>

<div id="maincontainer">	
	<!--HEADER-->
	<div id="topsection">
		<div class="innertube">
			<?php include('header.html'); ?>
		</div>
	</div>

	<!--MAIN CONTENT-->
	<div id="contentwrapper">
		<div id="contentcolumn">
			<div class="innertube">
				<h1>Illinois Marching Online Console</h1>
				Welcome to the Illinois Marching Online Console. This console was made from the ground up to be an easy to use way of adding content to Illinois Marching Online. 
				<br>With this console you can add, modify, or remove content from the site. If you have any questions, comments, or problems, feel free to email me at
				moverholt@ilmarching.com. 
				<p> I have added a new date function in the Scores and Festivals area. You no longer have to manually type in the date in the correct format. They are now drop-down<br>
				boxes. If there are any problems, email me.
				<p> <b>If you have not changed your password from the default value, please do so via the link in the menu. It is important to try and keep this area as secure as possible.</b></p>
				</div>
		</div>
	</div>
	<!--MENU-->
	<div id="leftcolumn">
		<div class="innertube">
			Welcome, <?php echo ucwords($username);?><br />
			<?php echo date("F d, Y");?><br />
			<?php echo ucwords($role) . " - <a href=includes/logout.inc.php><font color=#FFFFFF>logout</font></a>";?>
			<?php include("menu.php"); ?>
		</div>	
	</div>

	<div id="footer"><?php include('footer.html');?><div>
</div>

</body>
</html>
