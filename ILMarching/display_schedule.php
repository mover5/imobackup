<?php
$query = "SELECT Schedules.BandID, IF(Schedules.BandID IS NULL, Schedules.BandName, School) AS Name, Type, Class, DATE_FORMAT(PerformanceTime, '%l:%i %p') AS PerformanceTimeAMPM FROM Schedules LEFT JOIN Bands ON Schedules.BandID = Bands.BandID WHERE FestivalID = $FestivalID ORDER BY PerformanceTime ASC, Class ASC, Name ASC";
		$result = mysql_query($query, $connect);
		$num = mysql_num_rows($result);
		if ($num > 0) {
			$parade = "";
			$field = "";
			$field2 = "";
			$prevfieldclass = "NOTSET";
			$prevfield2class = "NOTSET";
			$prevparadeclass = "NOTSET";

			while ($row = mysql_fetch_array($result)) {
				$type = $row['Type'];
				$performancetime = $row['PerformanceTimeAMPM'];
				if ($performancetime == "12:00 AM") {
					$performancetime = "";
				} else {
					$performancetime .= ": ";
				}
				$class = $row['Class'];
				if ($class == "" || $class == NULL) {$class = "NOCLASS";}
				$school = $row['Name'];
				$bandid = $row['BandID'];
				$bandname = $row['Name'];
				
				if ($type == "Field") {
					if ($prevfieldclass <> $class) {
						if ($class == "Break" || $class == "Awards") {
							$field .= "<br />".$performancetime."<b>".$class."</b><br />";
						} else {
							if ($field == "") {
								if ($class <> "" && $class <> "NOCLASS" && $class <> "Exb") {
									$field .= "<b>Class $class</b><br />";
								} else if ($class == "Exb") {
									$field .= "<b>$class</b><br />";
								}	
							} else {
								if ($class <> "" && $class <> "NOCLASS" && $class <> "Exb") {
									$field .= "<br /><b>Class $class</b><br />";
								} else if ($class == "Exb") {
									$field .= "<br /><b>$class</b><br />";
								} else {
									$field .= "<br />";
								}
							}
							
						}
						$prevfieldclass = $class;
					} 
					if ($class <> "Break" && $class <> "Awards") {	
						if ($bandid == NULL || $bandid == "") {
							$field .= $performancetime."".$bandname."<br />";
						} else {
							$field .= $performancetime."<a href='bands_indiv.php?BandID=".$bandid."&year=".$showYear."'>".$school."</a><br />";
						}
							
					}
				} elseif ($type == "Field2") {
					if ($prevfield2class <> $class) {
						if ($class == "Break" || $class == "Awards") {
							$field2 .= "<br />".$performancetime."<b>".$class."</b><br />";
						} else {
							if ($field2 == "") {
								if ($class <> "" && $class <> "NOCLASS" && $class <> "Exb") {
									$field2 .= "<b>Class $class</b><br />";
								} else if ($class == "Exb") {
									$field2 .= "<b>$class</b><br />";
								}	
							} else {
								if ($class <> "" && $class <> "NOCLASS" && $class <> "Exb") {
									$field2 .= "<br /><b>Class $class</b><br />";
								} else if ($class == "Exb") {
									$field2 .= "<br /><b>$class</b><br />";
								} else {
									$field2 .= "<br />";
								}
							}
							
						}
						$prevfield2class = $class;
					} 
					if ($class <> "Break" && $class <> "Awards") {	
						if ($bandid == NULL || $bandid == "") {
							$field2 .= $performancetime."".$bandname."<br />";
						} else {
							$field2 .= $performancetime."<a href='bands_indiv.php?BandID=".$bandid."&year=".$showYear."'>".$school."</a><br />";
						}
							
					}
				} else {
					if ($prevparadeclass <> $class) {
						if ($class == "Break" || $class == "Awards") {
							$parade .= "<br />".$performancetime."<b>".$class."</b><br />";
						} else {
							if ($parade == "") {
								if ($class <> "" && $class <> "NOCLASS" && $class <> "Exb") {
									$parade .= "<b>Class $class</b><br />";
								} else if ($class == "Exb") {
									$parade .= "<b>$class</b><br />";
								}
							} else {
								if ($class <> "" && $class <> "NOCLASS" && $class <> "Exb") {
									$parade .= "<br /><b>Class $class</b><br />";
								} else if ($class == "Exb") {
									$parade .= "<br /><b>$class</b><br />";
								} else {
									$parade .= "<br />";
								}
							}
						}
						$prevparadeclass = $class;
					}
					if ($class <> "Break" && $class <> "Awards") {
						if ($bandid == NULL || $bandid == "") {
							$parade .= $performancetime."".$bandname."<br />";
						} else {
							$parade .= $performancetime."<a href='bands_indiv.php?BandID=".$bandid."&year=".$showYear."'>".$school."</a><br />";
						}
					}
				}	
			}
		} else {
			$query = "SELECT * FROM SchedulesArchive WHERE FestivalID = $FestivalID";
			$result = mysql_query($query, $connect);
			$num = mysql_num_rows($result);
			if ($num > 0) {
				$row = mysql_fetch_array($result);
				$parade = $row['Parade'];
				$field = $row['Field'];
			} else {
				$parade = "No Schedules Yet";
				$field = "";
			}
		}
		
		
		
		echo "<table id=tablescore border=1 cellpadding=4><tr>";
		if ($parade == "No Schedules Yet") {
			echo "<td>$parade</td>";
		} else {
			if ($parade <> "") {
				echo "<td>Parade</td>";
			}
			if ($field <> "") {
				echo "<td>Field</td>";
			}
			if ($field2 <> "") {
				echo "<td>Field Day 2</td>";
			}
			if ($parade <> "" || $field <> "" || $field2 <> "") {
				echo "</tr><tr>";
			}
			if ($parade <> "") {
				echo "<td>$parade</td>";
			}
			if ($field <> "") {
				echo "<td>$field</td>";
			}
			if ($field2 <> "") {
				echo "<td>$field2</td>";
			}
		}
		echo "</tr></table><p></p>";
?>
