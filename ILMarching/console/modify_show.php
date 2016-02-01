<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');
$modify = false;
if (check_login_status() == false || (get_login_role() != "admin" && get_login_role() != "contributor")) {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
	$connect = connectToDB();
}

if (isset($_REQUEST['modsubmit'])) {
	$bandID = $_REQUEST['BandID'];
	$year = $_REQUEST['Year'];
	$query = "SELECT BandShows.BandID, BandShows.Year, BandShows.Awards, BandShows.Title, BandShows.Repetoire, Bands.School FROM BandShows, Bands WHERE Bands.BandID = BandShows.BandID AND BandShows.BandID = $bandID AND Year = $year";
	$result = mysql_query($query, $connect);
	$q2 = "SELECT BandID, Year, IHSAEnrollment, DivisionOverride FROM Enrollment WHERE BandID = $bandID AND Year = $year";
	$r2 = mysql_query($q2, $connect);
	$num = mysql_num_rows($result);
	if ($num == 0) {
		$message = "There is no show listed for that band in that year. Please add one first";
		$modify = false;
	} else {
		$modify = true;
	}

}
if (isset($_REQUEST['yearsubmit'])) {
	$year = $_REQUEST['year'];
}
if (isset($_POST['BandID']) && (isset($_POST['Title']) || isset($_POST['Awards']))) {
	$BandID = $_POST['BandID'];
	$year = $_POST['year'];
	$Title = $_POST['Title'];
	$Rep = nl2br($_POST['Rep']);
	$Awards = $_POST['Awards'];
	$enrollment = $_POST['enrollment'];
	if (isset($_POST['override'])) {
		$override = 1;
	} else {
		$override = 0;
	}	
	$Title = addslashes($Title);
	$Rep = addslashes($Rep);
	$query = "UPDATE BandShows SET Title = '$Title', Repetoire = '$Rep', Awards = '$Awards' WHERE BandID = $BandID AND Year = $year";
	mysql_query($query, $connect);
	$query = "DELETE FROM Enrollment WHERE BandID = $BandID AND Year = $year";
	mysql_query($query, $connect);
	$query = "INSERT INTO Enrollment (BandID, Year, IHSAEnrollment, DivisionOverride) VALUES ($BandID, $year, '$enrollment', $override)";
	mysql_query($query, $connect);
	$message = "Updated show successfully";
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

				<?php
				if ((!isset($_REQUEST['yearsubmit'])) && (!isset($_REQUEST['modsubmit']))) {
					echo "<h2>Select Year</h2>";
					echo "<form action='modify_show.php' method='GET'>";
					echo "<select name='year' id='year'>";
					for ($i = date("Y"); $i >= 2000; $i--) {
						echo "<option value='$i'>$i</option>";
					}
					echo "</select>";
					echo "<input type='submit' name='yearsubmit' id='yearsubmit' value='Pick Year' />";
					echo "</form>";
				} else {
				if ($modify != true) {
					$query = "SELECT BandID, School FROM Bands WHERE BandID = ANY(SELECT BandID FROM BandShows WHERE Year = $year) ORDER BY School";
					$result=mysql_query($query);
				?>
				<h1>Modify Band's Yearly Info</h1>
				<form action="modify_show.php" method="POST">
					School: <select name="BandID" id="BandID">
						<?php
							while ($row = mysql_fetch_array($result)) {
								$school = $row['School'];
								$BandID = $row['BandID'];
								echo "<option value=\"$BandID\">".$school."</option>";
							}
						?>
					</select><br /><br />
					<input type='hidden' name='Year' id='Year' value='<?php echo $year; ?>' />
					<input type="submit" value="Modify Yearly Info" name="modsubmit" />
				</form>
				<?php } 
				if ($modify == true) {
					$row = mysql_fetch_array($result);
					$row2 = mysql_fetch_array($r2);
					$BandID = $row['BandID'];
					$year = $row['Year'];
					$Title = $row['Title'];
                    $Rep = str_replace("<br />", "", $row['Repetoire']);
                    if ($Rep == NULL) $Rep = "";
					$Awards = $row['Awards'];
					$school = $row['School'];
					$enrollment = $row2['IHSAEnrollment'];
					$override = $row2['DivisionOverride'];
					if ($override == 1) {
						$overridehtml = "checked";
					} else {
						$overridehtml = "";
					}
					echo "<h1>Modify $year $school's Yearly Info</h1>";
				echo "<form action=\"modify_show.php\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"BandID\" value=\"$BandID\" />";
				echo "<input type=\"hidden\" name=\"year\" value=\"$year\" />";
				echo "<table border=0 cellspacing=10>";
				echo "<tr><td>Title:</td><td><input type=\"text\" size=39 name=\"Title\" value=\"$Title\"/></td>";
				//echo "<tr><td>Repetoire:</td><td><input type=\"text\" size=39 name=\"Rep\" value=\"$Rep\"/></td>";
				echo "<tr><td valign='top'>Repetoire:</td><td><textarea rows=\"10\" cols=\"110\" name=\"Rep\">$Rep</textarea></td></tr>";

				echo "<tr><td>&nbsp;</td><td><input type=\"button\" value=\"Bold\" onclick=\"addTags(this.form.Awards, '<b>', '</b>')\"><input type=\"button\" value=\"Italic\" onclick=\"addTags(this.form.notes, '<i>', '</i>')\"><input type=\"button\" value=\"Underline\" onclick=\"addTags(this.form.notes, '<u>', '</u>')\"></td></tr>";
				echo "<tr><td valign='top'>Awards:</td><td><textarea rows=\"10\" cols=\"110\" name=\"Awards\">$Awards</textarea></td></tr>";
				echo "<tr><td valign='top'>Enrollment:</td><td><input type=\"text\" size=10 name=\"enrollment\" value=\"$enrollment\"/></tr>";
				echo "<tr><td valign='top'>Division Override:</td><td><input type=\"checkbox\" name=\"override\" $overridehtml/></tr>";
				echo "<tr><td colspan='2'><input type=\"submit\" value=\"Modify Yearly Info\"/></td></tr>";
				echo "</table>";
				echo "</form>";
				}
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
