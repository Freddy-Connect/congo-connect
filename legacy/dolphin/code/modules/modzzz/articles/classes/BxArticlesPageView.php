<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Article
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

class BxArticlesPageView extends BxDolTwigPageView {	

	function BxArticlesPageView(&$oMain, &$aDataEntry) {
		parent::BxDolTwigPageView('modzzz_articles_view', $oMain, $aDataEntry);
 
        $this->sSearchResultClassName = 'BxArticlesSearchResult';  
	}

	function getBlockCode_Website() {

		if(!trim($this->aDataEntry['website'])) return;

		$aVars = array(
			'website' => $this->aDataEntry['website'],
		);
		 
        return $this->_oTemplate->parseHtmlByName('block_website', $aVars);   
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

	function getBlockCode_Info() {
        return array($this->_blockInfo ($this->aDataEntry, $this->_oTemplate->blockFields($this->aDataEntry)));
    }
 

	//override the similar mod in the parent class
    function _blockInfo ($aData, $sFields = '') {

        $aAuthor = getProfileInfo($aData['author_id']);
 
		if($aData['anonymous']) 
		{
			$sAuthorName = '';
			$sAuthorLink = '';	

			$aVars = array ();
			$icoThumb = $this->_oTemplate->parseHtmlByName('anonymous', $aVars);  
		}else{
			$sAuthorName =  $aAuthor['NickName'];
			$sAuthorLink = getProfileLink($aAuthor['ID']);	
			$icoThumb = get_member_thumbnail($aAuthor['ID'], 'none', true);
		}


		if($aData['category_id']){
			$aSubCategory = $this->_oDb->getCategoryInfo($aData['category_id']); 
			$sSubCategoryUrl = $this->_oDb->getSubCategoryUrl($aSubCategory['uri']);
 		}
		
		$aCategory = $this->_oDb->getCategoryInfo($aData['parent_category_id']);
		$sCategoryUrl = $this->_oDb->getCategoryUrl($aCategory['uri']); 

		$sAllUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/owner/' . $aAuthor['NickName'];

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
 
			'bx_if:show_owner' => array( 
				'condition' => (!$aData['anonymous']),
				'content' => array(
					'other_items' => _t('_modzzz_articles_all_items_posted', $sAllUrl, getNickName($aAuthor['ID'])),
				) 
			), 


            'fields' => '',
            'views' => $aData['views'], 
			'subscribers' => $this->_oDb->getSubscribeCount($aData['id']),

        );
        return $this->_oTemplate->parseHtmlByName('entry_view_block_info', $aVars);
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
        modzzz_articles_import('Voting');
        $o = new BxArticlesVoting ('modzzz_articles', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_articles_import('Cmts');
        $o = new BxArticlesCmts ('modzzz_articles', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

 			$sCode = '';

            $oSubscription = BxDolSubscription::getInstance();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'modzzz_articles', '', (int)$this->aDataEntry['id']);
            $sCode .= $oSubscription->getData();
 
            $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'TitleSubscribe' => $aSubscribeButton['title'], 
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_modzzz_articles_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_articles_action_title_delete') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_articles_action_title_share') : '',

				'AddToFavorite' => $this->_oMain->isAllowedMarkAsFavorite($this->aDataEntry) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $this->aDataEntry['id']) ? _t('_modzzz_articles_action_remove_from_favorite') : _t('_modzzz_articles_action_add_to_favorite')) : '',

                'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_articles_action_remove_from_featured') : _t('_modzzz_articles_action_add_to_featured')) : '',
                'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_modzzz_articles_action_upload_photos') : '',
                'TitleEmbed' => $this->_oMain->isAllowedEmbed($this->aDataEntry) ? _t('_modzzz_articles_action_embed_video') : '', 
				'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_modzzz_articles_action_upload_videos') : '',
                'TitleUploadSounds' => $this->_oMain->isAllowedUploadSounds($this->aDataEntry) ? _t('_modzzz_articles_action_upload_sounds') : '',
                'TitleUploadFiles' => $this->_oMain->isAllowedUploadFiles($this->aDataEntry) ? _t('_modzzz_articles_action_upload_files') : '',
           
                'TitleActivate' => method_exists($this->_oMain, 'isAllowedActivate') && $this->_oMain->isAllowedActivate($this->aDataEntry) ? _t('_modzzz_articles_admin_activate') : ''   
			);

	        if(BxDolRequest::serviceExists('wall', 'get_repost_js_click')) {
				$sCode .= BxDolService::call('wall', 'get_repost_js_script');

				$aInfo['repostCpt'] = _t('_Repost');
				$aInfo['repostScript'] = BxDolService::call('wall', 'get_repost_js_click', array($this->_oMain->_iProfileId, 'modzzz_articles', 'add', (int)$this->aDataEntry['id']));
			}else{
				$aInfo['repostCpt'] = '';
				$aInfo['repostScript'] = '';
			}

			$sCodeActions = $oFunctions->genObjectsActions($aInfo, 'modzzz_articles');
			if(empty($sCodeActions))
                return '';

            return $sCode . $sCodeActions;
        }

        return '';
    }    
 
	function getBlockCode_Related() {    
 		$isPublicOnly = ($this->_oMain->_iProfileId) ? false : true;

		$sTags = $this->aDataEntry['tags'];
	 
		return $this->ajaxBrowse('related', getParam('modzzz_articles_perpage_related'),array(),$sTags,$this->aDataEntry['id'],$this->aDataEntry['title'], true, $isPublicOnly); 
	}

    function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $sValue2 = '', $sValue3 = '', $isDisableRss = false, $isPublicOnly = true) {
        $oMain = BxDolModule::getInstance('BxArticlesModule');
 
        bx_import ('SearchResult', $oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue, $sValue2, $sValue3);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);

		if(!$isPublicOnly){ 
			$this->aCurrent['restriction']['public']['value'] = array(BX_DOL_PG_ALL,BX_DOL_PG_MEMBERS);
			$this->aCurrent['restriction']['public']['operator'] = 'in';
		}

        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => getTemplateIcon('rss.png'))));

        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);

        if (!($s = $o->displayResultBlock())) 
            return '';
            
		//return $isPublicOnly ? array(MsgBox(_t('_Empty')), $aMenu) : '';


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
            '',
            '');
    }  

 	function getBlockCode_Tags() {

        $aVars = array (
            'tags' => $this->_oTemplate->parseTags($this->aDataEntry['tags'], 'topic-link topic-hover'),
        );

        return array($this->_oTemplate->parseHtmlByName('entry_view_tags', $aVars));
	}

    function getCode() {
 
        return parent::getCode();
    }

}
