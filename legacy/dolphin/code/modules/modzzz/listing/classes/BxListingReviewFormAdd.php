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

bx_import ('BxDolProfileFields');
bx_import ('BxDolFormMedia');

class BxListingReviewFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxListingReviewFormAdd ($oMain, $iProfileId, $iListingId = 0, $iEntryId = 0, $iThumb = 0) { 
        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;
  		bx_import ('SubPrivacy', $this->_oMain->_aModule);
		$GLOBALS['oBxListingModule']->_oSubPrivacy = new BxListingSubPrivacy($this->_oMain, $this->_oDb->_sTableReview); 
  
        // generate templates for form custom elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($this->_oMain->_iProfileId, $iEntryId, $iThumb);

        // privacy

        $aInputPrivacyCustom = array ();
        $aInputPrivacyCustom[] = array ('key' => '', 'value' => '----');
        $aInputPrivacyCustom[] = array ('key' => 'f', 'value' => _t('_modzzz_listing_privacy_fans_only'));
        $aInputPrivacyCustomPass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9f]+)$/'),
        );
 

        $aInputPrivacyCustom2 = array (
            array('key' => 'f', 'value' => _t('_modzzz_listing_privacy_fans')),
            array('key' => 'a', 'value' => _t('_modzzz_listing_privacy_admins_only'))
        );
        $aInputPrivacyCustom2Pass = array (
            'pass' => 'Preg', 
            'params' => array('/^([fa]+)$/'),
        );
  
        $aInputPrivacyView = $GLOBALS['oBxListingModule']->_oSubPrivacy->getGroupChooser($iProfileId, 'listing', 'view');
        $aInputPrivacyView['values'] = array_merge($aInputPrivacyView['values'], $aInputPrivacyCustom);
        $aInputPrivacyView['db'] = $aInputPrivacyCustomPass;
 
        $aInputPrivacyComment = $GLOBALS['oBxListingModule']->_oSubPrivacy->getGroupChooser($iProfileId, 'listing', 'comment');
        $aInputPrivacyComment['values'] = array_merge($aInputPrivacyComment['values'], $aInputPrivacyCustom);
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $GLOBALS['oBxListingModule']->_oSubPrivacy->getGroupChooser($iProfileId, 'listing', 'rate');
        $aInputPrivacyRate['values'] = array_merge($aInputPrivacyRate['values'], $aInputPrivacyCustom);
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;
 
 

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_reviews',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_listing_review_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'listing_id' => array(
                    'type' => 'hidden',
                    'name' => 'listing_id', 
                    'value' => $iListingId,
                    'db' => array (
                        'pass' => 'Int' 
                    ) 
                ),
                'header_grating' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_listing_form_header_grating')
                ),  
				'quality' => array(
                    'type' => 'range',
                    'name' => 'quality',
                    'caption' => _t('_modzzz_listing_form_caption_review_quality'),
                    'info' => _t('_modzzz_listing_form_info_review_quality'),
                    'value' => '0',
					'attrs' => array(
                        'min' => 0,
                        'max' => 5,
					),
                    'required' => false,   
                    'db' => array (
                        'pass' => 'Int', 
                    ) 
                 ),
				'features' => array(
                    'type' => 'range',
                    'name' => 'features',
                    'caption' => _t('_modzzz_listing_form_caption_review_features'),
                    'info' => _t('_modzzz_listing_form_info_review_features'),
                    'value' => '0',
					'attrs' => array(
                        'min' => 0,
                        'max' => 5,
					),
                    'required' => false,   
                    'db' => array (
                        'pass' => 'Int', 
                    ) 
                 ),
 
                'header_orating' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_listing_form_header_orating')
                ), 
				'moneyvalue' => array(
                    'type' => 'range',
                    'name' => 'moneyvalue',
                    'caption' => _t('_modzzz_listing_form_caption_review_value'),
                    'info' => _t('_modzzz_listing_form_info_review_value'),
                    'value' => '0',
					'attrs' => array(
                        'min' => 0,
                        'max' => 5,
					),
                    'required' => false,   
                    'db' => array (
                        'pass' => 'Int', 
                    ) 
                 ),
				'customer_support' => array(
                    'type' => 'range',
                    'name' => 'customer_support',
                    'caption' => _t('_modzzz_listing_form_caption_review_support'),
                    'info' => _t('_modzzz_listing_form_info_review_support'),
                    'value' => '0',
					'attrs' => array(
                        'min' => 0,
                        'max' => 5,
					),
                    'required' => false,   
                    'db' => array (
                        'pass' => 'Int', 
                    ) 
                ),
                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => '&nbsp;'
                ),  
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_listing_form_caption_title'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3, $this->_oMain->_oConfig->getTitleLength()),
                        'error' => _t ('_modzzz_listing_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => false,
                ),  
                'pros' => array(
                    'type' => 'textarea',
                    'name' => 'pros',
                    'caption' => _t('_modzzz_listing_form_caption_pros'),
                    'html' => 2,
					/*
                    'required' => true,  
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(20,64000),
                        'error' => _t ('_modzzz_listing_err_desc'),
                    ),*/                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ), 					
                'cons' => array(
                    'type' => 'textarea',
                    'name' => 'cons',
                    'caption' => _t('_modzzz_listing_form_caption_cons'),
                    'html' => 2,
					/*
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(20,64000),
                        'error' => _t ('_modzzz_listing_err_desc'),
                    ),*/                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),  
                'desc' => array(
                    'type' => 'textarea',
                    'name' => 'desc',
                    'caption' => _t('_modzzz_listing_form_caption_general_comments'),
                    'required' => true,
                    'html' => 2,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(20,64000),
                        'error' => _t ('_modzzz_listing_err_desc'),
                    ),                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),  
   
                // privacy
                
                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_listing_form_header_privacy'),
                ),

                'allow_view_to' => $aInputPrivacyView,
 
                'allow_comment_to' => $aInputPrivacyComment,

                'allow_rate_to' => $aInputPrivacyRate, 
  
				'Submit' => array (
					'type' => 'submit',
					'name' => 'submit_form',
					'value' => _t('_Submit'),
					'colspan' => false,
				),  

            ),            
        );
 
        parent::BxDolFormMedia ($aCustomForm);
    }

    /**
     * @access private
     */ 
    function _getFilesInEntry ($sModuleName, $sServiceMethod, $sName, $sMediaType, $iIdProfile, $iEntryId)
    {             

        $aReadyMedia = array ();
        if ($iEntryId)
            $aReadyMedia = $this->_oDb->getMediaIds($iEntryId, $sMediaType);
        
        if (!$aReadyMedia)
            return array();

        $aDataEntry = $this->_oDb->getReviewEntryById($iEntryId);

        $aFiles = array ();
        foreach ($aReadyMedia as $iMediaId)
        {
            switch ($sModuleName) {
            case 'photos':
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId, 'icon'), 'Search');
                break;
            case 'sounds':
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId, 'browse'), 'Search');
                break;
            default:
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId), 'Search');
            }
    
            if (!$this->_oMain->isEntryAdmin($aDataEntry, $iIdProfile) && $aRow['owner'] != $iIdProfile)
                continue;

            $aFiles[] = array (
                'name' => $sName,
                'id' => $iMediaId,
                'title' => $aRow['title'],
                'icon' => $aRow['file'],
                'owner' => $aRow['owner'],
                'checked' => 'checked',
            );
        }
        return $aFiles;
    }        

    /**
     * process media upload updates
     * call it after successful call $form->insert/update functions 
     * @param $iEntryId associated entry id
     * @return nothing
     */ 
    function processMedia ($iEntryId, $iProfileId) { 

        $aDataEntry = $this->_oDb->getReviewEntryById($iEntryId);
		
		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;

        foreach ($this->_aMedia as $sName => $a) {
 
            $aFiles = $this->_getFilesInEntry ($a['module'], $a['service_method'], $a['post'], $sName, (int)$iProfileId, $iEntryId);
            foreach ($aFiles as $aRow)
                $aFiles2Delete[$aRow['id']] = $aRow['id'];

            if (is_array($_REQUEST[$a['post']]) && $_REQUEST[$a['post']] && $_REQUEST[$a['post']][0]) {
                $this->updateMedia ($iEntryId, $_REQUEST[$a['post']], $aFiles2Delete, $sName);
            } else {
                $this->deleteMedia ($iEntryId, $aFiles2Delete, $sName);
            }

            $sUploadFunc = $a['upload_func'];
            if ($aMedia = $this->$sUploadFunc($a['tag'], $a['cat'])) {
                $this->_oDb->insertMedia ($iEntryId, $aMedia, $sName);
                if ($a['thumb'] && !$aDataEntry[$a['thumb']] && !$_REQUEST[$a['thumb']]) 
                    $this->_oDb->setThumbnail ($iEntryId, 0);
            }

            $aMediaIds = $this->_oDb->getMediaIds($iEntryId, $sName);

            if ($a['thumb']) { // set thumbnail to another one if current thumbnail is deleted                
                $sThumbFieldName = $a['thumb'];
                if ($aDataEntry[$sThumbFieldName] && !isset($aMediaIds[$aDataEntry[$sThumbFieldName]])) {
                    $this->_oDb->setThumbnail ($iEntryId, 0);
                } 
            }

            // process all deleted media - delete actual file
            $aDeletedMedia = array_diff ($aFiles2Delete, $aMediaIds);
            if ($aDeletedMedia) {
                foreach ($aDeletedMedia as $iMediaId) {
                    if (!$this->_oDb->isMediaInUse($iMediaId, $sName))
                        BxDolService::call($a['module'], 'remove_object', array($iMediaId));
                }
            }
        }

    }

 

}

