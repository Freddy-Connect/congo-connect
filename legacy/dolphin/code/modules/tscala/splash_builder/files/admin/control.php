<?php
	session_start();
	if(file_exists("credentials.php")) {
		require_once('credentials.php');
		
		
	}
	else {
		header('Location: install.php');
	}
	
	if(isset($_POST['payload'])) {
		if(get_magic_quotes_gpc()) {
		    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		    while (list($key, $val) = each($process)) {
		        foreach ($val as $k => $v) {
		            unset($process[$key][$k]);
		            if (is_array($v)) {
		                $process[$key][stripslashes($k)] = $v;
		                $process[] = &$process[$key][stripslashes($k)];
		            } else {
		                $process[$key][stripslashes($k)] = stripslashes($v);
		            }
		        }
		    }
		    unset($process);
		}

		$data = $_POST['payload'];

		$bytes = file_put_contents("config.conf", serialize($data));

		if($bytes === false || $bytes == 0) {
			$error = 1;
		}
		else {
			$error = 0;
		}
	}

	if(isset($_POST['email'])) {
		$content = "<?php
	define('Email', '" . $_POST['email'] . "');
	define('Password', '" . addslashes($_POST['password']) . "');
?>";
		$fp = fopen("credentials.php", "w");
		$bytes = fwrite($fp, $content);
		fclose($fp);

		if($bytes>0)
			$error = 0;
		else {
			$error = 1;
		}
	}

	echo json_encode(array('error'=>$error));
?>