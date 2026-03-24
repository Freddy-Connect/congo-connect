<?php
/***************************************************************************
*
*     copyright            : (C) 2017 AQB Soft
*     website              : http://www.aqbsoft.com
*
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
*
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
*
* This notice may not be removed from the source code.
*
***************************************************************************/

require_once(BX_DIRECTORY_PATH_INC . 'admin_design.inc.php');

bx_import('Module', $aModule);

global $_page;
global $_page_cont;

$iIndex = 1;
$_page['name_index'] = $iIndex;
$_page['header'] = _t('_aqb_pyml');

if(!@isAdmin()) {
    send_headers_page_changed();
	login_form("", 1);
	exit;
}

$oModule = new AqbPYMLModule($aModule);

if (isset($_POST['save'])) {
	$oModule->saveSettings();
}

if (isset($_POST['save_fields'])) {
	$oModule->saveFields();
}

$_page_cont[$iIndex]['page_main_code'] = DesignBoxAdmin(_t('_aqb_pyml_settings'), $oModule->getSettingsForm(), false, false, 11);
$_page_cont[$iIndex]['page_main_code'] .= DesignBoxAdmin(_t('_aqb_pyml_fields'), $oModule->_oTemplate->fieldsManager(), false, false, 11);

PageCodeAdmin();