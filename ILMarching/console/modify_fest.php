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

if (isset($_POST['modifysubmit'])) {
	$modify = true;
}
if (isset($_POST['f']) && isset($_POST['name']) && isset($_POST['website']) && isset($_POST['location']) && isset($_POST['contact'])) {
	$f = $_POST['f'];
	$name = $_POST['name'];
	$month = $_POST['month'];
	$day = $_POST['day'];
	$year = $_POST['year'];
	
	$date = $year . "-" . $month . "-" . $day;
	$website = $_POST['website'];
	$location = addslashes($_POST['location']);
	$contact = addslashes($_POST['contact']);
	$comments = addslashes(nl2br($_POST['comments']));
	$address = addslashes($_POST['address']);
	$judges = addslashes(nl2br($_POST['judges']));
	$moddate = date("Y-m-d");
	$query = "UPDATE Festivals SET Name = '$name', Date = '$date', WebsiteURL = '$website', Location = '$location', Address = '$address', Contact = '$contact', Judges = '$judges', Details = '$comments', FestivalYear = '$year', UpdatedBy = '$username', LastUpdated = '$moddate' WHERE FestivalID = '$f'";
	mysql_query($query);
	$message = "Updated $year - $name successfully";
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
	$("#FestivalName").autocomplete("ajax/get_festival_list_mod_fest.php", {
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
				<h1>Modify Festival</h1>
				<?php
				if ($modify != true) { ?>
				<form name='SelectFestival' action='modify_fest.php' method='POST' autocomplete="off">
					Festival <label>:</label>
					<input type="text" name="FestivalName" id="FestivalName" size='50'/><input type='hidden' name='FestivalID' id='FestivalID' value=''/> 
					<input type='hidden' name='modifysubmit' id='modifysubmit' value='Submit' />
				<input type='button' id='modifybutton' name='modifybutton' value='Select Festival' onClick='validateID()'/><br />(Just start typing the Festival Name)
				</form>
				
				<?php } 
				if ($modify == true) {
					$f = $_POST['FestivalID'];
					$query = "SELECT * FROM Festivals WHERE FestivalID = '".$f."' ORDER BY Date";

					$result=mysql_query($query);
					
					while ($Row = mysql_fetch_array($result))
					{
						$name = $Row['Name'];
						$date = $Row['Date'];
						$location = $Row['Location'];
						$website = $Row['WebsiteURL'];
						$contact = $Row['Contact'];
						$address = $Row['Address'];
						$judges = str_replace("<br />", "", $Row['Judges']);
						$judges = str_replace("<br>", "", $judges);
						$judges = str_replace("<p>", "", $judges);
						$comments = str_replace("<br />", "", $Row['Details']);	
						$comments = str_replace("<br>", "", $comments);
						$comments = str_replace("<p>", "", $comments);
					}
					$temp = strtotime($date);
					$year = date("Y", $temp);
					$month = date("m", $temp);
					$day = date("d", $temp);
					echo "<h1>Modify Festival: $year - $name</h1>";
					echo "<form name=\"form\" action=\"modify_fest.php\" method=\"post\">";
					echo "<input type=\"hidden\" name=\"f\" value=\"$f\" />";
					echo "<table border=0>";
					echo "<tr><td>Festival Name:</td><td colspan=2><input type=\"text\" size='100' name=\"name\" value=\"$name\"/></td></tr>";
					//echo "<tr><td>Festival Date:</td><td colspan=2><input type=\"text\" name=\"date\" value=\"$date\"/> (YYYY-MM-DD Format Only!)</td></tr>";
					
					
					echo "<tr><td>Festival Date:</td><td colspan=2>";
					
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


					echo "</select></td></tr>";
					
					echo "<tr><td>Festival Location:</td><td colspan=2><input type=\"text\" size='100' name=\"location\" value=\"$location\"/></td></tr>";
					echo "<tr><td>Festival Address:</td><td colspan=2><input type=\"text\" size='100' name=\"address\" value=\"$address\"/></td></tr>";
					echo "<tr><td>Festival Website:</td><td colspan=2><input type=\"text\" size='100' name=\"website\" value=\"$website\"/></td></tr>";
					echo "<tr><td>Festival Contact:</td><td colspan=2><input type=\"text\" size='100' name=\"contact\" value=\"$contact\"/></td></tr>";

					echo "<tr><td>&nbsp;</td><td><input type=\"button\" value=\"Bold\" onclick=\"addTags(this.form.comments, '<b>', '</b>')\"><input type=\"button\" value=\"Italic\" onclick=\"addTags(this.form.comments, '<i>', '</i>')\"><input type=\"button\" value=\"Underline\" onclick=\"addTags(this.form.comments, '<u>', '</u>')\"></td><td>&nbsp;</td></tr>";
					echo "<tr><td valign='top'>Festival Details:</td><td colspan=2><textarea rows=\"10\" cols=\"80\" name=\"comments\">$comments</textarea></td></tr>";
					echo "<tr><td valign='top'>Festival Judges:</td><td colspan=2><textarea rows=\"10\" cols=\"80\" name=\"judges\">$judges</textarea></td></tr>";
					echo "<tr><td colspan=3><input type=\"submit\" value=\"Modify Festival\"/></td></tr>";
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
