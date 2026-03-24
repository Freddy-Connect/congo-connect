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

class BxProfessionalReviewPageView extends BxDolTwigPageView {	

    function BxProfessionalReviewPageView(&$oReviewsMain, &$aReview) {
        parent::BxDolTwigPageView('modzzz_professional_reviews_view', $oReviewsMain, $aReview);
	
        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;  
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

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;

        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Videos() {

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;

        return $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Sounds() {

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;

		return $this->_blockSound ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'sounds'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Files() {

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;
 
        return $this->_blockFiles ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'files'), $this->aDataEntry['author_id']);
    }    
 
    function getBlockCode_Rate() {
        modzzz_professional_import('ReviewVoting');
        $o = new BxProfessionalReviewVoting ('modzzz_professional_review', (int)$this->aDataEntry['id']);
    	if (!$o->isEnabled()) return '';
        
		return array($o->getBigVoting ($this->_oMain->isAllowedRateSubProfile($this->_oDb->_sTableReview, $this->aDataEntry)));
     }        

    function getBlockCode_Comments() {    
        modzzz_professional_import('ReviewCmts');
        $o = new BxProfessionalReviewCmts ('modzzz_professional_review', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
			$aReviewEntry = $this->_oDb->getReviewEntryById($this->aDataEntry['id']);
			$iEntryId = $aReviewEntry['professional_id'];
	 
		    $aDataEntry = $this->_oDb->getEntryById($iEntryId);
   
            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'TitleEdit' => $this->_oMain->isAllowedSubEdit($aDataEntry, $aReviewEntry) ? _t('_modzzz_professional_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedSubDelete($aDataEntry, $aReviewEntry) ? _t('_modzzz_professional_action_title_delete') : '',

				'TitleUploadPhotos' => '', 
				'TitleUploadVideos' => '', 
				'TitleUploadSounds' => '', 
				'TitleUploadFiles' => '', 
 
             );

            if (!$this->aInfo['TitleEdit'] && !$this->aInfo['TitleDelete'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'modzzz_professional_review');
        } 

        return '';
    }    
  
 
    function getCode() { 
        return parent::getCode();
    }    
}
