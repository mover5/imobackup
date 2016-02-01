<?php
require('../../connection.inc.php');
if(isset($_POST['bandid'])) {
    $bandid = $_POST['bandid'];
    $query = "SELECT * FROM BandImages WHERE BandID = $bandid";
    $result = mysql_query($query, $connect);
    if (mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_array($result)) {
            $url = $row['ImageURL'];
            $imageid = $row['BandImageID'];
            if (!strstr($url, "http")) {
                $url = "../".$url;
            }
            echo "<div style='display:inline-block;text-align:center;padding-right:5px;'><img id='$imageid' src='$url' width='100' height='100' /><br /><button id='$imageid'>Delete</button></div>";
        }
    } else {
        echo "<b>No Images Available</b>";
    }
}


?>
