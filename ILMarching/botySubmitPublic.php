<?php
require('./wp-blog-header.php');
require('./layout.php');
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->
<?php
require("connection.inc.php");

$query = "SELECT Voted FROM BandsOfTheYearVotersPublic WHERE EmailHash = PASSWORD('" . $_POST['user'] . "')";
$result = mysql_query($query);
$row = mysql_fetch_array($result);
if ($row['Voted'] == 0) {

	$query = "SELECT * FROM BandsOfTheYearSettings LIMIT 1";
	$result = mysql_query($query, $connect);
	$row = mysql_fetch_array($result);
	$startDate = $row['StartDate'];
	$endDate = $row['EndDate'];
	$div = array();
	$div[1] = $row['Div1Num'];
	$div[2] = $row['Div2Num'];
	$div[3] = $row['Div3Num'];
	$div[4] = $row['Div4Num'];

	for ($division = 1; $division <= 4; $division++) {
		for ($i = 1; $i <= $div[$division]; $i++) {
			$bandid = $_POST[$division.'_'.$i];
			$query = "SELECT * FROM BandsOfTheYearPublic WHERE BandID = $bandid";
			$result = mysql_query($query, $connect);
			if (mysql_num_rows($result) == 0) {
				$firstplacevotes = 0;
				$secondplacevotes = 0;
				$thirdplacevotes = 0;
				$score = 0;
				$score = $score + ($div[$division] - $i + 1);
				if ($i == 1) {
					$firstplacevotes = $firstplacevotes + 1;
				} else if ($i == 2) {
					$secondplacevotes = $secondplacevotes + 1;
				} else if ($i == 3) {
					$thirdplacevotes = $thirdplacevotes + 1;
				}
				$query = "INSERT INTO BandsOfTheYearPublic (BandID, IMODivision, Score, FirstPlaceVotes, SecondPlaceVotes, ThirdPlaceVotes) VALUES ($bandid, $division, $score, $firstplacevotes, $secondplacevotes, $thirdplacevotes)";
				mysql_query($query, $connect);
			} else {
				$row = mysql_fetch_array($result);
				$botyid = $row['BotYID'];
				$firstplacevotes = $row['FirstPlaceVotes'];
				$secondplacevotes = $row['SecondPlaceVotes'];
				$thirdplacevotes = $row['ThirdPlaceVotes'];
				$score = $row['Score'];
				$score = $score + ($div[$division] - $i + 1);
				if ($i == 1) {
					$firstplacevotes = $firstplacevotes + 1;
				} else if ($i == 2) {
					$secondplacevotes = $secondplacevotes + 1;
				} else if ($i == 3) {
					$thirdplacevotes = $thirdplacevotes + 1;
				}
				$query = "UPDATE BandsOfTheYearPublic SET Score = $score, FirstPlaceVotes = $firstplacevotes, SecondPlaceVotes = $secondplacevotes, ThirdPlaceVotes = $thirdplacevotes WHERE BotYID = $botyid";
				mysql_query($query, $connect);
			}
		}
		
		//Most Improved Band
		$bandid = $_POST[$division . '_improv'];
		$query = "SELECT * FROM BandsOfTheYearPublic WHERE BandID = $bandid";
		$result = mysql_query($query, $connect);
		if (mysql_num_rows($result) == 0) {
			$mostimproved = 1;
			$query = "INSERT INTO BandsOfTheYearPublic (BandID, IMODivision, MostImproved) VALUES ($bandid, $division, $mostimproved)";
			mysql_query($query, $connect);
		} else {
			$row = mysql_fetch_array($result);
			$botyid = $row['BotYID'];
			$mostimproved = $row['MostImproved'];
			$mostimproved = $mostimproved + 1;
			$query = "UPDATE BandsOfTheYearPublic SET MostImproved = $mostimproved WHERE BotYID = $botyid";
			mysql_query($query, $connect);
		}
	}

	$query = "UPDATE BandsOfTheYearVotersPublic SET Voted = 1 WHERE EmailHash = PASSWORD('".$_POST['user']."')";
	mysql_query($query, $connect);

	echo "<center><h2>Ballot Submitted! Thank You!</h2></center>";
} else {
	echo "<center><h2>You have already voted in this years Bands of the Year. Thanks!</h2></center>";
}
?>
<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
