<?php
require_once "../../connection.inc.php";
$q = strtolower($_GET["q"]);
if (!$q) return;

$sql = "SELECT DISTINCT(Name) FROM Festivals WHERE Name LIKE '%$q%' ORDER BY Name ASC";
$rsd = mysql_query($sql);
while($rs = mysql_fetch_array($rsd)) {
	$cname = $rs['Name'];
	echo "$cname|$cname\n";
}
?>
