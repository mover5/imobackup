<?php
require('./wp-blog-header.php');
require('./layout.php');
include('connection.inc.php');
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->
<?php 
	$thisyear = date('Y');
   echo "<h3>Illinois Marching Online $thisyear Repertoire Listing</h3>";
   
   //Calculate Number of shows this year
   $query = "SELECT * FROM BandShows WHERE Year = $thisyear AND Title <> '' AND Title IS NOT NULL AND Title <> 'None'";
   $result = mysql_query($query, $connect);
   $numberofshows = mysql_num_rows($result);
   
   //Calculate Number of Bands
   $query = "SELECT * FROM Bands";
   $result = mysql_query($query, $connect);
   $numberofbands = mysql_num_rows($result);
   
   echo "<h2>" . $numberofshows . " / " . $numberofbands . " Shows Available</h2>";
   
   $query = "SELECT School, WebsiteURL, BandID From Bands ORDER BY School";
   $result = mysql_query($query, $connect);
   
   echo "<table id=tablescore border=1 cellpadding=4>";
   echo "<tr><th>School</th><th>Website</th><th>$thisyear Show</th></tr>";
   while ($row = mysql_fetch_array($result)) {
		$website = $row['WebsiteURL'];
		$school = $row['School'];
		$bandid = $row['BandID'];
		$getshows = "SELECT Title FROM BandShows WHERE BandID = $bandid AND Year = $thisyear";
		$getshowsresult = mysql_query($getshows, $connect);
		if (mysql_num_rows($result) == 0) {
			$show = "";
		} else {
			$showrow = mysql_fetch_array($getshowsresult);
			$show = $showrow['Title'];
		}
		
		if(($show != "none")&&($show != "")){
			$row_color = "#00FF00";
			$year = date("Y");
			$displaydate = mktime(0,0,0,5,1,$year);
			$today = time();
			if ($today < $displaydate) {
				$show = "Show Name Not Released";
			}
		} else {
			$row_color = "#FF0000";
		}
		
		echo "<tr bgcolor=$row_color>";
		echo "<td><a href='bands_indiv.php?BandID=$bandid'>$school</a></td>";
		if(($website != "none")&&($website != "")){echo "<td><a href=$website target=_blank>Website</a></td>";} else {echo "<td>No Website</td>";}
		if(($show != "none")&&($show != "")) {echo "<td>$show</td>";} else {echo "<td><b>NO INFORMATION</b></td>";}
		echo "</tr>";
   }
   echo "</table>";
   
   
?>
<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
