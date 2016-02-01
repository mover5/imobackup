<?php
require('../../connection.inc.php');
if(isset($_POST['imageid'])) {
    $imageid = $_POST['imageid'];
    $query = "DELETE FROM BandImages WHERE BandImageID=$imageid";
    mysql_query($query, $connect);
}

?>
