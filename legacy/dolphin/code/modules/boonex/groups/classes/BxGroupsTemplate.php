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

bx_import('BxDolTwigTemplate');

/*
 * Groups module View
 */
class BxGroupsTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    
	/**
	 * Constructor
	 */
	function BxGroupsTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }
 

    // ======================= ppage compose block functions 

    function blockDesc (&$aDataEntry) {
        $aVars = array (
            'description' => $aDataEntry['desc'],
        );
        return $this->parseHtmlByName('block_description', $aVars);
    }

    function blockFields (&$aDataEntry, $sType='') {
        $sRet = '<table class="bx_groups_fields">';
        
		if($sType){
			bx_groups_import (ucwords($sType).'FormAdd');    
			$sClass = 'BxGroups'.ucwords($sType).'FormAdd';
			$oForm = new $sClass($GLOBALS['oBxGroupsModule'], $_COOKIE['memberID']);
		}else{
			bx_groups_import ('FormAdd');      
			$oForm = new BxGroupsFormAdd ($GLOBALS['oBxGroupsModule'], $_COOKIE['memberID']); 
		}

        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display']) || !$aDataEntry[$k]) continue;
            $sRet .= '<tr><td class="bx_groups_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="bx_groups_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display']))){

				if($a['name'] == 'state'){
					$sRet .= $this->getStateName($aDataEntry['country'], $aDataEntry[$k]);
				}else{
					$sRet .= call_user_func_array(array($this, $a['display']), array($aDataEntry[$k]));
				}  

			}else if (0 == strcasecmp($k, 'country')){
                $sRet .= _t($GLOBALS['aPreValues']['Country'][$aDataEntry[$k]]['LKey']);
			}else{
                $sRet .= $aDataEntry[$k];
			}
            $sRet .= '<td></tr>';
        }
        $sRet .= '</table>';
        return $sRet;
    }

    function unit ($aData, $sTemplateName, &$oVotingView, $isShort = false){
    
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxGroupsModule');
 
        if (!$this->_oMain->isAllowedView ($aData) && $aData[$this->_oDb->_sFieldAllowViewTo]!='f' ) {
            $aVars = array ('extra_css_class' => 'bx_groups_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }

        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        }
 
		if($aData['icon']){ 
		    $sNoImage = $this->_oDb->getLogo($aData['id'], $aData['icon'], true);
 		}else{
			$sNoImage = $this->getImageUrl('no-image-thumb.png');
		}
		
		
		///////////////////////freddy events integration
		$iId = $aData['id'];
		
		if(getParam('bx_groups_boonex_events')=='on'){
			$iEventsCount = $this->_oDb->getBoonexEventsCount($iId);
		}
		
		if ($iEventsCount ==1){
			$iEventKey =_t('_bx_groups_event_unit_single');
		} else{
			$iEventKey =_t('_bx_groups_event_unit_plural');
		}
   ////////////////////////////////////////////////////////////////
		
		

        $aVars = array (
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $sNoImage,
            'group_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'group_title' => $aData['title'],
            'created' => defineTimeInterval($aData['created']),
            'fans_count' => $aData['fans_count'],
            'country_city' => $this->_oMain->_formatLocation($aData),
            'snippet_text' => $this->_oMain->_formatSnippetText($aData),
			
			'bx_if:events_count' => array( 
								'condition' => $iEventsCount,
								'content' => array(
			  'events_count_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'event/browse/' . $aData['uri'],
              'events_count' =>  $iEventsCount,  
			  'eventKey' => $iEventKey,
								), 
							), 
			
            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ),
        );

        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
    function forum_unit ($aData) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxGroupsModule');
 
		$sForumUri = $aData['forum_uri'];
		$sTopic = $aData['topic_title']; 
		$sTopicUri = $aData['topic_uri'];
		$sPostText = $aData['post_text']; 
		$sDate = defineTimeInterval($aData['when']); 
		$sPoster = $aData['user']; 

		$iLimitChars = (int)getParam('bx_groups_max_preview');
		$sPostText = $this->_oMain->_formatSnippetText($aData, $iLimitChars, $sPostText);
 
		$sTopicUrl = BX_DOL_URL_ROOT . 'forum/groups/topic/'.$sTopicUri.'.htm';

		$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail(getID($sPoster));
  
		$aVars = array( 

			'topic_url' => $sTopicUrl, 
			'topic' => $sTopic, 
			'snippet_text' => $sPostText, 

			'created' => $sDate,
			'author_url' => getProfileLink(getID($sPoster)),
			'author' => $sPoster,
			'thumb_url' => $sMemberThumb,
  
			'bx_if:main' => array( 
				'condition' => false,
				'content' => array(),  
			),  
		);
 
		return $this->parseHtmlByName('entry_view_block_forum', $aVars); 
	}
  
    function blockCustomSubItemFields (&$aDataEntry, $sType='', $aShow=array()) {
        
		$bHasValues = false;
		
		$sRet = '<table class="bx_groups_fields">';
        bx_groups_import ($sType.'FormAdd');   
		
		$sClass = 'BxGroups'.$sType.'FormAdd';
        $oForm = new $sClass ($GLOBALS['oBxGroupsModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            //if (!isset($a['display'])) continue;
 
            if (!in_array($a['name'],$aShow)) continue;
            
			if (!trim($aDataEntry[$k])) continue;

			$bHasValues = true;

            $sRet .= '<tr><td class="bx_groups_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="bx_groups_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display']))){ 

				if($a['name'] == 'state'){
					$sRet .= $this->getStateName($aDataEntry['country'], $aDataEntry[$k]);
				}else{ 
					$sRet .= call_user_func_array(array($this, $a['display']), array($a['listname'],$aDataEntry[$k]));
				}
 			}else{ 
				$sRet .= $aDataEntry[$k];
			}

            $sRet .= '<td></tr>';
        }
        $sRet .= '</table>';

        return ($bHasValues) ? $sRet : '';
    }
 
    function blockCustomFields (&$aDataEntry, $aShow=array()) {
        $sRet = '<table class="bx_groups_fields">';
        bx_groups_import ('FormAdd');        
        $oForm = new BxGroupsFormAdd ($GLOBALS['oBxGroupsModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            //if (!isset($a['display'])) continue;
 
            if (!in_array($a['name'],$aShow)) continue;
            
			if (!trim($aDataEntry[$k])) continue;

            $sRet .= '<tr><td class="bx_groups_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="bx_groups_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display']))){ 

				if($a['name'] == 'state'){
					$sRet .= $this->getStateName($aDataEntry['country'], $aDataEntry[$k]);
				}else{ 
					$sRet .= call_user_func_array(array($this, $a['display']), array($a['listname'],$aDataEntry[$k]));
				}
 			}else{ 
				$sRet .= $aDataEntry[$k];
			}

            $sRet .= '<td></tr>';
        }
        $sRet .= '</table>';

        return $sRet;
    }

    function filterDate ($i, $bLongFormat = true) {
		if($bLongFormat)
			return date('M d, Y', $i) . ' ('.defineTimeInterval($i) . ')';
		else
			return date('M d, Y', $i);
    } 
 
    function event_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxGroupsModule');
  
        if ( !$this->_oMain->isAllowedViewSubProfile('event_main', $aData) ) {            
            $aVars = array ('extra_css_class' => 'bx_groups_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

		$iLimitChars = (int)getParam('bx_groups_max_preview');

        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-event.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'event/view/' . $aData['uri'],
            'title' => $aData['title'],
            'event_start' => defineTimeInterval($aData['event_start']),
            'participants' => $aData['fans_count'],

			'country_city' => $this->_oMain->_formatLocation($aData), 
			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ),  

         ); 
 
         return $this->parseHtmlByName($sTemplateName, $aVars);
    }
  
    function venue_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxGroupsModule');
  
		$iGroupId = (int)$aData['group_id'];
 
        $aDataEntry = $this->_oDb->getEntryById($iGroupId);

        if ( !$this->_oMain->isAllowedViewSubProfile('venue_main', $aData) ) {            
            $aVars = array ('extra_css_class' => 'bx_groups_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
 

        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

		$iLimitChars = (int)getParam('bx_groups_max_preview');

        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-venue.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'venue/view/' . $aData['uri'],
            'title' => $aData['title'],
			'country_city' => $this->_oMain->_formatLocation($aData), 
			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 
        ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

   function news_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxGroupsModule');
  
 
        if ( !$this->_oMain->isAllowedViewSubProfile('news_main', $aData) ) {            
            $aVars = array ('extra_css_class' => 'bx_groups_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('bx_groups_max_preview');
  
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-news.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/view/' . $aData['uri'],
            'title' => $aData['title'],

			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 
        ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
  
    function sponsor_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxGroupsModule');
 
   
        if ( !$this->_oMain->isAllowedViewSubProfile('sponsor_main', $aData) ) {            
            $aVars = array ('extra_css_class' => 'bx_groups_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

		$iLimitChars = (int)getParam('bx_groups_max_preview');

        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-sponsor.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'sponsor/view/' . $aData['uri'],
            'title' => $aData['title'],

			'country_city' => $this->_oMain->_formatLocation($aData), 
			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 
        ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

   function client_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxGroupsModule');
 
   
        if ( !$this->_oMain->isAllowedViewSubProfile('client_main', $aData) ) {            
            $aVars = array ('extra_css_class' => 'bx_groups_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

		$iLimitChars = (int)getParam('bx_groups_max_preview');

        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-client.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'client/view/' . $aData['uri'],
            'title' => $aData['title'],

			'country_city' => $this->_oMain->_formatLocation($aData), 
			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 
        ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

   function employee_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxGroupsModule');
 
   
        if ( !$this->_oMain->isAllowedViewSubProfile('employee_main', $aData) ) {            
            $aVars = array ('extra_css_class' => 'bx_groups_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

		$iLimitChars = (int)getParam('bx_groups_max_preview');

        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-employee.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'employee/view/' . $aData['uri'],
            'title' => $aData['title'],

			'country_city' => $this->_oMain->_formatLocation($aData), 
			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 
        ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
  
   function blog_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxGroupsModule');
 
 
        if ( !$this->_oMain->isAllowedViewSubProfile('blog_main', $aData) ) {            
            $aVars = array ('extra_css_class' => 'bx_groups_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

		$iLimitChars = (int)getParam('bx_groups_max_preview');

        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-blog.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'blog/view/' . $aData['uri'],
            'title' => $aData['title'],
 
			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 
        ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
   
    function blockSubProfileInfo ($sType, &$aData, $bShowFields=true) {

		$this->_oMain = BxDolModule::getInstance('BxGroupsModule');

        $aAuthor = getProfileInfo($aData['author_id']);
 
        $aVars = array (
            'author_unit' => get_member_thumbnail($aAuthor['ID'], 'none', true),
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),

			'bx_if:cats' => array( 
				'condition' =>  $aData['categories'],
				'content' => array(
					'cats' => $this->parseSubCategories($sType, $aData['categories']),
  				), 
			),
			'bx_if:tags' => array( 
				'condition' =>  $aData['tags'],
				'content' => array(
					'tags' => $this->parseSubTags($aData['tags']),
  				), 
			),
  
			'bx_if:fields' => array( 
				'condition' => ($sType=='event'),
				'content' => array(
					'event_start' => date('M d, Y g:i A', $aData['event_start']),
					'event_end' => date('M d, Y g:i A', $aData['event_end']) 
  				), 
			),
 
         );

        return $this->parseHtmlByName('block_subprofile_info', $aVars);
    }
 
    function parseSubCategories ($sType, $sVal='') {
		
		$sName = 'Group'.ucwords($sType).'Categories';

		$sStr = '';
		$aVals = explode(';',$sVal);
		foreach($aVals as $aEachVal){
			$sStr .= htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sName][$aEachVal]['LKey'])) . '&#160';
 		}
 		return $sStr;
 	}
 
    function parseSubTags($s){ 
        $sRet = '';
        $a = explode (',', $s);
         
        foreach ($a as $sName)
            $sRet .= $sName.'&#160';
  
        return $sRet;
    }

	function getPreListDisplay($sField, $sVal){ 
 		return htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sField][$sVal]['LKey']) );
	}

	function getStateName($sCountry, $sState=''){  
		return $this->_oDb->getStateName($sCountry, $sState);
	}

	function youtubeId($url) {
		$url = str_replace('&amp;', '&', $url); 

		if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
			$sVideoId = $match[1];  
		}else{  
			$sVideoId = substr( parse_url($url, PHP_URL_PATH), 1 );
			$sVideoId = ltrim( $sVideoId, '/' ); 
		} 

		return $sVideoId;  
	}

 	function getWebsiteUrl($sField, $sUrl){ 

        $sRealUrl = strncasecmp($sUrl, 'http://', 7) != 0 && strncasecmp($sUrl, 'https://', 8) != 0 ? 'http://' . $sUrl : $sUrl;
		
		$aUrlParts = parse_url($sRealUrl);
		$sDisplayUrl = ($aUrlParts['host']) ? $aUrlParts['host'] : $sRealUrl;
 
		return '<a target=_blank href="'.$sRealUrl.'">'.$sDisplayUrl.'</a>'; 
	}

	/*begin 2.1.9*/ 
	function displayMembershipLevels($aValues) {
 
	    $aMemberships = array();
	    foreach($aValues as $aValue) { 

			$iThreshold = $this->_oDb->getMembershipThreshold($aValue['mem_id']); 
		 
            $aMemberships[] = array(
                'url_root' => BX_DOL_URL_ROOT,
                'id' => $aValue['mem_id'],
                'title' => $aValue['mem_name'],
                'threshold' => $iThreshold,  
	        );
	    }

		$aVars = array(
			'bx_repeat:levels' => $aMemberships,
			'message' => _t('_bx_groups_membership_settings_desc')
		);

	    $this->addAdminCss(array('explanation.css'));

	    return $this->parseHtmlByName('admin_memberships', $aVars);
	}
	/*end 2.1.9*/

 
}
