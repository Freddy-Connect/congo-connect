<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Listing
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

class BxListingPageMy extends BxDolPageView {	

    var $_oMain;
    var $_oTemplate;
    var $_oDb;
    var $_oConfig;
    var $_aProfile;

	function BxListingPageMy(&$oMain, &$aProfile) {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->_aProfile = $aProfile;
		parent::BxDolPageView('modzzz_listing_my');
	}

    function getBlockCode_Owner() {        
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch ($_REQUEST['filter']) {
        case 'add_listing':
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_listing':
            $sContent = $this->getBlockCode_My ();
            break;            
        case 'pending_listing':
            $sContent = $this->getBlockCode_Pending ();
            break;   
        case 'joined_listing':
            $sContent = $this->getBlockCode_LinkJoined ();
            break;   
        case 'expired_listing':
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
            _t('_modzzz_listing_block_submenu_main') => array('href' => $sBaseUrl, 'active' => empty($_REQUEST['filter']) || !$_REQUEST['filter']),
			_t('_modzzz_listing_block_submenu_pending_invoices') => array('href' => $sBaseUrl . '&filter=my_pending_invoices', 'active' => 'my_pending_invoices' == $_REQUEST['filter']),
 
            _t('_modzzz_listing_block_submenu_orders') => array('href' => $sBaseUrl . '&filter=my_orders', 'active' => 'my_orders' == $_REQUEST['filter']),
       
		    _t('_modzzz_listing_block_submenu_add_listing') => array('href' => $sBaseUrl . '&filter=add_listing', 'active' => 'add_listing' == $_REQUEST['filter']),
            _t('_modzzz_listing_block_submenu_manage_listing') => array('href' => $sBaseUrl . '&filter=manage_listing', 'active' => 'manage_listing' == $_REQUEST['filter']),
            _t('_modzzz_listing_block_submenu_pending_listing') => array('href' => $sBaseUrl . '&filter=pending_listing', 'active' => 'pending_listing' == $_REQUEST['filter']), 

            _t('_modzzz_listing_block_submenu_joined_listing') => array('href' => $sBaseUrl . '&filter=joined_listing', 'active' => 'joined_listing' == $_REQUEST['filter']), 
 
            _t('_modzzz_listing_block_submenu_expired_listing') => array('href' => $sBaseUrl . '&filter=expired_listing', 'active' => 'expired_listing' == $_REQUEST['filter']), 

        );
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse() {

        modzzz_listing_import ('SearchResult');
        $o = new BxListingSearchResult('user', process_db_input ($this->_aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION));
        $o->aCurrent['rss'] = 0;

        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_modzzz_listing_page_title_my_listing');

        if ($o->isError) {
           // freddy modif return DesignBoxContent(_t('_modzzz_listing_block_users_listing'), MsgBox(_t('_Empty')), 1);
		   return ;
        }

        if ($s = $o->processing()) {

            $this->_oTemplate->addCss (array('unit.css', 'twig.css', 'main.css'));
			
            return $s;
        } else {
           // freddy modif  return DesignBoxContent(_t('_modzzz_listing_block_users_listing'), MsgBox(_t('_Empty')), 1);
		   return ;
        }
    }

    function getBlockCode_Main() {
        $iActive = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        if ($iPending)
            $aVars['msg'] = sprintf(_t('_modzzz_listing_msg_you_have_pending_approval_listing'), $sBaseUrl . '&filter=pending_listing', $iPending);
        elseif (!$iActive)
            $aVars['msg'] = sprintf(_t('_modzzz_listing_msg_you_have_no_listing'), $sBaseUrl . '&filter=add_listing');
        else
            $aVars['msg'] = sprintf(_t('_modzzz_listing_msg_you_have_some_listing'), $sBaseUrl . '&filter=manage_listing', $iActive, $sBaseUrl . '&filter=add_listing');
        return $this->_oTemplate->parseHtmlByName('my_listing_main', $aVars);
    }

    function getBlockCode_Add() {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
        $this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my'); 
        $aVars = array ('form' => ob_get_clean(), 'id' => '');
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_listing_create_listing', $aVars);
    }
 
    function getBlockCode_Expired() {
        $sForm = $this->_oMain->_manageEntries ('my_expired', '', false, 'modzzz_listing_expired_user_form', array(
            'action_delete' => '_modzzz_listing_admin_delete',
        ), 'modzzz_listing_my_expired', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty_expirer_business'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_listing_my_expired');
        return $this->_oTemplate->parseHtmlByName('my_listing_manage', $aVars); 
    }

    function getBlockCode_Pending() {
        $sForm = $this->_oMain->_manageEntries ('my_pending', '', false, 'modzzz_listing_pending_user_form', array(
            'action_delete' => '_modzzz_listing_admin_delete',
        ), 'modzzz_listing_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty_attente_validation'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_listing_my_pending');
        return $this->_oTemplate->parseHtmlByName('my_listing_manage', $aVars); 
    }
 
    function getBlockCode_LinkJoined() {
        $sForm = $this->_oMain->_manageEntries ('joined', process_db_input ($this->_aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION), false, 'modzzz_listing_joined_user_form', array(
            'action_unjoin' => '_modzzz_listing_action_title_leave_admin',
		), 'modzzz_listing_my_joined', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty_suivre_aucune_entreprise'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_listing_my_joined');
        return $this->_oTemplate->parseHtmlByName('my_listing_manage', $aVars); 
    }

    function getBlockCode_Joined() {
        $sForm = $this->_oMain->_manageEntries ('joined', process_db_input ($this->_aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION), false, 'modzzz_listing_joined_user_form', array(
            'action_unjoin' => '_modzzz_listing_action_title_leave_admin',
		), 'modzzz_listing_my_joined', false, 7);
 
        if (!$sForm)
            return DesignBoxContent(_t('_modzzz_listing_block_submenu_joined_listing'), MsgBox(_t('_Empty_suivre_aucune_entreprise')), 1);
        
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_listing_my_joined');
        $s = $this->_oTemplate->parseHtmlByName('my_listing_manage', $aVars); 
 
		return DesignBoxContent(_t('_modzzz_listing_block_submenu_joined_listing'), $s, 1); 
    }
  
    function getBlockCode_Invoices() {
 
        $sForm = $this->_oMain->_manageOrders ('invoice', '', false, 'modzzz_listing_pending_user_form', array(
            'action_delete' => '_modzzz_listing_admin_delete',
        ), 'modzzz_listing_my_pending', false, 7);
        if (!$sForm)
		    //freddy modif return MsgBox(_t('_Empty'));
            return MsgBox(_t('_Empty_aucune_commande'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_listing_my_pending_invoices');
        return $this->_oTemplate->parseHtmlByName('my_listing_manage', $aVars); 
    }

    function getBlockCode_Orders() {
        $sForm = $this->_oMain->_manageOrders ('order', '', false, 'modzzz_listing_pending_user_form', array(), 'modzzz_listing_my_orders', false, 7, false);
        if (!$sForm)
            return MsgBox(_t('_Empty_commande'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_listing_my_orders');
        return $this->_oTemplate->parseHtmlByName('my_listing_manage', $aVars); 
    }

	function getBlockCode_My() {
        $sForm = $this->_oMain->_manageEntries ('user', process_db_input ($this->_aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION), false, 'modzzz_listing_user_form', array(
            'action_delete' => '_modzzz_listing_admin_delete',
        ), 'modzzz_listing_my_active', true, 7);
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_listing_my_active');
        return $this->_oTemplate->parseHtmlByName('my_listing_manage', $aVars);
    }    
}
