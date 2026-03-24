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

define('MEMBERSHIP_ID_NON_MEMBER', 1);
define('MEMBERSHIP_ID_STANDARD', 2);
define('MEMBERSHIP_ID_PROMOTION', 3);


class AqbAffDb extends BxDolModuleDb {	
	/*
	 * Constructor.
	 */
	
	function AqbAffDb(&$oConfig) {
		parent::BxDolModuleDb($oConfig);
		$this -> _oConfig = &$oConfig;
	}
	
	function isPointsSystemInstalled(){
		return (int)$this -> getOne("SELECT COUNT(*) FROM `sys_modules` WHERE `uri` = 'aqb_points' LIMIT 1") == 1 && $this -> getParam('aqb_affiliate_enable_points') == 'on'; 
	}
	
	function getReferralsNumber($iProfileId, $bType = 0, $isUpgraded = false, $bDirect = false){
		if ($isUpgraded) {
			$sJoin = "LEFT JOIN (SELECT COUNT(*) as `count`, `IDMember` FROM `sys_acl_levels_members` WHERE `IDLevel` > 3 GROUP BY `IDMember`) as `tml` ON `tml`.`IDMember` = `{$this->_sPrefix}referrals`.`member` ";
			$sSql  = " AND `tml`.`count` IS NOT NULL";			
		}
		
		if ($bType) $sSql .= " AND `type` <> 0"; elseif ($bType === false) $sSql .= " AND `type` = 0";
		
		if ($this -> isForcedMatrixEnabled($iProfileId) && !$bDirect){ 
			$aMemberRange = $this -> getRow("SELECT `lft`, `rgt`, `root_id`,`level` FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iProfileId}' OR `root_id` = '{$iProfileId}' ORDER BY `level` ASC LIMIT 1");
			if (empty($aMemberRange)) return 0;
									
			$iMatrixDeep = (int)$this -> getForcedMatrixLevels($iProfileId) + (int)$aMemberRange['level']; 								
						
			return $this -> getOne("SELECT COUNT(*) FROM `{$this -> _sPrefix}matrix` 
									LEFT JOIN `{$this->_sPrefix}referrals` ON `{$this -> _sPrefix}matrix`.`member` = `{$this -> _sPrefix}referrals`.`member`
									{$sJoin}
									WHERE `lft` > {$aMemberRange['lft']} AND `lft` < {$aMemberRange['rgt']} AND `root_id` = '{$aMemberRange['root_id']}' AND {$iMatrixDeep} >= `level`  {$sSql} LIMIT 1"); 
		}		
		
		return (int)$this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}referrals` {$sJoin} WHERE `referral` = '{$iProfileId}' {$sSql} LIMIT 1"); 
	}
	
	function getPriceForOneReferral($iMembershipId = 2, $sType = 'price'){
		$aValues = $this -> getRow("SELECT `referral_price`,`referral_points` FROM `{$this->_sPrefix}memlevels_pricing` WHERE `id_level` = '{$iMembershipId}' LIMIT 1"); 
		if ($sType == 'price') return (float)$aValues['referral_price'];
		return (int)$aValues['referral_points'];
	}
	
	function getPriceForUpgradedMember($iMembershipId = 2, $sType = 'price'){
		$aValues = $this -> getRow("SELECT `referral_upgrade_price` as `percentage`,`referral_upgrade_points` FROM `{$this->_sPrefix}memlevels_pricing` WHERE `id_level` = '{$iMembershipId}' LIMIT 1"); 
		if ($sType == 'price') return (int)$aValues['percentage'];
		return (int)$aValues['referral_upgrade_points'];
	}
	
	function getSentNumber($iMemberID){
		return (int)$this -> getOne("SELECT `count` FROM `{$this->_sPrefix}invitations` WHERE `owner_id` ='{$iMemberID}' AND `date` > UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 24 HOUR)) LIMIT 1");
	}
	
	function sendEmails($iMemberID, $iNum){
		$iCount = $this -> getSentNumber($iMemberID);
		
		if (!$iCount)
			return $this -> query("REPLACE INTO `{$this->_sPrefix}invitations` SET `owner_id` ='{$iMemberID}', `count` = '{$iNum}', `date` = UNIX_TIMESTAMP()");
		
		return $this -> query("UPDATE `{$this->_sPrefix}invitations` SET `count` = `count` + {$iNum}, `date` = UNIX_TIMESTAMP() WHERE `owner_id` ='{$iMemberID}'");
	}
	
	function addToQueue($sEmail, &$sBody, &$sSubject){
		return $this -> query("INSERT INTO `sys_sbs_queue`(`email`, `subject`, `body`) VALUES('{$sEmail}', '" . process_db_input($sSubject, BX_TAGS_STRIP) . "', '" . process_db_input($sBody, BX_TAGS_VALIDATE) . "')");
	}	
	
	function getPriceForAction($sAction = 'join', $sType = 'price', $iOwnerId, $iBannerId = 0){
		$aProfileMembershipInfo = $this -> getMemberMembershipInfo($iOwnerId);
		
		$bIsOnePrice = $this -> getParam('aqb_affiliate_use_one_price') == 'on';
		
		if($sAction == 'join' && (!(int)$iBannerId || $bIsOnePrice)) return $this -> getPriceForOneReferral((int)$aProfileMembershipInfo['ID'], $sType);	
		if($sAction == 'upgrade' && (!(int)$iBannerId || $bIsOnePrice)) return $this -> getPriceForUpgradedMember((int)$aProfileMembershipInfo['ID'], $sType);	
		return $this -> getPriceForBannersActions($iBannerId, $sType, $sAction);	
	}
	
	function getPriceForMembershipUpgrade($iMembershipId = 2, $sType = 'price'){
		$aValues = $this -> getRow("SELECT `referral_upgrade_price`,`referral_upgrade_points` FROM `{$this->_sPrefix}memlevels_pricing` WHERE `id_level` = '{$iMembershipId}' LIMIT 1"); 
		
		if ($sType == 'price') return (float)$aValues['referral_upgrade_price'];
		return (int)$aValues['referral_upgrade_points'];
	}
		
	function getBalance($iMemberId){
		$aResult = $this -> getRow("SELECT IF (FORMAT(SUM(`sum_price`),2) IS NULL, 0, FORMAT(SUM(`sum_price`),2)) as `price`, IF (SUM(`sum_points`) IS NULL, 0, SUM(`sum_points`)) as `points` FROM `{$this->_sPrefix}journal` WHERE `member_id` =  '{$iMemberId}' GROUP BY `member_id` LIMIT 1"); 
		if (empty($aResult)) return array('price' => 0, 'points' => 0);
		return $aResult;
	}
	
	function assignPoints($iMemberId, &$aAction){
		if (!(int)$aAction['points']) return false;
		
		if ($this -> query("INSERT INTO `aqb_points_history` SET `action_id` = '{$aAction['id']}', `profile_id` = '{$iMemberId}', `reason` = '{$aAction['title']}', `points` = '{$aAction['points']}', `time` = '{$aAction['time']}'")){	
			$iSum = (int)$this -> getOne("SELECT SUM(`points`) FROM `aqb_points_history` WHERE `profile_id` = '{$iMemberId}' LIMIT 1");
			if ($this -> query("UPDATE `Profiles` SET `AqbPoints`  = '{$iSum}' WHERE `id` = '{$iMemberId}'")) @unlink(BX_DIRECTORY_PATH_CACHE . 'user' . $iMemberId . '.php');			
			return true;
		}
		
		return false;	
	}

	function makeTransaction($iMemberId){
	   	$aPrice = $this -> getBalance($iMemberId);
		if (!(float)$aPrice['price'] && !(int)$aPrice['points']) return false;
		
		$aResult = $this -> getAll("SELECT `action_type`, SUM(`count`) as `count` FROM `{$this->_sPrefix}journal` WHERE `member_id` =  '{$iMemberId}' GROUP BY `action_type`");
		
		$sSql = '';
		foreach($aResult as $key => $aItem){
			$sSql .= "`{$aItem['action_type']}_num` = '{$aItem['count']}',";
		}
		
		if ($sSql) return $this -> query("INSERT INTO `{$this->_sPrefix}transactions` SET {$sSql} `member_id` = '{$iMemberId}', `points` = '{$aPrice['points']}', `price` = '{$aPrice['price']}',`date_start` = UNIX_TIMESTAMP(), `date_end` = UNIX_TIMESTAMP()");		
		return false;
	}
	
	function getPendingTransactions($iMemberId){
		return $this -> getRow("SELECT IF (FORMAT(SUM(`price`),2) IS NULL, 0, FORMAT(SUM(`price`),2))  as `price`, IF (SUM(`points`) IS NULL, 0, SUM(`points`)) as `points` FROM `{$this->_sPrefix}transactions` WHERE `member_id` = '{$iMemberId}' AND `status` = 'unpaid' LIMIT 1");
	}

	function getPaidTransactions($iMemberId){
		return $this -> getRow("SELECT IF (FORMAT(SUM(`price`),2) IS NULL, 0, FORMAT(SUM(`price`),2)) as `price`, IF (SUM(`points`) IS NULL, 0, SUM(`points`)) as `points` FROM `{$this->_sPrefix}transactions` WHERE `member_id` = '{$iMemberId}' AND `status` = 'paid' LIMIT 1");
	}	
	
	function clearJournal($iMemberId){
		return $this -> query("DELETE FROM `{$this->_sPrefix}journal` WHERE `member_id` =  '{$iMemberId}'");
	}	
	
	function getBannersInfo($iMemberID){
		$aDone = $this -> getRow("SELECT SUM(`click_num`) as `click`, SUM(`impression_num`) as `impression` FROM `{$this->_sPrefix}transactions` WHERE `member_id` = '{$iMemberID}' LIMIT 1");
		$aCurrent = $this -> getAll("SELECT SUM(`count`) as `count`, `action_type` FROM `{$this->_sPrefix}journal` WHERE `inviter_type` = 'banner' AND `member_id` = '{$iMemberID}' AND `action_type` NOT IN ('join','upgrade') GROUP BY `action_type`");
		
		foreach($aCurrent as $sKey => $aValue){
			$aDone[$aValue['action_type']] = (int)$aDone[$aValue['action_type']] + (int)$aValue['count'];	
		}
		
		return $aDone;
	}	
	
	function cleanOldHistory(){
		$iDay = $this -> _oConfig -> getUniqueHistoryDays(); 
		return $this -> query("DELETE FROM `{$this->_sPrefix}all_history` WHERE `date` <= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$iDay} DAY))");	
	}
	
	function createNewBanner($aInfo){
		$iWidth = (int)$aInfo['0'];
		$iHeight = (int)$aInfo['1'];
		$sImg = process_db_input($aInfo['img']);
		$sName = process_db_input($aInfo['name']);
		$sLink = $this -> _oConfig -> getBasePageUrl();		
		
		if (!($iWidth && $iHeight)) return false; 
		
		return $this -> query("INSERT INTO `{$this->_sPrefix}banners` SET `size` = '{$iWidth}x{$iHeight}', `img` = '{$sImg}',`name` = '{$sName}', `date` = UNIX_TIMESTAMP(), `link` = '{$sLink}'"); 
	}
	
	function updateBanner($aInfo){
		$iWidth = (int)$aInfo['0'] ? (int)$aInfo['0'] : $aInfo['width'];
		$iHeight = (int)$aInfo['1'] ? (int)$aInfo['1'] : $aInfo['height'];
		$sImg = process_db_input($aInfo['img']);
		$sName = process_db_input($aInfo['name']);
		$sLink = process_db_input($aInfo['link']);
		
		if ($iWidth && $iHeight) $sImage = ",`size` = '{$iWidth}x{$iHeight}'"; 
		if ($sImg) $sImage .= ", `img` = '{$sImg}'"; 
		
		return $this -> query("UPDATE `{$this->_sPrefix}banners` SET `name` = '{$sName}', `link` = '{$sLink}' {$sImage} WHERE `id` = '{$aInfo['id']}' LIMIT 1"); 
	}

	function getPriceForBannersActions($iBannerId, $sType = 'price', $sAction){
		$aRow = $this -> getRow("SELECT * FROM `{$this->_sPrefix}banners`  WHERE `id` = '{$iBannerId}' LIMIT 1"); 

		if (empty($aRow)) return 0;
		
		if ($sType == 'points') 
				return $aRow[$sAction . '_price_points'];
		return 	$aRow[$sAction . '_price'];	
	}	
	
	function addPriceInPoints(&$aInfo){
		$iImpPrice = (int)bx_get('impression_price_points');
		$iClickPrice = (int)bx_get('click_price_points');
		$iJoinPrice = (int)bx_get('join_price_points');
		$iUpgradePrice = (int)bx_get('upgrade_price_points');

		return $this -> query("UPDATE `{$this->_sPrefix}banners` SET `impression_price_points` = '{$iImpPrice}', `click_price_points` = '{$iClickPrice}', `join_price_points` = '{$iJoinPrice}', `upgrade_price_points` = '{$iUpgradePrice}' WHERE `id` = '{$aInfo['id']}' LIMIT 1"); 
	}

	function addPriceInCurrency(&$aInfo){
		$fImpPrice = (float)bx_get('impression_price');
		$fClickPrice = (float)bx_get('click_price');
		$fJoinPrice = (float)bx_get('join_price');
		$fUpgradePrice = (float)bx_get('upgrade_price');
		
		return $this -> query("UPDATE `{$this->_sPrefix}banners` SET `impression_price` = '{$fImpPrice}', `click_price` = '{$fClickPrice}', `join_price` = '{$fJoinPrice}', `upgrade_price` = '{$fUpgradePrice}' WHERE `id` = '{$aInfo['id']}' LIMIT 1"); 
	}
	
	function isBannerActive($iId){
		return $this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}banners` WHERE `active` = '1' AND `id` = '{$iId}' LIMIT 1") == 1;
	}
	
	function saveMemLevelsPricing(){
	  return $this -> query("REPLACE INTO `{$this->_sPrefix}memlevels_pricing` SET `id_level` = '".bx_get('id_level')."', `referral_price` = '".bx_get('referral_price')."', `referral_points` = '".bx_get('referral_points')."', `referral_upgrade_points` = '".bx_get('referral_upgrade_points')."', `referral_upgrade_price` = '".bx_get('referral_upgrade_price')."'");	   		
    }
	
	function updateMemLevelsPricing($iLevel){
	  $aInfo = $this -> getMemLevelsPricing($iLevel);
	  if (empty($aInfo)) return $this -> query("INSERT INTO `{$this->_sPrefix}memlevels_pricing` SET `invited_members` = '" . bx_get('invited_members') . "', `membership_level` = '" . bx_get('membership_level') . "', `id_level` = '{$iLevel}'");	   		
	  
	  return $this -> query("UPDATE `{$this->_sPrefix}memlevels_pricing` SET `invited_members` = '" . bx_get('invited_members') . "', `membership_level` = '" . bx_get('membership_level') . "' WHERE `id_level` = '{$iLevel}'");	   		
    }
	
	function getMemLevelsPricing($iId){
	 return $this -> getRow("SELECT * FROM `{$this->_sPrefix}memlevels_pricing` WHERE `id_level` = '{$iId}'");	   		
    }
	
	function getBanners($bActive = false){
		if ($bActive) $sSql = " AND `active` = '1'";
		return $this -> getAll("SELECT * FROM `{$this->_sPrefix}banners` WHERE 1 {$sSql} ORDER BY `date` DESC");
	}
	
	function getBanner($iID){
		return $this -> getRow("SELECT * FROM `{$this->_sPrefix}banners` WHERE `id` = '{$iID}' LIMIT 1");
	}

	function getCommissions($aParams){
	$sSelectClause = $sJoinClause = $sWhereClause = $sOrderBy = $sHaving = '';
	
	if (empty($aParams['view_order'])) $sOrderBy = '`tp`.`ID` ASC'; 
		else $sOrderBy = "`{$aParams['view_order']}` {$aParams['view_type']}"; 
   
   
	if (!empty($aParams['filter'])) $sProfile = "AND `tp`.`NickName` = '{$aParams['filter']}'";	
	
	if (!empty($aParams['commission']) && $aParams['commission'] != 'all') $sStatus = "AND `{$this->_sPrefix}transactions`.`status` = '{$aParams['commission']}'";		
	
	
	//--- Get Items ---//
    $sQuery = " 
			SELECT
    		`{$this->_sPrefix}transactions`.`id`,
			`tp`.`NickName` AS `username`,
			`tp`.`ID` AS `member_id`,
    		`tp`.`Sex` AS `sex`,
    		`tp`.`DateOfBirth` AS `date_of_birth`,
    		`tp`.`Country` AS `country`,
    		`tp`.`City` AS `city`,
    		`tp`.`DescriptionMe` AS `description`,
		    `tp`.`Email` AS `email`,
		   	DATE_FORMAT(`tp`.`DateReg`,  '" . $this -> _oConfig -> getDateFormat() . "' ) AS `registration`,
			DATE_FORMAT(FROM_UNIXTIME(`date_start`),  '" . $this -> _oConfig -> getDateFormat() . "' ) AS `date_start`,
			DATE_FORMAT(FROM_UNIXTIME(`date_end`),  '" . $this -> _oConfig -> getDateFormat() . "' ) AS `date_end`,
			IF (FORMAT(`{$this->_sPrefix}transactions`.`price`, 2) IS NULL, 0, FORMAT(`{$this->_sPrefix}transactions`.`price`,2)) as `price`,
			IF (`{$this->_sPrefix}transactions`.`points` IS NULL, 0, `points`) as `points`,
			IF (`{$this->_sPrefix}transactions`.`join_num` IS NULL, 0, `join_num`) as `join_num`,
			IF (`{$this->_sPrefix}transactions`.`click_num` IS NULL, 0, `click_num`) as `click_num`, 
			IF (`{$this->_sPrefix}transactions`.`impression_num` IS NULL, 0, `impression_num`) as `impression_num`,
			IF (`{$this->_sPrefix}transactions`.`upgrade_num` IS NULL,0, `upgrade_num`) as `upgrade_num`,
			`{$this->_sPrefix}transactions`.`status`,
			`{$this->_sPrefix}transactions`.`payment_status`
	    	FROM `{$this->_sPrefix}transactions` 
			LEFT JOIN `Profiles` AS `tp` ON `tp`.`ID` = `{$this->_sPrefix}transactions`.`member_id`
			WHERE (`tp`.`Couple`=0 OR `tp`.`Couple`>`tp`.`ID`) {$sProfile} {$sStatus} " . $sWhereClause . $sGroupClause . "
	    	ORDER BY  {$sOrderBy}
	    	LIMIT " . $aParams['view_start'] . ", " . $aParams['view_per_page'].'';

			$this -> _iMembersCount = (int)$this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}transactions` LEFT JOIN `Profiles` AS `tp` ON `{$this->_sPrefix}transactions`.`member_id` = `tp`.`ID`
															WHERE 1 {$sProfile} " . $sWhereClause);
		return $this -> getAll($sQuery);
	}
	
	function isPaymentInstalled($iMemberId){
		return (int)$this -> getOne("SELECT COUNT(*) FROM `bx_pmt_user_values` as `v` LEFT JOIN `bx_pmt_providers_options` as `o` ON `v`.`option_id` = `o`.`id` WHERE `user_id` = '{$iMemberId}' AND ((`name` = 'pp_active' AND `value` = 'on') OR (`name` = '2co_active' AND `value` = 'on')) LIMIT 1") > 0;
	}	
	
	function getMembers($aParams){
		$sSelectClause = $sJoinClause = $sWhereClause = $sOrderBy = $sHaving = '';
	
	if (empty($aParams['view_order'])) $sOrderBy = '`tp`.`ID` ASC'; 
		else $sOrderBy = "`{$aParams['view_order']}` {$aParams['view_type']}"; 
   
	if (!empty($aParams['filter'])) 
	{
		
		$matches = array();
		 
			$sWhereClause .= " AND (
	                `tp`.`NickName` LIKE '%" . $aParams['filter'] . "%' OR 
	                `tp`.`Email` LIKE '%" . $aParams['filter'] . "%' OR 
	                `tp`.`DescriptionMe` LIKE '%" . $aParams['filter'] . "%' OR 
	                `tp`.`Tags` LIKE '%" . $aParams['filter'] . "%' OR 
	                `tp`.`DateReg` LIKE '%" . $aParams['filter'] . "%')";
	   
	}	
	
	//--- Get Items ---//
    $sQuery = " 
			SELECT
    		`tp`.`ID` as `id`,
    		`tp`.`NickName` AS `username`,    		
    		`tp`.`Sex` AS `sex`,
    		`tp`.`DateOfBirth` AS `date_of_birth`,
    		`tp`.`Country` AS `country`,
    		`tp`.`City` AS `city`,
    		`tp`.`DescriptionMe` AS `description`,
		    `tp`.`Email` AS `email`,
		   	DATE_FORMAT(`tp`.`DateReg`,  '" . $this -> _oConfig -> getDateFormat() . "' ) AS `registration`,
    		DATE_FORMAT(`tp`.`DateLastLogin`,  '" . $this -> _oConfig-> getDateFormat() . "' ) AS `last_login`,
    		`tp`.`Status` AS `status`,
			IF (`ref`.`joins` IS NULL, 0, `ref`.`joins`) as `joins`, 
			IF (`upgrades`.`upgrades` IS NULL, 0, `upgrades`.`upgrades`) as `upgraded`, 
			(IFNULL(`trans`.`clicks`,0) + IFNULL(`journal_click`.`clicks`,0)) as `clicks`, 
			(IFNULL(`trans`.`impressions`,0) + IFNULL(`journal_impression`.`impressions`,0)) as `impressions`,
			IF (`transactions_paid`.`paid_price` IS NULL, 0, `transactions_paid`.`paid_price`) as paid_price,
			IF (`transactions_paid`.`paid_points`  IS NULL, 0, `transactions_paid`.`paid_points`) as paid_points,
			IF (`transactions_unpaid`.`unpaid_price`  IS NULL, 0, `transactions_unpaid`.`unpaid_price`) as unpaid_price, 
			IF (`transactions_unpaid`.`unpaid_points`  IS NULL, 0, `transactions_unpaid`.`unpaid_points`) as unpaid_points			
	    	FROM `Profiles` AS `tp`
			LEFT JOIN (SELECT COUNT(*) as `joins`, `referral` FROM `{$this->_sPrefix}referrals` GROUP BY `referral`) as `ref` ON `tp`.`ID` = `ref`.`referral`
			LEFT JOIN (
			SELECT `referral`, COUNT(*) as `upgrades` FROM `{$this->_sPrefix}referrals` 
			LEFT JOIN (SELECT `sys_acl_levels_members`.`IDMember`,
			UNIX_TIMESTAMP(`sys_acl_levels_members`.`DateStarts`) as `DateStarts`,
			UNIX_TIMESTAMP(`sys_acl_levels_members`.`DateExpires`) as `DateExpires`
			FROM `sys_acl_levels_members`	
			WHERE (`sys_acl_levels_members`.`DateStarts` IS NULL
								OR `sys_acl_levels_members`.`DateStarts` <= NOW())
							AND	(`sys_acl_levels_members`.`DateExpires` IS NULL
								OR `sys_acl_levels_members`.`DateExpires` > NOW())
			GROUP BY `sys_acl_levels_members`.`IDMember`
			ORDER BY `sys_acl_levels_members`.`DateStarts` DESC) as `tml` ON `tml`.`IDMember` = `{$this->_sPrefix}referrals`.`member` 
			WHERE `IDMember` IS NOT NULL
			GROUP BY `referral`) as `upgrades` ON `tp`.`ID` = `upgrades`.`referral`
			LEFT JOIN (SELECT SUM(`click_num`) as `clicks`, SUM(`impression_num`) as `impressions`, `member_id` FROM `{$this->_sPrefix}transactions` GROUP BY `member_id`) as `trans` ON `tp`.`ID` = `trans`.`member_id`
			LEFT JOIN (SELECT SUM(`count`) as `clicks`, `member_id` FROM `{$this->_sPrefix}journal` WHERE `action_type` = 'click' GROUP BY `member_id`) as `journal_click` ON `tp`.`ID` = `journal_click`.`member_id`
			LEFT JOIN (SELECT SUM(`count`) as `impressions`, `member_id` FROM `{$this->_sPrefix}journal` WHERE `action_type` = 'impression' GROUP BY `member_id`) as `journal_impression` ON `tp`.`ID` = `journal_impression`.`member_id`
			LEFT JOIN (SELECT FORMAT(SUM(`price`),2) as `paid_price`, SUM(`points`) as `paid_points`, `member_id`,`status` FROM `{$this->_sPrefix}transactions` WHERE `status` = 'paid' GROUP BY `member_id`) as `transactions_paid` ON `tp`.`ID` = `transactions_paid`.`member_id`
			LEFT JOIN (SELECT FORMAT(SUM(`price`),2) as `unpaid_price`, SUM(`points`) as `unpaid_points`, `member_id`,`status` FROM `{$this->_sPrefix}transactions` WHERE `status` = 'unpaid' GROUP BY `member_id`) as `transactions_unpaid` ON `tp`.`ID` = `transactions_unpaid`.`member_id`
		   	WHERE 1 AND (`tp`.`Couple`=0 OR `tp`.`Couple`>`tp`.`ID`) " . $sWhereClause . $sGroupClause . "
	    	ORDER BY  {$sOrderBy}
	    	LIMIT " . $aParams['view_start'] . ", " . $aParams['view_per_page'].'';

			$this -> _iMembersCount = (int)$this -> getOne("SELECT COUNT(`tp`.`ID`) FROM `Profiles` AS `tp` 
															WHERE 1 AND (`tp`.`Couple` = 0 OR `tp`.`Couple`>`tp`.`ID`)" . $sWhereClause);
		return $this -> getAll($sQuery);
	}
	
	function deleteProfileHistory($iId){
		$this -> query("DELETE FROM `{$this->_sPrefix}all_history` WHERE `owner_id` = '{$iId}'");
		$this -> query("DELETE FROM `{$this->_sPrefix}invitations` WHERE `owner_id` = '{$iId}'");
		$this -> query("DELETE FROM `{$this->_sPrefix}journal` WHERE `member_id` = '{$iId}'");
		$this -> query("DELETE FROM `{$this->_sPrefix}referrals` WHERE `referral` = '{$iId}' OR `member` = '{$iId}'");
		$this -> query("DELETE FROM `{$this->_sPrefix}transactions` WHERE `member_id` = '{$iId}'");		
		$this -> deleteNode($iId);
		return true;
	}
	
	function cleanHistory(){
		$this -> query("TRUNCATE TABLE `{$this->_sPrefix}all_history`");
		$this -> query("TRUNCATE TABLE `{$this->_sPrefix}invitations`");
		$this -> query("TRUNCATE TABLE `{$this->_sPrefix}journal`");
		$this -> query("TRUNCATE TABLE `{$this->_sPrefix}referrals`");
		$this -> query("TRUNCATE TABLE `{$this->_sPrefix}transactions`");		
		return true;
	}
	
	function deleteTransactionsItem($iItemId){
  	   $aInfo = $this -> getTransactionInfo($iItemId);
		
		if ($aInfo['tnx']){
			$sItems = "{$aInfo['member_id']}_" . $this -> getModuleId(). "_{$iId}_1"; 
			$aItems = $this -> getAll("SELECT `id` FROM `bx_pmt_transactions_pending` WHERE `items` = '{$sItems}'");
			
			if (!empty($aItems)){
				foreach($aItems as $iK => $aValue){
					$this -> query("DELETE FROM `bx_pmt_transactions` WHERE `pending_id` = '{$aValue['id']}'");
					$this -> query("DELETE FROM `bx_pmt_transactions_pending` WHERE `id` = '{$aValue['id']}'");
				}
			}
			
		}		

	 if ($this -> isPointsSystemInstalled()) $this -> query("DELETE FROM `aqb_points_history` WHERE `profile_id` = '{$aInfo['member_id']}' AND `action_id` = '0' AND `time` = '{$aInfo['date_end']}'");
	 return $this -> query("DELETE FROM `{$this->_sPrefix}transactions` WHERE `id` = '{$iItemId}'");
    }
  
	
	function getMemberMembershipInfo($memberID, $time = '')
	{
		$time = ($time == '') ? time() : (int)$time;

		$originalMembership = $this -> getmembermembershipinfo_current($memberID, $time);

		if(	$originalMembership['ID'] == MEMBERSHIP_ID_STANDARD ||
			$originalMembership['ID'] == MEMBERSHIP_ID_NON_MEMBER )
		{
			return $originalMembership;
		}

		$arrMembership = $originalMembership;

		do
		{
			$dateStarts = $arrMembership['DateStarts'];
			$arrMembership = $this -> getmembermembershipinfo_current($memberID, ((int)$dateStarts < 1 ? 0 : $dateStarts - 1));
		}
		while($arrMembership['ID'] == $originalMembership['ID'] && (int)$arrMembership['DateStarts']);

		$arrMembership = $originalMembership;

		do
		{
			$dateExpires = $arrMembership['DateExpires'];
			$arrMembership = $this -> getmembermembershipinfo_current($memberID, $dateExpires);
		}
		while($arrMembership['ID'] == $originalMembership['ID'] && (int)$arrMembership['DateExpires']);

		$originalMembership['DateStarts'] = $dateStarts;
		$originalMembership['DateExpires'] = $dateExpires;

		return $originalMembership;
	}
	
	function getmembermembershipinfo_current($memberID, $time = '')
   {
    $sCacheName = 'arrMemLevel'.$memberID.$time;
	$memberID = (int)$memberID;
	$time = ($time == '') ? time() : (int)$time;

	//fetch the last purchased/assigned membership
	//that is still active for the given member

    $arrMemLevel =& $this->fromMemory($sCacheName, 'getRow', "
		SELECT	`sys_acl_levels_members`.IDLevel as ID,
				`sys_acl_levels`.Name as Name,
				UNIX_TIMESTAMP(`sys_acl_levels_members`.DateStarts) as DateStarts,
				UNIX_TIMESTAMP(`sys_acl_levels_members`.DateExpires) as DateExpires,
                `sys_acl_levels_members`.`TransactionID` AS `TransactionID`
		FROM	`sys_acl_levels_members`
				RIGHT JOIN Profiles
				ON `sys_acl_levels_members`.IDMember = Profiles.ID
					AND	(`sys_acl_levels_members`.DateStarts IS NULL
						OR `sys_acl_levels_members`.DateStarts <= FROM_UNIXTIME($time))
					AND	(`sys_acl_levels_members`.DateExpires IS NULL
						OR `sys_acl_levels_members`.DateExpires > FROM_UNIXTIME($time))
				LEFT JOIN `sys_acl_levels`
				ON `sys_acl_levels_members`.IDLevel = `sys_acl_levels`.ID

		WHERE	Profiles.ID = $memberID

		ORDER BY `sys_acl_levels_members`.DateStarts DESC

		LIMIT 0, 1");

	//no such member found

    if (!$arrMemLevel || !count($arrMemLevel))
    {
		//fetch info about Non-member membership
        $arrMemLevel =& $this->fromCache('sys_acl_levels'.MEMBERSHIP_ID_NON_MEMBER, 'getRow', "SELECT ID, Name FROM `sys_acl_levels` WHERE ID = ".MEMBERSHIP_ID_NON_MEMBER);
        if (!$arrMemLevel || !count($arrMemLevel))
		{

			//this should never happen, but just in case
			echo "<br /><b>getMemberMembershipInfo()</b> fatal error: <b>Non-Member</b> membership not found.";
			exit();
		}

		return $arrMemLevel;
	}


	//no purchased/assigned memberships for the member or all of them
	//have expired -- the member is assumed to have Standard membership

	if(is_null($arrMemLevel['ID']))
	{
        $arrMemLevel =& $this->fromCache('sys_acl_levels'.MEMBERSHIP_ID_STANDARD, 'getRow', "SELECT ID, Name FROM `sys_acl_levels` WHERE ID = ".MEMBERSHIP_ID_STANDARD);
        if (!$arrMemLevel || !count($arrMemLevel))
		{
			//again, this should never happen, but just in case
			echo "<br /><b>getMemberMembershipInfo()</b> fatal error: <b>Standard</b> membership not found.";
			exit();
		}
	}

	 return $arrMemLevel;
	}
	
	function getMyJournalItems($iMemberId, $iStart, $PerPage){
		return $this -> getAll("SELECT * FROM `{$this->_sPrefix}journal` WHERE `member_id` = '{$iMemberId}' ORDER BY `last_update` DESC LIMIT {$iStart}, {$PerPage}");
	}

	function getMyJournalItemsCount($iMemberId){
		return (int)$this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}journal` WHERE `member_id` = '{$iMemberId}'");
	}
	
	function getBannersItems($aParams){
	$sSelectClause = $sJoinClause = $sWhereClause = $sOrderBy = $sHaving = '';
	
	if (empty($aParams['view_order'])) $sOrderBy = '`tp`.`ID` ASC'; 
		else $sOrderBy = "`{$aParams['view_order']}` {$aParams['view_type']}"; 
   
	if (!empty($aParams['filter_params'])) 
	{
	    $aFilterParams = preg_split('/[,]/', $aParams['filter_params'], -1, PREG_SPLIT_NO_EMPTY);
		
		if (is_array($aFilterParams))
		{
			$aFields = array();
			
			if (in_array('approved', $aFilterParams)) $aFields[]= "`b`.`active` = '{$aParams['filter']}'";
						
			if (count($aFields) > 0 ) $sWhereClause .= " AND (".implode(' OR ', $aFields).")"; 
		}
	}	
	
	$sQuery = " 
			SELECT * FROM `{$this -> _sPrefix}banners` 
		   	WHERE  1 " . $sWhereClause . $sGroupClause . "
	    	ORDER BY  {$sOrderBy}
	    	LIMIT " . $aParams['view_start'] . ", " . $aParams['view_per_page'].'';
		
		return $this -> getAll($sQuery);
	}
	
	function deleteBanner($iId){
		$aBanner = $this -> getBanner($iId);
		$bResult = $this -> query("DELETE FROM `{$this->_sPrefix}banners` WHERE `id` = '{$iId}'");
		return @unlink($this -> _oConfig -> getBannersPath() . $aBanner['img']) && $bResult;
	}
	
	function approveBanner($iId){
		return $this -> query("UPDATE `{$this->_sPrefix}banners` SET `active` = IF (`active` = '1','0','1') WHERE `id` = '$iId' LIMIT 1");
	}
	
	function hideMenu($sAction = 'hide', $sLink = 'referral'){
		$sSql = "`Visible` = 'memb'";
		if ($sAction == 'hide') $sSql = "`Visible` = ''";
			
		$sWhere = "`Caption` = '_aqb_aff_my_affilate'";
		if ($sLink == 'referral') $sWhere = "`Caption` = '_aqb_aff_my_referrals'";
		return $this -> query("UPDATE `sys_menu_top` SET {$sSql} WHERE {$sWhere}");
	}
	
	function seDefaultPage($sSql = 'referral'){
		switch($sSql){
			case 'referral':
				return $this -> query("UPDATE `sys_menu_top` SET `Link` = 'modules/?r=aqb_affiliate/referrals' WHERE `Caption` = '_aqb_aff_ref_title'");
			break;
			case 'affiliate':
				return $this -> query("UPDATE `sys_menu_top` SET `Link` = 'modules/?r=aqb_affiliate/affiliates' WHERE `Caption` = '_aqb_aff_ref_title'");
			break;
			case 'history':
				return $this -> query("UPDATE `sys_menu_top` SET `Link` = 'modules/?r=aqb_affiliate/history' WHERE `Caption` = '_aqb_aff_ref_title'");
			break;
		}
		return false;
	}	
	
	function approveCommission($iId){
		$aPrice = $this -> getRow("SELECT `member_id`, `points` FROM `{$this->_sPrefix}transactions` WHERE `id` = '{$iId}' and `status` = 'unpaid' LIMIT 1");
		if ($this -> isPointsSystemInstalled() && (int)$aPrice['points']){ 
		    $aAction = array('id' => 0, 'title' => '_aqb_aff_assign_points', 'points' => (int)$aPrice['points']);
			$this -> assignPoints((int)$aPrice['member_id'], $aAction);
		}
		return $this -> query("UPDATE `{$this->_sPrefix}transactions` SET `status` = 'paid' WHERE `id` = '{$iId}' LIMIT 1");
	}
	
	function deleteHistoryItem($iId){
		return $this -> query("DELETE FROM `{$this->_sPrefix}history` WHERE `id` = '{$iId}'");
	}
  
   
    function getMembershipLevels($iId = 0){
	    $sWhere = 'WHERE `ID` > 1';
		if ((int)$iId) $sWhere = "WHERE `ID` = '{$iId}'";
		
		return $this -> getAll("SELECT * FROM `sys_acl_levels` {$sWhere}");
	}
	
	function getMemPrice($iLevelId){
		return $this -> getRow("SELECT * FROM `{$this -> _sPrefix}memlevels_pricing` WHERE `id_level` = '{$iLevelId}' LIMIT 1");
	}
	
	function getSettingsCategory () {
        return (int)$this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'AQB Forced Matrix System' LIMIT 1");
    }
	
	function pointsIntegration(){
		$iId = (int)$this -> getOne("SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'aqb_points_assign_alert' LIMIT 1");
		
		if ($iId) {	
			$this -> query("REPLACE INTO `aqb_points_modules` VALUES ('aqb_affiliate', 'true')");
			$this -> query("INSERT INTO `sys_alerts` VALUES (NULL, 'aqb_affiliate', 'send', '{$iId}')");
			
			$this -> query("REPLACE INTO `aqb_points_actions`
															 SET 
															`title` = '_aqb_aff_action_aqb_aff_send',
															`description` = '',
															`handler` = 'send',
															`alerts_unit` = 'aqb_affiliate',
															`points` = '1',
															`day_limit` = '100',
															`active` = 'true',
															`module_uri` = 'aqb_affiliate',
															`param` = 'first',
															`group` = 'aqb_affiliate'");
															
		}
	}
	
	function uninstallPointsIntegration(){
		$this -> query("DELETE FROM `sys_alerts` WHERE `unit` = 'aqb_affiliate'");
		
		if ($this -> getOne("SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'aqb_points_assign_alert' LIMIT 1")) $this -> query("DELETE FROM `aqb_points_actions` WHERE `module_uri` = 'aqb_affiliate'");
	}	
	
	function getMatrixSettings(){
		$aSetting = array();
		$aSetting['enable'] = $this -> getParam('aqb_forced_matrix') == 'on';
		$aSetting['width'] = (int)$this -> getParam('aqb_matrix_width');
		
		$aIncame = explode(',', $this -> getParam('aqb_matrix_income'));
		$aSetting['income'] = array_values($aIncame);
		
		$aSetting['spillover'] = $this -> getParam('aqb_matrix_spillover');
		return $aSetting;
	}
	
	function setMatrixSettings(&$aParams){
		$bResult = $this -> setParam('aqb_forced_matrix',$aParams['enable']) &&
		$this -> setParam('aqb_matrix_width',$aParams['width']) &&
		$this -> setParam('aqb_matrix_spillover',$aParams['spillover']);		
		
		if (!empty($aParams['income']))
			$this -> setParam('aqb_matrix_income', implode(',', $aParams['income']));
		else $this -> setParam('aqb_matrix_income', ''); 	
	
		bx_import('BxDolAdminSettings');
		$oSettings = new BxDolAdminSettings(0);
		$oSettings -> _onSavePermalinks();
		
	    return $bResult;
	}
	
	
	function getMemLevelMatrixSettings($iMemLevel, $bPointsEnabled = false){
		$aSetting = array();
		
		$aParams = $this -> getRow("SELECT * FROM `{$this -> _sPrefix}memlevels_matrix` WHERE `id_level` = '{$iMemLevel}' LIMIT 1");
		
		if (empty($aParams)) return $aSetting;
				
		if ($bPointsEnabled){
			$aP = split('-', $aParams['points']);
			$aPoints = preg_split('/[;}{]/', $aP[0], -1, PREG_SPLIT_NO_EMPTY);
			$aPointsUpgrade = preg_split('/[;}{]/', $aP[1], -1, PREG_SPLIT_NO_EMPTY);
		}	
		
		$aL = split('-', $aParams['currency']);
		$aLevelsUpgrade = preg_split('/[;}{]/', $aL[1], -1, PREG_SPLIT_NO_EMPTY);
		$aLevels = preg_split('/[;}{]/', $aL[0], -1, PREG_SPLIT_NO_EMPTY);
					
	    $iNum = (int)$aParams['deep'];

	
		for($i = 0; $i < $iNum; $i++){
			$aLevel = split(':', $aLevels[$i]);
			$aLevelUpgrade = split(':', $aLevelsUpgrade[$i]);
			
			$aSetting['levels']['levels_' . $i . '_' . $iMemLevel] = (float)$aLevel[1];
			$aSetting['levels_upgrade']['levels_upgrade_' . $i . '_' . $iMemLevel] = (int)$aLevelUpgrade[1];
			
			if ($bPointsEnabled) {
				$aPoint = split(':', $aPoints[$i]);
				$aPointUpgrade = split(':', $aPointsUpgrade[$i]);
				
				$aSetting['points']['points_' . $i . '_' . $iMemLevel] = (int)$aPoint[1];
				$aSetting['points_upgrade']['points_upgrade_' . $i . '_' . $iMemLevel] = (int)$aPointUpgrade[1];
			}
	   }
	
		return array_merge($aSetting, array('deep' => $iNum, 'enabled' => $aParams['enabled']));
	}
	
	function setMemLevelSettings($iMLevel, $iDeep, $isEnabled, $sLevels, $sPoints, $sLevelsUpgrade, $sPointsUpgrade){
		if (!(int)$iMLevel) return;
		return $this -> query("REPLACE INTO `{$this -> _sPrefix}memlevels_matrix` SET `id_level` = '{$iMLevel}', `deep` = '{$iDeep}', `enabled` = '{$isEnabled}', `points` = '{$sPoints}-{$sPointsUpgrade}', `currency`='{$sLevels}-{$sLevelsUpgrade}'");
	}
	
	function getMembersReferralsTree($iMember){
		$aMemberRange = $this -> getRow("SELECT `lft`, `rgt`, `root_id`, `level` FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iMember}' LIMIT 1");
		
		if (empty($aMemberRange)) return false;
		
		$iMatrixDeep = (int)$this -> getForcedMatrixLevels($iMember) + (int)$aMemberRange['level']; 		
		
		return $this -> getAll("SELECT `m`.`member`,`m`.`lft`,`m`.`rgt`,`m`.`level` - {$aMemberRange['level']} as `level`,`m`.`root_id`, `Profiles`.`ID`,`Profiles`.`NickName`,`Profiles`.`Status`, `Profiles`.`FullName`, `Profiles`.`LastName`, `Profiles`.`FirstName`, `Profiles`.`DateReg`  FROM `{$this -> _sPrefix}matrix` as `m` LEFT JOIN `Profiles` ON `m`.`member` = `Profiles`.`ID` WHERE `lft` > {$aMemberRange['lft']} AND `lft` < {$aMemberRange['rgt']} AND `root_id` = '{$aMemberRange['root_id']}' AND `level` > 0 AND {$iMatrixDeep} >=	`level` ORDER BY `lft` ASC"); 
	}	
	
	
	function getMemberReferralsTable(&$aParams){
		$sSelectClause = $sJoinClause = $sWhereClause = $sOrderBy = $sHaving = '';
	   
	    if (!($iMember = (int)$aParams['member_id'])) return '';
	
		if (empty($aParams['view_order'])) $sOrderBy = '`tp`.`ID` ASC'; 
			else $sOrderBy = "`{$aParams['view_order']}` {$aParams['view_type']}"; 
   
		$bMatrix = $this -> isForcedMatrixEnabled($iMember);
   
		if (!empty($aParams['filter'])) 
		{
			
			$matches = array();
			 
				$sWhereClause .= " AND (
		                `tp`.`NickName` LIKE '%" . $aParams['filter'] . "%' OR 
						`tp`.`FullName` LIKE '%" . $aParams['filter'] . "%' OR 
						`tp`.`FirstName` LIKE '%" . $aParams['filter'] . "%' OR 
						`tp`.`LastName` LIKE '%" . $aParams['filter'] . "%' OR 
						 `p`.`NickName` LIKE '%" . $aParams['filter'] . "%' OR";
						if ($bMatrix) $sWhereClause .= "`level` LIKE '%" . $aParams['filter'] . "%' OR ";
		                $sWhereClause .= "`tp`.`DateReg` LIKE '%" . $aParams['filter'] . "%')";
		   
		}	
	
		$aMemberRange = $this -> getRow("SELECT `lft`, `rgt`, `root_id`, `level` FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iMember}' LIMIT 1");
		
	if ($bMatrix && empty($aMemberRange)) return false;	
		else
			$iMatrixDeep = (int)$this -> getForcedMatrixLevels($iMember) + (int)$aMemberRange['level']; 
		
	//--- Get Items ---//

   if (!$bMatrix) 
	$sQuery = "SELECT 
				   `r`.`member`,
				   '---' as `level`,
				   `tp`.`ID`,
				   `tp`.`NickName`,
				   `tp`.`Status`, 
				   IF (`tp`.`FullName` != '', `tp`.`FullName`, CONCAT(`tp`.`FirstName`, ' ', `tp`.`LastName`)) as `FullName`,
				   	DATE_FORMAT(`tp`.`DateReg`,  '" . $this -> _oConfig -> getDateFormat() . "' ) as `DateReg`,
				    `p`.`NickName` as `referral`
			  FROM `{$this->_sPrefix}referrals` as `r`
			  LEFT JOIN `Profiles` as `tp` ON `r`.`member` = `tp`.`ID`
			  LEFT JOIN `Profiles` as `p` ON `r`.`referral` = `p`.`ID`
			  WHERE `referral` = '{$iMember}' {$sWhereClause} 
			  ORDER BY  {$sOrderBy}
	    	  LIMIT {$aParams['view_start']},{$aParams['view_per_page']}";
	else 
	$sQuery = " 
			SELECT `m`.`member`,
				   `m`.`lft`,
				   `m`.`rgt`,
				   `m`.`level` - {$aMemberRange['level']} as `level`,
				   `m`.`root_id`, 
				   `tp`.`ID`,
				   `tp`.`NickName`,
				   `tp`.`Status`, 
				    IF (`tp`.`FullName` != '', `tp`.`FullName`, CONCAT(`tp`.`FirstName`, ' ', `tp`.`LastName`)) as `FullName`,				   
				   	DATE_FORMAT(`tp`.`DateReg`,  '" . $this -> _oConfig -> getDateFormat() . "' ) as `DateReg`,
					`p`.`NickName` as `referral`
		    FROM `{$this -> _sPrefix}matrix` as `m` 
			LEFT JOIN `Profiles` as `tp` ON `m`.`member` = `tp`.`ID`
			LEFT JOIN `{$this -> _sPrefix}referrals` as `r` ON `tp`.`ID` = `r`.`member` 
			LEFT JOIN `Profiles` as `p` ON `p`.`ID` = `r`.`referral`
			WHERE `lft` > {$aMemberRange['lft']} AND `lft` < {$aMemberRange['rgt']} AND `root_id` = '{$aMemberRange['root_id']}' AND `level` > 0 AND {$iMatrixDeep} >=	`level` {$sWhereClause} 
	    	ORDER BY  {$sOrderBy}
	    	LIMIT " . $aParams['view_start'] . ", " . $aParams['view_per_page'].'';

		if ($bMatrix) 
		$aParams['count'] = (int)$this -> getOne("SELECT COUNT(*)
		    FROM `{$this -> _sPrefix}matrix` as `m` 
			LEFT JOIN `Profiles` as `tp` ON `m`.`member` = `tp`.`ID`
			LEFT JOIN `{$this -> _sPrefix}referrals` as `r` ON `tp`.`ID` = `r`.`member` 
			LEFT JOIN `Profiles` as `p` ON `p`.`ID` = `r`.`referral`
			WHERE `lft` > {$aMemberRange['lft']} AND `lft` < {$aMemberRange['rgt']} AND `root_id` = '{$aMemberRange['root_id']}' AND `level` > 0 AND {$iMatrixDeep} >=	`level` {$sWhereClause}");
		else
		$aParams['count'] = (int)$this -> getOne("SELECT COUNT(*)
			  FROM `{$this->_sPrefix}referrals` as `r`
			  LEFT JOIN `Profiles` as `tp` ON `r`.`member` = `tp`.`ID`
			  LEFT JOIN `Profiles` as `p` ON `r`.`referral` = `p`.`ID`
			  WHERE `referral` = '{$iMember}' {$sWhereClause}");
		
		
		return $this -> getAll($sQuery);
	}
	
	function getMatrixLevel($iMemberId){
		return (int)$this -> getOne("SELECT `level` FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iMemberId}' OR `root_id` = '{$iMemberId}' ORDER BY `level` ASC LIMIT 1"); 
	}
	
	function isLevelAddAllowed($iParent){
		$iWidth = $this -> getForcedMatrixWidth();
		if (!(int)$iWidth) return true;
		
		$aMemberRange = $this -> getRow("SELECT `lft`, `rgt`, `root_id`, `level` FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iParent}' LIMIT 1");
		if (empty($aMemberRange)) return true;
			
		return (int)$this -> getOne("SELECT COUNT(`level`) FROM `{$this -> _sPrefix}matrix` WHERE `lft` > {$aMemberRange['lft']} AND `lft` < {$aMemberRange['rgt']} AND `root_id` = '{$aMemberRange['root_id']}' AND `level` = {$aMemberRange['level']} + 1") < $iWidth; 
	}	
	
	function isSpilloverEnabled(){
		return $this -> getParam('aqb_matrix_spillover') == 'on';
	}
	
	function isExists($iMemberID){
		return $this -> getOne("SELECT COUNT(*) FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iMemberID}' LIMIT 1") == 1;
	}
	
	function addNode($iParent, $iMember){
		if ($this -> isExists($iMember)) return false;
		
		$iPrevMem = $this -> getOne("SELECT `member` FROM `{$this -> _sPrefix}referrals` WHERE `referral` = '{$iParent}' AND `member` < {$iMember} ORDER BY `date` DESC LIMIT 1");
		
		if (!($iHeight = (int)$this -> getForcedMatrixLevels($iParent))) return false;

		$this -> query("LOCK TABLE `{$this -> _sPrefix}matrix` WRITE");

		if ($this -> isSpilloverEnabled()) $iParent = $this -> getNodeToAdd($iParent, $iPrevMem, $iHeight); 
		elseif(!$this -> isLevelAddAllowed($iParent)) {
			$this -> query("UNLOCK TABLES");
			return false;
		}		
		
		if (!(int)$iParent){
			$this -> query("UNLOCK TABLES");
			return false;
		}
		
		if (!(int)$this -> getOne("SELECT COUNT(*) FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iParent}' LIMIT 1")){
			$this -> query("INSERT INTO `{$this -> _sPrefix}matrix` SET `lft` = '1', `rgt` = '2', `level` = 0, `root_id` = '{$iParent}', `member` = '{$iParent}'");
		}	
		
		$aAttr = $this -> getRow("SELECT `root_id`,`rgt`,`lft`, `level` FROM `{$this -> _sPrefix}matrix` WHERE `root_id`  = '{$iParent}' OR `member` = '{$iParent}' ORDER BY `id` ASC LIMIT 1");
		
		if (empty($aAttr)){ 
			$bResult = $this -> query("INSERT INTO `{$this -> _sPrefix}matrix` SET `lft` = '1', `rgt` = '2', `level` = 1, `root_id` = '{$iParent}', `member` = '{$iMember}'");
			$this -> query("UNLOCK TABLES");
			return $bResult;
		}		
				
		$aAttr['root_id'] = (int)$aAttr['root_id'] ? (int)$aAttr['root_id'] : $iParent;
		$aAttr['rgt'] = (int)$aAttr['rgt'] ? (int)$aAttr['rgt'] : 1;
		$aAttr['lft'] = (int)$aAttr['lft'] ? (int)$aAttr['lft'] : 1;
		$aAttr['level'] = (int)$aAttr['level'] ? (int)$aAttr['level'] + 1 : 1;
		
		$this -> query("UPDATE `{$this -> _sPrefix}matrix` SET `rgt` = `rgt` + 2 WHERE `rgt` >= {$aAttr['rgt']} AND `root_id` = '{$aAttr['root_id']}'");
		$this -> query("UPDATE `{$this -> _sPrefix}matrix` SET `lft` = `lft` + 2 WHERE `lft` > {$aAttr['rgt']} AND `root_id` = '{$aAttr['root_id']}'");
		
		$bResult = $this -> query("INSERT INTO `{$this -> _sPrefix}matrix` SET `lft` = {$aAttr['rgt']}, `rgt` = {$aAttr['rgt']} + 1, `level` = '{$aAttr['level']}', `root_id` = '{$aAttr['root_id']}', `member` = '{$iMember}'"); 		
		$this -> query("UNLOCK TABLES");
				
		return $bResult;
	}
	
	
	function deleteNode($iMember){
		$this -> query("LOCK TABLE `{$this -> _sPrefix}matrix` WRITE");
		$aAttr = $this -> getRow("SELECT `root_id`,`rgt`,`lft`, `level` FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iMember}'");

		if (empty($aAttr)){ 
			$this -> query("UNLOCK TABLES");
			return false;
		}
		
		$iWidth = (int)$aAttr['rgt'] - (int)$aAttr['lft'] + 1;
		
		if ($iWidth > 2){
			$aNewRoot = $this -> getAll("SELECT * FROM `{$this -> _sPrefix}matrix` WHERE `lft` BETWEEN {$aAttr['lft']} AND {$aAttr['rgt']} AND `level` = {$aAttr['level']} + 1");
	
			foreach($aNewRoot as $iKey => $aVal){
				$this -> query("UPDATE `{$this -> _sPrefix}matrix` SET `rgt` = `rgt` - {$aVal['lft']} + 1, `level` = `level` - {$aAttr['level']} - 1, `root_id` = '{$aVal['member']}' WHERE `rgt` <= {$aVal['rgt']} AND {$aVal['lft']} < `rgt`");
				$this -> query("UPDATE `{$this -> _sPrefix}matrix` SET `lft` = `lft` - {$aVal['lft']} + 1 WHERE `lft` >= {$aVal['lft']} AND {$aVal['rgt']} > `lft`");		
			}
		}
		
		$this -> query("DELETE FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iMember}'");	
		$this -> query("UPDATE `{$this -> _sPrefix}matrix` SET `rgt` = `rgt` - {$iWidth} WHERE `rgt` > {$aAttr['rgt']} AND `root_id` = '{$aAttr['root_id']}'");
		$this -> query("UPDATE `{$this -> _sPrefix}matrix` SET `lft` = `lft` - {$iWidth} WHERE `lft` > {$aAttr['rgt']} AND `root_id` = '{$aAttr['root_id']}'");
		$this -> query("UNLOCK TABLES");
	}
	
	function getTimer(){
		return (float)$this -> getOne("SELECT `VALUE` FROM `sys_options` WHERE `Name` = 'aqb_matrix_timer'");
	}
	
	function setTimer($iVal){
		return $this -> query("UPDATE `sys_options` SET `VALUE` = '{$iVal}' WHERE `Name` = 'aqb_matrix_timer'");
	}
	
	function applyNewMatrix(){
		$aReferrals = $this -> getAll("SELECT * FROM `{$this -> _sPrefix}referrals` ORDER BY `date`"); 
		if (empty($aReferrals)) return false;
		
		$this -> query("TRUNCATE TABLE `{$this -> _sPrefix}matrix`");
		
		foreach($aReferrals as $iKey => $aRef){
			$this -> addNode((int)$aRef['referral'], (int)$aRef['member']);
		}	
	
		return true;
	}
	
	function getNodeToAdd($iOwnerId, $iPrevMem = 0, $iHeight = 0){
		$iWidth = $this -> getForcedMatrixWidth();
		if ($iWidth == 0) return $iOwnerId;
				
		$aMemberRange = $this -> getRow("SELECT `lft`, `rgt`, `root_id`, `level` FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iOwnerId}' ORDER BY `level` ASC LIMIT 1");
	
		if (empty($aMemberRange)) return $iOwnerId;
	
		$aLevels = $this -> getAll("SELECT COUNT(`level`) as `level_num`, `level`  FROM `{$this -> _sPrefix}matrix` WHERE `lft` > {$aMemberRange['lft']} AND `lft` < {$aMemberRange['rgt']} AND `root_id` = '{$aMemberRange['root_id']}' GROUP BY `level`"); 
	
		if (empty($aLevels)) return $iOwnerId;
		
		$iParentLevel = 0;
		$iDeep = 0;
		
		$aValue = array();
		foreach($aLevels as $iKey => $aValue){
			 $iSize = pow($iWidth, ((int)$aValue['level'] - (int)$aMemberRange['level']));
			 if ((int)$aValue['level_num'] < $iSize){
				$iParentLevel = (int)$aValue['level'] - 1;
				break;
			 }
			$iDeep++;
		}
		
		if ((int)$iParentLevel == (int)$aMemberRange['level'] && !(int)$iDeep) return $iOwnerId;
	
		if ($iParentLevel == 0 && $iHeight > ((int)$aValue['level'] - (int)$aMemberRange['level'])) return $this -> getOne("SELECT `member` FROM `{$this -> _sPrefix}matrix` WHERE `lft` > {$aMemberRange['lft']} AND `lft` < {$aMemberRange['rgt']} AND `root_id` = '{$aMemberRange['root_id']}' AND `level` = '{$aValue['level']}' ORDER BY `id` ASC LIMIT 1");

		if ($iParentLevel == 0 && $iHeight <= ((int)$aValue['level'] - (int)$aMemberRange['level'])) return false; 
		
		
		// find previous
		$aPrevious = array();
		if ($iPrevMem){		
			$aInfo = $this -> getRow("SELECT `lft`, `rgt`, `root_id` FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iPrevMem}' AND `level` = {$iParentLevel} + 1 LIMIT 1");
			if (!empty($aInfo)) $aPrevious = $this -> getRow("SELECT `member`,`lft` FROM `{$this -> _sPrefix}matrix` WHERE `lft` < {$aInfo['lft']} AND `rgt` > {$aInfo['rgt']} AND `root_id` = '{$aInfo['root_id']}' AND `level` = {$iParentLevel} LIMIT 1");
		}
		//find previous 
				
		if (!empty($aPrevious)) $sWhere = " AND `lft` > {$aPrevious['lft']}";
		$aResultParentRow = $this -> getAll("SELECT `lft`, `rgt`, `member` FROM `{$this -> _sPrefix}matrix` WHERE `lft` > {$aMemberRange['lft']} AND `lft` < {$aMemberRange['rgt']} AND `root_id` = '{$aMemberRange['root_id']}' AND `level` = '{$iParentLevel}' {$sWhere} ORDER BY `lft`");
		
		foreach($aResultParentRow as $iKey => $aValue){
			$iCount = $this -> getOne("SELECT COUNT(*) FROM `{$this -> _sPrefix}matrix` WHERE `lft` >= {$aValue['lft']} AND `lft` < {$aValue['rgt']} AND `root_id` = '{$aMemberRange['root_id']}' AND `level` = $iParentLevel + 1 ORDER BY `id`"); 
				
			if ($iCount < $iWidth) return (int)$aValue['member'];
		}

		if (!empty($aPrevious)) $sWhere = " AND `lft` <= {$aPrevious['lft']}";
		$aResultParentRow = $this -> getAll("SELECT `lft`, `rgt`, `member` FROM `{$this -> _sPrefix}matrix` WHERE `lft` > {$aMemberRange['lft']} AND `lft` < {$aMemberRange['rgt']} AND `root_id` = '{$aMemberRange['root_id']}' AND `level` = '{$iParentLevel}' {$sWhere} ORDER BY `lft`");
		
		foreach($aResultParentRow as $iKey => $aValue){
			$iCount = $this -> getOne("SELECT COUNT(*) FROM `{$this -> _sPrefix}matrix` WHERE `lft` >= {$aValue['lft']} AND `lft` < {$aValue['rgt']} AND `root_id` = '{$aMemberRange['root_id']}' AND `level` = $iParentLevel + 1 ORDER BY `id`"); 
			if ($iCount < $iWidth) return (int)$aValue['member'];
		}
		
		return false;
	}
	
	function applyCommissionToMyBranch($iMemberID, $sAction = 'join' , $fPrice = 0){
		$isIncomePriceEnabled = $this -> isPriceEnabled();
		$isIncomePrecentagEnabled = $this -> isPercentageEnabled();
		
		
		if ($bPointsEnable = $this -> isPointsSystemInstalled()){
			$isIncomePriceEnabled |= $this -> isPointsForJoinEnabled();
			$isIncomePrecentagEnabled |= $this -> isPointsForUpgradeEnabled();
		}
				
		if (!(int)$iMemberID || ($sAction == 'join' && !$isIncomePriceEnabled) || ($sAction == 'upgrade' && !$isIncomePrecentagEnabled)) return false;
		
			
		$aMembers = $this -> getMyBranch($iMemberID);
		
		if (empty($aMembers)) return false;
		
		foreach($aMembers as $iKey => $aVal){
			$aRes = $this -> getPriceForLevel($aVal, $aVal['level'], $bPointsEnable);

			if (!$bPointsEnable) $aRes['points_upgrade'] = $aRes['points'] = 0;
			
			if (($sAction == 'join' && !((int)$aRes['points'] || (float)$aRes['price'])) || ($sAction == 'upgrade' && !((int)$aRes['points_upgrade'] || (int)$aRes['price_upgrade']))) continue;
			
			if ($sAction == 'upgrade'){
				$aRes['points'] = $aRes['points_upgrade'];
				$aRes['price'] = number_format((int)$aRes['price_upgrade'] * (float)$fPrice / 100, 2);

				$this -> query("INSERT INTO `{$this->_sPrefix}journal` SET `member_id` = '{$aVal['member']}', `inviter_type` = 'referral', `sum_price` = '{$aRes['price']}', `sum_points` = '{$aRes['points']}' , `inviter` = '{$iMemberID}', `action_type` = 'upgrade', `last_update` = UNIX_TIMESTAMP()");
			}else 
				$this -> query("INSERT INTO `{$this->_sPrefix}journal` 
																	SET `member_id` = '{$aVal['member']}', 
																		`inviter_type` = 'referral', 
																		`sum_price` = '{$aRes['price']}', 
																		`sum_points` = '{$aRes['points']}' , 
																		`inviter` = '{$iMemberID}', 
																		`action_type` = '{$sAction}', 
																		`last_update` = UNIX_TIMESTAMP()");
		 		
		}
		return true;
	}
	
	function getPriceForLevel(&$aData, $iLevel, $bPointsEnable = false){
		$aLevelsValues = $this -> getMemLevelMatrixSettings($aData['membership'], $bPointsEnable);
		
		$aResult = array('points' => 0, 'price' => 0, 'points_upgrade' => 0, 'price_upgrade' => 0);
		if (empty($aLevelsValues)) return $aResult;
		
		$aResult['points'] = (int)$aLevelsValues['points']['points_'. ($iLevel - 1) . '_' . $aData['membership']];
		$aResult['price'] = (float)$aLevelsValues['levels']['levels_'. ($iLevel - 1) . '_' . $aData['membership']];
		$aResult['points_upgrade'] = (int)$aLevelsValues['points_upgrade']['points_upgrade_'. ($iLevel - 1) . '_' . $aData['membership']];
		$aResult['price_upgrade'] = (float)$aLevelsValues['levels_upgrade']['levels_upgrade_'. ($iLevel - 1) . '_' . $aData['membership']];
		
		return $aResult;
	}
	
	function getPriceByMemLevel($iMemLevelId, $iDays){
/* Freddy commentaire
		return (float)$this -> getOne("SELECT `Price` FROM `sys_acl_level_prices` WHERE `IDLevel` = '{$iMemLevelId}' AND `Days` = '{$iDays}' LIMIT 1");
		*/
		/* Freddy modification for compatiblity with martinboi module */
		return (float)$this -> getOne("SELECT `Price` FROM `sys_acl_level_prices` WHERE `IDLevel` = '{$iMemLevelId}' LIMIT 1");
	}
	
	function getMyBranch($iMemberID){
		if (!(int)$iMemberID) return array();
			
		$aMemberRange = $this -> getRow("SELECT `lft`, `rgt`, `root_id`, `level` FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iMemberID}' LIMIT 1");
		if (empty($aMemberRange)) return array();

		$iMaxLevelsNum = (int)$this -> getForcedMatrixLevels();
		if (!(int)$iMaxLevelsNum) return array();
		
		$iMatrixDeep = ((int)$aMemberRange['level'] - $iMaxLevelsNum);
		$iMatrixDeep = $iMatrixDeep < 0 ? 0 : $iMatrixDeep;
		
		$aMember = $this -> getAll("SELECT * FROM `{$this -> _sPrefix}matrix` WHERE `lft` <= {$aMemberRange['lft']} AND {$aMemberRange['rgt']} < `rgt` AND `root_id` = '{$aMemberRange['root_id']}' AND (`level` < {$aMemberRange['level']} AND `level` >= {$iMatrixDeep}) ORDER BY `level` DESC");

		
		$aMembers = array();
		foreach($aMember as $iKey => $aValue){ 
			$aMembership = $this -> getMemberMembershipInfo($aValue['member']);
			
			$aProfInfo = getProfileInfo($aValue['member']);
			if ($aProfInfo['Status'] != 'Active') continue;			
			
			$aInfo = $this -> getRow("SELECT * FROM `{$this -> _sPrefix}memlevels_matrix` WHERE `id_level` = '{$aMembership['ID']}' LIMIT 1");
			
			$iLevel = (int)$aMemberRange['level'] - (int)$aValue['level'];
			if (!(int)$aInfo['enabled'] || (int)$aInfo['deep'] < $iLevel) continue;	
			
			$aMembers[$iKey] = $aValue;
			$aMembers[$iKey]['level'] = $iLevel;
			$aMembers[$iKey]['membership'] = $aMembership['ID'];
		}
		
		return !empty($aMembers) ? $aMembers : false ;
	}
	
	function getForcedMatrixWidth(){
		return (int)$this -> getParam('aqb_matrix_width');
	}

	function getForcedMatrixLevels($iMember = 0){
		$iDeep = 15; 
		
		if ((int)$iMember){		
			$aMemLevel = $this -> getMemberMembershipInfo($iMember);
			$aParams = $this -> getRow("SELECT * FROM `{$this -> _sPrefix}memlevels_matrix` WHERE `id_level` = '{$aMemLevel['ID']}' LIMIT 1");
			if (!empty($aParams) && (int)$aParams['enabled'] && (int)$aParams['deep']) return (int)$aParams['deep']; else return 0;
		}
		
		$iMax = $this -> getOne("SELECT MAX(`deep`) FROM `{$this -> _sPrefix}memlevels_matrix` LIMIT 1");
		return $iMax ? $iMax : $iDeep;
	}
	
	function isForcedMatrixEnabled($iMember = 0){
		if ($this -> getParam('aqb_forced_matrix') != 'on') return false;
		
		$aMemLevel = $this -> getMemberMembershipInfo($iMember);
		$iEnabled = (int)$this -> getOne("SELECT `enabled` FROM `{$this -> _sPrefix}memlevels_matrix` WHERE `id_level` = '{$aMemLevel['ID']}' LIMIT 1");
		return $iEnabled ? true : false;
	}

	function isPercentageEnabled(){
		$sIncome = $this -> getParam('aqb_matrix_income');
		
		if (!$sIncome) return false;
		$aIncome = explode(',', $sIncome);
		if (in_array(1, $aIncome)) return true;
		
		return false;
	}

	function isPriceEnabled(){
		$sIncome = $this -> getParam('aqb_matrix_income');
		
		if (!$sIncome) return false;
		$aIncome = explode(',', $sIncome);
		if (in_array(2, $aIncome)) return true;
		
		return false;
	}
	
	function isPointsForUpgradeEnabled(){
		$sIncome = $this -> getParam('aqb_matrix_income');
		if (!(int)$this -> isPointsSystemInstalled()) return false;
		
		if (!$sIncome) return false;
		$aIncome = explode(',', $sIncome);
		if (in_array(3, $aIncome)) return true;
		
		return false;
	}

	function isPointsForJoinEnabled(){
		$sIncome = $this -> getParam('aqb_matrix_income');
		if (!(int)$this -> isPointsSystemInstalled()) return false;
		
		if (!$sIncome) return false;
		$aIncome = explode(',', $sIncome);
		if (in_array(4, $aIncome)) return true;
		
		return false;
	}
	
	function getReferrals(&$aProfiles, $iStart, $iPerPage){
		$aProfiles = $this -> getAll("SELECT COUNT(*) as `count`, `referral`,`Profiles`.* FROM `{$this->_sPrefix}referrals` LEFT JOIN `Profiles` ON `{$this->_sPrefix}referrals`.`referral` = `Profiles`.`ID` GROUP BY `referral` ORDER BY `count` DESC LIMIT {$iStart}, {$iPerPage}");
		 
		 return  $aProfiles !== false ? count($aProfiles) : 0; 
	}
	
	function getReferralsNum(){
		return $this -> getOne("SELECT COUNT(*) as `count` FROM `{$this->_sPrefix}referrals` LEFT JOIN `Profiles` ON `{$this->_sPrefix}referrals`.`referral` = `Profiles`.`ID`");
	}
	
	function getMyReferral($iMemberId){
		return (int)$this -> getOne("SELECT `referral` FROM `{$this->_sPrefix}referrals` LEFT JOIN `Profiles` ON `{$this->_sPrefix}referrals`.`referral` = `Profiles`.`ID` WHERE `{$this->_sPrefix}referrals`.`member` = '{$iMemberId}'");
	}
	
	function getMyReferralNickName($iMemberId){
		return $this -> getOne("SELECT `NickName` FROM `{$this->_sPrefix}referrals` LEFT JOIN `Profiles` ON `{$this->_sPrefix}referrals`.`referral` = `Profiles`.`ID` WHERE `{$this->_sPrefix}referrals`.`member` = '{$iMemberId}'");
	}
	
	function getMyParent($iMemberId){
		$aMemberRange = $this -> getRow("SELECT `lft`, `rgt`, `root_id`, `level` FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iMemberId}' LIMIT 1");
		if (empty($aMemberRange)) return '';
	
		return $this -> getOne("SELECT `NickName` FROM `{$this -> _sPrefix}matrix` as `m` LEFT JOIN `Profiles` ON `Profiles`.`ID` = `m`.`member` WHERE `lft` < {$aMemberRange['lft']} AND `rgt` > {$aMemberRange['rgt']} AND `root_id` = '{$aMemberRange['root_id']}' AND `level` = {$aMemberRange['level']} - 1 LIMIT 1");
	}
	
	function getMyInvitedMembers(&$aProfiles, $iProfileId, $iStart, $iPerPage){
		if ($this -> isForcedMatrixEnabled($iProfileId) && $this -> _oConfig -> showMyBranchMembersEnabled()){ 
			$aMemberRange = $this -> getRow("SELECT `lft`, `rgt`, `root_id` FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iProfileId}' ORDER BY `level` ASC LIMIT 1");
			if (empty($aMemberRange)) return 0;
			$aProfiles = $this -> getAll("SELECT `Profiles`.*,`{$this->_sPrefix}referrals`.`date`  
									FROM `{$this -> _sPrefix}matrix` 
									LEFT JOIN `{$this->_sPrefix}referrals` ON `{$this -> _sPrefix}matrix`.`member` = `{$this -> _sPrefix}referrals`.`member`
									LEFT JOIN `Profiles` ON `{$this->_sPrefix}matrix`.`member` = `Profiles`.`ID`
									WHERE `lft` > {$aMemberRange['lft']} AND `lft` < {$aMemberRange['rgt']} AND `root_id` = '{$aMemberRange['root_id']}'  ORDER BY `date` DESC LIMIT {$iStart}, {$iPerPage}"); 
		}else 	
			$aProfiles = $this -> getAll("SELECT * FROM `{$this->_sPrefix}referrals` LEFT JOIN `Profiles` ON `{$this->_sPrefix}referrals`.`member` = `Profiles`.`ID` WHERE `referral` = '{$iProfileId}' ORDER BY `date` DESC LIMIT {$iStart}, {$iPerPage}");
		 
		 return  $aProfiles !== false ? count($aProfiles) : 0; 
	}
	
	function getMyInvitedMembersCount($iProfileId){
		 if ($this -> isForcedMatrixEnabled($iProfileId) && $this -> _oConfig -> showMyBranchMembersEnabled()){ 
			$aMemberRange = $this -> getRow("SELECT `lft`, `rgt`, `root_id` FROM `{$this -> _sPrefix}matrix` WHERE `member` = '{$iProfileId}' ORDER BY `level` ASC LIMIT 1");
			if (empty($aMemberRange)) return 0;
			return $this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}matrix` WHERE `lft` > {$aMemberRange['lft']} AND `lft` < {$aMemberRange['rgt']} AND `root_id` = '{$aMemberRange['root_id']}' LIMIT 1"); 
		}	
		 return $this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}referrals` WHERE `referral` = '{$iProfileId}' LIMIT 1");
	}	
	
	function getSubMenuId($sType = 'referrals'){
		if ($sType == 'referrals') return $this -> getOne("SELECT  `ID` FROM  `sys_menu_top` WHERE  `Caption` =  '_aqb_aff_my_referrals' LIMIT 1");
		return $this -> getOne("SELECT `ID` FROM  `sys_menu_top` WHERE  `Caption` =  '_aqb_aff_my_history' LIMIT 1");
	}
	
	function getAffSubMenu(){
		return $this -> getOne("SELECT `ID` FROM  `sys_menu_top` WHERE  `Caption` =  '_aqb_aff_my_affilate' LIMIT 1");
	}
	
	function getTransactionInfo($iId){
		return $this -> getRow("SELECT * FROM `{$this -> _sPrefix}transactions` WHERE `id` = '{$iId}' LIMIT 1"); 
	}
	
	function paidCommission($iId, $sOrder, $iTime = 0){
		if ((int)$iTime) $sTime = $iTime; else $sTime = 'UNIX_TIMESTAMP()';
		return $this -> query("UPDATE `{$this -> _sPrefix}transactions` SET `payment_status` = 'complete', `status` = 'paid', `tnx` = '{$sOrder}', `date_end` = {$sTime} WHERE `id` = '{$iId}'"); 
	}
	
	function getProfileTransactions($iProfileId){
   	   return $this -> getAll("SELECT * FROM `{$this->_sPrefix}transactions` WHERE `member_id` = '{$iProfileId}'");
    }
	
	function makeUnpaid($iId){
	   return $this -> query("UPDATE `{$this -> _sPrefix}transactions` SET `payment_status` = '', `status` = 'unpaid', `tnx` = '', `date_end` = 0 WHERE `id` = '{$iId}'"); 
	}
	
	function getTransactionsPaymentProvider($sTransaction){
	   	$sTitle = $this -> getOne("SELECT `provider` FROM `bx_pmt_transactions_pending` WHERE `order` = '{$sTransaction}' LIMIT 1");
		return $this -> getOne("SELECT `id` FROM `bx_pmt_providers` WHERE `name` = '{$sTitle}' LIMIT 1"); 
	}
	
	function processCommissionManyally($iId, $sTransactionID){
		$aInfo = $this -> getTransactionInfo($iId);
		$sStatus = bx_get('status');
		$iTime = time();
		
		$sItems = "{$aInfo['member_id']}_" . $this -> getModuleId(). "_{$iId}_1"; 
			$aItems = $this -> getAll("SELECT `id` FROM `bx_pmt_transactions_pending` WHERE `items` = '{$sItems}'");
		if (!empty($aItems))
		{
				foreach($aItems as $iK => $aValue){
					$this -> query("DELETE FROM `bx_pmt_transactions` WHERE `pending_id` = '{$aValue['id']}'");
					$this -> query("DELETE FROM `bx_pmt_transactions_pending` WHERE `id` = '{$aValue['id']}'");
				}
			}
			
	
		if ($sStatus == 'unpaid')
		{
			if ($this -> isPointsSystemInstalled())
				$this -> query("DELETE FROM `aqb_points_history` WHERE `profile_id` = '{$aInfo['member_id']}' AND `action_id` = '0' AND `time` = '{$aInfo['date_end']}'");
			
			return $this -> makeUnpaid($iId);
		}		
		
		if ($sTransactionID)
		{
				$aPayments = $this -> getAvailablePayament($aInfo['member_id']);
				
				if (!empty($aPayments))
				{
				$iClient = getLoggedId();				
					if ($iPayment == (int)bx_get('payments'))
						$sProvider = $aPayments[$iPayment];				
				
				$aParams = array(
							'client' => $iClient,
							'seller' => $aInfo['member_id'],
							'items' => $sItems,
							'amount' => $aInfo['price'],
							'order'  => $sTransactionID,
							'provider' => $sProvider,
							'item_id' => $iId								
						 );
				
				$this -> createCartItems($aParams);			
		}
		}

		if ($this -> isPointsSystemInstalled() && (int)$aInfo['points'] && $aInfo['status'] == 'unpaid')
		{ 
		    $aAction = array('id' => 0, 'title' => '_aqb_aff_assign_points', 'points' => (int)$aInfo['points'], 'time' => $iTime);
			$this -> assignPoints((int)$aInfo['member_id'], $aAction);
		}	
		
		return $this -> paidCommission($iId, $sTransactionID, $iTime);
	}
	
	function createCartItems(&$aParams)
	{
		if (empty($aParams)) return false;
	
		$this -> query("INSERT INTO `bx_pmt_transactions_pending` SET 
								`client_id` = '{$aParams['client']}',
								`seller_id` = '{$aParams['seller']}', 
								`items` = '{$aParams['items']}', 
								`amount` = '{$aParams['amount']}', 
								`order` = '{$aParams['order']}', 
								`error_code` = 1, 
								`error_msg` = 'Payment was successfully accepted.', 
								`provider` = '{$aParams['provider']}', 
								`date` = UNIX_TIMESTAMP()");
								
		$iGetLast = $this -> lastId();
				
		return	$iGetLast && $this -> query("INSERT INTO `bx_pmt_transactions` SET 
								`pending_id` = '{$iGetLast}',
								`client_id` = '{$aParams['client']}',
								`seller_id` = '{$aParams['seller']}', 
								`item_count` = '1', 
								`item_id` = '{$aParams['item_id']}',
								`amount` = '{$aParams['amount']}', 
								`module_id` = " . $this -> getModuleId() . ",
								`order_id` = '{$aParams['order']}', 
								`date` = UNIX_TIMESTAMP()");
		 
	}
	
	function getAvailablePayament($iMemberID){
		$aPaymentProviders = $this -> getPairs("SELECT `id`,`name` FROM `bx_pmt_providers` ORDER BY `id`", 'id', 'name');
		$aAvailabel = $this -> getAll("SELECT `provider_id` FROM `bx_pmt_user_values` as `v` LEFT JOIN `bx_pmt_providers_options` as `o` ON `v`.`option_id` = `o`.`id` WHERE `user_id` = '{$iMemberID}' AND ((`name` = 'pp_active' AND `value` = 'on') OR (`name` = '2co_active' AND `value` = 'on'))");

		$aResult = array();
		if (!empty($aAvailabel) && !empty($aPaymentProviders)){
			foreach($aAvailabel as $iKey => $aVal){
				$aResult[$aVal['provider_id']]= $aPaymentProviders[$aVal['provider_id']];
			}		
		}
		return $aResult;	
	}

	function acceptMassPayment(&$aData){
        if (empty($aData)) return false;
        
		$aItems = array();
		$iCount = 0;
		
		for($i = 1; $i < 250; $i++){
			if (!isset($aData['receiver_email_'.$i]) || $aData['status_'.$i] != 'Completed') break;
			
			$aItems[] = array( 
				'email' => $aData['receiver_email_'.$i],
				'tnx' => $aData['masspay_txn_id_'.$i],
				'status' => $aData['status_'.$i],
				'price' => $aData['mc_gross_'.$i],
				'member_id' => $this -> getMemberByPaypalEmail($aData['receiver_email_'.$i])
			);
			
			$iCount++;
		}
		
		if (!(int)$iCount) return false;
		
		foreach($aItems as $iKey => $aValue)  
				if ($this -> updateTransaction($aValue)){
					$aData = $this -> getEmailTemplate($aValue);					
					$this -> addToQueue($aData['Email'], $aData['Subject'], $aData['Body']);
				}
	}
	
	function getEmailTemplate($aData = array()){
		$sLang = $this -> getParam('lang_default');
		if(empty($sLang)) $sLang = 'en';

		$iDefaultLangId = $this -> getOne("SELECT `ID` FROM `sys_localization_languages` WHERE `Name`='{$sLang}' LIMIT 1");
		$aMemberInfo = $this -> getMembersInfo($aData['member_id']);
		
		$aKeys = array(
            'Domain' => BX_DOL_URL_ROOT,
            'SiteName' => $this -> getParam('site_title'),
			'RealName' => $aMemberInfo['FullName'] ? $aMemberInfo['FullName'] : "{$aMemberInfo['FirstName']} {$aMemberInfo['LastName']}",
			'Sum' => $this -> getParam('pmt_default_currency_sign') . $aData['price'],
			'Date' => date('d.m.y H:i'),
			'HistoryPage' => BX_DOL_URL_ROOT . 'modules/index.php?r=aqb_affiliate/history'	
		);
		
		$iUseLang = $aMemberInfo['LangID'] ? $aMemberInfo['LangID'] : $iDefaultLangId;
		$aTemplate = $this -> getRow("SELECT `Subject`, `Body` FROM `sys_email_templates` WHERE `Name`= 't_AqbAffCommissionPaid' AND (`LangID` = '" . (int) $iUseLang . "' OR `LangID` = '0') ORDER BY `LangID` DESC LIMIT 1");
		
		foreach($aKeys as $sKey => $sValue){
			$aTemplate['Subject'] = str_replace("<$sKey>", $sValue, $aTemplate['Subject']);
			$aTemplate['Body'] = str_replace("<$sKey>", $sValue, $aTemplate['Body']);
		}	
	
		return array_merge($aTemplate, array('Email' => $aMemberInfo['Email']));
	}
	
	function getMembersInfo($iMemberId){
		return $this -> getRow("SELECT * FROM `Profiles` WHERE `ID` = '{$iMemberId}' LIMIT 1");
	}
	
	function updateTransaction($aValue){
		$iTime = time();
		$aInfo = $this -> getRow("SELECT * FROM `{$this -> _sPrefix}transactions` WHERE CAST(`price` AS DECIMAL) = CAST('{$aValue['price']}' AS DECIMAL) AND `payment_status` = 'pending' AND `member_id` = '{$aValue['member_id']}' AND `status` = 'unpaid' ORDER BY `date_start` DESC LIMIT 1");
		if (empty($aInfo))return false;
		
		if ($this -> isPointsSystemInstalled() && (int)$aInfo['points']){ 
		    $aAction = array('id' => 0, 'title' => '_aqb_aff_assign_points', 'points' => (int)$aInfo['points'], 'time' => $iTime);
			$this -> assignPoints((int)$aInfo['member_id'], $aAction);
		}	
				
		$aParams = array(
							'client' => 0,
							'seller' => $aInfo['member_id'],
							'items' => "{$aInfo['member_id']}_" . $this -> getModuleId(). "_{$aInfo['id']}_1",
							'amount' => $aInfo['price'],
							'order'  => $aValue['tnx'],
							'provider' => 'paypal',
							'item_id' => $aInfo['id']								
						 );
						 
		$this -> createCartItems($aParams);
		return $this -> paidCommission((int)$aInfo['id'], $aValue['tnx'], $iTime);
	}
	
	function getModuleId(){
		return $this -> getOne("SELECT `id` FROM `sys_modules` WHERE `uri` = 'aqb_affiliate' LIMIT 1");
	}
	
	
	function getPayPalNumber(){
		return $this -> getOne("SELECT `provider_id` FROM `bx_pmt_providers_options` WHERE `name` = 'pp_active' LIMIT 1");
	}
	
	function getMemberByPaypalEmail($sEmail){
		return (int)$this -> getOne("SELECT `user_id` FROM `bx_pmt_user_values` as `v` LEFT JOIN `bx_pmt_providers_options` as `o` ON `v`.`option_id` = `o`.`id` WHERE `name` = 'pp_business' AND `value` = '{$sEmail}'");
	}
	
	function getAllAvailableTransactionForPayment($sWhere = ''){
		if ($sWhere) $sWhere = " AND `id` IN {$sWhere} ";
									
		return $this -> getAll("SELECT 
								DATE_FORMAT(FROM_UNIXTIME(`date_start`),  '" . $this -> _oConfig -> getDateFormat() . "' ) AS `date_start`,
								`points`, `member_id`, `price`, `id`,`payment_status` 
								FROM `{$this -> _sPrefix}transactions` 
								LEFT JOIN (SELECT `user_id` FROM `bx_pmt_user_values` as `v` LEFT JOIN `bx_pmt_providers_options` as `o` ON `v`.`option_id` = `o`.`id` WHERE `name` = 'pp_active' AND `value` = 'on') as `p`
								ON `member_id` = `p`.`user_id` WHERE `payment_status` = '' {$sWhere}");
	}
	
	function getPaymentEmail($iMemberId){
		return $this -> getOne("SELECT `value` FROM `bx_pmt_user_values` as `v` LEFT JOIN `bx_pmt_providers_options` as `o` ON `v`.`option_id` = `o`.`id` WHERE `name` = 'pp_business' AND `user_id` = '{$iMemberId}' LIMIT 1");
	}
	
	function makeTransactionPending($iId){
		$this -> query("UPDATE `{$this -> _sPrefix}transactions` SET `payment_status` = 'pending' WHERE `id` = '{$iId}'"); 
	}
	
	function isIPNInstalled(){
		return (int)$this -> getOne("SELECT  `value` FROM  `bx_pmt_user_values` AS  `v` LEFT JOIN  `bx_pmt_providers_options` AS  `o` ON  `v`.`option_id` =  `o`.`id` WHERE  `user_id` =  '0' AND  `name` =  'pp_prc_type'") == 3;
	}
}	
?>