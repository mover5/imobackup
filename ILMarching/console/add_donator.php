<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');

if (check_login_status() == false || (get_login_role() != "admin")) {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
}
if (isset($_POST['addsubmit'])) {
	$connect = connectToDB();
	$name = $_POST['name'];
	$value = $_POST['amount'];
	echo $value;
	if ($name <> "" || $value <> "" || $name <> NULL || $value <> NULL) {
		$query = "SELECT * FROM Donations WHERE Donator = '$name'";
		$result = mysql_query($query, $connect);
		if (mysql_num_rows($result) == 0) {
			$query = "INSERT INTO Donations (Donator, Amount) VALUES ('$name', '$value')";
			mysql_query($query, $connect);
		} else {
			$message = "A Donation exists for this person. Please modify the amount";
		}
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
				<h1>Add Donator</h1>
				<form action='add_donator.php' method='POST'>
				<table border=0>
					<tr>
						<td>Donator Name:</td><td><input type='text' name='name' id='name' /></td>
					</tr>
					<tr>
						<td>Donation Amount:</td><td><input type='text' name='amount' id='amount' /></td>
					</tr>
					<tr>
						<td colspan='2'><input type='submit' name='addsubmit' id='addsubmit' value='Add Donator' /></td>
					</tr>
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

	<div id="footer"><?php include('footer.html');?><div>
</div>

</body>
</html>
