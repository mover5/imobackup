<?php
function getDivision($enrollment, $override, $connect) {
	if ($override == 1) {
		return 1;
	} else if ($enrollment == NULL || $enrollment == "") {
		return "";	
	}else {
		$query = "SELECT * FROM Divisions";
		$result = mysql_query($query, $connect);
		$values = array();
		while ($row = mysql_fetch_array($result)) {
			$values[$row['Division']] = $row['MaxEnrollment'];
		}
		if ($enrollment == "N/A") {
			return 5;
		} else if ($enrollment <= $values[4]) {
			return 4;
		} else if ($enrollment > $values[4] && $enrollment <= $values[3]) {
			return 3;
		} else if ($enrollment > $values[3] && $enrollment <= $values[2]) {
			return 2;
		} else if ($enrollment > $values[2]) {
			return 1;
		} 
	}
}

$thisyear = date('Y');
$lastyear = $thisyear - 1;

$query = "SELECT * FROM Enrollment WHERE BandID = $BandID AND Year = $thisyear";
$result = mysql_query($query, $connect);
$num = mysql_num_rows($result);
if ($num > 0) {
	$row = mysql_fetch_array($result);
	$thisenroll = $row['IHSAEnrollment'];
	$thisoverride = $row['DivisionOverride'];
} else {
	$thisenroll = "";
	$thisoverride = 0;
}
$query = "SELECT * FROM Enrollment WHERE BandID = $BandID AND Year = $lastyear";
$result = mysql_query($query, $connect);
$num = mysql_num_rows($result);
if ($num > 0) {
	$row = mysql_fetch_array($result);
	$lastenroll = $row['IHSAEnrollment'];
	$lastoverride = $row['DivisionOverride'];
} else {
	$lastenroll = "";
	$lastoverride = 0;
}

$thisDiv = getDivision($thisenroll, $thisoverride, $connect);
$lastDiv = getDivision($lastenroll, $lastoverride, $connect);

echo "<div style='padding:5px 0px 0px 0px'><b>$thisyear Enrollment</b> $thisenroll";
if ($thisenroll == "")
	echo "<i>n/a</i>";
echo "&nbsp;&nbsp;&nbsp;<b>IMO Division</b> ".$thisDiv;
if ($thisDiv == "")
	echo "<i>n/a</i>";
echo "</div>";
echo "<div style='padding:5px 0px 0px 0px'><b>$lastyear Enrollment</b> $lastenroll";
if ($lastenroll == "")
	echo "<i>n/a</i>";
echo "&nbsp;&nbsp;&nbsp;<b>IMO Division</b> ".$lastDiv;
if ($lastDiv == "")
	echo "<i>n/a</i>";
echo "</div>";


?>
