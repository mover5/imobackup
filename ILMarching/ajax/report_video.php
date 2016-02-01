<?php
require('../connection.inc.php');

if(isset($_POST['vidurl'])) {
    $query = "select VideoURL, BandYear, PerformanceType, School, Name from Videos v LEFT JOIN Bands b ON v.BandID = b.BandID LEFT JOIN Festivals f ON v.FestivalID = f.FestivalID WHERE VideoURL = '".$_POST['vidurl']."'";
    $result = mysql_query($query, $connect);
    $row = mysql_fetch_array($result);

    $to1 = "dbalash@gmail.com";
    $to2 = "overholt.mark@gmail.com";
    $from = "NoReply@ilmarching.com";
    $headers = "From: " . $from;
    $subject = "Reported ILMarching Video";
    $message = "Someone has reported a video!\n";
    $message .= "Video URL: " . $row['VideoURL'] . "\n";
    $message .= $row['School'] . "\n";
    $message .= $row['BandYear'] . " - " . $row['Name'] . " - " . $row['PerformanceType'] . "\n";
    $message .= "Reason: " . $_POST['reason'];
    mail($to1, $subject, $message, $headers);
    mail($to2, $subject, $message, $headers);
    echo "<font color='red'>Video Reported</font>";
}

?>
