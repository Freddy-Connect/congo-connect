<?php

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
bx_import('BxDolPageView');

class BxHelpPageView extends BxDolPageView {
    function BxHelpPageView() {
         parent::BxDolPageView('modzzz_atool_help');  
    }
      
    function getBlockCode_Desc()
    { 
 		global $_page;
 
		$iLang = getLangIdByName(getCurrentLangName());

        $oTool = BxDolModule::getInstance('BxAToolModule');  
		$aDataEntry = $oTool->_oDb->getEntryByPage ('help', $iLang);

		$_page['header'] = $aDataEntry['title'];
 
		$sRet = str_replace( '<site_url>', $GLOBALS['site']['url'], $aDataEntry['desc']);
 
		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		$sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sRet));
 
		return array($sRet, array(), array(), $aDataEntry['title']); 
    }

}
    
$_page['name_index'] = 1;

check_logged();

//$_page['header'] = _t( "_HELP_H" );
 
$_ni = $_page['name_index'];

$oEPV = new BxHelpPageView();
$_page_cont[$_ni]['page_main_code'] = $oEPV->getCode();

PageCode();