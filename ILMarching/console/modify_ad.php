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
$change = false;
if (isset($_POST['select'])) {
	$change = true;
	$AdID = $_POST['ad'];
	$query = "SELECT * FROM Ads WHERE AdID = $AdID";
	$result = mysql_query($query, $connect);
	$row = mysql_fetch_array($result);
}
if (isset($_POST['submit'])) {
	if ($_FILES['file']['name'] <> "") {
		if (file_exists("../ads/".$_POST['oldfile'])) {
			unlink("../ads/".$_POST['oldfile']);
		}
		$file = $_FILES["file"];
		$filename = $file['name'];
		$newfilename = str_replace(" ", "", $filename);
		move_uploaded_file($file['tmp_name'], "../ads/".$newfilename);
		$qfile = "Image = '$newfilename', ";
	} else {
		$qfile = "";
	}
		$title = $_POST['title'];
		$link = $_POST['link'];
		$contact = $_POST['contact'];
		$email = $_POST['email'];
		$price = $_POST['price'];
		$month = $_POST['month'];
		$day = $_POST['day'];
		$year = $_POST['year'];
		$id = $_POST['id'];
		$date = $year . "-" . $month . "-" . $day;
		
		if (isset($_POST['sidead'])) {
			$banner = 0;
		} else {
			$banner = 1;
		}
		$query = "UPDATE Ads SET Title = '$title', $qfile Link = '$link', Contact = '$contact', Email = '$email', Price = '$price', Banner = '$banner', ExpirationDate = '$date' WHERE AdID = $id";
		mysql_query($query);
		$message = "Updated Ad";
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
				<h1>Modify an Advertisement</h1>
				<?php if (!($change)) {  
						echo "<form action='modify_ad.php' method='POST'>";
						$query = "SELECT * FROM Ads ORDER BY Title";
						$result = mysql_query($query, $connect);
						echo "<select name='ad' id='ad'>";
						while ($row = mysql_fetch_array($result)) {
							$title = $row['Title'];
							$id = $row['AdID'];
							echo "<option value='$id'>$title</option>";
						}
						echo "</select>";
						echo "<input type='submit' value='Modify Ad' name='select' id='select' />";
						echo "</form>";
				} else { 
					$title = $row['Title'];
					$link = $row['Link'];
					$contact = $row['Contact'];
					$image = $row['Image'];
					$email = $row['Email'];
					$price = $row['Price'];
					$banner = $row['Banner'];
					$date = $row['ExpirationDate'];
					$temp = strtotime($date);
					$year = date("Y", $temp);
					$month = date("m", $temp);
					$day = date("d", $temp);
					?>
					<form action='modify_ad.php' method='POST' enctype='multipart/form-data'>
					<input type='hidden' name='id' id='id' value='<?php echo $AdID; ?>' />
					<table>
					<tr><td>Ad Title: </td><td><input type='text' name='title' id='title' value='<?php echo $title; ?>'/></td></tr>
					<?php 
					if ($banner == 1) {
					?>
						<tr><td>Image Preview: </td><td><img src='../ads/<?php echo $image; ?>' width='300' height='55' /><input type='hidden' name='oldfile' id='oldfile' value='<?php echo $image; ?>' /></td></tr>
					<?php
					} else {
					?>
						<tr><td>Image Preview: </td><td><img src='../ads/<?php echo $image; ?>' width='90' height='150' /><input type='hidden' name='oldfile' id='oldfile' value='<?php echo $image; ?>' /></td></tr>
					<?php
					}					
					?>
					<tr><td>Upload New Image?</td><td><input type='file' name='file' id='file' /></td></tr>
					<tr><td>Ad URL: </td><td><input type='text' name='link' id='link' value='<?php echo $link; ?>'/></td></tr>
					<tr><td>Ad Contact: </td><td><input type='text' name='contact' id='contact' value='<?php echo $contact; ?>'/></td></tr>
					<tr><td>Ad Contact Email: </td><td><input type='text' name='email' id='email' value='<?php echo $email; ?>'/></td></tr>
					<tr><td>Ad Price: </td><td><input type='text' name='price' id='price' value='<?php echo $price; ?>'/></td></tr>
					<tr><td>Side Ad? </td><td>
					<?php 
					if ($banner == 1) {
						echo "<input type='checkbox' name='sidead' id='sidead' />";
					} else {
						echo "<input type='checkbox' name='sidead' id='sidead' checked='checked'/>";
					}					
					?>
					</td></tr>
					<tr><td>Ad Expiration Date:</td><td><?php
					echo "Month: <select name=\"month\" size=\"1\"";
					echo "onchange=\"setDay(document.form.month.options[document.form.month.selectedIndex].value);\">";
					if ($month == 1) {
						echo "<option value=\"1\" selected=\"selected\">January</option>";
					} else {
						echo "<option value=\"1\">January</option>";
					}
					if ($month == 2) {
						echo "<option value=\"2\" selected=\"selected\">February</option>";
					} else {
						echo "<option value=\"2\">February</option>";
					}
					if ($month == 3) {
						echo "<option value=\"3\" selected=\"selected\">March</option>";
					} else {
						echo "<option value=\"3\">March</option>";
					}
					if ($month == 4) {
						echo "<option value=\"4\" selected=\"selected\">April</option>";
					} else {
						echo "<option value=\"4\">April</option>";
					}
					if ($month == 5) {
						echo "<option value=\"5\" selected=\"selected\">May</option>";
					} else {
						echo "<option value=\"5\">May</option>";
					}
					if ($month == 6) {
						echo "<option value=\"6\" selected=\"selected\">June</option>";
					} else {
						echo "<option value=\"6\">June</option>";
					}
					if ($month == 7) {
						echo "<option value=\"7\" selected=\"selected\">July</option>";
					} else {
						echo "<option value=\"7\">July</option>";
					}
					if ($month == 8) {
						echo "<option value=\"8\" selected=\"selected\">August</option>";
					} else {
						echo "<option value=\"8\">August</option>";
					}
					if ($month == 9) {
						echo "<option value=\"9\" selected=\"selected\">September</option>";
					} else {
						echo "<option value=\"9\">September</option>";
					}
					if ($month == 10) {
						echo "<option value=\"10\" selected=\"selected\">October</option>";
					} else {
						echo "<option value=\"10\">October</option>";
					}
					if ($month == 11) {
						echo "<option value=\"11\" selected=\"selected\">November</option>";
					} else {
						echo "<option value=\"11\">November</option>";
					}
					if ($month == 12) {
						echo "<option value=\"12\" selected=\"selected\">December</option>";
					} else {
						echo "<option value=\"12\">December</option>";
					}
					
					echo "</select> ";
					
					 echo "Day: <select name=\"day\" size=\"1\">";

					if ($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10 || $month == 12) {
						for ($i = 1; $i < 32; $i++) {
							if ($day == $i) {
								echo "<option value='$i' selected='selected'>$i</option>";
							} else {
								echo "<option value='$i'>$i</option>";
							}
							
						}
					} else if ($month == 4 || $month == 6 || $month == 9 || $month == 11) {
						for ($i = 1; $i < 31; $i++) {
							if ($day == $i) {
								echo "<option value='$i' selected='selected'>$i</option>";
							} else {
								echo "<option value='$i'>$i</option>";
							}
							
						}
					} else if ($month == 2) {
						for ($i = 1; $i < 30; $i++) {
							if ($day == $i) {
								echo "<option value='$i' selected='selected'>$i</option>";
							} else {
								echo "<option value='$i'>$i</option>";
							}
							
						}
					}
					 


					echo "</select> ";
					
					 echo "Year: <select name=\"year\" size=\"1\">";

					 
					 $countyear = date("Y");
					 for ($i = $countyear+2; $i >= 1995; $i--) {
						if ($year == $i) {
							echo "<option value='$i' selected='selected'>$i</option>";
						} else {
							echo "<option value='$i'>$i</option>";
						}
					 }


					echo "</select></td></tr>";?>
					</tr>
					<tr><td colspan='2'><input type='submit' name='submit' id='submit' value='Modify Advertisment' /></td></tr>
					</table>
					</form>
				<?php } ?>
				
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
