<?php
require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');
$connect = connectToDB();
$query = "SELECT Scores.ScoreID, Scores.BandName, Bands.BandID, Score, Captions,  Scores.Class, Type, School 
FROM Scores 
LEFT JOIN Bands ON Scores.BandID = Bands.BandID 
JOIN ClassOrder ON ClassOrder.Class = Scores.Class 
WHERE Scores.FestivalID = 247 ORDER BY Type DESC, ClassOrder.OrderNum, Score DESC";
$result = mysql_query($query, $connect);

$prevtype = "";
$prevclass = "";
$num = 0; 
$place = 0;

while ($row = mysql_fetch_array($result)) {
	$place++;
	$type = $row['Type'];
	$class = $row['Class'];
	$school = $row['School'];
	if ($school == NULL || $school == "") {$school = $row['BandName'];}
	$scoreid = $row['ScoreID'];
	

	
	if ($type <> $prevtype || $class <> $prevclass) {
		$place = 1;		
	}
	$prevtype = $type;
	$prevclass = $class;
	
	echo "<font size='-1'>".$place . ") ".$school." (".$scoreid.")</font><br />";

	$updateplace = "UPDATE Scores SET ClassPlace = $place WHERE ScoreID = $scoreid";
	mysql_query($updateplace, $connect);
	

}

?>
