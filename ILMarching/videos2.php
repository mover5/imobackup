<?php 
require('./wp-blog-header.php');
require('./layout.php');
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->
<script type='text/javascript'>
function showDrilldown(str) {
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		drillxmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		drillxmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	drillxmlhttp.onreadystatechange=function()
	{
		if (drillxmlhttp.readyState==4 && drillxmlhttp.status==200)
		{
			document.getElementById("drilldown").innerHTML=drillxmlhttp.responseText;
			document.getElementById("ddname").innerHTML=str;
		}
	}
	drillxmlhttp.open("GET", "ajax/drilldown.php?r="+str, true);
	drillxmlhttp.send();
}

function selectVideo(str) {
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		videoxmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		videoxmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	videoxmlhttp.onreadystatechange=function()
	{
		if (videoxmlhttp.readyState==4 && videoxmlhttp.status==200)
		{
			document.getElementById("selectvideo").innerHTML=videoxmlhttp.responseText;
		}
	}
	videoxmlhttp.open("GET", "ajax/videopage.php?"+str, true);
	videoxmlhttp.send();
}

function watchVideo(url, title, band, bandid, bandyear, festname, festid) {
	document.getElementById("videoframe").src = url;
	document.getElementById("title").innerHTML = title;
	document.getElementById("bandlink").innerHTML = band;
	document.getElementById("bandlink").href = "bands_indiv.php?BandID="+bandid+"&year="+bandyear;
	var festinfo;
	if (festname == "Missing Fest") {
		festinfo = bandyear;
	} else {
		festinfo = "<a href='festivals_detail.php?FestivalID="+festid+"'>"+bandyear+" - "+festname+"</a>";
	}
	document.getElementById("festinfo").innerHTML = festinfo;
}
</script>

<center><p><h2>Illinois Marching Online Videos - BETA</h2></p></center>
	<center>
<table border=1 cellpadding=10>
	<tr><td><div width=550 name='video' id='video'>
<?php
require("connection.inc.php");

// Get number of records
$query = "SELECT * FROM Videos";
$result = mysql_query($query, $connect);
$num = mysql_num_rows($result);
$rand = rand(0, $num-1);

$query = "SELECT v.VideoURL, s.Title, v.BandYear, f.Name, f.FestivalID, b.School, b.BandID FROM Videos v LEFT JOIN BandShows s ON (v.BandYear = s.Year AND v.BandID = s.BandID) LEFT JOIN Festivals f ON v.FestivalID = f.FestivalID LEFT JOIN Bands b ON s.BandID = b.BandID LIMIT $rand, 1";
$result = mysql_query($query, $connect);
$row = mysql_fetch_array($result);

$url = "http://youtube.com/embed/" . substr($row['VideoURL'], stripos($row['VideoURL'], "?v=") + 3);
$title = $row['Title'];
if ($title == "") $title = "Missing Show";
$bandyear = $row['BandYear'];
if ($row['Name'] == NULL) {
	$fest = $row['BandYear'];
} else {
	$festid = $row['FestivalID'];
	$fest = "<a href='festivals_detail.php?FestivalID=$festid'>".$row['BandYear'] ." - " . $row['Name']."</a>";
}
$school = $row['School'];
$bandid = $row['BandID'];

echo "<iframe id='videoframe' name='videoframe' width='550' height='400' src='$url' frameborder='0' allowfullscreen></iframe>";
echo "<center><br /><b id='title' name='title'>$title</b><br /><a id='bandlink' name='bandlink' href='bands_indiv.php?BandID=$bandid&year=$bandyear'>$school</a><br /><div id='festinfo'>$fest</div></center>";


?>
</div></td>

<!-- Drill Down -->
<td width=300>
Search By: <button class="button_submit" type='button' onclick='selectVideo("r=ALL&s=0")'>All</button> 
<button type='button' class="button_submit" onclick='showDrilldown("FESTIVAL")'>Festival</button>
 <button type='button' class="button_submit" onclick='showDrilldown("BAND")'>Band</button>
<button type='button' class="button_submit" onclick='showDrilldown("YEAR")'>Year</button><br />
<center><h3 id='ddname' name='ddname'>FESTIVAL</h3></center><div id='drilldown' name='drilldown' style='height:375;overflow:scroll'>

</div>
</td></tr>
<tr><td colspan=2 id='selectvideo' name='selectvideo'>

</td></tr>

</table></center>
<script type='text/javascript'>
showDrilldown('FESTIVAL');
selectVideo('r=ALL&s=0');
</script>

<!--****End Page Content***************-->
<?php imo_bottom(); ?>
