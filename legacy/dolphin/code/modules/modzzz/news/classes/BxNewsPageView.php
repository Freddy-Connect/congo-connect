<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx News
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
  
class BxNewsPageView extends BxDolTwigPageView {	

	function BxNewsPageView(&$oMain, &$aDataEntry) {
		parent::BxDolTwigPageView('modzzz_news_view', $oMain, $aDataEntry);

        $this->sSearchResultClassName = 'BxNewsSearchResult';
        $this->sFilterName = 'filter';

        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/'. $this->aDataEntry['uri'];
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  
	}
		
	function getBlockCode_Info() {
        return array($this->_blockInfo ($this->aDataEntry, $this->_oTemplate->blockFields($this->aDataEntry)));
    }

	//override the similar mod in the parent class
    function _blockInfo ($aData, $sFields = '') {

        $aAuthor = getProfileInfo($aData['author_id']);
 
		$sAuthorName =  $aAuthor['NickName'];
		$sAuthorLink = getProfileLink($aAuthor['ID']);	
		$icoThumb = get_member_thumbnail($aAuthor['ID'], 'none', true);
	 
		$sAllUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/owner/' . $aAuthor['NickName'];

 		$aCategory = $this->_oDb->getCategoryInfo($aData['parent_category_id']);
		$sCategoryUrl = $this->_oDb->getCategoryUrl($aCategory['uri']); 
  
		if($aData['category_id']){
			$aSubCategory = $this->_oDb->getCategoryInfo($aData['category_id']); 
			$sSubCategoryUrl = $this->_oDb->getSubCategoryUrl($aSubCategory['uri']);
 		}
 
        $aVars = array (
            'author_unit' => $icoThumb,
            'date' => getLocaleDate($aData['when'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['when']),
            
			'category_name' => $aCategory['name'], 
			'category_url' => $sCategoryUrl,
             
			'bx_if:subcategory' => array( 
				'condition' => $aData['category_id'],
				'content' => array(
					'subcategory_name' => $aSubCategory['name'], 
					'subcategory_url' => $sSubCategoryUrl,
				) 
			), 


			'bx_if:location' => array( 
				'condition' => $aData['country'],
				'content' => array(
					'location' => $this->_oTemplate->_formatLocation($aData, true, true),
				) 
			),

			'tags' => $this->_oTemplate->parseTags($aData['tags']),
            'fields' => $sFields, 
            'views' => $aData['views'], 
			'subscribers' => $this->_oDb->getSubscribeCount($aData['id']), 
            'other_items' => _t('_modzzz_news_all_items_posted', $sAllUrl, getNickName($aAuthor['ID'])),

        );
        return $this->_oTemplate->parseHtmlByName('entry_view_block_info', $aVars);
    }
  
	function getBlockCode_Desc() {
        return array($this->_oTemplate->blockDesc ($this->aDataEntry));
    }

	function getBlockCode_Photo() {
        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']); 
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
        modzzz_news_import('Voting');
        $o = new BxNewsVoting ('modzzz_news', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_news_import('Cmts');
        $o = new BxNewsCmts ('modzzz_news', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

 			$sCode = '';

            $oSubscription = BxDolSubscription::getInstance();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'modzzz_news', '', (int)$this->aDataEntry['id']);
            $sCode .= $oSubscription->getData();

            $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'TitleSubscribe' => $aSubscribeButton['title'], 
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_modzzz_news_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_news_action_title_delete') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_news_action_title_share') : '',
                'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_news_action_remove_from_featured') : _t('_modzzz_news_action_add_to_featured')) : '',
                'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_modzzz_news_action_upload_photos') : '',
                'TitleEmbed' => $this->_oMain->isAllowedEmbed($this->aDataEntry) ? _t('_modzzz_news_action_embed_video') : '',  
				'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_modzzz_news_action_upload_videos') : '',
                'TitleUploadSounds' => $this->_oMain->isAllowedUploadSounds($this->aDataEntry) ? _t('_modzzz_news_action_upload_sounds') : '',
                'TitleUploadFiles' => $this->_oMain->isAllowedUploadFiles($this->aDataEntry) ? _t('_modzzz_news_action_upload_files') : '',
           
                'TitleActivate' => method_exists($this->_oMain, 'isAllowedActivate') && $this->_oMain->isAllowedActivate($this->aDataEntry) ? _t('_modzzz_news_admin_activate') : ''   
			
			);

			if(BxDolRequest::serviceExists('wall', 'get_repost_js_click')) {
				$sCode .= BxDolService::call('wall', 'get_repost_js_script');

				$aInfo['repostCpt'] = _t('_Repost');
				$aInfo['repostScript'] = BxDolService::call('wall', 'get_repost_js_click', array($this->_oMain->_iProfileId, 'modzzz_news', 'add', (int)$this->aDataEntry['id']));
			}

			$sCodeActions = $oFunctions->genObjectsActions($aInfo, 'modzzz_news');
			if(empty($sCodeActions))
                return '';

            return $sCode . $sCodeActions;
        }

        return '';
    }       
 
	 function getBlockCode_CustomRSS() {

		$iNewsId = (int)$this->aDataEntry['id'];
		$aRSS = $this->_oDb->getRss($iNewsId);

		if(empty($aRSS))
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
	 
				if($iCounter == (int)getParam('modzzz_news_perpage_rss_feed')) break;
	 
				$iCounter++;
			 }

			 if($iCounter > 1){
				$aVars['topic'] = $sRSSTopic;

				$sFeeds .= $this->_oTemplate->parseHtmlByName('rss_block', $aVars); 
			 }
		}

	    return $sFeeds; 
	}


	function getBlockCode_Category() {
 
		$sValue = $this->aDataEntry['id'];
		$sValue2 = $this->aDataEntry['category_id'];
		$sValue3 = $this->aDataEntry['title'];

        return $this->ajaxBrowse('rel_category', $this->_oDb->getParam('modzzz_news_perpage_related'), array(), $sValue, $sValue2, $sValue3, true, false, false);
    }
 
	function getBlockCode_Related() {    

		$sValue = $this->aDataEntry['tags'];
		$sValue2 = $this->aDataEntry['id'];
		$sValue3 = $this->aDataEntry['title'];
		
		return $this->ajaxBrowse('related', getParam('modzzz_news_perpage_related'),array(), $sValue, $sValue2, $sValue3, true, false, false); 
	}
  
    function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $sValue2 = '', $sValue3 = '', $isDisableRss = false, $isPublicOnly = true, $bShowPagination = true) {

        $oMain = BxDolModule::getInstance('BxNewsModule');


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
            return $isPublicOnly ? array(MsgBox(_t('_Empty')), $aMenu) : '';


        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));

		if($bShowPagination)
			$sAjaxPaginate = $oPaginate->getSimplePaginate($this->_oConfig->getBaseUri() . $o->sBrowseUrl);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    }    
 
 	function getBlockCode_Tags() {

		if(!trim($this->aDataEntry['tags'])) return;

        $aVars = array (
            'tags' => $this->_oTemplate->parseTags($this->aDataEntry['tags'], 'topic-link topic-hover'),
        );

        return array($this->_oTemplate->parseHtmlByName('entry_view_tags', $aVars));
	}

 	function getBlockCode_Location() {

		if(!$this->aDataEntry['country']) return;

		if(!($this->aDataEntry['state'] || $this->aDataEntry['city'] || $this->aDataEntry['zip'])) return;

		return BxDolService::call('wmap', 'location_block', array('news', $this->aDataEntry[$this->_oDb->_sFieldId])); 
	}

    function getCode() { 
        return parent::getCode();
    }

}
