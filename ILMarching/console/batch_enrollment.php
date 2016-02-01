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

if (isset($_POST['upload'])) {
	if ($_FILES['file']['error'] > 0) {
		$message = "Error: " . $_FILES['file']['error'];
	} else {
		$thefile = fopen($_FILES['file']['tmp_name'], "r");
		$count = 0;
		$date = date("Y");
		while(!feof($thefile)) {
			$line = fgets($thefile);
			if ($count > 0) {
				$linearray = array();
				$linearray = explode(",", $line);
				$bandid = $linearray[1];
				if ($bandid == "") break;
				$enrollment = $linearray[2];
				if ($enrollment == "") $enrollment = 0;
				$override = $linearray[3];
				if ($override == "") $override = 0;
				
				// Check to see if entry exists. If it does, Update, else, Insert
				$query = "SELECT * FROM Enrollment WHERE BandID = $bandid AND Year = $date";
				$result = mysql_query($query, $connect);
				if (mysql_num_rows($result) > 0) { // Record exists
					$query = "UPDATE Enrollment SET IHSAEnrollment = $enrollment, DivisionOverride = $override WHERE BandID = $bandid AND Year = $date";
				} else {
					$query = "INSERT INTO Enrollment (BandID, Year, IHSAEnrollment, DivisionOverride) VALUES ($bandid, $date, $enrollment, $override)";
				}
				
				// Execute Query
				mysql_query($query, $connect);
				
			}
			$count++;
		}
		$message = "Added $count Enrollment Figures via Batch Add";
	}
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
				<h1>Batch Load IHSA Enrollments</h1>
				<h3>1. Start by getting the batch file</h2>
				<form action='gencsv.php' method='POST'>
				<input type='submit' name='getbatch' id='getbatch' value='Get Batch File' />
				</form>
				
				<h3>2. Open the file in Excel and add the enrollment figures for this year for each band. </h3>
				
				<h3>3. Upload the file here:</h3>
				<form action='batch_enrollment.php' method='POST' enctype='multipart/form-data'>
				<label for='file'>Batch File:</label>
				<input type='file' name='file' id='file' />
				<br />
				<input type='submit' name='upload' id='upload' value='Upload' />
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
