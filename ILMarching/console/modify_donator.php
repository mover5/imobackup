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

if (isset($_POST['donatorID'])) {
	$modify = true;
}
if (isset($_POST['name']) && isset($_POST['amount'])) {
	$name = $_REQUEST['name'];
	$amount = $_REQUEST['amount'];
	$donatorID = $_REQUEST['donatorID'];
	if (is_numeric($amount)) {
		$query = "UPDATE Donators SET name = '$name', amount = '$amount' WHERE donatorID = '$donatorID'";
		
		mysql_query($query);
		
		$message = "Modified Donation Successfully";
	} else {
		$message = "The donation amount must be a number";
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
				<h1>Modify Donation</h1>
				<?php
				if ($modify != true) {
					$query = "SELECT donatorID, name FROM Donators ORDER BY name ASC";

					$Result=mysql_query($query);
					
					?>
					
					<form action="modify_donator.php" method="post">
					<select name="donatorID"><option value=""></option><?php
					while($Row = mysql_fetch_array($Result))
					{
						$name = $Row['name'];
						$donatorID = $Row['donatorID'];
						echo "<option value=\"$donatorID\">".$name."</option>";
					
					}
					
					?>
					</select>
					<input type="submit" value="Modify Donation" />
					</form>
				<?php } 
				if ($modify == true) {
				$query = "SELECT * FROM Donators WHERE donatorID = '".$_REQUEST['donatorID']."' ORDER BY name";

				$Result=mysql_query($query);
				$options = "";
				while ($Row = mysql_fetch_array($Result))
				{
					 $name = $Row['name'];
					 $amount = $Row['amount'];					 
				}
				echo "<form action=\"modify_donator.php\" method=\"post\">";
				echo "<table border=0>";
				echo "<input type=\"hidden\" id=\"donatorID\" name=\"donatorID\" value=\"".$_REQUEST['donatorID']."\"/></td></tr>";
				echo "<tr><td>Donator Name:</td><td><input type=\"text\" name=\"name\" value=\"$name\"/></td></tr>";
				echo "<tr><td>Donation Amount:</td><td>$<input type=\"text\" name=\"amount\" value=\"$amount\"/></td></tr>";
				echo "<tr><td><input type=\"submit\" value=\"Modify Donation\"/></td></tr>";
				echo "</table>";
				echo "</form>";
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