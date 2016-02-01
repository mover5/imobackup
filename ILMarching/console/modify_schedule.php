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
	$festid = $_POST['FestivalID'];
	$query = "SELECT Name, FestivalYear FROM Festivals WHERE FestivalID = $festid";
	$result = mysql_query($query, $connect);
	$row = mysql_fetch_array($result);
	$FestivalName = $row['Name'];
	$FestivalYear = $row['FestivalYear'];
} elseif (isset($_POST['changesubmit'])) {
		$festid = $_POST['FestivalID'];
		$query = "SELECT AddedBy FROM Schedules WHERE FestivalID = $festid LIMIT 1";
		$result = mysql_query($query, $connect);
		$row = mysql_fetch_array($result);
		$addedby = $row['AddedBy'];
		$query = "DELETE FROM Schedules WHERE FestivalID = $festid";
		mysql_query($query, $connect);
		for ($row = 1; $row <= $_POST['num_rows']; $row++) {
			$time = $_POST['time_'.$row];
			$ampm = $_POST['ampm_'.$row];
			$bandid = $_POST['BandID_'.$row];
			$bandname = $_POST['bandname_'.$row];
			if ($bandid == "") {
				$band = "BandName";
				$bandval = "'" . $bandname . "'";
			} else {
				$band = "BandID";
				$bandval = $bandid;
			}
			$class = $_POST['class_'.$row];
			$type = $_POST['type_'.$row];
			if ($time <> '') {
				$perftime = date("G:i", strtotime($time." ".$ampm));
			} else {
				$perftime = "NULL";
			}

			$delete = false;
			if (isset($_POST['delete_'.$row])) {
				$delete = true;
			}
			if (!$delete) {
				if (($bandid <> "" || $bandname <> "" || $class == "Break" || $class == "Awards")) {
					$query = "INSERT INTO Schedules ($band, FestivalID, Class, Type, PerformanceTime, AddedBy, UpdatedBy) VALUES ($bandval, $festid, '$class', '$type', '$perftime', '$addedby', '$username')";
					mysql_query($query, $connect);
				}	
			}

		} //End For Loop
		
		$moddate = date("Y-m-d");
		$query = "UPDATE Festivals SET UpdatedBy = '$username', LastUpdated = '$moddate' WHERE FestivalID = $festid";
		mysql_query($query, $connect);
		$message = "Updated Schedule Successfully";
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
<script type="text/javascript" src="js/addTag.js"></script>
<script type="text/javascript" src="js/date.js"></script>
<script type="text/javascript">
//anylinkcssmenu.init("menu_anchors_class") ////Pass in the CSS class of anchor links (that contain a sub menu)
anylinkcssmenu.init("anchorclass")

var num_rows = 0;


function addScoreRow(obj) {

	var id_arr = obj.id.split("_");
	if (document.getElementById("time_"+(parseInt(id_arr[1])+1)) == null) {
		num_rows++;
		
		var last_index = document.getElementById("bodytable").rows.length; // total number of rows on the screen
		document.getElementById('num_rows').value = parseInt(document.getElementById('num_rows').value) + 1;
		var new_row = document.getElementById("bodytable").insertRow(last_index);
		var c1 = new_row.insertCell(0);
		var c2 = new_row.insertCell(1);
		var c3 = new_row.insertCell(2);
		var c4 = new_row.insertCell(3);
		var c5 = new_row.insertCell(4);
		var c6 = new_row.insertCell(5);
		var c7 = new_row.insertCell(6);
		var c8 = new_row.insertCell(7);
		
		var band_ids = document.getElementById("BandID_1");
		var class_drop = document.getElementById("class_1");
		var type_drop = document.getElementById("type_1");
		var add_btn = document.getElementById("add_1");
		var del_box = document.getElementById("delete_1");
		var ampm = document.getElementById("ampm_1");
		
		var new_band_ids = band_ids.cloneNode(true);
		var new_class_drop = class_drop.cloneNode(true);
		var new_type_drop = type_drop.cloneNode(true);
		var new_add_btn = add_btn.cloneNode(true);
		var new_del_box = del_box.cloneNode(true);
		var new_ampm = ampm.cloneNode(true);
		
		new_band_ids.id = "BandID_"+(num_rows+1);
		new_class_drop.id = "class_"+(num_rows+1);
		new_type_drop.id = "type_"+(num_rows+1);
		new_add_btn.id = "add_"+(num_rows+1);
		new_del_box.id = "delete_"+(num_rows+1);
		new_ampm.id = "ampm_"+(num_rows+1);
		
		new_band_ids.name = "BandID_"+(num_rows+1);
		new_class_drop.name = "class_"+(num_rows+1);
		new_type_drop.name = "type_"+(num_rows+1);
		new_add_btn.name = "add_"+(num_rows+1);
		new_del_box.name = "delete_"+(num_rows+1);
		new_ampm.name = "ampm_"+(num_rows+1);
		
		new_class_drop.selectedIndex = 0;
		new_type_drop.selectedIndex = 0;
		new_band_ids.selectedIndex = 0;
		new_ampm.selectedIndex = 0;
		
		var c1name = "time_"+(num_rows+1);
		var c4name = "bandname_"+(num_rows+1);
		c1.innerHTML = "<input type='text' name='"+c1name+"' id='"+c1name+"' size='6' /> ";
		c1.appendChild(new_ampm);
		c2.appendChild(new_band_ids);
		c3.innerHTML = "&lt;-OR-&gt;";
		c4.innerHTML = "<input type='text' name='"+c4name+"' id='"+c4name+"' size='15' />";
		c5.appendChild(new_class_drop);
		c6.appendChild(new_type_drop);
		c7.appendChild(new_add_btn);
		c8.appendChild(new_del_box);
		
		document.getElementById(c1name).focus();
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
				<h1>Modify Schedule</h1>
				<?php if (!$displayform) { ?>
				<form action='modify_schedule.php' method='POST'>
				Festival: <select name='FestivalID'>
				<?php
				$query = "SELECT Schedules.FestivalID, Name, FestivalYear FROM Schedules, Festivals WHERE Festivals.FestivalID = Schedules.FestivalID GROUP BY FestivalID ORDER BY FestivalYear DESC, Date ASC, Name ASC";
				$result = mysql_query($query, $connect);
				while ($row = mysql_fetch_array($result)) {
					$FestivalID = $row['FestivalID'];
					$Name = $row['Name'];
					$FestivalYear = $row['FestivalYear'];
					echo "<option value='$FestivalID'>$FestivalYear - $Name</option>";
				}
				?>
				</select>
				<input type='submit' name='modifysubmit' value='Modify Schedule' />
				</form>
				<?php } else { 
					echo "<h2>$FestivalYear - $FestivalName</h2>";
					echo "<form action='modify_schedule.php' method='POST'>";
					$query = "SELECT PerformanceTime, Schedules.BandID, Schedules.BandName, Class, Type FROM Schedules LEFT JOIN Bands ON Schedules.BandID = Bands.BandID WHERE FestivalID = $festid ORDER BY Type ASC, PerformanceTime ASC, ScheduleID ASC";
					$result = mysql_query($query, $connect);
					echo "<table border=1 id='bodytable'><tr><th>Performance Time</th><th>Band*</th><th>OR</th><th>Band Name Only*</th><th>Class*</th><th>Performance Type*</th><th>Row Options</th><th>Delete Row?</th></tr>";
					$num_row = 0;
					while ($row = mysql_fetch_array($result)) {
						$num_row++;
						$perftime = $row['PerformanceTime'];
						$bandid = $row['BandID'];
						$bandname = $row['BandName'];
						$class = $row['Class'];
						$type = $row['Type'];
						$time = date("g:i", strtotime($perftime));
						$ampm = date("A", strtotime($perftime));
						
						echo "<tr>";
						
						if ($time == "12:00" && $ampm == "AM") {
							$time = "";
						}
						echo "<td><input type='text' name='time_".$num_row."' id='time_".$num_row."' size='6' value='$time' /> <select name='ampm_".$num_row."' id='ampm_".$num_row."'>";
						if ($ampm == "AM") {
							echo "<option value='AM' selected='selected'>AM</option><option value='PM'>PM</option>";
						} else {
							echo "<option value='AM'>AM</option><option value='PM' selected='selected'>PM</option>";
						}
						echo "</select></td>";
						
						//BandID
						if ($bandid == NULL) {$bandid = "";}
						echo "<td><select name='BandID_".$num_row."' id='BandID_".$num_row."'>
						<option value=''>...</option>";
						$q2 = "SELECT School, BandID FROM Bands ORDER BY School ASC";
						$r2 = mysql_query($q2, $connect);
						while ($row2 = mysql_fetch_array($r2)) {
							$QBandID = $row2['BandID'];
							$QSchool = $row2['School'];
							if ($QBandID == $bandid) {
								echo "<option selected='selected' value='$QBandID'>$QSchool</option>";
							} else {
								echo "<option value='$QBandID'>$QSchool</option>";
							}
							
						}
						echo "</select></td>";
						
						echo "<td>&lt;-OR-&gt;</td>";
						
						//BandName
						if ($bandname == NULL) {$bandname = "";}
						echo "<td><input type='text' size='15' name='bandname_".$num_row."' id='bandname_".$num_row."' value='$bandname' /></td>";
						
						//Class
						echo "<td><select name='class_".$num_row."' id='class_".$num_row."'>";
						$q3 = "SELECT Class FROM ClassOrder ORDER BY OrderNum ASC";
						$r3 = mysql_query($q3, $connect);
						while ($row3 = mysql_fetch_array($r3)) {
							$QClass = $row3['Class'];
							if ($QClass == $class) {
								echo "<option selected='selected' value='$QClass'>$QClass</option>";
							} else {
								echo "<option value='$QClass'>$QClass</option>";
							}
							
						}
						echo "</select></td>";
						
						//Type
						echo "<td><select name='type_".$num_row."' id='type_".$num_row."'>";
						if ($type == "Field") {
							echo "<option selected='selected' value='Field'>Field</option>
								<option value='Parade'>Parade</option>
								<option value='Field2'>Field Day 2</option>";
						} elseif ($type == "Parade") {
							echo "<option value='Field'>Field</option>
								<option selected='selected' value='Parade'>Parade</option>
								<option value='Field2'>Field Day 2</option>";
						} elseif ($type == "Field2") {
							echo "<option value='Field'>Field</option>
								<option value='Parade'>Parade</option>
								<option selected='selected' value='Field2'>Field Day 2</option>";
						}
						echo "</select></td>";
						
						//Add Row Button
						echo "<td><input name='add_".$num_row."' id='add_".$num_row."' type='button' value='Add Row' onclick='addScoreRow(this)' /></td>";
						
						//Delete Row Box
						echo "<td><input type='checkbox' name='delete_".$num_row."' id='delete_".$num_row."' /></td>";
						?>
						<script type="text/javascript">
							num_rows++;
						</script>
						<?php
						
						echo "</tr>";
					}	
					echo "</table>";
					?>
						<script type="text/javascript">
							num_rows--;
						</script>
						<?php
					echo "<input type='hidden' id='num_rows' name='num_rows' value='$num_row' />";	
					echo "<input type='hidden' id='FestivalID' name='FestivalID' value='$festid' />";	
					
					echo "<br /><input type='submit' name='changesubmit' id='changesubmit' value='Submit Changes' />";
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
