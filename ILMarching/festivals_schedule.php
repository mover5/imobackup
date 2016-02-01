<?php

if (!isset($_REQUEST['FestivalID'])) {
	header("Location: festivals.php");
} else {
	$FestivalID = $_REQUEST['FestivalID'];
}
require("connection.inc.php");
echo "<a href='festivals_detail.php?FestivalID=$FestivalID'>&lt;&lt;Back</a><p></p>";
$query = "SELECT Name, FestivalYear, Location FROM Festivals WHERE FestivalID = '".$FestivalID."'";
$result = mysql_query($query, $connect);
$row = mysql_fetch_array($result);
$name = $row['Name'];
$showYear = $row['FestivalYear'];
$location = $row['Location'];
echo "<p><h1>Schedule For ".$showYear." ".$name.": ".$location."</h1><p>";
include("display_schedule.php");

		


		

