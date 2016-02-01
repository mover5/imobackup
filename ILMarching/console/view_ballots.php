<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');
$modify = false;
if (check_login_status() == false || get_login_role() != "admin") {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
	$connect = connectToDB();
}

if (isset($_POST['id'])) {
	$modify = true;
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
				<h1>View Ballots</h1>
				<?php
				if ($modify != true) {
					$query = "SELECT VoterID, Email FROM BandsOfTheYearVoters WHERE Voted = 1 ORDER BY Email";
					$result = mysql_query($query);
				?>
				<form action="view_ballots.php" method="post">
					<select name="id" id="id">
						<?php
							while ($row = mysql_fetch_array($result)) {
								echo "<option value=".$row['VoterID'].">".$row['Email']."</option>";
							}
						?>
					</select>
					<input type="submit" value="Submit" name="submit" />
				</form>
				<?php } 
				if ($modify == true) {
					$voterid = $_POST['id'];
					$query = "SELECT Email FROM BandsOfTheYearVoters WHERE VoterID = $voterid";
					$result = mysql_query($query, $connect);
					$row = mysql_fetch_array($result);
					$email = $row['Email'];
					echo "<h2>Ballot for $email</h2>";
					$query = "SELECT Email, School, IMODivision, Place FROM `BandsOfTheYearBallot` LEFT JOIN Bands ON BandsOfTheYearBallot.BandID = Bands.BandID LEFT JOIN BandsOfTheYearVoters ON BandsOfTheYearBallot.VoterID = BandsOfTheYearVoters.VoterID WHERE BandsOfTheYearBallot.VoterID = $voterid ORDER BY IMODivision, Place";
					$result = mysql_query($query, $connect);
					$mostimp = array();
					$divdisplay = array();
					while ($row = mysql_fetch_array($result)) {
						$div = $row['IMODivision'];
						$place = $row['Place'];
						$school = $row['School'];
						if ($place == -1) {
							$mostimp[$div] = "<br /><b>Most Improved Band: </b> $school";
						} else {
							$divdisplay[$div] .= "<b>$place</b>: $school<br />";
						}
					}
					
					echo "<table border=1><tr><th>Division I</th><th>Division II</th></tr>";
					echo "<tr><td valign='top'>".$divdisplay[1].$mostimp[1]."</td><td valign='top'>".$divdisplay[2].$mostimp[2]."</td></tr>";
					echo "<tr><th>Division III</th><th>Division IV</th></tr>";
					echo "<tr><td valign='top'>".$divdisplay[3].$mostimp[3]."</td><td valign='top'>".$divdisplay[4].$mostimp[4]."</td></tr>";
					echo "</table>";
				
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
