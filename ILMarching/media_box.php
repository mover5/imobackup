<?php
 // begin section  (media box)
//this table is inside the TD width=350 - for Media box
echo "<table border=0 width='368'>";

// Picture Code Here

$query = "SELECT ImageURL FROM BandImages WHERE BandID = $BandID AND Approved = 1";
$result = mysql_query($query, $connect);
if (mysql_num_rows($result) <= 0) {
	$pic = "<font size='+1'><center>No Images Available</center></font>";
	$counter = "0/0";
} else {
	$urlarray = array();
	$count = 0;
	while ($row = mysql_fetch_array($result)) {
		$urlarray[$count] = $row['ImageURL'];
		$count++;
		echo "<script type=\"text/javascript\">addToImageArray(\"".$row['ImageURL']."\");</script>";
	}
	$imageid = rand(0, ($count-1));
	
	echo "<script type=\"text/javascript\">setImageID($imageid);</script>";
	$url = $urlarray[$imageid];
	$pic = "<img src='$url' width=350 align=left name='bandimage' id='bandimage'>";
	$counter = ($imageid+1) . "/" . $count;
}
 
// Load Videos

$query = "SELECT v.VideoURL, s.Title, v.BandYear, f.Name FROM Videos v LEFT JOIN BandShows s ON (v.BandYear = s.Year AND v.BandID = s.BandID) LEFT JOIN Festivals f ON v.FestivalID = f.FestivalID WHERE v.BandID = $BandID AND v.Approved = 1";
$result = mysql_query($query, $connect);
$urlarray = array();
$titlearray = array();
$yeararray = array();
$festarray = array();
	$videocount = 0;
	while ($row = mysql_fetch_array($result)) {
		$url = "http://youtube.com/embed/" . substr($row['VideoURL'], stripos($row['VideoURL'], "?v=") + 3);
		$title = $row['Title'];
		$bandyear = $row['BandYear'];
		if ($row['Name'] == NULL) {
			$festid = $row['BandYear'];
		} else {
			$festid = $row['BandYear'] ." - " . $row['Name'];
		}
		
		$urlarray[$videocount] = $url;
		$titlearray[$videocount] = $bandid;
		$yeararray[$videocount] = $bandyear;
		$festarray[$videocount] = $festid;
		$videocount++;
		echo "<script type=\"text/javascript\">addToMovieArray(\"".$url."\", \"".$title."\", \"".$bandyear."\", \"".$festid."\");</script>";
	}
	
	$videoid = rand(0, ($videocount-1));
	
	echo "<script type=\"text/javascript\">setVideoID($videoid);</script>";

echo "<table border=0 width='368'>";
echo "<tr style='margin-bottom:0px;'><td id='imgtab' name='imgtab' onclick='changeToImage()' class='imo_tab_up' valign='middle' align='center'><b>Images</b></td>
<td width='113' valign='middle' align='center'>Media</td>
<td id='vidtab' name='vidtab' onclick='changeToVideo()' class='imo_tab_down' valign='middle' align='center'><b>Videos</b></td></tr>";
echo "<tr style='margin-top:0px;'><td width='350' id='media' colspan='3'>$pic</td></tr>";


	$prevlink = "<a href=\"javascript:prevImage()\" id='prevlink' name='prevlink'><b>Prev Image</b></a>";
	$nextlink = "<a href=\"javascript:nextImage()\" id='nextlink' name='nextlink'><b>Next Image</b></a>";

	
	echo "<tr align=center><td align=center width=121>$prevlink</td><td align=center width=113><div id='imgnum' name='imgnum'>$counter</div></td><td align=center>$nextlink</td></tr>";

	echo "<form name='uploadform' id='uploadform' action='upload_image_web.php' method='POST'>";
echo "<input type='hidden' name='bandid' id='bandid' value='$BandID' />";
echo "<tr><td colspan='3' align='center'><input name='uploadsubmit' id='uploadsubmit' type='submit' class='button_submit' value='Upload a Photo' /></td></tr>";
echo "</form>";

echo "</table>"; //end table for media box
//missing /TR????
//echo "</tr>";

//end section  (media box)

?>
