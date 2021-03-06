<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');

if (check_login_status() == false || get_login_role() != "admin") {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
}

if (isset($_POST['reset'])) {
	$connect = connectToDB();
	//Reset Scores
	$query = "TRUNCATE TABLE BandsOfTheYear";
	mysql_query($query);
	
	$query = "TRUNCATE TABLE BandsOfTheYearPublic";
	mysql_query($query);
	
	$query = "TRUNCATE TABLE BandsOfTheYearVotersPublic";
	mysql_query($query);
	
	//Reset Voters
	$query = "UPDATE BandsOfTheYearVoters SET Voted = 0";
	mysql_query($query);
	
	$message = "Bands of the Year data reset!";
} else {
	$message = "YOU WILL LOSE ALL BOTY DATA IF YOU RESET. DO NOT DO THIS LIGHTLY!";
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
				<?php if (isset($message)) {
					echo "<center><font size=+1 color=#FF0000>" . $message . "</font></center><p>";
				}
				?>
				<h1>Reset Bands of the Year</h1>
				
				<form action="reset_boty.php" method="post">
				<table border=0>
				<tr><td colspan='2'>ARE YOU SURE YOU WANT TO RESET THE RESULTS??</td></tr>
				<tr><td><input type="submit" name="reset" id="reset" value="Reset" /></td></tr>									
				</table>
				</form>
			</div>
		</div>
	</div>
	<!--MENU-->
	<div id="leftcolumn">
		<div class="innertube">
			Welcome, <?php echo ucwords(get_username());?><br />
			<?php echo date("F d, Y");?><br />
			<?php echo ucwords(get_login_role()) . " - <a href=includes/logout.inc.php><font color=#FFFFFF>logout</font></a>";?>
			<?php include("menu.php"); ?>
		</div>	
	</div>

	<div id="footer"><?php include('footer.html');?><div>
</div>

</body>
</html>
