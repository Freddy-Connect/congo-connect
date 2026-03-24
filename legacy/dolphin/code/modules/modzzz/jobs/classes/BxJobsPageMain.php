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

bx_import('BxDolTwigPageMain');

class BxJobsPageMain extends BxDolTwigPageMain {
	var $oMain;

   
   
   
       // Freddy ajout function ajaxBrowse; Cette function a été copiée de /inc/classes/BxDolTwigPageMain.php
	  //Le but c'est de ne pas afficher les block dans Job Home quand c'est vide
	 function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true)
    {
      // Freddy ajout $oMain = BxDolModule::getInstance('BxJobsModule');
	  $oMain = BxDolModule::getInstance('BxJobsModule');
	    bx_import ('SearchResult', $this->oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue);
        $o->aCurrent['paginate']['perPage'] = $iPerPage;
        $o->setPublicUnitsOnly($isPublicOnly);

        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('_RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => 'rss')));

        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);

        if (!($s = $o->displayResultBlock()))
           //Freddy mise en commentaire
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
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->oConfig->getBaseUri() . $o->sBrowseUrl);

        return array(
            $s,
            $aMenu,
            $sAjaxPaginate,
            '');
    }
	
	
	/////////////////Freddy ajout pour afficher le le nombre des stages et emploi qu'un membre a enregistrées ainsi que des offres auquelles il a postulées
	 function getBlockCode_EspaceEmploi() {
		 $aData = $this->aDataEntry;
		 $aProfileInfo = getProfileInfo($iProfileId);
		 
		 $aAuthor = getProfileInfo($aData['author_id']);
		$sAuthorLink = getProfileLink($aProfileInfo['ID']);	
		//$icoThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aProfileInfo['ID'], 'none');
		//$icoThumb = $GLOBALS['oFunctions']->getMemberIcon($aProfileInfo['ID'], 'left');
		$icoThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aProfileInfo['ID'], 'none');

		
		
		 $oListing = BxDolModule::getInstance('BxListingModule');
         $aCompanyData = $this->oDb->MembreBusinessLsting ($aProfileInfo['ID']);
		  $aCompanyName= $aCompanyData['title'];
		  $sCompanyUrl = BX_DOL_URL_ROOT . $oListing->_oConfig->getBaseUri() . 'view/' . $aCompanyData['uri']; 
		  
		    $aCountCompany = $this->oDb->CountMembreBusinessLsting ($aProfileInfo['ID']);
		  
		  
		
			
		$aFavoriteJobs = $this->oDb->CountFavoriteMember($aProfileInfo['ID']);
		$aJobapply = $this->oDb->CountJobApplyMember($aProfileInfo['ID']);
	    $aEmploiCount = $this->oDb->getModzzzCountJobsEmploi($aProfileInfo['ID']);
	    $aApplicationCount = $this->oDb->getRecruteurApplicationCount ($aProfileInfo['ID']);
		
		
	    if($aEmploiCount>= '1'){
			 
			$aEmploiCaption = _t('_modzzz_jobs_offres_emploi_home_plural') ; 
			
		}
		else{
			//$aEmploiCaption = _t('_modzzz_jobs_offres_emploi_home_single') ; 
			$aEmploiCaption = _t('_modzzz_jobs_offres_emploi_home_plural') ; 
			
			
		}
		
		
			
	  if($aApplicationCount >= '1'){
		  $aApplicationCaption = _t('_modzzz_jobs_offres_emploi_application_count_plural') ;  
			
		}
		else{
			$aApplicationCaption = _t('_modzzz_jobs_offres_emploi_application_count_single') ; 
			//$aApplicationCaption = _t('_modzzz_jobs_offres_emploi_application_count_plural') ;  
			
		}
		
		
		if($aFavoriteJobs<= '1'){
			//$aFavoriteCaption = _t('_modzzz_jobs_offres_enregistrees_single') ; 
			$aFavoriteCaption = _t('_modzzz_jobs_offres_enregistrees') ; 
		}
		else{
			$aFavoriteCaption = _t('_modzzz_jobs_offres_enregistrees') ;  
		}
		
		if($aJobapply<= '1'){
			$aApplyCaption = _t('_modzzz_jobs_mes_candidatures_single') ; 
		}
		else{
			$aApplyCaption = _t('_modzzz_jobs_mes_candidatures_pluriel') ;  
		}
		
		
		
		  $aVars = array (  
	 
	 'ProfilLink'=> $sAuthorLink,
	 'FirstName' => $aProfileInfo['FirstName'],
	 'LastName' => $aProfileInfo['LastName'],
     
	// 'FavoriteCaption'=> $aFavoriteCaption,
	
	
	 
	 'thumbnail'=> $icoThumb,
	
	
	  'FirstName' => $aProfileInfo['FirstName'],
	  
	  'CompanyName'=> $aCompanyName,
	    'CompanyUrl'=> $sCompanyUrl,
	 
	 
		
		
		  'bx_if:JobsCountPositive' => array( 
								'condition' =>  $aEmploiCount >= 1 ,
								'content' => array(
       
		        'EmploiCount' => $aEmploiCount,
	           'EmploiCaption' => $aEmploiCaption,
								), 
							),
		
		
		     'bx_if:JobsCountZero' => array( 
								'condition' =>  $aEmploiCount < 1 ,
								'content' => array(
       
		        'EmploiCount' => $aEmploiCount,
	           'EmploiCaption' => $aEmploiCaption,
			    'page_url' => 'javascript:void(0);',
								), 
							),
		
	  
	  
	  
	   'bx_if:JobsCountJobspace' => array( 
								'condition' =>  $aEmploiCount >= 1 ,
								'content' => array(
       
		'go_to_your_space_caption' => _t('_modzzz_jobs_acceder_votre_espace_job') ,
								), 
							),
							
		  'bx_if:JobsCandidats' => array( 
								'condition' =>  $aApplicationCount >= 1 ,
								'content' => array(
       
		'ApplicationCount' => $aApplicationCount , 
	    'ApplicationCaption'=> $aApplicationCaption,
								), 
							),
		 'bx_if:MesCandidatures' => array( 
								'condition' =>  $aJobapply >= 1 ,
								'content' => array(
       
		    'ApplyCaption'=> $aApplyCaption,
	        'jobapply' => $aJobapply,
								), 
							),
							
							
		 'bx_if:MesCandidaturesZero' => array( 
								'condition' =>  $aJobapply <1 ,
								'content' => array(
       
		    'ApplyCaption'=> $aApplyCaption,
	        'jobapply' => $aJobapply,
			'page_url' => 'javascript:void(0);',
								), 
							),
							
		 'bx_if:FavoriteJobs' => array( 
								'condition' =>  $aFavoriteJobs >= 1 ,
								'content' => array(
                'FavoriteJobs' => $aFavoriteJobs,
	            'FavoriteCaption'=> $aFavoriteCaption,
								), 
							),
			
			
			 'bx_if:FavoriteZero' => array( 
								'condition' =>  $aFavoriteJobs < 1 ,
								'content' => array(
                'FavoriteJobs' => $aFavoriteJobs,
	            'FavoriteCaption'=> $aFavoriteCaption,
				'page_url' => 'javascript:void(0);',
								), 
							),
			
							
			
							
							
							
	 'bx_if:jobs_titre_home_page' => array( 
								'condition' =>  $aCountCompany == 0 ,
								'content' => array(
       'jobs_titre_home_page' => _t('_modzzz_jobs_titre_home_page') ,
								), 
							),
		
	  
	  
	  
     );
	  return $this->oTemplate->parseHtmlByName('espace_emploi_home', $aVars);  
    }
		
	 
	
	
	
	/////////////////Freddy ajout pour afficher le le nombre des stages et emploi pour une entreprise ainsique le lien vers la liste des offres proposées
	 function getBlockCode_Recruteurs() {
		 
		  if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxJobsModule');
		 
		 $aData = $this->aDataEntry;
		 $aProfileInfo = getProfileInfo($iProfileId);
	 
		  $sJobEntries = '';
		$aEntries = array();
		
			$oBiz = BxDolModule::getInstance('BxListingModule');
			$aJobs = $this->oDb->getRecruteur();
				
			
		//////////

			$iIter = 1;
			$iCountJobs = count($aJobs);
			foreach($aJobs as $aEachJob) { 
			
			 ////freddy jobs , classified, event count
		    $iId = $aEachJob['id'];
		
		    if(getParam('modzzz_listing_jobs_active')=='on'){
			$iJobsCount = $this->oDb->getModzzzxJobsCount($iId);
		    }
			
			
			
		    $aCategory = $this->oDb->getCompanyCategoryById($aEachJob['parent_category_id']);
			$sCategoryUrl = BX_DOL_URL_ROOT . $oBiz->_oConfig->getBaseUri() . 'categories/'.$aCategory['uri'];
		  
		   if($aEachJob['category_id']){
			$aSubCategory = $this->oDb->getCompanyCategoryById($aEachJob['category_id']); 
			 $sSubCategoryUrl = BX_DOL_URL_ROOT . $oBiz->_oConfig->getBaseUri() . 'subcategories/'.$aSubCategory['uri'];
      		} 
			
			  $sImage = '';
        /*
		   if ($aEachJob['thumb']) {
            $a = array ('ID' => $aEachJob['author_id'], 'Avatar' => $aEachJob['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
            } 
			*/
		
		
		if($aEachJob['icon']){ 
		    $sImage = $this->oDb->getLogoBusinessListing($aEachJob['id'], $aEachJob['icon'], true);
			
	   }
	    else if ($aEachJob['thumb']){
			 $a = array ('ID' => $aEachJob['author_id'], 'Avatar' => $aEachJob['thumb']);
			  $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
			 $sImage = $aImage['file']  ;
		  }
		  else{
			$sImage = $this->oTemplate->getImageUrl('no-image-thumb.png');
		} 
		   
		
		   /*
		    ////freddy classified integration
		     if(getParam('modzzz_listing_classified_active')=='on'){
			$iClassifiedCount = $this->oDb->getModzzzxClassifiedCount($iId);
		      }
		    ///////////////////////freddy events integration
		
		    if(getParam('modzzz_listing_boonex_events')=='on'){
			$iEventsCount = $this->oDb->getBoonexEventsCount($iId);
		    }else{
			$iEventsCount = (int)$this->aDataEntry['events_count'];
		    }
			*/
            ////////////////////////////////////////////////////////////////

			
            
		
				$aEntries[] = array(  
					'thumb_url' => $sImage ,
					


					'recruteur_url' => BX_DOL_URL_ROOT . $oBiz->_oConfig->getBaseUri() . 'view/' . $aEachJob['uri'],
					'recruteur_title' => $aEachJob['title'], 
					'city' => $aEachJob['city'],
					'zip' => $aEachJob['zip'],
					'employees_count' => _t($GLOBALS['aPreValues']['ListingNombreEmployes'][$aEachJob['employees_count']]['LKey']),
					'category_name' => $aCategory['name'],
			        'category_url' => $sCategoryUrl,
				    
					
					'bx_if:sub_category' => array (
                    'condition' => $aEachJob['category_id'],
                    'content' => array ( 
					'subcategory_name' => $aSubCategory['name'],
					'subcategory_url' => $sSubCategoryUrl,
                     ),
                    ),
					
					
					 //freddy ajout --- Afficher le nombre total des offres de formation, articles, events + lien
			'bx_if:jobs_count' => array( 
								'condition' => $iJobsCount,
								'content' => array(
			  'jobs_count_url'=>  BX_DOL_URL_ROOT . $oBiz->_oConfig->getBaseUri() . 'review/browse/' . $aEachJob['uri'],
              'jobs_count' =>  $iJobsCount,  
								), 
							), 
							
			'bx_if:events_count' => array( 
								'condition' => $iEventsCount,
								'content' => array(
			 // 'events_count_url' => BX_DOL_URL_ROOT . $oBiz->_oConfig->getBaseUri() . 'event/browse/' . $aEachJob['uri'],
             // 'events_count' =>  $iEventsCount,  
								), 
							), 
			
			/*
			'bx_if:classified_count' => array( 
								'condition' => $iClassifiedCount,
								'content' => array(
			  'classified_count_url'=>  BX_DOL_URL_ROOT . $oBiz->_oConfig->getBaseUri() . 'news/browse/' . $aEachJob['uri'],
              'classified_count' =>  $iClassifiedCount,  
								), 
							), 
			*/
							
				 ////////////////////[END] FREDDY FIN INTEGRATION///////////////////////////////////////////////
								
					
					
					'spacer' => ($iCountJobs==$iIter) ? '' : '<br>'
					
				);
				$iIter++;
			}

			if($iCountJobs){
				$aVars = array('bx_repeat:entries' => $aEntries); 
				$sJobEntries = $this->oTemplate->parseHtmlByName('recruteur_entries', $aVars); 
			}
		
		 
     $aVars = array (  
	 
	
     'jobs' => $sJobEntries,
	 
     );
	  return $this->oTemplate->parseHtmlByName('entry_view_block_recruteurs', $aVars);  
	  
    }
	//////////////////////////////////////////////////////////////////
	
		/////////////////Freddy ajout
	 function getBlockCode_HomeJobs() {
		 $aData = $this->aDataEntry;
		 $aProfileInfo = getProfileInfo($iProfileId);
		 
		
		 $oListing = BxDolModule::getInstance('BxListingModule');
         $aCompanyData = $this->oDb->MembreBusinessLsting ($aProfileInfo['ID']);
		  $aCompanyName= $aCompanyData['title'];
		  $sCompanyUrl = BX_DOL_URL_ROOT . $oListing->_oConfig->getBaseUri() . 'view/' . $aCompanyData['uri']; 
		   
		   
		  $aApplicationCount = $this->oDb->getRecruteurApplicationCount ($aProfileInfo['ID']);
		 
		 
			
		  $aEmploiCount = $this->oDb->getModzzzCountJobsEmploi($aProfileInfo['ID']);
		// Freddy pour afficher le nombre des offres d'emplo au singulier ou au pluriel
		
		if($aApplicationCount<= '1'){
			$aApplicationCaption = _t('_modzzz_jobs_offres_emploi_application_count_single') ; 
		}
		else{
			$aApplicationCaption = _t('_modzzz_jobs_offres_emploi_application_count_plural') ;  
		}
		
		
		
       
		if($aEmploiCount<= '1'){
			$aEmploiCaption = _t('_modzzz_jobs_offres_emploi_home_single') ; 
		}
		else{
			$aEmploiCaption = _t('_modzzz_jobs_offres_emploi_home_plural') ;  
		}
		/////
		  
	$aStageCount = $this->oDb->getModzzzCountJobsStage($aProfileInfo['ID']);
	// Freddy pour afficher le nombre des stages au singulier ou au pluriel
       
		if($aStageCount<= '1'){
			$aStageCaption = _t('_modzzz_jobs_offres_stage_home_single') ; 
		}
		else{
			$aStageCaption = _t('_modzzz_jobs_offres_stage_home_plural') ;  
		}
		/////
		
	// Freddy pour afficher le lien vers la gestion des jobs si getModzzzxJobsCount est supérieur à 1
	  $aGoToYourSpace = $this->oDb->getGoToYourSpace($aProfileInfo['ID']);

     $aVars = array (  
	 
	 'EmploiCount' => $aEmploiCount,
	 'StageCount' => $aStageCount,
	 'FirstName' => $aProfileInfo['FirstName'],
	 'LastName' => $aProfileInfo['LastName'],
	 'jobs_caption'=> $aEmploiCaption,
	 'stages_caption'=> $aStageCaption,
	 'ApplicationCount' => $aApplicationCount , 
	 'ApplicationCaption'=> $aApplicationCaption,
	 'CompanyName'=> $aCompanyName,
	 'CompanyUrl'=> $sCompanyUrl,
	
	 
	 'bx_if:go_to_your_space_caption' => array( 
								'condition' => $aGoToYourSpace >= 1 ,
								'content' => array(
        'go_to_your_space_caption' => _t('_modzzz_jobs_acceder_votre_espace_job') ,
								), 
							),
			
     );
	 
	  return $this->oTemplate->parseHtmlByName('entry_jobs_home_gerer_emplois', $aVars);  
    }
	
	
	
   
    function BxJobsPageMain(&$oMain) {
		$this->oMain = $oMain;
        $this->sSearchResultClassName = 'BxJobsSearchResult';
        $this->sFilterName = 'filter';
		parent::BxDolTwigPageMain('modzzz_jobs_main', $oMain);
	}
   
    function getBlockCode_DailyJob() { 
        return $this->ajaxBrowse('today', 1); 
    }

    function getBlockCode_FeaturedJob() { 
        return $this->ajaxBrowse('featured', $this->oDb->getParam('modzzz_jobs_perpage_main_featured'));
    }  

    function getBlockCode_FeaturedWantedJob() { 

		if(!$this->oMain->isAllowedSeeker()) return;
			 
        return $this->ajaxBrowse('featured_wanted', $this->oDb->getParam('modzzz_jobs_perpage_main_featured'));
    }  
 
    function getBlockCode_FeaturedCompany() { 
        return $this->ajaxBrowse('company_featured', $this->oDb->getParam('modzzz_jobs_perpage_main_featured'));
    }  

    function getBlockCode_Recent() { 
        return $this->ajaxBrowse('recent', $this->oDb->getParam('modzzz_jobs_perpage_main_recent'));
    }  
	
    function getBlockCode_Seeking() { 

		if(!$this->oMain->isAllowedSeeker()) return;
			 
        return $this->ajaxBrowse('seeking', $this->oDb->getParam('modzzz_jobs_perpage_main_recent'));
    }  
	
    function getBlockCode_Categories() {
  
	    $aAllEntries = $this->oDb->getCategoryInfo(0, true);
     
        $aResult['bx_repeat:entries'] = array();        
 		foreach($aAllEntries as $aEntry)
		{	 
			$iNumCategory = $this->oDb->getParentCategoryCount($aEntry['id']);	 
			$sCatHref = $this->oDb->getCategoryUrl($aEntry['uri']);  
			$sCategory = $aEntry['name'];
 
	        $aResult['bx_repeat:entries'][] = array(
                /* Freddy mise en commentaire 
			    'cat_url' => $sCatHref, 
                'cat_name' => $sCategory,
			    'num_items' => $iNumCategory ,
				*/ 
				
				// Freddy ajout/ Ne pas afficher les categories dont le nombre total est zero
				 'bx_if:categorie' => array( 
								'condition' => $iNumCategory >=1,
								'content' => array(
                 'cat_url' => $sCatHref, 
                 'cat_name' => $sCategory,
			     'num_items' => $iNumCategory , 
								), 
							),
            );	        
	    } 
 
	    return $this->oTemplate->parseHtmlByName('jobs_categories', $aResult);  
	}
 
 
    function getBlockCode_States() {

		$iProfileId = getLoggedId(); 
		$aProfile = $iProfileId ? getProfileInfo($iProfileId) : array();

 		$sCountry = ($aProfile['Country']) ? $aProfile['Country'] : getParam('modzzz_jobs_default_country');
 
		$sCountryName = _t($GLOBALS['aPreValues']['Country'][$sCountry]['LKey']);

		$aStates = $this->oDb->getAll("SELECT `State`,`StateCode` FROM `States` WHERE CountryCode='{$sCountry}' ORDER BY `State`");
		 
		$aVars['bx_repeat:entries'] = array();        
  		foreach($aStates as $aEachState){
			 
			$sState = $aEachState['State'];
			$sStateCode = $aEachState['StateCode'];

			$sStateUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'local/' . $sCountry .'/'. $sStateCode;
  		
			$iNumCategory = $this->oDb->getStateCount($sStateCode);	 
			 
			$aVars['country_name'] = $sCountryName;

			$aVars['bx_repeat:entries'][] = array(
		 
				'bx_if:selstate' => array( 
					//// Freddy ajout/ Ne pas afficher les regions/states dont le nombre total est zero
					//'condition' => ($sStateCode == $this->sState),
					'condition' => ($sStateCode == $this->sState) && $iNumCategory >=1 ,
					'content' => array( 
						'state_url' => $sStateUrl, 
						'state_name' => $sState,
						'num_items' => $iNumCategory,  
					), 
				), 
				'bx_if:regstate' => array( 
					//// Freddy ajout/ Ne pas afficher les regions/states dont le nombre total est zero
					//'condition' => ($sStateCode != $this->sState),
					'condition' => ($sStateCode != $this->sState) && $iNumCategory >=1,
					'content' => array( 
						'state_url' => $sStateUrl, 
						'state_name' => $sState,
						'num_items' => $iNumCategory,   
					), 
				), 

			 ); 
	    } 
 
	    $aStates = array($this->oTemplate->parseHtmlByName('block_states_main', $aVars)); 
		$aStates[3] = _t('_modzzz_jobs_browse_jobs_in_country', $sCountryName);
		
		return $aStates;  
	}
 
	function getBlockCode_Search() {
   
        $this->oTemplate->pageStart();
 
        bx_import('BxDolCategories');
        bx_import('BxDolProfileFields'); 
  
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
		$aCountries[''] = "";  
		asort($aCountries);
		
		// Freddy masquer pays et afficher pays par defaut 
		$aProfile = getProfileInfo((int)$iProfileId); 
		// $sDefaultCountry = ($aProfile['Country']) ? $aProfile['Country'] : getParam('modzzz_jobs_default_country');
		//$sDefaultCountry = $aProfile['Country'];
		//$sSelCity= $aProfile['City'];
         ///////////////////
		 $sDefaultCountry = getParam('modzzz_listing_default_country');
		// $aStates = $this->oDb->getStateArray($sDefaultCountry); 
		$aStates = $this->oDb->getStateArray($sDefaultCountry);
		 
   		//freddy mise en commentaire   
		//$aStates = array();  
	 
   		 
 
		$sCatUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home/'.(getParam('modzzz_jobs_permalinks') ? '?' : '&').'ajax=cat&parent=' ;
 
		$sStateUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home/'.(getParam('modzzz_jobs_permalinks') ? '?' : '&').'ajax=state&country=' ; 
 
		$aCategories = $this->oDb->getFormCategoryArray();
 
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
               
				/*                
                'parent' => array(
                    'type' => 'select',
                    'name' => 'parent',
					'values'=> $aCategories,
                    'caption' => _t('_modzzz_jobs_form_caption_parent_category'),
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
                    'caption' => _t('_modzzz_jobs_form_caption_sub_category'),
					'attrs' => array(
                        'id' => 'subcat',
					),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ),
                */
				'Country' => array(
					'type' => 'select',
                    'name' => 'Country',
                    'caption' => _t('_modzzz_jobs_form_caption_country'),
                    'values' => $aCountries,
					'value' => getParam('modzzz_listing_default_country'),
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					), 
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
               /* 'City' => array(
                    'type' => 'text',
                    'name' => 'City',
                    'caption' => _t('_modzzz_jobs_form_caption_city'),
                    'required' => false,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
				*/  
				
				 'Keyword' => array(
                    'type' => 'text',
                    'name' => 'Keyword',
                    'caption' => _t('_modzzz_jobs_form_caption_keyword'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ), 
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_modzzz_jobs_continue_search'),
                    'colspan' => false,
                ),
            ),            
        );
 
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker(); 
  
        if ($oForm->isSubmittedAndValid ()) {
 
            modzzz_jobs_import ('SearchResult');
            $o = new BxJobsSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country') );
  
            if ($o->isError) {
                $this->oTemplate->displayPageNotFound ();
                exit;
            }

            if ($s = $o->processing()) {
                echo $s;
            } else {
                $this->oTemplate->displayNoData ();
                exit;
            }

            $this->oMain->isAllowedSearch(true); // perform search action 

            $this->oTemplate->addCss ('unit.css');
            $this->oTemplate->addCss ('main.css');
            $this->oTemplate->pageCode($o->aCurrent['title'], false, false);
			exit; 
        } 
 
        return array($oForm->getCode()); 
    } 
   
	function getBlockCode_Comments() 
	{   
		$iNumComments = getParam("modzzz_jobs_perpage_main_comment");
		$aAllEntries = $this->oDb->getLatestComments($iNumComments);

		if(!count($aAllEntries))
			return; 
   
		$aVars = array (
			'bx_repeat:comments' => array (),
		);

		foreach($aAllEntries as $aEntry) { 
		  
			$iMemberId = $aEntry['cmt_author_id'];
			$sNickName = getNickName($iMemberId);
			$sNickLink = getProfileLink($iMemberId);
			$sMessage = $aEntry['cmt_text']; 
			$dtSent = defineTimeInterval($aEntry['date']);
			$iListingId = $aEntry['cmt_object_id']; 
		/*
			$sImage = '';
			if ($aEntry['thumb']) {
				$a = array ('ID' => $aEntry['author_id'], 'Avatar' => $aEntry['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}
		*/
 			$sImage = ($iMemberId) ? $GLOBALS['oFunctions']->getMemberThumbnail($iMemberId) : '';

 			$iLimitChars = (int)getParam('modzzz_jobs_comments_max_preview');

			$sMessage = $this->oMain->_formatSnippetText($aEntry, $iLimitChars, $sMessage);
 
			$aListing = $this->oDb->getEntryById($iListingId);
			$sListingUri = $aListing['uri'];
			$sListingTitle = $aListing['title'];
 
			$sListingUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sListingUri;
   
			$aVars['bx_repeat:comments'][] = array (
				'thumb_url' => $sImage ? $sImage : $this->oTemplate->getImageUrl('no-icon-thumb.png'),
				'author_url' => $sNickLink,
				'author' => $sNickName,
				'created' => $dtSent,
				'snippet_text' => $sMessage,
				'item_url' => $sListingUrl,
				'item_title' => $sListingTitle,
 			);  
		}
 
		return $this->oTemplate->parseHtmlByName('block_comments', $aVars); 
	}
 
	function getBlockCode_Forum() {
  		 
		$iNumComments = getParam("modzzz_jobs_perpage_main_comment");
		$aPosts = $this->oDb->getLatestForumPosts($iNumComments);
  
		if(empty($aPosts))
			return;

		$aVars['bx_repeat:entries'] = array();
  		foreach($aPosts as $aEachPost){

			$sForumUri = $aEachPost['forum_uri'];
			$sTopic = $aEachPost['topic_title']; 
			$sTopicUri = $aEachPost['topic_uri'];
			$sPostText = $aEachPost['post_text']; 
			$sDate = defineTimeInterval($aEachPost['when']); 
			$sListingName = $aEachPost['title']; 
 			$sPoster = $aEachPost['user']; 

			$iLimitChars = (int)getParam('modzzz_jobs_forum_max_preview');
			$sPostText = $this->oMain->_formatSnippetText($aEachPost, $iLimitChars, $sPostText);
 
 /*
			$sImage = '';
			if ($aEachPost['thumb']) {
				$a = array ('ID' => $aEachPost['author_id'], 'Avatar' => $aEachPost['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}
		*/
 			$sImage = getID($sPoster) ? $GLOBALS['oFunctions']->getMemberThumbnail(getID($sPoster)) : '';

			$sListingUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sForumUri;
			$sTopicUrl = BX_DOL_URL_ROOT . 'forum/jobs/forum/'.$sForumUri.'-0.htm#topic/'.$sTopicUri.'.htm';
	
			$aVars['bx_repeat:entries'][] = array( 
							'topic_url' => $sTopicUrl, 
							'topic' => $sTopic, 
							'snippet_text' => $sPostText, 
							'item_title' => $sListingName, 
							'item_url' => $sListingUrl, 
							'created' => $sDate,
							'author_url' => getProfileLink(getID($sPoster)),
							'author' => $sPoster,
							'thumb_url' => $sImage ? $sImage : $this->oTemplate->getImageUrl('no-icon-thumb.png'),
						);
		}

		$sCode = $this->oTemplate->parseHtmlByName('block_forum', $aVars);  

		return $sCode;
	}
 
	function getBlockCode_Create() {
   		
		$sAskUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/my&filter=add_jobs'; 
    
		$aVars = array( 
			'create_url' => $sAskUrl, 
  		);
 
		$sCode = $this->oTemplate->parseHtmlByName('create_jobs', $aVars);  

		return $sCode;
	}
 
    function getBlockCode_Tags($iBlockId) { 
        bx_import('BxTemplTagsModule');
        $aParam = array(
            'type' => 'modzzz_jobs',
            'orderby' => 'popular',
			'pagination' => getParam('tags_perpage_browse')
        );

		$sUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home';
  
        $oTags = new BxTemplTags();
        $oTags->getTagObjectConfig();
    
        return array(
            $oTags->display($aParam, $iBlockId, '', $sUrl),
            array(),
            array(),
            _t('_Tags')
        ); 

    } 




}
