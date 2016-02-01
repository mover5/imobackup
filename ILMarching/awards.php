<?php
$query = "SELECT Name, Festivals.FestivalID, ClassPlace, Score, Captions, Scores.Class,
Type , IF( GrandChampionID = $BandID, 'YES', 'NO' ) AS GrandChampion
FROM Scores, Festivals, ClassOrder
WHERE Scores.FestivalID = Festivals.FestivalID
AND BandID = $BandID AND FestivalYear = $year
AND ClassOrder.Class = Scores.Class AND ClassPlace <> 99
ORDER BY FestivalYear DESC , Date ASC , Name ASC,
TYPE DESC, OrderNum ASC";
$result = mysql_query($query, $connect);
$num = mysql_num_rows($result);
if ($num == 0) {
	$query = "SELECT * FROM BandShows WHERE BandID = $BandID AND Year = $year";
	$result = mysql_query($query, $connect);
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$awards = $row['Awards'];
		if ($awards <> NULL && $awards <> "") {
			echo nl2br($awards);
		} else {
			echo "<div style='padding:0px 5px 0px 8px'><i>no information available</i></div>";
		}
	} else {
		echo "<div style='padding:0px 5px 0px 8px'><i>no information available</i></div>";
	}
} else {
	$prevshow = "";
	$scoreslink = "";
	echo "<table border=0>";
	while ($row = mysql_fetch_array($result)) {
		$name = $row['Name'];
		$place = $row['ClassPlace'];
		$captions = $row['Captions'];
		$class = $row['Class'];
		$type = $row['Type'];
		if ($type == 'Field2') {$type = "Field Day 2";}
		$gc = $row['GrandChampion'];
		$festid = $row['FestivalID'];
		
		if ($prevshow <> $name) {
			if ($prevshow <> "") {
				echo "</dl></td><td>$scoreslink</td></tr>";
				echo "<tr><td colspan='2'></td></tr>";
			}
			$prevshow = $name;

			echo "<tr><td style='padding-right:30px;'><dl><dt><a href='festivals_detail.php?FestivalID=$festid'>$name</a></dt>";

			if ($gc == "YES") {
				echo "<dd>Grand Champion</dd>";
			}
		}
		if ($captions <> NULL && $captions <> "") {
			$captions = "(".$captions.")";
		} else {
			$captions = "";
		}
		if ($class <> "Exb") {
			if ($class == "Finals" || $class == "SemiFinals") {
				$display = "in $class $captions";
			} else {
				$display = "in Class $class $type $captions";
			}
			echo "<dd>".getNumberExtension($place)." Place $display</dd>";
		}
	}
	echo "</dl></td><td>$scoreslink</td></tr></table>";
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
