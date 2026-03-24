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

class BxListingReviewPageView extends BxDolTwigPageView {	

    function BxListingReviewPageView(&$oReviewsMain, &$aReview) {
        parent::BxDolTwigPageView('modzzz_listing_reviews_view', $oReviewsMain, $aReview);
	
        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;  
	}
  
	function getBlockCode_Info() {
        return array($this->_oTemplate->blockSubProfileInfo ($this->aDataEntry));
    }

	function getBlockCode_Desc() {
 
        $aVars = array (
            'desc' => $this->aDataEntry['desc'],
            'bx_if:cons' => array (
                'condition' => trim($this->aDataEntry['cons']),
                'content' => array (  
					'cons' => $this->aDataEntry['cons'],
                 ),
            ),  
            'bx_if:pros' => array (
                'condition' => trim($this->aDataEntry['pros']),
                'content' => array (  
					'pros' => $this->aDataEntry['pros'],
                 ),
            ),  
            'bx_if:quality' => array (
                'condition' => $this->aDataEntry['quality'],
                'content' => array (  
					'quality_rate_percent' => round(($this->aDataEntry['quality']/5)*90), 
                 ),
            ),  
            'bx_if:features' => array (
                'condition' => $this->aDataEntry['features'],
                'content' => array (  
					'features_rate_percent' => round(($this->aDataEntry['features']/5)*90), 
                 ),
            ), 
            'bx_if:moneyvalue' => array (
                'condition' => $this->aDataEntry['moneyvalue'],
                'content' => array (  
					'moneyvalue_rate_percent' => round(($this->aDataEntry['moneyvalue']/5)*90), 
                 ),
            ), 
            'bx_if:customer_support' => array (
                'condition' => $this->aDataEntry['customer_support'],
                'content' => array (  
					'support_rate_percent' => round(($this->aDataEntry['customer_support']/5)*90), 
                 ),
            ), 
        );
        return array($this->_oTemplate->parseHtmlByName('block_review_description', $aVars)); 
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
        modzzz_listing_import('ReviewVoting');
        $o = new BxListingReviewVoting ('modzzz_listing_review', (int)$this->aDataEntry['id']);
    	if (!$o->isEnabled()) return '';
        
		return array($o->getBigVoting ($this->_oMain->isAllowedRateSubProfile($this->_oDb->_sTableReview, $this->aDataEntry)));
     }        

    function getBlockCode_Comments() {    
        modzzz_listing_import('ReviewCmts');
        $o = new BxListingReviewCmts ('modzzz_listing_review', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
			$aReviewEntry = $this->_oDb->getReviewEntryById($this->aDataEntry['id']);
			$iEntryId = $aReviewEntry['listing_id'];
	 
		    $aDataEntry = $this->_oDb->getEntryById($iEntryId);
   
            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'TitleEdit' => $this->_oMain->isAllowedSubEdit($aDataEntry, $aReviewEntry) ? _t('_modzzz_listing_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedSubDelete($aDataEntry, $aReviewEntry) ? _t('_modzzz_listing_action_title_delete') : '',

				'TitleUploadPhotos' => '', 
				'TitleUploadVideos' => '', 
				'TitleUploadSounds' => '', 
				'TitleUploadFiles' => '', 
 
             );

            if (!$this->aInfo['TitleEdit'] && !$this->aInfo['TitleDelete'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'modzzz_listing_review');
        } 

        return '';
    }    
  
 
    function getCode() { 
        return parent::getCode();
    }    
}
