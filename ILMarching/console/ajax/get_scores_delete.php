<?php
require_once "../../connection.inc.php";
$q = strtolower($_GET["q"]);
if (!$q) return;

$sql = "SELECT * FROM (SELECT Scores.FestivalID, Name, FestivalYear FROM Scores, Festivals WHERE Festivals.FestivalID = Scores.FestivalID AND (Name LIKE '%$q%' OR FestivalYear LIKE '%$q%') GROUP BY FestivalID UNION SELECT FestivalID, Name, FestivalYear FROM Festivals WHERE RainedOut = 1 AND (Name LIKE '%$q%' OR FestivalYear LIKE '%$q%')) AS a ORDER BY FestivalYear DESC, Name ASC";
$rsd = mysql_query($sql);
while($rs = mysql_fetch_array($rsd)) {
	$cname = $rs['Name'];
	$year = $rs['FestivalYear'];
	$id = $rs['FestivalID'];
	echo "$year - $cname|$id\n";
}
?>
