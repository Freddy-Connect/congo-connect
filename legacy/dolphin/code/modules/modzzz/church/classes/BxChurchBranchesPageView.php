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

class BxChurchBranchesPageView extends BxDolTwigPageView {	

    function BxChurchBranchesPageView(&$oBranchesMain, &$aBranches) {
        parent::BxDolTwigPageView('modzzz_church_branches_view', $oBranchesMain, $aBranches);
	
        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableBranchesMediaPrefix; 
	}
   
	function getBlockCode_InfoB4() {
        return array($this->_oTemplate->blockSubProfileInfo ($this->aDataEntry));
    }

	function getBlockCode_Location() {
   
		$sFields = $this->_oTemplate->blockSubProfileFields('branch', $this->aDataEntry);
  
		$aVars = array ( 
            'fields' => $sFields, 
        );

        return array($this->_oTemplate->parseHtmlByName('custom_block_info', $aVars)); 
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
 
    function getBlockCode_Rate() {
        modzzz_church_import('BranchesVoting');
        $o = new BxChurchBranchesVoting ('modzzz_church_branches', (int)$this->aDataEntry['id']);
    	if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRateSubProfile($this->_oDb->_sTableBranches, $this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_church_import('BranchesCmts');
        $o = new BxChurchBranchesCmts ('modzzz_church_branches', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
			$aBranchesEntry = $this->_oDb->getBranchesEntryById($this->aDataEntry['id']);
			$iEntryId = $aBranchesEntry['church_id'];
	
		    $aDataEntry = $this->_oDb->getEntryById($iEntryId);
   
            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                 'TitleEdit' => $this->_oMain->isAllowedEdit($aDataEntry) ? _t('_modzzz_church_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($aDataEntry) ? _t('_modzzz_church_action_title_delete') : '',
				'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotosSubProfile($this->_oDb->_sTableBranches,$this->aDataEntry) ? _t('_modzzz_church_action_upload_photos') : '', 
				'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideosSubProfile($this->_oDb->_sTableBranches,$this->aDataEntry) ? _t('_modzzz_church_action_upload_videos') : '', 
				'TitleUploadSounds' => $this->_oMain->isAllowedUploadSoundsSubProfile($this->_oDb->_sTableBranches,$this->aDataEntry) ? _t('_modzzz_church_action_upload_sounds') : '', 
				'TitleUploadFiles' => $this->_oMain->isAllowedUploadFilesSubProfile($this->_oDb->_sTableBranches,$this->aDataEntry) ? _t('_modzzz_church_action_upload_files') : '', 
 
            );
 
            if (!$this->aInfo['TitleEdit'] && !$this->aInfo['TitleDelete'] && !$this->aInfo['TitleUploadPhotos'] && !$aInfo['TitleUploadVideos'] && !$aInfo['TitleUploadSounds'] && !$aInfo['TitleUploadFiles'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'modzzz_church_branches');
        } 

        return '';
    }    
 

    function getCode() { 
        return parent::getCode();
    }    
}
