<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Investment
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
* see license.txt file; if not, write to investmenting@boonex.com
***************************************************************************/

bx_import ('BxDolProfileFields');
bx_import ('BxDolFormMedia');

class BxInvestmentFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;
	
    function BxInvestmentFormAdd ($oMain, $iProfileId, $iEntryId = 0, $iThumb = 0) {

		$this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
   
        bx_import('BxDolCategories');
       $oCategories = new BxDolCategories();        
 
   		$bPaidInvestment = $this->_oMain->isAllowedPaidInvestments (); 

		$sDefaultTitle = stripslashes($_REQUEST['title']);

		$aNumberList = $this->_oDb->getNumberList(1, 1000);
 
		if($iEntryId) {
			$aDataEntry = $this->_oDb->getEntryById($iEntryId); 
			$bPaidInvestment = $aDataEntry['invoice_no'] ? $bPaidInvestment : false;

			$sSelState = $aDataEntry['state']; 
			$sSelCountry = $aDataEntry['country'];  
			$aStates = $this->_oDb->getStateArray($sSelCountry);  

 		}else { 			
			if($_POST['country']){
				$sSelCountry = $_POST['country'];
			}else{
				$aProfile = getProfileInfo($this->_oMain->_iProfileId);
 				$sSelCountry = ($aProfile['Country']) ? $aProfile['Country'] : getParam('modzzz_investment_default_country');
			}
            
			
			// freddy ajout $aProfile = getProfileInfo($this->_oMain->_iProfileId);
			$aProfile = getProfileInfo($this->_oMain->_iProfileId);
			$sSelState = ($_POST['state']) ? $_POST['state'] : ''; 
			$aStates = $this->_oDb->getStateArray($sSelCountry);   
			$aStates[''] = '--'._t('_Select').'--'; 
			
			
			// freddy ajout
			$sSellerName = ($_POST['sellername']) ? $_POST['sellername'] :  $aProfile['FirstName'] .' '.$aProfile['LastName']; 
			$sSellerEmail = ($_POST['selleremail']) ? $_POST['selleremail'] :  $aProfile['Email']; 
			$sSellerTelephone = ($_POST['sellertelephone']) ? $_POST['sellertelephone'] :  $aProfile['Phone'] ; 
			$sSellerCity = ($_POST['city']) ? $_POST['city'] :  $aProfile['City'] ; 
			//////////////////////
		}
  
		$sInvestmentType = ($aDataEntry['investment_type']) ? $aDataEntry['investment_type'] : $_POST['investment_type']; 

		switch($sInvestmentType){ 
			case 'investor':
				$sTypeDescC  = _t('_modzzz_investment_investor'); 
				$sWhyCaptionC  = _t('_modzzz_investment_why_invest'); 
				
				// freddy ajout  $sHeaderInfoCaptionC 
				$sHeaderInfoCaptionC  = _t('_modzzz_investment_form_header_info_investisseur'); 
				
				// freddy ajout  $sHeaderCategoriesCaptionC 
				$sHeaderCategoriesCaptionC  = _t('_modzzz_investment_form_header_categories_investisseur'); 
				
				// freddy ajout  $sHeaderInvestissementCaptionC 
				$sHeaderInvestissementCaptionC  = _t('_modzzz_investment_form_header_investment'); 
				
				
				// freddy ajout  $sHeaderInvestissementCaptionC 
				$sHeaderDetailCaptionC  = _t('_modzzz_investment_form_header_detail'); 
				
				
				// freddy ajout  $sHeaderDescriptionCaptionC 
				$sHeaderDescriptionCaptionC  = _t('_modzzz_investment_form_caption_desc_investor'); 
			$sHeaderlocalisation_ProjetC  = _t('_modzzz_investment_form_header_localisation_Projet_investor');
			
			$sHeaderCountryCaptionC  = _t('_modzzz_investment_form_caption_country_investor'); 
				break;
			case 'entrepreneur':			
				$sTypeDescC  = _t('_modzzz_investment_entrepreneur');
				$sWhyCaptionC  = _t('_modzzz_investment_why_seek_invest'); 
				
				// freddy ajout  $sHeaderInfoCaptionC 
				$sHeaderInfoCaptionC  = _t('_modzzz_investment_form_header_info_entrepreneur'); 
				
				// freddy ajout  $sHeaderInvestissementCaptionC 
				$sHeaderDetailCaptionC  = _t('_modzzz_investment_form_header_projet'); 
				
				// freddy ajout  $sHeaderCategoriesCaptionC 
				$sHeaderCategoriesCaptionC  = _t('_modzzz_investment_form_header_categories_entrepreneur'); 
				
				// freddy ajout  $sHeaderInvestissementCaptionC 
				$sHeaderInvestissementCaptionC  = _t('_modzzz_investment_form_header_investment_entrepreneur'); 
				
				// freddy ajout  $sHeaderDescriptionCaptionC 
				$sHeaderDescriptionCaptionC  = _t('_modzzz_investment_form_caption_desc_entrepreneur'); 
				
				$sHeaderCountryCaptionC  = _t('_modzzz_investment_form_caption_country_entrepreneur');
				
				
				$sHeaderlocalisation_ProjetC  = _t('_modzzz_investment_form_header_localisation_Projet_entrepreneur'); 
				
				break; 
			case 'professional':			
				$sTypeDescC  = _t('_modzzz_investment_professional');
				$sWhyCaptionC  = _t('_modzzz_investment_why_seek_professional'); 
				
				// freddy ajout  $sHeaderInfoCaptionC 
				$sHeaderInfoCaptionC  = _t('_modzzz_investment_form_header_info_professionnel'); 
				
				// freddy ajout  $sHeaderCategoriesCaptionC 
				$sHeaderCategoriesCaptionC  = _t('_modzzz_investment_form_header_categories_professionnel'); 
				
				// freddy ajout  $sHeaderInvestissementCaptionC 
				$sHeaderInvestissementCaptionC  = _t('_modzzz_investment_form_header_investment'); 
				
				// freddy ajout  $sHeaderDescriptionCaptionC 
				$sHeaderDescriptionCaptionC  = _t('_modzzz_investment_form_caption_desc_professional'); 
				
				break;  
		}
 
		$sStateUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home/'.($this->_oMain->isPermalinkEnabled() ? '?' : '&').'ajax=state&country=' ; 
    
		if($bPaidInvestment){
			$iPackageId = ($iEntryId) ? (int)$this->_oDb->getPackageIdByInvoiceNo($aDataEntry['invoice_no']) : (int)$_POST['package_id']; 
			$sPackageName = $this->_oDb->getPackageName($iPackageId);
		}
 
        $this->_aMedia = array ();

        if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            $this->_aMedia['images'] = array (
                'post' => 'ready_images',
                'upload_func' => 'uploadPhotos',
                'tag' => BX_INVESTMENT_PHOTOS_TAG,
                'cat' => BX_INVESTMENT_PHOTOS_CAT,
                'thumb' => 'thumb',
                'module' => 'photos',
                'title_upload_post' => 'images_titles',
                'title_upload' => _t('_modzzz_investment_form_caption_file_title'),
                'service_method' => 'get_photo_array',
            );

        if (BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            $this->_aMedia['videos'] = array (
                'post' => 'ready_videos',
                'upload_func' => 'uploadVideos',
                'tag' => BX_INVESTMENT_VIDEOS_TAG,
                'cat' => BX_INVESTMENT_VIDEOS_CAT,
                'thumb' => false,
                'module' => 'videos',
                'title_upload_post' => 'videos_titles',
                'title_upload' => _t('_modzzz_investment_form_caption_file_title'),
                'service_method' => 'get_video_array',
            );

        if (BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            $this->_aMedia['sounds'] = array (
                'post' => 'ready_sounds',
                'upload_func' => 'uploadSounds',
                'tag' => BX_INVESTMENT_SOUNDS_TAG,
                'cat' => BX_INVESTMENT_SOUNDS_CAT,
                'thumb' => false,
                'module' => 'sounds',
                'title_upload_post' => 'sounds_titles',
                'title_upload' => _t('_modzzz_investment_form_caption_file_title'),
                'service_method' => 'get_music_array',
            );

        if (BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            $this->_aMedia['files'] = array (
                'post' => 'ready_files',
                'upload_func' => 'uploadFiles',
                'tag' => BX_INVESTMENT_FILES_TAG,
                'cat' => BX_INVESTMENT_FILES_CAT,
                'thumb' => false,
                'module' => 'files',
                'title_upload_post' => 'files_titles',
                'title_upload' => _t('_modzzz_investment_form_caption_file_title'),
                'service_method' => 'get_file_array',
            );
 
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
 
        $aCurrencyList = $oProfileFields->convertValues4Input('#!InvestmentCurrency');
        asort($aCurrencyList);
		
		 $aInvestmentStadeList = $oProfileFields->convertValues4Input('#!InvestmentStade');
		$aOuiNonModuleList = $oProfileFields->convertValues4Input('#!OuiNonModule');
        ksort($$aOuiNonModuleList);
		
		$aInvestorProjetRechercher = $oProfileFields->convertValues4Input('#!InvestorProjetRechercher');
		
		$aInvestorDiasporaRdcList = $oProfileFields->convertValues4Input('#!InvestorDiasporaRdc');
		
		$aProjetBudgetList = $oProfileFields->convertValues4Input('#!ProjetBudget');
		
		
		
		
		 
 
        // generate templates for custom form's elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($oMain->_iProfileId, $iEntryId, $iThumb);
 
        // privacy 
        $aInputPrivacyCustomPass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9f]+)$/'),
        );
 
        $aInputPrivacyComment = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'investment', 'comment');
        $aInputPrivacyComment['values'] = $aInputPrivacyComment['values'];
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'investment', 'rate');
        $aInputPrivacyRate['values'] = $aInputPrivacyRate['values'];
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyForum = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'investment', 'post_in_forum');
        $aInputPrivacyForum['values'] = $aInputPrivacyForum['values'];
        $aInputPrivacyForum['db'] = $aInputPrivacyCustomPass;
 
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_investment',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_investment_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

               /* 'header_info' => array(
                    'type' => 'block_header',
                    // freddy modif 
					//'caption' => _t('_modzzz_investment_form_header_info')
					'caption' =>$sHeaderInfoCaptionC
                ),
				*/                

                'investment_type' => array(
                    'type' => 'hidden',
                    'name' => 'investment_type',
                    'value' => $sInvestmentType,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ), 
                'investment_type_display' => array(
                    'type' => 'custom',
                    'caption' =>_t('_modzzz_investment_form_caption_investment_type'),
                    'content' => '<b>'.'<span style="color:green; font-size: 18px;">'.$sTypeDescC .'</span>'.'</b>',   
                ),	 
                'package_id' => array(
                    'type' => 'hidden',
                    'name' => 'package_id',
                    'value' => $iPackageId 
                ),    
				'package_name' => array( 
					'type' => 'custom',
                    'content' => $sPackageName,  
                    'name' => 'package_name',
                    'caption' => _t('_modzzz_investment_package'), 
                ), 
				
				
				
				 'header_personal_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_investment_form_header_personal_info'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
				
				 'sellername' => array(
                    'type' => 'text',
                    'name' => 'sellername',
					// freddy ajout 'value' => $sSellerName, 
					'value' => $sSellerName, 
                    'caption' => _t('_modzzz_investment_form_caption_sellername'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
				
				
				 'selleremail' => array(
                    'type' => 'email',
                    'name' => 'selleremail',
					// freddy ajout 'value' => $sSellerEmail, 
					'value' => $sSellerEmail, 
                    'caption' => _t('_modzzz_investment_form_caption_selleremail'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_investment_form_err_selleremail'),
                    ), 
					'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
				
                'sellertelephone' => array(
                    'type' => 'text',
                    'name' => 'sellertelephone',
					// freddy ajout 'value' => $sSellerTelephone, 
					'value' => $sSellerTelephone, 
                    'caption' => _t('_modzzz_investment_form_caption_sellertelephone'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_investment_form_err_sellertelephone'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                 'sellerwebsite' => array(
                    'type' => 'text',
                    'name' => 'sellerwebsite',
                    'caption' => _t('_modzzz_investment_form_caption_sellerwebsite'),
                    'required' => false,
                   
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
				
				'header_localisation_Projet' => array(
                    'type' => 'block_header',
                    'caption' => $sHeaderlocalisation_ProjetC ,
                    'collapsable' => true,
                    'collapsed' => false,
                ),
				'country' => array(
                    'type' => 'select',
                    'name' => 'country',
					'listname' => 'Country',
                    'caption' => $sHeaderCountryCaptionC,
                    'values' => $aCountries,
 					'value' => $sSelCountry,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
						'style' => 'width:240px',
					),	 
                    'required' => true, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{2})/'),
                    ),
					'display' => 'getPreListDisplay', 
                ),
				'state' => array(
					'type' => 'select',
					'name' => 'state',
					'value' => $sSelState,  
					'values'=> $aStates,
					'caption' => _t('_modzzz_investment_caption_state'),
					'attrs' => array(
						'id' => 'substate',
						'style' => 'width:240px',
					), 
					 'required' => false, 
					 /*'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_investment_form_err_state'),
                    ),
					*/
					'db' => array (
					'pass' => 'Preg', 
					'params' => array('/([a-zA-Z]+)/'),
					), 
					'display' => 'getStateName',  
				), 
                'city' => array(
                    'type' => 'text',
                    'name' => 'city',
                    'caption' => _t('_modzzz_investment_form_caption_city'),
					'value' => $sSellerCity, 
                    'required' => false, 
                   /* 'checker' => array (
                        'func' => 'length',
                        'params' => array(3,150),
                        'error' => _t ('_modzzz_investment_form_err_city'),
                    ),  
					*/
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),    
              
			  //////Êtes-vous membre de la diaspora congolaise ?
			   'zip' => array(
                   'type' => 'select',
                    'name' => 'zip',
					 'listname' => 'OuiNonModule',
					'values'=> $aOuiNonModuleList,
                    'caption' => _t('_modzzz_investment_form_caption_diaspora'),
					'attrs' => array(
						
						'style' => 'width:240px',
					),
					
					'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_investment_form_err_diaspora'),
                    ),
                    'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ),  
				
				////Avez-vous déjà collaboré avec des projets ou entreprises en RDC ? 
				'address2' => array(
                    'type' => 'select',
                    'name' => 'address2',
					 'listname' => 'OuiNonModule',
					'values'=> $aOuiNonModuleList,
                    'caption' => _t('_modzzz_investment_form_caption_collaboration'),
					'attrs' => array(
						
						'style' => 'width:240px',
					),
                   'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_investment_form_err_collaboration'),
                    ),
                   'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ),  
				
				
				 'header_projet' => array(
                    'type' => 'block_header',
                    'caption' => $sHeaderDetailCaptionC,
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_investment_form_caption_title'),
                    'value' => $sDefaultTitle, 
                    'required' => true, 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(2,150),
                        'error' => _t ('_modzzz_investment_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
				
				 'stade' => array(
                    'type' => 'select',
                    'name' => 'stade',
                    'listname' => 'InvestmentStade',
					'values'=> $aInvestmentStadeList,
                    'caption' => _t('_modzzz_investment_form_caption_stade'), 
					'attrs' => array(
						
						'style' => 'width:240px',
					),
                    'required' => false,
                    
                    'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ),  
				
				
				
				'categories' => $oCategories->getGroupChooserMutshi_Investment ('modzzz_investment', (int)$iProfileId, true), 
				
				
				//Montant que vous êtes prêt à investir
				
					'financement_recu' => array(
                    'type' => 'select',
					
                    'name' => 'financement_recu',
					 'listname' => 'ProjetBudget',
					'values'=> $aProjetBudgetList,
                    'caption' => _t('_modzzz_investment_form_caption_financement_budget'),
					'attrs' => array(
						
						'style' => 'width:240px',
					),
					'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_investment_form_err_financement'),
                    ), 
                   
                   'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ),  
				
				
				
				
				 'desc' => array(
                    'type' => 'textarea',
                    'name' => 'desc',
                   // freddy modif 'caption' => _t('_modzzz_investment_form_caption_desc'),
				   'caption' => $sHeaderDescriptionCaptionC,
                    'required' => true,
                    'html' => 1,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(20,256000),
                        'error' => _t ('_modzzz_investment_form_err_desc'),
                    ),                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),  
				
				//Quelles sont vos attentes vis-à-vis des investisseurs ?
				  'sellerfax' => array(
                    'type' => 'text',
                    'name' => 'sellerfax',
                    'caption' => _t('_modzzz_investment_form_caption_sellerfax'),
                    'required' => false,
                   
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
				
				/// Avez-vous des partenaires ou sponsors existants
								
				'address1' => array(
                    'type' => 'select',
                    'name' => 'address1',
					 'listname' => 'OuiNonModule',
					'values'=> $aOuiNonModuleList,
                    'caption' => _t('_modzzz_investment_form_caption_partenaires'),
					'attrs' => array(
						
						'style' => 'width:240px',
					),
                   'required' => false,
                    
                   'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ),  
				
				//  Voulez-vous de l'aide pour structurer votre projet ?
				'aide' => array(
                    'type' => 'select',
                    'name' => 'aide',
					 'listname' => 'OuiNonModule',
					'values'=> $aOuiNonModuleList,
                    'caption' => _t('_modzzz_investment_form_caption_aide'),
					'attrs' => array(
						
						'style' => 'width:240px',
					),
                   'required' => false,
                   
                   'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ),  
				
				
				
				////Types de projets recherchés (Type of Projects Sought)
				 'min_investment' => array(
                    'type' => 'select',
                    'name' => 'min_investment',
					 'listname' => 'InvestorProjetRechercher',
					'values'=> $aInvestorProjetRechercher,
                    'caption' => _t('_modzzz_investment_form_caption_min_investment'),
                   'attrs' => array(
						
						'style' => 'width:240px',
					),
                   'required' => false,
                   /*
				    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_investment_form_err_aide'),
                    ),
					*/
                 
					 'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ), 
				
				'diasporardc' => array(
                    'type' => 'select',
                    'name' => 'diasporardc',
					 'listname' => 'InvestorDiasporaRdc',
					'values'=> $aInvestorDiasporaRdcList,
                    'caption' => _t('_modzzz_investment_form_caption_diasporardc'),
					'attrs' => array(
						
						'style' => 'width:240px',
					),
                   'required' => false,
                   
                   'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ),    
				
				'presentation' => array(
                    'type' => 'select',
                    'name' => 'presentation',
					 'listname' => 'OuiNonModule',
					'values'=> $aOuiNonModuleList,
                    'caption' => _t('_modzzz_investment_form_caption_presentation'),
					'attrs' => array(
						
						'style' => 'width:240px',
					),
                   'required' => false,
                   
                   'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ),    
				
				'terme' => array(
                    'type' => 'select',
                    'name' => 'terme',
					 'listname' => 'OuiNonModule',
					'values'=> $aOuiNonModuleList,
                    'caption' => _t('_modzzz_investment_form_caption_terme'),
					'attrs' => array(
						
						'style' => 'width:240px',
					),
                   'required' => false,
                   
                   'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ), 
				
				'rencontre' => array(
                    'type' => 'select',
                    'name' => 'rencontre',
					 'listname' => 'OuiNonModule',
					'values'=> $aOuiNonModuleList,
                    'caption' => _t('_modzzz_investment_form_caption_rencontre'),
					'attrs' => array(
						
						'style' => 'width:240px',
					),
                   'required' => false,
                   
                   'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ),   
				
				
				
				'commentaires' => array(
                    'type' => 'textarea',
                    'name' => 'commentaires',
                   // freddy modif 'caption' => _t('_modzzz_investment_form_caption_desc'),
				   'caption' => _t ('_modzzz_investment_form_caption_commentaires'),
                    'required' => false,
                    'html' => 0,
                   /* 'checker' => array (
                        'func' => 'length',
                        'params' => array(20,256000),
                        'error' => _t ('_modzzz_investment_form_err_commentaires'),
                    ), 
					*/                   
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),  
				
				
				
				/*
				 'header_investment' => array(
                    'type' => 'block_header',
                    'caption' => $sHeaderInvestissementCaptionC,
                    'collapsable' => true,
                    'collapsed' => false,
                ),
				
			
               
                'max_investment' => array(
                    'type' => 'text',
                    'name' => 'max_investment',
                    'caption' => _t('_modzzz_investment_form_caption_max_investment'),
                    'required' => false, 
                    freddy comment
				    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([0-9.,]+)/'),
                    ), 
					
					'db' => array (
                        'pass' => 'Xss', 
                    ), 
                    'display' => true,
                ),	
				*/
               /* 'currency' => array(
                    'type' => 'select',
                    'name' => 'currency',
                    'caption' => _t('_modzzz_investment_form_caption_currency'),
					'values' => $aCurrencyList, 
					'value' => getParam('modzzz_investment_currency_sign'),
					'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                     ),  
                    'display' => true,
                ),	
				*/
				/*
				'header_categories' => array(
                    'type' => 'block_header',
                    // freddy modif 
					//'caption' => _t('_modzzz_investment_form_header_categories_investisseur'),
					'caption' => $sHeaderCategoriesCaptionC,
                    'collapsable' => true,
                    'collapsed' => false,
                ),  
				'categories' => $oCategories->getGroupChooserMutshi_Investment ('modzzz_investment', (int)$iProfileId, true), 
				
				
				*/
				
				
              /*  	
                'address2' => array(
                    'type' => 'text',
                    'name' => 'address2',
                    'caption' => _t('_modzzz_investment_form_caption_address2'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                
				*/
					
				

				 
               
                /*
				 'why' => array(
                    'type' => 'textarea',
                    'name' => 'why',
                    'caption' => $sWhyCaptionC,
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ),

 
                'tags' => array(
                    'type' => 'text',
                    'name' => 'tags',
                    'caption' => _t('_Tags'),
                    'info' => _t('_sys_tags_note'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_investment_form_err_tags'),
                    ),
                    'db' => array (
                        'pass' => 'Tags', 
                    ),
                ), 
				 

               
                'header_contact' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_investment_form_header_contact_details'),
                    'collapsable' => true,
                    'collapsed' => false,
                ), 
              
				
				*/
				
              
         
                // images

               /* 'header_images' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_investment_form_header_images'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
				*/
				
								
                'thumb' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['thumb_choice'],
                    'name' => 'thumb',
                    'caption' => _t('_modzzz_investment_form_caption_thumb_choice'),
                    'info' => _t('_modzzz_investment_form_info_thumb_choice'),
                    'required' => false,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ), 				 
                'images_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['choice'],
                    'name' => 'images_choice[]',
                    'caption' => _t('_modzzz_investment_form_caption_images_choice'),
                    'info' => _t('_modzzz_investment_form_info_images_choice'),
                    'required' => false,
                ),  
                'images_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['upload'],
                    'name' => 'images_upload[]',
                    'caption' => _t('_modzzz_investment_form_caption_images_upload'),
                    'info' => _t('_modzzz_investment_form_info_images_upload'),
                    'required' => false,
                ),
				
				  // files

               /* 'header_files' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_investment_form_header_files'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
				*/
 
                'files_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['choice'],
                    'name' => 'files_choice[]',
                    'caption' => _t('_modzzz_investment_form_caption_files_choice'),
                    'info' => _t('_modzzz_investment_form_info_files_choice'),
                    'required' => false,
                ),
 
                'files_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['upload'],
                    'name' => 'files_upload[]',
                    'caption' => _t('_modzzz_investment_form_caption_files_upload'),
                    'info' => _t('_modzzz_investment_form_info_files_upload'),
                    'required' => false,
                ),

				// embed video
				/*
				'header_video_embed' => array(
					'type' => 'block_header',
					'caption' => _t('_modzzz_investment_form_header_video_embed'),
					'collapsable' => true,
					'collapsed' => false,
				),
				*/
				'video_embed' => array(
					'type' => 'text',
					'name' => 'video_embed',
					'caption' => _t('_modzzz_investment_caption_video_embed_code'),
					'info' => _t('_modzzz_investment_form_info_video_embed_code'),
					'attrs'     => array('onclick' => 'this.focus();this.select();'),
					'required' => false,
					'html' => 2,      
					'db' => array (
					'pass' => 'XssHtml', 
					),  
				 ),
 
                // videos

              /*  'header_videos' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_investment_form_header_videos'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
 
                'videos_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['choice'],
                    'name' => 'videos_choice[]',
                    'caption' => _t('_modzzz_investment_form_caption_videos_choice'),
                    'info' => _t('_modzzz_investment_form_info_videos_choice'),
                    'required' => false,
                ),
  
                'videos_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['upload'],
                    'name' => 'videos_upload[]',
                    'caption' => _t('_modzzz_investment_form_caption_videos_upload'),
                    'info' => _t('_modzzz_investment_form_info_videos_upload'),
                    'required' => false,
                ),
				*/

                // sounds

              /*  'header_sounds' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_investment_form_header_sounds'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
 
                'sounds_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['choice'],
                    'name' => 'sounds_choice[]',
                    'caption' => _t('_modzzz_investment_form_caption_sounds_choice'),
                    'info' => _t('_modzzz_investment_form_info_sounds_choice'),
                    'required' => false,
                ),
 
                'sounds_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['upload'],
                    'name' => 'sounds_upload[]',
                    'caption' => _t('_modzzz_investment_form_caption_sounds_upload'),
                    'info' => _t('_modzzz_investment_form_info_sounds_upload'),
                    'required' => false,
                ),
				*/
  
              

                // privacy
                
               'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_investment_form_header_privacy'),
                ),
				

                'allow_view_investment_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'investment', 'view_investment'),

                'allow_comment_to' => $aInputPrivacyComment,

                'allow_rate_to' => $aInputPrivacyRate, 

               //'allow_post_in_forum_to' => $aInputPrivacyForum,  
                          
            ),            
        );

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

        $oModuleDb = new BxDolModuleDb();
        if (!$oModuleDb->getModuleByUri('forum'))
            unset ($aCustomForm['inputs']['allow_post_in_forum_to']);
		//[end] added 7.1

		if(!$bPaidInvestment) {
            unset ($aCustomForm['inputs']['package_id']);
            unset ($aCustomForm['inputs']['package_name']);  
		}
 
        $aFormInputsAdminPart = array ();
        if ($GLOBALS['oBxInvestmentModule']->isAdmin()) {

            require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
            $aMemberships = getMemberships ();
            unset ($aMemberships[MEMBERSHIP_ID_NON_MEMBER]); // unset Non-member
            $aMemberships = array('' => _t('_modzzz_investment_membership_filter_none')) + $aMemberships;
            $aFormInputsAdminPart = array (
 
				'membership_view_filter' => array(
					'type' => 'select',
					'name' => 'membership_view_filter',
					'caption' => _t('_modzzz_investment_caption_membership_view_filter'), 
					'info' => _t('_modzzz_investment_info_membership_view_filter'), 
					'values' => $aMemberships,
					'value' => '', 
					'checker' => array (
					'func' => 'preg',
					'params' => array('/^[0-9a-zA-Z]*$/'),
					'error' => _t ('_modzzz_investment_err_membership_view_filter'),
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
   
		if($sInvestmentType=='investor'){
			
			$aCustomForm['inputs']['title']['type']='hidden';
			$aCustomForm['inputs']['title']['value']= $sSellerName;
			
			
			
			$aCustomForm['inputs']['stade']['type']='hidden';
			$aCustomForm['inputs']['stade']['required']=false;
			
			$aCustomForm['inputs']['sellerfax']['type']='hidden';
			$aCustomForm['inputs']['sellerfax']['required']=false;
			
			$aCustomForm['inputs']['address1']['type']='hidden';
			$aCustomForm['inputs']['address1']['required']=false;
			
			$aCustomForm['inputs']['aide']['type']='hidden';
			$aCustomForm['inputs']['aide']['required']=false;
			
			$aCustomForm['inputs']['min_investment']['required']=true;
			$aCustomForm['inputs']['min_investment']['checker'] = array (
						
						'func' => 'avail',
						
						
						'error' => _t ('_modzzz_investment_form_err_aide'),
					); 
			
			
			
			
			
			$aCustomForm['inputs']['diasporardc']['required']=true;
			$aCustomForm['inputs']['diasporardc']['checker'] = array (
						
						'func' => 'avail',
						
						
						'error' => _t ('_modzzz_investment_form_err_diasporardc'),
					); 
					
					
					$aCustomForm['inputs']['terme']['required']=true;
			$aCustomForm['inputs']['terme']['checker'] = array (
						
						'func' => 'avail',
						
						
						'error' => _t ('_modzzz_investment_form_err_terme'),
					); 
					
					
					
					$aCustomForm['inputs']['presentation']['required']=true;
			$aCustomForm['inputs']['presentation']['checker'] = array (
						
						'func' => 'avail',
						
						
						'error' => _t ('_modzzz_investment_form_err_presentation'),
					); 
					
					
						$aCustomForm['inputs']['rencontre']['required']=true;
			$aCustomForm['inputs']['rencontre']['checker'] = array (
						
						'func' => 'avail',
						
						
						'error' => _t ('_modzzz_investment_form_err_rencontre'),
					); 
			
			
		/**
			$aCustomForm['inputs']['max_investment']['required']=true;
			
 
			$aCustomForm['inputs']['max_investment']['checker'] = array (
						
						'func' => 'avail',
						'error' => _t ('_modzzz_investment_form_err_max_investment'),
					);
					*/
					
					 $aCustomForm['inputs']['title']['caption']=_t('_modzzz_investment_form_caption_min_investment_investisseur'); 
					 
					  $aCustomForm['inputs']['financement_recu']['caption']=_t('_modzzz_investment_form_caption_financement_recu_investor');  
		}
		
		
		
		
	////////////////////////////////////////////////////////	

		if($sInvestmentType=='entrepreneur'){
		 $aCustomForm['inputs']['stade']['required']='true';	
		 $aCustomForm['inputs']['stade']['checker'] = array (
						 'func' => 'avail',
						 'error' => _t ('_modzzz_investment_form_err_stade'),
					);  
					
			$aCustomForm['inputs']['sellerfax']['required']='true';	
		 $aCustomForm['inputs']['sellerfax']['checker'] = array (
						 'func' => 'avail',
						 'error' => _t ('_modzzz_investment_form_err_attente'),
					);  
					
					
					$aCustomForm['inputs']['address1']['required']='true';	
		 $aCustomForm['inputs']['address1']['checker'] = array (
						 'func' => 'avail',
						 'error' => _t ('_modzzz_investment_form_err_partenaires'),
					);  
					
					
					$aCustomForm['inputs']['address1']['required']='true';	
		 $aCustomForm['inputs']['address1']['checker'] = array (
						 'func' => 'avail',
						 'error' => _t ('_modzzz_investment_form_err_aide'),
					);  
			
			$aCustomForm['inputs']['min_investment']['type']='hidden';
			$aCustomForm['inputs']['terme']['type']='hidden';
			$aCustomForm['inputs']['rencontre']['type']='hidden';
			$aCustomForm['inputs']['presentation']['type']='hidden';
			$aCustomForm['inputs']['diasporardc']['type']='hidden';
			
		/*	$aCustomForm['inputs']['max_investment']['required']=true;
			
 
			$aCustomForm['inputs']['max_investment']['checker'] = array (
						
						'func' => 'avail',
						'error' => _t ('_modzzz_investment_form_err_max_investment'),
					); 
					*/ 
			
			
			//$aCustomForm['inputs']['min_investment']['required']=true;
			/*$aCustomForm['inputs']['min_investment']['checker'] = array (
						
						'func' => 'avail',
						
						
						'error' => _t ('_modzzz_investment_form_err_min_investment_entrepreneur'),
					); 
					*/ 
			 $aCustomForm['inputs']['min_investment']['caption']=_t('_modzzz_investment_form_caption_min_investment_entrepreneur');
			 $aCustomForm['inputs']['min_investment']['info']=_t('_modzzz_investment_form_info_min_investment_entrepreneur');
			
			  
			  $aCustomForm['inputs']['files_upload']['caption']=_t('_modzzz_investment_form_caption_files_upload_entrepreneur');

			

		}
		
		
		// freddy ajout
		if($sInvestmentType=='professional'){
			$aCustomForm['inputs']['header_investment']['type']='hidden';
			$aCustomForm['inputs']['min_investment']['type']='hidden';
			$aCustomForm['inputs']['max_investment']['type']='hidden';
			$aCustomForm['inputs']['currency']['type']='hidden';
			$aCustomForm['inputs']['stade']['type']='hidden';
         }
		//////////////////////////////////

        $this->processMembershipChecksForMediaUploads ($aCustomForm['inputs']);

		if($bPaidInvestment){
			 $this->processPackageChecksForMediaUploads ($iPackageId, $aCustomForm['inputs']);
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
					unset($aInputs['header_video_embed']);
					unset($aInputs['video_embed']); 
				}   
            }        
        }  
    }

    function processMembershipChecksForMediaUploads (&$aInputs) {

        $isAdmin = $GLOBALS['logged']['admin'] && isProfileActive($this->_iProfileId);

        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'investment photos add', 'investment sounds add', 'investment videos add', 'investment files add'));

		$aCheck = checkAction($_COOKIE['memberID'], BX_PHOTOS_ADD);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED && !$isAdmin) {
            unset($aInputs['thumb']);
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

				if(($k=='videos') && (getParam("modzzz_investment_allow_embed")!="on")){
					unset($aInputs['header_video_embed']);
					unset($aInputs['video_embed']); 
				} 
            }        
        }

        $a = array ('images' => 'PHOTOS', 'videos' => 'VIDEOS', 'sounds' => 'SOUNDS', 'files' => 'FILES');
        foreach ($a as $k => $v) {
			if (defined("BX_INVESTMENT_{$v}_ADD"))
				$aCheck = checkAction($_COOKIE['memberID'], constant("BX_INVESTMENT_{$v}_ADD"));
            if ((!defined("BX_INVESTMENT_{$v}_ADD") || $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) && !$isAdmin) {
                unset($this->_aMedia[$k]);
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']);

				if(($k=='videos') && (getParam("modzzz_investment_allow_embed")!="on")){
					unset($aInputs['header_video_embed']);
					unset($aInputs['video_embed']); 
				} 

            }        
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




}
