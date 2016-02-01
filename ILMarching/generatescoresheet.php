<?php
require_once '/home/ilmarch/php/Spreadsheet/Excel/Writer.php';
require("connection.inc.php"); 

if (isset($_REQUEST['FestivalID'])) {
	$festid = $_REQUEST['FestivalID'];
	$query = "SELECT Bands.BandID, School, Schedules.BandName, Class 
	FROM Schedules 
	LEFT JOIN Bands 
	ON Schedules.BandID = Bands.BandID 
	WHERE FestivalID = $festid
	AND Type='Field' 
	AND Class <> 'Break' 
	AND Class <> 'Awards' 
	ORDER BY PerformanceTime";
} else {
	$query = "";
	$festid = "";
}


$workbook = new Spreadsheet_Excel_Writer();
$worksheet =& $workbook->addWorksheet();
$worksheet->setLandscape();

//Set the size of all the columns
$worksheet->setColumn(0,0,13.29);
$worksheet->setColumn(1,1,4.29);
$worksheet->setColumn(2,7,3.29);
$worksheet->setColumn(8,9,4.29);
$worksheet->setColumn(10,11,3.29);
$worksheet->setColumn(12,12,4.29);
$worksheet->setColumn(13,14,3.29);
$worksheet->setColumn(15,16,4.29);
$worksheet->setColumn(17,18,3.29);
$worksheet->setColumn(19,20,4.29);
$worksheet->setColumn(21,22,3.29);
$worksheet->setColumn(23,23,4.29);
$worksheet->setColumn(24,24,6.00);

//Header Formatting
$header =& $workbook->addFormat();
$header->setBold();
$header->setColor('black');
$header->setSize('10');

//Write the headers
$worksheet->write(0,3, 'MUSIC PERFORMANCE', $header);
$worksheet->write(0,11, 'VISUAL PERFORMANCE', $header);
$worksheet->write(0,19, 'GENERAL EFFECT', $header);

//Secondary Header Formatting
$header2 =& $workbook->addFormat();
$header2->setColor('black');
$header2->setSize('10');

//Write Secondary Headers
$worksheet->write(1,2,'Individual', $header2);
$worksheet->write(1,6,'   Ensemble', $header2);
$worksheet->write(1,10,'     Individual', $header2);
$worksheet->write(1,14,'Ensemble', $header2);
$worksheet->write(1,18,'   Music', $header2);
$worksheet->write(1,22,'Visual', $header2);

//Caption Header Formatting
$header3 =& $workbook->addFormat();
$header3->setColor('black');
$header3->setSize('8.5');
$header3->setTop('1');
$header3->setBottom('1');

//Caption Header Formatting Bold
$header3b =& $workbook->addFormat();
$header3b->setColor('black');
$header3b->setSize('8.5');
$header3b->setTop('1');
$header3b->setBottom('1');
$header3b->setBold();

//Caption Header Formatting With Right
$header3br =& $workbook->addFormat();
$header3br->setColor('black');
$header3br->setSize('8.5');
$header3br->setTop('1');
$header3br->setBottom('1');
$header3br->setRight('1');
$header3br->setBold();

//Write Captions
$worksheet->write(2,0,'School', $header3br);
$worksheet->write(2,1,'Brass', $header3);
$worksheet->write(2,2,'WW', $header3);
$worksheet->write(2,3,'Perc', $header3);
$worksheet->write(2,4,'Tot', $header3br);
$worksheet->write(2,5,'Qty', $header3);
$worksheet->write(2,6,'Acc', $header3);
$worksheet->write(2,7,'Mus', $header3);
$worksheet->write(2,8,'Tot', $header3b);
$worksheet->write(2,9,'AVG', $header3br);
$worksheet->write(2,10,'Acc', $header3);
$worksheet->write(2,11,'Qual', $header3);
$worksheet->write(2,12,'Tot', $header3br);
$worksheet->write(2,13,'Exc', $header3);
$worksheet->write(2,14,'Artis', $header3);
$worksheet->write(2,15,'Tot', $header3b);
$worksheet->write(2,16,'AVG', $header3br);
$worksheet->write(2,17,'Rep', $header3);
$worksheet->write(2,18,'Perf', $header3);
$worksheet->write(2,19,'Tot', $header3b);
$worksheet->write(2,20,'Tot*2', $header3br);
$worksheet->write(2,21,'Rep', $header3);
$worksheet->write(2,22,'Perf', $header3);
$worksheet->write(2,23,'Tot', $header3br);
$worksheet->write(2,24,'Total', $header3br);

//Setup Virticle Lines
$vlines =& $workbook->addFormat();
$vlines->setRight('1');

//Write Vlines
$worksheet->writeBlank(0,0,$vlines);
$worksheet->writeBlank(1,0,$vlines);
$worksheet->writeBlank(1,4,$vlines);
$worksheet->writeBlank(0,9,$vlines);
$worksheet->writeBlank(1,9,$vlines);
$worksheet->writeBlank(1,12,$vlines);
$worksheet->writeBlank(0,16,$vlines);
$worksheet->writeBlank(1,16,$vlines);
$worksheet->writeBlank(1,20,$vlines);
$worksheet->writeBlank(0,23,$vlines);
$worksheet->writeBlank(1,23,$vlines);
$worksheet->writeBlank(0, 24, $vlines);
$worksheet->writeBlank(1, 24, $vlines);


$prevclass = "NOTSET";
$rownum = 3;
//School Names
$bandtext =& $workbook->addFormat();
$bandtext->setColor('black');
$bandtext->setSize('7');
$bandtext->setRight('1');
$bandtext->setFontFamily('Courier');

//Class Names
$classtext =& $workbook->addFormat();
$classtext->setColor('black');
$classtext->setSize('10');
$classtext->setRight('1');
$classtext->setBold();

$result = mysql_query($query, $connect);
while ($row = mysql_fetch_array($result)) {
	$class = $row['Class'];
	$school = $row['School'];
	$bandname = $row['BandName'];
	if ($school == NULL) $school = $bandname;
	
	if ($prevclass <> $class) {
		if ($prevclass <> 'NOTSET') {
			$worksheet->writeBlank($rownum, 0, $vlines);
			$worksheet->writeBlank($rownum, 4, $vlines);
			$worksheet->writeBlank($rownum, 9, $vlines);
			$worksheet->writeBlank($rownum, 12, $vlines);
			$worksheet->writeBlank($rownum, 16, $vlines);
			$worksheet->writeBlank($rownum, 20, $vlines);
			$worksheet->writeBlank($rownum, 23, $vlines);
			$worksheet->writeBlank($rownum, 24, $vlines);
			$rownum = $rownum + 1;
		}
		$prevclass = $class;
		$worksheet->write($rownum, 0, $class, $classtext);
		$worksheet->writeBlank($rownum, 4, $vlines);
		$worksheet->writeBlank($rownum, 9, $vlines);
		$worksheet->writeBlank($rownum, 12, $vlines);
		$worksheet->writeBlank($rownum, 16, $vlines);
		$worksheet->writeBlank($rownum, 20, $vlines);
		$worksheet->writeBlank($rownum, 23, $vlines);
		$worksheet->writeBlank($rownum, 24, $vlines);
		$rownum = $rownum + 1;
		
	}
	
	$worksheet->write($rownum, 0, substr($school, 0, 17), $bandtext);
	$worksheet->writeBlank($rownum, 4, $vlines);
	$worksheet->writeBlank($rownum, 9, $vlines);
	$worksheet->writeBlank($rownum, 12, $vlines);
	$worksheet->writeBlank($rownum, 16, $vlines);
	$worksheet->writeBlank($rownum, 20, $vlines);
	$worksheet->writeBlank($rownum, 23, $vlines);
	$worksheet->writeBlank($rownum, 24, $vlines);
	
	$rownum = $rownum + 1;
}

//Top/Bottom
$topbottom =& $workbook->addFormat();
$topbottom->setTop('1');
$topbottom->setBottom('1');

//Top
$top =& $workbook->addFormat();
$top->setTop('1');


for ($i = 0; $i < 25; $i++) {
	$worksheet->writeBlank($rownum, $i, $topbottom);
}
$rownum = $rownum + 1;
$worksheet->write($rownum, 0, 'Max Totals', $classtext);
$header3->setSize('10');
$header3b->setSize('10');
$header3br->setSize('10');

$worksheet->write($rownum,1,'7.5', $header3);
$worksheet->write($rownum,2,'7.5', $header3);
$worksheet->write($rownum,3,'5', $header3);
$worksheet->write($rownum,4,'20', $header3br);
$worksheet->write($rownum,5,'7.5', $header3);
$worksheet->write($rownum,6,'7.5', $header3);
$worksheet->write($rownum,7,'5', $header3);
$worksheet->write($rownum,8,'20', $header3b);
$worksheet->write($rownum,9,'20', $header3br);
$worksheet->write($rownum,10,'10', $header3);
$worksheet->write($rownum,11,'10', $header3);
$worksheet->write($rownum,12,'20', $header3br);
$worksheet->write($rownum,13,'10', $header3);
$worksheet->write($rownum,14,'10', $header3);
$worksheet->write($rownum,15,'20', $header3b);
$worksheet->write($rownum,16,'20', $header3br);
$worksheet->write($rownum,17,'10', $header3);
$worksheet->write($rownum,18,'10', $header3);
$worksheet->write($rownum,19,'20', $header3b);
$worksheet->write($rownum,20,'40', $header3br);
$worksheet->write($rownum,21,'10', $header3);
$worksheet->write($rownum,22,'10', $header3);
$worksheet->write($rownum,23,'20', $header3br);
$worksheet->write($rownum,24,'100', $header3br);

$rownum = $rownum + 1;
for ($i = 0; $i < 25; $i++) {
	$worksheet->writeBlank($rownum, $i, $top);
}

if ($festid <> "") {
	$query = "SELECT Name FROM Festivals WHERE FestivalID = $festid";
	$result = mysql_query($query, $connect);
	$row = mysql_fetch_array($result);
	$name = $row['Name'];
	$name = str_replace(" ", "_", $name);
	$filename = "Score_Sheet_$name.xls";
} else {
	$filename = "blankscoresheet.xls";
}
$workbook->send($filename);
$workbook->close();
?>
