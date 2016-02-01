<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');

if (check_login_status() == false || (get_login_role() != "admin" && get_login_role() != "contributor")) {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
}

if (isset($_POST['BandID']) && isset($_POST['year'])) {
	$connect = connectToDB();
	$bandID = $_POST['BandID'];
	$year = $_POST['year'];
	
	$query = "DELETE FROM BandShows WHERE BandID = $bandID AND Year = $year";
	mysql_query($query, $connect);
	$query = "DELETE FROM Enrollment WHERE BandID = $bandID AND Year = $year";
	mysql_query($query, $connect);
	$message = "Deleted Show Succesfully";
	
} else {
	$message = "Please enter all requried fields";
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
				<h1>Delete Band's Show</h1>
				<form action="delete_show.php" method="post">
				<table border=0><tr><td>School: </td><td>
				<?php
					$connect = connectToDB();
					$query = "SELECT BandID, School FROM Bands ORDER BY School";
					$result=mysql_query($query, $connect);?>
					<select name="BandID" id="BandID">
						<?php
							while ($row = mysql_fetch_array($result)) {
								$school = $row['School'];
								$BandID = $row['BandID'];
								echo "<option value=\"$BandID\">".$school."</option>";
							}
						?>
					</select></td></tr>
					<tr><td>Year: </td><td>
					<select name='year' id='year'>
					<?php
					for ($i = date('Y'); $i >= 2000; $i--) {
						echo "<option value='$i'>$i</option>";
					}
					?>
					</select>
					</td></tr>
					<tr><td><input type="submit" value="Delete Show"/></td></tr></table>
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
