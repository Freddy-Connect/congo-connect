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

bx_import('BxDolTwigPageMain');
bx_import('BxTemplCategories');

class BxListingPageMain extends BxDolTwigPageMain {

    function BxListingPageMain(&$oMain) {

		$this->oDb = $oMain->_oDb;
        $this->oConfig = $oMain->_oConfig;
		$this->oMain = $oMain;

        $this->sSearchResultClassName = 'BxListingSearchResult';
        $this->sFilterName = 'filter';
		parent::BxDolTwigPageMain('modzzz_listing_main', $oMain);
	}
	
	
	
	
	
	/////////////////Freddy ajout pour afficher un bloc à la page d'accueil permettant d inscrire son entreprise 
	 function getBlockCode_InscriptionEntreprise() {
		 $aData = $this->aDataEntry;
		 $aProfileInfo = getProfileInfo($iProfileId);
		 
		 $aAuthor = getProfileInfo($aData['author_id']);
		$sAuthorLink = getProfileLink($aProfileInfo['ID']);	
		$icoThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aProfileInfo['ID'], 'none');
		//$icoThumb = $GLOBALS['oFunctions']->getMemberIcon($aProfileInfo['ID'], 'left');
		
			
		 $aFactureInvoiceCount = $this->oDb->getFactureInvoiceCount($aProfileInfo['ID']);
		  if($aFactureInvoiceCount<= '1'){
			$aFactureInvoice = _t('_modzzz_listing_invoice_commande_single') ; 
		}
		else{
			$aFactureInvoice = _t('_modzzz_listing_invoice_commande_plural') ;  
		}
		 
		
		 $aBusinessCount = $this->oDb->getModzzzCountBusinessEntreprise($aProfileInfo['ID']);
		 // Freddy pour afficher au singulier ou au pluriel le nombre d'entreprise inscrite par chaque membre 
		
	   if($aBusinessCount<= '1'){
			$aBusinessCaption = _t('_modzzz_listing_business_home_single') ;
			$aBusiness_caption_survol = _t('_modzzz_listing_business_survol_single') ; 
		}
		else{
			$aBusinessCaption = _t('_modzzz_listing_business_home_plural') ;  
			$aBusiness_caption_survol = _t('_modzzz_listing_business_survol_plural') ; 
		}
		
		 if($aBusinessCount == '0'){
			$aBusinessLink = BX_DOL_URL_ROOT .  'm/listing/home' ; // Si aucun business inscrit, rester sur la page d'accueil de business listing
		}
		else{
			$aBusinessLink = BX_DOL_URL_ROOT . 'm/listing/browse/my' ; // Sinon rediriger sur m/listing/browse/my
		}
		
		
		$aFavoriteEntreprises = $this->oDb->CountFavoriteMemberEntreprises($aProfileInfo['ID']);
		if($aFavoriteEntreprises > 1){
			$aFavoriteCaption = _t('_modzzz_listing_entreprises_favorites_plural') ; 
			 $aFavoriteCaption_survol = _t('_modzzz_listing_entreprises_favorites_survol_plural') ; 
		}
		else{
			$aFavoriteCaption = _t('_modzzz_listing_entreprise_favorite_single') ;  
			 $aFavoriteCaption_survol = _t('_modzzz_listing_entreprises_favorites_survol_single') ; 
		}
		
		  $aVars = array (  
       'thumbnail'=> $icoThumb,
	   'ProfilLink'=> $sAuthorLink,
	   'FirstName' => $aProfileInfo['FirstName'],
	    'LastName' => $aProfileInfo['LastName'],
		
		  'BusinessCount' => $aBusinessCount,
	    'Business_caption'=> $aBusinessCaption,
		'Business_Link'=> $aBusinessLink,
		
		
		
		
		
		
		  
	    'bx_if:membre_sans_page_entreprise' => array( 
								'condition' => $aBusinessCount < 1 ,
								'content' => array(
       
		
								), 
							),
							
							
							
			'bx_if:FactureInvoice' => array( 
								'condition' => $aFactureInvoiceCount  >= 1 ,
								'content' => array(
								'FactureCaption' => $aFactureInvoice,
	                            'FactureInvoiceCount' => $aFactureInvoiceCount,
       
		
								), 
							),				
			
							
			'bx_if:Entreprises_Nombre' => array( 
								'condition' => $aBusinessCount  >= 1 ,
								'content' => array(
								'BusinessCount' => $aBusinessCount,
	                            'Business_caption'=> $aBusinessCaption,
		                         'Business_Link'=> $aBusinessLink,
								 'Business_caption_survol'=> $aBusiness_caption_survol,
       
		
								), 
							),
							
							
			 'bx_if:Entreprises_Favorites' => array( 
								'condition' => $aFavoriteEntreprises  >= 1 ,
								'content' => array(
								'FavoriteCaption'=> $aFavoriteCaption,
		                         'FavoriteEntreprises' => $aFavoriteEntreprises,
								 'FavoriteCaption_survol'=> $aFavoriteCaption_survol,
       
		
								), 
							),
		
		
	   
	   
	   
	   
	    'bx_if:go_to_your_space_caption' => array( 
								'condition' => $aBusinessCount >= 1 ,
								'content' => array(
        'go_to_your_space_caption' => _t('_modzzz_listing_acceder_votre_espace') ,
		
		
								), 
							),
							
							
	
							
	  
     );
	  return $this->oTemplate->parseHtmlByName('block_inscription_entreprise', $aVars);  
	  
    }
		
	
  
    function getBlockCode_ListingCategories() {
  
  		$iLanguageId = getLangIdByName(getCurrentLangName());

	   /* Freddy commentaire pour désactiver les category par langue
	    $aAllEntries = $this->oDb->getParentCategories($iLanguageId);
		*/
		$aAllEntries = $this->oDb->getParentCategories();
		/////////// freddy fin moduf  ///////////////////
		
     
        $aResult['bx_repeat:entries'] = array();        
 		foreach($aAllEntries as $aEntry)
		{ 
		
	        $aResult['bx_repeat:entries'][] = array(
             /* 
			   'cat_url' => $this->oDb->getCategoryUrl($aEntry['uri']), 
                'cat_name' => $aEntry['name'],
			    'num_items' => $aEntry['cnt'], 
				*/
				
			// Freddy ajout/ Ne pas afficher les categories dont le nombre total est zero
				 'bx_if:categorie' => array( 
								'condition' => $aEntry['cnt'] >=1,
								'content' => array(
                 'cat_url' => $this->oDb->getCategoryUrl($aEntry['uri']), 
                'cat_name' => $aEntry['name'],
			    'num_items' =>  $aEntry['cnt'],
								), 
							),
            );	        
	    } 
 
	    return $this->oTemplate->parseHtmlByName('listing_categories', $aResult);  
	}
  
    function getBlockCode_States() {
		$iProfileId = getLoggedId();

		$aProfile = ($iProfileId) ? getProfileInfo($iProfileId) : array(); 
		$sCountry = ($aProfile['Country']) ? $aProfile['Country'] : getParam('modzzz_listing_default_country');
  
		$sCountryName = _t($GLOBALS['aPreValues']['Country'][$sCountry]['LKey']);

		$aStates = $this->oDb->getAll("SELECT `State`,`StateCode` FROM `States` WHERE CountryCode='{$sCountry}' ORDER BY `State`");
		  
		if(!count($aStates))
			return;
 
		$aVars = array();
		$aVars['country_name'] = $sCountryName;
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
					'condition' => ($sStateCode != $this->sState)  && $iNumCategory >=1,
					'content' => array( 
						'state_url' => $sStateUrl, 
						'state_name' => $sState,
						'num_items' => $iNumCategory,   
					), 
				), 

			 ); 
	    } 
 
 	    $aBlock = array($this->oTemplate->parseHtmlByName('block_states_main', $aVars)); 
		$aBlock[3] = _t('_modzzz_listing_regions_in_country', $sCountryName);
		
		return $aBlock;  
	}
	 // Freddy ajout function ajaxBrowse; Cette function a été copiée de /inc/classes/BxDolTwigPageMain.php
	  //Le but c'est de ne pas afficher les block dans Business Listing Home quand c'est vide
	 function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true)
    {
      // Freddy ajout $oMain = BxDolModule::getInstance('BxListingModule');
	  $oMain = BxDolModule::getInstance('BxListingModule');
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
	
	
 
    function getBlockCode_LatestFeaturedListing() { 
	    return $this->ajaxBrowse('featured', $this->oDb->getParam('modzzz_listing_perpage_main_featured')); 
    }
 
    function getBlockCode_Recent() { 
        return $this->ajaxBrowse('recent', $this->oDb->getParam('modzzz_listing_perpage_main_recent'));
    }

	function getBlockCode_Popular() { 
        return $this->ajaxBrowse('popular', $this->oDb->getParam('modzzz_listing_perpage_main_recent'));
    }
    
	function getBlockCode_Top() { 
        return $this->ajaxBrowse('top', $this->oDb->getParam('modzzz_listing_perpage_main_recent'));
    }

	function getListingMain() {
        return BxDolModule::getInstance('BxListingModule');
    }
  
	function getBlockCode_Search() {
   
        $this->oTemplate->pageStart();
 
        bx_import('BxDolCategories');
        bx_import('BxDolProfileFields'); 
   
        $oProfileFields = new BxDolProfileFields(0);
        $aDefCountries = $oProfileFields->convertValues4Input('#!Country');
		asort($aDefCountries);
		$aChooseCountries = array(''=>_t("_Select"));   
		$aCountries = array_merge($aChooseCountries, $aDefCountries);
  
		//$sDefaultCountry = getParam('modzzz_listing_default_country');

		//$aStates = $this->oDb->getStateArray($sDefaultCountry); 
		$aStates = array();  
 
		$sCatUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=cat&parent=' ;
 
		$sStateUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=state&country=' ; 
 
		$iLanguageId = getLangIdByName(getCurrentLangName());

		/* Freddy commentaire pour désactiver les category par langue
		$aCategories = $this->oDb->getFormCategoryArray($iLanguageId);
		*/
		$aCategories = $this->oDb->getFormCategoryArray();
		///////Fin freddy modif/////////////
 
        $aForm = array(

            'form_attrs' => array(
                'id'     => 'form_search_listing',
                'name'     => 'form_search_listing',
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
                    'caption' => _t('_modzzz_listing_form_caption_keyword'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),                
                'Parent' => array(
                    'type' => 'select',
                    'name' => 'Parent',
					'values'=> $aCategories,
                    'caption' => _t('_modzzz_listing_form_caption_categories'),
					'attrs' => array( 
						'onchange' => "getHtmlData('subcat','$sCatUrl'+this.value)",
                        'id' => 'parentcat',
					),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ),  
               /*
			    'Category' => array(
                    'type' => 'select',
                    'name' => 'Category',
					'values'=> array(),
                    'caption' => _t('_modzzz_listing_form_caption_subcategories'),
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
                    'caption' => _t('_modzzz_listing_form_caption_country'),
                   // 'value' => getParam('modzzz_listing_default_country'),
                    'values' => $aCountries,
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
					'caption' => _t('_modzzz_listing_caption_state'),
					'values'=> $aStates,  
					'attrs' => array( 
						'id' => 'substate',
					), 
				    'db' => array (
						'pass' => 'Xss', 
					), 
				), 
               /*
			    'City' => array(
                    'type' => 'text',
                    'name' => 'City',
                    'caption' => _t('_modzzz_listing_form_caption_city'),
                    'required' => false,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
				*/   
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_modzzz_listing_continue_search'),
                    'colspan' => false,
                ),
            ),            
        );
 
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker(); 
  
        if ($oForm->isSubmittedAndValid ()) {
 
            modzzz_listing_import ('SearchResult');
            $o = new BxListingSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Parent'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country') );
 
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

            $this->oTemplate->addCss (array('unit.css', 'twig.css', 'main.css'));
 
            $this->oTemplate->pageCode($o->aCurrent['title'], false, false);
			exit; 
        } 
 
        return array($oForm->getCode()); 
    } 
   
	function getBlockCode_Comments() { 
	  
		$iNumComments = getParam("modzzz_listing_perpage_main_comment");
		$aAllEntries = $this->oDb->getLatestComments($iNumComments);

		if(!count($aAllEntries)) return; 
			
		$aVars = array (
			'bx_repeat:comments' => array (),
		);

		foreach($aAllEntries as $aEntry) {
		   
			$iMemberID = $aEntry['cmt_author_id'];
			$sNickName = getNickName($iMemberID);
			$sNickLink = getProfileLink($iMemberID);
			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail($iMemberID);
			$sMessage = $aEntry['cmt_text']; 
			$dtSent = defineTimeInterval($aEntry['date']);
			$iListingId = $aEntry['cmt_object_id']; 
	 
			$sImage = '';
			if ($aEntry['thumb']) {
				$a = array ('ID' => $aEntry['author_id'], 'Avatar' => $aEntry['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}
 
			$iLimitChars = (int)getParam('modzzz_listing_comments_max_preview');

			$sMessage = $this->oMain->_formatSnippetText($aEntry, $iLimitChars, $sMessage);
 
			$aListing = $this->oDb->getEntryById($iListingId);
			$sListingUri = $aListing['uri'];
			$sListingTitle = $aListing['title'];
 
			$sListingUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sListingUri;
   
			$aVars['bx_repeat:comments'][] = array (
				'thumb_url' => $sMemberThumb,
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
  		 
		$iNumComments = getParam("modzzz_listing_perpage_main_comment");
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

			$iLimitChars = (int)getParam('modzzz_listing_forum_max_preview');
			$sPostText = $this->oMain->_formatSnippetText($aEachPost, $iLimitChars, $sPostText);
 
 			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail(getID($sPoster));

			$sImage = '';
			if ($aEachPost['thumb']) {
				$a = array ('ID' => $aEachPost['author_id'], 'Avatar' => $aEachPost['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}

			$sListingUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sForumUri;
			$sTopicUrl = BX_DOL_URL_ROOT . 'forum/listing/forum/'.$sForumUri.'-0.htm#topic/'.$sTopicUri.'.htm';
	
			$aVars['bx_repeat:entries'][] = array( 
							'topic_url' => $sTopicUrl, 
							'topic' => $sTopic, 
							'snippet_text' => $sPostText, 
							'item_title' => $sListingName, 
							'item_url' => $sListingUrl, 
							'created' => $sDate,
							'author_url' => getProfileLink(getID($sPoster)),
							'author' => $sPoster,
							'thumb_url' => $sMemberThumb,
						);
		}

		$sCode = $this->oTemplate->parseHtmlByName('block_forum', $aVars);  

		return $sCode;
	}
 
	function getBlockCode_Create() {
		
		// freddy add $aProfile = getProfileInfo($this->_oMain->_iProfileId);
			$aProfile = getProfileInfo($this->_oMain->_iProfileId);
		////////////////////
		
		$sAskUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/my&filter=add_listing'; 
    
		$aVars = array( 
			'create_url' => $sAskUrl, 
			
			// Freddy add  'BusinessName' => $aProfile['BusinessName'] ? $aProfile['BusinessName'] : _t('_modzzz_listing_enter_your_listing'),
			'BusinessName' => $aProfile['BusinessName'] ? $aProfile['BusinessName'] : _t('_modzzz_listing_enter_your_listing'),
			
  		);
 
		$sCode = $this->oTemplate->parseHtmlByName('create_listing', $aVars);  

		return array($sCode);
	}
 
    function getBlockCode_Tags($iBlockId) { 
        bx_import('BxTemplTagsModule');
        $aParam = array(
            'type' => 'modzzz_listing',
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
           // freddy modif
		   // _t('_Tags')
		   _t('_tags_plural_business_listing')
        ); 

    } 
 

}
