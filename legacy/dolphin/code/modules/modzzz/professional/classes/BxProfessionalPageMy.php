<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Professional
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

class BxProfessionalPageMy extends BxDolPageView {	

    var $_oMain;
    var $_oTemplate;
    var $_oDb;
    var $_oConfig;
    var $_aProfile;

	function BxProfessionalPageMy(&$oMain, &$aProfile) {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->_aProfile = $aProfile;
		parent::BxDolPageView('modzzz_professional_my');
	}

    function getBlockCode_Owner() {        
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch ($_REQUEST['filter']) {
        case 'add_professional':
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_professional':
            $sContent = $this->getBlockCode_My ();
            break;            
        case 'pending_professional':
            $sContent = $this->getBlockCode_Pending ();
            break;   
        case 'expired_professional':
            $sContent = $this->getBlockCode_Expired ();
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
            _t('_modzzz_professional_block_submenu_main') => array('href' => $sBaseUrl, 'active' => empty($_REQUEST['filter']) || !$_REQUEST['filter']),
		    
			_t('_modzzz_professional_block_submenu_pending_invoices') => array('href' => $sBaseUrl . '&filter=my_pending_invoices', 'active' => 'my_pending_invoices' == $_REQUEST['filter']),
            _t('_modzzz_professional_block_submenu_orders') => array('href' => $sBaseUrl . '&filter=my_orders', 'active' => 'my_orders' == $_REQUEST['filter']),
       
		    _t('_modzzz_professional_block_submenu_add_professional') => array('href' => $sBaseUrl . '&filter=add_professional', 'active' => 'add_professional' == $_REQUEST['filter']),
            _t('_modzzz_professional_block_submenu_manage_professional') => array('href' => $sBaseUrl . '&filter=manage_professional', 'active' => 'manage_professional' == $_REQUEST['filter']),
            _t('_modzzz_professional_block_submenu_pending_professional') => array('href' => $sBaseUrl . '&filter=pending_professional', 'active' => 'pending_professional' == $_REQUEST['filter']), 
            _t('_modzzz_professional_block_submenu_expired_professional') => array('href' => $sBaseUrl . '&filter=expired_professional', 'active' => 'expired_professional' == $_REQUEST['filter']), 

        );
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse() {

        modzzz_professional_import ('SearchResult');
        $o = new BxProfessionalSearchResult('user', $this->_aProfile['NickName']);
        $o->aCurrent['rss'] = 0;

        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_modzzz_professional_page_title_my_professional');

        if ($o->isError) {
            return DesignBoxContent(_t('_modzzz_professional_block_users_professional'), MsgBox(_t('_Empty')), 1);
        }

        if ($s = $o->processing()) {

            $this->_oTemplate->addCss (array('unit.css', 'twig.css', 'main.css'));
			
            return $s;
        } else {
            return DesignBoxContent(_t('_modzzz_professional_block_users_professional'), MsgBox(_t('_Empty')), 1);
        }
    }

    function getBlockCode_Main() {
        $iActive = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        if ($iPending)
            $aVars['msg'] = sprintf(_t('_modzzz_professional_msg_you_have_pending_approval_professional'), $sBaseUrl . '&filter=pending_professional', $iPending);
        elseif (!$iActive)
            $aVars['msg'] = sprintf(_t('_modzzz_professional_msg_you_have_no_professional'), $sBaseUrl . '&filter=add_professional');
        else
            $aVars['msg'] = sprintf(_t('_modzzz_professional_msg_you_have_some_professional'), $sBaseUrl . '&filter=manage_professional', $iActive, $sBaseUrl . '&filter=add_professional');
        return $this->_oTemplate->parseHtmlByName('my_professional_main', $aVars);
    }

    function getBlockCode_Add() {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
        $this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my'); 
        $aVars = array ('form' => ob_get_clean(), 'id' => '');
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_professional_create_professional', $aVars);
    }
 
    function getBlockCode_Expired() {
        $sForm = $this->_oMain->_manageEntries ('my_expired', '', false, 'modzzz_professional_expired_user_form', array(
            'action_delete' => '_modzzz_professional_admin_delete',
        ), 'modzzz_professional_my_expired', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_professional_my_expired');
        return $this->_oTemplate->parseHtmlByName('my_professional_manage', $aVars); 
    }

    function getBlockCode_Pending() {
        $sForm = $this->_oMain->_manageEntries ('my_pending', '', false, 'modzzz_professional_pending_user_form', array(
            'action_delete' => '_modzzz_professional_admin_delete',
        ), 'modzzz_professional_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_professional_my_pending');
        return $this->_oTemplate->parseHtmlByName('my_professional_manage', $aVars); 
    }

    function getBlockCode_Invoices() {
 
        $sForm = $this->_oMain->_manageOrders ('invoice', '', false, 'modzzz_professional_pending_user_form', array(
            'action_delete' => '_modzzz_professional_admin_delete',
        ), 'modzzz_professional_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_professional_my_pending_invoices');
        return $this->_oTemplate->parseHtmlByName('my_professional_manage', $aVars); 
    }

    function getBlockCode_Orders() {
        $sForm = $this->_oMain->_manageOrders ('order', '', false, 'modzzz_professional_pending_user_form', array(), 'modzzz_professional_my_orders', false, 7, false);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_professional_my_orders');
        return $this->_oTemplate->parseHtmlByName('my_professional_manage', $aVars); 
    }

	function getBlockCode_My() {
        $sForm = $this->_oMain->_manageEntries ('user', $this->_aProfile['NickName'], false, 'modzzz_professional_user_form', array(
            'action_delete' => '_modzzz_professional_admin_delete',
        ), 'modzzz_professional_my_active', true, 7);
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_professional_my_active');
        return $this->_oTemplate->parseHtmlByName('my_professional_manage', $aVars);
    }    
}
