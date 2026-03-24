<?php
/***************************************************************************
*
*     copyright            : (C) 2016 AQB Soft
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
$_page['header'] = _t('_aqb_conditional_fields');

if(!@isAdmin()) {
    send_headers_page_changed();
	login_form("", 1);
	exit;
}

$oModule = new AqbConditionalFieldsModule($aModule);
$oModule->_oTemplate->addAdminJs('admin.js');
$oModule->_oTemplate->addAdminCss('forms_adv.css');
$oModule->_oTemplate->addJsTranslation('_Are_you_sure');

$aAddButon = array(
	'add' => array(
		'title' => '_aqb_conditional_fields_add',
		'href' => '#',
		'onclick' => 'aqb_conditional_fields_get_add_popup(); return false;',
	),
);

$_page_cont[$iIndex]['page_main_code'] = DesignBoxAdmin(_t('_aqb_conditional_fields'), '<div id="aqb_conditional_fields_rows">'.$oModule->_oTemplate->getConditionalFieldsList().'</div>', $aAddButon, false, 11);

PageCodeAdmin();