<?php

if (isset($_POST['page'])) {
	$page = $_REQUEST['page'];
	if ($page == "FESTIVAL") {
		$subject = "ILMarching Festival Submission Form";
		$body = "Submission Form for: ".$_POST['festival'];
		$body .= "\n\nSubmitted by: ".$_POST['name']." - ".$_POST['email']."\n\n";
		$body .= "Festival Date: ".$_POST['date']."\n\n";
		$body .= "Festival Contact: ".$_POST['contact']."\n\n";
		$body .= "Festival Website: ".$_POST['website']."\n\n";
		$body .= "Festival Details: ".$_POST['details']."\n\n";
		$body .= "Parade Schedule: ".$_POST['parade']."\n\n";
		$body .= "Field Schedule: ".$_POST['field']."\n\n";
		$body .= "~ILMarching Online Submit Form";
	} else if ($page == "BAND") {
		$subject = "ILMarching Band Submission Form";
		$body = "Submission Form for: ".$_POST['band'];
		$body .= "\n\nSubmitted by: ".$_POST['name']." - ".$_POST['email']."\n\n";
		$body .= "Show Information: ".$_POST['show']."\n\n";
		$body .= "Award Information: ".$_POST['awards']."\n\n";
		$body .= "Interesting Information: ".$_POST['info']."\n\n";
		$body .= "~ILMarching Online Submit Form";
	} else if ($page == "SCORES") {
		$subject = "ILMarching Score Submission Form";
		$body = "Submission Form for: ".$_POST['festival'];
		$body .= "\n\nSubmitted by: ".$_POST['name']." - ".$_POST['email']."\n\n";
		$body .= "Recap Link: ".$_POST['recap']."\n\n";
		$body .= "Parade Scores: ".$_POST['parade']."\n\n";
		$body .= "Field Scores: ".$_POST['field']."\n\n";
		$body .= "~ILMarching Online Submit Form";
	} 
	
	$escapedbody = htmlspecialchars($body, ENT_QUOTES);

	echo "<form id='mailform' name='mailform' action='submit.php' method='POST'>";
	echo "<input type='hidden' name='spam' id='spam' value='".$_POST['spam']."' />";
	echo "<input type='hidden' name='body' id='body' value='$escapedbody' />";
	echo "<input type='hidden' name='subject' id='subject' value='$subject' />";
	echo "<input type='hidden' name='mailsubmit' id='mailsubmit' value='mailsubmit' />";
	echo "</form>";
	
	echo "<script type='text/javascript'>
	document.forms['mailform'].submit();
	</script>";
} 
?>
