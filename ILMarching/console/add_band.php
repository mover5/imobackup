
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

if (isset($_POST['school']) && isset($_POST['band_name']) && isset($_POST['city_town']) && isset($_POST['directors'])) {
	$connect = connectToDB();
	$school = addslashes($_REQUEST['school']);
	$band_name = addslashes($_REQUEST['band_name']);
	$city_town = addslashes($_REQUEST['city_town']);
	$directors = addslashes($_REQUEST['directors']);
	$colors = $_REQUEST['colors'];
	$address = addslashes($_REQUEST['address']);
	$website = $_REQUEST['website'];
	$pic_url = $_REQUEST['pic_url'];
	
	$query = "INSERT INTO Bands (School, BandName, Town, Directors, Colors, Address, WebsiteURL, PicURL)";
	$query = $query . " VALUES ('$school', '$band_name', '$city_town', '$directors', '$colors', '$address', '$website', '$pic_url')";
	mysql_query($query);
	$message =  "Added band: '$school' Successfully";
	
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
				<h1>Add Band</h1>
				* - Required Fields
				<form action="add_band.php" method="post">
				<table border=0>
				<tr><td>School:</td><td><input type="text" name="school" />*</td></tr>
				<tr><td>Band Name:</td><td><input type="text" name="band_name" />*</td></tr>
				<tr><td>City/Town:</td><td><input type="text" name="city_town" />*</td></tr>
				<tr><td>Directors:</td><td><input type="text" name="directors" />*</td></tr>
				<tr><td>Colors:</td><td><input type="text" name="colors" /></td></tr>
				<tr><td>Address:</td><td><input type="text" name="address" /></td></tr>
				<tr><td>Website URL:</td><td><input type="text" name="website" /></td></tr>
				<tr><td>Picture URL:</td><td><input type="text" name="pic_url" /></td></tr>

				<tr><td><input type="submit" value="Add Band"/></td></tr></table>
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
