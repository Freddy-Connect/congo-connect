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

function modzzz_jobs_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'jobs') {
        $oMain = BxDolModule::getInstance('BxJobsModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxTemplSearchResult');

define ('BX_JOBS_PHOTOS_CAT', 'Jobs');
define ('BX_JOBS_PHOTOS_TAG', 'jobs');

define ('BX_JOBS_VIDEOS_CAT', 'Jobs');
define ('BX_JOBS_VIDEOS_TAG', 'jobs');

define ('BX_JOBS_SOUNDS_CAT', 'Jobs');
define ('BX_JOBS_SOUNDS_TAG', 'jobs');

define ('BX_JOBS_FILES_CAT', 'Jobs');
define ('BX_JOBS_FILES_TAG', 'jobs');
define ('BX_JOBS_MAX_FANS', 1000);
 

/*
 * Jobs module
 *
 * This module allow users to create user's jobs, 
 * users can rate, comment and discuss job.
 * Job can have photos, videos, sounds and files, uploaded
 * by job's admins.
 *
 * 
 *
 * Profile's Wall:
 * 'add job' event is displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new job was created
 * change - job was chaned
 * rate - somebody rated job
 * commentPost - somebody posted comment in job
 *
 *
 *
 * Memberships/ACL:
 * jobs view job - BX_JOBS_VIEW_JOB
 * jobs browse - BX_JOBS_BROWSE
 * jobs search - BX_JOBS_SEARCH
 * jobs add job - BX_JOBS_ADD_JOB
 * jobs apply - BX_JOBS_APPLY
 * jobs comments delete and edit - BX_JOBS_COMMENTS_DELETE_AND_EDIT
 * jobs edit any job - BX_JOBS_EDIT_ANY_JOB
 * jobs delete any job - BX_JOBS_DELETE_ANY_JOB
 * jobs mark as featured - BX_JOBS_MARK_AS_FEATURED
 * jobs approve jobs - BX_JOBS_APPROVE_JOBS
 * jobs broadcast message - BX_JOBS_BROADCAST_MESSAGE
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different jobs
 * @see BxJobsModule::serviceHomepageBlock
 * BxDolService::call('jobs', 'homepage_block', array());
 *
 * Profile block with user's jobs
 * @see BxJobsModule::serviceProfileBlock
 * BxDolService::call('jobs', 'profile_block', array($iProfileId));
 *
 * Job's forum permissions (for internal usage only)
 * @see BxJobsModule::serviceGetForumPermission
 * BxDolService::call('jobs', 'get_forum_permission', array($iMemberId, $iForumId));
 *
 * Member menu item for jobs (for internal usage only)
 * @see BxJobsModule::serviceGetMemberMenuItem
 * BxDolService::call('jobs', 'get_member_menu_item', array());
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'modzzz_jobs'
 * The following alerts are rised
 *
 * 
 *
 *  add - new job was added
 *      $iObjectId - job id
 *      $iSenderId - creator of a job
 *      $aExtras['Status'] - status of added job
 *
 *  change - job's info was changed
 *      $iObjectId - job id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed job
 *
 *  delete - job was deleted
 *      $iObjectId - job id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - job was marked/unmarked as featured
 *      $iObjectId - job id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if job was marked as featured and 0 - if job was removed from featured 
 *
 */
class BxJobsModule extends BxDolTwigModule {

    var $_oPrivacy;
    var $_aQuickCache = array ();

    function BxJobsModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'filter';
        $this->_sPrefix = 'modzzz_jobs';

        bx_import ('Privacy', $aModule);
        $this->_oPrivacy = new BxJobsPrivacy($this);

        bx_import ('CompanyPrivacy', $aModule);
        $this->_oCompanyPrivacy = new BxJobsCompanyPrivacy($this);
  
	    $this->_oConfig->init($this->_oDb);

        $GLOBALS['oBxJobsModule'] = &$this;
 
 		//reloads subcategories on Add form
		if($_GET['ajax']=='cat') { 
			$iParentId = $_GET['parent'];
			echo $this->_oDb->getAjaxCategoryOptions($iParentId);
			exit;
		}  

		if($_GET['ajax']=='state') { 
			$sCountryCode = $_GET['country'];
			echo $this->_oDb->getStateOptions($sCountryCode);
			exit;
		}
		
		if($_GET['ajax']=='package') { 
			$iPackageId = (int)$_GET['package'];
			echo $this->_oTemplate->getFormPackageDesc($iPackageId);
			exit;
		}

    }

    function actionHome () {
        parent::_actionHome(_t('_modzzz_jobs_page_title_home'));
    }

    function actionFiles ($sUri) {
        parent::_actionFiles ($sUri, _t('_modzzz_jobs_page_title_files'));
    }

    function actionSounds ($sUri) {
        parent::_actionSounds ($sUri, _t('_modzzz_jobs_page_title_sounds'));
    }

    function actionVideos ($sUri) {
        parent::_actionVideos ($sUri, _t('_modzzz_jobs_page_title_videos'));
    }

    function actionPhotos ($sUri) {
        parent::_actionPhotos ($sUri, _t('_modzzz_jobs_page_title_photos'));
    }

    function actionComments ($sUri) {
        parent::_actionComments ($sUri, _t('_modzzz_jobs_page_title_comments'));
    }
 
    function actionBrowseFans ($sUri) {
        parent::_actionBrowseFans ($sUri, 'isAllowedViewFans', 'getFansBrowse', $this->_oDb->getParam('modzzz_jobs_perpage_browse_fans'), 'browse_fans/', _t('_modzzz_jobs_page_title_fans'));
    }
 
    function actionBrowseApplications ($sUri, $iEntryId=0, $iId=0, $sAction='') {
       
	   if($sAction=='remove'){
			if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
				$this->_oTemplate->displayPageNotFound ();
				return;
			}   

			if (!$this->isAllowedViewApplications($aDataEntry)) {            
				$this->_oTemplate->displayAccessDenied ();
				return;
			}  

		   $this->_oDb->removeApplications($iEntryId, $iId);
	   }
	    
	   $this->_actionApplications ($sUri, $this->_oDb->getParam('modzzz_jobs_perpage_browse_applications'), 'browse_applications/', _t('_modzzz_jobs_page_title_applications'));
    }
 
    function actionPopupLetter ($iId) {
		
		$aProfile = getProfileInfo($this->_oMain->_iProfileId);
		$aFirstName = $aProfile['FirstName'];  
		$aLastName = $aProfile['LastName']; 

		$aApplication = $this->_oDb->getApplication($iId);
		$sLetter = $aApplication['letter']; 
 
		$aVars = array(
			'letter' => $sLetter,   
		);
 
        $sContent = $this->_oTemplate->parseHtmlByName("cover_letter", $aVars);
	
		$aVarsPopup = array (
		     // Freddy ajout . $aFirstName .' '. $aLastName
            'title' => '<div style="text-transform:none">'._t('_modzzz_jobs_cover_letter'). ' '.$aFirstName .' '. $aLastName .'</div>',
            'content' => '<div class="cover_lecture">'. $sContent .'</div>',
        );        
        
		echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true);  
    }   
	
	 
	 //Freddy modif pour personnaliser le message 
	 function displayNoCandidature ()
    {
        $sTitle = _t('_modzzz_jobs_aucune_candidature');

        $GLOBALS['_page'] = array(
            'name_index' => 0,
            'header' => $sTitle,
            'header_text' => $sTitle
        );
        $GLOBALS['_page_cont'][0]['page_main_code'] = MsgBox($sTitle);

        PageCode();
        exit;
    }

    function _actionApplications ($sUri, $iPerPage, $sUrlBrowse, $sTitle) {
         
		 
		 
      
		$this->_oTemplate->addCss ('view.css');
	   $this->_oTemplate->addCss ('main.css');
	   
		
		

        if (!($aDataEntry = $this->_preProductTabs($sUri, $sTitle))) {
            return;
        }

        if (!$this->isAllowedViewApplications($aDataEntry)) {            
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;
 
        $aProfiles = array ();
        $iNum = $this->_oDb->getApplications($aProfiles, $aDataEntry[$this->_oDb->_sFieldId], $iStart, $iPerPage);
        if (!$iNum || !$aProfiles) {
           //Freddy modif pour personnaliser le message   $this->displayNoCandidature ();
		   // $this->_oTemplate->displayNoData ();
		   $this->displayNoCandidature ();
			
            return;
        }
        $iPages = ceil($iNum / $iPerPage);
 

        $sMainContent = '';
        foreach ($aProfiles as $aEachApplicant) { 

			
			$sComposeUrl = BX_DOL_URL_ROOT . 'mail.php?mode=compose&recipient_id=' . $aEachApplicant['member_id'];
			$sRemoveUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse_applications/' . $sUri . '/' . $aDataEntry[$this->_oDb->_sFieldId] . '/' . $aEachApplicant['id'] . '/remove';

			$iResumeId = (int)$aEachApplicant['resume_id'];
			if($iResumeId){
				$oResume = BxDolModule::getInstance('BxResumeModule');
				$aResume = $oResume->_oDb->getEntryById($iResumeId);
				$sResumeLink = BX_DOL_URL_ROOT . $oResume->_oConfig->getBaseUri() . 'view/' . $aResume['uri'];
			}

			if($aEachApplicant['resume']){
				$sDownloadLink = $this->_oDb->getResumeLink($aEachApplicant['resume']);
			}
			
			//Freddy ajout Befirend
		$iUserId = getLoggedId();
		$iId = $aEachApplicant['member_id'];
		$aFriends = getMyFriendsEx($iUserId);
		$aFriendsIds = array_keys($aFriends);
		// [END] Freddy ajout Befirend
		
		
		
		//$aProfile = getProfileInfo($this->_oMain->_iProfileId);
		 $aProfile = $this->_oDb->getProfile($iId);
		$aFirstName = $aProfile['FirstName'];  
		
		//$suidTelechargement =  $this->_iProfileId; 
		$suidTelechargement =  $aProfile['ID']; 
		

			$aVars = array (
			
			 //Freddy ajout Befirend
	        'bx_if:befriend' => array(
	  'condition' => !in_array($iId, $aFriendsIds) && !isFriendRequest($iUserId, $iId) &&  !$this->isEntryAdmin($aData),
	        'content' => array(
	        'id' => $iId
					    ),
				           ),
		// [END] Freddy ajout Befirend
		        
			    
			
				'thumb' => $GLOBALS['oFunctions']->getMemberThumbnail($aEachApplicant['member_id'], 'left', true),
             
			 //Freddy ajout : Identifiant de l'applicant --- $suid
			    //'uid' => $aEachApplicant['member_id'],
				'uid' => $suidTelechargement,
			//////////////////////////////////////////
			
				'bx_if:online' => array (
					'condition' => $iResumeId,
					'content' => array (
						'online_link' => $sResumeLink 
 					),
				),

				'bx_if:download' => array (
					'condition' => $aEachApplicant['resume'],
					'content' => array (
						'download_link' => $sDownloadLink 
 					),
				),
 
				'letter' => $aEachApplicant['letter'],
				//freddy modif 'posted' => date("d/m/Y H:i ",$aEachApplicant['created']),
				'posted' => date("d/m/Y",$aEachApplicant['created']),
				'compose_url' => $sComposeUrl,
				'FirstName' => $aFirstName,
				'remove_url' => $sRemoveUrl,
				// Freddy modif
				//'letter_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'popup_letter/'.$aEachApplicant['id'],
				
				'bx_if:letter_url' => array (
					'condition' => $aEachApplicant['letter'],
					'content' => array (
						'letter_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'popup_letter/'.$aEachApplicant['id'],
						'FirstName' => $aFirstName,
 					),
				),
				
				/////////Freddy add Email and Telephone
				'Email' => $aEachApplicant['Email'],
				'Telephone' => $aEachApplicant['telephone'],
				////////////////////////////////////
			);
	 
			$sMainContent .= $this->_oTemplate->parseHtmlByName('entry_view_each_applicant', $aVars);   
        }
        $sRet  = $GLOBALS['oFunctions']->centerContent($sMainContent, '.searchrow_block_simple');
        $sRet .= '<div class="clear_both"></div>';        

        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . $sUrlBrowse . $aDataEntry[$this->_oDb->_sFieldUri];
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');

        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sUrlStart . 'page={page}&per_page={per_page}' . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) : ''),
            'count' => $iNum,

            'per_page' => $iPerPage,
            'page' => $iPage,
            'per_page_changer' => false,
            'page_reloader' => true,
            'on_change_page' => '',
            'on_change_per_page' => "document.location='" . $sUrlStart . "page=1&per_page=' + this.value + '" . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) ."';": "';"),
        ));

        $sRet .= $oPaginate->getPaginate();

        $this->_oTemplate->pageStart();

        echo DesignBoxContent ($sTitle, $sRet, 1);

        $this->_oTemplate->pageCode($sTitle, false, false);
    }
	 
	function isValidAccess($iEntryId, $iProfileId, $sAction) {
		$iEntryId = (int)$iEntryId;
		$iProfileId = (int)$iProfileId;

		$aDataEntry = $this->_oDb->getEntryById($iEntryId);
		if($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
			return true;

		if(getParam('modzzz_jobs_post_credits')!='on' && getParam('modzzz_jobs_post_points')!='on')
			return true;

		return $this->_oDb->isValidAccess($iEntryId, $iProfileId, $sAction);
	}
	
	

    function actionView ($sUri) {
  
  		$aDataEntry = $this->_oDb->getEntryByUri($sUri);
		$iEntryId = (int)$aDataEntry['id'];

		if($this->isValidAccess($iEntryId, $this->_iProfileId, 'view')){ 
			$this->_oTemplate->addCss ('jquery.countdown.css');
			$this->_oTemplate->addJs ('jquery.countdown.js');
			

			parent::_actionView ($sUri, _t('_modzzz_jobs_msg_pending_approval'));
		}else{
			$this->showViewPaymentForm ($sUri); 
		}
    }

    function showViewPaymentForm ($sUri) {
  
        if (!($aDataEntry = $this->_preProductTabs($sUri))){
            $this->_oTemplate->displayAccessDenied (); 
            return; 
		}

        $this->_oTemplate->pageStart();

		//$aDataEntry = $this->_oDb->getEntryByUri($sUri);
		$sTitle = $aDataEntry['title'];
		$iEntryId = (int)$aDataEntry['id'];

		$sCode = $sBuyPointsLink = $sBuyCreditsLink = '';
		$iViewCost = $iMoneyBalance = 0;
  
		if(getParam('modzzz_jobs_view_credits')=='on'){ 
 
			$sPaymentUnit = 'credits'; 
			$sMoneyBalanceLabelC = _t("_modzzz_jobs_form_caption_credits_balance");
			$sMoneyTypeC = _t("_modzzz_jobs_credits");

			$oCredit = BxDolModule::getInstance('BxCreditModule');  
			$sBuyCreditsLink = BX_DOL_URL_ROOT . $oCredit->_oConfig->getBaseUri() . 'purchase_credits'; 
			$iMoneyBalance = (int)$oCredit->getMemberCredits($this->_iProfileId);
			$iViewCost = (int)$oCredit->_oDb->getActionValue('modzzz_jobs', 'view');
			if($iMoneyBalance < $iViewCost){
				$sCode = _t('_modzzz_jobs_msg_insufficient_credits_view', number_format($iMoneyBalance,0), number_format($iViewCost,0));
				$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyCreditsLink);
 			}
		}elseif(getParam('modzzz_jobs_view_points')=='on'){
			
 			$sPaymentUnit = 'points';
			$sMoneyBalanceLabelC = _t("_modzzz_jobs_form_caption_points_balance");
			$sMoneyTypeC = _t("_modzzz_jobs_points");

			$oPoint = BxDolModule::getInstance('BxPointModule');   
			$sBuyPointsLink = BX_DOL_URL_ROOT . $oPoint->_oConfig->getBaseUri() . 'purchase_points'; 
			$iMoneyBalance = $oPoint->_oDb->getMemberPoints($this->_iProfileId);
			$iViewCost = (int)$oPoint->_oDb->getActionValue($this->_iProfileId, 'modzzz_jobs', 'view');
			if($iMoneyBalance < $iViewCost){
				$sCode = _t('_modzzz_jobs_msg_insufficient_points_view', number_format($iMoneyBalance,0), number_format($iViewCost,0));
				$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyPointsLink);
 			}
		}
 
		if($sCode) {
			$sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array('content' => $sCode)); 
			echo $sCode;

			$this->_oTemplate->addCss ('view.css');
			$this->_oTemplate->addCss ('main.css');
			$this->_oTemplate->pageCode($aDataEntry[$this->_oDb->_sFieldTitle], true, false);
			return; 
		}
    
		$aCustomForm = array(
  
			'form_attrs' => array(
				'id' => 'jobs_form',
				'name' => 'jobs_form',
                'action' => '',
				'method' => 'post',
			),

			'params' => array (
				'db' => array(
					'submit_name' => 'submit_form', 
				),
			),

			'inputs' => array (

				'header_info' => array(
					'type' => 'block_header',
					'caption' => _t("_modzzz_jobs_form_caption_view", $sTitle),
				), 
				'money_balance' => array(
					'type' => 'custom',
					'caption' => $sMoneyBalanceLabelC,
					'content' => number_format($iMoneyBalance,0) .' '. $sMoneyTypeC,
				), 
				'view_cost' => array(
					'type' => 'custom',
 					'caption' => _t("_modzzz_jobs_form_caption_cost_to_view"),
					'content' => number_format($iViewCost,0) .' '. $sMoneyTypeC,
				),    
				'submit_form' => array(
					'type' => 'submit',
					'name' => 'submit_form',
					'value' => _t("_modzzz_jobs_form_caption_submit"),
				),
			)
		);
		  
		$oForm = new BxTemplFormView($aCustomForm);
		$oForm->initChecker();

		if ( $oForm->isSubmittedAndValid() ) {
	  
			if(getParam('modzzz_jobs_view_credits')=='on'){ 
	 
				if($iMoneyBalance < $iViewCost){
					$sCode = _t('_modzzz_jobs_msg_insufficient_credits_view', number_format($iMoneyBalance,0), number_format($iViewCost,0));
					$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyCreditsLink);
					echo MsgBox($sCode);
					return;
				}
			}elseif(getParam('modzzz_jobs_view_points')=='on'){
	 
				if($iMoneyBalance < $iViewCost){
					$sCode = _t('_modzzz_jobs_msg_insufficient_points_view', number_format($iMoneyBalance,0), number_format($iViewCost,0));
					$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyPointsLink);
					echo MsgBox($sCode);
					return;
				}
			}
  
			$bSuccess = $this->_oDb->deductPayment('view', $sPaymentUnit, $this->_iProfileId, $iEntryId, 0, $iViewCost);  
 
			parent::_actionView ($sUri, _t('_modzzz_jobs_msg_pending_approval'));
		 
		}else{  
			echo $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array('content' => $oForm->getCode()));  
		}
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode($aDataEntry[$this->_oDb->_sFieldTitle], true, false);
	}
 
    function actionCompanyView ($sUri) {
        $this->_actionCompanyView ($sUri, _t('_modzzz_jobs_msg_pending_approval'));
    }

    function _actionCompanyView ($sUri, $sMsgPendingApproval) {

        if (!($aDataEntry = $this->_oDb->getCompanyEntryByUri($sUri)))
            return;

        $this->_oTemplate->pageStart();

        bx_import ('CompanyPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'CompanyPageView';
        $oPage = new $sClass ($this, $aDataEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('CompanyCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'CompanyCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aDataEntry[$this->_oDb->_sFieldDescription]), 0, 255));
        $this->_oTemplate->addPageKeywords ($aDataEntry['company_tags']);

        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aDataEntry['company_name'], false, false);

        bx_import ('BxDolViews');
        new BxDolViews('modzzz_jobs_company', $aDataEntry[$this->_oDb->_sFieldId]);
    }



    function actionUploadPhotos ($sUri) {        
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadPhotos', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_jobs_page_title_upload_photos'));
    }

    function actionUploadVideos ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadVideos', 'videos', array ('videos_choice', 'videos_upload'), _t('_modzzz_jobs_page_title_upload_videos'));
    }

    function actionUploadSounds ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadSounds', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_modzzz_jobs_page_title_upload_sounds')); 
    }

    function actionUploadFiles ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadFiles', 'files', array ('files_choice', 'files_upload'), _t('_modzzz_jobs_page_title_upload_files')); 
    }

    function actionBroadcast ($iEntryId) {
        parent::_actionBroadcast ($iEntryId, _t('_modzzz_jobs_page_title_broadcast'), _t('_modzzz_jobs_msg_broadcast_no_recipients'), _t('_modzzz_jobs_msg_broadcast_message_sent'));
    }

    function actionBroadcastApplicants ($iEntryId) {
        $this->_actionBroadcastApplicants ($iEntryId, _t('_modzzz_jobs_applicants_page_title_broadcast'), _t('_modzzz_jobs_applicants_msg_broadcast_no_recipients'), _t('_modzzz_jobs_applicants_msg_broadcast_message_sent'));
    }

    function _actionBroadcastApplicants ($iEntryId, $sTitle, $sMsgNoRecipients, $sMsgSent) {
		global $tmpl;
		require_once( BX_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $tmpl . '/scripts/BxTemplMailBox.php');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if (!$this->isAllowedBroadcast($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$sTitle => '',
		));

        $aRecipients = $this->_oDb->getJobApplicants ($iEntryId);
        if (!$aRecipients) {
            echo MsgBox ($sMsgNoRecipients);
            $this->_oTemplate->pageCode($sMsgNoRecipients, true, true);
            return;
        }

        bx_import ('ApplicantsFormBroadcast', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ApplicantsFormBroadcast';
        $oForm = new $sClass ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
            
            $oEmailTemplate = new BxDolEmailTemplates();
            if (!$oEmailTemplate) {
                $this->_oTemplate->displayErrorOccured();
                return;
            }
            $aTemplate = $oEmailTemplate->getTemplate($this->_sPrefix . '_broadcast_applicants'); 
            $aTemplateVars = array (
                'BroadcastTitle' => $this->_oDb->unescape($oForm->getCleanValue ('title')),
                'BroadcastMessage' => nl2br($this->_oDb->unescape($oForm->getCleanValue ('message'))),
                'EntryTitle' => $aDataEntry[$this->_oDb->_sFieldTitle],
                'EntryUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],                
            );
  
            $iSentMailsCounter = 0;            
            foreach ($aRecipients as $aProfile) {	

       	        $iSentMailsCounter += sendMail($aProfile['Email'], $aTemplate['Subject'], $aTemplate['Body'], $aProfile['ID'], $aTemplateVars);

				$this->broadCastToInbox($aProfile, $aTemplate, $aTemplateVars);  
            }
            if (!$iSentMailsCounter) {
                $this->_oTemplate->displayErrorOccured();
                return;
            }

            echo MsgBox ($sMsgSent);

            $this->isAllowedBroadcast($aDataEntry, true); // perform send broadcast message action             
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode($sMsgSent, true, true);
            return;
        } 

        echo $oForm->getCode ();

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode($sTitle);
    }
 
    function  broadCastToInbox($aProfile, $aTemplate, $aTemplateVars){

		$aMailBoxSettings = array
		(
			'member_id'	 =>  $this->_iProfileId, 
			'recipient_id'	 => $aProfile['ID'], 
			'messages_types'	 =>  'mail',  
		);

		$aComposeSettings = array
		(
			'send_copy' => false , 
			'send_copy_to_me' => false , 
			'notification' => false ,
		);
		$oMailBox = new BxTemplMailBox('mail_page', $aMailBoxSettings);

		$sMessageBody = $aTemplate['Body'];
		$sMessageBody = str_replace("<NickName>", getNickName($aProfile['ID']), $sMessageBody);
		$sMessageBody = str_replace("<EntryUrl>", $aTemplateVars['EntryUrl'], $sMessageBody);
		$sMessageBody = str_replace("<EntryTitle>", $aTemplateVars['EntryTitle'], $sMessageBody);
		$sMessageBody = str_replace("<BroadcastMessage>", $aTemplateVars['BroadcastMessage'], $sMessageBody);
	  
		$oMailBox -> iWaitMinutes = 0;//turn off anti-spam
		$oMailBox -> sendMessage($aTemplateVars['BroadcastTitle'], $sMessageBody, $aProfile['ID'], $aComposeSettings); 

    }
	
	//[begin] favorites
    function isAllowedMarkAsFavorite ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_JOBS_MARK_AS_FAVORITE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

	function isFavorite($iProfileId, $iEntryId){
		return $this->_oDb->isFavorite($iProfileId, $iEntryId);
	}
 
    function actionMarkFavorite ($iEntryId) {
        $this->_actionMarkFavorite ($iEntryId, _t('_modzzz_jobs_msg_added_to_favorite'), _t('_modzzz_jobs_msg_removed_from_favorite'));
    }

    function _actionMarkFavorite ($iEntryId, $sMsgSuccessAdd, $sMsgSuccessRemove) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedMarkAsFavorite($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->markAsFavorite($this->_iProfileId, $iEntryId)) {
            $this->isAllowedMarkAsFavorite($aDataEntry, true); // perform action
            $this->onEventMarkAsFavorite ($iEntryId, $aDataEntry);
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
            $sJQueryJS = genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox($this->_oDb->isFavorite($this->_iProfileId, $iEntryId) ? $sMsgSuccessAdd : $sMsgSuccessRemove) . $sJQueryJS;
            exit;
        }        

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
        exit;        
    } 

    function onEventMarkAsFavorite ($iEntryId, $aDataEntry) {

        // arise alert
		$oAlert = new BxDolAlerts($this->_sPrefix, 'mark_as_favorite', $iEntryId, $this->_iProfileId, array('favorite' => $this->_oDb->isFavorite($this->_iProfileId, $iEntryId)));
		$oAlert->alert();
    }  
	//[end] favorites
 
   
    
	function actionApply ($iEntryId) {
        $this->_actionApply ($iEntryId, _t('_modzzz_jobs_page_title_apply'),  _t('_modzzz_jobs_msg_application_sent'));
    }
	
	/*
	$aDataEntry = $this->_oDb->getEntryById($iEntryId);
	$iEntryJobCountry =  $aDataEntry['country'];
	$iEntryJobRegion = ($aDataEntry['state']) ? $this->_oDb->getStateName($iEntryJobCountry, $aDataEntry['state']) : '';
	
		$iEntryJobTitle = '<div class="Job-Apply-Job-Title">'. $aDataEntry['title'].'</div>'.'<div class="infoUnit infoUnitFontIcon  bx-twig-unit-line bx-def-font-small bx-def-font-grayed Job-Apply-Job-Title-Location"> '.'<i class="sys-icon map-marker"></i>'.'&nbsp;'.$aDataEntry['zip'].'&nbsp;'.$aDataEntry['city'].','.'&nbsp;'.$iEntryJobRegion.'</div>';
		
		$iEntryJobCountry =  $aDataEntry['country'];
		
		$iEntryJobZip =  $aDataEntry['zip'];
		$iEntryJobCity =  $aDataEntry['city'];
		
		
       
	    $this->_actionApply ($iEntryId, $iEntryJobTitle,  _t('_modzzz_jobs_msg_application_sent'));
    }
	*/
	////////////////////////////////

    function _actionApply ($iEntryId, $sTitle, $sMsgSent) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if (!$this->isAllowedApply($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
		
		if($aDataEntry['application_link']){

			$sApplyUrl = strncasecmp($aDataEntry['application_link'], 'http://', 7) != 0 && strncasecmp($aDataEntry['application_link'], 'https://', 8) != 0 ? 'http://' . $aDataEntry['application_link'] : $aDataEntry['application_link'];

			header('location: ' . $sApplyUrl);
			exit;
		}

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
  
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$sTitle => '',
		));

        bx_import ('FormApply', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormApply';
        $oForm = new $sClass ($this, $aDataEntry, $this->_iProfileId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
            
			$iResumeId = (int)$oForm->getCleanValue ('resume_id');
			$sMessage = $oForm->getCleanValue ('message');
			/////Freddy add telephone and Email
			$sEmail = $oForm->getCleanValue ('Email');
			$sTelephone = $oForm->getCleanValue ('telephone');
			
			/////////////////////////////
			$sResume = $this->_actionUploadResume(); 
			$sResumePath = ($sResume) ? $this->_oConfig->getResumePath() : ''; 
			
			$this->_oDb->saveApplication($this->_iProfileId, $iEntryId, $sMessage, $sEmail,$sTelephone,$sResume, $iResumeId); 
		  
			$aCompany = $this->_oDb->getCompanyById($aDataEntry['company_id']);
			if($aCompany){
				$sEmail = $aCompany['company_email'];
				$iRecipient = $aCompany['company_author_id'];
				$sCompanyName = $aCompany['company_name'];
			}else{ 
				$iRecipient = $aDataEntry['author_id'];
				$aAuthor = getProfileInfo($iRecipient);  
				$sEmail = $aAuthor['Email'];
				//freddy modif
				//$sCompanyName = $aAuthor['NickName'];
				$sCompanyName = $aAuthor['FirstName'] .' '.$aAuthor['LastName'];
			}
 
            $oEmailTemplate = new BxDolEmailTemplates();
            if (!$oEmailTemplate) {
                $this->_oTemplate->displayErrorOccured();
                return;
            }

			$sAttachMessage = ($sResume) ? _t('_modzzz_jobs_apply_attach_message') : '';

			if(getParam('modzzz_resume_job_connect')=='on'){

				if($iResumeId){
					$sAttachMessage = ($sAttachMessage) ? $sAttachMessage.'<br><br>' : '';
					
					$oResume = BxDolModule::getInstance('BxResumeModule');	
					$aResume = $oResume->_oDb->getEntryById($iResumeId);
					$sResumeTitle = $aResume['title'];
					$sResumeUrl = BX_DOL_URL_ROOT . $oResume->_oConfig->getBaseUri() . 'view/' . $aResume['uri'];
					$sResumeLink = '<a href="'.$sResumeUrl.'">'.$sResumeTitle.'</a>';

					$sAttachMessage .= _t('_modzzz_jobs_view_resume_online_at') . $sResumeLink;
				} 
			}
			
			// Freddy Ajout -- Lien de telechargemet du profil/CV dans email
		
			$suid=  $this->_iProfileId; 
            $sTelechargementProfilUrl = BX_DOL_URL_ROOT.'m/linkedin_profile/generateresume?profile= ' . $suid . '';  
			///////////////////////////////////////////////////////////////////
			
			$sApplicationTitle = _t('_modzzz_jobs_apply_letter_title', $aDataEntry[$this->_oDb->_sFieldTitle]);
            $aTemplate = $oEmailTemplate->getTemplate($this->_sPrefix . '_apply'); 
            $aTemplateVars = array (
                // Freddy ajout Email and Telephone
				'ApplyEmail' => $oForm->getCleanValue ('Email'),
				'ApplyTelephone' => $oForm->getCleanValue ('telephone'),
				////////////////
				'ApplyTitle' => $sApplicationTitle,
                'ApplyMessage' => $oForm->getCleanValue ('message'),
                'CompanyName' => $sCompanyName,
                'AttachMessage' => $sAttachMessage, 
				
			   
                'NickName' => getNickName($this->_iProfileId),
                'NickUrl' => getProfileLink($this->_iProfileId), 
                'EntryTitle' => $aDataEntry[$this->_oDb->_sFieldTitle],
                'EntryUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],  
				'Applicants_URL' => BX_DOL_URL_ROOT .  'm/jobs/browse_applications/' . $aDataEntry[$this->_oDb->_sFieldUri],

				
				//Freddy ajout avar de la personne qui invite
				'PosterAvatar' =>  get_member_thumbnail($this->_iProfileId, 'none', true) ,
				
				 //'SiteName' =>  str_replace("<SiteName>", $GLOBALS['site']['title'], $sSubject), 
				
				// Freddy ajout
				'TelechargementProfilUrl' => $sTelechargementProfilUrl,             
            );
        
			if($sResume){ 
				$this->sendMail($sResume, $sResumePath, $sEmail, $sApplicationTitle, $aTemplate['Body'], $iRecipient, $aTemplateVars);
			}else{  
				sendMail($sEmail, $sApplicationTitle, $aTemplate['Body'], $iRecipient, $aTemplateVars);
			}

            echo MsgBox ($sMsgSent);

            $this->isAllowedApply($aDataEntry, true); // perform send apply message action             
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode($sMsgSent, true, true);
            return;
        } 
		
		/* Freddy affichage du titre et location de l'emploi dans le formulaire  */
		
		$iEntryJobCountry =  $aDataEntry['country'];
	    $iEntryJobRegion = ($aDataEntry['state']) ? $this->_oDb->getStateName($iEntryJobCountry, $aDataEntry['state']) : '';
        $iEntryJobTitle = $aDataEntry['title'];
     	$iEntryJobZip =  $aDataEntry['zip'];
		$iEntryJobCity =  $aDataEntry['city'];
		$iEntryJobUri =  $aDataEntry['uri'];
		//////////////////////////////////////////////////////////////

        // Freddy integration Linkedin Profile
		$iIdCandidat= getLoggedId();
		 $aProfil_Edit_Global = BX_DOL_URL_ROOT.'pedit.php?ID='.$iIdCandidat;
		$aProfileApplyForm = getProfileInfo($this->iProfileId);
		$icoThumbApply = get_member_thumbnail($aProfileApplyForm['ID'], 'none');
	   
		$aLinkedinProfileExperience = $this->_oDb->count_plinkin_experience($aProfileApplyForm['ID']);
	    $aLinkedinProfileEducation = $this->_oDb->count_plinkin_education($aProfileApplyForm['ID']);
		 $aLinkedinProfileSkills = $this->_oDb->count_plinkin_skills($aProfileApplyForm['ID']);
		
		
		 $aLinkedinProfileHeadline = $this->_oDb->plinkin_headline_postuler($aProfileApplyForm['ID']);
		 if ($aLinkedinProfileHeadline){
			 $aAlert_Applicant_Headline = '<i class="sys-icon language"> </i>'.' '. $aLinkedinProfileHeadline;
			 
			 
         }else{ 
		 $aAlert_Applicant_Headline_Inconcomplet= '<i class="sys-icon language"> </i>'.' '._t('_Alert_Applicant_Headline_to_Add').' '.'<i class="sys-icon pencil"> </i>' .' '.'<i class="fa fa-warning"></i>';
		        $aProfil_Edit_Global = BX_DOL_URL_ROOT.'pedit.php?ID='.$iIdCandidat;
				
		 }
		 
		 
		if($aLinkedinProfileExperience <1 || $aLinkedinProfileEducation<1 || $aLinkedinProfileSkills <1 ) {
           $aAlert_Applicant_Message_Profil_incomplet= _t('_Alert_Applicant_Message_Profil_incomplet');
		}
		
		
		$iapplicant_Fullname = $aProfileApplyForm['FirstName'].' '.$aProfileApplyForm['LastName'];
		$iapplicant_FullnameIncomplet = $aProfileApplyForm['FirstName'].' '.$aProfileApplyForm['LastName'];
		$iFullNameID = $this->_iProfileId;
		$aUrlFullNameComplet= BX_DOL_URL_ROOT.'m/'.'linkedin_profile/'.'publicsettings?profile='.$iFullNameID; 
		$aUrlFullNameInComplet = BX_DOL_URL_ROOT.'pedit.php?ID='.$iIdCandidat;
		
		
		$aUrlUpdateProfileIncomplet ='<div class="Update-Profile">'. '<i class="sys-icon edit"> </i>'.' '. _t('_modzzz_jobs_profile_update'). '</div>';
		$aUrlUpdateProfileComplet ='<i class="sys-icon edit"> </i>'.' '. _t('_modzzz_jobs_profile_update');
		 
      echo $this->_oTemplate->parseHtmlByName('view_profile_status_link.html',
	  array(
     	
		'Job_Region_Apply' =>  $iEntryJobRegion,
		'Job_Title_Apply' =>   $iEntryJobTitle,
		'Job_Zip_Apply' =>  $iEntryJobZip,
		'Job_City_Apply' =>  $iEntryJobCity,
		'Retour_Annonce' =>  BX_DOL_URL_ROOT.'m/'.'jobs/'.'view/'.$iEntryJobUri,
	  
	  
	   'uid' => $this->_iProfileId,
	 'Profil_Edit_Global' =>  $aProfil_Edit_Global,
	  'applicant_author_thumb' => $icoThumbApply,
	  
	  
	  
	  'bx_if:UpdateProfileIncomplet' => array( 
								'condition' => $aLinkedinProfileExperience <1 || $aLinkedinProfileEducation<1 || $aLinkedinProfileSkills <1 ,
								'content' => array(
         'Update_Css' => $aUrlUpdateProfileIncomplet ,
		 
		 'Profil_Edit_Global' =>  $aProfil_Edit_Global,
								), 
							),
							
		 'bx_if:UpdateProfileComplet' => array( 
								'condition' =>  $aLinkedinProfileExperience >0 && $aLinkedinProfileEducation >0 && $aLinkedinProfileSkills >0 ,
								'content' => array(
         'Update_Css' => $aUrlUpdateProfileComplet ,
		 
		 'Profil_Edit_Global' =>  $aProfil_Edit_Global,
								), 
							),
							
		 'applicant_Fullname' => $iapplicant_Fullname  ,
		 
		 'URL_FullName' => $aUrlFullNameComplet ,					
			
	  //'applicant_headline' => $iapplicant_headline,
	  
	  /*
	   'bx_if:FullName' => array( 
								'condition' => $aLinkedinProfileExperience >1 && $aLinkedinProfileEducation >1 && $aLinkedinProfileSkills >1,
								'content' => array(
         'applicant_Fullname' => $iapplicant_Fullname  ,
		 
		 'URL_FullName' => $aUrlFullNameComplet ,
								), 
							),
		*/
							
		'bx_if:FullNameIncomplet' => array( 
								'condition' => $aLinkedinProfileExperience <1 || $aLinkedinProfileEducation<1 || $aLinkedinProfileSkills <1 ,
								'content' => array(
         'applicant_FullnameIncomplet' => $iapplicant_FullnameIncomplet,
		 
		 'URL_FullName_Incomplet' => $iapplicant_Fullname ,
		 
		 
								), 
							),
		
	  
	  
	  
	  'Applicant_Headline_Complet' => $aAlert_Applicant_Headline  ,
	  'url_profil_edit_candidat' => $aProfil_Edit_Global,
	  'Applicant_Headline_Inconcomplet'=>$aAlert_Applicant_Headline_Inconcomplet,
	  
	 
	  'bx_if:Message_Profil_Complet' => array( 
								'condition' => $aLinkedinProfileExperience >0 && $aLinkedinProfileEducation >0 && $aLinkedinProfileSkills >0,
								'content' => array(
        'Message_Profil_Complet' => _t('_Votre_profil_sera_partage_avec_le_recruteur'),
		 'uid' => $this->_iProfileId,
		 
		 
								), 
							),
	 
			
	   'bx_if:Message_Profil_incomplet' => array( 
								'condition' => $aAlert_Applicant_Message_Profil_incomplet,
								'content' => array(
        'Message_Profil_incomplet' => $aAlert_Applicant_Message_Profil_incomplet,
		 'url_profil_edit_candidat' => BX_DOL_URL_ROOT.'pedit.php?ID='.$iIdCandidat,
								), 
							),
							
		 
	   
	   'bx_if:Alert_Applicant_Experience' => array( 
								'condition' => $aLinkedinProfileExperience <1,
								'content' => array(
        'Alert_Applicant_Experience' => _t('_Alert_Applicant_Experience_to_Add'),
		 'url_profil_edit_candidat' => BX_DOL_URL_ROOT.'pedit.php?ID='.$iIdCandidat,
								), 
							),
							
		 'bx_if:Alert_Applicant_Education' => array( 
								'condition' => $aLinkedinProfileEducation <1,
								'content' => array(
        'Alert_Applicant_Education' => _t('_Alert_Applicant_Education_to_Add'),
		 'url_profil_edit_candidat' => BX_DOL_URL_ROOT.'pedit.php?ID='.$iIdCandidat,
								), 
							),
							
		 'bx_if:Alert_Applicant_Skills' => array( 
								'condition' => $aLinkedinProfileSkills <1,
								'content' => array(
        'Alert_Applicant_Skills' => _t('_Alert_Applicant_Skills_to_Add'),
		 'url_profil_edit_candidat' => BX_DOL_URL_ROOT.'pedit.php?ID='.$iIdCandidat,
								), 
							),
							
	  
	  
	  )
	  ); 
	 
		echo $oForm->getCode ();
		
		echo $this->_oTemplate->parseHtmlByName(
		'view_retour_annonce_link.html',
	    array('Retour_Annonce' =>  BX_DOL_URL_ROOT.'m/'.'jobs/'.'view/'.$iEntryJobUri,)
		); 
		// Freddy integration Linkedin Profile
     /*
	 echo $this->_oTemplate->parseHtmlByName('view_profile_status_link.html',
	    array('uid' => $this->_iProfileId
		
		
		)); 
		*/
	 // [END ]Freddy integration Linkedin Profile
		
        $this->_oTemplate->addCss ('main.css');
		
		//  FREDDY AJOUT  $this->_oTemplate->addCss ('main.css');
		 $this->_oTemplate->addCss ('general.css');
		 $this->_oTemplate->addCss ('top_menu.css');
		  $this->_oTemplate->addCss ('common.css');
		  $this->_oTemplate->addCss ('abserve.css');
		  $this->_oTemplate->addCss ('form_adv.css');

		 ///////////////////////////////////////////////
		
        $this->_oTemplate->pageCode($sTitle);
    }

	/**
	 * Send email function
	 *
	 * @param string $sRecipientEmail		- Email where email should be send
	 * @param string $sMailSubject			- subject of the message
	 * @param string $sMailBody				- Body of the message
	 * @param integer $iRecipientId			- ID of recipient profile
	 * @param array $aPlus					- Array of additional information
	 *
	 *
	 * @return boolean 						- trie if message was send
	 * 										- false if not
	 */
	function sendMail($sFileName, $sPath, $sRecipientEmail, $sMailSubject, $sMailBody, $iRecipientId = 0, $aPlus = array(), $sEmailFlag = 'html' ) {
		global $site;

		if($iRecipientId)
			$aRecipientInfo = getProfileInfo( $iRecipientId );

		$sEmailNotify       = isset($GLOBALS['site']['email_notify']) ? $GLOBALS['site']['email_notify'] : getParam('site_email_notify');
		$sSiteTitle         = isset($GLOBALS['site']['title']) ? $GLOBALS['site']['title'] : getParam('site_title');

		if ($aPlus || $iRecipientId) {
			if(!is_array($aPlus))
				$aPlus = array();
			$oEmailTemplates = new BxDolEmailTemplates();
			$sMailSubject = $oEmailTemplates->parseContent($sMailSubject, $aPlus, $iRecipientId);
			$sMailBody = $oEmailTemplates->parseContent($sMailBody, $aPlus, $iRecipientId);
		}
 
		$sMailSubject = '=?UTF-8?B?' . base64_encode( $sMailSubject ) . '?=';
   
		$sFile = $sPath.$sFileName;
		$sFileSize = filesize($sFile);
		$handle = fopen($sFile, "r");
		$content = fread($handle, $sFileSize);
		fclose($handle);
		$content = chunk_split(base64_encode($content));
		$uid = md5(uniqid(time()));
		$name = basename($sFile);
 
  
		$sMailParameters	= "-f{$sEmailNotify}";
		//$sMailHeader = "From: ".$from_name." <".$from_mail.">\r\n";
		$sMailHeader = "From: =?UTF-8?B?" . base64_encode( $sSiteTitle ) . "?= <{$sEmailNotify}>\r\n";
		$sMailHeader .= "Reply-To: ".$sEmailNotify."\r\n";
		$sMailHeader .= "MIME-Version: 1.0\r\n";
		$sMailHeader .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
		$sMailHeader .= "This is a multi-part message in MIME format.\r\n";
		$sMailHeader .= "--".$uid."\r\n";
		$sMailHeader .= "Content-type:text/html; charset=UTF-8\r\n";
		$sMailHeader .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$sMailHeader .= $sMailBody."\r\n\r\n";
		$sMailHeader .= "--".$uid."\r\n";
		$sMailHeader .= "Content-Type: application/octet-stream; name=\"".$sFileName."\"\r\n"; // use different content types here
		$sMailHeader .= "Content-Transfer-Encoding: base64\r\n";
		$sMailHeader .= "Content-Disposition: attachment; filename=\"".$sFileName."\"\r\n\r\n";
		$sMailHeader .= $content."\r\n\r\n";
		$sMailHeader .= "--".$uid."--";
  
		$iSendingResult = mail( $sRecipientEmail, $sMailSubject, $sMailBody, $sMailHeader, $sMailParameters );
 
		return $iSendingResult;
	}
  
    function actionInvite ($iEntryId) {
        parent::_actionInvite ($iEntryId, 'modzzz_jobs_invitation', $this->_oDb->getParam('modzzz_jobs_max_email_invitations'), _t('_modzzz_jobs_msg_invitation_sent'), _t('_modzzz_jobs_msg_no_users_to_invite'), _t('_modzzz_jobs_page_title_invite'));
    }

    function _getInviteParams ($aDataEntry, $aInviter) {
        return array (
                'JobName' => $aDataEntry['title'],
                'JobLocation' => _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']) . (trim($aDataEntry['city']) ? ', '.$aDataEntry['city'] : '') . ', ' . $aDataEntry['zip'],
                'JobUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'InviterUrl' => $aInviter ? getProfileLink($aInviter['ID']) : 'javascript:void(0);',
                //Freddy modif
				//'InviterNickName' => $aInviter ? $aInviter['NickName'] : _t('_modzzz_jobs_user_unknown'),
				'InviterNickName' => $aInviter ? $aInviter['FirstName'].' '.$aInviter['LastName'] : _t('_modzzz_jobs_user_unknown'),
                'InvitationText' => stripslashes(strip_tags($_POST['inviter_text'])),
            );        
    }    

    function actionCalendar ($iYear = '', $iMonth = '') {
        parent::_actionCalendar ($iYear, $iMonth, _t('_modzzz_jobs_page_title_calendar'));
    }

    // Freddy Ajout Emploi/Stage---post_type 
  // function actionSearch ($sKeyword = '', $sCategory = '', $sCountry = '', $sState = '', $sCity = '') {
	   function actionSearch ($sKeyword = '', $sCategory = '', $sCountry = '', $sState = '', $sCity = '', $sType = '' , $sParent = '') {


        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        if ($sKeyword) 
            $_GET['Keyword'] = $sKeyword;
        if ($sCategory)
            $_GET['Category'] = $sCategory;
		if ($sCity) 
            $_GET['City'] = $sCity;
		if ($sState) 
            $_GET['State'] = $sState; 
         if ($sCountry)
            $_GET['Country'] = $sCountry;
			
			// Freddy Ajout Emploi/Stage---post_type 
			
			 if ($sType)
            $_GET['Type'] = $sType ;
			
			 if ($sParent)
            $_GET['Parent'] = $sParent; 
			
 
         // Freddy Ajout Emploi/Stage---post_type 
    // if ($sCountry || $sCategory || $sKeyword || $sState || $sCity ) {
		   if ($sKeyword || $sCategory || $sCountry || $sState || $sCity || $sType || $sParent  ) {
            $_GET['submit_form'] = 1;  
        }
        
        modzzz_jobs_import ('FormSearch');
        $oForm = new BxJobsFormSearch ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
            modzzz_jobs_import ('SearchResult');
           // Freddy Ajout Emploi/Stage---post_type 
		  //  $o = new BxJobsSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country') );
		    $o = new BxJobsSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country'), $oForm->getCleanValue('Country'), $oForm->getCleanValue('Type'), $oForm->getCleanValue('Parent') );

            if ($o->isError) {
                $this->_oTemplate->displayPageNotFound ();
                return;
            }

            if ($s = $o->processing()) {
                echo $s;
            } else {
                $this->_oTemplate->displayNoData ();
                return;
            }

            $this->isAllowedSearch(true); // perform search action 

			$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

            $this->_oTemplate->pageCode($o->aCurrent['title'], false, false);
            return;

        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode(_t('_modzzz_jobs_caption_search'));
    } 
 
    function actionAdd () {
        parent::_actionAdd (_t('_modzzz_jobs_page_title_add'));
    }

    function _addForm ($sRedirectUrl) {
  
		$bPaidJob = $this->isAllowedPaidJob (); 
		if( $bPaidJob && (!isset($_POST['submit_form'])) ){

			return $this->showPackageSelectForm(); 
		}else{

			$sCode = $sBuyPointsLink = $sBuyCreditsLink = '';
			$iPostCost = $iMoneyBalance = 0;
	  
			if(getParam('modzzz_jobs_post_credits')=='on'){ 
	 
				$sPaymentUnit = 'credits'; 
				$sMoneyBalanceLabelC = _t("_modzzz_jobs_form_caption_credits_balance");
				$sMoneyTypeC = _t("_modzzz_jobs_credits");

				$oCredit = BxDolModule::getInstance('BxCreditModule');  
				$sBuyCreditsLink = BX_DOL_URL_ROOT . $oCredit->_oConfig->getBaseUri() . 'purchase_credits'; 
				$iMoneyBalance = (int)$oCredit->getMemberCredits($this->_iProfileId);
				$iPostCost = (int)$oCredit->_oDb->getActionValue('modzzz_jobs', 'post');
				if($iMoneyBalance < $iPostCost){
					$sCode = _t('_modzzz_jobs_msg_insufficient_credits_post', number_format($iMoneyBalance,0), number_format($iPostCost,0));
					$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyCreditsLink);
				}
			}elseif(getParam('modzzz_jobs_post_points')=='on') {
				
				$sPaymentUnit = 'points';
				$sMoneyBalanceLabelC = _t("_modzzz_jobs_form_caption_points_balance");
				$sMoneyTypeC = _t("_modzzz_jobs_points");

				$oPoint = BxDolModule::getInstance('BxPointModule');   
				$sBuyPointsLink = BX_DOL_URL_ROOT . $oPoint->_oConfig->getBaseUri() . 'purchase_points'; 
				$iMoneyBalance = $oPoint->_oDb->getMemberPoints($this->_iProfileId);
				$iPostCost = (int)$oPoint->_oDb->getActionValue($this->_iProfileId, 'modzzz_jobs', 'post');
				if($iMoneyBalance < $iPostCost) {
					$sCode = _t('_modzzz_jobs_msg_insufficient_points_post', number_format($iMoneyBalance,0), number_format($iPostCost,0));
					$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyPointsLink);
				}
			}
 
			if($sCode) {
				$sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array('content' => $sCode)); 
				echo $sCode;
			}else{ 
				$this->showAddForm($sRedirectUrl);
			}
		}
	}
  
    function showPostPaymentForm () {
    
		$sCode = $sBuyPointsLink = $sBuyCreditsLink = '';
		$iPostCost = $iMoneyBalance = 0;
  
		if(getParam('modzzz_jobs_post_credits')=='on'){ 
 
			$sPaymentUnit = 'credits'; 
			$sMoneyBalanceLabelC = _t("_modzzz_jobs_form_caption_credits_balance");
			$sMoneyTypeC = _t("_modzzz_jobs_credits");

			$oCredit = BxDolModule::getInstance('BxCreditModule');  
			$sBuyCreditsLink = BX_DOL_URL_ROOT . $oCredit->_oConfig->getBaseUri() . 'purchase_credits'; 
			$iMoneyBalance = (int)$oCredit->getMemberCredits($this->_iProfileId);
			$iPostCost = (int)$oCredit->_oDb->getActionValue('modzzz_jobs', 'post');
			if($iMoneyBalance < $iPostCost){
				$sCode = _t('_modzzz_jobs_msg_insufficient_credits_post', number_format($iMoneyBalance,0), number_format($iPostCost,0));
				$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyCreditsLink);
 			}
		}elseif(getParam('modzzz_jobs_post_points')=='on'){
			
 			$sPaymentUnit = 'points';
			$sMoneyBalanceLabelC = _t("_modzzz_jobs_form_caption_points_balance");
			$sMoneyTypeC = _t("_modzzz_jobs_points");

			$oPoint = BxDolModule::getInstance('BxPointModule');   
			$sBuyPointsLink = BX_DOL_URL_ROOT . $oPoint->_oConfig->getBaseUri() . 'purchase_points'; 
			$iMoneyBalance = $oPoint->_oDb->getMemberPoints($this->_iProfileId);
			$iPostCost = (int)$oPoint->_oDb->getActionValue($this->_iProfileId, 'modzzz_jobs', 'post');
			if($iMoneyBalance < $iPostCost){
				$sCode = _t('_modzzz_jobs_msg_insufficient_points_post', number_format($iMoneyBalance,0), number_format($iPostCost,0));
				$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyPointsLink);
 			}
		}
 
		if($sCode) {
			$sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array('content' => $sCode)); 
			echo $sCode;

			$this->_oTemplate->addCss ('view.css');
			$this->_oTemplate->addCss ('main.css');
			$this->_oTemplate->pageCode($aDataEntry[$this->_oDb->_sFieldTitle], false, false);
			return; 
		}
		
		$aQuantity = array();
		for($iter=1; $iter<=1000; $iter++)
			$aQuantity[$iter] = $iter;

		$aCustomForm = array(
  
			'form_attrs' => array(
				'id' => 'jobs_form',
				'name' => 'jobs_form',
                'action' => '',
				'method' => 'post',
			),

			'params' => array (
				'db' => array(
					'submit_name' => 'submit_form', 
				),
			),

			'inputs' => array (

				'header_payment' => array(
					'type' => 'block_header',
					'caption' => _t("_modzzz_jobs_form_caption_post", $sTitle),
				), 
				'money_balance' => array(
					'type' => 'custom',
					'caption' => $sMoneyBalanceLabelC,
					'content' => number_format($iMoneyBalance,0) .' '. $sMoneyTypeC,
				), 
				'post_cost' => array(
					'type' => 'custom',
 					'caption' => _t("_modzzz_jobs_form_caption_cost_to_post"),
					'content' => number_format($iPostCost,0) .' '. $sMoneyTypeC .' '. _t("_modzzz_jobs_form_per_day"),
				),   
                'quantity' => array(
                    'caption'  => _t('_modzzz_jobs_caption_num_post_days'),
                    'type'   => 'select',
                    'name' => 'quantity',
                    'values' => $aQuantity,
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_modzzz_jobs_caption_err_post_days'),
                    ),
                ), 
				'submit_form' => array(
					'type' => 'submit',
					'name' => 'submit_form',
					'value' => _t("_modzzz_jobs_form_caption_submit"),
				),
			)
		);
		  
		$oForm = new BxTemplFormView($aCustomForm);
		$oForm->initChecker();

		if ( $oForm->isSubmittedAndValid() ) {
			
			$iQuantity = (int)$oForm->getCleanValue('quantity');
			$iTotalCost = $iQuantity * $iPostCost;
 
			if(getParam('modzzz_jobs_post_credits')=='on'){ 
	 
				if($iMoneyBalance < $iTotalCost){
					$sCode = _t('_modzzz_jobs_msg_insufficient_credits_post', number_format($iMoneyBalance,0), number_format($iTotalCost,0));
					$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyCreditsLink);
					echo MsgBox($sCode);
					return;
				}
			}elseif(getParam('modzzz_jobs_post_points')=='on'){
	 
				if($iMoneyBalance < $iTotalCost){
					$sCode = _t('_modzzz_jobs_msg_insufficient_points_post', number_format($iMoneyBalance,0), number_format($iTotalCost,0));
					$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyPointsLink);
					echo MsgBox($sCode);
					return;
				}
			}
  
			$bSuccess = $this->_oDb->deductPayment('post', $sPaymentUnit, $this->_iProfileId, $iEntryId, $iQuantity, $iTotalCost);  

			if($bSuccess){
				$this->_oDb->updateEntryExpiration($iEntryId, $iQuantity);
				$sResultMsg = _t("_modzzz_jobs_msg_post_success") ; 
			}else{
				$sResultMsg = _t("_modzzz_jobs_msg_post_failure") ; 
			}
 
			$sCode = MsgBox($sResultMsg);
		} 
  
        if ($oForm->isSubmittedAndValid ()) {
			$this->showAddForm(false);
		}else{ 
			$sContent = $sCode . $oForm->getCode(); 

			echo $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array('content' => $sContent)); 
		} 
	}
  
    function showPackageSelectForm() {
		
		//Freddy Integration Business listing
        $aProfile = getProfileInfo($this->_oMain->_iProfileId);
       // $BusinessID =  $aProfile['ID']; 
    if(getParam('modzzz_listing_jobs_active')=='on')
			$aCompanies = $this->_oDb->getMergedCompanyList($aProfile['ID']);   
  // Fin Freddy Integration Business Listing
   
		$aPackage = $this->_oDb->getPackageList();

		$sPackageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/?ajax=package&package=' ; 

		$iPackageId = ($_POST['package_id']) ? $_POST['package_id'] : $this->_oDb->getInitPackage();
		$sPackageDesc = $this->_oTemplate->getFormPackageDesc($iPackageId);
  
		$aForm = array(
            'form_attrs' => array(
                'name' => 'package_form',
                'method' => 'post', 
                'action' => '',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_package',
                ),
            ),
            'inputs' => array( 
  
				'title' => array(
                    'type' => 'hidden',
                    'name' => 'title',
                    'value' => stripslashes($_REQUEST['title']) 
                ),  
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_modzzz_jobs_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),	
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_jobs_form_err_package'),
                    ),   
                ),  
 				
				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_jobs_package_desc'),  
                ), 
				
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_jobs_continue'),
                    'name'  => 'submit_package',
                ),
            ),
        );
		
		/////FREDDY SI LE MEMBRE N'A PAS ENCORE INSCRIT UNE ENTREPRISE  ALORS AFFICHER LE MESSAGE 
		// NB Ce bout de code ne fonctionne seulement lorsque Activate Paid Jobs listings est activé 
		
		if(count($aCompanies)==0){
		
		      unset ($aForm['inputs']['title']);
			  unset ($aForm['inputs']['package_id']);
			 unset ($aForm['inputs']['package_desc']);
			 unset ($aForm['inputs']['submit']); 
			echo MsgBox (_t('_modzzz_jobs_add_business_buton_register'));
		
		}
		////////////////////////////////////
		
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid () && $oForm->getCleanValue('package_id')) {
			$this->showAddForm(false);
		}else{ 
			echo $oForm->getCode(); 
		}
    }
 
    function showAddForm($sRedirectUrl) {
 
        bx_import ('FormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {

			$iQuantity = (int)$oForm->getCleanValue('quantity');
			$iTotalCost = $iQuantity * $iPostCost;
 

			$bPayPointsCredits = false; 
			if($this->isValidAccess(0, $this->_iProfileId, 'post')){ 

				if(getParam('modzzz_jobs_post_credits')=='on'){ 
		 
					if($iMoneyBalance < $iTotalCost){
						$sCode = _t('_modzzz_jobs_msg_insufficient_credits_post', number_format($iMoneyBalance,0), number_format($iTotalCost,0));
						$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyCreditsLink);
						echo  MsgBox($sCode) . $oForm->getCode();
						return;
					}
				}elseif(getParam('modzzz_jobs_post_points')=='on'){
		 
					if($iMoneyBalance < $iTotalCost){
						$sCode = _t('_modzzz_jobs_msg_insufficient_points_post', number_format($iMoneyBalance,0), number_format($iTotalCost,0));
						$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyPointsLink);
						echo  MsgBox($sCode) . $oForm->getCode();
						return;
					}
				}

				$bPayPointsCredits = true;
			}
   
 			$bPaidJob = $this->isPaidPackage($oForm->getCleanValue('package_id')); 

			if($bPaidJob)
				$sStatus = 'pending';
			else
				$sStatus =  ((getParam($this->_sPrefix.'_autoapproval') == 'on') || $this->isAdmin()) ? 'approved' : 'pending';

            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId, 
            );                        
  
			$sCompanyId = $oForm->getCleanValue('company_id');
			if($sCompanyId && $sCompanyId!='0' && $sCompanyId!='-99'){
				$aCompanyInfo = explode('|', $oForm->getCleanValue('company_id')); 
				$aValsAdd['company_type'] = $aCompanyInfo[0]; 
				$aValsAdd['company_id'] = $aCompanyInfo[1]; 
 			} 

			if(!($oForm->getCleanValue('city') || $oForm->getCleanValue('zip'))){
				//new company
				if($sCompanyId=='-99'){
					if($oForm->getCleanValue('company_name') && ($oForm->getCleanValue('company_city') || $oForm->getCleanValue('company_zip'))){

						if($oForm->getCleanValue('company_country') && !$oForm->getCleanValue('country')){
							$aValsAdd['country'] = $oForm->getCleanValue('company_country');
							$aValsAdd['state'] = $oForm->getCleanValue('company_state'); 
							$aValsAdd['address1'] = $oForm->getCleanValue('company_address');
							$aValsAdd['city'] = $oForm->getCleanValue('company_city'); 
							$aValsAdd['zip'] = $oForm->getCleanValue('company_zip');

							unset ($oForm->aInputs['country']);
							unset ($oForm->aInputs['state']);
							unset ($oForm->aInputs['address1']);
							unset ($oForm->aInputs['city']);
							unset ($oForm->aInputs['zip']); 
						}
					}
				}elseif($aValsAdd['company_type'] == 'listing'){
					
					$oListing = BxDolModule::getInstance('BxListingModule'); 
					$aCompany = $oListing->_oDb->getEntryById($aValsAdd['company_id']);

					if(!$oForm->getCleanValue('country')){ 
						$aValsAdd['country'] = $aCompany['country'];
						$aValsAdd['state'] = $aCompany['state'];
						$aValsAdd['address1'] = $aCompany['address1'];
						$aValsAdd['city'] = $aCompany['city'];
						$aValsAdd['zip'] = $aCompany['zip'];

						unset ($oForm->aInputs['country']);
						unset ($oForm->aInputs['state']);
						unset ($oForm->aInputs['address1']);
						unset ($oForm->aInputs['city']);
						unset ($oForm->aInputs['zip']); 
					}
				}elseif($aValsAdd['company_type'] == 'job'){

					$aCompany = $this->_oDb->getCompanyEntryById($aValsAdd['company_id']);

					if(!$oForm->getCleanValue('country')){ 
						$aValsAdd['country'] = $aCompany['company_country'];
						$aValsAdd['state'] = $aCompany['company_state'];
						$aValsAdd['address1'] = $aCompany['company_address'];
						$aValsAdd['city'] = $aCompany['company_city'];
						$aValsAdd['zip'] = $aCompany['company_zip'];

						unset ($oForm->aInputs['country']);
						unset ($oForm->aInputs['state']);
						unset ($oForm->aInputs['address1']);
						unset ($oForm->aInputs['city']);
						unset ($oForm->aInputs['zip']); 
					}
				}
			}
 
            unset ($oForm->aInputs['header_company']);
            unset ($oForm->aInputs['company_name']);
            unset ($oForm->aInputs['company_desc']);
            unset ($oForm->aInputs['company_country']);
            unset ($oForm->aInputs['company_state']);
            unset ($oForm->aInputs['company_address']);
            unset ($oForm->aInputs['company_city']);
            unset ($oForm->aInputs['company_zip']);
            unset ($oForm->aInputs['company_email']);
            unset ($oForm->aInputs['company_telephone']);
            unset ($oForm->aInputs['company_fax']); 
            unset ($oForm->aInputs['company_website']); 
            unset ($oForm->aInputs['employee_count']);
            unset ($oForm->aInputs['office_count']); 
            unset ($oForm->aInputs['company_icon']); 
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 
 
  				$this->_oDb->addYoutube($iEntryId);

				$oForm->processAddMedia($iEntryId, $this->_iProfileId);
 
				$iCompanyId = $this->_oDb->postCompany($iEntryId, $this->_iProfileId);

                $this->processCompanyMedia($iCompanyId);

                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());
                $this->onEventCreate($iEntryId, $sStatus, $aDataEntry);
  

				if($bPayPointsCredits){
					$this->_oDb->updateEntryExpiration($iEntryId, $iQuantity);
					$this->_oDb->deductPayment('post', $sPaymentUnit, $this->_iProfileId, $iEntryId, $iQuantity, $iTotalCost);  
				
				}elseif($this->isAllowedPaidJob()){
 
					$iPackageId = $oForm->getCleanValue('package_id');
					$aPackage = $this->_oDb->getPackageById($iPackageId);
					$fPrice = $aPackage['price'];
					$iDays = $aPackage['days'];
  					$iFeatured = $aPackage['featured'];

					$sInvoiceStatus = ($fPrice) ? 'pending' : 'paid';
					$sInvoiceNo = $this->_oDb->createInvoice($iEntryId, $iPackageId, $fPrice, $iDays, $sInvoiceStatus);

					$this->_oDb->updateEntryInvoice($iEntryId, $sInvoiceNo); 
		 
					if($fPrice){
						$this->initializeCheckout($iEntryId, $fPrice, 1, 0, $aDataEntry[$this->_oDb->_sFieldTitle]);  
						return;  
					}else{
						if($iDays)
							$this->_oDb->updateEntryExpiration($iEntryId, $iDays); 
 
						if($iFeatured)
							$this->_oDb->updateFeaturedStatus($iEntryId); 
					}
				}else{
					$iNumActiveDays = (int)getParam("modzzz_jobs_free_expired");
					if($iNumActiveDays && (!$this->isAdmin()))
						$this->_oDb->updateEntryExpiration($iEntryId, $iNumActiveDays);  
 				} 

				if (!$sRedirectUrl)  
					$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

                header ('Location:' . $sRedirectUrl);
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
			$this->_oTemplate->addJs ('job.js'); 
			 
			
			
            echo $oForm->getCode (); 
        }
    }
  
    function actionEdit ($iEntryId) {
        $this->_actionEdit ($iEntryId, _t('_modzzz_jobs_page_title_edit'));
    }

  function _actionEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));

        if (!$this->isAllowedEdit($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        bx_import ('FormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormEdit';
        $oForm = new $sClass ($this, $aDataEntry[$this->_oDb->_sFieldAuthorId], $iEntryId, $aDataEntry);
        if (isset($aDataEntry[$this->_oDb->_sFieldJoinConfirmation]))
            $aDataEntry[$this->_oDb->_sFieldJoinConfirmation] = (int)$aDataEntry[$this->_oDb->_sFieldJoinConfirmation];
 
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
 			$iPackageId = $this->_oDb->getPackageIdByInvoiceNo($aDataEntry['invoice_no']); 
			$bPaidListing = $this->isPaidPackage($iPackageId);   
 			if($bPaidListing){ 
				$aValsAdd = array ();
			}else{ 
				$sStatus = $this->_oDb->getParam($this->_sPrefix . '_autoapproval') == 'on' || $this->isAdmin() ? 'approved' : 'pending';
				$aValsAdd = array ($this->_oDb->_sFieldStatus => $sStatus);
			}

            if ($oForm->update ($iEntryId, $aValsAdd)) {

				//[begin] youtube
				$aYoutubes2Keep = array(); 
				if( is_array($_POST['prev_video']) && count($_POST['prev_video'])){ 
					foreach ($_POST['prev_video'] as $iYoutubeId){
						$aYoutubes2Keep[$iYoutubeId] = $iYoutubeId;
					}
				}
					
				$aYoutubeIds = $this->_oDb->getYoutubeIds($iEntryId); 
				$aDeletedYoutube = array_diff ($aYoutubeIds, $aYoutubes2Keep);

				if ($aDeletedYoutube) {
					foreach ($aDeletedYoutube as $iYoutubeId) {
						$this->_oDb->removeYoutubeEntry($iYoutubeId);
					}
				} 
 
				$this->_oDb->addYoutube($iEntryId);
 				//[end] youtube 

                $oForm->processMedia($iEntryId, $this->_iProfileId);

                $this->isAllowedEdit($aDataEntry, true); // perform action

                $this->onEventChanged ($iEntryId, $sStatus);
 
				if($bPaidListing && $aDataEntry[$this->_oDb->_sFieldStatus]=='pending'){
  
					$aPackage = $this->_oDb->getPackageById($iPackageId);
					$fPrice = $aPackage['price'];
			 
					$this->initializeCheckout($iEntryId, $fPrice);  
					return;   
				}else{  
					header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
				}   
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        }

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode($sTitle);
    }
 


    function actionDelete ($iEntryId) {
        parent::_actionDelete ($iEntryId, _t('_modzzz_jobs_msg_job_was_deleted'));
    }

    function actionMarkFeatured ($iEntryId) {
        parent::_actionMarkFeatured ($iEntryId, _t('_modzzz_jobs_msg_added_to_featured'), _t('_modzzz_jobs_msg_removed_from_featured'));
    }
 
    function actionMarkToday ($iEntryId) {
        $this->_actionMarkToday ($iEntryId, _t('_modzzz_jobs_msg_added_to_today'), _t('_modzzz_jobs_msg_removed_from_today'));
    }
 
    function _actionMarkToday ($iEntryId, $sMsgSuccessAdd, $sMsgSuccessRemove) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedMarkAsToday($aDataEntry)) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->markAsToday($iEntryId)) {
            $this->isAllowedMarkAsToday($aDataEntry, true); // perform action
            $this->onEventMarkAsToday ($iEntryId, $aDataEntry);
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
            $sJQueryJS = genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox($aDataEntry[$this->_oDb->_sFieldDailyJob] ? $sMsgSuccessRemove : $sMsgSuccessAdd) . $sJQueryJS;
            exit;
        }        

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
        exit;        
    }
 
    function actionJoin ($iEntryId, $iProfileId) {

        parent::_actionJoin ($iEntryId, $iProfileId, _t('_modzzz_jobs_msg_joined_already'), _t('_modzzz_jobs_msg_joined_request_pending'), _t('_modzzz_jobs_msg_join_success'), _t('_modzzz_jobs_msg_join_success_pending'), _t('_modzzz_jobs_msg_leave_success'));
    }    

    function actionSharePopup ($iEntryId) {
        parent::_actionSharePopup ($iEntryId, _t('_modzzz_jobs_caption_share_job'));
    }

    function actionCompanySharePopup ($iEntryId) {
        $this->_actionCompanySharePopup ($iEntryId, _t('_modzzz_jobs_caption_share_company'));
    }

    function _actionCompanySharePopup ($iEntryId, $sTitle) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getCompanyEntryById ($iEntryId))) {
            echo MsgBox(_t('_Empty'));
            exit;
        }

        require_once (BX_DIRECTORY_PATH_INC . "shared_sites.inc.php");
        $sEntryUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'companyview/' . $aDataEntry[$this->_oDb->_sFieldCompanyUri];
        $aSitesPrepare = getSitesArray ($sEntryUrl);        
        $sIconsUrl = getTemplateIcon('digg.png');        
        $sIconsUrl = str_replace('digg.png', '', $sIconsUrl);
        $aSites = array ();
        foreach ($aSitesPrepare as $k => $r) {
            $aSites[] = array (
                'icon' => $sIconsUrl . $r['icon'],
                'name' => $k,
                'url' => $r['url'],
            );
        }

        $aVarsContent = array (
            'bx_repeat:sites' => $aSites,
        );
        $aVarsPopup = array (
            'title' => $sTitle,
            'content' => $this->_oTemplate->parseHtmlByName('popup_share', $aVarsContent),
        );        
        echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true);
        exit;
    }
 
    function actionManageFansPopup ($iEntryId) {
        parent::_actionManageFansPopup ($iEntryId, _t('_modzzz_jobs_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', BX_JOBS_MAX_FANS);
    }

    function actionTags() {
        parent::_actionTags (_t('_modzzz_jobs_page_title_tags'));
    }    

    function actionCategories ($sUri='') { 
        $this->_oTemplate->pageStart();
        bx_import ('PageCategory', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageCategory';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css')); 
        $this->_oTemplate->pageCode(_t('_modzzz_jobs_page_title_categories'), false, false);
    }

    function actionSubCategories ($sUri='') {
        $this->_oTemplate->pageStart();
        bx_import ('PageSubCategory', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageSubCategory';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

         $this->_oTemplate->pageCode(_t('_modzzz_jobs_page_title_subcategories'), false, false);
    }

    function actionDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'files');

        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getEntryByIdAndOwner((int)$iEntryId, 0, true))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }

        if (!$this->isAllowedView ($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

    // ================================== external actions


     /**
     * forum permissions
     * @param $iMemberId profile id
     * @param $iForumId forum id
     * @return array with permissions
     */ 
    function serviceGetForumPermission($iMemberId, $iForumId) {

        $iMemberId = (int)$iMemberId;
        $iForumId = (int)$iForumId;

        $aFalse = array ( // default permissions, for visitors for example
            'admin' => 0,
            'read' => 1,
            'post' => 0,
        );
 
        if (!($aForum = $this->_oDb->getForumById ($iForumId))) {    
			return $aFalse;
        }
  
        if (!($aDataEntry = $this->_oDb->getEntryById ($aForum['entry_id']))){
 			return $aFalse;
		}
 
        $aTrue = array (
            'admin' => $aDataEntry[$this->_oDb->_sFieldAuthorId] == $iMemberId || $this->isAdmin() ? 1 : 0, // author is admin
            'read' => $this->isAllowedPostInForum ($aDataEntry, $iMemberId) ? 1 : 0,
            'post' => $this->isAllowedPostInForum ($aDataEntry, $iMemberId) ? 1 : 0,
        );
  
        return $aTrue;
    }


    /**
     * Homepage block with different jobs
     * @return html to display on homepage in a block
     */     
    function serviceHomepageBlock () {

        if (!$this->_oDb->isAnyPublicContent())
            return '';
        
        bx_import ('PageMain', $this->_aModule);
        $o = new BxJobsPageMain ($this);
        $o->sUrlStart = BX_DOL_URL_ROOT . 'index.php?';
		
		$this->_oTemplate->addCss (array('unit.css', 'twig.css','main.css'));
  
        $sDefaultHomepageTab = $this->_oDb->getParam('modzzz_jobs_homepage_default_tab');
        $sBrowseMode = $sDefaultHomepageTab;
        switch ($_GET['filter']) {            
            case 'featured':
            case 'recent':
            case 'top':
            case 'popular':
            case $sDefaultHomepageTab:            
                $sBrowseMode = $_GET['filter'];
                break;
        }

        return $o->ajaxBrowse(
            $sBrowseMode,
            $this->_oDb->getParam('modzzz_jobs_perpage_homepage'), 
            array(
                _t('_modzzz_jobs_tab_featured') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=featured', 'active' => 'featured' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_jobs_tab_recent') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=recent', 'active' => 'recent' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_jobs_tab_top') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=top', 'active' => 'top' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_jobs_tab_popular') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=popular', 'active' => 'popular' == $sBrowseMode, 'dynamic' => true),
            )
        );
    }

    /**
     * Profile block with user's jobs
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlock ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxJobsPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']) . '?';
		
		$this->_oTemplate->addCss (array('unit.css', 'twig.css','main.css'));
 
        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_jobs_perpage_profile'), 
            array(),
            process_db_input ($aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            true,
            false 
        );
    }

    /**
     * Account block with different events
     * @return html to display member jobs in account page a block
     */ 
    function serviceAccountPageBlockOLD () {
  
        $aProfile = getProfileInfo($this->_iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxJobsPageMain ($this);        
        $o->sUrlStart = $GLOBALS['site']['url'] . 'member.php?';
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css','main.css'));
 
        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_jobs_perpage_account'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }
 
    function serviceAccountPageBlock () {
  
        $aProfile = getProfileInfo($this->_iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxJobsPageMain ($this);        
        $o->sUrlStart = $GLOBALS['site']['url'] . 'member.php?';
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css','main.css'));
 
        return $o->ajaxBrowse(
            'apply', 
            $this->_oDb->getParam('modzzz_jobs_perpage_account'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }

     /**
     * Account block with different events
     * @return html to display local jobs in account page a block
     */  
    function serviceAccountLocalBlock () {

        if (!$this->_oDb->isAnyPublicContent())
            return '';

		$aProfileInfo = getProfileInfo($this->_iProfileId);
		$sCity = $aProfileInfo['City'];

		if(!$sCity)
			return;

        bx_import ('PageMain', $this->_aModule);
        $o = new BxJobsPageMain ($this);        
        $o->sUrlStart = BX_DOL_URL_ROOT . '?';
 
        return $o->ajaxBrowse(
            'local',
            $this->_oDb->getParam('modzzz_jobs_perpage_account'),
			array(),
			$sCity
        );
    }
 
    function serviceGetMemberMenuItem () {
        parent::_serviceGetMemberMenuItem (_t('_modzzz_jobs'), _t('_modzzz_jobs'), 'jobs.png');
    }
 
    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        return parent::_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_modzzz_jobs_spy_post',
            'change' => '_modzzz_jobs_spy_post_change',
            'join' => '_modzzz_jobs_spy_join',
            'rate' => '_modzzz_jobs_spy_rate',
            'commentPost' => '_modzzz_jobs_spy_comment',
        ));
    }

    function serviceGetSubscriptionParams ($sAction, $iEntryId) {

        $a = array (
            'change' => _t('_modzzz_jobs_sbs_change'),
            'commentPost' => _t('_modzzz_jobs_sbs_comment'),
            'rate' => _t('_modzzz_jobs_sbs_rate'),
            'join' => _t('_modzzz_jobs_sbs_join')  
        );

        return parent::_serviceGetSubscriptionParams ($sAction, $iEntryId, $a);
    }

    // ================================== admin actions

    function actionLocal ($sCountry='', $sState='', $sCategory='') { 
        $this->_oTemplate->pageStart();
        bx_import ('PageLocal', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageLocal';
        $oPage = new $sClass ($this, $sCountry, $sState, $sCategory);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'twig.css','main.css'));
 
		$sTitle = _t('_modzzz_jobs_page_title_local');

		if($sCountry){
			$sTitle .= ' - ' . $this->_oTemplate->getPreListDisplay('Country', $sCountry);
		}
 
		if($sState){
			$sTitle .= ' - ' . $this->_oDb->getStateName($sCountry, $sState); 
		}

		if($sCategory){
			$sTitle .= ' - ' . $sCategory; 
		}
 
        $this->_oTemplate->pageCode($sTitle, false, false);  
     } 
 
    function actionLocalCountry ($sCountry='', $sCategory='') { 
        $this->_oTemplate->pageStart();
        bx_import ('PageLocal', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageLocal';
        $oPage = new $sClass ($this, $sCountry, '', $sCategory);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'twig.css','main.css'));

        $this->_oTemplate->pageCode(_t('_modzzz_jobs_page_title_local'), false, false);
    }

    function actionAdministration ($sUrl = '', $sParam1 = '', $sParam2 = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();
/*
        $aVars = array (
            'module_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri(),
        );
        $sContent = $this->_oTemplate->parseHtmlByName ('admin_links', $aVars);
        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_jobs_admin_links'));
*/

        $aMenu = array(
            'pending_approval' => array(
                'title' => _t('_modzzz_jobs_menu_admin_pending_approval'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_approval', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false)),
            ),
            'admin_entries' => array(
                'title' => _t('_modzzz_jobs_menu_admin_entries'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ),   
			'invoices' => array(
                'title' => _t('_modzzz_jobs_menu_manage_invoices'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/invoices',
                '_func' => array ('name' => 'actionAdministrationInvoices', 'params' => array($sParam1)),
            ), 			
			'orders' => array(
                'title' => _t('_modzzz_jobs_menu_manage_orders'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/orders',
                '_func' => array ('name' => 'actionAdministrationOrders', 'params' => array($sParam1)),
            ),
			'packages' => array(
                'title' => _t('_modzzz_jobs_menu_manage_packages'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages',
                '_func' => array ('name' => 'actionAdministrationPackages', 'params' => array($sParam1)),
            ),   
           'categories' => array(
                'title' => _t('_modzzz_jobs_menu_admin_manage_categories'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/categories',
                '_func' => array ('name' => 'actionAdministrationCategories', 'params' => array($sParam1)),
            ),
            'subcategories' => array(
                'title' => _t('_modzzz_jobs_menu_admin_manage_subcategories'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/subcategories',
                '_func' => array ('name' => 'actionAdministrationSubCategories', 'params' => array($sParam1,$sParam2)),
            ), 
            'create' => array(
                'title' => _t('_modzzz_jobs_menu_admin_add_entry'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
            'settings' => array(
                'title' => _t('_modzzz_jobs_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'pending_approval';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_jobs_page_title_administration'), $aMenu);
 
		$this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css'));
	
		if($sUrl == 'create') 
			$this->_oTemplate->addAdminJs ('job.js'); 
			
			
			 
			

        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_jobs_page_title_administration'));
    }

    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('Jobs');
    }

    function actionAdministrationCategories ($sParam1='') {
 		$sMessage = "";
  		$iCategory = (int)process_db_input($sParam1);
 
		// check actions
		if(is_array($_POST))
		{
			if(isset($_POST['action_save']) && !empty($_POST['action_save']))
			{  
 				$this->_oDb->SaveCategory();
				$sMessage = _t("Successfully Saved Category");
 			} 
			if(isset($_POST['action_edit']) && !empty($_POST['action_edit']))
			{   
 				$this->_oDb->UpdateCategory();
				$sMessage = _t("Successfully Updated Category");
  			} 
			if(isset($_POST['action_delete']) && !empty($_POST['action_delete']))
			{  
 				$this->_oDb->DeleteCategory();
				$sMessage = _t("Successfully Removed Category");
			} 
			if(isset($_POST['action_add']) && !empty($_POST['action_add']))
			{  
				$iCategory = 0;  
			} 
			if(isset($_POST['action_sub']) && !empty($_POST['action_sub']))
			{  
				$sRedirUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/subcategories/'.$iCategory;
				
				header("Location: " . $sRedirUrl);
			} 		

		}
 
		$aCategories = $this->_oDb->getParentCategories();
		$aCategory[] = array(
			'value' => '',
			'caption' => ''  
		);
		foreach ($aCategories as $oCategory)
		{
			$sKey = $oCategory['id'];
			$sValue = $oCategory['name'];
   
			$aCategory[] = array(  
				'value' => $sKey,
				'caption' => $sValue ,
				'selected' => ($sKey == $iCategory) ? 'selected="selected"' : ''
			);
		}
		
		$sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('top_block_select.html', array(
			'name' => _t('_modzzz_jobs_categories'),
			'bx_repeat:items' => $aCategory,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/categories/'
		));
  
		$aCategory = $this->_oDb->getCategoryById($iCategory);
	 
		$sFormName = 'categories_form';
  
	    if($iCategory){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_jobs_categ_btn_edit'),
				'action_delete' => _t('_modzzz_jobs_categ_btn_delete'), 
				'action_add' => _t('_modzzz_jobs_categ_btn_add'),
				'action_sub' => _t('_modzzz_jobs_categ_btn_subcategories'), 
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_jobs_categ_btn_save')
			), 'pathes', false);	 
	    }
  
		$aVars = array(
			'name' => $aCategory['name'],
			'id'=> $aCategory['id'], 
			
			'custom_field1_value' => $aCategory['custom_field1'],
			'custom_field2_value' => $aCategory['custom_field2'],
			'custom_field3_value' => $aCategory['custom_field3'],
			'custom_field4_value' => $aCategory['custom_field4'],
			'custom_field5_value' => $aCategory['custom_field5'],
			'custom_field6_value' => $aCategory['custom_field6'],
			'custom_field7_value' => $aCategory['custom_field7'],
			'custom_field8_value' => $aCategory['custom_field8'],
			'custom_field9_value' => $aCategory['custom_field9'],
			'custom_field10_value' => $aCategory['custom_field10'],
 
 			'form_name' => $sFormName, 
			'controls' => $sControls,   
		);

		if($sMessage){
 			$sContent .= MsgBox($sMessage) ;
			$sContent .= "<form method=post>";
			$sContent .= BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
 				'action_add' => _t('_modzzz_jobs_categ_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_categories',$aVars);
		}

		return $sContent;
	}

    function actionAdministrationSubCategories ($sParam1='', $sParam2='') {
 		$sMessage = "";
  		$iCategory = (int)process_db_input($sParam1);
   		$iSubCategory = (int)process_db_input($sParam2);
		$sCategoryName = $this->_oDb->getCategoryName($iCategory);
		
		if(!$iCategory){
			$sContent = MsgBox(_t('_modzzz_jobs_manage_subcategories_msg')); 

			return $sContent; 
		}

		// check actions
		if(is_array($_POST))
		{
			if(isset($_POST['action_save']) && !empty($_POST['action_save']))
			{  
 				$this->_oDb->SaveCategory($iCategory);
				$sMessage = _t("Successfully Saved Category");
 			} 
			if(isset($_POST['action_edit']) && !empty($_POST['action_edit']))
			{   
 				$this->_oDb->UpdateCategory();
				$sMessage = _t("Successfully Updated Category");
  			} 
			if(isset($_POST['action_delete']) && !empty($_POST['action_delete']))
			{  
 				$this->_oDb->DeleteCategory();
				$sMessage = _t("Successfully Removed Category");
			} 
			if(isset($_POST['action_add']) && !empty($_POST['action_add']))
			{  
				$iSubCategory = 0;  
			}  
		}
 
		$aSubCategories = $this->_oDb->getSubCategories($iCategory);

		foreach ($aSubCategories as $oSubCategory)
		{
			$sKey = $oSubCategory['id'];
			$sValue = $oSubCategory['name'];
   
			$aSubCategory[] = array(
 
				'custom_field1_value' => $oSubCategory['custom_field1'],
				'custom_field2_value' => $oSubCategory['custom_field2'],
				'custom_field3_value' => $oSubCategory['custom_field3'],
				'custom_field4_value' => $oSubCategory['custom_field4'],
				'custom_field5_value' => $oSubCategory['custom_field5'],
				'custom_field6_value' => $oSubCategory['custom_field6'],
				'custom_field7_value' => $oSubCategory['custom_field7'],
				'custom_field8_value' => $oSubCategory['custom_field8'],
				'custom_field9_value' => $oSubCategory['custom_field9'],
				'custom_field10_value' => $oSubCategory['custom_field10'],

				'value' => $sKey,
				'caption' => $sValue ,
				'selected' => ($sKey == $iSubCategory) ? 'selected="selected"' : ''
			);
		}
		
		$sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('top_block_select.html', array(
			'name' => $sCategoryName .': '. _t('_modzzz_jobs_subcategories'),
			'bx_repeat:items' => $aSubCategory,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/subcategories/'.$iCategory.'/'
		));
 
		$aCategory = $this->_oDb->getCategoryById($iSubCategory);
 
		$sFormName = 'categories_form';
 
	    if($iSubCategory){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_jobs_categ_btn_edit'),
				'action_delete' => _t('_modzzz_jobs_categ_btn_delete'), 
				'action_add' => _t('_modzzz_jobs_categ_btn_add'),
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_jobs_categ_btn_save')
			), 'pathes', false);	 
	    }
  
		$aVars = array(
			'name' => $aCategory['name'],
			'id'=> $aCategory['id'],  
 			'form_name' => $sFormName, 
			'controls' => $sControls,   
		);

		if($sMessage){
 			$sContent .= MsgBox($sMessage) ;
			$sContent .= "<form method=post>";
			$sContent .= BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
 				'action_add' => _t('_modzzz_jobs_categ_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_categories',$aVars);
		}

		return $sContent;
	} 


    function actionAdministrationManage ($isAdminEntries = false) {
        return parent::_actionAdministrationManage ($isAdminEntries, '_modzzz_jobs_admin_delete', '_modzzz_jobs_admin_activate');
    }

    // ================================== events
    function onEventMarkAsToday ($iEntryId, $aDataEntry) {

        // arise alert
		$oAlert = new BxDolAlerts($this->_sPrefix, 'mark_as_today', $iEntryId, $this->_iProfileId, array('Daily' => $aDataEntry[$this->_oDb->_sFieldDailyJob]));
		$oAlert->alert();
    }   
 
    // ================================== permissions
    
    function isAllowedCompanyView ($aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ($this->isAdmin() || $aDataEntry['company_author_id'] == $this->_iProfileId) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_JOBS_VIEW_JOB, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
 
	    return true; 
    }

    function isAllowedView ($aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_JOBS_VIEW_JOB, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;

        // check user job 
	    $isAllowed =   $this->_oPrivacy->check('view_job', $aDataEntry['id'], $this->_iProfileId); 
 
		return $isAllowed && $this->_isAllowedViewByMembership ($aDataEntry); 

    }

    function _isAllowedViewByMembership (&$aDataEntry) { 
        if (!$aDataEntry['membership_view_filter']) return true;
        require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
        $aMembershipInfo = getMemberMembershipInfo($this->_iProfileId);
 
		if($aMembershipInfo['DateExpires']) 
			return $aDataEntry['membership_view_filter'] == $aMembershipInfo['ID'] && $aMembershipInfo['DateStarts'] < time() && $aMembershipInfo['DateExpires'] > time() ? true : false;
		else
			return $aDataEntry['membership_view_filter'] == $aMembershipInfo['ID'] && $aMembershipInfo['DateStarts'] < time() ? true : false; 
    }
 
    function isAllowedBrowse ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_JOBS_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_JOBS_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_JOBS_ADD_JOB, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedEdit ($aDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;

        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_JOBS_EDIT_ANY_JOB, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedMarkAsToday ($aDataEntry, $isPerformAction = false) {
        
		//freddy commenter pour autoriser de mettre un stage comme offre du jour
		/* if($aDataEntry['post_type'] == 'seeker')
			return false;
			*/
			
		
        if ($this->isAdmin()) 
            return true;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_JOBS_MARK_AS_TODAY, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function isAllowedMarkAsFeatured ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_JOBS_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }
 
    function isAllowedBroadcast ($aDataEntry, $isPerformAction = false) {
        
	//freddy  Commenter pour broadcaster stage
		/* if($aDataEntry['post_type'] == 'seeker')
			return false;
			*/
		
		if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_JOBS_BROADCAST_MESSAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }
	
	// Freddy Manage Applications
	
	function isAlloweManageApplications ($aDataEntry, $isPerformAction = false) {
        
		// freddy commenter pour manager applocation stage
		/*
		 if($aDataEntry['post_type'] == 'seeker')
			return false;
		*/
		
		if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_JOBS_BROADCAST_MESSAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
        
    }

    function isAllowedApply ($aDataEntry, $isPerformAction = false) {

		// freddy Commenter pour autoriser de postuler aux stages
		/*
		if($aDataEntry['post_type'] == 'seeker')
			return false;
			*/

		if($this->_oDb->hasApplied($this->_iProfileId, $aDataEntry['id']))
            return false;
 
        if ($this->isAdmin()) 
            return true;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_JOBS_APPLY, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }
 
    function isAllowedDelete (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_JOBS_DELETE_ANY_JOB, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }     

    function onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_jobs_join_request', BX_JOBS_MAX_FANS);
    }

    function onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_jobs_join_reject');
    }

    function onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_jobs_fan_remove');
    }

    function onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_jobs_fan_become_admin');
    }

    function onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_jobs_admin_become_fan');
    }

    function onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_jobs_join_confirm');
    }
  

    function isAllowedJoin (&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;
 
 	// freddy autoriser de devenir fan d 1 stage
	/*
		if($aDataEntry['post_type'] == 'seeker')
			return false;
			*/

        return $this->_oPrivacy->check('join', $aDataEntry['id'], $this->_iProfileId);
    }
 
    function isAllowedSendInvitation (&$aDataEntry) {
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) ? true : false;
    }

    function isAllowedShare (&$aDataEntry) {
        return true;
    }
 
	/**********/
    function isAllowedCompanyEdit ($aDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry[$this->_oDb->_sFieldCompanyAuthorId] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;

        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_JOBS_EDIT_ANY_JOB, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedCompanyMarkAsFeatured ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_JOBS_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function isAllowedCompanyBroadcast ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || $this->isCompanyEntryAdmin($aDataEntry)) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_JOBS_BROADCAST_MESSAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function isAllowedCompanyDelete (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry[$this->_oDb->_sFieldCompanyAuthorId] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_JOBS_DELETE_ANY_JOB, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }     
 
    function isAllowedCompanySendInvitation (&$aDataEntry) {
        return $this->isAdmin() || $this->isCompanyEntryAdmin($aDataEntry) ? true : false;
    }  
	/**********/
 
    function isAllowedPostInForum(&$aDataEntry, $iProfileId = -1) {
        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
 
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) || $this->_oPrivacy->check('post_in_forum', $aDataEntry['id'], $iProfileId);
    }

    function isAllowedRate(&$aDataEntry) {        
        if ($this->isAdmin())
            return true;
        return $this->_oPrivacy->check('rate', $aDataEntry['id'], $this->_iProfileId);        
    }

	function isAllowedViewFans(&$aDataEntry) {
        if ($this->isAdmin())
            return true;
        return $this->_oPrivacy->check('view_fans', $aDataEntry['id'], $this->_iProfileId);
    }

	function isAllowedViewApplications(&$aDataEntry) {
		return $this->isAdmin() || $this->isEntryAdmin($aDataEntry); 
    }
 
    function isAllowedComments(&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        return $this->_oPrivacy->check('comment', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedCompanyRate(&$aDataEntry) {        
        if ($this->isAdmin())
            return true;
        return $this->_oCompanyPrivacy->check('rate', $aDataEntry['id'], $this->_iProfileId);        
    }

    function isAllowedCompanyComments(&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        return $this->_oCompanyPrivacy->check('comment', $aDataEntry['id'], $this->_iProfileId);
    }
 
    function isAllowedUploadPhotos(&$aDataEntry) {
       
        if (!BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            return false;
 
		if (!$this->_iProfileId) 
            return false;  
	 
        if ($this->isAdmin())
            return true; 
		 
		if ($aDataEntry['invoice_no'] && !$this->_oDb->isPackageAllowedPhotos($aDataEntry['invoice_no']))
            return false;    

        if (!$this->isMembershipEnabledForImages())
            return false;
 
        return $this->_oPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedEmbed(&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isEntryAdmin($aDataEntry))
            return true;
 
		return false;                
    }

    function isAllowedUploadVideos(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            return false;
 
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
		if ($aDataEntry['invoice_no'] && !$this->_oDb->isPackageAllowedVideos($aDataEntry['invoice_no']))
            return false;  
        if (!$this->isMembershipEnabledForVideos())
            return false;                
        return $this->_oPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadSounds(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            return false;
 
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
		if ($aDataEntry['invoice_no'] && !$this->_oDb->isPackageAllowedSounds($aDataEntry['invoice_no']))
            return false;    
        if (!$this->isMembershipEnabledForSounds())
            return false;                        
        return $this->_oPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFiles(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            return false;

        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
		if ($aDataEntry['invoice_no'] && !$this->_oDb->isPackageAllowedFiles($aDataEntry['invoice_no']))
            return false;    
        if (!$this->isMembershipEnabledForFiles())
            return false;                        
        return $this->_oPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedCreatorCommentsDeleteAndEdit (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (getParam('modzzz_jobs_author_comments_admin') && $this->isEntryAdmin($aDataEntry))
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_JOBS_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedCompanyCreatorCommentsDeleteAndEdit (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (getParam('modzzz_jobs_author_comments_admin') && $this->isCompanyEntryAdmin($aDataEntry))
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_JOBS_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedManageAdmins($aDataEntry) {
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))
            return true;
        return false;
    }

    function isAllowedManageFans($aDataEntry) {
        return $this->isEntryAdmin($aDataEntry);
    }

    function isFan($aDataEntry, $iProfileId = 0, $isConfirmed = true) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isFan ($aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }

    function isEntryAdmin($aDataEntry, $iProfileId = 0) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;
        return $this->_oDb->isJobAdmin ($aDataEntry['id'], $iProfileId) && isProfileActive($iProfileId);
    }

    function isCompanyEntryAdmin($aDataEntry, $iProfileId = 0) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;

        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry[$this->_oDb->_sFieldCompanyAuthorId] == $iProfileId && isProfileActive($iProfileId))
            return true;

        return  false;
    }


    function _defineActions () {
        defineMembershipActions(array('jobs purchase','jobs mark as favorite', 'jobs relist', 'jobs extend', 'jobs purchase featured', 'jobs view job', 'jobs apply', 'jobs browse', 'jobs search', 'jobs add job', 'jobs comments delete and edit', 'jobs edit any job', 'jobs delete any job', 'jobs mark as featured','jobs mark as today', 'jobs approve jobs', 'jobs broadcast message'));
    }

    function _browseMy (&$aProfile) {        
        parent::_browseMy ($aProfile, _t('_modzzz_jobs_page_title_my_jobs'));
    }    

    function processCompanyMedia ($iCompanyId) {
 
 		$sIcon = $this->_actionUploadIcon($iCompanyId);
  
		if($sIcon)	
			$this->_oDb->UpdateCompanyMedia($iCompanyId, $sIcon);  
	}

    function _actionUploadResume ($iJobId=0) {
	   
		$sPath = $this->_oConfig->getResumePath();	
 
		if ( 0 < $_FILES['resume']['size'] && 0 < strlen( $_FILES['resume']['name'] ) ) {
			$iTime = strtolower(date('M_d_Y_h_i_s_A'));
			$sFileName = getNickName($this->_iProfileId).'_resume_'.$iTime;
			$sExt = $this->moveUploadedFile( $_FILES, 'resume', $sPath . $sFileName, '' );  
			if( strlen( $sExt ) && !(int)$sExt ) {
    
				$sFullPath = $sPath.$sFileName.$sExt;
 	   
				chmod( $sFullPath, 0644 );
				 
				if ($sExt != ''){
					$sResumeFile = $sFileName.$sExt;
 				}
			}
 
			return $sResumeFile ;  
		}
		return '';
	}

	function moveUploadedFile( $aFiles, $sFName, $sPathAndName)
	{ 
		$sExt = substr(strrchr($aFiles['resume']['name'], '.'), 0);
 
		$sPathAndName .= $sExt;
		move_uploaded_file( $aFiles[$sFName]['tmp_name'], $sPathAndName );  
 
		return $sExt;
	}

    function _actionUploadIcon ($iCompanyId=0) {
	   
		$sPath = $this->_oConfig->getCompanyIconsPath();	
 
 		$iImageWidth = getParam("modzzz_jobs_company_image_width");
		$iImageHeight = getParam("modzzz_jobs_company_image_height"); 

		$iIconWidth = getParam("modzzz_jobs_company_icon_width");
		$iIconHeight = getParam("modzzz_jobs_company_icon_height");
 
		if ( 0 < $_FILES['company_icon']['size'] && 0 < strlen( $_FILES['company_icon']['name'] ) ) {
			$sFileName = time();
			$sExt = moveUploadedImage( $_FILES, 'company_icon', $sPath . $sFileName, '', false );
			if( strlen( $sExt ) && !(int)$sExt ) {
  
				if($iCompanyId)
					$this->_actionRemoveIcon($iCompanyId);
   
				$sIconPath = $sPath.$sFileName.$sExt;
				$sImagePath = $sPath.'large_'.$sFileName.$sExt;
	  
				imageResize( $sIconPath, $sImagePath, $iImageWidth, $iImageHeight);
				copy($sImagePath,$sIconPath);
				imageResize( $sIconPath, $sIconPath, $iIconWidth, $iIconHeight);
			
				chmod( $sIconPath, 0644 );
				chmod( $sImagePath, 0644 );
				 
				if ($sExt != ''){
					$sIcon = $sFileName.$sExt;
 				}
			}

			return $sIcon ;  
		} 
	}

	function _actionRemoveIcon($iId) {
	  
		$sName = $this->_oDb->getOne("SELECT `company_icon` FROM `" . $this->_oDb->_sPrefix . "companies` WHERE `id` = '$iId'");

		if(!$sName)
			return;

  		$sIconPath = $this->_oConfig->getCompanyIconsPath() . $sName ;
   		$sImagePath = $this->_oConfig->getCompanyIconsPath() . 'large_'. $sName ;

		if (file_exists($sIconPath) && !is_dir($sIconPath)) {
			@unlink( $sIconPath ); 
 		} 

		if (file_exists($sImagePath) && !is_dir($sImagePath)) {
			@unlink( $sImagePath ); 
 		} 

 	} 
   
	/**************/
    function actionCompanyEdit ($iEntryId) {
        $this->_actionCompanyEdit ($iEntryId, _t('_modzzz_jobs_page_company_title_edit'));
    }

    function actionCompanyDelete ($iEntryId) {
        $this->_actionCompanyDelete ($iEntryId, _t('_modzzz_jobs_company_msg_job_was_deleted'));
    }

    function actionCompanyMarkFeatured ($iEntryId) {
        $this->_actionCompanyMarkFeatured ($iEntryId, _t('_modzzz_jobs_company_msg_added_to_featured'), _t('_modzzz_jobs_company_msg_removed_from_featured'));
    }

    function actionCompanyBroadcast ($iEntryId) {
        $this->_actionCompanyBroadcast ($iEntryId, _t('_modzzz_jobs_page_company_title_broadcast'), _t('_modzzz_jobs_company_msg_broadcast_no_recipients'), _t('_modzzz_jobs_msg_broadcast_message_sent'));
    }

    function actionCompanyInvite ($iEntryId) {
        $this->_actionCompanyInvite ($iEntryId, 'modzzz_jobs_company_invitation', $this->_oDb->getParam('modzzz_jobs_max_email_invitations'), _t('_modzzz_jobs_msg_invitation_sent'), _t('_modzzz_jobs_msg_no_users_to_invite'), _t('_modzzz_jobs_page_company_title_invite'));
    }

    function _getCompanyInviteParams ($aDataEntry, $aInviter) {
        return array (
                'CompanyName' => $aDataEntry['company_name'],
                'CompanyLocation' => _t($GLOBALS['aPreValues']['Country'][$aDataEntry['company_country']]['LKey']) . (trim($aDataEntry['company_city']) ? ', '.$aDataEntry['company_city'] : '') . ', ' . $aDataEntry['company_zip'],
                'CompanyUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'companyview/' . $aDataEntry['company_uri'],
                'InviterUrl' => $aInviter ? getProfileLink($aInviter['ID']) : 'javascript:void(0);',
                'InviterNickName' => $aInviter ? $aInviter['NickName'] : _t('_modzzz_jobs_user_unknown'),
                'InvitationText' => stripslashes(strip_tags($_POST['inviter_text'])),
            );        
    }  


    function _actionCompanyEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getCompanyEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldCompanyTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldCompanyUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldCompanyTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'companyview/' . $aDataEntry[$this->_oDb->_sFieldCompanyUri],
            $sTitle => '',
        ));

        if (!$this->isAllowedCompanyEdit($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        bx_import ('FormCompanyEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormCompanyEdit';
        $oForm = new $sClass ($this, $aDataEntry[$this->_oDb->_sFieldCompanyAuthorId], $iEntryId, $aDataEntry);
        
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {

            //$sStatus = $this->_oDb->getParam($this->_sPrefix . '_autoapproval') == 'on' || $this->isAdmin() ? 'approved' : 'pending';
            //$aValsAdd = array ($this->_oDb->_sFieldCompanyStatus => $sStatus);
            if ($this->_oDb->companyUpdate ($iEntryId)) {

                $this->processCompanyMedia($iEntryId, $this->_iProfileId);

                $this->isAllowedCompanyEdit($aDataEntry, true); // perform action
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'companyview/' . $aDataEntry[$this->_oDb->_sFieldCompanyUri]);
                exit;

            } else {

                echo MsgBox(_t('_Error Occured'));
                
            }            

        } else {

            echo $oForm->getCode ();

        }

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode($sTitle);
    }

    function _actionCompanyDelete ($iEntryId, $sMsgSuccess) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getCompanyEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedCompanyDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteCompanyEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedCompanyDelete($aDataEntry, true); // perform action
             $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/' . ($this->_iProfileId ? 'user/' . $this->_oDb->getProfileNickNameById($this->_iProfileId) : '');
            $sJQueryJS = genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
        exit;
    }

    function _actionCompanyMarkFeatured ($iEntryId, $sMsgSuccessAdd, $sMsgSuccessRemove) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getCompanyEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedCompanyMarkAsFeatured($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->markCompanyAsFeatured($iEntryId)) {
            $this->isAllowedCompanyMarkAsFeatured($aDataEntry, true); // perform action
             $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'companyview/' . $aDataEntry[$this->_oDb->_sFieldCompanyUri];
            $sJQueryJS = genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox($aDataEntry[$this->_oDb->_sFieldFeatured] ? $sMsgSuccessRemove : $sMsgSuccessAdd) . $sJQueryJS;
            exit;
        }        

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
        exit;        
    }

   function _actionCompanyBroadcast ($iEntryId, $sTitle, $sMsgNoRecipients, $sMsgSent) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getCompanyEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if (!$this->isAllowedCompanyBroadcast($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldCompanyTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldCompanyUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldCompanyTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'companyview/' . $aDataEntry[$this->_oDb->_sFieldCompanyUri],
            $sTitle => '',
        ));

        $aRecipients = $this->_oDb->getCompanyBroadcastRecipients ($iEntryId);
        if (!$aRecipients) {
            echo MsgBox ($sMsgNoRecipients);
            $this->_oTemplate->pageCode($sMsgNoRecipients, true, true);
            return;
        }

        bx_import ('FormBroadcast', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormBroadcast';
        $oForm = new $sClass ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
            
            $oEmailTemplate = new BxDolEmailTemplates();
            if (!$oEmailTemplate) {
                $this->_oTemplate->displayErrorOccured();
                return;
            }
            $aTemplate = $oEmailTemplate->getTemplate($this->_sPrefix . '_broadcast'); 
            $aTemplateVars = array (
                'BroadcastTitle' => $oForm->getCleanValue ('title'),
                'BroadcastMessage' => $oForm->getCleanValue ('message'),
                'EntryTitle' => $aDataEntry[$this->_oDb->_sFieldCompanyTitle],
                'EntryUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'companyview/' . $aDataEntry[$this->_oDb->_sFieldCompanyUri],                
            );
            $iSentMailsCounter = 0;            
            foreach ($aRecipients as $aProfile) {		        
       	        $iSentMailsCounter += sendMail($aProfile['Email'], $aTemplate['Subject'], $aTemplate['Body'], $aProfile['ID'], $aTemplateVars);
            }
            if (!$iSentMailsCounter) {
                $this->_oTemplate->displayErrorOccured();
                return;
            }

            echo MsgBox ($sMsgSent);

            $this->isAllowedCompanyBroadcast($aDataEntry, true); // perform send broadcast message action             
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode($sMsgSent, true, true);
            return;
        } 

        echo $oForm->getCode ();

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode($sTitle);
    }

    function _actionCompanyInvite ($iEntryId, $sEmailTemplate, $iMaxEmailInvitations, $sMsgInvitationSent, $sMsgNoUsers, $sTitle) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getCompanyEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldCompanyTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldCompanyUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldCompanyTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'companyview/' . $aDataEntry[$this->_oDb->_sFieldCompanyUri],
            $sTitle . $aDataEntry[$this->_oDb->_sFieldCompanyTitle] => '',
        ));

        bx_import('BxDolTwigFormInviter');
        $oForm = new BxDolTwigFormInviter ($this, $sMsgNoUsers);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        

            $aInviter = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getCompanyInviteParams ($aDataEntry, $aInviter);
            
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;

            // send invitation to registered members
            if (false !== bx_get('inviter_users') && is_array(bx_get('inviter_users'))) {
				$aInviteUsers = bx_get('inviter_users');
                foreach ($aInviteUsers as $iRecipient) {
                    $aRecipient = getProfileInfo($iRecipient);
                    $aPlus = array_merge (array ('NickName' => ' ' . $aRecipient['NickName']), $aPlusOriginal);
                    $iSuccess += sendMail(trim($aRecipient['Email']), $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus) ? 1 : 0;
                }
            }

            // send invitation to additional emails
            $iMaxCount = $iMaxEmailInvitations;
            $aEmails = preg_split ("#[,\s\\b]+#", bx_get('inviter_emails'));
            $aPlus = array_merge (array ('NickName' => ''), $aPlusOriginal);
            if ($aEmails && is_array($aEmails)) {
                foreach ($aEmails as $sEmail) {
                    if (strlen($sEmail) < 5) 
                        continue;
                    $iRet = sendMail(trim($sEmail), $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus) ? 1 : 0;
                    $iSuccess += $iRet;
                    if ($iRet && 0 == --$iMaxCount) 
                        break;
                }             
            }

            $sMsg = sprintf($sMsgInvitationSent, $iSuccess);
            echo MsgBox($sMsg);
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode ($sMsg, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle . $aDataEntry[$this->_oDb->_sFieldCompanyTitle]);
    }

	/**************/
 

    function parseTags($s)
    {
        return $this->_parseAnything($s, ',', BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/tag/');
    }
 
    function _parseAnything($s, $sDiv, $sLinkStart, $sClassName = '')
    {
        $sRet = '';
        $a = explode ($sDiv, $s);
        $sClass = $sClassName ? 'class="'.$sClassName.'"' : '';
        
        foreach ($a as $sName)
            $sRet .= '<a '.$sClass.' href="' . $sLinkStart . urlencode(title2uri($sName)) . '">'.$sName.'</a>&#160';
        
        return $sRet;
    }
 
    function onEventCreate ($iEntryId, $sStatus, $aDataEntry = array()) {
 
		if ('approved' == $sStatus) {
            $this->reparseTags ($iEntryId);
            $this->reparseCategories ($iEntryId);
        }

		//7.1
        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_add', array($this->_oConfig->getUri(), $iEntryId));

        $this->_oDb->createForum ($aDataEntry, $this->_oDb->getProfileNickNameById($this->_iProfileId));

 		$oAlert = new BxDolAlerts($this->_sPrefix, 'add', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
		$oAlert->alert();
    }
 
    function onEventChanged ($iEntryId, $sStatus) {
 
        $this->reparseTags ($iEntryId);
        $this->reparseCategories ($iEntryId);

		//7.1
        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_add', array($this->_oConfig->getUri(), $iEntryId));

 
		$oAlert = new BxDolAlerts($this->_sPrefix, 'change', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
		$oAlert->alert();
    } 
 
	function processJobs() { 
	
		if (getParam('modzzz_jobs_paid_active')=='on') 
			$this->_oDb->processJobs(); 
		else
			$this->processFreeJobs();
	}

	function processFreeJobs() { 
 
		$iExpireDays = (int)getParam("modzzz_jobs_free_expired"); 
 
		if(!$iExpireDays) return;

		$aEntries = $this->_oDb->getAllExpiredEntries();
		foreach($aEntries as $aEachEntry){

			$iEntryId = (int)$aEachEntry['id'];

			$this->_oDb->updateFreeEntryExpired($iEntryId); 

			$this->_oDb->alertOnAction('modzzz_jobs_expired', $iEntryId, $aEachEntry['author_id']); 
			
			/*
			if ($this->_oDb->deleteEntryByIdAndOwner($iEntryId, 0, true)) { 
				$this->onEventDeleted ($iEntryId);
			}
			*/
		} 
	 
		$this->_oDb->processExpiringJobs();   
	}
 
    function actionPackages () { 
        $this->_oTemplate->pageStart();
        bx_import ('PagePackages', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PagePackages';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t('_modzzz_jobs_page_title_packages'), false, false);
    }

   function actionAdministrationPackages ($sParam1='') {
 		$sMessage = "";
  		$iPackage = (int)process_db_input($sParam1);
 
		// check actions
		if(is_array($_POST)){
		
			if(isset($_POST['action_save']) && !empty($_POST['action_save']))
			{  
 				$this->_oDb->SavePackage();
				$sMessage = _t("Successfully Saved Package");
 			} 
			if(isset($_POST['action_edit']) && !empty($_POST['action_edit']))
			{   
 				$this->_oDb->UpdatePackage();
				$sMessage = _t("Successfully Updated Package");
				$iPackage = 0; 
  			} 
			if(isset($_POST['action_delete']) && !empty($_POST['action_delete']))
			{  
 				$this->_oDb->DeletePackage();
				$sMessage = _t("Successfully Removed Package");
			} 
			if(isset($_POST['action_add']) && !empty($_POST['action_add']))
			{  
				$iPackage = 0;  
			} 
 
		}
 
		$aPackages = $this->_oDb->getPackages();
		$aPackage[] = array(
			'value' => '',
			'caption' => ''  
		);
		foreach ($aPackages as $oPackage)
		{
			$sKey = $oPackage['id'];
			$sValue = $oPackage['name'];
 
			$aPackage[] = array(
				'value' => $sKey,
				'caption' => $sValue ,
				'selected' => ($sKey == $iPackage) ? 'selected="selected"' : ''
			);
		}
		
		$sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('top_block_select.html', array(
			'name' => _t('_modzzz_jobs_packages'),
			'bx_repeat:items' => $aPackage,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages/'
		));


		$aPackage = $this->_oDb->getRow("SELECT * FROM `" . $this->_oDb->_sPrefix . "packages` WHERE  `id` = '$iPackage'");
		  
		$sFormName = 'packages_form';
  
	    if($iPackage){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_jobs_categ_btn_edit'),
				'action_delete' => _t('_modzzz_jobs_categ_btn_delete'), 
				'action_add' => _t('_modzzz_jobs_categ_btn_add')  
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_jobs_categ_btn_save')
			), 'pathes', false);	 
	    }
 
		$aVars = array(
 			'id'=> $aPackage['id'],  
			'name' => $aPackage['name'], 
   			'price' => $aPackage['price'],
			'days' => $aPackage['days'],
			'featured' => $aPackage['featured'],
			'description' => $aPackage['description'], 
			'photo_no_select' => $aPackage['photos'] ? '' : "selected='selected'",
			'photo_yes_select' => $aPackage['photos'] ? "selected='selected'" : '',
			'video_no_select' => $aPackage['videos'] ? '' : "selected='selected'",
			'video_yes_select' => $aPackage['videos'] ? "selected='selected'" : '',
			'file_no_select' => $aPackage['files'] ? '' : "selected='selected'",
			'file_yes_select' => $aPackage['files'] ? "selected='selected'" : '',
			
			'sound_no_select' => $aPackage['sounds'] ? '' : "selected='selected'", 
			'sound_yes_select' => $aPackage['sounds'] ? "selected='selected'" : '',

			'featured_no_select' => $aPackage['featured'] ? '' : "selected='selected'", 
			'featured_yes_select' => $aPackage['featured'] ? "selected='selected'" : '',

 			'form_name' => $sFormName, 
			'controls' => $sControls,   
		);

		if($sMessage){
 			$sContent .= MsgBox($sMessage) ;
			$sContent .= "<form method=post>";
			$sContent .= BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
 				'action_add' => _t('_modzzz_jobs_categ_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_packages',$aVars);
		}

		return $sContent;
	}

    function actionAdministrationOrders () {

        if ($_POST['action_activate'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
                $this->_oDb->activateOrder($iId, $this->isAdmin()); 
            }

        } elseif ($_POST['action_delete'] && is_array($_POST['entry'])) {
  
            foreach ($_POST['entry'] as $iId) { 
                $this->_oDb->deleteOrder($iId, $this->isAdmin()); 
            }
        }
 
		$sContent = $this->_manageOrders ('order', '', true, 'bx_twig_admin_form', array(
			'action_activate' => '_modzzz_jobs_admin_activate',
			'action_delete' => '_modzzz_jobs_admin_delete',
		));
     
        return $sContent;
    }

    function actionAdministrationInvoices () {

        if ($_POST['action_delete'] && is_array($_POST['entry'])) {
  
            foreach ($_POST['entry'] as $iId) { 
                $this->_oDb->deleteInvoice($iId, $this->isAdmin()); 
            }
        }
 
		$sContent = $this->_manageOrders ('invoice', '', true, 'bx_twig_admin_form', array(
 			'action_delete' => '_modzzz_jobs_admin_delete',
		));
     
        return $sContent;
    }

    function _manageOrders ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 14, $bActionsPanel = true) {

        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = $sMode . '_admin';

        if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;

        $sPagination = $sActionsPanel = '';
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
        } elseif (!($sContent = $o->displayOrdersResultBlock($sMode))) {
            if ($isMsgBoxIfEmpty)
                $sContent = MsgBox(_t('_Empty'));
            else
                return '';
        } else {
            $sPagination = $sAjaxPaginationBlockId ? $o->showPaginationAjax($sAjaxPaginationBlockId) : $o->showPagination();
			if($bActionsPanel)
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
 
 	function isPaidPackage($iPackageId){
 
		if(!$this->isAllowedPaidJob())
			return false;

		return $this->_oDb->isPaidPackage($iPackageId);  
	}
 
    function isAllowedPaidJob ($bCheckAdmin=true) {
  
		 if($bCheckAdmin && $this->isAdmin())
			return false;

        // admin always have access  
        if (getParam('modzzz_jobs_paid_active')=='on') 
            return true;	
            
		return false;
	}

	function isPaidListing($iEntryId){
		$iEntryId = (int)$iEntryId;
         
		if (getParam('modzzz_jobs_paid_active')!='on') 
            return false;	
 
		$aEntry = $this->_oDb->getEntryById($iEntryId);
		$sInvoiceNo = $aEntry['invoice_no'];
 
 		$aInvoice = $this->_oDb->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];

		return $this->_oDb->isPaidPackage($iPackageId);  
	}


    function actionPaypalProcess($iProfileId, $iJobId) {
        $sPostData = '';
        $sPageContent = '';

        $aData = &$_REQUEST;

        if($aData) {
 
			$aDataEntry = $this->_oDb->getEntryById ($iJobId);
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPostPurchase(_t('_modzzz_jobs_purchase_failed')); 
 				return;
			}

        	array_walk($aResponse['content'], create_function('&$arg', "\$arg = trim(\$arg);"));
        	if(strcmp($aResponse['content'][0], "INVALID") == 0){
  				$this->actionPostPurchase(_t('_payment_pp_err_wrong_transaction'));
 				return; 
        	}

			if(strcmp($aResponse['content'][0], "VERIFIED") != 0){
  				$this->actionPostPurchase(_t('_payment_pp_err_wrong_verification_status'));
 				return;  
			}
  
			//if (($aData['receiver_email'] != trim(getParam('modzzz_jobs_paypal_email'))) || ($aData['txn_type'] != 'web_accept')) {

			if($aData['txn_type'] != 'web_accept') {
				$this->actionPostPurchase(_t('_modzzz_jobs_purchase_failed'));
			}else{ 
				if($this->_oDb->isExistPaypalTransaction($iProfileId, $aData['txn_id'])) { 
					$this -> actionPostPurchase(_t('_modzzz_jobs_transaction_completed_already', $sRedirectUrl)); 
				} else {
					if( $this->_oDb->saveTransactionRecord($iProfileId, $iJobId, $aData['txn_id'], 'Paypal Purchase')) { 
						
						$this->_oDb->setItemStatus($iJobId, 'approved'); 
						
						$this->_oDb->setInvoiceStatus($iJobId, 'paid'); 
					} else {
						$this -> actionPostPurchase(_t('_modzzz_jobs_trans_save_failed'));
					}
				}
			}
           
        }
    }

	function _getReceivedAmount(&$aResultData) {
	    $fAmount = 0.00; 
		$sCurrencyCode = $this->_oConfig->getPurchaseCurrency();

    	if($aResultData['mc_currency'] == $sCurrencyCode && isset($aResultData['payment_gross']) && !empty($aResultData['payment_gross']))	
    		$fAmount = (float)$aResultData['payment_gross'];	
    	else if($aResultData['mc_currency'] == $sCurrencyCode && isset($aResultData['mc_gross']) && !empty($aResultData['mc_gross']))	
    		$fAmount = (float)$aResultData['mc_gross'];
    	else if($aResultData['settle_currency'] == $sCurrencyCode && isset($aResultData['settle_amount']) && !empty($aResultData['settle_amount']))	
    		$fAmount = (float)$aResultData['settle_amount'];

    	return $fAmount;
    }
 
    function _readValidationData($sRequest) {
        $sHeader = "POST /cgi-bin/webscr HTTP/1.0\r\n";
    	$sHeader .= "Host: www.paypal.com\r\n";
    	$sHeader .= "Content-Type: application/x-www-form-urlencoded\r\n";
    	$sHeader .= "Content-Length: " . strlen($sRequest) . "\r\n";
    	$sHeader .= "Connection: close\r\n\r\n";
    	
    	$iErrCode = 0;
    	$sErrMessage = "";
		$rSocket = fsockopen("ssl://www.paypal.com", 443, $iErrCode, $sErrMessage, 60);
 
    	if(!$rSocket)
    		return array('code' => 2, 'message' => 'Can\'t connect to remote host for validation (' . $sErrMessage . ')');

    	fputs($rSocket, $sHeader . $sRequest);
    	$sResponse = '';
        while(!feof($rSocket))
            $sResponse .= fread($rSocket, 1024);
    	fclose($rSocket);
      
    	$aResponse = explode("\r\n\r\n", $sResponse);
    	$sResponseHeader = $aResponse[0];
    	$sResponseContent = $aResponse[1];

    	return array('code' => 0, 'content' => explode("\n", $sResponseContent));
    }

	function initializeCheckout($iJobId, $fTotalCost, $iQuantity=1, $bFeatured=0, $sTitle='') {
  
		$aFormData = array(
			'cmd' => '_xclick',
			'amount' => sprintf( "%.2f", (float)$fTotalCost)
		);
    	 
		if($bFeatured){
			$sNotifyUrl = $this->_oConfig->getFeaturedCallbackUrl() . $this->_iProfileId .'/'. $iJobId;
			$sItemDesc = _t('_modzzz_jobs_paypal_featured_item_desc', $sTitle);
 		}else{
			$sNotifyUrl = $this->_oConfig->getPurchaseCallbackUrl() . $this->_iProfileId .'/'. $iJobId;
			$sItemDesc = _t('_modzzz_jobs_paypal_item_desc', $sTitle);
		}

		$aDataEntry = $this->_oDb->getEntryById($iJobId);
 		$sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $aFormData = array_merge($aFormData, array(
			'business' => getParam('modzzz_jobs_paypal_email'), 
            'item_name' => $sItemDesc,
			'amount' => $fTotalCost, 
            'item_number' => $iJobId,
            'quantity' => $iQuantity,
            'currency_code' => $this->_oConfig->getPurchaseCurrency(),
            'no_note' => '1',
            'no_shipping' => '1',  
			'return' => $this->_oConfig->getReturnUrl() . $sUri,
			'notify_url' => $sNotifyUrl,  
			'rm' => '1'
        ));
  
    	Redirect($this->_oConfig->getPurchaseBaseUrl(), $aFormData, 'post', "Job Listing");
    	exit();
	}

    function actionPostPurchase($sTransMessage = '') {
 
        if(! $this->_iProfileId) {
            header('location: ' . BX_DOL_URL_ROOT . 'member.php');
        }
 
		$sMessageOutput = MsgBox($sTransMessage);  
	  
        $this->_oTemplate->pageStart();
    
	    echo $sMessageOutput;
    
        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->pageCode(_t('_modzzz_jobs_post_purchase_header')); 
    }
 

	//modzzz.com
    function actionPurchaseFeatured($iJobId, $sTransMessage = '') {
 
        if(! $this->_iProfileId) {
            header('location: ' . BX_DOL_URL_ROOT . 'member.php');
        }

	    if($sTransMessage){
			$sMessageOutput = MsgBox($sTransMessage);  
		}
 
		$iPerDayCost = getParam('modzzz_jobs_featured_cost');

		$aDataEntry = $this->_oDb->getEntryById($iJobId);
		$sTitle = $aDataEntry['title'];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

		$aForm = array(
            'form_attrs' => array(
                'name' => 'buy_featured_form',
                'method' => 'post', 
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_featured/'.$iJobId,
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_purchase',
                ),
            ),
            'inputs' => array( 
  
                'title' => array(
                    'type' => 'custom',
                    'name' => 'title',
					'caption'  => _t('_modzzz_jobs_form_caption_current_item'),
                    'content' => $sTitle,
                ), 
                'cost' => array(
                    'type' => 'custom',
                    'name' => 'cost',
					'caption'  => _t('_modzzz_jobs_featured_cost_per_day'),
                    'content' => $iPerDayCost .' '. $this->_oConfig->getPurchaseCurrency(),
                ), 
                'status' => array(
                    'type' => 'custom',
                    'name' => 'status',
					'caption'  => _t('_modzzz_jobs_featured_status'),
                    'content' => ($aDataEntry['featured']) ? _t('_modzzz_jobs_featured_until') .' '. $this->_oTemplate->filterCustomDate($aDataEntry['featured_expiry_date']) : _t('_modzzz_jobs_not_featured'), 
                ), 
                'quantity' => array(
                    'caption'  => _t('_modzzz_jobs_caption_num_featured_days'),
                    'type'   => 'text',
                    'name' => 'quantity',
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_modzzz_jobs_caption_err_featured_days'),
                    ),
                ),
                'submit' => array(
                    'type'  => 'submit',
                    'value' => ($aDataEntry['featured']) ? _t('_modzzz_jobs_extend_featured') : _t('_modzzz_jobs_get_featured'),
                    'name'  => 'submit_purchase',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  

        if ($oForm->isSubmittedAndValid() && $oForm->getCleanValue('quantity')) { 

			$fCost =  number_format($iPerDayCost, 2); 
  
			$this->initializeCheckout($iJobId, $fCost, $oForm->getCleanValue('quantity'), true, $sTitle);  
			return;   
        } else {
             echo $sMessageOutput . $oForm->getCode();
        }

        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->addCss ('paid.css');  
        $this->_oTemplate->pageCode(_t('_modzzz_jobs_purchase_featured')); 
    }
 
    function actionPaypalFeaturedProcess($iProfileId, $iJobId) {
        $sPostData = '';
        $sPageContent = '';

        $aData = &$_REQUEST;

        if($aData) {
			$iQuantity = (int)$aData['quantity'];

			$aDataEntry = $this->_oDb->getEntryById($iJobId); 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
 
        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPurchaseFeatured(_t('_modzzz_jobs_purchase_failed')); 
 				return;
			}

        	array_walk($aResponse['content'], create_function('&$arg', "\$arg = trim(\$arg);"));
        	if(strcmp($aResponse['content'][0], "INVALID") == 0){
  				$this->actionPurchaseFeatured(_t('_payment_pp_err_wrong_transaction'));
 				return; 
        	}

			if(strcmp($aResponse['content'][0], "VERIFIED") != 0){
  				$this->actionPurchaseFeatured(_t('_payment_pp_err_wrong_verification_status'));
 				return;  
			}
 			
			if($aData['txn_type'] != 'web_accept') {
				$this->actionPurchaseFeatured($iJobId, _t('_modzzz_jobs_purchase_failed'));
			}else { 
				$fAmount = $this->_getReceivedAmount($aData);
			
				if($this->_oDb->isExistFeaturedTransaction($iProfileId, $aData['txn_id'])) {
					$this -> actionPurchaseFeatured($iJobId, _t('_modzzz_jobs_transaction_completed_already')); 
				} else {
					if( $this->_oDb->saveFeaturedTransactionRecord($iProfileId, $iJobId,  $iQuantity, $fAmount, $aData['txn_id'], 'Paypal Purchase')) {

						$this->_oDb->updateFeaturedEntryExpiration($iJobId, $iQuantity); 
			    
					} else {
						$this -> actionPurchaseFeatured($iJobId, _t('_modzzz_jobs_purchase_fail'));
					}
				}
			}
            
        }
    }
 
    function isAllowedPurchaseFeaturedOLD ($aDataEntry, $isPerformAction = false) {
  
		if(getParam("modzzz_jobs_buy_featured")!='on')
			return false;
    
		if ($this->isAdmin())
            return false;

		if($aDataEntry['featured'] && !$aDataEntry['featured_expiry_date'])
            return false;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_JOBS_PURCHASE_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    } 

    function isMembershipEnabledForImages () {
        return ($this->_isMembershipEnabledFor ('BX_PHOTOS_ADD') && $this->_isMembershipEnabledFor ('BX_JOBS_PHOTOS_ADD'));
    }

    function isMembershipEnabledForVideos () {
        return $this->_isMembershipEnabledFor ('BX_VIDEOS_ADD');
        return ($this->_isMembershipEnabledFor ('BX_VIDEOS_ADD') && $this->_isMembershipEnabledFor ('BX_JOBS_VIDEOS_ADD')); 
    }

    function isMembershipEnabledForSounds () {
        return ($this->_isMembershipEnabledFor ('BX_SOUNDS_ADD') && $this->_isMembershipEnabledFor ('BX_JOBS_SOUNDS_ADD'));
    }

    function isMembershipEnabledForFiles () {
        return ($this->_isMembershipEnabledFor ('BX_FILES_ADD') && $this->_isMembershipEnabledFor ('BX_JOBS_FILES_ADD'));
    }
 
    function _isMembershipEnabledFor ($sMembershipActionConstant) { 
        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'jobs photos add', 'jobs sounds add', 'jobs videos add', 'jobs files add'));
		if (!defined($sMembershipActionConstant))
			return false;
		$aCheck = checkAction($_COOKIE['memberID'], constant($sMembershipActionConstant));
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }  
 

	/*relisting and extension */
 
    function isAllowedPremium (&$aDataEntry, $isPerformAction = false) {
 
        if (getParam('modzzz_jobs_paid_active')!='on') 
            return false;	
		 
		if($aDataEntry['status'] != 'approved')
            return false;

        if ($this->isAdmin() && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return false;

		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
  
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_JOBS_PURCHASE, $isPerformAction); 
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 

    function actionPremium ($iEntryId) {

        if (!$aDataEntry = $this->_oDb->getEntryById((int)$iEntryId)) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
 		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t('_modzzz_jobs_page_title_premium') => '',
		));

        if (!$this->isAllowedPremium($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_actionPremium($iEntryId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_modzzz_jobs_page_title_premium'));
	}
 
    function _actionPremium ($iEntryId) {
   
		$aPackage = $this->_oDb->getPackageList(true);

		$sPackageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/?ajax=package&package=' ; 

		$iPackageId = ($_POST['package_id']) ? $_POST['package_id'] : $this->_oDb->getInitPackage();
		$sPackageDesc = $this->_oTemplate->getFormPackageDesc($iPackageId);
 
		$aDataEntry = $this->_oDb->getEntryById($iEntryId);
 	    $sTitle = $aDataEntry[$this->_oDb->_sFieldTitle];

		$aForm = array(
            'form_attrs' => array(
                'name' => 'package_form',
                'method' => 'post', 
                'action' => '',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_package',
                ),
            ),
            'inputs' => array( 
			    'title' => array( 
                    'type' => 'custom',
                    'caption' => _t('_modzzz_jobs_form_caption_current_item'),  
					'content'=> $sTitle,
                 ), 
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_modzzz_jobs_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_jobs_form_err_package'),
                    ),   
                ),   
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_jobs_package_desc'),  
                ), 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_jobs_continue'),
                    'name'  => 'submit_package',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid () && $oForm->getCleanValue('package_id')) {

			$iPackageId = $oForm->getCleanValue('package_id');
			$aPackage = $this->_oDb->getPackageById($iPackageId);
			$fPrice = $aPackage['price'];
			$iDays = $aPackage['days'];
			$iFeatured = $aPackage['featured'];
			
			//premium
			$sInvoiceStatus = $this->isAdmin() ? 'paid' : 'pending';
			$sInvoiceNo = $this->_oDb->createInvoice($iEntryId, $iPackageId, $fPrice, $iDays, $sInvoiceStatus);
  
			$this->_oDb->updateEntryInvoice($iEntryId, $sInvoiceNo); 
		 
			if(!$this->isAdmin()){ 
				$this->initializeCheckout($iEntryId, $fPrice, 1, 0, $sTitle);  
				return;  
			}else{

				$sStatus = ((getParam($this->_sPrefix.'_autoapproval') == 'on') || $this->isAdmin()) ? 'approved' : 'pending';

				if($iDays)
					$this->_oDb->updateEntryExpiration($iEntryId, $iDays, $sStatus); 

				if($iFeatured)
					$this->_oDb->updateFeaturedStatus($iEntryId);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

                header ('Location:' . $sRedirectUrl);
                exit;
			} 
 
		}else{ 
			echo $oForm->getCode(); 
		}
    }
 
    function isAllowedRelist (&$aDataEntry, $isPerformAction = false) {
  
		if($aDataEntry['status'] != 'expired')
            return false;

		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_JOBS_RELIST, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 

    function actionRelist ($iEntryId) {

        if (!$aDataEntry = $this->_oDb->getEntryById((int)$iEntryId)) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if (!$this->isAllowedRelist($aDataEntry)) {
             $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
  		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t('_modzzz_jobs_page_title_relist') => '',
		));

        $this->_actionRelist($iEntryId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_modzzz_jobs_page_title_relist'));
	}
 
    function _actionRelist ($iEntryId) {
   
		$aPackage = $this->_oDb->getPackageList();

		$sPackageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/?ajax=package&package=' ; 

		$iPackageId = ($_POST['package_id']) ? $_POST['package_id'] : $this->_oDb->getInitPackage();
		$sPackageDesc = $this->_oTemplate->getFormPackageDesc($iPackageId);
 
		$aDataEntry = $this->_oDb->getEntryById($iEntryId);
		$sTitle = $aDataEntry['title'];

		$aForm = array(
            'form_attrs' => array(
                'name' => 'package_form',
                'method' => 'post', 
                'action' => '',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_package',
                ),
            ),
            'inputs' => array( 
  			    'title' => array( 
                    'type' => 'custom',
                    'caption' => _t('_modzzz_jobs_form_caption_current_item'),  
					'content'=> $sTitle,
                 ),
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_modzzz_jobs_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_jobs_form_err_package'),
                    ),   
                ),  
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_jobs_package_desc'),  
                ),  
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_jobs_continue'),
                    'name'  => 'submit_package',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid () && $oForm->getCleanValue('package_id')) {

			$iPackageId = $oForm->getCleanValue('package_id');
			$aPackage = $this->_oDb->getPackageById($iPackageId);
			$fPrice = $aPackage['price'];
			$iDays = $aPackage['days'];
			$iFeatured = $aPackage['featured'];
			
			//relist
			$sInvoiceStatus = ($fPrice && (!$this->isAdmin())) ? 'pending' : 'paid';
			$sInvoiceNo = $this->_oDb->createInvoice($iEntryId, $iPackageId, $fPrice, $iDays, $sInvoiceStatus);
  
			$this->_oDb->updateEntryInvoice($iEntryId, $sInvoiceNo); 
		 
			if($fPrice && (!$this->isAdmin())){ 
				$this->initializeCheckout($iEntryId, $fPrice, 1, 0, $sTitle);  
				return;  
			}else{
                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());

				$sStatus = ((getParam($this->_sPrefix.'_autoapproval') == 'on') || $this->isAdmin()) ? 'approved' : 'pending';

				if($iDays)
					$this->_oDb->updateEntryExpiration($iEntryId, $iDays, $sStatus); 

				if($iFeatured)
					$this->_oDb->updateFeaturedStatus($iEntryId);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

                header ('Location:' . $sRedirectUrl);
                exit;
			} 
 
		}else{ 
			echo $oForm->getCode(); 
		}
    }
  
    function isAllowedExtend (&$aDataEntry, $isPerformAction = false) {
         
        if (getParam('modzzz_jobs_paid_active')!='on') 
            return false;	
		 
		if($aDataEntry['status'] != 'approved')
            return false;
				
		if(!$aDataEntry['expiry_date'])
            return false;
  
		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
		 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_JOBS_EXTEND, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 
 
    function actionExtend ($iEntryId) {

        if (!$aDataEntry = $this->_oDb->getEntryById((int)$iEntryId)) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
        if (!$this->isPaidListing($iEntryId)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        if (!$this->isAllowedExtend($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
  		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t('_modzzz_jobs_page_title_extend') => '',
		));

        $this->_actionExtend($iEntryId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_modzzz_jobs_page_title_extend'));
	}
 
    function _actionExtend ($iEntryId) {
   
		$aEntry = $this->_oDb->getEntryById($iEntryId);
 		$sTitle = $aEntry['title'];

		$iPackageId = $this->_oDb->getPackageIdByInvoiceNo($aEntry['invoice_no']);
		$sPackageDesc = $this->_oTemplate->getFormPackageDesc($iPackageId);
 
		$aForm = array(
            'form_attrs' => array(
                'name' => 'package_form',
                'method' => 'post', 
                'action' => '',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_package',
                ),
            ),
            'inputs' => array( 
  
                'package_id' => array( 
                    'type' => 'hidden',
                    'name' => 'package_id',
					'value' => $iPackageId 
                ), 

                'item_title' => array( 
                    'type' => 'custom',
                    'caption' => _t('_modzzz_jobs_form_caption_current_item'),  
					'content'=> $sTitle,
                 ),  
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => $sPackageDesc,  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_jobs_package_desc'),  
                ),   
 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_jobs_extend'),
                    'name'  => 'submit_package',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid () && $oForm->getCleanValue('package_id')) {
 
			$iPackageId = $oForm->getCleanValue('package_id');
			$aPackage = $this->_oDb->getPackageById($iPackageId);
			$fPrice = $aPackage['price'];
			$iDays = $aPackage['days'];
			$iFeatured = $aPackage['featured'];

			//extend 
			$sInvoiceStatus = ($fPrice && (!$this->isAdmin())) ? 'pending' : 'paid';
			$sInvoiceNo = $this->_oDb->createInvoice($iEntryId, $iPackageId, $fPrice, $iDays, $sInvoiceStatus);

			$this->_oDb->updateEntryInvoice($iEntryId, $sInvoiceNo); 
		 
			if($fPrice && (!$this->isAdmin())){
				$this->initializeCheckout($iEntryId, $fPrice, 1, 0, $sTitle);  
				return;  
			}else{
                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());

				$sStatus = ((getParam($this->_sPrefix.'_autoapproval') == 'on') || $this->isAdmin()) ? 'approved' : 'pending';

				if($iDays)
					$this->_oDb->updateEntryExpiration($iEntryId, $iDays, $sStatus); 

				if($iFeatured)
					$this->_oDb->updateFeaturedStatus($iEntryId);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

                header ('Location:' . $sRedirectUrl);
                exit;
			}
 
		}else{ 
			echo $oForm->getCode(); 
		}
    }
	/***END Re-list***************/

	function actionMapMultiple($iCenterLat=0, $iCenterLng=0, $iRadius=0){
   
		// Get parameters from URL
		//$iCenterLat = $_GET["lat"];
		//$iCenterLng = $_GET["lng"];
		//$iRadius = $_GET["radius"];

		// Start XML file, create parent node
		$dom = new DOMDocument("1.0");
		$node = $dom->createElement("markers");
		$parnode = $dom->appendChild($node);

		$iCount = (int)getParam('modzzz_jobs_map_count');
		$sMeasurementType = getParam('modzzz_jobs_measurement_type');

		switch($sMeasurementType){
			case 'miles':
				$iCalcValue = 3959;
			break;
			case 'kilometers':
				$iCalcValue = 6371;
			break;
			default:
				$iCalcValue = 6371;
			break;
		}
  
		// Search the rows in the markers table
		$query = sprintf("SELECT `p`.`title`, `p`.`uri`, `p`.`address1`,  `p`.`city`, `p`.`country`,`p`.`company_id`, `p`.`vacancies`, `p`.`role`,  `p`.`thumb`, `m`.`lat`, `m`.`lng`, ( %s * acos( cos( radians('%s') ) * cos( radians( `m`.`lat` ) ) * cos( radians( `m`.`lng` ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( `m`.`lat` ) ) ) ) AS distance FROM `modzzz_jobs_profiles` AS `m` INNER JOIN `modzzz_jobs_main` AS `p` ON (`p`.`id` = `m`.`id`) WHERE `m`.`failed` = 0 AND `p`.`status` = 'approved' HAVING distance < '%s' ORDER BY distance LIMIT 0 , %s",
		  $iCalcValue,
		  mysql_real_escape_string($iCenterLat),
		  mysql_real_escape_string($iCenterLng),
		  mysql_real_escape_string($iCenterLat),
		  mysql_real_escape_string($iRadius),
		  $iCount);
 
		$result = db_res($query);
 
		header("Content-type: text/xml");

		// Iterate through the rows, adding XML nodes for each
		while ($row = @mysql_fetch_assoc($result)){
			  $node = $dom->createElement("marker");
			  $newnode = $parnode->appendChild($node);
			  
			  $sCountry = _t($GLOBALS['aPreValues']['Country'][$row['country']]['LKey']);

			  $sRole = ($row['role']) ? $row['role'] : _t('_modzzz_jobs_na'); 
			  $sRole = '<b>'._t('_modzzz_jobs_role').'</b>: '.$sRole ;
			 
			  $iVacancies = ($row['vacancies']) ? $row['vacancies'] : 1;  
			  $sVacancies = '<b>'._t('_modzzz_jobs_vacancies').'</b> [ '.$iVacancies.' ]';
			  
			  if($row['company_id']){ 
					$aCompany = $this->_oDb->getCompanyEntryById($row['company_id']);
					$sCompanyName = $aCompany['company_name'];
					$sCompanyUrl = 'm/jobs/companyview/' . $aCompany['company_uri'];  
			  }
			 
			  $sCompany = '<b>'._t('_modzzz_jobs_company').'</b>: <a href="'. $sCompanyUrl .'">'. $sCompanyName .'</a>, '. $sVacancies;
			 
			  $newnode->setAttribute("url", 'm/jobs/view/'.$row['uri']);
			  $newnode->setAttribute("name", $row['title']);
			  $newnode->setAttribute("address", $row['address']);
			  $newnode->setAttribute("lat", $row['lat']);
			  $newnode->setAttribute("lng", $row['lng']);
			  $newnode->setAttribute("distance", $row['distance']);
			  $newnode->setAttribute("role", $sRole);
			  $newnode->setAttribute("company", $sCompany);
		}

		echo $dom->saveXML();
	}
 
	function actionMapSingle($iItemId){
    
		// Start XML file, create parent node
		$dom = new DOMDocument("1.0");
		$node = $dom->createElement("markers");
		$parnode = $dom->appendChild($node);
  
		// Search the rows in the markers table
		  
		$query = sprintf("SELECT `p`.`title`, `p`.`uri`, `p`.`company_id`, `p`.`role`, `p`.`vacancies`, `p`.`address1`,  `p`.`city`, `p`.`country`, `p`.`thumb`, `m`.`lat`, `m`.`lng`, `p`.`city` FROM `modzzz_jobs_profiles` AS `m` INNER JOIN `modzzz_jobs_main` AS `p` ON (`p`.`id` = `m`.`id`) WHERE `m`.`failed` = 0 AND `p`.`status` = 'approved' AND `m`.`id` = '%s'", 
		  mysql_real_escape_string($iItemId));
		  

		$result = db_res($query);

		 
		header("Content-type: text/xml");

		// Iterate through the rows, adding XML nodes for each
		while ($row = @mysql_fetch_assoc($result)){
		  $node = $dom->createElement("marker");
		  $newnode = $parnode->appendChild($node);

		  $sRole = ($row['role']) ? $row['role'] : _t('_modzzz_jobs_na'); 
		  $sRole = '<b>'._t('_modzzz_jobs_role').'</b>: '.$sRole ;
		 
		  $iVacancies = ($row['vacancies']) ? $row['vacancies'] : 1;  
		  $sVacancies = '<b>'._t('_modzzz_jobs_vacancies').'</b> [ '.$iVacancies.' ]';
		  
		  if($row['company_id']){ 
				$aCompany = $this->_oDb->getCompanyEntryById($row['company_id']);
				$sCompanyName = $aCompany['company_name'];
				$sCompanyUrl = 'm/jobs/companyview/' . $aCompany['company_uri'];  
		  }
		 
		  $sCompany = '<b>'._t('_modzzz_jobs_company').'</b>: <a href="'. $sCompanyUrl .'">'. $sCompanyName .'</a>, '. $sVacancies;

		  $newnode->setAttribute("url", 'm/jobs/view/'.$row['uri']);
		  $newnode->setAttribute("name", $row['title']);
		  $newnode->setAttribute("address", $row['address']);
		  $newnode->setAttribute("lat", $row['lat']);
		  $newnode->setAttribute("lng", $row['lng']);
		  $newnode->setAttribute("role", $sRole);
		  $newnode->setAttribute("company", $sCompany); 
		}

		echo $dom->saveXML(); 
	}
  
    function actionEmbed ($sUri) {
 
        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
  		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t('_modzzz_jobs_page_title_embed_video') => '',
		));

        bx_import ('EmbedForm', $this->_aModule);
		$oForm = new BxJobsEmbedForm ($this, $aDataEntry);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid()) {        
 
			$sEmbedCode = $oForm->getCleanValue('video_embed');

			$this->_oDb->saveEmbed($aDataEntry[$this->_oDb->_sFieldId], $sEmbedCode);
   
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			header ('Location:' . $sRedirectUrl);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode(_t('_modzzz_jobs_page_title_embed_video') .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

	function isAllowedSeeker(){
		return (getParam('modzzz_jobs_seeker_activated') == 'on') ? true : false; 
	}

	function serviceSearch() {
   
        $this->_oTemplate->pageStart();
 
        bx_import('BxDolCategories');
        bx_import('BxDolProfileFields'); 
  
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
		$aCountries[''] = "";  
		asort($aCountries);
		$aProfile = getProfileInfo((int)$iProfileId);
		$sDefaultCountry = $aProfile['Country'];
		
		
		
   		$aStates = $this->_oDb->getStateArray($sDefaultCountry); 
		// Freddy Ajout PostType
		$aJobPostTypeList = $oProfileFields->convertValues4Input('#!JobPostType');
		 

		$sStateUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/?ajax=state&country=' ;
 
		$sCatUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/?ajax=cat&parent=' ;

		$aCategories = $this->_oDb->getFormCategoryArray();
 
        $aForm = array(

            'form_attrs' => array(
                'name'     => 'form_search_jobs',
                'action'   => '',
                'method'   => 'post',
            ),      

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(
                'Keyword' => array(
                    'type' => 'text',
                    'name' => 'Keyword',
                    'caption' => _t('_modzzz_jobs_form_caption_keyword'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),  
				
				// Freddy Ajout Emploi/Stage---post_type
			
				
				 'Type' => array( 
                    'type' => 'select',
                    'name' => 'Type',
					
					 'values'=> array(
					            'all' => _t('_modzzz_jobs_all'),
								'provider'=>_t('_modzzz_jobs_provider'),
								'seeker'=>_t('_modzzz_jobs_seeker'),
								
							),
							
                    'caption' => _t('_modzzz_jobs_form_caption_post_type'), 
                'attrs' => array (
                        'style' => 'width:135px',
                    ), 
                ), 
				
				  
				              
                'Parent' => array(
                    'type' => 'select',
                    'name' => 'parent',
					'values'=> $aCategories,
                    'caption' => _t('_modzzz_jobs_parent_categories'),
					'attrs' => array(
						'onchange' => "getHtmlData('subcat','$sCatUrl'+this.value)",
                        'id' => 'parentcat',
					),
                    'required' => false, 
                ), 
                'Category' => array(
                    'type' => 'select',
                    'name' => 'Category',
					'values'=> array(),
                    'caption' => _t('_Categories'),
					'attrs' => array(
                        'id' => 'subcat',
					),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ),
                'Country' => array(
					'type' => 'select',
                    'name' => 'Country',
                    'caption' => _t('_modzzz_jobs_form_caption_country'),
                    'values' => $aCountries,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					), 
					'value' => $sDefaultCountry, 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{0,2})/'),
                    ),  
                ),
				'State' => array(
					'type' => 'select',
					'name' => 'State', 
					'caption' => _t('_modzzz_jobs_caption_state'),
					'values'=> $aStates,  
					'attrs' => array(
						'id' => 'substate',
					), 
				    'db' => array (
						'pass' => 'Preg', 
						'params' => array('/([a-zA-Z]+)/'),
					), 
				), 
                'City' => array(
                    'type' => 'text',
                    'name' => 'City',
                    'caption' => _t('_modzzz_jobs_form_caption_city'),
                    'required' => false,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),    
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_modzzz_jobs_continue'),
                    'colspan' => true,
                ),
            ),            
        );
 
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker(); 
  
        if ($oForm->isSubmittedAndValid ()) {
 
            modzzz_jobs_import ('SearchResult');
            // Freddy Ajout Emploi/Stage---post_type 
			//$o = new BxJobsSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country') );
			$o = new BxJobsSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country'), $oForm->getCleanValue('Type'), $oForm->getCleanValue('Parent')  );
 
            if ($o->isError) {
                $this->_oTemplate->displayPageNotFound ();
                exit;
            }

            if ($s = $o->processing()) {
                echo $s;
            } else {
                $this->_oTemplate->displayNoData ();
                exit;
            }

            $this->isAllowedSearch(true); // perform search action 

			$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

            $this->_oTemplate->pageCode($o->aCurrent['title'], false, false);
			exit;

        } 
 
        return $oForm->getCode(); 
    }
 
	function actionPay ($sUri) { 

		if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
			$sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
		else
			$sReg = '/^[\d\w\-_]+$/u'; // latin characters only

		if (!preg_match($sReg, $sUri)) {
			$this->_oTemplate->displayPageNotFound ();
			return false;
		}

        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
        $iEntryId = (int)$aDataEntry['id'];

        if (!$this->isAllowedEdit($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		$iPackageId = $this->_oDb->getPackageIdByInvoiceNo($aDataEntry['invoice_no']); 
		$bPaidListing = $this->isPaidPackage($iPackageId);   
	 
		if($bPaidListing && $aDataEntry[$this->_oDb->_sFieldStatus]=='pending'){

			$aPackage = $this->_oDb->getPackageById($iPackageId);
			$fPrice = $aPackage['price'];
	 
			$this->initializeCheckout($iEntryId, $fPrice);  
			return;   
		}else{  
			header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
		}   
		exit;  
    }

	/* functions added for v7.1 */

    function serviceGetMemberMenuItemAddContent ()
    {
        if (!$this->isAllowedAdd())
            return '';
        return parent::_serviceGetMemberMenuItem (_t('_modzzz_jobs_single'), _t('_modzzz_jobs_single'), 'credit-card', false, '&filter=add_job');
    }


   /**
     * Install map support
     */
    function serviceMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('jobs', array(
            'part' => 'jobs',
            'title' => '_modzzz_jobs',
            'title_singular' => '_modzzz_jobs_single',
            'icon' => 'modules/modzzz/jobs/|map_marker.png',
            'icon_site' => 'credit-card',
            'join_table' => 'modzzz_jobs_main',
            'join_where' => "AND `p`.`status` = 'approved'",
            'join_field_id' => 'id',
            'join_field_country' => 'country',
            'join_field_city' => 'city',
            'join_field_state' => 'state',
            'join_field_zip' => 'zip',
            'join_field_address' => '',
            'join_field_title' => 'title',
            'join_field_uri' => 'uri',
            'join_field_author' => 'author_id',
            'join_field_privacy' => 'allow_view_job_to',
            'permalink' => 'modules/?r=jobs/view/',
        )));
    }
 
	//remove old one first
    function serviceGetWallPost ($aEvent)
    {
        $aParams = array(
            'txt_object' => '_modzzz_jobs_wall_object',
            'txt_added_new_single' => '_modzzz_jobs_wall_added_new',
            'txt_added_new_plural' => '_modzzz_jobs_wall_added_new_items',
            'txt_privacy_view_event' => 'view_job',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallPost ($aEvent, $aParams);
    }
 
    function serviceGetWallPostComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_job',
            'obj_privacy' => $this->_oPrivacy
        );
        return $this->_serviceGetWallPostComment($aEvent, $aParams);
    }

    function _serviceGetWallPostComment($aEvent, $aParams)
    {
        $iId = (int)$aEvent['object_id'];
        if(!$aParams['obj_privacy']->check($aParams['txt_privacy_view_event'], $iId, $this->_iProfileId))
            return '';

        $iOwner = (int)$aEvent['owner_id'];
        $sOwner = getNickName($iOwner);

        $aContent = unserialize($aEvent['content']);
        if(empty($aContent) || !isset($aContent['comment_id']))
            return '';

        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass($this->_sPrefix, $iId);
        if(!$oCmts->isEnabled())
            return '';

        $aItem = $this->_oDb->getEntryByIdAndOwner($iId, $iOwner, 1);
        $aComment = $oCmts->getCommentRow((int)$aContent['comment_id']);

        $sImage = '';
        if($aItem[$this->_oDb->_sFieldThumb]) {
            $a = array('ID' => $aItem[$this->_oDb->_sFieldAuthorId], 'Avatar' => $aItem[$this->_oDb->_sFieldThumb]);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        }

        $sCss = '';
        $sUri = $this->_oConfig->getUri();
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/';
        $sNoPhoto = $this->_oTemplate->getIconUrl('no-photo.png');
        if($aEvent['js_mode'])
            $sCss = $this->_oTemplate->addCss(array('wall_post.css', 'unit.css', 'twig.css','main.css'), true);
        else
            $this->_oTemplate->addCss(array('wall_post.css', 'unit.css', 'twig.css','main.css'));

        bx_import('Voting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Voting';
        $oVoting = new $sClass ($this->_sPrefix, 0, 0);

        $sTextAddedNew = _t('_modzzz_' . $sUri . '_wall_added_new_comment');
        $sTextWallObject = _t('_modzzz_' . $sUri . '_wall_object');
        $aTmplVars = array(
            'cpt_user_name' => $sOwner,
            'cpt_added_new' => $sTextAddedNew,
            'cpt_object' => $sTextWallObject,
            'cpt_item_url' => $sBaseUrl . $aItem[$this->_oDb->_sFieldUri],
            'cnt_comment_text' => $aComment['cmt_text'],
            'unit' => $this->_oTemplate->unit($aItem, 'unit', $oVoting),
            'post_id' => $aEvent['id'],
        );
        return array(
            'title' => $sOwner . ' ' . $sTextAddedNew . ' ' . $sTextWallObject,
            'description' => $aComment['cmt_text'],
            'content' => $sCss . $this->_oTemplate->parseHtmlByName('wall_post_comment', $aTmplVars)
        );
    }
  
    function serviceGetWallPostOutline($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_job',
            'obj_privacy' => $this->_oPrivacy,
            'templates' => array(
                'grouped' => 'wall_outline_grouped'
            )
        );
        return parent::_serviceGetWallPostOutline($aEvent, 'credit-card', $aParams);
    }
 
    function _formatLocation (&$aDataEntry, $isCountryLink = false, $isFlag = false)
    {
        $sFlag = $isFlag ? ' ' . genFlag($aDataEntry['country']) : '';
        $sCountry = _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']);
        if ($isCountryLink)
            $sCountry = '<a href="' . $this->_oConfig->getBaseUri() . 'browse/country/' . strtolower($country['Country']) . '">' . $sCountry . '</a>';
        return (trim($aDataEntry['city']) ? $aDataEntry['city'] . ', ' : '') . $sCountry . $sFlag;
    }

    function _formatSnippetTextForOutline($aEntryData)
    {
        return $this->_oTemplate->parseHtmlByName('wall_outline_extra_info', array(
            'desc' => $this->_formatSnippetText($aEntryData, 200),
            'location' => $this->_formatLocation($aEntryData, false, false),
            'fans_count' => $aEntryData['fans_count'],
        ));
    }

    function _formatSnippetText ($aEntryData, $iMaxLen = 200, $sField='')
    {  $sField = ($sField) ? $sField : $aEntryData[$this->_oDb->_sFieldDescription];
        return strmaxtextlen($sField, $iMaxLen);
    }
 
    function isAllowedReadForum(&$aDataEntry, $iProfileId = -1)
    {
        return true;
    }

    function actionPayFeature ($iEntryId) {
 
        header('Content-type:text/html;charset=utf-8');

		$iEntryId = (int)$iEntryId;
		$aDataEntry = $this->_oDb->getEntryById($iEntryId);
		$sTitle = $aDataEntry['title'];

		$sCode = $sBuyPointsLink = $sBuyCreditsLink = '';
		$iFeaturedCost = $iMoneyBalance = 0;
  
		if(getParam('modzzz_jobs_featured_credits')=='on'){ 
 
			$sPaymentUnit = 'credits'; 
			$sMoneyBalanceLabelC = _t("_modzzz_jobs_form_caption_credits_balance");
			$sMoneyTypeC = _t("_modzzz_jobs_credits");

			$oCredit = BxDolModule::getInstance('BxCreditModule');  
			$sBuyCreditsLink = BX_DOL_URL_ROOT . $oCredit->_oConfig->getBaseUri() . 'purchase_credits'; 
			$iMoneyBalance = (int)$oCredit->getMemberCredits($this->_iProfileId);
			$iFeaturedCost = (int)$oCredit->_oDb->getActionValue('modzzz_jobs', 'feature');
			if($iMoneyBalance < $iFeaturedCost){
				$sCode = _t('_modzzz_jobs_msg_insufficient_credits_feature', number_format($iMoneyBalance,0), number_format($iFeaturedCost,0));
				$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyCreditsLink);
 			}
		}elseif(getParam('modzzz_jobs_featured_points')=='on'){
			
 			$sPaymentUnit = 'points';
			$sMoneyBalanceLabelC = _t("_modzzz_jobs_form_caption_points_balance");
			$sMoneyTypeC = _t("_modzzz_jobs_points");

			$oPoint = BxDolModule::getInstance('BxPointModule');   
			$sBuyPointsLink = BX_DOL_URL_ROOT . $oPoint->_oConfig->getBaseUri() . 'purchase_points'; 
			$iMoneyBalance = $oPoint->_oDb->getMemberPoints($this->_iProfileId);
			$iFeaturedCost = (int)$oPoint->_oDb->getActionValue($this->_iProfileId, 'modzzz_jobs', 'feature');
			if($iMoneyBalance < $iFeaturedCost){
				$sCode = _t('_modzzz_jobs_msg_insufficient_points_feature', number_format($iMoneyBalance,0), number_format($iFeaturedCost,0));
				$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyPointsLink);
 			}
		}
 
		if($sCode) {
			$sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array('content' => $sCode));
	 
			$aVarsPopup = array (
				'title' => _t('_modzzz_jobs_page_title_feature_item'),
				'content' => $sCode,
			);        
			
			echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true);
			exit;
		}
  
  		$aQuantity = array(); 
		for($iter=1; $iter<=1000; $iter++)
			$aQuantity[$iter] = $iter;

		$aCustomForm = array(

			'form_attrs' => array(
				'id' => 'jobs_form',
				'name' => 'jobs_form',
				'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'pay_feature/' . $iEntryId,
				'method' => 'post',
				'onsubmit' => "return bx_ajax_form_check(this)",
			),

			'params' => array (
				'db' => array(
					'submit_name' => 'submit_form', 
				),
			),

			'inputs' => array (

				'header_info' => array(
					'type' => 'block_header',
					'caption' => _t("_modzzz_jobs_form_caption_feature", $sTitle),
				), 
				'money_balance' => array(
					'type' => 'custom',
					'caption' => $sMoneyBalanceLabelC,
					'content' => number_format($iMoneyBalance,0) .' '. $sMoneyTypeC,
				), 
				'feature_cost' => array(
					'type' => 'custom',
 					'caption' => _t("_modzzz_jobs_form_caption_cost_to_feature"),
					'content' => number_format($iFeaturedCost,0) .' '. $sMoneyTypeC .' '. _t("_modzzz_jobs_form_per_day"),
				),   
                'quantity' => array(
                    'caption'  => _t('_modzzz_jobs_caption_num_featured_days'),
                    'type'   => 'select',
                    'name' => 'quantity',
                    'values' => $aQuantity,
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_modzzz_jobs_caption_err_featured_days'),
                    ),
                ),
				'submit_form' => array(
					'type' => 'hidden',
					'name' => 'submit_form', 
					'value' => 1,
				),
				'submit' => array(
					'type' => 'submit',
					'name' => 'submit',
					'value' => _t("_modzzz_jobs_form_caption_submit"),
				),
			)
		);
		  
		$oForm = new BxTemplFormView($aCustomForm);
		$oForm->initChecker();

		if ( $oForm->isSubmittedAndValid() ) {
			
			$iQuantity = (int)$oForm->getCleanValue('quantity');
			$iTotalCost = $iQuantity * $iFeaturedCost;
 
			if(getParam('modzzz_jobs_featured_credits')=='on'){ 
	 
				if($iMoneyBalance < $iTotalCost){
					$sCode = _t('_modzzz_jobs_msg_insufficient_credits_feature', number_format($iMoneyBalance,0), number_format($iTotalCost,0));
					$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyCreditsLink);
					echo MsgBox($sCode);
					return;
				}
			}elseif(getParam('modzzz_jobs_featured_points')=='on'){
	 
				if($iMoneyBalance < $iTotalCost){
					$sCode = _t('_modzzz_jobs_msg_insufficient_points_feature', number_format($iMoneyBalance,0), number_format($iTotalCost,0));
					$sCode .= '&nbsp;'._t('_modzzz_jobs_buy_link', $sBuyPointsLink);
					echo MsgBox($sCode);
					return;
				}
			}
  
  			$bSuccess = $this->_oDb->deductPayment('feature', $sPaymentUnit, $this->_iProfileId, $iEntryId, $iQuantity, $iTotalCost); 		 

			$sResultMsg = ($bSuccess) ? _t("_modzzz_jobs_msg_feature_success") : _t("_modzzz_jobs_msg_feature_failure"); 
		  
			if($bSuccess){
				$this->_oDb->updateFeaturedStatus($iEntryId);
				$this->_oDb->updateFeaturedEntryExpiration($iEntryId, $iQuantity); 
			} 
		 
			$sCode = MsgBox($sResultMsg);
		} else {
			$sCode = $oForm->getCode();
		}
 
		// check whether form submitted (AJAX) and show corresponding output
		if (bx_get('BxAjaxSubmit')) {
 
			$sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/'. $aDataEntry['uri'];
			echo $sCode . genAjaxyPopupJS($aDataEntry['id'], 'ajaxy_popup_result_div', $sRedirect);
 
		}else{

			$sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array('content' => $sCode));
	 
			$aVarsPopup = array (
				'title' => _t('_modzzz_jobs_page_title_feature_item'),
				'content' => $sCode,
			);        
			
			echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true); 
		}
 	}
 
    function isAllowedPurchaseFeatured ($iEntryId, $isPerformAction = false) {
 		
		if ($this->isAdmin())
            return false;

		//if(getParam("modzzz_jobs_buy_featured")!='on')
		$iEntryId = (int)$iEntryId;
		$aDataEntry = $this->_oDb->getEntryById ($iEntryId);

		if(getParam("modzzz_jobs_featured_credits")!='on' && getParam("modzzz_jobs_featured_points")!='on')
			return false;
  
		if($aDataEntry['featured'] && !$aDataEntry['featured_expiry_date'])
            return false;
 
        if (!( ($GLOBALS['logged']['admin']||$GLOBALS['logged']['member']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_JOBS_PURCHASE_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    } 



	//logo mod
    function processLogo($iEntryId) {
 	    $iEntryId  = (int)$iEntryId;

 		$sIcon = $this->_actionUploadLogoIcon($iEntryId);

		if($sIcon){	
			$this->_oDb->updatePostWithLogo($iEntryId, $sIcon);  
		}
	}

    function _actionUploadLogoIcon ( $iEntryId=0 ) {

		$iEntryId  = (int)$iEntryId;

		$iIconWidth = (int)getParam("modzzz_jobs_icon_width");
		$iIconHeight = (int)getParam("modzzz_jobs_icon_height"); 
		$sIcon = "";
	  
		$sFile = "iconfile";
 		$sPath = $this->_oConfig->getMediaPath();	
		if ( 0 < $_FILES[$sFile]['size'] && 0 < strlen( $_FILES[$sFile]['name'] ) ) {
			$sFileName = time();
			$sExt = $this->moveUploadedImage( $_FILES, $sFile, $sPath . $sFileName, '', false );
			if( strlen( $sExt ) && !(int)$sExt ) {
			 
				if($iEntryId)
					$this->_oDb->_actionRemoveIcon($iEntryId);
 
				$sFullPath = $sPath.$sFileName.$sExt;
 
				imageResize( $sFullPath, $sFullPath, $iIconWidth, $iIconHeight);
				
				chmod( $sFullPath, 0644 );
				 
				if ($sExt != '')
					$sIcon = $sFileName.$sExt;
			} 
		}
 
		return $sIcon;
	}
 
	function actionLogo($sAction, $iEntryId){
	    $iEntryId  = (int)$iEntryId;

		if($sAction=='remove'){
			if ($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin())) {			
				$this->_oDb->_actionRemoveIcon($iEntryId); 

				$this->_oDb->updatePostWithLogo($iEntryId);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

				header ('Location:' . $sRedirectUrl);
			}else{
                $this->_oTemplate->displayPageNotFound ();
                return; 
			}
		}else{
			$this->_oTemplate->displayPageNotFound ();
			return; 
		}

	}
	//end - logo modification

	function moveUploadedImage( $aFiles, $fname, $path_and_name, $maxsize='', $imResize='true' )
	{
		global $max_photo_height;
		global $max_photo_width;

		$height = $max_photo_height;
		if ( !$height )
			$height = 400;
		$width = $max_photo_width;
		if ( !$width )
			$width = 400;

		if ( $maxsize && ($aFiles[$fname]['size'] > $maxsize || $aFiles[$fname]['size'] == 0) ) {
			if ( file_exists($aFiles[$fname]['tmp_name']) ) {
				unlink($aFiles[$fname]['tmp_name']);
			}
			return false;
		} else {
			$scan = getimagesize($aFiles[$fname]['tmp_name']);

			if ( ($scan['mime'] == 'image/jpeg' && $ext = '.jpg' ) ||
				( $scan['mime'] == 'image/gif' && $ext = '.gif' ) ||
				( $scan['mime'] == 'image/png' && $ext = '.png' ) ) //deleted .bmp format
			{

				$path_and_name .= $ext;
				move_uploaded_file( $aFiles[$fname]['tmp_name'], $path_and_name );

				if ( $imResize )
					imageResize( $path_and_name, $path_and_name, $width, $height );

			} else {
				return IMAGE_ERROR_WRONG_TYPE;
			}
		}

		return $ext;
	}



}
