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

function modzzz_listing_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'listing') {
        $oMain = BxDolModule::getInstance('BxListingModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxTemplSearchResult');
bx_import('BxDolCategories');


define ('BX_LISTING_PHOTOS_CAT', 'Listing');
define ('BX_LISTING_PHOTOS_TAG', 'listing');

define ('BX_LISTING_VIDEOS_CAT', 'Listing');
define ('BX_LISTING_VIDEOS_TAG', 'listing');

define ('BX_LISTING_SOUNDS_CAT', 'Listing');
define ('BX_LISTING_SOUNDS_TAG', 'listing');

define ('BX_LISTING_FILES_CAT', 'Listing');
define ('BX_LISTING_FILES_TAG', 'listing');

define ('BX_LISTING_MAX_FANS', 1000);
define ('BX_LISTING_MAX_EMPLOYEES', 1000);
 
 
/*
 * Listing module
 *
 * This module allow users to create user's listing, 
 * users can rate, comment and discuss listing.
 * Listing can have photos, videos, sounds and files, uploaded
 * by listing's admins.
 *
 * 
 *
 * Profile's Wall:
 * 'add listing' event is displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new listing was created
 * change - listing was chaned
 * rate - somebody rated listing
 * commentPost - somebody posted comment in listing
 *
 *
 *
 * Memberships/ACL:
 * listing view listing - BX_LISTING_VIEW_LISTING
 * listing browse - BX_LISTING_BROWSE
 * listing search - BX_LISTING_SEARCH
 * listing add listing - BX_LISTING_ADD_LISTING
 * listing comments delete and edit - BX_LISTING_COMMENTS_DELETE_AND_EDIT
 * listing edit any listing - BX_LISTING_EDIT_ANY_LISTING
 * listing delete any listing - BX_LISTING_DELETE_ANY_LISTING
 * listing mark as featured - BX_LISTING_MARK_AS_FEATURED
 * listing approve listing - BX_LISTING_APPROVE_LISTING
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different listing
 * @see BxListingModule::serviceHomepageBlock
 * BxDolService::call('listing', 'homepage_block', array());
 *
 * Profile block with user's listing
 * @see BxListingModule::serviceProfileBlock
 * BxDolService::call('listing', 'profile_block', array($iProfileId));
 *

 *
 * Member menu item for listing (for internal usage only)
 * @see BxListingModule::serviceGetMemberMenuItem
 * BxDolService::call('listing', 'get_member_menu_item', array());
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'modzzz_listing'
 * The following alerts are rised
 *
 
 *
 *  add - new listing was added
 *      $iObjectId - listing id
 *      $iSenderId - creator of a listing
 *      $aExtras['Status'] - status of added listing
 *
 *  change - listing's info was changed
 *      $iObjectId - listing id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed listing
 *
 *  delete - listing was deleted
 *      $iObjectId - listing id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - listing was marked/unmarked as featured
 *      $iObjectId - listing id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if listing was marked as featured and 0 - if listing was removed from featured 
 *
 */
class BxListingModule extends BxDolTwigModule {

    var $_oPrivacy; 
	var $_aQuickCache = array ();

    function BxListingModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'listing';
        $this->_sPrefix = 'modzzz_listing';

        bx_import ('Privacy', $aModule);
        bx_import ('SubPrivacy', $aModule);
        $this->_oPrivacy = new BxListingPrivacy($this);
 
	    $this->_oConfig->init($this->_oDb);

        $GLOBALS['oBxListingModule'] = &$this;

		//reloads subcategories on Add form
 
		if(bx_get('ajax')=='langcat') { 
			$iLanguageId = bx_get('speak');
			/* Freddy commentaire pour désactiver les category par langue
			echo $this->_oDb->getParentOptions($iLanguageId);
			*/
			echo $this->_oDb->getParentOptions();
			////// fin freddy modif
			exit;
		}  

		if(bx_get('ajax')=='public') { 
			$iCategoryId = bx_get('id');
			echo $this->_oDb->getAjaxPublicOptions($iCategoryId);
			exit;
		}  

		if(bx_get('ajax')=='admincat') { 
			$iCategoryId = bx_get('id');
			echo $this->_oDb->getAjaxCategoryOptions($iCategoryId);
			exit;
		}  

		if(bx_get('ajax')=='cat') { 
			$iParentId = (int)bx_get('parent');
			echo $this->_oDb->getAjaxCategoryOptions($iParentId, $this->isAdmin());
			exit;
		}  

		if(bx_get('ajax')=='multicat') { 
			$iParentId = (int)bx_get('parent');
			echo $this->_oDb->getAjaxMultiCategoryOptions($iParentId, $this->isAdmin());
			exit;
		}  

		if(bx_get('ajax')=='state') { 
			$sCountryCode = bx_get('country');
			echo $this->_oDb->getStateOptions($sCountryCode);
			exit;
		}	

		if(bx_get('ajax')=='package') { 
			$iPackageId = (int)bx_get('package');
			echo $this->_oTemplate->getFormPackageDesc($iPackageId);
			exit;
		}

    }

    function actionHome () {
        parent::_actionHome(_t('_modzzz_listing_page_title_home'));
    }

    function actionFiles ($sUri) {
        parent::_actionFiles ($sUri, _t('_modzzz_listing_page_title_files'));
    }

    function actionSounds ($sUri) {
        parent::_actionSounds ($sUri, _t('_modzzz_listing_page_title_sounds'));
    }

    function actionVideos ($sUri) {
        parent::_actionVideos ($sUri, _t('_modzzz_listing_page_title_videos'));
    }

    function actionPhotos ($sUri) {
        parent::_actionPhotos ($sUri, _t('_modzzz_listing_page_title_photos'));
    }

    function actionComments ($sUri) {
        parent::_actionComments ($sUri, _t('_modzzz_listing_page_title_comments'));
    }
  
	function actionView ($sUri) {
        $this->_actionView ($sUri, _t('_modzzz_listing_msg_pending_approval'));
    }
 
    function _actionView ($sUri, $sMsgPendingApproval) {

        if (!($aDataEntry = $this->_preProductTabs($sUri)))
            return;

        $this->_oTemplate->pageStart();

        bx_import ('PageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageView';
        $oPage = new $sClass ($this, $aDataEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();
   
        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aDataEntry[$this->_oDb->_sFieldDescription]), 0, 255));
        $this->_oTemplate->addPageKeywords ($aDataEntry[$this->_oDb->_sFieldTags]);

	    //[begin] seo modification 
		$sTitle = $aDataEntry[$this->_oDb->_sFieldTitle];
		$sLocation = $this->_formatLocation($aDataEntry);
		$sSiteTitle = isset($GLOBALS['site']['title']) ? $GLOBALS['site']['title'] : getParam('site_title');

        $this->_oTemplate->setPageTitle ($sTitle . ' | '. $sLocation . ' | '. $sSiteTitle);
 
		$sDescription = $sTitle .' '. _t('_modzzz_listing_located') . ' @ '. $sLocation; 

		$aImages = array();
		$aPhotos = $this->_oDb->getAllPhotos($aDataEntry[$this->_oDb->_sFieldId]);
		foreach($aPhotos as $aEachPhoto){
 
			$a = array ('ID' => $aDataEntry['author_id'], 'Avatar' => $aEachPhoto['thumb']);
			$aImage = BxDolService::call('photos', 'get_image', array($a, 'file'), 'Search');
			$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			
			$aImages[] = array(
				'image' => ($sImage) ? $sImage : $this->_oTemplate->getImageUrl('no-image-thumb.png')
			); 
		}

		if(!count($aPhotos)){
			$aImages[] = array(
				'image' => $this->_oTemplate->getImageUrl('no-image-thumb.png')
			); 
		}
          
		if ($oMap = BxDolModule::getInstance('BxWmapModule')){
			$sPart = $this->_oConfig->getUri();
			if (isset($oMap->_aParts[$sPart])){ 
				$sParamPrefix = 'bx_wmap_entry_' . $sPart;
				$iEntryId = (int)$aDataEntry[$this->_oDb->_sFieldId];
				$r = $oMap->_oDb->getDirectLocation($iEntryId, $oMap->_aParts[$sPart]);
				$fLatitude = $r['lat'];
				$fLongitude = $r['lng'];
			} 
		}
 
	    $aVars = array (
			'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
			'title' => $sTitle,
			'description' => $sDescription,
			'bx_repeat:images' => $aImages,	
			'street' => $aDataEntry['address1'],
			'city' => $aDataEntry['city'],
			'state' => $aDataEntry['state'],
			'zip' => $aDataEntry['zip'],
			'country' => $aDataEntry['country'],
			'site_name' => $sSiteTitle,
			'latitude' => $fLatitude, 
			'longitude' => $fLongitude,   
		); 
        $sInjection = $this->_oTemplate->parseHtmlByName ('head_injection', $aVars);
 
		$this->_oTemplate->addInjection('injection_head', 'text',  $sInjection); 
	   //[end] seo modification

        $this->_oTemplate->addJsTranslation(array('_are you sure?'));

        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->addCss ('unit_fan.css');
        $this->_oTemplate->pageCode($aDataEntry[$this->_oDb->_sFieldTitle], false, false);

        bx_import ('BxDolViews');
        new BxDolViews($this->_sPrefix, $aDataEntry[$this->_oDb->_sFieldId]);
    }
  
    function actionUploadPhotos ($sUri) {        
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadPhotos', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_listing_page_title_upload_photos'));
    }

    function actionUploadVideos ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadVideos', 'videos', array ('videos_choice', 'videos_upload'), _t('_modzzz_listing_page_title_upload_videos'));
    }

    function actionUploadSounds ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadSounds', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_modzzz_listing_page_title_upload_sounds')); 
    }

    function actionUploadFiles ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadFiles', 'files', array ('files_choice', 'files_upload'), _t('_modzzz_listing_page_title_upload_files')); 
    }
  
    function actionCalendar ($iYear = '', $iMonth = '') {
        parent::_actionCalendar ($iYear, $iMonth, _t('_modzzz_listing_page_title_calendar'));
    }

    function actionSearch ($sKeyword = '', $sParent = '', $sCategory = '', $sCity = '', $sState = '', $sCountry = '') {

        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        if ($sKeyword) 
            $_GET['Keyword'] = $sKeyword;
        if ($sCategory)
            $_GET['Category'] = $sCategory;
        if ($sParent)
            $_GET['Parent'] = $sParent; 
		if ($sCity) 
            $_GET['City'] = $sCity;
		if ($sState) 
            $_GET['State'] = $sState; 
         if ($sCountry)
            $_GET['Country'] = $sCountry;
  
        if ($sCountry || $sCategory || $sParent || $sKeyword || $sState || $sCity ) {
            $_GET['submit_form'] = 1;  
        }
        
        modzzz_listing_import ('FormSearch');
        $oForm = new BxListingFormSearch ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
            modzzz_listing_import ('SearchResult');
            $o = new BxListingSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Parent'),$oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country') );

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

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t('_modzzz_listing_caption_search'));
    } 
 
    function actionAdd () {
        $this->_actionAdd (_t('_modzzz_listing_page_title_add'));
    }

    function _actionAdd ($sTitle)
    {
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $this->_addForm(false);
 
        $this->_oTemplate->addJs ('multi_select.js');
		// freddy add  $this->_oTemplate->addJs ('jquery.webForms.js');
		$this->_oTemplate->addJs ('jquery.webForms.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode($sTitle);
    }
 
    function _addForm ($sRedirectUrl) {
  
		$bPaidListing = $this->isAllowedPaidListings (); 
		if( $bPaidListing && (!isset($_POST['submit_form'])) ){
			return $this->showPackageSelectForm();
		}else{
			$this->showAddForm($sRedirectUrl);
		}
	}
 
    function showPackageSelectForm() {
   
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
                    'caption' => _t('_modzzz_listing_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),	
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_listing_form_err_package'),
                    ),   
                ),  
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_listing_package_desc'),  
                ), 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_listing_continue'),
                    'name'  => 'submit_package',
                ),
            ),
        );
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
 
 			$bPaidListing = $this->isPaidPackage($oForm->getCleanValue('package_id')); 

			if($bPaidListing)
				$sStatus = 'pending';
			else
				$sStatus =  ((getParam($this->_sPrefix.'_autoapproval') == 'on') || $this->isAdmin()) ? 'approved' : 'pending';

            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
            );                        
            $aValsAdd[$this->_oDb->_sFieldAuthorId] = $this->_iProfileId;

            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

				if(!$this->isAdmin() && getParam('modzzz_listing_post_credits')=='on'){ 

					$oCredit = BxDolModule::getInstance('BxCreditModule'); 
					$iCredits = (int)$oCredit->_oDb->getMemberCredits($this->_iProfileId); 
					$iClassifiedCredits = (int)$oCredit->_oDb->getActionValue('modzzz_listing', 'add');
					 
					if($iClassifiedCredits > $iCredits){
						$s = MsgBox(_t('_modzzz_listing_msg_credit_insufficient',$iClassifiedCredits,$iCredits));  
						$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
						echo $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
						return;
					}else{
						$oCredit->assignCredits($this->_iProfileId, "modzzz_listing", "add", "subtract", time(), abs($iClassifiedCredits));  
					}
				}

                $this->isAllowedAdd(true); // perform action                 
 
 				$this->processLogo($iEntryId); //logo 

				$this->_oDb->addPeriod($iEntryId); //operation 
				
 				$this->_oDb->addYoutube($iEntryId); //video

				$oForm->processAddMedia($iEntryId, $this->_iProfileId);
 
                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());
                $this->onEventCreate($iEntryId, $sStatus, $aDataEntry);
				
				if($oForm->getCleanValue('category_id')){
					$this->_oDb->addSubCategory($iEntryId, $oForm->getCleanValue('category_id'));
				}

				if($this->isAllowedPaidListings()){
  
					$iPackageId = $oForm->getCleanValue('package_id');
					$aPackage = $this->_oDb->getPackageById($iPackageId);
					$fPrice = $aPackage['price'];
					$iDays = $aPackage['days'];
  					$iFeatured = $aPackage['featured'];

					$sInvoiceStatus = ($fPrice) ? 'pending' : 'paid';
					$sInvoiceNo = $this->_oDb->createInvoice($iEntryId, $iPackageId, $fPrice, $iDays, $sInvoiceStatus);

					$this->_oDb->updateEntryInvoice($iEntryId, $sInvoiceNo); 
						 
					//$sRedirectUrl = $this->_oDb->generatePaymentUrl($iEntryId, $fPrice);

					if($fPrice){
						$this->initializeCheckout($iEntryId, $fPrice);  
						return;  
					}else{
						if($iDays)
							$this->_oDb->updateEntryExpiration($iEntryId, $iDays); 
 
						if($iFeatured)
							$this->_oDb->updateFeaturedStatus($iEntryId);
						
						if (!$sRedirectUrl)  
							$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
					}
				}else{
					$iNumActiveDays = (int)getParam("modzzz_listing_free_expired");
					if($iNumActiveDays && (!$this->isAdmin()))
						$this->_oDb->updateEntryExpiration($iEntryId, $iNumActiveDays); 
 
					if (!$sRedirectUrl)  
						$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
				} 
				 
                header ('Location:' . $sRedirectUrl);
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured'));
            } 
  
        } else { 
 
			if(!$this->isAdmin() && getParam('modzzz_listing_post_credits')=='on'){ 

				$oCredit = BxDolModule::getInstance('BxCreditModule'); 
				$iCredits = (int)$oCredit->_oDb->getMemberCredits($this->_iProfileId); 
				$iClassifiedCredits = (int)$oCredit->_oDb->getActionValue('modzzz_listing', 'add');
 
				if($iClassifiedCredits > $iCredits){
					$s = MsgBox(_t('_modzzz_listing_msg_credit_insufficient',$iClassifiedCredits,$iCredits));  
					$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
					echo $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
				}else{
					$s = MsgBox(_t('_modzzz_listing_msg_credit_sufficient',$iClassifiedCredits,$iCredits));  
					$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
					echo $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s)); 
					echo $oForm->getCode (); 
				}
			}else{
				echo $oForm->getCode ();  
			}
        }
    }

    function actionEdit ($iEntryId) {
        $this->_actionEdit ($iEntryId, _t('_modzzz_listing_page_title_edit'));
    }

    function _actionEdit ($iEntryId, $sTitle) { 
	
	
	

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

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

				if($oForm->getCleanValue('category_id')){
					$this->_oDb->addSubCategory($iEntryId, $oForm->getCleanValue('category_id'));
				}
 
				//[begin] operation
				$aItems2Keep = array(); 
				if( is_array($_POST['prev_operation']) && count($_POST['prev_operation'])){ 
					foreach ($_POST['prev_operation'] as $iPeriodId){
						$aItems2Keep[$iPeriodId] = $iPeriodId;
					}
				}
					
				$aPeriodIds = $this->_oDb->getPeriodIds($iEntryId);
			
				$aDeletedItem = array_diff ($aPeriodIds, $aItems2Keep);

				if ($aDeletedItem) {
					foreach ($aDeletedItem as $iItemId) {
						$this->_oDb->removePeriodEntry($iEntryId, $iItemId);
					}
				} 
					 
				$this->_oDb->addPeriod($iEntryId); 
				//[end] operation
 
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

				$this->processLogo($iEntryId); //logo mod

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

        $this->_oTemplate->addJs ('modules/modzzz/listing/js/|multi_select.js');
		
		// freddy add for Edit  $this->_oTemplate->addJs ('jquery.webForms.js');
		// $this->_oTemplate->addJs ('modules/modzzz/listing/js/|jquery.webForms.js');
		 /////
		 
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode($sTitle);
    }
 
    function actionDelete ($iEntryId) {
        parent::_actionDelete ($iEntryId, _t('_modzzz_listing_msg_listing_was_deleted'));
    }
  
    function actionMarkFeatured ($iEntryId) {
        parent::_actionMarkFeatured ($iEntryId, _t('_modzzz_listing_msg_added_to_featured'), _t('_modzzz_listing_msg_removed_from_featured'));
    }
 
    function actionSharePopup ($iEntryId) {
        parent::_actionSharePopup ($iEntryId, _t('_modzzz_listing_caption_share_listing'));
    }
 
   function actionJoin ($iEntryId, $iProfileId) {

        parent::_actionJoin ($iEntryId, $iProfileId, _t('_modzzz_listing_msg_joined_already'), _t('_modzzz_listing_msg_joined_request_pending'), _t('_modzzz_listing_msg_join_success'), _t('_modzzz_listing_msg_join_success_pending'), _t('_modzzz_listing_msg_leave_success'));
    }    
 
    function actionTags() {
        parent::_actionTags (_t('_modzzz_listing_page_title_tags'));
    }    
 
    function actionPackages () { 
        $this->_oTemplate->pageStart();
        bx_import ('PagePackages', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PagePackages';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
        $this->_oTemplate->pageCode(_t('_modzzz_listing_page_title_packages'), false, false);
    }

    function actionCategories ($sUri='') { 

		if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
			$sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
		else
			$sReg = '/^[\d\w\-_]+$/u'; // latin characters only

		if ($sUri && !preg_match($sReg, $sUri)) {
			$this->_oTemplate->displayPageNotFound ();
			return false;
		}
 
        $this->_oTemplate->pageStart();
        bx_import ('PageCategory', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageCategory';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
		$sTitle = _t('_modzzz_listing_page_title_categories');

		if($sUri){
			$aCategoryInfo = $this->_oDb->getCategoryInfoByUri($sUri); 
			$sCategoryName = $aCategoryInfo['name'];
			$sTitle .= ' - ' . $sCategoryName;
		}
		   
		$this->_oTemplate->pageCode($sTitle, false, false);
    }

    function actionSubCategories ($sUri='') {

		if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
			$sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
		else
			$sReg = '/^[\d\w\-_]+$/u'; // latin characters only

		if (!preg_match($sReg, $sUri)) {
			$this->_oTemplate->displayPageNotFound ();
			return false;
		}

        $this->_oTemplate->pageStart();
        bx_import ('PageSubCategory', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageSubCategory';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
		$sTitle = _t('_modzzz_listing_page_title_subcategories');

		if($sUri){
			$aCategoryInfo = $this->_oDb->getCategoryInfoByUri($sUri); 
			$sCategoryName = $aCategoryInfo['name'];
 			$sTitle .= ' - ' . $sCategoryName;
		}

        $this->_oTemplate->pageCode($sTitle, false, false);
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

    function actionMakeClaimPopup ($iEntryId) {
        parent::_actionMakeClaimPopup ($iEntryId, _t('_modzzz_listing_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', modzzz_listing_MAX_FANS);
    }

    function _actionMakeClaimPopup ($iEntryId, $sTitle, $sFuncGetFans = 'getFans', $sFuncIsAllowedManageFans = 'isAllowedManageFans', $sFuncIsAllowedManageAdmins = 'isAllowedManageAdmins', $iMaxFans = 1000) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById ($iEntryId))) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }

        if (!$this->$sFuncIsAllowedManageFans($aDataEntry)) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Access denied')));
            exit;
        }

        $aProfiles = array ();
        $iNum = $this->_oDb->$sFuncGetFans($aProfiles, $iEntryId, true, 0, $iMaxFans);
        if (!$iNum) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }

        $sActionsUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "view/" . $aDataEntry[$this->_oDb->_sFieldUri] . '?ajax_action=';
        $aButtons = array (
            array (
                'type' => 'submit',
                'name' => 'fans_remove',
                'value' => _t('_sys_btn_fans_remove'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_fans_content', '{$sActionsUrl}remove&ids=' + sys_manage_items_get_manage_fans_ids()); return false;\"",
            ),
        );

        if ($this->$sFuncIsAllowedManageAdmins($aDataEntry)) {

            $aButtons = array_merge($aButtons, array (
                array (
                    'type' => 'submit',
                    'name' => 'fans_add_to_admins',
                    'value' => _t('_sys_btn_fans_add_to_admins'),
                    'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_fans_content', '{$sActionsUrl}add_to_admins&ids=' + sys_manage_items_get_manage_fans_ids()); return false;\"",
                ),
                array (
                    'type' => 'submit',
                    'name' => 'fans_move_admins_to_fans',
                    'value' => _t('_sys_btn_fans_move_admins_to_fans'),
                    'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_fans_content', '{$sActionsUrl}admins_to_fans&ids=' + sys_manage_items_get_manage_fans_ids()); return false;\"",
                ),            
            ));
        };
        bx_import ('BxTemplSearchResult');
        $sControl = BxTemplSearchResult::showAdminActionsPanel('sys_manage_items_manage_fans', $aButtons, 'sys_fan_unit');

        $aVarsContent = array (            
            'suffix' => 'manage_fans',
            'content' => $this->_profilesEdit($aProfiles, false, $aDataEntry),
            'control' => $sControl,
        );
        $aVarsPopup = array (
            'title' => $sTitle,
            'content' => $this->_oTemplate->parseHtmlByName('manage_items_form', $aVarsContent),
        );        
        echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true);
        exit;
    }
 
    function actionInvite ($iEntryId) {
        $this->_actionInvite ($iEntryId, 'modzzz_listing_invitation', $this->_oDb->getParam('modzzz_listing_max_email_invitations'), _t('_modzzz_listing_invitation_sent'), _t('_modzzz_listing_no_users_msg'), _t('_modzzz_listing_caption_invite'));
    }

    function _actionInvite ($iEntryId, $sEmailTemplate, $iMaxEmailInvitations, $sMsgInvitationSent, $sMsgNoUsers, $sTitle) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));

        bx_import('BxDolTwigFormInviter');
        $oForm = new BxDolTwigFormInviter ($this, $sMsgNoUsers);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        

            $aInviter = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getInviteParams ($aDataEntry, $aInviter);
            
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;

            // send invitation to registered members
            if (isset($_REQUEST['inviter_users']) && is_array($_REQUEST['inviter_users'])) {
                foreach ($_REQUEST['inviter_users'] as $iRecipient) {
                    $aRecipient = getProfileInfo($iRecipient);
                   //freddy modif
				   // $aPlus = array_merge (array ('NickName' => ' ' . $aRecipient['NickName']), $aPlusOriginal);
				   $aPlus = array_merge (array ('NickName' => ' ' . $aRecipient['FirstName'] .' '. $aRecipient['LastName']), $aPlusOriginal);

					//freddy modif
					$sSubject = str_replace('<NickName>', $aPlus['NickName'], $aTemplate['Subject']);

                    $iSuccess += sendMail(trim($aRecipient['Email']), $sSubject, $aTemplate['Body'], '', $aPlus) ? 1 : 0;
                }
            }

            // send invitation to additional emails
            $iMaxCount = $iMaxEmailInvitations;
            $aEmails = preg_split ("#[,\s\\b]+#", $_REQUEST['inviter_emails']);
            $aPlus = array_merge (array ('NickName' => _t('_modzzz_listing_friend')), $aPlusOriginal);
            if ($aEmails && is_array($aEmails)) {
                foreach ($aEmails as $sEmail) {
                    if (strlen($sEmail) < 5) 
                        continue;

					$sSubject = str_replace('<NickName>', _t('_modzzz_listing_friend'), $aTemplate['Subject']);

                    $iRet = sendMail(trim($sEmail), $sSubject, $aTemplate['Body'], '', $aPlus) ? 1 : 0;
                    $iSuccess += $iRet;
                    if ($iRet && 0 == --$iMaxCount) 
                        break;
                }             
            }

            $sMsg = sprintf($sMsgInvitationSent, $iSuccess);
 
			$sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			echo MsgBox($sMsg) . genAjaxyPopupJS(0, 'ajaxy_popup_result_div', $sRedirect);

            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode ($sMsg, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }
  
    function _getInviteParams ($aDataEntry, $aInviter) {

		$aLocation = array();
		$aLocation[] = _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']);
		 
		if(trim($aDataEntry['state']))
			$aLocation[] = $this->_oDb->getStateName($aDataEntry['country'], $aDataEntry['state']);
		 
		if(trim($aDataEntry['city']))
			$aLocation[] = $aDataEntry['city'];
 
		if(trim($aDataEntry['zip']))
			$aLocation[] = $aDataEntry['zip'];
			
			 
			
			

        return array (
                'ListingName' => $aDataEntry['title'],
                'ListingLocation' => implode(', ', $aLocation),
                'ListingUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'InviterUrl' => $aInviter ? getProfileLink($aInviter['ID']) : 'javascript:void(0);',
                'InviterNickName' => $aInviter ? $aInviter['FirstName'] .' '.$aInviter['LastName'] : _t('_modzzz_listing_user_unknown'),
				
				//Freddy ajout avar de la personne qui invite
				 // 'InviterAvatar' =>  get_member_thumbnail($aInviter['ID'], 'none', true) ,
				'PosterAvatar' => get_member_thumbnail($aInviter['ID'], 'none', true),
				 //'SiteName' =>  str_replace("<SiteName>", $GLOBALS['site']['title'], $sSubject),
                
				
				'InvitationText' => stripslashes(strip_tags($_REQUEST['inviter_text'])),
            );        
    }
 
    function actionInquire ($iEntryId) {
        $this->_actionInquire ($iEntryId, 'modzzz_listing_inquiry', _t('_modzzz_listing_caption_make_inquiry'), _t('_modzzz_listing_inquiry_sent'), _t('_modzzz_listing_inquiry_not_sent'));
    }

    function _actionInquire ($iEntryId, $sEmailTemplate, $sTitle, $sMsgSuccess, $sMsgFail) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));

        bx_import ('InquireForm', $this->_aModule);
		$oForm = new BxListingInquireForm ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
 
			$aInquirer = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getInquireParams ($oForm, $aDataEntry, $aInquirer);
		  
			$iRecipient = $aDataEntry['author_id'];
			
 
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;
  
            // send message to listing owner
            if (trim($oForm->getCleanValue('inquire_text'))) { 
				 $aRecipient = getProfileInfo($iRecipient); 

				 $sContactEmail = trim($aDataEntry['selleremail']) ? trim($aDataEntry['selleremail']) : trim($aRecipient['Email']);
 
				//freddy modif
				// $sSubject = str_replace("<NickName>",$aInquirer['NickName'], $aTemplate['Subject']);
				$sSubject = str_replace("<FirstName>",$aInquirer['FirstName'].' '.$aInquirer['LastName'], $aTemplate['Subject']);
				 $sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $sSubject);
				

				 $aPlus = array_merge (array ('RecipientName' => ' ' . $aRecipient['FirstName'].' '.$aRecipient['LastName']), $aPlusOriginal);
  
				 $aPlusOriginal['Subject'] = $sSubject;
				 $this->inquireToInbox($aRecipient, $aTemplate, $aPlusOriginal);  

                 $iSuccess = sendMail($sContactEmail, $sSubject, $aTemplate['Body'], '', $aPlus) ? 1 : 0;  
			}
			
            $sMsg = ($iSuccess) ? $sMsgSuccess : $sMsgFail;
       
			$sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			echo MsgBox($sMsg) . genAjaxyPopupJS(0, 'ajaxy_popup_result_div', $sRedirect);

            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode ($sMsg, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

    function _getInquireParams ($oForm, $aDataEntry, $aInquirer) {
 
		$sIndustry = $oForm->getCleanValue('industry');
 		$aCategory = ($sIndustry) ? $this->_oDb->getCategoryById($sIndustry) : array();

  		$aEmployees = $this->_oTemplate-> getEmployees();

        return array ( 
			'Name' => $oForm->getCleanValue('name'), 
			'CompanyName' => $oForm->getCleanValue('companyname') ? $oForm->getCleanValue('companyname') : 'N/A', 
			'Industry' => ($sIndustry) ? $aCategory['name'] : 'N/A', 
			'Employees' => $oForm->getCleanValue('employee_count') ? $aEmployees[$oForm->getCleanValue('employee_count')] : 'N/A', 
			'Email' => $oForm->getCleanValue('email'), 
			'Phone' => $oForm->getCleanValue('phone') ? $oForm->getCleanValue('phone') : 'N/A', 
			'Cellphone' => $oForm->getCleanValue('cellphone') ? $oForm->getCleanValue('cellphone') : 'N/A',  
			'ListTitle' => $aDataEntry['title'], 
			'ListUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			'SenderLink' => $aInquirer ? getProfileLink($aInquirer['ID']) : 'javascript:void(0);',
			
			//Freddy modif
			//'SenderName' => $aInquirer ? $aInquirer['NickName'] : _t('_modzzz_listing_user_unknown'),
			'SenderName' => $aInquirer ? $aInquirer['FirstName'].' '. $aInquirer['LastName'] : _t('_modzzz_listing_user_unknown'),
			
			
			//Freddy ajout avar de la personne qui invite
		   //  'SenderAvatar' =>  get_member_thumbnail($aInquirer['ID'], 'none', true) ,
		    'PosterAvatar' => get_member_thumbnail($aInviter['ID'], 'none', true),
			
			'Message' => stripslashes(strip_tags($_REQUEST['inquire_text'])),
		);        
    }
  
    function  inquireToInbox($aProfile, $aTemplate, $aTemplateVars){
		global $tmpl;
		require_once( BX_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $tmpl . '/scripts/BxTemplMailBox.php');

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
  
		$sMessageBody = str_replace("<bx_include_auto:_email_header.html />\r\n\r\n<p>", "", $aTemplate['Body']);
		$sMessageBody = str_replace("\r\n\r\n<bx_include_auto:_email_footer.html />", "", $sMessageBody);
		$sMessageBody = str_replace("<pre>", "", $sMessageBody);
		$sMessageBody = str_replace("</pre>", "", $sMessageBody);
		$sMessageBody = str_replace("<RecipientName>", getNickName($aProfile['ID']), $sMessageBody);

		foreach($aTemplateVars as $sKey=>$sVar){
				$sMessageBody = str_replace("<{$sKey}>", $sVar, $sMessageBody);
		}

		$oMailBox -> iWaitMinutes = 0;//turn off anti-spam
		$oMailBox -> sendMessage($aTemplateVars['Subject'], $sMessageBody, $aProfile['ID'], $aComposeSettings); 

    }

	/*[begin] claim*/
    function actionClaim ($iEntryId) {
        $this->_actionClaim ($iEntryId, 'modzzz_listing_claim', _t('_modzzz_listing_caption_make_claim'), _t('_modzzz_listing_claim_sent'), _t('_modzzz_listing_claim_not_sent'));
    }

    function _actionClaim ($iEntryId, $sEmailTemplate, $sTitle, $sMsgSuccess, $sMsgFail) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));

        bx_import ('ClaimForm', $this->_aModule);
		$oForm = new BxListingClaimForm ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
 
			$aClaimer = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getClaimParams ($aDataEntry, $aClaimer);
		  
			$iRecipient = $aDataEntry['author_id'];
 
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;
  
			$arrAdmins = $this->_oDb->saveClaimRequest($iEntryId, $this->_iProfileId,$_REQUEST['claim_text']);

            // send message to administrator
            if (isset($_REQUEST['claim_text'])) { 
				 
				$arrAdmins = $this->_oDb->getSiteAdmins();

				foreach($arrAdmins as $iRecipient) { 
					$aRecipient = getProfileInfo($iRecipient); 

					// Freddy modif
					//$sSubject = str_replace("<NickName>",$aClaimer['NickName'], $aTemplate['Subject']);
					$sSubject = str_replace("<FirstName>",$aClaimer['FirstName'], $aTemplate['Subject']);
					$sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $sSubject);

					$aPlus = array_merge (array ('RecipientName' => ' ' . $aRecipient['FirstName'].' '. $aRecipient['LastName']), $aPlusOriginal);

					$iSuccess += sendMail(trim($aRecipient['Email']), $sSubject, $aTemplate['Body'], '', $aPlus) ? 1 : 0;  
				}
			}
			
            $sMsg = ($iSuccess) ? $sMsgSuccess : $sMsgFail;
            echo MsgBox($sMsg);
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode ($sMsg, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

    function _getClaimParams ($aDataEntry, $aClaimer) {
        return array (
                'ListTitle' => $aDataEntry['title'], 
                'ListUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'SenderLink' => $aClaimer ? getProfileLink($aClaimer['ID']) : 'javascript:void(0);',
                'SenderName' => $aClaimer ? $aClaimer['FirstName'].' '. $aClaimer['LastName'] : _t('_modzzz_listing_user_unknown'),
                'Message' => stripslashes(strip_tags($_REQUEST['claim_text'])),
            );        
    }
/*[end] claim*/



    // ================================== external actions

    /**
     * Homepage block with different listing
     * @return html to display on homepage in a block
     */     
    function serviceHomepageBlock () {

        if (!$this->_oDb->isAnyPublicContent()){ 
			return '';
        } 
        bx_import ('PageMain', $this->_aModule);
        $o = new BxListingPageMain ($this);
        $o->sUrlStart = BX_DOL_URL_ROOT . 'index.php?';

		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));
  
        $sDefaultHomepageTab = $this->_oDb->getParam('modzzz_listing_homepage_default_tab');
        $sBrowseMode = $sDefaultHomepageTab;
        switch ($_GET['listing']) {            
            case 'featured':
            case 'recent':
            case 'top':
            case 'popular':
            case $sDefaultHomepageTab:            
                $sBrowseMode = $_GET['listing'];
                break;
        }

        return $o->ajaxBrowse(
            $sBrowseMode,
            $this->_oDb->getParam('modzzz_listing_perpage_homepage'), 
            array(
                _t('_modzzz_listing_tab_featured') => array('href' => BX_DOL_URL_ROOT . 'index.php?listing=featured', 'active' => 'featured' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_listing_tab_recent') => array('href' => BX_DOL_URL_ROOT . 'index.php?listing=recent', 'active' => 'recent' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_listing_tab_top') => array('href' => BX_DOL_URL_ROOT . 'index.php?listing=top', 'active' => 'top' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_listing_tab_popular') => array('href' => BX_DOL_URL_ROOT . 'index.php?listing=popular', 'active' => 'popular' == $sBrowseMode, 'dynamic' => true),
            )
        );
    }

    /**
     * Profile block with user's listing
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlock ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxListingPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']);
        $o->sUrlStart .= (false === strpos($o->sUrlStart, '?') ? '?' : '&');
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_listing_perpage_profile'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }

    /**
     * Profile block with user's listing
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileEmployersBlock ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxListingPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']);
        $o->sUrlStart .= (false === strpos($o->sUrlStart, '?') ? '?' : '&');
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'employers', 
            $this->_oDb->getParam('modzzz_listing_perpage_profile'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }

     /**
     * Account block with different events
     * @return html to display area listings in account page a block
     */  
    function serviceAccountAreaBlock () {

        if (!$this->_oDb->isAnyPublicContent())
            return '';

		$aProfileInfo = getProfileInfo($this->_iProfileId);
		$sValue = ($aProfileInfo['zip']) ? $aProfileInfo['zip'] : $aProfileInfo['City'];
 
		if(!$sValue) return;
			 
        bx_import ('PageMain', $this->_aModule);
        $o = new BxListingPageMain ($this);        
        $o->sUrlStart = BX_DOL_URL_ROOT . '?';
 
        return $o->ajaxBrowse(
            'local',
            $this->_oDb->getParam('modzzz_listing_perpage_accountpage'),
			array(),
			$sValue
        );
    }

    /**
     * Account block with different events
     * @return html to display member listings in account page a block
     */ 
    function serviceAccountPageBlock () {
  
        $aProfile = getProfileInfo($this->_iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxListingPageMain ($this);        
        $o->sUrlStart = $GLOBALS['site']['url'] . 'member.php?';
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_listing_perpage_profile'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }


    function serviceGetMemberMenuItem () {
        parent::_serviceGetMemberMenuItem (_t('_modzzz_listing'), _t('_modzzz_listing'), 'briefcase');
    }
 
    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        return parent::_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_modzzz_listing_spy_post',
            'change' => '_modzzz_listing_spy_post_change', 
            'join' => '_modzzz_listing_spy_join',
            'work' => '_modzzz_listing_spy_work',
            'rate' => '_modzzz_listing_spy_rate',
            'commentPost' => '_modzzz_listing_spy_comment',
        ));
    }

    function serviceGetSubscriptionParams ($sAction, $iEntryId) {

        $a = array (
            'change' => _t('_modzzz_listing_sbs_change'),
            'commentPost' => _t('_modzzz_listing_sbs_comment'),
            'rate' => _t('_modzzz_listing_sbs_rate'), 
            'join' => _t('_modzzz_listing_sbs_join'), 
            'work' => _t('_modzzz_listing_sbs_work'), 
        );

        return parent::_serviceGetSubscriptionParams ($sAction, $iEntryId, $a);
    }

    // ================================== admin actions

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
			'name' => _t('_modzzz_listing_packages'),
			'bx_repeat:items' => $aPackage,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages/'
		));


		$aPackage = $this->_oDb->getRow("SELECT * FROM `" . $this->_oDb->_sPrefix . "packages` WHERE  `id` = '$iPackage'");
		  
		$sFormName = 'packages_form';
  
	    if($iPackage){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_listing_categ_btn_edit'),
				'action_delete' => _t('_modzzz_listing_categ_btn_delete'), 
				'action_add' => _t('_modzzz_listing_categ_btn_add')  
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_listing_categ_btn_save')
			), 'pathes', false);	 
	    }
 
		$aVars = array(
 			'id'=> $aPackage['id'],  
			'name' => htmlspecialchars($aPackage['name']), 
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
			'coupon_no_select' => $aPackage['coupons'] ? '' : "selected='selected'",
			'coupon_yes_select' => $aPackage['coupons'] ? "selected='selected'" : '',
				
			'banner_no_select' => $aPackage['banner'] ? '' : "selected='selected'",
			'banner_yes_select' => $aPackage['banner'] ? "selected='selected'" : '',
	 
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
 				'action_add' => _t('_modzzz_listing_categ_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_packages',$aVars);
		}

		return $sContent;
	}
 
    function actionAdministration ($sUrl = '',$sParam1='',$sParam2='') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();
  
        $aMenu = array(
            'pending_approval' => array(
                'title' => _t('_modzzz_listing_menu_admin_pending_approval'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_approval', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false)),
            ),
            'admin_entries' => array(
                'title' => _t('_modzzz_listing_menu_admin_entries'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ),   
            'categories' => array(
                'title' => _t('_modzzz_listing_menu_admin_manage_categories'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/categories',
                '_func' => array ('name' => 'actionAdministrationCategories', 'params' => array($sParam1)),
            ),
            'subcategories' => array(
                'title' => _t('_modzzz_listing_menu_admin_manage_subcategories'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/subcategories',
                '_func' => array ('name' => 'actionAdministrationSubCategories', 'params' => array($sParam1,$sParam2)),
            ), 
			'invoices' => array(
                'title' => _t('_modzzz_listing_menu_manage_invoices'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/invoices',
                '_func' => array ('name' => 'actionAdministrationInvoices', 'params' => array($sParam1)),
            ), 			
			'orders' => array(
                'title' => _t('_modzzz_listing_menu_manage_orders'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/orders',
                '_func' => array ('name' => 'actionAdministrationOrders', 'params' => array($sParam1)),
            ),
			'packages' => array(
                'title' => _t('_modzzz_listing_menu_manage_packages'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages',
                '_func' => array ('name' => 'actionAdministrationPackages', 'params' => array($sParam1)),
            ),  
			'claims' => array(
                'title' => _t('_modzzz_listing_menu_manage_claims'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/claims',
                '_func' => array ('name' => 'actionAdministrationClaims', 'params' => array($sParam1)),
            ), 
            'create' => array(
                'title' => _t('_modzzz_listing_menu_admin_add_entry'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
            'settings' => array(
                'title' => _t('_modzzz_listing_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'pending_approval';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_listing_admin_block_administration'), $aMenu);
  
        $this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css'));
 
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_listing_page_title_administration'));
    }

    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('Listing');
    }
 
	//[begin] - sub category management

	function deleteCategoryById($iId, $sType='')
	{  
		 $iId = (int)$iId;
	
		 $aItems = $this->_oDb->getItemsByCategoryId($iId, $sType);
		 foreach($aItems as $aEachItem){ 
			  if ($this->_oDb->deleteEntryByIdAndOwner($aEachItem['id'], $this->_iProfileId, 0)) {
				   $this->onEventDeleted ($aEachItem['id']);
			  }
		 }
  
		 return $this->_oDb->deleteCategoryById($iId); 
	} 
 
    function actionAdministrationSubCategories ($sParam1='', $sParam2='') 
    {
		$sAddBlock = $this->getAddSubCategoryForm();

		$sEditBlock = $this->getEditSubCategoryForm();

		$sTransferBlock = $this->getTransferSubCategoryForm();

		$sDeleteBlock = $this->getDeleteSubCategoryForm($sParam1,$sParam2);

		return $sAddBlock . $sEditBlock . $sTransferBlock . $sDeleteBlock;
	}
 
	function getDeleteSubCategoryForm($iLanguageId='', $iParent='')
	{ 
		// check actions
		if(bx_get('pathes') !== false) {
			$aPathes = bx_get('pathes');

			if(is_array($aPathes) && !empty($aPathes))
				foreach($_POST['pathes'] as $sValue) {
					$iId = (int)process_db_input($sValue, BX_TAGS_STRIP); 
					if (bx_get('action_disable') !== false){
						//$this->_oDb->disableCategory((int)$iId);
						$sActionMessage = MsgBox(_t('_modzzz_listing_msg_categ_disable_success'));
					}else if(bx_get('action_delete') !== false){
						$this->deleteCategoryById($iId, 'child');
						$sActionMessage = MsgBox(_t('_modzzz_listing_msg_categ_delete_success'));
					}
				}
		}
 
		$aCategory = ($iLanguageId) ? $this->_oDb->getFormCategoryArray($iLanguageId) : array();

		$aLanguage = $this->_oDb->getLanguages();
		$aLanguage = array(''=>_t('_Select')) + $aLanguage;

		$sAjaxUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=langcat&speak=';
		
		$sAdminUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/subcategories/';

		$aForm = array(

			'form_attrs' => array(
				'name'     => 'delete_form',
				'action'   => $_SERVER['REQUEST_URI'],
				'method'   => 'post', 
			),

			'params' => array (
				'db' => array(  
					'submit_name' => 'submit_delete_form'
				),
			),

			'inputs' => array(
				'header_delete' => array(
					'type' => 'block_header',
					'name' => 'header_delete', 
					'caption' => _t('_modzzz_listing_form_header_delete_subcategory'),  
				), 
				'language' => array(
					'type' => 'select',
					'name' => 'language',
					'required' => true,
					'values' => $aLanguage, 
					'value' => $iLanguageId,  
					'caption' => _t('_modzzz_listing_form_caption_language'), 
					'attrs' => array(
						'onchange' => "getHtmlData('delete_parent','$sAjaxUrl'+this.value)", 
					),  
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_language'),
                    ),  
					'db' => array(
						'pass' => 'Xss'
					),
				),
				'parent' => array(
					'type' => 'select',
					'name' => 'parent',
					'required' => true, 
					'values' => $aCategory,  
					'value' => $iParent,  
					'caption' => _t('_modzzz_listing_form_caption_parent_category'), 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_category'),
                    ),  
					'attrs' => array(
                        'id' => 'delete_parent',
						'onchange' => "window.location='$sAdminUrl'+document.delete_form.language.value+'/'+this.value", 
					),	 
					'db' => array(
						'pass' => 'Xss'
					),
				), 
			)
		);

		$oForm = new BxTemplFormView($aForm); 
	    $oForm->initChecker(array('language'=>$iLanguageId,'parent'=>$iParent));

		$sTopControls = $oForm->getCode();
  
  		$sTopControls .= '<br><br><span  class="bx-def-font-large" style="color:red">'._t('_modzzz_listing_msg_warning_delete').'</span>';

		$aCategories = ($iParent) ? $this->_oDb->getSubCategories($iParent) : array();
		if(!empty($aCategories)) {
			$mixedTmplItems = array();
			foreach($aCategories as $aCategory)
				$mixedTmplItems[] = array(
					'name' => $aCategory['name'],
					'value' => $aCategory['id'],
					'title'=> $aCategory['name'] .' ('.$aCategory['cnt'].' '._t('_modzzz_listing_items').')',
				);
		} else
			$mixedTmplItems = MsgBox(_t('_Empty'));

		$sFormName = 'categories_form';
		$sControls = $sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
			/*'action_disable' => _t('_categ_btn_disable'),*/
			'action_delete' => _t('_categ_btn_delete')
		), 'pathes');
 
		$sContent .= $GLOBALS['oAdmTemplate']->parseHtmlByName('categories_list.html', array(
			'top_controls' => $sActionMessage . $sTopControls,
			'form_name' => $sFormName,
			'bx_repeat:items' => $mixedTmplItems,
			'controls' => $sControls
		));

		return $sContent;
	}
 
	function getAddSubCategoryForm()
	{ 
		$aLanguage = $this->_oDb->getLanguages();
 		$aLanguage = array(''=>_t('_Select')) + $aLanguage;

		if($_POST['language']){
			$aCategories = $this->_oDb->getFormCategoryArray($_POST['language']);
			$aCategories = array(''=>_t('_Select')) + $aCategories;
		}else{
			$aCategories = array();
		}

		$sAjaxLangUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=langcat&speak=';

		$aForm = array(

			'form_attrs' => array(
				'name'     => 'subcategory_form',
				'action'   => $_SERVER['REQUEST_URI'],
				'method'   => 'post',
				'enctype' => 'multipart/form-data',
			),

			'params' => array (
				'db' => array(
					'table' => 'modzzz_listing_categ',
                    'uri' => 'uri',
                    'uri_title' => 'name', 
					'submit_name' => 'submit_add_form'
				),
			),

			'inputs' => array(
				'header_add' => array(
					'type' => 'block_header',
					'name' => 'header_add', 
					'caption' => _t('_modzzz_listing_form_header_add_category'),  
				), 
				'language' => array(
					'type' => 'select',
					'name' => 'language',
					'required' => true,
					'values' => $aLanguage, 
					'caption' => _t('_modzzz_listing_form_caption_language'), 
					'attrs' => array(
						'onchange' => "getHtmlData('add_parent','$sAjaxLangUrl'+this.value)", 
					),  
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_language'),
                    ),  
					'db' => array(
						'pass' => 'Xss'
					),
				),
				'parent' => array(
					'type' => 'select',
					'name' => 'parent',
					'required' => true, 
					'values' => $aCategories, 
					'caption' => _t('_modzzz_listing_form_caption_parent_category'), 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_category'),
                    ),  
					'attrs' => array(
                        'id' => 'add_parent',
					),	 
					'db' => array(
						'pass' => 'Xss'
					),
				),
				'public' => array(
					'type' => 'select',
					'name' => 'public',
					'required' => true,
					'values' => array(1=>_t('_modzzz_listing_yes'), 0=>_t('_modzzz_listing_no')), 
					'caption' => _t('_modzzz_listing_form_caption_public'),
					'info' => _t('_modzzz_listing_form_info_public'),  
					'db' => array(
						'pass' => 'Xss'
					),
				),
				'name' => array(
					'type' => 'text',
					'name' => 'name', 
					'caption' => _t('_categ_form_name'),
					'required' => true,
					'checker' => array (
						'func' => 'length',
						'params' => array(3, 100),
						'error' => _t('_categ_form_field_name_err'),
					),
					'db' => array(
						'pass' => 'Xss'
					) 
				), 
				'submit' => array (
					'type' => 'submit',
					'name' => 'submit_add_form',
					'value' => _t('_modzzz_listing_form_caption_add_new_subcategory'),
					'colspan' => false,
				),
			)
		);

		$oForm = new BxTemplFormView($aForm); 
		$oForm->initChecker();
		$sResult = '';

		if ($oForm->isSubmittedAndValid()) {
			 
			if ($this->_oDb->checkIfCategoryExists($oForm->getCleanValue('name'), $oForm->getCleanValue('parent')) == 0) {
	 
				$aValsAdd = array ( 
					$this->_oDb->_sFieldUri => $oForm->generateUri() 
				);   
				$oForm->insert($aValsAdd);
 
				$sActionMessage = MsgBox(_t('_modzzz_listing_msg_categ_add_success'));
			} else
				$sActionMessage = sprintf(_t('_categ_exist_err'), $oForm->getCleanValue('name'));
		}

		return $sActionMessage .
			$GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oForm->getCode()));
	}
 
	function getEditSubCategoryForm()
	{ 
		$sAjaxCatUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=admincat&id=';
 		$sAjaxLangUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=langcat&speak=';
		$sAjaxPublicUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=public&id=';

		$aLanguage = $this->_oDb->getLanguages();
 		$aLanguage = array(''=>_t('_Select')) + $aLanguage;
 
		if($_POST['language']){
			$aCategories = $this->_oDb->getFormCategoryArray($_POST['language']);
			$aCategories = array(''=>_t('_Select')) + $aCategories;
		}else{
			$aCategories = array();
		}

		if($_POST['exist_category']){
			$aExistSubCategories = $this->_oDb->getFormSubCategoryArray($_POST['exist_category']);
			$aExistSubCategories = array(''=>_t('_Select')) + $aExistSubCategories;
		}else{
			$aExistSubCategories = array();
		}
 
		$aForm = array(

			'form_attrs' => array(
				'name'     => 'ceditfrm',
				'action'   => $_SERVER['REQUEST_URI'],
				'method'   => 'post',
				'enctype' => 'multipart/form-data',
			),

			'params' => array (
				'db' => array( 
					'table' => 'modzzz_listing_categ',
                    'key' => 'id',
					'submit_name' => 'submit_edit_form'
				),
			),

			'inputs' => array(
				'header_edit' => array(
					'type' => 'block_header',
					'name' => 'header_edit', 
					'caption' => _t('_modzzz_listing_form_header_edit_category'),  
				), 
				'language' => array(
					'type' => 'select',
					'name' => 'language',
					'required' => true,
					'values' => $aLanguage, 
					'caption' => _t('_modzzz_listing_form_caption_language'), 
					'attrs' => array(
						'onchange' => "getHtmlData('exist_category','$sAjaxLangUrl'+this.value);getHtmlData('public','$sAjaxPublicUrl');document.ceditfrm.name.value=''", 
					),  
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_language'),
                    ),  
				), 
				'exist_category' => array(
					'type' => 'select',
					'name' => 'exist_category',
					'required' => true,
					'values' => $aCategories, 
					'caption' => _t('_modzzz_listing_form_caption_exist_category'), 
					'attrs' => array(
						'id' => 'exist_category',
						'onchange' => "getHtmlData('exist_subcategory','$sAjaxCatUrl'+this.value);document.ceditfrm.name.value=''", 
					), 			
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_category'),
                    ),    
				),
 				'exist_subcategory' => array(
					'type' => 'select',
					'name' => 'exist_subcategory',
					'caption' => _t('_modzzz_listing_form_caption_exist_subcategory'), 
					'required' => true, 
					'values' => $aExistSubCategories, 
					'attrs' => array(
						'id' => 'exist_subcategory',
						'onchange' =>  "getHtmlData('public','$sAjaxPublicUrl'+this.value);document.ceditfrm.name.value=this.options[this.selectedIndex].text", 
					),  
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_subcategory'),
                    ),  
				), 
				'public' => array(
					'type' => 'select',
					'name' => 'public',
					'required' => true, 
					'caption' => _t('_modzzz_listing_form_caption_public'),
					'info' => _t('_modzzz_listing_form_info_public'),   
					'attrs' => array(
						'id' => 'public',
 					),  
					'db' => array(
						'pass' => 'Xss'
					) 
				), 
				'name' => array(
					'type' => 'text',
					'name' => 'name',
					'caption' => _t('_modzzz_listing_form_caption_new_name'),
					'required' => true,
					'checker' => array (
						'func' => 'length',
						'params' => array(3, 100),
						'error' => _t('_categ_form_field_name_err'),
					),
					'db' => array(
						'pass' => 'Xss'
					) 
				),  
				'submit' => array (
					'type' => 'submit',
					'name' => 'submit_edit_form',
					'value' => _t('_modzzz_listing_form_caption_update_category'),
					'colspan' => false,
				),
			)
		);

		$oForm = new BxTemplFormView($aForm); 
		$oForm->initChecker();
		$sResult = '';

		if ($oForm->isSubmittedAndValid()) {
			  
			$iOldCategId = (int)$oForm->getCleanValue('exist_subcategory'); 
 
			$aValsAdd = array ();  
			$oForm->update($iOldCategId, $aValsAdd);

			$oForm->aInputs['public']['values'] = array(1=>_t('_modzzz_listing_yes'), 0=>_t('_modzzz_listing_no'));
			$oForm->aInputs['exist_category']['values'] = $this->_oDb->getFormCategoryArray($_POST['language']);
			$oForm->aInputs['exist_subcategory']['values'] = $this->_oDb->getFormSubCategoryArray($_POST['exist_category']);

			$sActionMessage = MsgBox(_t('_modzzz_listing_msg_subcateg_update_success')); 
		} 

		return 
			$GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $sActionMessage . $oForm->getCode()));
	}
  
	function getTransferSubCategoryForm()
	{ 
		$sAjaxCatUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=admincat&id=';
 		$sAjaxLangUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=langcat&speak=';

		$aLanguage = $this->_oDb->getLanguages();
 		$aLanguage = array(''=>_t('_Select')) + $aLanguage;
 
		if($_POST['language']){
			$aCategories = $this->_oDb->getFormCategoryArray($_POST['language']);
			$aCategories = array(''=>_t('_Select')) + $aCategories;
		}else{
			$aCategories = array();
		}

		if($_POST['from_category']){
			$aFromSubCategories = $this->_oDb->getFormSubCategoryArray($_POST['from_category']);
			$aFromSubCategories = array(''=>_t('_Select')) + $aFromSubCategories;
		}else{
			$aFromSubCategories = array();
		}

		if($_POST['to_category']){
			$aToSubCategories = $this->_oDb->getFormSubCategoryArray($_POST['to_category']);
			$aToSubCategories = array(''=>_t('_Select')) + $aToSubCategories;
		}else{
			$aToSubCategories = array();
		}

		$aForm = array(

			'form_attrs' => array(
				'name'     => 'transfer_form',
				'action'   => $_SERVER['REQUEST_URI'],
				'method'   => 'post',
				'enctype' => 'multipart/form-data',
			),

			'params' => array (
				'db' => array( 
					'submit_name' => 'submit_transfer_form'
				),
			),

			'inputs' => array(
				'header_transfer' => array(
					'type' => 'block_header',
					'name' => 'header_transfer', 
					'caption' => _t('_modzzz_listing_form_header_transfer'),  
				), 
				'language' => array(
					'type' => 'select',
					'name' => 'language',
					'required' => true,
					'values' => $aLanguage, 
					'caption' => _t('_modzzz_listing_form_caption_language'), 
					'attrs' => array(
						'onchange' => "getHtmlData('from_category','$sAjaxLangUrl'+this.value); getHtmlData('to_category','$sAjaxLangUrl'+this.value)", 
					),  
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_language'),
                    ),  
					'db' => array(
						'pass' => 'Xss'
					),
				), 
				'from_category' => array(
					'type' => 'select',
					'name' => 'from_category',
					'required' => true,
					'values' => $aCategories, 
					'caption' => _t('_modzzz_listing_form_caption_from_category'), 
					'attrs' => array(
						'id' => 'from_category',
						'onchange' => "getHtmlData('from_subcategory','$sAjaxCatUrl'+this.value)", 
					), 			
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_category'),
                    ),   
					'db' => array(
						'pass' => 'Xss'
					),
				),
 				'from_subcategory' => array(
					'type' => 'select',
					'name' => 'from_subcategory',
					'caption' => _t('_modzzz_listing_form_caption_from_subcategory'), 
					'required' => true, 
					'values' => $aFromSubCategories,
					'attrs' => array(
                        'id' => 'from_subcategory',
					),
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_subcategory'),
                    ), 
					'db' => array(
						'pass' => 'Xss'
					),
				), 

 				'to_category' => array(
					'type' => 'select',
					'name' => 'to_category',
					'required' => true,
					'values' => $aCategories, 
					'caption' => _t('_modzzz_listing_form_caption_to_category'),
					'attrs' => array(
						'id' => 'to_category', 
						'onchange' => "getHtmlData('to_subcategory','$sAjaxCatUrl'+this.value)", 
					), 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_category'),
                    ), 
					'db' => array(
						'pass' => 'Xss'
					),
				),  
 				'to_subcategory' => array(
					'type' => 'select',
					'name' => 'subcategory',
					'caption' => _t('_modzzz_listing_form_caption_to_subcategory'), 
					'required' => true, 
					'values' => $aToSubCategories, 
					'attrs' => array(
                        'id' => 'to_subcategory',
					),
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_subcategory'),
                    ), 
					'db' => array(
						'pass' => 'Xss'
					),
				),   
				'submit' => array (
					'type' => 'submit',
					'name' => 'submit_transfer_form',
					'value' => _t('_Submit'),
					'colspan' => false,
				),
			)
		);

		$oForm = new BxTemplFormView($aForm); 
		$oForm->initChecker();
		$sResult = '';

		if ($oForm->isSubmittedAndValid()) {
			 
			if ($oForm->getCleanValue('from_subcategory') == $oForm->getCleanValue('to_subcategory')) { 
				$sActionMessage = MsgBox(_t('_modzzz_listing_form_err_transfer_category')); 
			}else{ 
				$this->_oDb->transferSubCategory($oForm->getCleanValue('from_subcategory'), $oForm->getCleanValue('to_subcategory'));
 
				$sActionMessage = MsgBox(_t('_modzzz_listing_msg_categ_transfer_success')); 
			}  
		}

		return 
			$GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $sActionMessage . $oForm->getCode()));
	}
	//[end] - sub category management




	//[begin] - category management
    function actionAdministrationCategories ($sParam1='') {

		$sAddBlock = $this->getAddCategoryForm();

		$sEditBlock = $this->getEditCategoryForm();

		$sTransferBlock = $this->getTransferCategoryForm();

		$sDeleteBlock = $this->getDeleteCategoryForm();

		return $sAddBlock . $sEditBlock . $sTransferBlock . $sDeleteBlock;
	}
 
	function getAddCategoryForm()
	{

		$aLanguage = $this->_oDb->getLanguages();
	 
		$aForm = array(

			'form_attrs' => array(
				'name'     => 'category_add_form',
				'action'   => $_SERVER['REQUEST_URI'],
				'method'   => 'post',
				'enctype' => 'multipart/form-data',
			),

			'params' => array (
				'db' => array(
					'table' => 'modzzz_listing_categ',
                    'uri' => 'uri',
                    'uri_title' => 'name', 
					'submit_name' => 'submit_add_form'
				),
			),

			'inputs' => array(
				'header_add' => array(
					'type' => 'block_header',
					'name' => 'header_add', 
					'caption' => _t('_modzzz_listing_form_header_add_category'),  
				), 
				'language' => array(
					'type' => 'select',
					'name' => 'language',
					'required' => true,
					'values' => $aLanguage, 
					'caption' => _t('_modzzz_listing_form_caption_language'),
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_language'),
                    ),  
					'db' => array(
						'pass' => 'Xss'
					),
				),
				'public' => array(
					'type' => 'select',
					'name' => 'public',
					'required' => true,
					'values' => array(1=>_t('_modzzz_listing_yes'), 0=>_t('_modzzz_listing_no')), 
					'caption' => _t('_modzzz_listing_form_caption_public'),
					'info' => _t('_modzzz_listing_form_info_public'),  
					'db' => array(
						'pass' => 'Xss'
					),
				),
				'name' => array(
					'type' => 'text',
					'name' => 'name',
					'caption' => _t('_categ_form_name'),
					'required' => true,
					'checker' => array (
						'func' => 'length',
						'params' => array(3, 100),
						'error' => _t('_categ_form_field_name_err'),
					),
					'db' => array(
						'pass' => 'Xss'
					) 
				), 
				'submit' => array (
					'type' => 'submit',
					'name' => 'submit_add_form',
					'value' => _t('_modzzz_listing_form_caption_add_new_category'),
					'colspan' => false,
				),
			)
		);

		$oForm = new BxTemplFormView($aForm); 
		$oForm->initChecker();
 
		if ($oForm->isSubmittedAndValid()) {
			 
			if ($this->_oDb->checkIfCategoryExists($oForm->getCleanValue('name')) == 0) {
	 
				$aValsAdd = array ( 
					$this->_oDb->_sFieldUri => $oForm->generateUri() 
				); 

				$oForm->insert($aValsAdd);
 
				$sActionMessage = MsgBox(_t('_modzzz_listing_msg_categ_add_success'));
			} else
				$sActionMessage = MsgBox(sprintf(_t('_categ_exist_err'), $oForm->getCleanValue('name')));
		}

		return $sActionMessage .
			$GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oForm->getCode()));
	}
 
	function getEditCategoryForm()
	{
 
		$sAjaxPublicUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=public&id=';
 		$sAjaxLangUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=langcat&speak=';

		$aLanguage = $this->_oDb->getLanguages();
 		$aLanguage = array(''=>_t('_Select')) + $aLanguage;
  
		if($_POST['language']){
			$aCategories = $this->_oDb->getFormCategoryArray($_POST['language']);
			$aCategories = array(''=>_t('_Select')) + $aCategories;
		}else{
			$aCategories = array();
		}
 
		$aForm = array(

			'form_attrs' => array(
				'name'     => 'ceditfrm',
				'action'   => $_SERVER['REQUEST_URI'],
				'method'   => 'post',
				'enctype' => 'multipart/form-data',
			),

			'params' => array (
				'db' => array( 
					'table' => 'modzzz_listing_categ',
                    'key' => 'id',
					'submit_name' => 'submit_edit_form'
				),
			),

			'inputs' => array(
				'header_edit' => array(
					'type' => 'block_header',
					'name' => 'header_edit', 
					'caption' => _t('_modzzz_listing_form_header_edit_category'),  
				), 
				'language' => array(
					'type' => 'select',
					'name' => 'language',
					'values' => $aLanguage, 
					'caption' => _t('_modzzz_listing_form_caption_language'), 
					'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('exist_category','$sAjaxLangUrl'+this.value);getHtmlData('public','$sAjaxPublicUrl');document.ceditfrm.name.value=''", 
					),   
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_language'),
                    ),    
				), 
				'exist_category' => array(
					'type' => 'select',
					'name' => 'exist_category',
					'required' => true,
					'values' => $aCategories, 
					'caption' => _t('_modzzz_listing_form_caption_exist_category'), 
					'attrs' => array(
						'id' => 'exist_category',
						'onchange' =>  "getHtmlData('public','$sAjaxPublicUrl'+this.value);document.ceditfrm.name.value=this.options[this.selectedIndex].text", 
					), 			
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_category'),
                    ),    
				),  
				'public' => array(
					'type' => 'select',
					'name' => 'public',
					'required' => true, 
					'caption' => _t('_modzzz_listing_form_caption_public'),
					'info' => _t('_modzzz_listing_form_info_public'),   
					'attrs' => array(
						'id' => 'public',
 					),  
					'db' => array(
						'pass' => 'Xss'
					) 
				), 
				'name' => array(
					'type' => 'text',
					'name' => 'name',
					'caption' => _t('_modzzz_listing_form_caption_new_name'),
					'required' => true,
					'checker' => array (
						'func' => 'length',
						'params' => array(3, 100),
						'error' => _t('_categ_form_field_name_err'),
					),
					'db' => array(
						'pass' => 'Xss'
					) 
				), 
				'submit' => array (
					'type' => 'submit',
					'name' => 'submit_edit_form',
					'value' => _t('_modzzz_listing_form_caption_update_category'),
					'colspan' => false,
				),
			)
		);

		$oForm = new BxTemplFormView($aForm); 
		$oForm->initChecker(); 

		if ($oForm->isSubmittedAndValid()) {
			  
			$iOldCategId = (int)$oForm->getCleanValue('exist_category'); 
 
			$aValsAdd = array ();  
			$oForm->update($iOldCategId, $aValsAdd);

			$oForm->aInputs['public']['values'] = array(1=>_t('_modzzz_listing_yes'), 0=>_t('_modzzz_listing_no'));
			$oForm->aInputs['exist_category']['values'] = $this->_oDb->getFormCategoryArray($_POST['language']);

			$sActionMessage = MsgBox(_t('_modzzz_listing_msg_categ_update_success')); 
		} 

		return $sActionMessage .
			$GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $oForm->getCode()));
	}
 
	function getTransferCategoryForm()
	{

		$sAjaxCatUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=admincat&id=';
 		$sAjaxLangUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=langcat&speak=';

		$aLanguage = $this->_oDb->getLanguages();
 		$aLanguage = array(''=>_t('_Select')) + $aLanguage;
  
		if($_POST['language']){
			$aCategories = $this->_oDb->getFormCategoryArray($_POST['language']);
			$aCategories = array(''=>_t('_Select')) + $aCategories;
		}else{
			$aCategories = array();
		}
  
		if($_POST['to_category']){
			$aToSubCategories = $this->_oDb->getFormSubCategoryArray($_POST['to_category']);
			$aToSubCategories = array(''=>_t('_Select')) + $aToSubCategories;
		}else{
			$aToSubCategories = array();
		}
 
		$aForm = array(

			'form_attrs' => array(
				'name'     => 'transfer_form',
				'action'   => $_SERVER['REQUEST_URI'],
				'method'   => 'post',
				'enctype' => 'multipart/form-data',
			),

			'params' => array (
				'db' => array( 
					'submit_name' => 'submit_transfer_form'
				),
			),

			'inputs' => array(
				'header_transfer' => array(
					'type' => 'block_header',
					'name' => 'header_transfer', 
					'caption' => _t('_modzzz_listing_form_header_transfer'),  
				), 
				'language' => array(
					'type' => 'select',
					'name' => 'language',
					'values' => $aLanguage, 
					'caption' => _t('_modzzz_listing_form_caption_language'), 
					'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('from_category','$sAjaxLangUrl'+this.value); getHtmlData('to_category','$sAjaxLangUrl'+this.value)", 
					),   
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_language'),
                    ),  
					'db' => array(
						'pass' => 'Xss'
					),
				), 
				'from_category' => array(
					'type' => 'select',
					'name' => 'from_category',
					'required' => true,
					'values' => $aCategories, 
					'caption' => _t('_modzzz_listing_form_caption_from_category'), 
					'attrs' => array(
						'id' => 'from_category',
						'onchange' => "getHtmlData('from_subcategory','$sAjaxCatUrl'+this.value)", 
					), 			
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_category'),
                    ),   
					'db' => array(
						'pass' => 'Xss'
					),
				), 
 				'to_category' => array(
					'type' => 'select',
					'name' => 'to_category',
					'required' => true,
					'values' => $aCategories, 
					'caption' => _t('_modzzz_listing_form_caption_to_category'),
					'attrs' => array(
						'id' => 'to_category', 
						'onchange' => "getHtmlData('to_subcategory','$sAjaxCatUrl'+this.value)", 
					), 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,10),
                        'error' => _t ('_modzzz_listing_form_err_select_category'),
                    ), 
					'db' => array(
						'pass' => 'Xss'
					),
				),  
 				'to_subcategory' => array(
					'type' => 'select',
					'name' => 'subcategory',
					'caption' => _t('_modzzz_listing_form_caption_to_subcategory'), 
					'required' => false, 
					'values' => $aToSubCategories, 
					'attrs' => array(
                        'id' => 'to_subcategory',
					), 
					'db' => array(
						'pass' => 'Xss'
					),
				),   
				'submit' => array (
					'type' => 'submit',
					'name' => 'submit_transfer_form',
					'value' => _t('_Submit'),
					'colspan' => false,
				),
			)
		);

		$oForm = new BxTemplFormView($aForm); 
		$oForm->initChecker();
 
		if ($oForm->isSubmittedAndValid()) {
			 
			if ($oForm->getCleanValue('from_category') == $oForm->getCleanValue('to_category')) {

				$sActionMessage = MsgBox(_t('_modzzz_listing_form_err_transfer_category'));
				$aValsAdd = array ();
			}else{ 
				$this->_oDb->transferParentCategory($oForm->getCleanValue('from_category'), $oForm->getCleanValue('to_category'), $oForm->getCleanValue('to_subcategory'));
 
				$sActionMessage = MsgBox(_t('_modzzz_listing_msg_categ_transfer_success'));
			}  
		}

		return 
			$GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $sActionMessage . $oForm->getCode()));
	}

	function getDeleteCategoryForm()
	{
 
		// check actions
		if(bx_get('pathes') !== false) {
			$aPathes = bx_get('pathes');

			if(is_array($aPathes) && !empty($aPathes))
				foreach($_POST['pathes'] as $sValue) {
					$iId = (int)process_db_input($sValue, BX_TAGS_STRIP); 
					if (bx_get('action_disable') !== false){
						//$this->_oDb->disableCategory((int)$iId);
						$sActionMessage = MsgBox(_t('_modzzz_listing_msg_categ_disable_success'));
					}else if(bx_get('action_delete') !== false){
						$this->deleteCategoryById($iId, 'parent');
						$sActionMessage = MsgBox(_t('_modzzz_listing_msg_categ_delete_success'));
					}
				}
		}
 
		$iFirstLanguage = 0;
		$aLanguage = $this->_oDb->getLanguages(); 
		foreach($aLanguage as $iValue=>$sCaption) { 
			$iFirstLanguage = ($iFirstLanguage) ? $iFirstLanguage : $iValue;
			$aItems[] = array(
				'value' => $iValue,
				'caption' => $sCaption,
				'selected' => (bx_get('speak')==$iValue) ? 'selected="selected"' : '',
			);
		}

		$sTopControls = '<span  class="bx-def-font-large" style="color:red">'._t('_modzzz_listing_msg_warning_delete').'</span><br><br>';
		$sTopControls .= $GLOBALS['oAdmTemplate']->parseHtmlByName('categories_list_top_controls.html', array(
			'name' => _t('_modzzz_listing_form_caption_language'),
			'bx_repeat:items' => $aItems,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/categories/?speak='
		));
 
		$iLanguageId = bx_get('speak') ? bx_get('speak') : $iFirstLanguage;

		$aCategories = $this->_oDb->getParentCategories($iLanguageId);
		if(!empty($aCategories)) {
			$mixedTmplItems = array();
			foreach($aCategories as $aCategory)
				$mixedTmplItems[] = array(
					'name' => $aCategory['name'],
					'value' => $aCategory['id'],
					'title'=> $aCategory['name'] .' ('.$aCategory['cnt'].' '._t('_modzzz_listing_items').')',
				);
		} else
			$mixedTmplItems = MsgBox(_t('_Empty'));

		$sFormName = 'categories_form';
		$sControls = $sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
			/*'action_disable' => _t('_categ_btn_disable'),*/
			'action_delete' => _t('_categ_btn_delete')
		), 'pathes');

		$sContent .= $GLOBALS['oAdmTemplate']->parseHtmlByName('categories_list.html', array(
			'top_controls' => $sActionMessage . $sTopControls,
			'form_name' => $sFormName,
			'bx_repeat:items' => $mixedTmplItems,
			'controls' => $sControls
		));

		return $sContent;
	}  
	//[end] - category management
   
    function actionAdministrationManage ($isAdminEntries = false) {
        return parent::_actionAdministrationManage ($isAdminEntries, '_modzzz_listing_admin_delete', '_modzzz_listing_admin_activate');
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
			'action_activate' => '_modzzz_listing_admin_activate',
			'action_delete' => '_modzzz_listing_admin_delete',
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
 			'action_delete' => '_modzzz_listing_admin_delete',
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
		
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }
 
	function actionAdministrationClaims () {

        if ($_POST['action_assign'] && is_array($_POST['entry'])) {
 
            foreach ($_POST['entry'] as $iId) {  
                $this->_oDb->assignClaim($iId, $this->isAdmin()); 
            } 
        }

        if ($_POST['action_delete'] && is_array($_POST['entry'])) {
  
            foreach ($_POST['entry'] as $iId) { 
                $this->_oDb->deleteClaim($iId, $this->isAdmin()); 
            }
        }
 
		$sContent = $this->_manageClaims ('claim', '', true, 'bx_twig_admin_form', array(
 			'action_assign' => '_modzzz_listing_admin_assign',
 			'action_delete' => '_modzzz_listing_admin_delete',
		));
     
        return $sContent;
    }

    function _manageClaims ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 14, $bActionsPanel = true) {

        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = $sMode . '_admin';
 

        if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;

        $sPagination = $sActionsPanel = '';
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
        } elseif (!($sContent = $o->displayClaimResultBlock($sMode))) { 
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


    // ================================== events
 

 
    // ================================== permissions
    
	function isPaidListing($iEntryId, $bCheckAdmin=true){
		$iEntryId = (int)$iEntryId;
         
		if (getParam('modzzz_listing_paid_active')!='on') 
            return false;	
 
		$aEntry = $this->_oDb->getEntryById($iEntryId);
		$sInvoiceNo = $aEntry['invoice_no'];
 
 		$aInvoice = $this->_oDb->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];

		return $this->_oDb->isPaidPackage($iPackageId, $bCheckAdmin);  
	}
 
 	function isPaidPackage($iPackageId, $bCheckAdmin=true){
 
		if(!$this->isAllowedPaidListings($bCheckAdmin))
			return false;

		return $this->_oDb->isPaidPackage($iPackageId);  
	}
 
    function isAllowedPaidListings ($bCheckAdmin=true) {
  
		 if($bCheckAdmin && $this->isAdmin())
			return false;

       return (getParam('modzzz_listing_paid_active')=='on') ? true : false; 
	}

    function actionBrowseFans ($sUri) {
        parent::_actionBrowseFans ($sUri, 'isAllowedViewFans', 'getFansBrowse', $this->_oDb->getParam('modzzz_listing_perpage_browse_fans'), 'browse_fans/', _t('_modzzz_listing_page_title_fans'));
    }
 
    function actionManageFansPopup ($iEntryId) {
        parent::_actionManageFansPopup ($iEntryId, _t('_modzzz_listing_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', BX_LISTING_MAX_FANS);
    }
 
    function onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_listing_fan_remove');
    }

    function onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_listing_fan_become_admin');
    }

    function onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_listing_admin_become_fan');
    }

	function isAllowedViewFans(&$aDataEntry) {
        if ($this->isAdmin())
            return true;
        return $this->_oPrivacy->check('view_fans', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedManageFans($aDataEntry) {
        return $this->isEntryAdmin($aDataEntry);
    }

    function isFan($aDataEntry, $iProfileId = 0, $isConfirmed = true) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isFan ($aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }
 
    function onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_listing_join_request', BX_LISTING_MAX_FANS);
    }

    function onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_listing_join_reject');
    }

    function onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_listing_join_confirm');
    }

    function isAllowedJoin (&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;
        return $this->_oPrivacy->check('join', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedView ($aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_LISTING_VIEW_LISTING, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
 
        // check user group  
        $isAllowed =  $this->_oPrivacy->check('view_listing', $aDataEntry['id'], $this->_iProfileId);   
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
		$aCheck = checkAction($this->_iProfileId, BX_LISTING_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_LISTING_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_LISTING_ADD_LISTING, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedEdit ($aDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;

        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_LISTING_EDIT_ANY_LISTING, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 
  
    function isAllowedMarkAsFeatured ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_LISTING_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }
   
    function isAllowedDelete (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_LISTING_DELETE_ANY_LISTING, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }     
  
    function isAllowedInquire (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_LISTING_MAKE_INQUIRY, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 
	
    function isAllowedClaim (&$aDataEntry, $isPerformAction = false) {
		if ($this->isEntryAdmin($aDataEntry))
            return false;
  
		if ($this->_oDb->isClaimed($aDataEntry['id'])) return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_LISTING_MAKE_CLAIM, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 

    function isAllowedSendInvitation (&$aDataEntry) {
        return true;
    }
 
    function isAllowedShare (&$aDataEntry) {
        return true;
    }
  
    function isAllowedRate(&$aDataEntry) {        
        if ($this->isAdmin())
            return true;
        return $this->_oPrivacy->check('rate', $aDataEntry['id'], $this->_iProfileId);        
    }

    function isAllowedComments(&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        return $this->_oPrivacy->check('comment', $aDataEntry['id'], $this->_iProfileId);
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
        if ($this->isAdmin()) return true;        
        if (!$GLOBALS['logged']['member'] || $aDataEntry['author_id'] != $this->_iProfileId)
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_LISTING_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedManageAdmins($aDataEntry) {
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))
            return true;
        return false;
    }
   
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('permalinks_listing') == 'on'));
		 
        return $bEnabled;
    }
 
    function _defineActions () {
        defineMembershipActions(array('listing post reviews','listing mark as favorite', 'listing purchase', 'listing relist', 'listing extend','listing purchase featured', 'listing view listing', 'listing browse', 'listing search', 'listing add listing', 'listing comments delete and edit', 'listing edit any listing', 'listing delete any listing', 'listing mark as featured', 'listing approve listing', 'listing make claim', 'listing make inquiry', 'listing broadcast message'));
    }
 
    function _browseMy (&$aProfile) {        
        $this->_browseMyModified ($aProfile, _t('_modzzz_listing_page_title_my_listing'));
    } 
	
    function _browseMyModified (&$aProfile, $sTitle) {

        // check access
        if (!$this->_iProfileId) {
            $this->_oTemplate->displayAccessDenied();
            return;
        } 

        $bAjaxMode = isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ? true : false;

        // process delete action 
        if (bx_get('action_delete') && is_array(bx_get('entry'))) {
			$aEntries = bx_get('entry');
            foreach ($aEntries as $iEntryId) {
                $iEntryId = (int)$iEntryId;
                $aDataEntry = $this->_oDb->getEntryById($iEntryId);
                if (!$this->isAllowedDelete($aDataEntry)) 
                    continue;
                if ($this->_oDb->deleteEntryByIdAndOwner($iEntryId, $this->_iProfileId, 0)) {
                    $this->onEventDeleted ($iEntryId);
                }
            }
        }

        // process unjoin action
        if (bx_get('action_unjoin') && is_array(bx_get('entry'))) {
            $aEntries = bx_get('entry');
            foreach ($aEntries as $iEntryId) {
                $iEntryId = (int)$iEntryId;
                $aDataEntry = $this->_oDb->getEntryById($iEntryId);
                if (!$this->isAllowedJoin($aDataEntry))
                    continue;
  
                $this->_oDb->leaveEntry($iEntryId, $this->_iProfileId);
            }
        }
 
        bx_import ('PageMy', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageMy';
        $oPage = new $sClass ($this, $aProfile);

        // manage my data entries
        if ($bAjaxMode && ($_sPrefix . '_my_active') == bx_get('block')) {
            echo $oPage->getBlockCode_My();
            exit;
        }

        // manage my pending data entries 
        if ($bAjaxMode && ($_sPrefix . '_my_pending') == bx_get('block')) {
            echo $oPage->getBlockCode_Pending();
            exit;
        }

	   // manage my expired data entries 
		if ($bAjaxMode && ($_sPrefix . '_my_expired') == bx_get('block')) {
            echo $oPage->getBlockCode_Expired();
            exit;
        }

        // manage my joined data entries
        if ($bAjaxMode && ($this->_sPrefix . '_my_joined') == bx_get('block')) {
            header('Content-type:text/html;charset=utf-8');
            echo $oPage->getBlockCode_Joined();
            exit;
        }
 
        $this->_oTemplate->pageStart();

        // display whole page
        if (!$bAjaxMode)
            echo $oPage->getCode();

        $this->_oTemplate->addJs ('multi_select.js');
		// freddy add  $this->_oTemplate->addJs ('jquery.webForms.js');
		$this->_oTemplate->addJs ('jquery.webForms.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('form.css');
        $this->_oTemplate->addCss ('admin.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode($sTitle, false, false);
    } 
 
    function onEventCreate ($iEntryId, $sStatus, $aDataEntry = array()) {
   
		if ('approved' == $sStatus) {
            $this->reparseTags ($iEntryId);
            //$this->reparseCategories ($iEntryId);
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
        //$this->reparseCategories ($iEntryId);
  
        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_change', array($this->_oConfig->getUri(), $iEntryId));

		$oAlert = new BxDolAlerts($this->_sPrefix, 'change', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
		$oAlert->alert();
    } 

    function getTagLinks($sTagList, $sType = 'tag', $sDivider = ' ') {
        if (strlen($sTagList)) {
            $aTags = explode($sDivider, $sTagList);
            foreach ($aTags as $iKey => $sValue) {
                $sValue   = trim($sValue, ','); 
                $aRes[$sValue] = $sValue;
            }
        }
        return $aRes;
    }

   function parseTags($s)
    {
        return $this->_parseAnything($s, ',', BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/tag/');
    }

    function parseCategories($s)
    {
        return $this->_parseAnything($s, CATEGORIES_DIVIDER, BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/category/');
    }

    function _parseAnything($s, $sDiv, $sLinkStart, $sClassName = '')
    {
        $sRet = '';
        $a = explode ($sDiv, $s);
        $sClass = $sClassName ? 'class="'.$sClassName.'"' : '';
        
        foreach ($a as $sName)
          // freddy modif
		  //  $sRet .= '<a '.$sClass.' href="' . $sLinkStart . urlencode(title2uri($sName)) . '">'.$sName.'</a>&#160';
		  
		    $sRet .= '<span style=" text-transform: capitalize;  background-color: #F9F8F8;  box-shadow: 0px 1px 2px rgb(0 0 0 / 30%);
    padding: 5px;
    display: inline-block;
    font-size: 12px;
    margin-right: 3px;
    margin-bottom: 3px;
    overflow: hidden;
    white-space: normal;
    text-overflow: ellipsis;">'.'<a '.$sClass.' href="' . $sLinkStart . urlencode(title2uri($sName)) . '">'.'<i class="sys-icon sliders"></i>'.' '.$sName.'</a>'.' '.'</span>';
        
        return $sRet;
    }

 
 
 
 
    // ================================== permissions  

    
  
    function isAdmin () {
        return $GLOBALS['logged']['admin'] || $GLOBALS['logged']['moderator'];
    }             

    // ================================== other 
 
    function actionPaypalProcess($iProfileId, $iListingId) {
        $sPostData = '';
        $sPageContent = '';

        $aData = &$_REQUEST;

        if($aData) {
 
			$aDataEntry = $this->_oDb->getEntryById ($iListingId);
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPostPurchase(_t('_modzzz_listing_purchase_failed')); 
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
  
			//if (($aData['receiver_email'] != trim(getParam('modzzz_listing_paypal_email'))) || ($aData['txn_type'] != 'web_accept')) {

			if($aData['txn_type'] != 'web_accept') {
				$this->actionPostPurchase(_t('_modzzz_listing_purchase_failed'));
			}else{ 
				if($this->_oDb->isExistPaypalTransaction($iProfileId, $aData['txn_id'])) { 
					$this -> actionPostPurchase(_t('_modzzz_listing_transaction_completed_already', $sRedirectUrl)); 
				} else {
					if( $this->_oDb->saveTransactionRecord($iProfileId, $iListingId, $aData['txn_id'], 'Paypal Purchase')) { 
						
						$this->_oDb->setItemStatus($iListingId, 'approved'); 
						
						$this->_oDb->setInvoiceStatus($iListingId, 'paid');

						$this->actionPostPurchase(_t('_modzzz_listing_purchase_success', $sRedirectUrl));
					} else {
						$this -> actionPostPurchase(_t('_modzzz_listing_trans_save_failed'));
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

	function initializeCheckout($iListingId, $fTotalCost, $iQuantity=1, $bFeatured=0) {
  
		$aFormData = array(
			'cmd' => '_xclick',
			'amount' => sprintf( "%.2f", (float)$fTotalCost)
		);
    	 
		if($bFeatured){
			$sNotifyUrl = $this->_oConfig->getFeaturedCallbackUrl() . $this->_iProfileId .'/'. $iListingId;
			$sItemDesc = getParam('modzzz_listing_featured_purchase_desc');
 		}else{
			$sNotifyUrl = $this->_oConfig->getPurchaseCallbackUrl() . $this->_iProfileId .'/'. $iListingId;
			$sItemDesc = getParam('modzzz_listing_paypal_item_desc');
		}

		$aDataEntry = $this->_oDb->getEntryById($iListingId);
 		$sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $aFormData = array_merge($aFormData, array(
			'business' => getParam('modzzz_listing_paypal_email'), 
            'item_name' => $sItemDesc,
			'amount' => $fTotalCost, 
            'item_number' => $iListingId,
            'quantity' => $iQuantity,
            'currency_code' => $this->_oConfig->getPurchaseCurrency(),
            'no_note' => '1',
            'no_shipping' => '1',  
			'return' => $this->_oConfig->getReturnUrl(),
			'notify_url' => $sNotifyUrl,  
			'rm' => '1'
        ));
  
    	Redirect($this->_oConfig->getPurchaseBaseUrl(), $aFormData, 'post', "Business Listing");
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
        $this->_oTemplate->pageCode(_t('_modzzz_listing_post_purchase_header')); 
    }
 
    function actionPurchaseFeatured($iListingId, $sTransMessage = '') {
 
        if(! $this->_iProfileId) {
            header('location: ' . BX_DOL_URL_ROOT . 'member.php');
        }

	    if($sTransMessage){
			$sMessageOutput = MsgBox($sTransMessage);  
		}
 
		$iPerDayCost = getParam('modzzz_listing_featured_cost');

		$aDataEntry = $this->_oDb->getEntryById($iListingId);
		$sTitle = $aDataEntry['title'];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_listing_purchase_featured') => '',
        ));

		$aForm = array(
            'form_attrs' => array(
                'name' => 'buy_featured_form',
                'method' => 'post', 
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_featured/'.$iListingId,
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
					'caption'  => _t('_modzzz_listing_form_caption_title'),
                    'content' => $sTitle,
                ), 
                'cost' => array(
                    'type' => 'custom',
                    'name' => 'cost',
					'caption'  => _t('_modzzz_listing_featured_cost_per_day'),
                    'content' => $iPerDayCost .' '. $this->_oConfig->getPurchaseCurrency(),
                ), 
                'status' => array(
                    'type' => 'custom',
                    'name' => 'status',
					'caption'  => _t('_modzzz_listing_featured_status'),
                    'content' => ($aDataEntry['featured']) ? _t('_modzzz_listing_featured_until') . ' '. $this->_oTemplate->filterCustomDate($aDataEntry['featured_expiry_date']) : _t('_modzzz_listing_not_featured'), 
                ), 
                'quantity' => array(
                    'caption'  => _t('_modzzz_listing_caption_num_featured_days'),
                    'type'   => 'text',
                    'name' => 'quantity',
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_modzzz_listing_caption_err_featured_days'),
                    ),
                ),
                'submit' => array(
                    'type'  => 'submit',
                    'value' => ($aDataEntry['featured']) ? _t('_modzzz_listing_extend_featured') : _t('_modzzz_listing_get_featured'),
                    'name'  => 'submit_purchase',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  

        if ($oForm->isSubmittedAndValid() && $oForm->getCleanValue('quantity')) { 

			$fCost =  number_format($iPerDayCost, 2); 
  
			$this->initializeCheckout($iListingId, $fCost, $oForm->getCleanValue('quantity'), true);  
			return;   
        } else {
             echo $sMessageOutput . $oForm->getCode();
        }

        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->pageCode(_t('_modzzz_listing_purchase_featured')); 
    }
 
    function actionPaypalFeaturedProcess($iProfileId, $iListingId) {
        $sPostData = '';
        $sPageContent = '';

        $aData = &$_REQUEST;

        if($aData) {
			$iQuantity = (int)$aData['quantity'];

			$aDataEntry = $this->_oDb->getEntryById($iListingId); 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
 
        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPurchaseFeatured(_t('_modzzz_listing_purchase_failed')); 
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
				$this->actionPurchaseFeatured($iListingId, _t('_modzzz_listing_purchase_failed'));
			}else { 
				$fAmount = $this->_getReceivedAmount($aData);
			
				if($this->_oDb->isExistFeaturedTransaction($iProfileId, $aData['txn_id'])) {
					$this -> actionPurchaseFeatured($iListingId, _t('_modzzz_listing_transaction_completed_already')); 
				} else {
					if( $this->_oDb->saveFeaturedTransactionRecord($iProfileId, $iListingId,  $iQuantity, $fAmount, $aData['txn_id'], 'Paypal Purchase')) {

						$this->_oDb->updateFeaturedEntryExpiration($iListingId, $iQuantity); 
			   
						$this->actionPurchaseFeatured($iListingId, _t('_modzzz_listing_purchase_success',  $iQuantity));
					} else {
						$this -> actionPurchaseFeatured($iListingId, _t('_modzzz_listing_purchase_fail'));
					}
				}
			}
            
        }
    }
 
    function isAllowedPurchaseFeaturedOLD ($aDataEntry, $isPerformAction = false) {
  
		if(getParam("modzzz_listing_buy_featured")!='on')
			return false;
 
		if ($this->isAdmin())
            return false;

		if($aDataEntry['featured'] && !$aDataEntry['featured_expiry_date'])
            return false;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_LISTING_PURCHASE_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    } 

    function actionLocal ($sCountry='', $sState='', $sCategory='') { 
        $this->_oTemplate->pageStart();
        bx_import ('PageLocal', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageLocal';
        $oPage = new $sClass ($this, $sCountry, $sState, $sCategory);
        echo $oPage->getCode();

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
		$sTitle = _t('_modzzz_listing_page_title_local');

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

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
		$sTitle = _t('_modzzz_listing_page_title_local');

		if($sCountry){
			$sTitle .= ' - ' . $this->_oTemplate->getPreListDisplay('Country', $sCountry);
		}
  
		if($sCategory){
			$sTitle .= ' - ' . $sCategory; 
		}

        $this->_oTemplate->pageCode($sTitle, false, false);
    } 
 
    function isMembershipEnabledForImages () {
        return ($this->_isMembershipEnabledFor ('BX_PHOTOS_ADD') && $this->_isMembershipEnabledFor ('BX_LISTING_PHOTOS_ADD'));
    }

    function isMembershipEnabledForVideos () {
        return ($this->_isMembershipEnabledFor ('BX_VIDEOS_ADD') && $this->_isMembershipEnabledFor ('BX_LISTING_VIDEOS_ADD')); 
    }

    function isMembershipEnabledForSounds () {
        return ($this->_isMembershipEnabledFor ('BX_SOUNDS_ADD') && $this->_isMembershipEnabledFor ('BX_LISTING_SOUNDS_ADD'));
    }

    function isMembershipEnabledForFiles () {
        return ($this->_isMembershipEnabledFor ('BX_FILES_ADD') && $this->_isMembershipEnabledFor ('BX_LISTING_FILES_ADD'));
    }
 
    function _isMembershipEnabledFor ($sMembershipActionConstant) { 
        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'listing photos add', 'listing sounds add', 'listing videos add', 'listing files add'));
		if (!defined($sMembershipActionConstant))
			return false;
		$aCheck = checkAction($_COOKIE['memberID'], constant($sMembershipActionConstant));
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }  

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
            'read' => 0,
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
            'read' => $this->isAllowedView ($aDataEntry) ? 1 : 0,
            'post' => $this->isAllowedPostInForum ($aDataEntry, $iMemberId) ? 1 : 0,
        );
  
        return $aTrue;
    }
 
     function isAllowedPostInForum(&$aDataEntry, $iProfileId = -1) {
        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) || $this->_oPrivacy->check('post_in_forum', $aDataEntry['id'], $iProfileId);
    }
 
    function isEntryAdmin111($aDataEntry, $iIdProfile = 0) {
        if (!$iIdProfile)
            $iIdProfile = $this->_iProfileId;
        return ($this->isAdmin() || $aDataEntry['author_id'] == $iIdProfile);
    }

    function isEntryAdmin($aDataEntry, $iProfileId = 0)
    {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;
        return $this->_oDb->isGroupAdmin ($aDataEntry['id'], $iProfileId) && isProfileActive($iProfileId);
    }

	/*relisting and extension */
  
    function isAllowedPremium (&$aDataEntry, $isPerformAction = false) {
  
        if (getParam('modzzz_listing_paid_active')!='on') 
            return false;	
		 
		if($aDataEntry['status'] != 'approved')
            return false;

        if ($this->isPaidListing($aDataEntry['id'], false))
            return false;
       
		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_LISTING_PURCHASE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 

    function actionPremium ($iEntryId) {

        if (!$aDataEntry = $this->_oDb->getEntryById((int)$iEntryId)) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if (!$this->isAllowedPremium($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_listing_page_title_premium') => '',
        ));

        $this->_actionPremium($iEntryId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_modzzz_listing_page_title_premium'));
	}
 
    function _actionPremium ($iEntryId) {
   
		$aPackage = $this->_oDb->getPackageList(true);

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
  
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_modzzz_listing_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_listing_form_err_package'),
                    ),   
                ),   
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_listing_package_desc'),  
                ), 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_listing_continue'),
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
				$this->initializeCheckout($iEntryId, $fPrice);  
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
    /***/
 
    function isAllowedRelist (&$aDataEntry, $isPerformAction = false) {
  
		if($aDataEntry['status'] != 'expired')
            return false;

		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_LISTING_RELIST, $isPerformAction);
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
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_listing_page_title_relist') => '',
        ));

        $this->_actionRelist($iEntryId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_modzzz_listing_page_title_relist'));
	}
 
    function _actionRelist ($iEntryId) {
   
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
  
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_modzzz_listing_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_listing_form_err_package'),
                    ),   
                ),  
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_listing_package_desc'),  
                ),  
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_listing_continue'),
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
				$this->initializeCheckout($iEntryId, $fPrice);  
				return;  
			}else{
                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());

				$sStatus = ((getParam($this->_sPrefix.'_autoapproval') == 'on') || $this->isAdmin()) ? 'approved' : 'pending';

				if($iDays)
					$this->_oDb->updateEntryExpiration($iEntryId, $iDays, true); 

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
        
        if (getParam('modzzz_listing_paid_active')!='on') 
            return false;	
		 
		if($aDataEntry['status'] != 'approved')
            return false;

		if(!$aDataEntry['expiry_date'])
            return false;
  
        if (!$this->isPaidListing($aDataEntry['id'], false))
            return false;
       
		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_LISTING_EXTEND, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 
 
    function actionExtend ($iEntryId) {

        if (!$aDataEntry = $this->_oDb->getEntryById((int)$iEntryId)) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 /*
        if (!$this->isPaidListing($iEntryId)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
*/
        if (!$this->isAllowedExtend($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_listing_page_title_extend') => '',
        ));

        $this->_actionExtend($iEntryId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_modzzz_listing_page_title_extend'));
	}
 
    function _actionExtend ($iEntryId) {
   
		$aEntry = $this->_oDb->getEntryById($iEntryId);
 
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
                    'caption' => _t('_modzzz_listing_form_caption_current_item'),  
					'content'=> $aEntry['title'],
                 ), 

 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => $sPackageDesc,  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_listing_package_desc'),  
                ),   
 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_listing_extend'),
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
				$this->initializeCheckout($iEntryId, $fPrice);  
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

	/***END Extend ***************/
 
	//[begin] [broadcast]
    function isAllowedBroadcast ($aDataEntry, $isPerformAction = false) {
        if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_LISTING_BROADCAST_MESSAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function actionBroadcast ($iEntryId) {
        $this->_actionBroadcast ($iEntryId, _t('_modzzz_listing_page_title_broadcast'), _t('_modzzz_listing_msg_broadcast_no_recipients'), _t('_modzzz_listing_msg_broadcast_message_sent'));
    }

    function _actionBroadcast ($iEntryId, $sTitle, $sMsgNoRecipients, $sMsgSent) {
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
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

 		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$sTitle => '',
		));

        $aRecipients = $this->_oDb->getBroadcastRecipients ($iEntryId);
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
			// freddy ajout listing owner
			$aProfile = getProfileInfo($this->_iProfileId);
			
            $aTemplate = $oEmailTemplate->getTemplate($this->_sPrefix . '_broadcast'); 
            $aTemplateVars = array (
                'BroadcastTitle' => $this->_oDb->unescape($oForm->getCleanValue ('title')),
                'BroadcastMessage' => nl2br($this->_oDb->unescape($oForm->getCleanValue ('message'))),
                'EntryTitle' => $aDataEntry[$this->_oDb->_sFieldTitle],
                'EntryUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],  
				
				// freddy ajout
				'PosterAvatar' =>  get_member_thumbnail($this->_iProfileId, 'none', true) ,   
				 'OwnerName' => $aProfile['FirstName'].' '. $aProfile['LastName'] ,           
            );
  
            $iSentMailsCounter = 0;            
            foreach ($aRecipients as $aProfile) {	
				$aTemplateVars['<NickName>'] = $aProfile['ID'];

       	        $iSentMailsCounter += sendMail($aProfile['Email'], $aTemplate['Subject'], $aTemplate['Body'], $aProfile['ID'], $aTemplateVars);

				$this->broadCastToInbox($aProfile, $aTemplate, $aTemplateVars);  
            }
            if (!$iSentMailsCounter) {
                $this->_oTemplate->displayErrorOccured();
                return;
            }
 
			$sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			echo MsgBox($sMsgSent) . genAjaxyPopupJS(0, 'ajaxy_popup_result_div', $sRedirect);

            $this->isAllowedBroadcast($aDataEntry, true); // perform send broadcast message action             
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode($sMsgSent, true, true);
            return;
        } 

        echo $oForm->getCode ();

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode($sTitle);
    }

    function broadCastToInbox($aProfile, $aTemplate, $aTemplateVars){

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
		
		// freddy ajout
		$sMessageBody = str_replace("<OwnerName>", $aTemplateVars['OwnerName'], $sMessageBody);
		
		
				 

		$oMailBox -> iWaitMinutes = 0;//turn off anti-spam
		$oMailBox -> sendMessage($aTemplateVars['BroadcastTitle'], $sMessageBody, $aProfile['ID'], $aComposeSettings); 

    }
	//[begin] [broadcast]

	//[begin] favorites
    function isAllowedMarkAsFavorite ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_LISTING_MARK_AS_FAVORITE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

	function isFavorite($iProfileId, $iEntryId){
		return $this->_oDb->isFavorite($iProfileId, $iEntryId);
	}
 
    function actionMarkFavorite ($iEntryId) {
        $this->_actionMarkFavorite ($iEntryId, _t('_modzzz_listing_msg_added_to_favorite'), _t('_modzzz_listing_msg_removed_from_favorite'));
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


	/******[BEGIN] Review functions **************************/ 
     
	function actionReview ($sAction, $sIdUri='') {
 
		switch($sAction){
			case 'add': 
				$this->actionReviewAdd ($sIdUri, '_modzzz_listing_page_title_review_add');
			break;
			case 'edit':
				$this->actionReviewEdit ($sIdUri, '_modzzz_listing_page_title_review_edit');
			break;
			case 'delete':
				$this->actionReviewDelete ($sIdUri, _t('_modzzz_listing_msg_listing_review_was_deleted'));
			break;
			case 'view':
				$this->actionReviewView ($sIdUri, _t('_modzzz_listing_msg_pending_review_approval')); 
			break; 
			case 'browse': 
				return $this->actionReviewBrowse ($sIdUri, '_modzzz_listing_page_title_review_browse'); 
			break;  
		}
	}
    
    function actionReviewBrowse ($sUri, $sTitle) {
       
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
		
		$this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_listing_menu_view_reviews') => '',
        ));
 
        bx_import ('ReviewPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry['title'], false, false);  
    }
 
    function actionReviewView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aReviewEntry = $this->_oDb->getReviewEntryByUri($sUri);
		$iEntryId = (int)$aReviewEntry['listing_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aReviewEntry[$this->_oDb->_sFieldTitle] .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri], 
        ));
 
        if ((!$this->_iProfileId || $aReviewEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableReview, $aReviewEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        } 

        $this->_oTemplate->pageStart();

        bx_import ('ReviewPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewPageView';
        $oPage = new $sClass ($this, $aReviewEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('ReviewCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aReviewEntry[$this->_oDb->_sFieldDescription]), 0, 255));
        //$this->_oTemplate->addPageKeywords ($aReviewEntry[$this->_oDb->_sFieldTags]);

        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->addCss ('unit_fan.css');

		$sPageTitle = $aReviewEntry[$this->_oDb->_sFieldTitle] .' | '. $aDataEntry[$this->_oDb->_sFieldTitle];
 
        $this->_oTemplate->pageCode($sPageTitle, false, false);

        bx_import ('BxDolViews');
        new BxDolViews($this->_oDb->_sReviewPrefix, $aDataEntry[$this->_oDb->_sFieldId]);
    }
 
    function actionReviewEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aReviewEntry = $this->_oDb->getReviewEntryById($iEntryId);
		$iReviewId = (int)$aReviewEntry['listing_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iReviewId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aDataEntry, $aReviewEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aReviewEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/view/' . $aReviewEntry[$this->_oDb->_sFieldUri],
            _t($sTitle) => '',
        ));

        bx_import ('ReviewFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewFormEdit';
        $oForm = new $sClass ($this, $aReviewEntry[$this->_oDb->_sFieldAuthorId], $iReviewId,  $iEntryId, $aReviewEntry);
  
        $oForm->initChecker($aReviewEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'review_main';
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;

			$aValsAdd = array();
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
                $this->isAllowedEdit($aDataEntry, true); // perform action

                $this->onEventSubProfileChanged ('review', $iEntryId, $sStatus);
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/view/' . $aReviewEntry[$this->_oDb->_sFieldUri]);
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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry['title']);  
    }

    function actionReviewDelete ($iReviewId, $sMsgSuccess) {

		$aReviewEntry = $this->_oDb->getReviewEntryById($iReviewId);
		$iEntryId = (int)$aReviewEntry['listing_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iReviewId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete($aDataEntry, $aReviewEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iReviewId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteReviewByIdAndOwner($iReviewId, $iEntryId, $this->_iProfileId, $this->isAdmin())) { 
            $this->onEventReviewDeleted ($iReviewId, $aReviewEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
  
            $sJQueryJS = genAjaxyPopupJS($iReviewId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iReviewId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionReviewAdd ($iReviewId, $sTitle) {
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iReviewId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	
 
        if (!$this->isAllowedPostReviews($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t($sTitle) => '',
        ));

        $this->_addReviewForm($iReviewId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry['title']);  
    }
 
    function _addReviewForm ($iReviewId) { 
 
        bx_import ('ReviewFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iReviewId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'review_main';
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {
   
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getReviewEntryById($iEntryId);
                $this->onEventSubProfileCreate('review', $iEntryId, $sStatus, $aDataEntry);
   
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 
    function onEventReviewDeleted ($iEntryId, $aDataEntry = array()) {
 
        // delete votings
        bx_import('ReviewVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewVoting';
        $oVoting = new $sClass ($this->_oDb->_sReviewPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('ReviewCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewCmts';
        $oCmts = new $sClass ($this->_oDb->_sReviewPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        bx_import ('BxDolViews');
        $oViews = new BxDolViews($this->_oDb->_sReviewPrefix, $iEntryId, false);
        $oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    function isAllowedPostReviews(&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;

        if ($this->isEntryAdmin($aDataEntry)) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_LISTING_POST_REVIEWS, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    }

    function actionBrowseReviews ($sUri) {
        $this->_actionBrowseReviews ($sUri, _t('_modzzz_listing_page_title_reviews'));
    }

    function _actionBrowseReviews ($sUri, $sTitle) {

		$iPerPage=$this->_oDb->getParam('modzzz_listing_perpage_browse_reviews');

        if (!($aDataEntry = $this->_preProductTabs($sUri, $sTitle))) { 
            return;
        }
 
        if (!$this->isAllowedViewReviews($aDataEntry)) {            
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->getReviewsBrowse($aProfiles, $iStart, $iPerPage, $aDataEntry[$this->_oDb->_sFieldId]);
        if (!$iNum || !$aProfiles) {
            $this->_oTemplate->displayNoData ();
            return;
        }
        $iPages = ceil($iNum / $iPerPage);
 
        $sRet = '';
        foreach ($aProfiles as $aProfile) {
            $sRet .= $this->_oTemplate->review_unit($aProfile);
        }
        $sRet .= '<div class="clear_both"></div>';        

        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse_reviews/' . $aDataEntry[$this->_oDb->_sFieldUri];
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

        $this->_oTemplate->addCss('main.css');
        $this->_oTemplate->addCss('view.css');

        echo DesignBoxContent ($sTitle, $sRet, 1);

        $this->_oTemplate->pageCode($sTitle, false, false);
    }

    function actionAllReviews () {

		$sTitle = _t('_modzzz_listing_page_title_reviews');

		$iPerPage=$this->_oDb->getParam('modzzz_listing_perpage_browse_reviews');
  
        if (!$this->isAllowedBrowse()) {            
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->getReviewsBrowse($aProfiles, $iStart, $iPerPage);
        if (!$iNum || !$aProfiles) {
            $this->_oTemplate->displayNoData ();
            return;
        }
        $iPages = ceil($iNum / $iPerPage);
 
        $sRet = '';
        foreach ($aProfiles as $aProfile) {
            $sRet .= $this->_oTemplate->review_unit($aProfile);
        }
        $sRet .= '<div class="clear_both"></div>';        

        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'all_reviews' ;
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

        $this->_oTemplate->addCss('main.css');
         $this->_oTemplate->addCss('view.css');

        echo DesignBoxContent ($sTitle, $sRet, 1);

        $this->_oTemplate->pageCode($sTitle, false, false);
    }

    /*******[END - Review Functions] ******************************/

	//[begin] [subprofile]
    function isSubProfileFan($sTable, $aDataEntry, $iProfileId = 0, $isConfirmed = true) {
 		
		if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isSubProfileFan ($sTable, $aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }

    function isAllowedViewSubProfile ($sTable, $aSubEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId) 
            return true;

		$aDataEntry = $this->_oDb->getEntryById($aSubEntry['listing_id']);

        if ($this->isEntryAdmin($aDataEntry))
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_LISTING_VIEW_LISTING, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
  
        $this->_oSubPrivacy = new BxListingSubPrivacy($this, $sTable); 
	    return $this->_oSubPrivacy->check('view', $aSubEntry['id'], $this->_iProfileId); 
    }

    function isAllowedRateSubProfile($sTable, &$aSubEntry) {       
        if ($this->isAdmin())
            return true;
     
		$aDataEntry = $this->_oDb->getEntryById($aSubEntry['listing_id']);

        if ($this->isEntryAdmin($aDataEntry))
            return true;

		$this->_oSubPrivacy = new BxListingSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('rate', $aSubEntry['id'], $this->_iProfileId);        
    }

    function isAllowedCommentsSubProfile($sTable, &$aSubEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;

		$aDataEntry = $this->_oDb->getEntryById($aSubEntry['listing_id']);

        if ($this->isEntryAdmin($aDataEntry))
            return true;

        $this->_oSubPrivacy = new BxListingSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('comment', $aSubEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadPhotosSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        
		$this->_oSubPrivacy = new BxListingSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadVideosSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        
		$this->_oSubPrivacy = new BxListingSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadSoundsSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        
		$this->_oSubPrivacy = new BxListingSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFilesSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        
		$this->_oSubPrivacy = new BxListingSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }

    function actionUploadPhotosSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadPhotosSubProfile', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_listing_page_title_upload_photos'));
    }

    function actionUploadVideosSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadVideosSubProfile', 'videos', array ('videos_choice', 'videos_upload'), _t('_modzzz_listing_page_title_upload_videos'));
    } 

    function actionUploadFilesSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadFilesSubProfile', 'files', array ('files_choice', 'files_upload'), _t('_modzzz_listing_page_title_upload_files'));
    } 

    function actionUploadSoundsSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadSoundsSubProfile', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_modzzz_listing_page_title_upload_sounds'));
    } 
 
    function _actionUploadMediaSubProfile ($sType, $sUri, $sIsAllowedFuncName, $sMedia, $aMediaFields, $sTitle) {
   
		switch($sType){  
			case 'news':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix;
				$sTable = $this->_oDb->_sTableNews ;
				$sDataFuncName = 'getNewsEntryByUri';
			break; 
			case 'event':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventMediaPrefix;
				$sTable = $this->_oDb->_sTableEvent ;
				$sDataFuncName = 'getEventEntryByUri';
			break;
			case 'review':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;
				$sTable = $this->_oDb->_sTableReview ;
				$sDataFuncName = 'getReviewEntryByUri';
			break;  
		}
 
        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }
 
        if (!($aDataEntry = $this->_oDb->$sDataFuncName($sUri)))
            return;

        if (!$this->$sIsAllowedFuncName($sTable, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $aListingEntry = $this->_oDb->getEntryById($aDataEntry['listing_id']);
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aListingEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aListingEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));

        $iEntryId = $aDataEntry[$this->_oDb->_sFieldId];

        bx_import (ucwords($sType) . 'FormUploadMedia', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . ucwords($sType) . 'FormUploadMedia';
        $oForm = new $sClass ($this, $aDataEntry[$this->_oDb->_sFieldAuthorId],$aDataEntry['listing_id'], $iEntryId, $aDataEntry, $sMedia, $aMediaFields);
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {

            $oForm->processMedia($iEntryId, $this->_iProfileId);

            $this->$sIsAllowedFuncName($sTable, $aDataEntry, true); // perform action

            header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . $sType . '/view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
            exit;

         } else {

            echo $oForm->getCode ();

        }

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');            
        $this->_oTemplate->pageCode($sTitle);
    } 
 
    function isAllowedSubAdd ($aDataEntry, $isPerformAction = false) {

		if ($this->isAdmin())
            return true;
  
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_LISTING_ADD_LISTING, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedSubEdit ($aDataEntry, $aSubDataEntry, $isPerformAction = false) {

        if ($this->isAdmin()) 
            return true;

        if ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true;

        if ($GLOBALS['logged']['member'] && $aSubDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true;
 
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_LISTING_EDIT_ANY_LISTING, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 
  
    function isAllowedSubDelete ($aDataEntry, $aSubDataEntry, $isPerformAction = false) {

        if ($this->isAdmin()) 
            return true;

        if ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true;

        if ($GLOBALS['logged']['member'] && $aSubDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true;

        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_LISTING_DELETE_ANY_LISTING, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }    

    
    function onEventSubProfileCreate ($sType, $iEntryId, $sStatus, $aDataEntry = array()) {
         
		if ('approved' == $sStatus) {
			 //
        }
 
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'add', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
		//$oAlert->alert();
    }
 
    function onEventSubProfileChanged($sType, $iEntryId, $sStatus) {
  
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'change', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
		//$oAlert->alert();
    }

	//[end] [subprofile]


   function onEventDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        $this->reparseTags ($iEntryId);
        //$this->reparseCategories ($iEntryId);

        // delete votings
        bx_import('Voting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Voting';
        $oVoting = new $sClass ($this->_sPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass ($this->_sPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        bx_import ('BxDolViews');
        $oViews = new BxDolViews($this->_sPrefix, $iEntryId, false);
        $oViews->onObjectDelete();

        // delete forum
        $this->_oDb->deleteForum ($iEntryId);

		// delete associated locations
		if (BxDolModule::getInstance('BxWmapModule')){ 
			BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri(), $iEntryId));  
		}
 
		//[begin] delete events
		$aEvents = $this->_oDb->getAllSubItems('event', $iEntryId);
		foreach($aEvents as $aEachEvent){
			
			$iId = (int)$aEachEvent['id'];
 
			// delete votings
			bx_import('EventVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'EventVoting';
			$oVoting = new $sClass ($this->_oDb->_sEventPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);
 
			// delete comments 
			bx_import('EventCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'EventCmts';
			$oCmts = new $sClass ($this->_oDb->_sEventPrefix, $iId);
			$oCmts->onObjectDelete ();
		} 
		$this->_oDb->deleteEvents($iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		//[end] delete events
  
		//[begin] delete news
		if(getParam('modzzz_listing_modzzz_mnews')=='on'){ 
			$oNews = BxDolModule::getInstance('BxMNewsModule'); 
			$aNews = $this->_oDb->getModzzzNews($iEntryId);
			foreach($aNews as $aEachNews){ 
				if ($oNews->_oDb->deleteEntryByIdAndOwner($aEachNews['ID'], 0, 0)) {
					$oNews->isAllowedDelete($aEachNews, true); // perform action
					$oNews->onNewsDeleted ($aEachNews['ID'], $aEachNews);
				} 
			} 
		}else{

			$aNews = $this->_oDb->getAllSubItems('news', $iEntryId);
			foreach($aNews as $aEachNews){
				
				$iId = (int)$aEachNews['id'];

				// delete votings
				bx_import('NewsVoting', $this->_aModule);
				$sClass = $this->_aModule['class_prefix'] . 'NewsVoting';
				$oVoting = new $sClass ($this->_oDb->_sNewsPrefix, 0, 0);
				$oVoting->deleteVotings ($iId);

				// delete comments 
				bx_import('NewsCmts', $this->_aModule);
				$sClass = $this->_aModule['class_prefix'] . 'NewsCmts';
				$oCmts = new $sClass ($this->_oDb->_sNewsPrefix, $iId);
				$oCmts->onObjectDelete ();
			}  

			$this->_oDb->deleteNews($iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		}
		//[end] delete news
 
		//[begin] delete review 
		$aReview = $this->_oDb->getAllSubItems('review', $iEntryId);
		foreach($aReview as $aEachReview){
			
			$iId = (int)$aEachReview['id'];

			// delete votings
			bx_import('ReviewVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'ReviewVoting';
			$oVoting = new $sClass ($this->_oDb->_sReviewPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);

			// delete comments 
			bx_import('ReviewCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'ReviewCmts';
			$oCmts = new $sClass ($this->_oDb->_sReviewPrefix, $iId);
			$oCmts->onObjectDelete ();
		
 			$this->_oDb->deleteReviewByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		} 
  		//[end] delete review 
 
		$oModuleDb = new BxDolModuleDb();
 
		//[begin] delete associated jobs 
		if(getParam("modzzz_listing_jobs_active")=='on'){  
			if($oModuleDb->getModuleByUri('jobs')){ 
				$oJob = BxDolModule::getInstance('BxJobsModule');
				$aJobs = $this->_oDb->getBusinessJobs($iEntryId); 
				foreach($aJobs as $iJobId){  
					if ($oJob->_oDb->deleteEntryByIdAndOwner($iJobId, $this->_iProfileId, 0)) {
						$oJob->onEventDeleted ($iJobId);
					} 
				}
			}
		}
		//[end] delete associated jobs
 
		//[begin] delete associated coupons 
		if(getParam("modzzz_listing_coupons_active")=='on'){  
			if($oModuleDb->getModuleByUri('coupons')){ 
				$oCoupon = BxDolModule::getInstance('BxCouponsModule');
				$aCoupons = $this->_oDb->getBusinessCoupons($iEntryId); 
				foreach($aCoupons as $iCouponId){  
					if ($oCoupon->_oDb->deleteEntryByIdAndOwner($iCouponId, $this->_iProfileId, 0)) {
						$oCoupon->onEventDeleted ($iCouponId);
					} 
				}
			}
		}
		//[end] delete associated coupons
 
		//[begin] delete associated deals 
		if(getParam("modzzz_listing_deals_active")=='on'){ 
			if($oModuleDb->getModuleByUri('deals')){
				$oDeal = BxDolModule::getInstance('BxDealsModule');
				$aDeals = $this->_oDb->getBusinessDeals($iEntryId);
				foreach($aDeals as $iDealId){ 
					if ($oDeal->_oDb->deleteEntryByIdAndOwner($iDealId, $this->_iProfileId, 0)) {
						$oDeal->onEventDeleted ($iDealId);
					}
  				}
			}
		}
		//[end] delete associated deals
 
        // arise alert
		$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		$oAlert->alert();
    }        

	/* functions added for v7.1 */

    function serviceGetMemberMenuItemAddContent ()
    {
        if (!$this->isAllowedAdd())
            return '';
        return parent::_serviceGetMemberMenuItem (_t('_modzzz_listing_listing_single'), _t('_modzzz_listing_listing_single'), 'briefcase', false, '&listing=add_listing');
    }


   /**
     * Install map support
     */
    function serviceMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('listing', array(
            'part' => 'listing',
            'title' => '_modzzz_listing',
            'title_singular' => '_modzzz_listing_single',
            'icon' => 'modules/modzzz/listing/|map_marker.png',
            'icon_site' => 'briefcase',
            'join_table' => 'modzzz_listing_main',
            'join_where' => "AND `p`.`status` = 'approved'",
            'join_field_id' => 'id',
            'join_field_country' => 'country',
            'join_field_city' => 'city',
            'join_field_state' => 'state',
            'join_field_zip' => 'zip',
            'join_field_address' => 'address1',
            'join_field_title' => 'title',
            'join_field_uri' => 'uri',
            'join_field_author' => 'author_id',
            'join_field_privacy' => 'allow_view_listing_to',
            'permalink' => 'modules/?r=listing/view/',
        )));
    }
 
	//remove old one first
    function serviceGetWallPost ($aEvent)
    {
        $aParams = array(
            'txt_object' => '_modzzz_listing_wall_object',
            'txt_added_new_single' => '_modzzz_listing_wall_added_new',
            'txt_added_new_plural' => '_modzzz_listing_wall_added_new_items',
            'txt_privacy_view_event' => 'view_listing',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallPost ($aEvent, $aParams);
    }
 
    function serviceGetWallPostComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_listing',
            'obj_privacy' => $this->_oPrivacy
        );
        return $this->_serviceGetWallPostComment($aEvent, $aParams);
    }
 
    function _serviceGetWallPostComment($aEvent, $aParams)
    {
        $iId = (int)$aEvent['object_id'];
        $iOwner = (int)$aEvent['owner_id'];
        $sOwner = getNickName($iOwner);

        $aItem = $this->_oDb->getEntryByIdAndOwner($iId, $iOwner, 1);
        if(empty($aItem) || !is_array($aItem))
        	return array('perform_delete' => true);

        if(!$aParams['obj_privacy']->check($aParams['txt_privacy_view_event'], $iId, $this->_iProfileId))
            return '';

        $aContent = unserialize($aEvent['content']);
        if(empty($aContent) || !isset($aContent['comment_id']))
            return '';

        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass($this->_sPrefix, $iId);
        if(!$oCmts->isEnabled())
            return '';

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
            $sCss = $this->_oTemplate->addCss(array('wall_post.css', 'unit.css', 'twig.css'), true);
        else
            $this->_oTemplate->addCss(array('wall_post.css', 'unit.css', 'twig.css'));

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
            'txt_privacy_view_event' => 'view_listing',
            'obj_privacy' => $this->_oPrivacy,
            'templates' => array(
                'grouped' => 'wall_outline_grouped'
            )
        );
        return parent::_serviceGetWallPostOutline($aEvent, 'briefcase', $aParams);
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
            'employees_count' => $aEntryData['employees_count'],
        ));
    }

    function _formatSnippetText ($aEntryData, $iMaxLen = 300, $sField='')
    {  $sField = ($sField) ? $sField : $aEntryData[$this->_oDb->_sFieldDescription];
        return strmaxtextlen($sField, $iMaxLen);
    }
 
    function serviceDeleteProfileData ($iProfileId)
    {
        $iProfileId = (int)$iProfileId;

        if (!$iProfileId)
            return false;

        // delete entries which belongs to particular author
        $aDataEntries = $this->_oDb->getEntriesByAuthor ($iProfileId);
        foreach ($aDataEntries as $iEntryId) {
            if ($this->_oDb->deleteEntryByIdAndOwner($iEntryId, $iProfileId, false))
                $this->onEventDeleted ($iEntryId);
        }

        // delete from list of fans/employees/admins
        $this->_oDb->removeEmployeeFromAllEntries ($iProfileId);
        $this->_oDb->removeFanFromAllEntries ($iProfileId);
        $this->_oDb->removeAdminFromAllEntries ($iProfileId);
    }



   //[begin] employee functions
   function actionWork ($iEntryId, $iProfileId) {

        $this->_actionWork ($iEntryId, $iProfileId, _t('_modzzz_listing_msg_worked_already'), _t('_modzzz_listing_msg_worked_request_pending'), _t('_modzzz_listing_msg_work_success'), _t('_modzzz_listing_msg_work_success_pending'), _t('_modzzz_listing_msg_resign_success'));
    }  
 
    function onEventWorkRequest ($iEntryId, $iProfileId, $aDataEntry) {
        $this->_onEventWorkRequest ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_listing_work_request', BX_LISTING_MAX_EMPLOYEES);
    }

    function onEventWorkReject ($iEntryId, $iProfileId, $aDataEntry) {
        $this->_onEventWorkReject ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_listing_work_reject');
    }

    function onEventWorkConfirm ($iEntryId, $iProfileId, $aDataEntry) {
        $this->_onEventWorkConfirm ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_listing_work_confirm');
    }

    function isAllowedWork (&$aDataEntry) { 
        if (!$this->_iProfileId) 
            return false;

		return true; 
        //return $this->_oPrivacy->check('work', $aDataEntry['id'], $this->_iProfileId);
    }
 
    function actionBrowseEmployees ($sUri) {
        $this->_actionBrowseEmployees ($sUri, 'isAllowedViewEmployees', 'getEmployeesBrowse', $this->_oDb->getParam('modzzz_listing_perpage_browse_employees'), 'browse_employees/', _t('_modzzz_listing_page_title_employees'));
    }
 
    function actionManageEmployeesPopup ($iEntryId) {
        $this->_actionManageEmployeesPopup ($iEntryId, _t('_modzzz_listing_caption_manage_employees'), 'getEmployees', 'isAllowedManageEmployees', 'isAllowedManageAdmins', BX_LISTING_MAX_EMPLOYEES);
    }
 
    function onEventEmployeeRemove ($iEntryId, $iProfileId, $aDataEntry) {        
        $this->_onEventEmployeeRemove ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_listing_employee_remove');
    }

    function onEventEmployeeBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry) {        
        $this->_onEventEmployeeBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_listing_employee_become_admin');
    }

    function onEventAdminBecomeEmployee ($iEntryId, $iProfileId, $aDataEntry) {        
        $this->_onEventAdminBecomeEmployee ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_listing_admin_become_employee');
    }

	function isAllowedViewEmployees(&$aDataEntry) {
        if ($this->isAdmin())
            return true;
        return $this->_oPrivacy->check('view_employees', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedManageEmployees($aDataEntry) {
        return $this->isEntryAdmin($aDataEntry);
    }

    function isEmployee($aDataEntry, $iProfileId = 0, $isConfirmed = true) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isEmployee ($aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }

   function _actionBrowseEmployees ($sUri, $sFuncAllowed, $sFuncDbGetEmployees, $iPerPage, $sUrlBrowse, $sTitle)
    {
        if (!($aDataEntry = $this->_preProductTabs($sUri, $sTitle))) {
            return;
        }

        if (!$this->$sFuncAllowed($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->$sFuncDbGetEmployees($aProfiles, $aDataEntry[$this->_oDb->_sFieldId], $iStart, $iPerPage);
        if (!$iNum || !$aProfiles) {
            $this->_oTemplate->displayNoData ();
            return;
        }
        $iPages = ceil($iNum / $iPerPage);

        bx_import('BxTemplSearchProfile');
        $oBxTemplSearchProfile = new BxTemplSearchProfile();
        $sMainContent = '';
        foreach ($aProfiles as $aProfile) {
            $sMainContent .= $oBxTemplSearchProfile->displaySearchUnit($aProfile);
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

        echo DesignBoxContent ($sTitle, $sRet, 11);

        $this->_oTemplate->pageCode($sTitle, false, false);
    }


    function _actionWork ($iEntryId, $iProfileId, $sMsgAlreadyWorked, $sMsgAlreadyWorkedPending, $sMsgWorkSuccess, $sMsgWorkSuccessPending, $sMsgLeaveSuccess)
    {
        header('Content-type:text/html;charset=utf-8');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, 0, true))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedWork($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        $isEmployee = $this->_oDb->isEmployee ($iEntryId, $this->_iProfileId, true) || $this->_oDb->isEmployee ($iEntryId, $this->_iProfileId, false);

        if ($isEmployee) {

            if ($this->_oDb->resignEntry($iEntryId, $this->_iProfileId)) {
                $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
                echo MsgBox($sMsgLeaveSuccess) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);
                exit;
            }

        } else {

            $isConfirmed = false;//($this->isEntryAdmin($aDataEntry) || !$aDataEntry[$this->_oDb->_sFieldWorkConfirmation] ? true : false);

            if ($this->_oDb->workEntry($iEntryId, $this->_iProfileId, $isConfirmed)) {
                if ($isConfirmed) {
                    $this->onEventWork ($iEntryId, $this->_iProfileId, $aDataEntry);
                    $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
                } else {
                    $this->onEventWorkRequest ($iEntryId, $this->_iProfileId, $aDataEntry);
                    $sRedirect = '';
                }
                echo MsgBox($isConfirmed ? $sMsgWorkSuccess : $sMsgWorkSuccessPending) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);
                exit;
            }
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
        exit;
    }

    function _actionManageEmployeesPopup ($iEntryId, $sTitle, $sFuncGetEmployees = 'getEmployees', $sFuncIsAllowedManageEmployees = 'isAllowedManageEmployees', $sFuncIsAllowedManageAdmins = 'isAllowedManageAdmins', $iMaxEmployees = 1000)
    {
        header('Content-type:text/html;charset=utf-8');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner ($iEntryId, 0, true))) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }

        if (!$this->$sFuncIsAllowedManageEmployees($aDataEntry)) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Access denied')));
            exit;
        }

        $aProfiles = array ();
        $iNum = $this->_oDb->$sFuncGetEmployees($aProfiles, $iEntryId, true, 0, $iMaxEmployees);
        if (!$iNum) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }

        $sActionsUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "view/" . $aDataEntry[$this->_oDb->_sFieldUri] . '?ajax_employee_action=';
        $aButtons = array (
            array (
                'type' => 'submit',
                'name' => 'employees_remove',
                'value' => _t('_sys_btn_fans_remove'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_employees_content', '{$sActionsUrl}remove&ids=' + sys_manage_items_get_manage_employees_ids(), false, 'post'); return false;\"",
            ),
        );

        if ($this->$sFuncIsAllowedManageAdmins($aDataEntry)) {

            $aButtons = array_merge($aButtons, array (
                array (
                    'type' => 'submit',
                    'name' => 'employees_add_to_admins',
                    'value' => _t('_sys_btn_fans_add_to_admins'),
                    'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_employees_content', '{$sActionsUrl}add_to_admins&ids=' + sys_manage_items_get_manage_employees_ids(), false, 'post'); return false;\"",
                ),/*
                array (
                    'type' => 'submit',
                    'name' => 'employees_move_admins_to_employees',
                    'value' => _t('_sys_btn_fans_move_admins_to_employees'),
                    'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_employees_content', '{$sActionsUrl}admins_to_employees&ids=' + sys_manage_items_get_manage_employees_ids(), false, 'post'); return false;\"",
                ),*/
            ));
        };
        bx_import ('BxTemplSearchResult');
        $sControl = BxTemplSearchResult::showAdminActionsPanel('sys_manage_items_manage_employees', $aButtons, 'sys_fan_unit');

        $aVarsContent = array (
            'suffix' => 'manage_employees',
            'content' => $this->_profilesEdit($aProfiles, false, $aDataEntry),
            'control' => $sControl,
        );
        $aVarsPopup = array (
            'title' => $sTitle,
            'content' => $this->_oTemplate->parseHtmlByName('manage_items_form', $aVarsContent),
        );
        echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true);
        exit;
    }

    function onEventWork ($iEntryId, $iProfileId, $aDataEntry) {
        // we do not need to send any notofication mail here because it will be part of standard subscription process 
		$oAlert = new BxDolAlerts($this->_sPrefix, 'work', $iEntryId, $iProfileId);
		$oAlert->alert();
    }
 
    function _onEventWorkRequest ($iEntryId, $iProfileId, $aDataEntry, $sEmailTemplate, $iMaxEmployees = 1000)
    {
        $iNum = $this->_oDb->getAdmins($aGroupAdmins, $iEntryId, 0, $iMaxEmployees);
        $aGroupAdmins[] = getProfileInfo($aDataEntry[$this->_oDb->_sFieldAuthorId]);
        foreach ($aGroupAdmins as $aProfile)
            $this->_notifyEmail ($sEmailTemplate, $aProfile['ID'], $aDataEntry);

        $oAlert = new BxDolAlerts($this->_sPrefix, 'work_request', $iEntryId, $iProfileId);
        $oAlert->alert();
    }

    function _onEventWorkReject ($iEntryId, $iProfileId, $aDataEntry, $sEmailTemplate)
    {
        $this->_notifyEmail ($sEmailTemplate, $iProfileId, $aDataEntry);
        $oAlert = new BxDolAlerts($this->_sPrefix, 'work_reject', $iEntryId, $iProfileId);
        $oAlert->alert();
    }

    function _onEventEmployeeRemove ($iEntryId, $iProfileId, $aDataEntry, $sEmailTemplate)
    {
        $this->_notifyEmail ($sEmailTemplate, $iProfileId, $aDataEntry);
        $oAlert = new BxDolAlerts($this->_sPrefix, 'employee_remove', $iEntryId, $iProfileId);
        $oAlert->alert();
    }

    function _onEventEmployeeBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry, $sEmailTemplate)
    {
        $this->_notifyEmail ($sEmailTemplate, $iProfileId, $aDataEntry);
        $oAlert = new BxDolAlerts($this->_sPrefix, 'employee_become_admin', $iEntryId, $iProfileId);
        $oAlert->alert();
    }

    function _onEventAdminBecomeEmployee ($iEntryId, $iProfileId, $aDataEntry, $sEmailTemplate)
    {
        $this->_notifyEmail ($sEmailTemplate, $iProfileId, $aDataEntry);
        $oAlert = new BxDolAlerts($this->_sPrefix, 'admin_become_employee', $iEntryId, $iProfileId);
        $oAlert->alert();
    }

    function _onEventWorkConfirm ($iEntryId, $iProfileId, $aDataEntry, $sEmailTemplate)
    {
        $this->_notifyEmail ($sEmailTemplate, $iProfileId, $aDataEntry);
        $oAlert = new BxDolAlerts($this->_sPrefix, 'work_confirm', $iEntryId, $iProfileId);
        $oAlert->alert();
    }
 
	function _processEmployeesActions ($aDataEntry, $iMaxEmployees = 1000)
    {
        header('Content-type:text/html;charset=utf-8');

        if (false !== bx_get('ajax_employee_action') && $this->isAllowedManageEmployees($aDataEntry) && 0 == strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {

            $iEntryId = $aDataEntry[$this->_oDb->_sFieldId];
            $aIds = array ();
            if (false !== bx_get('ids'))
                $aIds = $this->_getCleanIdsArray (bx_get('ids'));

            $isShowConfirmedEmployeesOnly = false;
            switch (bx_get('ajax_employee_action')) {
                case 'remove':
                    $isShowConfirmedEmployeesOnly = true;
                    if ($this->_oDb->removeEmployees($iEntryId, $aIds)) {
                        foreach ($aIds as $iProfileId)
                            $this->onEventEmployeeRemove ($iEntryId, $iProfileId, $aDataEntry);
                    }
                    break;
                case 'add_to_admins':
                    $isShowConfirmedEmployeesOnly = true;
                    if ($this->isAllowedManageAdmins($aDataEntry) && $this->_oDb->addGroupAdmin($iEntryId, $aIds)) {
                        $aProfiles = array ();
                        $iNum = $this->_oDb->getAdmins($aProfiles, $iEntryId, 0, $iMaxEmployees, $aIds);
                        foreach ($aProfiles as $aProfile)
                            $this->onEventEmployeeBecomeAdmin ($iEntryId, $aProfile['ID'], $aDataEntry);
                    }
                    break;
                case 'admins_to_employees':
                    $isShowConfirmedEmployeesOnly = true;
                    $iNum = $this->_oDb->getAdmins($aGroupAdmins, $iEntryId, 0, $iMaxEmployees);
                    if ($this->isAllowedManageAdmins($aDataEntry) && $this->_oDb->removeGroupAdmin($iEntryId, $aIds)) {
                        foreach ($aGroupAdmins as $aProfile) {
                            if (in_array($aProfile['ID'], $aIds))
                                $this->onEventAdminBecomeEmployee ($iEntryId, $aProfile['ID'], $aDataEntry);
                        }
                    }
                    break;
                case 'confirm':
                    if ($this->_oDb->confirmEmployees($iEntryId, $aIds)) {
                        echo '<script type="text/javascript" language="javascript">
                            document.location = "' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "view/" . $aDataEntry[$this->_oDb->_sFieldUri] . '";
                        </script>';
                        $aProfiles = array ();
                        $iNum = $this->_oDb->getEmployees($aProfiles, $iEntryId, true, 0, $iMaxEmployees, $aIds);
                        foreach ($aProfiles as $aProfile) {
                            $this->onEventWork ($iEntryId, $aProfile['ID'], $aDataEntry);
                            $this->onEventWorkConfirm ($iEntryId, $aProfile['ID'], $aDataEntry);
                        }
                    }
                    break;
                case 'reject':
                    if ($this->_oDb->rejectEmployees($iEntryId, $aIds)) {
                        foreach ($aIds as $iProfileId)
                            $this->onEventWorkReject ($iEntryId, $iProfileId, $aDataEntry);
                    }
                    break;
                case 'list':
                    break;
            }

            $aProfiles = array ();
            $iNum = $this->_oDb->getEmployees($aProfiles, $iEntryId, $isShowConfirmedEmployeesOnly, 0, $iMaxEmployees);
            if (!$iNum) {
                echo MsgBox(_t('_Empty'));
            } else {
                echo $this->_profilesEdit ($aProfiles, true, $aDataEntry);
            }
            exit;
        }
    }
 
    function actionAddEmployee ($iEntryId) {

		$sTitle = _t('_modzzz_listing_title_add_employee');
 
        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));

        bx_import ('EmployeeForm', $this->_aModule);
		$oForm = new BxListingEmployeeForm ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
  
			$sMemberName = trim($oForm->getCleanValue('employee'));
            if ($sMemberName) { 
				$iProfileId = getID($sMemberName);
				$iSuccess = ($iProfileId) ? $this->_oDb->workEntry($iEntryId, $iProfileId, true) : false;
			}
			
			$sMsgSuccess = _t('_modzzz_listing_add_employee_success', $sMemberName);
			$sMsgFail = _t('_modzzz_listing_add_employee_failed', $sMemberName);
 
            $sMsg = ($iSuccess) ? $sMsgSuccess : $sMsgFail;
            echo MsgBox($sMsg); 
		}

		if($iSuccess){ 
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode ($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

    function isAllowedReadForum(&$aDataEntry, $iProfileId = -1)
    {
        return true;
    }

	function actionWebsite ($sUri) {
       
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
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));


        $sUrl = strncasecmp($aDataEntry['businesswebsite'], 'http://', 7) != 0 && strncasecmp($aDataEntry['businesswebsite'], 'https://', 8) != 0 ? 'http://' . $aDataEntry['businesswebsite'] : $aDataEntry['businesswebsite'];
 
		$aVars = array (  
            'site_url' => $sUrl  
        );
 
        echo $this->_oTemplate->parseHtmlByName('block_external', $aVars); 
   
		$sTitle = _t('_modzzz_listing_title_website', $aDataEntry[$this->_oDb->_sFieldTitle]);
        $this->_oTemplate->pageCode($sTitle , false, false);  
    }

	function hasWebsite(){
 
		if($_REQUEST["r"] || $_REQUEST["orca_integration"]=='listing'){ 
 
			$pattern = '/^listing\/view\//';
			preg_match($pattern, $_REQUEST["r"], $matches);
			$aDataEntry = array();
			if ($matches[0]){ 
				$sUrl = str_replace($matches[0],'',$_REQUEST["r"]);	  
				if($sUrl){
					$aDataEntry = $this->_oDb->getEntryByUri($sUrl);  
				}
			}  
  
			return ($aDataEntry['businesswebsite']) ? true : false;  

		}else{
			return false;
		} 
	}
 
    function serviceStatesInstall()
    {
		$this->_oDb->statesInstall(); 
	}
   
 
    function serviceInitializeCategories()
    {
		$this->_oDb->initCategories(); 
	}


	/******[BEGIN] News functions **************************/ 
    function actionNews ($sAction, $sParam1='', $sParam2='') { 
		switch($sAction){
			case 'download': 
				$this->newsDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionNewsAdd ($sParam1, '_modzzz_listing_page_title_news_add');
			break;
			case 'edit':
				$this->actionNewsEdit ($sParam1, '_modzzz_listing_page_title_news_edit');
			break;
			case 'delete':
				$this->actionNewsDelete ($sParam1, _t('_modzzz_listing_msg_listing_news_was_deleted'));
			break;
			case 'view':
				$this->actionNewsView ($sParam1, _t('_modzzz_listing_msg_pending_news_approval')); 
			break; 
			case 'browse':
				return $this->actionNewsBrowse ($sParam1, '_modzzz_listing_page_title_news_browse'); 
			break;  
		}
	}
    
    function actionNewsBrowse ($sUri, $sTitle) {
      
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
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t($sTitle) => '',
        ));

        bx_import ('NewsPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);  
    }
 
    function actionNewsView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aNewsEntry = $this->_oDb->getNewsEntryByUri($sUri);
		$iEntryId = (int)$aNewsEntry['listing_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        } 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aNewsEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        if ((!$this->_iProfileId || $aNewsEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableNews, $aNewsEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   

        $this->_oTemplate->pageStart();
  
        bx_import ('NewsPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsPageView';
        $oPage = new $sClass ($this, $aNewsEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

 		echo $oPage->getCode();
  
        bx_import('NewsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aNewsEntry['desc']), 0, 255));
 
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css', 'entry_view.css'));

        $this->_oTemplate->pageCode($aNewsEntry['title'], false, false); 
    }
 
    function actionNewsEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aNewsEntry = $this->_oDb->getNewsEntryById($iEntryId);
		$iNewsId = (int)$aNewsEntry['listing_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iNewsId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedEdit($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		$GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t($sTitle) => '',
        ));

        bx_import ('NewsFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsFormEdit';
        $oForm = new $sClass ($this, $aNewsEntry['uri'], $iNewsId,  $iEntryId, $aNewsEntry);
  
        $oForm->initChecker($aNewsEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'news_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
                $this->isAllowedEdit($aDataEntry, true); // perform action
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/view/' . $aNewsEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aNewsEntry['title']);  
    }

    function actionNewsDelete ($iNewsId, $sMsgSuccess) {

		$aNewsEntry = $this->_oDb->getNewsEntryById($iNewsId);
		$iEntryId = (int)$aNewsEntry['listing_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteNewsByIdAndOwner($iNewsId, $iEntryId, $this->_iProfileId, $this->isAdmin())) { 
            $this->onEventNewsDeleted ($iNewsId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
  
            $sJQueryJS = genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionNewsAdd ($iNewsId, $sTitle) {
  
		$iNewsId = (int)$iNewsId;

		//[begin] news integration - modzzz
		if(getParam('modzzz_listing_modzzz_mnews')=='on'){ 
			$oNews = BxDolModule::getInstance('BxMNewsModule');
			$sRedirectUrl = BX_DOL_URL_ROOT . $oNews->_oConfig->getBaseUri() . 'browse/my&filter=add_news&business=' . $iNewsId;
		  
			header ('Location:' . $sRedirectUrl);
			exit;
		}
 		//[end] news integration - modzzz


        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iNewsId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t($sTitle) => '',
        ));
		
        $this->_addNewsForm($iNewsId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
    }
 
    function _addNewsForm ($iNewsId) { 
 
        bx_import ('NewsFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iNewsId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'news_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId  
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {
   
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getNewsEntryById($iEntryId);
    
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventNewsDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseNewsTags ($iEntryId);
        //$this->reparseNewsCategories ($iEntryId);

        // delete votings
        bx_import('NewsVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsVoting';
        $oVoting = new $sClass ($this->_oDb->_sNewsPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('NewsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsCmts';
        $oCmts = new $sClass ($this->_oDb->_sNewsPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sNewsPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }    
 
	function newsDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'news_files');

        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getNewsEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }

        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableNews, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

    /*******[END - News Functions] ******************************/



	/******[BEGIN] Event functions **************************/
	
	function eventDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'event_files');
 
        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getEventEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }
 
        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableEvent, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

	function serviceMapEventInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('listing_event', array(
            'part' => 'listing_event',
            'title' => '_modzzz_listing_event',
            'title_singular' => '_modzzz_listing_event_single',
            'icon' => 'modules/modzzz/listing/|map_marker.png',
            'icon_site' => 'briefcase',
            'join_table' => 'modzzz_listing_event_main',
            'join_where' => "AND `p`.`status` = 'approved'",
            'join_field_id' => 'id',
            'join_field_country' => 'country',
            'join_field_city' => 'city',
            'join_field_state' => 'state',
            'join_field_zip' => 'zip',
            'join_field_address' => 'address1',
            'join_field_title' => 'title',
            'join_field_uri' => 'uri',
            'join_field_author' => 'author_id',
            'join_field_privacy' => 'allow_view_to',
            'permalink' => 'modules/?r=listing/event/view/',
        )));
    }
 
    function actionEvents ($sAction, $sParam1='', $sParam2='') {
		switch($sAction){ 
			case 'browse':
				return $this->actionEventBrowse ($sParam1, '_modzzz_listing_page_title_event_browse'); 
			break;  
		}
	}

    function actionEvent ($sAction, $sParam1='', $sParam2='') {
		switch($sAction){
			case 'download': 
				$this->eventDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionEventAdd ($sParam1, '_modzzz_listing_page_title_event_add');
			break;
			case 'edit':
				$this->actionEventEdit ($sParam1, '_modzzz_listing_page_title_event_edit');
			break;
			case 'delete':
				$this->actionEventDelete ($sParam1, _t('_modzzz_listing_msg_listing_event_was_deleted'));
			break;
			case 'view':
				$this->actionEventView ($sParam1, _t('_modzzz_listing_msg_pending_event_approval')); 
			break; 
			case 'browse':
				return $this->actionEventBrowse ($sParam1, '_modzzz_listing_page_title_event_browse'); 
			break;  
		}
	}
    
    function actionEventBrowse ($sUri, $sTitle) {
       
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
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t($sTitle) => '',
        ));
 
        bx_import ('EventPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
   		
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css' ));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);  
    }
 
    function actionEventView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aEventEntry = $this->_oDb->getEventEntryByUri($sUri);
		$iEntryId = (int)$aEventEntry['listing_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aEventEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

         $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aEventEntry[$this->_oDb->_sFieldTitle] => '',
        ));


        if ((!$this->_iProfileId || $aEventEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableEvent, $aEventEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }  
 
        $this->_oTemplate->pageStart();
 
        bx_import ('EventPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventPageView';
        $oPage = new $sClass ($this, $aEventEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

 		echo $oPage->getCode();

        bx_import('EventCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aEventEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aEventEntry['title'], false, false); 
    }

    function actionEventEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aEventEntry = $this->_oDb->getEventEntryById($iEntryId);
		$iListingId = (int)$aEventEntry['listing_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iListingId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedEdit($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t($sTitle) => '',
        ));


        bx_import ('EventFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventFormEdit';
        $oForm = new $sClass ($this, $aEventEntry['uri'], $iListingId,  $iEntryId, $aEventEntry);
  
        $oForm->initChecker($aEventEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'event_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
			    if (BxDolModule::getInstance('BxWmapModule'))
					BxDolService::call('wmap', 'response_entry_change', array($this->_oConfig->getUri().'_event', $iEntryId));

                //$this->isAllowedEdit($aDataEntry, true); // perform action
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'event/view/' . $aEventEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aEventEntry['title']);  
    }

    function actionEventDelete ($iEventId, $sMsgSuccess) {

		$aEventEntry = $this->_oDb->getEventEntryById($iEventId);
		$iListingId = (int)$aEventEntry['listing_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iListingId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteEventByIdAndOwner($iEventId, $iListingId, $this->_iProfileId, $this->isAdmin())) { 
            $this->onEventEventDeleted ($iEventId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
  
            $sJQueryJS = genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionEventAdd ($iListingId, $sTitle) {
    
		//[begin] event integration - modzzz
		if(getParam('modzzz_listing_boonex_events')=='on'){ 
			$oEvent = BxDolModule::getInstance('BxEventsModule');
			$sRedirectUrl = BX_DOL_URL_ROOT . $oEvent->_oConfig->getBaseUri() . 'browse/my&bx_events_filter=add_event&listing=' . $iListingId;
		  
			header ('Location:' . $sRedirectUrl);
			exit;
		}
 		//[end] event integration - modzzz

        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iListingId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		$GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t($sTitle) => '',
        ));

        $this->_addEventForm($iListingId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
    }
 
    function _addEventForm ($iListingId) { 
 
        bx_import ('EventFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iListingId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'event_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

 				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
				if (BxDolModule::getInstance('BxWmapModule')){
					BxDolService::call('wmap', 'response_entry_add', array($this->_oConfig->getUri() . '_event', $iEntryId));
				}

                $aDataEntry = $this->_oDb->getEventEntryById($iEntryId);
    
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'event/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventEventDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseEventTags ($iEntryId);
        //$this->reparseEventCategories ($iEntryId);

        // delete votings
        bx_import('EventVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventVoting';
        $oVoting = new $sClass ($this->_oDb->_sEventPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('EventCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventCmts';
        $oCmts = new $sClass ($this->_oDb->_sEventPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete associated locations
        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri().'_event', $iEntryId));

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sEventPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }   
 
    /*******[END - EVENT Functions] ******************************/
 
	function getListingLink($iEntryId){
		$iEntryId = (int)$iEntryId;
		$aDataEntry = $this->_oDb->getEntryById($iEntryId);
		
		$sUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

		return '<a href="'.$sUrl.'">'.$aDataEntry[$this->_oDb->_sFieldTitle].'</a>';
	}
  
    function actionPayFeature ($iEntryId) {
 
        header('Content-type:text/html;charset=utf-8');

		$iEntryId = (int)$iEntryId;
		$aDataEntry = $this->_oDb->getEntryById($iEntryId);
		$sTitle = $aDataEntry['title'];

		$sCode = $sBuyPointsLink = $sBuyCreditsLink = '';
		$iFeaturedCost = $iMoneyBalance = 0;
  
		if(getParam('modzzz_listing_featured_credits')=='on'){ 
 
			$sPaymentUnit = 'credits'; 
			$sMoneyBalanceLabelC = _t("_modzzz_listing_form_caption_credits_balance");
			$sMoneyTypeC = _t("_modzzz_listing_credits");

			$oCredit = BxDolModule::getInstance('BxCreditModule');  
			$sBuyCreditsLink = BX_DOL_URL_ROOT . $oCredit->_oConfig->getBaseUri() . 'purchase_credits'; 
			$iMoneyBalance = (int)$oCredit->getMemberCredits($this->_iProfileId);
			$iFeaturedCost = (int)getParam('modzzz_listing_credits_cost');
			if($iMoneyBalance < $iFeaturedCost){
				$sCode = _t('_modzzz_listing_insufficient_credits_message', number_format($iMoneyBalance,0), number_format($iFeaturedCost,0));
				$sCode .= '&nbsp;'._t('_modzzz_listing_buy_link', $sBuyCreditsLink);
 			}
		}elseif(getParam('modzzz_listing_featured_points')=='on'){
			
 			$sPaymentUnit = 'points';
			$sMoneyBalanceLabelC = _t("_modzzz_listing_form_caption_points_balance");
			$sMoneyTypeC = _t("_modzzz_listing_points");

			$oPoint = BxDolModule::getInstance('BxPointModule');   
			$sBuyPointsLink = BX_DOL_URL_ROOT . $oPoint->_oConfig->getBaseUri() . 'purchase_points'; 
			$iMoneyBalance = $oPoint->_oDb->getMemberPoints($this->_iProfileId);
			$iFeaturedCost = (int)getParam('modzzz_listing_points_cost');
			if($iMoneyBalance < $iFeaturedCost){
				$sCode = _t('_modzzz_listing_insufficient_points_message', number_format($iMoneyBalance,0), number_format($iFeaturedCost,0));
				$sCode .= '&nbsp;'._t('_modzzz_listing_buy_link', $sBuyPointsLink);
 			}
		}
 
		if($sCode) {
			$sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array('content' => $sCode));
	 
			$aVarsPopup = array (
				'title' => _t('_modzzz_listing_page_title_feature_item'),
				'content' => $sCode,
			);        
			
			echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true);
			exit;
		}
  
		for($iter=1; $iter<=1000; $iter++)
			$aQuantity[$iter] = $iter;

		$aCustomForm = array(

			'form_attrs' => array(
				'id' => 'listing_form',
				'name' => 'listing_form',
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
					'caption' => _t("_modzzz_listing_form_caption_feature", $sTitle),
				), 
				'money_balance' => array(
					'type' => 'custom',
					'caption' => $sMoneyBalanceLabelC,
					'content' => number_format($iMoneyBalance,0) .' '. $sMoneyTypeC,
				), 
				'feature_cost' => array(
					'type' => 'custom',
 					'caption' => _t("_modzzz_listing_form_caption_cost_to_feature"),
					'content' => number_format($iFeaturedCost,0) .' '. $sMoneyTypeC .' '. _t("_modzzz_listing_form_per_day"),
				),   
                'quantity' => array(
                    'caption'  => _t('_modzzz_listing_caption_num_featured_days'),
                    'type'   => 'select',
                    'name' => 'quantity',
                    'values' => $aQuantity,
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_modzzz_listing_caption_err_featured_days'),
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
					'value' => _t("_modzzz_listing_form_caption_submit"),
				),
			)
		);
		  
		$oForm = new BxTemplFormView($aCustomForm);
		$oForm->initChecker();

		if ( $oForm->isSubmittedAndValid() ) {
			
			$iQuantity = (int)$oForm->getCleanValue('quantity');
			$iTotalCost = $iQuantity * $iFeaturedCost;
 
			if(getParam('modzzz_listing_featured_credits')=='on'){ 
	 
				if($iMoneyBalance < $iTotalCost){
					$sCode = _t('_modzzz_listing_insufficient_credits_message', number_format($iMoneyBalance,0), number_format($iTotalCost,0));
					$sCode .= '&nbsp;'._t('_modzzz_listing_buy_link', $sBuyCreditsLink);
					echo MsgBox($sCode);
					return;
				}
			}elseif(getParam('modzzz_listing_featured_points')=='on'){
	 
				if($iMoneyBalance < $iTotalCost){
					$sCode = _t('_modzzz_listing_insufficient_points_message', number_format($iMoneyBalance,0), number_format($iTotalCost,0));
					$sCode .= '&nbsp;'._t('_modzzz_listing_buy_link', $sBuyPointsLink);
					echo MsgBox($sCode);
					return;
				}
			}
  
			$bSuccess = $this->_oDb->updateFeaturedStatus($iEntryId);
			$sResultMsg = ($bSuccess) ? _t("_modzzz_listing_msg_feature_success") : _t("_modzzz_listing_msg_feature_failure"); 
		  
			if($bSuccess){
				$this->_oDb->updateFeaturedEntryExpiration($iEntryId, $iQuantity); 
				$this->_oDb->deductPayment($sPaymentUnit, $this->_iProfileId, $iTotalCost);  
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
				'title' => _t('_modzzz_listing_page_title_feature_item'),
				'content' => $sCode,
			);        
			
			echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true); 
		}
 	}
 
    function isAllowedPurchaseFeatured ($iEntryId, $isPerformAction = false) {
 		
		if ($this->isAdmin())
            return false;
 
		//if(getParam("modzzz_listing_buy_featured")!='on')
		$iEntryId = (int)$iEntryId;
		$aDataEntry = $this->_oDb->getEntryById ($iEntryId);

		if(getParam("modzzz_listing_featured_credits")!='on' && getParam("modzzz_listing_featured_points")!='on')
			return false;
  
		if($aDataEntry['featured'] && !$aDataEntry['featured_expiry_date'])
            return false;
 
        if (!( ($GLOBALS['logged']['admin']||$GLOBALS['logged']['member']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_LISTING_PURCHASE_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    } 
 
    function isAllowedModulePost($sModule) {
		switch($sModule){
			case 'deals':
				if(getParam("modzzz_listing_deals_active")=='on'){  
					$oDeals = BxDolModule::getInstance('BxDealsModule');
					if($oDeals->isAllowedAdd()){
						return true; 
					}
				}
			break;
			case 'coupons':
				if(getParam("modzzz_listing_coupons_active")=='on'){  
					$oCoupons = BxDolModule::getInstance('BxCouponsModule');
					if($oCoupons->isAllowedAdd()){
						return true; 
					}
				}
			break;
			case 'jobs':
				if(getParam("modzzz_listing_jobs_active")=='on'){  
					$oJobs = BxDolModule::getInstance('BxJobsModule');
					if($oJobs->isAllowedAdd()){
						return true; 
					}
				}
			break; 
		}
		return false;
	}
 
    function getModulePostUrl($sModule, $iId) {
		switch($sModule){
			case 'deals':
				if(getParam("modzzz_listing_deals_active")=='on'){  
					$oDeals = BxDolModule::getInstance('BxDealsModule'); 
				    return BX_DOL_URL_ROOT . $oDeals->_oConfig->getBaseUri() . 'browse/my&filter=add_deal&business='.$iId; 
 				}
			break;
			case 'coupons':
				if(getParam("modzzz_listing_coupons_active")=='on'){  
					$oCoupons = BxDolModule::getInstance('BxCouponsModule'); 
				    return BX_DOL_URL_ROOT . $oCoupons->_oConfig->getBaseUri() . 'browse/my&filter=add_coupon&business='.$iId; 
 				}
			break;
			case 'jobs':
				if(getParam("modzzz_listing_jobs_active")=='on'){  
					$oJobs = BxDolModule::getInstance('BxJobsModule'); 
				    return BX_DOL_URL_ROOT . $oJobs->_oConfig->getBaseUri() . 'browse/my&filter=add_job&business='.$iId; 
 				}
			break;  
		}
		return '';
	}

    function isAllowedEmbed(&$aDataEntry) { 
        return $this->isAllowedEdit($aDataEntry);   
    }
 
    function actionEmbed ($sUri) {
 
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

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t('_modzzz_listing_page_title_embed_video') => '',
		));

        if (!$this->isAllowedEmbed($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        bx_import ('EmbedForm', $this->_aModule);
		$oForm = new BxListingEmbedForm ($this, $aDataEntry);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid()) {        
  
			$iEntryId = $aDataEntry[$this->_oDb->_sFieldId];
			
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
 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			header ('Location:' . $sRedirectUrl);
            return;
        } 

        echo $oForm->getCode (); 
        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css'); 
        $this->_oTemplate->pageCode(_t('_modzzz_listing_page_title_embed_video') .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }
	//[end modzzz] embed video modification


	//ADDED multi-categories
	function actionAjaxMultiCategoryOptions(){
		$iCategoryId = (int)$_GET['id']; 
		echo $this->_oDb->getAjaxMultiCategoryOptions($iCategoryId, $this->isAdmin());
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
		$aDataEntry = $this->_oDb->getEntryById($iEntryId); 

		$iIconWidth = (int)getParam("modzzz_listing_icon_width");
		$iIconHeight = (int)getParam("modzzz_listing_icon_height"); 
		$sIcon = "";
	  
		$sFile = "iconfile";
 		$sPath = $this->_oConfig->getMediaPath();	
		if ( 0 < $_FILES[$sFile]['size'] && 0 < strlen( $_FILES[$sFile]['name'] ) ) {
			$sFileName = time();
			$sExt = $this->moveUploadedImage( $_FILES, $sFile, $sPath . $sFileName, '', false );
			if( strlen( $sExt ) && !(int)$sExt ) {
			 
				if($iEntryId)
					$this->_oDb->removeLogo($aDataEntry['icon']);
 
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
				$this->_oDb->removeLogo($aDataEntry['icon']); 

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

	function serviceGetWallAddComment($aInstructable)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_listing',
            'obj_privacy' => $this->_oPrivacy
        );
        return $this->_serviceGetWallAddComment($aInstructable, $aParams);
    }
 
    function _serviceGetWallAddComment($aEvent, $aParams)
    {
    	$iId = (int)$aEvent['object_id'];
        $iOwner = (int)$aEvent['owner_id'];
        $sOwner = $iOwner != 0 ? getNickName($iOwner) : _t('_Anonymous');

        $aContent = unserialize($aEvent['content']);
        if(empty($aContent) || empty($aContent['object_id']))
            return '';

		$iItem = (int)$aContent['object_id'];
        $aItem = $this->_oDb->getEntryByIdAndOwner($iItem, $iOwner, 1);
        if(empty($aItem) || !is_array($aItem))
        	return array('perform_delete' => true);

        if(!$aParams['obj_privacy']->check($aParams['txt_privacy_view_event'], $iItem, $this->_iProfileId))
            return '';

        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass($this->_sPrefix, $iItem);
        if(!$oCmts->isEnabled())
            return '';

        $aComment = $oCmts->getCommentRow($iId);
        if(empty($aComment) || !is_array($aComment))
        	return array('perform_delete' => true);

        $sImage = '';
        if($aItem[$this->_oDb->_sFieldThumb]) {
            $a = array('ID' => $aItem[$this->_oDb->_sFieldAuthorId], 'Avatar' => $aItem[$this->_oDb->_sFieldThumb]);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        }

        $sCss = '';
        $sCssPrefix = str_replace('_', '-', $this->_sPrefix);
        $sUri = $this->_oConfig->getUri();
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/';
        $sNoPhoto = $this->_oTemplate->getIconUrl('no-photo.png');
        if($aEvent['js_mode'])
            $sCss = $this->_oTemplate->addCss(array('wall_post.css', 'unit.css', 'twig.css'), true);
        else
            $this->_oTemplate->addCss(array('wall_post.css', 'unit.css', 'twig.css'));

        bx_import('Voting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Voting';
        $oVoting = new $sClass ($this->_sPrefix, 0, 0);

        $sTextWallObject = _t('_modzzz_' . $sUri . '_wall_object');

        $sTmplName = isset($aParams['templates']['main']) ? $aParams['templates']['main'] : 'modules/boonex/wall/|timeline_comment.html';
        return array(
            'title' => _t('_modzzz_' . $sUri . '_wall_added_new_title_comment', $sOwner, $sTextWallObject),
            'description' => $aComment['cmt_text'],
            'content' => $sCss . $this->_oTemplate->parseHtmlByName($sTmplName, array(
        		'mod_prefix' => $sCssPrefix,
	            'cpt_user_name' => $sOwner,
	            'cpt_added_new' => _t('_modzzz_' . $sUri . '_wall_added_new_comment'),
	            'cpt_object' => $sTextWallObject,
	            'cpt_item_url' => $sBaseUrl . $aItem[$this->_oDb->_sFieldUri],
	            'cnt_comment_text' => $aComment['cmt_text'],
	            'snippet' => $this->_oTemplate->unit($aItem, 'unit', $oVoting)
        	))
        );
    }

}
