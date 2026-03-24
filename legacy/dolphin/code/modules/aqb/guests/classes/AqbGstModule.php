<?php
/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
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

bx_import('BxDolModule');
bx_import('BxDolAdminSettings');

require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

define('AQB_GST_DURATION_TODAY', 'today');
define('AQB_GST_DURATION_WEEK', 'week');
define('AQB_GST_DURATION_ALL', 'all');

class AqbGstModule extends BxDolModule {

	//--- Constructor ---//
	function AqbGstModule($aModule) {
	    parent::BxDolModule($aModule);

	    $this->_oConfig->init($this->_oDb);
	}

	//--- Admin Settings Methods ---//
	function getSettings($mixedResult) {
	    $iId = (int)$this->_oDb->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name`='" . $this->_oConfig->getUri() . "'");
	    if(empty($iId))
	       return MsgBox('_aqb_gst_msg_no_results');

        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();

        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;

        return $sResult;
	}

	function setSettings($aData) {
	    $iId = (int)$this->_oDb->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name`='" . $this->_oConfig->getUri() . "'");
	    if(empty($iId))
	       return MsgBox(_t('_aqb_gst_msg_no_results'));

	    $oSettings = new BxDolAdminSettings($iId);
	    return $oSettings->saveChanges($_POST);
	}

	//--- Service Methods ---//
	function serviceGetBlockAccount() {
	    return $this->_oTemplate->getBlockGuests();
	}

	function serviceGetBlockProfile($iProfileId) {
		if($iProfileId != getLoggedId())
			return '';

	    return $this->_oTemplate->getBlockGuests();
	}

	//--- Action Methods ---//
	function actionAdmin($sName = '') {
		$GLOBALS['iAdminPage'] = 1;
		require_once(BX_DIRECTORY_PATH_INC . 'admin_design.inc.php');

		$sUri = $this->_oConfig->getUri();
		$sUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'admin';

		check_logged();
		if(!@isAdmin()) {
		    send_headers_page_changed();
			login_form("", 1);
			exit;
		}

		//--- Process actions ---//
		$mixedResultSettings = '';
		if(isset($_POST['save']) && isset($_POST['cat']))
		    $mixedResultSettings = $this->setSettings($_POST);

		$this->_oTemplate->addAdminJs(array('main.js'));
		$this->_oTemplate->addAdminCss(array('main.css'));

		$aParams = array(
			'title' => array(
				'page' => _t('_aqb_gst_page_admin')
			),
			'content' => array(
				'page_main_code' => DesignBoxAdmin(_t('_aqb_gst_block_admin_settings'), $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $this->getSettings($mixedResultSettings))))
			)
		);
		$this->_oTemplate->getPageCodeAdmin($aParams);
	}

	function actionGetGuests() {
		$oJson = new Services_JSON();
		header('Content-Type:text/javascript');

		$iStart = (int)bx_get('start');

		$iPerPage = (int)bx_get('per_page');
		if($iPerPage == 0)
			$iPerPage = $this->_oConfig->getPerPage();

		$sFilter = process_db_input(bx_get('filter'));
		if(!in_array($sFilter, array(AQB_GST_DURATION_TODAY, AQB_GST_DURATION_WEEK, AQB_GST_DURATION_ALL)))
			$sFilter = '';

		$iUserId = getLoggedId();
		$sContent = $this->_oTemplate->getGuests($iUserId, $iStart, $iPerPage, $sFilter);

		return $oJson->encode(array('code' => 0, 'content' => $sContent, 'eval' => $this->_oConfig->getJsObject('main') . '.onGetGuets(oData)'));
	}
}
?>