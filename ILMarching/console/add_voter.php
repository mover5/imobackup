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

if (isset($_POST['email'])) {
	
	$email = trim($_POST['email']);
	$query = "SELECT * FROM BandsOfTheYearVoters WHERE Email = '$email'";
	$result = mysql_query($query);
	if (mysql_num_rows($result) == 0)  {
		$query = "INSERT INTO BandsOfTheYearVoters (Email, Voted) VALUES ('$email', 0)";
		mysql_query($query);
		$message =  "Added voter '$email' Successfully";
	} else {
		$message = "A voter with that email already exists";
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
				<h1>Add BotY Voter</h1>
				* - Required Fields
				<form action="add_voter.php" method="post">
				<table border=0>
				<tr><td>Email:</td><td><input type="text" name="email" id="email" />*</td></tr>
				<tr><td><input type="submit" name="submit" id="submit" value="Submit" /></td></tr>									
				</table>
				</form><p></p>
				<table border='1'><tr><th align='left'>Email</th><th align='left'>Voted</th></tr>
				<?php
				$query = "SELECT Email, Voted FROM BandsOfTheYearVoters WHERE 1 ORDER BY Email ASC";
				$result = mysql_query($query);
				while ($row =  mysql_fetch_array($result)) {
					echo "<tr>";
					echo "<td>".$row['Email']."</td>";
					if ($row['Voted'] == 1) {
						$voted = "Yes";
					} else {
						$voted = "No";
					}
					echo "<td>$voted</td>";
					echo "</tr>";
				}
				?>
				</table>
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
