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

class AqbGstDb extends BxDolModuleDb {	
	/*
	 * Constructor.
	 */
	function AqbGstDb(&$oConfig) {
		parent::BxDolModuleDb($oConfig);

		$this->_oConfig = &$oConfig;
	}

	function getGuests($iUserId, $sDuration, $iStart, $iPerPage) {
		$sWhereClause = "";

		switch($sDuration) {
			case AQB_GST_DURATION_TODAY:
				$sWhereClause = " AND `ts`>" . mktime(0, 0, 0, date('m'), date('d'), date('Y'));
				break;

			case AQB_GST_DURATION_WEEK:
				$iWeekDay = date('w');

				$iUtsMonday = time() - 86400 * (($iWeekDay != 0 ? $iWeekDay : 7) - 1);
				$sWhereClause = " AND `ts`>" . mktime(0, 0, 0, date('m', $iUtsMonday), date('d', $iUtsMonday), date('Y', $iUtsMonday));
				break;

			case AQB_GST_DURATION_ALL:
				break;
		}

		$sSql = "SELECT SQL_CALC_FOUND_ROWS `viewer` FROM `sys_profile_views_track` WHERE `id`='" . $iUserId . "' AND `viewer`<>'0'" . $sWhereClause . " ORDER BY `ts` DESC LIMIT " . $iStart . ", " . $iPerPage;
		return array(
			'ids' => $this->getColumn($sSql),
			'count' => (int)$this->getOne("SELECT FOUND_ROWS()")
		);
	}
}
?>