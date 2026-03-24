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

bx_import('BxDolTwigPageView');

class BxJobsPageView extends BxDolTwigPageView {	

	function BxJobsPageView(&$oMain, &$aDataEntry) {
		parent::BxDolTwigPageView('modzzz_jobs_view', $oMain, $aDataEntry);

        $this->sSearchResultClassName = 'BxJobsSearchResult';
        $this->sFilterName = 'filter';

        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/'. $this->aDataEntry['uri'];
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');   
	}
		 
	function getBlockCode_Company() {
 	    
		$aData = $this->aDataEntry;
		
		 $aAuthor = getProfileInfo($aData['author_id']);
 
		$sAuthorName =  $aAuthor['NickName'];
		
		// Freddy ajout
		$sAuthorFirst_Name_Last_Name =  $aAuthor['FirstName'] .''.  $aAuthor['LastName'];
		
		//////////////////////
		$sAuthorLink = getProfileLink($aAuthor['ID']);	
		$icoThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aAuthor['ID'], 'none');
 
 
		//Freddy mise en commenatire pour afficher le bloc company pour ceux qui n ont pas poster le job en tant qu individuel
		//if(!$aData['company_id']) return;
			  
		if($aData['company_type']=='job'){
			$aCompanyData = $this->_oDb->getCompanyById($aData['company_id']);
			
			//$sImage = $this->_oDb->getCompanyIcon($aCompanyData['id'], $aCompanyData['company_icon'],true);
			$sImage = $icoThumb;
    		
			
			$sCompanyUri = $aCompanyData['company_uri'];
			
			
			//$sCompanyName = $aCompanyData['company_name'];
			$sCompanyName = $sAuthorFirst_Name_Last_Name ;
		
		   // $sCompanyUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'companyview/' . $aCompanyData['company_uri'];
		   $sCompanyUrl = $sAuthorLink;
			
			 
			$sCompanyCountry = $aCompanyData['company_country'];
			$sCompanyState = ($aCompanyData['company_state']) ? $this->_oDb->getStateName($sCompanyCountry, $aCompanyData['company_state']) : '';
			$sCompanyCity = $aCompanyData['company_city'];
			$iCreated = $aCompanyData['created'];

		}else{
			$oListing = BxDolModule::getInstance('BxListingModule');

			$aCompanyData = $oListing->_oDb->getEntryById($aData['company_id']);

			$sImage = '';
			if ($aCompanyData['thumb']) {
				$a = array ('ID' => $aCompanyData['author_id'], 'Avatar' => $aCompanyData['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			} 
 
 			$sCompanyName = $aCompanyData['title'];
			$sCompanyUrl = BX_DOL_URL_ROOT . $oListing->_oConfig->getBaseUri() . 'view/' . $aCompanyData['uri']; 
			$sCompanyCountry = $aCompanyData['country'];
			$sCompanyState = ($aCompanyData['company_state']) ? $this->_oDb->getStateName($sCompanyCountry, $aCompanyData['company_state']) : '';
			$sCompanyCity = $aCompanyData['city'];
			$iCreated = $aCompanyData['created'];
		}

       
 		$aCategory = $this->_oDb->getCategoryById($aData['parent_category_id']);
		$sCategoryUrl = $this->_oDb->getCategoryUrl($aCategory['uri']); 
		if($aData['category_id']){
			$aSubCategory = $this->_oDb->getCategoryById($aData['category_id']); 
			$sSubCategoryUrl = $this->_oDb->getSubCategoryUrl($aSubCategory['uri']);
		}
		
		//Freddy Job Location
		$sJobFlag = genFlag($aData['country']);
		$sJobcity = $aData['city'];
		$sJobzip = $aData['zip'];
		$sJobCountry= $aData['country'];	
		$sJobstate = ($aData['state']) ? $this->_oMain->_oDb->getStateName($sJobCountry, $aData['state']) : '';
	    $aProfile = getProfileInfo($this->_oMain->_iProfileId);
		
	/*
		  $aJobType ='<span style="font-size:10px;"></span>' . '&nbsp;'.'|'.'&nbsp;'. _t($GLOBALS['aPreValues']['JobType'][$aData['job_type']]['LKey']);
		$aJobStatus = '<span style="font-size:10px;"></span>'. '&nbsp;'.'|'.'&nbsp;'. _t($GLOBALS['aPreValues']['JobStatus'][$aData['job_status']]['LKey']);
		*/
		
		// Freddy pour afficher le nombre des poste au singulier ou au pluriel		
		if($aData['vacancies']=='1'){
			$aVacancies = _t('_modzzz_jobs_vacancies_single') ; 
		}
		else{
			$aVacancies = _t('_modzzz_jobs_vacancies') ; 
		}
		
		// Freddy pour afficher le nombre des candidatures au singulier ou au pluriel
        $application_single_plural = $this->_oDb->getApplicationsCount($aData['id']);
		if($application_single_plural<= '1'){
			$aApplications = _t('_modzzz_jobs_form_unit_view_applications_single') ; 
		}
		else{
			$aApplications = _t('_modzzz_jobs_form_unit_view_applications') ;  
		}
		
		
		// Freddy ajout  salary management
		$sCurrency = $aData['currency'];
		
		if  ($aData['max_salary'] || $aData['min_salary']){
		$aSalary_Minimum = ($aData['min_salary']) ? $sCurrency .''. number_format($aData['min_salary'] , 0, ',', ' ') : ' ';
		$aSalary_Maximum = ($aData['max_salary']) ? $sCurrency .''. number_format($aData['max_salary'], 0, ',', ' ') :  _t('_modzzz_jobs_upwards');
		$aSalary_Type = _t($GLOBALS['aPreValues']['JobSalaryType'][$aData['salary_type']]['LKey']);
		$aSeparateur=   '-';
				  
		}
		else{
			$aSalary_Negociable=   _t('_modzzz_jobs_Salary_Negociable');
	    }
		
		
      // Freddy pour afficher les candidatures que lorsque le count est superier à 10
       /* $application_count= $this->_oDb->getApplicationsCount($aData['id']);
		
		if ( $application_count >='10') {
			$aApplicant_be_the_first_or_not = '<strong>'. $application_count .'</strong>'. ' ' ._t('_modzzz_jobs_form_unit_view_applications');
		}
		else {
			$aApplicant_be_the_first_or_not= _t('_modzzz_jobs_Be_one_of_the_first_ten_applicants');
		}
		*/
		
		
		///Freddy le message selon que le membre a pply ou pas 
		$application_count= $this->_oDb->getApplicationsCount($aData['id']);
          if($this->_oDb->hasApplied($aProfile['ID'],$aData['id']))
		  { 
		  $smessage_applicant = '<i class="sys-icon desktop"></i>'.'&nbsp;'. _t('_modzzz_jobs_already_applied_for') ? '<i class="sys-icon desktop"></i>' .'&nbsp;'. _t('_modzzz_jobs_already_applied_for') : '';
		  }
		  else if ($application_count >='5'){
			// $smessage_applicant = '<strong>'. $application_count .'</strong>'. ' ' ._t('_modzzz_jobs_form_unit_view_applications');
			$smessage_applicant = '';
		  }
		  else{ $smessage_applicant= '<i class="sys-icon user"></i>'.' '._t('_modzzz_jobs_Be_one_of_the_first_ten_applicants');
		  }
	
		  ///////////////////
		  
		  
		 // freddy pour ne pas afficher le bouton si le membre a deja postuler
		  if (!$this->_oDb->hasApplied($aProfile['ID'],$aData['id'])&& $aData['application_link'] ){ 
		   
		  $aApplyLink = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'apply/' . $aData['id'];
		  $aAPPLY =  _t('_modzzz_jobs_easy_apply');
					
		  }
		  else{ $applyLin = '';
		       $aAPPLY =  '';		 
			  }
		  
		 $add_favorite_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'mark_favorite/' . $aData['id'];
      
	    $aVars = array (
		 'AddToFavorite' => $this->_oMain->isAllowedMarkAsFavorite($aData) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $aData['id']) ? _t('_modzzz_jobs_action_remove_from_favorite') : _t('_modzzz_jobs_action_add_to_favorite')) : '',
			 
			  'Favorite_Url' =>  $add_favorite_url, 
			  'id' => $aData['id'],
		
		
		'bx_if:applyLink' => array( 
				'condition' => !$this->_oDb->hasApplied($aProfile['ID'],$aData['id']) && !$aData['application_link'],
				'content' => array(
					'applyLink' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'apply/' . $aData['id'],
					'APPLY' =>  _t('_modzzz_jobs_easy_apply'),
					
				) 
			),
			
			
			
			
			
			'bx_if:applyLinkExternal' => array( 
				'condition' => !$this->_oDb->hasApplied($aProfile['ID'],$aData['id'])&& $aData['application_link'],
				'content' => array(
					'applyLinkExternal' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'apply/' . $aData['id'],
					'APPLYExternal' =>  _t('_modzzz_jobs_candidater')
					
				) 
			),
			
			
		
		////// Freddy add
		'FullName'=> $sAuthorFirst_Name_Last_Name,
		
		 // 'Applicant_be_the_first_or_not' => $aApplicant_be_the_first_or_not,
			
			'Salary_Minimum' =>$aSalary_Minimum ,
		     'Salary_Maximum' =>$aSalary_Maximum ,
		      'Salary_Type' => $aSalary_Type ,
			 'Salary_Negociable'=> $aSalary_Negociable,
			  'Separateur' => $aSeparateur,
			  
			  ///////////////////freddy
			
			 
			
			 
			 
			/*
			'bx_if:DisplayApply' => array( 
				'condition' => !$this->_oDb->hasApplied($aProfile['ID'],$aData['id']) && !$aData['application_link'],
				'content' => array(
		         'applyLink' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'apply/' . $aData['id'],
				 'message_title' => _t('_modzzz_jobs_candidater') ? _t('_modzzz_jobs_candidater') : '',
				 
					
				) 
			),
			*/
		    
			
				'bx_if:Job_Status_Type' => array( 
				'condition' => ($aData['job_status'] || $aData['job_type']),
				'content' => array(
					'job_status' => _t($GLOBALS['aPreValues']['JobStatus'][$aData['job_status']]['LKey']),
					'job_type' =>  _t($GLOBALS['aPreValues']['JobType'][$aData['job_type']]['LKey']),
					
				) 
			), 
			
			
		
		
		 // Freddy ajout
		 'title' => $aData['title'],
		   'city' => $aData['city'],
			'zip' => $aData['zip'],
			 'Application_count' => $this->_oDb->getApplicationsCount($aData['id']),
			 'Application_caption'=> $aApplications,
			 'vacancies' => ($aData['vacancies']) ? $aData['vacancies'] : 1, 
			'vacancies_caption' => $aVacancies,
			 
			 'drapeau' => $sJobFlag , 
			 'province' =>$sJobstate,
			 'country' => _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']),
			 'vacancies' => ($aData['vacancies']) ? $aData['vacancies'] : 1, 
			 // Fin freddy ajout
			 
			 // freddy stage or job seeker or provider
		/*
		 'bx_if:post_type_stage' => array( 
								'condition' =>($aData['post_type']=='seeker'),
								'content' => array(
             'post_type_stage' => _t('_modzzz_jobs_post_type_stage'), 
			  'job_status' => $aJobStatus,
			 'job_type' => $aJobType,
								), 
							),
							
			 'bx_if:post_type_emploi' => array( 
								'condition' =>($aData['post_type']=='provider'),
								'content' => array(
              'post_type_emploi' => _t('_modzzz_jobs_post_type_emploi'),  
			  'job_status' => $aJobStatus,
			 'job_type' => $aJobType,
								), 
							),
				*/
							
		
			 
			
			// freddy
			 'bx_if:experience' => array( 
								'condition' => $aData['experience'],
								'content' => array(
             'experience' => _t($GLOBALS['aPreValues']['JobExperience'][$aData['experience']]['LKey']),
								), 
							),
							
			'bx_if:qualification' => array( 
								'condition' => $aData['qualification'],
								'content' => array(
             'qualification' => _t($GLOBALS['aPreValues']['JobEducation'][$aData['qualification']]['LKey']),
								), 
							),
							
			'bx_if:career_level' => array( 
								'condition' => $aData['career_level'],
								'content' => array(
             'career_level' => _t($GLOBALS['aPreValues']['JobCareerLevel'][$aData['career_level']]['LKey']),
								), 
							),
			
			
		
			'company_thumb' => $sImage ? $sImage : $this->_oTemplate->getIconUrl('no-photo.png'), 
			'company_url' => $sCompanyUrl,
			'company_name' => $sCompanyName,
			'company_created' => getLocaleDate($iCreated, BX_DOL_LOCALE_DATE_SHORT),
			'company_created_ago' => defineTimeInterval($iCreated),
  
			'country_city' => (trim($sCompanyCity) ? $sCompanyCity.', ' : '') .  (trim($sCompanystate) ? $sCompanystate.', ' : '') . _t($GLOBALS['aPreValues']['Country'][$sCompanyCountry]['LKey']), 

            'author_thumb' => $icoThumb,
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
 
			'category_name' => $aCategory['name'],
			'category_url' => $sCategoryUrl,

            'bx_if:sub_category' => array (
                'condition' => $this->aDataEntry['category_id'],
                'content' => array ( 
					'subcategory_name' => $aSubCategory['name'],
					'subcategory_url' => $sSubCategoryUrl,
                ),
            ),

			'tags' => $this->_oTemplate->parseTags($aData['tags']),
            'fields' => '',
            'author_username' => $sAuthorName,
            'author_url' => $sAuthorLink,
        );

        return array($this->_oTemplate->parseHtmlByName('block_company', $aVars));
		
		
    }
   
	function getBlockCode_Info() {

		//Freddy Commentaire : afficher info même pour les entreprises
		//if($this->aDataEntry['company_id']) return;
			 
			 
        return array($this->_blockInfo ($this->aDataEntry));
		
    }

	//override the similar mod in the parent class
    function _blockInfo ($aData) {

        $aAuthor = getProfileInfo($aData['author_id']);
 
		$sAuthorName =  $aAuthor['NickName'];
		$sAuthorLink = getProfileLink($aAuthor['ID']);	
		$icoThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aAuthor['ID'], 'none', true);
 
 		$aCategory = $this->_oDb->getCategoryById($aData['parent_category_id']);
		$sCategoryUrl = $this->_oDb->getCategoryUrl($aCategory['uri']); 
		if($aData['category_id']){
			$aSubCategory = $this->_oDb->getCategoryById($aData['category_id']); 
			$sSubCategoryUrl = $this->_oDb->getSubCategoryUrl($aSubCategory['uri']);
		}
		
		//Freddy ajout Befirend
		$iUserId = getLoggedId();
		$iId = $aData['author_id'];
		$aFriends = getMyFriendsEx($iUserId);
		$aFriendsIds = array_keys($aFriends);
		// [END] Freddy ajout Befirend

        $aVars = array (
		
		 //Freddy ajout Befirend
	        'bx_if:befriend' => array(
	        'condition' => !in_array($iId, $aFriendsIds) && !isFriendRequest($iUserId, $iId) &&  !$this->_oMain->isEntryAdmin($aData),
	        'content' => array(
	        'id' => $iId
					    ),
				           ),
		// [END] Freddy ajout Befirend
		
		 // [END] Freddy Contact job poster
		 'bx_if:contacter_url' => array(
	                'condition' =>  !$this->_oMain->isEntryAdmin($aData),
	                'content' => array(
	                
					'contacter_url' => BX_DOL_URL_ROOT . 'mail.php?mode=compose&recipient_id=' . $aData['author_id'],
					    ),
				           ),
						   
		
            'author_thumb' => $icoThumb,
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
            
			'category_name' => $aCategory['name'],
			'category_url' => $sCategoryUrl,

            'bx_if:sub_category' => array (
                'condition' => $this->aDataEntry['category_id'],
                'content' => array ( 
					'subcategory_name' => $aSubCategory['name'],
					'subcategory_url' => $sSubCategoryUrl,
                ),
            ),
			
			 'bx_if:experience' => array( 
								'condition' => $aData['experience'],
								'content' => array(
             'experience' => _t($GLOBALS['aPreValues']['JobExperience'][$aData['experience']]['LKey']),
								), 
							),
							
			'bx_if:qualification' => array( 
								'condition' => $aData['qualification'],
								'content' => array(
             'qualification' => _t($GLOBALS['aPreValues']['JobEducation'][$aData['qualification']]['LKey']),
								), 
							),
							
			'bx_if:career_level' => array( 
								'condition' => $aData['career_level'],
								'content' => array(
             'career_level' => _t($GLOBALS['aPreValues']['JobCareerLevel'][$aData['career_level']]['LKey']),
								), 
							),
				
				'bx_if:Job_Status_Type' => array( 
				'condition' => ($aData['job_status'] || $aData['job_type']),
				'content' => array(
					'job_status' => _t($GLOBALS['aPreValues']['JobStatus'][$aData['job_status']]['LKey']),
					'job_type' =>  _t($GLOBALS['aPreValues']['JobType'][$aData['job_type']]['LKey']),
					
				) 
			), 
			
			
				
			'tags' => $this->_oTemplate->parseTags($aData['tags']),
            'fields' => '', 

			'bx_if:expires' => array( 
				'condition' => (($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($aData)) && $aData['expiry_date']),
				'content' => array(   
					'expire' => date('d.m.Y', $aData['expiry_date']) 
 				),  		
			), 
			'bx_if:featured' => array( 
				'condition' => (($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($aData)) && $aData['featured']),
				'content' => array(    
					'featured' => $aData['featured_expiry_date'] ? _t('_modzzz_jobs_featured_until') .' '. $this->_oTemplate->filterCustomDate($aData['featured_expiry_date']) : _t('_modzzz_jobs_featured_jobs'),  
				),  		
			), 
        );

        return $this->_oTemplate->parseHtmlByName('entry_view_block_info', $aVars);
    }

	function getBlockCode_Desc() {
        return array($this->_oTemplate->blockDesc ($this->aDataEntry));
    }

	function getBlockCode_Skills() {

		if(!trim($this->aDataEntry['skills']))
			return;
 
        return $this->_oTemplate->blockSkills ($this->aDataEntry);
    }
 
 	function getBlockCode_VideoEmbed() {

		$aVideoUrls = $this->_oDb->getYoutubeVideos($this->aDataEntry['id']);
		
		$sFirstVideoId = '';
		$sFirstVideoTitle = '';
		$aVideos = array();
		if(empty($aVideoUrls))
			return;

		foreach($aVideoUrls as $aEachUrl){  
			$sFirstVideoId = ($sFirstVideoId) ? $sFirstVideoId : $this->_oTemplate->youtubeId($aEachUrl['url']);
			$sFirstVideoTitle = ($sFirstVideoTitle) ? $sFirstVideoTitle : $aEachUrl['title'];
			$aVideos[] = array ( 
				'video_id' => $this->_oTemplate->youtubeId($aEachUrl['url']), 
				'video_title' => process_db_input($aEachUrl['title']), 
			);
		}

		$aVars = array(
			'video_id' => $sFirstVideoId,
			'video_title' => $sFirstVideoTitle,
			'bx_repeat:video' => $aVideos
		);
		 
        return $this->_oTemplate->parseHtmlByName('block_youtube_videos', $aVars);   
    }

	function getBlockCode_Photo() {
        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Video() {
        return $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Sound() {
        return $this->_blockSound ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'sounds'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Files() {
        return $this->_blockFiles ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'files'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Rate() {
        modzzz_jobs_import('Voting');
        $o = new BxJobsVoting ('modzzz_jobs', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_jobs_import('Cmts');
        $o = new BxJobsCmts ('modzzz_jobs', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

            $oSubscription = new BxDolSubscription();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'modzzz_jobs', '', (int)$this->aDataEntry['id']);

 			$isFan = $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 0) || $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 1);

            $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'TitleSubscribe' => $aSubscribeButton['title'], 
                'TitleApply' => $this->_oMain->isAllowedApply($this->aDataEntry) ? _t('_modzzz_jobs_action_title_apply') : '',
				'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_modzzz_jobs_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_jobs_action_title_delete') : '',
				'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_modzzz_jobs_action_title_invite') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_jobs_action_title_share') : '',
                'TitleJoin' => $this->_oMain->isAllowedJoin($this->aDataEntry) ? ($isFan ? _t('_modzzz_jobs_action_title_leave') : _t('_modzzz_jobs_action_title_join')) : '',
                'IconJoin' => $isFan ? 'signout' : 'signin',
				'TitleBroadcast' => $this->_oMain->isAllowedBroadcast($this->aDataEntry) ? _t('_modzzz_jobs_action_title_broadcast') : '',
 				
				//Freddy Manage Applications 08 juillet 2015
				'TitleManageApplications' => $this->_oMain->isAlloweManageApplications($this->aDataEntry) ? _t('_modzzz_jobs_action_title_manage_applicants') : '',
				
				'TitleBroadcastApplicants' => $this->_oMain->isAllowedBroadcast($this->aDataEntry) ? _t('_modzzz_jobs_action_title_broadcast_applicants') : '',
				'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_jobs_action_remove_from_featured') : _t('_modzzz_jobs_action_add_to_featured')) : '',
				'TitlePurchaseFeatured' => '',
				'AddToFavorite' => $this->_oMain->isAllowedMarkAsFavorite($this->aDataEntry) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $this->aDataEntry['id']) ? _t('_modzzz_jobs_action_remove_from_favorite') : _t('_modzzz_jobs_action_add_to_favorite')) : '',
				'TitlePurchaseFeatured' => '',
				/*'TitlePurchaseFeatured' => $this->_oMain->isAllowedPurchaseFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_jobs_action_title_extend_featured') : _t('_modzzz_jobs_action_title_purchase_featured')) : '',*/
                'TitleRelist' => $this->_oMain->isAllowedRelist($this->aDataEntry) ? _t('_modzzz_jobs_action_title_relist') : '',
                'TitleExtend' => $this->_oMain->isPaidListing($this->aDataEntry['id']) ? ($this->_oMain->isAllowedExtend($this->aDataEntry) ? _t('_modzzz_jobs_action_title_extend') : '') : '',
                'TitlePremium' => $this->_oMain->isAllowedPremium($this->aDataEntry) ? ($this->_oMain->isPaidListing($this->aDataEntry['id']) ? '' : _t('_modzzz_jobs_action_title_premium')) : '',

				'TitleManageFans' => $this->_oMain->isAllowedManageFans($this->aDataEntry) ? _t('_modzzz_jobs_action_manage_fans') : '',
			    'AddToToday' => $this->_oMain->isAllowedMarkAsToday($this->aDataEntry) ? ($this->aDataEntry['today_job'] ? _t('_modzzz_jobs_action_remove_from_today') : _t('_modzzz_jobs_action_add_to_today')) : '',
                'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_modzzz_jobs_action_upload_photos') : '',
	            'TitleEmbed' => $this->_oMain->isAllowedEmbed($this->aDataEntry) ? _t('_modzzz_jobs_action_embed_video') : '',  
                'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_modzzz_jobs_action_upload_videos') : '',
                'TitleUploadSounds' => $this->_oMain->isAllowedUploadSounds($this->aDataEntry) ? _t('_modzzz_jobs_action_upload_sounds') : '',
                'TitleUploadFiles' => $this->_oMain->isAllowedUploadFiles($this->aDataEntry) ? _t('_modzzz_jobs_action_upload_files') : '',
            );

            if (!$aInfo['TitleRelist'] && !$aInfo['TitleExtend'] && !$aInfo['TitlePremium'] && !$aInfo['TitleEdit'] && !$aInfo['TitleDelete'] && !$aInfo['TitleJoin'] && !$aInfo['TitleManageFans'] && !$aInfo['TitleInvite'] && !$aInfo['TitleShare'] && !$aInfo['TitleBroadcast'] && !$aInfo['TitleBroadcastApplicants'] && !$aInfo['AddToFeatured'] && !$aInfo['TitleUploadPhotos'] && !$aInfo['TitleEmbed'] && !$aInfo['TitleUploadVideos'] && !$aInfo['TitleUploadSounds'] && !$aInfo['TitleUploadFiles']) 
                return '';

            return $oSubscription->getData() . $oFunctions->genObjectsActions($aInfo, 'modzzz_jobs');
        } 

        return '';
    }    
  
	function getBlockCode_Local() {   
		$iCompanyId = $this->aDataEntry['company_id'];

		if(!$iCompanyId)
			return;

		$aCompany = $this->_oDb->getCompanyEntryById($iCompanyId);
		$sCity = $aCompany['company_city'];

		return $this->ajaxBrowse('local', $this->_oDb->getParam('modzzz_jobs_perpage_main_recent'),array(),$sCity,$this->aDataEntry['id']); 
	}
  
	function getBlockCode_Stats() {
        return array($this->_blockCustomDisplay ($this->aDataEntry, 'statistics'));
    }
 
	function getBlockCode_Location() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'location');
    }
  
	function _blockCustomDisplay($aDataEntry, $sType) {
		
		switch($sType)
		{  
			case "statistics":
				$aAllow = array('role', 'vacancies', 'experience', 'qualification', 'job_type', 'job_status');
			break;
			case "location":
				$aAllow = array('address1','city','state','country','zip');
			break;  
		}
  
		$sFields = $this->_oTemplate->blockFields($aDataEntry, $aAllow);

		$aVars = array ( 
            'fields' => $sFields, 
        );

        return $this->_oTemplate->parseHtmlByName('custom_block_info', $aVars);   
    }
 
	function getBlockCode_Other() {    
 
		if(!$this->aDataEntry['company_id'])
			return;

		return $this->ajaxBrowse('other', $this->_oDb->getParam('modzzz_jobs_perpage_main_recent'),array(),$this->aDataEntry['company_id'],$this->aDataEntry['id']); 
	}

    function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $sValue2 = '', $isDisableRss = false, $isPublicOnly = true) {
        $oMain = BxDolModule::getInstance('BxJobsModule');

        bx_import ('SearchResult', $oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue, $sValue2);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);
 
        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => getTemplateIcon('rss.png'))));

        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);

        if (!($s = $o->displayResultBlock())) 
		//Freddy mise en commentaire et modif pour masquer les Blocks vide
		   // return $isPublicOnly ? array(MsgBox(_t('_Empty')), $aMenu) : '';
            return ;


        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->_oConfig->getBaseUri() . $o->sBrowseUrl);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    }   

    function getBlockCode_Fans() {

		//fredd seeker
	/*	if($this->aDataEntry['post_type'] == 'seeker')
			return;
			*/

        return parent::_blockFans ($this->_oDb->getParam('modzzz_jobs_perpage_view_fans'), 'isAllowedViewFans', 'getFans');
    }            

    function getBlockCode_FansUnconfirmed() {

		//fredd seeker
	/*	if($this->aDataEntry['post_type'] == 'seeker')
			return;
			*/

        return parent::_blockFansUnconfirmed (BX_JOBS_MAX_FANS);
    }

	function getBlockCode_Forum() {
  		
		$iEntryId = (int)$this->aDataEntry['ID'];

		$iNumComments = getParam("modzzz_jobs_perpage_main_comment");
		$aPosts = $this->_oDb->getLatestForumPosts($iNumComments, $iEntryId);
 
		if(empty($aPosts))
			return;
 		 
		$aVars['bx_repeat:entries'] = array();
  		foreach($aPosts as $aEachPost){

			$sForumUri = $aEachPost['forum_uri'];
			$sTopic = $aEachPost['topic_title']; 
			$sTopicUri = $aEachPost['topic_uri'];
			$sPostText = $aEachPost['post_text']; 
			$sDate = defineTimeInterval($aEachPost['when']); 
			$sJobsName = $aEachPost['title']; 
 			$sPoster = $aEachPost['user']; 

			$iLimitChars = (int)getParam('modzzz_jobs_forum_max_preview');
			$sPostText = $this->oMain->_formatSnippetText($aEachPost, $iLimitChars, $sPostText);
 
  			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail(getID($sPoster));

			$sImage = '';
			if ($aEachPost['thumb']) {
				$a = array ('ID' => $aEachPost['author_id'], 'Avatar' => $aEachPost['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}

			$sJobsUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sForumUri;
			$sTopicUrl = BX_DOL_URL_ROOT . 'forum/jobs/topic/'.$sTopicUri.'.htm';

			$aVars['bx_repeat:entries'][] = array( 
							'topic_url' => $sTopicUrl, 
							'topic' => $sTopic, 
							'snippet_text' => $sPostText, 
							'item_title' => $sJobsName, 
							'item_url' => $sJobsUrl, 
							'created' => $sDate,
							'author_url' => getProfileLink(getID($sPoster)),
							'author' => $sPoster,
							'thumb_url' => $sMemberThumb,
						);
		}

		$sCode = $this->_oTemplate->parseHtmlByName('job_forum', $aVars);  

		return $sCode;
	}
   
	function getBlockCode_Logo() {  
		
		if(!$sIcon = $this->aDataEntry['icon']) return;
			 
		$aVars = array (
		    'image' => $this->_oDb->getLogo($this->aDataEntry['id'], $sIcon),
			'bx_if:remove' => array( 
				'condition' => $this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry),
				'content' => array( 
					'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'logo/remove/' . $this->aDataEntry['id'],   
				), 
			) 
 		);
		return $this->_oTemplate->parseHtmlByName('block_logo', $aVars);	 
	}

    function getCode() {
 
        $this->_oMain->_processFansActions ($this->aDataEntry, BX_JOBS_MAX_FANS);

        return parent::getCode();
    }
	
	
	/////////////////////////FREDDY AJOUT BLOC MODIFIER --- VOIR CANDIDATS---ECRIRE AUX CANDIDATS///////////////////////////////////////////////////
	
	function getBlockCode_ManageJobs() {
    
		if(!$this->_oMain->isAllowedEdit($this->aDataEntry)) return; 

	$invite_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'invite/' . $this->aDataEntry['id']; 
	$broadcast_applicants_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'broadcast_applicants/' . $this->aDataEntry['id']; 
    $manage_applicants_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse_applications/' . $this->aDataEntry['uri'];
	$edit_jobs_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'edit/' . $this->aDataEntry['id'];
	$photos_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'upload_photos/' . $this->aDataEntry['uri'];
	$supprimer_edit_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'delete/' . $this->aDataEntry['id'];

		
  
		$aVars = array(  
			
			'broadcast_applicants_url' => $broadcast_applicants_url, 
			'manage_applicants_url' => $manage_applicants_url, 
			 'invite_Url' =>  $invite_url, 
			 'TitleEdit_Url' =>  $edit_jobs_url, 
			  
			 'Photos_Url' =>  $photos_url, 
			 'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_modzzz_jobs_action_upload_photos') : '',
			 
			 'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_jobs_action_remove_from_featured') : _t('_modzzz_jobs_action_add_to_featured')) : '',
			 'URLAddToFeatured' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'mark_featured/' . $this->aDataEntry['id'],
			 
			 
			  'AddToToday' => $this->_oMain->isAllowedMarkAsToday($this->aDataEntry) ? ($this->aDataEntry['today_job'] ? _t('_modzzz_jobs_action_remove_from_today') : _t('_modzzz_jobs_action_add_to_today')) : '',
			  'URLAddToToday' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'mark_today/' . $this->aDataEntry['id'],
				
			  
			  'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_jobs_action_title_delete') : '',
			   'supprimer_edit_url' =>  $supprimer_edit_url, 
			  'id' =>$this->aDataEntry['id'],
			

  		);
  
		return $this->_oTemplate->parseHtmlByName('block_manage_job', $aVars);   
	}
	
	////////////////////////////////////////////////////////////////////////




function getBlockCode_ManageApplyandFavorite() {
	 if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxJobsModule');
    
		 $aProfile = getProfileInfo($this->_oMain->_iProfileId);
		 $aData = $this->aDataEntry;
		 
		if($aData['company_id']){
 			if(getParam("modzzz_listing_jobs_active")=='on' && $aData['company_type']=='listing'){
				$oListing = BxDolModule::getInstance('BxListingModule');

				$aCompany = $oListing->_oDb->getEntryById($aData['company_id']);
		$sImage = '';
		
		if($aCompany['icon']){ 
		    $sImage = $this->_oDb->getLogoBusinessListing($aCompany['id'], $aCompany['icon'], true);
			
	   }
	    else if ($aCompany['thumb']){
			 $a = array ('ID' => $aCompany['author_id'], 'Avatar' => $aCompany['thumb']);
			  $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
			 $sImage = $aImage['file'] ? $aImage['file'] : $this->_oTemplate->getImageUrl('job-offered-thumb') ;
		  }
		  else{
			$sImage = $this->_oTemplate->getImageUrl('job-offered-thumb');
		} 
 		
		
		if($aData['icon']){ 
		    $sNoImage = $this->_oDb->getLogo($aData['id'], $aData['icon'], true);
 		}else{
			$sNoImage = ($aData['post_type']=='seeker') ? $this->_oTemplate->getImageUrl('job-wanted-thumb.png') : $this->_oTemplate->getImageUrl('job-offered-thumb.png');
		}	
			
			
				$sCompanyName = $aCompany['title'];
				$sCompanyUrl = BX_DOL_URL_ROOT . $oListing->_oConfig->getBaseUri() . 'view/' . $aCompany['uri'];  
			}else{
				
		        // Freddy ajout
		  $aAuthor = getProfileInfo($aData['author_id']);
          $sAuthorName =  $aAuthor['NickName'];
		  $sAuthorFirst_Name_Last_Name =  $aAuthor['FirstName'] .''.  $aAuthor['LastName'];
   		  $sAuthorLink = getProfileLink($aAuthor['ID']);	
		  
          //////////////////////
				
				$aCompany = $this->_oMain->_oDb->getCompanyEntryById($aData['company_id']);
				
				// Freddy modif
				//$sCompanyName = $aCompany['company_name'];
				$sCompanyName =  $sAuthorFirst_Name_Last_Name;
				
				// $sCompanyUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'companyview/' . $aCompany['company_uri']; 
				$sCompanyUrl =  $sAuthorLink; 

				

			}
		}
       

	 $sJobFlag = genFlag($aData['country']);
	 
		
	$add_favorite_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'mark_favorite/' . $aData['id'];
	
	
	///Freddy le message selon que le membre a pply ou pas 
		$application_count= $this->_oDb->getApplicationsCount($aData['id']);
          if($this->_oDb->hasApplied($aProfile['ID'],$aData['id']))
		  { 
		  $smessage_applicant = '<i class="sys-icon desktop"></i>'.'&nbsp;'. _t('_modzzz_jobs_already_applied_for') ? '<i class="sys-icon desktop"></i>' .'&nbsp;'. _t('_modzzz_jobs_already_applied_for') : '';
		  }
		  else if ($application_count >='5'){
			// $smessage_applicant = '<strong>'. $application_count .'</strong>'. ' ' ._t('_modzzz_jobs_form_unit_view_applications');
			$smessage_applicant = '';
		  }
		  else{ $smessage_applicant= '<i class="sys-icon user"></i>'.' '._t('_modzzz_jobs_Be_one_of_the_first_ten_applicants');
		  }
	
		  ///////////////////
		  
		  
		 // freddy pour ne pas afficher le bouton si le membre a deja postuler
		  if (!$this->_oDb->hasApplied($aProfile['ID'],$aData['id'])&& !$aData['application_link'] ){ 
		   
		  $aApplyLink = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'apply/' . $aData['id'];
		  $aAPPLY =  _t('_modzzz_jobs_easy_apply');
					
		  }
		  else{ $applyLin = '';
		       $aAPPLY =  '';		 
			  }
			  ///////////////////////////////////////////////////////

  
		$aVars = array(  
			 'AddToFavorite' => $this->_oMain->isAllowedMarkAsFavorite($aData) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $aData['id']) ? _t('_modzzz_jobs_action_remove_from_favorite') : _t('_modzzz_jobs_action_add_to_favorite')) : '',
			 
			  'Favorite_Url' =>  $add_favorite_url, 
			  'id' => $aData['id'],
			  
			  
			  'title' => $aData['title'],
			  
		      'city' => $aData['city'],
			  'zip' => $aData['zip'],
			 'drapeau' => $sJobFlag , 
			 'country' => _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']),
			 
			  'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
			 
			 
			  'thumb_url' => $sImage ? $sImage : $sNoImage,
			'company_url' => $sCompanyUrl,
			'company_name' => $sCompanyName,
			 
			 
			 
			  'bx_if:views' => array( 
				'condition' => $aData['views'] >=1,
				'content' => array(
					'views' => '<i class="sys-icon eye"></i>' .' '. $aData['views'] . ' '. _t('_modzzz_jobs_views'),
					
				) 
			),
			 
			/* 'ApplyLink'=> $aApplyLink,
			'APPLY'=> $aAPPLY,
			 'message_applicant' =>  $smessage_applicant,
			 */
			 
			  'bx_if:Message' => array( 
				'condition' => $this->_oDb->hasApplied($aProfile['ID'],$aData['id']) ,
				'content' => array(
			    'Mssage_Deja_Apply' =>  _t('_modzzz_jobs_already_applied_for') ,
				'MessageApplyLink' =>  'javascript:void(0);',
					
				) 
			),
			
			
			 'bx_if:applyLink' => array( 
				'condition' => !$this->_oDb->hasApplied($aProfile['ID'],$aData['id']) && !$aData['application_link'],
				'content' => array(
					'applyLink' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'apply/' . $aData['id'],
					'APPLY' =>  _t('_modzzz_jobs_easy_apply'),
					
				) 
			),
			
			
			'bx_if:applyLinkExternal' => array( 
				'condition' => !$this->_oDb->hasApplied($aProfile['ID'],$aData['id'])&& $aData['application_link'],
				'content' => array(
					'applyLinkExternal' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'apply/' . $aData['id'],
					'APPLYExternal' =>  _t('_modzzz_jobs_candidater')
					
				) 
			),
			
			 

  		);
		
  
		return $this->_oTemplate->parseHtmlByName('block_manage_apply_favorite', $aVars);   
	}
	
	
	////////////////////////////////////////////////////////////////////////


	

}
