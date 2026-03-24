<?php
/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
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

class AqbPltDb extends BxDolModuleDb {	
	/*
	 * Constructor.
	 */
	function AqbPltDb(&$oConfig) {
		parent::BxDolModuleDb($oConfig);

		$this->_oConfig = &$oConfig;
	}

	function getProfiles($sType, $iUserId, $sDuration = AQB_PLT_DURATION_ALL, $iStart = 0, $iPerPage = 0) {
		if(empty($iPerPage))
			$iPerPage = $this->_oConfig->getPerPage();

		$sWhereClause = "";
		switch($sDuration) {
			case AQB_PLT_DURATION_TODAY:
				$sWhereClause = " AND `ts`>" . mktime(0, 0, 0, date('m'), date('d'), date('Y'));
				break;

			case AQB_PLT_DURATION_WEEK:
				$iWeekDay = date('w');

				$iUtsMonday = time() - 86400 * (($iWeekDay != 0 ? $iWeekDay : 7) - 1);
				$sWhereClause = " AND `ts`>" . mktime(0, 0, 0, date('m', $iUtsMonday), date('d', $iUtsMonday), date('Y', $iUtsMonday));
				break;

			case AQB_PLT_DURATION_ALL:
				break;
		}

		$sSql = '';
		switch($sType) {
			case AQB_PLT_TYPE_VIEWED_ME:
				$sSql = "SELECT SQL_CALC_FOUND_ROWS `viewer` FROM `sys_profile_views_track` WHERE `id`='" . $iUserId . "' AND `viewer`<>'0'" . $sWhereClause . " ORDER BY `ts` DESC LIMIT " . $iStart . ", " . $iPerPage;
				break;

			case AQB_PLT_TYPE_FAVORITED_ME:
				$sWhereClause = str_replace("`ts`", "`When`", $sWhereClause);
				$sSql = "SELECT SQL_CALC_FOUND_ROWS `ID` FROM `sys_fave_list` WHERE `Profile`='" . $iUserId . "'" . $sWhereClause . " ORDER BY `When` DESC LIMIT " . $iStart . ", " . $iPerPage;
				break;

			case AQB_PLT_TYPE_SUBSCRIBED_ME:
				$sSql = "SELECT SQL_CALC_FOUND_ROWS `te`.`subscriber_id` FROM `sys_sbs_entries` AS `te` LEFT JOIN `sys_sbs_types` AS `tt` ON `te`.`subscription_id`=`tt`.`id` WHERE `tt`.`unit`='profile' AND `tt`.`action`='' AND `te`.`subscriber_type`='1' AND `te`.`object_id`='" . $iUserId . "' ORDER BY `te`.`id` DESC LIMIT " . $iStart . ", " . $iPerPage;
				break;
		}

		if(empty($sSql))
			return array(
				'ids' => array(), 
				'count' => 0
			);

		return array(
			'ids' => $this->getColumn($sSql),
			'count' => (int)$this->getOne("SELECT FOUND_ROWS()")
		);
	}

	function getProfilesNew($sType, $iUserId, $iStart = 0, $iPerPage = 0) {
		if(empty($iPerPage))
			$iPerPage = $this->_oConfig->getPerPage();

		$sSql = '';
		switch($sType) {
			case AQB_PLT_TYPE_VIEWED_ME:
				$sSql = "SELECT SQL_CALC_FOUND_ROWS `viewer` FROM `sys_profile_views_track` WHERE `id`='" . $iUserId . "' AND `viewer`<>'0' AND `aqb_viewed`='0' ORDER BY `ts` DESC LIMIT " . $iStart . ", " . $iPerPage;
				break;

			case AQB_PLT_TYPE_FAVORITED_ME:
				$sSql = "SELECT SQL_CALC_FOUND_ROWS `ID` FROM `sys_fave_list` WHERE `Profile`='" . $iUserId . "' AND `aqb_viewed`='0' ORDER BY `When` DESC LIMIT " . $iStart . ", " . $iPerPage;
				break;

			case AQB_PLT_TYPE_SUBSCRIBED_ME:
				$sSql = "SELECT SQL_CALC_FOUND_ROWS `te`.`subscriber_id` FROM `sys_sbs_entries` AS `te` LEFT JOIN `sys_sbs_types` AS `tt` ON `te`.`subscription_id`=`tt`.`id` WHERE `tt`.`unit`='profile' AND `tt`.`action`='' AND `te`.`subscriber_type`='1' AND `te`.`object_id`='" . $iUserId . "' AND `te`.`aqb_viewed`='0' ORDER BY `te`.`id` DESC LIMIT " . $iStart . ", " . $iPerPage;
				break;
		}

		if(empty($sSql))
			return array(
				'ids' => array(), 
				'count' => 0
			);

		return array(
			'ids' => $this->getColumn($sSql),
			'count' => (int)$this->getOne("SELECT FOUND_ROWS()")
		);
	}

	function markAsViewed($sType, $iUserId) {
		$sSql = '';

		switch($sType) {
			case AQB_PLT_TYPE_VIEWED_ME:
				$sSql = "UPDATE `sys_profile_views_track` SET `aqb_viewed`='1' WHERE `id`='" . $iUserId . "' AND `viewer`<>'0' AND `aqb_viewed`='0'";
				break;

			case AQB_PLT_TYPE_FAVORITED_ME:
				$sSql = "UPDATE `sys_fave_list` SET `aqb_viewed`='1' WHERE `Profile`='" . $iUserId . "' AND `aqb_viewed`='0'";
				break;

			case AQB_PLT_TYPE_SUBSCRIBED_ME:
				$iSbsId = (int)$this->getOne("SELECT `id` FROM `sys_sbs_types` WHERE `unit`='profile' AND `action`='' LIMIT 1");

				$sSql = "UPDATE `sys_sbs_entries` SET `aqb_viewed`='1' WHERE `subscriber_type`='1' AND `subscription_id`='" . $iSbsId . "' AND `object_id`='" . $iUserId . "' AND `aqb_viewed`='0'";
				break;
		}

		return (int)$this->query($sSql) > 0;
	}
}
?>