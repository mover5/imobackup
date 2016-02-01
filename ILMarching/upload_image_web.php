<?php
require('./wp-blog-header.php');
require('./layout.php');
require("connection.inc.php");
if (isset($_REQUEST['bandid'])) {
	$bandid = $_REQUEST['bandid'];
} else {
	$bandid = -1;
}

$bid = $_POST['uploadband'];

if (isset($_POST['uploadimage']) && $bid != -1) {
	if ((($_FILES["file"]["type"] == "image/bmp") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/pjpeg")) 
	&& ($_FILES["file"]["size"] < 2097153)) {
		$extension = strstr($_FILES['file']['name'], ".");
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
			if (file_exists($filepath)) {
				$count++;
				$filepath = "bandpics/" . $school . $count . $extension;
			} else {
				$findname = 0;
			}
		}
		move_uploaded_file($_FILES['file']['tmp_name'], $filepath);
		$query = "INSERT INTO BandImages (BandID, ImageURL, Approved) VALUES ($bid, '$filepath', 0)";
		mysql_query($query, $connect);
		$message = "Uploaded an image for $school. Please allow some time for the staff to approve this image";
		
	} else {
		$message = "Image must be either a <b>jpg</b>, <b>bmp</b>, or <b>png</b> image, and it must be smaller than <b>2 Megabytes</b>";
	}
} elseif ($bid == -1) {
    $message = "You must select a band to upload an image for.";
}
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->
<?php
echo "<font color='#FF0000' size='+1'>$message</font>";
?>
<h1>Upload an Image for a band!</h1>
<h3>First, Select a Band:</h3>
<form action='upload_image_web.php' method='POST' enctype='multipart/form-data'>
<select name='uploadband' id='uploadband'>
<?php
if ($bandid == -1) {
  echo "<option value='-1' selected='selected'>...</option>";
}
$query = "SELECT BandID, School FROM Bands";
$result = mysql_query($query, $connect);
while ($row = mysql_fetch_array($result)) {
	$bid = $row['BandID'];
	$school = $row['School'];
	if ($bid == $bandid) {
		echo "<option value='$bid' selected='selected'>$school</option>";
	} else {
		echo "<option value='$bid'>$school</option>";
	}
}
?>
</select>
<h3>Next, select an Image to upload:</h3>
It must be either a <b>jpg</b>, <b>bmp</b>, or <b>png</b> image, and it must be smaller than <b>2 Megabytes</b> <br />
<input type='file' name='file' id='file' /> <p></p>
<input type='submit' name='uploadimage' id='uploadimage' value='Upload Image' />
</form>

<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
