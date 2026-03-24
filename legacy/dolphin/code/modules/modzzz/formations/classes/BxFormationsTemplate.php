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

bx_import('BxDolTwigTemplate');

/*
 * Formations module View
 */
class BxFormationsTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    
	/**
	 * Constructor
	 */
	function BxFormationsTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }

    function company_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxFormationsModule');

        if (!$this->_oMain->isAllowedCompanyView ($aData)) {            
            $aVars = array ('extra_css_class' => 'modzzz_formations_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
 
        $sImage = $this->_oMain->_oDb->getCompanyIcon($aData['id'], $aData['company_icon'],true);
  
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'),
            'company_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'companyview/' . $aData['company_uri'],
            'company_name' => $aData['company_name'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData,300,$aData['company_desc']),
            'formation_count' => $this->_oDb->getFormationCount($aData['id']),
            'country_city' => _t($GLOBALS['aPreValues']['Country'][$aData['company_country']]['LKey']) . (trim($aData['company_city']) ? ', '.$aData['company_city'] : ''),
        
            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
					'author' => getNickName($aData['company_author_id']),
					'author_url' => $aData['company_author_id'] ? getProfileLink($aData['company_author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ),	 
		
		);        
 
        return $this->parseHtmlByName('company_unit', $aVars);
    }

    function order_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxFormationsModule');
  
        $sAuthorName = getNickName($aData['author_id']); 
		$sAuthorLink = getProfileLink($aData['author_id']);  
 		$sCreateDate = $this->filterDate($aData['order_date']);
  		$sDueDate = $this->filterDate($aData['expiry_date']);
		$sPackageName = $this->_oMain->_oDb->getPackageName($aData['package_id']);
      
        $aVars = array (
 		    'id' => $aData['id'],  
            'formation_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'formation_title' => $aData['title'],
            'create_date' => $sCreateDate,
            'due_date' => $sDueDate, 
            'author' => $sAuthorName,
            'author_url' => $sAuthorLink,
            'invoice_no' => $aData['invoice_no'],
            'order_no' => $aData['order_no'],
            'package' => $sPackageName, 
            'product_status' => $this->getStatus($aData['status']),
            'order_status' => $this->getStatus($aData['order_status']),
			'payment_method' => $aData['payment_method'],
            'invoice_no' => $aData['invoice_no'], 
         );
  
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    function invoice_unit ($aData, $sTemplateName, &$oVotingView) {

       if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxFormationsModule');
 
        $sAuthorName = getNickName($aData['author_id']); 
		$sAuthorLink = getProfileLink($aData['author_id']);  
 		$sCreateDate = $this->filterDate($aData['invoice_date']);
  		$sDueDate = $this->filterDate($aData['invoice_due_date']);
  		$sExpiryDate = $this->filterDate($aData['invoice_expiry_date']);
 
		$sPackageName = $this->_oMain->_oDb->getPackageName($aData['package_id']);
 
        $aVars = array (
 		    'id' => $aData['formation_id'],  

			'bx_if:pay' => array( 
				'condition' =>  $this->_oMain->isPaidPackage($aData['package_id']),
				'content' => array(
					'pay_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'pay/' . $aData['uri'],
				) 
			),

            'formation_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'formation_title' => $aData['title'],
            'create_date' => $sCreateDate,
            'due_date' => $sDueDate, 
            'expiry_date' => $sExpiryDate, 
            'author' => $sAuthorName,
            'author_url' => $sAuthorLink,
            'invoice_id' => $aData['id'], 
            'invoice_no' => $aData['invoice_no'],
            'package' => $sPackageName, 
            'invoice_status' => $this->getStatus($aData['invoice_status']),
            'total' => number_format($aData['price']), 
         );
  
         return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
    function unit ($aData, $sTemplateName, &$oVotingView, $isShort=false) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxFormationsModule');

        if (!$this->_oMain->isAllowedView ($aData)) {            
            $aVars = array ('extra_css_class' => 'modzzz_formations_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
		
		$aProfile = getProfileInfo($this->_oMain->_iProfileId);
		
		 //  22/07/2015 Freddy Mise en commentaire pour ne pas afficher la photo du module Formation
	 /*
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
		*/
		
		
       
  
		if($aData['company_id']){
 			if(getParam("modzzz_listing_formations_active")=='on' && $aData['company_type']=='listing'){
				$oListing = BxDolModule::getInstance('BxListingModule');

				$aCompany = $oListing->_oDb->getEntryById($aData['company_id']);
				
				
				// Freddy Recuperer logo listing
			/*	$sImage = '';
			if ($aCompany['thumb']) {
				$a = array ('ID' => $aCompany['author_id'], 'Avatar' => $aCompany['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				//$sImage = $aImage['no_image'] ? '' : $aImage['file'];
				$sImage = $aImage['formation-offered-thumb'] ? '' : $aImage['file'];
				
				
			} 
			*/
			
			
			
			$sImage = '';
		
		if($aCompany['icon']){ 
		    $sImage = $this->_oDb->getLogoBusinessListing($aCompany['id'], $aCompany['icon'], true);
			
	   }
	    else if ($aCompany['thumb']){
			 $a = array ('ID' => $aCompany['author_id'], 'Avatar' => $aCompany['thumb']);
			  $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
			 $sImage = $aImage['file'] ? $aImage['file'] : $this->getImageUrl('formation-offered-thumb') ;
		  }
 		else{
			$sImage = $this->getImageUrl('formation-offered-thumb');
		} 
		
			// Freddy fin recuperation logo listing
			
			
			
				$sCompanyName = $aCompany['title'];
				$sCompanyUrl = BX_DOL_URL_ROOT . $oListing->_oConfig->getBaseUri() . 'view/' . $aCompany['uri'];  

				$sCompanyCountry = $aCompany['country'];
				$sCompanyState = trim($aCompany['state']) ? $this->_oMain->_oDb->getStateName($sCompanyCountry, $aCompany['state']) : '';
				$sCompanyCity = trim($aCompany['city']);
 
				$sLocation = ($sCompanyCity ? $sCompanyCity.', ' : '') . ($sCompanyState ? $sCompanyState.', ' : '') . _t($GLOBALS['aPreValues']['Country'][$sCompanyCountry]['LKey']); 
 
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

				$sCompanyCountry = $aCompany['company_country'];
				$sCompanyState = ($aCompany['company_state']) ? $this->_oMain->_oDb->getStateName($sCompanyCountry, $aCompany['company_state']) : '';
				$sCompanyCity = $aCompany['company_city'];

				$sLocation = ($sCompanyCity ? $sCompanyCity.', ' : '') . ($sCompanyState ? $sCompanyState.', ' : '') . _t($GLOBALS['aPreValues']['Country'][$sCompanyCountry]['LKey']); 

			}
		}
  
 		//$aCategory = $this->_oDb->getCategoryInfo($aData['category_id']);
		//$sCategoryUrl = $this->_oDb->getSubCategoryUrl($aCategory['uri']);
 
 		$aCategory = $this->_oDb->getCategoryById($aData['parent_category_id']);
		$sCategoryUrl = $this->_oDb->getCategoryUrl($aCategory['uri']); 
		if($aData['category_id']){
			$aSubCategory = $this->_oDb->getCategoryById($aData['category_id']); 
			$sSubCategoryUrl = $this->_oDb->getSubCategoryUrl($aSubCategory['uri']);
		}
 
		$sCurrency = $aData['currency'];
  		
		$SECONDS_IN_DAY = 86400; 
		$iExpireDays = (int)getParam("modzzz_formations_free_expired"); 
		$iExpireTime = ($iExpireDays * $SECONDS_IN_DAY) + $aData['created']; 
  
		if($aData['icon']){ 
		    $sNoImage = $this->_oDb->getLogo($aData['id'], $aData['icon'], true);
 		}else{
			$sNoImage = ($aData['post_type']=='seeker') ? $this->getImageUrl('formation-wanted-thumb.png') : $this->getImageUrl('formation-offered-thumb.png');
		}
		
		//Freddy Formation Location
		$sFormationFlag = genFlag($aData['country']);
		$sFormationcity = $aData['city'];
		$sFormationzip = $aData['zip'];
		$sFormationCountry= $aData['country'];	
		$sFormationstate = ($aData['state']) ? $this->_oMain->_oDb->getStateName($sFormationCountry, $aData['state']) : '';
 
				$sFormationLocation =  ($sFormationFlag ? $sFormationFlag.' ' : '').($sFormationzip ? $sFormationzip.' ' : '') . ($sFormationcity ? $sFormationcity.', ' : '').($sFormationstate ? $sFormationstate.' ' : '')  ; 
		
		
		
		///Freddy le message selon que le membre a apply ou pas 
		$application_count= $this->_oDb->getApplicationsCount($aData['id']);
          if($this->_oDb->hasApplied($aProfile['ID'],$aData['id']))
		  { 
		  $smessage_applicant = '<i class="sys-icon desktop"></i>'.'&nbsp;'. _t('_modzzz_formations_already_applied_for') ? '<i class="sys-icon desktop"></i>' .'&nbsp;'. _t('_modzzz_formations_already_applied_for') : '';
		  }
		  else if ($application_count >='5'){
			 $smessage_applicant = '<strong>'. $application_count .'</strong>'. ' ' ._t('_modzzz_formations_form_unit_view_applications');
		  }
		  else{ $smessage_applicant= _t('_modzzz_formations_Be_one_of_the_first_ten_applicants');
		  }
	
		  ///////////////////
		


		
		
		////////// Fin Freddy
		
		// Freddy add
		$aFormationType ='<span style="font-size:10px;"></span>' .  _t($GLOBALS['aPreValues']['FormationType'][$aData['formation_type']]['LKey']);
		$aFormationStatus = '<span style="font-size:10px;"></span>' . '&nbsp;'.'|'.'&nbsp;'.  _t($GLOBALS['aPreValues']['FormationStatus'][$aData['formation_status']]['LKey']);
		///////
		
		
		
		
		
		
		// Freddy ajout  salary management
		
		if  ($aData['max_salary'] || $aData['min_salary']){
		$aSalary_Minimum = ($aData['min_salary']) ? $sCurrency .''. number_format($aData['min_salary'] , 0, ',', ' ') : ' ';
		$aSalary_Maximum = ($aData['max_salary']) ? $sCurrency .''. number_format($aData['max_salary'], 0, ',', ' ') :  _t('_modzzz_formations_upwards');
		$aSalary_Type = _t($GLOBALS['aPreValues']['FormationSalaryType'][$aData['salary_type']]['LKey']);
		$aSeparateur=   '-';
				  
		}
		else{
			$aSalary_Negociable=   _t('_modzzz_formations_Salary_Negociable');
	    }
		
              

        $aVars = array (  
		 
		
		//// Freddy add
		   'message_applicant' =>  $smessage_applicant,
		
		    'city' =>  $aData['city'], 
			'zip' =>  $aData['zip'], 
			'drapeau' =>  $sFormationFlag , 
			'province' => $sFormationstate,
			'country' => _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']),
			
			//'company' => ($sCompanyName) ? $sCompanyName : _t('_modzzz_formations_na'),
			'company' => $sCompanyName,
		   
		   // 'company_url' => ($sCompanyUrl) ? $sCompanyUrl : 'javascript:void(0);',
		    'company_url' => $sCompanyUrl,
			
			
			
			'bx_if:role' => array( 
								'condition' => $aData['role'],
								'content' => array(
             'role' => $aData['role'],
								), 
							),
			
			'bx_if:duration' => array( 
								'condition' => $aData['duration'],
								'content' => array(
             'duration' => $aData['duration'],
								), 
							),
			
			
			'bx_if:formation_type' => array( 
				'condition' => $aData['formation_type'] ,
				'content' => array(
					
					//'formation_type' =>  _t($GLOBALS['aPreValues']['FormationType'][$aDataEntry['formation_type']]['LKey']),
					'formation_type' =>$this->parseMultiPreValues ('FormationType', $aData['formation_type']),
					
				) 
			), 
			
					
			'post_type_stage' => $aPost_Type_Stage,
			'formation_status' => $aFormationStatus,
		    'formation_type' => $aFormationType,
		     'formation_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
			 'created' => defineTimeInterval($aData['created']),
			
			
			
			'Sauvegardee' => $this->_oMain->isAllowedMarkAsFavorite($aData) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $aData['id']) ? '<i class="sys-icon heart"></i>' . ' ' .  _t('_modzzz_formations_action_afficher_formation_favorite') : '') : '',

			  'commune' => $aData['zip'],
			  
			 
			  
			  'Application_count_Unit_Admin'=> $application_count,
			 'Application_caption_Unit_Admin'=> _t('_modzzz_formations_form_unit_view_applications') ,
			  
			
				
				'bx_if:Formation_Status_Type' => array( 
				'condition' => ($aData['formation_status'] || $aData['formation_type']),
				'content' => array(
					'formation_status' => _t($GLOBALS['aPreValues']['FormationStatus'][$aData['formation_status']]['LKey']),
					'formation_type' =>  _t($GLOBALS['aPreValues']['FormationType'][$aData['formation_type']]['LKey']),
					
				) 
			), 
			
				
			
				
				 
				  'bx_if:vacancies' => array( 
								'condition' => $aData['vacancies']>1,
								'content' => array(
             'vacancies' => $aData['vacancies'],
			 'vacancies_caption' => _t('_modzzz_formations_vacancies') ,
								), 
							),
			
			
			 'bx_if:experience' => array( 
								'condition' => $aData['experience'],
								'content' => array(
             'experience' => _t($GLOBALS['aPreValues']['FormationExperience'][$aData['experience']]['LKey']),
								), 
							),
							
			 
		//////////////////////Fin Freddy add
		          
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $sNoImage,
            'formation_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'formation_title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData),

			'bx_if:showexpire' => array( 
				'condition' => $iExpireDays,
				'content' => array(
					'expires' => date('M d, Y', $iExpireTime),
 				) 
			),
 
			'bx_if:location' => array( 
				'condition' => $sLocation,
				'content' => array(
					'location' => $sLocation, 
 				) 
			),

			'category_name' => $aCategory['name'],
			'category_url' => $sCategoryUrl,

            'bx_if:sub_category' => array (
                'condition' => $aData['category_id'],
                'content' => array ( 
					'subcategory_name' => $aSubCategory['name'],
					'subcategory_url' => $sSubCategoryUrl,
                ),
            ),	

			/*'category' => '<a href="'.$sCategoryUrl.'">'.$aCategory['name'].'</a>', */ 
            'tags' => $this->_oMain->parseTags($aData['tags']), 
			
			'vacancies' => ($aData['vacancies']) ? $aData['vacancies'] : 1, 
			'comments_count' => (int)$aData['comments_count'],
			'role' => ($aData['role']) ? $aData['role'] : _t('_modzzz_formations_na'),
			'experience' => _t($GLOBALS['aPreValues']['FormationExperience'][$aData['experience']]['LKey']),
			'qualification' => _t($GLOBALS['aPreValues']['FormationEducation'][$aData['qualification']]['LKey']),
			
			
			'Salary_Minimum' =>$aSalary_Minimum ,
		     'Salary_Maximum' =>$aSalary_Maximum ,
		      'Salary_Type' => $aSalary_Type ,
			 'Salary_Negociable'=> $aSalary_Negociable,
			  'Separateur' => $aSeparateur,
			
			/*
			
			'bx_if:salary' => array( 
				'condition' => ($aData['max_salary'] || $aData['min_salary']),
				'content' => array(
					'salary_minimum' => ($aData['min_salary']) ? $sCurrency .''. number_format($aData['min_salary'] , 0, ',', ' ') : '-',
					'salary_maximum' => ($aData['max_salary']) ? $sCurrency .''. number_format($aData['max_salary'], 0, ',', ' ') : _t('_modzzz_formations_upwards'),
					'salary_type' => _t($GLOBALS['aPreValues']['FormationSalaryType'][$aData['salary_type']]['LKey']),
				) 
			), 
			
			*/

			

            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
					
                ),
            ),	 
        );        
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 

    // ======================= ppage compose block functions 

    function blockSkills (&$aDataEntry) {
    
		$aVars = array ( 
		
		
            'description' =>  $aDataEntry['skills'] 
			
			
        );

        return $this->parseHtmlByName('block_skills', $aVars);
    }

    function blockDesc (&$aDataEntry) {
		 if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxFormationsModule');
			
			 $aProfile = getProfileInfo($this->_oMain->_iProfileId);

		
		$aCategory = $this->_oDb->getCategoryById($aDataEntry['parent_category_id']);
		$sCategoryUrl = $this->_oDb->getCategoryUrl($aCategory['uri']); 
		if($aDataEntry['category_id']){
			$aSubCategory = $this->_oDb->getCategoryById($aDataEntry['category_id']); 
			$sSubCategoryUrl = $this->_oDb->getSubCategoryUrl($aSubCategory['uri']);
		}
		
		 $add_favorite_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'mark_favorite/' . $aDataEntry['id'];
	     $showPopupAnyHtml = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'share_popup/' . $aData['id'];
    
		$aVars = array (
            'title' => $aDataEntry['title'],
            'description' =>  $aDataEntry['desc'],
			
			 'TitleShare' => $this->_oMain->isAllowedShare($aDataEntry) ? _t('_modzzz_formations_action_title_share') : '',
			   'SharePopup' => $showPopupAnyHtml,
			 
			 
			 'bx_if:skills' => array( 
								'condition' => $aDataEntry['skills'],
								'content' => array(
              'skills' =>  $aDataEntry['skills'] ,
								), 
							),
							
			 'bx_if:condition_admission' => array( 
								'condition' => $aDataEntry['condition_admission'],
								'content' => array(
              'condition_admission' =>  $aDataEntry['condition_admission'] ,
								), 
							),
							
							
	    
		'AddToFavorite' => $this->_oMain->isAllowedMarkAsFavorite($aDataEntry) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $aDataEntry['id']) ? _t('_modzzz_formations_action_remove_from_favorite') : _t('_modzzz_formations_action_add_to_favorite')) : '',
			 
			  'Favorite_Url' =>  $add_favorite_url, 
			  'id' => $aDataEntry['id'],
		
		'bx_if:applyLink' => array( 
				'condition' => !$this->_oDb->hasApplied($aProfile['ID'],$aDataEntry['id']) && !$aDataEntry['application_link'],
				'content' => array(
					'applyLink' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'apply/' . $aDataEntry['id'],
					'APPLY' =>  _t('_modzzz_formations_easy_apply'),
					
				) 
			),
			
			 'bx_if:Message' => array( 
				'condition' => $this->_oDb->hasApplied($aProfile['ID'],$aDataEntry['id']) ,
				'content' => array(
			    'Mssage_Deja_Apply' =>  _t('_modzzz_formations_already_applied_for') ,
				'MessageApplyLink' =>  'javascript:void(0);',
					
				) 
			),
			
			'bx_if:applyLinkExternal' => array( 
				'condition' => !$this->_oDb->hasApplied($aProfile['ID'],$aDataEntry['id'])&& $aDataEntry['application_link'],
				'content' => array(
					'applyLinkExternal' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'apply/' . $aDataEntry['id'],
					'APPLYExternal' =>  _t('_modzzz_formations_candidater')
					
				) 
			),
			
			
		
		 'category_name' => $aCategory['name'],
			'category_url' => $sCategoryUrl,

            'bx_if:sub_category' => array (
                'condition' => $this->aDataEntry['category_id'],
                'content' => array ( 
					'subcategory_name' => $aSubCategory['name'],
					'subcategory_url' => $sSubCategoryUrl,
                ),
            ),
			
			'bx_if:role' => array( 
								'condition' => $aDataEntry['role'],
								'content' => array(
             'role' => $aDataEntry['role'],
								), 
							),
			
			'bx_if:duration' => array( 
								'condition' => $aDataEntry['duration'],
								'content' => array(
             'duration' => $aDataEntry['duration'],
								), 
							),
			
			
			'bx_if:formation_type' => array( 
				'condition' => $aDataEntry['formation_type'] ,
				'content' => array(
					
					//'formation_type' =>  _t($GLOBALS['aPreValues']['FormationType'][$aDataEntry['formation_type']]['LKey']),
					'formation_type' =>$this->parseMultiPreValues ('FormationType', $aDataEntry['formation_type']),
					
				) 
			), 
			
			 'bx_if:experience' => array( 
								'condition' => $aDataEntry['experience'],
								'content' => array(
             'experience' => _t($GLOBALS['aPreValues']['FormationExperience'][$aDataEntry['experience']]['LKey']),
								), 
							),
							
			'bx_if:qualification' => array( 
								'condition' => $aDataEntry['qualification'],
								'content' => array(
             'qualification' => _t($GLOBALS['aPreValues']['FormationEducation'][$aDataEntry['qualification']]['LKey']),
								), 
							),
							
			'bx_if:career_level' => array( 
								'condition' => $aDataEntry['career_level'],
								'content' => array(
             'career_level' => _t($GLOBALS['aPreValues']['FormationCareerLevel'][$aDataEntry['career_level']]['LKey']),
								), 
							),
				
				'bx_if:Formation_Status_Type' => array( 
				'condition' => ($aDataEntry['formation_status'] || $aDataEntry['formation_type']),
				'content' => array(
					'formation_status' => _t($GLOBALS['aPreValues']['FormationStatus'][$aDataEntry['formation_status']]['LKey']),
					'formation_type' =>  _t($GLOBALS['aPreValues']['FormationType'][$aDataEntry['formation_type']]['LKey']),
					
				) 
			), 
			
	 );

        return $this->parseHtmlByName('block_description', $aVars);
    }
	
	// freddy add
	  function parseMultiPreValues ($sName, $sVal='') {  
		$sStr = '';
		$aVals = split(';',$sVal);
		foreach($aVals as $aEachVal){
			if($GLOBALS['aPreValues'][$sName][$aEachVal]['LKey'])
 				$sStr .='&nbsp;&nbsp;<i class="sys-icon certificate"></i> ' . htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sName][$aEachVal]['LKey'])) ;
			else
 				$sStr .= $aEachVal . '&nbsp;'; 
		}
 		return $sStr;
 	}
	//////////////////////////////

    function blockCompanyDesc (&$aDataEntry) {
        $aVars = array (
            'title' => $aDataEntry['company_name'],
            'description' => $aDataEntry['company_desc']  
        );
        return $this->parseHtmlByName('block_company_description', $aVars);
    }


    function blockFields (&$aDataEntry, $aShow) {
        
		$sRet = '';
		$sRetHead = '<table class="modzzz_formations_fields">';
        modzzz_formations_import ('FormAdd');        
        $oForm = new BxFormationsFormAdd ($GLOBALS['oBxFormationsModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display']) || !$aDataEntry[$k]) continue;

            if (!in_array($a['name'],$aShow)) continue;
 
            $sRet .= '<tr><td class="modzzz_formations_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_formations_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display']))){ 
				if($a['name'] == 'state'){
					$sRet .= $this->getStateName($aDataEntry['country'], $aDataEntry[$k]);
				}else{
					$sRet .= call_user_func_array(array($this, $a['display']), array($a['listname'], $aDataEntry[$k]));
				}
			}else if (0 == strcasecmp($k, 'country')){
                $sRet .= _t($GLOBALS['aPreValues']['Country'][$aDataEntry[$k]]['LKey']);
			}else{
                $sRet .= $aDataEntry[$k];
			}
            $sRet .= '<td></tr>';
        }

		if(!$sRet)
			return;

        $sRetFoot = '</table>';
		
		$sCode = $sRetHead . $sRet . $sRetFoot;
        
		return $sCode;
    }
  
    function blockCompanyFields (&$aDataEntry, $aShow=array()) {

		$bHasValues = false;
 
        $sRet = '<table class="modzzz_formations_fields">';
        modzzz_formations_import ('FormCompanyAdd');        
        $oForm = new BxFormationsFormCompanyAdd ($GLOBALS['oBxFormationsModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display']) || !$aDataEntry[$k]) continue;
 
			if(count($aShow)){
				if (!in_array($a['name'],$aShow)) continue;
			}

            $sRet .= '<tr><td class="modzzz_formations_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_formations_field_value">';

            if (is_string($a['display']) && is_callable(array($this, $a['display']))){
				if($a['name'] == 'company_state'){
					$sRet .= $this->getStateName($aDataEntry['company_country'], $aDataEntry[$k]);
				}else{			
					$sRet .= call_user_func_array(array($this, $a['display']), array($aDataEntry[$k]));
				}
			}else if (0 == strcasecmp($k, 'company_country')){
                $sRet .= _t($GLOBALS['aPreValues']['Country'][$aDataEntry[$k]]['LKey']);
			}else{
                $sRet .= $aDataEntry[$k];
			}
            $sRet .= '<td></tr>';
 
			$bHasValues = true; 
        }

		if(!$bHasValues)
			return;

        $sRet .= '</table>';
        return $sRet;
    }


	function getOptionDisplay($sField='',$sVal='')
	{ 
		return ucwords($sVal);
	}

	function getStatus($sStatus){
		switch($sStatus){
			case "pending":
				$sLangStatus = _t("_modzzz_formations_pending");
			break;
			case "paid":
				$sLangStatus = _t("_modzzz_formations_paid");
			break;
			case "active":
				$sLangStatus = _t("_modzzz_formations_active");
			break;
			case "inactive":
				$sLangStatus = _t("_modzzz_formations_inactive");
			break;
			case "approved":
				$sLangStatus = _t("_modzzz_formations_approved");
			break;
			case "expired":
				$sLangStatus = _t("_modzzz_formations_expired");
			break; 
		}

		return $sLangStatus;
	}
 
    function filterDate ($i, $bLongFormat = false) {
		if($bLongFormat)
			return getLocaleDate($i, BX_DOL_LOCALE_DATE) . ' ('.defineTimeInterval($i) . ')'; 
		else
			return getLocaleDate($i, BX_DOL_LOCALE_DATE);
	}

    function filterCustomDate ($i, $bLongFormat = false) {
 		if($bLongFormat)
			return date('M d, Y g:i A', $i);
		else
			return date('M d, Y', $i);
	}

	function displayAvailableLevels($aValues) {
	    $sCurrencyCode = strtolower($this->_oConfig->getCurrencyCode());
	    $sCurrencySign = $this->_oConfig->getCurrencySign();
        $sCurrencySign = str_replace("\$", "&#36;", $sCurrencySign);

	    $aMemberships = array();
	    foreach($aValues as $aValue) { 
  
            $aMemberships[] = array(
                'url_root' => BX_DOL_URL_ROOT,
                'id' => $aValue['id'],
                'title' => str_replace("\$", "&#36;", $aValue['name']),
                'description' => str_replace("\$", "&#36;", $aValue['description']),
                'days' => $aValue['days'] > 0 ?  $aValue['days'] . ' ' . _t('_modzzz_formations_days') : _t('_modzzz_formations_expires_never') ,
                'price' => $aValue['price'],
                'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
				'currency_sign' => $sCurrencySign,
				'videos' => ($aValue['videos']) ? ucwords(_t('_modzzz_formations_yes')) : ucwords(_t('_modzzz_formations_no')),
				'photos' => ($aValue['photos']) ? ucwords(_t('_modzzz_formations_yes')) : ucwords(_t('_modzzz_formations_no')),
				'sounds' => ($aValue['sounds']) ? ucwords(_t('_modzzz_formations_yes')) : ucwords(_t('_modzzz_formations_no')),
				'files' => ($aValue['files']) ? ucwords(_t('_modzzz_formations_yes')) : ucwords(_t('_modzzz_formations_no')),
				'featured' => ($aValue['featured']) ? ucwords(_t('_modzzz_formations_yes')) : ucwords(_t('_modzzz_formations_no')),

 	        );
	    }

		$aVars = array('bx_repeat:levels' => $aMemberships);

	    $this->addCss('levels.css');
	    return $this->parseHtmlByName('available_packages', $aVars);
	}
 
	function getPreListDisplay($sField, $sVal){ 
 		return htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sField][$sVal]['LKey']) );
	}

	function getStateName($sCountry, $sState){ 
 
		return $this->_oDb->getStateName($sCountry, $sState);
	}

	function getWebsiteLink($sVal){ 
		return "<a href='{$sVal}' target=_blank>{$sVal}</a>";
	}

	function getFormPackageDesc($iPackageId) {
	    $sCurrencyCode = strtolower($this->_oConfig->getCurrencyCode());
	    $sCurrencySign = $this->_oConfig->getCurrencySign();
  
		$aPackage = $this->_oDb->getPackageById($iPackageId);

		$aVars = array(
			'url_root' => BX_DOL_URL_ROOT,
			'id' => $aPackage['id'],
			'title' => $aPackage['name'],
			'description' => str_replace("\$", "&#36;", $aPackage['description']),
			'days' => $aPackage['days'] > 0 ?  $aPackage['days'] . ' ' . _t('_modzzz_formations_days') : _t('_modzzz_formations_expires_never') ,
			'price' => $aPackage['price'],
			'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
			'currency_sign' => $sCurrencySign, 
			'videos' => ($aPackage['videos']) ? ucwords(_t('_modzzz_formations_yes')) : ucwords(_t('_modzzz_formations_no')),
			'photos' => ($aPackage['photos']) ? ucwords(_t('_modzzz_formations_yes')) : ucwords(_t('_modzzz_formations_no')),
			'sounds' => ($aPackage['sounds']) ? ucwords(_t('_modzzz_formations_yes')) : ucwords(_t('_modzzz_formations_no')),
			'files' => ($aPackage['files']) ? ucwords(_t('_modzzz_formations_yes')) : ucwords(_t('_modzzz_formations_no')),
			'featured' => ($aPackage['featured']) ? ucwords(_t('_modzzz_formations_yes')) : ucwords(_t('_modzzz_formations_no')),

		);
 
	    $this->addCss('levels.css');
	    return $this->parseHtmlByName('form_package', $aVars);
	}

	function youtubeId($url) {
		$url = str_replace('&amp;', '&', $url); 

		if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
			$sVideoId = $match[1];  
		}else{  
			$sVideoId = substr( parse_url($url, PHP_URL_PATH), 1 );
			$sVideoId = ltrim( $sVideoId, '/' ); 
		} 

		return $sVideoId;  
	}

}
