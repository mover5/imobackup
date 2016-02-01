<?php 
include("connection.inc.php");

$my_t=getdate(date("U"));
$this_year = ("$my_t[year]");
$year = $_GET["year"];
if ($year == "") $year = $this_year;

echo "<a href='festivals.php?year=$year'>&lt;&lt;Back</a><p></p>";
	$query = "SELECT FestivalID, Date, Name, Location, LastUpdated FROM Festivals WHERE FestivalYear = '".$year."' ORDER BY Date, Name";

	$result=mysql_query($query);

	$prevdate = "";	
	while ($row = mysql_fetch_array($result)) {
		$date = $row['Date'];
		$name = $row['Name'];
		$location = $row['Location'];
		$FestivalID = $row['FestivalID'];
		if ($prevdate <> $date) {
			$prevdate = $date;
			$date = strtotime($date);
			$date = date("F d, Y", $date);
			echo "<h2>$date</h2>";
		}

		echo "<b>".$name."</b> " . $location . "<br />";
	}

?>
