<?php

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
bx_import('BxDolPageView');

class BxAdvicePageView extends BxDolPageView {
 
    function BxAdvicePageView() {
         parent::BxDolPageView('modzzz_atool_advice');  
    }
      
    function getBlockCode_Desc()
    { 
		global $_page;

		$iLang = getLangIdByName(getCurrentLangName());

        $oTool = BxDolModule::getInstance('BxAToolModule');  
		$aDataEntry = $oTool->_oDb->getEntryByPage ('advice', $iLang);
		
		$_page['header'] = $aDataEntry['title'];
		$_page['header_text'] = $aDataEntry['title'];

		$sContent = str_replace( '<site_url>', $GLOBALS['site']['url'], $aDataEntry['desc']);
 
		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		$sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));
  
		return array($sRet, array(), array(), $aDataEntry['title']);  
    }

}
    
$_page['name_index'] = 1;

check_logged();

//$_page['header'] = _t( "_ADVICE_H" );
//$_page['header_text'] = _t( "_ADVICE_H1" );

$_ni = $_page['name_index'];

$oPV = new BxAdvicePageView();
$_page_cont[$_ni]['page_main_code'] = $oPV->getCode();
 
PageCode();