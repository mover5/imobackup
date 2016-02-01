<?php 
require('./wp-blog-header.php');
require('./layout.php');
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->

<?php
include('connection.inc.php');
   
$userid = $_REQUEST['UserID'];
$query = "SELECT RealName, Role, Bio FROM Users WHERE UserID = $userid";
$result = mysql_query($query);
while ($row = mysql_fetch_array($result)) {
echo "<h1>".$row['RealName']." - ".ucfirst($row['Role']) . "</h1>";
$bio = $row['Bio'];
if ($bio == NULL || $bio == "") {
	echo "No Bio Available";
} else {
	echo $bio;
}

}
?>
<!--***************End Page Content***************-->
<?php imo_bottom(); ?>
