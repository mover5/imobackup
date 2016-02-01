<?php
include('connection.inc.php');
require("/home/ilmarch/sendgrid-php/ilmarching_sendgrid.php");

$to1 = "dbalash@gmail.com";
$to2 = "overholt.mark@gmail.com";

// Every night verification of email accounts
//$subject = "Mail Account Verification";
//$message = "This email is to verify that the mail account is still working.\n If you are seeing this message, everything is OK!";
//mail($to1, $subject, $message, $headers);
//mail($to2, $subject, $message, $headers);

// 2 weeks before expiration
$date = date("Y-m-d", strtotime("+2 weeks"));
$query = "SELECT * FROM Ads WHERE ExpirationDate = '" . $date . "'";
$result = mysql_query($query, $connect);
while ($row = mysql_fetch_array($result)) {
    $subject = "Ad Expiration: " . $row['Title'];
    $message = "This is an automated message from the ILMarching Servers\n\n";
    $message .= "The advertisement: ";
    $message .= $row['Title'] . " is set to expire in 2 weeks, " . $row['ExpirationDate'];
    $message .= "\n\nThe current price for this ad is: $" . $row['Price'];
    $message .= "\nPlease notify " . $row['Contact'] . " at " . $row['Email'] . "to renew this ad.";
    $message .= "\n\nThis ad will stop being displayed in 2 weeks if action is not taken.";
    $message .= "\n\nThank You";
    sendEmail($to1, $subject, $message);
    sendEmail($to2, $subject, $message);
    
    //echo "Sent mail for ad: " . $row['Title'] . "<br />";
}

// 1 week before expiration
$date = date("Y-m-d", strtotime("+1 weeks"));
$query = "SELECT * FROM Ads WHERE ExpirationDate = '" . $date . "'";
$result = mysql_query($query, $connect);
while ($row = mysql_fetch_array($result)) {
    $subject = "Ad Expiration: " . $row['Title'];
    $message = "This is an automated message from the ILMarching Servers\n\n";
    $message .= "The advertisement: ";
    $message .= $row['Title'] . " is set to expire in 1 week, " . $row['ExpirationDate'];
    $message .= "\n\nThe current price for this ad is: $" . $row['Price'];
    $message .= "\nPlease notify " . $row['Contact'] . " at " . $row['Email'] . "to renew this ad.";
    $message .= "\n\nThis ad will stop being displayed in 1 week if action is not taken.";
    $message .= "\n\nThank You";
    sendEmail($to1, $subject, $message);
    sendEmail($to2, $subject, $message);
    
    //echo "Sent mail for ad: " . $row['Title'] . "<br />";
}

// expiration date
$date = date("Y-m-d");
$query = "SELECT * FROM Ads WHERE ExpirationDate = '" . $date . "'";
$result = mysql_query($query, $connect);
while ($row = mysql_fetch_array($result)) {
    $subject = "Ad Expiration: " . $row['Title'];
    $message = "This is an automated message from the ILMarching Servers\n\n";
    $message .= "The advertisement: ";
    $message .= $row['Title'] . " is expiring today!";
    $message .= "\n\nThe current price for this ad is: $" . $row['Price'];
    $message .= "\nPlease notify " . $row['Contact'] . " at " . $row['Email'] . "to renew this ad.";
    $message .= "\n\nThis ad has stopped displaying on the main site.";
    $message .= "\n\nThank You";
    mail($to1, $subject, $message, $headers);
    mail($to2, $subject, $message, $headers);
    
    //echo "Sent mail for ad: " . $row['Title'] . "<br />";
}

// Images to Approve
$query = "SELECT * FROM BandImages WHERE Approved = 0";
$result = mysql_query($query, $connect);
$num = mysql_num_rows($result);
if ($num <> 0) {
	$subject = "$num ILMarching Image(s) to Approve";
	$message = "There are $num images to approve. Go to the console to approve them";
	sendEmail($to1, $subject, $message);
        sendEmail($to2, $subject, $message);

}

// Videos to Approve
$query = "SELECT * FROM Videos WHERE Approved = 0";
$result = mysql_query($query, $connect);
$num = mysql_num_rows($result);
if ($num <> 0) {
	$subject = "$num ILMarching Video(s) to Approve";
	$message = "There are $num videos to approve. Go to the console to approve them";
	sendEmail($to1, $subject, $message);
    sendEmail($to2, $subject, $message);
}
echo "Mail Updates Complete";

?>

