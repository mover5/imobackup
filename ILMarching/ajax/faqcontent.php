<?php
require("../connection.inc.php");

$id = $_GET['id'];

mysql_select_db("ilmarch_wordpress", $connect);
$result = mysql_query("SELECT * FROM wp_posts WHERE ID=$id");
$row = mysql_fetch_array($result);
echo nl2br($row['post_content']);

?>
