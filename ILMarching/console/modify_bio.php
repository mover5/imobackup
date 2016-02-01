<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');
$modify = false;
if (check_login_status() == false || (get_login_role() != "admin" && get_login_role() != "contributor" && get_login_role() != "moderator")) {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
	$connect = connectToDB();
}

if (isset($_POST['bioID'])) {
	$modify = true;
}
if (isset($_POST['bio'])) {
	$bioID = $_REQUEST['bioID'];
	$biography = nl2br($_REQUEST['bio']);
	$query = "UPDATE Users SET Bio = '$biography' WHERE UserID = '$bioID'";
	mysql_query($query);
	
	$message = "Modified Bio Successfully";
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
				<h1>Modify Biography</h1>
				<?php
				if ($modify != true) {
					if ($role == "admin") {
						$query = "SELECT UserID, Name FROM Users ORDER BY Name ASC";
					} else {
						$query = "SELECT UserID, Name FROM Users WHERE Name = '$username' ORDER BY Name ASC";
					}
					

					$Result=mysql_query($query);
					
					?>
					
					<form action="modify_bio.php" method="post">
					<select name="bioID"><?php
					while($Row = mysql_fetch_array($Result))
					{
						$name = $Row['Name'];
						$bioID = $Row['UserID'];
						echo "<option value=\"$bioID\">".$name."</option>";
					
					}
					
					?>
					</select>
					<input type="submit" value="Modify Biography" />
					</form>
				<?php } 
				if ($modify == true) {
				$query = "SELECT * FROM Users WHERE UserID = '".$_REQUEST['bioID']."' ORDER BY Name";

				$Result=mysql_query($query);
				while ($Row = mysql_fetch_array($Result))
				{
					 $name = $Row['Name'];
					 $biography = str_replace("<br />", "", $Row['Bio']);
					 
				}
				
				echo "<form action=\"modify_bio.php\" method=\"post\">";
				echo "<h2>$name</h2>";
				echo "<table border=0>";
				echo "<input type=\"hidden\" id=\"bioID\" name=\"bioID\" value=\"".$_REQUEST['bioID']."\"/></td></tr>";
				echo "<tr><td>&nbsp;</td><td><input type=\"button\" value=\"Bold\" onclick=\"addTags(this.form.bio, '<b>', '</b>')\"><input type=\"button\" value=\"Italic\" onclick=\"addTags(this.form.bio, '<i>', '</i>')\"><input type=\"button\" value=\"Underline\" onclick=\"addTags(this.form.bio, '<u>', '</u>')\"></td></tr>";
				echo "<tr><td valign=\"top\">Biography:</td><td><textarea rows=\"30\" cols=\"60\" name=\"bio\">$biography</textarea></td></tr>";
				echo "<tr><td><input type=\"submit\" value=\"Modify Biography\"/></td></tr>";
				echo "</table>";
				echo "</form>";
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
