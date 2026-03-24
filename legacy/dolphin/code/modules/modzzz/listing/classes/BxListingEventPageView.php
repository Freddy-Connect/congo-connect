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

class BxListingEventPageView extends BxDolTwigPageView {	

    function BxListingEventPageView(&$oMain, &$aEvent) {
        parent::BxDolTwigPageView('modzzz_listing_events_view', $oMain, $aEvent);
	
        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'event/view/'. $this->aDataEntry['uri'];
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  
 
        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventMediaPrefix; 
	}
 
	function getBlockCode_Info() {
        return array($this->_oTemplate->blockSubProfileInfo ($this->aDataEntry));
    }
 
	function getBlockCode_Desc() {
  
        $aVars = array (
            'from' => date('M d, Y g:i A', $this->aDataEntry['event_start']),
            'to' => date('M d, Y g:i A', $this->aDataEntry['event_end']),
            'location' => $this->_oMain->_formatLocation($this->aDataEntry, false, true), 
            'desc' => $this->aDataEntry['desc'],  
        );
        return array($this->_oTemplate->parseHtmlByName('block_event_description', $aVars)); 
    }

	function getBlockCode_Media() {
		
		$oModuleDb = new BxDolModuleDb();   
 
        // top menu and sorting
        $aModes = array('photos', 'videos', 'sounds', 'files');
 
        foreach( $aModes as $sMyMode ) { 
			if(!$oModuleDb->isModule($sMyMode)) unset($aModes[$sMyMode]);
		}
  
        $aDBTopMenu = array();
  
        if (empty($_GET['mediaMode'])) {
			$sMode = 'photos';
			if(!in_array($sMode, $aModes)) 
        		$sMode = $aModes[0];  
        } else {
        	$sMode = (in_array($_GET['mediaMode'], $aModes) || $_GET['mediaMode']=='youtube') ? $_GET['mediaMode'] : $sMode = $aModes[0];
        }
   
        foreach( $aModes as $sMyMode ) {
      
			if(!$oModuleDb->isModule($sMyMode)) continue;

			$sModeTitle = _t('_sys_module_'.$sMyMode);
               
            $aDBTopMenu[$sModeTitle] = array('href' => $this->sUrlStart . "mediaMode=$sMyMode", 'dynamic' => true, 'active' => ( $sMyMode == $sMode ));
       }
	   //$aDBTopMenu[_t('_modzzz_listing_block_video_embed')] = array('href' => $this->sUrlStart . "mediaMode=youtube", 'dynamic' => true, 'active' => ( 'youtube' == $sMode ));

	   switch($sMode){
			case 'photos':
				$aBlock = $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
			break;
			case 'videos': 
				$aBlock = $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
 			break;
			case 'sounds':
				$aBlock = $this->_blockSound ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'sounds'), $this->aDataEntry['author_id']);
 			break;
			case 'files':
				$aBlock = $this->_blockFiles ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'files'), $this->aDataEntry['author_id']);
 			break; 
			case 'youtube':
				//$aBlock = $this->getBlockCode_VideoEmbed();
 			break; 
	   }

	   $aBlock = is_array($aBlock) ? $aBlock : array($aBlock);

	   if($aBlock[0]=='') $aBlock[0] = MsgBox(_t('_Empty'));

	   $aBlock[1] = $aDBTopMenu;

	   return $aBlock;  
    }
 
    function getBlockCode_Photos() {
        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Videos() {
        return $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Sounds() {
        return $this->_blockSound ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'sounds'), $this->aDataEntry['author_id']);
    }    
  
    function getBlockCode_Files() {
        return $this->_blockFiles ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'files'), $this->aDataEntry['author_id']);
    }    

	function _blockFiles ($aReadyMedia, $iAuthorId = 0) {        

        if (!$aReadyMedia)
            return '';

        $aVars = array (
            'bx_repeat:files' => array (),
        );

        foreach ($aReadyMedia as $iMediaId) {        

            $a = BxDolService::call('files', 'get_file_array', array($iMediaId), 'Search');
            if (!$a['date'])
                continue;

            bx_import('BxTemplFormView');
            $oForm = new BxTemplFormView(array());

            $aInputBtnDownload = array (
                'type' => 'submit',
                'name' => 'download', 
                'value' => _t ('_download'), 
                'attrs' => array(
                    'onclick' => "window.open ('" . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "event/download/".$this->aDataEntry[$this->_oDb->_sFieldId]."/{$iMediaId}','_self');",
                ),
            );

            $aVars['bx_repeat:files'][] = array (
                'id' => $iMediaId,
                'title' => $a['title'],
                'icon' => $a['file'],                
                'date' => defineTimeInterval($a['date']),
                'btn_download' => $oForm->genInputButton ($aInputBtnDownload),
            );            
        }

        if (!$aVars['bx_repeat:files'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_files', $aVars);
    }
 
    function getBlockCode_Rate() {
        modzzz_listing_import('EventVoting');
        $o = new BxListingEventVoting ('modzzz_listing_event', (int)$this->aDataEntry['id']);
    	if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRateSubProfile($this->_oDb->_sTableEvent, $this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_listing_import('EventCmts');
        $o = new BxListingEventCmts ('modzzz_listing_event', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
			$aEventEntry = $this->_oDb->getEventEntryById($this->aDataEntry['id']);
			$iEntryId = $aEventEntry['listing_id'];
	 
		    $aDataEntry = $this->_oDb->getEntryById($iEntryId);
   
            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'TitleEdit' => $this->_oMain->isAllowedEdit($aDataEntry) ? _t('_modzzz_listing_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($aDataEntry) ? _t('_modzzz_listing_action_title_delete') : '',
				'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotosSubProfile($this->_oDb->_sTableEvent,$this->aDataEntry) ? _t('_modzzz_listing_action_upload_photos') : '', 
				'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideosSubProfile($this->_oDb->_sTableEvent,$this->aDataEntry) ? _t('_modzzz_listing_action_upload_videos') : '', 
				'TitleUploadSounds' => $this->_oMain->isAllowedUploadSoundsSubProfile($this->_oDb->_sTableEvent,$this->aDataEntry) ? _t('_modzzz_listing_action_upload_sounds') : '', 
				'TitleUploadFiles' =>  $this->_oMain->isAllowedUploadFilesSubProfile($this->_oDb->_sTableEvent,$this->aDataEntry) ? _t('_modzzz_listing_action_upload_files') : '', 

            );

            if (!$this->aInfo['TitleEdit'] && !$this->aInfo['TitleDelete'] && !$this->aInfo['TitleUploadPhotos'] && !$this->aInfo['TitleUploadVideos'] && !$this->aInfo['TitleUploadSounds'] && !$this->aInfo['TitleUploadFiles'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'modzzz_listing_event');
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
		
		switch($sType){
		  
			case "contact":
				$aAllow = array('website','email','telephone','fax');
			break;
			case "location":
				$aAllow = array('place','address1','city','state','country','zip');
			break;  
		}
  
		$sFields = $this->_oTemplate->blockCustomFields($aDataEntry, $aAllow, 'event');

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
