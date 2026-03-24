<?php
	require_once('credentials.php');
	/* Include the language file or redirect to install page */
	if(file_exists("language.php")) {
		require_once('language.php');
	}
	else {
		header('Location: install.php');
	}
	


	$emailBody = '<b>' . __('forgot_email_intro') . '<br /><br />';
	$emailBody .= '<b>' . __('forgot_email_address') . '</b>' . " : " . Email . '<br /><br />';
	$emailBody .= '<b>' . __('forgot_email_pass') . '</b>' . " : " . Password . '<br /><br />';

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	if(mail(Email, __('forgot_email_subject') , $emailBody, $headers)) {
		$error = 0;
	}
	else {
		$error = 1;
	}

	echo json_encode(array('error' => $error));
?>