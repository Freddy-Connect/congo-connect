<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Professional
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

class BxProfessionalPageView extends BxDolTwigPageView {	

	function BxProfessionalPageView(&$oMain, &$aDataEntry) {
		parent::BxDolTwigPageView('modzzz_professional_view', $oMain, $aDataEntry);
	
        $this->sSearchResultClassName = 'BxProfessionalSearchResult';
        $this->sFilterName = 'filter';

        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/'. $this->aDataEntry['uri'];  
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  

		$this->sUri = $this->aDataEntry['uri']; 
	}
	    
	function getBlockCode_Info() {
        return array($this->_blockInfo ($this->aDataEntry));
    }

	function getBlockCode_Schedule() {

		$sSchedule = $this->getSchedule();
		if(!$sSchedule) return;

        $aVars = array (
			'schedule' => $sSchedule, 
		);

        return $this->_oTemplate->parseHtmlByName('block_schedule', $aVars); 
    }
  
	//override the similar mod in the parent class
    function _blockInfo ($aData) {

        $aAuthor = getProfileInfo($aData['author_id']);
 
		$sAuthorName =  $aAuthor['NickName'];
		$sAuthorLink = getProfileLink($aAuthor['ID']);	
		$icoThumb = get_member_thumbnail($aAuthor['ID'], 'none', true);
	  
		$sOnlinePresence = $this->getOnlinePresence();
	 
        $aVars = array (
            'author_thumb' => $icoThumb,
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
            'cats' => $this->_oDb->getCategoryName($aData['category_id']),
			'tags' => $this->_oTemplate->parseTags($aData['tags']),
             
			'bx_if:online_presence' => array( 
				'condition' => $sOnlinePresence,
				'content' => array(   
					'online_presence' => $sOnlinePresence,
 				),  		
			),
 
			'bx_if:owner' => array( 
				'condition' =>  $this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($aData),
				'content' => array(   
					'expire' => $aData['expiry_date'] ? date('M d, Y', $aData['expiry_date']) : _t('_modzzz_professional_never'),
					'featured' => $aData['featured'] ? ($aData['featured_expiry_date'] ? _t('_modzzz_professional_featured_until') .' '. $this->_oTemplate->filterCustomDate($aData['featured_expiry_date']) : _t('_modzzz_professional_featured_professional')) : _t('_modzzz_professional_not_featured'), 
				),  		
			), 
        );
        return $this->_oTemplate->parseHtmlByName('entry_view_block_info', $aVars);
    }

	function getOnlinePresence(){
		
		$aData = $this->aDataEntry;
 
		if(!($aData['facebook']||$aData['website']||$aData['blog']||$aData['linkedin']||$aData['twitter']||$aData['youtube']||$aData['pinterest']||$aData['skype'])) return '';

        $aVars = array (
  			'bx_if:skype' => array( 
				'condition' => $aData['skype'],
				'content' => array(   
					'skype_url' => $aData['skype']
 				),  		
			),  
  			'bx_if:facebook' => array( 
				'condition' => $aData['facebook'],
				'content' => array(   
					'facebook_url' => (substr($aData['facebook'],0,3)=="www") ? "https://".$aData['facebook'] : $aData['facebook']  
 				),  		
			),  			
			'bx_if:website' => array( 
				'condition' => $aData['website'],
				'content' => array(   
					'website_url' => (substr($aData['website'],0,3)=="www") ? "http://".$aData['website'] : $aData['website']
 				),  		
			),
  			'bx_if:blog' => array( 
				'condition' => $aData['blog'],
				'content' => array(   
					'blog_url' => $aData['blog']
 				),  		
			),
			'bx_if:linkedin' => array( 
				'condition' => $aData['linkedin'],
				'content' => array(   
					'linkedin_url' => (substr($aData['linkedin'],0,3)=="www") ? "https://".$aData['linkedin']  : $aData['linkedin']
 				),  		
			),
			'bx_if:twitter' => array( 
				'condition' => $aData['twitter'],
				'content' => array(   
					'twitter_url' => (substr($aData['twitter'],0,3)=="www") ? "https://".$aData['twitter'] : $aData['twitter']
 				),  		
			), 
 			'bx_if:youtube' => array( 
				'condition' => $aData['youtube'],
				'content' => array(   
					'youtube_url' => (substr($aData['youtube'],0,3)=="www") ? "https://".$aData['youtube'] : $aData['youtube']
 				),  		
			),
			'bx_if:pinterest' => array( 
				'condition' => $aData['pinterest'], 
				'content' => array(   
					'pinterest_url' => (substr($aData['pinterest'],0,3)=="www") ? "https://".$aData['pinterest'] : $aData['pinterest']
 				),  		
			),
        );

        return $this->_oTemplate->parseHtmlByName('entry_view_online_presence', $aVars);
    }
  
	function getBlockCode_Desc() {

 		$aCategory = $this->_oDb->getCategoryById($this->aDataEntry['parent_category_id']);
		$sCategoryUrl = $this->_oDb->getCategoryUrl($aCategory['uri']); 
		if($this->aDataEntry['category_id']){
			$aSubCategory = $this->_oDb->getCategoryById($this->aDataEntry['category_id']); 
			$sSubCategoryUrl = $this->_oDb->getSubCategoryUrl($aSubCategory['uri']);
		}

        $aVars = array (

            'bx_if:proprietor' => array (
                'condition' => trim($this->aDataEntry['proprietor']),
                'content' => array ( 
					'proprietor' => $this->aDataEntry['proprietor'], 
                ),
            ),

            'bx_if:businessname' => array (
                'condition' => trim($this->aDataEntry['businessname']),
                'content' => array ( 
					'businessname' => $this->aDataEntry['businessname'], 
                ),
            ),
 
            'description' => $this->aDataEntry['desc'],  
 
			'location' => $this->_oMain->_formatLocation($this->aDataEntry, true, true),  

			'category_name' => $aCategory['name'],

			'category_url' => $sCategoryUrl,

            'bx_if:sub_category' => array (
                'condition' => $this->aDataEntry['category_id'],
                'content' => array ( 
					'subcategory_name' => $aSubCategory['name'],
					'subcategory_url' => $sSubCategoryUrl,
                ),
            )
		);	

        return array($this->_oTemplate->parseHtmlByName('block_main_description', $aVars));
    }

 	function getBlockCode_Inquire() {
		$aVars = array (
			'caption' => _t('_modzzz_professional_action_title_inquire'),
			'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .'inquire/'. $this->aDataEntry['id'] 
		); 
		
		return array($this->_oTemplate->parseHtmlByName('block_button', $aVars));	
	}
 
 	function getBlockCode_Review() {

		if(!$this->_oMain->isAllowedPostReviews($this->aDataEntry)) return; 
		$aVars = array (
			'caption' => _t('_modzzz_professional_action_title_post_review'),
			'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .'review/add/'. $this->aDataEntry['id'] 
		); 
		
		return array($this->_oTemplate->parseHtmlByName('block_button', $aVars));	
	}

 
	function getBlockCode_BusinessContact() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'businesscontact');
    }

	function getBlockCode_Location() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'location');
    }
 
	function getBlockCode_Jobs() {

		if(getParam("modzzz_professional_jobs_active")!='on')  
			return;

		$oJobs = BxDolModule::getInstance('BxJobsModule');
 
        $oJobs->_oTemplate->addCss('unit.css');

        return $this->ajaxBrowseSubProfile(
            'jobs',
            'jobs',
            $this->_oDb->getParam('modzzz_professional_perpage_view_subitems'), 
            array(), $this->aDataEntry['id'], true, false, false 
        );  
	}
 
	function getBlockCode_Coupons() {

		if(getParam("modzzz_professional_coupons_active")!='on')  
			return;

		$oCoupons = BxDolModule::getInstance('BxCouponsModule');
 
        $oCoupons->_oTemplate->addCss('unit.css');

        return $this->ajaxBrowseSubProfile(
            'coupons',
            'coupons',
            $this->_oDb->getParam('modzzz_professional_perpage_view_subitems'), 
            array(), $this->aDataEntry['id'], true, false, false 
        );  
	}

	function getBlockCode_Deals() {

		if(getParam("modzzz_professional_deals_active")!='on')  
			return;

		$oDeals = BxDolModule::getInstance('BxDealsModule');
 
        $oDeals->_oTemplate->addCss('unit.css');

        return $this->ajaxBrowseSubProfile(
            'deals',
            'deals',
            $this->_oDb->getParam('modzzz_professional_perpage_view_subitems'), 
            array(), $this->aDataEntry['id'], true, false, false 
        );  
	} 

	function _blockCustomDisplay($aDataEntry, $sType) {
		
		switch($sType)
		{  
			case "businesscontact":
				$aAllow = array('businessemail','businesstelephone','businessfax');
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
 
	function youtubeId($url) {
		$v='';
		if (preg_match('%youtube\\.com/(.+)%', $url, $match)) {
			$match = $match[1];
			$replace = array("watch?v=", "v/", "vi/");
			$sQueryString = str_replace($replace, "", $match); 
			$aQueryParams = explode('&',$sQueryString);
			$v = $aQueryParams[0]; 
		}else{ 
			$url = parse_url($sVideoEmbed);
			parse_str($url['query']);
		}

		return $v;  
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
        modzzz_professional_import('Voting');
        $o = new BxProfessionalVoting ('modzzz_professional', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
 
        return array($o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_professional_import('Cmts');
        $o = new BxProfessionalCmts ('modzzz_professional', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;
 
        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
 
 			$sCode = '';

            $oSubscription = BxDolSubscription::getInstance();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'modzzz_professional', '', (int)$this->aDataEntry['id']);
            $sCode .= $oSubscription->getData();

            $isFan = $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 0) || $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 1);

			$isConfirmedFan = $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 1);
 
            $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'TitleSubscribe' => $aSubscribeButton['title'], 
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_modzzz_professional_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_professional_action_title_delete') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_professional_action_title_share') : '',
                'TitleBroadcast' => $this->_oMain->isAllowedBroadcast($this->aDataEntry) ? _t('_modzzz_professional_action_title_broadcast') : '',  
    
				'TitleJoin' => $this->_oMain->isAllowedJoin($this->aDataEntry) ? ($isFan ? _t('_modzzz_professional_action_title_leave') : _t('_modzzz_professional_action_title_join')) : '',
                'IconJoin' => $isFan ? 'sign-out' : 'sign-in',

				'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_modzzz_professional_action_title_promote') : '',  
				'TitleClaim' => $this->_oMain->isAllowedClaim($this->aDataEntry) ? _t('_modzzz_professional_action_title_claim') : '',
 				'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_professional_action_remove_from_featured') : _t('_modzzz_professional_action_add_to_featured')) : '',

				'AddToFavorite' => $this->_oMain->isAllowedMarkAsFavorite($this->aDataEntry) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $this->aDataEntry['id']) ? _t('_modzzz_professional_action_remove_from_favorite') : _t('_modzzz_professional_action_add_to_favorite')) : '',

                'TitlePostClient' => $this->_oMain->isAllowedPostClients($this->aDataEntry) ? _t('_modzzz_professional_action_title_post_client') : '',
                'TitlePostService' => $this->_oMain->isAllowedPostServices($this->aDataEntry) ? _t('_modzzz_professional_action_title_post_service') : '',
 
                'TitleManageFans' => $isConfirmedFan && $this->_oMain->isAllowedManageFans($this->aDataEntry) ? _t('_modzzz_professional_action_manage_fans') : '',
				'TitlePurchaseFeatured' => $this->_oMain->isAllowedPurchaseFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_professional_action_title_extend_featured') : _t('_modzzz_professional_action_title_purchase_featured')) : '',
		 
                'TitleRelist' => $this->_oMain->isAllowedRelist($this->aDataEntry) ? _t('_modzzz_professional_action_title_relist') : '',
                'TitleExtend' => $this->_oMain->isPaidProfessional($this->aDataEntry['id']) ? ($this->_oMain->isAllowedExtend($this->aDataEntry) ? _t('_modzzz_professional_action_title_extend') : '') : '',
                'TitlePremium' => $this->_oMain->isAllowedPremium($this->aDataEntry) ? ($this->_oMain->isPaidProfessional($this->aDataEntry['id']) ? '' : _t('_modzzz_professional_action_title_premium')) : '',
 
                'TitleEmbed' => $this->_oMain->isAllowedEmbed($this->aDataEntry) ? _t('_modzzz_professional_action_upload_youtube') : '', 
 
				'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_modzzz_professional_action_upload_photos') : '',
                'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_modzzz_professional_action_upload_videos') : '',
                'TitleUploadSounds' => $this->_oMain->isAllowedUploadSounds($this->aDataEntry) ? _t('_modzzz_professional_action_upload_sounds') : '',
                'TitleUploadFiles' => $this->_oMain->isAllowedUploadFiles($this->aDataEntry) ? _t('_modzzz_professional_action_upload_files') : '',
            );
  
	        if(BxDolRequest::serviceExists('wall', 'get_repost_js_click')) {
				$sCode .= BxDolService::call('wall', 'get_repost_js_script');

				$aInfo['repostCpt'] = _t('_Repost');
				$aInfo['repostScript'] = BxDolService::call('wall', 'get_repost_js_click', array($this->_oMain->_iProfileId, 'modzzz_professional', 'add', (int)$this->aDataEntry['id']));
			}else {
                $aInfo['repostCpt'] = '';
            }

			$sCodeActions = $oFunctions->genObjectsActions($aInfo, 'modzzz_professional');
			if(empty($sCodeActions))
                return '';

            return $sCode . $sCodeActions;
        }

        return '';
    }  
  
	function getBlockCode_Local() {  
		return $this->ajaxBrowse('other_local', $this->_oDb->getParam('modzzz_professional_perpage_main_recent'),array(),$this->aDataEntry['id'],$this->aDataEntry['city'],$this->aDataEntry['state']);   
 	}

	function getBlockCode_Other() {    
		return $this->ajaxBrowse('other', $this->_oDb->getParam('modzzz_professional_perpage_main_recent'),array(),$this->aDataEntry['author_id'],$this->aDataEntry['id']); 
	}

    function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $sValue2 = '', $isDisableRss = false, $isPublicOnly = true, $bShowAll=true) {
        $oMain = BxDolModule::getInstance('BxProfessionalModule');

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
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->_oConfig->getBaseUri() . $o->sBrowseUrl, -1, -1, $bShowAll);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    }   

    function getBlockCode_Fans() {
        return parent::_blockFans ($this->_oDb->getParam('modzzz_professional_perpage_view_fans'), 'isAllowedViewFans', 'getFans');
    }            

    function getBlockCode_FansUnconfirmed() {
        return parent::_blockFansUnconfirmed (BX_PROFESSIONAL_MAX_FANS);
    }

    function getCode() {
 
        $this->_oMain->_processFansActions ($this->aDataEntry, BX_PROFESSIONAL_MAX_FANS);

        return parent::getCode();
    }

	function getBlockCode_Reviews () {
  
        return $this->ajaxBrowseSubProfile(
            'review',
            'reviews',
            $this->_oDb->getParam('modzzz_professional_perpage_browse_subitems'),
            array(), $this->sUri, true, false 
        ); 
    }
 
	function getBlockCode_Services() {
 
        return $this->ajaxBrowseSubProfile(
            'service',
            'services',
            $this->_oDb->getParam('modzzz_professional_perpage_browse_subitems'), 
            array(), $this->sUri, true, false 
        ); 
    }
 
    function ajaxBrowseSubProfile($sType, $sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true, $bShowAll=true) {

        bx_import ('SearchResult', $this->_oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);
 
        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);
 
        if (!($s = $o->displaySubProfileResultBlock($sType))) {
			 return;
             //return array(MsgBox(_t('_Empty')), $aMenu);
		} 

        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->_oConfig->getBaseUri() . $o->sBrowseUrl, -1, -1, $bShowAll);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    }    
 
 	function getBlockCode_Tags() {

        $aVars = array (
            'tags' => $this->_oTemplate->parseTags($this->aDataEntry['tags'], 'topic-link topic-hover'),
        );

        return array($this->_oTemplate->parseHtmlByName('entry_view_tags', $aVars));
	}

	function getBlockCode_Forum() {
  
		$iNumComments = (int)getParam("modzzz_professional_perpage_main_comment");
		$aPosts = $this->_oDb->getLatestForumPosts($iNumComments, $this->aDataEntry['id']);
  
		if(empty($aPosts))
			return;

  		foreach($aPosts as $aEachPost){ 
			$sCode .= $this->_oTemplate->forum_unit ($aEachPost, 'forum_unit', true);
		} 
 
		return array($sCode);
	}
  
	function getSchedule() {

		$iEntryId = (int)$this->aDataEntry['id'];
 
		$aPeriod = $this->_oDb->getScheduleFeed ($iEntryId, 'main'); 

		if(!count($aPeriod)) return;

		$aEntries = array();
		foreach ($aPeriod as $k => $r) {  
 
			$sDay = $this->_oDb->getDays($r['from_day'], 'long');
 
			$iFromHour = $r['from_hour'];
			$iToHour = $r['to_hour'];

			$iFromMinute = $r['from_minute'];
			$iToMinute = $r['to_minute'];

			$sFromPeriod = $this->_oDb->getPeriods($r['from_period']);
			$sToPeriod = $this->_oDb->getPeriods($r['to_period']);
 
			$aEntries[$k] = array(); 
		 
			$aEntries[$k]['day'] = $sDay;
			
			$aEntries[$k]['fromtime'] = "$iFromHour:$iFromMinute $sFromPeriod";
			
			$aEntries[$k]['totime'] = "$iToHour:$iToMinute $sToPeriod";   
		}
   
		$aVars = array (  
			'bx_repeat:items' => $aEntries,
		);  

		return $this->_oTemplate->parseHtmlByName('entry_view_schedule', $aVars); 
	}

/*
	//begin- calendar   
 	function getBlockCode_Calendar() {
		global $oTemplConfig;
	
		$sCalendarC = _t('_Calendar');
 
		$iId = $this->aDataEntry['id'];
 
		if(!$this->_oDb->isJoinChallengeAlready($iId, $this->_oMain->_iProfileId))
			 return;
	 
		$sChallengeUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $this->aDataEntry['uri'];
 
		$sCalCode = $this->PageCalendarBuilder();
		
		if(!$sCalCode) return;

	 	$sRetHtml  = "<div id='challenge_fullcal_block'>"; 
		$sRetHtml .= $sCalCode;
 		$sRetHtml .= "</div>";
	  
		return array($sRetHtml);
	}

	function PageCalendarBuilder() {
		global $dir;
		global $site;
		global $sdatingThumbWidth;
		global $sdatingThumbHeight;
		global $oTemplConfig;

		$iId = $this->aDataEntry['id'];
	 
		$iChallengeDays = $this->_oDb->getChallengeDays($iId, $this->aDataEntry['author_id']);
		if(!$iChallengeDays) return;
 
		$iPicSize = $this->iIconSize + 15;
 
		$iStartTime = db_value("SELECT `created` AS `StartDate_f` FROM `modzzz_professional_main` WHERE `id`='{$iId}'");

		// now year, month and day
		list($iNowYear, $iNowMonth, $iNowDay) = explode( '-', date('Y-m-d') );
		// current year, month, month name, day, days in month
		if ( isset($_REQUEST['month']) ) {
			list($iCurMonth, $iCurYear) = explode( '-', $_REQUEST['month'] );
			$iCurMonth = (int)$iCurMonth;
			$iCurYear = (int)$iCurYear;
		}
		else {
			list($iCurMonth, $iCurYear) = explode( '-', date('n-Y', $iStartTime) );
		}
		list($sCurMonthName, $iCurDaysInMonth) = explode( '-', date('F-t', mktime( 0, 0, 0, $iCurMonth, $iNowDay, $iCurYear )) );
		// previous month year, month
		$iPrevYear = $iCurYear;
		$iPrevMonth = $iCurMonth - 1;
		if ( $iPrevMonth <= 0 ) {
			$iPrevMonth = 12;
			$iPrevYear--;
		}
		// next month year, month
		$iNextYear = $iCurYear;
		$iNextMonth = $iCurMonth + 1;
		if ( $iNextMonth > 12 ) {
			$iNextMonth = 1;
			$iNextYear++;
		}
		// days in previous month
		$iPrevDaysInMonth = (int)date( 't', mktime( 0, 0, 0, $iPrevMonth, $iNowDay, $iPrevYear ) );
		// days-of-week of first day in current month
		$iFirstDayDow = (int)date( 'w', mktime( 0, 0, 0, $iCurMonth, 1, $iCurYear ) );
		// from which day of previous month calendar starts
		$iPrevShowFrom = $iPrevDaysInMonth - $iFirstDayDow + 1;

		// select array
		$aCalendar = array();
	   
		$sRequest = "SELECT DAYOFMONTH(`StartDate`) AS `StartDay`, MONTH(`StartDate`) AS `StartMonth`, YEAR(`StartDate`) AS `StartYear`, DAYOFMONTH(`EndDate`) AS `EndDay`, MONTH(`EndDate`) AS `EndMonth`, YEAR(`EndDate`) AS `EndYear` FROM `modzzz_professional_challenge_members`
		WHERE  
	   `ChallengeID` = '{$iId}' AND `MemberID`='{$this->_oMain->_iProfileId}' 
		";
  
		$resMember = db_res( $sRequest );
		while ( $aMemberData = mysql_fetch_assoc($resMember) ) {
			$dtChallengeStart = mktime(0,0,0,$aMemberData['StartMonth'],$aMemberData['StartDay'],$aMemberData['StartYear']);
 
			$dtChallengeEnd = mktime(0,0,0,$aMemberData['EndMonth'],$aMemberData['EndDay'],$aMemberData['EndYear']);
		} 

		$sRequest = "SELECT DAYOFMONTH(`DayCompleted`) AS `CompletedDay`, MONTH(`DayCompleted`) AS `CompletedMonth` FROM `modzzz_professional_challenge_tracking`
							WHERE ( MONTH(`DayCompleted`) = {$iCurMonth} AND YEAR(`DayCompleted`) = {$iCurYear} OR
									MONTH( DATE_ADD(`DayCompleted`, INTERVAL 1 MONTH) ) = {$iCurMonth} AND YEAR( DATE_ADD(`DayCompleted`, INTERVAL 1 MONTH) ) = {$iCurYear} OR
									MONTH( DATE_SUB(`DayCompleted`, INTERVAL 1 MONTH) ) = {$iCurMonth} AND YEAR( DATE_SUB(`DayCompleted`, INTERVAL 1 MONTH) ) = {$iCurYear} ) 
							AND `modzzz_professional_challenge_tracking`.`ChallengeID` = '{$iId}' AND `MemberID`='{$this->_oMain->_iProfileId}' 
							";
  
		$vRes = db_res( $sRequest );
		while ( $aCompletedData = mysql_fetch_assoc($vRes) ) {
			$aCalendar["{$aCompletedData['CompletedDay']}-{$aCompletedData['CompletedMonth']}"]= "completed"; 
		}

		// make calendar grid
		$bPreviousMonth = ($iFirstDayDow > 0 ? true : false);
		$bNextMonth = false;
		$iCurrentDay = ($bPreviousMonth) ? $iPrevShowFrom : 1;

		for ($i = 0; $i < 6; $i++) {
			for ($j = 0; $j < 7; $j++) {
				$aCalendarGrid[$i][$j]['day'] = $iCurrentDay;
				$aCalendarGrid[$i][$j]['month'] = ($bPreviousMonth ? $iPrevMonth : ($bNextMonth ? $iNextMonth : $iCurMonth));
				$aCalendarGrid[$i][$j]['current'] = (!$bPreviousMonth && !$bNextMonth);
				$aCalendarGrid[$i][$j]['today'] = ($iNowYear == $iCurYear && $iNowMonth == $iCurMonth && $iNowDay == $iCurrentDay && !$bPreviousMonth && !$bNextMonth);
				// make day increment
				$iCurrentDay++;
				if ( $bPreviousMonth && $iCurrentDay > $iPrevDaysInMonth ) {
					$bPreviousMonth = false;
					$iCurrentDay = 1;
				}
				if ( !$bPreviousMonth && !$bNextMonth && $iCurrentDay > $iCurDaysInMonth ) {
					$bNextMonth = true;
					$iCurrentDay = 1;
				}
			}
		}

 		$sAllC = _t('_All');
		$sPrevC = _t('_modzzz_professional_prev');
		$sNextC = _t('_modzzz_professional_next');
		$sSundaySC = _t('_modzzz_professional_sunday_short');
		$sMondaySC = _t('_modzzz_professional_monday_short');
		$sTuesdaySC = _t('_modzzz_professional_tuesday_short');
		$sWednesdaySC = _t('_modzzz_professional_wednesday_short');
		$sThursdaySC = _t('_modzzz_professional_thursday_short');
		$sFridaySC = _t('_modzzz_professional_friday_short');
		$sSaturdaySC = _t('_modzzz_professional_saturday_short');
		$sCalendarC = _t('_modzzz_professional_calendar');
  		$sCurMonYear = _t('_modzzz_professional_'.strtolower($sCurMonthName)) .', '. $iCurYear;

		$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $this->aDataEntry['uri'];
 
		$sCalendarPrevHref = "{$sRedirectUrl}?ajax_reload=1&action=calendar&month={$iPrevMonth}-{$iPrevYear}";
		$sCalendarNextHref = "{$sRedirectUrl}?ajax_reload=1&action=calendar&month={$iNextMonth}-{$iNextYear}";
 
		$sCalTableTrs = '';
		for ($i = 0; $i < 6; $i++) {
			$sCalTableTrs .= '<tr>';
			for ($j = 0; $j < 7; $j++) {
				if ( $aCalendarGrid[$i][$j]['today'] )
					$sCellClass = 'calendar_today';
				elseif ( $aCalendarGrid[$i][$j]['current'] )
					$sCellClass = 'calendar_current';
				else
					$sCellClass = 'calendar_non_current';


				$vDayMonthValue = $aCalendarGrid[$i][$j]['day'] .'-'.  $aCalendarGrid[$i][$j]['month'];
	 
 				$dtChallengeThisDay = mktime(0,0,0,$aCalendarGrid[$i][$j]['month'],$aCalendarGrid[$i][$j]['day'],$iCurYear);
  				$dtChallengeNow = mktime(0,0,0, date("n"),date("j"),date("Y"));

				if ( isset($aCalendar[$vDayMonthValue])) {
			
					$sCalTableTrs .= <<<EOF
<td style="width:30px;height:30px;" class="completed">
EOF;

					$iThisDay = $aCalendarGrid[$i][$j]['day'];
					$iThisMonth = $aCalendarGrid[$i][$j]['month'];
				    
					$sCalTableTrs .= "<center>X</center>"; 

				}elseif(($dtChallengeStart<=$dtChallengeThisDay)&&($dtChallengeEnd>=$dtChallengeThisDay)){
					 
					$iThisDay = $aCalendarGrid[$i][$j]['day'];
					$iThisMonth = $aCalendarGrid[$i][$j]['month'];		
					
					//if date is greater than today, dont make markable
					$sMarkClass = ($dtChallengeThisDay>$dtChallengeNow) ? "" : "markable";
		 
					$sCalTableTrs .= <<<EOF
<td id='challenge_{$iThisDay}_{$iThisMonth}' style="width:30px;height:30px;" class="notCompleted {$sMarkClass}">
EOF;
 			 		$sCalendarMarkHref = "{$sRedirectUrl}?ajax_reload=1&action=calendar&mark=1&day={$iThisDay}&month={$iThisMonth}-{$iCurYear}&id={$iId}";
	  
					if($dtChallengeThisDay>$dtChallengeNow){
						$sCalTableTrs .= $aCalendarGrid[$i][$j]['day']; 				
					}else{	
						$sCalTableTrs .= "<a href='javascript:void(0)' onClick=\"getHtmlData( 'challenge_fullcal_block', '{$sCalendarMarkHref}' );\">{$aCalendarGrid[$i][$j]['day']}</a>"; 				
					}
				}else{
					$sCalTableTrs .= <<<EOF
<td style="width:30px;height:30px;" class="{$sCellClass}">{$aCalendarGrid[$i][$j]['day']}
EOF;

				}

				$sCalTableTrs .= '</td>';
			}
			$sCalTableTrs .= '</tr>';
		}

		$sRetHtml = <<<EOF
 
<div id="blog_buildcal_block" align="center" >
	<table cellpadding="1" cellspacing="1" border="0" width="100%" class="text" style="text-align:center;margin-top:5px;">
		<tr>
			<td class="calendar_current" style="padding: 3px;">
				<a href='javascript:void(0)' onClick="getHtmlData( 'challenge_fullcal_block', '{$sCalendarPrevHref}' );">{$sPrevC}</a>
			</td>
			<td colspan="5" class="calendar_current">{$sCurMonYear}</td>
			<td class="calendar_current" style="padding: 3px;">
				<a href='javascript:void(0)' onClick="getHtmlData( 'challenge_fullcal_block', '{$sCalendarNextHref}' );">{$sNextC}</a>
			</td>
		</tr>
		<tr>
			<td style="width:{$iPicSize}px;" class="calendar_non_current">{$sSundaySC}</td>
			<td style="width:{$iPicSize}px;" class="calendar_non_current">{$sMondaySC}</td>
			<td style="width:{$iPicSize}px;" class="calendar_non_current">{$sTuesdaySC}</td>
			<td style="width:{$iPicSize}px;" class="calendar_non_current">{$sWednesdaySC}</td>
			<td style="width:{$iPicSize}px;" class="calendar_non_current">{$sThursdaySC}</td>
			<td style="width:{$iPicSize}px;" class="calendar_non_current">{$sFridaySC}</td>
			<td style="width:{$iPicSize}px;" class="calendar_non_current">{$sSaturdaySC}</td>
		</tr>
	{$sCalTableTrs}
	</table>
</div>
<br /> 
EOF;


			return $sRetHtml;
	 }
	//end- calendar 

*/

}
