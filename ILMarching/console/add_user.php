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

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password2'])) {
	$connect = connectToDB();
	$username = $_POST['username'];
	$realname = $_POST['realname'];
	$password = $_POST['password'];
	$password2 = $_POST['password2'];
	$role = $_POST['role'];
	
	if ($password == $password2) {
		$query = "INSERT INTO Users (Name, RealName, Password, Role) VALUES ('$username', '$realname', SHA('$password'), '$role')";
		mysql_query($query);
		$message =  "Added user '$username' Successfully";
	} else {
		$message = "Passwords did not match. Try again.";
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
				<h1>Add User</h1>
				* - Required Fields
				<form action="add_user.php" method="post">
				<table border=0>
				<tr><td>Username:</td><td><input type="text" name="username" id="username" />*</td></tr>
				<tr><td>Real Name:</td><td><input type="text" name="realname" id="realname" />*</td></tr>
				<tr><td>Password:</td><td><input type="password" name="password" id="password" />*</td></tr>
				<tr><td>Re-Type Password:</td><td><input type="password" name="password2" id="password2" />*</td></tr>
				<tr><td>Role:</td><td><select name="role" id="role">
						<option value="admin">Admin</option>
						<option value="moderator">Moderator</option>
						<option value="contributor">Contributor</option>
						<option value="support">Support</option>
						<option value="volunteer">Volunteer</option>
					</select>*</td></tr>
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
