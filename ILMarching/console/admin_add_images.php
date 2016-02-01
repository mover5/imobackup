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
	$message = "";
}

if (isset($_POST['submit_images'])) {
	$numrows = $_POST['num_rows'];
	for ($itemrow = 1; $itemrow <= $numrows; $itemrow++) {
		$file = $_FILES["file_".$itemrow];
		if ((($file["type"] == "image/bmp") || ($file["type"] == "image/png") || ($file["type"] == "image/jpeg") 
		|| ($file["type"] == "image/pjpeg")) && ($file["size"] < 4097153)) {
			$extension = strtolower(strstr($file["name"], "."));
			$bid = $_POST['band_'.$itemrow];
			if ($bid != 0) {
				$query = "SELECT School FROM Bands WHERE BandID = $bid";
				$result = mysql_query($query, $connect);
				$row = mysql_fetch_array($result);
				$school = $row['School'];
				$school = trim($school);
				$school = str_replace(" ", "_", $school);
				$school = str_replace("'", "", $school);
				$count = 1;
				$filepath = "bandpics/" . $school . $count . $extension;
				$findname = 1;
				while ($findname) {
					if (file_exists("../".$filepath)) {
						$count++;
						$filepath = "bandpics/" . $school . $count . $extension;
					} else {
						$findname = 0;
					}
				}
				move_uploaded_file($file['tmp_name'], "../".$filepath);
				$query = "INSERT INTO BandImages (BandID, ImageURL, Approved) VALUES ($bid, '$filepath', 1)";
				mysql_query($query, $connect);
				$message .= "Uploaded an image for $school.<br />";
			} else {
				$message .= "You must select a band for each image.<br />";
			}
		} else {
			$message .= "Image Upload Failed due to image type or size.<br />";
		}		
	}
}
$message .= "Remember: The more images you upload at once...the longer it will take when you hit Submit. Be Patient!";
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

var num_rows = 1;

function addImageRow() {

	var currentid = parseInt(document.getElementById('num_rows').value)
	if (document.getElementById("band_"+(parseInt(currentid)+1)) == null) {
		num_rows++;
		
		var last_index = document.getElementById("bodytable").rows.length; // total number of rows on the screen
		document.getElementById('num_rows').value = currentid + 1;
		var new_row = document.getElementById("bodytable").insertRow(last_index);
		var c1 = new_row.insertCell(0);
		var c2 = new_row.insertCell(1);
		
		var band_ids = document.getElementById("band_1");

		var new_band_ids = band_ids.cloneNode(true);

		new_band_ids.id = "band_"+(num_rows);

		new_band_ids.name = "band_"+(num_rows);
		
		new_band_ids.selectedIndex = 0;

		var c2name = "file_"+(num_rows);

		c1.appendChild(new_band_ids);
		c2.innerHTML = "<input type='file' name='"+c2name+"' id='"+c2name+"' />";

	}
}
function getKeyCode(e, obj) {
	var keynum;
	if(window.event) // IE
  	{
  		keynum = e.keyCode;
  	}
	else if(e.which) // Netscape/Firefox/Opera
  	{
 		 keynum = e.which;
  	}
  	
  	if (keynum == 107) {
		var id_arr = obj.id.split("_");
		var id = id_arr[1];
		id = parseInt(id) + 1;
		var rows = parseInt(document.getElementById('num_rows').value);
		if (id <= rows) {
			document.getElementById(id_arr[0] + "_" + id).focus();
		}
		return false;
	}
	
	if (keynum == 109) {
		var id_arr = obj.id.split("_");
		var id = id_arr[1];
		id = parseInt(id) - 1;
		if (id > 0) {
			document.getElementById(id_arr[0] + "_" + id).focus();
		}
		return false;
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
				<h1>Add Band Images</h1>
				<form action='admin_add_images.php' method='POST' enctype='multipart/form-data'>
				<input name='addrow' id='addrow' type='button' value='Add Row' onclick='addImageRow()' /><br />
				<table border=1 id='bodytable'>
				<tr><th>Band</th><th>Image</th></tr>
				
				<tr>
				
				
				<td>
				<select name='band_1' id='band_1'>
				<option value='0'>...</option>
				<?php
					$query = "SELECT BandID, School FROM Bands";
					$result = mysql_query($query, $connect);
					while ($row = mysql_fetch_array($result)) {
						$bid = $row['BandID'];
						$school = $row['School'];
						echo "<option value='$bid'>$school</option>";
					}
				?>
				</select>
				</td>
				
				<td>
				<input type='file' name='file_1' id='file_1' />
				</td>
				
				</tr>		
				</table>
				<input type='hidden' id='num_rows' name='num_rows' value='1' /><br />
				<input type='submit' id='submit_images' name='submit_images' value='Submit' />
				</form>
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

	<!--<div id="footer"><?php //include('footer.html');?><div>-->
</div>

</body>
</html>
