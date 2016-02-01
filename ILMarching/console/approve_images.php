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
	$connect = connectToDB();
}
if (isset($_POST['submit'])) {
		// Get the ids to check
		$query = "SELECT BandImageID FROM BandImages WHERE Approved = 0";
		$result = mysql_query($query, $connect);
		while ($row = mysql_fetch_array($result)) {
			$id = $row['BandImageID'];
			$app = $_POST[$id];
			if ($app) {
				$query = "UPDATE BandImages SET Approved = 1 WHERE BandImageID = $id";
			} else {
				$query = "DELETE FROM BandImages WHERE BandImageID = $id";
			}
			mysql_query($query, $connect);
		}
		$message = "Images Approved";
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
				<h1>Approve new band images</h1>
				<form action='approve_images.php' method='POST'>
				<?php
				$query = "SELECT BandImageID, BandImages.BandID, ImageURL, School FROM BandImages, Bands WHERE Bands.BandID = BandImages.BandID AND Approved = 0";
				$result = mysql_query($query, $connect);
				if (mysql_num_rows($result) > 0) {
					$num = mysql_num_rows($result);
					echo "<h2>$num Image(s) to approve</h2>";
					$count = 0;
					echo "<table border=1>";
					while ($row = mysql_fetch_array($result)) {
						$url = $row['ImageURL'];
						if (substr($url, 0, 8) == "bandpics") $url = "../" . $url;
						$imageid = $row['BandImageID'];
						$school = $row['School'];
						if ($count == 0) { // first image
							echo "<tr><td><b>$school</b>";
							echo "<br />";
							echo "<select name='$imageid'><option value=0>Not Approved</option><option value=1 selected='selected'>Approved</option></select><br />";
							echo "<img src='$url' width=300 /></td>";
							$count++;
						} elseif ($count % 2 <> 0) {
							echo "<td><b>$school</b>";
							echo "<br />";
							echo "<select name='$imageid'><option value=0>Not Approved</option><option value=1 selected='selected'>Approved</option></select><br />";
							echo "<img src='$url' width=300 /></td>";
							$count++;
						} elseif ($count % 2 == 0 && $count <> 0) { // even counts, not 0
							echo "</tr><tr><td><b>$school</b>";
							echo "<br />";
							echo "<select name='$imageid'><option value=0>Not Approved</option><option value=1 selected='selected'>Approved</option></select><br />";
							echo "<img src='$url' width=300 /></td>";
							$count++;
						}
					}
					echo "</tr></table>";
					echo "<input type='submit' name='submit' id='submit' value='Submit' />";
					echo "</form>";
				} else {
					echo "<h2>No Images to Approve</h2>";
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
