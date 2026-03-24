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

class BxChurchPageMy extends BxDolPageView {	

    var $_oMain;
    var $_oTemplate;
    var $_oDb;
    var $_oConfig;
    var $_aProfile;

	function BxChurchPageMy(&$oMain, &$aProfile) {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->_aProfile = $aProfile;
		parent::BxDolPageView('modzzz_church_my');
	}

    function getBlockCode_Owner() {        
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch ($_REQUEST['filter']) {
        case 'add_church':
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_church':
            $sContent = $this->getBlockCode_My ();
            break;   
        case 'expired_church':
            $sContent = $this->getBlockCode_Expired ();
            break;			
        case 'pending_church':
            $sContent = $this->getBlockCode_Pending ();
            break;   
        case 'my_pending_invoices':
            $sContent = $this->getBlockCode_Invoices ();
            break;  
        case 'my_orders':
            $sContent = $this->getBlockCode_Orders ();
            break;   
        default:
            $sContent = $this->getBlockCode_Main ();
        }

        $sBrowseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/";
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aMenu = array(
            _t('_modzzz_church_block_submenu_main') => array('href' => $sBaseUrl, 'active' => empty($_REQUEST['filter']) || !$_REQUEST['filter']),
		    
			_t('_modzzz_church_block_submenu_pending_invoices') => array('href' => $sBaseUrl . '&filter=my_pending_invoices', 'active' => 'my_pending_invoices' == $_REQUEST['filter']),
            _t('_modzzz_church_block_submenu_orders') => array('href' => $sBaseUrl . '&filter=my_orders', 'active' => 'my_orders' == $_REQUEST['filter']),
       
		    _t('_modzzz_church_block_submenu_add_church') => array('href' => $sBaseUrl . '&filter=add_church', 'active' => 'add_church' == $_REQUEST['filter']),
            _t('_modzzz_church_block_submenu_manage_church') => array('href' => $sBaseUrl . '&filter=manage_church', 'active' => 'manage_church' == $_REQUEST['filter']),
            _t('_modzzz_church_block_submenu_pending_church') => array('href' => $sBaseUrl . '&filter=pending_church', 'active' => 'pending_church' == $_REQUEST['filter']),
            _t('_modzzz_church_block_submenu_expired_church') => array('href' => $sBaseUrl . '&filter=expired_church', 'active' => 'expired_church' == $_REQUEST['filter']), 

        );
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse() {

        modzzz_church_import ('SearchResult');
        $o = new BxChurchSearchResult('user', $this->_aProfile['NickName']);
        $o->aCurrent['rss'] = 0;

        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_modzzz_church_page_title_my_church');

        if ($o->isError) {
            return DesignBoxContent(_t('_modzzz_church_block_users_church'), MsgBox(_t('_Empty')), 1);
        }

        if ($s = $o->processing()) {
			$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
          
            return $s;
        } else {
            return DesignBoxContent(_t('_modzzz_church_block_users_church'), MsgBox(_t('_Empty')), 1);
        }
    }

    function getBlockCode_Main() {
        $iActive = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        if ($iPending)
            $aVars['msg'] = sprintf(_t('_modzzz_church_msg_you_have_pending_approval_church'), $sBaseUrl . '&filter=pending_church', $iPending);
        elseif (!$iActive)
            $aVars['msg'] = sprintf(_t('_modzzz_church_msg_you_have_no_church'), $sBaseUrl . '&filter=add_church');
        else
            $aVars['msg'] = sprintf(_t('_modzzz_church_msg_you_have_some_church'), $sBaseUrl . '&filter=manage_church', $iActive, $sBaseUrl . '&filter=add_church');
        return $this->_oTemplate->parseHtmlByName('my_church_main', $aVars);
    }

    function getBlockCode_Add() {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
        $this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my'); 
        $aVars = array ('form' => ob_get_clean(), 'id' => '');
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_church_create_church', $aVars);
    }

    function getBlockCode_Expired() {
        $sForm = $this->_oMain->_manageEntries ('my_expired', '', false, 'modzzz_church_expired_user_form', array(
            'action_delete' => '_modzzz_church_admin_delete',
        ), 'modzzz_church_my_expired', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_church_my_expired');
        return $this->_oTemplate->parseHtmlByName('my_church_manage', $aVars); 
    }

    function getBlockCode_Pending() {
        $sForm = $this->_oMain->_manageEntries ('my_pending', '', false, 'modzzz_church_pending_user_form', array(
            'action_delete' => '_modzzz_church_admin_delete',
        ), 'modzzz_church_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_church_my_pending');
        return $this->_oTemplate->parseHtmlByName('my_church_manage', $aVars); 
    }

    function getBlockCode_Invoices() {
 
        $sForm = $this->_oMain->_manageOrders ('invoice', '', false, 'modzzz_church_pending_user_form', array(
            'action_delete' => '_modzzz_church_admin_delete',
        ), 'modzzz_church_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_church_my_pending_invoices');
        return $this->_oTemplate->parseHtmlByName('my_church_manage', $aVars); 
    }

    function getBlockCode_Orders() {
        $sForm = $this->_oMain->_manageOrders ('order', '', false, 'modzzz_church_pending_user_form', array(), 'modzzz_church_my_orders', false, 7, false);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_church_my_orders');
        return $this->_oTemplate->parseHtmlByName('my_church_manage', $aVars); 
    }

	function getBlockCode_My() {
        $sForm = $this->_oMain->_manageEntries ('user', $this->_aProfile['NickName'], false, 'modzzz_church_user_form', array(
            'action_delete' => '_modzzz_church_admin_delete',
        ), 'modzzz_church_my_active', true, 7);
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_church_my_active');
        return $this->_oTemplate->parseHtmlByName('my_church_manage', $aVars);
    }    
}
