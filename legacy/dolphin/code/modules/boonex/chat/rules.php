<?php

require_once( BX_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $aModule['class_prefix'] . 'Module.php');

bx_import('BxDolPageView');

class BxRulesPageView extends BxDolPageView {
    function BxRulesPageView() {
         parent::BxDolPageView('modzzz_atool_chat_rules');  
    }
      
    function getBlockCode_Desc()
    { 
		global $_page;

		$iLang = getLangIdByName(getCurrentLangName());

        $oTool = BxDolModule::getInstance('BxAToolModule');  
		$aDataEntry = $oTool->_oDb->getEntryByPage ('chat', $iLang);
  
  		$_page['header'] = $aDataEntry['title'];

  		$sRet = str_replace( '{0}', $GLOBALS['site']['title'], $aDataEntry['desc']);
 		$sRet = str_replace( '{1}', $GLOBALS['site']['url'], $sRet);

		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		$sRet = $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sRet));
 
		return array($sRet, array(), array(), $aDataEntry['title']); 
    }

}
    
global $_page;
global $_page_cont;

$_page['name_index'] = 1;

check_logged();

//$_page['header'] = _t( "_chat_page_rules_caption" );
 
$_ni = $_page['name_index'];

$oEPV = new BxRulesPageView();
$_page_cont[$_ni]['page_main_code'] = $oEPV->getCode();

PageCode();