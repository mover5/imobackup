<?php
require_once "../../connection.inc.php";
$q = strtolower($_GET["q"]);
if (!$q) return;

//$sql = "SELECT Name, FestivalYear, FestivalID FROM Festivals WHERE Name LIKE '%$q%' OR FestivalYear LIKE '%$q%' ORDER BY FestivalYear DESC, Name ASC";
$sql = "SELECT * FROM (SELECT DISTINCT Name, FestivalYear, Scores.FestivalID FROM Scores LEFT JOIN Festivals ON Scores.FestivalID = Festivals.FestivalID WHERE Name LIKE '%$q%' OR FestivalYear LIKE '%$q%'
UNION
SELECT Name, FestivalYear, FestivalID FROM Festivals WHERE RainedOut = 1 AND Name LIKE '%$q%' OR FestivalYear LIKE '%$q%') AS a ORDER BY FestivalYear DESC, Name ASC";
$rsd = mysql_query($sql);
while($rs = mysql_fetch_array($rsd)) {
	$cname = $rs['Name'];
	$year = $rs['FestivalYear'];
	$id = $rs['FestivalID'];
	echo "$year - $cname|$id\n";
}
?>
