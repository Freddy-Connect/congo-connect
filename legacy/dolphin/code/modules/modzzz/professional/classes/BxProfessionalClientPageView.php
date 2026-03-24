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

class BxProfessionalClientPageView extends BxDolTwigPageView {	

    function BxProfessionalClientPageView(&$oClientsMain, &$aClient) {
        parent::BxDolTwigPageView('modzzz_professional_clients_view', $oClientsMain, $aClient);
	
        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableClientMediaPrefix;  
	}
  
	function getBlockCode_Info() {
        return $this->_oTemplate->blockSubProfileInfo ($this->aDataEntry);
    }

	function getBlockCode_Desc() {
        $aDataEntry = $this->_oDb->getEntryById($this->aDataEntry['professional_id']);

        $aVars = array (
            'breadcrumb' => $this->_oTemplate->genBreadcrumb($aDataEntry),
            'description' => $this->aDataEntry['desc'],  
        );

        return array($this->_oTemplate->parseHtmlByName('block_description', $aVars));  
    }
 
    function getBlockCode_Photos() {

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableClientMediaPrefix;

        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
    }    

	function getBlockCode_VideoEmbed() {

		$aVideoUrls = $this->_oDb->getYoutubeVideos($this->aDataEntry['id'],'service');
		
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
				'video_title' => $aEachUrl['title'], 
			);
		}

		$aVars = array(
			'video_id' => $sFirstVideoId,
			'video_title' => $sFirstVideoTitle,
			'bx_repeat:video' => $aVideos
		);
		 
        return $this->_oTemplate->parseHtmlByName('block_youtube_videos', $aVars);   
    }

    function getBlockCode_Videos() {

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableClientMediaPrefix;

        return $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
    }    
     
    function getBlockCode_Comments() {    
        modzzz_professional_import('ClientCmts');
        $o = new BxProfessionalClientCmts ('modzzz_professional_client', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
 
		    $aDataEntry = $this->_oDb->getEntryById($this->aDataEntry['professional_id']);
   
            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'TitleEdit' => $this->_oMain->isAllowedSubEdit($aDataEntry, $this->aDataEntry) ? _t('_modzzz_professional_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedSubDelete($aDataEntry, $this->aDataEntry) ? _t('_modzzz_professional_action_title_delete') : '', 
				'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotosSubProfile($this->_oDb->_sTableClient,$this->aDataEntry) ? _t('_modzzz_professional_action_upload_photos') : '', 
				'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideosSubProfile($this->_oDb->_sTableClient,$this->aDataEntry) ? _t('_modzzz_professional_action_upload_videos') : '', 
                'TitleEmbed' => $this->_oMain->isAllowedSubEmbed($this->_oDb->_sTableClient,$aDataEntry) ? _t('_modzzz_professional_action_upload_youtube') : '', 
 
             );

            if (!$this->aInfo['TitleUploadPhotos'] && !$this->aInfo['TitleUploadVideos'] && !$this->aInfo['TitleEmbed'] && !$this->aInfo['TitleEdit'] && !$this->aInfo['TitleDelete'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'modzzz_professional_client');
        } 

        return '';
    }    
  
	function getBlockCode_Contact() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'contact');
    }

	function getBlockCode_Location() { 
        return $this->_blockCustomDisplay ($this->aDataEntry, 'location');
    }
 
	function _blockCustomDisplay($aDataEntry, $sType) {
		
		switch($sType) {
		  
			case "contact":
				$aAllow = array('website','email','telephone','mobile','fax');
			break;
			case "location":
				$aAllow = array('address1','address2','city','state','country','zip');
			break;  
		}
  
		$sFields = $this->_oTemplate->blockCustomFields($aDataEntry, $aAllow, 'client');

		if(!$sFields) return;
 
		$aVars = array ( 
            'fields' => $sFields, 
        );

        return array($this->_oTemplate->parseHtmlByName('custom_block_info', $aVars));   
    }
 
    function getCode() { 
        return parent::getCode();
    }    
}
