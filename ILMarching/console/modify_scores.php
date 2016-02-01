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
	$query = "SELECT Name, FestivalYear, GrandChampionID, GrandChampionBandName, ScoreNotes, RainedOut FROM Festivals WHERE FestivalID = $festid";
	$result = mysql_query($query, $connect);
	$row = mysql_fetch_array($result);
	$FestivalName = $row['Name'];
	$FestivalYear = $row['FestivalYear'];
	$GCBandName = $row['GrandChampionBandName'];
	$GCID = $row['GrandChampionID'];
	$rain = $row['RainedOut'];
	$notes = $row['ScoreNotes'];
} elseif (isset($_POST['changesubmit'])) {
		$message = "";
		$festid = $_POST['FestivalID'];
		if (!isset($_POST['rain'])) {
			$query = "UPDATE Festivals SET RainedOut = 0 WHERE FestivalID = $festid";
			mysql_query($query, $connect);
			$query = "SELECT AddedBy FROM Scores WHERE FestivalID = $festid LIMIT 1";
			$result = mysql_query($query, $connect);
			$row = mysql_fetch_array($result);
			$addedby = $row['AddedBy'];
			$query = "DELETE FROM Scores WHERE FestivalID = $festid";
			mysql_query($query, $connect);
			$calculatePlaces = false;
			for ($row = 1; $row <= $_POST['num_rows']; $row++) {
				$place = $_POST['place_'.$row];
				$score = $_POST['score_'.$row];
				$bandid = $_POST['BandID_'.$row];
				$bandname = $_POST['bandname_'.$row];
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
				if (isset($_POST['tie_'.$row])) {
					$tie = 1;
				} else {
					$tie = 0;
				}
				$delete = false;
				if (isset($_POST['delete_'.$row])) {
					$delete = true;
				}
				if ($score <> "") {
					$calculatePlaces = true;
					$place = 'NULL';
				} else {
					$score = 'NULL';
				}
				
				if ($place == "" && $score == 'NULL') {
					$place = '99';
				}
				if (!$delete) {
					if (($bandid <> "" || $bandname <> "")) {
						$query = "INSERT INTO Scores ($band, FestivalID, ClassPlace, Score, Captions, Class, Type, Tiebreaker, AddedBy, UpdatedBy) VALUES ($bandval, $festid, $place, $score, '$captions', '$class', '$type', $tie, '$addedby', '$username')";
						mysql_query($query, $connect);
					}	
				}

			} //End For Loop
			
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
			$query = "UPDATE Festivals SET $band = $bandval, ScoreNotes = '$notes' WHERE FestivalID = $festid";
			mysql_query($query, $connect);
			if ($calculatePlaces) {
				$query = "SELECT Scores.ScoreID, Scores.BandName, Bands.BandID, Scores.ClassPlace, Score, Captions,  Scores.Class, Type, School 
				FROM Scores 
				LEFT JOIN Bands ON Scores.BandID = Bands.BandID 
				JOIN ClassOrder ON ClassOrder.Class = Scores.Class 
				WHERE Scores.FestivalID = $festid ORDER BY Type DESC, ClassOrder.OrderNum, Score DESC, Tiebreaker DESC, ClassPlace ASC";
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
			
			//Files
			$query = "DELETE FROM Documents WHERE FestivalID = '$festid'";
			mysql_query($query, $connect);
			for ($row = 1; $row <= $_POST['num_file_rows']; $row++) {
				$linkname = $_POST['filename_'.$row];
				if (isset($_POST['exists_'.$row])) {
					if (!isset($_POST['deletefile_'.$row])) {
						$linkurl = $_POST['linkurl_'.$row];
						$query = "INSERT INTO Documents (FestivalID, LinkName, LinkURL) VALUES ($festid, '$linkname', '$linkurl')";
						mysql_query($query, $connect);
					} else {
						$filename = explode("/", $_POST['linkurl_'.$row]);
						$filepath = "../uploads/".$filename[sizeof($filename)-1];
						 if (file_exists($filepath)) {
							 unlink($filepath);
						 }		 
					}
				} else {
					$file = $_FILES["file_".$row];
					$filename = $file['name'];
					$newfilename = str_replace(" ", "", $filename);
					if (!isset($_POST['deletefile_'.$row])) {
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
				}
			}
			
			
			$message .= "Updated Scores Successfully";
		} else {
			$query = "UPDATE Festivals SET RainedOut = 1 WHERE FestivalID = $festid";
			mysql_query($query, $connect);
			$message .= "Updated the show to 'Rained Out'";
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

var num_rows = 0;
var num_file_rows = 0;

$().ready(function() {
	$("#FestivalName").autocomplete("ajax/get_festival_list_modify.php", {
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
		var c12 = new_row.insertCell(11);
		//var c10 = new_row.insertCell(0);
		//var c11 = new_row.insertCell(1);
		
		var band_ids = document.getElementById("BandID_1");
		var class_drop = document.getElementById("class_1");
		var type_drop = document.getElementById("type_1");
		var add_btn = document.getElementById("add_1");
		var del_box = document.getElementById("delete_1");
		
		var new_band_ids = band_ids.cloneNode(true);
		var new_class_drop = class_drop.cloneNode(true);
		var new_type_drop = type_drop.cloneNode(true);
		var new_add_btn = add_btn.cloneNode(true);
		var new_del_box = del_box.cloneNode(true);
		
		new_band_ids.id = "BandID_"+(num_rows+1);
		new_class_drop.id = "class_"+(num_rows+1);
		new_type_drop.id = "type_"+(num_rows+1);
		new_add_btn.id = "add_"+(num_rows+1);
		new_del_box.id = "delete_"+(num_rows+1);
		
		new_band_ids.name = "BandID_"+(num_rows+1);
		new_class_drop.name = "class_"+(num_rows+1);
		new_type_drop.name = "type_"+(num_rows+1);
		new_add_btn.name = "add_"+(num_rows+1);
		new_del_box.name = "delete_"+(num_rows+1);
		
		new_class_drop.selectedIndex = 0;
		new_type_drop.selectedIndex = 0;
		new_band_ids.selectedIndex = 0;
		
		var c3name = "score_"+(num_rows+1);
		var c6name = "bandname_"+(num_rows+1);
		var c7name = "captions_"+(num_rows+1);
		var c1name = "place_"+(num_rows+1);
		var c10name = "tie_"+(num_rows+1);
		c3.innerHTML = "<input type='text' name='"+c3name+"' id='"+c3name+"' size='7' />";
		c4.appendChild(new_band_ids);
		c5.innerHTML = "<b>OR</b>";
		c6.innerHTML = "<input type='text' name='"+c6name+"' id='"+c6name+"' size='15' />";
		c7.innerHTML = "<input type='text' name='"+c7name+"' id='"+c7name+"' size='15' />";
		c8.appendChild(new_class_drop);
		c9.appendChild(new_type_drop);
		c10.innerHTML = "<input type='checkbox' name='"+c10name+"' id='"+c10name+"' />";
		c11.appendChild(new_add_btn);
		c12.appendChild(new_del_box);
		c1.innerHTML = "<input type='text' name='"+c1name+"' id='"+c1name+"' size='4' />";
		c2.innerHTML = "<b>OR</b>";
		
		document.getElementById(c1name).focus();
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
		var c4 = new_row.insertCell(3);
		
		c3.align = 'center';
		
		var c1name = "filename_"+(num_file_rows+1);
		var c2name = "file_" +(num_file_rows+1);
		var c3name = "addfile_"+(num_file_rows+1);
		var c4name = "deletefile_"+(num_file_rows+1);
		c1.innerHTML = "<input type='text' name='"+c1name+"' id='"+c1name+"' />";
		c2.innerHTML = "<input type='file' name='"+c2name+"' id='"+c2name+"' />";
		c3.innerHTML = "<input type='button' name='"+c3name+"' id='"+c3name+"' value='+' onclick='addFileRow(this)'/>";
		c4.innerHTML = "<input type='checkbox' name='"+c4name+"' id='"+c4name+"' />";
		
		document.getElementById(c1name).focus();
	}
}

function getKeyCode(e, obj) {
	var keynum;
	if(window.event) // IE
  	{
  		keynum = e.keyCode;
  	}
	else if(e.which) // Netscape/Firefox/Opera
  	{
 		 keynum = e.which;
  	}
  	
  	if (keynum == 107) {
		var id_arr = obj.id.split("_");
		var id = id_arr[1];
		id = parseInt(id) + 1;
		var rows = parseInt(document.getElementById('num_rows').value);
		if (id <= rows) {
			document.getElementById(id_arr[0] + "_" + id).focus();
		}
		return false;
	}
	
	if (keynum == 109) {
		var id_arr = obj.id.split("_");
		var id = id_arr[1];
		id = parseInt(id) - 1;
		if (id > 0) {
			document.getElementById(id_arr[0] + "_" + id).focus();
		}
		return false;
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
				<h1>Modify Scores</h1>
				<?php if (!$displayform) { ?>
				<form name='SelectFestival' action='modify_scores.php' method='POST' autocomplete="off">
					Festival <label>:</label>
					<input type="text" name="FestivalName" id="FestivalName" size='50'/><input type='hidden' name='FestivalID' id='FestivalID' value=''/> 
					<input type='hidden' name='modifysubmit' id='modifysubmit' value='Submit' />
				<input type='button' id='modifybutton' name='modifybutton' value='Modify Scores' onClick='validateID()'/><br />(Just start typing the Festival Name)
				</form>
				<?php } else { 
					echo "<h2>$FestivalYear - $FestivalName</h2>";
					echo "<form action='modify_scores.php' method='POST'  enctype='multipart/form-data'>";
					if ($rain) {
						$selected = "checked='checked'";
					} else {
						$selected = "";
					}
					echo "<input type='checkbox' name='rain' id='rain' $selected /> <font size='+1'><strong>Rained Out.</strong></font><br />";
					if (!$rain) {
						$query = "SELECT * FROM Scores WHERE FestivalID = $festid ORDER BY Type DESC, Class, ClassPlace, Score DESC";
						$result = mysql_query($query, $connect);
						echo "<table border=1 id='bodytable'><tr><th>Place</th><th>OR</th><th>Score*</th><th>Band*</th><th>OR</th><th>Band Name Only*</th><th>Captions</th><th>Class*</th><th>Performance Type*</th><th>Tie Breaker</th><th>Row Options</th><th>Delete Row?</th></tr>";
						$num_row = 0;
						while ($row = mysql_fetch_array($result)) {
							$num_row++;
							$score = $row['Score'];
							$bandid = $row['BandID'];
							$bandname = $row['BandName'];
							$captions = $row['Captions'];
							$class = $row['Class'];
							$type = $row['Type'];
							$place = $row['ClassPlace'];
							$tie = $row['Tiebreaker'];
							
							echo "<tr>";
							
							//Places
							echo "<td><input type='text' size='4' name='place_".$num_row."' id='place_".$num_row."' value='$place' onKeyDown='return getKeyCode(event, this)'/></td>";
							
							echo "<td><b>OR</b></td>";
							
							//Scores
							echo "<td><input type='text' size='7' name='score_".$num_row."' id='score_".$num_row."' value='$score' onKeyDown='return getKeyCode(event, this)'/></td>";
							
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
							
							echo "<td><b>OR</b></td>";
							
							//BandName
							if ($bandname == NULL) {$bandname = "";}
							echo "<td><input type='text' size='15' name='bandname_".$num_row."' id='bandname_".$num_row."' value='$bandname' onKeyDown='return getKeyCode(event, this)'/></td>";
							
							//Captions
							echo "<td><input type='text' size='15' name='captions_".$num_row."' id='captions_".$num_row."' value='$captions' onKeyDown='return getKeyCode(event, this)'/></td>";
							
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
						} else if ($type == "Field2") {
							echo "<option value='Field'>Field</option>
								<option value='Parade'>Parade</option>
								<option selected='selected' value='Field2'>Field Day 2</option>";
						} else {
							echo "<option value='Field'>Field</option>
								<option selected='selected' value='Parade'>Parade</option>
								<option value='Field2'>Field Day 2</option>";
						}
							echo "</select></td>";
							if ($tie == 1) {
								echo "<td><input type='checkbox' name='tie_".$num_row."' id='tie_".$num_row."' checked='checked'/></td>";
							} else {
								echo "<td><input type='checkbox' name='tie_".$num_row."' id='tie_".$num_row."' /></td>";
							}
							
							
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
						
						if ($GCID == NULL) {$GCID = "";}
						if ($GCBandName == NULL) {$GCBandName = "";}
						?>
						<p>
						<table border=0>
						<tr>
							<td valign='top'>
								<table border=0>
									<tr><th>&nbsp;</th><th>Band</th><th>&nbsp;</th><th>Band Name Only</th></tr>
									<tr valign='top'>
										<td><b>Grand Champion:</b></td>
										<?php
											echo "<td><select name='GCBandID' id='GCBandID'>
											<option value=''>...</option>";
											$q2 = "SELECT School, BandID FROM Bands ORDER BY School ASC";
											$r2 = mysql_query($q2, $connect);
											while ($row2 = mysql_fetch_array($r2)) {
												$QBandID = $row2['BandID'];
												$QSchool = $row2['School'];
												if ($QBandID == $GCID) {
													echo "<option selected='selected' value='$QBandID'>$QSchool</option>";
												} else {
													echo "<option value='$QBandID'>$QSchool</option>";
												}
												
											}
											echo "</select></td>";
										?>
										<td>&lt;-OR-&gt;</td>
										<td><input type='text' name='GCbandname' id='GCbandname' size='15' value='<?php echo $GCBandName; ?>'/></td>
									</tr>
								</table>
								<br />
								<strong>Attach Files</strong>
								<table id='filetable' border=1>
									<tr>
										<th>Link Name</th><th>Upload File</th><th>Row Options</th><th>Delete File</th>
									</tr>
										<?php
											$docquery = "SELECT * FROM Documents WHERE FestivalID = '$festid'";
											$docresult = mysql_query($docquery, $connect);
											$count = 1;
											while ($docrow = mysql_fetch_array($docresult)) {
												
												echo "<tr>";
												$linkname = $docrow['LinkName'];
												$linkid = $docrow['DocumentID'];
												$linkurl = $docrow['LinkURL'];
												echo "<td><input type='text' name='filename_$count' id='filename_$count' value='$linkname' /></td>";
												$filename = explode("/", $linkurl);
												$filename = $filename[sizeof($filename)-1];
												echo "<td><b>$filename</b><input name='exists_$count' id='exists_$count' type='hidden' value='1' /><input type='hidden' name='linkurl_$count' id='linkurl_$count' value='$linkurl' /></td>";
												echo "<td align='center'><input type='button' name='addfile_$count' id='addfile_$count' value='+' onclick='addFileRow(this)'/></td>";
												echo "<td><input type='checkbox' name='deletefile_".$count."' id='deletefile_".$count."' /></td>";

												echo "</tr>";
												?>
												<script type="text/javascript">
													num_file_rows++;
												</script>
												<?php
												$count++;
											}
											echo "<tr><td><input type='text' name='filename_$count' id='filename_$count' /></td>";
											echo "<td><input type='file' name='file_$count' id='file_$count' /></td>";
											echo "<td align='center'><input type='button' name='addfile_$count' id='addfile_$count' value='+' onclick='addFileRow(this)'/></td>";
											echo "<td><input type='checkbox' name='deletefile_".$count."' id='deletefile_".$count."' /></td></tr>";
										?>

								</table>
								<br />
							<?php } ?>
							<input type='submit' name='changesubmit' id='changesubmit' value='Modify Scores' />
						<?php
						if (!$rain) { ?>	
						</td>
						<td valign='top'>
							<table border=1>
								<tr>
									<th>Score Notes</th>
								</tr>
								<tr>
									<td><textarea name='notes' id='notes' rows='15' cols='50'><?php echo $notes; ?></textarea></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<input type='hidden' id='num_file_rows' name='num_file_rows' value='<?php echo $count; ?>' />
				<?php
				}	
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
