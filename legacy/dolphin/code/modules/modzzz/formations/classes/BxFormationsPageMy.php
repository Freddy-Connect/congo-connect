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

class BxFormationsPageMy extends BxDolPageView {	

    var $_oMain;
    var $_oTemplate;
    var $_oDb;
    var $_oConfig;
    var $_aProfile;

	function BxFormationsPageMy(&$oMain, &$aProfile) {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->_aProfile = $aProfile;
		parent::BxDolPageView('modzzz_formations_my');
	}

    function getBlockCode_Owner() {        
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch (bx_get('filter')) {
        case 'add_formation':
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_formations':
            $sContent = $this->getBlockCode_My ();
            break;            
        case 'pending_formations':
            $sContent = $this->getBlockCode_Pending ();
            break; 
        case 'expired_formations':
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

        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aMenu = array(
            _t('_modzzz_formations_block_submenu_main') => array('href' => $sBaseUrl, 'active' => !bx_get('filter')),
            _t('_modzzz_formations_block_submenu_add_formation') => array('href' => $sBaseUrl . '&filter=add_formation', 'active' => 'add_formation' == bx_get('filter')),
            _t('_modzzz_formations_block_submenu_manage_formations') => array('href' => $sBaseUrl . '&filter=manage_formations', 'active' => 'manage_formations' == bx_get('filter')),
            _t('_modzzz_formations_block_submenu_pending_formations') => array('href' => $sBaseUrl . '&filter=pending_formations', 'active' => 'pending_formations' == bx_get('filter')),
            _t('_modzzz_formations_block_submenu_expired_formations') => array('href' => $sBaseUrl . '&filter=expired_formations', 'active' => 'expired_formations' == $_REQUEST['filter']), 
		 _t('_modzzz_formations_block_submenu_orders') => array('href' => $sBaseUrl . '&filter=my_orders', 'active' => 'my_orders' == $_REQUEST['filter']),
		 			
		_t('_modzzz_formations_block_submenu_pending_invoices') => array('href' => $sBaseUrl . '&filter=my_pending_invoices', 'active' => 'my_pending_invoices' == $_REQUEST['filter']),



        );
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse() {

        modzzz_formations_import ('SearchResult');
        $o = new BxFormationsSearchResult('user', process_db_input ($this->_aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION));
        $o->aCurrent['rss'] = 0;

        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_modzzz_formations_page_title_my_formations');

        if ($o->isError) {
			 // Freddy Comment  return DesignBoxContent(_t('_modzzz_formations_block_users_formations'), MsgBox(_t('_Empty')), 1);
			  return DesignBoxContent( '','' );
        }

        if ($s = $o->processing()) {
            $this->_oTemplate->addCss ('unit.css');
            $this->_oTemplate->addCss ('main.css');            
            return $s;
        } else {
 // Freddy comment  return DesignBoxContent(_t('_modzzz_formations_block_users_formations'), MsgBox(_t('_Empty')), 1);
			 return DesignBoxContent( '','' );        }
    }
	
	
	
	/////////////////Freddy ajout
	 function getBlockCode_GererFormations() {
		 $aData = $this->aDataEntry;
		 $aProfileInfo = getProfileInfo($iProfileId);
			
		  $aFormationCount = $this->_oDb->getModzzzCountFormationsFormation($aProfileInfo['ID']);
		  
		
		 
		  
		  $sFormationEntries = '';
		$aEntries = array();
		
			//$oFormation = BxDolModule::getInstance('BxFormationsModule');
			$aFormations = $this->_oDb->getBusinessFormationsById($aProfileInfo['ID']);
			
		
		//////////

			$iIter = 1;
			$iCountFormations = count($aFormations);
			foreach($aFormations as $aEachFormation) {  
			
 $manage_applicants_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse_applications/' . $aEachFormation['uri'];
 $Application_count = $this->_oDb->getApplicationsCount($aEachFormation['id']);

			
			
			// Freddy pour afficher le nombre des candidatures au singulier ou au pluriel
        $application_single_plural = $this->_oDb->getApplicationsCount($aEachFormation['id']);
		if($application_single_plural<= '1'){
			$aApplications = _t('_modzzz_formations_form_unit_view_applications_single') ; 
		}
		else{
			$aApplications = _t('_modzzz_formations_form_unit_view_applications') ;  
		}
		//////////////////////////////////////
		
				$aEntries[] = array(  
					//'formation_url' => BX_DOL_URL_ROOT . $oFormation->_oConfig->getBaseUri() . 'view/' . $aEachFormation['uri'],
					'formation_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aEachFormation['uri'],
					
					'formation_title' => $aEachFormation['title'], 
					'Application_caption'=> $aApplications,
					'Application_count' => $Application_count,
					'manage_applicants_url' => $manage_applicants_url, 
					
					'bx_if:voir_candidats' => array( 
								'condition' => $Application_count >= 1 ,
								'content' => array(
					 'manage_applicants_url' => $manage_applicants_url, 
                     'voir_candidats' => '<i class="sys-icon eye"> &nbsp;</i>'.'&nbsp;'._t('_modzzz_formations_voir_candidats').'&nbsp;'. '<i class="sys-icon hand-o-left"></i>' ,
								), 
							),
					'spacer' => ($iCountFormations==$iIter) ? '' : '<br>'
					
				);
				$iIter++;
			}

			if($iCountFormations){
				$aVars = array('bx_repeat:entries' => $aEntries); 
				$sFormationEntries = $this->_oTemplate->parseHtmlByName('formation_entries', $aVars); 
			}
		
		 
     $aVars = array (  
	 
	 'FormationCount' => $aFormationCount,
	 
     'formations' => $sFormationEntries,
	 
	 'FirstName' => $aProfileInfo['FirstName'],
     );
	  return $this->_oTemplate->parseHtmlByName('entry_view_block_gerer_formations', $aVars);  
    }
	//////////////////////////////////////////////////////////////////
	
	 function getBlockCode_GererStages() {
		  $aData = $this->aDataEntry;
		 
		  $aProfileInfo = getProfileInfo($iProfileId);
		  $aStageCount = $this->_oDb->getModzzzCountFormationsStage($aProfileInfo['ID']);
		  
		    $sFormationEntries = '';
		$aEntries = array();
		
			//$oFormation = BxDolModule::getInstance('BxFormationsModule');
			$aFormations = $this->_oDb->getBusinessStageById($aProfileInfo['ID']);

			$iIter = 1;
			$iCountFormations = count($aFormations);
			foreach($aFormations as $aEachFormation) { 
			$Application_count = $this->_oDb->getApplicationsCount($aEachFormation['id']);
			 $manage_applicants_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse_applications/' . $aEachFormation['uri'];
			
			// Freddy pour afficher le nombre des candidatures au singulier ou au pluriel
        $application_single_plural = $this->_oDb->getApplicationsCount($aEachFormation['id']);
		if($application_single_plural<= '1'){
			$aApplications = _t('_modzzz_formations_form_unit_view_applications_single') ; 
		}
		else{
			$aApplications = _t('_modzzz_formations_form_unit_view_applications') ;  
		}
		//////////////////////////////////////
			 
				$aEntries[] = array(  
					//'formation_url' => BX_DOL_URL_ROOT . $oFormation->_oConfig->getBaseUri() . 'view/' . $aEachFormation['uri'],
					'stage_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aEachFormation['uri'],
					
					'stage_title' => $aEachFormation['title'], 
					'Application_caption'=> $aApplications,
					'Application_count' => $this->_oDb->getApplicationsCount($aEachFormation['id']),
					'manage_applicants_url' => $manage_applicants_url,
					'bx_if:voir_candidats' => array( 
								'condition' => $Application_count >= 1 ,
								'content' => array(
					 'manage_applicants_url' => $manage_applicants_url, 
                     'voir_candidats' => '<i class="sys-icon eye"> &nbsp;</i>'.'&nbsp;'._t('_modzzz_formations_voir_candidats').'&nbsp;'. '<i class="sys-icon hand-o-left"></i>' ,
								), 
							),
					'spacer' => ($iCountFormations==$iIter) ? '' : '<br>'
				);
				$iIter++;
			}

			if($iCountFormations){
				$aVars = array('bx_repeat:entries' => $aEntries); 
				$sFormationEntries = $this->_oTemplate->parseHtmlByName('stage_entries', $aVars); 
			}
		
     $aVars = array (  
	 
	
	 'StageCount' => $aStageCount,
	 'stages' => $sFormationEntries,
	 'FirstName' => $aProfileInfo['FirstName'],

     );
	  return $this->_oTemplate->parseHtmlByName('entry_view_block_gerer_stages', $aVars);  
    }
	
	
	////////////

   
	

    function getBlockCode_Main() {
        $iActive = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'approved');
        $iPending = $this->_oDb->getCountByAuthorAndStatus($this->_aProfile['ID'], 'pending');
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "browse/my";
        $aVars = array ('msg' => '');
        if ($iPending)
            $aVars['msg'] = sprintf(_t('_modzzz_formations_msg_you_have_pending_approval_formations'), $sBaseUrl . '&filter=pending_formations', $iPending);
        elseif (!$iActive)
            $aVars['msg'] = sprintf(_t('_modzzz_formations_msg_you_have_no_formations'), $sBaseUrl . '&filter=add_formation');
        else
            $aVars['msg'] = sprintf(_t('_modzzz_formations_msg_you_have_some_formations'), $sBaseUrl . '&filter=manage_formations', $iActive, $sBaseUrl . '&filter=add_formation');
        return $this->_oTemplate->parseHtmlByName('my_formations_main', $aVars);
    }

    function getBlockCode_Add() {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
        $this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my'); 
        $aVars = array ('form' => ob_get_clean(), 'id' => '');
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_formations_create_formation', $aVars);
    }

    function getBlockCode_Expired() {
        $sForm = $this->_oMain->_manageEntries ('my_expired', '', false, 'modzzz_formations_expired_user_form', array(
            'action_delete' => '_modzzz_formations_admin_delete',
        ), 'modzzz_formations_my_expired', false, 7);
        if (!$sForm)
            return MsgBox(_t('_modzzz_formation_Empty_formations_expire'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_formations_my_expired');
        return $this->_oTemplate->parseHtmlByName('my_formations_manage', $aVars); 
    }

    function getBlockCode_Pending() {
        $sForm = $this->_oMain->_manageEntries ('my_pending', '', false, 'modzzz_formations_pending_user_form', array(
            'action_delete' => '_modzzz_formations_admin_delete',
        ), 'modzzz_formations_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_modzzz_formation_Empty_formations_en_attente'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_formations_my_pending');
        return $this->_oTemplate->parseHtmlByName('my_formations_manage', $aVars); 
    }

	function getBlockCode_My() {
 
        $sForm = $this->_oMain->_manageEntries ('user', process_db_input ($this->_aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION), false, 'modzzz_formations_user_form', array(
            'action_delete' => '_modzzz_formations_admin_delete', 
        ), 'modzzz_formations_my_active', true, 7);
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_formations_my_active');
        return $this->_oTemplate->parseHtmlByName('my_formations_manage', $aVars);
    }    

	//futuristic function
    function _manageMyEntries ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 0) {

        bx_import ('SearchResult', $this->_oMain->_aModule);
        $sClass = $this->_oMain->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = 'unit_admin';

        if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;
 
		$o->aCurrent['restriction']['activeStatus']['value'] = array('approved','expired');
		$o->aCurrent['restriction']['activeStatus']['operator'] = 'in';

        $sPagination = $sActionsPanel = '';
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
        } elseif (!($sContent = $o->displayResultBlock())) {
            if ($isMsgBoxIfEmpty)
                $sContent = MsgBox(_t('_Empty'));
            else
                return '';
        } else {
            $sPagination = $sAjaxPaginationBlockId ? $o->showPaginationAjax($sAjaxPaginationBlockId) : $o->showPagination();
            $sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons);
        }

        $aVars = array (
            'form_name' => $sFormName,
            'content' => $sContent,
            'pagination' => $sPagination,
            'filter_panel' => $isFilter ? $o->showAdminFilterPanel(false !== bx_get($this->_sFilterName) ? bx_get($this->_sFilterName) : '', 'filter_input_id', 'filter_checkbox_id', $this->_sFilterName) : '',
            'actions_panel' => $sActionsPanel,
        );        
        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }

    function getBlockCode_Invoices() {
 
        $sForm = $this->_oMain->_manageOrders ('invoice', '', false, 'modzzz_formations_pending_user_form', array(
            'action_delete' => '_modzzz_formations_admin_delete',
        ), 'modzzz_formations_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_modzzz_formation_Empty_formations_commande_attente'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_formations_my_pending_invoices');
        return $this->_oTemplate->parseHtmlByName('my_formations_manage', $aVars); 
    }

    function getBlockCode_Orders() {
        $sForm = $this->_oMain->_manageOrders ('order', '', false, 'modzzz_formations_pending_user_form', array(), 'modzzz_formations_my_orders', false, 7, false);
        if (!$sForm)
            return MsgBox(_t('_modzzz_formation_Empty_formations_invoices'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_formations_my_orders');
        return $this->_oTemplate->parseHtmlByName('my_formations_manage', $aVars); 
    }



}
