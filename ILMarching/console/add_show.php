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
$year = "";
if (isset($_REQUEST['year'])) {
	$year = $_REQUEST['year'];
}
if (isset($_POST['BandID']) && isset($_POST['title'])) {
	$connect = connectToDB();
	$bandID = $_POST['BandID'];
	$year = $_POST['year'];
	$title = $_POST['title'];
    $rep = nl2br($_POST['rep']);
	$enrollment = $_POST['enrollment'];
	if (isset($_POST['override'])) {
		$override = 1;
	} else {
		$override = 0;
	}
	$title = addslashes($title);
	$rep = addslashes($rep);
	$query = "SELECT * FROM BandShows WHERE BandID = $bandID AND Year = $year";
	$result = mysql_query($query, $connect);
	$num = mysql_num_rows($result);
	if ($num == 0) {
		$query = "INSERT INTO BandShows (BandID, Year, Title, Repetoire) VALUES ($bandID, $year, '$title', '$rep')";
		mysql_query($query, $connect);
		$query = "INSERT INTO Enrollment (BandID, Year, IHSAEnrollment, DivisionOverride) VALUES ($bandID, $year, $enrollment, $override)";
		
		mysql_query($query, $connect);
		$message = "Added the '$title' to the list of shows";
	} else {
		$message = "A show already exists for this band and year. Either modify it, or delete it and add a new show";
	}
	
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
				<h1>Add Band's Show</h1>
				<?php
				if ($year == "") {
					echo "<h2>Select Year</h2>";
					echo "<form action='add_show.php' method='GET'>";
					echo "<select name='year' id='year'>";
					for ($i = date("Y"); $i >= 2000; $i--) {
						echo "<option value='$i'>$i</option>";
					}
					echo "</select>";
					echo "<input type='submit' name='yearsubmit' id='yearsubmit' value='Pick Year' />";
					echo "</form>";
				} else {
				?>
				* - Required Field
				<form action="add_show.php" method="POST">
				<table border=0><tr><td>School: </td><td>
				<?php
					$connect = connectToDB();
					$query = "SELECT BandID, School FROM Bands WHERE BandID <> ALL(SELECT BandID FROM BandShows WHERE Year = $year) ORDER BY School";
					$result=mysql_query($query, $connect);?>
					<select name="BandID" id="BandID">
						<?php
							while ($row = mysql_fetch_array($result)) {
								$school = $row['School'];
								$BandID = $row['BandID'];
								echo "<option value=\"$BandID\">".$school."</option>";
							}
						?>
					</select>*</td></tr>
					<input type='hidden' name='year' id='year' value='<?php echo $year; ?>' />
					<tr><td>Show Title: </td><td><input type='text' name='title' id='title' size='40' />*</td></tr>
<!--					<tr><td>Repetoire: </td><td><input type='text' name='rep' id='rep' size='40' />*</td></tr> -->
                    <tr><td valign='top'>Repetoire:</td><td><textarea rows='10' cols='110' name='rep'></textarea></td></tr>

					<tr><td>Enrollment: </td><td><input type='text' name='enrollment' id='enrollment' size='10' /></td></tr>
					<tr><td>Division Override: </td><td><input type='checkbox' name='override' id='override' /></td></tr>
				<tr><td><input type="submit" value="Add Yearly Info"/></td></tr></table>
				</form>
				<?php } ?>
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
