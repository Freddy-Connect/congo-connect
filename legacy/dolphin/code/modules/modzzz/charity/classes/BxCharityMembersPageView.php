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

class BxCharityMembersPageView extends BxDolTwigPageView {	

    function BxCharityMembersPageView(&$oMembersMain, &$aMembers) {
        parent::BxDolTwigPageView('modzzz_charity_members_view', $oMembersMain, $aMembers);
	
        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableMembersMediaPrefix; 
	}

   	function getBlockCode_Info() {
        return array($this->_oTemplate->blockSubProfileInfo ($this->aDataEntry));
    }

	function getBlockCode_Desc() {
        $aVars = array (
            'description' => $this->aDataEntry['desc'],
        );
        return array($this->_oTemplate->parseHtmlByName('block_description', $aVars));

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
                    'onclick' => "window.open ('" . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "members/download/".$this->aDataEntry[$this->_oDb->_sFieldId]."/{$iMediaId}','_self');",
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
        modzzz_charity_import('MembersVoting');
        $o = new BxCharityMembersVoting ('modzzz_charity_members', (int)$this->aDataEntry['id']);
    	if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRateSubProfile($this->_oDb->_sTableMembers, $this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_charity_import('MembersCmts');
        $o = new BxCharityMembersCmts ('modzzz_charity_members', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
			$aMembersEntry = $this->_oDb->getMembersEntryById($this->aDataEntry['id']);
			$iEntryId = $aMembersEntry['charity_id'];
	
		    $aDataEntry = $this->_oDb->getEntryById($iEntryId);
   
            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                 'TitleEdit' => $this->_oMain->isAllowedEdit($aDataEntry) ? _t('_modzzz_charity_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($aDataEntry) ? _t('_modzzz_charity_action_title_delete') : '',
				'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotosSubProfile($this->_oDb->_sTableMembers,$this->aDataEntry) ? _t('_modzzz_charity_action_upload_photos') : '', 
				'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideosSubProfile($this->_oDb->_sTableMembers,$this->aDataEntry) ? _t('_modzzz_charity_action_upload_videos') : '', 
				'TitleUploadSounds' => $this->_oMain->isAllowedUploadSoundsSubProfile($this->_oDb->_sTableMembers,$this->aDataEntry) ? _t('_modzzz_charity_action_upload_sounds') : '', 
				'TitleUploadFiles' => $this->_oMain->isAllowedUploadFilesSubProfile($this->_oDb->_sTableMembers,$this->aDataEntry) ? _t('_modzzz_charity_action_upload_files') : '', 


            );
 
            if (!$this->aInfo['TitleEdit'] && !$this->aInfo['TitleDelete'] && !$this->aInfo['TitleUploadPhotos'] && !$aInfo['TitleUploadVideos'] && !$aInfo['TitleUploadSounds'] && !$aInfo['TitleUploadFiles'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'modzzz_charity_members');
        } 

        return '';
    }    
 

    function getCode() { 
        return parent::getCode();
    }    
}
