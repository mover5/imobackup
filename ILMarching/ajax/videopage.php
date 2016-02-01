<?php
require("../connection.inc.php");
$sizelimit = 6;
if (isset($_GET['s'])) {
	$start = $_GET['s'];
} else {
	$start = 0;
}
$request = $_GET['r'];
$basequery = "SELECT b.BandID, School, VideoURL,  Title, f.Name, BandYear, v.FestivalID FROM Videos v LEFT JOIN BandShows s ON (v.BandYear = s.Year AND v.BandID = s.BandID) LEFT JOIN Bands b ON v.BandID = b.BandID LEFT JOIN Festivals f ON v.FestivalID = f.FestivalID ";
if ($request == "FESTIVAL") {
	$id = $_GET['i'];
	$prev = $start - $sizelimit;
	if ($prev < 0) $prev = 0;
	$prevparam = "r=$request&i=$id&s=$prev";
	
	$next = $start + $sizelimit;
	$nextparam = "r=$request&i=$id&s=$next";
	
	$query = $basequery . "WHERE v.FestivalID = $id AND v.Approved = 1 ORDER BY School";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result);
	$search = $row['BandYear'] . " - " . $row['Name'];
	printTable($query, $prevparam, $nextparam, $search);
} else if ($request == "ALL") {
	$prev = $start - $sizelimit;
	if ($prev < 0) $prev = 0;
	$prevparam = "r=$request&s=$prev";
	
	$next = $start + $sizelimit;
	$nextparam = "r=$request&s=$next";
	$query = $basequery . "WHERE v.Approved = 1 ORDER BY BandYear DESC, School ASC";
	$search = "All Videos";
	printTable($query, $prevparam, $nextparam, $search);
} else if ($request == "SEARCH") {
    $search = $_GET['p']; // Search parameter
    
    // Setup navigation parameters
	$prev = $start - $sizelimit;
	if ($prev < 0) $prev = 0;
	$prevparam = "r=$request&p=$search&s=$prev";
	
	$next = $start + $sizelimit;
	$nextparam = "r=$request&p=$search&s=$next";
	$query = $basequery . "WHERE (School LIKE '%$search%' OR Title LIKE '%$search%' OR BandYear LIKE '%$search%' OR VideoURL LIKE '%$search%') AND v.Approved = 1 ORDER BY BandYear DESC, School ASC";
	printTable($query, $prevparam, $nextparam, $search);
} else if ($request == "YEAR") {
	$year = $_GET['y'];
	$prev = $start - $sizelimit;
	if ($prev < 0) $prev = 0;
	$prevparam = "r=$request&y=$year&s=$prev";
	
	$next = $start + $sizelimit;
	$nextparam = "r=$request&y=$year&s=$next";
	$query = $basequery . "WHERE BandYear = $year AND v.Approved = 1 ORDER BY BandYear DESC, School ASC";
	$search = $year;
	printTable($query, $prevparam, $nextparam, $search);
} else if ($request == "BAND") {
	$id = $_GET['i'];
	$prev = $start - $sizelimit;
	if ($prev < 0) $prev = 0;
	$prevparam = "r=$request&i=$id&s=$prev";
	
	$next = $start + $sizelimit;
	$nextparam = "r=$request&i=$id&s=$next";
	
	$query = $basequery . "WHERE v.BandID = $id AND v.Approved = 1 ORDER BY BandYear DESC";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result);
    $search = $row['School'];
	printTable($query, $prevparam, $nextparam, $search);
} 



// Prints the actual interface for the user. 
// @Input $query - The SQL query to get the list of Videos to display. Must return
// BandID, School, VideoURL,  Title, Name, BandYear, and FestivalID 	
// @Input $prevparam - The GET parameter for the 'Previous Page' link. Specific to the query type
// @Input $nextparam - The GET parameter for the 'Next Page' link. Specific to the query type
// @Input $search - What terms were searched for. Specific to the query type.
function printTable($query, $prevparam, $nextparam, $search) {
	global $start, $sizelimit;
	require("../connection.inc.php");
	$result = mysql_query($query, $connect);
	$totalrows = mysql_num_rows($result);
	$query = $query . " LIMIT $start, $sizelimit";
	$result = mysql_query($query, $connect);
	echo "<center><h3>Videos Found: $totalrows</h3></center>";
    echo "<center><b>Search Terms: &quot;$search&quot;</b></center>";
	echo "<center><table cellpadding=10><tr>";
	$count = 0;
	while ($row = mysql_fetch_array($result)) {
		$count++;
		$fullurl = $row['VideoURL'];
		$vidid = substr($row['VideoURL'], stripos($row['VideoURL'], "?v=") + 3);
		$embedurl = "http://youtube.com/embed/" . $vidid . "?autoplay=1";
		$school = $row['School'];
		$bandid = $row['BandID'];
		$bandyear = $row['BandYear'];
		$festname = $row['Name'];
		$festid = $row['FestivalID'];
		if ($festname == "") $festname = "Missing Fest";
		$show = $row['Title'];
		if ($show == "") $show = "Missing Show";
		if ($count > 3) {
			echo "</tr><tr><td colspan=6><hr></td></tr><tr>";
			$count = 1;
		}
		echo "<td width=130><b>$bandyear - $school</b><hr><img src='http://img.youtube.com/vi/$vidid/1.jpg' 
		onclick='watchVideo(\"$embedurl\", \"".str_replace("'", "&apos;", $show)."\", \"$school\", \"$bandid\", \"$bandyear\", \"$festname\", \"$festid\", \"$fullurl\")'>
		</td><td width=128>$show<hr>$festname</td>";
		
	}
	while ($count < 3) {
		echo "<td width=130>&nbsp;</td><td width=128>&nbsp;</td>";
		$count++;
	}
	echo "</tr>";
	
	//Pagination
	echo "<tr><td colspan=6><hr>";
	$totalpages = ($totalrows - ($totalrows % $sizelimit)) / $sizelimit;
	if ($totalrows % $sizelimit != 0) {$totalpages += 1;}
	$currentpage = ($start / $sizelimit) + 1;
	if ($currentpage != $totalpages) {
		$nextimg = "<img src='images/next_32.png' onclick='selectVideo(\"$nextparam\")'>";
	} else {
		$nextimg = "<img src='images/next_32.png'>";
	}
	if ($currentpage != 1) {
		$previmg = "<img src='images/previous_32.png' onclick='selectVideo(\"$prevparam\")'>";
	} else {
		$previmg = "<img src='images/previous_32.png'>";
	}
	echo "<center>
	$previmg
	<b>
	$currentpage
	&nbsp;
	/
	&nbsp;
	$totalpages
	</b>
	$nextimg
	<br />
	<b>Page</b>
	</center>";
	
	echo "</td></tr>";
	echo "</table></center>";
}


?>
