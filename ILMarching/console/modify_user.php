<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');
$modify = false;
if (check_login_status() == false || get_login_role() != "admin") {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
	$connect = connectToDB();
}

if (isset($_POST['id'])) {
	$modify = true;
}
if (isset($_POST['role'])) {
	$modify = false;
	$role = $_POST['role'];
	$realname = $_POST['realname'];
	$password = $_POST['password'];
	$password2 = $_POST['password2'];
	$id = $_POST['userid'];
	if ($password == '') {
		//Update user without password
		$query = "UPDATE Users SET Role='$role', RealName='$realname' WHERE UserID = '$id'";
		mysql_query($query);
		$message = "Updated user.";
	} elseif ($password != '' && $password == $password2) {
		//Update User with new password
		$query = "UPDATE Users SET Role='$role', RealName='$realname', Password=SHA('$password') WHERE UserID = '$id'";
		mysql_query($query);
		$message = "Updated user and changed their password.";
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
				<h1>Modify User</h1>
				<?php
				if ($modify != true) {
					$query = "SELECT UserID, Name FROM Users ORDER BY Name";
					$result = mysql_query($query);
				?>
				<form action="modify_user.php" method="post">
					<select name="id" id="id">
						<?php
							while ($row = mysql_fetch_array($result)) {
								echo "<option value=".$row['UserID'].">".$row['Name']."</option>";
							}
						?>
					</select>
					<input type="submit" value="Submit" name="submit" />
				</form>
				<?php } 
				if ($modify == true) {
					$query = "SELECT * FROM Users WHERE UserID = " . $_POST['id'];
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
					if ($row['Role'] == "admin") {
						$options = "<option value='admin' selected='selected'>Admin</option>
									<option value='moderator'>Moderator</option>
									<option value='contributor'>Contributor</option>
									<option value='support'>Support</option>
									<option value='volunteer'>Volunteer</option>";
					} elseif ($row['Role'] == "moderator") {
						$options = "<option value='admin'>Admin</option>
									<option value='moderator' selected='selected'>Moderator</option>
									<option value='contributor'>Contributor</option>
									<option value='support'>Support</option>
									<option value='volunteer'>Volunteer</option>";
					} elseif ($row['Role'] == "contributor") {
						$options = "<option value='admin'>Admin</option>
									<option value='moderator'>Moderator</option>
									<option value='contributor' selected='selected'>Contributor</option>
									<option value='support'>Support</option>
									<option value='volunteer'>Volunteer</option>";
					} elseif ($row['Role'] == "support") {
						$options = "<option value='admin'>Admin</option>
									<option value='moderator'>Moderator</option>
									<option value='contributor'>Contributor</option>
									<option value='support' selected='selected'>Support</option>
									<option value='volunteer'>Volunteer</option>";
					} elseif ($row['Role'] == "volunteer") {
						$options = "<option value='admin'>Admin</option>
									<option value='moderator'>Moderator</option>
									<option value='contributor'>Contributor</option>
									<option value='support'>Support</option>
									<option value='volunteer' selected='selected'>Volunteer</option>";
					}
					$realname = $row['RealName'];
				?>
				<form action="modify_user.php" method="post">
					<h2><?php echo $row['Name']; ?></h2>
					<input type="hidden" name="userid" value="<?php echo $_POST['id']?>" />
					<table border=0>
						<tr><td>Role: </td><td>
						<select name="role" id="role">
						<?php echo $options; ?>
						</select>
						</td></tr>
						<tr><td>Real Name</td>
						<td><input type='text' name='realname' id='realname' value='<?php echo $realname; ?>' /></td></tr>
						<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
						<tr><td><b>Leave blank if you want the password to be the same.</b></td><td></td></tr>
						<tr><td>Password:</td><td><input type="password" name="password" id="password" /></td></tr>
						<tr><td>Re-Type Password:</td><td><input type="password" name="password2" id="password2" /></td></tr>
						<tr><td><input type="submit" name="submit" id="submit" value="Submit" /></td></tr>
					</table>
				</form>
				<?php
				}
				?>
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
