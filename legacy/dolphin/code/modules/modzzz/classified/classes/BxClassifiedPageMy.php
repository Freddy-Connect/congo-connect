<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Classified
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

class BxClassifiedPageMy extends BxDolPageView {	

    var $_oMain;
    var $_oTemplate;
    var $_oDb;
    var $_oConfig;
    var $_aProfile;

	function BxClassifiedPageMy(&$oMain, &$aProfile) {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->_aProfile = $aProfile;
		parent::BxDolPageView('modzzz_classified_my');
	}

    function getBlockCode_Owner() {        
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch ($_REQUEST['filter']) {
        case 'add_classified':
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_classified':
            $sContent = $this->getBlockCode_My ();
            break;            
        case 'pending_classified':
            $sContent = $this->getBlockCode_Pending ();
            break; 
        case 'expired_classified':
            $sContent = $this->getBlockCode_Expired ();
            break; 
        case 'salepending_classified':
            $sContent = $this->getBlockCode_SalePending ();
            break; 
        //case 'sold_classified':
        //    $sContent = $this->getBlockCode_Sold();
        //    break; 			
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
            _t('_modzzz_classified_block_submenu_main') => array('href' => $sBaseUrl, 'active' => empty($_REQUEST['filter']) || !$_REQUEST['filter']),
		    
			_t('_modzzz_classified_block_submenu_pending_invoices') => array('href' => $sBaseUrl . '&filter=my_pending_invoices', 'active' => 'my_pending_invoices' == $_REQUEST['filter']),
            _t('_modzzz_classified_block_submenu_orders') => array('href' => $sBaseUrl . '&filter=my_orders', 'active' => 'my_orders' == $_REQUEST['filter']),
       
		    _t('_modzzz_classified_block_submenu_add_classified') => array('href' => $sBaseUrl . '&filter=add_classified', 'active' => 'add_classified' == $_REQUEST['filter']),
            _t('_modzzz_classified_block_submenu_manage_classified') => array('href' => $sBaseUrl . '&filter=manage_classified', 'active' => 'manage_classified' == $_REQUEST['filter']),

            _t('_modzzz_classified_block_submenu_salepending_classified') => array('href' => $sBaseUrl . '&filter=salepending_classified', 'active' => 'salepending_classified' == $_REQUEST['filter']),

            //_t('_modzzz_classified_block_submenu_sold_classified') => array('href' => $sBaseUrl . '&filter=sold_classified', 'active' => 'sold_classified' == $_REQUEST['filter']),
 
            _t('_modzzz_classified_block_submenu_pending_classified') => array('href' => $sBaseUrl . '&filter=pending_classified', 'active' => 'pending_classified' == $_REQUEST['filter']),

            _t('_modzzz_classified_block_submenu_expired_classified') => array('href' => $sBaseUrl . '&filter=expired_classified', 'active' => 'expired_classified' == $_REQUEST['filter']), 


        );
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse() {

        modzzz_classified_import ('SearchResult');
        $o = new BxClassifiedSearchResult('user', $this->_aProfile['NickName']);
        $o->aCurrent['rss'] = 0;

        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_modzzz_classified_page_title_my_classified');

        if ($o->isError) {
            return DesignBoxContent(_t('_modzzz_classified_block_users_classified'), MsgBox(_t('_Empty')), 1);
        }

        if ($s = $o->processing()) {
            $this->_oTemplate->addCss ('unit.css');
            $this->_oTemplate->addCss ('main.css');            
            return $s;
        } else {
           // freddy modif  return DesignBoxContent(_t('_modzzz_classified_block_users_classified'), MsgBox(_t('_Empty')), 1);
		   return;
        }
    }

    function getBlockCode_Main() {
        $iActive = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        if ($iPending)
            $aVars['msg'] = sprintf(_t('_modzzz_classified_msg_you_have_pending_approval_classified'), $sBaseUrl . '&filter=pending_classified', $iPending);
        elseif (!$iActive)
            $aVars['msg'] = sprintf(_t('_modzzz_classified_msg_you_have_no_classified'), $sBaseUrl . '&filter=add_classified');
        else
            $aVars['msg'] = sprintf(_t('_modzzz_classified_msg_you_have_some_classified'), $sBaseUrl . '&filter=manage_classified', $iActive, $sBaseUrl . '&filter=add_classified');
        return $this->_oTemplate->parseHtmlByName('my_classified_main', $aVars);
    }

    function getBlockCode_Add() {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
        $this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my'); 
        $aVars = array ('form' => ob_get_clean(), 'id' => '');
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_classified_create_classified', $aVars);
    }

    function getBlockCode_Expired() {
        $sForm = $this->_oMain->_manageEntries ('my_expired', '', false, 'modzzz_classified_expired_user_form', array(
            'action_delete' => '_modzzz_classified_admin_delete',
        ), 'modzzz_classified_my_expired', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty_Annonce_Expirer'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_classified_my_expired');
        return $this->_oTemplate->parseHtmlByName('my_classified_manage', $aVars); 
    }

    function getBlockCode_Pending() {
        $sForm = $this->_oMain->_manageEntries ('my_pending', '', false, 'modzzz_classified_pending_user_form', array(
            'action_delete' => '_modzzz_classified_admin_delete',
        ), 'modzzz_classified_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty_classified_attente_validation'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_classified_my_pending');
        return $this->_oTemplate->parseHtmlByName('my_classified_manage', $aVars); 
    }

    function getBlockCode_SalePending() {

        if ($_POST['action_cancel'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) { 
                $this->_oMain->_oDb->deleteSaleOffer($iId); 
            } 
        } 

        if ($_POST['action_mark_complete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
                $this->_oMain->_oDb->markSaleCompleted($iId); 
            } 
        } 

        $sForm = $this->_oMain->_manageSalePending ('sale_pending', '', false, 'modzzz_classified_salepending_user_form', array(
            'action_cancel' => '_modzzz_classified_admin_cancel',
            'action_mark_complete' => '_modzzz_classified_admin_mark_complete' 
        ), 'modzzz_classified_sale_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_classified_sale_pending');
        return $this->_oTemplate->parseHtmlByName('my_classified_manage', $aVars); 
    }
  
    function getBlockCode_Invoices() {
  
        $sForm = $this->_oMain->_manageOrders ('invoice', '', false, 'modzzz_classified_pending_user_form', array(
            'action_delete' => '_modzzz_classified_admin_delete',
        ), 'modzzz_classified_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty_classified_invoices'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_classified_my_pending_invoices');
        return $this->_oTemplate->parseHtmlByName('my_classified_manage', $aVars); 
    }

    function getBlockCode_Orders() {
        $sForm = $this->_oMain->_manageOrders ('order', '', false, 'modzzz_classified_pending_user_form', array(), 'modzzz_classified_my_orders', false, 7, false);
        if (!$sForm)
            return MsgBox(_t('_Empty_classified_orders'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_classified_my_orders');
        return $this->_oTemplate->parseHtmlByName('my_classified_manage', $aVars); 
    }

	function getBlockCode_My() {
        $sForm = $this->_oMain->_manageEntries ('user', $this->_aProfile['NickName'], false, 'modzzz_classified_user_form', array(
            'action_delete' => '_modzzz_classified_admin_delete',
        ), 'modzzz_classified_my_active', true, 7);
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_classified_my_active');
        return $this->_oTemplate->parseHtmlByName('my_classified_manage', $aVars);
    }    
}
