<?php
require('./wp-blog-header.php');
require('./layout.php');
if (!isset($_REQUEST['FestivalID'])) {
	header("Location: festivals.php");
} else {
	$FestivalID = $_REQUEST['FestivalID'];
}
require("connection.inc.php");
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->
<script type="text/javascript" src="js/helperFunctions.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
      <script type="text/javascript">
	var map;
	var geocoder;
	var directionDisplay;
	var directionsService = new google.maps.DirectionsService();
	var chicago = new google.maps.LatLng(41.850033, -87.6500523);
	var start;
	var end;
	var foundEnd = false;
	var foundStart = false;

    function initialize(latval, longval) {
	    directionsDisplay = new google.maps.DirectionsRenderer();

		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(latval, longval);
		var myOptions = {
		  zoom: 9,
		  center: latlng,
		  mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		directionsDisplay.setMap(map);

  }
    
    function codeAddress(address) {
    if (geocoder) {
      geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          map.setCenter(results[0].geometry.location);
          end = results[0].geometry.location;
          if (foundStart) {
			  calcRoute();
		  } else {
			  foundEnd = true;
		  }
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
  
  function putUserLocation() {
	  // Try W3C Geolocation (Preferred)
	  if(navigator.geolocation) {
		browserSupportFlag = true;
		navigator.geolocation.getCurrentPosition(function(position) {
		  initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
		  start = initialLocation;
		  if (foundEnd) {
			  calcRoute();
		  } else {
			  foundStart = true;
		  }
		  var marker = new google.maps.Marker({
              map: map, 
              position: initialLocation
          });
		}, function() {
		  handleNoGeolocation(browserSupportFlag);
		});
	  // Try Google Gears Geolocation
	  } else if (google.gears) {
		browserSupportFlag = true;
		var geo = google.gears.factory.create('beta.geolocation');
		geo.getCurrentPosition(function(position) {
		  initialLocation = new google.maps.LatLng(position.latitude,position.longitude);
		  start = initialLocation;
		  if (foundEnd) {
			  calcRoute();
		  } else {
			  foundStart = true;
		  }
		  var marker = new google.maps.Marker({
              map: map, 
              position: initialLocation
          });
		}, function() {
		  handleNoGeoLocation(browserSupportFlag);
		});
	  // Browser doesn't support Geolocation
	  } else {
		browserSupportFlag = false;
		handleNoGeolocation(browserSupportFlag);
	  }
	  
	  function handleNoGeolocation(errorFlag) {
		if (errorFlag == true) {
		  alert("Geolocation service failed.");
		  initialLocation = chicago;
		} else {
		  alert("Your browser doesn't support geolocation. We've placed you in Chicago.");
		  initialLocation = chicago;
		}
		start = initialLocation;
		if (foundEnd) {
		    calcRoute();
	    } else {
		    foundStart = true;
	    }
		var marker = new google.maps.Marker({
              map: map, 
              position: initialLocation
          });
	  }	  
  }
  
  function calcRoute() {
	var request = {
		origin:start, 
		destination:end,
		travelMode: google.maps.DirectionsTravelMode.DRIVING
	};
  directionsService.route(request, function(result, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(result);
    }
  });

  }

    </script>
<?php 

function getZip($address) {
	return substr($address, strlen($address)-5, 5);
}

if (isset($_GET['highlight'])) {
	    $search = explode(" AND ", $_GET['highlight']);
}

	$query = "SELECT Name, Date, FestivalYear, Location, Festivals.WebsiteURL, Contact, Details, Judges, Festivals.Address, IF(GrandChampionID <> 'NULL', School, GrandChampionBandName) AS GrandChampion, GrandChampionID, LastUpdated FROM Festivals LEFT JOIN Bands ON Festivals.GrandChampionID = Bands.BandID WHERE FestivalID = '".$FestivalID."'";

	$Result=mysql_query($query);
	
	
	
	while($Row = mysql_fetch_array($Result))
  	{
		$name = $Row['Name'];
		$date = $Row['Date'];
		$showYear = $Row['FestivalYear'];
		$location = $Row['Location'];
		$website = $Row['WebsiteURL'];
		$contact = $Row['Contact'];
		$comments = $Row['Details'];
		$judges = $Row['Judges'];
		$address = $Row['Address'];
		$gcid = $Row['GrandChampionID'];
		$gcname = $Row['GrandChampion'];
		$lastupdated = $Row['LastUpdated'];
		$phpDate = strtotime( $date );		
	}
		echo "<p><h2>".$name.": ".$location." - ".date("F d, Y", $phpDate)."</h2><p>";
		
		echo "<table border=0><tr><td valign='top'>";

		$yearSelector="";		
		$dateArray = array();
		$query = "SELECT FestivalID,FestivalYear FROM Festivals WHERE Name = '$name' ORDER BY FestivalYear DESC";
		$result = mysql_query($query, $connect);
		$num = mysql_num_rows($result);
		if ($num == 0) {
		//	echo "No Previous Festivals";
		} 
		else {
			while ($row = mysql_fetch_array($result)) {
				$id = $row['FestivalID'];
				$year = $row['FestivalYear'];
				
				if ($year <> $showYear) {
					$string = "<a href='festivals_detail.php?FestivalID=$id&archive=0'>$year</a>";
					array_push($dateArray, $string);
					$yearSelector = $yearSelector . "<option value='festivals_detail.php?FestivalID=$id&archive=0' >$year</option>";				
				}
				else
					$yearSelector = $yearSelector . "<option value='festivals_detail.php?FestivalID=$id&archive=0'  selected>$year</option>";				
			}
			echo "<form style='margin-bottom:0;' name='form1'>";
			echo "Select a year: ";
				echo "<select name='switch_year' onChange=\"MM_jumpMenu('parent',this,0)\" class='form_input'>";
						echo "$yearSelector";
				echo "</select></form>";
		}
		echo "</td></tr></table><p></p>";
	
		include('./scores_indiv.inc2.php');
		echo "<table border=0><tr><td valign='top' width='315'>";
		echo "<h2>Contact Information:</h2>";
		echo $contact."<br>";
		echo "<a href=\"".$website."\" target='_blank'>Festival Website</a><p>";
				
		$zipcode = getZip($address);
		//if (($phpDate+86400) >= strtotime("now") && strtotime("now") >= ($phpDate-604800)) {
			echo "<h2>Weather:</h2>";
			?>
			<div id="wx_module_3967">
				<a href="http://www.weather.com/weather/local/<?php echo $zipcode; ?>">Weather Forecast for <?php echo $zipcode; ?></a>
			</div>
			<?php
		//}
		
		echo "</td><td valign='top'>";
		echo "<h2>Details:</h2>";
		echo $comments;
		echo "<p>";
		echo "<h2>Blank Score Sheet</h2>";
		echo "Want to try to score the bands for this festival yourself?<br />";
		echo "<a href='generatescoresheet.php?FestivalID=$FestivalID'>Click here to download a blank score sheet!</a>";

		echo "</td></tr>";
		if ($judges <> "") {
			echo "<tr><td colspan=2>";
			echo "<br /><h2>Judging Panel</h2>";
			echo $judges;
		
			echo "</td></tr>";
		}
		echo "</table><p></p>";

		echo "<h2>Performance Schedule:</h2>";
		echo "<a href='festivals_schedule.php?FestivalID=$FestivalID'><h3>Printer Friendly Schedule</h3></a>";
		include("display_schedule.php");
		
		//Display Map and Address of Festival
		if ($address <> "") {
			echo "<h2>Festival Address - $address</h2>";
			echo "<div id=\"map_canvas\" style=\"width: 630px;height: 400px\"></div><br /><a href='festival_directions.php?FestivalID=$FestivalID'>Get Driving Directions</a>";
			$latval = 0;
			$longval = 0;
			echo "<script type=\"text/javascript\">
				initialize($latval, $longval);
				codeAddress('$address');
				//putUserLocation();
				</script>";
			echo "<p></p>";
		}
		$lastupdated = date("m-d-Y", strtotime($lastupdated));
		if ($lastupdated <> "01-01-1970") {
			echo "<strong>Last Updated On: $lastupdated</strong>";
		}
	?>	
<script type="text/javascript">

   /* Locations can be edited manually by updating 'wx_locID' below.  Please also update */
   /* the location name and link in the above div (wx_module) to reflect any changes made. */
   var wx_locID = '<?php echo $zipcode; ?>';

   /* If you are editing locations manually and are adding multiple modules to one page, each */
   /* module must have a unique div id.  Please append a unique # to the div above, as well */
   /* as the one referenced just below.  If you use the builder to create individual modules  */
   /* you will not need to edit these parameters. */
   var wx_targetDiv = 'wx_module_3967';

   /* Please do not change the configuration value [wx_config] manually - your module */
   /* will no longer function if you do.  If at any time you wish to modify this */
   /* configuration please use the graphical configuration tool found at */
   /* https://registration.weather.com/ursa/wow/step2 */
   var wx_config='SZ=180x150*WX=FHW*LNK=SSNL*UNT=F*BGI=music*MAP=null|null*DN=ilmarching.com*TIER=0*PID=1210583595*MD5=fd25f3279546ef478acb23af58bf8854';

   document.write('<scr'+'ipt src="'+document.location.protocol+'//wow.weather.com/weather/wow/module/'+wx_locID+'?config='+wx_config+'&proto='+document.location.protocol+'&target='+wx_targetDiv+'"></scr'+'ipt>');  
</script>		

<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
