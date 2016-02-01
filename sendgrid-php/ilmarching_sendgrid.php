<?php
require("sendgrid-php.php");

function sendEmail($to, $subject, $message) {
	$sendgrid = new SendGrid('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');

	$email = new SendGrid\Email();
	$email->addTo($to)->setFrom('noreply@ilmarching.com')->setSubject($subject)->setText('text')->setHtml($message);

	$sendgrid->send($email);
}
?>
