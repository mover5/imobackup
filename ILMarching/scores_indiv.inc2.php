<?php
require("connection.inc.php");

if (!isset($_REQUEST['FestivalID'])) {
	header("Location: scores.php");
} else {
	$FestivalID = $_REQUEST['FestivalID'];
	$archive = $_REQUEST['archive'];
}


?>
<?php // imo_top(); ?>
<!--***************Start Page Content***************-->
<?php 
    $this_script = "scores_indiv.php";
	
	$query = "SELECT * FROM Festivals WHERE FestivalID = $FestivalID";
	$result = mysql_query($query, $connect);
	$row = mysql_fetch_array($result);
	$name = $row['Name'];
	$date = $row['Date'];
	$date = strtotime($date);
	$date = date("F d, Y", $date);
	$city_town = $row['Location'];
	$rain = $row['RainedOut'];				
	if ($rain) {
		echo "<h3>This show was rained out.</h3>";
	}
	$query = "SELECT * FROM Documents WHERE FestivalID = $FestivalID";
	$result = mysql_query($query, $connect);
	$linkarray = array();
	while ($row = mysql_fetch_array($result)) {
		$url = $row['LinkURL'];
		$linkname = $row['LinkName'];
		$str = "<a href='$url'>$linkname</a>";
		array_push($linkarray, $str);
	}
	$str = implode(", ", $linkarray);

	if (!$rain) {
     if ($archive) {
			  $query = "SELECT * FROM ScoresArchive WHERE FestivalID=".$FestivalID;
			  $result=mysql_query($query);
			  $num=mysql_numrows($result);
				$i=0;

				$grand_champ = mysql_result($result,$i,"GrandChampion");
				
				$class_1 = str_replace("\n", "<br>\n", mysql_result($result,$i,"Class1"));
				$class_2 = str_replace("\n", "<br>\n", mysql_result($result,$i,"Class2"));
				$class_3 = str_replace("\n", "<br>\n", mysql_result($result,$i,"Class3"));
				$class_4 = str_replace("\n", "<br>\n", mysql_result($result,$i,"Class4"));
				$class_5 = str_replace("\n", "<br>\n", mysql_result($result,$i,"Class5"));
				$class_6 = str_replace("\n", "<br>\n", mysql_result($result,$i,"Class6"));
				$class_7 = str_replace("\n", "<br>\n", mysql_result($result,$i,"Class7"));
				$class_8 = str_replace("\n", "<br>\n", mysql_result($result,$i,"Class8"));
				$class_9 = str_replace("\n", "<br>\n", mysql_result($result,$i,"Class9"));
				$class_10 = str_replace("\n", "<br>\n", mysql_result($result,$i,"Class10"));

		echo "<table border=1 cellpadding=5 id=tablescore width=98%>";
		echo "<tr><td>$class_1";
		if($class_2 != ""){
			echo "<td>$class_2";
			if($class_3 != ""){
				echo "<td>$class_3";
				if($class_4 != ""){
					echo "<tr><td>$class_4";
					if($class_5 != ""){
						echo "<td>$class_5";
						if($class_6 != ""){
							echo "<td>$class_6";
							if($class_7 != ""){
								echo "<tr><td>$class_7";
								if($class_8 != ""){
									echo "<td>$class_8";
									if($class_9 != ""){
										echo "<td>$class_9";
										if($class_10 != ""){
											echo "<tr><td>$class_10";
										}
									}
								}
							}
						}
					}
				}
			}
		}	
		echo "</table>";
		echo "<table border=1 cellpadding=5 width=98%><tr><td><b>Grand Champion: $grand_champ</b></table>"; 
	 } else {
		
			$query = "SELECT School, GrandChampionBandName, ScoreNotes FROM Festivals LEFT JOIN Bands ON Festivals.GrandChampionID = Bands.BandID WHERE FestivalID = $FestivalID";
			$result = mysql_query($query, $connect);
			$row = mysql_fetch_array($result);
			$grand_champ = $row['School'];
			$notes = nl2br($row['ScoreNotes']);
			
			if ($grand_champ == NULL || $grand_champ == "") {$grand_champ = $row['GrandChampionBandName'];}

			//added constraint to only show GC if name is found
		if (($grand_champ != NULL && $grand_champ != "") || ($str <> "")) { 

			echo "<table width='100%'><tr>";
			echo "<td>";
			if ($grand_champ != NULL && $grand_champ != "") 
				echo "<h3>Grand Champion: $grand_champ</h3>"; 
			echo "</td>";
			echo "<td ALIGN='right'>";
			if ($str <> "") {
				echo "<h3>Links: " . $str . "</h3>";
			}
			echo "</td>";
			echo "<tr><table>";
		}	
			 $query = "SELECT FestivalYear FROM Festivals WHERE FestivalID = $FestivalID";
		 $result = mysql_query($query, $connect);
		 $row = mysql_fetch_array($result);
		 $year = $row['FestivalYear'];
		 $query = "SELECT * FROM Scores WHERE FestivalID = $FestivalID AND Type='Field2'";
		 $result = mysql_query($query, $connect);
		 if (mysql_num_rows($result) > 0) {
			 		 $query = "SELECT Scores.BandName, Bands.BandID, ClassPlace, Score, Captions,  Scores.Class, Type, School, IF(Videos.Approved = 1, VideoURL, NULL) AS VideoURL
		 FROM Scores 
		 LEFT JOIN Bands ON Scores.BandID = Bands.BandID 
		 JOIN ClassOrder ON ClassOrder.Class = Scores.Class 
         LEFT JOIN Videos ON (Scores.BandID = Videos.BandID AND Scores.FestivalID = Videos.FestivalID AND Videos.PerformanceType = IF(Scores.Class = 'Finals', 'Finals', IF(Scores.Class = 'SemiFinals', 'SemiFinals', 'Prelim')))
		 WHERE Scores.FestivalID = $FestivalID
		 ORDER BY Type ASC, ClassOrder.OrderNum, ClassPlace ASC, Score DESC, School ASC";
		 } else {
			 		 $query = "SELECT Scores.BandName, Bands.BandID, ClassPlace, Score, Captions,  Scores.Class, Type, School, IF(Videos.Approved = 1, VideoURL, NULL) AS VideoURL
		 FROM Scores 
		 LEFT JOIN Bands ON Scores.BandID = Bands.BandID 
		 JOIN ClassOrder ON ClassOrder.Class = Scores.Class
         LEFT JOIN Videos ON (Scores.BandID = Videos.BandID AND Scores.FestivalID = Videos.FestivalID AND Videos.PerformanceType = IF(Scores.Class = 'Finals', 'Finals', IF(Scores.Class = 'SemiFinals', 'SemiFinals', 'Prelim')))
         WHERE Scores.FestivalID = $FestivalID 
		 ORDER BY Type DESC, ClassOrder.OrderNum, ClassPlace ASC, Score DESC, School ASC";
		 }

			$result = mysql_query($query, $connect);

			$prevtype = "";
			$prevclass = "";
			$num = 0; 
	$count = 0;
			echo "<table border=0><tr>";
			while ($row = mysql_fetch_array($result)) {
			$count = $count + 1;
 
				$type = $row['Type'];
				if ($type == "Field2") {$type = "Field Day 2";}
				$class = $row['Class'];
				$score = $row['Score'];
				$videourl = $row['VideoURL'];
				$captions = $row['Captions'];
				$bandid = $row['BandID'];
				if ($captions <> "") {$captions = "(" . $captions . ")";}
				$school = $row['School'];
				if ($school == NULL || $school == "") {$school = $row['BandName'];}
				if ($score == NULL || $score == "") {
					$score = $row['ClassPlace'].")";
					if ($row['ClassPlace'] == 99) {
						$score = "";
					}
				}
				if ($videourl <> NULL && $videourl <> "") {
                    $vidid = substr($videourl, stripos($videourl, "?v=") + 3);
                    $imovid = "videos.php?video=" . $vidid;
				    $videolink = "<a href='$imovid'><img width='20' height='20' src='images/videoicon.png'></a>";
				} else {
				    $videolink = "";
				}

				if ($type <> $prevtype || $class <> $prevclass) {
					if ($num <> 0) {echo "</td>";}	
					if ($num == 3) {
						echo "</tr><tr>";
						$num = 0;
					}
					$num++;	
					echo "<td valign='top' style='padding-bottom:25px;padding-right:25px;'>";
					if ($class == "Finals" || $class == "SemiFinals") {
						echo "<h3 style='border-bottom:2px solid black'>" . $class . ":</h3>";
					} else {
						echo "<h3 style='border-bottom:2px solid black'>".$type . " " . $class . ":</h3>";
					}
					
				}
				$prevtype = $type;
				$prevclass = $class;
				if ($bandid == NULL || $bandid == "") {
					echo "".$score . " ".$school." " . $captions . " " . $videolink . "</font><br /><hr>";
				} else {
					echo "".$score . " <a href='bands_indiv.php?BandID=".$bandid."&year=".$year."'>".$school."</a> " . $captions . " " . $videolink . "</font><br /><hr>";
				}
			}
		if ($count == 0)
		 echo "<td width='100%'> No results available yet";
			echo "</td></tr></table>";
			$query = "SELECT ScoreNotes FROM Festivals LEFT JOIN Bands ON Festivals.GrandChampionID = Bands.BandID WHERE FestivalID = $FestivalID";
			$result = mysql_query($query, $connect);
			$row = mysql_fetch_array($result);
			$notes = nl2br($row['ScoreNotes']);
			echo "<table border=0 cellpadding=5 width=98%>";
			if ($notes <> NULL && $notes <> "") {
				echo "<tr><td><b>Notes:</b> $notes</td></tr>";
			}
			echo "</table>"; 
	}     
}
echo "<hr>";  

?>

<!--***************End Page Content***************-->

<?php //imo_bottom(); ?>
