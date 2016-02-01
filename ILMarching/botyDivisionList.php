<?php
require('./wp-blog-header.php');
require('./layout.php');
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->

<?php
require("connection.inc.php");

$division = $_REQUEST['div'];
if ($division == "") $division = 1;


			$query = "SELECT * FROM Divisions";
			$result = mysql_query($query, $connect);
			$divlimits = array();
			while ($row = mysql_fetch_array($result)) {
				$divlimits[$row['Division']] = $row['MaxEnrollment'];
			}
			$year = date('Y');
			$query = "SELECT * FROM (SELECT 1 AS Division, Bands.BandID, School FROM Bands, Enrollment WHERE Bands.BandID = Enrollment.BandID AND Year = $year AND ((IHSAEnrollment > ".$divlimits[2]." AND IHSAEnrollment <> 'N/A') OR (DivisionOverride = 1))
						UNION
						SELECT 2 AS Division, Bands.BandID, School FROM Bands, Enrollment WHERE Bands.BandID = Enrollment.BandID AND Year = $year AND IHSAEnrollment > ".$divlimits[3]." AND IHSAEnrollment <= ".$divlimits[2]." AND IHSAEnrollment <> 'N/A' AND DivisionOverride <> 1
						UNION
						SELECT 3 AS Division, Bands.BandID, School FROM Bands, Enrollment WHERE Bands.BandID = Enrollment.BandID AND Year = $year AND IHSAEnrollment > ".$divlimits[4]." AND IHSAEnrollment <= ".$divlimits[3]." AND IHSAEnrollment <> 'N/A' AND DivisionOverride <> 1
						UNION
						SELECT 4 AS Division, Bands.BandID, School FROM Bands, Enrollment WHERE Bands.BandID = Enrollment.BandID AND Year = $year AND IHSAEnrollment <= ".$divlimits[4]." AND IHSAEnrollment <> 'N/A' AND DivisionOverride <> 1) as L WHERE Division = $division ORDER BY School ASC";
						
			$result = mysql_query($query, $connect);
			echo "<center><h1>Division $division Band list</h1>";
			while ($row = mysql_fetch_array($result)) {
				echo "<a href='bands_indiv.php?BandID=".$row['BandID']."&year=$year'>".$row['School'] . "</a><br />";
			}
			echo "</center>";



?>
<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
