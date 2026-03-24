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

class BxGroupsPageView extends BxDolTwigPageView {	

	function BxGroupsPageView(&$oMain, &$aDataEntry) {
		parent::BxDolTwigPageView('bx_groups_view', $oMain, $aDataEntry);
	 
        $this->sSearchResultClassName = 'BxGroupsSearchResult';
        $this->sFilterName = 'bx_groups_filter';

        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/'. $this->aDataEntry['uri'];
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&'); 

		$this->sUri = $this->aDataEntry['uri'];
	}
   
    function getBlockCode_Info()
    {
		$sFields = '';//$this->_oTemplate->blockFields($this->aDataEntry);

        return array($this->_blockInfo ($this->aDataEntry, $sFields, $this->_oMain->_formatLocation($this->aDataEntry, false, true)));
    }

	function getBlockCode_Desc() {
        return array($this->_oTemplate->blockDesc ($this->aDataEntry));
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
        bx_groups_import('Voting');
        $o = new BxGroupsVoting ('bx_groups', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {  
	
		if(!$this->_oMain->isAllowedViewComments($this->aDataEntry)) return;
 
        bx_groups_import('Cmts');
        $o = new BxGroupsCmts ('bx_groups', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
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
		 
        return array($this->_oTemplate->parseHtmlByName('block_youtube_videos', $aVars));   
    }
  
	function getBlockCode_Local() {    
		return $this->ajaxBrowse('other_local', $this->_oDb->getParam('bx_groups_perpage_main_recent'),array(),$this->aDataEntry['id'],$this->aDataEntry['city'],$this->aDataEntry['state']); 
	}

	function getBlockCode_Other() {    
		return $this->ajaxBrowse('other', $this->_oDb->getParam('bx_groups_perpage_main_recent'),array(),$this->aDataEntry['author_id'],$this->aDataEntry['id']); 
	}

    function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $sValue2 = '', $isDisableRss = false, $isPublicOnly = true) {
        $oMain = BxDolModule::getInstance('BxGroupsModule');

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
  
    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
 
 			$sCode = '';

            $oSubscription = BxDolSubscription::getInstance();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'bx_groups', '', (int)$this->aDataEntry['id']);
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
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_bx_groups_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_bx_groups_action_title_delete') : '',
                'TitleJoin' => $this->_oMain->isAllowedJoin($this->aDataEntry) ? ($isFan ? _t('_bx_groups_action_title_leave') : _t('_bx_groups_action_title_join')) : '',
                'IconJoin' => $isFan ? 'sign-out' : 'sign-in', 
			    'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_bx_groups_action_title_invite') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_bx_groups_action_title_share') : '',
                'TitleBroadcast' => $this->_oMain->isAllowedBroadcast($this->aDataEntry) ? _t('_bx_groups_action_title_broadcast') : '',
               
                'TitleSponsorAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_bx_groups_action_title_add_sponsor') : '',
               
	            'TitleVenueAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_bx_groups_action_title_add_venue') : '',
                
				'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_bx_groups_action_remove_from_featured') : _t('_bx_groups_action_add_to_featured')) : '',
  
				'TitleEventAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_bx_groups_action_title_add_event') : '',
				'TitleNewsAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_bx_groups_action_title_add_news') : '',
				'TitleBlogAdd' => ($isConfirmedFan || $this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_bx_groups_action_title_add_blog') : '',
 
				'TitleBan' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_bx_groups_action_title_ban') : '',

	            'TitleFanAdd' => $this->_oMain->isAllowedAdminAddFan($this->aDataEntry) ? _t('_bx_groups_action_title_add_fan') : '',
	            'TitleAdminAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_bx_groups_action_title_add_admin') : '',
                'TitleManageAdmins' => $this->_oMain->isAllowedManageAdmins($this->aDataEntry) ? _t('_bx_groups_action_manage_admins') : '',

				'TitlePurchaseFeatured' => $this->_oMain->isAllowedPurchaseFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_bx_groups_action_title_extend_featured') : _t('_bx_groups_action_title_purchase_featured')) : '',
 
                'TitleManageFans' => $this->_oMain->isAllowedManageFans($this->aDataEntry) ? _t('_bx_groups_action_manage_fans') : '',
                'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_bx_groups_action_upload_photos') : '',
                'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_bx_groups_action_upload_videos') : '',
                'TitleEmbed' => $this->_oMain->isAllowedEmbed($this->aDataEntry) ? _t('_bx_groups_action_embed_video') : '',  
                'TitleUploadSounds' => $this->_oMain->isAllowedUploadSounds($this->aDataEntry) ? _t('_bx_groups_action_upload_sounds') : '',
                'TitleUploadFiles' => $this->_oMain->isAllowedUploadFiles($this->aDataEntry) ? _t('_bx_groups_action_upload_files') : '',
                'TitleActivate' => method_exists($this->_oMain, 'isAllowedActivate') && $this->_oMain->isAllowedActivate($this->aDataEntry) ? _t('_bx_groups_admin_activate') : '',
  
            );

	        if(BxDolRequest::serviceExists('wall', 'get_repost_js_click')) {
				$sCode .= BxDolService::call('wall', 'get_repost_js_script');

				$aInfo['repostCpt'] = _t('_Repost');
				$aInfo['repostScript'] = BxDolService::call('wall', 'get_repost_js_click', array($this->_oMain->_iProfileId, 'bx_groups', 'add', (int)$this->aDataEntry['id']));
			}else {
                $aInfo['repostCpt'] = '';
            }

			$sCodeActions = $oFunctions->genObjectsActions($aInfo, 'bx_groups');
			if(empty($sCodeActions))
                return '';

            return $sCode . $sCodeActions;
        }

        return '';
    }  

    function getBlockCode_Fans() {
        return parent::_blockFans ($this->_oDb->getParam('bx_groups_perpage_view_fans'), 'isAllowedViewFans', 'getFans');
    }            

    function getBlockCode_FansUnconfirmed() {
        return parent::_blockFansUnconfirmed (BX_GROUPS_MAX_FANS);
    }
  
    function getBlockCode_Banned()
    { 
        if ( !$this->_oMain->isAdmin() && !$this->_oMain->isEntryAdmin($this->aDataEntry) )
            return '';

        $aProfiles = array ();
        $iNum = $this->_oDb->getBanned($aProfiles, $this->aDataEntry[$this->_oDb->_sFieldId]);
        if (!$iNum)
            return MsgBox(_t('_Empty'));

        $sActionsUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . "view/" . $this->aDataEntry[$this->_oDb->_sFieldUri] . '?ajax_action=';
        $aButtons = array (
            array (
                'type' => 'submit',
                'name' => 'members_unban',
                'value' => _t('_bx_groups_btn_members_unban'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_banned_content', '{$sActionsUrl}unban&ids=' + sys_manage_items_get_banned_ids(), false, 'post'); return false;\"",
            ), 
        );
        bx_import ('BxTemplSearchResult');
        $sControl = BxTemplSearchResult::showAdminActionsPanel('sys_manage_items_banned', $aButtons, 'sys_fan_unit');
        $aVars = array(
            'suffix' => 'banned',
            'content' => $this->_oMain->_profilesEdit($aProfiles),
            'control' => $sControl,
        );
        return $this->_oMain->_oTemplate->parseHtmlByName('manage_items_form', $aVars);
    }
 
	function getBlockCode_Forum() {
  		
        if ( !$this->_oMain->isAllowedReadForum ($this->aDataEntry, $this->_oMain->_iProfileId) )
			return;
 
		$iEntryId = (int)$this->aDataEntry['id'];
		$iLimit = (int)getParam('bx_groups_perpage_view_subitems');
 
		$aPosts = $this->_oDb->getItemForumPosts($iLimit, $iEntryId);

		if(empty($aPosts))return;
			 
		$sCode = '';
  		foreach($aPosts as $aEachPost){
			$sCode .= $this->_oTemplate->forum_unit($aEachPost);
		}
		$sCode .= '<div class="clear_both"></div>';

		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sCode));
	}
 
   function getBlockCode_News () {
 
        $this->_oTemplate->addCss('unit.css');

        return $this->ajaxBrowseSubProfile(
            'news',
            'news',
            $this->_oDb->getParam('bx_groups_perpage_view_subitems'), 
            array(), $this->sUri, true, false 
        ); 
    }

    function getBlockCode_Venues () {
 
        $this->_oTemplate->addCss('unit.css');

        return $this->ajaxBrowseSubProfile(
            'venue',
            'venues',
            $this->_oDb->getParam('bx_groups_perpage_view_subitems'), 
            array(), $this->sUri, true, false 
        ); 
    }

    function getBlockCode_Blogs () {
  
        $this->_oTemplate->addCss('unit.css');

        return $this->ajaxBrowseSubProfile(
            'blog',
            'blogs',
            $this->_oDb->getParam('bx_groups_perpage_view_subitems'), 
            array(), $this->sUri, true, false 
        ); 
    }
 
    function getBlockCode_Sponsors () {
 
        $this->_oTemplate->addCss('unit.css');

        return $this->ajaxBrowseSubProfile(
            'sponsor',
            'sponsors',
            $this->_oDb->getParam('bx_groups_perpage_view_subitems'), 
            array(), $this->sUri, true, false 
        );
 
    }

    function getBlockCode_Events () {
  
        $this->_oTemplate->addCss('unit.css');

        return $this->ajaxBrowseSubProfile(
            'event',
            'events',
            $this->_oDb->getParam('bx_groups_perpage_view_subitems'), 
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
 
	 function getBlockCode_CustomRSS() {

		$iGroupId = (int)$this->aDataEntry['id'];
		$aRSS = $this->_oDb->getRss($iGroupId);

		if(empty($aRSS)) return;
			   
		$sFeeds = '';
		$aVars = array();
		foreach($aRSS as $aEachRSS){

			  $sRSSTopic = trim($aEachRSS['name']);
			  $sRSSLink = trim($aEachRSS['url']);
 
			  $iCounter = 1;
			  $aVars['bx_repeat:entries'] = array();

			  $doc = new DOMDocument();
			  @$doc->load($sRSSLink);

			  foreach ($doc->getElementsByTagName('item') as $node) {

			    $sDate = $node->getElementsByTagName('pubDate')->item(0)->nodeValue;

				$aVars['bx_repeat:entries'][] = array ( 
				  'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
				  'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
				  'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,

				  'bx_if:date' => array (
						'condition' => $sDate,
						'content' => array (
							'date' => $sDate
						 ),
				   ), 
				);
	 
				if($iCounter == (int)getParam('bx_groups_perpage_rss_feed')) break;
	 
				$iCounter++;
			 }

			 if($iCounter > 1){
				$aVars['topic'] = $sRSSTopic;

				$sFeeds .= $this->_oTemplate->parseHtmlByName('group_rss', $aVars); 
			 }
		}

	    return $sFeeds; 
	}
 
	function getBlockCode_Location() {
        $aDataEntry = $this->aDataEntry;
  
		$aAllow = array('city', 'state', 'zip', 'country'); 
  
		$sFields = $this->_oTemplate->blockCustomFields($aDataEntry, $aAllow);

		if(!$sFields) return;

		$aVars = array ( 
            'fields' => $sFields, 
        );

        return array($this->_oTemplate->parseHtmlByName('custom_block_info', $aVars));   
    }

    function getBlockCode_Admins() {
        return $this->_blockAdmins (getParam('bx_groups_perpage_view_fans'), 'isAllowedViewFans', 'getAdmins');
    }  
  
    function _blockAdmins($iPerPage, $sFuncIsAllowed = 'isAllowedViewFans', $sFuncGetFans = 'getAdmins') {

        if (!$this->_oMain->$sFuncIsAllowed($this->aDataEntry)) 
            return '';
      
        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->$sFuncGetFans($aProfiles, $this->aDataEntry[$this->_oDb->_sFieldId], $iStart, $iPerPage);
        if (!$iNum || !$aProfiles)
            return MsgBox(_t("_Empty"));
        $iPages = ceil($iNum / $iPerPage);
 
        bx_import('BxTemplSearchProfile');
        $oBxTemplSearchProfile = new BxTemplSearchProfile();
        $ret = '';
        foreach ($aProfiles as $aProfile) {
            $ret .= $oBxTemplSearchProfile->displaySearchUnit($aProfile, array ('ext_css_class' => 'bx-def-margin-sec-top-auto'));
        } 
        $ret .= '<div class="clear_both"></div>';

        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $iNum,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . bx_append_url_params(BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . "view/" . $this->aDataEntry[$this->_oDb->_sFieldUri], 'page={page}&per_page={per_page}') . '\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate('', -1, -1, false);

        return array($ret, array(), $sAjaxPaginate);
    }
 
    function getCode() {

        $this->_oMain->_processFansActions ($this->aDataEntry, BX_GROUPS_MAX_FANS);

        return parent::getCode();
    }

	function getBlockCode_Logo() {  
		
		if(!$sIcon = $this->aDataEntry['icon']) return;
			 
		$aVars = array (
		    'image' => $this->_oDb->getLogo($this->aDataEntry['id'], $sIcon),
			'bx_if:remove' => array( 
				'condition' => $this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry),
				'content' => array( 
					'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'logo/remove/' . $this->aDataEntry['id'],   
				), 
			) 
 		);
		return $this->_oTemplate->parseHtmlByName('block_logo', $aVars);	 
	}

	function getBlockCode_Survey() {

		if(getParam("bx_groups_survey_active")!='on')  
			return;

		$oModule = BxDolModule::getInstance('BxSurveyModule');
 
        $oModule->_oTemplate->addCss(array('unit.css','twig.css'));

        return $this->ajaxBrowseSubProfile(
            'survey',
            'survey',
            $this->_oDb->getParam('bx_groups_perpage_view_subitems'), 
            array(), $this->aDataEntry['uri'], true, false, false 
        );  
	}

 

}
