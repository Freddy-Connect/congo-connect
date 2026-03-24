<?php
/***************************************************************************
*
*     copyright            : (C) 2016 AQB Soft
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

class AqbConditionalFieldsDb extends BxDolModuleDb {
	/*
	 * Constructor.
	 */
	function __construct(&$oConfig) {
		parent::__construct($oConfig);
	}

	function addConditionalField($sField, $sDependsOn, $sValue) {
		$sField = process_db_input($sField);
		$sDependsOn = process_db_input($sDependsOn);
		$sValue = process_db_input($sValue);
		$this->query("INSERT IGNORE INTO `{$this->_sPrefix}data` SET `field` = '{$sField}', `depends_on` = '{$sDependsOn}', `show_if_value` = '{$sValue}'");
	}

	function remove($sField, $sDependsOn, $sValue) {
		$sField = process_db_input($sField);
		$sDependsOn = process_db_input($sDependsOn);
		$sValue = process_db_input($sValue);
		$this->query("DELETE FROM `{$this->_sPrefix}data` WHERE `field` = '{$sField}' AND `depends_on` = '{$sDependsOn}' AND `show_if_value` = '{$sValue}'");
	}

	function getConditionalFields() {
		$this->query("DELETE FROM `{$this->_sPrefix}data` WHERE `field` NOT IN (SELECT * FROM (SELECT `Name` FROM `sys_profile_fields`) as `names`)");
		$this->query("DELETE FROM `{$this->_sPrefix}data` WHERE `depends_on` NOT IN (SELECT * FROM (SELECT `Name` FROM `sys_profile_fields`) as `names`)");

		return $this->getAll("
			SELECT `{$this->_sPrefix}data`.`field`, `{$this->_sPrefix}data`.`depends_on`, `{$this->_sPrefix}data`.`show_if_value`, `pf1`.`type`, `pf2`.`Values` AS `values`
			FROM `{$this->_sPrefix}data`
			JOIN `sys_profile_fields` as `pf1` ON `pf1`.`Name` = `{$this->_sPrefix}data`.`field`
			JOIN `sys_profile_fields` as `pf2` ON `pf2`.`Name` = `{$this->_sPrefix}data`.`depends_on`
			ORDER BY `{$this->_sPrefix}data`.`field` ASC
		");
	}

	function getFieldsAndSections() {
		return $this->getPairs("
			SELECT `Name`, `NameUF` FROM (
				SELECT CONCAT('Section: ', `Name`) AS `NameUF`, `Name`, 1 AS `Order` FROM `sys_profile_fields` WHERE `Type` = 'block'
				UNION
				SELECT CONCAT('Field: ', `Name`) AS `NameUF`, `Name`, 2 AS `Order` FROM `sys_profile_fields` WHERE `Type` <> 'block'
			) AS `t`
			ORDER BY `Order` ASC, `Name` ASC
		", 'Name', 'NameUF');
	}

	function getSelectorFields() {
		return $this->getPairs("SELECT `Name` FROM `sys_profile_fields` WHERE `Type` = 'select_one'", 'Name', 'Name');
	}

	function getFieldValues($sField) {
		$aResult = array();
		if (!$sField) return $aResult;

		$sValues = $this->getOne("SELECT `Values` FROM `sys_profile_fields` WHERE `Name` = '".process_db_input($sField)."'");
		if (!$sValues) return $aResult;

		if (strncmp($sValues, '#!', 2) === 0) {
			$aValues = $GLOBALS['aPreValues'][substr($sValues, 2)];
			if ($aValues)
			foreach ($aValues as $mKey => $aData) $aResult[$mKey] = _t($aData['LKey']);
		} else {
			$aValues = explode("\n", $sValues);
			if ($aValues)
			foreach ($aValues as $mKey) $aResult[$mKey] = _t('_FieldValues_'.$mKey);
		}

		return $aResult;
	}

	function getBlockName($sBlockCaption) {
		$sPossibleName = $this->getOne("
			SELECT `Key`
			FROM `sys_localization_keys`
			WHERE
				`ID` IN (SELECT * FROM (SELECT DISTINCT `IDKey` FROM `sys_localization_strings` WHERE `String` = '".addslashes($sBlockCaption)."') as `keys`)
				AND `Key` LIKE '_FieldCaption_%'
			LIMIT 1
		");

		if (!$sPossibleName) return;
		return substr($sPossibleName, 14, -5);
	}
}