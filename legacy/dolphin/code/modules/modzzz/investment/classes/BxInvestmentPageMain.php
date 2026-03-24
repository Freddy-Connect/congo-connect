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
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolTwigPageMain');
bx_import('BxTemplCategories');

class BxInvestmentPageMain extends BxDolTwigPageMain {
	
	
	
	/////////////////Freddy ajout pour afficher un bloc à la page d'accueil permettant d inscrire son entreprise 
	 function getBlockCode_InscriptionInvestment() {
		 $aData = $this->aDataEntry;
		 $aProfileInfo = getProfileInfo($iProfileId);
		 
		 $aAuthor = getProfileInfo($aData['author_id']);
		$sAuthorLink = getProfileLink($aProfileInfo['ID']);	
		//$icoThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aProfileInfo['ID'], 'none');
		$icoThumb = $GLOBALS['oFunctions']->getMemberIcon($aProfileInfo['ID'], 'left');
		
			
		 $aFactureInvoiceCount = $this->oDb->getFactureInvoiceCount($aProfileInfo['ID']);
		  if($aFactureInvoiceCount<= '1'){
			$aFactureInvoice = _t('_modzzz_investment_invoice_commande_single') ; 
		}
		else{
			$aFactureInvoice = _t('_modzzz_investmentinvoice_commande_plural') ;  
		}
		 
		
		 $aInvestmentCount = $this->oDb->getModzzzCountInvestment($aProfileInfo['ID']);
		 // Freddy pour afficher au singulier ou au pluriel le nombre d'entreprise inscrite par chaque membre 
		
	   if($aInvestmentCount<= '1'){
			$aInvestmentCaption = _t('_modzzz_investment_business_home_single') ; 
		}
		else{
			$aInvestmentCaption = _t('_modzzz_investments_business_home_plural') ;  
		}
		
		 if($aInvestmentCount == '0'){
			$aInvestmentLink = BX_DOL_URL_ROOT .  'm/investment/home' ; // Si aucun business inscrit, rester sur la page d'accueil de business listing
		}
		else{
			$aInvestmentLink = BX_DOL_URL_ROOT . 'm/investment/browse/my' ; // Sinon rediriger sur m/listing/browse/my
		}
		
		
		$aFavoriteInvestment = $this->oDb->CountFavoriteMemberInvestment($aProfileInfo['ID']);
		if($aFavoriteInvestment > 1){
			$aFavoriteCaption = _t('_modzzz_investement_entreprises_favorites_plural') ; 
		}
		else{
			$aFavoriteCaption = _t('_modzzz_investment_entreprise_favorite_single') ;  
		}
		
		  $aVars = array (  
       'thumbnail'=> $icoThumb,
	   'ProfilLink'=> $sAuthorLink,
	   'FirstName' => $aProfileInfo['FirstName'],
	    'LastName' => $aProfileInfo['LastName'],
		
		  'InvestmentCount' => $aInvestmentCount,
	    'Investment_caption'=> $aInvestmentCaption,
		'Investment_Link'=> $aInvestmentLink,
		
		
		
		
		  
	    'bx_if:membre_sans_projet' => array( 
								'condition' => $aInvestmentCount < 1 ,
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
			
							
			'bx_if:Investment_Nombre' => array( 
								'condition' => $aInvestmentCount  >= 1 ,
								'content' => array(
								'InvestmentCount' => $aInvestmentCount,
	                            'Investment_caption'=> $aInvestmentCaption,
		                         'Investment_Link'=> $aInvestmentLink,
       
		
								), 
							),
							
							
			 'bx_if:Investment_Favorites' => array( 
								'condition' => $aFavoriteInvestment  >= 1 ,
								'content' => array(
								'FavoriteCaption'=> $aFavoriteCaption,
		                         'FavoriteInvestment' => $aFavoriteInvestment,
       
		
								), 
							),
		
		
	   
	   
	   
	   
	    'bx_if:go_to_your_space_caption' => array( 
								'condition' => $aInvestmentCount >= 1 ,
								'content' => array(
        'go_to_your_space_caption' => _t('_modzzz_listing_acceder_votre_espace') ,
		
		
								), 
							),
							
							
	
							
	  
     );
	  return $this->oTemplate->parseHtmlByName('block_inscription_investment', $aVars);  
	  
    }
		
	
	
	
	 // Freddy ajout function ajaxBrowse; Cette function a été copiée de /inc/classes/BxDolTwigPageMain.php
	  //Le but c'est de ne pas afficher les block dans Investment Home quand c'est vide
	 function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true)
    {
      // Freddy ajout $oMain = BxDolModule::getInstance('BxInvestmentModule');
	  $oMain = BxDolModule::getInstance('BxInvestmentModule');
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
	
	
	

    function BxInvestmentPageMain(&$oMain) {

		$this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
		$this->oMain = $oMain;

        $this->sSearchResultClassName = 'BxInvestmentSearchResult';
        $this->sFilterName = 'filter';
		parent::BxDolTwigPageMain('modzzz_investment_main', $oMain);
	}
  
    function getBlockCode_Map() {
 
 		$sMeasurementType = getParam('modzzz_investment_measurement_type'); 
		$sBaseUri = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri();

		$iProfileId = getLoggedId();
        $aViewer = getProfileInfo($iProfileId);

		if($iProfileId){
 			$this->oTemplate->addInjection('injection_body', 'text',  'onload="load();searchLocations(\''.$sBaseUri.'\');"');
		}else{
 			$this->oTemplate->addInjection('injection_body', 'text',  'onload="load();"');
		}

		$sSiteLang = getCurrentLangName();
		$sKey = getParam('modzzz_investment_key');
		$this->oTemplate->addJs ('http://maps.google.com/maps/api/js?sensor=false&language='.$sSiteLang.'&key='.$sKey);
 		
		
		$this->oTemplate->addJs ('gmap_multiple.js');
  
		switch($sMeasurementType){
			case 'miles':
				$sMeasurementTypeC = _t('_modzzz_investment_miles');
			break;
			case 'kilometers':
				$sMeasurementTypeC = _t('_modzzz_investment_kilometers');
			break;
			default:
				$sMeasurementTypeC = _t('_modzzz_investment_kilometers');
			break;
		}
 
		$sCountry = $this->oTemplate->getPreListDisplay('Country', $aViewer['Country']);

		$aVars = array (  
			'city_country' => (trim($aViewer['City'])) ? trim($aViewer['City']) .','. $sCountry : '',
			'zoom' => getParam('modzzz_investment_map_zoom'),
			'lat' => getParam('modzzz_investment_map_lat'),
			'long' => getParam('modzzz_investment_map_long'),
			'distance' => (int)getParam('modzzz_investment_map_default_distance'), 
			'distance_type' => $sMeasurementTypeC,
			'base_uri' => $sBaseUri 
		);
 
        return $this->oTemplate->parseHtmlByName('block_map_multiple', $aVars);
	} 
 
	function getBlockCode_Comments() 
	{   
		$iNumComments = getParam("modzzz_investment_perpage_main_comment");
		$aAllEntries = $this->oDb->getLatestComments($iNumComments);

		if(!count($aAllEntries))
			return; 
   
		$aVars = array (
			'bx_repeat:comments' => array (),
		);
 
		foreach($aAllEntries as $aEntry){
		   
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
 
			$iLimitChars = (int)getParam('modzzz_investment_comments_max_preview');

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
  		 
		$iNumComments = getParam("modzzz_investment_perpage_main_comment");
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

			$iLimitChars = (int)getParam('modzzz_investment_forum_max_preview');
			$sPostText = $this->oMain->_formatSnippetText($aEachPost, $iLimitChars, $sPostText);
 
 			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail(getID($sPoster));

			$sImage = '';
			if ($aEachPost['thumb']) {
				$a = array ('ID' => $aEachPost['author_id'], 'Avatar' => $aEachPost['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}

			$sListingUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sForumUri;
			$sTopicUrl = BX_DOL_URL_ROOT . 'forum/investment/forum/'.$sForumUri.'-0.htm#topic/'.$sTopicUri.'.htm';
	
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
   		
		$sAskUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/my&filter=add_investment'; 
    
		$aVars = array( 
			'create_url' => $sAskUrl, 
  		);
 
		$sCode = $this->oTemplate->parseHtmlByName('create_investment', $aVars);  

		return $sCode;
	}
 
    function getBlockCode_Tags($iBlockId) { 
        bx_import('BxTemplTagsModule');
        $aParam = array(
            'type' => 'modzzz_investment',
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
 
    function getBlockCode_InvestmentCategories() {
		bx_import('BxTemplCategories');
  		
		$sType = 'modzzz_investment';
		
		$oCateg = new BxTemplCategories();
		$oCateg->getTagObjectConfig();

	    $aAllEntries = $this->oDb->getCategories($sType);
    
        $aResult['bx_repeat:entries'] = array();        
 		foreach($aAllEntries as $aEntry)
		{	 
			$iNumCategory = $this->oDb->getCategoryCount($sType,$aEntry['Category']);	
	
			$sHrefTmpl = $oCateg->getHrefWithType($sType);  
			$sCategory = $aEntry['Category'];
            $sCatHref = str_replace( '{tag}', urlencode(title2uri($sCategory)), $sHrefTmpl);
 
	        $aResult['bx_repeat:entries'][] = array(
              /*  'cat_url' => $sCatHref, 
                'cat_name' => $sCategory,
			    'num_items' => $iNumCategory,
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
 
	    return $this->oTemplate->parseHtmlByName('investment_categories', $aResult);  
	}
  
    function getBlockCode_States() {
		$iProfileId = $_COOKIE['memberID'];

		if(!$iProfileId)
			return;

		$aProfile = getProfileInfo($iProfileId);
		$sCountry = ($aProfile['Country']) ? $aProfile['Country'] : getParam('modzzz_investment_default_country');
  
		$sCountryName = _t($GLOBALS['aPreValues']['Country'][$sCountry]['LKey']);

		$aStates = $this->_oDb->getAll("SELECT `State`,`StateCode` FROM `States` WHERE CountryCode='{$sCountry}' ORDER BY `State`");
		 
		if(!count($aStates))
			return;
 
		$aVars = array();
		$aVars['country_name'] = $sCountryName; 
		$aVars['bx_repeat:entries'] = array(); 
		
  		foreach($aStates as $aEachState){
			 
			$sState = $aEachState['State'];
			$sStateCode = $aEachState['StateCode'];

			$sStateUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'local/' . $sCountry .'/'. $sStateCode;
  		
			$iNumCategory = $this->_oDb->getStateCount($sStateCode);	 
			  
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

	    $aStates = array($this->oTemplate->parseHtmlByName('investment_states', $aVars)); 
		$aStates[3] = _t('_modzzz_investment_regions_for_country', $sCountryName);
		
		return $aStates;  
	}

    function getBlockCode_LatestFeaturedInvestors() {
  
		$iNum = (int)getParam('modzzz_investment_perpage_main_featured');
  
	    return $this->ajaxBrowse('featured_investors', $iNum); 
    }

    function getBlockCode_LatestFeaturedEntrepreneurs() {
  
		$iNum = (int)getParam('modzzz_investment_perpage_main_featured');
  
	    return $this->ajaxBrowse('featured_entrepreneurs', $iNum); 
    }
    
    function getBlockCode_LatestFeaturedProfessionals() {
  
		$iNum = (int)getParam('modzzz_investment_perpage_main_featured');
  
	    return $this->ajaxBrowse('featured_professionals', $iNum); 
    }
 
	function getBlockCode_RecentEntrepreneurs() { 
        return $this->ajaxBrowse('recent_entrepreneurs', $this->oDb->getParam('modzzz_investment_perpage_main_recent'));
    }

    function getBlockCode_RecentInvestors() { 
        return $this->ajaxBrowse('recent_investors', $this->oDb->getParam('modzzz_investment_perpage_main_recent'));
    }

    function getBlockCode_RecentProfessionals() { 
        return $this->ajaxBrowse('recent_professionals', $this->oDb->getParam('modzzz_investment_perpage_main_recent'));
    }

	function getBlockCode_Popular() { 
        return $this->ajaxBrowse('popular', $this->oDb->getParam('modzzz_investment_perpage_main_recent'));
    }
    
	function getBlockCode_Top() { 
        return $this->ajaxBrowse('top', $this->oDb->getParam('modzzz_investment_perpage_main_recent'));
    }

	function getInvestmentMain() {
        return BxDolModule::getInstance('BxInvestmentModule');
    }
   
	function getBlockCode_Search() {
   
        $this->oTemplate->pageStart();
 
        bx_import('BxDolProfileFields'); 
		bx_import('BxDolCategories');
        $oCategories = new BxDolCategories();
        $oCategories->getTagObjectConfig ();
        $aCategories = $oCategories->getCategoriesList('modzzz_investment', (int)$iProfileId, true);
  		$aCategories[''] = _t('_modzzz_investment_all_categories');  
 
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
		$aCountries[''] = "";  
		asort($aCountries);
		$aProfile = getProfileInfo((int)$iProfileId); 
		$sDefaultCountry = ($aProfile['Country']) ? $aProfile['Country'] : getParam('modzzz_investment_default_country');
		$aStates = $this->oDb->getStateArray($sDefaultCountry);  
 
		$sStateUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home/'.($this->oMain->isPermalinkEnabled() ? '?' : '&').'ajax=state&country=' ;
 
		$sCatUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home/'.($this->oMain->isPermalinkEnabled() ? '?' : '&').'ajax=cat&parent=' ;

  
        $aForm = array(

            'form_attrs' => array(
                'id'     => 'form_search_investment',
                'name'     => 'form_search_investment',
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
                    'caption' => _t('_modzzz_investment_form_caption_keyword'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ), 
                'Type' => array( 
                    'type' => 'select',
                    'name' => 'Type',
					'values'=> array(
							'all' => _t('_modzzz_investment_all'),
							'investor' => _t('_modzzz_investment_investor'),
							'entrepreneur' => _t('_modzzz_investment_entrepreneur') 
					),
                    'caption' => _t('_modzzz_investment_form_caption_type'), 
                ), 
                'Category' => array(
                    'type' => 'select_box',
                    'name' => 'Category',
                    'caption' => _t('_modzzz_investment_form_caption_category'),
                    'values' => $aCategories,  
					'required' => false,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ), 
                'Country' => array(
					'type' => 'select',
                    'name' => 'Country',
                    'caption' => _t('_modzzz_investment_form_caption_country'),
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
					'caption' => _t('_modzzz_investment_caption_state'),
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
                    'caption' => _t('_modzzz_investment_form_caption_city'),
                    'required' => false,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),    
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_modzzz_investment_continue'),
                    'colspan' => true,
                ),
            ),            
        );
 
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker(); 
  
        if ($oForm->isSubmittedAndValid ()) {
 
            modzzz_investment_import ('SearchResult');
            $o = new BxInvestmentSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Type'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country') );
 
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


}
