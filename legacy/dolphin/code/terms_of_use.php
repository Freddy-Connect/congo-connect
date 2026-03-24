<?php

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
bx_import('BxDolPageView');

class BxTermsPageView extends BxDolPageView {
    function BxTermsPageView() {
         parent::BxDolPageView('modzzz_atool_terms');  
    }
      
    function getBlockCode_Desc()
    { 
 		global $_page;

		$iLang = getLangIdByName(getCurrentLangName());

        $oTool = BxDolModule::getInstance('BxAToolModule');  
		$aDataEntry = $oTool->_oDb->getEntryByPage ('terms', $iLang);

		$_page['header'] = $aDataEntry['title'];
		$_page['header_text'] = _t( $aDataEntry['title'], $site['title'] );

		$sRet = str_replace( '<site_url>', $GLOBALS['site']['url'], $aDataEntry['desc']);
 		$sRet = str_replace( '{0}', $GLOBALS['site']['title'], $sRet);
 
		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		$sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sRet));
 
		return array($sRet, array(), array(), $aDataEntry['title']); 
    }

}
    
$_page['name_index'] = 1;

check_logged();

//$_page['header'] = _t( "_TERMS_OF_USE_H" );
//$_page['header_text'] = _t( "_TERMS_OF_USE_H1" );

$_ni = $_page['name_index'];

$oEPV = new BxTermsPageView();
$_page_cont[$_ni]['page_main_code'] = $oEPV->getCode();

PageCode();