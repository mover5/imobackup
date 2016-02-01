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
if (isset($_POST['scoresubmit'])) {
	//Check if festival already has entries...if so, modify, dont add
	$festid = $_POST['FestivalID'];
	$calculatePlaces = false;
	$query = "SELECT * FROM Scores WHERE FestivalID = $festid";
	$result = mysql_query($query, $connect);
	$num = mysql_num_rows($result);
	if ($num == 0) {
		$message = "";
		for ($row = 1; $row <= $_POST['num_rows']; $row++) {
			$place = $_POST['place_'.$row];
			$score = $_POST['score_'.$row];
			$bandid = $_POST['BandID_'.$row];
			$bandname = $_POST['bandname_'.$row];
			if (isset($_POST['tie_'.$row])) {
				$tie = 1;
			} else {
				$tie = 0;
			}
			if ($bandid == "") {
				$band = "BandName";
				$bandval = "'" . $bandname . "'";
			} else {
				$band = "BandID";
				$bandval = $bandid;
			}
			$captions = $_POST['captions_'.$row];
			$class = $_POST['class_'.$row];
			$type = $_POST['type_'.$row];
			
			if ($score <> "") {
				$calculatePlaces = true;
				$place = 'NULL';
			} else {
				$score = 'NULL';
			}
			
			if ($place == "" && $score == 'NULL') {
				$place = '99';
			}			
			if (($bandid <> "" || $bandname <> "")) {
				$query = "INSERT INTO Scores ($band, FestivalID, ClassPlace, Score, Captions, Class, Type, Tiebreaker, AddedBy) VALUES ($bandval, $festid, $place, $score, '$captions', '$class', '$type', $tie, '$username')";
				mysql_query($query, $connect);
			}		
		}
		$gcid = $_POST['GCBandID'];
		$gcbandname = $_POST['GCbandname'];
		$notes = $_POST['notes'];
		if ($gcid == "") {
			$band = "GrandChampionBandName";
			$bandval = "'" . $gcbandname . "'";
		} else {
			$band = "GrandChampionID";
			$bandval = $gcid;
		}
		$query = "UPDATE Festivals SET $band = $bandval,  ScoreNotes = '$notes' WHERE FestivalID = $festid";
		mysql_query($query, $connect);
		if ($calculatePlaces) {
			$query = "SELECT Scores.ScoreID, Scores.BandName, Bands.BandID, Scores.ClassPlace, Score, Captions,  Scores.Class, Type, School 
			FROM Scores 
			LEFT JOIN Bands ON Scores.BandID = Bands.BandID 
			JOIN ClassOrder ON ClassOrder.Class = Scores.Class 
			WHERE Scores.FestivalID = $festid ORDER BY Type DESC, ClassOrder.OrderNum, Score DESC, ClassPlace ASC";
			$result = mysql_query($query, $connect);

			$prevtype = "";
			$prevclass = "";
			$placenum = 0;

			while ($row = mysql_fetch_array($result)) {
				$placenum++;
				$type = $row['Type'];
				$class = $row['Class'];
				$school = $row['School'];
				if ($school == NULL || $school == "") {$school = $row['BandName'];}
				$scoreid = $row['ScoreID'];
				

				
				if ($type <> $prevtype || $class <> $prevclass) {
					$placenum = 1;		
				}
				$prevtype = $type;
				$prevclass = $class;
				if ($row['ClassPlace'] == NULL || $row['ClassPlace'] == "") {
					$updateplace = "UPDATE Scores SET ClassPlace = $placenum WHERE ScoreID = $scoreid";
					mysql_query($updateplace, $connect);
				}
			}
		}
		
		//Add Files
		for ($row = 1; $row <= $_POST['num_file_rows']; $row++) {
			$linkname = $_POST['filename_'.$row];
			$file = $_FILES["file_".$row];
			$filename = $file['name'];
			$newfilename = str_replace(" ", "", $filename);
			if (($linkname == NULL || $linkname == "") && ($filename <> NULL && $filename <> "")) {
				$message .= "Please type a name for the document link. <br />";
			} else if ($linkname <> NULL && $linkname <> "" && $filename <> NULL && $filename <> ""){
				if ($file['error'] > 0) {
					$message .= "There was an Error uploading file ".$filename."<br />";
				} else {
					if (file_exists("../uploads/".$filename)) {
						$message .= "File ".$filename." already exists.<br />";
					} else {
						move_uploaded_file($file['tmp_name'], "../uploads/".$newfilename);
						$query = "INSERT INTO Documents (FestivalID, LinkName, LinkURL) VALUES ($festid, '$linkname', 'http://ilmarching.com/uploads/$newfilename')";
						mysql_query($query, $connect);
					}
				}
			}
		}

		
		$message .= "Added Scores Successfully";
	} else {
		$message = "Scores have already been added for this Festival. Modify them instead of adding new ones";
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
anylinkcssmenu.init("anchorclass");

var num_rows = 0;
var num_file_rows = 0;

$().ready(function() {
	$("#FestivalName").autocomplete("get_festival_list.php", {
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
		document.AddScores.submit();
	} else {
		alert("Please select a Festival from the Autocomplete box");
		return;
	}
}
function addFileRow(obj) {	
	var id_arr = obj.id.split("_");
	
	if (document.getElementById("filename_"+(parseInt(id_arr[1])+1)) == null) {
		num_file_rows++;
		var last_index = document.getElementById("filetable").rows.length; // total number of rows on the screen
		document.getElementById('num_file_rows').value = parseInt(document.getElementById('num_file_rows').value) + 1;
		
		var new_row = document.getElementById("filetable").insertRow(last_index);
		
		var c1 = new_row.insertCell(0);
		var c2 = new_row.insertCell(1);
		var c3 = new_row.insertCell(2);
		
		c3.align = 'right';
		
		var c1name = "filename_"+(num_file_rows+1);
		var c2name = "file_" +(num_file_rows+1);
		var c3name = "addfile_"+(num_file_rows+1);
		c1.innerHTML = "<input type='text' name='"+c1name+"' id='"+c1name+"' />";
		c2.innerHTML = "<input type='file' name='"+c2name+"' id='"+c2name+"' />";
		c3.innerHTML = "<input type='button' name='"+c3name+"' id='"+c3name+"' value='+' onclick='addFileRow(this)'/>";
		
		document.getElementById(c1name).focus();
	}
}

function addScoreRow(obj) {	
	var id_arr = obj.id.split("_");
	
	if (document.getElementById("score_"+(parseInt(id_arr[1])+1)) == null) {
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
		var c9 = new_row.insertCell(8);
		var c10 = new_row.insertCell(9);
		var c11 = new_row.insertCell(10);
		
		var band_ids = document.getElementById("BandID_1");
		var class_drop = document.getElementById("class_1");
		var type_drop = document.getElementById("type_1");
		var add_btn = document.getElementById("add_1");
		
		var new_band_ids = band_ids.cloneNode(true);
		var new_class_drop = class_drop.cloneNode(true);
		var new_type_drop = type_drop.cloneNode(true);
		var new_add_btn = add_btn.cloneNode(true);
		
		new_band_ids.id = "BandID_"+(num_rows+1);
		new_class_drop.id = "class_"+(num_rows+1);
		new_type_drop.id = "type_"+(num_rows+1);
		new_add_btn.id = "add_"+(num_rows+1);
		
		new_band_ids.name = "BandID_"+(num_rows+1);
		new_class_drop.name = "class_"+(num_rows+1);
		new_type_drop.name = "type_"+(num_rows+1);
		new_add_btn.name = "add_"+(num_rows+1);
		
		classobj = document.getElementById("class_"+(last_index-1));
		typeobj = document.getElementById("type_"+(last_index-1));
		
		new_class_drop.selectedIndex = classobj.selectedIndex;
		new_type_drop.selectedIndex = typeobj.selectedIndex;
		
		var c1name = "place_"+(num_rows+1);
		var c3name = "score_"+(num_rows+1);
		var c6name = "bandname_"+(num_rows+1);
		var c7name = "captions_"+(num_rows+1);
		var c10name = "tie_"+(num_rows+1);
		c1.innerHTML = "<input type='text' name='"+c1name+"' id='"+c1name+"' size='4' />";
		c2.innerHTML = "&lt;-OR-&gt;";
		c3.innerHTML = "<input type='text' name='"+c3name+"' id='"+c3name+"' size='7' />";
		c4.appendChild(new_band_ids);
		c5.innerHTML = "&lt;-OR-&gt;";
		c6.innerHTML = "<input type='text' name='"+c6name+"' id='"+c6name+"' size='15' />";
		c7.innerHTML = "<input type='text' name='"+c7name+"' id='"+c7name+"' size='15' />";
		c8.appendChild(new_class_drop);
		c9.appendChild(new_type_drop);
		c10.innerHTML = "<input type='checkbox' name='"+c10name+"' id='"+c10name+"' />";
		c11.appendChild(new_add_btn);
		
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
				<h1>Add Scores</h1>
				* - Required Fields<p>
				<form name='AddScores' action='add_scores.php' method='POST' autocomplete="off" enctype="multipart/form-data">
				<table id='headtable'>

				<tr><td><b>Festival:</b></td><td>
				<input type="text" name="FestivalName" id="FestivalName" size='50'/><input type='hidden' name='FestivalID' id='FestivalID' value=''/> (Just start typing the Festival Name)
				</td></tr>
				</table><br />
				<table id='bodytable' border=1>
				<tr><th>Place</th><th>OR</th><th>Score*</th><th>Band*</th><th>OR</th><th>Band Name Only*</th><th>Captions</th><th>Class*</th><th>Performance Type*</th><th>Tie Breaker</th><th>Row Options</th></tr>
				<tr id='score_row'>
				<td><input type='text' name='place_1' id='place_1' size='4' /></td>
				<td>&lt;-OR-&gt;</td>
				<td><input type='text' name='score_1' id='score_1' size='7' /></td>
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
				<td><input type='text' name='captions_1' id='captions_1' size='15' /></td>
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
				</select></td>
				<td><input type='checkbox' name='tie_1' id='tie_1' /></td>
				<td><input name='add_1' id='add_1' type='button' value='Add Row' onclick='addScoreRow(this)' /></td>
				</tr>
				</table>
				
				<input type='hidden' id='num_rows' name='num_rows' value='1' />
				<p>
				<table border=0>
					<tr>
						<td valign='top'>
							<table border=0>
								<tr><th>&nbsp;</th><th>Band</th><th>&nbsp;</th><th>Band Name Only</th></tr>
								<tr valign='top'>
									<td><b>Grand Champion:</b></td>
									<td><select name='GCBandID' id='GCBandID'><option value=''>...</option>
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
									<td><input type='text' name='GCbandname' id='GCbandname' size='15' /></td>
								</tr>
							</table>
							<br />
							<strong>Attach Files</strong>
							<table id='filetable' border=1>
								<tr>
									<th>Link Name</th><th>Upload File</th><th>Row Options</th>
								</tr>
								<tr>
									<td><input type='text' name='filename_1' id='filename_1' /></td>
									<td><input type='file' name='file_1' id='file_1' /></td>
									<td align='right'><input type='button' name='addfile_1' id='addfile_1' value='+' onclick='addFileRow(this)'/></td>
								</tr>
							</table>
							<br />
							<input type='hidden' name='scoresubmit' id='scoresubmit' value='Add Scores' />
							<input type='button' name='scorebutton' id='scorebutton' value='Add Scores' onClick='validateID()'/>
						</td>
						<td valign='top'>
							<table border=1>
								<tr>
									<th>Score Notes</th>
								</tr>
								<tr>
									<td><textarea name='notes' id='notes' rows='15' cols='50'></textarea></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<input type='hidden' id='num_file_rows' name='num_file_rows' value='1' />
				



				
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
