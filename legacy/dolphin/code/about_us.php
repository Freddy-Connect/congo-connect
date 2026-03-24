<?php

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
bx_import('BxDolPageView');

class BxAboutPageView extends BxDolPageView {
 
    function BxAboutPageView() {
         parent::BxDolPageView('modzzz_atool_about');  
    }
      
    function getBlockCode_Desc()
    { 
		global $_page;

		$iLang = getLangIdByName(getCurrentLangName());

        $oTool = BxDolModule::getInstance('BxAToolModule');  
		$aDataEntry = $oTool->_oDb->getEntryByPage ('about', $iLang);
		
		$_page['header'] = $aDataEntry['title'];

		$sContent = str_replace( '<site_url>', $GLOBALS['site']['url'], $aDataEntry['desc']);
 
		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		$sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));
  
		return array($sRet, array(), array(), $aDataEntry['title']);  
    }

}
    
$_page['name_index'] = 1;

check_logged();

//$_page['header'] = _t( "_ABOUT_US_H" );

$_ni = $_page['name_index'];

$oPV = new BxAboutPageView();
$_page_cont[$_ni]['page_main_code'] = $oPV->getCode();
 
PageCode();