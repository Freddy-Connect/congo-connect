<?php

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
bx_import('BxDolPageView');
 
class BxFaqPageView extends BxDolPageView {
    function BxFaqPageView() {
         parent::BxDolPageView('modzzz_atool_faq');  
    }
      
    function getBlockCode_Desc()
    {  
 		global $_page;

		$iLang = getLangIdByName(getCurrentLangName()); 
		$oTool = BxDolModule::getInstance('BxAToolModule');  
		$aDataEntry = $oTool->_oDb->getEntryByPage ('faq', $iLang);

		$_page['header'] = $aDataEntry['title'];
		$_page['header_text'] = _t( $aDataEntry['title'], $site['title'] );

		$sRet = str_replace( '<site_url>', $GLOBALS['site']['url'], $aDataEntry['desc']);
 
		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		$sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sRet));
 
		return array($sRet, array(), array(), $aDataEntry['title']); 
    }

}
    
$_page['name_index'] = 1;

check_logged();
 
//$_page['header'] = $aDataEntry['title'];
//$_page['header_text'] = _t( $aDataEntry['title'], $site['title'] );

$_ni = $_page['name_index'];

$oEPV = new BxFaqPageView();
$_page_cont[$_ni]['page_main_code'] = $oEPV->getCode();

PageCode();