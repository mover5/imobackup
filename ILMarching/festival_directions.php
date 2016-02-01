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
		directionsDisplay.setPanel(document.getElementById("directionsPanel"));

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

	  }	  
  }
  
  function calcRoute() {
	var request = {
		origin:start, 
		destination:end,
		travelMode:google.maps.DirectionsTravelMode.DRIVING
	};
  directionsService.route(request, function(result, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(result);
    }
  });

  }

    </script>
<?php 

if (isset($_GET['highlight'])) {
	    $search = explode(" AND ", $_GET['highlight']);
}

	$query = "SELECT * FROM Festivals WHERE FestivalID = '".$FestivalID."'";

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
		$address = $Row['Address'];
		$gcid = $Row['GrandChampionID'];
		$gcname = $Row['GrandChampionBandName'];
		$phpDate = strtotime( $date );		
	}

         if (isset($_GET['highlight'])) {
            foreach ($search as $value) {
               $replace = "<FONT style=\"BACKGROUND-COLOR: #BBBBBB\">" . $value . "</FONT>";
               $name = str_ireplace($value, $replace, $name);
               $phpDate = str_ireplace($value, $replace, $phpDate);
               $showYear = str_ireplace($value, $replace, $showYear);
               $location = str_ireplace($value, $replace, $location);
               $contact = str_ireplace($value, $replace, $contact);
               $comments = str_ireplace($value, $replace, $comments);
            }
         }
	
		echo "<p><h3><a href='festivals_detail.php?FestivalID=$FestivalID'>".$showYear." ".$name.": ".$location."</a></h3><p>";
		echo "<h2>".date("F d, Y", $phpDate)."</h2><p>";
		
		
		
		//Display Map and Address of Festival
		if ($address <> "") {
			echo "<h2>Festival Address - $address</h2>";
			echo "<div id=\"map_canvas\" style=\"width: 630px;height: 400px\"></div><br /><div id='directionsPanel' style='width: 630px; height:1000px;'></div><p></p>";
			$latval = 0;
			$longval = 0;
			echo "<script type=\"text/javascript\">
				initialize($latval, $longval);
				codeAddress('$address');
				putUserLocation();
				</script>";
			echo "<p></p>";
		}
		
		
?>

<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
