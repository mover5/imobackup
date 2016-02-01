<?php
session_start();

require('./wp-blog-header.php');
require('./layout.php');
include('console/includes/functions.inc.php');
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="js/helperFunctions.js"></script>
      <script type="text/javascript">
	var map;
	var geocoder;
	var imageurls = new Array();
	var imagepreload = new Array();
	var preloaded = new Array();
	var imagecount = 0;
	var imageid = -1;
	var videourls = new Array();
	var title = new Array();
	var bandyear = new Array();
	var festid = new Array();
	var videocount = 0;
	var videoid = -1;
	
	function changeToVideo() {
		document.getElementById("imgtab").className = "imo_tab_down";
		document.getElementById("vidtab").className = "imo_tab_up";
		document.getElementById("prevlink").innerHTML = "<b>Prev Video</b>";
		document.getElementById("nextlink").innerHTML = "<b>Next Video</b>";
		document.getElementById("prevlink").href = "javascript:prevVideo()";
		document.getElementById("nextlink").href = "javascript:nextVideo()";
		document.getElementById("uploadsubmit").value = "Upload a Video";
		document.getElementById("uploadform").action = "upload_video.php";
		var mediaobj = document.getElementById("media");

		if (videocount == 0) {
			mediaobj.innerHTML = "<font size='+1'><center>No Videos Available</center></font>";
		} else {
			mediaobj.innerHTML = "<iframe id='video' name='video' width='353' height='315' src='"+videourls[videoid]+"' frameborder='0' allowfullscreen></iframe><br /><center><div id='title' name='title'>" + title[videoid] + "</div></center><br /><center><div id='fest' name='fest'>" + festid[videoid]+"</div></center>";
		}
		 

		
		displayVideoNum();
	}
	
	function changeToImage() {
		document.getElementById("imgtab").className = "imo_tab_up";
		document.getElementById("vidtab").className = "imo_tab_down";
		document.getElementById("prevlink").innerHTML = "<b>Prev Image</b>";
		document.getElementById("nextlink").innerHTML = "<b>Next Image</b>";
		document.getElementById("prevlink").href = "javascript:prevImage()";
		document.getElementById("nextlink").href = "javascript:nextImage()";
		document.getElementById("uploadsubmit").value = "Upload a Photo";
		document.getElementById("uploadform").action = "upload_image_web.php";
		
		var mediaobj = document.getElementById("media");
		if (imagecount == 0) {
			mediaobj.innerHTML = "<font size='+1'><center>No Images Available</center></font>";
		} else {
			mediaobj.innerHTML = "<img src='"+imageurls[imageid]+"' width=350 align=left name='bandimage' id='bandimage'>";
		}
		
		displayImageNum();
	}
	
	function addToImageArray(url) {	
		imageurls[imagecount] = url;
		preloaded[imagecount] = 0;
		imagecount++;
		
	}
	
	function addToMovieArray(url, showtitle, year, fest) {
		videourls[videocount] = url;
		title[videocount] = showtitle;
		bandyear[videocount] = year;
		festid[videocount] = fest;
		
		videocount++;
	}
	function setVideoID(id) {
		videoid = id;
	}
	
	function nextVideo() {
		if (videoid < (videocount-1)) {
			videoid++;
		} else {
			videoid = 0;
		}
		document.getElementById("title").innerHTML = title[videoid];
		document.getElementById("fest").innerHTML = festid[videoid];
		document.getElementById("video").src = videourls[videoid];
		displayVideoNum();
		
	}
	
	function prevVideo() {
		if (videoid > 0) {
			videoid--;
		} else {
			videoid = (videocount-1);
		}		
		document.getElementById("title").innerHTML = title[videoid];
		document.getElementById("fest").innerHTML = festid[videoid];
		document.getElementById("video").src = videourls[videoid];
		
		
		displayVideoNum();
		
	}

	function displayVideoNum() {
		var display;
		if (videocount == 0) {
			display = "0/0";
		} else {
			display = (videoid+1) + "/" + videocount
		}
		document.getElementById("imgnum").innerHTML = display;
	}
	
	function setImageID(id) {
		imageid = id;
		var next = id + 1;
		var prev = id - 1;
		if (next >= imagecount) next = 0;
		if (prev < 0) prev = (imagecount - 1)
		
		imagepreload[id] = new Image();
		imagepreload[id].src = imageurls[id];
		preloaded[id] = 1;
		
		if (preloaded[next] == 0) {
			imagepreload[next] = new Image();
			imagepreload[next].src = imageurls[next];
			preloaded[next] = 1;
		}
		
		if (preloaded[prev] == 0) {
			imagepreload[prev] = new Image();
			imagepreload[prev].src = imageurls[prev];
			preloaded[prev] = 1;
		}
		
	}
	
	function nextImage() {
		if (imageid < (imagecount-1)) {
			imageid++;
		} else {
			imageid = 0;
		}
		document.getElementById("bandimage").src = imageurls[imageid];
		displayImageNum();
		var next = imageid + 1;
		if (next >= imagecount) next = 0;
		if (preloaded[next] == 0) {
			imagepreload[next] = new Image();
			imagepreload[next].src = imageurls[next];
			preloaded[next] = 1;
		}
	}
	
	function prevImage() {
		if (imageid > 0) {
			imageid--;
		} else {
			imageid = (imagecount-1);
		}
		document.getElementById("bandimage").src = imageurls[imageid];
		displayImageNum();
		var prev = imageid - 1;
		if (prev < 0) prev = (imagecount - 1)
		if (preloaded[prev] == 0) {
			imagepreload[prev] = new Image();
			imagepreload[prev].src = imageurls[prev];
			preloaded[prev] = 1;
		}
	}

	function displayImageNum() {
		var display;
		if (imagecount == 0) {
			display = "0/0";
		} else {
			display = (imageid+1) + "/" + imagecount
		}
		document.getElementById("imgnum").innerHTML = display;
	}

    function initialize(latval, longval) {
	  
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(latval, longval);
		var myOptions = {
		  zoom: 9,
		  center: latlng,
		  mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  }
    
    function codeAddress(address) {
    if (geocoder) {
      geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          map.setCenter(results[0].geometry.location);
          var marker = new google.maps.Marker({
              map: map, 
              position: results[0].geometry.location
          });
        } else {
          alert("Geocode was not successful for the following reason: " + status);
        }
      });
    }
  }

    </script>
<?php 

	require("connection.inc.php");
	
	if (!isset($_REQUEST['BandID'])) {
		header("Location: bands.php");
	} else {
		$BandID = $_REQUEST['BandID'];
	}
	 $year = $_REQUEST["year"];
	 if ($year == '' || $year < 2000) {
		$year = date("Y");
	 }
     if (isset($_GET['highlight'])) {
	    $search = explode(" AND ", $_GET['highlight']);
     }
	 
	  $query = "SELECT * FROM Bands WHERE BandID=".$BandID;
	  $result=mysql_query($query);
	  $num=mysql_numrows($result);

	$i=0;
	$school = mysql_result($result,$i,"School");
	$band_name = mysql_result($result,$i,"BandName");
	$city_town = mysql_result($result,$i,"Town");
	$colors = mysql_result($result,$i,"Colors");
	$directors = mysql_result($result,$i,"Directors");
	$website 	= mysql_result($result,$i,"WebsiteURL");
	//$pic_url 	= mysql_result($result,$i,"PicURL");
	$notes = mysql_result($result,$i,"Notes");
	$address = addslashes(mysql_result($result,$i,"Address"));

	if (isset($_GET['highlight'])) {
		foreach ($search as $value) {
			$replace = "<FONT style=\"BACKGROUND-COLOR: #BBBBBB\">" . $value . "</FONT>";
			$school = str_ireplace($value, $replace, $school);
			$band_name = str_ireplace($value, $replace, $band_name);
			$city_town = str_ireplace($value, $replace, $city_town);
			$colors = str_ireplace($value, $replace, $colors);
			$directors = str_ireplace($value, $replace, $directors);
			$notes = str_ireplace($value, $replace, $notes);
			
		}
	}

		
//begin section (School info)
echo "<div><h2 style='display:inline;'>$school</h2>";
echo "<i>&nbsp;$band_name</i>&nbsp;&nbsp;$city_town, IL";
if (check_login_status() == true && (get_login_role() == "admin" || get_login_role() == "contributor")) {
    echo "<div style='float: right;'>";
    echo "<h3 style='display:inline;'>Admin Tasks: </h3>";
    echo "<a href='console/modify_band.php?BandID=$BandID'>Edit Band</a>, ";
    echo "<a href='console/modify_show.php?modsubmit=true&BandID=$BandID&Year=$year'>Edit Show</a>&nbsp;";
    echo "</div>";
}
echo "</div>";
echo "<hr>";
//end section (School info)

//begin section (Year info)
echo "<form action=\"bands_indiv.php?BandID=$BandID\" method=\"post\">";
echo "Select a year: <select name=\"year\" onChange=\"MM_jumpMenu('parent',this,0)\" class='form_input'>";

for ($i=date("Y"); $i>1999; $i--) {
	if ($i == $year) {
		echo "<option selected='selected' value=\"bands_indiv.php?BandID=$BandID&year=\">$i</option>";
	} else {
		echo "<option value=\"bands_indiv.php?BandID=$BandID&year=$i\">$i</option>";
	}
}
 
echo "</select>";
echo "</form>";

//Begin table for Scores - section 1
echo "<table cellpadding=0 id=tablescore border=0>";
$query = "SELECT * FROM BandShows WHERE BandID = $BandID AND Year = $year";
$result = mysql_query($query, $connect);
$num = mysql_num_rows($result);
if ($num > 0) {
	$row = mysql_fetch_array($result);
	$show = $row['Title'];
    $rep = $row['Repetoire'];
}
if ($show == NULL || $show == "") {
	$show = "<i>no information available</i>";
} else {
    $show = "&quot;$show&quot;";
}
$displaydate = mktime(0,0,0,5,1,$year);
$today = time();
if ($today < $displaydate) {
	$show = "<i>no information available</i>";
    $rep = "";
}

echo "<tr>";

 echo "<td width=400>"; //left side column (show + placing)
   echo "<div style='padding:5px 0px 2px 0px'><b>$year Show</b></div><div style='padding:0px 5px 0px 8px'>$show<br /><br /><i>$rep</i></div>";
   echo "<hr><div style='padding:0px 0px 2px 0px'><b>$year Awards &amp; Placements</b></div>";
   include ('awards.php');
 echo "</td> ";
 echo "<td width=350 rowspan=3>";
   include ('media_box.php');
   include ('school_info.php');
   include('showDivision.php');
 echo "</td>";
echo "</tr>";
//End table for Scores  
echo "</table>";

echo "<hr>";
//end section (Year info)
echo "<table cellpadding=0 id=tablescore border=0>";

//begin section  (media box)
echo "<tr>"; //row to hold media box and school info
 echo "<td align=center valign=middle width=350>"; 
  echo "<table border=0 width='450'>"; // table for school info
 
//missing /TR?
echo "</tr>";
echo "</table>"; //end table for media box

///td here?? 

//begin map
echo "<td>";   

//begin table for right side of section 2 (enrollment/division)
 echo "<table border=0>";

//end table for right side of section 2 (enrollment/division)
 echo "</table>";
 echo "</td>"; //ends TD for right side (col 2?) of section 2
//end map

echo "</tr>";// ends row for section 2
echo "</table> ";//ends table for section 2

$thisyear = date('Y');
$query = "SELECT * FROM `Schedules` JOIN Festivals ON Schedules.FestivalID = Festivals.FestivalID WHERE FestivalYear = $thisyear and BandID = $BandID AND (Type = 'Field' OR Type = 'Field2') GROUP BY Schedules.FestivalID ORDER BY Date ASC";
$result = mysql_query($query, $connect);
if (mysql_num_rows($result)) {
	echo "<div style='padding:0px 0px 2px 0px'><strong>Attending Festivals In $thisyear</strong></div>";
	while ($row = mysql_fetch_array($result)) {
			$name = $row['Name'];
			$date = $row['Date'];
			$festivalID = $row['FestivalID'];
			echo "<div style='padding:0px 8px'>".$date . " - <a href='festivals_detail.php?FestivalID=$festivalID'>$name</a></div>";
	}
	echo "<hr>";
}

echo "<div><b>Notes</b></div>";
echo "<div style='padding:2px 0px 0px 8px'>$notes</div>";

//begin map
echo "<hr>";   
   echo "<p>" . stripslashes($address) . "</p>";
   echo "<div id=\"map_canvas\" style=\"width: 425px; height: 450px\"></div>";
//end map

 

$latval = 0;
$longval = 0;
echo "<script type=\"text/javascript\">
	initialize($latval, $longval);
	codeAddress('$address')
	</script>";
?>
<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
