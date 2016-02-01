<?php
include('../connection.inc.php'); 
if (isset($_REQUEST['page'])) {
	$page = $_REQUEST['page'];
	if ($page == "FESTIVAL") {
		echo "<table>
		<tr><td><b>Your Name:</b></td><td><input type='text' name='name' size='50'/></td></tr>
		<tr><td><b>Your Email:</b></td><td><input type='text' name='email' size='50'/></td></tr>
		<tr><td><b>Festival Name:</b></td><td><input type='text' name='festival' size='50'/></td></tr>
		<tr><td><b>Festival Date:</b></td><td><input type='text' name='date' size='50'/></td></tr>
		<tr><td><b>Festival Contact:</b></td><td><input type='text' name='contact' size='50'/></td></tr>
		<tr><td><b>Festival Website:</b></td><td><input type='text' name='website' size='50'/></td></tr>
		<tr><td valign='top'><b>Festival Description:</b></td><td><textarea name='details' rows='15' cols='40'></textarea></td></tr>
		<tr><td valign='top'><b>Parade Schedule:</b></td>
		<td><textarea name='parade' rows='15' cols='40'></textarea></td></tr>
		<tr><td valign='top'><b>Field Schedule:</b></td><td><textarea name='field' rows='15' cols='40'></textarea></td></tr>";
	} else if ($page == "BAND") {
		echo "<table>
		<tr><td><b>Your Name:</b></td><td><input type='text' name='name' size='50'/></td></tr>
		<tr><td><b>Your Email:</b></td><td><input type='text' name='email' size='50'/></td></tr>
		<tr><td><b>Band Name:</b></td><td><select name='band' id='band'>";
		$query = "SELECT * FROM Bands ORDER BY School";
		$result = mysql_query($query, $connect);
		while ($row = mysql_fetch_array($result)) {
			$name = $row['School'];
			echo "<option value='$name'>$name</option>";
		}
		echo "</select></td></tr>
		<tr><td valign='top'><b>Band's Show:</b><br />Please include the YEAR and the SHOW TITLE</td><td><textarea name='show' rows='15' cols='40'></textarea></td></tr>
		<tr><td valign='top'><b>Band's Awards:</b><br />Please include the exact award, the <br />festival it was won at, and the year</td>
		<td><textarea name='awards' rows='15' cols='40'></textarea></td></tr>
		<tr><td valign='top'><b>Interesting Information about your band:</b></td><td><textarea name='info' rows='15' cols='40'></textarea></td></tr>";
	} else if ($page == "SCORES") {
		echo "<table>
		<tr><td><b>Your Name:</b></td><td><input type='text' name='name' size='50'/></td></tr>
		<tr><td><b>Your Email:</b></td><td><input type='text' name='email' size='50'/></td></tr>
		<tr><td><b>Festival Name:</b></td><td>
		<select name='festival'>";
		$thisyear = date('Y');
		$query = "SELECT * FROM Festivals WHERE FestivalYear <= $thisyear ORDER BY FestivalYear DESC, Date ASC, Name ASC";
		$result = mysql_query($query, $connect);
		while ($row = mysql_fetch_array($result)) {
			$name = $row['Name'];
			$festivalYear = $row['FestivalYear'];
			echo "<option value='$festivalYear - $name'>$festivalYear - $name</option>";
		}
		echo "</select>
		</td></tr>
		<tr><td><b>Link to Recap Sheet</b><br />If Available</td><td><input type='text' name='recap' size='50'/></td></tr>
		<tr><td valign='top'><b>Parade Scores:</b></td><td><textarea name='parade' rows='15' cols='40'></textarea></td></tr>
		<tr><td valign='top'><b>Field Scores:</b></td>
		<td><textarea name='field' rows='15' cols='40'></textarea></td></tr>";
	} 
	echo "<tr><td align='left' colspan='2'>Please type the security phrase: <b>NOSPAM</b> <input type='text' name='spam' id='spam' /><input type='submit' name='submit' class='button_submit' value='Submit' /></td></tr></table>";
	echo "<input type='hidden' name='page' id='page' value='$page' />";
} else {
	echo "<h2>No Page Selected</h2>";
}


?>
