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

bx_import('BxDolTwigModuleDb');
 
/*
 *  module Data
 */
class BxManagerDb extends BxDolTwigModuleDb {	

	var $_oConfig;

	/*
	 * Constructor.
	 */
	function BxManagerDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);
		$this->_oConfig = $oConfig;

		$this->_sTableFans = '';  
        $this->_sTableArchive = 'archive';  
        $this->_sFieldId = 'ID'; 
	}
  
 
    function getArchiveById ($iId) {
    	$iId = (int)$iId;
 
        return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableArchive . "` WHERE `{$this->_sFieldId}` = $iId LIMIT 1");
    }
 
    function deleteArchiveById ($iId) {
 
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableArchive . "` WHERE `{$this->_sFieldId}` = $iId LIMIT 1")))
            return false;
 
        return true;
    }
 
	function archiveEntry($iId) {
		$iId = (int)$iId;
		$iTime = time();

        $bSuccess = $this->query ("INSERT INTO `" . $this->_sPrefix . $this->_sTableArchive . "` SELECT *, '$iTime' FROM `sys_objects_actions` WHERE `{$this->_sFieldId}` = $iId");
		
		if($bSuccess) {
			$this->deleteEntryById($iId);
		}

		$this->cleanCache('sys_objects_actions');   
	}

    function getEntryById ($iId ) {
   		$iId = (int)$iId;
  
        return $this->getRow ("SELECT * FROM `sys_objects_actions` WHERE `{$this->_sFieldId}` = $iId  LIMIT 1");
    }

    function deleteEntryById ($iId, $iOwner=0, $isAdmin=0) {
 		$iId = (int)$iId;

        if (!($iRet = $this->query ("DELETE FROM `sys_objects_actions` WHERE `{$this->_sFieldId}` = $iId LIMIT 1")))
            return false;
 
 		$this->cleanCache('sys_objects_actions');   

        return true;
    }        
  
    function activateEntry ($iId) {
 		$iId = (int)$iId;

        $bSuccess = $this->query ("INSERT INTO `sys_objects_actions` (`Caption`,`Icon`,`Url`,`Script`,`Eval`,`Order`,`Type`,`bDisplayInSubMenuHeader`) SELECT `Caption`,`Icon`,`Url`,`Script`,`Eval`,`Order`,`Type`,`bDisplayInSubMenuHeader` FROM `" . $this->_sPrefix . $this->_sTableArchive . "` WHERE `{$this->_sFieldId}` = $iId");
		
		if($bSuccess) {
			$this->deleteArchiveById($iId);
		}

		$this->cleanCache('sys_objects_actions');   
	}

    function getFormUnits () {
		return $this->getPairs ("SELECT DISTINCT `Type` FROM `sys_objects_actions`", 'Type', 'Type'); 
	}

    function getUnits () {
		return $this->getAll ("SELECT DISTINCT `Type` FROM `sys_objects_actions`"); 
	}

    function sortActions($sType, $iEntryId, $iOrder) {
 		$iEntryId = (int)$iEntryId;
 		$iOrder = (int)$iOrder;

		$bResult = $this->query ("UPDATE `sys_objects_actions` SET `Order`=$iOrder WHERE `Type` = '$sType' AND `{$this->_sFieldId}`=$iEntryId");  
		
		$this->cleanCache('sys_objects_actions'); 
		
		return $bResult;
	}

    function cleanCache ($sName)
    {
        //if (!getParam('sys_db_cache_enable'))
        //    return true;

        $oCache = $GLOBALS['MySQL']->getDbCacheObject ();

        $sKey = $GLOBALS['MySQL']->genDbCacheKey($sName);

        return $oCache->delData($sKey);
    }



}
