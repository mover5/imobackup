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
    $message = "Are you sure you want to delete this video?";
}
else if (isset($_POST['changesubmit'])) {
	$videourl = $_POST['videourl'];
	$query = "DELETE FROM Videos WHERE VideoURL = '$videourl'";
	mysql_query($query);

	$message = "Deleted $videourl.";
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
				<h1>Delete Video</h1>
				<?php
				if ($modify != true) { ?>
				<form name='SelectVideo' action='delete_video.php' method='POST' autocomplete="off">
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
                    
                    echo "<form action='delete_video.php' method='POST'>";
                    echo "<table border=0>";
                    echo "<tr><td>YoutubeURL:</td><td><b>$f<input type='hidden' name='videourl' id='videourl' value='$f' /></b></td></tr>";
                    $query = "SELECT * FROM Bands WHERE BandID = $bandid";
                    $result = mysql_query($query, $connect);
                    echo "<tr><td>Band:</td><td>";
                    $row = mysql_fetch_array($result);
                    $school = $row['School'];
                    echo $school;
                    echo "</td></tr>";
                    $query = "SELECT * FROM Festivals WHERE FestivalID = $festid ORDER BY Date DESC, Name ASC";
                    $result = mysql_query($query, $connect);
                    echo "<tr><td>Festival:</td><td>";
                    $row = mysql_fetch_array($result);
                    $name = $row['Name'];
                    $festyear = $row['FestivalYear'];
                    echo "$festyear - $name";
                    echo "</td></tr>";
                    echo "<tr><td>Year:</td><td>$year</td></tr>";
                    echo "</table>";
                    echo "<input type='submit' name='changesubmit' id='changesubmit' value='Delete Video' />";
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
