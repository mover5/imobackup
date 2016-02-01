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

if (isset($_POST['submit'])) {
	$bandid = $_POST['bandid'];
	$festid = $_POST['festid'];
    $type = $_POST['type'];
	if ($festid == 'null') {
		$year = $_POST['year'];
	} else {
		$query = "SELECT FestivalYear FROM Festivals WHERE FestivalID = $festid";
		$result = mysql_query($query, $connect);
		$row = mysql_fetch_array($result);
		$year = $row['FestivalYear'];
	}
	
	if ($year != "" && $year != null) {
		$url = $_POST['url'];
		
		$query = "SELECT * FROM Videos WHERE ((VideoURL = '$url') OR (BandID = $bandid AND festid = $festid AND BandYear = $year AND PerformanceType = $type))";
		$result = mysql_query($query, $connect);
		if (mysql_num_rows($result) == 0) {
			$query = "INSERT INTO Videos VALUES ('$url', $bandid, $year, $festid, '$type', 1)";
			mysql_query($query, $connect);
			$message = "Added video with URL: $url";
		} else {
			$message = "A video already exists of that performance";
		}
	} else {
		$message = "You must supply a valid year or a valid festival for this video";
	}
} else {
    $message = "You must enter EITHER a Festival or a Show Year.";
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="css/IMOconsole.css" />
<link rel="stylesheet" type="text/css" href="css/anylinkcssmenu.css" />
<script src="js/equalcolumns.js" type="text/javascript"></script> 
<script type="text/javascript" src="js/anylinkcssmenu.js"></script>
<script type="text/javascript" src="js/addTag.js"></script>
<script type="text/javascript" src="js/date.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<script type='text/javascript' src='js/jquery.autocomplete.js'></script>
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
				<h1>Add Video</h1>
				<form action='add_video.php' method='POST'>
				<table border='0'>
				<?php 
					$query = "SELECT * FROM Festivals ORDER BY FestivalYear DESC, Name ASC";
					$result = mysql_query($query, $connect);
				?>
				<tr><td>Festival: </td><td><select id='festid' name='festid'>
				<option value='null'>...</option>
				<?php
					while ($row = mysql_fetch_array($result)) {
						echo "<option value='".$row['FestivalID']."'>".$row['FestivalYear']." - " . $row['Name']."</option>";
					}
				?>
				</select></td></tr>	
				<tr><td>Show Year: </td><td><input type='text' name='year' id='year' /></td></tr>
				<tr><td>Band: </td><td>
				<?php 
					$query = "SELECT * FROM Bands ORDER BY School ASC";
					$result = mysql_query($query, $connect);
				?>
				<select id='bandid' name='bandid'>
				<?php
					while ($row = mysql_fetch_array($result)) {
						echo "<option value='".$row['BandID']."'>".$row['School']."</option>";
					}
				?>
			    </select>
				</td></tr>
				<tr><td>Type: </td><td>
				<select id='type' name='type'>
				<option value='Prelim' selected='selected'>Prelims</option>
				<option value='SemiFinals'>SemiFinals</option>
				<option value='Finals'>Finals</option>
                </select>
				</td></tr>
				<tr><td>YouTube URL: </td><td><input type='text' id='url' size='100' name='url' /></td></tr>	
				<tr><td colspan='2'><input type='submit' name='submit' id='submit' value='Add Video' /></td></tr>	
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

	<!--<div id="footer"><?php //include('footer.html');?><div>-->
</div>

</body>
</html>
