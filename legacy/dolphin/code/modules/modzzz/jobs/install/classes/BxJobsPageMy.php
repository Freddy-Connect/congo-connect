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

class BxJobsPageMy extends BxDolPageView {	

    var $_oMain;
    var $_oTemplate;
    var $_oDb;
    var $_oConfig;
    var $_aProfile;

	function BxJobsPageMy(&$oMain, &$aProfile) {
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->_aProfile = $aProfile;
		parent::BxDolPageView('modzzz_jobs_my');
	}

    function getBlockCode_Owner() {        
        if (!$this->_oMain->_iProfileId || !$this->_aProfile)
            return '';

        $sContent = '';
        switch (bx_get('filter')) {
        case 'add_job':
            $sContent = $this->getBlockCode_Add ();
            break;
        case 'manage_jobs':
            $sContent = $this->getBlockCode_My ();
            break;            
        case 'pending_jobs':
            $sContent = $this->getBlockCode_Pending ();
            break; 
        case 'expired_jobs':
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
            _t('_modzzz_jobs_block_submenu_main') => array('href' => $sBaseUrl, 'active' => !bx_get('filter')),
            _t('_modzzz_jobs_block_submenu_add_job') => array('href' => $sBaseUrl . '&filter=add_job', 'active' => 'add_job' == bx_get('filter')),
            _t('_modzzz_jobs_block_submenu_manage_jobs') => array('href' => $sBaseUrl . '&filter=manage_jobs', 'active' => 'manage_jobs' == bx_get('filter')),
            _t('_modzzz_jobs_block_submenu_pending_jobs') => array('href' => $sBaseUrl . '&filter=pending_jobs', 'active' => 'pending_jobs' == bx_get('filter')),
            _t('_modzzz_jobs_block_submenu_expired_jobs') => array('href' => $sBaseUrl . '&filter=expired_jobs', 'active' => 'expired_jobs' == $_REQUEST['filter']), 

        );
        return array($sContent, $aMenu, '', '');
    }

    function getBlockCode_Browse() {

        modzzz_jobs_import ('SearchResult');
        $o = new BxJobsSearchResult('user', process_db_input ($this->_aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION));
        $o->aCurrent['rss'] = 0;

        $o->sBrowseUrl = "browse/my";
        $o->aCurrent['title'] = _t('_modzzz_jobs_page_title_my_jobs');

        if ($o->isError) {
			 // Freddy Comment  return DesignBoxContent(_t('_modzzz_jobs_block_users_jobs'), MsgBox(_t('_Empty')), 1);
			  return DesignBoxContent( '','' );
        }

        if ($s = $o->processing()) {
            $this->_oTemplate->addCss ('unit.css');
            $this->_oTemplate->addCss ('main.css');            
            return $s;
        } else {
 // Freddy comment  return DesignBoxContent(_t('_modzzz_jobs_block_users_jobs'), MsgBox(_t('_Empty')), 1);
			 return DesignBoxContent( '','' );        }
    }
	
	
	
	/////////////////Freddy ajout
	 function getBlockCode_GererJobs() {
		 $aData = $this->aDataEntry;
		 $aProfileInfo = getProfileInfo($iProfileId);
			
		  $aEmploiCount = $this->_oDb->getModzzzCountJobsEmploi($aProfileInfo['ID']);
		  
		
		 
		  
		  $sJobEntries = '';
		$aEntries = array();
		
			//$oJob = BxDolModule::getInstance('BxJobsModule');
			$aJobs = $this->_oDb->getBusinessJobsById($aProfileInfo['ID']);
			
		
		//////////

			$iIter = 1;
			$iCountJobs = count($aJobs);
			foreach($aJobs as $aEachJob) {  
			
 $manage_applicants_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse_applications/' . $aEachJob['uri'];
 $Application_count = $this->_oDb->getApplicationsCount($aEachJob['id']);

			
			
			// Freddy pour afficher le nombre des candidatures au singulier ou au pluriel
        $application_single_plural = $this->_oDb->getApplicationsCount($aEachJob['id']);
		if($application_single_plural<= '1'){
			$aApplications = _t('_modzzz_jobs_form_unit_view_applications_single') ; 
		}
		else{
			$aApplications = _t('_modzzz_jobs_form_unit_view_applications') ;  
		}
		//////////////////////////////////////
		
				$aEntries[] = array(  
					//'job_url' => BX_DOL_URL_ROOT . $oJob->_oConfig->getBaseUri() . 'view/' . $aEachJob['uri'],
					'job_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aEachJob['uri'],
					
					'job_title' => $aEachJob['title'], 
					'Application_caption'=> $aApplications,
					'Application_count' => $Application_count,
					'manage_applicants_url' => $manage_applicants_url, 
					
					'bx_if:voir_candidats' => array( 
								'condition' => $Application_count >= 1 ,
								'content' => array(
					 'manage_applicants_url' => $manage_applicants_url, 
                     'voir_candidats' => '<i class="sys-icon eye"> &nbsp;</i>'.'&nbsp;'._t('_modzzz_jobs_voir_candidats').'&nbsp;'. '<i class="sys-icon hand-o-left"></i>' ,
								), 
							),
					'spacer' => ($iCountJobs==$iIter) ? '' : '<br>'
					
				);
				$iIter++;
			}

			if($iCountJobs){
				$aVars = array('bx_repeat:entries' => $aEntries); 
				$sJobEntries = $this->_oTemplate->parseHtmlByName('job_entries', $aVars); 
			}
		
		 
     $aVars = array (  
	 
	 'EmploiCount' => $aEmploiCount,
	 
     'jobs' => $sJobEntries,
	 
	 'FirstName' => $aProfileInfo['FirstName'],
     );
	  return $this->_oTemplate->parseHtmlByName('entry_view_block_gerer_emplois', $aVars);  
    }
	//////////////////////////////////////////////////////////////////
	
	 function getBlockCode_GererStages() {
		  $aData = $this->aDataEntry;
		 
		  $aProfileInfo = getProfileInfo($iProfileId);
		  $aStageCount = $this->_oDb->getModzzzCountJobsStage($aProfileInfo['ID']);
		  
		    $sJobEntries = '';
		$aEntries = array();
		
			//$oJob = BxDolModule::getInstance('BxJobsModule');
			$aJobs = $this->_oDb->getBusinessStageById($aProfileInfo['ID']);

			$iIter = 1;
			$iCountJobs = count($aJobs);
			foreach($aJobs as $aEachJob) { 
			$Application_count = $this->_oDb->getApplicationsCount($aEachJob['id']);
			 $manage_applicants_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse_applications/' . $aEachJob['uri'];
			
			// Freddy pour afficher le nombre des candidatures au singulier ou au pluriel
        $application_single_plural = $this->_oDb->getApplicationsCount($aEachJob['id']);
		if($application_single_plural<= '1'){
			$aApplications = _t('_modzzz_jobs_form_unit_view_applications_single') ; 
		}
		else{
			$aApplications = _t('_modzzz_jobs_form_unit_view_applications') ;  
		}
		//////////////////////////////////////
			 
				$aEntries[] = array(  
					//'job_url' => BX_DOL_URL_ROOT . $oJob->_oConfig->getBaseUri() . 'view/' . $aEachJob['uri'],
					'stage_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aEachJob['uri'],
					
					'stage_title' => $aEachJob['title'], 
					'Application_caption'=> $aApplications,
					'Application_count' => $this->_oDb->getApplicationsCount($aEachJob['id']),
					'manage_applicants_url' => $manage_applicants_url,
					'bx_if:voir_candidats' => array( 
								'condition' => $Application_count >= 1 ,
								'content' => array(
					 'manage_applicants_url' => $manage_applicants_url, 
                     'voir_candidats' => '<i class="sys-icon eye"> &nbsp;</i>'.'&nbsp;'._t('_modzzz_jobs_voir_candidats').'&nbsp;'. '<i class="sys-icon hand-o-left"></i>' ,
								), 
							),
					'spacer' => ($iCountJobs==$iIter) ? '' : '<br>'
				);
				$iIter++;
			}

			if($iCountJobs){
				$aVars = array('bx_repeat:entries' => $aEntries); 
				$sJobEntries = $this->_oTemplate->parseHtmlByName('stage_entries', $aVars); 
			}
		
     $aVars = array (  
	 
	
	 'StageCount' => $aStageCount,
	 'stages' => $sJobEntries,
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
            $aVars['msg'] = sprintf(_t('_modzzz_jobs_msg_you_have_pending_approval_jobs'), $sBaseUrl . '&filter=pending_jobs', $iPending);
        elseif (!$iActive)
            $aVars['msg'] = sprintf(_t('_modzzz_jobs_msg_you_have_no_jobs'), $sBaseUrl . '&filter=add_job');
        else
            $aVars['msg'] = sprintf(_t('_modzzz_jobs_msg_you_have_some_jobs'), $sBaseUrl . '&filter=manage_jobs', $iActive, $sBaseUrl . '&filter=add_job');
        return $this->_oTemplate->parseHtmlByName('my_jobs_main', $aVars);
    }

    function getBlockCode_Add() {
        if (!$this->_oMain->isAllowedAdd()) {
            return MsgBox(_t('_Access denied'));
        }
        ob_start();
        $this->_oMain->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my'); 
        $aVars = array ('form' => ob_get_clean(), 'id' => '');
        $this->_oTemplate->addCss ('forms_extra.css');
        return $this->_oTemplate->parseHtmlByName('my_jobs_create_job', $aVars);
    }

    function getBlockCode_Expired() {
        $sForm = $this->_oMain->_manageEntries ('my_expired', '', false, 'modzzz_jobs_expired_user_form', array(
            'action_delete' => '_modzzz_jobs_admin_delete',
        ), 'modzzz_jobs_my_expired', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_jobs_my_expired');
        return $this->_oTemplate->parseHtmlByName('my_jobs_manage', $aVars); 
    }

    function getBlockCode_Pending() {
        $sForm = $this->_oMain->_manageEntries ('my_pending', '', false, 'modzzz_jobs_pending_user_form', array(
            'action_delete' => '_modzzz_jobs_admin_delete',
        ), 'modzzz_jobs_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_jobs_my_pending');
        return $this->_oTemplate->parseHtmlByName('my_jobs_manage', $aVars); 
    }

	function getBlockCode_My() {
 
        $sForm = $this->_oMain->_manageEntries ('user', process_db_input ($this->_aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION), false, 'modzzz_jobs_user_form', array(
            'action_delete' => '_modzzz_jobs_admin_delete', 
        ), 'modzzz_jobs_my_active', true, 7);
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_jobs_my_active');
        return $this->_oTemplate->parseHtmlByName('my_jobs_manage', $aVars);
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
 
        $sForm = $this->_oMain->_manageOrders ('invoice', '', false, 'modzzz_jobs_pending_user_form', array(
            'action_delete' => '_modzzz_jobs_admin_delete',
        ), 'modzzz_jobs_my_pending', false, 7);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_jobs_my_pending_invoices');
        return $this->_oTemplate->parseHtmlByName('my_jobs_manage', $aVars); 
    }

    function getBlockCode_Orders() {
        $sForm = $this->_oMain->_manageOrders ('order', '', false, 'modzzz_jobs_pending_user_form', array(), 'modzzz_jobs_my_orders', false, 7, false);
        if (!$sForm)
            return MsgBox(_t('_Empty'));
        $aVars = array ('form' => $sForm, 'id' => 'modzzz_jobs_my_orders');
        return $this->_oTemplate->parseHtmlByName('my_jobs_manage', $aVars); 
    }



}
