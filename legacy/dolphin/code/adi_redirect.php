<?php

	require_once(dirname(__FILE__).'/inc/header.inc.php');
	require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
	define('BX_DOL_ADMIN_INDEX', 1);

	$bLogged = isLogged();
	
	if( $bLogged && isAdmin() )
	{
		$admin_url = BX_DOL_URL_ADMIN.'adi_admincp/adi_index.php';
		header('location: '.$admin_url);
		exit;
	}
	else
	{
		$admin_url = BX_DOL_URL_ADMIN.'index.php';
		header('location: '.$admin_url);
		exit;
	}

?>