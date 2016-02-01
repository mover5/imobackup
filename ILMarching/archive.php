<?php 
require('./wp-blog-header.php');
require('./layout.php');
?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->
<div id="content">
<?
require('wordpress_connection.inc.php');

function switchOrder($dir) {
    if ($dir == "ASC") return "DESC";
    else return "ASC";
}

if (isset($_REQUEST['col'])) {
    $column = $_REQUEST['col'];
} else {
    $column = "post_date";
}

if (isset($_REQUEST['dir'])) {
    $dir = $_REQUEST['dir'];
} else {
    $dir = "ASC";
}

if ($column == "post_date") {
    $datelink = "archive.php?col=post_date&dir=".switchOrder($dir);
    $titlelink = "archive.php?col=post_title&dir=ASC";
} else {
    $datelink = "archive.php?col=post_date&dir=ASC";
    $titlelink = "archive.php?col=post_title&dir=".switchOrder($dir);
}



$query = "SELECT ID, post_title, post_date FROM wp_posts WHERE post_status = 'publish' ORDER BY $column $dir";
$result = mysql_query($query);
	echo "<p><h2>Illinois Marching Online News Archives</h3><p>";
	
echo "<table class='table_grid' border=0 cellspacing=5 width='100%'>";
echo "<thead><tr class='catbg'>";
echo "<td><a href='$datelink'><font color='#FFFFFF'>Date</font></a></td>";
echo "<td><a href='$titlelink'><font color='#FFFFFF'>Article</font></a></td>";
echo "</tr></thead>";

	$i=0;	
    while ($row = mysql_fetch_array($result)) {
    if ($i % 2 == 0) $style = "windowbg"; else $style = "h";
	$title = $row['post_title'];
	$date = $row['post_date'];
	$date = strtotime($date);
	$date = date("F d, Y", $date);
	$id = $row['ID'];
	echo "<tr><td class='$style'>$date</td>";
	
	echo "<td class='$style'><a href='http://ilmarching.com/?p=$id'>$title</a></td></tr>";	
	$i++;
	
}	
echo "</table>";
mysql_close();
?>

</div>
<!--***************End Page Content***************-->

<?php imo_bottom(); ?>
