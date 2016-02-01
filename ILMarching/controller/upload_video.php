<?php

if (isset($_POST['submit'])) {
    require('../connection.inc.php');
    $bandid = $_POST['bandid'];
    $festid = $_POST['festid'];
    $type = $_POST['type'];
    $url = $_POST['url'];
    if ($url == "" || $url == NULL) {
        echo "You must include a URL with your submission";
    } else if ($bandid == "...") {
        echo "You must select a band to associate this video with";
    } else {
        $query = "SELECT FestivalYear FROM Festivals WHERE FestivalID = $festid";
        $result = mysql_query($query, $connect);
        $row = mysql_fetch_array($result);
        $year = $row['FestivalYear'];

        $query = "SELECT * FROM Videos WHERE ((VideoURL = '$url') OR (BandID = $bandid AND BandYear = $year AND FestivalID = $festid AND PerformanceType = '$type'))";
        $result = mysql_query($query, $connect);
        if (mysql_num_rows($result) == 0) {
            $query = "INSERT INTO Videos VALUES ('$url', $bandid, $year, $festid, '$type', 0)";
            mysql_query($query, $connect);
            echo "Added video with URL: $url<br />Your submission will be reviewed and approved within 24 hours.";
        } else {
            echo "A video already exists of that performance";
        }
    }
}
?>
