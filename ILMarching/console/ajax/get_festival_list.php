<?php
require_once "../../connection.inc.php";
$q = strtolower($_GET["q"]);
if (!$q) return;

$sql = "SELECT Name, FestivalYear, FestivalID FROM Festivals WHERE Name LIKE '%$q%' OR FestivalYear LIKE '%$q%' ORDER BY FestivalYear DESC, Name ASC";
$rsd = mysql_query($sql);
while($rs = mysql_fetch_array($rsd)) {
	$cname = $rs['Name'];
	$year = $rs['FestivalYear'];
	$id = $rs['FestivalID'];
	echo "$year - $cname|$id\n";
}
?>
