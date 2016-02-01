<?php
require("sendgrid-php.php");

function sendEmail($to, $subject, $message) {
	$sendgrid = new SendGrid('ilmarching_mail', 'aksld8jc98aisndcasd98ch98273hnd9das8ejncm');

	$email = new SendGrid\Email();
	$email->addTo($to)->setFrom('noreply@ilmarching.com')->setSubject($subject)->setText('text')->setHtml($message);

	$sendgrid->send($email);
}
?>
