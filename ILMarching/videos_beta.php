<?php 
require('./wp-blog-header.php');
require('./layout.php');
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->
<script type='text/javascript'>
function searchVideo() {
    var search = document.getElementById("search").value;
    var str = "r=SEARCH&p=" + search
    selectVideo(str);
}

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

function changeToFest() {
    document.getElementById("festtab").className = "imo_tab_up";
    document.getElementById("alltab").className = "imo_tab_down";
    document.getElementById("bandtab").className = "imo_tab_down";
    document.getElementById("yeartab").className = "imo_tab_down";
}
function changeToBand() {
    document.getElementById("festtab").className = "imo_tab_down";
    document.getElementById("alltab").className = "imo_tab_down";
    document.getElementById("bandtab").className = "imo_tab_up";
    document.getElementById("yeartab").className = "imo_tab_down";
}
function changeToYear() {
    document.getElementById("festtab").className = "imo_tab_down";
    document.getElementById("alltab").className = "imo_tab_down";
    document.getElementById("bandtab").className = "imo_tab_down";
    document.getElementById("yeartab").className = "imo_tab_up";
}
function changeToAll() {
    document.getElementById("festtab").className = "imo_tab_down";
    document.getElementById("alltab").className = "imo_tab_up";
    document.getElementById("bandtab").className = "imo_tab_down";
    document.getElementById("yeartab").className = "imo_tab_down";
}
</script>

<center><p><h2>Illinois Marching Online Videos - BETA</h2></p></center>
	<center>


<!-- Menu -->
<table border=0>
<tr style='margin-bottom:0px;'>
<td id='alltab' name='alltab' class='imo_tab_up' valign='middle' align='center' onclick='selectVideo("r=ALL");showDrilldown("SEARCH");changeToAll()'>Search</td>
<td id='festtab' name='festtab' class='imo_tab_down' valign='middle' align='center' onclick='showDrilldown("FESTIVAL");changeToFest();'>Festival</td>
<td id='bandtab' name='bandtab' class='imo_tab_down' valign='middle' align='center' onclick='showDrilldown("BAND");changeToBand();'>Band</td>
<td id='yeartab' name='yeartab' class='imo_tab_down' valign='middle' align='center' onclick='showDrilldown("YEAR");changeToYear();'>Year</td>
</tr>
</table>
<table border=1 cellpadding=10>
	<tr><td><div width=550 name='video' id='video'>
<?php
require("connection.inc.php");

if (isset($_GET['video'])) {
    $videoid = $_GET['video'];
    $youtubeurl = "http://www.youtube.com/watch?v=" . $videoid;
    $query = "SELECT BandShows.Title, Videos.BandYear, Festivals.Name, Festivals.FestivalID, Bands.School, Bands.BandID
     FROM Videos LEFT JOIN Bands ON Videos.BandID = Bands.BandID 
     LEFT JOIN Festivals ON Videos.FestivalID = Festivals.FestivalID 
     LEFT JOIN BandShows ON (Videos.BandID = BandShows.BandID AND Videos.BandYear = BandShows.Year) 
     WHERE VideoURL = '$youtubeurl'";
    $result = mysql_query($query, $connect);
    $row = mysql_fetch_array($result);
    $url = "http://youtube.com/embed/" . substr($youtubeurl, stripos($youtubeurl, "?v=") + 3);
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
    
} else {

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
}
echo "<iframe id='videoframe' name='videoframe' width='550' height='400' src='$url' frameborder='0' allowfullscreen></iframe>";
echo "<center><br /><b id='title' name='title'>$title</b><br /><a id='bandlink' name='bandlink' href='bands_indiv.php?BandID=$bandid&year=$bandyear'>$school</a><br /><div id='festinfo'>$fest</div></center>";


?>
</div></td>

<!-- Drill Down -->
<td width=300>
<center><h3 id='ddname' name='ddname'>SEARCH</h3></center><div id='drilldown' name='drilldown' style='height:425;overflow:scroll'>

</div>
</td></tr>
<tr><td colspan=2 id='selectvideo' name='selectvideo'>

</td></tr>

</table></center>
<script type='text/javascript'>
showDrilldown('SEARCH');
selectVideo('r=ALL&s=0');
</script>

<!--****End Page Content***************-->
<?php imo_bottom(); ?>
