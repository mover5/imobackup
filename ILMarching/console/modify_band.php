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

if (isset($_REQUEST['BandID'])) {
	$modify = true;
}
if (isset($_POST['BandID']) && isset($_POST['school']) && isset($_POST['band_name']) && isset($_POST['city_town']) && isset($_POST['directors'])) {
	$b = $_POST['BandID'];
	$school = addslashes($_POST['school']);
	$band_name = addslashes($_POST['band_name']);
	$city_town = addslashes($_POST['city_town']);
	$website = $_POST['website'];
	$directors = addslashes($_POST['directors']);
	$colors = $_POST['colors'];
	$address = addslashes($_POST['address']);
	$pic_url = $_POST['pic_url'];
	$notes = addslashes($_POST['notes']);
		
	$query = "UPDATE Bands SET School = '$school', BandName = '$band_name', Town = '$city_town', WebsiteURL = '$website', Directors = '$directors', Colors = '$colors', PicURL = '$pic_url', Notes = '$notes', Address = '$address' WHERE BandID = '$b'";
	mysql_query($query);
	$message = "Updated $school successfully";
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
				if ($modify != true) {
					$query = "SELECT BandID, School FROM Bands ORDER BY School";
					$result=mysql_query($query);
				?>
				<h1>Modify Band</h1>
				<form action="modify_band.php" method="post">
					<select name="BandID" id="BandID">
						<?php
							while ($row = mysql_fetch_array($result)) {
								$school = $row['School'];
								$BandID = $row['BandID'];
								echo "<option value=\"$BandID\">".$school."</option>";
							}
						?>
					</select>
					<input type="submit" value="Modify Band" name="submit" />
				</form>
				<?php } 
				if ($modify == true) {
					$b = $_REQUEST['BandID'];
					$query = "SELECT * FROM Bands WHERE BandID = '".$b."'";					
					$result=mysql_query($query);
					$row = mysql_fetch_array($result);
					$school = $row['School'];
					$band_name = $row['BandName'];
					$city_town = $row['Town'];
					$website = $row['WebsiteURL'];
					$directors = $row['Directors'];
					$colors = $row['Colors'];
					$address = $row['Address'];
					$pic_url = $row['PicURL'];
					$notes = str_replace("<br />", "", $row['Notes']);	
					echo "<h1>Modify Band: $school</h1>";
				echo "<form action=\"modify_band.php\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"BandID\" value=\"$b\" />";
				echo "<table border=0 cellspacing=10";
				echo "<tr><td>School:</td><td><input type=\"text\" size=39 name=\"school\" value=\"$school\"/></td><td>Band Name:</td><td><input type=\"text\" size=39 name=\"band_name\" value=\"$band_name\"/></td></tr>";
				echo "<tr><td>City/Town:</td><td><input type=\"text\" size=39 name=\"city_town\" value=\"$city_town\"/></td><td>Address:</td><td><input type=\"text\" size=39 name=\"address\" value=\"$address\"/></td></tr>";
				echo "<tr><td>Directors:</td><td><input type=\"text\" size=39 name=\"directors\" value=\"$directors\"/></td><td>Colors:</td><td><input type=\"text\" size=39 name=\"colors\" value=\"$colors\"/></td></tr>";
				echo "<tr><td>Website:</td><td><input type=\"text\" size=39 name=\"website\" value=\"$website\"/></td><td>Picture URL:</td><td><input type=\"text\" size=39 name=\"pic_url\" value=\"$pic_url\"/></td></tr>";
				
				
				echo "<tr></td><td>&nbsp;</td><td colspan='3'><input type=\"button\" value=\"Bold\" onclick=\"addTags(this.form.notes, '<b>', '</b>')\"><input type=\"button\" value=\"Italic\" onclick=\"addTags(this.form.notes, '<i>', '</i>')\"><input type=\"button\" value=\"Underline\" onclick=\"addTags(this.form.notes, '<u>', '</u>')\"></td></tr>";
				echo "<tr><td>Band Notes:</td><td colspan='3'><textarea rows=\"10\" cols=\"110\" name=\"notes\">$notes</textarea></td></tr>";
				echo "<tr><td><input type=\"submit\" value=\"Modify Band\"/></td></tr>";
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
