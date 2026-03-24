<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx JobsCover
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
 * JobsCover module Data
 */
class BxJobsCoverDb extends BxDolTwigModuleDb {	

	/*
	 * Constructor.
	 */
	function BxJobsCoverDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);
		 
		$this->_sTableFans = '';
		$this->_sPrefix = 'modzzz_jobscover_';
	}

    function deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin)
    {
		$iId = (int)$iId;

        $sWhere = '';
        if (!$isAdmin)
            $sWhere = " AND `{$this->_sFieldAuthorId}` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `{$this->_sFieldId}` = $iId $sWhere LIMIT 1")))
            return false;

        $this->deleteEntryMediaAll ($iId, 'images');
 
        return true;
    }
 
    function deleteEntryMedia ($iId, $iMediaId, $sMediaType='images')
    {   
		$iId = (int)$iId;

		if(!$iId) return;

		BxDolService::call(('images' == $sMediaType ? 'photos' : $sMediaType), 'remove_object', array($iMediaId));
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableMediaPrefix . "{$sMediaType}` WHERE `media_id` = '$iMediaId'");
    
		$aDataEntry = $this->getEntryById($iId);
 		
		$oModule = BxDolModule::getInstance('BxJobsModule');
		$aModuleEntry = $oModule->_oDb->getEntryById($aDataEntry['jobs_id']);
 
		if($aModuleEntry['cover_id']==$iMediaId){
			$this->query ("UPDATE `modzzz_jobs_main` SET `cover_id`='' WHERE `id` = ". (int)$aDataEntry['jobs_id']); 
		}

        if(!$bExists = $this->getOne ("SELECT `entry_id` FROM `" . $this->_sPrefix . $this->_sTableMediaPrefix . "{$sMediaType}` WHERE `entry_id` = '$iId' LIMIT 1")){
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `{$this->_sFieldId}` = $iId");
		} 
	}

 	function initialize (){
		global $db;

		$fields = mysql_list_fields($db['db'], "modzzz_jobscover_main"); 
		$columns = mysql_num_fields($fields);
			   
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
			   
		if (!in_array('jobs_id', $field_array)) {
			db_res("ALTER TABLE `modzzz_jobscover_main` ADD `jobs_id` int(10) NOT NULL default '0'");
		} 
 
		$fields = mysql_list_fields($db['db'], "modzzz_jobs_main"); 
		$columns = mysql_num_fields($fields);
			   
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
		 
		if (!in_array('cover_id', $field_array)) {
			db_res("ALTER TABLE `modzzz_jobs_main` ADD `cover_id` int(10) NOT NULL default '0'");
		}  
	}

	function cleanup (){
		global $db;

		$fields = mysql_list_fields($db['db'], "modzzz_jobscover_main"); 
		$columns = mysql_num_fields($fields);
			   
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
			   
		if (in_array('jobs_id', $field_array)) {
			db_res("ALTER TABLE `modzzz_jobscover_main` DROP `jobs_id`");
		} 

		$fields = mysql_list_fields($db['db'], "modzzz_jobs_main"); 
		$columns = mysql_num_fields($fields);
			   
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}

		if (in_array('cover_id', $field_array)) {
			db_res("ALTER TABLE `modzzz_jobs_main` DROP `cover_id`"); 
		} 
 
		db_res("UPDATE `sys_page_compose` SET `Column`=`Column`-1 WHERE `Page`='modzzz_jobs_view' AND `Column`!=0");
	}

	function getCoverMediaById ($iMediaId){
		$iMediaId = (int)$iMediaId;
  
        return $this->getRow ("SELECT m.`jobs_id`, i.`entry_id`, i.`media_id` FROM `" . $this->_sPrefix . "main` m, `" . $this->_sPrefix . "images` i WHERE i.`entry_id`=m.`id` AND i.`media_id` = $iMediaId");
	}
 
	function activateCover ($iId, $iCoverId){
		$iId = (int)$iId;
		$iCoverId = (int)$iCoverId;
 
        $this->query ("UPDATE `modzzz_jobs_main` SET `cover_id`=$iCoverId WHERE `id` = $iId");
	}
 
    function deleteCoverByIdAndOwner ($iId, $iJobsId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `id` = $iId AND `jobs_id`=$iJobsId $sWhere LIMIT 1")))
            return false;
        $this->query ("DELETE FROM `" . $this->_sPrefix . "images` WHERE `entry_id` = $iId");
  
        return true;
    } 

    function deleteCovers ($iEntryId) {
 		$iEntryId = (int)$iEntryId;
 
		$aCover = $this->getAllJobsCovers($iEntryId);

		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `jobs_id`=$iEntryId")))
            return false;

		foreach($aCover as $aEachCover){
			
			$iId = (int)$aEachCover['id'];

			$this->deleteEntryMediaAll ($iId, 'images'); 
 		}

        return true;
    }

	function hasCovers($iId){
		$iId = (int)$iId;
  
        return $this->getOne ("SELECT COUNT(i.`entry_id`) FROM `" . $this->_sPrefix . "main` m, `" . $this->_sPrefix . "images` i WHERE i.`entry_id`=m.`id` AND m.`jobs_id` = $iId");
	}
 
	function getAllJobsCovers($iJobsId){
		$iJobsId = (int)$iJobsId;
 
		return $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `jobs_id`=$iJobsId");
	}
 
	function removeCover($sUri){
		$this->query ("UPDATE `modzzz_jobs_main` SET `cover_id`='' WHERE `uri` = '$sUri'");
	} 

}
