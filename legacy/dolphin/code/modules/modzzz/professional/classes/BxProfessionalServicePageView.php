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

class BxProfessionalServicePageView extends BxDolTwigPageView {	

    function BxProfessionalServicePageView(&$oMain, &$aService) {
        parent::BxDolTwigPageView('modzzz_professional_services_view', $oMain, $aService);
	
		$this->oMain = $oMain;

		$this->sSearchResultClassName = 'BxProfessionalSearchResult';
        $this->sFilterName = 'filter';

        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableServiceMediaPrefix;  
 
        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/'. $this->aDataEntry['uri'];
  
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  

		$this->sUri = $this->aDataEntry['uri']; 
	}
	    
	function getBlockCode_Info() {
        return $this->_oTemplate->blockSubProfileInfo ($this->aDataEntry);
    }

	function getBlockCode_Desc() {
        $aDataEntry = $this->_oDb->getEntryById($this->aDataEntry['professional_id']);

		$sLength = $this->_oTemplate->getPreListDisplay('ServiceLength', $this->aDataEntry['length']);
 
		$sPrice = $this->_oTemplate->getCurrencySign($this->aDataEntry['currency']) . number_format($this->aDataEntry['price'],2);  
		
		if($this->aDataEntry['price_type']=='minimum'){
			$sPrice .= ' ' . _t('_modzzz_professional_and_up');
		}

		if($this->aDataEntry['price_type']=='negotiable'){
			$sPrice .= ' ' . _t('_modzzz_professional_negotiable_lower');
		}

        $aVars = array (
			'price' => $sPrice,
			'length' => $sLength,
            'title' => $this->aDataEntry['title'],  
            'description' => $this->aDataEntry['desc'],  
        );

        return array($this->_oTemplate->parseHtmlByName('block_service_description', $aVars));  
    }

	function getBlockCode_Reviews () {
 
        return $this->ajaxBrowseSubProfile(
            'review',
            'service_reviews',
            $this->_oDb->getParam('modzzz_professional_perpage_browse_subitems'), 
            array(), $this->sUri, true, false 
        ); 
    }
 
 	function getBlockCode_BookMe() {

		if(!$this->oMain->isAllowedBooking()) return;
 
		$aVars = array (
			'caption' => _t('_modzzz_professional_action_title_book_me'),
			'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .'booking/add/'. $this->aDataEntry['uri'] 
		); 
		
		return array($this->_oTemplate->parseHtmlByName('block_button', $aVars));	
	}
 
    function getBlockCode_Photos() {

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableServiceMediaPrefix;

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

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableServiceMediaPrefix;

        return $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Sounds() {

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableServiceMediaPrefix;

		return $this->_blockSound ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'sounds'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Files() {

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableServiceMediaPrefix;
 
        return $this->_blockFiles ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'files'), $this->aDataEntry['author_id']);
    }    
 
    function getBlockCode_Rate() {
        modzzz_professional_import('ServiceVoting');
        $o = new BxProfessionalServiceVoting ('modzzz_professional_service', (int)$this->aDataEntry['id']);
    	if (!$o->isEnabled()) return '';
        
		return array($o->getBigVoting ($this->_oMain->isAllowedRateSubProfile($this->_oDb->_sTableService, $this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_professional_import('ServiceCmts');
        $o = new BxProfessionalServiceCmts ('modzzz_professional_service', (int)$this->aDataEntry['id']);
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
                'TitleBookMe' => '',/*$this->_oMain->isAllowedBooking($this->aDataEntry) ? _t('_modzzz_professional_action_title_book_me') : '',*/
                'TitleEdit' => $this->_oMain->isAllowedSubEdit($aDataEntry, $this->aDataEntry) ? _t('_modzzz_professional_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedSubDelete($aDataEntry, $this->aDataEntry) ? _t('_modzzz_professional_action_title_delete') : '',
                'TitleEmbed' => $this->_oMain->isAllowedSubEmbed($this->_oDb->_sTableService,$this->aDataEntry) ? _t('_modzzz_professional_action_upload_youtube') : '', 
				'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotosSubProfile($this->_oDb->_sTableService,$this->aDataEntry) ? _t('_modzzz_professional_action_upload_photos') : '', 
				'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideosSubProfile($this->_oDb->_sTableService,$this->aDataEntry) ? _t('_modzzz_professional_action_upload_videos') : '',  
            );

            if (!$this->aInfo['TitleUploadPhotos'] && !$this->aInfo['TitleUploadVideos'] && !$this->aInfo['TitleEmbed'] && !$this->aInfo['TitleEdit'] && !$this->aInfo['TitleDelete'] && !$this->aInfo['TitleBookMe'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'modzzz_professional_service');
        } 

        return '';
    }    
  
	function ajaxBrowseSubProfile($sType, $sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true, $bShowAll=true) {

        bx_import ('SearchResult', $this->_oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);
 
        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);
 
        if (!($s = $o->displaySubProfileResultBlock($sType))) {
			 return;
             //return array(MsgBox(_t('_Empty')), $aMenu);
		} 

        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->_oConfig->getBaseUri() . $o->sBrowseUrl, -1, -1, $bShowAll);

        return array($s,$aMenu,$sAjaxPaginate,''); 
    }    
 
    function getCode() { 
        return parent::getCode();
    }  

	function getBlockCode_Bookings () {
 
        // top menu and sorting
        $aModes = array('pending', 'approved');
        $aDBTopMenu = array();
        
        if (empty($_GET['bookMode'])) {
        	$sMode = 'pending';
        } else {
        	$sMode = (in_array($_GET['bookMode'], $aModes)) ? $_GET['bookMode'] : $sMode = 'pending';
        }
   
        foreach( $aModes as $sMyMode ) {
            switch ($sMyMode) {
                case 'pending':
                    $sModeTitle = _t('_modzzz_professional_tab_pending');
                break; 
                case 'approved': 
                    $sModeTitle = _t('_modzzz_professional_tab_approved');
                break; 
            }
            $aDBTopMenu[$sModeTitle] = array('href' => $this->sUrlStart . "bookMode=$sMyMode", 'dynamic' => true, 'active' => ( $sMyMode == $sMode ));
        }
 
        $sCode = $this->ajaxBrowseSubProfile(
            'booking',
            'bookings',
            $this->_oDb->getParam('modzzz_professional_perpage_browse_subitems'), 
            $aDBTopMenu, $sMode.'|'.$this->aDataEntry['uri'], true, false 
        );  
 
        return ($sCode) ? $sCode : array(MsgBox(_t('_Empty')), $aDBTopMenu); 
    }





	
}
