<?php
/***************************************************************************
*
*     copyright            : (C) 2013 AQB Soft
*     website              : http://www.aqbsoft.com
*
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
*
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
*
* This notice may not be removed from the source code.
*
***************************************************************************/

bx_import('BxDolModuleDb');

class AqbAdvancedSearchFormDb extends BxDolModuleDb {
	/*
	 * Constructor.
	 */
	function __construct(&$oConfig) {
		parent::__construct($oConfig);
	}

	function getSearchFields() {
		return $this->getAll("SELECT `ID`, `Name` FROM `sys_profile_fields` WHERE (`Type` = 'select_one' OR `Type` = 'select_set') AND (`SearchSimpleBlock` > 0 OR `SearchQuickBlock` > 0 OR `SearchAdvBlock` > 0)");
	}

	function getFieldProperties($iFieldID) {
		$aField = $this->getRow("SELECT `ID`, `Name` FROM `sys_profile_fields` WHERE (`Type` = 'select_one' OR `Type` = 'select_set') AND (`SearchSimpleBlock` > 0 OR `SearchQuickBlock` > 0 OR `SearchAdvBlock` > 0) AND `ID` = {$iFieldID} LIMIT 1");
		if (!$aField) return false;

		$aFieldExtra = $this->getRow("SELECT `Control`, `ShowEmptyValue` FROM `{$this->_sPrefix}properties` WHERE `FieldID` = {$iFieldID} LIMIT 1");

		if ($aFieldExtra) $aField = array_merge($aField, $aFieldExtra);

		return $aField;
	}

	function saveProperties($iFieldID, $sControl, $bShowEmptyValue) {
		if ($sControl == 'default' && !$bShowEmptyValue) {
			$this->query("DELETE FROM `{$this->_sPrefix}properties` WHERE `FieldID` = {$iFieldID} LIMIT 1");
		} else {
			$this->query("REPLACE INTO `{$this->_sPrefix}properties` (`FieldID`, `Control`, `ShowEmptyValue`) VALUES({$iFieldID}, '{$sControl}', {$bShowEmptyValue})");
		}
	}

	function getFields2Override() {
		return $this->getAllWithKey("
			SELECT `sys_profile_fields`.`Name`, `{$this->_sPrefix}properties`.`Control`, `{$this->_sPrefix}properties`.`ShowEmptyValue`
			FROM `sys_profile_fields`
			JOIN `{$this->_sPrefix}properties`
			ON `sys_profile_fields`.`ID` = `{$this->_sPrefix}properties`.`FieldID`
		", 'Name');
	}
}