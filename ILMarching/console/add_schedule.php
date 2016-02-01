<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');

if (check_login_status() == false || (get_login_role() != "admin" && get_login_role() != "contributor")) {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
	$connect = connectToDB();
}

//Add Info To Database Here 
if (isset($_POST['schedulesubmit'])) {
	//Check if festival already has entries...if so, modify, dont add
	$festid = $_POST['FestivalID'];
	$query = "SELECT * FROM Schedules WHERE FestivalID = $festid";
	$result = mysql_query($query, $connect);
	$num = mysql_num_rows($result);
	if ($num == 0) {
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
			
			if (($bandid <> "" || $bandname <> "" || $class == "Break" || $class == "Awards")) {
				$query = "INSERT INTO Schedules ($band, FestivalID, Class, Type, PerformanceTime, AddedBy) VALUES ($bandval, $festid, '$class', '$type', '$perftime', '$username')";
				mysql_query($query, $connect);
			}		
		}
		$moddate = date("Y-m-d");
		$query = "UPDATE Festivals SET UpdatedBy = '$username', LastUpdated = '$moddate' WHERE FestivalID = $festid";
		mysql_query($query, $connect);
		$message = "Added Schedule Successfully";
	} else {
		$message = "There is already a schedule associated with this Festival. Please Modify it instead of adding a new one.";
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
<script type="text/javascript" src="js/addTag.js"></script>
<script type="text/javascript" src="js/date.js"></script>
<script type="text/javascript">
//anylinkcssmenu.init("menu_anchors_class") ////Pass in the CSS class of anchor links (that contain a sub menu)
anylinkcssmenu.init("anchorclass");

var num_rows = 0;

function addScheduleRow(obj) {
	
	
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
		
		var band_ids = document.getElementById("BandID_1");
		var class_drop = document.getElementById("class_1");
		var type_drop = document.getElementById("type_1");
		var add_btn = document.getElementById("add_1");
		var ampm = document.getElementById("ampm_1");
		
		var new_band_ids = band_ids.cloneNode(true);
		var new_class_drop = class_drop.cloneNode(true);
		var new_type_drop = type_drop.cloneNode(true);
		var new_add_btn = add_btn.cloneNode(true);
		var new_ampm = ampm.cloneNode(true);
		
		new_band_ids.id = "BandID_"+(num_rows+1);
		new_class_drop.id = "class_"+(num_rows+1);
		new_type_drop.id = "type_"+(num_rows+1);
		new_add_btn.id = "add_"+(num_rows+1);
		new_ampm.id = "ampm_"+(num_rows+1);
		
		new_band_ids.name = "BandID_"+(num_rows+1);
		new_class_drop.name = "class_"+(num_rows+1);
		new_type_drop.name = "type_"+(num_rows+1);
		new_add_btn.name = "add_"+(num_rows+1);
		new_ampm.name = "ampm_"+(num_rows+1);
		
		classobj = document.getElementById("class_"+(last_index-1));
		typeobj = document.getElementById("type_"+(last_index-1));
		ampmobj = document.getElementById("ampm_"+(last_index-1));
		
		new_class_drop.selectedIndex = classobj.selectedIndex;
		new_type_drop.selectedIndex = typeobj.selectedIndex;
		new_ampm.selectedIndex = ampmobj.selectedIndex;
		
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
				<h1>Add Festival Schedule</h1>
				* - Required Fields<p>
				<form action='add_schedule.php' method='POST'>
				<table id='headtable'>

				<tr><td><b>Festival:</b></td><td><select name='FestivalID'>
				<?php
				$query = "SELECT FestivalID, Name, FestivalYear FROM Festivals ORDER BY FestivalYear DESC, Date ASC, Name ASC";
				$result = mysql_query($query, $connect);
				while ($row = mysql_fetch_array($result)) {
					$FestivalID = $row['FestivalID'];
					$Name = $row['Name'];
					$Year = $row['FestivalYear'];
					echo "<option value='$FestivalID'>$Year - $Name</option>";
				}
				
				?>
				</select></td></tr>
				</table><br />
				<table id='bodytable' border=1>
				<tr><th>Performance Time</th><th>Band*</th><th>OR</th><th>Band Name Only*</th><th>Class*</th><th>Performance Type*</th><th>Row Options</th></tr>
				<tr id='schedule_row'>
				<td><input type='text' name='time_1' id='time_1' size='6' /> <select name='ampm_1' id='ampm_1'><option value='AM'>AM</option><option value='PM'>PM</option></select></td>
				<td><select name='BandID_1' id='BandID_1'>
				<option value=''>...</option>
				<?php
				$query = "SELECT School, BandID FROM Bands ORDER BY School ASC";
				$result = mysql_query($query, $connect);
				while ($row = mysql_fetch_array($result)) {
					$BandID = $row['BandID'];
					$School = $row['School'];
					echo "<option value='$BandID'>$School</option>";
				}
				?>
				</select></td>
				<td>&lt;-OR-&gt;</td>
				<td><input type='text' name='bandname_1' id='bandname_1' size='15' /></td>
				<td><select name='class_1' id='class_1'>
				<?php
				$query = "SELECT Class FROM ClassOrder ORDER BY OrderNum ASC";
				$result = mysql_query($query, $connect);
				while ($row = mysql_fetch_array($result)) {
					$class = $row['Class'];
					echo "<option value='$class'>$class</option>";
				}
				?>
				</select></td>
				<td><select name='type_1' id='type_1'>
				<option value='Field'>Field</option>
				<option value='Parade'>Parade</option>
				<option value='Field2'>Field Day 2</option>
				</select></td>
				<td><input name='add_1' id='add_1' type='button' value='Add Row' onclick='addScheduleRow(this)' /></td>
				</tr>
				</table>
				
				<input type='hidden' id='num_rows' name='num_rows' value='1' />
				<p>
				<input type='submit' name='schedulesubmit' id='schedulesubmit' value='Add Schedule' />
				</p>
				<br />
				
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
