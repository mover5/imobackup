<?php 
require('./wp-blog-header.php');
require('./layout.php');
require('./connection.inc.php');
?>
<!--***************Start Page Content***************-->
<?php imo_top(); ?>

<script type="text/javascript" src="js/helperFunctions.js"></script>
<script type="text/javascript">

function collapse(id) {
	table=document.getElementById(id + '_table');

	for(var i=1;i<table.rows.length;i++){
		table.rows[i].style.display='none';
	}
	
	document.getElementById(id+'_button').src = 'images/expand.gif';
	document.getElementById(id+'_button').onclick = new Function('expand("'+id+'")');
}

function expand(id) {
	table=document.getElementById(id + '_table');

	for(var i=1;i<table.rows.length;i++){
		table.rows[i].style.display='';
	}
	
	document.getElementById(id+'_button').src = 'images/collapse.gif';
	document.getElementById(id+'_button').onclick = new Function('collapse("'+id+'")');
}

</script>
<?php 

$this_script = "festivals.php";
	
	if (isset($_REQUEST['year'])) {
		$queryyear = $_REQUEST['year'];
	} else {
		$queryyear = date('Y');
	}
	$query = "SELECT DISTINCT(FestivalYear) FROM Festivals ORDER BY FestivalYear DESC";
	$result = mysql_query($query, $connect) or die("Fail") ; 
$yeararray = array();
$yearSelector = "";
	
while ($row = mysql_fetch_array($result)) {
	$year = $row['FestivalYear'];
	if ($row['FestivalYear'] <= date('Y')) {
		array_push($yeararray, "<a href='scores.php?year=".$row['FestivalYear']."'>".$row['FestivalYear']."</a>");
	}

	if ($year != $queryyear)
		$yearSelector = $yearSelector . "<option value='festivals.php?year=$year'>$year</option>";				
	else
		$yearSelector = $yearSelector . "<option value='festivals.php?year=$year' selected>$year</option>";						
}
					
//	echo "<p><h2>Illinois Marching Online Festivals: $queryyear</h2><p>";
	echo "<h2>Illinois Marching Online Festivals: $queryyear</h2>";
/*
	echo "<h3>Years: ";	
	echo implode(", ", $yeararray);
	echo "</h3>";
*/

		
//	 		echo "<br>";
			echo "<form style='margin-bottom:0;' name='form1'>";
			echo "Select a year: ";
			echo "<select name='switch_year' onChange=\"MM_jumpMenu('parent',this,0)\" class='form_input'>";
			echo "$yearSelector";
			echo "</select></form>";
	 		echo "<br>";
	


	$query = "SELECT * FROM (SELECT Festivals.FestivalID, Festivals.Name, Festivals.Date, Festivals.Location, 0 as Archive, School as GrandChampion
	FROM Festivals, Scores, Bands 
	WHERE Festivals.FestivalID = Scores.FestivalID AND Festivals.GrandChampionID = Bands.BandID AND FestivalYear = $queryyear
	GROUP BY Festivals.FestivalID 
UNION
SELECT FestivalID, Name, Date, Location, 0 as Archive, 'Rained Out' as GrandChampion
FROM Festivals WHERE FestivalYear = $queryyear AND RainedOut = 1
UNION
SELECT Festivals.FestivalID, Festivals.Name, Festivals.Date, Festivals.Location, 0 as Archive, GrandChampionBandName as GrandChampion
	FROM Festivals, Scores, Bands 
	WHERE Festivals.FestivalID = Scores.FestivalID AND GrandChampionID IS NULL AND FestivalYear = $queryyear
	GROUP BY Festivals.FestivalID 
	UNION
		SELECT Festivals.FestivalID, Festivals.Name, Festivals.Date, Festivals.Location, 0 as Archive, '' as GrandChampion
		FROM Festivals
		WHERE GrandChampionID IS NULL AND GrandChampionBandName IS NULL AND FestivalYear = $queryyear
		GROUP BY Festivals.FestivalID 
	UNION 
	SELECT Festivals.FestivalID, Name, Date, Location, 1 as Archive, GrandChampion
	FROM ScoresArchive, Festivals 
	WHERE ScoresArchive.FestivalID = Festivals.FestivalID AND FestivalYear = $queryyear) as A ORDER BY Date, Name
";
		$result = mysql_query($query, $connect);
	$num = mysql_num_rows($result);
	if ($num == 0) {
		echo "<h2>No Scores to Display</h2>";
	} else {
		
	$prevdate = "";
	$count = 0;
	$initial = 1;
	$headings = 0;
	echo "<table cellspacing='5' border='0' width='100%'>";
	while ($row = mysql_fetch_array($result)) {
		$FestivalID = $row['FestivalID'];
		$name = $row['Name'];
		$date = $row['Date'];
		$city_town = $row['Location'];
//waj	$gc = $row['GrandChampion'];
$gc = $row['GrandChampion'];
if (strlen($gc) > 0)
  if ($gc != 'Rained Out')
    $gc = "Grand Champion: " . $gc;
		$archive = $row['Archive'];
		
		// Normalize Height
		/*if (strlen($gc) > 17 || strlen($city_town) > 17 || strlen($name) > 39) {
			if (strlen($gc) <= 17) {
				$gc = $gc . "<br />&nbsp;";
			}
			
			if (strlen($city_town) <= 17) {
				$city_town = $city_town . "<br />&nbsp;";
			}
			
			if (strlen($name) <= 39) {
				$name = "<a href=\"./scores_indiv.php?FestivalID=$FestivalID&archive=$archive\">$name</a><br />&nbsp;";
			} else {
				$name = "<a href=\"./scores_indiv.php?FestivalID=$FestivalID&archive=$archive\">$name</a>";
			}
		} else {
			$name = "<a href=\"./scores_indiv.php?FestivalID=$FestivalID&archive=$archive\">$name</a>";
		}*/
//WAJ		$name = "<a href=\"./scores_indiv.php?FestivalID=$FestivalID&archive=$archive\">$name</a>";
	$name = "<a href=\"./festivals_detail.php?FestivalID=$FestivalID&archive=$archive\">$name</a>";

		if ($prevdate <> $date) {
			if ($initial == 0) {
				echo "</tbody><tbody class='divider'><tr><td colspan='3'></td></tr></tbody>";
			}
			$prevdate = $date;
			$date = strtotime($date);
			$date = date("F d, Y", $date);
			echo "<tbody name='".$headings."_table' id='".$headings."_table' class='data'>";

			if ($initial == 1) {
				$contentHead = "contentHeader1";
				$initial = 0;
			} else {
				$contentHead = "contentHeader2";
			}
			//echo "<tr><td colspan='3' width='620'><div class='cat_bar'>$date
			//<div id='collapseButton'><img src='images/collapse.gif' onClick='collapse(\"".$headings."\")' 
			//name='".$headings."_button' id='".$headings."_button'></div></div></td></tr>";
			
			echo "<tr><td colspan='3'><div class='cat_bar'>";
			echo "<div class='hbg'>";
			echo "<div class='collapse'>
			<img src='images/collapse.gif' onClick='collapse(\"".$headings."\")' name='".$headings."_button' id='".$headings."_button'>
			</div>"; // Button
			echo $date; // Date
			echo "</div></div></td></tr>";
/*WAJ no col headers
			echo "<tr class='windowbg2'>
		<td class='windowbg'><b>Festival</b></td>
		<td class='windowbg'><b>City/Town</b></td>
		<td class='windowbg'><b>Grand Champion</b></td></tr>";
*/
		$count = 0;
			$headings++;
		}
		if ($count % 2 == 0) {
			$content = "h";	
		} else {
			$content = "windowbg";	
		}
		/*echo "<tr>
		<td width='320'><div id='$content'>$name</div></td>
		<td width='150'><div id='$content'>$city_town</div></td>
		<td width='150'><div id='$content'>$gc</div></td></tr>";*/
		echo "<tr class='windowbg2'>
		<td class='$content'>$name</td>
		<td class='$content'>$city_town</td>
		<td class='$content'>$gc</td></tr>";
//waj		<td class='$content'>$gc</td></tr>";
		$count++;
	}
	echo "</tbody>";
	echo "</table>";
}

?>

<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
