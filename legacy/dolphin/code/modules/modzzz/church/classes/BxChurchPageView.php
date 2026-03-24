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

bx_import('BxDolTwigPageView');

class BxChurchPageView extends BxDolTwigPageView {	

	function BxChurchPageView(&$oMain, &$aDataEntry) {
		parent::BxDolTwigPageView('modzzz_church_view', $oMain, $aDataEntry);
	
		$this->oMain = $oMain;

        $this->sSearchResultClassName = 'BxChurchSearchResult';
        $this->sFilterName = 'filter';

        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/'. $this->aDataEntry['uri'];
		$this->sUri = $this->aDataEntry['uri'];
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  
	
		switch($_GET['ajax']) {  
			case 'delete_review':
				$iChurchId = $_GET['id'];
				$iReviewId = $_GET['review'];
				$this->_oDb->DeleteReview($iChurchId, $iReviewId); 
				echo '';
				exit;
			break; 
		}		
	
	}
 
	function getBlockCode_Donors() {  
  
		if(!$this->oMain->isAllowedViewDonors($this->aDataEntry))
			return;

	    $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'view/' . $this->sUri . '?';

        $this->_oTemplate->addCss(array('unit.css','twig.css'));

        return $this->ajaxBrowseSubProfile(
            'donors',
            'donors',
            $this->_oDb->getParam('modzzz_church_perpage_view_subitems'), 
            array(), $this->sUri, true, false 
        );
 
    }
  
	function getBlockCode_ListedBy() {

        $aData = $this->aDataEntry;
  
        $aAuthor = getProfileInfo($aData['author_id']);
 
		$sAuthorName =  $aAuthor['NickName'];
		$sAuthorLink = getProfileLink($aAuthor['ID']);	
		$icoThumb = get_member_thumbnail($aAuthor['ID'], 'none', true);
	 
        $aVars = array (
            'author_unit' => $icoThumb,
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
             'cats' => $this->_oDb->getCategoryName($aData['category_id']),
			'tags' => $this->_oTemplate->parseTags($aData['tags']),
            'views' => $aData['views'],
            'fields' => '',
            'author_username' => $sAuthorName,
            'author_url' => $sAuthorLink,

			'bx_if:owner' => array( 
				'condition' =>  ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($aData)),
				'content' => array(   
					'expire' => $aData['expiry_date'] ? date('M d, Y', $aData['expiry_date']) : _t('_modzzz_church_never'), 
					'featured' => $aData['featured'] ? ($aData['featured_expiry_date'] ? _t('_modzzz_church_featured_until') .' '. $this->_oTemplate->filterCustomDate($aData['featured_expiry_date']) : _t('_modzzz_church_featured_listing')) : _t('_modzzz_church_not_featured_listing'), 
				),  		
			),

        );
        return array($this->_oTemplate->parseHtmlByName('entry_view_block_info', $aVars));
    }
  
 	function getBlockCode_Info() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'info');
    }
 
  
	function getBlockCode_Desc() {
        return array($this->_oTemplate->blockDesc ($this->aDataEntry));
    }

	function getBlockCode_Believe() {
		
		if(!$this->aDataEntry['believe'])
			return;

        return array($this->_oTemplate->blockDesc ($this->aDataEntry,'believe'));
    }

	function getBlockCode_History() {
		
		if(!$this->aDataEntry['history'])
			return;

        return array($this->_oTemplate->blockDesc ($this->aDataEntry,'history'));
    }

	function getBlockCode_ServiceHours() {
		
		if(!$this->aDataEntry['service_hours'])
			return;

        return array($this->_oTemplate->blockDesc ($this->aDataEntry,'service_hours'));
    }
 
	function getBlockCode_BusinessContact() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'businesscontact');
    }

	function getBlockCode_Location() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'location');
    }
  
	function _blockCustomDisplay($aDataEntry, $sType) {
		
		switch($sType){ 
			case "info":
				$aAllow = array('capacity','dress_code');
			break;  
			case "businesscontact":
				$aAllow = array('businesswebsite','businessemail','businesstelephone','businessfax');
			break;
			case "location":
				$aAllow = array('address1','address2','city','state','country','zip');
			break;  
		}
  
		$sFields = $this->_oTemplate->blockCustomFields($aDataEntry,$aAllow);
		
		if(!$sFields) return;

		$aVars = array ( 
            'fields' => $sFields, 
        );

        return array($this->_oTemplate->parseHtmlByName('custom_block_info', $aVars));   
    }

	function getBlockCode_VideoEmbed() { 
		$sBlock = '';
	    $sVideoEmbed = trim($this->aDataEntry['video_embed']);
		
		if(!$sVideoEmbed)
			  return;
	 
 		$pos = (strpos($sVideoEmbed, 'youtube.com') || strpos($sVideoEmbed, 'youtu.be'));

		if ($pos === false) {  
			$sBlock = 'block_embed';  
		}else{ 
			$pos = strpos($sVideoEmbed, 'iframe');
			if ($pos === false) {  
				$sBlock = 'block_youtube'; 
				$sVideoEmbed = $this->_oTemplate->youtubeId($sVideoEmbed);
			}else{
				$sBlock = 'block_embed';  
			}
		}
 
		if(!$sVideoEmbed)
			  return;

		$aVars = array ('embed' => $sVideoEmbed); 
			  
	    return $this->_oTemplate->parseHtmlByName($sBlock, $aVars);  
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
        modzzz_church_import('Voting');
        $o = new BxChurchVoting ('modzzz_church', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_church_import('Cmts');
        $o = new BxChurchCmts ('modzzz_church', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

		$aLoggedInfo = array();
        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

            $oSubscription = new BxDolSubscription();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'modzzz_church', '', (int)$this->aDataEntry['id']);
			
			$isFan = $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 0) || $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 1);

            $aLoggedInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'TitleSubscribe' => $aSubscribeButton['title'], 
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_modzzz_church_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_church_action_title_delete') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_church_action_title_share') : '',
			    'IconJoin' => $isFan ? 'sign-out' : 'sign-in',
				'TitleJoin' => $this->_oMain->isAllowedJoin($this->aDataEntry) ? ($isFan ? _t('_modzzz_church_action_title_leave') : _t('_modzzz_church_action_title_join')) : '',
				'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_modzzz_church_action_title_promote') : '',  
				'TitleBroadcast' => $this->_oMain->isAllowedBroadcast($this->aDataEntry) ? _t('_modzzz_church_action_title_broadcast') : '',
	 		
				'TitleClaim' => $this->_oMain->isAllowedClaim($this->aDataEntry) ? _t('_modzzz_church_action_title_claim') : '',
                'TitleInquire' => $this->_oMain->isAllowedInquire($this->aDataEntry) ? _t('_modzzz_church_action_title_inquire') : '',
				'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_church_action_remove_from_featured') : _t('_modzzz_church_action_add_to_featured')) : '',
                'TitleManageFans' => $this->_oMain->isAllowedManageFans($this->aDataEntry) ? _t('_modzzz_church_action_manage_fans') : '',
				'TitlePurchaseFeatured' => $this->_oMain->isAllowedPurchaseFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_church_action_title_extend_featured') : _t('_modzzz_church_action_title_purchase_featured')) : '',
		 
                'TitleRelist' => $this->_oMain->isAllowedRelist($this->aDataEntry) ? _t('_modzzz_church_action_title_relist') : '',
                'TitleExtend' => $this->_oMain->isPaidChurch($this->aDataEntry['id']) ? ($this->_oMain->isAllowedExtend($this->aDataEntry) ? _t('_modzzz_church_action_title_extend') : '') : '',
                'TitlePremium' => $this->_oMain->isAllowedPremium($this->aDataEntry) ? ($this->_oMain->isPaidChurch($this->aDataEntry['id']) ? '' : _t('_modzzz_church_action_title_premium')) : '',

                'TitleEventAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_church_action_title_add_event') : '',
                'TitleStaffAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_church_action_title_add_staff') : '',
                'TitleSermonAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_church_action_title_add_sermon') : '',
                'TitleDoctrineAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_church_action_title_add_doctrine') : '',
				'TitleNewsAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_church_action_title_add_news') : '',
                'TitleMembersAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_church_action_title_add_members') : '',
                'TitleBranchesAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_church_action_title_add_branches') : '',
                'TitleMinistriesAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_church_action_title_add_ministries') : '',

                'TitlePostFaq' => $this->_oMain->isAllowedCreateFaqs($this->aDataEntry) ? _t('_modzzz_church_action_title_add_to_faq') : '',
                'TitlePostReview' => $this->_oMain->isAllowedPostReviews($this->aDataEntry) ? _t('_modzzz_church_action_title_post_review') : '',
  
				'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_modzzz_church_action_upload_photos') : '',
                'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_modzzz_church_action_upload_videos') : '',
                'TitleUploadSounds' => $this->_oMain->isAllowedUploadSounds($this->aDataEntry) ? _t('_modzzz_church_action_upload_sounds') : '',
                'TitleUploadFiles' => $this->_oMain->isAllowedUploadFiles($this->aDataEntry) ? _t('_modzzz_church_action_upload_files') : '',
            );

			$sSubscribeInfo = $oSubscription->getData(); 

		}else{

            $aLoggedInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'ScriptSubscribe' => '',                      
                'TitleSubscribe' => '',                       
                'TitleEdit' => '',                            
                'TitleDelete' => '',                          
                'TitleShare' => '',                           
                'TitleJoin' => '',                            
				'TitleInvite' => '',                          
				'TitleBroadcast' => '',   
				'TitleClaim' => '',                           
                'TitleInquire' => '',                         
				'AddToFeatured' => '',                        
                'TitleManageFans' => '',                      
				'TitlePurchaseFeatured' => '',   
                'TitleRelist' => '',                          
                'TitleExtend' => '',                          
                'TitlePremium' => '',  
                'TitleEventAdd' => '',                        
                'TitleStaffAdd' => '',                        
                'TitleSermonAdd' => '',                       
                'TitleDoctrineAdd' => '',                     
				'TitleNewsAdd' => '',                         
                'TitleMembersAdd' => '',                      
                'TitleBranchesAdd' => '',                     
                'TitleMinistriesAdd' => '',  
                'TitlePostFaq' => '',                         
                'TitlePostReview' => '',    
				'TitleUploadPhotos' => '',                    
                'TitleUploadVideos' => '',                    
                'TitleUploadSounds' => '',                    
                'TitleUploadFiles' => '',                     
            );

		}

		$aDonateInfo = array (   
			'TitleDonate' => $this->_oMain->isAllowedDonate($this->aDataEntry) ? _t('_modzzz_church_action_title_make_donation') : '', 
		);
 
		$aInfo = array_merge($aLoggedInfo, $aDonateInfo);

		if (!$aInfo['TitleEdit'] && !$aInfo['TitleDelete'] && !$aInfo['TitleShare'] && !$aInfo['TitleJoin'] && !$aInfo['TitleInvite'] && !$aInfo['TitleBroadcast'] && !$aInfo['TitleManageFans'] && !$aInfo['TitlePurchaseFeatured'] && !$aInfo['AddToFeatured'] && !$aInfo['TitleUploadPhotos'] && !$aInfo['TitleUploadVideos'] && !$aInfo['TitleUploadSounds'] && !$aInfo['TitleUploadFiles'] && !$aInfo['TitleClaim'] && !$aInfo['TitleInquire'] && !$aInfo['TitleRelist'] && !$aInfo['TitleExtend'] && !$aInfo['TitlePremium'] && !$aInfo['TitleEventAdd'] && !$aInfo['TitleStaffAdd'] && !$aInfo['TitleSermonAdd'] && !$aInfo['TitleDoctrineAdd'] && !$aInfo['TitleNewsAdd'] && !$aInfo['TitleMembersAdd'] && !$aInfo['TitleBranchesAdd'] && !$aInfo['TitleMinistriesAdd'] && !$aInfo['TitlePostFaq'] && !$aInfo['TitlePostReview'] && !$aInfo['TitleDonate']) 
			return '';

		return $sSubscribeInfo . $oFunctions->genObjectsActions($aInfo, 'modzzz_church');
       
    }    
 
	function getBlockCode_Local() {    
		return $this->ajaxBrowse('other_local', $this->_oDb->getParam('modzzz_church_perpage_main_recent'),array(),$this->aDataEntry['id'],$this->aDataEntry['city'],$this->aDataEntry['state']);   
	}

	function getBlockCode_Other() {    
		return $this->ajaxBrowse('other', $this->_oDb->getParam('modzzz_church_perpage_main_recent'),array(),$this->aDataEntry['author_id'],$this->aDataEntry['id']); 
	}

    function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $sValue2 = '', $isDisableRss = false, $isPublicOnly = true) {
        $oMain = BxDolModule::getInstance('BxChurchModule');

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
            return $isPublicOnly ? array(MsgBox(_t('_Empty')), $aMenu) : '';


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

    function getBlockCode_Fans() {
        return parent::_blockFans ($this->_oDb->getParam('modzzz_church_perpage_view_fans'), 'isAllowedViewFans', 'getFans');
    }            

    function getBlockCode_FansUnconfirmed() {
        return parent::_blockFansUnconfirmed (BX_CHURCH_MAX_FANS);
    }

   function getBlockCode_News () {
 
        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'view/' . $this->sUri . '?';
  
        $this->_oTemplate->addCss(array('unit.css','twig.css'));

        return $this->ajaxBrowseSubProfile(
            'news',
            'news',
            $this->_oDb->getParam('modzzz_church_perpage_view_subitems'), 
            array(), $this->sUri, true, false 
        );
 
    }
 
    function getBlockCode_Events () {
 
        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'view/' . $this->sUri . '?';
  
        $this->_oTemplate->addCss(array('unit.css','twig.css'));

        return $this->ajaxBrowseSubProfile(
            'event',
            'events',
            $this->_oDb->getParam('modzzz_church_perpage_view_subitems'), 
            array(), $this->sUri, true, false 
        );
 
    }
   
    function ajaxBrowseSubProfile($sType, $sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true) {

        bx_import ('SearchResult', $this->_oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);
 
        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);
 
        if (!($s = $o->displaySubProfileResultBlock($sType))) {
             return array(MsgBox(_t('_Empty')), $aMenu);
		} 

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
 
        $this->_oMain->_processFansActions ($this->aDataEntry, BX_CHURCH_MAX_FANS);

        return parent::getCode();
    }

}
