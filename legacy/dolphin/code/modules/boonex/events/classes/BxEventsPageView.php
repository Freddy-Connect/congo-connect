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

class BxEventsPageView extends BxDolTwigPageView {	

    function BxEventsPageView(&$oMain, &$aEvent) {
        parent::BxDolTwigPageView('bx_events_view', $oMain, $aEvent);
	
		$this->_oMain = $oMain;

        $this->sSearchResultClassName = 'BxEventsSearchResult';
        $this->sFilterName = 'bx_events_filter';
		
		$this->sUri = $this->aDataEntry['EntryUri'];
        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/'. $this->aDataEntry['EntryUri'];
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&'); 
	}
  
	function getBlockCode_Info() {
        return  array($this->_oTemplate->blockInfo ($this->aDataEntry));
    }

	function getBlockCode_ParticipantsInfo() {
	  	
	    $isFan = $this->_oDb->isFan((int)$this->aDataEntry['ID'], $this->_oMain->_iProfileId, 1);
 
		if(!($isFan || $this->_oMain->isAdmin()))
			return;
	  	
	    if(!$this->aDataEntry['ParticipantsInfo'])
			return;
	  			
        return $this->_oTemplate->blockParticipantsInfo ($this->aDataEntry);
    }
    
  	function getBlockCode_Desc() {
        return $this->_oTemplate->blockDesc ($this->aDataEntry);
    }  
     
    function getBlockCode_Photos() {
        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['ID'], 'images'), $this->aDataEntry['ResponsibleID']);
    }    

    function getBlockCode_Videos() {
        return $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['ID'], 'videos'), $this->aDataEntry['ResponsibleID']);
    }    

    function getBlockCode_Sounds() {
        return $this->_blockSound ($this->_oDb->getMediaIds($this->aDataEntry['ID'], 'sounds'), $this->aDataEntry['ResponsibleID']);
    }    

    function getBlockCode_Files() {
        return $this->_blockFiles ($this->_oDb->getMediaIds($this->aDataEntry['ID'], 'files'), $this->aDataEntry['ResponsibleID']);
    }    

    function getBlockCode_Rate() {
        bx_events_import('Voting');
        $o = new BxEventsVoting ('bx_events', (int)$this->aDataEntry['ID']);
    	if (!$o->isEnabled()) return '';
        return  array($o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        bx_events_import('Cmts');
        $o = new BxEventsCmts ('bx_events', (int)$this->aDataEntry['ID']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            
 
	function getBlockCode_Forum() {
 
        if ( !$this->_oMain->isAllowedReadForum ($this->aDataEntry, $this->_oMain->_iProfileId) )
			return;

		$iEventId = (int)$this->aDataEntry['ID'];
		$iLimit = (int)getParam('bx_events_perpage_view_subitems');
 
		$aPosts = $this->_oDb->getItemForumPosts($iLimit, $iEventId);
 
		if(empty($aPosts))
			return;

		$aVars['bx_repeat:entries'] = array();
  		foreach($aPosts as $aEachPost){

			$sForumUri = $aEachPost['forum_uri'];
			$sTopic = $aEachPost['topic_title']; 
			$sTopicUri = $aEachPost['topic_uri'];
			$sPostText = $aEachPost['post_text']; 
			$sDate = defineTimeInterval($aEachPost['when']); 
			$sEventName = $aEachPost['Title']; 
 			$sPoster = $aEachPost['user']; 

			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail(getID($sPoster));

			$iLimitChars = (int)getParam('bx_events_forum_max_preview');
			$sPostText = $this->_oMain->_formatSnippetText($aEachPost, $iLimitChars, $sPostText);
 
			$sImage = '';
			if ($aEachPost['thumb']) {
				$a = array ('ID' => $aEachPost['author_id'], 'Avatar' => $aEachPost['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}

			$sEventUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .'view/' . $sForumUri;
			$sTopicUrl = BX_DOL_URL_ROOT . 'forum/events/topic/'.$sForumUri.'.htm';
	
			$aVars['bx_repeat:entries'][] = array( 
							'topic_url' => $sTopicUrl, 
							'topic' => $sTopic, 
							'snippet_text' => $sPostText, 
 
							'bx_if:main' => array( 
								'condition' => false,
								'content' => array(), 
							), 

							'created' => $sDate,
							'author_url' => getProfileLink(getID($sPoster)),
							'author' => $sPoster,
							'thumb_url' => $sMemberThumb,
						);
		}

		$sCode = $this->_oTemplate->parseHtmlByName('block_forum', $aVars);  

		return $sCode;
	}
 
	function getBlockCode_FacebookCommentsOLD() {

		$aVars = array( 
			'item_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'view/' . $this->sUri, 
			'num_posts' => getParam('bx_events_num_fcomments'), 
			'width' => getParam('bx_events_width_fcomments'), 
			'app_id' => getParam('bx_events_fapp_id'),  
		);
 
		return $this->_oTemplate->parseHtmlByName('block_facebook_comments', $aVars);  
 	}

	function getBlockCode_VideoEmbed() {

		$aVideoUrls = $this->_oDb->getYoutubeVideos($this->aDataEntry['ID']);

		$sFirstVideoId = '';
		$sFirstVideoTitle = '';
		$aVideos = array();

		if(empty($aVideoUrls))return;
			 
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
 
	function getBlockCode_Recurring() {  

		if ($this->aDataEntry['Recurring']=='no')
			return;
	
		$aVars = array (
		    'description' => _t('_bx_events_recurring_period_message',$this->aDataEntry['RecurringPeriod']),
		);
		
		$sMessage = $this->_oTemplate->parseHtmlByName('block_recurr', $aVars);	 

		$sMessageBlock = $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sMessage));

		if($this->aDataEntry['Parent']){ 
 
			$aBlock = $this->ajaxBrowse('recurring', 5, array(), $this->aDataEntry['Parent'], $this->aDataEntry['EventEnd'], '', true, true, true); 

			$aBlock[0] = $sMessageBlock . $aBlock[0]; 
			return $aBlock; 
		}else{ 
			return $sMessageBlock;
		}
	}
   
	function getBlockCode_Organizer() {  
 
		if(!$this->aDataEntry['OrganizerName'])
			return; 	 

		$aAllow = array('OrganizerName','OrganizerPhone','OrganizerEmail','OrganizerWebsite');
  
		$sFields = $this->_oTemplate->blockCustomFields($this->aDataEntry, $aAllow); 
		$aVars = array ('fields' => $sFields); 
         
		return array($this->_oTemplate->parseHtmlByName('custom_block_info', $aVars));   
	}
	
	function getBlockCode_Local() {    
		return $this->ajaxBrowse('other_local',  $this->_oDb->getParam('bx_events_perpage_main_recent'),array(),$this->aDataEntry['ID'],$this->aDataEntry['City'],$this->aDataEntry['State']); 
	}

	function getBlockCode_Other() {    
		return $this->ajaxBrowse('other', $this->_oDb->getParam('bx_events_perpage_main_recent'),array(),$this->aDataEntry['ResponsibleID'],$this->aDataEntry['ID']); 
	}

    function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $sValue2 = '', $sValue3 = '', $isDisableRss = true, $isPublicOnly = true, $bShortPaginate=false) {
        $oMain = BxDolModule::getInstance('BxEventsModule');

        bx_import ('SearchResult', $oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue, $sValue2, $sValue3);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);

        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => getTemplateIcon('rss.png'))));

        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);

        if (!($s = $o->displayResultBlock())) 
           // Freddy commentaire
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
		
		if($bShortPaginate)
			$sAjaxPaginate = $oPaginate->getSimplePaginate($this->_oConfig->getBaseUri()  .  'view/' . $this->sUri);
        else
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
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'bx_events', '', (int)$this->aDataEntry['ID']);
            $sCode .= $oSubscription->getData();

            $isFan = $this->_oDb->isFan((int)$this->aDataEntry['ID'], $this->_oMain->_iProfileId, 0) || $this->_oDb->isFan((int)$this->aDataEntry['ID'], $this->_oMain->_iProfileId, 1);

            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['ResponsibleID'],
                'ID' => (int)$this->aDataEntry['ID'],
                'URI' => $this->aDataEntry['EntryUri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'TitleSubscribe' => $aSubscribeButton['title'],                
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_bx_events_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_bx_events_action_title_delete') : '',
                'IconJoin' => $isFan ? 'sign-out' : 'sign-in',
				'TitleJoin' => $this->_oMain->isAllowedJoin($this->aDataEntry) ? ($isFan ? _t('_bx_events_action_title_leave') : _t('_bx_events_action_title_join')) : '',
                'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_bx_events_action_title_invite') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_bx_events_action_title_share') : '',
                'TitleBroadcast' => $this->_oMain->isAllowedBroadcast($this->aDataEntry) ? _t('_bx_events_action_title_broadcast') : '',
                'TitleSponsorAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_bx_events_action_title_add_sponsor') : '',
                'TitleNewsAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_bx_events_action_title_add_news') : '',
	            'TitleVenueAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_bx_events_action_title_add_venue') : '',
			 
			    'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['Featured'] ? _t('_bx_events_action_remove_from_featured') : _t('_bx_events_action_add_to_featured')) : '',
				'TitleExcel' => $this->_oMain->isAllowedExcel($this->aDataEntry) ? _t('_bx_events_action_title_excel') : '',
				'TitlePrint' => $this->_oMain->isAllowedPrint($this->aDataEntry) ? _t('_bx_events_action_title_print') : '',
			 
                'TitleRelist' => $this->_oMain->isAllowedRelist($this->aDataEntry) ? _t('_bx_events_action_title_relist') : '',
                'TitleExtend' => $this->_oMain->isPaidListing($this->aDataEntry['ID']) ? ($this->_oMain->isAllowedExtend($this->aDataEntry) ? _t('_bx_events_action_title_extend') : '') : '',
                'TitlePremium' => $this->_oMain->isAllowedPremium($this->aDataEntry) ? ($this->_oMain->isPaidListing($this->aDataEntry['ID']) ? '' : _t('_bx_events_action_title_premium')) : '',
 
	            'TitleAdminAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_bx_events_action_title_add_admin') : '',
                'TitleManageAdmins' => $this->_oMain->isAllowedManageAdmins($this->aDataEntry) ? _t('_bx_events_action_manage_admins') : '',
  
				'TitleDiscount' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_bx_events_action_title_add_discount') : '',
				'TitlePurchaseFeatured' => '',

			    /*'TitlePurchaseFeatured' => $this->_oMain->isAllowedPurchaseFeatured($this->aDataEntry) ? ($this->aDataEntry['Featured'] ? _t('_bx_events_action_title_extend_featured') : _t('_bx_events_action_title_purchase_featured')) : '',*/
				'TitleManageFans' => $this->_oMain->isAllowedManageFans($this->aDataEntry) ? _t('_bx_events_action_manage_fans') : '',
                'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_bx_events_action_upload_photos') : '',

                'TitleEmbed' => $this->_oMain->isAllowedEmbed($this->aDataEntry) ? _t('_bx_events_action_embed_video') : '',  

                'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_bx_events_action_upload_videos') : '',
                'TitleUploadSounds' => $this->_oMain->isAllowedUploadSounds($this->aDataEntry) ? _t('_bx_events_action_upload_sounds') : '',
                'TitleUploadFiles' => $this->_oMain->isAllowedUploadFiles($this->aDataEntry) ? _t('_bx_events_action_upload_files') : '', 
           
				'TitleBan' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_bx_events_action_title_ban') : '',


                'TitleActivate' => method_exists($this->_oMain, 'isAllowedActivate') && $this->_oMain->isAllowedActivate($this->aDataEntry) ? _t('_bx_events_admin_activate') : ''  
			);

			if(BxDolRequest::serviceExists('wall', 'get_repost_js_click')) {
				$sCode .= BxDolService::call('wall', 'get_repost_js_script');

				$this->aInfo['repostCpt'] = _t('_Repost');
				$this->aInfo['repostScript'] = BxDolService::call('wall', 'get_repost_js_click', array($this->_oMain->_iProfileId, 'bx_events', 'add', (int)$this->aDataEntry['ID']));
			}else {
                $aInfo['repostCpt'] = '';
            }

            $sCodeActions = $oFunctions->genObjectsActions($this->aInfo, 'bx_events');
            if(empty($sCodeActions))
                return '';

            return $sCode . $sCodeActions;
        }

        return '';
    }  

    function getBlockCode_Participants() {
        return parent::_blockFans ($this->_oDb->getParam('bx_events_perpage_participants'), 'isAllowedViewParticipants', 'getFans');
    }        

    function getBlockCode_ParticipantsUnconfirmed() {
        return parent::_blockFansUnconfirmed (BX_EVENTS_MAX_FANS);
    }

    function getBlockCode_News () {
 
        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'view/' . $this->sUri . '?';
   
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $this->ajaxBrowseSubProfile(
            'news',
            'news',
            $this->_oDb->getParam('bx_events_perpage_view_subitems'), 
            array(), $this->sUri, true, false 
        );
 
    }

    function getBlockCode_Venues () {
 
        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'view/' . $this->sUri . '?';
  
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $this->ajaxBrowseSubProfile(
            'venue',
            'venues',
            $this->_oDb->getParam('bx_events_perpage_view_subitems'), 
            array(), $this->sUri, true, false 
        );
 
    }

    function getBlockCode_Sponsors () {
 
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));
 
        return $this->ajaxBrowseSubProfile(
            'sponsor',
            'sponsors',
            $this->_oDb->getParam('bx_events_perpage_view_subitems'), 
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
 
       
	   //Freddy mise en commentaire
	    if (!($s = $o->displaySubProfileResultBlock($sType))) {
            // return array(MsgBox(_t('_Empty')), $aMenu);
			return ;
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
 
	function getBlockCode_CustomRSSOLD() {

		$iEventId = (int)$this->aDataEntry['ID'];
		$aRSS = $this->_oDb->getRss($iEventId);

		if(!count($aRSS))
			  return;
 
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
 
				$aVars['bx_repeat:entries'][] = array ( 
				  'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
				  'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
				  'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
				  'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
				  );
	 
				if($iCounter == (int)getParam('bx_events_perpage_rss_feed')) break;
	 
				$iCounter++;
			 }

			 if($iCounter > 1){
				$aVars['topic'] = $sRSSTopic;

				$sFeeds .= $this->_oTemplate->parseHtmlByName('event_rss', $aVars); 
			 }
		}

	    return $sFeeds; 
	}
 

	function getBlockCode_Location() {
 
		$aAllow = array('Place', 'Street','City','State','Country','Zip');
  
		$sFields = $this->_oTemplate->blockCustomFields($this->aDataEntry, $aAllow); 
		$aVars = array ('fields' => $sFields); 
         
		return array($this->_oTemplate->parseHtmlByName('custom_block_info', $aVars));  

    }
   
    function getBlockCode_Admins() {
        return $this->_blockAdmins (getParam('bx_events_perpage_participants'), 'isAllowedViewParticipants', 'getAdmins');
    }  
  
    function _blockAdmins($iPerPage, $sFuncIsAllowed = 'isAllowedViewParticipants', $sFuncGetFans = 'getAdmins') {

        if (!$this->_oMain->$sFuncIsAllowed($this->aDataEntry)) 
            return '';
      
        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->$sFuncGetFans($aProfiles, $this->aDataEntry[$this->_oDb->_sFieldId], $iStart, $iPerPage);
        if (!$iNum || !$aProfiles)
           //Freddy commentaire
		   // return MsgBox(_t("_Empty"));
		    return ;
        $iPages = ceil($iNum / $iPerPage);
 
        bx_import('BxTemplSearchProfile');
        $oBxTemplSearchProfile = new BxTemplSearchProfile();
        $sMainContent = '';
        foreach ($aProfiles as $aProfile) {
            $sMainContent .= $oBxTemplSearchProfile->displaySearchUnit($aProfile, array ('ext_css_class' => 'bx-def-margin-sec-top-auto'));
        }
        $ret .= $sMainContent;
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
/*
        $aDBBottomMenu = array();
        if ($iPages > 1) {
            $sUrlStart = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . "view/".$this->aDataEntry[$this->_oDb->_sFieldUri];
            $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');            
            if ($iPage > 1)
                $aDBBottomMenu[_t('_Back')] = array('href' => $sUrlStart . "page=" . ($iPage - 1), 'dynamic' => true, 'class' => 'backFans', 'icon' => getTemplateIcon('sys_back.png'), 'icon_class' => 'left', 'static' => false);
            if ($iPage < $iPages) {                                
                $aDBBottomMenu[_t('_Next')] = array('href' => $sUrlStart . "page=" . ($iPage + 1), 'dynamic' => true, 'class' => 'moreFans', 'icon' => getTemplateIcon('sys_next.png'), 'static' => false);
            }
        }
 
		$ret .= '<div class="clear_both"></div>';

		return array($ret, array(), $aDBBottomMenu);
    }    
 */

	function getBlockCode_Discounts() {    
 
        $aDiscounts = $this->_oDb->getAllDiscounts($this->aDataEntry['ID']);

		$sOutput = $this->_oTemplate->parseHtmlByName('discount_unit_head', array());
		foreach($aDiscounts as $aEachDiscount){
			$sOutput .= $this->_oTemplate->discount_unit($aEachDiscount, $this->aDataEntry['EventStart']);
		}
        
		return count($aDiscounts) ? array($sOutput) : '';
	}

	function getBlockCode_Logo() {  
		
		if(!$sIcon = $this->aDataEntry['icon']) return;
			 
		$aVars = array (
		    'image' => $this->_oDb->getLogo($this->aDataEntry['ID'], $sIcon),
			'bx_if:remove' => array( 
				'condition' => $this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry),
				'content' => array( 
					'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'logo/remove/' . $this->aDataEntry['ID'],   
				), 
			) 
 		);
		return $this->_oTemplate->parseHtmlByName('block_logo', $aVars);	 
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
                'value' => _t('_bx_events_btn_members_unban'),
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
 
    function getCode() {

        $this->_oMain->_processFansActions ($this->aDataEntry, BX_EVENTS_MAX_FANS);

        return parent::getCode();
    }  
	
	function getBlockCode_EventRSS() {
   		
		$sUrl = BX_DOL_URL_ROOT . 'rss_factory.php?action=events&pid='.$this->aDataEntry[$this->_oDb->_sFieldAuthorId]; 
    
		$aVars = array( 
			'url' => $sUrl, 
  		);
 
		$sCode = $this->_oTemplate->parseHtmlByName('block_event_rss', $aVars);  

		return array($sCode);
	}
	
	
	/////////////////////////////////////////////////////////////////////////
	
	function getBlockCode_TitreEvenement() {
	 if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxEventModule');
    
		 
		 $aData = $this->aDataEntry;
		 
	 
	
  
		$aVars = array(  
		
		       'title' => $aData['Title'],
			  'date' => getLocaleDate($aData['Date'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['Date']),
			  'start_date' => getLocaleDate($aData['EventStart'], BX_DOL_LOCALE_DATE),
            'end_date' => getLocaleDate($aData['EventEnd'], BX_DOL_LOCALE_DATE),
			
			 'place' => $aData['Place'],

			'bx_if:street' => array (
				'condition' => $aData['Street'],
				'content' => array (
					'street' => $aData['Street'],
				),
			),	

			'bx_if:zipcode' => array (
				'condition' => $aData['Zip'],
				'content' => array (
					'zip' => $aData['Zip'],
				),
			),			
			  
            'country' => _t($GLOBALS['aPreValues']['Country'][$aData['Country']]['LKey']), 
			'city' => $aData['City'],
			 'drapeau' =>genFlag($aData['Country']),
			// 'country_city' => $this->_oMain->_formatEventLocation($aData, true, true, true),

  		);
		
  
		return $this->_oTemplate->parseHtmlByName('block_titre', $aVars);   
	
	
	
	}
	
	
	///////////////////////////////////////////////////////////////////
 function getBlockCode_ManageMyEvent() {
		
    
		if(!$this->_oMain->isAllowedEdit($this->aDataEntry)) return; 
		
		
		//$add_staff_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'staff/add/' . $this->aDataEntry['id']; 
		
		$add_photos_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'upload_photos/' . $this->aDataEntry['EntryUri']; 
		$add_files_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'upload_files/' . $this->aDataEntry['EntryUri']; 
		$embed_videos_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'embed/' . $this->aDataEntry['EntryUri']; 
		
		$modifier_edit_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'edit/' . $this->aDataEntry['ID'];
		//$add_listing_cover_url = BX_DOL_URL_ROOT . 'modules/?r=' . 'listingcover/' . 'cover/' . 'add/' . $this->aDataEntry['id']; 
		//$add_listing_enventr_url = BX_DOL_URL_ROOT . 'm/' . 'events/' . 'browse/' . 'my&bx_events_filter=' . 'add_event&listing=' . $this->aDataEntry['id'];
		$broadcast_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'broadcast/' . $this->aDataEntry['ID']; 

		$InviteUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'invite/' . $this->aDataEntry['ID'];
		$supprimer_edit_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'delete/' . $this->aDataEntry['ID'];
		$aVars = array(  
			
			   'add_photos_url' => $add_photos_url, 
			   'embed_videos' => $embed_videos_url, 
			   'add_files_url' => $add_files_url, 
			   'modifier_edit_url' =>  $modifier_edit_url, 
			  // 'add_listing_cover_url' =>  $add_listing_cover_url, 
			  // 'add_listing_enventr_url' =>  $add_listing_enventr_url, 
			    'broadcast' =>  $broadcast_url, 
				  
			// 'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_modzzz_listing_action_title_promote') : '',  
			  'InviteUrl' => $InviteUrl,
			 
			    'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_bx_events_action_title_delete') : '',
			   'supprimer_edit_url' =>  $supprimer_edit_url, 
			  'id' =>$this->aDataEntry['ID'],
  		);
		
		
		return $this->_oTemplate->parseHtmlByName('block_manage_event', $aVars);   
	}
	/******************FIN FREDDY MANAGE classified****************/
	
	
	////////////////////////////////////////////////////////////////////////


 function getBlockCode_ManagePrintExcel() {
		
    
		if(!$this->_oMain->isAllowedEdit($this->aDataEntry)) return; 
		
		
		$aVars = array(  
			
			  'TitleExcel' => $this->_oMain->isAllowedExcel($this->aDataEntry) ? _t('_bx_events_action_title_excel') : '',
				'TitlePrint' => $this->_oMain->isAllowedPrint($this->aDataEntry) ? _t('_bx_events_action_title_print') : '',
				
				'UrlTitlePrint' =>  BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'print/' . $this->aDataEntry['ID'],
				 'UrlTitleExcel' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'excel/' . $this->aDataEntry['ID'],
				
  		);
		
		
		return $this->_oTemplate->parseHtmlByName('block_manage_print_excel', $aVars);   
	}
	/******************FIN FREDDY MANAGE classified****************/

function getBlockCode_ManagePageUser() {
	 if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxEventsModule');
    
		 
		 $aData = $this->aDataEntry;
				 
	 
	  global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

           
			
			$isFan = $this->_oDb->isFan((int)$aData['ID'], $this->_oMain->_iProfileId, 0) || $this->_oDb->isFan((int)$aData['ID'], $this->_oMain->_iProfileId, 1);
	 
		
	
	$showPopupAnyHtml = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'share_popup/' . $aData['ID'];
	
	$Join_Suivre= BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'join/'. $aData['ID'] . '/$this->_oMain->_iProfileId';
	
    
		$aVars = array(  
		
	
			   'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_bx_events_action_title_share') : '',
			   'SharePopup' => $showPopupAnyHtml,
			   
			   
			    //'iViewer' => $this->_oMain->_iProfileId,
			   'TitleJoin' => $this->_oMain->isAllowedJoin($aData) ? ($isFan ? _t('_bx_events_action_title_leave') : _t('_bx_events_action_title_join')) : '',
			   
			   'Join_Suivre' => $Join_Suivre,
			 'id' => $aData['ID'],
  		);
		
  
		return $this->_oTemplate->parseHtmlByName('block_manage_page', $aVars);   
	}
	
	}
	
}
