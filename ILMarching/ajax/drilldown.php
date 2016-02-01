<?php
require("../connection.inc.php");
$request = $_GET['r'];
if ($request == "FESTIVAL") {
	$query = "SELECT FestivalYear, Name, v.FestivalID FROM Videos v LEFT JOIN Festivals f ON v.FestivalID = f.FestivalID WHERE v.FestivalID IS NOT NULL AND v.Approved = 1 GROUP BY v.FestivalID ORDER BY FestivalYear DESC, Name ASC";
	$result = mysql_query($query, $connect);
	while ($row = mysql_fetch_array($result)) {
		$festid = $row['FestivalID'];
		echo "<font size='-1'><a href='javascript:selectVideo(\"r=FESTIVAL&s=0&i=".$festid."\")'>".$row['FestivalYear']." - ".$row['Name']."</a></font>";
		echo "<hr>";
	}
} else if ($request == "YEAR") {
	$query = "SELECT BandYear FROM Videos WHERE Videos.Approved = 1 GROUP BY BandYear ORDER BY BandYear DESC";
	$result = mysql_query($query, $connect);

	while ($row = mysql_fetch_array($result)) {
		echo "<a href='javascript:selectVideo(\"r=YEAR&s=0&y=".$row['BandYear']."\")'>".$row['BandYear']."</a>";
		echo "<hr>";
	}
} else if ($request == "BAND") {
	$query = "SELECT b.BandID, School FROM Videos v LEFT JOIN Bands b ON b.BandID = v.BandID WHERE v.Approved = 1 GROUP BY v.BandID ORDER BY School ASC";
	$result = mysql_query($query, $connect);
	
	while ($row = mysql_fetch_array($result)) {
		echo "<a href='javascript:selectVideo(\"r=BAND&s=0&i=".$row['BandID']."\")'>".$row['School']."</a>";
		echo "<hr>";
	}
} else if ($request = "SEARCH") {
    echo "Search for a show<br />";
    echo "<input type='text' id='search' name='search' />";
    echo "<input type='button' class='button_submit' value='Search' onclick='searchVideo()'/>";
}
?>
