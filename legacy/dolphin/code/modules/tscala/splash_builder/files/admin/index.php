<?php
	session_start();

	if(file_exists("credentials.php")) {
		require_once('credentials.php');
		if(strlen(Email)>0 && strlen(Password)>0) {
			if($_SESSION['user'] == Email) {
				header('Location: home.php');
			}
			else {
				header('Location: login.php');
			}
		}
		else {
			header('Location: install.php');	
		}
	}
	else {
		header('Location: install.php');
	}
?>