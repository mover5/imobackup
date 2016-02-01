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
				<h1>Bands of the Year Results</h1>
				<?php
				$query = "SELECT * FROM BandsOfTheYearSettings LIMIT 1";
				$result = mysql_query($query, $connect);
				$row = mysql_fetch_array($result);
				$div1 = $row['Div1Num'];
				$div2 = $row['Div2Num'];
				$div3 = $row['Div3Num'];
				$div4 = $row['Div4Num'];
				
				echo "<table border=1><tr><th>Division I</th><th>Division II</th></tr>";
				echo "<tr>";
				echo "<td valign='top'>";
				//Division 1
				$query = "SELECT School, Score, FirstPlaceVotes FROM BandsOfTheYearPublic LEFT JOIN Bands ON BandsOfTheYearPublic.BandID = Bands.BandID WHERE IMODivision = 1 ORDER BY Score DESC, FirstPlaceVotes DESC, SecondPlaceVotes DESC, ThirdPlaceVotes DESC, School ASC";
				$result = mysql_query($query, $connect);
				if (mysql_num_rows($result) == 0) {
					echo "No Votes Cast";
				} else {
					for ($i = 1; $i <= $div1; $i++) {
						$row = mysql_fetch_array($result);
						echo "<b>$i: </b>" . $row['School'] . " (" . $row['Score'] . " points";
						if ($row['FirstPlaceVotes'] != 0) {
							echo ", " . $row['FirstPlaceVotes'] . " first-place";
						}
						echo ")<br />";
					}
					$others = "";
					while ($row = mysql_fetch_array($result)) {
						if ($row['FirstPlaceVotes'] != 0) {
							if ($others == "") {
								$others = $row['School'] . " (" . $row['FirstPlaceVotes'] . ")";
							} else {
								$others .= "<br />" . $row['School'] . " (" . $row['FirstPlaceVotes'] . ")";
							}
						}
					}
					if ($others != "") {
						echo "<br /><b>Other Bands recieving First Place Votes: </b><br />$others";
					}
					
					echo "<p></p>";
					
					$query = "SELECT School FROM BandsOfTheYearPublic LEFT JOIN Bands ON BandsOfTheYearPublic.BandID = Bands.BandID WHERE MostImproved = (SELECT MAX(MostImproved) as max FROM `BandsOfTheYearPublic` WHERE IMODivision = 1) AND IMODivision = 1";
					$result = mysql_query($query);
					if (mysql_num_rows($result) > 1) {
						$title = "Bands";
					} else {
						$title = "Band";
					}
					echo "<b>Most Improved $title</b>";
					while ($row = mysql_fetch_array($result)) {
						echo "<br />" . $row['School'];
					}
				}
				echo "</td><td valign='top'>";
				//Division 2
				$query = "SELECT School, Score, FirstPlaceVotes FROM BandsOfTheYearPublic LEFT JOIN Bands ON BandsOfTheYearPublic.BandID = Bands.BandID WHERE IMODivision = 2 ORDER BY Score DESC, FirstPlaceVotes DESC, SecondPlaceVotes DESC, ThirdPlaceVotes DESC, School ASC";
				$result = mysql_query($query, $connect);
				if (mysql_num_rows($result) == 0) {
					echo "No Votes Cast";
				} else {
					for ($i = 1; $i <= $div2; $i++) {
						$row = mysql_fetch_array($result);
						echo "<b>$i: </b>" . $row['School'] . " (" . $row['Score'] . " points";
						if ($row['FirstPlaceVotes'] != 0) {
							echo ", " . $row['FirstPlaceVotes'] . " first-place";
						}
						echo ")<br />";
					}
					$others = "";
					while ($row = mysql_fetch_array($result)) {
						if ($row['FirstPlaceVotes'] != 0) {
							if ($others == "") {
								$others = $row['School'] . " (" . $row['FirstPlaceVotes'] . ")";
							} else {
								$others .= "<br />" . $row['School'] . " (" . $row['FirstPlaceVotes'] . ")";
							}
						}
					}
					if ($others != "") {
						echo "<br /><b>Other Bands recieving First Place Votes: </b><br />$others";
					}
					echo "<p></p>";
					
					$query = "SELECT School FROM BandsOfTheYearPublic LEFT JOIN Bands ON BandsOfTheYearPublic.BandID = Bands.BandID WHERE MostImproved = (SELECT MAX(MostImproved) as max FROM `BandsOfTheYearPublic` WHERE IMODivision = 2) AND IMODivision = 2";
					$result = mysql_query($query);
					if (mysql_num_rows($result) > 1) {
						$title = "Bands";
					} else {
						$title = "Band";
					}
					echo "<b>Most Improved $title</b>";
					while ($row = mysql_fetch_array($result)) {
						echo "<br />" . $row['School'];
					}
				}
				
				echo "</td>";
				echo "</tr>";
				echo "<tr><th>Division III</th><th>Division IV</th></tr>";
				echo "<tr>";
				echo "<td valign='top'>";
				//Division 3
				$query = "SELECT School, Score, FirstPlaceVotes FROM BandsOfTheYearPublic LEFT JOIN Bands ON BandsOfTheYearPublic.BandID = Bands.BandID WHERE IMODivision = 3 ORDER BY Score DESC, FirstPlaceVotes DESC, SecondPlaceVotes DESC, ThirdPlaceVotes DESC, School ASC";
				$result = mysql_query($query, $connect);
				if (mysql_num_rows($result) == 0) {
					echo "No Votes Cast";
				} else {
					for ($i = 1; $i <= $div3; $i++) {
						$row = mysql_fetch_array($result);
						echo "<b>$i: </b>" . $row['School'] . " (" . $row['Score'] . " points";
						if ($row['FirstPlaceVotes'] != 0) {
							echo ", " . $row['FirstPlaceVotes'] . " first-place";
						}
						echo ")<br />";
					}
					$others = "";
					while ($row = mysql_fetch_array($result)) {
						if ($row['FirstPlaceVotes'] != 0) {
							if ($others == "") {
								$others = $row['School'] . " (" . $row['FirstPlaceVotes'] . ")";
							} else {
								$others .= "<br />" . $row['School'] . " (" . $row['FirstPlaceVotes'] . ")";
							}
						}
					}
					if ($others != "") {
						echo "<br /><b>Other Bands recieving First Place Votes: </b><br />$others";
					}
					
					echo "<p></p>";
					
					$query = "SELECT School FROM BandsOfTheYearPublic LEFT JOIN Bands ON BandsOfTheYearPublic.BandID = Bands.BandID WHERE MostImproved = (SELECT MAX(MostImproved) as max FROM `BandsOfTheYearPublic` WHERE IMODivision = 3) AND IMODivision = 3";
					$result = mysql_query($query);
					if (mysql_num_rows($result) > 1) {
						$title = "Bands";
					} else {
						$title = "Band";
					}
					echo "<b>Most Improved $title</b>";
					while ($row = mysql_fetch_array($result)) {
						echo "<br />" . $row['School'];
					}
				}
				
				echo "</td><td valign='top'>";
				//Division 4
				$query = "SELECT School, Score, FirstPlaceVotes FROM BandsOfTheYearPublic LEFT JOIN Bands ON BandsOfTheYearPublic.BandID = Bands.BandID WHERE IMODivision = 4 ORDER BY Score DESC, FirstPlaceVotes DESC, SecondPlaceVotes DESC, ThirdPlaceVotes DESC, School ASC";
				$result = mysql_query($query, $connect);
				if (mysql_num_rows($result) == 0) {
					echo "No Votes Cast";
				} else {
					for ($i = 1; $i <= $div4; $i++) {
						$row = mysql_fetch_array($result);
						echo "<b>$i: </b>" . $row['School'] . " (" . $row['Score'] . " points";
						if ($row['FirstPlaceVotes'] != 0) {
							echo ", " . $row['FirstPlaceVotes'] . " first-place";
						}
						echo ")<br />";
					}
					$others = "";
					while ($row = mysql_fetch_array($result)) {
						if ($row['FirstPlaceVotes'] != 0) {
							if ($others == "") {
								$others = $row['School'] . " (" . $row['FirstPlaceVotes'] . ")";
							} else {
								$others .= "<br />" . $row['School'] . " (" . $row['FirstPlaceVotes'] . ")";
							}
						}
					}
					if ($others != "") {
						echo "<br /><b>Other Bands recieving First Place Votes: </b><br />$others";
					}
					echo "<p></p>";
					
					$query = "SELECT School FROM BandsOfTheYearPublic LEFT JOIN Bands ON BandsOfTheYearPublic.BandID = Bands.BandID WHERE MostImproved = (SELECT MAX(MostImproved) as max FROM `BandsOfTheYearPublic` WHERE IMODivision = 4) AND IMODivision = 4";
					$result = mysql_query($query);
					if (mysql_num_rows($result) > 1) {
						$title = "Bands";
					} else {
						$title = "Band";
					}
					echo "<b>Most Improved $title</b>";
					while ($row = mysql_fetch_array($result)) {
						echo "<br />" . $row['School'];
					}
				}
				echo "</td>";
				echo "</tr>";
				echo "</table>";
				echo "<p></p>";
				$query = "SELECT COUNT(*) AS count FROM BandsOfTheYearVotersPublic WHERE Voted = 1";
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				echo "<b>".$row['count']." People Voted!</b>";
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
