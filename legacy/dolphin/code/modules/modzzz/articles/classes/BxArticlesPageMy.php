<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Article
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

class BxArticlesPageMy extends BxDolPageView {	

    var $_oMain;
    var $_oTemplate;
    var $_oDb;
    var $_oConfig;
    var $_aProfile;

	function BxArticlesPageMy(&$oMain, &$aProfile) {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->_aProfile = $aProfile;
		parent::BxDolPageView('modzzz_articles_my');
	}

    function getBlockCode_Owner() {        
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch ($_REQUEST['filter']) {
        case 'add_article':
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_articles':
            $sContent = $this->getBlockCode_My ();
            break;            
        case 'pending_articles':
            $sContent = $this->getBlockCode_Pending ();
            break;       
        case 'draft_articles':
            $sContent = $this->getBlockCode_Draft ();
            break; 			
        default:
            $sContent = $this->getBlockCode_Main ();
        }

        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aMenu = array(
            _t('_modzzz_articles_block_submenu_main') => array('href' => $sBaseUrl, 'active' => empty($_REQUEST['filter']) || !$_REQUEST['filter']),
            _t('_modzzz_articles_block_submenu_add_article') => array('href' => $sBaseUrl . '&filter=add_article', 'active' => 'add_article' == $_REQUEST['filter']),
            _t('_modzzz_articles_block_submenu_manage_articles') => array('href' => $sBaseUrl . '&filter=manage_articles', 'active' => 'manage_articles' == $_REQUEST['filter']),
            _t('_modzzz_articles_block_submenu_pending_articles') => array('href' => $sBaseUrl . '&filter=pending_articles', 'active' => 'pending_articles' == $_REQUEST['filter']),
            _t('_modzzz_articles_block_submenu_draft_articles') => array('href' => $sBaseUrl . '&filter=draft_articles', 'active' => 'draft_articles' == $_REQUEST['filter']),

        );
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse() {

        modzzz_articles_import ('SearchResult');
        $o = new BxArticlesSearchResult('user', $this->_aProfile['NickName']);
        $o->aCurrent['rss'] = 0;

        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_modzzz_articles_page_title_my_articles');

        if ($o->isError) {
            return DesignBoxContent(_t('_modzzz_articles_block_users_articles'), MsgBox(_t('_Empty')), 1);
        }

        if ($s = $o->processing()) {
            $this->_oTemplate->addCss (array('unit.css', 'twig.css', 'main.css'));
        
            return $s;
        } else {
            return DesignBoxContent(_t('_modzzz_articles_block_users_articles'), MsgBox(_t('_Empty')), 1);
        }
    }

    function getBlockCode_Main() {
        $iActive = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        if ($iPending)
            $aVars['msg'] = sprintf(_t('_modzzz_articles_msg_you_have_pending_approval_articles'), $sBaseUrl . '&filter=pending_articles', $iPending);
        elseif (!$iActive)
            $aVars['msg'] = sprintf(_t('_modzzz_articles_msg_you_have_no_articles'), $sBaseUrl . '&filter=add_article');
        else
            $aVars['msg'] = sprintf(_t('_modzzz_articles_msg_you_have_some_articles'), $sBaseUrl . '&filter=manage_articles', $iActive, $sBaseUrl . '&filter=add_article');
        return $this->_oTemplate->parseHtmlByName('my_articles_main', $aVars);
    }

    function getBlockCode_Add() {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
        $this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my'); 
        $aVars = array ('form' => ob_get_clean(), 'id' => '');
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_articles_create_article', $aVars);
    }

    function getBlockCode_Pending() {
        $sForm = $this->_oMain->_manageEntries ('my_pending', '', false, 'modzzz_articles_pending_user_form', array(
            'action_delete' => '_modzzz_articles_admin_delete',
        ), 'modzzz_articles_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_articles_my_pending');
        return $this->_oTemplate->parseHtmlByName('my_articles_manage', $aVars); 
    }

    function getBlockCode_Draft() {
        $sForm = $this->_oMain->_manageEntries ('my_draft', '', false, 'modzzz_articles_draft_user_form', array(
            'action_delete' => '_modzzz_articles_admin_delete',
        ), 'modzzz_articles_my_draft', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_articles_my_draft');
        return $this->_oTemplate->parseHtmlByName('my_articles_manage', $aVars); 
    }

	function getBlockCode_My() {
        $sForm = $this->_oMain->_manageEntries ('user', $this->_aProfile['NickName'], false, 'modzzz_articles_user_form', array(
            'action_delete' => '_modzzz_articles_admin_delete',
        ), 'modzzz_articles_my_active', true, 7);
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_articles_my_active');
        return $this->_oTemplate->parseHtmlByName('my_articles_manage', $aVars);
    }    
}
