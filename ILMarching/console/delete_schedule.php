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

if (isset($_POST['deletesubmit'])) {
	 $FestivalID = $_POST['FestivalID'];
	 $query = "DELETE FROM Schedules WHERE FestivalID='$FestivalID'";
	 mysql_query($query);
	$moddate = date("Y-m-d");
	$query = "UPDATE Festivals SET UpdatedBy = '$username', LastUpdated = '$moddate' WHERE FestivalID = $FestivalID";
	mysql_query($query, $connect);
	$message = "Deleted Schedule successfully";
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
				<h1>Delete Schedules</h1>
				<form action='delete_schedule.php' method='POST'>
				Festival: <select name='FestivalID'>
				<?php
				$query = "SELECT Schedules.FestivalID, Name, FestivalYear FROM Schedules, Festivals WHERE Festivals.FestivalID = Schedules.FestivalID GROUP BY FestivalID ORDER BY FestivalYear DESC, Date ASC, Name ASC";
				$result = mysql_query($query, $connect);
				while ($row = mysql_fetch_array($result)) {
					$FestivalID = $row['FestivalID'];
					$Name = $row['Name'];
					$FestivalYear = $row['FestivalYear'];
					echo "<option value='$FestivalID'>$FestivalYear - $Name</option>";
				}
				?>
				</select>
				<input type='submit' name='deletesubmit' value='Delete Schedule' />
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
