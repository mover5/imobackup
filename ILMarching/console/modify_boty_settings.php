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
	$connect = connectToDB();
}

if (isset($_POST['start']) && isset($_POST['end']) && isset($_POST['div1']) && isset($_POST['div2']) && isset($_POST['div3']) && isset($_POST['div4'])) {

	$start = $_POST['start'];
	$end = $_POST['end'];
	$password = $_POST['password'];
	$password2 = $_POST['password2'];
	$div1 = $_POST['div1'];
	$div2 = $_POST['div2'];
	$div3 = $_POST['div3'];
	$div4 = $_POST['div4'];
	
	if ($password == "") {
		$query = "UPDATE BandsOfTheYearSettings SET StartDate = '$start', EndDate = '$end', Div1Num = $div1, Div2Num = $div2, Div3Num = $div3, Div4Num = $div4";
		mysql_query($query);
		$message =  "Modified Settings";
	} else if ($password != "" && $password == $password2) {
		$query = "UPDATE BandsOfTheYearSettings SET StartDate = '$start', EndDate = '$end', Div1Num = $div1, Div2Num = $div2, Div3Num = $div3, Div4Num = $div4, Password = PASSWORD('$password')";
		mysql_query($query);
		$message =  "Modified Settings";
	} else {
		$message = "Passwords did not match. Try again.";
	}

} else {
	$message = "Please fill in all requried fields";
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
				<h1>Modify BotY Settings</h1>
				* - Required Fields
				<?php
					$query = "SELECT * FROM BandsOfTheYearSettings LIMIT 1";
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
				?>
				<form action="modify_boty_settings.php" method="post">
				<table border=0>
				<tr><td>BotY Start Date:</td><td><input type="text" name="start" id="start" value="<?php echo $row['StartDate']; ?>" />* (YYYY-MM-DD)</td></tr>
				<tr><td>BotY End Date:</td><td><input type="text" name="end" id="end" value="<?php echo $row['EndDate']; ?>" />*</td></tr>
				<tr><td>Voting Password:</td><td><input type="text" name="password" id="password" /> (Modify this ONLY if you want to change the password)</td></tr>
				<tr><td>Re-Type Password:</td><td><input type="text" name="password2" id="password2" /></td></tr>
				<tr><td>Div 1 Bands:</td><td><input type="text" name="div1" id="div1" value="<?php echo $row['Div1Num']; ?>" />*</td></tr>
				<tr><td>Div 2 Bands:</td><td><input type="text" name="div2" id="div2" value="<?php echo $row['Div2Num']; ?>" />*</td></tr>
				<tr><td>Div 3 Bands:</td><td><input type="text" name="div3" id="div3" value="<?php echo $row['Div3Num']; ?>" />*</td></tr>
				<tr><td>Div 4 Bands:</td><td><input type="text" name="div4" id="div4" value="<?php echo $row['Div4Num']; ?>" />*</td></tr>
				<tr><td><input type="submit" name="submit" id="submit" value="Submit" /></td></tr>									
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
