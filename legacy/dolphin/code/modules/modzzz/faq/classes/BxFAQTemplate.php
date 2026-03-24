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
bx_import('BxDolCategories');

/*
 * FAQ module View
 */
class BxFAQTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    
	/**
	 * Constructor
	 */
	function BxFAQTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }

    function unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxFAQModule');

        if (!$this->_oMain->isAllowedView ($aData)) {            
            $aVars = array ('extra_css_class' => 'modzzz_faq_unit');
            return '';//$this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        modzzz_faq_import('Voting');
        $oRating = new BxFAQVoting ('modzzz_faq', $aData['id']);
 
		$sDateTime = defineTimeInterval($aData['created']); 
		$iHelpful = (int)$aData['rate_up'];
 
		$iLimitChars = (int)getParam('modzzz_faq_snippet_length');

        $aVars = array (
            'id' => $aData['id'], 
            'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars, $aData['snippet']),
            		 
			'author' => getNickName($aData['author_id']),
			'author_url' => getProfileLink($aData['author_id']),
			'created' => defineTimeInterval($aData['created']),
			'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
 
            'faq_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'faq_title' => $aData['title'],
			'found_helpful' => sprintf(_t('_modzzz_faq_found this helpful'), $iHelpful), 
            'comments_count' => $aData['comments_count'],
            'all_categories' => $this->_oMain->parseCategories($aData['categories']),  
            'post_tags' => $this->_oMain->parseTags($aData['tags']),  
        );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    // ======================= ppage compose block functions 

    function blockDesc (&$aDataEntry) {
        $aVars = array (
            'title' => $aDataEntry['title'],
            'description' => $aDataEntry['desc'],
        );
        return $this->parseHtmlByName('block_description', $aVars);
    }

    function blockFields (&$aDataEntry) {
        $sRet = '<table class="modzzz_faq_fields">';
        modzzz_faq_import ('FormAdd');        
        $oForm = new BxFAQFormAdd ($GLOBALS['oBxFAQModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
            $sRet .= '<tr><td class="modzzz_faq_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_faq_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display'])))
                $sRet .= call_user_func_array(array($this, $a['display']), array($aDataEntry[$k]));
            else
                $sRet .= $aDataEntry[$k];
            $sRet .= '<td></tr>';
        }
        $sRet .= '</table>';
        return $sRet;
    }
 


}
