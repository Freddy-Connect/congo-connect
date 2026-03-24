<?php
/***************************************************************************
*
*     copyright            : (C) 2017 AQB Soft
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

class AqbPYMLDb extends BxDolModuleDb {
	/*
	 * Constructor.
	 */
	function __construct(&$oConfig) {
		parent::__construct($oConfig);
	}

	function getFields() {
		$res = $this->getAll("
			SELECT `sys_profile_fields`.*, `{$this->_sPrefix}fields`.`type` AS `Match`
			FROM `sys_profile_fields`
			JOIN `{$this->_sPrefix}fields` ON `sys_profile_fields`.`ID` = `{$this->_sPrefix}fields`.`field_id`
			ORDER BY `Name` ASC
		");

		foreach ($res as $i => $aField) {
			if ($aField['Type'] == 'select_set' && !strpos('#!', 'Values')) $res[$i]['Values'] = explode("\n", $aField['Values']);
		}

		return $res;
	}

	function getPossibleFields() {
		return $this->getAll("SELECT `ID`, `Name` FROM `sys_profile_fields` WHERE `Type` NOT IN ('system', 'area', 'block') AND `Name` NOT IN ('Password', 'Email', 'EmailNotify') ORDER BY `Name` ASC");
	}

	function saveFields($aFields) {
		$this->query("TRUNCATE TABLE `{$this->_sPrefix}fields`");
		foreach ($aFields as $aField) {
			$this->query("REPLACE INTO `{$this->_sPrefix}fields` (`field_id`, `type`) VALUES ({$aField['field_id']}, '{$aField['type']}')");
		}
	}

	function getProfilesCount($iProfile) {
		$sWhere = $this->getMatchCondition($iProfile);
		return $this->getOne("SELECT COUNT(`ID`) FROM `Profiles` WHERE {$sWhere}");
	}

	function getProfiles($iProfile, $iStartFrom, $iPerPage, $iSeed) {
		$sWhere = $this->getMatchCondition($iProfile);
		return $this->getColumn("SELECT `ID` FROM `Profiles` WHERE {$sWhere} ORDER BY RAND({$iSeed}) LIMIT {$iStartFrom}, {$iPerPage}");
	}

	function getMatchCondition($iProfile) {
		static $result;
		if ($result) return $result;

		$aProfile = getProfileInfo($iProfile);

		$aWhere = array();
		$aWhere[] = '`Status` = \'Active\'';
		$aWhere[] = '(`Couple` = 0 OR `Couple` > `ID`)';
		$aWhere[] = "`ID` <> {$iProfile}";
		$aWhere[] = "`ID` NOT IN (SELECT * FROM (SELECT `ID` FROM `sys_friend_list` WHERE `Profile` = {$iProfile} UNION SELECT `Profile` FROM `sys_friend_list` WHERE `ID` = {$iProfile}) AS `friends`)";

		$aFields = $this->getFields();
		$aWhereMatch = array();
		if ($aFields) {
			foreach ($aFields as $aField) {
				$sProfileValue = $aProfile[$aField['Name']];
				if (!$sProfileValue) continue;

				if ($aField['Type'] == 'date') {
					$years = $this->getParam('aqb_pyml_date_diff');
					$sOperand = $aField['Match'] == 'MATCH' ? '<=' : '>';
					$aWhereMatch[] = "ABS(DATEDIFF(`{$aField['Name']}`, '{$sProfileValue}')) {$sOperand} 365*{$years}";
				} elseif ($aField['Type'] == 'select_set') {
					$aProfileValues = explode(',', $sProfileValue);
					$aWhereSet = array();
					foreach ($aProfileValues as $v) {
						$v = addslashes($v);
						$aWhereSet[] = "FIND_IN_SET('{$v}', `{$aField['Name']}`)";
					}
					$sWhereSet = '('.implode(' OR ', $aWhereSet).')';

					$aWhereMatch[] = $aField['Match'] == 'MATCH' ? $sWhereSet : 'NOT '.$sWhereSet;
				} else {
					$sOperand = $aField['Match'] == 'MATCH' ? '=' : '<>';
					$aWhereMatch[] = "`{$aField['Name']}` {$sOperand} '".addslashes($sProfileValue)."'";
				}
			}
		}

		if ($aWhereMatch) $result = implode(' AND ', array_merge($aWhere, $aWhereMatch));
		else $result = 'FALSE';

		return $result;
	}
}