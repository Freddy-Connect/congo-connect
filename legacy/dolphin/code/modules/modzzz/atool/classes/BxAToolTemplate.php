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
 * ATool module View
 */
class BxAToolTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    
	/**
	 * Constructor
	 */
	function BxAToolTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }
  
    function sitemap_unit ($aData, $sTemplateName) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxAToolModule');
   
        $aVars = array (            
            'id' => $aData['id'], 
            'edit_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitemap/manage/' . $aData['id'],
            'remove_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'sitemap_delete/' . $aData['id'],
            'archive_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'sitemap_archive/' . $aData['id'], 
            'caption' => _t($aData['title']), 
            'order' => $aData['order'], 
         ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
     }
 
     function archive_sitemap_unit ($aData, $sTemplateName) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxAToolModule');
   
        $aVars = array (            
            'id' => $aData['id'],  
            'caption' => _t($aData['title']), 
            'date' => date('M d, Y', $aData['date']), 
         ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
     }
  
     function stat_unit ($aData, $sTemplateName) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxAToolModule');
   
        $aVars = array (            
            'id' => $aData['ID'], 
            'edit_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitestat/add/' . $aData['ID'],
            'remove_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'stat_delete/' . $aData['ID'],
            'archive_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'stat_archive/' . $aData['ID'], 
            'caption' => $aData['Title'], 
            'icon' => $aData['IconName'], 
            'order' => $aData['StatOrder'], 
         ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
     }
 
     function archive_stat_unit ($aData, $sTemplateName) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxAToolModule');
   
        $aVars = array (            
            'id' => $aData['ID'],  
            'caption' => $aData['Title'], 
            'icon' => $aData['IconName'], 
            'date' => date('M d, Y', $aData['Date']), 
         ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
     }
 
     function comment_unit ($aData, $sTemplateName) {
 
        $aVars = array (            
            'id' => $aData['cmt_id'], 
            'message' => $aData['cmt_text'], 
            'author' => getNickName($aData['cmt_author_id']), 
            'author_url' => getProfileLink($aData['cmt_author_id']),
            'recipient' => getNickName($aData['cmt_object_id']), 
            'recipient_url' => getProfileLink($aData['cmt_object_id']),  
            'date' => $aData['cmt_time'], 
         ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
     }

    function status_unit ($aData, $sTemplateName) {
 
        $aVars = array (            
            'id' => $aData['ID'], 
            'status' => $aData['UserStatusMessage'], 
            'owner' => getNickName($aData['ID']), 
            'owner_url' => getProfileLink($aData['ID']), 
            'date' => date('M d, Y', $aData['UserStatusMessageWhen']), 
         ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
     }

    function message_unit ($aData, $sTemplateName) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxAToolModule');
 
        $aVars = array (            
            'id' => $aData['ID'], 
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/message/respond/' . $aData['ID'],
            'subject' => $aData['Subject'], 
            'message' => $aData['Text'], 
            'author' => getNickName($aData['Sender']), 
            'author_url' => getProfileLink($aData['Sender']),
            'recipient' => getNickName($aData['Recipient']), 
            'recipient_url' => getProfileLink($aData['Recipient']), 
            'date' => $aData['Date'], 
         ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
     }

  
    function unit ($aData, $sTemplateName) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxAToolModule');
   
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
            $this->_oMain = BxDolModule::getInstance('BxAToolModule');
   
        $aVars = array (            
            'id' => $aData['ID'],  
            'caption' => $aData['Caption'], 
            'icon' => $aData['Icon'], 
            'type' => $aData['Type'], 
            'date' => date('M d, Y', $aData['Date']), 
         ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
     }
   
   /**
     * Insert/Delete CSS file from output stack.
     *
     * @param  string  $sType      the file type (css or js)
     * @param  string  $sAction    add/delete
     * @param  mixed   $mixedFiles string value represents a single CSS file name. An array - array of CSS file names.
     * @return boolean result of operation.
     */
    function _processFilesOLD($sType, $sAction, $mixedFiles, $bDynamic = false)
    {
        if(empty($mixedFiles))
            return $bDynamic ? "" : false;

        if(is_string($mixedFiles))
            $mixedFiles = array($mixedFiles);

        $sUpcaseType = ucfirst($sType);
        $sMethodLocate = '_getAbsoluteLocation' . $sUpcaseType;
        $sMethodWrap = '_wrapInTag' . $sUpcaseType;
        $sResult = '';
        foreach($mixedFiles as $sFile) {
            //--- Process 3d Party CSS/JS file ---//
            if(strpos($sFile, "http://") !== false || strpos($sFile, "https://") !== false) {
                $sUrl = $sFile;
                $sPath = $sFile;
            }
            //--- Process Custom CSS/JS file ---//
            else if(strpos($sFile, "|") !== false) {
                $sFile = implode('', explode("|", $sFile));
				
                $sUrl = BX_DOL_URL_ROOT . $sFile;
                $sPath = realpath(BX_DIRECTORY_PATH_ROOT . $sFile);
 				if($sType=='js')
					echo $sPath .":<br>";
            }
            //--- Process Common CSS/JS file(check in default locations) ---//
            else {
                $sUrl = $this->$sMethodLocate('url', $sFile);
                $sPath = $this->$sMethodLocate('path', $sFile);
            }



            if(empty($sPath) || empty($sUrl))
                continue;

            switch($sAction) {
                case 'add':
                    if($bDynamic)
                        $sResult .= $this->$sMethodWrap($sUrl);
                    else {
                        $bFound = false;
                        foreach($GLOBALS[$this->_sPrefix . $sUpcaseType]  as $iKey => $aValue)
                            if($aValue['url'] == $sUrl && $aValue['path'] == $sPath) {
                                $bFound = true;
                                break;
                            }

                        if(!$bFound)
                            $GLOBALS[$this->_sPrefix . $sUpcaseType][] = array('url' => $sUrl, 'path' => $sPath);
                    }
                    break;
                case 'delete':
                    if(!$bDynamic)
                        foreach($GLOBALS[$this->_sPrefix . $sUpcaseType]  as $iKey => $aValue)
                            if($aValue['url'] == $sUrl) {
                                unset($GLOBALS[$this->_sPrefix . $sUpcaseType][$iKey]);
                                break;
                            }
                    break;
            }
        }
		 
        return $bDynamic ? $sResult : true;
    }


   
}
