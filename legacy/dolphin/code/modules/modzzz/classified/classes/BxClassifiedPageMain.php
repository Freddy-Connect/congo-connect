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
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolTwigPageMain');
bx_import('BxTemplCategories');

class BxClassifiedPageMain extends BxDolTwigPageMain {

    function BxClassifiedPageMain(&$oMain) {

		$this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->_oTemplate = $oMain->_oTemplate;
		$this->oMain = $oMain;

        $this->sSearchResultClassName = 'BxClassifiedSearchResult';
        $this->sFilterName = 'filter';
		parent::BxDolTwigPageMain('modzzz_classified_main', $oMain);
	}
	
		/////////////////Freddy ajout pour afficher un bloc à la page d'accueil permettant d inscrire son entreprise 
	 function getBlockCode_Annonces() {
		 $aData = $this->aDataEntry;
		 $aProfileInfo = getProfileInfo($iProfileId);
		 
		 $aAuthor = getProfileInfo($aData['author_id']);
		$sAuthorLink = getProfileLink($aProfileInfo['ID']);	
		$icoThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aProfileInfo['ID'], 'none');
			
		 $aAnnoncesCount = $this->oDb->getModzzzCountAnnonceHome($aProfileInfo['ID']);
		
		 // Freddy pour afficher au singulier ou au pluriel le nombre d'entreprise inscrite par chaque membre 
		
	   if($aAnnoncesCount<= '1'){
			$aAnnoncesCaption = _t('_modzzz_classified_business_home_single') ; 
		}
		else{
			$aAnnoncesCaption = _t('_modzzz_classified_business_home_plural') ;  
		}
		
		
		$aFavoriteClassified = $this->oDb->CountFavoriteMemberClassified($aProfileInfo['ID']);
		if($aFavoriteClassified > 1){
			$aFavoriteCaption = _t('_modzzz_classified_entreprises_favorites_plural') ; 
		}
		else{
			$aFavoriteCaption = _t('_modzzz_classified_entreprise_favorite_single') ;  
		}
		
		 $aFactureInvoiceCount = $this->oDb->getFactureInvoiceCount($aProfileInfo['ID']);
		  if($aFactureInvoiceCount<= '1'){
			$aFactureInvoice = _t('_modzzz_classified_invoice_commande_single') ; 
		}
		else{
			$aFactureInvoice = _t('_modzzz_classified_invoice_commande_plural') ;  
		}
		
		
		  $aVars = array (  
       'thumbnail'=> $icoThumb,
	   'ProfilLink'=> $sAuthorLink,
	  
	   'FirstName' => $aProfileInfo['FirstName'],
	    'LastName' => $aProfileInfo['LastName'],
		
		
		
		     'bx_if:FactureInvoice' => array( 
								'condition' => $aFactureInvoiceCount  >= 1 ,
								'content' => array(
								'FactureCaption' => $aFactureInvoice,
	                            'FactureInvoiceCount' => $aFactureInvoiceCount,
       
		
								), 
							),		
						
			 'bx_if:Annonces_Favorites' => array( 
								'condition' => $aFavoriteClassified  >= 1 ,
								'content' => array(
								'FavoriteCaption'=> $aFavoriteCaption,
		                    'FavoriteAnnonces' => $aFavoriteClassified,
       
		
								), 
							),
		
		  
	    'bx_if:membre_sans_page_annonce' => array( 
								'condition' => $aFavoriteClassified >= 1 ,
								'content' => array(
       
		
								), 
							),
		
	   
	    'bx_if:Zero_Annonce' => array( 
								'condition' => $aAnnoncesCount < 1 ,
								'content' => array(
		  'AnnoncesCount' => $aAnnoncesCount,
	     'AnnoncesCaption'=> $aAnnoncesCaption,
        
								), 
							),
	   
	   
	    'bx_if:go_to_your_space_caption' => array( 
								'condition' => $aAnnoncesCount >= 1 ,
								'content' => array(
		  'AnnoncesCount' => $aAnnoncesCount,
	     'AnnoncesCaption'=> $aAnnoncesCaption,
        
		 		
		
								), 
							),
							
		 'bx_if:Membre_Zero_Annonce' => array( 
								'condition' => $aAnnoncesCount < 1 ,
								'content' => array(
								), 
							),
	  
	  'bx_if:Membre_Plusieurs_Annonces' => array( 
								'condition' => $aAnnoncesCount > 1 ,
								'content' => array(
								), 
							),
	  
     );
	
	  return $this->oTemplate->parseHtmlByName('block_gestion_annonces', $aVars);  
	  
    }
		
	
     
	function getBlockCode_Comments() 
	{   
		$iNumComments = getParam("modzzz_classified_perpage_main_comment");
		$aAllEntries = $this->oDb->getLatestComments($iNumComments);

		if(!count($aAllEntries))
			return; 
   
		$aVars = array (
			'bx_repeat:comments' => array (),
		);
 
		foreach($aAllEntries as $aEntry){
		   
			$aProfile = getProfileInfo((int)$aEntry['cmt_author_id']);
			$iMemberID = $aProfile['ID'];
 
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
 
			$iLimitChars = (int)getParam('modzzz_classified_comments_max_preview');

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
  		 
		$iNumComments = getParam("modzzz_classified_perpage_main_comment");
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

			$iLimitChars = (int)getParam('modzzz_classified_forum_max_preview');
			$sPostText = $this->oMain->_formatSnippetText($aEachPost, $iLimitChars, $sPostText);
 
 			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail(getID($sPoster));

			$sImage = '';
			if ($aEachPost['thumb']) {
				$a = array ('ID' => $aEachPost['author_id'], 'Avatar' => $aEachPost['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}

			$sListingUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sForumUri;
			$sTopicUrl = BX_DOL_URL_ROOT . 'forum/classified/topic/'.$sTopicUri.'.htm';
	
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
  
		$bPaidClassifieds = $this->oMain->isAllowedPaidClassifieds (); 
		
		if($bPaidClassifieds)
			$aPackage = $this->_oDb->getPackageList();
 
		$sCatUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.($this->oMain->isPermalinkEnabled() ? '?' : '&').'ajax=cat&parent=' ;
		$aCategory = $this->_oDb->getFormCategoryArray();
		$aSubCategory = array();

		$sPackageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.($this->oMain->isPermalinkEnabled() ? '?' : '&').'ajax=package&package=' ; 

		$iPackageId = ($_POST['package_id']) ? $_POST['package_id'] : $this->_oDb->getInitPackage();
		$sPackageDesc = $this->_oTemplate->getFormPackageDesc($iPackageId);

		if($_POST['parent_category_id']) {
 			$iSelSubCategory = $_POST['category_id']; 
			$iSelCategory = $_POST['parent_category_id']; 
			$aSubCategory = $this->_oDb->getFormCategoryArray($iSelCategory); 
		} 
 
        $oProfileFields = new BxDolProfileFields(0);
        $aTypes = $oProfileFields->convertValues4Input('#!ClassifiedType');
        ksort($aTypes);
 
		$aForm = array(
            'form_attrs' => array(
                'name' => 'package_form',
                'method' => 'post', 
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/my&filter=add_classified',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_package',
                ),
            ),
            'inputs' => array( 
    
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_classified_form_caption_title'),
                     'required' => true, 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_modzzz_classified_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 	 
                'parent_category_id' => array(
                    'type' => 'select',
                    'name' => 'parent_category_id',
					'values'=> $aCategory,
                    'value' => $iSelCategory,
                    'caption' => _t('_modzzz_classified_parent_categories'),
					'attrs' => array(
						'onchange' => "getHtmlData('subcatadd','$sCatUrl'+this.value)",
                        'id' => 'parentcat',
					),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_classified_form_err_category'),
                    ), 
                ),

                'category_id' => array(
                    'type' => 'select',
                    'name' => 'category_id',
					'values'=> $aSubCategory,
                    'value' => $iSelSubCategory, 
                    'caption' => _t('_Categories'),
					'attrs' => array(
                        'id' => 'subcatadd',
					),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_classified_form_err_category'),
                    ),
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ),
 
                'classified_type' => array( 
                    'type' => 'select',
                    'name' => 'classified_type',
				    'values' => $aTypes, 
                    'caption' => _t('_modzzz_classified_form_caption_type_of_classified'), 
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_classified_form_err_type_of_classified'),
                    ),   
                ), 
 				 
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_modzzz_classified_form_caption_package'),
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),	
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_classified_form_err_package'),
                    ),   
                ),  
				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_classified_package_desc'),  
                ), 
				'Submit' => array (
					'type' => 'submit',
					'name' => 'submit_package',
					'value' => _t('_modzzz_classified_continue'),
					'colspan' => false,
				),   
    
            ),
        );
  
 		if(!$bPaidClassifieds){ 
			unset($aForm['inputs']['package_id']);
 			unset($aForm['inputs']['package_desc']); 
		}

        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
  
		$sPromoText = $this->_oDb->getPromoText();

		$sBlockCode = $sPromoText .'<br>'. $oForm->getCode(); 

		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sBlockCode));  
    }
 
	function getBlockCode_CreateOLD() {
   		
		$sAskUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/my&filter=add_classified'; 
    
		$aVars = array( 
			'create_url' => $sAskUrl, 
  		);
 
		$sCode = $this->oTemplate->parseHtmlByName('create_classified', $aVars);  

		return array($sCode);
	}
 
    function getBlockCode_Tags($iBlockId) { 
        bx_import('BxTemplTagsModule');
        $aParam = array(
            'type' => 'modzzz_classified',
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
 
    function getBlockCode_ClassifiedCategories() {
 
	    $aAllEntries = $this->_oDb->getCategoryInfo(0, true);
     
        $aResult['bx_repeat:entries'] = array();        
 		foreach($aAllEntries as $aEntry)
		{	 
			$iNumCategory = $this->_oDb->getParentCategoryCount($aEntry['id']);	 
			$sCatHref = $this->_oDb->getCategoryUrl($aEntry['uri']);  
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
 
	    return $this->oTemplate->parseHtmlByName('classified_categories', $aResult);  
	}
  
    function getBlockCode_States() {
		$iProfileId = getLoggedId();

		$aProfile = ($iProfileId) ? getProfileInfo($iProfileId) : array(); 
		$sCountry = ($aProfile['Country']) ? $aProfile['Country'] : getParam('modzzz_classified_default_country');
 
		$sCountryName = _t($GLOBALS['aPreValues']['Country'][$sCountry]['LKey']);

		$aStates = $this->_oDb->getAll("SELECT `State`,`StateCode` FROM `States` WHERE CountryCode='{$sCountry}' ORDER BY `State`");
		 
		if(!count($aStates)) return;
			 
		$aVars = array();
		$aVars['country_name'] = $sCountryName; 
		$aVars['bx_repeat:entries'] = array(); 
		
  		foreach($aStates as $aEachState){
			 
			$sState = $aEachState['State'];
			$sStateCode = $aEachState['StateCode'];

			$sStateUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'local/' . $sCountry .'/'. $sStateCode;
  		
			$iNumCategory = $this->_oDb->getStateCount($sStateCode);	 
			  
			$aWidth = array(25,100,50,33,25);
			$iCol = (int)getParam('modzzz_classified_state_columns'); 

			$aVars['bx_repeat:entries'][] = array(
		 
				'bx_if:selstate' => array( 
				//// Freddy ajout/ Ne pas afficher les regions/states dont le nombre total est zero
					//'condition' => ($sStateCode == $this->sState),
					'condition' => ($sStateCode == $this->sState) && $iNumCategory >=1 ,
					
					'content' => array( 
						'width' => $aWidth[$iCol], 
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
						'width' => $aWidth[$iCol], 
						'state_url' => $sStateUrl, 
						'state_name' => $sState,
						'num_items' => $iNumCategory,   
					), 
				), 

			 ); 
	    } 
 
 	    $aStates = array($this->oTemplate->parseHtmlByName('block_states_main', $aVars)); 
		$aStates[3] = _t('_modzzz_classified_browse_classified_in_country', $sCountryName);
		
		return $aStates;  
	}
	
	 // Freddy ajout function ajaxBrowse; Cette function a été copiée de /inc/classes/BxDolTwigPageMain.php
	  //Le but c'est de ne pas afficher les block dans Classified Listing Home quand c'est vide
	 function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true)
    {
      // Freddy ajout $oMain = BxDolModule::getInstance('BxClassifiedModule');
	  $oMain = BxDolModule::getInstance('BxClassifiedModule');
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
	
	

    function getBlockCode_LatestFeaturedClassified() {
  
		$iNumClassifieds = (int)getParam('modzzz_classified_perpage_main_featured');
  
	    return $this->ajaxBrowse('featured', $iNumClassifieds); 
    }
 
    function getBlockCode_Recent() { 
        return $this->ajaxBrowse('recent', $this->oDb->getParam('modzzz_classified_perpage_main_recent'));
    }

	function getBlockCode_Popular() { 
        return $this->ajaxBrowse('popular', $this->oDb->getParam('modzzz_classified_perpage_main_recent'));
    }
    
	function getBlockCode_Top() { 
        return $this->ajaxBrowse('top', $this->oDb->getParam('modzzz_classified_perpage_main_recent'));
    }

	function getClassifiedMain() {
        return BxDolModule::getInstance('BxClassifiedModule');
    }
   
	function getBlockCode_Search() {
   
        $this->oTemplate->pageStart();
 
        bx_import('BxDolCategories');
        bx_import('BxDolProfileFields'); 
  
        $oProfileFields = new BxDolProfileFields(0);
       /* freddy modif 
	    $aCountries = $oProfileFields->convertValues4Input('#!Country');
		$aCountries[''] = "";  
		asort($aCountries);
		*/
		
		 $aDefCountries = $oProfileFields->convertValues4Input('#!Country');
		asort($aDefCountries);
		$aChooseCountries = array(''=>_t("_Select"));   
		$aCountries = array_merge($aChooseCountries, $aDefCountries);
		


		$aProfile = getProfileInfo((int)$iProfileId); 
		$sDefaultCountry = ($aProfile['Country']) ? $aProfile['Country'] : getParam('modzzz_classified_default_country');

		/* freddy modif
		$aStates = $this->oDb->getStateArray($sDefaultCountry); 
		*/ 
		$aStates = array();  
 
		$sStateUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home/'.($this->oMain->isPermalinkEnabled() ? '?' : '&').'ajax=state&country=' ;
 
		$sCatUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home/'.($this->oMain->isPermalinkEnabled() ? '?' : '&').'ajax=cat&parent=' ;

		$aCategories = $this->oDb->getFormCategoryArray();
 
        $aForm = array(

            'form_attrs' => array(
                'name'     => 'form_search_classified',
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
                    'caption' => _t('_modzzz_classified_form_caption_keyword'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),                
               /*
			    'parent' => array(
                    'type' => 'select',
                    'name' => 'parent',
					'values'=> $aCategories,
                    'caption' => _t('_modzzz_classified_parent_categories_search'),
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
                    'caption' => _t('_modzzz_classified_form_caption_subcategories'),
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
                    'caption' => _t('_modzzz_classified_form_caption_country'),
                    'values' => $aCountries,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					), 
					
					
					// 'value' => getParam('modzzz_listing_default_country'),
					//'value' => $sDefaultCountry, 
                    'values' => $aCountries,
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{0,2})/'),
                    ),  
                ),
				
				'State' => array(
					'type' => 'select',
					'name' => 'State', 
					'caption' => _t('_modzzz_classified_caption_state'),
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
                    'caption' => _t('_modzzz_classified_form_caption_city'),
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
                    'value' => _t('_modzzz_classified_continue'),
                    'colspan' => false,
                ),
            ),            
        );
 
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker(); 
  
        if ($oForm->isSubmittedAndValid ()) {
 
            modzzz_classified_import ('SearchResult');
            $o = new BxClassifiedSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country') );
 
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
