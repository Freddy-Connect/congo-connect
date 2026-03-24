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

class BxFAQPageView extends BxDolTwigPageView {	

	function BxFAQPageView(&$oMain, &$aDataEntry) {
		parent::BxDolTwigPageView('modzzz_faq_view', $oMain, $aDataEntry);
	}
		
	function getBlockCode_Info() {
        return array($this->_blockInfo ($this->aDataEntry, $this->_oTemplate->blockFields($this->aDataEntry)));
    }
  	
	function getBlockCode_Help() {
    
		$aData = $this->aDataEntry;
   
		$iFAQ = (int)$aData['id'];
		$icoUp = $this->_oTemplate->getIconUrl('faq_thumb_up.png');
		$icoDown = $this->_oTemplate->getIconUrl('faq_thumb_down.png');

		$iHelpful = (int)$aData['rate_up'];
		$iUnHelpful = (int)$aData['rate_down'];
 
		$oConfig = $GLOBALS['oBxFAQModule']->_oConfig; 
		$sRateUpUrl = BX_DOL_URL_ROOT . $oConfig->getBaseUri() . "rate_up/{$iFAQ}";
		$sRateDownUrl = BX_DOL_URL_ROOT . $oConfig->getBaseUri() . "rate_down/{$iFAQ}";

		$sTemplate = ($this->_oMain->_iProfileId) ? 'faq_help_rate' : 'faq_help';
  
		if($this->_oDb->rateHelpAlready($aData['id'], $this->_oMain->_iProfileId))
		{
			$sCode = MsgBox(_t('_modzzz_faq_mark_help_already')); 
			$sTemplate = 'faq_help'; 
		}
 
		$aVars = array (
			'id' => $iFAQ,
			'helpful_text' => sprintf(_t('_modzzz_faq_helpful_results'), $iHelpful) ,
			'unhelpful_text' => sprintf(_t('_modzzz_faq_unhelpful_results'), $iUnHelpful),
			'ico_up' => $icoUp,
			'ico_down' => $icoDown,
			'rate_up_url' => $sRateUpUrl,
			'rate_down_url' => $sRateDownUrl,  
		);
 
		$sCode .= $this->_oTemplate->parseHtmlByName($sTemplate, $aVars); 
		
		return $sCode;
	}

	//override the similar mod in the parent class
    function _blockInfo ($aData, $sFields = '') {

        $aAuthor = getProfileInfo($aData['author_id']);
 
		$sAuthorName =  $aAuthor['NickName'];
		$sAuthorLink = getProfileLink($aAuthor['ID']);	
		$icoThumb = get_member_thumbnail($aAuthor['ID'], 'none');
	  
        $aVars = array (
            'author_unit' => $icoThumb,
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
            'cats' => $this->_oTemplate->parseCategories($aData['categories']),
            'tags' => $this->_oTemplate->parseTags($aData['tags']),
            'fields' => $sFields,
            'author_username' => $sAuthorName,
            'author_url' => $sAuthorLink,
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
 
	    $sVideoEmbed = trim($this->aDataEntry['video_embed']);
		
		if(!$sVideoEmbed) return;
			  
		$sYoutubeEmbed = $this->youtubeId($sVideoEmbed);
  
		if($sYoutubeEmbed){ 
			$aVars = array ('embed' => $sYoutubeEmbed);  
			return $this->_oTemplate->parseHtmlByName('block_youtube', $aVars); 
		}else{
			$aVars = array ('embed' => $sVideoEmbed);  
			return $this->_oTemplate->parseHtmlByName('block_embed', $aVars);  
		}
	}
 
	function youtubeId($url) {
		$url = str_replace('&amp;', '&', $url); 

		if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
			$sVideoId = $match[1];  
		}
		/*else{  
			$sVideoId = substr( parse_url($url, PHP_URL_PATH), 1 );
			$sVideoId = ltrim( $sVideoId, '/' ); 
		}*/ 

		return $sVideoId;  
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
        modzzz_faq_import('Voting');
        $o = new BxFAQVoting ('modzzz_faq', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_faq_import('Cmts');
        $o = new BxFAQCmts ('modzzz_faq', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

            $oSubscription = new BxDolSubscription();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'modzzz_faq', '', (int)$this->aDataEntry['id']);

            $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'TitleSubscribe' => $aSubscribeButton['title'], 
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_modzzz_faq_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_faq_action_title_delete') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_faq_action_title_share') : '',
                'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_faq_action_remove_from_featured') : _t('_modzzz_faq_action_add_to_featured')) : '',
                'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_modzzz_faq_action_upload_photos') : '',
                'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_modzzz_faq_action_upload_videos') : '',
                'TitleUploadSounds' => $this->_oMain->isAllowedUploadSounds($this->aDataEntry) ? _t('_modzzz_faq_action_upload_sounds') : '',
                'TitleUploadFiles' => $this->_oMain->isAllowedUploadFiles($this->aDataEntry) ? _t('_modzzz_faq_action_upload_files') : '',
            );

            if (!$aInfo['TitleEdit'] && !$aInfo['TitleDelete'] && !$aInfo['TitleShare'] && !$aInfo['AddToFeatured'] && !$aInfo['TitleUploadPhotos'] && !$aInfo['TitleUploadVideos'] && !$aInfo['TitleUploadSounds'] && !$aInfo['TitleUploadFiles']) 
                return '';

            return $oSubscription->getData() . $oFunctions->genObjectsActions($aInfo, 'modzzz_faq');
        } 

        return '';
    }    
 

    function getCode() {
 
        return parent::getCode();
    }

}
