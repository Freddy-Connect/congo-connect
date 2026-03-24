<?php
 
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );

 
if ( isset($_COOKIE['adminlogin']) || isAdmin()) {
 
 	$iProfileId = bx_get('ID');
 
	bx_login ((int)$iProfileId); // autologin here
 
	header( "Location:" . BX_DOL_URL_ROOT . "administration/");
} else {
	$_page['name_index'] = 0;
	$_page['header'] = $site['title'];
	$_page['header_text'] = $site['title'];
	$_page_cont[0]['page_main_code'] = MsgBox(_t('_Access denied'));
	PageCode();
}