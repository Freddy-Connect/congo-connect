<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
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

bx_import('BxDolPageView');

class BxCharityPageMy extends BxDolPageView {	

    var $_oMain;
    var $_oTemplate;
    var $_oDb;
    var $_oConfig;
    var $_aProfile;

	function BxCharityPageMy(&$oMain, &$aProfile) {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->_aProfile = $aProfile;
		parent::BxDolPageView('modzzz_charity_my');
	}

    function getBlockCode_Owner() {        
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch ($_REQUEST['filter']) {
        case 'add_charity':
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_charity':
            $sContent = $this->getBlockCode_My ();
            break;    
        case 'pending_charity':
            $sContent = $this->getBlockCode_Pending ();
            break;   
        default:
            $sContent = $this->getBlockCode_Main ();
        }

        $sBrowseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/";
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aMenu = array(
            _t('_modzzz_charity_block_submenu_main') => array('href' => $sBaseUrl, 'active' => empty($_REQUEST['filter']) || !$_REQUEST['filter']),
  		    _t('_modzzz_charity_block_submenu_add_charity') => array('href' => $sBaseUrl . '&filter=add_charity', 'active' => 'add_charity' == $_REQUEST['filter']),
            _t('_modzzz_charity_block_submenu_manage_charity') => array('href' => $sBaseUrl . '&filter=manage_charity', 'active' => 'manage_charity' == $_REQUEST['filter']),
            _t('_modzzz_charity_block_submenu_pending_charity') => array('href' => $sBaseUrl . '&filter=pending_charity', 'active' => 'pending_charity' == $_REQUEST['filter']),
 
        );
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse() {

        modzzz_charity_import ('SearchResult');
        $o = new BxCharitySearchResult('user', $this->_aProfile['NickName']);
        $o->aCurrent['rss'] = 0;

        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_modzzz_charity_page_title_my_charity');

        if ($o->isError) {
            return DesignBoxContent(_t('_modzzz_charity_block_users_charity'), MsgBox(_t('_Empty')), 1);
        }

        if ($s = $o->processing()) {
            $this->_oTemplate->addCss ('unit.css');
            $this->_oTemplate->addCss ('main.css');            
            return $s;
        } else {
            return DesignBoxContent(_t('_modzzz_charity_block_users_charity'), MsgBox(_t('_Empty')), 1);
        }
    }

    function getBlockCode_Main() {
        $iActive = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        if ($iPending)
            $aVars['msg'] = sprintf(_t('_modzzz_charity_msg_you_have_pending_approval_charity'), $sBaseUrl . '&filter=pending_charity', $iPending);
        elseif (!$iActive)
            $aVars['msg'] = sprintf(_t('_modzzz_charity_msg_you_have_no_charity'), $sBaseUrl . '&filter=add_charity');
        else
            $aVars['msg'] = sprintf(_t('_modzzz_charity_msg_you_have_some_charity'), $sBaseUrl . '&filter=manage_charity', $iActive, $sBaseUrl . '&filter=add_charity');
        return $this->_oTemplate->parseHtmlByName('my_charity_main', $aVars);
    }

    function getBlockCode_Add() {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
        $this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my'); 
        $aVars = array ('form' => ob_get_clean(), 'id' => '');
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_charity_create_charity', $aVars);
    }
 
    function getBlockCode_Pending() {
        $sForm = $this->_oMain->_manageEntries ('my_pending', '', false, 'modzzz_charity_pending_user_form', array(
            'action_delete' => '_modzzz_charity_admin_delete',
        ), 'modzzz_charity_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_charity_my_pending');
        return $this->_oTemplate->parseHtmlByName('my_charity_manage', $aVars); 
    }
 
	function getBlockCode_My() {
        $sForm = $this->_oMain->_manageEntries ('user', $this->_aProfile['NickName'], false, 'modzzz_charity_user_form', array(
            'action_delete' => '_modzzz_charity_admin_delete',
        ), 'modzzz_charity_my_active', true, 7);
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_charity_my_active');
        return $this->_oTemplate->parseHtmlByName('my_charity_manage', $aVars);
    }    
}

