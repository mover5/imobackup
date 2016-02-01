<?php
require('./wp-blog-header.php');
require('./layout.php');
include('connection.inc.php');
$showform = true;
if (isset($_POST['compare'])) {
	$showform = false;
}

if (isset($_REQUEST['BandID'])) {
	$setBandID = $_REQUEST['BandID'];
} else {
	$setBandID = 41;
}
if (isset($_REQUEST['year'])) {
	$setyear = $_REQUEST['year'];
} else {
	$setyear = date('Y');
}
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->
<script type='text/javascript'>
function addRow(obj) {
	var num_rows = document.getElementById('numrows').value;
	var next_row = parseInt(num_rows) + 1;
	document.getElementById('numrows').value = next_row;
	var previous_index = parseInt(document.getElementById("bodytable").rows.length) - 1;
	var new_row = document.getElementById("bodytable").insertRow(document.getElementById("bodytable").rows.length);
	var c1 = new_row.insertCell(0);
	var c2 = new_row.insertCell(1);
	
	var band = document.getElementById("band_1");
	var new_band = band.cloneNode(true);
	new_band.id = "band_"+next_row;
	new_band.name = "band_"+next_row;
	
	c1.appendChild(new_band);
	c2.innerHTML = "<input type='button' name='add' id='add' onclick='return addRow(this)' value='+' />";
	document.getElementById('bodytable').rows[previous_index].cells[1].innerHTML = "&nbsp;";
}
</script>
<?php

	//Select Bands Form
	if ($showform) {
		echo "<h1>Compare Bands</h1><hr>";
		echo "<form action='compare_bands.php' method='post'>";
			echo "<table><tr><td valign='top'>";
			echo "<table id='headertable' border='0'>";
				echo "<tr><th colspan='2'>Year to Compare</th></tr><tr><td><b>Year:</b></td><td align='right'>";
				echo "<select name='year'>";
				$query = "SELECT DISTINCT(FestivalYear) FROM Scores, Festivals WHERE Scores.FestivalID = Festivals.FestivalID ORDER BY FestivalYear DESC";
				$result = mysql_query($query, $connect);
				while ($row = mysql_fetch_array($result)) {
						$year = $row['FestivalYear'];
						if ($setyear == $year) {
							echo "<option value='$year' selected='selected'>$year</option>";
						} else {
							echo "<option value='$year'>$year</option>";
						}
						
				}
				echo "</select>";
				echo "</td></tr>";
				echo "<tr><td colspan='2' align='right'><input type='submit' name='compare' id='compare' value='Compare Bands' /></td></tr>";
			echo "</table></td><td valign='top'>";
			echo "<table id='bodytable' border='0'>";
				echo "<tr><th align='left' colspan='2'>Bands</th></tr>";
				echo "<tr>";
				echo "<td><select id='band_1' name='band_1'>";
				$query = "SELECT School, BandID FROM Bands ORDER BY School ASC";
				$result = mysql_query($query, $connect);
				while ($row = mysql_fetch_array($result)) {
					$id = $row['BandID'];
					$school = $row['School'];
					if ($id == $setBandID) {
						echo "<option value='$id' selected='selected'>$school</option>";
					} else {
						echo "<option value='$id'>$school</option>";
					}
					
				}
				echo "</select></td>";
				echo "<td>&nbsp;</td></tr>";
				echo "<tr>";
				echo "<td><select id='band_2' name='band_2'>";
				$query = "SELECT School, BandID FROM Bands ORDER BY School ASC";
				$result = mysql_query($query, $connect);
				while ($row = mysql_fetch_array($result)) {
					$id = $row['BandID'];
					$school = $row['School'];
					echo "<option value='$id'>$school</option>";
				}
				echo "</select></td>";
				echo "<td><input type='button' name='add' id='add' onclick='return addRow(this)' value='+' /></td></tr>";
			echo "</table></td></tr></table>";
			echo "<input type='hidden' name='numrows' id='numrows' value='2' />";
		echo "</form>";
	} else {
		$year = $_POST['year'];
		echo "<h1><a href='compare_bands.php'>Compare Bands</a> - $year</h1><hr>";
		$numrows = $_POST['numrows'];
		$numtables = ceil($numrows / 3);
		for ($k = 0; $k < $numtables; $k++) {
			$tableheader = "";
			$min = (($k * 3) + 1);
			$max = (($k * 3) + 3);
			if ($max > $numrows) {
				$max = $numrows;
			}
			for ($i = $min; $i <= $max; $i++) {
				$bandid = $_POST['band_'.$i];
				$query = "SELECT * FROM Bands WHERE BandID = $bandid";
				$result = mysql_query($query, $connect);
				$row = mysql_fetch_array($result);
				$tableheader .= "<th><a href='bands_indiv.php?BandID=$bandid&year=$year'>".$row['School']."</a></th>";
			}
			
			echo "<table border='1'>";
			echo "<tr>$tableheader</tr><tr>";
			for ($i = $min; $i <= $max; $i++) {
				$bandid = $_POST['band_'.$i];
				$query = "SELECT * FROM Bands WHERE BandID = $bandid";
				$result = mysql_query($query, $connect);
				$row = mysql_fetch_array($result);
				echo "<td valign='top'><table border='0'>";
				echo "<tr><td><b>Location:</b></td><td>" . $row['Town'] . ", IL</td></tr>";
				$query = "SELECT * FROM Enrollment WHERE BandID = $bandid AND Year = $year";
				$result = mysql_query($query, $connect);
				$row = mysql_fetch_array($result);
				echo "<tr><td><b>Enrollment: </b></td><td>" . $row['IHSAEnrollment'] . "</td></tr>";
				$query = "SELECT * FROM BandShows WHERE BandID = $bandid AND Year = $year";
				$result = mysql_query($query, $connect);
				$row = mysql_fetch_array($result);
				echo "<tr><td><b>Show Title: </b></td><td>" . $row['Title'] . "</td></tr>";
				echo "<tr><td colspan='2' align='center'><u><b>Scores</b></u></td></tr>";
				$query = "SELECT Scores.FestivalID, Name, Scores.Class, Type, ClassPlace, Score 
				FROM Scores, Festivals, ClassOrder 
				WHERE Scores.FestivalID = Festivals.FestivalID 
				AND Scores.Class = ClassOrder.Class
				AND FestivalYear = $year 
				AND BandID = $bandid 
				AND Type = 'Field'
				ORDER BY Date, Type DESC, OrderNum ASC";
				$result = mysql_query($query, $connect);
				while ($row = mysql_fetch_array($result)) {
					$name = $row['Name'];
					$class = $row['Class'];
					$type = $row['Type'];
					$place = $row['ClassPlace'];
					$score = $row['Score'];
					$festid = $row['FestivalID'];
					if ($score == NULL || $score == "") {
						$score = getNumberExtension($place) . " Place";
					}
					echo "<tr><td colspan='2'><hr></td></tr><tr><td><b><a href='festivals_detail.php?FestivalID=$festid'>$name</a> - $class:</b></td><td valign='bottom'><a href='scores_indiv.php?FestivalID=$festid&archive-0'>$score</a></td></tr>";
				}
				echo "</table></td><br />";
			}
		}
		echo "</tr></table>";
		
	}
	
	function getNumberExtension($place) {
		$returnstring = "";
		if ($place == 1) {
			$returnstring = $place . "st";
		} else if ($place == 2) {
			$returnstring = $place . "nd";
		} else if ($place == 3) {
			$returnstring = $place . "rd";
		} else {
			$returnstring = $place . "th";
		}
		return $returnstring;
	}
?>
<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
