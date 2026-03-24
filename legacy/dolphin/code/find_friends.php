<?php

	define('BX_INDEX_PAGE', 1);
	require_once( 'inc/header.inc.php' );
	require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
	require_once( BX_DIRECTORY_PATH_INC . 'admin.inc.php' );
	require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );
	require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );

	$_page['name_index'] = 81;
	$_page['header']     = _t( "Find Your Friends" );
	$member['ID']        = getLoggedId();
	$member['Password']  = getLoggedPassword();
	$_ni                 = $_page['name_index'];

	send_headers_page_changed();
	$contents  = '';
	$file_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'find_friends'.DIRECTORY_SEPARATOR.'adi_inpage.php';
	include_once($file_path);
	$contents  = '<div class="sys_bc_wrapper bx-def-border" style="margin:20px 0px;padding: 10px 0px 20px 0px;"><center>'.$contents.'</center></div>';
	$_page_cont[$_ni]['page_main_code'] = $contents;

	// Submenu actions
	$aVars = array(
	    'ID' => $member['ID'],
	    'BaseUri' => BX_DOL_URL_ROOT,
	    'cpt_am_account_profile_page' => _t('_sys_am_account_profile_page')
	);
	$GLOBALS['oTopMenu']->setCustomSubActions($aVars, 'AccountTitle', false);
	PageCode();

?>