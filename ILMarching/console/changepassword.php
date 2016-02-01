<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');
$modify = false;
if (check_login_status() == false) {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
	$connect = connectToDB();
}
if (isset($_POST['password']) && isset($_POST['password2'])) {
	$password = $_POST['password'];
	$password2 = $_POST['password2'];
	$id = $_POST['userid'];
	if ($password != '' && $password == $password2) {
		//Update User with new password
		$query = "UPDATE Users SET Password=SHA('$password') WHERE UserID = '$id'";
		mysql_query($query);
		$message = "Password Changed Successfully";
	} elseif ($password != '' && $password != $password2) {
		$message = "The Passwords much match";
	}
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
				<h1>Change Password</h1>
				<?php  
					$query = "SELECT * FROM Users WHERE Name = '" . $username . "'";
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
				?>
				<form action="changepassword.php" method="post">
					<h2><?php echo $row['Name']; ?></h2>
					<input type="hidden" name="userid" value="<?php echo $row['UserID']?>" />
					<table border=0>
						<tr><td>Password:</td><td><input type="password" name="password" id="password" /></td></tr>
						<tr><td>Re-Type Password:</td><td><input type="password" name="password2" id="password2" /></td></tr>
						<tr><td><input type="submit" name="submit" id="submit" value="Change Password" /></td></tr>
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
