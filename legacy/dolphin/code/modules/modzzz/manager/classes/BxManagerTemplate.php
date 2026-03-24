<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Confession
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
 * Manager module View
 */
class BxManagerTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    
	/**
	 * Constructor
	 */
	function BxManagerTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }
  
    function unit ($aData, $sTemplateName) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxManagerModule');
   
        $aVars = array (            
            'id' => $aData['ID'], 
            'edit_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create/' . $aData['ID'],
            'remove_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'delete/' . $aData['ID'],
            'archive_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'archive/' . $aData['ID'], 
            'caption' => $aData['Caption'], 
            'icon' => $aData['Icon'], 
            'type' => $aData['Type'], 
            'order' => $aData['Order'], 
         ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
     }
 
     function archive_unit ($aData, $sTemplateName) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxManagerModule');
   
        $aVars = array (            
            'id' => $aData['ID'],  
            'caption' => $aData['Caption'], 
            'icon' => $aData['Icon'], 
            'type' => $aData['Type'], 
            'date' => date('M d, Y', $aData['Date']), 
         ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
     }
   
   
   
}
