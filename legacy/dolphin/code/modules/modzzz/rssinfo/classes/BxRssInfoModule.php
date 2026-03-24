<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx RssInfo
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

function modzzz_rssinfo_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'rssinfo') {
        $oMain = BxDolModule::getInstance('BxRssInfoModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');



class BxRssInfoModule extends BxDolTwigModule {
 
    var $_aQuickCache = array ();

    function BxRssInfoModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'modzzz_rssinfo_filter';
        $this->_sPrefix = 'modzzz_rssinfo';
  
	    $this->_oConfig->init($this->_oDb);

        $GLOBALS['oBxRssInfoModule'] = &$this;
    }
 
    function actionHome () {
		header ('Location:' . BX_DOL_URL_ROOT . 'member.php');
    }
  
    function actionAdd () {
        parent::_actionAdd (_t('_modzzz_rssinfo_page_title_add'));
    }

    function _addForm ($sRedirectUrl) {

        bx_import ('FormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
  
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
				$this->_oDb->_sFieldUpdated => time(),  
				$this->_oDb->_sFieldPublished => time(),  
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
            ); 
			
            $iEntryId = $oForm->insert ($aValsAdd);

            if ($iEntryId) {
                
                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());
                $this->onEventCreate($iEntryId, $sStatus, $aDataEntry);
                if (!$sRedirectUrl)
                    $sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries';
                header ('Location:' . $sRedirectUrl);
                exit;

            } else { 
                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }

    function _actionEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/',
        ));

        if (!$this->isAllowedEdit($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        bx_import ('FormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormEdit';
        $oForm = new $sClass ($this, $aDataEntry[$this->_oDb->_sFieldAuthorId], $iEntryId, $aDataEntry);
 
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {
  
            $aValsAdd = array (
 				$this->_oDb->_sFieldUpdated => time()  
			);
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
                $this->onEventChanged ($iEntryId, $sStatus);
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries');
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else {

            echo $oForm->getCode ();

        }
 
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode($sTitle);
    }

    function actionEdit ($iEntryId) {
        $this->_actionEdit ($iEntryId, _t('_modzzz_rssinfo_page_title_edit'));
    }

    function actionDelete ($iEntryId) {
        parent::_actionDelete ($iEntryId, _t('_modzzz_rssinfo_msg_rssinfo_was_deleted'));
    }
 
    // ================================== external actions
 
 
    // ================================== admin actions

    function actionAdministration ($sUrl = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();

        $aMenu = array(
 
            'pending_publish' => array(
                'title' => _t('_modzzz_rssinfo_menu_admin_pending_publish'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_publish', 
                '_func' => array ('name' => 'actionAdministrationPublish', 'params' => array()),
            ), 
            'admin_entries' => array(
                'title' => _t('_modzzz_rssinfo_menu_entries'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ),            
            'create' => array(
                'title' => _t('_modzzz_rssinfo_menu_admin_add_entry'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
            'settings' => array(
                'title' => _t('_modzzz_rssinfo_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'admin_entries';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_rssinfo_page_title_administration'), $aMenu);
 
		$this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css'));
        
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_rssinfo_page_title_administration'));
    }



    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('RssInfo');
    }

    function actionAdministrationManage ($isAdminEntries = false) {
        return $this->_actionAdministrationManage ($isAdminEntries, '_modzzz_rssinfo_admin_delete', '_modzzz_rssinfo_admin_unpublish');
    }


    function _actionAdministrationManage ($isAdminEntries, $sKeyBtnDelete, $sKeyBtnActivate, $sUrl = false) {
		
		if($this->isAdmin()){
			if ($_POST['action_unpublish'] && is_array($_POST['entry'])) {

				foreach ($_POST['entry'] as $iId) {
					$this->_oDb->unPublishEntry($iId);
				}

			} elseif ($_POST['action_delete'] && is_array($_POST['entry'])) {

				foreach ($_POST['entry'] as $iId) { 
					$this->_oDb->deleteEntryByIdAndOwner($iId); 
				}
			}
		}

        if ($isAdminEntries) {
            $sContent = $this->_manageEntries ('admin', '', true, 'bx_twig_admin_form', array(
                'action_delete' => $sKeyBtnDelete,
				'action_unpublish' => $sKeyBtnActivate,
            ), '', true, 0, $sUrl);
        } 
            
        return $sContent;
    }
 
    function actionAdministrationPublish () {
		$sKeyBtnDelete = '_modzzz_rssinfo_admin_delete';
		$sKeyBtnPublish = '_modzzz_rssinfo_admin_publish';

        if ($_POST['action_publish'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {

				$aDataEntry = $this->_oDb->getEntryById($iId);
                $this->_oDb->publishEntry($iId);  
            }

        } elseif ($_POST['action_delete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {

                $aDataEntry = $this->_oDb->getEntryById($iId);
                if (!$this->isAllowedDelete($aDataEntry)) 
                    continue;

                if ($this->_oDb->deleteEntryByIdAndOwner($iId, 0, $this->isAdmin())) {
                    $this->onEventDeleted ($aDataEntry);
                }
            }
        }
 
		$sContent = $this->_manageEntries ('pending', '', true, 'bx_twig_admin_form', array(
			'action_publish' => $sKeyBtnPublish,
			'action_delete' => $sKeyBtnDelete,
		));
    
        return $sContent;
    }



    // ================================== events
 

 
    // ================================== permissions
    
    function isAllowedView ($aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ($this->isAdmin()) 
            return true;
 
		return false;
    }
 
    function isAllowedAdd ($isPerformAction = false) {
        return $this->isAdmin();
	} 

    function isAllowedEdit ($aDataEntry, $isPerformAction = false) {
        return $this->isAdmin(); 
    } 
 
    function isAllowedDelete (&$aDataEntry, $isPerformAction = false) {
        return $this->isAdmin(); 
    }     
  
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('permalinks_rssinfo') == 'on'));
		 
        return $bEnabled;
    }
 
    function _defineActions () {
        defineMembershipActions(array());
    }
 
    function onEventCreate ($iEntryId, $sStatus, $aDataEntry) {
		//
	}
 
    function onEventChanged ($iEntryId, $sStatus) {
		//
	} 

    function onEventDeleted ($aDataEntry) {
		$this->_oDb->deleteItemEntries($aDataEntry); 
	}

    function onEventPublished ($aDataEntry, $sStatus) {
	 
		$this->_oDb->updateItemStatus($aDataEntry, $sStatus); 
	}   
 
    function isEntryAdmin($aDataEntry, $iIdProfile = 0) {
        if (!$iIdProfile)
            $iIdProfile = $this->_iProfileId;
        return ($this->isAdmin() || $aDataEntry['author_id'] == $iIdProfile);
    }
   
	function actionPublish (){
		$this->_oDb->fetchFeed();
 
		echo "<center><b>The Feeds have been published</b></center>";
	}

    function isAllowedBrowse ($isPerformAction = false) {
        return ($this->isAdmin());  
    }

    function serviceInitialize()
    {
		$this->_oDb->initialize(); 
	}	

    function serviceCleanup()
    {
		$this->_oDb->cleanup(); 
	}	

 
}