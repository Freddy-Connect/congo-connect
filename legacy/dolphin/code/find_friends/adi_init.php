<?php

	$org_include_path = get_include_path();
	define('ADI_ORG_INCLUDE_PATH', $org_include_path);
	$include_path = dirname(dirname(__FILE__));

	if(!function_exists('isLogged'))
	{
		define('BX_MEMBER_PAGE', 1);
		require_once( $include_path.'/inc/header.inc.php' );
		require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
		require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
		require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
	}

?>