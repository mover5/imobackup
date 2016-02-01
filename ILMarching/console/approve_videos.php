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
if (isset($_GET['submit'])) {
		// Get the ids to check
		$query = "SELECT VideoURL FROM Videos WHERE Approved = 0";
		$result = mysql_query($query, $connect);
		while ($row = mysql_fetch_array($result)) {
			$id = $row['VideoURL'];
			$urlid = substr($id, stripos($id, "?v=") + 3);
			$app = $_GET[$urlid];
			if ($app) {
				$query = "UPDATE Videos SET Approved = 1 WHERE VideoURL = '$id'";
			} else {
				$query = "DELETE FROM Videos WHERE VideoURL = '$id'";
			}
			//echo $query . "<br />";
			mysql_query($query, $connect);
		}
		$message = "Videos Approved";
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
				<h1>Approve new band Videos</h1>
				<form action='approve_videos.php' method='GET'>
				<?php
				$query = "SELECT VideoURL, v.BandID, BandYear, v.FestivalID, PerformanceType, School, Name, Title FROM Videos v LEFT JOIN Bands b ON v.BandID = b.BandID LEFT JOIN Festivals f ON v.FestivalID = f.FestivalID LEFT JOIN BandShows ON (v.BandID = BandShows.BandID AND v.BandYear = BandShows.Year) WHERE v.FestivalID IS NOT NULL AND Approved = 0";
				$result = mysql_query($query, $connect);
				if (mysql_num_rows($result) > 0) {
					$num = mysql_num_rows($result);
					echo "<h2>$num Videos(s) to approve</h2>";
					$count = 0;
					echo "<table border=1>";
					while ($row = mysql_fetch_array($result)) {
						$url = $row['VideoURL'];
						$BandID = $row['BandID'];
						$School = $row['School'];
						$BandYear = $row['BandYear'];
						$FestivalID = $row['FestivalID'];
						$Festival = $row['Name'];
						$PerfType = $row['PerformanceType'];
						$ShowTitle = $row['Title'];
						if ($count == 0) { // first image
							echo "<tr><td><b>$School</b>";
							$count++;
						} elseif ($count % 2 <> 0) {
							echo "<td><b>$School</b>";
							$count++;
						} elseif ($count % 2 == 0 && $count <> 0) { // even counts, not 0
							echo "</tr><tr><td><b>$School</b>";
							$count++;
						}
						echo "<br />";
						$embedurl = "http://youtube.com/embed/" . substr($url, stripos($url, "?v=") + 3);
						$urlid = substr($url, stripos($url, "?v=") + 3);
						echo "<iframe id='videoframe' name='videoframe' width='300' height='200' src='$embedurl' frameborder='0' allowfullscreen></iframe>";
						
						echo "<br />";
						echo "<center>$BandYear - $Festival - $PerfType</center>";
						echo "<center>$ShowTitle</center>";
						echo "<center><select name='$urlid'><option value=0>Not Approved</option><option value=1 selected='selected'>Approved</option></select></center>";
						echo "</td>";
					}
					echo "</tr></table>";
					echo "<input type='submit' name='submit' id='submit' value='Submit' />";
					echo "</form>";
				} else {
					echo "<h2>No Videos to Approve</h2>";
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
