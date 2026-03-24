<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx RssInfo
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
 * RssInfo module View
 */
class BxRssInfoTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    
	/**
	 * Constructor
	 */
	function BxRssInfoTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }
  
    function unit ($aData, $sTemplateName) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxRssInfoModule');

        if (!$this->_oMain->isAllowedView ($aData)) {            
            $aVars = array ('extra_css_class' => 'modzzz_rssinfo_unit');
            return '';
        }
  
        $aVars = array (
			'id' => $aData['id'],  
            'rssinfo_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'edit/' . $aData['id'],
            'rssinfo_title' => $aData['title'],
            'rssinfo_link' => $aData['link'],
            'created' => date('M d, Y g:i A', $aData['created']),
            'updated' => date('M d, Y g:i A', $aData['updated']),
         );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
}