<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');

if (check_login_status() == false || (get_login_role() != "admin" && get_login_role() != "contributor" && get_login_role() != "moderator")) {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
	$connect = connectToDB();
}

if (isset($_REQUEST['name']) && isset($_REQUEST['bio'])) {
	 $userid = $_REQUEST['name'];
	 $bio = nl2br($_REQUEST['bio']);

 	$query = "UPDATE Users SET Bio='$bio' WHERE UserID = $userid";
	mysql_query($query);
	$message = "Added Biography Successfully";
	
} else {
	$message = "Please enter data into all the fields";
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
<script type="text/javascript" src="js/addTag.js"></script>
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
				<h1>Add Biography</h1>
				All Fields Required
				<form action="add_bio.php" method="POST">
				<table border=0>

				<tr><td>Staff Name:</td><td>
				<select name='name'>
				<?php
				if ($role == "admin") {
					$query = "SELECT Name, UserID FROM Users";
				} else {
					$query = "SELECT Name, UserID FROM Users WHERE Name = '$username'";
					echo $query;
				}
				
				$result = mysql_query($query, $connect);
				while ($row = mysql_fetch_array($result)) {
					$userID = $row['UserID'];
					$name = $row['Name'];
					echo "<option value='$userID'>$name</option>";
				}
				?>
				</select>
				</td></tr>
				
				<tr><td>&nbsp;</td><td><input type="button" value="Bold" onclick="addTags(this.form.bio, '<b>', '</b>')"><input type="button" value="Italic" onclick="addTags(this.form.bio, '<i>', '</i>')"><input type="button" value="Underline" onclick="addTags(this.form.bio, '<u>', '</u>')"></td></tr>
				<tr><td valign="top">Biography:</td><td><textarea rows="30" cols="60" name="bio"></textarea></td></tr>
				<tr><td><input type="submit" value="Add Biography"/></td></tr></table>
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
