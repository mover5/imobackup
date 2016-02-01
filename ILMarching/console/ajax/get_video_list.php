<?php
require_once "../../connection.inc.php";
$q = strtolower($_GET["q"]);
if (!$q) return;

//$sql = "SELECT Name, FestivalYear, FestivalID FROM Festivals WHERE Name LIKE '%$q%' OR FestivalYear LIKE '%$q%' ORDER BY FestivalYear DESC, Name ASC";
$sql = "SELECT * FROM Videos WHERE VideoURL LIKE '%$q%'";
$rsd = mysql_query($sql);
while($rs = mysql_fetch_array($rsd)) {
	$url = $rs['VideoURL'];
	echo "$url|$url\n";
}
?>
