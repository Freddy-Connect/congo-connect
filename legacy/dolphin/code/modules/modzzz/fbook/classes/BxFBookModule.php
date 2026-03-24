<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx FBook
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

function modzzz_fbook_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'fbook') {
        $oMain = BxDolModule::getInstance('BxFBookModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
 
 

/*
 * FBook module
 * 
 */
class BxFBookModule extends BxDolTwigModule {
  
    function BxFBookModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'modzzz_fbook_filter';
        $this->_sPrefix = 'modzzz_fbook';
 
        $GLOBALS['oBxFBookModule'] = &$this; 
    }

    function actionHome () {
        parent::_actionHome(_t('_modzzz_fbook_page_title_home'));
    }
 
    function serviceModInstall()
    {
		$this->_oDb->checkForNewPage();
	}
 
    function serviceCommentsBlock () {
		
		if(!$this->isAllowedView()) return;
 
		$aVars = array( 
			'item_url' => $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"], 
			'num_posts' => getParam('modzzz_fbook_num_fcomments'), 
			'width' => getParam('modzzz_fbook_width_fcomments'), 
			'app_id' => getParam('modzzz_fbook_fapp_id'), 
			'language' => getParam('modzzz_fbook_language'),  
			'colorscheme' => getParam('modzzz_fbook_colorscheme'),
		);
 
		return $this->_oTemplate->parseHtmlByName('block_comments', $aVars);   
    }
  
    function isAllowedView ($isPerformAction = false) {

        // admin always have access
        if ( $this->isAdmin() ) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_FBOOK_VIEW, $isPerformAction);
        return ($aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED);
    }
 
    function _defineActions () {
        defineMembershipActions(array('fbook view'));
    }
 
    function actionAdministration ($sUrl = '',$sParam1='') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();
 
        $aMenu = array( 
            'settings' => array(
                'title' => _t('_modzzz_fbook_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'settings';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_fbook_page_title_administration'), $aMenu);

        $this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css'));
 
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_fbook_page_title_administration'));
    }
 
    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('FBook');
    }

    // ================================== events
  
    function isEntryAdmin($aDataEntry, $iProfileId = 0) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;

        return false;
    } 
  

}
