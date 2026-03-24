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

bx_import('BxDolTwigPageView');
 
class BxClassifiedPageView extends BxDolTwigPageView {	

	function BxClassifiedPageView(&$oMain, &$aDataEntry) {
		parent::BxDolTwigPageView('modzzz_classified_view', $oMain, $aDataEntry);
	
        $this->sSearchResultClassName = 'BxClassifiedSearchResult';
        $this->sFilterName = 'filter';

        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/'. $this->aDataEntry['uri'];
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  
	}
  
	function getBlockCode_Info() {
     
	
	 //Freddy
		 if($this->aDataEntry['company_id']) return;
        return array($this->_blockInfo ($this->aDataEntry));
    }

	//override the similar mod in the parent class
    function _blockInfo ($aData) {
		
		$iAuthorId = (int)$aData['author_id'];
		if($iAuthorId){
			$aAuthor = getProfileInfo($aData['author_id']); 
			$sAuthorName =  $aAuthor['NickName'];
			$sAuthorLink = getProfileLink($aAuthor['ID']);	
			$icoThumb = get_member_thumbnail($aAuthor['ID'], 'none', true);
		}else{
			$aVars = array (
				'author_name' => $aData['author_name'] 
			);
			$icoThumb = $this->_oTemplate->parseHtmlByName('anonymous', $aVars);  
		}

        $aVars = array (
            'author_thumb' => $icoThumb,
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
            'cats' => $this->_oDb->getCategoryName($aData['category_id']),
			'tags' => $this->_oTemplate->parseTags($aData['tags']),
            'fields' => '',
 
			'bx_if:owner' => array( 
				'condition' =>  $this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($aData),
				'content' => array(   
					'expire' => $aData['expiry_date'] ? date('M d, Y', $aData['expiry_date']) : _t('_modzzz_classified_never'), 
					 
					'featured' => $aData['featured'] ? ($aData['featured_expiry_date'] ? _t('_modzzz_classified_featured_until') .' '. $this->_oTemplate->filterCustomDate($aData['featured_expiry_date']) : _t('_modzzz_classified_featured_classified')) : _t('_modzzz_classified_not_featured'), 
	 
				),  		
			),

        );
        return $this->_oTemplate->parseHtmlByName('entry_view_block_info', $aVars);
    }

	function getBlockCode_Share11() {  
 
		$aVars = array (
		    'site_url' => $this->sUrlStart,
			'width' => getParam('modzzz_classified_facebook_app_width')
		);
		return $this->_oTemplate->parseHtmlByName('block_social_share', $aVars);	 
	}
 
	function getBlockCode_Desc() {
        return array($this->_oTemplate->blockDesc ($this->aDataEntry));
    }

	function getBlockCode_Contact() { 
	
	//Freddy ajout $aData = $this->aDataEntry;
		$aData = $this->aDataEntry;
		
        if (!$this->_oMain->isAllowedViewContacts ($this->aDataEntry)) {
			$oModuleDb = new BxDolModuleDb();  
			if($oModuleDb->isModule('membership')){
				$sUpgradeUrl = BxDolService::call('membership', 'get_upgrade_url', array());
				//Freddy Remplacement/Modification de $sMsg = _t('_modzzz_classified_upgrade_membership_url', $sUpgradeUrl); Par                $sMsg = _t('_modzzz_classified_upgrade_membership_martinboi'); pour utiliser le module Membership de Martinboi
				//$sMsg = _t('_modzzz_classified_upgrade_membership_url', $sUpgradeUrl);
				$sMsg = _t('_modzzz_classified_upgrade_membership_martinboi');
			}else{ 
				//$sMsg = _t('_modzzz_classified_upgrade_membership');
				$sMsg = _t('_modzzz_classified_upgrade_membership_martinboi');
			}
            return array( MsgBox($sMsg) );
        }
/*
		$bShow = true; 
		if($this->aDataEntry['allow_view_contact'] && !$this->_oMain->isAdmin()){
			$bShow = false; 

			$aViewerMembership = getMemberMembershipInfo(getLoggedId());
			$iViewerMembershipId = $aViewerMembership['ID'];
			$aAllowMembership = explode(CATEGORIES_DIVIDER, $this->aDataEntry['allow_view_contact']);
			foreach($aAllowMembership as $iMembershipId){
				if($iViewerMembershipId == $iMembershipId) $bShow = true;  
			}
		} 
	 
		if(!$bShow) return;
*/

// Freddy ajout
		$iAuthorId = (int)$aData['author_id'];
		if($iAuthorId){
			$aAuthor = getProfileInfo($aData['author_id']); 
			$sAuthorName =  $aAuthor['NickName'];
			$sAuthorLink = getProfileLink($aAuthor['ID']);	
			$icoThumb = get_member_thumbnail($aAuthor['ID'], 'none', true);
		}else{
			$aVars = array (
				'author_name' => $aData['author_name'] 
			);
			$icoThumb = $this->_oTemplate->parseHtmlByName('anonymous', $aVars);  
		}
		$sUrl_compose =  BX_DOL_URL_ROOT  . 'mail.php?mode=compose&recipient_id='.$iAuthorId ;
        
		
		//Freddy ajout Befirend
		$iUserId = getLoggedId();
		$iId = $aData['author_id'];
		$aFriends = getMyFriendsEx($iUserId);
		$aFriendsIds = array_keys($aFriends);
		// [END] Freddy ajout Befirend
		
		
		 $aVars = array (
		  'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
            'cats' => $this->_oDb->getCategoryName($aData['category_id']),
			'tags' => $this->_oTemplate->parseTags($aData['tags']),
            'fields' => '',
 
			'bx_if:owner' => array( 
				'condition' =>  $this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($aData),
				'content' => array(   
					'expire' => $aData['expiry_date'] ? date('M d, Y', $aData['expiry_date']) : _t('_modzzz_classified_never'), 
					 
					'featured' => $aData['featured'] ? ($aData['featured_expiry_date'] ? _t('_modzzz_classified_featured_until') .' '. $this->_oTemplate->filterCustomDate($aData['featured_expiry_date']) : _t('_modzzz_classified_featured_classified')) : _t('_modzzz_classified_not_featured'), 
	 
				),  		
			),

		 'author_thumb' => $icoThumb,
		//'message_url'=>  $sUrl_compose,
		
		
		 //Freddy ajout Befirend
		 
	 'bx_if:befriend' => array(
	'condition' => !in_array($iId, $aFriendsIds) && !isFriendRequest($iUserId, $iId) &&  !$this->_oMain->isEntryAdmin($aData),
	 'content' => array(
	  'id' => $iId
					    ),
				           ),
		// [END] Freddy ajout Befirend
		
		
		
		 'bx_if:contacter_url' => array(
	                'condition' =>  !$this->_oMain->isEntryAdmin($aData),
	                'content' => array(
	                'contacter_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'inquire/' . $this->aDataEntry['id'],
					    ),
				           ),
		 
		    
			
			
		       
			    'bx_if:sellertelephone' => array( 
								'condition' =>$aData['sellertelephone'],
								'content' => array(
              'sellertelephone' => $aData['sellertelephone'],  
								), 
							),
			   'bx_if:mobile' => array( 
								'condition' =>$aData['sellerfax'],
								'content' => array(
              'mobile' => $aData['sellerfax'],  
								), 
							),
			
		 
		 );
    // Freddy remlpacement de return $this->_blockCustomDisplay ($this->aDataEntry, 'contact'); Par return array($this->_oTemplate->parseHtmlByName('block_contact_details', $aVars));
		//return $this->_blockCustomDisplay ($this->aDataEntry, 'contact');
		return array($this->_oTemplate->parseHtmlByName('block_contact_details', $aVars));
    }
  
 	function getBlockCode_Additional() { 
        return $this->_blockCustomDisplay ($this->aDataEntry, 'additional');
    }

	function _blockCustomDisplay($aDataEntry, $sType) {
		
		switch($sType) {
		  
			case "contact":
				$aAllow = array('sellername','sellerwebsite','selleremail','sellertelephone','sellerfax');
			break;
			case "location":
				$aAllow = array('address1','address2','city','state','country','zip');
			break; 
			case "additional":
				$aAllow = array('custom_field1','custom_field2','custom_field3','custom_field4','custom_field5','custom_field6','custom_field7','custom_field8','custom_field9','custom_field10','custom_sub_field1','custom_sub_field2','custom_sub_field3','custom_sub_field4','custom_sub_field5','custom_sub_field6','custom_sub_field7','custom_sub_field8','custom_sub_field9','custom_sub_field10');
			break; 
		}
  
		$sFields = $this->_oTemplate->blockCustomFields($aDataEntry,$aAllow);

		if(!$sFields) return;

		$aVars = array ( 
            'fields' => $sFields, 
        );

        return array ($this->_oTemplate->parseHtmlByName('custom_block_info', $aVars));   
    }
   
	function getBlockCode_Photo() {
        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Video() {
        return $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Sound() {
        return $this->_blockSound ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'sounds'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Files() {
        return $this->_blockFiles ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'files'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Rate() {
        modzzz_classified_import('Voting');
        $o = new BxClassifiedVoting ('modzzz_classified', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_classified_import('Cmts');
        $o = new BxClassifiedCmts ('modzzz_classified', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

            $oSubscription = new BxDolSubscription();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'modzzz_classified', '', (int)$this->aDataEntry['id']);

            $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'TitleSubscribe' => $aSubscribeButton['title'], 
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_modzzz_classified_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_classified_action_title_delete') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_classified_action_title_share') : '',
				'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_modzzz_classified_action_title_promote') : '',  
                'TitleInquire' => $this->_oMain->isAllowedInquire($this->aDataEntry) ? _t('_modzzz_classified_action_title_inquire') : '',
                'TitleBuy' => $this->_oMain->isAllowedBuy($this->aDataEntry) ? _t('_modzzz_classified_action_title_buy') : '',
				'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_classified_action_remove_from_featured') : _t('_modzzz_classified_action_add_to_featured')) : '',
				
				'AddToFavorite' => $this->_oMain->isAllowedMarkAsFavorite($this->aDataEntry) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $this->aDataEntry['id']) ? _t('_modzzz_classified_action_remove_from_favorite') : _t('_modzzz_classified_action_add_to_favorite')) : '',
				
				'TitlePurchaseFeatured' => $this->_oMain->isAllowedPurchaseFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_classified_action_title_extend_featured') : _t('_modzzz_classified_action_title_purchase_featured')) : '',
		
                'TitleRelist' => $this->_oMain->isAllowedRelist($this->aDataEntry) ? _t('_modzzz_classified_action_title_relist') : '',
                'TitleExtend' => $this->_oMain->isPaidClassified($this->aDataEntry['id']) ? ($this->_oMain->isAllowedExtend($this->aDataEntry) ? _t('_modzzz_classified_action_title_extend') : '') : '',
                'TitlePremium' => $this->_oMain->isAllowedPremium($this->aDataEntry) ? ($this->_oMain->isPaidClassified($this->aDataEntry['id']) ? '' : _t('_modzzz_classified_action_title_premium')) : '',
 
				'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_modzzz_classified_action_upload_photos') : '',
                'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_modzzz_classified_action_upload_videos') : '',
                'TitleEmbed' => $this->_oMain->isAllowedEmbed($this->aDataEntry) ? _t('_modzzz_classified_action_embed_video') : '', 
                'TitleUploadSounds' => $this->_oMain->isAllowedUploadSounds($this->aDataEntry) ? _t('_modzzz_classified_action_upload_sounds') : '',
                'TitleUploadFiles' => $this->_oMain->isAllowedUploadFiles($this->aDataEntry) ? _t('_modzzz_classified_action_upload_files') : '',
            );

			$sSubscribeInfo = $oSubscription->getData();

		}else{

           $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => 0,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'ScriptSubscribe' => '',
                'TitleSubscribe' => '', 
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_classified_action_title_share') : '',
				'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_modzzz_classified_action_title_promote') : '',  
                'TitleInquire' => $this->_oMain->isAllowedInquire($this->aDataEntry) ? _t('_modzzz_classified_action_title_inquire') : '',
                'TitleBuy' => $this->_oMain->isAllowedBuy($this->aDataEntry) ? _t('_modzzz_classified_action_title_buy') : '',
	 				
				'TitleEdit' => '',	
				'TitleDelete' => '',	
				'AddToFeatured' => '',	
				'TitlePurchaseFeatured' => '',	
                'TitleRelist' => '',	

                'TitleRelist' => '',	
                'TitleExtend' => '',	
                'TitlePremium' => '',	
 
				'TitleUploadPhotos' => '', 
                'TitleUploadVideos' => '', 
                'TitleEmbed' => '', 
                'TitleUploadSounds' => '', 
                'TitleUploadFiles' => '',	
            ); 
		}

		if (!$aInfo['TitleEdit'] && !$aInfo['TitleDelete'] && !$aInfo['TitleShare'] && !$aInfo['AddToFeatured'] && !$aInfo['TitleUploadPhotos'] && !$aInfo['TitleUploadVideos'] && !$aInfo['TitleEmbed'] && !$aInfo['TitleUploadSounds'] && !$aInfo['TitleUploadFiles'] && !$aInfo['TitleInquire']) 
			return '';

		return  $sSubscribeInfo . $oFunctions->genObjectsActions($aInfo, 'modzzz_classified'); 
    }    
 
	function getBlockCode_Local() {    

		return $this->ajaxBrowse('other_local', $this->_oDb->getParam('modzzz_classified_perpage_main_recent'),array(),$this->aDataEntry['id'],$this->aDataEntry['city'],$this->aDataEntry['state']);  
	}

	function getBlockCode_Other() {    
		return $this->ajaxBrowse('other', $this->_oDb->getParam('modzzz_classified_perpage_main_recent'),array(),$this->aDataEntry['author_id'],$this->aDataEntry['id']); 
	}

    function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $sValue2 = '', $isDisableRss = false, $isPublicOnly = true) {
        $oMain = BxDolModule::getInstance('BxClassifiedModule');

        bx_import ('SearchResult', $oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue, $sValue2);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);

        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => getTemplateIcon('rss.png'))));

        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);

        if (!($s = $o->displayResultBlock())) 
           
			 //Freddy mise en commentaire
		   // return $isPublicOnly ? array(MsgBox(_t('_Empty')), $aMenu) : '';


        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->_oConfig->getBaseUri() . $o->sBrowseUrl);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    } 

    function getCode() { 
        return parent::getCode();
    }	

	/*[begin] - patch 2.1.5*/ 
	function getBlockCode_Map() {

        if (!$this->_oMain->isAllowedViewContacts ($this->aDataEntry)) {
			$oModuleDb = new BxDolModuleDb();  
			if($oModuleDb->isModule('membership')){
				$sUpgradeUrl = BxDolService::call('membership', 'get_upgrade_url', array());
				$sMsg = _t('_modzzz_classified_upgrade_membership_url', $sUpgradeUrl);
			}else{ 
				$sMsg = _t('_modzzz_classified_upgrade_membership');
			}
            return array( MsgBox($sMsg) );
        }

        return BxDolService::call('wmap', 'location_block', array('classified', $this->aDataEntry[$this->_oDb->_sFieldId]));
    }

	function getBlockCode_Location() { 
        if (!$this->_oMain->isAllowedViewContacts ($this->aDataEntry)) {
			$oModuleDb = new BxDolModuleDb();  
			if($oModuleDb->isModule('membership')){
				$sUpgradeUrl = BxDolService::call('membership', 'get_upgrade_url', array());
				$sMsg = _t('_modzzz_classified_upgrade_membership_url', $sUpgradeUrl);
			}else{ 
				$sMsg = _t('_modzzz_classified_upgrade_membership');
			}
            return array( MsgBox($sMsg) );
        }

        return $this->_blockCustomDisplay ($this->aDataEntry, 'location'); 
    }
   	/*[end] - patch 2.1.5*/

	function getBlockCode_VideoEmbed() {

		$aVideoUrls = $this->_oDb->getYoutubeVideos($this->aDataEntry['id']);
		
		$sFirstVideoId = '';
		$sFirstVideoTitle = '';
		$aVideos = array();
		if(empty($aVideoUrls))
			return;

		foreach($aVideoUrls as $aEachUrl){  
			$sFirstVideoId = ($sFirstVideoId) ? $sFirstVideoId : $this->_oTemplate->youtubeId($aEachUrl['url']);
			$sFirstVideoTitle = ($sFirstVideoTitle) ? $sFirstVideoTitle : $aEachUrl['title'];
			$aVideos[] = array ( 
				'video_id' => $this->_oTemplate->youtubeId($aEachUrl['url']), 
				'video_title' => process_db_input($aEachUrl['title']), 
			);
		}

		$aVars = array(
			'video_id' => $sFirstVideoId,
			'video_title' => $sFirstVideoTitle,
			'bx_repeat:video' => $aVideos
		);
		 
        return $this->_oTemplate->parseHtmlByName('block_youtube_videos', $aVars);   
    }

/******************FREDDY MANAGE classified*****************/
	
	function getBlockCode_ManageMyClassified() {
		
    
		if(!$this->_oMain->isAllowedEdit($this->aDataEntry)) return; 
		
		
		//$add_staff_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'staff/add/' . $this->aDataEntry['id']; 
		
		$add_photos_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'upload_photos/' . $this->aDataEntry['uri']; 
		$add_files_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'upload_files/' . $this->aDataEntry['uri']; 
		$embed_videos_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'embed/' . $this->aDataEntry['uri']; 
		
		$modifier_edit_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'edit/' . $this->aDataEntry['id'];
		$supprimer_edit_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'delete/' . $this->aDataEntry['id'];
		
		$aVars = array(  
			
			'add_photos_url' => $add_photos_url, 
			'embed_videos' => $embed_videos_url, 
			'add_files_url' => $add_files_url, 
			 'modifier_edit_url' =>  $modifier_edit_url, 
			  
			  'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_classified_action_title_delete') : '',
			   'supprimer_edit_url' =>  $supprimer_edit_url, 
			  'id' =>$this->aDataEntry['id'],
			 
  		);
		
		
		return $this->_oTemplate->parseHtmlByName('block_manage_classified', $aVars);   
	}
	/******************FIN FREDDY MANAGE classified****************/
	
	function getBlockCode_ManagePageUser() {
	 if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxClassifiedModule');
			  
    
		 
		 $aData = $this->aDataEntry;
		

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

          
	 
		
	$add_favorite_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'mark_favorite/' . $aData['id'];
	
	$showPopupAnyHtml = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'share_popup/' . $aData['id'];
	
	$Invite_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'invite/' . $aData['id'];
	
	$Renseignement_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'inquire/' . $aData['id'];
    
  
		$aVars = array(  
		
		       'thumbnail_url' => $sUserThumbnail,
			   'Profile_Link' => $sAuthorLink,
			   
			     'TitleInquire' => $this->_oMain->isAllowedInquire($this->aDataEntry) ? _t('_modzzz_classified_action_title_inquire') : '',
				  'InquireUrl' =>$Renseignement_url,
				  
				  'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_modzzz_classified_action_title_promote') : '',  
                   'InviteUrl' =>$Invite_url,
			   
			   
			   'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_classified_action_title_share') : '',
			   'SharePopup' => $showPopupAnyHtml,
			   
			   
			  
			   
			    
			   
			 'AddToFavorite' => $this->_oMain->isAllowedMarkAsFavorite($aData) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $aData['id']) ? _t('_modzzz_classified_action_remove_from_favorite') : _t('_modzzz_classified_action_add_to_favorite')) : '',
			 
			  'Favorite_Url' =>  $add_favorite_url, 
			  'id' => $aData['id'],
			  
			  
			 

  		);
		
  
		return $this->_oTemplate->parseHtmlByName('block_manage_page', $aVars);   
	}
	
	}
	
	
	////////////////////////////////////////////////////////////////////////




}


