<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');
$modify = false;
if (check_login_status() == false || (get_login_role() != "admin" && get_login_role() != "contributor")) {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
	$connect = connectToDB();
}
$displayform = false;
if (isset($_POST['modifysubmit'])) {
	$displayform = true;
	$FestivalName = $_POST['FestivalName'];
	$query = "SELECT * FROM Festivals WHERE Name = '$FestivalName' ORDER BY FestivalYear DESC LIMIT 1";
	$result = mysql_query($query);
	if (mysql_num_rows($result) == 0) {
		$filldata = 0;
	} else {
		$filldata = 1;
		$row = mysql_fetch_array($result);
	}
} elseif (isset($_POST['changesubmit'])) {
	if (isset($_POST['name']) && isset($_POST['location']) && isset($_POST['website']) && isset($_POST['contact'])) {
		$name = $_POST['name'];

		$month = $_POST['month'];
		$day = $_POST['day'];
		$year = $_POST['year'];
		
		$date = $year . "-" . $month . "-" . $day;
		$website = $_POST['website'];
		$location = addslashes($_POST['location']);
		$contact = addslashes($_POST['contact']);
		$address = addslashes($_POST['address']);
		$judges = addslashes(nl2br($_POST['judges']));
		$comments = addslashes(nl2br($_POST['comments']));
		$moddate = date("Y-m-d");
		
		$query = "INSERT INTO Festivals (Name, Date, FestivalYear, Location, Address, WebsiteURL, Contact, Details, Judges, UpdatedBy, LastUpdated)";
		$query = $query . " VALUES ('$name', '$date', '$year', '$location', '$address', '$website', '$contact', '$comments', '$judges', '$username', '$moddate')";
		mysql_query($query);
		$message = "Added Festival: $name";
	} else {
		$message = "Please enter all required fields";
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


$().ready(function() {
	$("#FestivalName").autocomplete("ajax/get_festival_list_for_add_fest.php", {
		width: 260,
		matchContains: true,
		//mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
		$("#FestivalName").result(function(event, data, formatted) {
			$("#FestivalID").val(data[1]);
		});
});

function validateID() {
	if (document.getElementById('FestivalID').value != "") {
		document.SelectFestival.submit();
	} else {
		alert("Please select a Festival from the Autocomplete box");
		return;
	}
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
				?>
				<h1>Add Festival</h1>
				<?php if (!$displayform) { ?>
				<form name='SelectFestival' action='add_fest.php' method='POST' autocomplete="off">
					Festival <label>:</label>
					<input type="text" name="FestivalName" id="FestivalName" size='50'/><input type='hidden' name='FestivalID' id='FestivalID' value=''/> 
					<input type='hidden' name='modifysubmit' id='modifysubmit' value='Submit' />
				<input type='submit' id='modifybutton' name='modifybutton' value='Select Festival' /><br />(Just start typing the Festival Name)
				</form>
				<?php } else { 

					
					echo "<form action='add_fest.php' method='POST'  enctype='multipart/form-data'>";
					echo "<h2>$FestivalName<input type='hidden' name='name' id='name' value='$FestivalName' /></h2>"; ?>
					<table border=0>
				
				<tr><td>Festival Date:</td>
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
				</select>*</td>
				<td>
				 Day: <select name="day" size="1">
				 <option value="1" selected="selected">1</option>
				 <?php
				 
				 for ($i = 2; $i < 32; $i++) {
				 echo "<option value='$i'>$i</option>";
				 }
				 ?>

				</select>*</td>
				<td>
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

				</select>*</td></tr>
				<?php
				if ($filldata) {
					$location = $row['Location'];
					$address = $row['Address'];
					$website = $row['WebsiteURL'];
					$contact = $row['Contact'];
					$details = $row['Details'];
				} else {
					$location = "";
					$address = "";
					$website = "";
					$contact = "";
					$details = "";
				}
				?>
				<tr><td>Festival Location:</td><td colspan = '3'><input type="text" size='60' name="location" value='<?php echo $location; ?>'/>*</td></tr>
				<tr><td>Festival Address:</td><td colspan = '3'><input type="text" size='60' name="address" value='<?php echo $address; ?>'/>*</td></tr>
				<tr><td>Festival Website:</td><td colspan = '3'><input type="text" size='60' name="website" value='<?php echo $website; ?>'/>*</td></tr>
				<tr><td>Festival Contact:</td><td colspan = '3'><input type="text" size='60' name="contact" value='<?php echo $contact; ?>'/>*</td></tr>
				<tr><td>&nbsp;</td><td colspan='3'><input type="button" value="Bold" onclick="addTags(this.form.comments, '<b>', '</b>')"><input type="button" value="Italic" onclick="addTags(this.form.comments, '<i>', '</i>')"><input type="button" value="Underline" onclick="addTags(this.form.comments, '<u>', '</u>')"></td></tr>
				<tr><td valign='top'>Festival Details:</td><td colspan = '3'><textarea rows="10" cols="70" name="comments"><?php echo $details; ?></textarea></td></tr>
				<tr><td valign='top'>Festival Judges:</td><td colspan = '3'><textarea rows="10" cols="70" name="judges"></textarea></td></tr>
				<tr><td colspan='4'><input type="submit" name='changesubmit' id='changesubmit' value="Add Festival"/></td></tr></table>
				
				<?php
					
					echo "</form>";
				
				} ?>
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
