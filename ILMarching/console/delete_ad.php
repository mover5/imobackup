<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');
$modify = false;
if (check_login_status() == false || (get_login_role() != "admin")) {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
	$connect = connectToDB();
}


if (isset($_POST['select'])) {
	$AdID = $_POST['ad'];
	$query = "SELECT * FROM Ads WHERE AdID = $AdID";
	$result = mysql_query($query, $connect);
	$row = mysql_fetch_array($result);
	$image = $row['Image'];
	if (file_exists("../ads/".$image)) {
			unlink("../ads/".$image);
	}
	$query = "DELETE FROM Ads WHERE AdID = $AdID";
	mysql_query($query);
	$message = "Deleted Ad";
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
				<h1>Delete an Advertisement</h1><?php
						echo "<form action='delete_ad.php' method='POST'>";
						$query = "SELECT * FROM Ads ORDER BY Title";
						$result = mysql_query($query, $connect);
						echo "<select name='ad' id='ad'>";
						while ($row = mysql_fetch_array($result)) {
							$title = $row['Title'];
							$id = $row['AdID'];
							echo "<option value='$id'>$title</option>";
						}
						echo "</select>";
						echo "<input type='submit' value='Delete Ad' name='select' id='select' />";
						echo "</form>";?>
				
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
