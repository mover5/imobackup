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
if (isset($_POST['email'])) {
	$modify = false;
	$email = $_POST['email'];
	$bandid = $_POST['band'];
	$id = $_POST['emailid'];
	
	$query = "UPDATE BandDirectorEmail SET BandID = $bandid, Email = '$email' WHERE EmailID = $id";
	mysql_query($query, $connect);
	$message = "Updated Email Successfully";
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
				<h1>Modify Director Email</h1>
				<?php
				if ($modify != true) {
					$query = "SELECT EmailID, Director FROM BandDirectorEmail ORDER BY Director";
					$result = mysql_query($query);
				?>
				<form action="modify_director_email.php" method="post">
					<select name="id" id="id">
						<?php
							while ($row = mysql_fetch_array($result)) {
								echo "<option value=".$row['EmailID'].">".$row['Director']."</option>";
							}
						?>
					</select>
					<input type="submit" value="Submit" name="submit" />
				</form>
				<?php } 
				if ($modify == true) {
					$query = "SELECT * FROM BandDirectorEmail WHERE EmailID = " . $_POST['id'];
					$result = mysql_query($query);
					$row = mysql_fetch_array($result);
					$director = $row['Director'];
					$email = $row['Email'];
					$bandid = $row['BandID'];
				?>
				<form action="modify_director_email.php" method="post">
					<h2><?php echo $director; ?></h2>
					<input type="hidden" name="emailid" value="<?php echo $_POST['id']?>" />
					<table border=0>
					<tr><td>Band: </td><td>
					<select name="band" id="band">
					<?php
					$bandquery = "SELECT BandID, School FROM Bands";
					$bandresult = mysql_query($bandquery, $connect);
					while ($bandrow = mysql_fetch_array($bandresult)) {
						$id = $bandrow['BandID'];
						$school = $bandrow['School'];
						if ($id == $bandid) {
							echo "<option value='$id' selected='selected'>$school</option>";
						} else {
							echo "<option value='$id'>$school</option>";
						}
					}
					?>
				</select>
					</td></tr>
						<tr><td>Email: </td><td>
						<input type='text' name='email' id='email' value='<?php echo $email; ?>' />
						</td></tr>
						<tr><td><input type="submit" name="submit" id="submit" value="Submit" /></td></tr>
					</table>
				</form>
				<?php
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
