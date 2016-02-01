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

if (isset($_POST['band']) && isset($_POST['director']) && isset($_POST['email'])) {

	$bandid = $_POST['band'];
	$director = $_POST['director'];
	$email = $_POST['email'];
	

		$query = "INSERT INTO BandDirectorEmail (BandID, Director, Email) VALUES ($bandid, '$director', '$email')";
		mysql_query($query, $connect);
		$message =  "Added email ($email) for director '$director' Successfully";

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
				<h1>Add Director Email Address</h1>
				* - Required Fields
				<form action="add_director_email.php" method="post">
				<table border=0>
				<tr><td>Band:</td><td>
				<select name="band" id="band">
					<?php
					$query = "SELECT BandID, School FROM Bands";
					$result = mysql_query($query, $connect);
					while ($row = mysql_fetch_array($result)) {
						$id = $row['BandID'];
						$school = $row['School'];
						echo "<option value='$id'>$school</option>";
					}
					?>
				</select>
				*</td></tr>
				<tr><td>Director Name:</td><td><input type="text" name="director" id="director" />*</td></tr>
				<tr><td>Email:</td><td><input type="text" name="email" id="email" />*</td></tr>
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
