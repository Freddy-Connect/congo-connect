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
* see license.txt file; if not, write to classifieding@boonex.com
***************************************************************************/

bx_import ('BxDolProfileFields');
bx_import ('BxDolFormMedia');

class BxClassifiedFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxClassifiedFormAdd ($oMain, $iProfileId, $iEntryId = 0, $iThumb = 0) {

		$this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
        $this->_oTemplate = $oMain->_oTemplate;
		
		
		
   		 $aProfileInfo = getProfileInfo($iProfileId);
		
		//Freddy Integration Business listing
  if(getParam('modzzz_listing_classified_active')=='on')
			$aCompanies = $this->_oDb->getMergedCompanyList($this->_oMain->_iProfileId);  
			////////////////////////////////////////////////////////////////////////////
			
			$aProfileInfoBiz = getProfileInfo($iProfileId);
			$sBusinessCity = $this->_oDb->getBusinessCity($aProfileInfoBiz['ID']); 
			$sBusinessZip = $this->_oDb->getBusinessZip($aProfileInfoBiz['ID']); 
			$sBusinessState = $this->_oDb->getBusinessState($aProfileInfoBiz['ID']); 
			$sBusinessAddress1 = $this->_oDb->getBusinessAddress1($aProfileInfoBiz['ID']); 
			$sBusinessWebsite = $this->_oDb->getBusinessWebsite($aProfileInfoBiz['ID']);
			$sBusinessTelephone = $this->_oDb->getBusinessTelephone($aProfileInfoBiz['ID']);
			$sBusinessMobile = $this->_oDb->getBusinessMobile($aProfileInfoBiz['ID']); 
			
			
  // Fin Freddy Integration Business Listing
		
		
   
   		$bPaidClassified = $this->_oMain->isAllowedPaidClassifieds (); 
		
		//Freddy
		$bBusiness = false;
		///

		$sDefaultTitle = stripslashes($_REQUEST['title']);

		$aNumberList = $this->_oDb->getNumberList(1, 1000);
 
		if($iEntryId) {
			$aDataEntry = $this->_oDb->getEntryById($iEntryId); 
			$bPaidClassified = $aDataEntry['invoice_no'] ? $bPaidClassified : false;

			$sSelState = ($_POST['state']) ? $_POST['state'] : $aDataEntry['state']; 
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : $aDataEntry['country'];  
			$aStates = $this->_oDb->getStateArray($sSelCountry);  

			$iCategoryId = (int)$aDataEntry['category_id']; 
			$iParentCategoryId = $this->_oDb->getParentCategoryById($iCategoryId);   
		// Freddy Integration business Listing
			if($aDataEntry['company_id']){ 
				$bBusiness = true;
				if(getParam("modzzz_listing_classified_active")=='on' && $aDataEntry['company_type']=='listing'){ 
					$oBusiness = BxDolModule::getInstance('BxListingModule'); 
					$aBusiness = $oBusiness->_oDb->getEntryById($aDataEntry['company_id']);
					$sBusinessName = $aBusiness['title'];
					
		
 				}else{
					$sBusinessName = $this->_oMain->_oDb->getCompanyName($aDataEntry['company_id']);
  				}
			}
			// Fin Freddy Integration business listing 
		}else { 	
			
			//Freddy		
			
			$aProfile = getProfileInfo($this->_oMain->_iProfileId);
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : $aProfile['Country']; 
			$sSelCity = ($_POST['city']) ? $_POST['city'] : $aProfile['City']; 
			$sSelZip = ($_POST['zip']) ? $_POST['zip'] : $aProfile['zip']; 
 
			$sSelState = ($_POST['state']) ? $_POST['state'] : ''; 
			$aStates = $this->_oDb->getStateArray($sSelCountry);  
			
			// Freddy ajout
			$aStates[''] = '--'._t('_Select').'--';  
		}
 
		$iParentCategoryId = ($iParentCategoryId) ? $iParentCategoryId : (int)$_POST['parent_category_id']; 
		$iCategoryId = ($iCategoryId) ? $iCategoryId : (int)$_POST['category_id']; 

		$sParentCategoryInfo = $this->_oDb->getCategoryInfo($iParentCategoryId);
		$sParentCategoryName = $sParentCategoryInfo['name'];
		$sParentCategoryType =  $sParentCategoryInfo['type'];
		$sCategoryName = $this->_oDb->getCategoryName($iCategoryId);
 
		$sClassifiedType = ($aDataEntry['classified_type']) ? $aDataEntry['classified_type'] : $_POST['classified_type']; 
		
		$sWhyCaptionC = _t('_modzzz_classified_form_caption_why'); 
 
		$sStateUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home/'.($this->_oMain->isPermalinkEnabled() ? '?' : '&').'ajax=state&country=' ; 
   
		$sCatUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home/'.($this->_oMain->isPermalinkEnabled() ? '?' : '&').'ajax=cat&parent=' ;
   
		if($bPaidClassified){
			$iPackageId = ($iEntryId) ? (int)$this->_oDb->getPackageIdByInvoiceNo($aDataEntry['invoice_no']) : (int)$_POST['package_id']; 
			$sPackageName = $this->_oDb->getPackageName($iPackageId);
		}
 
        $this->_aMedia = array ();

        if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            $this->_aMedia['images'] = array (
                'post' => 'ready_images',
                'upload_func' => 'uploadPhotos',
                'tag' => BX_CLASSIFIED_PHOTOS_TAG,
                'cat' => BX_CLASSIFIED_PHOTOS_CAT,
                'thumb' => 'thumb',
                'module' => 'photos',
                'title_upload_post' => 'images_titles',
                'title_upload' => _t('_modzzz_classified_form_caption_file_title'),
                'service_method' => 'get_photo_array',
            );

        if (BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            $this->_aMedia['videos'] = array (
                'post' => 'ready_videos',
                'upload_func' => 'uploadVideos',
                'tag' => BX_CLASSIFIED_VIDEOS_TAG,
                'cat' => BX_CLASSIFIED_VIDEOS_CAT,
                'thumb' => false,
                'module' => 'videos',
                'title_upload_post' => 'videos_titles',
                'title_upload' => _t('_modzzz_classified_form_caption_file_title'),
                'service_method' => 'get_video_array',
            );

        if (BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            $this->_aMedia['sounds'] = array (
                'post' => 'ready_sounds',
                'upload_func' => 'uploadSounds',
                'tag' => BX_CLASSIFIED_SOUNDS_TAG,
                'cat' => BX_CLASSIFIED_SOUNDS_CAT,
                'thumb' => false,
                'module' => 'sounds',
                'title_upload_post' => 'sounds_titles',
                'title_upload' => _t('_modzzz_classified_form_caption_file_title'),
                'service_method' => 'get_music_array',
            );

        if (BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            $this->_aMedia['files'] = array (
                'post' => 'ready_files',
                'upload_func' => 'uploadFiles',
                'tag' => BX_CLASSIFIED_FILES_TAG,
                'cat' => BX_CLASSIFIED_FILES_CAT,
                'thumb' => false,
                'module' => 'files',
                'title_upload_post' => 'files_titles',
                'title_upload' => _t('_modzzz_classified_form_caption_file_title'),
                'service_method' => 'get_file_array',
            );
 
        $oProfileFields = new BxDolProfileFields(0);
        $aDefCountries = $oProfileFields->convertValues4Input('#!Country');
		asort($aDefCountries);
		$aChooseCountries = array('-'=>_t("_Select"));   
		$aCountries = array_merge($aChooseCountries, $aDefCountries);

        $aDefCurrency = $oProfileFields->convertValues4Input('#!ClassifiedsCurrency');
        asort($aDefCurrency);
		$aChooseCurrency = array('-'=>_t("_Select"));   
		$aCurrencyList = array_merge($aChooseCurrency, $aDefCurrency);

        $aPaymentTypes = $oProfileFields->convertValues4Input('#!ClassifiedPaymentType');
        //asort($aDefCurrency);

        // generate templates for custom form's elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($oMain->_iProfileId, $iEntryId, $iThumb);

        $aCustomYoutubeTemplates = $this->generateCustomYoutubeTemplate ($oMain->_iProfileId, $iEntryId);

       // privacy

         $aInputPrivacyCustomPass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9f]+)$/'),
        );

  
        $aInputPrivacyComment = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'classified', 'comment');
        $aInputPrivacyComment['values'] = $aInputPrivacyComment['values'];
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'classified', 'rate');
        $aInputPrivacyRate['values'] = $aInputPrivacyRate['values'];
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyForum = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'classified', 'post_in_forum');
        $aInputPrivacyForum['values'] = $aInputPrivacyForum['values'];
        $aInputPrivacyForum['db'] = $aInputPrivacyCustomPass;
 
		$aMembershipOptions = array('' => _t('_modzzz_classified_all')); 
		$aMembership = getMemberships();
		foreach ( $aMembership as $iMembershipID => $sMembershipName ) {
			
			// Freddy ajout  : Ne pas afficher les mambership Non-Member Standard et Promotion dans la liste 
			if ($iMembershipID == MEMBERSHIP_ID_NON_MEMBER) continue;
			if ($iMembershipID == MEMBERSHIP_ID_STANDARD) continue;
			if ($iMembershipID == MEMBERSHIP_ID_PROMOTION) continue;
			// [END] Freddy Ajout
			
			
			//if ($iMembershipID == MEMBERSHIP_ID_NON_MEMBER) continue;
			$aMembershipOptions[$iMembershipID] = $sMembershipName;
		}


        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_classified',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_classified_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_classified_form_header_info')
                ),   
                'classified_type' => array(
                    'type' => 'hidden',
                    'name' => 'classified_type',
                    'value' => $sClassifiedType,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),  
                'package_id' => array(
                    'type' => 'hidden',
                    'name' => 'package_id',
                    'value' => $iPackageId 
                ),    
                'parent_category_id' => array(
                    'type' => 'hidden',
                    'name' => 'parent_category_id',
                    'value' => $iParentCategoryId,  
                ),
				
				///Freddy integration Business listing
				  'header_custom_info_category' => array(
                    'type' => 'custom',
                    //'caption' => $sheader_info_Category, 
                    'collapsable' => false,
                    'collapsed' => false,
                ), 
              
				
				 'display_business' => array(
                    'type' => 'custom',
                    'name' => 'display_business',
                    'caption' => _t('_modzzz_classified_form_caption_listed_by'),
                    'content' => $sBusinessName,
                ),  
				///Fin Freddy integration Business listing  
                'category_id' => array(
                    'type' => 'hidden',
                    'name' => 'category_id',
                    'value' => $iCategoryId,  
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ),  
					 
				'package_name' => array( 
					'type' => 'custom',
                    'content' => $sPackageName,  
                    'name' => 'package_name',
                    'caption' => _t('_modzzz_classified_package'), 
                ),
				'parent_category_name' => array( 
					'type' => 'custom',
                    'content' => $sParentCategoryName,  
                    'name' => 'parent_category_name',
                    'caption' => _t('_modzzz_classified_parent_category_name'), 
                ),
				'category_name' => array( 
					'type' => 'custom',
                    'content' => $sCategoryName,  
                    'name' => 'category_name',
                    'caption' => _t('_modzzz_classified_category_name'), 
                ),  
                'user_name' => array(
                    'type' => 'text',
                    'name' => 'user_name',
                    'caption' => _t('_modzzz_classified_form_caption_user_name'),
                    'required' => true, 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,150),
                        'error' => _t ('_modzzz_classified_form_err_user_name'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
				
				 'company_id' => array(
                    'type' => 'select',
                    'name' => 'company_id',
                    'caption' => _t('_modzzz_classified_form_caption_company'),
                    //'info' => _t('_modzzz_classified_form_info_company'),
					'attrs' => array(
						'onchange' => "toggleCompanyBlock(this.options[this.selectedIndex].value)",
						'id' => "company_id",
					),	 
                    'required' => false, 
                    'values' => $aCompanies,   
                ),  
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_classified_form_caption_title'),
					 'info' => _t('_modzzz_classified_form_info_title'),
                    'value' => $sDefaultTitle, 
                    'required' => true, 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,150),
                        'error' => _t ('_modzzz_classified_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 	
                'desc' => array(
                    'type' => 'textarea',
                    'name' => 'desc',
                    'caption' => _t('_modzzz_classified_form_caption_desc'),
                    'required' => true,
                    'html' => 2,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(200,64000),
                        'error' => _t ('_modzzz_classified_form_err_desc'),
                    ),                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),  
               /* 'why' => array(
                    'type' => 'textarea',
                    'name' => 'why',
                    'caption' => $sWhyCaptionC,
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ), 
                'quantity' => array(
                    'type' => 'select',
                    'name' => 'quantity',
                    'caption' => _t('_modzzz_classified_form_caption_quantity'),
					'values' => $aNumberList, 
					'required' => false, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([0-9]+)/'),
                    ),
					  
                    'display' => true,
                ),
				*/
				  //Freddy j'ai modifié dans BDD le type de price. Il était en float et je l'ai modifié en varchar 100
                'price' => array(
                    'type' => 'text',
                    'name' => 'price',
                    'caption' => _t('_modzzz_classified_form_caption_price'),
                    'required' => false,
                     'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                
                ), 
                'tags' => array(
                    'type' => 'text',
                    'name' => 'tags',
                    'caption' => _t('_modzzz_classified_form_caption_tags'),
                    'info' => _t('_modzzz_classified_form_info_tags'),
                    'required' => false,
                   /* 'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_classified_form_err_tags'),
                    ),
					*/
                    'db' => array (
                        'pass' => 'Tags', 
                    ),
                ),   


    
             
				/*
				//Freddy j'ai modifié dans BDD le type de saleprice. Il était en float et je l'ai modifié en varchar 100
                'saleprice' => array(
                    'type' => 'text',
                    'name' => 'saleprice',
                    'caption' => _t('_modzzz_classified_form_caption_saleprice'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([0-9.]+)/'),
                    ),  
                    'display' => true,
                ),	
                'payment_type' => array(
                    'type' => 'select',
                    'name' => 'payment_type',
                    'listname' => 'ClassifiedPaymentType',
                    'caption' => _t('_modzzz_classified_form_caption_payment_type'),
					'values' => $aPaymentTypes, 
					'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                     ),  
                    'display' => true,
                ),
                'currency' => array(
                    'type' => 'select',
                    'name' => 'currency',
                    'caption' => _t('_modzzz_classified_form_caption_currency'),
					'values' => $aCurrencyList, 
					'value' => getParam('modzzz_classified_currency_sign'),
					'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                     ),  
                    'display' => true,
                ),
				*/

                'header_location' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_classified_form_header_location'),
                    'collapsable' => true,
                    'collapsed' => false,
                ), 
				'country' => array(
                   //Freddy
				   //'type' => 'select',
					'type' => 'select',
                    'name' => 'country',
					'listname' => 'Country',
                    'caption' => _t('_modzzz_classified_form_caption_country'),
                    'values' => $aCountries,
 					'value' => $sSelCountry,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					),	 
                    'required' => true, 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,5),
                        'error' => _t ('_modzzz_classified_form_err_country'),
                    ),  
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{2})/'),
                    ),
					'display' => 'getPreListDisplay', 
                ), 
				'state' => array(
					'type' => 'select',
					'name' => 'state',
					
					//'value' => $sSelState, 
					'value' => $sBusinessState, 
					 
					'values'=> $aStates,
					'caption' => _t('_modzzz_classified_caption_state'),
					'attrs' => array(
						'id' => 'substate',
					), 
					
						//// Freddy add required
					 'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_classified_form_err_state'),
                    ), 
					
					'db' => array (
						'pass' => 'Preg', 
						'params' => array('/([a-zA-Z]+)/'),
					), 
					'display' => 'getStateName',  
				),  
				
				 

                'city' => array(
                    'type' => 'text',
                    'name' => 'city',
                    'caption' => _t('_modzzz_classified_form_caption_city'),
                    'required' => true, 
					
					// Freddy modif
					//'value' => $sSelCity,
					'value' => $sBusinessCity,
					
					
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,150),
                        'error' => _t ('_modzzz_classified_form_err_city'),
                    ),  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),    
                'address1' => array(
                    'type' => 'text',
                    'name' => 'address1',
                    'caption' => _t('_modzzz_classified_form_caption_address1'),
					
					// Freddy add 'value'=> $sBusinessAddress1,
					'value'=> $sBusinessAddress1,
					
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),   				
               /* 'address2' => array(
                    'type' => 'text',
                    'name' => 'address2',
					
                    'caption' => _t('_modzzz_classified_form_caption_address2'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
				*/
				
				'zip' => array(
                    'type' => 'text',
                    'name' => 'zip',
                    'caption' => _t('_modzzz_classified_form_caption_zip'),
					
					//Freddy modif
					//'value' => $sSelZip,
					'value' => $sBusinessZip,
					
                    'required' => false,
                   /*
				    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_classified_form_err_zip'),
                    ), 
					*/
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 

               
                'header_contact' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_classified_form_header_contact_details'),
                    'collapsable' => true,
                    'collapsed' => false,
                ), 
				
				
				
              /*  'sellername' => array(
                    'type' => 'text',
                    'name' => 'sellername',
                    'caption' => _t('_modzzz_classified_form_caption_sellername'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
				*/
                 'sellerwebsite' => array(
                    'type' => 'text',
                    'name' => 'sellerwebsite',
                    'caption' => _t('_modzzz_classified_form_caption_sellerwebsite'),
					
					// Freddy add   'value' => $sBusinessWebsite,
					'value' => $sBusinessWebsite,
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
               /* 'selleremail' => array(
                    'type' => 'email',
                    'name' => 'selleremail',
                    'caption' => _t('_modzzz_classified_form_caption_selleremail'),
                    'required' => false,  
					'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
				*/
				
				// Telephone 1
                'sellertelephone' => array(
                    'type' => 'text',
                    'name' => 'sellertelephone',
                    'caption' => _t('_modzzz_classified_form_caption_sellertelephone'),
					
					// Freddy add 'value' => $sBusinessTelephone,
					'value' => $sBusinessTelephone,
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
               
			   
			   // Telephone 2
			   /*
			    'sellerfax' => array(
                    'type' => 'text',
                    'name' => 'sellerfax',
                    'caption' => _t('_modzzz_classified_form_caption_sellerfax'),
					// Freddy add   'value' => $sBusinessMobile,
					'value' => $sBusinessMobile,
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
				*/
				
				  
				/* freddy comment
				'allow_view_contact' => array(
                    'type' => 'select_box',
                    'name' => 'allow_view_contact',
                    'caption' => _t('_modzzz_classified_form_caption_allow_view_contact'),
					'values' => $aMembershipOptions,  
					'required' => false, 
                    'db' => array (
                        'pass' => 'Categories', 
                     ) 
                ),
				*/
	            // additional custom fields

               /* 'header_additional' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_classified_form_header_additional_info'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),			

                'custom_field1' => array(
                    'type' => 'text',
                    'name' => 'custom_field1',
                    'caption' => _t('_modzzz_classified_form_custom_field1'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_field2' => array(
                    'type' => 'text',
                    'name' => 'custom_field2',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_field3' => array(
                    'type' => 'text',
                    'name' => 'custom_field3',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_field4' => array(
                    'type' => 'text',
                    'name' => 'custom_field4',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_field5' => array(
                    'type' => 'text',
                    'name' => 'custom_field5',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  
 
				'custom_field6' => array(
                    'type' => 'text',
                    'name' => 'custom_field6',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_field7' => array(
                    'type' => 'text',
                    'name' => 'custom_field7',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_field8' => array(
                    'type' => 'text',
                    'name' => 'custom_field8',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_field9' => array(
                    'type' => 'text',
                    'name' => 'custom_field9',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_field10' => array(
                    'type' => 'text',
                    'name' => 'custom_field10',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_sub_field1' => array(
                    'type' => 'text',
                    'name' => 'custom_sub_field1',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_sub_field2' => array(
                    'type' => 'text',
                    'name' => 'custom_sub_field2',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_sub_field3' => array(
                    'type' => 'text',
                    'name' => 'custom_sub_field3',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_sub_field4' => array(
                    'type' => 'text',
                    'name' => 'custom_sub_field4',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_sub_field5' => array(
                    'type' => 'text',
                    'name' => 'custom_sub_field5',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  
 
				'custom_sub_field6' => array(
                    'type' => 'text',
                    'name' => 'custom_sub_field6',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_sub_field7' => array(
                    'type' => 'text',
                    'name' => 'custom_sub_field7',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_sub_field8' => array(
                    'type' => 'text',
                    'name' => 'custom_sub_field8',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_sub_field9' => array(
                    'type' => 'text',
                    'name' => 'custom_sub_field9',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'custom_sub_field10' => array(
                    'type' => 'text',
                    'name' => 'custom_sub_field10',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
				*/ 
            
                // images

                'header_images' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_classified_form_header_images'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'thumb' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['thumb_choice'],
                    'name' => 'thumb',
                    'caption' => _t('_modzzz_classified_form_caption_thumb_choice'),
                    'info' => _t('_modzzz_classified_form_info_thumb_choice'),
                    'required' => false,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ), 				 
                'images_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['choice'],
                    'name' => 'images_choice[]',
                    'caption' => _t('_modzzz_classified_form_caption_images_choice'),
                    'info' => _t('_modzzz_classified_form_info_images_choice'),
                    'required' => false,
                ),  
                'images_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['upload'],
                    'name' => 'images_upload[]',
                    'caption' => _t('_modzzz_classified_form_caption_images_upload'),
                    'info' => _t('_modzzz_classified_form_info_images_upload'),
                    'required' => false,
                ),

                // youtube videos
              
			  /* 'header_youtube' => array(
                   'type' => 'block_header',
                   'caption' => _t('_modzzz_classified_form_header_youtube'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
			   */
               'youtube_choice' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['choice'],
                   'name' => 'youtube_choice[]',
                   'caption' => _t('_modzzz_classified_form_caption_youtube_choice'),
                   'info' => _t('_modzzz_classified_form_info_youtube_choice'),
                   'required' => false,
               ), 
               'youtube_attach' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['upload'],
                   'name' => 'youtube_upload[]',
                   'caption' => _t('_modzzz_classified_form_caption_youtube_attach'),
                   'info' => _t('_modzzz_classified_form_info_youtube_attach'),
                   'required' => false,
               ),
 
                // videos

              /*
			    'header_videos' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_classified_form_header_videos'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
 
                'videos_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['choice'],
                    'name' => 'videos_choice[]',
                    'caption' => _t('_modzzz_classified_form_caption_videos_choice'),
                    'info' => _t('_modzzz_classified_form_info_videos_choice'),
                    'required' => false,
                ),
  
                'videos_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['upload'],
                    'name' => 'videos_upload[]',
                    'caption' => _t('_modzzz_classified_form_caption_videos_upload'),
                    'info' => _t('_modzzz_classified_form_info_videos_upload'),
                    'required' => false,
                ),

                // sounds

                'header_sounds' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_classified_form_header_sounds'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
 
                'sounds_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['choice'],
                    'name' => 'sounds_choice[]',
                    'caption' => _t('_modzzz_classified_form_caption_sounds_choice'),
                    'info' => _t('_modzzz_classified_form_info_sounds_choice'),
                    'required' => false,
                ),
 
                'sounds_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['upload'],
                    'name' => 'sounds_upload[]',
                    'caption' => _t('_modzzz_classified_form_caption_sounds_upload'),
                    'info' => _t('_modzzz_classified_form_info_sounds_upload'),
                    'required' => false,
                ),
				*/
  
                // files

             /*   'header_files' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_classified_form_header_files'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
				*/
 
                'files_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['choice'],
                    'name' => 'files_choice[]',
                    'caption' => _t('_modzzz_classified_form_caption_files_choice'),
                    'info' => _t('_modzzz_classified_form_info_files_choice'),
                    'required' => false,
                ),
 
                'files_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['upload'],
                    'name' => 'files_upload[]',
                    'caption' => _t('_modzzz_classified_form_caption_files_upload'),
                    'info' => _t('_modzzz_classified_form_info_files_upload'),
                    'required' => false,
                ),

                // privacy
                
                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_classified_form_header_privacy'),
                ),

                'allow_view_classified_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'classified', 'view_classified'),

                'allow_comment_to' => $aInputPrivacyComment,

                'allow_rate_to' => $aInputPrivacyRate, 

               // 'allow_post_in_forum_to' => $aInputPrivacyForum,  
                          
            ),            
        );
		
		// Freddy Business integration
		if(count($aCompanies)==0){
            unset ($aCustomForm['inputs']['company_id']);
		} 
		
		
		// Freddy
		// Si PRODUITS " 
		// 1200 correspond a l'enregistrement "PRODUITS" dans la table Category "modzzz_classified_categ"
		
		if($iParentCategoryId=='1200'){
		    $aCustomForm['inputs']['title']['info']=_t('_modzzz_classified_form_info_title_produit');
			$aCustomForm['inputs']['title']['checker'] = array (
						 'func' => 'length',
                        'params' => array(10,100),
                        'error' => _t ('_modzzz_classified_form_err_title_product'),
						); 
				     }
		//[END]Fin Produit "
		
		
		// Si SERVICES " 
		// 1201 correspond a l'enregistrement "SERVICES" dans la table Category "modzzz_classified_categ"
		if($iParentCategoryId=='1201'){
		    $aCustomForm['inputs']['title']['info']=_t('_modzzz_classified_form_info_title_service');
			$aCustomForm['inputs']['title']['checker'] = array (
						 'func' => 'length',
                        'params' => array(10,100),
                        'error' => _t ('_modzzz_classified_form_err_title_service'),
						); 
				     
				}
		//[END]Services "
		
		// Si OPPORTUNITES D'AFFAIRESS " 
		// 1203 correspond a l'enregistrement "OPPORTUNITES D'AFFAIRESS" dans la table Category "modzzz_classified_categ"
		if($iParentCategoryId=='1203'){
		    $aCustomForm['inputs']['title']['info']=_t('_modzzz_classified_form_info_title_opportunite');
			$aCustomForm['inputs']['price']['type']='hidden';
			$aCustomForm['inputs']['title']['checker'] = array (
						 'func' => 'length',
                        'params' => array(10,100),
                        'error' => _t ('_modzzz_classified_form_err_title_opportunite'),
						); 
				     
				}
		//[END]OPPORTUNITES D'AFFAIRESS"
		
		
		// Si APPEL D OFFRES " 
		// 1295 correspond a l'enregistrement "APPEL D OFFRES" dans la table Category "modzzz_classified_categ"
		if($iParentCategoryId=='1295'){
		    $aCustomForm['inputs']['title']['info']=_t('_modzzz_classified_form_info_title_appel_doffre');
			$aCustomForm['inputs']['price']['type']='hidden';
			$aCustomForm['inputs']['title']['checker'] = array (
						 'func' => 'length',
                        'params' => array(10,100),
                        'error' => _t ('_modzzz_classified_form_err_title_appel_doffre'),
						); 
				     
				}
		//[END]APPEL D OFFRES "
		
		
		
		
		

		
		
		// Fin freddy

        if (!$aCustomForm['inputs']['images_choice']['content']) {
            unset ($aCustomForm['inputs']['thumb']);
            unset ($aCustomForm['inputs']['images_choice']);
        }

        if (!$aCustomForm['inputs']['videos_choice']['content'])
            unset ($aCustomForm['inputs']['videos_choice']);

        if (!$aCustomForm['inputs']['sounds_choice']['content'])
            unset ($aCustomForm['inputs']['sounds_choice']);

        if (!$aCustomForm['inputs']['files_choice']['content'])
            unset ($aCustomForm['inputs']['files_choice']);
  

		//[begin] added 7.1
       if (!isset($this->_aMedia['images'])) {
            unset ($aCustomForm['inputs']['header_images']);
            unset ($aCustomForm['inputs']['thumb']);
            unset ($aCustomForm['inputs']['images_choice']);
            unset ($aCustomForm['inputs']['images_upload']);
            unset ($aCustomForm['inputs']['allow_upload_photos_to']);
        }

        if (!isset($this->_aMedia['videos'])) {
            unset ($aCustomForm['inputs']['header_videos']);
            unset ($aCustomForm['inputs']['videos_choice']);
            unset ($aCustomForm['inputs']['videos_upload']);
            unset ($aCustomForm['inputs']['allow_upload_videos_to']);
        }

        if (!isset($this->_aMedia['sounds'])) {
            unset ($aCustomForm['inputs']['header_sounds']);
            unset ($aCustomForm['inputs']['sounds_choice']);
            unset ($aCustomForm['inputs']['sounds_upload']);
            unset ($aCustomForm['inputs']['allow_upload_sounds_to']);
        }

        if (!isset($this->_aMedia['files'])) {
            unset ($aCustomForm['inputs']['header_files']);
            unset ($aCustomForm['inputs']['files_choice']);
            unset ($aCustomForm['inputs']['files_upload']);
            unset ($aCustomForm['inputs']['allow_upload_files_to']);
        }

        if (!$aCustomYoutubeTemplates['choice']) {
 			unset($aCustomForm['inputs']['youtube_choice']); 
  		}
 
        $oModuleDb = new BxDolModuleDb();
        if (!$oModuleDb->getModuleByUri('forum'))
            unset ($aCustomForm['inputs']['allow_post_in_forum_to']);
		//[end] added 7.1

 
 
 
      if(!$bBusiness){
            unset ($aCustomForm['inputs']['display_business']); 
		}
		// Fin Freddy Business listing
		
		
		if(!$bPaidClassified) {
            unset ($aCustomForm['inputs']['package_id']);
            unset ($aCustomForm['inputs']['package_name']);  
		}

		if($iParentCategoryId){
			$bHasCustomFields = false;
			$aCategoryCustomFields = $this->_oMain->_oDb->getCategoryCustomData($iParentCategoryId);
			for($iter=1; $iter<=10; $iter++){
				$sCustomVal = trim($aCategoryCustomFields['custom_field'.$iter]);
			
				if($sCustomVal){
					$bHasCustomFields = true;
					$aCustomForm['inputs']['custom_field'.$iter]['caption']= _t($sCustomVal);
				}else{
					unset ($aCustomForm['inputs']['custom_field'.$iter]);  
				}
			}
		}

		if($iCategoryId){
			$aSubCategoryCustomFields = $this->_oMain->_oDb->getCategoryCustomData($iCategoryId);
			for($iter=1; $iter<=10; $iter++){
				$sCustomVal = trim($aSubCategoryCustomFields['custom_field'.$iter]);
			
				if($sCustomVal){
					$bHasCustomFields = true;
					$aCustomForm['inputs']['custom_sub_field'.$iter]['caption']= _t($sCustomVal);
				}else{
					unset ($aCustomForm['inputs']['custom_sub_field'.$iter]);  
				}
			}
		}

		if(!$bHasCustomFields){ 
				unset ($aCustomForm['inputs']['header_additional']);  
		}

        $aFormInputsAdminPart = array ();
        if ($GLOBALS['oBxClassifiedModule']->isAdmin()) {

            require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
            $aMemberships = getMemberships ();
            unset ($aMemberships[MEMBERSHIP_ID_NON_MEMBER]); // unset Non-member
            $aMemberships = array('' => _t('_modzzz_classified_membership_filter_none')) + $aMemberships;
            $aFormInputsAdminPart = array (
 
				'membership_view_filter' => array(
					'type' => 'select',
					'name' => 'membership_view_filter',
					'caption' => _t('_modzzz_classified_caption_membership_view_filter'), 
					'info' => _t('_modzzz_classified_info_membership_view_filter'), 
					'values' => $aMemberships,
					'value' => '', 
					'checker' => array (
					'func' => 'preg',
					'params' => array('/^[0-9a-zA-Z]*$/'),
					'error' => _t ('_modzzz_classified_err_membership_view_filter'),
					),                                        
					'db' => array (
					'pass' => 'Preg', 
					'params' => array('/([0-9a-zA-Z]*)/'),
					),
					
				),
  
            );
        } 

        $aFormInputsSubmit = array (
            'Submit' => array (
                'type' => 'submit',
                'name' => 'submit_form',
                'value' => _t('_Submit'),
                'colspan' => false,
            ),            
        );

        $aCustomForm['inputs'] = array_merge($aCustomForm['inputs'], $aFormInputsAdminPart, $aFormInputsSubmit);
  
		if($iLoggedIn=getLoggedId()){
			unset($aCustomForm['inputs']['user_name']); 
		}


		if($bPaidClassified){
			 $this->processPackageChecksForMediaUploads ($iPackageId, $aCustomForm['inputs']);
		}else{
			 $this->processMembershipChecksForMediaUploads ($aCustomForm['inputs']); 
		}

        parent::BxDolFormMedia ($aCustomForm);
    }

    function processPackageChecksForMediaUploads ($iPackageId, &$aInputs) {

        $isAdmin = $GLOBALS['logged']['admin'] && isProfileActive($this->_iProfileId);

		if($isAdmin)
		   return;

		$aPackage = $this->_oDb->getPackageById($iPackageId);
  
        $a = array ('images', 'videos', 'sounds', 'files' );
        foreach ($a as $k ) {
			$isAllowedMedia = $aPackage[$k];
            if ( !$isAllowedMedia ) {
                unset($this->_aMedia[$k]);  
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']);

 				if($k=='images'){
					unset($aInputs[$k.'_thumb']);
					unset($aInputs['allow_upload_photos_to']); 
				}else{
					unset($aInputs['allow_upload_'.$k.'_to']); 
				} 

				if($k=='videos'){
					unset($aInputs['header_youtube']);
					unset($aInputs['youtube_choice']); 
					unset($aInputs['youtube_attach']); 
				} 

            }        
        }  
    }

    function processMembershipChecksForMediaUploads (&$aInputs) {

        $isAdmin = $GLOBALS['logged']['admin'] && isProfileActive($this->_iProfileId);

        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'classified photos add', 'classified sounds add', 'classified videos add', 'classified files add', 'classified allow embed'));

		if (defined("BX_PHOTOS_ADD")){
			$aCheck = checkAction($_COOKIE['memberID'], BX_PHOTOS_ADD);
			if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED && !$isAdmin) {
				unset($aInputs['thumb']);
			}
		}

        $a = array ('images' => 'PHOTOS', 'videos' => 'VIDEOS', 'sounds' => 'SOUNDS', 'files' => 'FILES');
        foreach ($a as $k => $v) {
			if (defined("BX_{$v}_ADD"))
				$aCheck = checkAction($_COOKIE['memberID'], constant("BX_{$v}_ADD"));
            if ((!defined("BX_{$v}_ADD") || $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) && !$isAdmin) {
                unset($this->_aMedia[$k]);
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']); 
            }        
        }

        $a = array ('images' => 'PHOTOS', 'videos' => 'VIDEOS', 'sounds' => 'SOUNDS', 'files' => 'FILES');
        foreach ($a as $k => $v) {
			if (defined("BX_CLASSIFIED_{$v}_ADD"))
				$aCheck = checkAction($_COOKIE['memberID'], constant("BX_CLASSIFIED_{$v}_ADD"));
            if ((!defined("BX_CLASSIFIED_{$v}_ADD") || $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) && !$isAdmin) {
                unset($this->_aMedia[$k]);
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']); 

            }        
        } 

		$aCheck = checkAction($_COOKIE['memberID'],  BX_CLASSIFIED_ALLOW_EMBED);
		if ( $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED && !$isAdmin) { 
			unset($aInputs['header_youtube']);
			unset($aInputs['youtube_choice']); 
			unset($aInputs['youtube_attach']);  
 		}


    }

    /**
     * @access private
     */ 
    function _getFilesInEntry ($sModuleName, $sServiceMethod, $sName, $sMediaType, $iIdProfile, $iEntryId)
    {             

        $aReadyMedia = array ();
        if ($iEntryId)
            $aReadyMedia = $this->_oDb->getMediaIds($iEntryId, $sMediaType);
        
        if (!$aReadyMedia)
            return array();

        $aDataEntry = $this->_oDb->getEntryById($iEntryId);

        $aFiles = array ();
        foreach ($aReadyMedia as $iMediaId)
        {
            switch ($sModuleName) {
            case 'photos':
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId, 'icon'), 'Search');
                break;
            case 'sounds':
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId, 'browse'), 'Search');
                break;
            default:
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId), 'Search');
            }
    
            if (!$this->_oMain->isEntryAdmin($aDataEntry, $iIdProfile) && $aRow['owner'] != $iIdProfile)
                continue;

            $aFiles[] = array (
                'name' => $sName,
                'id' => $iMediaId,
                'title' => $aRow['title'],
                'icon' => $aRow['file'],
                'owner' => $aRow['owner'],
                'checked' => 'checked',
            );
        }
        return $aFiles;
    }        

    /**
     * process media upload updates
     * call it after successful call $form->insert/update functions 
     * @param $iEntryId associated entry id
     * @return nothing
     */ 
    function processMedia ($iEntryId, $iProfileId) { 

        $aDataEntry = $this->_oDb->getEntryById($iEntryId);

        foreach ($this->_aMedia as $sName => $a) {
			 
            $aFiles = $this->_getFilesInEntry ($a['module'], $a['service_method'], $a['post'], $sName, (int)$iProfileId, $iEntryId);
            foreach ($aFiles as $aRow)
                $aFiles2Delete[$aRow['id']] = $aRow['id'];

            if (is_array($_REQUEST[$a['post']]) && $_REQUEST[$a['post']] && $_REQUEST[$a['post']][0]) {
                $this->updateMedia ($iEntryId, $_REQUEST[$a['post']], $aFiles2Delete, $sName);
            } else {
                $this->deleteMedia ($iEntryId, $aFiles2Delete, $sName);
            }

            $sUploadFunc = $a['upload_func'];
            if ($aMedia = $this->$sUploadFunc($a['tag'], $a['cat'])) {
                $this->_oDb->insertMedia ($iEntryId, $aMedia, $sName);
                if ($a['thumb'] && !$aDataEntry[$a['thumb']] && !$_REQUEST[$a['thumb']]) 
                    $this->_oDb->setThumbnail ($iEntryId, 0);
            }

            $aMediaIds = $this->_oDb->getMediaIds($iEntryId, $sName);

            if ($a['thumb']) { // set thumbnail to another one if current thumbnail is deleted                
                $sThumbFieldName = $a['thumb'];
                if ($aDataEntry[$sThumbFieldName] && !isset($aMediaIds[$aDataEntry[$sThumbFieldName]])) {
                    $this->_oDb->setThumbnail ($iEntryId, 0);
                } 
            }

            // process all deleted media - delete actual file
			if(is_array($aFiles2Delete)){
				$aDeletedMedia = array_diff ($aFiles2Delete, $aMediaIds);
				if ($aDeletedMedia) {
					foreach ($aDeletedMedia as $iMediaId) {
						if (!$this->_oDb->isMediaInUse($iMediaId, $sName))
							BxDolService::call($a['module'], 'remove_object', array($iMediaId));
					}
				}
			}
        }

    }    

	function generateCustomYoutubeTemplate ($iProfileId, $iEntryId) {
	 
		$aTemplates = array ();
	
		$aYoutubes = $this->_oDb->getYoutubeVideos ($iEntryId); 
 
		$aFeeds = array();
		foreach ($aYoutubes as $k => $r) {
			$aFeeds[$k] = array();
			$aFeeds[$k]['id'] = $r['id'];
			$aFeeds[$k]['video_id'] = $this->_oTemplate->youtubeId($r['url']);
			$aFeeds[$k]['video_title'] = $r['title'];
		}

		$aVarsChoice = array ( 
			'bx_if:empty' => array(
				'condition' => false,
				'content' => array ()
			), 
			'bx_repeat:videos' => $aFeeds,
		);                               
		$aTemplates['choice'] =  empty($aFeeds) ? '' : $this->_oMain->_oTemplate->parseHtmlByName('form_field_youtube_choice', $aVarsChoice);
		
		// upload form
		$aVarsUpload = array ();            
		$aTemplates['upload'] = $this->_oMain->_oTemplate->parseHtmlByName('form_field_youtube', $aVarsUpload);
 
		return $aTemplates;
	} 




}
