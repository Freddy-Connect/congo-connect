<?php

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
bx_import('BxDolPageView');

class BxPrivacyPageView extends BxDolPageView {
    function BxPrivacyPageView() {
         parent::BxDolPageView('modzzz_atool_privacy');  
    }
      
    function getBlockCode_Desc()
    { 
 		global $_page;

		$iLang = getLangIdByName(getCurrentLangName());

        $oTool = BxDolModule::getInstance('BxAToolModule');  
		$aDataEntry = $oTool->_oDb->getEntryByPage ('privacy', $iLang);
 
 		$_page['header'] = $aDataEntry['title'];

  		$sRet = str_replace( '{0}', $GLOBALS['site']['title'], $aDataEntry['desc']);
 		$sRet = str_replace( '{1}', $GLOBALS['site']['url'], $sRet);

		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		$sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sRet));
 
		return array($sRet, array(), array(), $aDataEntry['title']); 
    }

}
    
$_page['name_index'] = 1;

check_logged();

//$_page['header'] = _t( "_PRIVACY_H" );
 
$_ni = $_page['name_index'];

$oEPV = new BxPrivacyPageView();
$_page_cont[$_ni]['page_main_code'] = $oEPV->getCode();

PageCode();