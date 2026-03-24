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

bx_import('BxDolTwigModuleDb');

/*
 * FAQ module Data
 */
class BxFAQDb extends BxDolTwigModuleDb {	

	/*
	 * Constructor.
	 */
	function BxFAQDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);

		$this->_sTableFans = '';
		$this->_sTableMain = 'main';
        $this->_sTableRateHelp = 'ratehelp'; 
        $this->_sTableMediaPrefix = '';
        $this->_sFieldId = 'id';
		$this->_sAuthorId = 'author_id';
        $this->_sFieldUri = 'uri';
        $this->_sFieldTitle = 'title';
        $this->_sFieldDescription = 'desc';
        $this->_sFieldTags = 'tags';
        $this->_sFieldThumb = 'thumb';
        $this->_sFieldStatus = 'status';
        $this->_sFieldFeatured = 'featured';
        $this->_sFieldCreated = 'created';
        $this->_sTableAdmins = 'admins';
        $this->_sFieldAllowViewTo = 'allow_view_faq_to';
        $this->_sFieldRateUp = 'rate_up';
        $this->_sFieldRateDown = 'rate_down';


	}
 
    function rateHelp ($iId, $sAction, $iProfileId) {
    
		$sFieldName = ($sAction=="up") ? "`{$this->_sFieldRateUp}`" : "`{$this->_sFieldRateDown}`";
 
		if(!$this->rateHelpAlready($iId, $iProfileId))
			$this->query ("INSERT INTO `" . $this->_sPrefix . $this->_sTableRateHelp . "` SET `{$this->_sFieldId}` = $iId, `{$this->_sAuthorId}` = $iProfileId");
 
		return $this->query ("UPDATE `" . $this->_sPrefix . $this->_sTableMain . "` SET {$sFieldName} = {$sFieldName} + 1 WHERE `{$this->_sFieldId}` = $iId LIMIT 1");
    }

	function rateHelpAlready($iId, $iProfileId) {
		$bRated = $this->getOne ("SELECT `{$this->_sFieldId}` FROM `" . $this->_sPrefix . $this->_sTableRateHelp . "` WHERE `{$this->_sFieldId}` = $iId AND `{$this->_sAuthorId}` = $iProfileId LIMIT 1");

		return $bRated;
	}
 
	function getCategories($sType)
	{ 
 		$aAllEntries = $this->getAll("SELECT DISTINCT `Category` FROM `sys_categories` WHERE `Type` = '{$sType}' AND `Status`='active' ORDER BY `Category`"); 
		
		return $aAllEntries; 
	}

	function getCategoryCount($sType,$sCategory)
	{ 
		$sCategory = process_db_input($sCategory);
 
		$iNumCategory = $this->getOne("SELECT count(`" . $this->_sPrefix . "main`.`id`) FROM `" . $this->_sPrefix . "main`  inner JOIN `sys_categories` ON `sys_categories`.`ID`=`" . $this->_sPrefix . "main`.`id` WHERE 1 AND  `sys_categories`.`Category` IN('{$sCategory}') AND `sys_categories`.`Type` = '{$sType}' AND `" . $this->_sPrefix . "main`.`status`='approved'"); 
		
		return $iNumCategory;
	}

    function deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin) {
        if ($iRet = parent::deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin)) {
            $this->query ("DELETE FROM `" . $this->_sPrefix . "admins` WHERE `id_entry` = $iId");
            $this->deleteEntryMediaAll ($iId, 'images');
            $this->deleteEntryMediaAll ($iId, 'videos');
            $this->deleteEntryMediaAll ($iId, 'sounds');
            $this->deleteEntryMediaAll ($iId, 'files');
        }
        return $iRet;
    }
 
	function processDaily(){
 
         $iRandomFaq = (int)$this->getOne("SELECT `id` FROM `" . $this->_sPrefix . "main` WHERE `status`='approved' AND `id` NOT IN (SELECT `id` FROM `" . $this->_sPrefix . "sel_daily_items`) ORDER BY RAND() LIMIT 1"); 
 
		if($iRandomFaq){ 
			$this->query("INSERT INTO `" . $this->_sPrefix . "sel_daily_items` SET `id`='$iRandomFaq'"); 

			$this->query("UPDATE `" . $this->_sPrefix . "today_item` SET `id`='$iRandomFaq'"); 
		}else{
			$this->query("TRUNCATE TABLE `" . $this->_sPrefix . "sel_daily_items`"); 

			$iRandomFaq = (int)$this->getOne("SELECT `id` FROM `" . $this->_sPrefix . "main` WHERE `status`='approved' AND `id` NOT IN (SELECT `id` FROM `" . $this->_sPrefix . "today_item`) ORDER BY RAND() LIMIT 1"); 
			
			if($iRandomFaq){
				$this->query("INSERT INTO `" . $this->_sPrefix . "sel_daily_items` SET `id`='$iRandomFaq'"); 

				$this->query("UPDATE `" . $this->_sPrefix . "today_item` SET `id`='$iRandomFaq'"); 
			}
		}
	}


}
