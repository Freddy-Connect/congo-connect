<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx News
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
bx_import('BxDolCategories');

/*
 * News module View
 */
class BxNewsTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    
	/**
	 * Constructor
	 */
	function BxNewsTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }

    function _formatLocation (&$aData, $isCountryLink = false, $isFlag = false)
    {
        $sFlag = $isFlag ? ' ' . genFlag($aData['country']) : '';
        $sCountry = _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']);
        if ($isCountryLink)
            $sCountry = '<a href="' . $this->_oConfig->getBaseUri() . 'browse/country/' . strtolower($aData['country']) . '">' . $sCountry . '</a>';

		$sStreetCity = (trim($aData['street']) ? $aData['street'] . ', ' : '') . (trim($aData['city']) ? $aData['city'] : '');

        return ($sStreetCity ? $sStreetCity . '<br>' : '') . ($aData['state'] ? $this->getStateName($aData['country'], $aData['state']) . ', ' : '') . $sCountry . $sFlag;
    }

	function getStateName($sCountry, $sState=''){  
		return $this->_oDb->getStateName($sCountry, $sState);
	}

    function parsePreValues ($sName, $sVal='') {  
 		return htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sName][$sVal]['LKey']) );
 	}
 
    function unit ($aData, $sTemplateName, &$oVotingView, $isShort=false) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxNewsModule');

        if (!$this->_oMain->isAllowedBrowse ()) {            
            $aVars = array ('extra_css_class' => 'modzzz_news_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $aAuthor = getProfileInfo($aData['author_id']);
  
		$sOwnerThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aAuthor['ID'], 'left'); 
   
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
    
		$iLimitChars = $this->_oConfig->getSnippetLength();

		$bShowMember = (getParam('modzzz_news_show_member')=='on');

 		$aCategory = $this->_oDb->getCategoryInfo($aData['parent_category_id']);
		$sCategoryUrl = $this->_oDb->getCategoryUrl($aCategory['uri']); 
		
		if($aData['category_id']){
			$aSubCategory = $this->_oDb->getCategoryInfo($aData['category_id']); 
			$sSubCategoryUrl = $this->_oDb->getSubCategoryUrl($aSubCategory['uri']);
 		}
 
		/*[begin] school integration (modzzz)*/
		if(getParam('modzzz_schools_modzzz_news')=='on'){ 
			$sSchoolLink = BxDolModule::getInstance('BxSchoolsModule')->getSchoolLink($aData['school_id']);
		}
	    /*[end] school integration (modzzz)*/
 
		/*[begin] event integration (modzzz)*/
		if(getParam('bx_events_modzzz_news')=='on'){ 
			$sEventLink = BxDolModule::getInstance('BxEventsModule')->getEventLink($aData['event_id']);
		}
	    /*[end] event integration (modzzz)*/

		/*[begin] group integration (modzzz)*/
		if(getParam('bx_groups_modzzz_news')=='on'){ 
			$sGroupLink = BxDolModule::getInstance('BxGroupsModule')->getGroupLink($aData['group_id']);
		}
	    /*[end] group integration (modzzz)*/

		/*[begin] band integration (modzzz)*/
		if(getParam('modzzz_bands_modzzz_news')=='on'){ 
			$sBandLink = BxDolModule::getInstance('BxBandsModule')->getBandLink($aData['band_id']);
		}
	    /*[end] band integration (modzzz)*/
 
		/*[begin] business integration (modzzz)*/
		if(getParam('modzzz_listing_modzzz_news')=='on'){ 
			$sBusinessLink = BxDolModule::getInstance('BxListingModule')->getListingLink($aData['business_id']);
		}
	    /*[end] business integration (modzzz)*/



        $aVars = array (
			'id' => $aData['id'], 
		 
            'news_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'news_title' => $aData['title'],
            'fans_count' => $aData['fans_count'],
            'comments_count' => $aData['comments_count'], 
            'post_tags' => $this->_oMain->parseTags($aData['tags']), 
            
			'bx_if:event' => array (
				'condition' => (getParam('bx_events_modzzz_news')=='on' && $aData['event_id']),
				'content' => array (
					'event' => $sEventLink,
				),
			),

			'bx_if:group' => array (
				'condition' => (getParam('bx_groups_modzzz_news')=='on' && $aData['group_id']),
				'content' => array (
					'group' => $sGroupLink,
				),
			),

			'bx_if:school' => array (
				'condition' => (getParam('modzzz_schools_modzzz_news')=='on' && $aData['school_id']),
				'content' => array (
					'school' => $sSchoolLink,
				),
			),

			'bx_if:business' => array (
				'condition' => (getParam('modzzz_listing_modzzz_news')=='on' && $aData['business_id']),
				'content' => array (
					'business' => $sBusinessLink,
				),
			),

			'bx_if:band' => array (
				'condition' => (getParam('modzzz_bands_modzzz_news')=='on' && $aData['band_id']),
				'content' => array (
					'band' => $sBandLink,
				),
			),
 
			'category_name' => $aCategory['name'],
			'category_url' => $sCategoryUrl,

			'bx_if:subcategory' => array( 
				'condition' => $aData['category_id'],
				'content' => array(
					'subcategory_name' => $aSubCategory['name'], 
					'subcategory_url' => $sSubCategoryUrl,
				) 
			), 

			'bx_if:showmember' => array( 
				'condition' => $bShowMember,
				'content' => array(
					'post_uthumb' => $sOwnerThumb,
				) 
			), 

			'bx_if:showphoto' => array( 
				'condition' => (!$bShowMember),
				'content' => array(
					'news_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
					'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'),
				) 
			),  
 
          //  'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars, $aData['snippet']),
		  'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars, $aData['desc']),
             
            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['when']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ),	 
        );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    // ======================= ppage compose block functions 

    function blockDesc (&$aDataEntry) {
        $aVars = array (
            'description' => $aDataEntry['desc'],
        );
        return $this->parseHtmlByName('block_description', $aVars);
    }

    function blockFields (&$aDataEntry) {
        $sRet = '<table class="modzzz_news_fields">';
        modzzz_news_import ('FormAdd');        
        $oForm = new BxNewsFormAdd ($GLOBALS['oBxNewsModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
            $sRet .= '<tr><td class="modzzz_news_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_news_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display'])))
                $sRet .= call_user_func_array(array($this, $a['display']), array($aDataEntry[$k]));
            else
                $sRet .= $aDataEntry[$k];
            $sRet .= '<td></tr>';
        }
        $sRet .= '</table>';
        return $sRet;
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

}
