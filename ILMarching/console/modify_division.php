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
if (isset($_POST['modify'])) {
	$div2 = $_POST['div2max'];
	$div3 = $_POST['div3max'];
	$div4 = $_POST['div4max'];
	
	if ($div4 < $div3 && $div3 < $div2) {
		$query = "UPDATE Divisions SET MaxEnrollment = $div2 WHERE Division = 2";
		mysql_query($query, $connect);
		
		$query = "UPDATE Divisions SET MaxEnrollment = $div3 WHERE Division = 3";
		mysql_query($query, $connect);
		
		$query = "UPDATE Divisions SET MaxEnrollment = $div4 WHERE Division = 4";
		mysql_query($query, $connect);
	
		$message = "Divisions Modified Successfully";
	} else {
		$message = "Divisions Overlap. Please adjust";
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
anylinkcssmenu.init("anchorclass");

function changeLabel(obj, idtochange) {
	document.getElementById(idtochange).innerHTML = obj.value;
}

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
				$query = "SELECT * FROM Divisions";
				$result = mysql_query($query, $connect);
				$values = array();
				while ($row = mysql_fetch_array($result)) {
					$values[$row['Division']] = $row['MaxEnrollment'];
				}
				?>
				<h1>Modify Division Limits</h1>
				<form action='modify_division.php' method='post'>
				<table>
					<tr><td>Division 1:</td><td><label id='div1min'><?php echo $values[2]; ?></label> &lt; <b>Enrollment</b></td></tr>
					<tr><td>Division 2:</td><td><label id='div2min'><?php echo $values[3]; ?></label> &lt; <b>Enrollment</b> &lt;= <input type='text' name='div2max' id='div2max' size='4' value='<?php echo $values[2]; ?>' onkeyup='return changeLabel(this, "div1min")'/></td></tr>
					<tr><td>Division 3:</td><td><label id='div3min'><?php echo $values[4]; ?></label> &lt; <b>Enrollment</b> &lt;= <input type='text' name='div3max' id='div3max' size='4' value='<?php echo $values[3]; ?>' onkeyup='return changeLabel(this, "div2min")'/></td></tr>
					<tr><td>Division 4:</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Enrollment</b> &lt;= <input type='text' name='div4max' id='div4max' size='4' value='<?php echo $values[4]; ?>' onkeyup='return changeLabel(this, "div3min")' /></td></tr>
				</table>
				<input type='submit' name='modify' id='modify' value='Modify Divisions' />
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
