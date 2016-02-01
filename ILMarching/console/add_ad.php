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

if (isset($_POST['submit'])) {
	if ($_POST['title'] <> "" && $_POST['link'] <> "" && $_POST['contact'] <> "" && $_POST['email'] <> "" && $_POST['price'] <> "") {
		$title = $_POST['title'];
		$link = $_POST['link'];
		$contact = $_POST['contact'];
		$email = $_POST['email'];
		$price = $_POST['price'];
		$month = $_POST['month'];
		$day = $_POST['day'];
		$year = $_POST['year'];
		
		$date = $year . "-" . $month . "-" . $day;
		
		if (isset($_POST['sidead'])) {
			$banner = 0;
		} else {
			$banner = 1;
		}
		$file = $_FILES["file"];
		$filename = $file['name'];
		$newfilename = str_replace(" ", "", $filename);
		move_uploaded_file($file['tmp_name'], "../ads/".$newfilename);
		$query = "INSERT INTO Ads (Title, Image, Link, Contact, Email, Price, Banner, ExpirationDate) VALUES ('$title', '$newfilename', '$link', '$contact', '$email', '$price', '$banner', '$date')";
		mysql_query($query);
	} else {
		$message = "All Fields are Required";
	}
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
				<h1>Add a new Advertisement</h1>
				<form action='add_ad.php' method='POST' enctype='multipart/form-data'>
				<table>
				<tr><td>Ad Title: </td><td><input type='text' name='title' id='title' /></td></tr>
				<tr><td>Upload Ad Image: </td><td><input type='file' name='file' id='file' /></td></tr>
				<tr><td>Ad URL: </td><td><input type='text' name='link' id='link' /></td></tr>
				<tr><td>Ad Contact: </td><td><input type='text' name='contact' id='contact' /></td></tr>
				<tr><td>Ad Contact Email: </td><td><input type='text' name='email' id='email' /></td></tr>
				<tr><td>Ad Price: </td><td><input type='text' name='price' id='price' /></td></tr>
				<tr><td>Side Ad? </td><td><input type='checkbox' name='sidead' id='sidead' /></td></tr>
				<tr><td>Ad Expiration Date:</td>
				<td>
				Month: <select name="month" size="1"
				onchange="setDay(document.form.month.options[document.form.month.selectedIndex].value);">
				<option value="1" selected="selected">January</option>
				<option value="2">February</option>
				<option value="3">March</option>
				<option value="4">April</option>
				<option value="5">May</option>
				<option value="6">June</option>
				<option value="7">July</option>
				<option value="8">August</option>
				<option value="9">September</option>
				<option value="10">October</option>
				<option value="11">November</option>
				<option value="12">December</option>
				</select>
				
				 Day: <select name="day" size="1">
				 <option value="1" selected="selected">1</option>
				 <?php
				 
				 for ($i = 2; $i < 32; $i++) {
				 echo "<option value='$i'>$i</option>";
				 }
				 ?>

				</select>
				 Year: <select name="year" size="1">
				 <?php
				 
				 $year = date("Y");
				 for ($i = $year+2; $i >= 1995; $i--) {
					 if ($i == $year) {
						 echo "<option selected='selected' value='$i'>$i</option>";
					 } else {
						 echo "<option value='$i'>$i</option>";
					 }
				 
				 }
				 ?>

				</select></td></tr>
				<tr><td colspan='2'><input type='submit' name='submit' id='submit' value='Add Advertisment' /></td></tr>
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
