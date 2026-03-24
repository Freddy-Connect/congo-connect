<?php
	require_once('admin/credentials.php');
	/* Include the language file or redirect to install page */
	if(file_exists("admin/language.php")) {
		require_once('admin/language.php');
	}
	else {
		header('Location: admin/install.php');
	}
	
	$field_1['name'] = stripslashes($_POST['field_1']['name']);
	$field_1['value'] = stripslashes($_POST['field_1']['value']);
	$field_2['name'] = stripslashes($_POST['field_2']['name']);
	$field_2['value'] = stripslashes($_POST['field_2']['value']);
	$field_3['name'] = stripslashes($_POST['field_3']['name']);
	$field_3['value'] = stripslashes($_POST['field_3']['value']);
	$field_4['name'] = stripslashes($_POST['field_4']['name']);
	$field_4['value'] = str_replace("\n", '<br />', stripslashes($_POST['field_4']['value']));

	$emailBody = '<b>' . $field_1['name'] . '</b>' . " : " . $field_1['value'] . '<br /><br />';
	$emailBody .= '<b>' . $field_2['name'] . '</b>' . " : " . $field_2['value'] . '<br /><br />';
	$emailBody .= '<b>' . $field_3['name'] . '</b>' . " : " . $field_3['value'] . '<br /><br />';
	$emailBody .= '<b>' . $field_4['name'] . '</b>' . " : " . $field_4['value'];

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	if(mail(Email, __('send_mail_message') , $emailBody, $headers)) {
		$error = 0;
	}
	else {
		$error = 1;
	}

	echo json_encode(array('error' => $error));
?>