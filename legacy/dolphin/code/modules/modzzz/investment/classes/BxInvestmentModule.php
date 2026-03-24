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

function modzzz_investment_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'investment') {
        $oMain = BxDolModule::getInstance('BxInvestmentModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxTemplSearchResult');
 

define ('BX_INVESTMENT_PHOTOS_CAT', 'Investment');
define ('BX_INVESTMENT_PHOTOS_TAG', 'investment');

define ('BX_INVESTMENT_VIDEOS_CAT', 'Investment');
define ('BX_INVESTMENT_VIDEOS_TAG', 'investment');

define ('BX_INVESTMENT_SOUNDS_CAT', 'Investment');
define ('BX_INVESTMENT_SOUNDS_TAG', 'investment');

define ('BX_INVESTMENT_FILES_CAT', 'Investment');
define ('BX_INVESTMENT_FILES_TAG', 'investment');
 
/*
 * Investment module
 *
 * This module allow users to create user's investment, 
 * users can rate, comment and discuss investment.
 * Investment can have photos, videos, sounds and files, uploaded
 * by investment's admins.
 *
 * 
 *
 * Profile's Wall:
 * 'add investment' event is displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new investment was created
 * change - investment was chaned
 * rate - somebody rated investment
 * commentPost - somebody posted comment in investment
 *
 *
 *
 * Memberships/ACL:
 * investment view investment - BX_INVESTMENT_VIEW_INVESTMENT
 * investment browse - BX_INVESTMENT_BROWSE
 * investment search - BX_INVESTMENT_SEARCH
 * investment add investment - BX_INVESTMENT_ADD_INVESTMENT
 * investment comments delete and edit - BX_INVESTMENT_COMMENTS_DELETE_AND_EDIT
 * investment edit any investment - BX_INVESTMENT_EDIT_ANY_INVESTMENT
 * investment delete any investment - BX_INVESTMENT_DELETE_ANY_INVESTMENT
 * investment mark as featured - BX_INVESTMENT_MARK_AS_FEATURED
 * investment approve investment - BX_INVESTMENT_APPROVE_INVESTMENT
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different investment
 * @see BxInvestmentModule::serviceHomepageBlock
 * BxDolService::call('investment', 'homepage_block', array());
 *
 * Profile block with user's investment
 * @see BxInvestmentModule::serviceProfileBlock
 * BxDolService::call('investment', 'profile_block', array($iProfileId));
 *

 *
 * Member menu item for investment (for internal usage only)
 * @see BxInvestmentModule::serviceGetMemberMenuItem
 * BxDolService::call('investment', 'get_member_menu_item', array());
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'modzzz_investment'
 * The following alerts are rised
 *
 
 *
 *  add - new investment was added
 *      $iObjectId - investment id
 *      $iSenderId - creator of a investment
 *      $aExtras['Status'] - status of added investment
 *
 *  change - investment's info was changed
 *      $iObjectId - investment id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed investment
 *
 *  delete - investment was deleted
 *      $iObjectId - investment id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - investment was marked/unmarked as featured
 *      $iObjectId - investment id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if investment was marked as featured and 0 - if investment was removed from featured 
 *
 */
class BxInvestmentModule extends BxDolTwigModule {

    var $_oPrivacy;
     
	var $_aQuickCache = array ();

    
	
	
	function BxInvestmentModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'filter';
        $this->_sPrefix = 'modzzz_investment';

        bx_import ('Privacy', $aModule);
        $this->_oPrivacy = new BxInvestmentPrivacy($this);
 
        $this->_oTemplate->addJs ('kerrors.js');

	    $this->_oConfig->init($this->_oDb);

        $GLOBALS['oBxInvestmentModule'] = &$this;
 
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
        parent::_actionHome(_t('_modzzz_investment_page_title_home'));
    }

    function actionFiles ($sUri) {
        parent::_actionFiles ($sUri, _t('_modzzz_investment_page_title_files'));
    }

    function actionSounds ($sUri) {
        parent::_actionSounds ($sUri, _t('_modzzz_investment_page_title_sounds'));
    }

    function actionVideos ($sUri) {
        parent::_actionVideos ($sUri, _t('_modzzz_investment_page_title_videos'));
    }

    function actionPhotos ($sUri) {
        parent::_actionPhotos ($sUri, _t('_modzzz_investment_page_title_photos'));
    }

    function actionComments ($sUri) {
        parent::_actionComments ($sUri, _t('_modzzz_investment_page_title_comments'));
    }
  
    function actionView ($sUri) {
        parent::_actionView ($sUri, _t('_modzzz_investment_msg_pending_approval'));
    }

    function actionUploadPhotos ($sUri) {        
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadPhotos', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_investment_page_title_upload_photos'));
    }

    function actionUploadVideos ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadVideos', 'videos', array ('videos_choice', 'videos_upload'), _t('_modzzz_investment_page_title_upload_videos'));
    }

    function actionUploadSounds ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadSounds', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_modzzz_investment_page_title_upload_sounds')); 
    }

    function actionUploadFiles ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadFiles', 'files', array ('files_choice', 'files_upload'), _t('_modzzz_investment_page_title_upload_files')); 
    }
  
    function actionCalendar ($iYear = '', $iMonth = '') {
        parent::_actionCalendar ($iYear, $iMonth, _t('_modzzz_investment_page_title_calendar'));
    }

    function actionSearch ($sKeyword = '', $sType = '',  $sCategory = '', $sCountry = '', $sState = '', $sCity = '') {

        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        if ($sKeyword) 
            $_GET['Keyword'] = $sKeyword;
        if ($sType) 
            $_GET['Type'] = $sType;
        if ($sCategory)
            $_GET['Category'] = explode(',', $sCategory); 
		if ($sCity) 
            $_GET['City'] = $sCity;
		if ($sState) 
            $_GET['State'] = $sState; 
         if ($sCountry)
            $_GET['Country'] = explode(',', $sCountry);

        if (is_array($_GET['Category']) && 1 == count($_GET['Category']) && !$_GET['Category'][0]) {
            unset($_GET['Category']);
            unset($sCategory);
        }
 
        if (is_array($_GET['Country']) && 1 == count($_GET['Country']) && !$_GET['Country'][0]) {
            unset($_GET['Country']);
            unset($sCountry);
        }
  
        if ($sCountry || $sType || $sCategory || $sKeyword || $sState || $sCity ) {
            $_GET['submit_form'] = 1;  
        }
        
        modzzz_investment_import ('FormSearch');
        $oForm = new BxInvestmentFormSearch ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
            modzzz_investment_import ('SearchResult');
            $o = new BxInvestmentSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Type'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country') );

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
        $this->_oTemplate->pageCode(_t('_modzzz_investment_caption_search'));
    } 


    function actionSearchOLD ($sKeyword = '', $sCategory = '', $sCity = '', $sCountry = '') {
 
        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        if ($sKeyword) 
            $_REQUEST['Keyword'] = $sKeyword;
        if ($sKeyword) 
            $_REQUEST['City'] = $sCity;
        if ($sCategory)
            $_REQUEST['Category'] = explode(',', $sCategory); 
        if ($sCountry)
            $_REQUEST['Country'] = explode(',', $sCountry);

        if (is_array($_REQUEST['Country']) && 1 == count($_REQUEST['Country']) && !$_REQUEST['Country'][0]) {
            unset ($_REQUEST['Country']);
            unset($sCountry);
        }

        if (is_array($_REQUEST['Category']) && 1 == count($_REQUEST['Category']) && !$_REQUEST['Category'][0]) {
            unset ($_REQUEST['Category']);
            unset($sCategory);
        }
 
        if ($sCategory || $sKeyword || $sCity || $sCountry) {
            $_REQUEST['submit_form'] = 1;
        }
        
        bx_import ('FormSearch', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormSearch';
        $oForm = new $sClass ();
        $oForm->initChecker();        

        if ($oForm->isSubmittedAndValid ()) {
 
            bx_import ('SearchResult', $this->_aModule);
            $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
            $o = new $sClass('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('Country'));
 
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
        $this->_oTemplate->pageCode(_t('_modzzz_investment_page_title_search'));
    }

    function actionAdd () {
        parent::_actionAdd (_t('_modzzz_investment_page_title_add'));
    }
	
	// freddy ajout function _actionAdd ($sTitle) 31 12 2024
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
    
		if(!isset($_POST['submit_form'])){
			return $this->showPackageSelectForm();
		}else{
			$this->showAddForm($sRedirectUrl);
		}
	}
 
    function showPackageSelectForm() {
 
		$bPaidInvestments = $this->isAllowedPaidInvestments (); 
		
		if($bPaidInvestments)
			$aPackage = $this->_oDb->getPackageList();
  

		$sPackageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/'.($this->isPermalinkEnabled() ? '?' : '&').'ajax=package&package=' ; 

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
				
				
  
                'investment_type' => array( 
                    'type' => 'select',
                    'name' => 'investment_type',
					'values'=> array(
					        '' => _t('_Select'),
							'entrepreneur' => _t('_modzzz_investment_entrepreneur'),
							'investor' => _t('_modzzz_investment_investor'),
							//freddy comment professional
							//'professional' => _t('_modzzz_investment_professional'),
					),
                    'caption' => _t('_modzzz_investment_form_caption_type_of_investment'), 
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_investment_form_err_type_of_investment'),
                    ),   
                ), 
 				 
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_modzzz_investment_form_caption_package'),
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),	
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_investment_form_err_package'),
                    ),   
                ),  
				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_investment_package_desc'),  
                ), 
				'Submit' => array (
					'type' => 'submit',
					'name' => 'submit_package',
					'value' => _t('_modzzz_investment_continue'),
					'colspan' => false,
				),   
    
            ),
        );
  
 		if(!$bPaidInvestments){ 
			unset($aForm['inputs']['package_id']);
 			unset($aForm['inputs']['package_desc']); 
		}

        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid ()) {
			$this->showAddForm(false);
		}else{  
			$sPromoText = $this->_oDb->getPromoText();

			echo $sPromoText .'<br>'. $oForm->getCode(); 
		}
    }
  
    function showAddForm($sRedirectUrl) {

        bx_import ('FormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
   			$bPaidInvestment = $this->isPaidPackage($oForm->getCleanValue('package_id')); 

			if($bPaidInvestment)
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

                $this->isAllowedAdd(true); // perform action                 

                $oForm->processMedia($iEntryId, $this->_iProfileId);

                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());
                $this->onEventCreate($iEntryId, $sStatus, $aDataEntry);

				if($this->isAllowedPaidInvestments()){
					$iPackageId = $oForm->getCleanValue('package_id');
					$aPackage = $this->_oDb->getPackageById($iPackageId);
					$fPrice = $aPackage['price']; 
					$iDays = $aPackage['days'];
   					$iFeatured = $aPackage['featured'];

					$sInvoiceStatus = ($fPrice) ? 'pending' : 'paid';
					$sInvoiceNo = $this->_oDb->createInvoice($iEntryId, $iPackageId, $fPrice, $iDays, $sInvoiceStatus);
						
					$this->_oDb->updateEntryInvoice($iEntryId, $sInvoiceNo); 
			   
					if($fPrice){ 
						$this->initializeCheckout($iEntryId, $fPrice, 1, 0, $oForm->getCleanValue('title'));  
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
					$iNumActiveDays = (int)getParam("modzzz_investment_free_expired");
					if($iNumActiveDays && (!$this->isAdmin()))
						$this->_oDb->updateEntryExpiration($iEntryId, $iNumActiveDays); 
 
					if (!$sRedirectUrl)  
						$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
				 
					header ('Location:' . $sRedirectUrl);
					exit;
				}  

            } else { 
                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
  
    function actionEdit ($iEntryId) {
        parent::_actionEdit ($iEntryId, _t('_modzzz_investment_page_title_edit'));
    }

    function actionDelete ($iEntryId) {
        parent::_actionDelete ($iEntryId, _t('_modzzz_investment_msg_investment_was_deleted'));
    }

    function actionMarkFeatured ($iEntryId) {
        parent::_actionMarkFeatured ($iEntryId, _t('_modzzz_investment_msg_added_to_featured'), _t('_modzzz_investment_msg_removed_from_featured'));
    }
 
    function actionSharePopup ($iEntryId) {
        parent::_actionSharePopup ($iEntryId, _t('_modzzz_investment_caption_share_investment'));
    }
 
    function actionTags() {
        parent::_actionTags (_t('_modzzz_investment_page_title_tags'));
    }    
 
    function actionPackages () { 
        $this->_oTemplate->pageStart();
        bx_import ('PagePackages', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PagePackages';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t('_modzzz_investment_page_title_packages'), false, false);
    }
 
    function actionCategories() {
        parent::_actionCategories (_t('_modzzz_investment_page_title_categories'));
    }    
 
    function actionDownload ($iEntryId, $iMediaId) {
 
        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'files');

        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getEntryByIdAndOwner((int)$iEntryId, 0, true))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }

        if (!($this->_iProfileId && $this->isAllowedView ($aDataEntry))) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }
 
    function actionInvite ($iEntryId) {
        $this->_actionInvite ($iEntryId, 'modzzz_investment_invitation', $this->_oDb->getParam('modzzz_investment_max_email_invitations'), _t('_modzzz_investment_invitation_sent'), _t('_modzzz_investment_no_users_msg'), _t('_modzzz_investment_caption_invite'));
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
                    $aPlus = array_merge (array ('NickName' => ' ' . $aRecipient['FirstName'].' '.$aRecipient['LastName']), $aPlusOriginal);
                    $iSuccess += sendMail(trim($aRecipient['Email']), $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus) ? 1 : 0;
                }
            }

            // send invitation to additional emails
            $iMaxCount = $iMaxEmailInvitations;
            $aEmails = preg_split ("#[,\s\\b]+#", $_REQUEST['inviter_emails']);
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
        $this->_oTemplate->pageCode($sTitle . $aDataEntry[$this->_oDb->_sFieldTitle]);
    }
  
    function _getInviteParams ($aDataEntry, $aInviter) {
        return array (
                'InvestmentName' => $aDataEntry['title'],
                'InvestmentLocation' => _t($GLOBALS['aPreValues']['country'][$aDataEntry['Country']]['LKey']) . (trim($aDataEntry['city']) ? ', '.$aDataEntry['city'] : ''),
                'InvestmentUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'InviterUrl' => $aInviter ? getProfileLink($aInviter['ID']) : 'javascript:void(0);',
                'InviterNickName' => $aInviter ? $aInviter['FirstName'].' '.$aInviter['LastName'] : _t('_modzzz_investment_user_unknown'),
                'InvitationText' => stripslashes(strip_tags($_REQUEST['inviter_text'])),
				 'PosterAvatar' =>  get_member_thumbnail($aInviter['ID'], 'none', true) ,
            );        
    }

 
    function actionItemBuy ($iEntryId) {

		$aDataEntry = $this->_oDb->getEntryById($iEntryId);

		$sItemUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];

        $this->_actionItemBuy ($iEntryId, 'modzzz_investment_make_buy_offer', _t('_modzzz_investment_caption_make_buy_offer'), _t('_modzzz_investment_message_sent', $sItemUrl), _t('_modzzz_investment_message_not_sent', $sItemUrl));
    }

    function _actionItemBuy ($iEntryId, $sEmailTemplate, $sTitle, $sMsgSuccess, $sMsgFail) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        bx_import ('ItemBuyForm', $this->_aModule);
		$oForm = new BxInvestmentItemBuyForm ($this, $aDataEntry);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
 
			$aInquirer = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getItemBuyParams ($aDataEntry, $aInquirer);
		  
			$iRecipient = $aDataEntry['author_id'];
 
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;
  
            // send message to investment owner
            if (isset($_REQUEST['message'])) { 
				 $aRecipient = getProfileInfo($iRecipient); 

				$sSubject = str_replace("<NickName>",$aInquirer['FirstName'].' '.$aInquirer['LastName'], $aTemplate['Subject']);
				$sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $sSubject);

				 $aPlus = array_merge (array ('RecipientName' => ' ' . $aRecipient['FirstName'].' '. $aRecipient['LastName']), $aPlusOriginal);

                 $iSuccess = sendMail(trim($aRecipient['Email']), $sSubject, $aTemplate['Body'], '', $aPlus) ? 1 : 0;  
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

    function _getItemBuyParams ($aDataEntry, $aBuyer) {
        return array (
                'InvestmentTitle' => $aDataEntry['title'], 
                'InvestmentUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'SenderLink' => $aBuyer ? getProfileLink($aBuyer['ID']) : 'javascript:void(0);',
                'SenderName' => $aBuyer ? $aBuyer['NickName'] : _t('_modzzz_investment_user_unknown'),
                'Message' => stripslashes(strip_tags($_REQUEST['message'])),
				'PosterAvatar' =>  get_member_thumbnail($aBuyer['ID'], 'none', true) ,
            );        
    }
 

    function actionInquire ($iEntryId) {
        $this->_actionInquire ($iEntryId, 'modzzz_investment_inquiry', _t('_modzzz_investment_caption_make_inquiry'), _t('_modzzz_investment_inquiry_sent'), _t('_modzzz_investment_inquiry_not_sent'));
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

        bx_import ('InquireForm', $this->_aModule);
		$oForm = new BxInvestmentInquireForm ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
 
			$aInquirer = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getInquireParams ($aDataEntry, $aInquirer);
		  
			$iRecipient = $aDataEntry['author_id'];
 
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;
  
            // send message to investment owner
			$sDesc = trim($oForm->getCleanValue('inquire_text'));
            if ($sDesc) { 

				 $this->_oDb->postInquiry($iEntryId, $this->_iProfileId, $sDesc);

				 $aRecipient = getProfileInfo($iRecipient); 

				 $sContactEmail = trim($aDataEntry['selleremail']) ? trim($aDataEntry['selleremail']) : trim($aRecipient['Email']);
 
				 $sSubject = str_replace("<NickName>",$aInquirer['FirstName'].' '.$aInquirer['LastName'], $aTemplate['Subject']);
				 $sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $sSubject);

				 $aPlus = array_merge (array ('RecipientName' => ' ' . $aRecipient['FirstName'].' '. $aRecipient['LastName']), $aPlusOriginal);
				 
				 /// FREDDY POUR ENVOYER LE MESSAGE DANS LA BOITE EMAIL////
				 $aPlusOriginal['Subject'] = $sSubject;
				  $this->inquireToInbox($aRecipient, $aTemplate, $aPlusOriginal); 
				 /////////////////////////////////////////////////////////////////

                 $iSuccess = sendMail($sContactEmail, $sSubject, $aTemplate['Body'], '', $aPlus) ? 1 : 0;  
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
        $this->_oTemplate->pageCode($sTitle . $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

    function _getInquireParams ($aDataEntry, $aInquirer) {
        return array (
                'InvestmentTitle' => $aDataEntry['title'], 
                'InvestmentUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'SenderLink' => $aInquirer ? getProfileLink($aInquirer['ID']) : 'javascript:void(0);',
                'SenderName' => $aInquirer ? $aInquirer['FirstName'].' '.$aInquirer['LastName'] : _t('_modzzz_investment_user_unknown'),
                'Message' => stripslashes(strip_tags($_REQUEST['inquire_text'])),
				
				 'PosterAvatar' =>  get_member_thumbnail($aInquirer['ID'], 'none', true) ,
            );        
    }
	
	
	//////////////////////FREDDY AJOUT POUR ENVOYER LESSAGE DANS MESSAGERIE LOCAM
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

	
	
	/////////////////////////////////FIN AJOUT ///////////////////////

 
    // ================================== external actions

    /**
     * Homepage block with different investment
     * @return html to display on homepage in a block
     */     
    function serviceIndexBlock () {

        if (!$this->_oDb->isAnyPublicContent()){  
			return '';
        } 

        bx_import ('PageMain', $this->_aModule);

        $o = new BxInvestmentPageMain ($this); 
        $o->sUrlStart = BX_DOL_URL_ROOT . 'index.php?';

		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));
  
        $sDefaultHomepageTab = $this->_oDb->getParam('modzzz_investment_homepage_default_tab');
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
            $this->_oDb->getParam('modzzz_investment_perpage_homepage'), 
            array(
                _t('_modzzz_investment_tab_featured') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=featured', 'active' => 'featured' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_investment_tab_recent') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=recent', 'active' => 'recent' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_investment_tab_top') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=top', 'active' => 'top' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_investment_tab_popular') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=popular', 'active' => 'popular' == $sBrowseMode, 'dynamic' => true),
            )
        );
    }

    /**
     * Profile block with user's investment
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlock ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxInvestmentPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']) . '?';
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_investment_perpage_profile'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }

     /**
     * Account block with different events
     * @return html to display area investments in account page a block
     */  
    function serviceAreaBlock () {

        if (!$this->_oDb->isAnyPublicContent())
            return '';

		$aProfileInfo = getProfileInfo($this->_iProfileId);
		$sCity = $aProfileInfo['City'];

		if(!$sCity)
			return;

        bx_import ('PageMain', $this->_aModule);
        $o = new BxInvestmentPageMain ($this);        
        $o->sUrlStart = BX_DOL_URL_ROOT . 'member.php?';
 
        return $o->ajaxBrowse(
            'local',
            $this->_oDb->getParam('modzzz_investment_perpage_accountpage'),
			array(),
			$sCity
        );
    }

    /**
     * Account block with different events
     * @return html to display member investments in account page a block
     */ 
    function serviceAccountPageBlock () {
  
        $aProfile = getProfileInfo($this->_iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxInvestmentPageMain ($this);        
        $o->sUrlStart = $GLOBALS['site']['url'] . 'member.php?';
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_investment_perpage_profile'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }


    function serviceGetMemberMenuItem () {
        parent::_serviceGetMemberMenuItem (_t('_modzzz_investment'), _t('_modzzz_investment'), 'money');
    }
 

    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        return parent::_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_modzzz_investment_spy_post',
            'change' => '_modzzz_investment_spy_post_change', 
            'rate' => '_modzzz_investment_spy_rate',
            'commentPost' => '_modzzz_investment_spy_comment',
        ));
    }

    function serviceGetSubscriptionParams ($sAction, $iEntryId) {

        $a = array (
            'change' => _t('_modzzz_investment_sbs_change'),
            'commentPost' => _t('_modzzz_investment_sbs_comment'),
            'rate' => _t('_modzzz_investment_sbs_rate'), 
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
			'name' => _t('_modzzz_investment_packages'),
			'bx_repeat:items' => $aPackage,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages/'
		));


		$aPackage = $this->_oDb->getRow("SELECT * FROM `" . $this->_oDb->_sPrefix . "packages` WHERE  `id` = '$iPackage'");
		  
		$sFormName = 'packages_form';
  
	    if($iPackage){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_investment_categ_btn_edit'),
				'action_delete' => _t('_modzzz_investment_categ_btn_delete'), 
				'action_add' => _t('_modzzz_investment_categ_btn_add')  
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_investment_categ_btn_save')
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
 				'action_add' => _t('_modzzz_investment_categ_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_packages',$aVars);
		}
 
		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));
	}
 
    function actionAdministration ($sUrl = '',$sParam1='',$sParam2='') {

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
        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_investment_admin_links'));
*/

        $aMenu = array(
            'pending_approval' => array(
                'title' => _t('_modzzz_investment_menu_admin_pending_approval'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_approval', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false)),
            ),
            'admin_entries' => array(
                'title' => _t('_modzzz_investment_menu_admin_entries'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ),   
			'invoices' => array(
                'title' => _t('_modzzz_investment_menu_manage_invoices'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/invoices',
                '_func' => array ('name' => 'actionAdministrationInvoices', 'params' => array($sParam1)),
            ), 			
			'orders' => array(
                'title' => _t('_modzzz_investment_menu_manage_orders'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/orders',
                '_func' => array ('name' => 'actionAdministrationOrders', 'params' => array($sParam1)),
            ),
			'packages' => array(
                'title' => _t('_modzzz_investment_menu_manage_packages'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages',
                '_func' => array ('name' => 'actionAdministrationPackages', 'params' => array($sParam1)),
            ),    
            'create' => array(
                'title' => _t('_modzzz_investment_menu_admin_add_entry'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
            'promo' => array(
                'title' => _t('_modzzz_investment_menu_admin_promo_details'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/promo',
                '_func' => array ('name' => 'actionAdministrationPromo', 'params' => array($sParam1)),
            ), 
            'settings' => array(
                'title' => _t('_modzzz_investment_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'pending_approval';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_investment_admin_block_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css'));
     
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_investment_page_title_administration'));
    }

    function actionAdministrationPromo($sParam1='') {
 
		if (!($aDataEntry = $this->_oDb->getPromoData())) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}
  
		if (!$this->isAllowedEdit($aDataEntry)) {
			$this->_oTemplate->displayAccessDenied ();
			return;
		}			
  
		ob_start();        
		$this->_editPromoForm($aDataEntry);
		$aVars = array (
			'content' => ob_get_clean(),
		); 
	  
        return $this->_oTemplate->parseHtmlByName('default_padding', $aVars);
    }

    function _editPromoForm ($aDataEntry) { 
  
        bx_import ('FormPromoEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormPromoEdit';
        $oForm = new $sClass ($this, $aDataEntry['id']);
 
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {

              if ($oForm->update (1, array())) {
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "administration/settings" );
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        }
 
    }
     
    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('Investment');
    }
  
    function actionAdministrationManage ($isAdminEntries = false) {
        return parent::_actionAdministrationManage ($isAdminEntries, '_modzzz_investment_admin_delete', '_modzzz_investment_admin_activate');
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
			'action_activate' => '_modzzz_investment_admin_activate',
			'action_delete' => '_modzzz_investment_admin_delete',
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
 			'action_delete' => '_modzzz_investment_admin_delete',
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
   
     function _manageSalePending ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 14, $bActionsPanel = true) {

        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = $sMode . '_admin';

        if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;

        $sPagination = $sActionsPanel = '';
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
        } elseif (!($sContent = $o->displaySalePendingResultBlock($sMode))) {
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

    // ================================== permissions
    
	function isPaidInvestment($iEntryId){
		$iEntryId = (int)$iEntryId;
         
		if (getParam('modzzz_investment_paid_active')!='on') 
            return false;	
 
		$aEntry = $this->_oDb->getEntryById($iEntryId);
		$sInvoiceNo = $aEntry['invoice_no'];
 
 		$aInvoice = $this->_oDb->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];

		return $this->_oDb->isPaidPackage($iPackageId);  
	}

 	function isPaidPackage($iPackageId){
 
		if(!$this->isAllowedPaidInvestments())
			return false;

		return $this->_oDb->isPaidPackage($iPackageId);  
	}
 
    function isAllowedPaidInvestments ($bCheckAdmin=true) {
  
		 if($bCheckAdmin && $this->isAdmin())
			return false;

        // admin always have access  
        if (getParam('modzzz_investment_paid_active')=='on') 
            return true;	
            
		return false;
	}

    function isAllowedView ($aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_VIEW_INVESTMENT, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
 
        // check user group  
        $isAllowed =  $this->_oPrivacy->check('view_investment', $aDataEntry['id'], $this->_iProfileId);   
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
		$aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_ADD_INVESTMENT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedEdit ($aDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;

        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_EDIT_ANY_INVESTMENT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedMarkAsFeatured ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }
  
    function isAllowedDelete (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_DELETE_ANY_INVESTMENT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }     
  
    function isAllowedInquire (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_MAKE_INQUIRY, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 

    function isAllowedBuy (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_BUY_ITEM, $isPerformAction);
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
		$aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedManageAdmins($aDataEntry) {
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))
            return true;
        return false;
    }
   
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('permalinks_investment') == 'on'));
		 
        return $bEnabled;
    }


    function _defineActions () {
        defineMembershipActions(array('investment purchase','investment mark as favorite', 'investment relist', 'investment extend','investment purchase featured', 'investment view investment', 'investment browse', 'investment search', 'investment add investment', 'investment comments delete and edit', 'investment edit any investment', 'investment delete any investment', 'investment mark as featured', 'investment approve investment', 'investment make inquiry', 'investment buy item', 'investment photos add', 'investment videos add', 'investment files add', 'investment sounds add'));
    }

 
    function _browseMy (&$aProfile) {        
        parent::_browseMy ($aProfile, _t('_modzzz_investment_page_title_my_investment'));
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
         $a = explode ($sDiv, $s);
        $sClass = $sClassName ? 'class="'.$sClassName.'"' : '';
        
		$aCats = array();
        foreach ($a as $sName)
            $aCats[] = '<a '.$sClass.' href="' . $sLinkStart . urlencode(title2uri($sName)) . '">'.$sName.'</a>&#160';
        
		$sRet = implode('&#160&#183;&#160', $aCats);

        return $sRet;
    }
  
    function actionPaypalFeaturedProcess($iProfileId, $iInvestmentId) {
    
        $aData = &$_REQUEST;

        if($aData) {
			$iQuantity = (int)$aData['quantity'];

			$aDataEntry = $this->_oDb->getEntryById($iInvestmentId); 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
 
        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPurchaseFeatured(_t('_modzzz_investment_featured_purchase_failed')); 
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
				$this->actionPurchaseFeatured($iInvestmentId, _t('_modzzz_investment_featured_purchase_failed'));
			}else { 
				
				$fAmount = $this->_getReceivedAmount($aData);

				if($this->_oDb->isExistFeaturedTransaction($iProfileId, $aData['txn_id'])) {
					$this -> actionPurchaseFeatured($iInvestmentId, _t('_modzzz_investment_transaction_completed_already')); 
				} else {
					if( $this->_oDb->saveFeaturedTransactionRecord($iProfileId, $iInvestmentId,  $iQuantity, $fAmount, $aData['txn_id'], 'Paypal Purchase')) {

						$this->_oDb->updateFeaturedEntryExpiration($iInvestmentId, $iQuantity); 
			   
						$this->actionPurchaseFeatured($iInvestmentId, _t('_modzzz_investment_purchase_success',  $iQuantity));
					} else {
						$this -> actionPurchaseFeatured($iInvestmentId, _t('_modzzz_investment_trans_save_failed'));
					}
				}
			}
	 
		}
    }


    function actionPaypalProcess($iProfileId, $iInvestmentId) {
    
        $aData = &$_REQUEST;

        if($aData) {
    
			$aDataEntry = $this->_oDb->getEntryById($iInvestmentId); 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
 
        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPostPurchase(_t('_modzzz_investment_purchase_failed')); 
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
  
			if($aData['txn_type'] != 'web_accept') {
				$this->actionPostPurchase(_t('_modzzz_investment_purchase_failed'));
			}else{ 
				if($this->_oDb->isExistPaypalTransaction($iProfileId, $aData['txn_id'])) { 
					$this -> actionPostPurchase(_t('_modzzz_investment_transaction_completed_already', $sRedirectUrl)); 
				} else {
					if( $this->_oDb->saveTransactionRecord($iProfileId, $iInvestmentId, $aData['txn_id'], 'Paypal Purchase')) { 
						
						$this->_oDb->setItemStatus($iInvestmentId, 'approved');

						$this->_oDb->setInvoiceStatus($iInvestmentId, 'paid');

						$this->actionPostPurchase(_t('_modzzz_investment_purchase_success', $sRedirectUrl));
					} else {
						$this -> actionPostPurchase(_t('_modzzz_investment_trans_save_failed'));
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

	function initializeCheckout($iInvestmentId, $fTotalCost, $iQuantity=1, $bFeatured=0, $sTitle='') {
  
		$aFormData = array(
			'cmd' => '_xclick',
			'amount' => sprintf( "%.2f", (float)$fTotalCost)
		);
    	 
		if($bFeatured){
			$sNotifyUrl = $this->_oConfig->getFeaturedCallbackUrl() . $this->_iProfileId .'/'. $iInvestmentId;
			$sItemDesc = _t('_modzzz_investment_paypal_featured_item_desc', $sTitle);
 		}else{
			$sNotifyUrl = $this->_oConfig->getPurchaseCallbackUrl() . $this->_iProfileId .'/'. $iInvestmentId;
			$sItemDesc = _t('_modzzz_investment_paypal_item_desc', $sTitle);
		}

		$aDataEntry = $this->_oDb->getEntryById($iInvestmentId);
 		$sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $aFormData = array_merge($aFormData, array(
			'business' => getParam('modzzz_investment_paypal_email'), 
            'item_name' => $sItemDesc,
			'amount' => $fTotalCost, 
            'item_number' => $iInvestmentId,
            'quantity' => $iQuantity,
            'currency_code' => $this->_oConfig->getPurchaseCurrency(),
            'no_note' => '1',
            'no_shipping' => '1',  
			'return' => $this->_oConfig->getReturnUrl() . $sUri,
			'notify_url' => $sNotifyUrl,  
			'rm' => '1'
        ));
  
    	Redirect($this->_oConfig->getPurchaseBaseUrl(), $aFormData, 'post', "Investments Listing");
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
        $this->_oTemplate->pageCode(_t('_modzzz_investment_post_purchase_header')); 
    }
  
	//modzzz.com
    function actionPurchaseFeatured($iInvestmentId, $sTransMessage = '') {
 
        if(! $this->_iProfileId) {
            header('location: ' . BX_DOL_URL_ROOT . 'member.php');
        }

	    if($sTransMessage){
			$sMessageOutput = MsgBox($sTransMessage);  
		}
 
		$iPerDayCost = getParam('modzzz_investment_featured_cost');

		$aDataEntry = $this->_oDb->getEntryById($iInvestmentId);
		$sTitle = $aDataEntry['title'];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

		$aForm = array(
            'form_attrs' => array(
                'name' => 'buy_featured_form',
                'method' => 'post', 
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_featured/'.$iInvestmentId,
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
					'caption'  => _t('_modzzz_investment_form_caption_title'),
                    'content' => $sTitle,
                ), 
                'cost' => array(
                    'type' => 'custom',
                    'name' => 'cost',
					'caption'  => _t('_modzzz_investment_featured_cost_per_day'),
                    'content' => $iPerDayCost .' '. $this->_oConfig->getPurchaseCurrency(),
                ), 
                'status' => array(
                    'type' => 'custom',
                    'name' => 'status',
					'caption'  => _t('_modzzz_investment_featured_status'),
                    'content' => ($aDataEntry['featured']) ? _t('_modzzz_investment_featured_until') . ' '. $this->_oTemplate->filterCustomDate($aDataEntry['featured_expiry_date']) : _t('_modzzz_investment_not_featured'), 
                ),  
                'quantity' => array(
                    'caption'  => _t('_modzzz_investment_caption_num_featured_days'),
                    'type'   => 'text',
                    'name' => 'quantity',
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_modzzz_investment_caption_err_featured_days'),
                    ),
                ),
                'submit' => array(
                    'type'  => 'submit',
                    'value' => ($aDataEntry['featured']) ? _t('_modzzz_investment_extend_featured') : _t('_modzzz_investment_get_featured'),
                    'name'  => 'submit_purchase',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  

        if ($oForm->isSubmittedAndValid() && $oForm->getCleanValue('quantity')) { 
 
			$fCost =  number_format($iPerDayCost, 2); 

            //header('location:' . $this->_oDb->generateFeaturedPaymentUrl($iInvestmentId, $oForm->getCleanValue('quantity'), $fCost));

			$this->initializeCheckout($iInvestmentId, $fCost, $oForm->getCleanValue('quantity'), true, $sTitle);  
			return; 
        } else {
             echo $sMessageOutput . $oForm->getCode();
        }

        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->pageCode(_t('_modzzz_investment_purchase_featured')); 
    }
  
    function isAllowedPurchaseFeatured ($aDataEntry, $isPerformAction = false) {
  
		if(getParam("modzzz_investment_buy_featured")!='on')
			return false;
  
		if ($this->isAdmin())
            return false;

		if($aDataEntry['featured'] && !$aDataEntry['featured_expiry_date'])
            return false;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
  
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_PURCHASE_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    } 
	
	
	//[begin] favorites
    function isAllowedMarkAsFavorite ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_MARK_AS_FAVORITE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

	function isFavorite($iProfileId, $iEntryId){
		return $this->_oDb->isFavorite($iProfileId, $iEntryId);
	}
 
    function actionMarkFavorite ($iEntryId) {
        $this->_actionMarkFavorite ($iEntryId, _t('_modzzz_investment_msg_added_to_favorite'), _t('_modzzz_investment_msg_removed_from_favorite'));
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
	
	

    function actionLocal ($sCountry='', $sState='') { 
        $this->_oTemplate->pageStart();
        bx_import ('PageLocal', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageLocal';
        $oPage = new $sClass ($this, $sCountry, $sState);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

         $this->_oTemplate->pageCode(_t('_modzzz_investment_page_title_local'), false, false);
    } 

    function actionLocalCountry ($sCountry='') { 
        $this->_oTemplate->pageStart();
        bx_import ('PageLocal', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageLocal';
        $oPage = new $sClass ($this, $sCountry);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t('_modzzz_investment_page_title_local'), false, false);
    } 
 
    function isMembershipEnabledForImages () {
        return ($this->_isMembershipEnabledFor ('BX_PHOTOS_ADD') && $this->_isMembershipEnabledFor ('BX_INVESTMENT_PHOTOS_ADD'));
    }

    function isMembershipEnabledForVideos () {
        return $this->_isMembershipEnabledFor ('BX_VIDEOS_ADD');
        return ($this->_isMembershipEnabledFor ('BX_VIDEOS_ADD') && $this->_isMembershipEnabledFor ('BX_INVESTMENT_VIDEOS_ADD')); 
    }

    function isMembershipEnabledForSounds () {
        return ($this->_isMembershipEnabledFor ('BX_SOUNDS_ADD') && $this->_isMembershipEnabledFor ('BX_INVESTMENT_SOUNDS_ADD'));
    }

    function isMembershipEnabledForFiles () {
        return ($this->_isMembershipEnabledFor ('BX_FILES_ADD') && $this->_isMembershipEnabledFor ('BX_INVESTMENT_FILES_ADD'));
    }
 
    function _isMembershipEnabledFor ($sMembershipActionConstant) { 
        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'investment photos add', 'investment sounds add', 'investment videos add', 'investment files add'));
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

     function isAllowedPostInForum(&$aDataEntry, $iProfileId = -1) {
        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) || $this->_oPrivacy->check('post_in_forum', $aDataEntry['id'], $iProfileId);
    }

    function isEntryAdmin($aDataEntry, $iProfileId = 0) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;
        return $this->_oDb->isGroupAdmin ($aDataEntry['id'], $iProfileId) && isProfileActive($iProfileId);
    } 


	/*reinvestment and extension */
  
    function isAllowedPremium (&$aDataEntry, $isPerformAction = false) {
  
        if (getParam('modzzz_investment_paid_active')!='on') 
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
        $aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_PURCHASE, $isPerformAction);
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
 
        $this->_actionPremium($iEntryId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_modzzz_investment_page_title_premium'));
	}
 
    function _actionPremium ($iEntryId) {
   
	   $aDataEntry = $this->_oDb->getEntryById($iEntryId);
	   $sTitle = $aDataEntry[$this->_oDb->_sFieldTitle];

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
  
                'title' => array( 
                    'type' => 'custom',
                    'caption' => _t('_modzzz_investment_form_caption_current_item'),  
					'content'=> $sTitle,
                 ),  
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_modzzz_investment_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_investment_form_err_package'),
                    ),   
                ),   
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_investment_package_desc'),  
                ), 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_investment_continue'),
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

     /***/


    function isAllowedRelist (&$aDataEntry, $isPerformAction = false) {
  
		if($aDataEntry['status'] != 'expired')
            return false;

		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_RELIST, $isPerformAction);
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
 
        $this->_actionRelist($iEntryId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_modzzz_investment_page_title_relist'));
	}
 
    function _actionRelist ($iEntryId) {
   
	    $aDataEntry = $this->_oDb->getEntryById($iEntryId);
	    $sTitle = $aDataEntry[$this->_oDb->_sFieldTitle];
 
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
                    'type' => 'custom',
                    'caption' => _t('_modzzz_investment_form_caption_current_item'),  
					'content'=> $sTitle,
                ), 
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_modzzz_investment_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_investment_form_err_package'),
                    ),   
                ),  
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_investment_package_desc'),  
                ),  
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_investment_continue'),
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
        
        if (getParam('modzzz_investment_paid_active')!='on') 
            return false;	
		 
		if($aDataEntry['status'] != 'approved')
            return false;

		if(!$aDataEntry['expiry_date'])
            return false;

        //if ($this->isAdmin() && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
        //    return false;

		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_INVESTMENT_EXTEND, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 


    function actionExtend ($iEntryId) {

        if (!$aDataEntry = $this->_oDb->getEntryById((int)$iEntryId)) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

 
        if (!$this->isPaidInvestment($iEntryId)) {
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


        $this->_actionExtend($iEntryId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_modzzz_investment_page_title_extend'));
	}
 
    function _actionExtend ($iEntryId) {
   
		$aDataEntry = $this->_oDb->getEntryById($iEntryId);
 	    $sTitle = $aDataEntry[$this->_oDb->_sFieldTitle];
 
		$iPackageId = $this->_oDb->getPackageIdByInvoiceNo($aDataEntry['invoice_no']);
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

                'title' => array( 
                    'type' => 'custom',
                    'caption' => _t('_modzzz_investment_form_caption_current_item'),  
					'content'=> $sTitle,
                 ), 

 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => $sPackageDesc,  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_investment_package_desc'),  
                ),   
 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_investment_extend'),
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
  
	/* functions added for v7.1 */

    function serviceGetMemberMenuItemAddContent ()
    {
        if (!$this->isAllowedAdd())
            return '';
        return parent::_serviceGetMemberMenuItem (_t('_modzzz_investment_investment_single'), _t('_modzzz_investment_investment_single'), 'money', false, '&filter=add_investment');
    }
 
    /**
     * Install map support
     */
    function serviceMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('investment', array(
            'part' => 'investment',
            'title' => '_modzzz_investment',
            'title_singular' => '_modzzz_investment_single',
            'icon' => 'modules/modzzz/investment/|map_marker.png',
            'icon_site' => 'money',
            'join_table' => 'modzzz_investment_main',
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
            'join_field_privacy' => 'allow_view_investment_to',
            'permalink' => 'modules/?r=investment/view/',
        )));
    }
 
	//remove old one first
    function serviceGetWallPost ($aEvent)
    {
        $aParams = array(
            'txt_object' => '_modzzz_investment_wall_object',
            'txt_added_new_single' => '_modzzz_investment_wall_added_new',
            'txt_added_new_plural' => '_modzzz_investment_wall_added_new_items',
            'txt_privacy_view_event' => 'view_investment',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallPost ($aEvent, $aParams);
    }
 
    function serviceGetWallPostComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_investment',
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
            'txt_privacy_view_event' => 'view_investment',
            'obj_privacy' => $this->_oPrivacy,
            'templates' => array(
                'grouped' => 'wall_outline_grouped'
            )
        );
        return parent::_serviceGetWallPostOutline($aEvent, 'money', $aParams);
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

    function _formatSnippetText ($aEntryData, $iMaxLen = 300, $sField='')
    {  $sField = ($sField) ? $sField : $aEntryData[$this->_oDb->_sFieldDescription];
        return strmaxtextlen($sField, $iMaxLen);
    }

    function isAllowedReadForum(&$aDataEntry, $iProfileId = -1)
    {
        return true;
    }


}