<?php
	
	$org_include_path = get_include_path();
	define('ADI_ORG_INCLUDE_PATH', $org_include_path);
	$admin_path = dirname(dirname(__FILE__));
	$root_path = dirname($admin_path);
	set_include_path($admin_path);
	chdir($admin_path);

	// Redirect on Error
	if(isset($_GET['errno']))
	{
		header('location: ../');
	}

	require_once( $root_path.'/inc/header.inc.php' );
	require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
	require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
	require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
	require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
	define('BX_DOL_ADMIN_INDEX', 1);

	$bLogged = isLogged();
	if( !($bLogged && isAdmin()) )
	{
		$admin_url = BX_DOL_URL_ADMIN.'index.php';
		header('location: '.$admin_url);
	}

	// Set AdiInviter root path
	$adi_lib_path = $root_path . DIRECTORY_SEPARATOR . 'find_friends';

?>