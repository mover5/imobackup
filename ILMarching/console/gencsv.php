<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');

if (check_login_status() == false || get_login_role() != "admin") {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
	$connect = connectToDB();
}
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=enrollment.csv");
header("Pragma: no-cache");
header("Expires: 0");


$date = date("Y");
$prevdate = $date - 1;
echo "BandName,BandID,$date Enrollment,Override\n";
$query = "SELECT School, Bands.BandID, E.IHSAEnrollment, E2.DivisionOverride FROM Bands LEFT JOIN (SELECT * FROM Enrollment WHERE Year = $date) E ON Bands.BandID = E.BandID LEFT JOIN (SELECT * FROM Enrollment WHERE Year = $prevdate) E2 ON Bands.BandID = E2.BandID ORDER BY School ASC";
$result = mysql_query($query, $connect);
while ($row = mysql_fetch_array($result)) {
	echo $row['School'] . "," . $row['BandID'] . "," , $row['IHSAEnrollment'] . "," . $row['DivisionOverride'] . "\n";
}

?>
