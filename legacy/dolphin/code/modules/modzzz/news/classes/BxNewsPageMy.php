<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx News
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

class BxNewsPageMy extends BxDolPageView {	

    var $_oMain;
    var $_oTemplate;
    var $_oDb;
    var $_oConfig;
    var $_aProfile;

	function BxNewsPageMy(&$oMain, &$aProfile) {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->_aProfile = $aProfile;
		parent::BxDolPageView('modzzz_news_my');
	}

    function getBlockCode_Owner() {        
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch ($_REQUEST['filter']) {
        case 'add_news':
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_news':
            $sContent = $this->getBlockCode_My ();
            break;            
        case 'pending_news':
            $sContent = $this->getBlockCode_Pending ();
            break; 
        case 'draft_news':
            $sContent = $this->getBlockCode_Draft ();
            break;			
        default:
            $sContent = $this->getBlockCode_Main ();
        }

        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aMenu = array(
            _t('_modzzz_news_block_submenu_main') => array('href' => $sBaseUrl, 'active' => empty($_REQUEST['filter']) || !$_REQUEST['filter']),
            _t('_modzzz_news_block_submenu_add_news') => array('href' => $sBaseUrl . '&filter=add_news', 'active' => 'add_news' == $_REQUEST['filter']),
            _t('_modzzz_news_block_submenu_manage_news') => array('href' => $sBaseUrl . '&filter=manage_news', 'active' => 'manage_news' == $_REQUEST['filter']),
            _t('_modzzz_news_block_submenu_pending_news') => array('href' => $sBaseUrl . '&filter=pending_news', 'active' => 'pending_news' == $_REQUEST['filter']),
            _t('_modzzz_news_block_submenu_draft_news') => array('href' => $sBaseUrl . '&filter=draft_news', 'active' => 'draft_news' == $_REQUEST['filter']),
		);
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse() {

        modzzz_news_import ('SearchResult');
        $o = new BxNewsSearchResult('user', $this->_aProfile['NickName']);
        $o->aCurrent['rss'] = 0;

        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_modzzz_news_page_title_my_news');

        if ($o->isError) {
            return DesignBoxContent(_t('_modzzz_news_block_users_news'), MsgBox(_t('_Empty')), 1);
        }

        if ($s = $o->processing()) {
            $this->_oTemplate->addCss ('unit.css');
            $this->_oTemplate->addCss ('main.css');            
            return $s;
        } else {
            return DesignBoxContent(_t('_modzzz_news_block_users_news'), MsgBox(_t('_Empty')), 1);
        }
    }

    function getBlockCode_Main() {
        $iActive = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        if ($iPending)
            $aVars['msg'] = sprintf(_t('_modzzz_news_msg_you_have_pending_approval_news'), $sBaseUrl . '&filter=pending_news', $iPending);
        elseif (!$iActive)
            $aVars['msg'] = sprintf(_t('_modzzz_news_msg_you_have_no_news'), $sBaseUrl . '&filter=add_news');
        else
            $aVars['msg'] = sprintf(_t('_modzzz_news_msg_you_have_some_news'), $sBaseUrl . '&filter=manage_news', $iActive, $sBaseUrl . '&filter=add_news');
        return $this->_oTemplate->parseHtmlByName('my_news_main', $aVars);
    }

    function getBlockCode_Add() {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
        $this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my'); 
        $aVars = array ('form' => ob_get_clean(), 'id' => '');
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_news_create_news', $aVars);
    }
 
    function getBlockCode_Draft() {
        $sForm = $this->_oMain->_manageEntries ('my_draft', '', false, 'modzzz_news_draft_user_form', array(
            'action_delete' => '_modzzz_news_admin_delete',
        ), 'modzzz_news_my_draft', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_news_my_draft');
        return $this->_oTemplate->parseHtmlByName('my_news_manage', $aVars); 
    }

    function getBlockCode_Pending() {
        $sForm = $this->_oMain->_manageEntries ('my_pending', '', false, 'modzzz_news_pending_user_form', array(
            'action_delete' => '_modzzz_news_admin_delete',
        ), 'modzzz_news_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_news_my_pending');
        return $this->_oTemplate->parseHtmlByName('my_news_manage', $aVars); 
    }

	function getBlockCode_My() {
        $sForm = $this->_oMain->_manageEntries ('user', $this->_aProfile['NickName'], false, 'modzzz_news_user_form', array(
            'action_delete' => '_modzzz_news_admin_delete',
        ), 'modzzz_news_my_active', true, 7);
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_news_my_active');
        return $this->_oTemplate->parseHtmlByName('my_news_manage', $aVars);
    }    
}
