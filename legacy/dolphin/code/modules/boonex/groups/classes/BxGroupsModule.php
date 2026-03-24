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

function bx_groups_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'groups') {
        $oMain = BxDolModule::getInstance('BxGroupsModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxDolInstallerUtils');

define ('BX_GROUPS_PHOTOS_CAT', 'Groups');
define ('BX_GROUPS_PHOTOS_TAG', 'groups');

define ('BX_GROUPS_VIDEOS_CAT', 'Groups');
define ('BX_GROUPS_VIDEOS_TAG', 'groups');

define ('BX_GROUPS_SOUNDS_CAT', 'Groups');
define ('BX_GROUPS_SOUNDS_TAG', 'groups');

define ('BX_GROUPS_FILES_CAT', 'Groups');
define ('BX_GROUPS_FILES_TAG', 'groups');

define ('BX_GROUPS_MAX_FANS', 1000);
 

/*
 * Groups module
 *
 * This module allow users to create user's groups, 
 * users can rate, comment and discuss group.
 * Group can have photos, videos, sounds and files, uploaded
 * by group's fans and/or admins.
 *
 * 
 *
 * Profile's Wall:
 * 'add group' group is displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new group was created
 * change - group was chaned
 * join - somebody joined group
 * rate - somebody rated group
 * commentPost - somebody posted comment in group
 *
 *
 *
 * Memberships/ACL:
 * groups view group - BX_GROUPS_VIEW_GROUP
 * groups browse - BX_GROUPS_BROWSE
 * groups search - BX_GROUPS_SEARCH
 * groups add group - BX_GROUPS_ADD_GROUP
 * groups comments delete and edit - BX_GROUPS_COMMENTS_DELETE_AND_EDIT
 * groups edit any group - BX_GROUPS_EDIT_ANY_GROUP
 * groups delete any group - BX_GROUPS_DELETE_ANY_GROUP
 * groups mark as featured - BX_GROUPS_MARK_AS_FEATURED
 * groups approve groups - BX_GROUPS_APPROVE_GROUPS
 * groups broadcast message - BX_GROUPS_BROADCAST_MESSAGE
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different groups
 * @see BxGroupsModule::serviceHomepageBlock
 * BxDolService::call('groups', 'homepage_block', array());
 *
 * Profile block with user's groups
 * @see BxGroupsModule::serviceProfileBlock
 * BxDolService::call('groups', 'profile_block', array($iProfileId));
 *
 * Group's forum permissions (for internal usage only)
 * @see BxGroupsModule::serviceGetForumPermission
 * BxDolService::call('groups', 'get_forum_permission', array($iMemberId, $iForumId));
 *
 * Member menu item for groups (for internal usage only)
 * @see BxGroupsModule::serviceGetMemberMenuItem
 * BxDolService::call('groups', 'get_member_menu_item', array());
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'bx_groups'
 * The following alerts are rised
 *
 *  join - user joined a group
 *      $iObjectId - group id
 *      $iSenderId - joined user
 *
 *  join_request - user want to join a group
 *      $iObjectId - group id
 *      $iSenderId - user id which want to join a group
 *
 *  join_reject - user was rejected to join a group
 *      $iObjectId - group id
 *      $iSenderId - regected user id
 *
 *  fan_remove - fan was removed from a group
 *      $iObjectId - group id
 *      $iSenderId - fan user if which was removed from admins
 *
 *  fan_become_admin - fan become group's admin
 *      $iObjectId - group id
 *      $iSenderId - nerw group's fan user id
 *
 *  admin_become_fan - group's admin become regular fan
 *      $iObjectId - group id
 *      $iSenderId - group's admin user id which become regular fan
 *
 *  join_confirm - group's admin confirmed join request
 *      $iObjectId - group id
 *      $iSenderId - condirmed user id
 *
 *  add - new group was added
 *      $iObjectId - group id
 *      $iSenderId - creator of a group
 *      $aExtras['Status'] - status of added group
 *
 *  change - group's info was changed
 *      $iObjectId - group id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed group
 *
 *  delete - group was deleted
 *      $iObjectId - group id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - group was marked/unmarked as featured
 *      $iObjectId - group id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if group was marked as featured and 0 - if group was removed from featured 
 *
 */
class BxGroupsModule extends BxDolTwigModule {

    var $_oPrivacy;
    var $_oSubPrivacy; 
    var $_aQuickCache = array ();

    function BxGroupsModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'bx_groups_filter';
        $this->_sPrefix = 'bx_groups';

	    $this->_oConfig->init($this->_oDb);

        bx_import ('Privacy', $aModule);
        bx_import ('SubPrivacy', $aModule);
        $this->_oPrivacy = new BxGroupsPrivacy($this);

		//reloads states on Add form
		if($_GET['ajax']=='state')
		{
			$sCountryCode = $_GET['country'];
			echo $this->_oDb->getStateOptions($sCountryCode);
			exit;
		}		

        $GLOBALS['oBxGroupsModule'] = &$this;
    }

    function actionHome () {
        parent::_actionHome(_t('_bx_groups_page_title_home'));
    }

    function actionFiles ($sUri) {
        parent::_actionFiles ($sUri, _t('_bx_groups_page_title_files'));
    }

    function actionSounds ($sUri) {
        parent::_actionSounds ($sUri, _t('_bx_groups_page_title_sounds'));
    }

    function actionVideos ($sUri) {
        parent::_actionVideos ($sUri, _t('_bx_groups_page_title_videos'));
    }

    function actionPhotos ($sUri) {
        parent::_actionPhotos ($sUri, _t('_bx_groups_page_title_photos'));
    }

    function actionComments ($sUri) {

		$sTitle = _t('_bx_groups_page_title_comments');

		$this->sUri = $sUri;
  
        if (!($aDataEntry = $this->_preProductTabs($sUri, $sTitle)))
            return;

        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $o = new $sClass ($this->_sPrefix, (int)$aDataEntry[$this->_oDb->_sFieldId]);
        if (!$o->isEnabled()) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

		if (!$this->isAllowedViewComments($aDataEntry)){
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $sRet = $o->getCommentsFirst ();

        $this->_oTemplate->pageStart();

        echo DesignBoxContent ($sTitle, $sRet, 1);

        $this->_oTemplate->pageCode($sTitle, 0, 0);
    }
 
    function actionBrowseFans ($sUri) {

		$this->sUri = $sUri;

        parent::_actionBrowseFans ($sUri, 'isAllowedViewFans', 'getFansBrowse', $this->_oDb->getParam('bx_groups_perpage_browse_fans'), 'browse_fans/', _t('_bx_groups_page_title_fans'));
    }

/*
    function actionView ($sUri) {
        parent::_actionView ($sUri, _t('_bx_groups_msg_pending_approval'));
    }
*/

    function actionView ($sUri) {  

		$this->sUri = $sUri;

        $this->_actionView ($sUri, _t('_bx_groups_msg_pending_approval'));
    }
 
    function _preProductTabs ($sUri, $sSubTab = '')
    {
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
            return false;
        }

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending' && !$this->isAdmin() && !($aDataEntry[$this->_oDb->_sFieldAuthorId] == $this->_iProfileId && $aDataEntry[$this->_oDb->_sFieldAuthorId]))  {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => $sSubTab ? BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri] : '',
            $sSubTab => '',
        ));
 
        if ((!$this->_iProfileId || $aDataEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedView($aDataEntry, true)) {

			$isFan = $this->_oDb->isFan((int)$aDataEntry['id'], $this->_iProfileId, 1);

			if( !($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) && $aDataEntry[$this->_oDb->_sFieldAllowViewTo]=='f' && (!$isFan)){ 
				return $aDataEntry;
			}else{
				$this->_oTemplate->displayAccessDenied ();
				return false;
			}
        }
 
        return $aDataEntry;
    }
 
    function _actionView ($sUri, $sMsgPendingApproval) {

        if (!($aDataEntry = $this->_preProductTabs($sUri)))
            return;

        $this->_oTemplate->pageStart();

		$isFan = $this->_oDb->isFan((int)$aDataEntry['id'], $this->_iProfileId, 1);

		if( !($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) && $aDataEntry[$this->_oDb->_sFieldAllowViewTo]=='f' && (!$isFan)){ 
		 
			bx_import ('PageViewPrivate', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'PageViewPrivate';

		}else{ 
			bx_import ('PageView', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'PageView';
        } 
        $oPage = new $sClass ($this, $aDataEntry);
 
        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

		//begin 2.1.9
 		$iFansCount = $this->_oDb->getFanCount($aDataEntry[$this->_oDb->_sFieldId]); 
		$aMembership = getMemberMembershipInfo($aDataEntry[$this->_oDb->_sFieldAuthorId]);
		$iThreshold = (int)$this->_oDb->getMembershipThreshold($aMembership['ID']);

		if($iThreshold && ($iFansCount >= $iThreshold)){  
             $aVars = array ('msg' => _t('_bx_groups_threshold_msg_notification_members', $iThreshold));  
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }
		//end 2.1.9

        $sPageCode = $oPage->getCode();
 
		// add group customizer 
		if (BxDolInstallerUtils::isModuleInstalled("group_customize"))
		{
			$sCustomBlock = '<div id="group_customize_page" style="display: none;">' .
				BxDolService::call('group_customize', 'get_customize_block', array()) . '</div>';
			$sMainCss = '<style type="text/css">' . 
				BxDolService::call('group_customize', 'get_group_style', array($aDataEntry[$this->_oDb->_sFieldId])) . '</style>';

			echo "
			<div id=\"custom_block\">
				$sCustomBlock
			</div>
			<div id=\"divUnderCustomization\">
				$sMainCss
				$sPageCode
				<div class=\"clear_both\"></div>
			</div>
			";

		}else{
			echo $sPageCode; 
		}
 
        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aDataEntry[$this->_oDb->_sFieldDescription]), 0, 255));
        $this->_oTemplate->addPageKeywords ($aDataEntry[$this->_oDb->_sFieldTags]);

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
		$this->sUri = $sUri;

        parent::_actionUploadMedia ($sUri, 'isAllowedUploadPhotos', 'images', array ('images_choice', 'images_upload'), _t('_bx_groups_page_title_upload_photos'));
    }

    function actionUploadVideos ($sUri) {
		$this->sUri = $sUri;

        parent::_actionUploadMedia ($sUri, 'isAllowedUploadVideos', 'videos', array ('videos_choice', 'videos_upload'), _t('_bx_groups_page_title_upload_videos'));
    }

    function actionUploadSounds ($sUri) {
		$this->sUri = $sUri;

        parent::_actionUploadMedia ($sUri, 'isAllowedUploadSounds', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_bx_groups_page_title_upload_sounds')); 
    }

    function actionUploadFiles ($sUri) {
		$this->sUri = $sUri;

        parent::_actionUploadMedia ($sUri, 'isAllowedUploadFiles', 'files', array ('files_choice', 'files_upload'), _t('_bx_groups_page_title_upload_files')); 
    }
  
    function _getInviteParams ($aDataEntry, $aInviter) {
        return array (
                'GroupName' => $aDataEntry['title'],
                'GroupLocation' => _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']) . (trim($aDataEntry['city']) ? ', '.$aDataEntry['city'] : '') . ', ' . $aDataEntry['zip'],
                'GroupUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'AcceptUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'accept/' . $aDataEntry['id'], 
				'InviterUrl' => $aInviter ? getProfileLink($aInviter['ID']) : 'javascript:void(0);',
                'InviterNickName' => $aInviter ? $aInviter['NickName'] : _t('_bx_groups_user_unknown'),
                'InvitationText' => stripslashes(strip_tags($_POST['inviter_text'])),
            );        
    }    

    function actionCalendar ($iYear = '', $iMonth = '')
    {
        parent::_actionCalendar ($iYear, $iMonth, _t('_bx_groups_page_title_calendar'));
    }


    function actionSearchOLD ($sKeyword = '', $sCategory = '') {
        parent::_actionSearch ($sKeyword, $sCategory, _t('_bx_groups_page_title_search'));
    }

    function actionAdd () {
        parent::_actionAdd (_t('_bx_groups_page_title_add'));
    }
  
    function _addForm ($sRedirectUrl) {

        bx_import ('FormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {

            $sStatus = $this->_oDb->getParam($this->_sPrefix.'_autoapproval') == 'on' || $this->isAdmin() ? 'approved' : 'pending';
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        

            $iEntryId = $oForm->insert ($aValsAdd);

            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 

				$this->processLogo($iEntryId); //logo mod
 
				//rss mod
				$this->_oDb->addRss($iEntryId);

				$this->_oDb->addYoutube($iEntryId);
 
                $oForm->processMedia($iEntryId, $this->_iProfileId);

                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());
                $this->onEventCreate($iEntryId, $sStatus, $aDataEntry);

				$this->_oDb->addGroupAdmin($iEntryId, array($this->_iProfileId));

                if (!$sRedirectUrl)
                    $sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else {
            
            echo $oForm->getCode ();

        }
    }
 
    function actionEdit ($iEntryId) {
        $this->_actionEdit ($iEntryId, _t('_bx_groups_page_title_edit'));
    }

    function _actionEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
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
  
			$sStatus = $this->_oDb->getParam($this->_sPrefix . '_autoapproval') == 'on' || $this->isAdmin() ? 'approved' : 'pending';
			$aValsAdd = array ($this->_oDb->_sFieldStatus => $sStatus);
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
 
				if( is_array($_POST['prev_feed']) && count($_POST['prev_feed'])){
					$aFeeds2Keep = array();

					foreach ($_POST['prev_feed'] as $iFeedId){
						$aFeeds2Keep[$iFeedId] = $iFeedId;
					}

					$aFeedIds = $this->_oDb->getRssIds($iEntryId);
				
					$aDeletedFeed = array_diff ($aFeedIds, $aFeeds2Keep);

					if ($aDeletedFeed) {
						foreach ($aDeletedFeed as $iFeedId) {
							$this->_oDb->removeRss($iEntryId, $iFeedId);
						}
					} 
				}

				$this->_oDb->addRss($iEntryId);


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
						$this->_oDb->removeYoutube($iEntryId, $iYoutubeId);
					}
				} 
 
				$this->_oDb->addYoutube($iEntryId);
 				//[end] youtube
 
 				$this->processLogo($iEntryId); //logo mod

                $oForm->processMedia($iEntryId, $this->_iProfileId);

                $this->isAllowedEdit($aDataEntry, true); // perform action

                $this->onEventChanged ($iEntryId, $sStatus);
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
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
        $this->_oTemplate->pageCode($sTitle);
    }
  
    function actionDelete ($iEntryId) {
        parent::_actionDelete ($iEntryId, _t('_bx_groups_msg_group_was_deleted'));
    }

    function actionMarkFeatured ($iEntryId) {

        header('Content-type:text/html;charset=utf-8');

        $sMsgSuccessAdd = _t('_bx_groups_msg_added_to_featured'); 
		$sMsgSuccessRemove = _t('_bx_groups_msg_removed_from_featured');
 
        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedMarkAsFeatured($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->markAsFeatured($iEntryId)) {
            $this->isAllowedMarkAsFeatured($aDataEntry, true); // perform action
            $this->onEventMarkAsFeatured ($iEntryId, $aDataEntry);
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
            $sJQueryJS = genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox($aDataEntry[$this->_oDb->_sFieldFeatured] ? $sMsgSuccessRemove : $sMsgSuccessAdd) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
        exit;
    }
 

    function actionJoin ($iEntryId, $iProfileId)
    {
        parent::_actionJoin ($iEntryId, $iProfileId, _t('_bx_groups_msg_joined_already'), _t('_bx_groups_msg_joined_request_pending'), _t('_bx_groups_msg_join_success'), _t('_bx_groups_msg_join_success_pending'), _t('_bx_groups_msg_leave_success'));
    } 

    function actionSharePopup ($iEntryId) {
        parent::_actionSharePopup ($iEntryId, _t('_bx_groups_caption_share_group'));
    }

    function actionManageFansPopup ($iEntryId) {
        parent::_actionManageFansPopup ($iEntryId, _t('_bx_groups_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', BX_GROUPS_MAX_FANS);
    }

    function actionTags() {
        parent::_actionTags (_t('_bx_groups_page_title_tags'));
    }    

    function actionCategories() {
        parent::_actionCategories (_t('_bx_groups_page_title_categories'));
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

    // ================================== external actions

    /**
     * Homepage block with different groups
     * @return html to display on homepage in a block
     */     
    function serviceHomepageBlock () {

        if (!$this->_oDb->isAnyPublicContent())
            return '';
        
        bx_import ('PageMain', $this->_aModule);
        $o = new BxGroupsPageMain ($this);
        $o->sUrlStart = BX_DOL_URL_ROOT . 'index.php?';

        $sDefaultHomepageTab = $this->_oDb->getParam('bx_groups_homepage_default_tab');
        $sBrowseMode = $sDefaultHomepageTab;
        switch ($_GET['bx_groups_filter']) {            
            case 'featured':
            case 'recent':
            case 'top':
            case 'popular':
            case $sDefaultHomepageTab:            
                $sBrowseMode = $_GET['bx_groups_filter'];
                break;
        }

        return $o->ajaxBrowse(
            $sBrowseMode,
            $this->_oDb->getParam('bx_groups_perpage_homepage'), 
            array(
                _t('_bx_groups_tab_featured') => array('href' => BX_DOL_URL_ROOT . 'index.php?bx_groups_filter=featured', 'active' => 'featured' == $sBrowseMode, 'dynamic' => true),
                _t('_bx_groups_tab_recent') => array('href' => BX_DOL_URL_ROOT . 'index.php?bx_groups_filter=recent', 'active' => 'recent' == $sBrowseMode, 'dynamic' => true),
                _t('_bx_groups_tab_top') => array('href' => BX_DOL_URL_ROOT . 'index.php?bx_groups_filter=top', 'active' => 'top' == $sBrowseMode, 'dynamic' => true),
                _t('_bx_groups_tab_popular') => array('href' => BX_DOL_URL_ROOT . 'index.php?bx_groups_filter=popular', 'active' => 'popular' == $sBrowseMode, 'dynamic' => true),
            )
        );
    }

    /**
     * Profile block with user's groups
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlock ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxGroupsPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']) . '?';

        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('bx_groups_perpage_profile'), 
            array(),
            process_db_input ($aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            true,
            false 
        );
    }

    /**
     * Account page block with groups user joied 
     * @return html to display in a block
     */     
    function serviceAccountJoinedBlock () {
        $iProfileId = (int)$this->_iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxGroupsPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']) . '?';
 
        return $o->ajaxBrowse(
            'joined', 
            $this->_oDb->getParam('bx_groups_perpage_profile'), 
            array(),
            process_db_input ($aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            true,
            false 
        );
    }


    /**
     * Profile block with groups user joied
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlockJoined ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxGroupsPageMain ($this);        
		 
		if(strpos($_SERVER['REQUEST_URI'], 'member.php') !== false) {
			$o->sUrlStart = BX_DOL_URL_ROOT . 'member.php?';
		} else {
			$o->sUrlStart = getProfileLink($aProfile['ID']) . '?';
		}

        return $o->ajaxBrowse(
            'joined', 
            $this->_oDb->getParam('bx_groups_perpage_profile'), 
            array(),
            process_db_input ($aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            true,
            false 
        );
    }
  
 
    // ================================== admin actions
 
    function actionAdministration ($sUrl = '', $sParam1='') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();
 
        $aMenu = array(
            'pending_approval' => array(
                'title' => _t('_bx_groups_menu_admin_pending_approval'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_approval', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false)),
            ),
            'admin_entries' => array(
                'title' => _t('_bx_groups_menu_admin_entries'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ),            
            'create' => array(
                'title' => _t('_bx_groups_menu_admin_add_entry'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),/*2.1.9*/
			'membership' => array(
                'title' => _t('_bx_groups_menu_admin_membership'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/membership',
                '_func' => array ('name' => 'actionAdministrationMembership', 'params' => array($sParam1)),
            ),/*2.1.9*/  
            'settings' => array(
                'title' => _t('_bx_groups_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'pending_approval';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_bx_groups_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css')); 
        $this->_oTemplate->pageCodeAdmin (_t('_bx_groups_page_title_administration'));
    }

    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('Groups');
    }

    function actionAdministrationManage ($isAdminEntries = false) {
        return parent::_actionAdministrationManage ($isAdminEntries, '_bx_groups_admin_delete', '_bx_groups_admin_activate');
    }

    // ================================== events


    function onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry, 'bx_groups_join_request', BX_GROUPS_MAX_FANS);
    }

    function onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry, 'bx_groups_join_reject');
    }

    function onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry, 'bx_groups_fan_remove');
    }

    function onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry, 'bx_groups_fan_become_admin');
    }

    function onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry, 'bx_groups_admin_become_fan');
    }

    function onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry, 'bx_groups_join_confirm');
    }

    // ================================== permissions
 
    function isAllowedBrowse ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_GROUPS_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_GROUPS_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_GROUPS_ADD_GROUP, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedSubAdd ($aDataEntry, $isPerformAction = false) {

		if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
  
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_GROUPS_ADD_GROUP, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedSubEdit ($aDataEntry, $aSubEntry, $isPerformAction = false) {

        if ($this->isAdmin() || $this->isSubEntryAdmin($aSubEntry)) 
            return true;
 
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_GROUPS_EDIT_ANY_GROUP, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED; 
    } 

    function isAllowedSubDelete ($aSubEntry, $isPerformAction = false) {
        
        if ($this->isAdmin() || $this->isSubEntryAdmin($aSubEntry)) 
            return true;
 
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_GROUPS_DELETE_ANY_GROUP, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }   
 
    function isAllowedEdit ($aDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;

        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_GROUPS_EDIT_ANY_GROUP, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedMarkAsFeatured ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_GROUPS_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function isAllowedBroadcast ($aDataEntry, $isPerformAction = false) {
        //if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
        //    return true;

        if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_GROUPS_BROADCAST_MESSAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function isAllowedDelete (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_GROUPS_DELETE_ANY_GROUP, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }     
 
    function isAllowedSendInvitation (&$aDataEntry) {
        return ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) ? true : false;
    }

    function isAllowedShare (&$aDataEntry)
    {
    	return ($aDataEntry[$this->_oDb->_sFieldAllowViewTo] == BX_DOL_PG_ALL);
    }

    function isAllowedPostInForum(&$aDataEntry, $iProfileId = -1) {
        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) || $this->_oPrivacy->check('post_in_forum', $aDataEntry[$this->_oDb->_sFieldId], $iProfileId);
    }
 
    function isAllowedReadForum(&$aDataEntry, $iProfileId = -1)
    {
		$oModuleDb = new BxDolModuleDb();
		if (!$aForum = $oModuleDb->getModuleByUri('forum')) return false;
 
        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) || $this->_oPrivacy->check('view_forum', $aDataEntry['id'], $iProfileId);
    }
  
    function isAllowedRate(&$aDataEntry) {        
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        return $this->_oPrivacy->check('rate', $aDataEntry[$this->_oDb->_sFieldId], $this->_iProfileId);        
    }

    function isAllowedViewComments(&$aDataEntry) {
      
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        return $this->_oPrivacy->check('view_comment', $aDataEntry[$this->_oDb->_sFieldId], $this->_iProfileId);
    }

    function isAllowedComments(&$aDataEntry) {
      
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        return $this->_oPrivacy->check('comment', $aDataEntry[$this->_oDb->_sFieldId], $this->_iProfileId);
    }

    function isAllowedViewFans(&$aDataEntry) {
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        return $this->_oPrivacy->check('view_fans', $aDataEntry[$this->_oDb->_sFieldId], $this->_iProfileId);
    }

    function isAllowedUploadPhotos(&$aDataEntry)
    {
        if (!BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            return false;
        if (!$this->_iProfileId)
            return false;
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        if ($this->isEntryAdmin($aDataEntry))
            return true; 

        return $this->_oPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadVideos(&$aDataEntry)
    {
        if (!BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            return false;
        if (!$this->_iProfileId)
            return false;
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForVideos())
            return false;
        if ($this->isEntryAdmin($aDataEntry))
            return true; 

        return $this->_oPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadSounds(&$aDataEntry)
    {
        if (!BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            return false;
        if (!$this->_iProfileId)
            return false;
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForSounds())
            return false;
        if ($this->isEntryAdmin($aDataEntry))
            return true; 


        return $this->_oPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFiles(&$aDataEntry)
    {
        if (!BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            return false;
        if (!$this->_iProfileId)
            return false;
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForFiles())
            return false;
        if ($this->isEntryAdmin($aDataEntry))
            return true; 
 
        return $this->_oPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedCreatorCommentsDeleteAndEdit (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (getParam('bx_groups_author_comments_admin') && $this->isEntryAdmin($aDataEntry))
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_GROUPS_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedManageAdmins($aDataEntry) {

        if ($this->isAdmin()) 
            return true;

        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))
            return true;
        return false;
    }

    function isAllowedManageFans($aDataEntry) {
        return ($this->isAdmin() || $this->isEntryAdmin($aDataEntry));
    }

    function isFan($aDataEntry, $iProfileId = 0, $isConfirmed = true) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isFan ($aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }

    function isEntryAdmin($aDataEntry, $iProfileId = 0, $sIdField='id') {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;
        return $this->_oDb->isGroupAdmin ($aDataEntry[$sIdField], $iProfileId) && isProfileActive($iProfileId);
    }
 
    function _defineActions () {
        defineMembershipActions(array('groups purchase featured','groups view group', 'groups browse', 'groups search', 'groups add group', 'groups comments delete and edit', 'groups edit any group', 'groups delete any group', 'groups mark as featured', 'groups approve groups', 'groups broadcast message','groups sounds add', 'groups photos add', 'groups videos add', 'groups files add', 'groups broadcast message'));
    }

    function _browseMy (&$aProfile) {        
        parent::_browseMy ($aProfile, _t('_bx_groups_page_title_my_groups'));
    }    
  
    function isAllowedView ($aData, $isPerformAction = false, $sTable='main') {
 		
		if($sTable!='main') 
			return $this->isAllowedViewSubProfile ($sTable, $aData, $isPerformAction);
  
		if ($this->isAdmin() || $this->isEntryAdmin($aData)) 
			return true; 
	  
        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_GROUPS_VIEW_GROUP, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
  
        // check user group   
		$isAllowed =  $this->_oPrivacy->check('view_group', $aData['id'], $this->_iProfileId);    
		return $isAllowed && $this->_isAllowedViewByMembership ($aData);  
    }

    function _isAllowedViewByMembership (&$aDataEntry) { 
        if (!$aDataEntry['group_membership_view_filter']) return true;
        require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
        $aMembershipInfo = getMemberMembershipInfo($this->_iProfileId);
 
		if($aMembershipInfo['DateExpires']) 
			return $aDataEntry['group_membership_view_filter'] == $aMembershipInfo['ID'] && $aMembershipInfo['DateStarts'] < time() && $aMembershipInfo['DateExpires'] > time() ? true : false;
		else
			return $aDataEntry['group_membership_view_filter'] == $aMembershipInfo['ID'] && $aMembershipInfo['DateStarts'] < time() ? true : false; 
    }
    
    function _isAllowedJoinByMembership (&$aEvent) {        
        if (!$aEvent['group_membership_filter']) return true;
        require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
        $aMembershipInfo = getMemberMembershipInfo($this->_iProfileId);
         
		//return $aEvent['group_membership_filter'] == $aMembershipInfo['ID'] && $aMembershipInfo['DateStarts'] < time() && $aMembershipInfo['DateExpires'] > time() ? true : false;
  
		return $aEvent['group_membership_filter'] == $aMembershipInfo['ID'] && (!$aMembershipInfo['DateStarts'] || $aMembershipInfo['DateStarts'] < time()) && (!$aMembershipInfo['DateExpires'] || $aMembershipInfo['DateExpires'] > time()) ? true : false;  
    }
 
    function actionBroadcast ($iEntryId) {
        $this->_actionBroadcast ($iEntryId, _t('_bx_groups_page_title_broadcast'), _t('_bx_groups_msg_broadcast_no_recipients'), _t('_bx_groups_msg_broadcast_message_sent'));
    }

    function actionInvite ($iEntryId) {
        $this->_actionInvite ($iEntryId, 'bx_groups_invitation', $this->_oDb->getParam('bx_groups_max_email_invitations'), _t('_bx_groups_msg_invitation_sent'), _t('_bx_groups_msg_no_users_to_invite'), _t('_bx_groups_page_title_invite'));
    }
 
    function actionSearch ($sKeyword = '', $sCategory = '', $sCountry = '', $sState = '', $sCity = '', $sGroupStart = '', $sGroupEnd = '') {

        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();


		$sKeyword = ($sKeyword!='-') ? $sKeyword : '';
		$sCategory = ($sCategory!='-') ? $sCategory : '';
		$sCountry = ($sCountry!='-') ? $sCountry : '';
		$sState = ($sState!='-') ? $sState : '';
		$sCity = ($sCity!='-') ? $sCity : '';

        if ($sKeyword) 
            $_GET['Keyword'] = $sKeyword;
        if ($sCategory)
            $_GET['Category'] = explode(',', $sCategory);
        if ($sCountry)
            $_GET['Country'] = $sCountry;
		if ($sState) 
            $_GET['State'] = $sState;
		if ($sCity) 
            $_GET['City'] = $sCity;
 

        if (is_array($_GET['Category']) && 1 == count($_GET['Category']) && !$_GET['Category'][0]) {
            unset($_GET['Category']);
            unset($sCategory);
        }
 
        if ($sCountry || $sCategory || $sKeyword || $sState || $sCity ) {
            $_GET['submit_form'] = 1;  
        }
        
        bx_groups_import ('FormSearch');
        $oForm = new BxGroupsFormSearch ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
            bx_groups_import ('SearchResult');
            $o = new BxGroupsSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('Country'), $oForm->getCleanValue('State'), $oForm->getCleanValue('City'));

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

            $this->_oTemplate->addCss ('unit.css');
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode($o->aCurrent['title'], false, false);
            return;

        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode(_t('_bx_groups_page_title_search'));
    } 
    
    /**
     * Account block with different groups
     * @return html to display on homepage in a block
     */ 
    function serviceAccountPageBlock () {
 
        if (!$this->_oDb->isAnyPublicContent())
            return '';

		$aProfileInfo = getProfileInfo($this->_iProfileId);
		$sCity = $aProfileInfo['City'];

		if(!$sCity)
			return;

        bx_import ('PageMain', $this->_aModule);
        $o = new BxGroupsPageMain ($this);        
        $o->sUrlStart = BX_DOL_URL_ROOT . '?';
 
        return $o->ajaxBrowse(
            'local',
            $this->_oDb->getParam('bx_groups_perpage_accountpage'),
			array(),
			$sCity
        );
    }
  
    function actionAccept ($iEntryId, $iProfileId=0, $sCode='') {
 
		$sMsgJoinSuccess = _t('_bx_groups_msg_join_success');
		$sMsgJoinSuccessPending = _t('_bx_groups_msg_join_success_pending');
		$sMsgJoinRequestPending = _t('_bx_groups_msg_joined_request_pending');

        $this->_oTemplate->pageStart();

        $iEntryId = (int)$iEntryId;
        $iProfileId = (int)$iProfileId;

        if (!$this->_iProfileId) {  
		
			global $_page;
			global $_page_cont;
  
  			$sRedirect = BX_DOL_URL_ROOT . 'member.php';  
			$sRelocate = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'accept/' . $iEntryId .'/'. $iProfileId .'/'. $sCode;  

			$_page['name_index'] = 150;
			$_page['css_name'] = '';

			$_ni = $_page['name_index'];
			$_page_cont[$_ni]['page_main_code'] = MsgBox( _t( '_Please Wait' ) );
			$_page_cont[$_ni]['url_relocate'] = htmlspecialchars( $sUrlRelocate );
  
		    Redirect($sRedirect, array('ID' =>'', 'Password' => '', 'relocate' => $sRelocate));   
			PageCode();
			
			return;
        }
 
        if ($iProfileId && ($iProfileId != $this->_iProfileId)) {
            echo MsgBox(_t('_Access denied')); 
			$this->_oTemplate->addCss ('main.css');
			$this->_oTemplate->pageCode(_t('_bx_groups_accept_invitation') . $aDataEntry[$this->_oDb->_sFieldTitle]);
			return; 
        }
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

		$isFan = $this->_oDb->isFan ($iEntryId, $this->_iProfileId, true); 
		if ($isFan) { 
			//fan already 
			$sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];  
			header('Location: ' . $sRedirect); 
			return;
		} 

		$isFan = $this->_oDb->isFan ($iEntryId, $this->_iProfileId, false); 
		if ($isFan) { 
			//fan pending 
			echo MsgBox($sMsgJoinRequestPending); 
			$this->_oTemplate->addCss ('main.css');
			$this->_oTemplate->pageCode(_t('_bx_groups_accept_invitation') . $aDataEntry[$this->_oDb->_sFieldTitle]);
			return;
		} 

        if (!$this->_oDb->isValidInvite($iEntryId, $sCode)) { 
            echo MsgBox(_t('_Access denied')); 
			$this->_oTemplate->addCss ('main.css');
			$this->_oTemplate->pageCode(_t('_bx_groups_accept_invitation') . $aDataEntry[$this->_oDb->_sFieldTitle]);
			return; 
        }
  
		$isConfirmed = ($this->isEntryAdmin($aDataEntry) || !$aDataEntry[$this->_oDb->_sFieldJoinConfirmation] ? true : false);

		if ($this->_oDb->joinEntry($iEntryId, $this->_iProfileId, $isConfirmed)) {
			if ($isConfirmed) {
				
				//$this->_oDb->flagActivity('join', $iEntryId, $this->_iProfileId);
	 
				$this->_oDb->removeInvite($iEntryId, $iProfileId);

				$this->onEventJoin ($iEntryId, $this->_iProfileId, $aDataEntry);
				$sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

				header('Location: ' . $sRedirect);
			} else {
				$this->onEventJoinRequest ($iEntryId, $this->_iProfileId, $aDataEntry);
		 
				$this->_oDb->removeInvite($iEntryId, $iProfileId);

				echo MsgBox($sMsgJoinSuccessPending); 
				$this->_oTemplate->addCss ('main.css');
				$this->_oTemplate->pageCode(_t('_bx_groups_accept_invitation') . $aDataEntry[$this->_oDb->_sFieldTitle]);
				return;
			}
		}
	 
        echo MsgBox(_t('_Error Occured'));
 
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode(_t('_bx_groups_accept_invitation') . $aDataEntry[$this->_oDb->_sFieldTitle]); 
    }    
   
    function actionLocal ($sCountry='', $sState='', $sCategory='') { 
        $this->_oTemplate->pageStart();
        bx_import ('PageLocal', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageLocal';
        $oPage = new $sClass ($this, $sCountry, $sState, $sCategory);
        echo $oPage->getCode();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('unit.css');

		$sTitle = _t('_bx_groups_page_title_local');

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
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('unit.css');
        $this->_oTemplate->pageCode(_t('_bx_groups_page_title_local'), false, false);
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
            $aTemplate = $oEmailTemplate->getTemplate($this->_sPrefix . '_broadcast'); 

			//$aTemplate['Body'] = str_replace(array('<pre>','</pre>'), array('',''), $aTemplate['Body']); 
 
            $aTemplateVars = array (
                'BroadcastTitle' => $this->_oDb->unescape($oForm->getCleanValue ('title')),
                'BroadcastMessage' => nl2br($this->_oDb->unescape($oForm->getCleanValue ('message'))),
                'EntryTitle' => $aDataEntry[$this->_oDb->_sFieldTitle],
                'EntryUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],                
            );
            $iSentMailsCounter = 0;            
            foreach ($aRecipients as $aProfile) {		        
       	        $iSentMailsCounter += sendMail($aProfile['Email'], $aTemplate['Subject'], $aTemplate['Body'], $aProfile['ID'], $aTemplateVars);
            
				$this->broadCastToInbox($aProfile, $aTemplate, $aTemplateVars);  
			}
            if (!$iSentMailsCounter) {
                $this->_oTemplate->displayErrorOccured();
                return;
            }

            echo MsgBox ($sMsgSent);

            $this->isAllowedBroadcast($aDataEntry, true); // perform send broadcast message action             
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode($sMsgSent, true, true);
            return;
        } 

        echo $oForm->getCode ();

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode($sTitle);
    }
 
    function _actionInvite ($iEntryId, $sEmailTemplate, $iMaxEmailInvitations, $sMsgInvitationSent, $sMsgNoUsers, $sTitle) {

		global $tmpl;
		require_once( BX_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $tmpl . '/scripts/BxTemplMailBox.php');
 

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if (!$this->isAllowedSendInvitation($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$sTitle . $aDataEntry[$this->_oDb->_sFieldTitle] => '',
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
			
			$sAcceptUrl = $aPlusOriginal['AcceptUrl'];

            // send invitation to registered members
            if (false !== bx_get('inviter_users') && is_array(bx_get('inviter_users'))) {
				$aInviteUsers = bx_get('inviter_users');
                foreach ($aInviteUsers as $iRecipient) {
                    $aRecipient = getProfileInfo($iRecipient);

 					$sInviteCode = $this->_oDb->addInvite($iEntryId, $iRecipient);
					$aPlusOriginal['AcceptUrl'] = $sAcceptUrl .'/'. $iRecipient .'/'. $sInviteCode; 

					$aPlus = array_merge (array ('NickName' => ' ' . $aRecipient['NickName']), $aPlusOriginal);
                    $iSuccess += sendMail(trim($aRecipient['Email']), $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus) ? 1 : 0;
                 
					$this->inviteToInbox($aRecipient, $aTemplate, $aPlusOriginal);  
				}
            }

            // send invitation to additional emails
            $iMaxCount = $iMaxEmailInvitations;
            $aEmails = preg_split ("#[,\s\\b]+#", bx_get('inviter_emails'));
            $aPlus = array_merge (array ('NickName' => ''), $aPlusOriginal);
            if ($aEmails && is_array($aEmails)) {
                foreach ($aEmails as $sEmail) {
                    if (strlen($sEmail) < 5) 
                        continue;

 					$sInviteCode = $this->_oDb->addInvite($iEntryId, 0);
					$aPlus['AcceptUrl'] = $sAcceptUrl .'/0/'. $sInviteCode; 
 
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
 
    function  inviteToInbox($aProfile, $aTemplate, $aPlusOriginal){
 
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
		$sMessageBody = str_replace("<GroupName>", $aPlusOriginal['GroupName'] , $sMessageBody);
		$sMessageBody = str_replace("<GroupLocation>", $aPlusOriginal['GroupLocation'] , $sMessageBody);
		$sMessageBody = str_replace("<GroupUrl>", $aPlusOriginal['GroupUrl'] , $sMessageBody);
		$sMessageBody = str_replace("<AcceptUrl>", $aPlusOriginal['AcceptUrl'] .'/'. $aProfile['ID'] , $sMessageBody); 
		$sMessageBody = str_replace("<InviterUrl>", $aPlusOriginal['InviterUrl'] , $sMessageBody);
		$sMessageBody = str_replace("<InviterNickName>", $aPlusOriginal['InviterNickName'] , $sMessageBody);
		$sMessageBody = str_replace("<InvitationText>", $aPlusOriginal['InvitationText'] , $sMessageBody);
		
		$oMailBox -> iWaitMinutes = 0;
		$oMailBox -> sendMessage($aTemplate['Subject'], $sMessageBody, $aProfile['ID'], $aComposeSettings); 
	
   }

   function  broadCastToInbox($aProfile, $aTemplate, $aTemplateVars){
 
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
		$sMessageBody = str_replace("<NickName>", getNickName($this->_iProfileId), $sMessageBody);
		$sMessageBody = str_replace("<EntryUrl>", $aTemplateVars['EntryUrl'], $sMessageBody);
		$sMessageBody = str_replace("<EntryTitle>", $aTemplateVars['EntryTitle'], $sMessageBody);
		$sMessageBody = str_replace("<BroadcastMessage>", $aTemplateVars['BroadcastMessage'], $sMessageBody);
			
		$oMailBox -> iWaitMinutes = 0; 
		$oMailBox -> sendMessage($aTemplateVars['BroadcastTitle'], $sMessageBody, $aProfile['ID'], $aComposeSettings); 
	
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
            $sRet .= '<a '.$sClass.' href="' . $sLinkStart . urlencode(title2uri($sName)) . '">'.$sName.'</a>&#160';
        
        return $sRet;
    } 
 
    function isMembershipEnabledForImages () {
        return ($this->_isMembershipEnabledFor ('BX_PHOTOS_ADD') && $this->_isMembershipEnabledFor ('BX_GROUPS_PHOTOS_ADD'));
    }

    function isMembershipEnabledForVideos () {
        return ($this->_isMembershipEnabledFor ('BX_VIDEOS_ADD') && $this->_isMembershipEnabledFor ('BX_GROUPS_VIDEOS_ADD')); 
    }

    function isMembershipEnabledForSounds () {
        return ($this->_isMembershipEnabledFor ('BX_SOUNDS_ADD') && $this->_isMembershipEnabledFor ('BX_GROUPS_SOUNDS_ADD'));
    }

    function isMembershipEnabledForFiles () {
        return ($this->_isMembershipEnabledFor ('BX_FILES_ADD') && $this->_isMembershipEnabledFor ('BX_GROUPS_FILES_ADD'));
    }
 
    function _isMembershipEnabledFor ($sMembershipActionConstant) { 
        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'groups photos add', 'groups sounds add', 'groups videos add', 'groups files add'));
		if (!defined($sMembershipActionConstant))
			return false;
		$aCheck = checkAction($_COOKIE['memberID'], constant($sMembershipActionConstant));
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }  
  
	/******[BEGIN] Sponsor functions **************************/ 
    function actionSponsor ($sAction, $sParam1, $sParam2='') {
		switch($sAction){
			case 'download': 
				$this->sponsorDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionSponsorAdd ($sParam1, '_bx_groups_page_title_sponsor_add');
			break;
			case 'edit':
				$this->actionSponsorEdit ($sParam1, '_bx_groups_page_title_sponsor_edit');
			break;
			case 'delete':
				$this->actionSponsorDelete ($sParam1, _t('_bx_groups_msg_group_was_sponsor_deleted'));
			break;
			case 'view':
				$this->actionSponsorView ($sParam1, _t('_bx_groups_msg_pending_sponsor_approval')); 
			break; 
			case 'browse':
				return $this->actionSponsorBrowse ($sParam1, '_bx_groups_page_title_sponsor_browse'); 
			break;  
		}
	}
    
    function actionSponsorBrowse ($sUri, $sTitle) {
       
        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$this->sUri = $sUri;

        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_bx_groups_menu_view_sponsors') => '',
        ));

        bx_import ('SponsorPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('unit.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]), false, false);  
    }
 
    function actionSponsorView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }
 
		$aSponsorEntry = $this->_oDb->getSponsorEntryByUri($sUri);
		$iEntryId = (int)$aSponsorEntry['group_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aSponsorEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_bx_groups_menu_view_sponsors') => '',
        ));

        if ( (!$this->_iProfileId || $aSponsorEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && (!$this->isAllowedView($aDataEntry, true) || !$this->isAllowedViewSubProfile($this->_oDb->_sTableSponsor, $aSponsorEntry, true)) ) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        bx_import ('SponsorPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorPageView';
        $oPage = new $sClass ($this, $aSponsorEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        $sPageCode = $oPage->getCode();

		// add group customizer
		if (BxDolInstallerUtils::isModuleInstalled("group_customize"))
		{ 
			$sMainCss = '<style type="text/css">' . 
				BxDolService::call('group_customize', 'get_group_style', array($aDataEntry['id'])) . '</style>';

			echo "
			<div id=\"divUnderCustomization\">
				$sMainCss
				$sPageCode
				<div class=\"clear_both\"></div>
			</div>
			";
		}else{
			echo $sPageCode;
		}

        bx_import('SponsorCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aSponsorEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aSponsorEntry['title'], false, false); 
    }


    function actionSponsorEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aSponsorEntry = $this->_oDb->getSponsorEntryById($iEntryId);
		$iSponsorId = (int)$aSponsorEntry['group_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iSponsorId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aDataEntry, $aSponsorEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        bx_import ('SponsorFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorFormEdit';
        $oForm = new $sClass ($this, $aSponsorEntry['uri'], $iSponsorId,  $iEntryId, $aSponsorEntry);
  
        $oForm->initChecker($aSponsorEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'sponsor_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
  			$this->_oDb->_sFieldAuthorId = 'author_id';

			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableSponsorMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
    
  				$this->onEventSubItemChanged ('sponsor', $iEntryId, $sStatus, $aDataEntry);

                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'sponsor/view/' . $aSponsorEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle, $aSponsorEntry['title']));  
    }

    function actionSponsorDelete ($iSponsorId, $sMsgSuccess) {

		$aSponsorEntry = $this->_oDb->getSponsorEntryById($iSponsorId);
		$iEntryId = (int)$aSponsorEntry['group_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, 0, true))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iSponsorId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete($aSponsorEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iSponsorId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteSponsorByIdAndOwner($iSponsorId, $iEntryId, 0, true)) { 
            $this->onEventSponsorDeleted ($iSponsorId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
  
            $sJQueryJS = genAjaxyPopupJS($iSponsorId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iSponsorId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionSponsorAdd ($iSponsorId, $sTitle) {
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iSponsorId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];
 
        if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $this->_addSponsorForm($iSponsorId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]));  
    }
 
    function _addSponsorForm ($iSponsorId) { 
 
        bx_import ('SponsorFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iSponsorId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'sponsor_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
  			$this->_oDb->_sFieldAuthorId = 'author_id';

			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableSponsorMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId  
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {
   
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getSponsorEntryById($iEntryId);
    
				$this->onEventSubItemCreate ('sponsor', $iEntryId, $sStatus, $aDataEntry);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'sponsor/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventSponsorDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseSponsorTags ($iEntryId);
        //$this->reparseSponsorCategories ($iEntryId);

        // delete votings
        bx_import('SponsorVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorVoting';
        $oVoting = new $sClass ($this->_oDb->_sSponsorPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('SponsorCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorCmts';
        $oCmts = new $sClass ($this->_oDb->_sSponsorPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

	    // delete associated locations
        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri().'_sponsor', $iEntryId));

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sSponsorPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        


    /*******[END - Sponsor Functions] ******************************/

	/******[BEGIN] Blog functions **************************/ 
    function actionBlog ($sAction, $sParam1, $sParam2='') {
		switch($sAction){
			case 'download': 
				$this->blogDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionBlogAdd ($sParam1, '_bx_groups_page_title_blog_add');
			break;
			case 'edit':
				$this->actionBlogEdit ($sParam1, '_bx_groups_page_title_blog_edit');
			break;
			case 'delete':
				$this->actionBlogDelete ($sParam1, _t('_bx_groups_msg_group_was_blog_deleted'));
			break;
			case 'view':
				$this->actionBlogView ($sParam1, _t('_bx_groups_msg_pending_blog_approval')); 
			break; 
			case 'browse':
				return $this->actionBlogBrowse ($sParam1, '_bx_groups_page_title_blog_browse'); 
			break;  
		}
	}
    
    function actionBlogBrowse ($sUri, $sTitle) {
       
        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$this->sUri = $sUri;

        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
  
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_bx_groups_menu_view_blogs') => '',
        ));

        bx_import ('BlogPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BlogPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('unit.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]), false, false);  
    }
 
    function actionBlogView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }
  
		$aBlogEntry = $this->_oDb->getBlogEntryByUri($sUri);
		$iEntryId = (int)$aBlogEntry['group_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];
 
        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aBlogEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_bx_groups_menu_view_blogs') => '',
        ));

        if ( (!$this->_iProfileId || $aBlogEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && (!$this->isAllowedView($aDataEntry, true) || !$this->isAllowedViewSubProfile($this->_oDb->_sTableBlog, $aBlogEntry, true)) ) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        bx_import ('BlogPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BlogPageView';
        $oPage = new $sClass ($this, $aBlogEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        $sPageCode = $oPage->getCode();

		// add group customizer
		if (BxDolInstallerUtils::isModuleInstalled("group_customize"))
		{ 
			$sMainCss = '<style type="text/css">' . 
				BxDolService::call('group_customize', 'get_group_style', array($aDataEntry['id'])) . '</style>';

			echo "
			<div id=\"divUnderCustomization\">
				$sMainCss
				$sPageCode
				<div class=\"clear_both\"></div>
			</div>
			";
		}else{
			echo $sPageCode;
		}

        bx_import('BlogCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BlogCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aBlogEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aBlogEntry['title'], false, false); 
    }


    function actionBlogEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aBlogEntry = $this->_oDb->getBlogEntryById($iEntryId);
		$iBlogId = (int)$aBlogEntry['group_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iBlogId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aDataEntry, $aBlogEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        bx_import ('BlogFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BlogFormEdit';
        $oForm = new $sClass ($this, $aBlogEntry['uri'], $iBlogId,  $iEntryId, $aBlogEntry);
  
        $oForm->initChecker($aBlogEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'blog_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
  			$this->_oDb->_sFieldAuthorId = 'author_id';

			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableBlogMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
   
  				$this->onEventSubItemChanged ('blog', $iEntryId, $sStatus, $aDataEntry);

                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'blog/view/' . $aBlogEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle, $aBlogEntry['title']));  
    }

    function actionBlogDelete ($iBlogId, $sMsgSuccess) {

		$aBlogEntry = $this->_oDb->getBlogEntryById($iBlogId);
		$iEntryId = (int)$aBlogEntry['group_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, 0, true))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iBlogId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete($aBlogEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iBlogId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteBlogByIdAndOwner($iBlogId, $iEntryId, 0, true)) {
             
            $this->onEventBlogDeleted ($iBlogId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
  
            $sJQueryJS = genAjaxyPopupJS($iBlogId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iBlogId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionBlogAdd ($iBlogId, $sTitle) {
  
		if (!($aDataEntry = $this->_oDb->getEntryById($iBlogId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

		$isConfirmedFan = $this->_oDb->isFan((int)$aDataEntry['id'], $this->_iProfileId, 1);

        if (!($isConfirmedFan || $this->isAdmin() || $this->isEntryAdmin($aDataEntry))) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
  		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $this->_addBlogForm($iBlogId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]));  
    }
 
    function _addBlogForm ($iBlogId) { 
 
        bx_import ('BlogFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BlogFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iBlogId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'blog_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
  			$this->_oDb->_sFieldAuthorId = 'author_id';

			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableBlogMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId  
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {
   
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getBlogEntryById($iEntryId);
    
				$this->onEventSubItemCreate ('blog', $iEntryId, $sStatus, $aDataEntry);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'blog/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventBlogDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseBlogTags ($iEntryId);
        //$this->reparseBlogCategories ($iEntryId);

        // delete votings
        bx_import('BlogVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BlogVoting';
        $oVoting = new $sClass ($this->_oDb->_sBlogPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('BlogCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BlogCmts';
        $oCmts = new $sClass ($this->_oDb->_sBlogPrefix, $iEntryId);
        $oCmts->onObjectDelete ();
 
        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sBlogPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        


    /*******[END - Blog Functions] ******************************/



	/******[BEGIN] Venue functions **************************/ 
    function actionVenue ($sAction, $sParam1, $sParam2='') {
		switch($sAction){
			case 'download': 
				$this->venueDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionVenueAdd ($sParam1, '_bx_groups_page_title_venue_add');
			break;
			case 'edit':
				$this->actionVenueEdit ($sParam1, '_bx_groups_page_title_venue_edit');
			break;
			case 'delete':
				$this->actionVenueDelete ($sParam1, _t('_bx_groups_msg_group_was_venue_deleted'));
			break;
			case 'view':
				$this->actionVenueView ($sParam1, _t('_bx_groups_msg_pending_venue_approval')); 
			break; 
			case 'browse':
				return $this->actionVenueBrowse ($sParam1, '_bx_groups_page_title_venue_browse'); 
			break;  
		}
	}
    
    function actionVenueBrowse ($sUri, $sTitle) {
       
        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }
 
 		$this->sUri = $sUri;

        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        bx_import ('VenuePageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenuePageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('unit.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]), false, false);  
    }
 
    function actionVenueView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }
 
		$aVenueEntry = $this->_oDb->getVenueEntryByUri($sUri);
		$iEntryId = (int)$aVenueEntry['group_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];
 
        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aVenueEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_bx_groups_menu_view_venues') => '',
        ));
 
        if ( (!$this->_iProfileId || $aVenueEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && (!$this->isAllowedView($aDataEntry, true) || !$this->isAllowedViewSubProfile($this->_oDb->_sTableVenue, $aVenueEntry, true)) ) { 
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        bx_import ('VenuePageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenuePageView';
        $oPage = new $sClass ($this, $aVenueEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        $sPageCode = $oPage->getCode();

		// add group customizer
		if (BxDolInstallerUtils::isModuleInstalled("group_customize"))
		{ 
			$sMainCss = '<style type="text/css">' . 
				BxDolService::call('group_customize', 'get_group_style', array($aDataEntry['id'])) . '</style>';

			echo "
			<div id=\"divUnderCustomization\">
				$sMainCss
				$sPageCode
				<div class=\"clear_both\"></div>
			</div>
			";
		}else{
			echo $sPageCode;
		}

        bx_import('VenueCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenueCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aVenueEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aVenueEntry['title'], false, false); 
    }


    function actionVenueEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aVenueEntry = $this->_oDb->getVenueEntryById($iEntryId);
		$iVenueId = (int)$aVenueEntry['group_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iVenueId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aDataEntry, $aVenueEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        bx_import ('VenueFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenueFormEdit';
        $oForm = new $sClass ($this, $aVenueEntry['uri'], $iVenueId,  $iEntryId, $aVenueEntry);
  
        $oForm->initChecker($aVenueEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'venue_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
  			$this->_oDb->_sFieldAuthorId = 'author_id';

			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableVenueMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
    
   				$this->onEventSubItemChanged ('venue', $iEntryId, $sStatus, $aDataEntry);

                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'venue/view/' . $aVenueEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle, $aVenueEntry['title']));  
    }

    function actionVenueDelete ($iVenueId, $sMsgSuccess) {

		$aVenueEntry = $this->_oDb->getVenueEntryById($iVenueId);
		$iEntryId = (int)$aVenueEntry['group_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, 0, true))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iVenueId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete($aVenueEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iVenueId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteVenueByIdAndOwner($iVenueId, $iEntryId,  0, true)) {
          
            $this->onEventVenueDeleted ($iVenueId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
  
            $sJQueryJS = genAjaxyPopupJS($iVenueId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iVenueId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionVenueAdd ($iVenueId, $sTitle) {
  
		if (!($aDataEntry = $this->_oDb->getEntryById($iVenueId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $this->_addVenueForm($iVenueId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]));  
    }
 
    function _addVenueForm ($iVenueId) { 
 
        bx_import ('VenueFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenueFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iVenueId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'venue_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
  			$this->_oDb->_sFieldAuthorId = 'author_id';

			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableVenueMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId  
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {
   
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getVenueEntryById($iEntryId);
    
				$this->onEventSubItemCreate ('venue', $iEntryId, $sStatus, $aDataEntry);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'venue/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventVenueDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseVenueTags ($iEntryId);
        //$this->reparseVenueCategories ($iEntryId);

        // delete votings
        bx_import('VenueVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenueVoting';
        $oVoting = new $sClass ($this->_oDb->_sVenuePrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('VenueCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenueCmts';
        $oCmts = new $sClass ($this->_oDb->_sVenuePrefix, $iEntryId);
        $oCmts->onObjectDelete ();

          // delete associated locations
        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri().'_venue', $iEntryId));

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sVenuePrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        


    /*******[END - VENUE Functions] ******************************/


	/******[BEGIN] News functions **************************/ 
    function actionNews ($sAction, $sParam1, $sParam2='') {
		switch($sAction){
			case 'download': 
				$this->newsDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionNewsAdd ($sParam1, '_bx_groups_page_title_news_add');
			break;
			case 'edit':
				$this->actionNewsEdit ($sParam1, '_bx_groups_page_title_news_edit');
			break;
			case 'delete':
				$this->actionNewsDelete ($sParam1, _t('_bx_groups_msg_group_was_news_deleted'));
			break;
			case 'view':
				$this->actionNewsView ($sParam1, _t('_bx_groups_msg_pending_news_approval')); 
			break; 
			case 'browse':
				return $this->actionNewsBrowse ($sParam1, '_bx_groups_page_title_news_browse'); 
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

		$this->sUri = $sUri;

        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_bx_groups_menu_view_news') => '',
        ));

        bx_import ('NewsPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('unit.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]), false, false);  
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
		$iEntryId = (int)$aNewsEntry['group_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];
 
        $this->_oTemplate->pageStart();
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aNewsEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_bx_groups_menu_view_news') => '',
        ));
 
        if ( (!$this->_iProfileId || $aNewsEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && (!$this->isAllowedView($aDataEntry, true) || !$this->isAllowedViewSubProfile($this->_oDb->_sTableNews, $aNewsEntry, true)) ) { 
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        bx_import ('NewsPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsPageView';
        $oPage = new $sClass ($this, $aNewsEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        $sPageCode = $oPage->getCode();

		// add group customizer
		if (BxDolInstallerUtils::isModuleInstalled("group_customize"))
		{ 
			$sMainCss = '<style type="text/css">' . 
				BxDolService::call('group_customize', 'get_group_style', array($aDataEntry['id'])) . '</style>';

			echo "
			<div id=\"divUnderCustomization\">
				$sMainCss
				$sPageCode
				<div class=\"clear_both\"></div>
			</div>
			";
		}else{
			echo $sPageCode;
		}
  
        bx_import('NewsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aNewsEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aNewsEntry['title'], false, false); 
    }


    function actionNewsEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aNewsEntry = $this->_oDb->getNewsEntryById($iEntryId);
		$iNewsId = (int)$aNewsEntry['group_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iNewsId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aDataEntry, $aNewsEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
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
  			$this->_oDb->_sFieldAuthorId = 'author_id';

			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
    
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
        $this->_oTemplate->pageCode(_t($sTitle, $aNewsEntry['title']));  
    }

    function actionNewsDelete ($iNewsId, $sMsgSuccess) {

		$aNewsEntry = $this->_oDb->getNewsEntryById($iNewsId);
		$iEntryId = (int)$aNewsEntry['group_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, 0, true))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete($aNewsEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteNewsByIdAndOwner($iNewsId, $iEntryId,  0, true)) {
           
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
 
		//[begin] news integration - modzzz
		if(getParam('bx_groups_modzzz_news')=='on'){ 
			$oNews = BxDolModule::getInstance('BxNewsModule');
			$sRedirectUrl = BX_DOL_URL_ROOT . $oNews->_oConfig->getBaseUri() . 'browse/my&filter=add_news&group=' . $iNewsId;
		  
			header ('Location:' . $sRedirectUrl);
			exit;
		}
 		//[end] news integration - modzzz


		if (!($aDataEntry = $this->_oDb->getEntryById($iNewsId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $this->_addNewsForm($iNewsId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]));  
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
  			$this->_oDb->_sFieldAuthorId = 'author_id';

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


    /*******[END - News Functions] ******************************/



	/******[BEGIN] Event functions **************************/ 
    function actionEvent ($sAction, $sParam1, $sParam2='') {
		switch($sAction){
			case 'download': 
				$this->eventDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionEventAdd ($sParam1, '_bx_groups_page_title_event_add');
			break;
			case 'edit':
				$this->actionEventEdit ($sParam1, '_bx_groups_page_title_event_edit');
			break;
			case 'delete':
				$this->actionEventDelete ($sParam1, _t('_bx_groups_msg_group_was_event_deleted'));
			break;
			case 'view':
				$this->actionEventView ($sParam1, _t('_bx_groups_msg_pending_event_approval')); 
			break; 
			case 'browse':
				return $this->actionEventBrowse ($sParam1, '_bx_groups_page_title_event_browse'); 
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

		$this->sUri = $sUri;

        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_bx_groups_menu_view_events') => '',
        ));

        bx_import ('EventPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('unit.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]), false, false);  
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
		$iEntryId = (int)$aEventEntry['group_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];
 
        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aEventEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_bx_groups_menu_view_events') => '',
        ));
 
        if ( (!$this->_iProfileId || $aEventEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && (!$this->isAllowedView($aDataEntry, true) || !$this->isAllowedViewSubProfile($this->_oDb->_sTableEvent, $aEventEntry, true)) ) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }
 
        bx_import ('EventPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventPageView';
        $oPage = new $sClass ($this, $aEventEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        $sPageCode = $oPage->getCode();

		// add group customizer
		if (BxDolInstallerUtils::isModuleInstalled("group_customize"))
		{ 
			$sMainCss = '<style type="text/css">' . 
				BxDolService::call('group_customize', 'get_group_style', array($aDataEntry['id'])) . '</style>';

			echo "
			<div id=\"divUnderCustomization\">
				$sMainCss
				$sPageCode
				<div class=\"clear_both\"></div>
			</div>
			";
		}else{
			echo $sPageCode;
		}

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
		$iGroupId = (int)$aEventEntry['group_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iGroupId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aDataEntry, $aEventEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        bx_import ('EventFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventFormEdit';
        $oForm = new $sClass ($this, $aEventEntry['uri'], $iGroupId,  $iEntryId, $aEventEntry);
  
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
  			$this->_oDb->_sFieldAuthorId = 'author_id';

			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
    
				$this->onEventSubItemChanged ('event', $iEntryId, $sStatus, $aDataEntry);

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
        $this->_oTemplate->pageCode(_t($sTitle, $aEventEntry['title']));  
    }

    function actionEventDelete ($iEventId, $sMsgSuccess) {

		$aEventEntry = $this->_oDb->getEventEntryById($iEventId);
		$iEntryId = (int)$aEventEntry['group_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, 0, true))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete($aEventEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteEventByIdAndOwner($iEventId, $iEntryId,  0, true)) {
           
            $this->onEventEventDeleted ($iEventId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
  
            $sJQueryJS = genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionEventAdd ($iEventId, $sTitle) {
  
		//[begin] event integration - modzzz
		if(getParam('bx_groups_boonex_events')=='on'){ 
			$oEvent = BxDolModule::getInstance('BxEventsModule');
			$sRedirectUrl = BX_DOL_URL_ROOT . $oEvent->_oConfig->getBaseUri() . 'browse/my&bx_events_filter=add_event&group=' . $iEventId;
		  
			header ('Location:' . $sRedirectUrl);
			exit;
		}
 		//[end] event integration - modzzz
  
		if (!($aDataEntry = $this->_oDb->getEntryById($iEventId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $this->_addEventForm($iEventId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]));  
    }
 
    function _addEventForm ($iEventId) { 
 
        bx_import ('EventFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iEventId);
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
  			$this->_oDb->_sFieldAuthorId = 'author_id';

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
	  
                $aDataEntry = $this->_oDb->getEventEntryById($iEntryId);
    
				$this->onEventSubItemCreate ('event', $iEntryId, $sStatus, $aDataEntry);

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



    function actionPaypalFeaturedProcess($iProfileId, $iGroupId) {
    
        $aData = &$_REQUEST;

        if($aData) {
			$iQuantity = (int)$aData['quantity'];

			$aDataEntry = $this->_oDb->getEntryById($iGroupId); 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
 
        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPurchaseFeatured(_t('_bx_groups_featured_purchase_failed')); 
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
				$this->actionPurchaseFeatured($iGroupId, _t('_bx_groups_featured_purchase_failed'));
			}else { 
				
				$fAmount = $this->_getReceivedAmount($aData);

				if($this->_oDb->isExistFeaturedTransaction($iProfileId, $aData['txn_id'])) {
					$this -> actionPurchaseFeatured($iGroupId, _t('_bx_groups_transaction_completed_already')); 
				} else {
					if( $this->_oDb->saveFeaturedTransactionRecord($iProfileId, $iGroupId,  $iQuantity, $fAmount, $aData['txn_id'], 'Paypal Purchase')) {

						$this->_oDb->updateFeaturedEntryExpiration($iGroupId, $iQuantity); 
			   
						$this->actionPurchaseFeatured($iGroupId, _t('_bx_groups_purchase_success',  $iQuantity));
					} else {
						$this -> actionPurchaseFeatured($iGroupId, _t('_bx_groups_trans_save_failed'));
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

	function initializeCheckout($iGroupId, $fTotalCost, $iQuantity=1, $bFeatured=0, $sTitle='') {
  
		$aFormData = array(
			'cmd' => '_xclick',
			'amount' => sprintf( "%.2f", (float)$fTotalCost)
		);
    	 
		if($bFeatured){
			$sNotifyUrl = $this->_oConfig->getFeaturedCallbackUrl() . $this->_iProfileId .'/'. $iGroupId;
			$sItemDesc = _t('_bx_groups_featured_purchase_desc', $sTitle);
 		}

		$aDataEntry = $this->_oDb->getEntryById($iGroupId);
 		$sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $aFormData = array_merge($aFormData, array(
			'business' => getParam('bx_groups_paypal_email'), 
            'item_name' => $sItemDesc,
			'amount' => $fTotalCost, 
            'item_number' => $iGroupId,
            'quantity' => $iQuantity,
            'currency_code' => $this->_oConfig->getPurchaseCurrency(),
            'no_note' => '1',
            'no_shipping' => '1',  
			'return' => $this->_oConfig->getReturnUrl() . $sUri,
			'notify_url' => $sNotifyUrl,  
			'rm' => '1'
        ));
  
    	Redirect($this->_oConfig->getPurchaseBaseUrl(), $aFormData, 'post', "Feature Group");
    	exit();
	}
 

    function actionPurchaseFeatured($iGroupId, $sTransMessage = '') {
 
        if(! $this->_iProfileId) {
            header('location: ' . BX_DOL_URL_ROOT . 'member.php');
        }

	    if($sTransMessage){
			$sMessageOutput = MsgBox($sTransMessage);  
		}
 
		$iPerDayCost = getParam('bx_groups_featured_cost');

		$aDataEntry = $this->_oDb->getEntryById($iGroupId);
		$sTitle = $aDataEntry['title'];

        $this->_oTemplate->pageStart();
 
		$aForm = array(
            'form_attrs' => array(
                'name' => 'buy_featured_form',
                'method' => 'post', 
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_featured/'.$iGroupId,
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
					'caption'  => _t('_bx_groups_form_caption_title'),
                    'content' => $sTitle,
                ), 
                'cost' => array(
                    'type' => 'custom',
                    'name' => 'cost',
					'caption'  => _t('_bx_groups_featured_cost_per_day'),
                    'content' => $iPerDayCost .' '. $this->_oConfig->getPurchaseCurrency(),
                ), 
                'status' => array(
                    'type' => 'custom',
                    'name' => 'status',
					'caption'  => _t('_bx_groups_featured_status'),
                    'content' => ($aDataEntry['featured']) ? _t('_bx_groups_featured_until', $this->_oTemplate->filterDate($aDataEntry['featured_expiry_date'])) : _t('_bx_groups_not_featured'), 
                ),  
                'quantity' => array(
                    'caption'  => _t('_bx_groups_caption_num_featured_days'),
                    'type'   => 'text',
                    'name' => 'quantity',
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_bx_groups_caption_err_featured_days'),
                    ),
                ),
                'submit' => array(
                    'type'  => 'submit',
                    'value' => ($aDataEntry['featured']) ? _t('_bx_groups_extend_featured') : _t('_bx_groups_get_featured'),
                    'name'  => 'submit_purchase',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  

        if ($oForm->isSubmittedAndValid() && $oForm->getCleanValue('quantity')) { 
 
			$fCost =  number_format($iPerDayCost, 2); 

            //header('location:' . $this->_oDb->generateFeaturedPaymentUrl($iGroupId, $oForm->getCleanValue('quantity'), $fCost));

			$this->initializeCheckout($iGroupId, $fCost, $oForm->getCleanValue('quantity'), true, $sTitle);  
			return; 
        } else {
             echo $sMessageOutput . $oForm->getCode();
        }

        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->addCss ('paid.css'); 
        $this->_oTemplate->pageCode(_t('_bx_groups_purchase_featured')); 
    }
  
    function isAllowedPurchaseFeatured ($aDataEntry, $isPerformAction = false) {

		if(getParam("bx_groups_buy_featured")!='on')
			return false;
 
		if($aDataEntry['author_id'] != $this->_iProfileId)
			return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_GROUPS_PURCHASE_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    } 
  
   function serviceGetForumPermission($iMemberId, $iForumId) {

        $iMemberId = (int)$iMemberId;
        $iForumId = (int)$iForumId;

        $aFalse = array (
            'admin' => 0,
            'read' => 0,
            'post' => 0,
        );

        if (!($aForum = $this->_oDb->getForumById ($iForumId)))
            return $aFalse;

        if (!($aDataEntry = $this->_oDb->getEntryById ($aForum['entry_id'])))
            return $aFalse;

        $aTrue = array (
            'admin' => (($aDataEntry[$this->_oDb->_sFieldAuthorId] == $iMemberId) || $this->isEntryAdmin($aDataEntry) || $this->isAdmin()) ? 1 : 0, // author is admin
            'read' => $this->isAllowedReadForum ($aDataEntry, $iMemberId) ? 1 : 0,
            'post' => $this->isAllowedPostInForum ($aDataEntry, $iMemberId) ? 1 : 0,
        );

        return $aTrue;
    }

	//[begin modzzz] embed video modification 
    function isAllowedEmbed(&$aDataEntry) { 
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        if (!$this->isMembershipEnabledForVideos())
            return false;                
        return $this->_oPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedEmbedNEW(&$aDataEntry) {
		
        if( !$this->isAllowedEdit($aDataEntry) ) return false;   

        if ( $this->isAdmin() ) return true;
             
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_GROUPS_ALLOW_EMBED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED; 
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

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        bx_import ('EmbedForm', $this->_aModule);
		$oForm = new BxGroupsEmbedForm ($this, $aDataEntry);
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
					$this->_oDb->removeYoutube($iEntryId, $iYoutubeId);
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
        $this->_oTemplate->pageCode(_t('_bx_groups_page_title_embed_video') . $aDataEntry[$this->_oDb->_sFieldTitle]);
    }
	//[end modzzz] embed video modification

    function serviceGetMemberMenuItem ()
    {
        return parent::_serviceGetMemberMenuItem (_t('_bx_groups'), _t('_bx_groups'), 'users');
    }

    function serviceGetMemberMenuItemAddContent ()
    {
        if (!$this->isAllowedAdd())
            return '';
        return parent::_serviceGetMemberMenuItem (_t('_bx_groups_group_single'), _t('_bx_groups_group_single'), 'users', false, '&bx_groups_filter=add_group');
    }

    function serviceGetWallPost ($aEvent)
    {
        $aParams = array(
            'txt_object' => '_bx_groups_wall_object',
            'txt_added_new_single' => '_bx_groups_wall_added_new',
            'txt_added_new_plural' => '_bx_groups_wall_added_new_items',
            'txt_privacy_view_event' => 'view_group',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallPost ($aEvent, $aParams);
    }

	function serviceGetWallAddComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_group',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallAddComment($aEvent, $aParams);
    }
 
    function serviceGetWallPostComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_group',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallPostComment($aEvent, $aParams);
    }

    function serviceGetWallPostOutline($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_group',
            'obj_privacy' => $this->_oPrivacy,
            'templates' => array(
                'grouped' => 'wall_outline_grouped'
            )
        );
        return parent::_serviceGetWallPostOutline($aEvent, 'users', $aParams);
    }

    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array())
    {
        return parent::_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_bx_groups_spy_post',
            'change' => '_bx_groups_spy_post_change',
            'join' => '_bx_groups_spy_join',
            'rate' => '_bx_groups_spy_rate',
            'commentPost' => '_bx_groups_spy_comment',
        ));
    }

    function serviceGetSubscriptionParams ($sAction, $iEntryId)
    {
        $a = array (
            'change' => _t('_bx_groups_sbs_change'),
            'commentPost' => _t('_bx_groups_sbs_comment'),
            'rate' => _t('_bx_groups_sbs_rate'),
            'join' => _t('_bx_groups_sbs_join'),
        );

        return parent::_serviceGetSubscriptionParams ($sAction, $iEntryId, $a);
    }

    /**
     * Install map support
     */
    function serviceMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('groups', array(
            'part' => 'groups',
            'title' => '_bx_groups',
            'title_singular' => '_bx_events_single',
            'icon' => 'modules/boonex/groups/|map_marker.png',
            'icon_site' => 'users',
            'join_table' => 'bx_groups_main',
            'join_where' => "AND `p`.`status` = 'approved'",
            'join_field_id' => 'id',
            'join_field_country' => 'country',
            'join_field_city' => 'city',
            'join_field_state' => '',
            'join_field_zip' => 'zip',
            'join_field_address' => '',
            'join_field_title' => 'title',
            'join_field_uri' => 'uri',
            'join_field_author' => 'author_id',
            'join_field_privacy' => 'allow_view_group_to',
            'permalink' => 'modules/?r=groups/view/',
        )));
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

    function onEventSubItemCreate ($sType, $iEntryId, $sStatus, $aDataEntry = array())
    {
        if ('approved' == $sStatus) {
			//
        }

        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_add', array($this->_oConfig->getUri().'_'.$sType, $iEntryId));
 
    }

    function onEventSubItemChanged ($sType, $iEntryId, $sStatus, $aDataEntry = array())
    {
        if ('approved' == $sStatus) {
			//
        }

        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_change', array($this->_oConfig->getUri().'_'.$sType, $iEntryId)); 
    }


    function onEventDeleted ($iEntryId, $aDataEntry = array())
    {
        // delete associated tags and categories
        $this->reparseTags ($iEntryId);
        $this->reparseCategories ($iEntryId);

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
        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri(), $iEntryId));


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

			// delete associated locations
			if (BxDolModule::getInstance('BxWmapModule'))
				BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri().'_event', $iId));
		} 

		$this->_oDb->deleteEvents($iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		//[end] delete events


		//[begin] delete sponsors
		$aSponsors = $this->_oDb->getAllSubItems('sponsor', $iEntryId);
		foreach($aSponsors as $aEachSponsor){
			
			$iId = (int)$aEachSponsor['id'];
 
			// delete votings
			bx_import('SponsorVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'SponsorVoting';
			$oVoting = new $sClass ($this->_oDb->_sSponsorPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);
 
			// delete comments 
			bx_import('SponsorCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'SponsorCmts';
			$oCmts = new $sClass ($this->_oDb->_sSponsorPrefix, $iId);
			$oCmts->onObjectDelete ();

			// delete associated locations
			if (BxDolModule::getInstance('BxWmapModule'))
				BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri().'_sponsor', $iId)); 
		} 

		$this->_oDb->deleteSponsors($iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		//[end] delete sponsors

		//[begin] delete blogs
		$aBlogs = $this->_oDb->getAllSubItems('blog', $iEntryId);
 
		foreach($aBlogs as $aEachBlog){
			
			$iId = (int)$aEachBlog['id'];
 
			// delete votings
			bx_import('BlogVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'BlogVoting';
			$oVoting = new $sClass ($this->_oDb->_sBlogPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);
 
			// delete comments 
			bx_import('BlogCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'BlogCmts';
			$oCmts = new $sClass ($this->_oDb->_sBlogPrefix, $iId);
			$oCmts->onObjectDelete ();
		} 

		$this->_oDb->deleteBlogs($iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		//[end] delete blogs
 
		//[begin] delete venues
		$aVenues = $this->_oDb->getAllSubItems('venue', $iEntryId);
		foreach($aVenues as $aEachVenue){
			
			$iId = (int)$aEachVenue['id'];
 
			// delete votings
			bx_import('VenueVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'VenueVoting';
			$oVoting = new $sClass ($this->_oDb->_sVenuePrefix, 0, 0);
			$oVoting->deleteVotings ($iId);
 
			// delete comments 
			bx_import('VenueCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'VenueCmts';
			$oCmts = new $sClass ($this->_oDb->_sVenuePrefix, $iId);
			$oCmts->onObjectDelete ();

			// delete associated locations
			if (BxDolModule::getInstance('BxWmapModule'))
				BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri().'_venue', $iId));  
		} 

		$this->_oDb->deleteVenues($iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		//[end] delete venues
  
		//[begin] delete news
		if(getParam('bx_groups_modzzz_news')=='on'){ 
			$oNews = BxDolModule::getInstance('BxNewsModule'); 
			$aNews = $this->_oDb->getModzzzNews($iEntryId);
			foreach($aNews as $aEachNews){ 
				if ($oNews->_oDb->deleteEntryByIdAndOwner($aEachNews['id'], 0, 0)) {
					$oNews->isAllowedDelete($aEachNews, true); // perform action
					$oNews->onNewsDeleted ($aEachNews['id'], $aEachNews);
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
 

        // arise alert
        $oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
        $oAlert->alert();
    }


    function serviceVenueMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('groups_venue', array(
            'part' => 'groups_venue',
            'title' => '_bx_groups_venue',
            'title_singular' => '_bx_groups_venue_single',
            'icon' => 'modules/boonex/groups/|map_marker.png',
            'icon_site' => 'users',
            'join_table' => 'bx_groups_venue_main',
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
            'permalink' => 'modules/?r=groups/venue/view/',
        )));
    }

    function serviceSponsorMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('groups_sponsor', array(
            'part' => 'groups_sponsor',
            'title' => '_bx_groups_sponsor',
            'title_singular' => '_bx_groups_sponsor_single',
            'icon' => 'modules/boonex/groups/|map_marker.png',
            'icon_site' => 'users',
            'join_table' => 'bx_groups_sponsor_main',
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
            'permalink' => 'modules/?r=groups/sponsor/view/',
        )));
    }


    function serviceEventMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('groups_event', array(
            'part' => 'groups_event',
            'title' => '_bx_groups_event',
            'title_singular' => '_bx_groups_event_single',
            'icon' => 'modules/boonex/groups/|map_marker.png',
            'icon_site' => 'users',
            'join_table' => 'bx_groups_event_main',
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
            'permalink' => 'modules/?r=groups/event/view/',
        )));
    }
 
    function isSubEntryAdmin($aSubEntry, $iProfileId = 0, $sIdField='id') {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
 
		$aDataEntry = $this->_oDb->getEntryById ((int)$aSubEntry['group_id']);

        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;
	 
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aSubEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;

        return $this->_oDb->isGroupAdmin ($aDataEntry[$sIdField], $iProfileId) && isProfileActive($iProfileId);
    }


	//[begin modzzz] add member modification  
    function actionAddMember ($sType, $iEntryId) {
 
        header('Content-type:text/html;charset=utf-8');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner ($iEntryId, 0, true))) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }
 
		if ($sType=='admin' && !$this->isAllowedManageAdmins($aDataEntry)) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Access denied')));
            exit;
        }
   
   		if ($sType=='fan' && !$this->isAllowedManageFans($aDataEntry)) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Access denied')));
            exit;
        }

		switch (bx_get('ajax_action')) {

			case 'add_member': 

				$sUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
	 
				$sMembers = bx_get('member');
				if($sMembers){
					$aMember = explode(',',$sMembers);
					foreach($aMember as $sEachMember){
						$sEachMember = trim($sEachMember);
						$iMember = ($sEachMember) ? getID($sEachMember) : 0;
	  
						if(!$iMember) continue;
 
						if($sType=='fan'){

							$isFan = $this->_oDb->isFan ($iEntryId, $iMember, true) || $this->_oDb->isFan ($iEntryId, $iMember, false);
  
							if(!$isFan){
								if($this->_oDb->joinEntry($iEntryId, $iMember, true)){
									$this->onEventJoin ($iEntryId, $iMember, $aDataEntry);
								}
							}

						}elseif($sType=='admin'){

							if ($this->_oDb->addGroupAdmin($iEntryId, array($iMember))) {
								 //
							} 
						}
					}

					$sMsg = ($sType=='fan') ? MsgBox(_t('_bx_groups_msg_add_fan_success')) : MsgBox(_t('_bx_groups_msg_add_admin_success'));

					echo $sMsg . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sUrl);

				}else{
					$sMsg = ($sType=='fan') ? MsgBox(_t('_bx_groups_msg_add_fan_fail')) : MsgBox(_t('_bx_groups_msg_add_admin_fail'));

					echo $sMsg . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sUrl); 
				}
				exit; 
			break;  
		}
  
        $this->_oTemplate->pageStart();

        bx_import ('MemberForm', $this->_aModule);
		$oForm = new BxGroupsMemberForm ($this, $sType, $iEntryId);
        $oForm->initChecker();
 
		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		$sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $oForm->getCode ()));
  
        $sForm = '<div id="group_member_content">' . $sCode . '</div>';
 
        $aVarsPopup = array (
            'title' => ($sType=='fan') ? _t('_bx_groups_title_add_fan') : _t('_bx_groups_title_add_admin'),
            'content' => $sForm,
        );        
        
		echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true); 
 	} 
	//[end modzzz] add member modification


	//[begin modzzz] ban member modification  
    function actionBanMember ($iEntryId) {
 
        header('Content-type:text/html;charset=utf-8');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner ($iEntryId, 0, true))) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }
 
		if (!$this->isEntryAdmin($aDataEntry)) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Access denied')));
            exit;
        }
  
		switch (bx_get('ajax_action')) {

			case 'ban_member': 

				$sUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
	 
				$sMemberName = trim(bx_get('member'));  
				$iMemberId = ($sMemberName) ? getID($sMemberName) : 0;
 
				if( $iMemberId && $this->_oDb->banMember($iEntryId, $iMemberId, $this->_iProfileId) ){
					$sMsg =  MsgBox(_t('_bx_groups_msg_ban_success'));

					echo $sMsg . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sUrl);

				}else{
					$sMsg = MsgBox(_t('_bx_groups_msg_ban_fail'));

					echo $sMsg . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sUrl); 
				}
				exit; 
			break;  
		}
  
        $this->_oTemplate->pageStart();

        bx_import ('BanForm', $this->_aModule);
		$oForm = new BxGroupsBanForm ($this, $iEntryId);
        $oForm->initChecker();
 
		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		$sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $oForm->getCode ()));
  
        $sForm = '<div id="group_member_content">' . $sCode . '</div>';
 
        $aVarsPopup = array (
            'title' => _t('_bx_groups_popup_title_ban_member'),
            'content' => $sForm,
        );        
        
		echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true); 
 	} 
	//[end modzzz] ban member modification

 

    function _processFansActions ($aDataEntry, $iMaxFans = 1000)
    {
        header('Content-type:text/html;charset=utf-8');

        if (false !== bx_get('ajax_action') && $this->isAllowedManageFans($aDataEntry) && 0 == strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {

            $iEntryId = $aDataEntry[$this->_oDb->_sFieldId];
            $aIds = array ();
            if (false !== bx_get('ids'))
                $aIds = $this->_getCleanIdsArray (bx_get('ids'));

            $isShowConfirmedFansOnly = false;
            switch (bx_get('ajax_action')) {

				case 'remove_admin': 
					 
                    if ($this->_oDb->removeAdmins($iEntryId, $aIds)) {
						//
					}
					$aProfiles = array ();
					$iNum = $this->_oDb->getAdmins($aProfiles, $iEntryId, 0, $iMaxFans);
					if (!$iNum) {
						echo MsgBox(_t('_Empty'));
					} else {
						echo $this->_profilesEdit ($aProfiles, true, $aDataEntry);
					} 
					exit; 
                    break; 

                case 'remove':
                    $isShowConfirmedFansOnly = true;
                    if ($this->_oDb->removeFans($iEntryId, $aIds)) {
                        foreach ($aIds as $iProfileId)
                            $this->onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry);
                    }
                    break;
                case 'add_to_admins':
                    $isShowConfirmedFansOnly = true;
                    if ($this->isAllowedManageAdmins($aDataEntry) && $this->_oDb->addGroupAdmin($iEntryId, $aIds)) {
                        $aProfiles = array ();
                        $iNum = $this->_oDb->getAdmins($aProfiles, $iEntryId, 0, $iMaxFans, $aIds);
                        foreach ($aProfiles as $aProfile)
                            $this->onEventFanBecomeAdmin ($iEntryId, $aProfile['ID'], $aDataEntry);
                    }
                    break;
                case 'admins_to_fans':
                    $isShowConfirmedFansOnly = true;
                    $iNum = $this->_oDb->getAdmins($aGroupAdmins, $iEntryId, 0, $iMaxFans);
                    if ($this->isAllowedManageAdmins($aDataEntry) && $this->_oDb->removeGroupAdmin($iEntryId, $aIds)) {
                        foreach ($aGroupAdmins as $aProfile) {
                            if (in_array($aProfile['ID'], $aIds))
                                $this->onEventAdminBecomeFan ($iEntryId, $aProfile['ID'], $aDataEntry);
                        }
                    }
                    break;
                case 'confirm':
                    if ($this->_oDb->confirmFans($iEntryId, $aIds)) {
                        echo '<script type="text/javascript" language="javascript">
                            document.location = "' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "view/" . $aDataEntry[$this->_oDb->_sFieldUri] . '";
                        </script>';
                        $aProfiles = array ();
                        $iNum = $this->_oDb->getFans($aProfiles, $iEntryId, true, 0, $iMaxFans, $aIds);
                        foreach ($aProfiles as $aProfile) {
                            $this->onEventJoin ($iEntryId, $aProfile['ID'], $aDataEntry);
                            $this->onEventJoinConfirm ($iEntryId, $aProfile['ID'], $aDataEntry);
                        }
                    }
                    break;
                case 'reject':
                    if ($this->_oDb->rejectFans($iEntryId, $aIds)) {
                        foreach ($aIds as $iProfileId)
                            $this->onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry);
                    }
                    break;

                case 'unban':
                    $this->_oDb->unbanMembers($iEntryId, $aIds);

					$aProfiles = array ();
					$iNum = $this->_oDb->getBanned($aProfiles, $iEntryId);
					if (!$iNum) {
						echo MsgBox(_t('_Empty'));
					} else {
						echo $this->_profilesEdit ($aProfiles, true, $aDataEntry);
					}
					exit; 
 
                    break;

                case 'list':
                    break;
            }

            $aProfiles = array ();
            $iNum = $this->_oDb->getFans($aProfiles, $iEntryId, $isShowConfirmedFansOnly, 0, $iMaxFans);
            if (!$iNum) {
                echo MsgBox(_t('_Empty'));
            } else {
                echo $this->_profilesEdit ($aProfiles, true, $aDataEntry);
            }
            exit;
        }
    }


    function actionManageAdminsPopup ($iEntryId) {
        $this->_actionManageAdminsPopup ($iEntryId, _t('_bx_groups_caption_manage_admins'), 'isAllowedManageAdmins', BX_GROUPS_MAX_FANS);
    }

    function _actionManageAdminsPopup ($iEntryId, $sTitle, $sFuncIsAllowedManageAdmins = 'isAllowedManageAdmins', $iMaxFans = 1000)
    {
        header('Content-type:text/html;charset=utf-8');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner ($iEntryId, 0, true))) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }

        if (!$this->$sFuncIsAllowedManageAdmins($aDataEntry)) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Access denied')));
            exit;
        }

        $aProfiles = array ();
        $iNum = $this->_oDb->getAdmins($aProfiles, $iEntryId, 0, $iMaxFans);
        if (!$iNum) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }
 
        $sActionsUrl = bx_append_url_params(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "view/" . $aDataEntry[$this->_oDb->_sFieldUri],  'ajax_action=');
 
        $aButtons = array (
            array (
                'type' => 'submit',
                'name' => 'fans_remove',
                'value' => _t('_sys_btn_fans_remove'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_fans_content', '{$sActionsUrl}remove_admin&ids=' + sys_manage_items_get_manage_fans_ids(), false, 'post'); return false;\"",
            ),
        );
 

        bx_import ('BxTemplSearchResult');
        $sControl = BxTemplSearchResult::showAdminActionsPanel('sys_manage_items_manage_fans', $aButtons, 'sys_fan_unit');

        $aVarsContent = array (
			'entry_id' => $iEntryId,
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

	function blogDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'blog_files');
 
        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getBlogEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }
 
        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableBlog, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

	function sponsorDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'sponsor_files');
 
        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getSponsorEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }
 
        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableSponsor, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

	function venueDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'venue_files');
 
        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getVenueEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }
 
        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableVenue, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

    function isSubProfileFan($sTable, $aDataEntry, $iProfileId = 0, $isConfirmed = true) {
 		
		if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isSubProfileFan ($sTable, $aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }

    function isAllowedViewSubProfile ($sTable, $aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_GROUPS_VIEW_GROUP, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
  
        $this->_oSubPrivacy = new BxGroupsSubPrivacy($this, $sTable); 
	    $isAllowed = $this->_oSubPrivacy->check('view', $aDataEntry['id'], $this->_iProfileId); 
 
		$aGroupEntry = $this->_oDb->getEntryById((int)$aDataEntry['group_id']);

		return $isAllowed && $this->_isAllowedViewByMembership ($aGroupEntry);  
    }
 
	function isAllowedRateSubProfile($sTable, &$aDataEntry) {       
        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;
        
		$this->_oSubPrivacy = new BxGroupsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('rate', $aDataEntry['id'], $this->_iProfileId);   
    }

    function isAllowedCommentsSubProfile($sTable, &$aDataEntry) {
           
        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;

        $this->_oSubPrivacy = new BxGroupsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('comment', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadPhotosSubProfile($sTable, &$aDataEntry) {
        
        if (!BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            return false;	
 
		if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        if ($this->isSubEntryAdmin($aDataEntry))
            return true;
  
		$this->_oSubPrivacy = new BxGroupsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadVideosSubProfile($sTable, &$aDataEntry) {
        
        if (!BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            return false;

		if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForVideos())
            return false;
        if ($this->isSubEntryAdmin($aDataEntry))
            return true;

        
		$this->_oSubPrivacy = new BxGroupsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadSoundsSubProfile($sTable, &$aDataEntry) {
 
        if (!BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            return false;
		
		if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForSounds())
            return false;
        if ($this->isSubEntryAdmin($aDataEntry))
            return true;

		$this->_oSubPrivacy = new BxGroupsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFilesSubProfile($sTable, &$aDataEntry) {
        
        if (!BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            return false;
		
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        if ($this->isSubEntryAdmin($aDataEntry))
            return true;

		$this->_oSubPrivacy = new BxGroupsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }
 
    function actionUploadPhotosSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadPhotosSubProfile', 'images', array ('images_choice', 'images_upload'), _t('_bx_groups_page_title_upload_photos'));
    }

    function actionUploadVideosSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadVideosSubProfile', 'videos', array ('videos_choice', 'videos_upload'), _t('_bx_groups_page_title_upload_videos'));
    } 

    function actionUploadFilesSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadFilesSubProfile', 'files', array ('files_choice', 'files_upload'), _t('_bx_groups_page_title_upload_files'));
    } 

    function actionUploadSoundsSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadSoundsSubProfile', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_bx_groups_page_title_upload_sounds'));
    } 

    function _actionUploadMediaSubProfile ($sType, $sUri, $sIsAllowedFuncName, $sMedia, $aMediaFields, $sTitle) {
   
		switch($sType){
			case 'venue':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableVenueMediaPrefix;
				$sTable = $this->_oDb->_sTableVenue ;
				$sDataFuncName = 'getVenueEntryByUri';
			break;
			case 'blog':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableBlogMediaPrefix;
				$sTable = $this->_oDb->_sTableBlog ;
				$sDataFuncName = 'getBlogEntryByUri';
			break;
			case 'sponsor':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableSponsorMediaPrefix;
				$sTable = $this->_oDb->_sTableSponsor ;
				$sDataFuncName = 'getSponsorEntryByUri';
			break;
			case 'event':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventMediaPrefix;
				$sTable = $this->_oDb->_sTableEvent ;
				$sDataFuncName = 'getEventEntryByUri';
			break;
			case 'news':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix;
				$sTable = $this->_oDb->_sTableNews ;
				$sDataFuncName = 'getNewsEntryByUri';
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

        $aGroupEntry = $this->_oDb->getEntryById($aDataEntry['group_id']);
  
  		$this->sUri = $aGroupEntry[$this->_oDb->_sFieldUri];

        $GLOBALS['oTopMenu']->setCustomSubHeader($aGroupEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aGroupEntry[$this->_oDb->_sFieldUri]);
  
        $iEntryId = $aDataEntry[$this->_oDb->_sFieldId];

        bx_import (ucwords($sType) . 'FormUploadMedia', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . ucwords($sType) . 'FormUploadMedia';
        $oForm = new $sClass ($this, $aDataEntry[$this->_oDb->_sFieldAuthorId],$aDataEntry['group_id'], $iEntryId, $aDataEntry, $sMedia, $aMediaFields);
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
   
	function isActiveMenuLink($sType='', $sUri=''){
		$oModuleDb = new BxDolModuleDb(); 
		return $oModuleDb->getModuleByUri('forum') ? true : false; 
	}
  
	function getGroupLink($iEntryId){

		$iEntryId = (int)$iEntryId;
		$aDataEntry = $this->_oDb->getEntryById($iEntryId);
		
		$sUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

		return '<a href="'.$sUrl.'">'.$aDataEntry[$this->_oDb->_sFieldTitle].'</a>';
	}

    function isAllowedActivate (&$aEvent, $isPerformAction = false)
    {
        if ($aEvent['status'] != 'pending')
            return false;
        if ($this->isAdmin())
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_GROUPS_APPROVE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }


    /*begin 2.1.9*/  
 	function actionAdministrationMembership(){
		
		$sCode = '';
		if(isset($_POST['submit'])) {

			$this->_oDb->emptyMembershipRecords();
 
			foreach($_POST['level'] as $iLevelId=>$iThreshold) { 
  				$this->_oDb->saveMembershipRecords($iLevelId, $iThreshold);
			}
			$sCode .= MsgBox(_t('_bx_groups_membership_changes_saved')); 
		}
 
        $aMembership = $this->_oDb->getMembershipsBy(array('type' => 'price_all'));
        if(empty($aMembership))
            return MsgBox(_t('_membership_txt_empty'));

        $sCode .= $this->_oTemplate->displayMembershipLevels($aMembership);

		return $sCode;
	}

    function isAllowedJoin (&$aDataEntry) {        
        if (!$this->_iProfileId) 
            return false;
  
        if ($this->_oDb->isBanned($aDataEntry[$this->_oDb->_sFieldId], $this->_iProfileId)) 
            return false;

		$iFansCount = $this->_oDb->getFanCount($aDataEntry[$this->_oDb->_sFieldId]);//$aDataEntry[$this->_oDb->_sFieldFansCount];
		$aMembership = getMemberMembershipInfo($aDataEntry[$this->_oDb->_sFieldAuthorId]);
		$iThreshold = (int)$this->_oDb->getMembershipThreshold($aMembership['ID']);
 
		if($iThreshold && ($iFansCount >= $iThreshold)) return false; 

        $isAllowed = $this->_oPrivacy->check('join', $aDataEntry['id'], $this->_iProfileId);     
        return $isAllowed && $this->_isAllowedJoinByMembership ($aDataEntry);
    } 
    /*end 2.1.9*/

    function isAllowedAdminAddFan ($aDataEntry) {        

		$iFansCount = $this->_oDb->getFanCount($aDataEntry[$this->_oDb->_sFieldId]);//$aDataEntry[$this->_oDb->_sFieldFansCount];
		$aMembership = getMemberMembershipInfo($aDataEntry[$this->_oDb->_sFieldAuthorId]);
		$iThreshold = (int)$this->_oDb->getMembershipThreshold($aMembership['ID']);
 
		if($iThreshold && ($iFansCount >= $iThreshold)) return false; 

		return ($this->isAdmin() || $this->isEntryAdmin($aDataEntry));
	}
 

	//logo mod
    function processLogo($iEntryId) {
 	    $iEntryId  = (int)$iEntryId;

 		$sIcon = $this->_actionUploadIcon($iEntryId);

		if($sIcon){	
			$this->_oDb->updatePostWithLogo($iEntryId, $sIcon);  
		}
	}

    function _actionUploadIcon ($iEntryId=0 ) {
		$iEntryId  = (int)$iEntryId;

		$iIconWidth = (int)getParam("bx_groups_icon_width");
		$iIconHeight = (int)getParam("bx_groups_icon_height"); 
		$sIcon = "";
	  
		$sFile = "iconfile";
 		$sPath = $this->_oConfig->getMediaPath();	
		if ( 0 < $_FILES[$sFile]['size'] && 0 < strlen( $_FILES[$sFile]['name'] ) ) {
			$sFileName = time();
			$sExt = $this->moveUploadedImage( $_FILES, $sFile, $sPath . $sFileName, '', false );
			if( strlen( $sExt ) && !(int)$sExt ) {
			 
				if($iEntryId)
					$this->_oDb->_actionRemoveIcon($iEntryId);
 
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
				$this->_oDb->_actionRemoveIcon($iEntryId); 

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
	//end - logo modification

	//begin - survey modification
    function isAllowedModulePost($sModule) {
		switch($sModule){ 
			case 'survey':
				if(getParam("bx_groups_survey_active")=='on'){  
					$oSurvey = BxDolModule::getInstance('BxSurveyModule');
					if($oSurvey->isAllowedAdd()){
						return true; 
					}
				}
			break;
 
		}
		return false;
	}
	//end - survey modification

}
