<?php
require('./wp-blog-header.php');
require('./layout.php');
require("/home/ilmarch/sendgrid-php/ilmarching_sendgrid.php");
include('connection.inc.php');

function spamcheck($field)
  {
  //filter_var() sanitizes the e-mail
  //address using FILTER_SANITIZE_EMAIL
  $field=filter_var($field, FILTER_SANITIZE_EMAIL);

  //filter_var() validates the e-mail
  //address using FILTER_VALIDATE_EMAIL
  if(filter_var($field, FILTER_VALIDATE_EMAIL))
    {
    return TRUE;
    }
  else
    {
    return FALSE;
    }
  }
$message = "";
if (isset($_POST['submit'])) {
	$email = $_POST['email'];
	$query = "SELECT * FROM BandsOfTheYearVotersPublic WHERE EmailHash = PASSWORD('$email')";
	$result = mysql_query($query);
	if (mysql_num_rows($result) > 0) {
		$message = "That email address has already signed up to vote!";
	} else if (!spamcheck($email)) {
		$message = "Invalid Email Address";	
	} else {
		$query = "INSERT INTO BandsOfTheYearVotersPublic (EmailHash, Voted) VALUES (PASSWORD('$email'), 0)";
		mysql_query($query);
		
		//Send Email
		$subject = "Bands of the Year Public Vote";
		$from = "noreply@ilmarching.com";
		$body = "Thank you for chosing to vote in ILMarching.com's Bands of the Year Public Vote\n\n";
		$body .= "The entire public vote will count for 1 ballot in the real Bands of the Year vote.\n\n";
		$body .= "To cast your vote, go to the following website:\n";
		$body .= "http://ilmarching.com/botyBallotPublic.php\n\n";
		$body .= "Your username is: $email \n";
		$body .= "Your password is: " . substr(sha1($email), 0, 6);
		$body .= "\n\nThis password is Case Sensitive. Type it in exactly as you see here.\n\n";
		$body .= "Cast your ballot as you see fit, remembering that Number 1. is the best band in the division, and so on down the line.\n\n";
		$body .= "Also be sure to vote for the band that you thought was 'Most Improved' in that division this season.\n\n";
		$body .= "Other than the Most Improved vote, base your votes SOLELY on this year's performances ONLY!!\n\n";
		$body .= "Thanks for taking the time to vote, and happy voting!\n\nThanks,\nIllinois Marching Online Staff";
		
		sendEmail($email, $subject, $body);
		
		$message = "Registration sent to the provided email address!";
	}
}

?>
<?php imo_top(); ?>
<!--***************Start Page Content***************-->

<h1>Public Bands of the Year Ballot Sign-up</h1>

Welcome everyone to this year's ILMarching Bands of the Year voting. As in previous years, we will be taking all of the bands that have competed this year, 
and holding a vote to pick the best bands in each division. Traditionally, this vote has ONLY been for directors, judges, etc. This year, we want to add a
public component to the mix. As we do for our ISU and UofI Polls, we will take the collective vote of the entire public, and count it at ONE ballot for this year's
Bands of the Year. So now, you have a say (albeit a small one) in which bands become our Bands of the Year. <p></p>
We ask that if you would like to vote, that you register to do so with a valid email address. This is to help ensure that people can not vote more than once and skew 
the results. So with that, if you would like to cast a ballot in the public vote, enter your email address below. You will be sent an email with all of the instructions
you will need to vote.<p></p>
Privacy Note: We will NOT save your email address...so in no way will your email be given to ANYONE via this form. As soon as the email is sent to you with instructions,
your email will be forgotten by our system, replaced instead by a One-Way Hash, which will be used to identify you. If you have any technical questions or concerns regarding
this, feel free to email me at moverholt@ilmarching.com and I would be happy to explain our policy and technically how we are doing this to ensure your privacy. 

<p></p>
<form action='publicBotySignup.php' method='POST'>
<center><b>Sign-Up to Vote</b><br />Email: <input size='60' type='text' name='email' id='email' /><br /><input type='submit' name='submit' id='submit' value='Sign-Up' /></center>
</form>
<?php
if ($message <> "") {
	echo "<p></p><strong>" . $message . "</strong><p></p>";
}
?>
<!--***************End Page Content***************-->
<?php imo_bottom(); ?>
