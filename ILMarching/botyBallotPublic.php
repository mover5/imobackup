<?php
require('./wp-blog-header.php');
require('./layout.php');
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->
<script type="text/javascript">
function validateBallot(div1, div2, div3, div4)
{
	var fieldname;
	var otherfieldname;
	var i;
	var j;
	for (i=1; i <= div1; i++) {
			fieldname = "1_" + i;
			if (document.getElementById(fieldname).selectedIndex == 0) {
				alert("You did not make a selection for Division 1, Place " + i);
				return;
			}
			for (j = i+1; j <= div1; j++) {
					otherfieldname = "1_" + j;
					if (document.getElementById(fieldname).value == document.getElementById(otherfieldname).value) {
							alert("The band in Place " + i + " for Division 1 is the same band as Place " + j + " for Division 1");
							return;
					}
			}
	}
	for (i=1; i <= div2; i++) {
			fieldname = "2_" + i;
			if (document.getElementById(fieldname).selectedIndex == 0) {
				alert("You did not make a selection for Division 2, Place " + i);
				return;
			}
			for (j = i+1; j <= div2; j++) {
					otherfieldname = "2_" + j;
					if (document.getElementById(fieldname).value == document.getElementById(otherfieldname).value) {
							alert("The band in Place " + i + " for Division 2 is the same band as Place " + j + " for Division 2");
							return;
					}
			}
	}
	for (i=1; i <= div3; i++) {
			fieldname = "3_" + i;
			if (document.getElementById(fieldname).selectedIndex == 0) {
				alert("You did not make a selection for Division 3, Place " + i);
				return;
			}
			for (j = i+1; j <= div3; j++) {
					otherfieldname = "3_" + j;
					if (document.getElementById(fieldname).value == document.getElementById(otherfieldname).value) {
							alert("The band in Place " + i + " for Division 3 is the same band as Place " + j + " for Division 3");
							return;
					}
			}
	}
	for (i=1; i <= div4; i++) {
			fieldname = "4_" + i;
			if (document.getElementById(fieldname).selectedIndex == 0) {
				alert("You did not make a selection for Division 4, Place " + i);
				return;
			}
			for (j = i+1; j <= div4; j++) {
					otherfieldname = "4_" + j;
					if (document.getElementById(fieldname).value == document.getElementById(otherfieldname).value) {
							alert("The band in Place " + i + " for Division 4 is the same band as Place " + j + " for Division 4");
							return;
					}
			}
	}
	for (i=1; i <= 4; i++) {
		fieldname = i + "_improv";
		if (document.getElementById(fieldname).selectedIndex == 0) {
				alert("You did not make a selection for 'Most Improved Band' in Division " + i);
				return;
		}
	}
	var r=confirm("This is your last chance to change your ballot. Are you sure you want to submit your ballot?");
	if (r==true) {
		document.ballot.submit();
	}
	
}
</script>
<?php
require("connection.inc.php");

$query = "SELECT * FROM BandsOfTheYearSettings LIMIT 1";
$result = mysql_query($query, $connect);
$row = mysql_fetch_array($result);
$startDate = $row['StartDate'];
$endDate = $row['EndDate'];
$div1 = $row['Div1Num'];
$div2 = $row['Div2Num'];
$div3 = $row['Div3Num'];
$div4 = $row['Div4Num'];

if (strtotime($startDate) <= strtotime("now") && strtotime($endDate)+86400 >= strtotime("now")) {
	if (isset($_POST['login'])) {
		$query = "SELECT * FROM BandsOfTheYearVotersPublic WHERE EmailHash = PASSWORD('".$_POST['username']."')";
		$result = mysql_query($query, $connect);
		$row = mysql_fetch_array($result);
		if (mysql_num_rows($result) == 0) {
			echo "<center><h2>Unreckognized Username. Have you registered?</h2></center>";
		} else if ($row['Voted'] == 1) {
			echo "<center><h2>You have already voted in this years Bands of the Year. Thanks!</h2></center>";
		} else if ($_POST['password'] == substr(sha1($_POST['username']), 0, 6) && $row['Voted'] == 0) {
			$divoptions[1] = "";
			$divoptions[2] = "";
			$divoptions[3] = "";
			$divoptions[4] = "";
			
			$query = "SELECT * FROM Divisions";
			$result = mysql_query($query, $connect);
			$divlimits = array();
			while ($row = mysql_fetch_array($result)) {
				$divlimits[$row['Division']] = $row['MaxEnrollment'];
			}
			$year = date('Y');
			$query = "SELECT 1 AS Division, Bands.BandID, School FROM Bands, Enrollment WHERE Bands.BandID = Enrollment.BandID AND Year = $year AND ((IHSAEnrollment > ".$divlimits[2]." AND IHSAEnrollment <> 'N/A') OR (DivisionOverride = 1))
						UNION
						SELECT 2 AS Division, Bands.BandID, School FROM Bands, Enrollment WHERE Bands.BandID = Enrollment.BandID AND Year = $year AND IHSAEnrollment > ".$divlimits[3]." AND IHSAEnrollment <= ".$divlimits[2]." AND IHSAEnrollment <> 'N/A' AND DivisionOverride <> 1
						UNION
						SELECT 3 AS Division, Bands.BandID, School FROM Bands, Enrollment WHERE Bands.BandID = Enrollment.BandID AND Year = $year AND IHSAEnrollment > ".$divlimits[4]." AND IHSAEnrollment <= ".$divlimits[3]." AND IHSAEnrollment <> 'N/A' AND DivisionOverride <> 1
						UNION
						SELECT 4 AS Division, Bands.BandID, School FROM Bands, Enrollment WHERE Bands.BandID = Enrollment.BandID AND Year = $year AND IHSAEnrollment <= ".$divlimits[4]." AND IHSAEnrollment <> 'N/A' AND DivisionOverride <> 1";
						
			$result = mysql_query($query, $connect);
			while ($row = mysql_fetch_array($result)) {
				$divoptions[$row['Division']] .= "<option value='".$row['BandID']."'>".$row['School']."</option>";
			}
			echo "<form action='botySubmitPublic.php' method='POST' name='ballot'>";
			echo "<input type='hidden' name='user' id='user' value='".$_POST['username']."' />";
			echo "<center><h1>Bands of the Year $year</h1><table border=1>";
			echo "<tr><th><a target='_blank' href='botyDivisionList.php?div=1'>Division I</a></th><th><a target='_blank' href='botyDivisionList.php?div=2'>Division II</a></th></tr>";
			echo "<tr><td valign='top'>";
			echo "<table>";
			for ($i = 0; $i < $div1; $i++) {
				echo "<tr><td align='right'><b>".($i+1)."</b>:</td><td> <select id='1_".($i+1)."' name='1_".($i+1)."'><option value='0'></option>" . $divoptions[1] . "</select></td></tr>";
			}
			if ($div1 < $div2) {
				for ($i = 0; $i < ($div2 - $div1); $i++) {
					echo "<tr><td><br /></td></tr>";
				}
			}
			echo "<tr><td colspan='2'><center><hr><br /></center></td></tr>";
			echo "<tr><td colspan='2'><center><b>Most Improved Band</b></center></td></tr>";
			echo "<tr><td colspan='2'><center><select id='1_improv' name='1_improv'><option value='0'></option>" . $divoptions[1] . "</select></center></td></tr>";
			echo "</table></td><td valign='top'>";
			echo "<table>";
			for ($i = 0; $i < $div2; $i++) {
				echo "<tr><td align='right'><b>".($i+1)."</b>:</td><td> <select id='2_".($i+1)."' name='2_".($i+1)."'><option value='0'></option>" . $divoptions[2] . "</select></td></tr>";
			}
			if ($div2 < $div1) {
				for ($i = 0; $i < ($div1 - $div2); $i++) {
					echo "<tr><td><br /></td></tr>";
				}
			}
			echo "<tr><td colspan='2'><center><hr><br /></center></td></tr>";
			echo "<tr><td colspan='2'><center><b>Most Improved Band</b></center></td></tr>";
			echo "<tr><td colspan='2'><center><select id='2_improv' name='2_improv'><option value='0'></option>" . $divoptions[2] . "</select></center></td></tr>";
			echo "</table></td></tr>";
			echo "<tr><th><a target='_blank' href='botyDivisionList.php?div=3'>Division III</a></th><th><a target='_blank' href='botyDivisionList.php?div=4'>Division IV</a></th></tr>";
			echo "<tr><td valign='top'>";
			echo "<table>";
			for ($i = 0; $i < $div3; $i++) {
				echo "<tr><td align='right'><b>".($i+1)."</b>:</td><td> <select id='3_".($i+1)."' name='3_".($i+1)."'><option value='0'></option>" . $divoptions[3] . "</select></td></tr>";
			}
			if ($div3 < $div4) {
				for ($i = 0; $i < ($div4 - $div3); $i++) {
					echo "<tr><td><br /></td></tr>";
				}
			}
			echo "<tr><td colspan='2'><center><hr><br /></center></td></tr>";
			echo "<tr><td colspan='2'><center><b>Most Improved Band</b></center></td></tr>";
			echo "<tr><td colspan='2'><center><select id='3_improv' name='3_improv'><option value='0'></option>" . $divoptions[3] . "</select></center></td></tr>";
			echo "</table></td><td valign='top'>";
			echo "<table>";
			for ($i = 0; $i < $div4; $i++) {
				echo "<tr><td align='right'><b>".($i+1)."</b>:</td><td> <select id='4_".($i+1)."'  name='4_".($i+1)."'><option value='0'></option>" . $divoptions[4] . "</select></td></tr>";
			}
			if ($div4 < $div3) {
				for ($i = 0; $i < ($div3 - $div4); $i++) {
					echo "<tr><td><br /></td></tr>";
				}
			}
			echo "<tr><td colspan='2'><center><hr><br /></center></td></tr>";
			echo "<tr><td colspan='2'><center><b>Most Improved Band</b></center></td></tr>";
			echo "<tr><td colspan='2'><center><select id='4_improv' name='4_improv'><option value='0'></option>" . $divoptions[4] . "</select></center></td></tr>";
			echo "</table></td></tr>";
			echo "</table><br /><input type='button' name='submitbutton' id='submitbutton' value='Submit Ballot' onClick='validateBallot($div1, $div2, $div3, $div4);' /></center></form>";
		} else {
			echo "<center><h2>Incorrect Username or Password</h2><a href='botyBallotPublic.php'>Try Again</a></center>";
		}
	} else {
		echo "<center><form action='botyBallotPublic.php' method='POST'>";
		echo "<h2>BOTY Public Login</h2>";
		echo "Username: <input type='text' name='username' id='username' /><br />";
		echo "Password: <input type='password' name='password' id='password' /><br />";
		echo "<input type='submit' name='login' id='login' value='Log In' />";
		echo "</form></center>";
	}

	
} else if (strtotime($startDate) > strtotime("now")) {	
	echo "<h2>Bands of the Year for this year has not opened yet.</h2><center>Voting opens <br /><b>$startDate</b></center>";
} else {
	echo "<h2>Bands of the Year voting for this year is closed</h2>";
}




?>
<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
