<?php
session_start();

require_once('includes/functions.inc.php');
require_once('includes/connect.inc.php');
$modify = false;
if (check_login_status() == false || (get_login_role() != "admin" && get_login_role() != "contributor")) {
	$_SESSION['error']  = "You do not have the authorization to access this page";
	redirect('login.php');
} else {
	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
	$connect = connectToDB();
}

if (isset($_POST['modifysubmit'])) {
	$modify = true;
}
if (isset($_POST['changesubmit'])) {
	$videourl = $_POST['videourl'];
	$festid = $_POST['festid'];
	$bandid = $_POST['bandid'];
	$type = $_POST['type'];
    if ($festid == 'null') {
        $bandyear = $_POST['bandyear'];
    } else {
        $query = "SELECT FestivalYear FROM Festivals WHERE FestivalID = $festid";
        $result = mysql_query($query, $connect);
        $row = mysql_fetch_array($result);
        $bandyear = $row['FestivalYear'];
    }
	$query = "UPDATE Videos SET BandID = '$bandid', BandYear = '$bandyear', FestivalID = '$festid', PerformanceType = '$type', Approved = 1 WHERE VideoURL = '$videourl'";
	mysql_query($query);

	$message = "Updated $videourl.";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="css/IMOconsole.css" />
<link rel="stylesheet" type="text/css" href="css/anylinkcssmenu.css" />
<script src="js/equalcolumns.js" type="text/javascript"></script> 
<script type="text/javascript" src="js/anylinkcssmenu.js"></script>
<script type="text/javascript" src="js/addTag.js"></script>
<script type="text/javascript" src="js/date.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<script type='text/javascript' src='js/jquery.autocomplete.js'></script>
<script type="text/javascript">
//anylinkcssmenu.init("menu_anchors_class") ////Pass in the CSS class of anchor links (that contain a sub menu)
anylinkcssmenu.init("anchorclass")
$().ready(function() {
	$("#VideoURL").autocomplete("ajax/get_video_list.php", {
		width: 260,
		matchContains: true,
		//mustMatch: true,
		//minChars: 0,
		//multiple: true,
		//highlight: false,
		//multipleSeparator: ",",
		selectFirst: false
	});
		$("#VideoURL").result(function(event, data, formatted) {
			$("#VideoID").val(data[1]);
		});
});

function validateID() {
	if (document.getElementById('VideoID').value != "") {
		document.SelectFestival.submit();
	} else {
		alert("Please select a Video from the Autocomplete box");
		return;
	}
}
</script>
</head>

<body>

<div id="maincontainer">	
	<!--HEADER-->
	<div id="topsection">
		<div class="innertube">
			<?php include('header.html'); ?>
		</div>
	</div>

	<!--MAIN CONTENT-->
	<div id="contentwrapper">
		<div id="contentcolumn">
			<div class="innertube">
				<?php if (isset($message)) {
					echo "<center><font size=+1 color=#FF0000>" . $message . "</font></center><p>";
				}
				?>
				<h1>Modify Video</h1>
				<?php
				if ($modify != true) { ?>
				<form name='SelectVideo' action='modify_video.php' method='POST' autocomplete="off">
					Youtube URL <label>:</label>
					<input type="text" name="VideoURL" id="VideoURL" size='50'/><input type='hidden' name='VideoID' id='VideoID' value=''/> 
					<input type='hidden' name='modifysubmit' id='modifysubmit' value='Submit' />
				<input type='button' id='modifybutton' name='modifybutton' value='Select Video' onClick='validateID()'/><br />(Just start typing the Youtube URL)
				</form>
				
				<?php } 
				if ($modify == true) {
					$f = $_POST['VideoID'];
                    $query = "SELECT * FROM Videos WHERE VideoURL = '$f'";
                    $result = mysql_query($query, $connect);
                    $row = mysql_fetch_array($result);
                    $bandid = $row['BandID'];
                    $festid = $row['FestivalID'];
                    $year = $row['BandYear'];
                    $type = $row['PerformanceType'];
                    
                    echo "<form action='modify_video.php' method='POST'>";
                    echo "<table border=0>";
                    echo "<tr><td>YoutubeURL:</td><td><b>$f<input type='hidden' name='videourl' id='videourl' value='$f' /></b></td></tr>";
                    $query = "SELECT * FROM Bands ORDER BY School ASC";
                    $result = mysql_query($query, $connect);
                    echo "<tr><td>Band:</td><td>";
                    echo "<select name='bandid' id='bandid'>";
                    while ($row = mysql_fetch_array($result)) {
                        $id = $row['BandID'];
                        $school = $row['School'];
                        if ($id == $bandid) {
                            echo "<option value='$id' selected='selected'>$school</option>";
                        } else {
                            echo "<option value='$id'>$school</option>"; 
                        }
                    }
                    echo "</select></td></tr>";
                    $query = "SELECT * FROM Festivals ORDER BY Date DESC, Name ASC";
                    $result = mysql_query($query, $connect);
                    echo "<tr><td>Festival:</td><td>";
                    echo "<select name='festid' id='festid'>";
                    echo "<option value='null' selected='selected'>...</option>";
                    while ($row = mysql_fetch_array($result)) {
                        $id = $row['FestivalID'];
                        $name = $row['Name'];
                        $festyear = $row['FestivalYear'];
                        if ($id == $festid) {
                            echo "<option value='$id' selected='selected'>$festyear - $name</option>";
                        } else {
                            echo "<option value='$id'>$festyear - $name</option>"; 
                        }
                    }
                    echo "</select></td></tr>";
                    echo "<tr><td>Year:</td><td><input type='text' name='bandyear' id='bandyear' value='$year' /></td></tr>";
                    echo "<tr><td>Type:</td><td>";
                    echo "<select name='type' id='type'>";
                    if ($type == "Prelim") {
                        echo "<option value='Prelim' selected='selected'>Prelims</option>";
                    } else {
                        echo "<option value='Prelim'>Prelims</option>";
                    }
                    if ($type == "SemiFinals") {
                        echo "<option value='SemiFinals' selected='selected'>SemiFinals</option>";
                    } else {
                        echo "<option value='SemiFinals'>SemiFinals</option>";
                    }
                    if ($type == "Finals") {
                        echo "<option value='Finals' selected='selected'>Finals</option>";
                    } else {
                        echo "<option value='Finals'>Finals</option>";
                    } 
                    echo "</select>";
                    echo "</td></tr>";
                    echo "</table>";
                    echo "<input type='submit' name='changesubmit' id='changesubmit' value='Modify Video' />";
                    echo "</form>";
                    echo "<p>";
                    $url = "http://youtube.com/embed/" . substr($f, stripos($f, "?v=") + 3);
                    echo "<h2>Video Preview:</h2>";
                    echo "<iframe id='video' name='video' width='353' height='315' src='$url' frameborder='0' allowfullscreen></iframe>";
				}
				?>
			</div>
		</div>
	</div>
	<!--MENU-->
	<div id="leftcolumn">
		<div class="innertube">
			Welcome, <?php echo ucwords(get_username());?><br />
			<?php echo date("F d, Y");?><br />
			<?php echo ucwords(get_login_role()) . " - <a href=includes/logout.inc.php><font color=#FFFFFF>logout</font></a>";?>
			<?php include("menu.php"); ?>
		</div>	
	</div>

	<div id="footer"><?php include('footer.html');?><div>
</div>

</body>
</html>
