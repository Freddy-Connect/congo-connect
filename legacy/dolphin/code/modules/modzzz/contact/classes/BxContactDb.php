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
class BxContactDb extends BxDolTwigModuleDb {	

	var $_oConfig;

	/*
	 * Constructor.
	 */
	function BxContactDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);
		$this->_oConfig = $oConfig;

		$this->_sTableFans = '';   
	}
  
  	function getDepartmentById ($iId) {
		$iId = (int)$iId;

		return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `id`=$iId");
    } 

	function getFormDepartments () {
		return $this->getPairs ("SELECT `id`, `title` FROM `" . $this->_sPrefix . $this->_sTableMain . "`", 'id', 'title');
    } 

    function deleteEntryByIdAndOwner ($iId, $iOwner=0, $isAdmin=0)
    {
  
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `{$this->_sFieldId}` = $iId $sWhere LIMIT 1")))
            return false;
 
        return true;
    }
    
    function deleteEntryById ($iId) { 
 		$this->deleteEntryByIdAndOwner($iId);    
    }        
   
	function deleteType($iEntryId){
		global $db;

		$iEntryId = (int)$iEntryId;

		$sTypeLangKey = $this->getOne("SELECT `title` FROM `" . $this->_sPrefix . "action_types` WHERE `id`={$iEntryId} LIMIT 1");
			deleteStringFromLanguage($sTypeLangKey); 
 
		$aTypeActions = $this->getTypeActions($iEntryId);
		foreach($aTypeActions as $iKey=>$sLangKey){
			deleteStringFromLanguage($sLangKey); 
		}

		$this->query("DELETE FROM `" . $this->_sPrefix . "actions` WHERE `id_entry`={$iEntryId}");
		$this->query("DELETE FROM `" . $this->_sPrefix . "action_types` WHERE `id`={$iEntryId}");

		$fields = mysql_list_fields($db['db'],  $this->_sPrefix ."feedback_main"); 
		$columns = mysql_num_fields($fields);
			   
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
			   
		if (in_array('custom_type_'.$iEntryId, $field_array)) {
			$this->query("ALTER TABLE `" . $this->_sPrefix . "feedback_main` DROP `custom_type_{$iEntryId}`");
		} 
	}

	function getTypeData($iEntryId){
		$iEntryId = (int)$iEntryId;
		return $this->getRow("SELECT * FROM `" . $this->_sPrefix . "action_types` WHERE `id`={$iEntryId}");
	}
 
	function getActionName($iActionId){
		$iActionId = (int)$iActionId;
		$sName = $this->getOne ("SELECT `name` FROM `" . $this->_sPrefix . "actions` WHERE `id`={$iActionId} LIMIT 1"); 
		
		return _t($sName);
	}

	function getTypeActions($iEntryId){
		$iEntryId = (int)$iEntryId;

		return $this->getPairs ("SELECT `id`, `name` FROM `" . $this->_sPrefix . "actions`  WHERE `id_entry`={$iEntryId}", 'id', 'name'); 
 	}

    function getActionTypeIds () {
 
		return $this->getPairs ("SELECT `id`, `select_type` FROM `" . $this->_sPrefix . "action_types`", 'id', 'select_type');
    }

    function getActionTypes () {
 
		return $this->getAll ("SELECT `id`, `title`, `compulsory`, `field_name`, `select_type` FROM `" . $this->_sPrefix . "action_types`");
    }

	function getActions($iEntryId, $iLimit=0){

		if($iLimit)
			$sQuery = "LIMIT 0, {$iLimit}";

		return $this->getAll("SELECT `id`, `name` FROM `" . $this->_sPrefix . "actions` WHERE `id_entry`={$iEntryId} {$sQuery}");
	}
 
 	function addTypeToMain($iEntryId, $sType=''){

		$fields = mysql_list_fields($db['db'],  $this->_sPrefix ."feedback_main"); 
		$columns = mysql_num_fields($fields);
			   
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
			   
		if (!in_array('custom_type_'.$iEntryId, $field_array)) {

			$sFieldName = 'custom_type_'.$iEntryId;

			if($sType=='date'){
				$this->query("ALTER TABLE `" . $this->_sPrefix . "feedback_main` ADD `{$sFieldName}` int(11) NOT NULL");
			
			}elseif($sType=='multi-text'){
				$this->query("ALTER TABLE `" . $this->_sPrefix . "feedback_main` ADD `{$sFieldName}` text NOT NULL");
		
			}else{
				$this->query("ALTER TABLE `" . $this->_sPrefix . "feedback_main` ADD `{$sFieldName}` varchar(255) NOT NULL");
			}

			$this->query("UPDATE `" . $this->_sPrefix . "action_types` SET `field_name`='$sFieldName' WHERE `id`=$iEntryId");

		} 
	}

 	function addActions($iEntryId){

		if(is_array($_POST['tpaction'])){ 
			foreach($_POST['tpaction'] as $iKey=>$sValue){
			
				$sActionName = process_db_input($sValue);
 
				if(trim($sActionName)){  

					$iLangCategory = (int)$this->getOne("SELECT `ID` FROM  `sys_localization_categories` WHERE `Name` = 'Modzzz Contact' LIMIT 1");

					$this->query("INSERT INTO `" . $this->_sPrefix . "actions` SET `id_entry`=$iEntryId");
					$iLastId = $this->lastID(); 

					$sLangKey = '_modzzz_contact_custom_type_action_'.$iLastId; 
					addStringToLanguage($sLangKey, $sActionName, -1, $iLangCategory); 

					$this->query("UPDATE `" . $this->_sPrefix . "actions` SET `name`='$sLangKey' WHERE `id`=$iLastId"); 
				}
			} 
		}
	}
  
	function removeAction($iEntryId, $iActionId){ 
	    
		$sLangKey = '_modzzz_contact_custom_type_action_'.$iActionId;
		deleteStringFromLanguage($sLangKey);
		compileLanguage();

		$this->query("DELETE FROM `" . $this->_sPrefix . "actions` WHERE `id_entry`='$iEntryId' AND `id`='$iActionId'");   
	}

    function getActionIds ($iEntryId) {
		return $this->getPairs ("SELECT `id` FROM `" . $this->_sPrefix . "actions`  WHERE `id_entry` = '$iEntryId'", 'id', 'id');
    } 
   
	function getSelectType($sType=''){
		 $aTypes = array(
			'text' => _t('_modzzz_contact_type_text'),
			'multi-text' => _t('_modzzz_contact_type_textarea'),
			'date' => _t('_modzzz_contact_type_date'),
			'single-select' => _t('_modzzz_contact_type_single_select'),
			'multi-select' => _t('_modzzz_contact_type_multi_select'), 
		 );

		 return ($sType) ? $aTypes[$sType] : $aTypes;
	}
 

}