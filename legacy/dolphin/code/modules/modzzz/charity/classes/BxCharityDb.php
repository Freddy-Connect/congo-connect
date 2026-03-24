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
 * Charity module Data
 */
class BxCharityDb extends BxDolTwigModuleDb {	
	
	var $_oConfig;

	/*
	 * Constructor.
	 */
	function BxCharityDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);

		$this->_oConfig = $oConfig;

		/********Event ************/
        $this->_sTableEvent = 'event_main';
        $this->_sEventPrefix = 'modzzz_charity_event';
        $this->_sTableEventMediaPrefix = 'event_';
  
		/********Staff ************/
        $this->_sTableStaff = 'staff_main';
        $this->_sStaffPrefix = 'modzzz_charity_staff';
        $this->_sTableStaffMediaPrefix = 'staff_';

		/********Supporter ************/
        $this->_sTableSupporter = 'supporter_main';
        $this->_sSupporterPrefix = 'modzzz_charity_supporter';
        $this->_sTableSupporterMediaPrefix = 'supporter_';
 
		/********News ************/
        $this->_sTableNews = 'news_main';
        $this->_sNewsPrefix = 'modzzz_charity_news';
        $this->_sTableNewsMediaPrefix = 'news_';
 
		/********Members ************/
		$this->_sTableMembers = 'members_main'; 
        $this->_sMembersPrefix = 'modzzz_charity_members';
	    $this->_sTableMembersMediaPrefix = 'members_';

		/********Programs ************/
		$this->_sTablePrograms = 'programs_main'; 
        $this->_sProgramsPrefix = 'modzzz_charity_programs';
	    $this->_sTableProgramsMediaPrefix = 'programs_';

		/********Branches ************/
		$this->_sTableBranches = 'branches_main'; 
        $this->_sBranchesPrefix = 'modzzz_charity_branches';
	    $this->_sTableBranchesMediaPrefix = 'branches_';
 
		/********Review ************/
        $this->_sTableReview = 'review_main';
        $this->_sReviewPrefix = 'modzzz_charity_review';
        $this->_sTableReviewMediaPrefix = 'review_';
		/********[END] Review ************/
 
		/********Faq ************/
        $this->_sTableFaq = 'faq_main';
        $this->_sTableFaqItems = 'faq_items';
        $this->_sFaqPrefix = 'modzzz_charity_faq';
		/********[END] Faq ************/
 
        $this->_sTableMain = 'main';
        $this->_sTableMediaPrefix = '';
        $this->_sFieldId = 'id';
        $this->_sFieldAuthorId = 'author_id';
        $this->_sFieldUri = 'uri';
        $this->_sFieldTitle = 'title';
        $this->_sFieldDescription = 'desc';
        $this->_sFieldTags = 'tags';
        $this->_sFieldThumb = 'thumb';
        $this->_sFieldStatus = 'status';
        $this->_sFieldFeatured = 'featured';
        $this->_sFieldCreated = 'created';
        $this->_sTableAdmins = 'admins';
        $this->_sFieldAllowViewTo = 'allow_view_charity_to';
	}
  
	function getCharities(){
		return $this->getAll("SELECT `id`, `uri`, `title` FROM `" . $this->_sPrefix . "main` ORDER BY `title` ASC"); 
	}
 
	function setItemStatus($iItemId, $sStatus) {
		
 		 $this->query("UPDATE `" . $this->_sPrefix . "main` SET `status`='$sStatus' WHERE `id`='$iItemId'"); 
	}

	function setInvoiceStatus($iItemId, $sStatus) {
		
 		 $this->query("UPDATE `" . $this->_sPrefix . "invoices` SET `invoice_status`='$sStatus' WHERE `charity_id`='$iItemId'"); 
	}
  
 	function getProfileAdmins() { 
	
 		$aAllAdmins = $this->getAll("SELECT `ID` FROM `Profiles` WHERE `Role` & " . BX_DOL_ROLE_ADMIN . " OR `Role` & " . BX_DOL_ROLE_MODERATOR . " AND `Status`='Active'"); 
		
		return $aAllAdmins; 
	}

 	function isOwnerAdmin($iProfileId) {
	 
 		$bAdmin = $this->getOne("SELECT `ID` FROM `Profiles` WHERE `ID`='$iProfileId' AND (`Role` & " . BX_DOL_ROLE_ADMIN . " OR `Role` & " . BX_DOL_ROLE_MODERATOR . ") AND `Status`='Active'"); 
		
		return $bAdmin; 
	}

	function saveClaimRequest($iEntryId, $iProfileId, $sClaimText) {
		$sClaimText = process_db_input($sClaimText);
 		$iTime = time();

		$aAllEntries = $this->query("INSERT INTO `" . $this->_sPrefix . "claim` SET `charity_id`='$iEntryId', `member_id`='$iProfileId', `message`='$sClaimText', `processed`=0, `claim_date`=$iTime");  

	}

 	function getClaimById($iEntryId) { 
		$aClaim = $this->getRow("SELECT `id`,`charity_id`,`member_id`,`message`,`processed`,`claim_date`,`assign_date` FROM `" . $this->_sPrefix . "claim` WHERE `id`='$iEntryId'");   
	
		return $aClaim;
	}
 
	function deleteClaim($iEntryId, $isAdmin=false) { 

		if(!$isAdmin)
			return;
 
		$this->query("DELETE FROM `" . $this->_sPrefix . "claim` WHERE `id`='$iEntryId'");   
	}
  
	function assignClaim($iClaimId, $isAdmin=false) { 

		if(!$isAdmin)
			return;

 		$iTime = time();

 		$aClaim = $this->getClaimById($iClaimId);
		$iClaimantId = (int)$aClaim['member_id'];
 		$iEntryId = (int)$aClaim['charity_id'];

		$this->query("UPDATE `" . $this->_sPrefix . "main` SET `author_id`=$iClaimantId WHERE `id`='$iEntryId'");   

		$this->query("UPDATE `" . $this->_sPrefix . "claim` SET `assign_date`=$iTime, `processed`=1 WHERE `id`='$iClaimId'");   

		$this->alertOnAction('modzzz_charity_claim_assign', $iEntryId, $iClaimantId );  
	}
 
    /**begin- package functions **/  

	function getPackageIdByInvoiceNo($sInvoiceNo){
		$aInvoice = $this->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];
		
		return $iPackageId;
	}
  
    function deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin) {
        if ($iRet = parent::deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin)) {
            $this->query ("DELETE FROM `" . $this->_sPrefix . "admins` WHERE `id_entry` = $iId");
            $this->deleteEntryMediaAll ($iId, 'images');
            $this->deleteEntryMediaAll ($iId, 'videos');
            $this->deleteEntryMediaAll ($iId, 'sounds');
            $this->deleteEntryMediaAll ($iId, 'files');

 			$this->query("DELETE FROM `" . $this->_sPrefix . "claim` WHERE `charity_id`='$iId'");  
            $this->query ("DELETE FROM `" . $this->_sPrefix . "invoices` WHERE `charity_id` = $iId");
            $this->query ("DELETE FROM `" . $this->_sPrefix . "orders` WHERE `buyer_id` = $iOwner");
            $this->query ("DELETE FROM `" . $this->_sPrefix . "featured_orders` WHERE `buyer_id` = $iOwner"); 

        }
        return $iRet;
    }
 
	function getNumberList($iStart, $iEnd, $iIncrement=1) {
	
		$aVals=array();
		$aVals[0] = ''; 
		for($iter=$iStart; $iter<=$iEnd; $iter+=$iIncrement) { 
			$aVals[$iter] = $iter; 
		}

		return $aVals;
	}

	function getYearList($iNumYears) {
	
		$iNow = date("Y");
		$aVals=array();
		$aVals[0] = ''; 
		for($iter=$iNow; $iter>=($iNow-$iNumYears); $iter--)
		{
			$aVals[$iter] = $iter; 
		}
  
		return $aVals;
	}

	/*[begin] map*/
    function updateLocation ($iAuthorId, $iId, $fLat, $fLng, $iZoom, $iType) {
        return $this->query ("INSERT INTO `" . $this->_sPrefix . "profiles` SET `id` = '$iId', `author_id` = '$iAuthorId', `ts` = UNIX_TIMESTAMP(), `lat` = '$fLat', `lng` = '$fLng', `zoom` = '$iZoom', `type` = '$iType' ON DUPLICATE KEY UPDATE `ts` = UNIX_TIMESTAMP(), `lat` = '$fLat', `lng` = '$fLng', `zoom` = '$iZoom', `type` = '$iType'");
    }

    function deleteLocation ($iId) {
        return $this->query ("DELETE FROM `" . $this->_sPrefix . "profiles` WHERE `id` = '$iId'");
    }

    function insertCountryLocation ($sCountryCode, $fLat, $fLng, $isFailed = 0) {
        return $this->query ("INSERT INTO `" . $this->_sPrefix . "countries` SET `country` = '$sCountryCode', `lat` = '$fLat', `lng` = '$fLng', `failed` = '$isFailed' ON DUPLICATE KEY UPDATE `lat` = '$fLat', `lng` = '$fLng', `failed` = '$isFailed'");
    }

    function insertCityLocation ($sCountryCode, $sCity, $fLat, $fLng, $isFailed = 0) {
        return $this->query ("INSERT INTO `" . $this->_sPrefix . "cities` SET `country` = '$sCountryCode', `city` = '$sCity', `lat` = '$fLat', `lng` = '$fLng', `failed` = '$isFailed' ON DUPLICATE KEY UPDATE `lat` = '$fLat', `lng` = '$fLng', `failed` = '$isFailed'");
    }

    function insertProfileLocation ($iAuthorId, $iId, $fLat, $fLng, $iMapZoom, $sMapType, $sAddress, $sCountry, $iPrivacy = 0, $isFailed = 0) {
        $sPrivacyUpdate = '';
        $sPrivacyInsert = "`allow_view_location_to` = '" . BX_CHARITY_DEFAULT_PRIVACY . "',";
        if ($iPrivacy) {
            $sPrivacyInsert = $sPrivacyUpdate = "`allow_view_location_to` = '$iPrivacy',";
        }
        return $this->query ("INSERT INTO `" . $this->_sPrefix . "profiles` SET `id` = '$iId', `author_id` = '$iAuthorId', `lat` = '$fLat', `lng` = '$fLng', `zoom` = '$iMapZoom', `type` = '$sMapType', `address` = '$sAddress', `country`= '$sCountry', $sPrivacyInsert `ts` = UNIX_TIMESTAMP(), `failed` = '$isFailed' ON DUPLICATE KEY UPDATE  `lat` = '$fLat', `lng` = '$fLng', `zoom` = '$iMapZoom', `type` = '$sMapType', `address` = '$sAddress', `country`= '$sCountry', $sPrivacyUpdate `ts` = UNIX_TIMESTAMP(), `failed` = '$isFailed'");
    }

    function getUndefinedCountries ($iLimit) {
        return $this->getPairs ("SELECT `c`.`ISO2`, `c`.`country` FROM `sys_countries` AS `c` LEFT JOIN `" . $this->_sPrefix . "countries` AS `m` ON (`m`.`country` = `c`.`ISO2`) WHERE ISNULL(`m`.`country`) LIMIT $iLimit", 'ISO2', 'Country');
    }

    function getUndefinedCities ($iLimit) {
        return $this->getPairs ("SELECT `p`.`city`, `p`.`country` FROM `" . $this->_sPrefix . "main` AS `p` LEFT JOIN `" . $this->_sPrefix . "cities` AS `m` ON (`m`.`country` = `p`.`country` AND `m`.`city` = `p`.`city`) WHERE ISNULL(`m`.`country`) LIMIT $iLimit", 'country', 'city');
    }    

    function getUndefinedProfiles ($iLimit) {
        return $this->getAllWithKey ("SELECT `p`.* FROM `" . $this->_sPrefix . "main` AS `p` LEFT JOIN `" . $this->_sPrefix . "profiles` AS `m` ON (`m`.`id` = `p`.`id`) WHERE ISNULL(`m`.`id`) LIMIT $iLimit", 'id');
    }    

    function getProfileInfo ($iID) {
        return $this->getRow("SELECT * FROM `" . $this->_sPrefix . "main` WHERE `id`='$iID'");
    }    
 
    function clearProfiles ($isClearFailedOnly) {
        return $this->_clearTable ($isClearFailedOnly, 'profiles');
    }

    function clearCountries ($isClearFailedOnly) {
        return $this->_clearTable ($isClearFailedOnly, 'countries');
    }

    function clearCities ($isClearFailedOnly) {
        return $this->_clearTable ($isClearFailedOnly, 'cities');
    }

    function _clearTable ($isClearFailedOnly, $sTable) {
        if ($isClearFailedOnly) {
            $ret = $this->query ("DELETE FROM `" . $this->_sPrefix . "$sTable` WHERE `failed` != 0");
            $this->query ("OPTIMIZE TABLE `" . $this->_sPrefix . "$sTable`");
            return $ret;
        } else {
            return $this->query ("TRUNCATE TABLE `" . $this->_sPrefix . "$sTable`");
        }
    }    

    function getProfileById($iProfileId) { 
        return $this->getRow("SELECT `m`.`id`, `p`.`title`, `p`.`address1`, `p`.`address2`, `p`.`thumb`, `m`.`lat`, `m`.`lng`, `m`.`zoom`, `m`.`type`, `m`.`address`, `m`.`country`, `p`.`city`, `m`.`allow_view_location_to` FROM `" . $this->_sPrefix . "profiles` AS `m` INNER JOIN `" . $this->_sPrefix . "main` AS `p` ON (`p`.`id` = `m`.`id`) WHERE `m`.`failed` = 0 AND `p`.`status` = 'approved' AND `m`.`id` = '$iProfileId' LIMIT 1");
    } 

    function getAuthorById($iProfileId) { 
        return $this->getRow("SELECT `author_id` FROM `" . $this->_sPrefix . "main` WHERE `id` = '$iProfileId' LIMIT 1");
    } 


    function getProfilesByBounds($fLatMin, $fLatMax, $fLngMin, $fLngMax) {
        $sWhere = $this->_getLatLngWhere ($fLatMin, $fLatMax, $fLngMin, $fLngMax);
        return $this->getAll("SELECT `m`.`id`, `p`.`thumb`, `p`.`title` AS `NickName`, `m`.`lat`, `m`.`lng` FROM `" . $this->_sPrefix . "profiles` AS `m` INNER JOIN `" . $this->_sPrefix . "main` AS `p` ON (`p`.`id` = `m`.`id`) WHERE `m`.`failed` = 0 AND `p`.`status` = 'approved' AND `m`.`allow_view_location_to` = '" . BX_DOL_PG_ALL . "' $sWhere LIMIT 100");
    } 

    function getCountryByCode($sCountryCode, $isScrict = true) {

        $sJoin = $isScrict ? 'INNER' : 'LEFT';

        return $this->getRow("SELECT `m`.`country`, `m`.`lat`, `m`.`lng`, COUNT(`p`.`id`) AS `num`
            FROM `" . $this->_sPrefix . "countries` AS `m` 
            $sJoin JOIN `" . $this->_sPrefix . "main` AS `p` ON (`p`.`country` = `m`.`country` AND `p`.`status` = 'approved') 
            $sJoin JOIN `" . $this->_sPrefix . "profiles` AS `pm` ON (`pm`.`id` = `p`.`id` AND `pm`.`failed` = 0 AND `pm`.`allow_view_location_to` = '" . BX_DOL_PG_ALL . "')
            WHERE `m`.`failed` = 0 AND `m`.`country` = '$sCountryCode'
            GROUP BY `p`.`country`
            LIMIT 1"); 
    } 

    function getCountriesByBounds($fLatMin, $fLatMax, $fLngMin, $fLngMax) {
        $sWhere = $this->_getLatLngWhere ($fLatMin, $fLatMax, $fLngMin, $fLngMax);
        return $this->getAll("SELECT `m`.`country`, `m`.`lat`, `m`.`lng`, COUNT(`p`.`id`) AS `num`
            FROM `" . $this->_sPrefix . "countries` AS `m` 
            INNER JOIN `" . $this->_sPrefix . "main` AS `p` ON (`p`.`country` = `m`.`country` AND `p`.`status` = 'approved') 
            INNER JOIN `" . $this->_sPrefix . "profiles` AS `pm` ON (`pm`.`id` = `p`.`id` AND `pm`.`failed` = 0 AND `pm`.`allow_view_location_to` = '" . BX_DOL_PG_ALL . "')
            WHERE `m`.`failed` = 0 $sWhere 
            GROUP BY `p`.`country`
            LIMIT 100"); 
    } 

    function getCitiesByBounds($fLatMin, $fLatMax, $fLngMin, $fLngMax) {
        $sWhere = $this->_getLatLngWhere ($fLatMin, $fLatMax, $fLngMin, $fLngMax);
        return $this->getAll("SELECT `m`.`country`, `m`.`city`, `m`.`lat`, `m`.`lng`, COUNT(`p`.`id`) AS `num`
            FROM `" . $this->_sPrefix . "cities` AS `m`
            INNER JOIN `" . $this->_sPrefix . "main` AS `p` ON (`p`.`country` = `m`.`country` AND `p`.`city` = `m`.`city` AND `p`.`status` = 'approved')
            INNER JOIN `" . $this->_sPrefix . "profiles` AS `pm` ON (`pm`.`id` = `p`.`id` AND `pm`.`failed` = 0 AND `pm`.`allow_view_location_to` = '" . BX_DOL_PG_ALL . "')
            WHERE `m`.`failed` = 0 $sWhere 
            GROUP BY `m`.`city`
            LIMIT 100"); 
    } 

    function _getLatLngWhere ($fLatMin, $fLatMax, $fLngMin, $fLngMax) {

        $sWhere = " AND `m`.`lat` < $fLatMax AND `m`.`lat` > $fLatMin ";
        if ($fLngMin < $fLngMax)
            $sWhere .= " AND `m`.`lng` < $fLngMax AND `m`.`lng` > $fLngMin ";
        else
            $sWhere .= " AND ((`m`.`lng` < $fLngMax AND `m`.`lng` > -180) OR (`m`.`lng` < 180 AND `m`.`lng` > $fLngMin)) ";
        return $sWhere;
    }    

    function isCityLocationExists($sCountryCode, $sCity) {
        return $this->getOne("SELECT `m`.`country` FROM `" . $this->_sPrefix . "cities` AS `m` WHERE `m`.`country` = '$sCountryCode' AND `m`.`city` = '$sCity' AND `m`.`failed` = 0 LIMIT 1") ? true : false;
    }

    function getSettingsCategory($s) {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = '$s' LIMIT 1");
    }   

	/*[end] map*/
 
 
    function saveTransactionRecord($iBuyerId, $iCharityId, $sTransNo, $sTransType) {
        $iBuyerId    = (int)$iBuyerId;
        $iCharityId  = (int)$iCharityId; 
   
		$aDataEntry = $this->getEntryById($iCharityId);
        $sInvoiceNo  = $aDataEntry['invoice_no']; 
  		$iTime = time();

         $bProcessed = $this->query("INSERT INTO `" . $this->_sPrefix . "orders` 
							SET `buyer_id` = {$iBuyerId}, 
							`invoice_no` =  '{$sInvoiceNo}', 
							`order_no` =  '{$sTransNo}',  
							`payment_method`  = '{$sTransType}', 
  							`order_date`  = $iTime  
						"); 

		$iOrderId = $this->lastId();
 
		 //if order successful, update expiration date of charity
		 if($iOrderId){
			$aInvoice = $this->getInvoiceByNo($sInvoiceNo);
			$iDays = (int)$aInvoice['days'];
			$this->updateEntryExpiration($iCharityId, $iDays);

			$iPackageId = (int)$aInvoice['package_id'];
			$aPackage = $this->getPackageById($iPackageId); 
			$iFeatured = (int)$aPackage['featured'];
			if($iFeatured){
				$this->updateFeaturedStatus($iCharityId);
			}
		 }

		 return $iOrderId; 
    }

    function isExistPaypalTransaction($iBuyerId, $sTransNo) {
        $iBuyerId  = (int)$iBuyerId;
 
        return $this->getOne("SELECT COUNT(`order_no`) FROM `" . $this->_sPrefix . "orders` 
            WHERE `buyer_id` = {$iBuyerId} AND `order_no` =  '{$sTransNo}'  
        "); 
    }

	function createInvoice($iEntryId, $iPackageId, $fPrice, $iDays, $sStatus='pending'){
         
		 $iEntryId = intval($iEntryId);
		 $iPackageId = intval($iPackageId); 
		 $fPrice = floatval($fPrice); 
		 $iDays = intval($iDays);
 		 $iCreated = time();
		 $iExpireDate = 0; 
 		 $sInvoiceNo = $this->_getLicense();

		 $iInvoiceActiveDays = (int)getParam('modzzz_charity_invoice_valid_days');
 		 if($iInvoiceActiveDays) {
			 $SECONDS_IN_DAY = 86400; 
			 $iCreated = time();
			 $iExpireDate = $iCreated + ($SECONDS_IN_DAY * $iInvoiceActiveDays);
		 }

		 $this->query("INSERT INTO `" . $this->_sPrefix . "invoices` 
							SET `invoice_no` = '{$sInvoiceNo}',  
							`price` = {$fPrice},
							`days` = {$iDays},
							`charity_id` = {$iEntryId},
							`package_id` = {$iPackageId},
							`invoice_due_date`  = $iCreated,
							`invoice_expiry_date`  = $iExpireDate,
							`invoice_date`  = $iCreated,
							`invoice_status`  = '$sStatus'  

        "); 
 
		return $sInvoiceNo; 
	}

	function _getLicense() {
		list($fMilliSec, $iSec) = explode(' ', microtime());
		$fSeed = (float)$iSec + ((float)$fMilliSec * 100000);
		srand($fSeed);

		$sResult = '';
		for($i=0; $i < 16; ++$i) {
			switch(rand(1,2)) {
				case 1: 
					$c = chr(rand(ord('A'),ord('Z')));
					break;
				case 2: 
					$c = chr(rand(ord('0'),ord('9')));
					break;
			}
			$sResult .= $c;
		}
		return $sResult;
	}
 
    function updateEntryInvoice($iEntryId, $sInvoiceNo) { 
        return $this->query("UPDATE `" . $this->_sPrefix . "main` SET `invoice_no` = '$sInvoiceNo' WHERE `id` = '{$iEntryId}'"); 
    }
    
	function updateFeaturedStatus($iEntryId) {  
		$this->query("UPDATE `" . $this->_sPrefix . "main` SET `featured`=1 WHERE `id`=$iEntryId");
	}

    function updateEntryExpiration($iEntryId, $iDays, $bUpdateStatus=false) { 

         $aEntry = $this->getRow("SELECT `status`, `expiry_date` FROM `" . $this->_sPrefix . "main` WHERE `id` = '{$iEntryId}'"); 
		 $iExpireDate = (int)$aEntry['expiry_date'];
		 $bExpired = ($aEntry['status']=='expired');

		 $SECONDS_IN_DAY = 86400; 
		 $iExpireDate = ($iExpireDate) ? $iExpireDate : time();
		 $iExpireDate = ($bExpired) ? time() : $iExpireDate;
		 $iExpireDate += ($SECONDS_IN_DAY * $iDays);
 
		 if($bUpdateStatus)
			$sExtraUpdate = ", `status`='approved'";

         return $this->query("UPDATE `" . $this->_sPrefix . "main` SET `expiry_date` =  $iExpireDate {$sExtraUpdate} WHERE `id` = '{$iEntryId}'"); 
    }

    /** Invoice functions*/
    function deleteInvoice($iId, $isAdmin=false) { 

		if(!$isAdmin)
			return;

        return $this->query("DELETE FROM `" . $this->_sPrefix . "invoices` WHERE `id` = '{$iId}'"); 
    }

    function getInvoiceByNo($sInvoiceNo) {  
        return $this->getRow("SELECT `invoice_no`, `price`, `days`, `charity_id`, `package_id`, `invoice_status`, `invoice_due_date`, `invoice_expiry_date`, `invoice_date` FROM `" . $this->_sPrefix . "invoices` WHERE `invoice_no` = '{$sInvoiceNo}'"); 
    }
 
    /** Order functions*/ 
    function deleteOrder($iId, $isAdmin=false) { 

		if(!$isAdmin)
			return;

        return $this->query("DELETE FROM `" . $this->_sPrefix . "orders` WHERE `id` = '{$iId}'"); 
    }
  
	function activateOrder($iId, $isAdmin=false) { 

		if(!$isAdmin)
			return;
		
        return $this->query("UPDATE `" . $this->_sPrefix . "orders` SET `order_status`='approved'  WHERE `order_status`='pending' AND `id` = '{$iId}'"); 
    }
 
   /** [begin] category functions*/
	function getCategories($sType)
	{ 
 		$aAllEntries = $this->getAll("SELECT `Category` FROM `sys_categories` WHERE `Type` = '{$sType}' AND `Status`='active' AND Owner=0"); 
		
		return $aAllEntries; 
	}

	function getCategoryCount($sType,$sCategory)
	{ 
		$sCategory = process_db_input($sCategory);

		$iNumCategory = $this->getOne("SELECT count(`" . $this->_sPrefix . "main`.`id`) FROM `" . $this->_sPrefix . "main`  inner JOIN `sys_categories` ON `sys_categories`.`ID`=`" . $this->_sPrefix . "main`.`id` WHERE 1 AND  `sys_categories`.`Category` IN('{$sCategory}') AND `sys_categories`.`Type` = '{$sType}' AND `" . $this->_sPrefix . "main`.`status`='approved'"); 
		
		return $iNumCategory;
	}
	/** [end] category functions*/

	/** state functions*/

	function getStateCount($sState){
		
		if (!$GLOBALS['logged']['admin']){ 
			if ($GLOBALS['logged']['member']){ 
				$aProfile = getProfileInfo($_COOKIE['memberID']); 
				require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
				$aMembershipInfo = getMemberMembershipInfo($_COOKIE['memberID']); 
				$iMembershipId = $aMembershipInfo['ID']; 

			    $sExtraCheck = " AND `membership_view_filter` IN ('', '$iMembershipId')"; 
			}else{
				$sExtraCheck = "AND `membership_view_filter`=''";
			}
		}
 
		return  $this->getOne("SELECT COUNT(`id`) FROM `" . $this->_sPrefix . "main` WHERE  `state` = '$sState'  AND `status`='approved' {$sExtraCheck}"); 
	}
   
 	function getStateName($sCountry, $sState=''){
		$sState = process_db_input($sState);

		$sState = $this->getOne("SELECT `State` FROM `States` WHERE `CountryCode`='{$sCountry}' AND `StateCode`='{$sState}' LIMIT 1");
 
		return $sState;
	}

	function getStateOptions($sCountry='') {
		$aStates = $this->getStateArray ($sCountry);
 
		foreach($aStates as $aEachCode=>$aEachState){ 
			$sOptions .= "<option value='{$aEachCode}'>{$aEachState}</option>";
		}

		return $sOptions;
	}

 	function getStateArray($sCountry=''){
 
		$aStates = array();
		$aDbStates = $this->getAll("SELECT * FROM `States` WHERE `CountryCode`='{$sCountry}'  ORDER BY `State` ");
		 
		$aStates[] = '';
		foreach ($aDbStates as $aEachState){
			$sState = $aEachState['State'];
			$sStateCode = $aEachState['StateCode'];
			
			$aStates[$sStateCode] = $sState;
  		} 
		return $aStates;
	}


	/*featured functions*/ 
    function saveFeaturedTransactionRecord($iBuyerId, $iCharityId, $iQuantity, $fPrice, $sTransId, $sTransType) {
        $iBuyerId    = (int)$iBuyerId;
        $iCharityId  = (int)$iCharityId; 
        $iQuantity  = (int)$iQuantity; 
		$iTime = time();

		$aDataEntry = $this->getEntryById($iCharityId);
   
        $bProcessed = $this->query("INSERT INTO `" . $this->_sPrefix . "featured_orders` 
							SET `buyer_id` = {$iBuyerId}, 
							`price` = {$fPrice},
							`days` = {$iQuantity},
							`item_id` =  {$iCharityId},
 							`trans_id` = '{$sTransId}',  
							`trans_type` = '{$sTransType}', 
  							`created` = $iTime  
        "); 
 
		$iOrderId = $this->lastId();
   
		if($iOrderId){
			$this->alertOnAction('modzzz_charity_featured_admin_notify', $iCharityId, $iBuyerId, $iQuantity, true);

			$this->alertOnAction('modzzz_charity_featured_buyer_notify', $iCharityId, $iBuyerId, $iQuantity);
		}

		return $iOrderId; 
    }

    function updateFeaturedEntryExpiration($iEntryId, $iDays) { 

		 $SECONDS_IN_DAY = 86400; 
		 $iCreated = time();
 
		 $aDataEntry = $this->getEntryById($iEntryId);
		 $iExistExpireDate = $aDataEntry['featured_expiry_date'];
		
		 if($iExistExpireDate < $iCreated){ 
		     $iExpireDate = $iCreated + ($SECONDS_IN_DAY * $iDays);

			 $bProcessed = $this->query("UPDATE `" . $this->_sPrefix . "main` SET `featured`=1, `featured_expiry_date` = $iExpireDate, `featured_date`=$iCreated WHERE `id` = '{$iEntryId}'"); 
		 }else{
		     $iExpireDate = ($SECONDS_IN_DAY * $iDays);

			 $bProcessed = $this->query("UPDATE `" . $this->_sPrefix . "main` SET `featured`=1, `featured_expiry_date` = `featured_expiry_date` + $iExpireDate, `featured_date`=$iCreated  WHERE `id` = '{$iEntryId}'"); 
		 }
	
		 return $bProcessed;
	}


    function isExistFeaturedTransaction($iBuyerId, $sTransID) {
        $iBuyerId  = (int)$iBuyerId;
 
        return $this->getOne("SELECT COUNT(`trans_id`) FROM `" . $this->_sPrefix . "featured_orders` 
            WHERE `buyer_id` = {$iBuyerId} AND `trans_id` =  '{$sTransID}'  
        "); 
    }
  
	function processCharities(){ 
 
		$this->processFeaturedCharities();
	}
 
	function processFeaturedCharities(){
		
		if(getParam('modzzz_charity_buy_featured') != 'on')
			return;

		$iTime = time();
   
        $aCharities = $this->getAll("SELECT `id`, `author_id`, `featured`, `featured_expiry_date` FROM `" . $this->_sPrefix . "main` WHERE `featured`=1 AND `featured_expiry_date`>0 AND `featured_expiry_date` <= $iTime"); 

		foreach($aCharities as $aEachList){
  
			$iCharityId = (int)$aEachList['id'];
			$iRecipientId = (int)$aEachList['author_id'];

			$this->alertOnAction('modzzz_charity_featured_expire_notify', $iCharityId, $iRecipientId  );
		
	        $this->query("UPDATE `" . $this->_sPrefix . "main` SET `featured`=0, `featured_expiry_date`=0, `featured_date`=0  WHERE `id`=$iCharityId"); 
		}

	}

	function alertOnAction($sTemplate, $iCharityId, $iRecipientId=0, $iDays=0, $bAdmin=false) {
	   
		$aPlus = array();

		if($iCharityId){
			$aDataEntry = $this->getEntryById($iCharityId);
			$aPlus['ListTitle'] = $aDataEntry['title']; 
			$aPlus['ListLink'] = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri']; 
		}

		if($iRecipientId){
			$aRecipient = getProfileInfo($iRecipientId); 
			$aPlus['NickName'] = $aRecipient['NickName']; 
			$aPlus['NickLink'] = getProfileLink($iRecipientId);
			$sNotifyEmail = $aRecipient['Email']; 
		}

		$aPlus['Days'] = $iDays; 
		$aPlus['SiteName'] = isset($GLOBALS['site']['title']) ? $GLOBALS['site']['title'] : getParam('site_title');
		$aPlus['SiteUrl'] = isset($GLOBALS['site']['url']) ;
 
		$oEmailTemplate = new BxDolEmailTemplates(); 

		$aTemplate = $oEmailTemplate->getTemplate($sTemplate, $iRecipientId);
		$sMessage = $aTemplate['Body'];
		$sSubject = $aTemplate['Subject'];   
		$sSubject = str_replace("<SiteName>", $aPlus['SiteName'], $sSubject);

		if($bAdmin){
			$sNotifyEmail = $GLOBALS['site']['email_notify'];
			$iRecipientId = 0;
		}
 
		sendMail($sNotifyEmail, $sSubject, $sMessage, $iRecipientId, $aPlus, 'html' );   
	}
  
	function getLatestComments($iLimit=5){

		$iLimit = ($iLimit) ? $iLimit : 5;

		return $this->getAll("SELECT `cmt_object_id`,`cmt_author_id`,`cmt_text`, UNIX_TIMESTAMP(`cmt_time`) AS `date` FROM `" . $this->_sPrefix . "cmts` cmt INNER JOIN `" . $this->_sPrefix . "main` m  ON cmt.`cmt_object_id`= m.`id` WHERE m.`allow_view_charity_to` = '". BX_DOL_PG_ALL ."' AND `cmt_text` NOT LIKE '<object%' ORDER BY `cmt_time` DESC LIMIT $iLimit"); 
	}  

	function getLatestForumPosts($iLimit=5, $iEntryId=0){

		$iLimit = ($iLimit) ? $iLimit : 5;
 
		if($iEntryId)
			$sQueryId = "t.`forum_id`='$iEntryId' AND ";

		return $this->getAll("SELECT e.`title`, f.`forum_uri`, p.`user`, p.`post_text`, t.`topic_uri`, t.`topic_title`,p.`when` FROM `" . $this->_sPrefix . "forum` f, `" . $this->_sPrefix . "forum_topic` t, `" . $this->_sPrefix . "forum_post` p, `" . $this->_sPrefix . "main` e WHERE  {$sQueryId} p.`topic_id`=t.`topic_id` AND t.`forum_id`=f.`forum_id` AND e.`id`=f.`entry_id` AND e.`allow_view_charity_to`='". BX_DOL_PG_ALL ."'  ORDER BY  p.`when` DESC LIMIT $iLimit");
	} 

  
	/***** NEWS **************************************/
 
    function getNewsEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getNewsEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteNewsByIdAndOwner ($iId, $iCharityId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `id` = $iId AND `charity_id`=$iCharityId $sWhere LIMIT 1")))
            return false;
 
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableNewsMediaPrefix . "images` WHERE `entry_id` = $iId");
 
        return true;
    } 
 
    function deleteNews ($iCharityId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        
		$aNews = $this->getAllSubItems('news', $iCharityId);
  
		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `charity_id`=$iCharityId $sWhere")))
            return false;

		foreach($aNews as $aEachNews){
			
			$iId = (int)$aEachNews['id'];
 
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableNewsMediaPrefix . "images` WHERE `entry_id` = $iId"); 
		}

        return true;
    } 
  	/***** [end] News **************************************/

	/***** begin] EVENT **************************************/
    function getEventEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableEvent . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getEventEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableEvent . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteEventByIdAndOwner ($iId, $iCharityId, $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEvent . "` WHERE `id` = $iId AND `charity_id`=$iCharityId $sWhere LIMIT 1")))
            return false;
 
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventMediaPrefix . "files` WHERE `entry_id` = $iId");
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventMediaPrefix . "images` WHERE `entry_id` = $iId");
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventMediaPrefix . "videos` WHERE `entry_id` = $iId");
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventMediaPrefix . "sounds` WHERE `entry_id` = $iId");

        return true;
    } 

    function deleteEvents ($iCharityId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
       
		$aEvents = $this->getAllSubItems('event', $iCharityId);

		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEvent . "` WHERE `charity_id`=$iCharityId $sWhere")))
            return false;

		foreach($aEvents as $aEachEvent){
			
			$iId = (int)$aEachEvent['id'];

			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventMediaPrefix . "files` WHERE `entry_id` = $iId");
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventMediaPrefix . "images` WHERE `entry_id` = $iId");
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventMediaPrefix . "videos` WHERE `entry_id` = $iId");
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventMediaPrefix . "sounds` WHERE `entry_id` = $iId"); 
		}

        return true;
    } 
    //[END] Events
 
	/***** begin] Supporter **************************************/
    function getSupporterEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableSupporter . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getSupporterEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableSupporter . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteSupporterByIdAndOwner ($iId, $iCharityId, $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSupporter . "` WHERE `id` = $iId AND `charity_id`=$iCharityId $sWhere LIMIT 1")))
            return false;
 
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSupporterMediaPrefix . "files` WHERE `entry_id` = $iId");
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSupporterMediaPrefix . "images` WHERE `entry_id` = $iId");
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSupporterMediaPrefix . "videos` WHERE `entry_id` = $iId");
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSupporterMediaPrefix . "sounds` WHERE `entry_id` = $iId");

        return true;
    } 

    function deleteSupporters ($iCharityId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
       
		$aSupporters = $this->getAllSubItems('supporter', $iCharityId);

		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSupporter . "` WHERE `charity_id`=$iCharityId $sWhere")))
            return false;

		foreach($aSupporters as $aEachSupporter){
			
			$iId = (int)$aEachSupporter['id'];

			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSupporterMediaPrefix . "files` WHERE `entry_id` = $iId");
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSupporterMediaPrefix . "images` WHERE `entry_id` = $iId");
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSupporterMediaPrefix . "videos` WHERE `entry_id` = $iId");
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSupporterMediaPrefix . "sounds` WHERE `entry_id` = $iId"); 
		}

        return true;
    } 
    //[END] Supporters

	/***** Staff **************************************/
    function getStaffEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableStaff . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getStaffEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableStaff . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteStaffByIdAndOwner ($iId, $iCharityId, $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStaff . "` WHERE `id` = $iId AND `charity_id`=$iCharityId $sWhere LIMIT 1")))
            return false;
 
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStaffMediaPrefix . "files` WHERE `entry_id` = $iId");
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStaffMediaPrefix . "images` WHERE `entry_id` = $iId");
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStaffMediaPrefix . "videos` WHERE `entry_id` = $iId");
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStaffMediaPrefix . "sounds` WHERE `entry_id` = $iId");

        return true;
    } 

    function deleteStaffs ($iCharityId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
       
		$aStaffs = $this->getAllSubItems('staff', $iCharityId);

		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStaff . "` WHERE `charity_id`=$iCharityId $sWhere")))
            return false;

		foreach($aStaffs as $aEachStaff){
			
			$iId = (int)$aEachStaff['id'];

			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStaffMediaPrefix . "files` WHERE `entry_id` = $iId");
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStaffMediaPrefix . "images` WHERE `entry_id` = $iId");
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStaffMediaPrefix . "videos` WHERE `entry_id` = $iId");
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableStaffMediaPrefix . "sounds` WHERE `entry_id` = $iId"); 
		}

        return true;
    } 
    //[END] Staff
  
	/***** MEMBERS **************************************/
 
 	function getMembers($iEntryId, $iLimit=0){
		
		if($iLimit)
			$sQuery = "LIMIT 0, {$iLimit}";

		return $this->getAll("SELECT `id`, `charity_id`, `uri`, `title`, `desc`, `position`,  `thumb` FROM `" . $this->_sPrefix . $this->_sTableMembers . "` WHERE `charity_id`={$iEntryId} {$sQuery}");
	}

    function getMembersEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableMembers . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getMembersEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableMembers . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteMembersByIdAndOwner ($iId, $iCharityId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableMembers . "` WHERE `id` = $iId AND `charity_id`=$iCharityId $sWhere LIMIT 1")))
            return false;
 
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableMembersMediaPrefix . "images` WHERE `entry_id` = $iId");
 
        return true;
    } 
 
    function deleteMembers ($iCharityId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        
		$aMembers = $this->getAllSubItems('members', $iCharityId);
  
		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableMembers . "` WHERE `charity_id`=$iCharityId $sWhere")))
            return false;

		foreach($aMembers as $aEachMembers){
			
			$iId = (int)$aEachMembers['id'];
 
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableMembersMediaPrefix . "images` WHERE `entry_id` = $iId"); 
		}

        return true;
    } 
	/*****[END] MEMBERS **************************************/
  

	/***** Programs **************************************/
 
 	function getPrograms($iEntryId, $iLimit=0){
		
		if($iLimit)
			$sQuery = "LIMIT 0, {$iLimit}";

		return $this->getAll("SELECT `id`, `charity_id`, `uri`, `title`, `desc`, `position`,  `thumb` FROM `" . $this->_sPrefix . $this->_sTablePrograms . "` WHERE `charity_id`={$iEntryId} {$sQuery}");
	}

    function getProgramsEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTablePrograms . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getProgramsEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTablePrograms . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteProgramsByIdAndOwner ($iId, $iCharityId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTablePrograms . "` WHERE `id` = $iId AND `charity_id`=$iCharityId $sWhere LIMIT 1")))
            return false;
 
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableProgramsMediaPrefix . "images` WHERE `entry_id` = $iId");
 
        return true;
    } 
 
    function deletePrograms ($iCharityId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        
		$aPrograms = $this->getAllSubItems('programs', $iCharityId);
  
		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTablePrograms . "` WHERE `charity_id`=$iCharityId $sWhere")))
            return false;

		foreach($aPrograms as $aEachPrograms){
			
			$iId = (int)$aEachPrograms['id'];
 
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableProgramsMediaPrefix . "images` WHERE `entry_id` = $iId"); 
		}

        return true;
    } 
	/*****[END] Programs **************************************/
  

	/***** Branches **************************************/
 
 	function getBranches($iEntryId, $iLimit=0){
		
		if($iLimit)
			$sQuery = "LIMIT 0, {$iLimit}";

		return $this->getAll("SELECT `id`, `charity_id`, `uri`, `title`, `desc`, `position`,  `thumb` FROM `" . $this->_sPrefix . $this->_sTableBranches . "` WHERE `charity_id`={$iEntryId} {$sQuery}");
	}

    function getBranchesEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableBranches . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getBranchesEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableBranches . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteBranchesByIdAndOwner ($iId, $iCharityId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableBranches . "` WHERE `id` = $iId AND `charity_id`=$iCharityId $sWhere LIMIT 1")))
            return false;
 
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableBranchesMediaPrefix . "images` WHERE `entry_id` = $iId");
 
        return true;
    } 
 
    function deleteBranches ($iCharityId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        
		$aBranches = $this->getAllSubItems('branches', $iCharityId);
  
		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableBranches . "` WHERE `charity_id`=$iCharityId $sWhere")))
            return false;

		foreach($aBranches as $aEachBranches){
			
			$iId = (int)$aEachBranches['id'];
 
			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableBranchesMediaPrefix . "images` WHERE `entry_id` = $iId"); 
		}

        return true;
    } 
	/*****[END] Branches **************************************/
   

	/***** [begin] Reviews **************************************/
    function getReviewEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableReview . "` WHERE `{$this->_sFieldId}` = $iId LIMIT 1");
    }
 
    function getReviewEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableReview . "` WHERE `{$this->_sFieldUri}` = '$sUri' LIMIT 1");
    }
 
    function deleteReviewByIdAndOwner ($iId, $iCharityId, $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `{$this->_sFieldAuthorId}` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableReview . "` WHERE `{$this->_sFieldId}` = $iId AND `charity_id`=$iCharityId $sWhere LIMIT 1")))
            return false;
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableReviewMediaPrefix . "files` WHERE `entry_id` = $iId");
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableReviewMediaPrefix . "images` WHERE `entry_id` = $iId");
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableReviewMediaPrefix . "videos` WHERE `entry_id` = $iId");
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableReviewMediaPrefix . "sounds` WHERE `entry_id` = $iId");
 
        return true;
    } 
	
	function getReviews($sType='', $sqlLimit='', $iEntryId=0) {
		$iEntryId = (int)$iEntryId; 
		if($iEntryId){ 
			$sIdQuery = ' AND `charity_id`='.$iEntryId;
		}

		if($sType)
			$sTypeQuery = " AND `category`='$sType'";

		$aAllEntries = $this->getAll("SELECT * FROM `" . $this->_sPrefix . $this->_sTableReview . "` WHERE 1 {$sTypeQuery} {$sIdQuery} ORDER BY `created` DESC {$sqlLimit}");

		return $aAllEntries;   
	}

 	function getReviewCount($sType='', $iEntryId=0) {
		$iEntryId = (int)$iEntryId;
		if($iEntryId){ 
			$sIdQuery = ' AND `charity_id`='.$iEntryId;
		}

		if($sType){
			$sTypeQuery = " AND `category`='$sType'";
		}

		$bCount = $this->getOne("SELECT COUNT(`id`) FROM `" . $this->_sPrefix . $this->_sTableReview . "` WHERE 1  {$sTypeQuery} {$sIdQuery}");

		return $bCount;   
	}
 
	/***** [END] Reviews **************************************/

	/***** [begin] Faqs **************************************/
    function getFaqEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableFaq . "` WHERE `{$this->_sFieldId}` = $iId LIMIT 1");
    }
 
    function getFaqEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableFaq . "` WHERE `{$this->_sFieldUri}` = '$sUri' LIMIT 1");
    }
 
    function getFaqByCharity($iCharityId) {
         return $this->getOne ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableFaq . "` WHERE `charity_id` = '$iCharityId' LIMIT 1");
    }
  
    function deleteFaqByIdAndOwner ($iId, $iCharityId, $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `{$this->_sFieldAuthorId}` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableFaq . "` WHERE `{$this->_sFieldId}` = $iId AND `charity_id`=$iCharityId $sWhere LIMIT 1")))
            return false;
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableFaqItems . "` WHERE `charity_id` = $iCharityId");
     
        return true;
    } 
  
 	function getFaqCount($iEntryId=0) {
		$iEntryId = (int)$iEntryId;
		if($iEntryId){ 
			$sIdQuery = ' AND `charity_id`='.$iEntryId;
		}
  
		$bCount = $this->getOne("SELECT COUNT(`id`) FROM `" . $this->_sPrefix . $this->_sTableFaqItems . "` WHERE 1 {$sIdQuery}");

		return $bCount;   
	}

	function getFaqItems($iCharityId, $sqlLimit='') {
		$iCharityId = (int)$iCharityId; 
	 
		$aAllEntries = $this->getAll("SELECT `id`, `faq_id`, `question`, `answer` FROM `" . $this->_sPrefix . $this->_sTableFaqItems . "` WHERE `charity_id`={$iCharityId} ORDER BY `question` ASC {$sqlLimit}");

		return $aAllEntries;   
	}
 
 	function addFaqItems($iFaqId, $iCharityId){
		$iCreated = time();

		if(is_array($_POST['question'])){ 
			foreach($_POST['question'] as $iKey=>$sQuestion){
				$sAnswer = trim($_POST['answer'][$iKey]);
			 
				if($sAnswer){
					$this->query("INSERT INTO `" . $this->_sPrefix . $this->_sTableFaqItems . "` SET `faq_id`=$iFaqId, `charity_id`='$iCharityId', `question`='$sQuestion', `answer`='$sAnswer', `created`=$iCreated");
				} 
 			} 
		} 
	}

 	function removeFaqItem(){
		if(is_array($_POST['item_faq'])){ 
			foreach($_POST['item_faq'] as $iItemId){ 
				$this->query("DELETE FROM `" . $this->_sPrefix . $this->_sTableFaqItems . "` WHERE `id`='$iItemId'");  
 			} 
		}
	}
 
	/***** [END] Faqs **************************************/

    function isSubProfileFan($sTable, $iSubEntryId, $iProfileId, $isConfirmed) {
        $isConfirmed = $isConfirmed ? 1 : 0;

        $iEntryId = (int)$this->getOne ("SELECT `charity_id` FROM `" . $this->_sPrefix . $sTable . "` WHERE `id` = '$iSubEntryId' LIMIT 1");
 
        return $this->getOne ("SELECT `when` FROM `" . $this->_sPrefix . $this->_sTableFans . "` WHERE `id_entry` = '$iEntryId' AND `id_profile` = '$iProfileId' AND `confirmed` = '$isConfirmed' LIMIT 1");
    }

	function getAllSubItems($sSubItem, $iCharityId){
		$aSubItems = array();
		$iCharityId = (int)$iCharityId;

		switch($sSubItem){ 
			case 'event':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableEvent . "` WHERE `charity_id`=$iCharityId");
			break;  
			case 'branches':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableBranches . "` WHERE `charity_id`=$iCharityId");
			break;  
			case 'supporter':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableSupporter . "` WHERE `charity_id`=$iCharityId");
			break;  
			case 'members':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableMembers . "` WHERE `charity_id`=$iCharityId");
			break;  
			case 'programs':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTablePrograms . "` WHERE `charity_id`=$iCharityId");
			break;  
			case 'news':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `charity_id`=$iCharityId");
			break;   
			case 'staff':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableStaff . "` WHERE `charity_id`=$iCharityId");
			break;  
 			case 'faq':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableFaq . "` WHERE `charity_id`=$iCharityId");
			break; 
			case 'review':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableReview . "` WHERE `charity_id`=$iCharityId");
			break; 
		}
 
		return $aSubItems;	
	}
 

 //[BEGIN] DONATION
    function generatePaymentUrl($iEntryId, $fTotalCost, $sCustomStr) {
   
		$aEntry = $this->getEntryById($iEntryId);
		$sPaypalEmail = $aEntry['paypal'];

        return  $this ->_oConfig->getPurchaseBaseUrl() . '?cmd=_xclick'
			    . '&rm=2'
                . '&no_shipping=0'      
				. '&currency_code='.$this->_oConfig->getPurchaseCurrency()
                . '&return=' .  $this->_oConfig->getDonationUrl() . $this->_iProfileId .'/'. $iEntryId
				. '&business='  . $sPaypalEmail
                . '&custom='  . $sCustomStr  
                . '&item_name=' . _t('_modzzz_charity_paypal_donation_desc',$aEntry['title'])
                . '&item_number='.$iEntryId  
                . '&amount=' . $fTotalCost;
    }
 
    function saveDonationRecord($iEntryId, $iDonorId, $iAmount, $sTransId, $sPaypal, $sCustomStr, $sTransType) {
        $iEntryId  = (int)$iEntryId;
		$iDonorId  = (int)$iDonorId;
    
		$aCustom = explode('|', $sCustomStr); 
		$bAnonymous = (int)$aCustom[0];
		
 		$sFirstName = '';
		$sLastName = '';
		if($iDonorId){
			$aProfile = getProfileInfo($iDonorId);
			$sFirstName = $aProfile['FirstName'];
			$sLastName = $aProfile['LastName'];
		}else{
			$sFirstName = $aCustom[1];
			$sLastName = $aCustom[2];
		}


        $bProcessed = $this->query("INSERT INTO `" . $this->_sPrefix . "paypal_trans` SET `charity_id` = '{$iEntryId}', `donor_id` = '{$iDonorId}', `paypal` =  '{$sPaypal}', `trans_id` =  '{$sTransId}', `created`  = UNIX_TIMESTAMP(), `trans_type`  = '{$sTransType}', `amount` = '{$iAmount}', `anonymous` = {$bAnonymous}, `first_name`='$sFirstName', `last_name`='$sLastName'
        "); 
		 
		return $bProcessed;
    }

    function isExistDonationTransaction($iDonorId, $sTransId) {
        $iDonorId  = (int)$iDonorId;
 
        return $this->getOne("SELECT COUNT(`trans_id`) FROM `" . $this->_sPrefix . "paypal_trans` 
            WHERE `donor_id` = {$iDonorId} AND `trans_id` =  '{$sTransId}'  
        "); 
    }
 
	function incrementDonation($iEntryId, $iAmount){
        $iAmount = (double)$iAmount;

		return $this->query("UPDATE `" . $this->_sPrefix . "main` SET `paypal_amount` = `paypal_amount` + {$iAmount} WHERE `id` = '{$iEntryId}'");  
	}

	function onDonationAlert($sItem, $iEntryId, $iHero, $iOwner, $aData, $fAmount=0) {
	 
		$sCustomStr = $aData['custom'];
		$aCustom = explode('|', $sCustomStr); 
		$bAnonymous = (int)$aCustom[0];
 
		if($bAnonymous){  
 			$sHeroName = _t('_modzzz_charity_someone'); 
			$sHeroEmail = $aData['business'];
			$sHeroLink = 'javascript:void(0)';
		}elseif($iHero){
			$aHero = getProfileInfo($iHero);
			$sHeroName = $aHero['NickName']; 
			$sHeroEmail = $aHero['Email'];
			$sHeroLink = getProfileLink($aHero['ID']); 
		}else{
			if($aCustom[1] || $aCustom[2])
				$sHeroName = $aCustom[1] .' '. $aCustom[2];
			else	
				$sHeroName = _t('_modzzz_charity_someone');
			
			$sHeroEmail = $aData['business'];
 			$sHeroLink = 'javascript:void(0)';
		}
 
		$aOwner = getProfileInfo($iOwner);
		$sOwnerName = $aOwner['NickName'];
		$sOwnerEmail = $aOwner['Email'];
		$sOwnerLink = getProfileLink($aOwner['ID']); 
	  
		$aCharity = $this->getEntryById($iEntryId);
 
		$sCharityUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/'.$aCharity['uri'];
		$sCharityTitle = $aCharity['title'];

		$aPlus = array();
		$aPlus['HeroName'] = $sHeroName;
		$aPlus['HeroLink'] = $sHeroLink; 
		$aPlus['OwnerName'] = $sOwnerName;
		$aPlus['OwnerLink'] = $sOwnerLink; 
		$aPlus['CharityName'] = $sCharityTitle; 
		$aPlus['CharityLink'] = $sCharityUrl;
		$aPlus['Amount'] = $fAmount;

		$aPlus['SiteName'] = isset($GLOBALS['site']['title']) ? $GLOBALS['site']['title'] : getParam('site_title');
		$aPlus['SiteUrl'] = isset($GLOBALS['site']['url']) ;

		$oEmailTemplate = new BxDolEmailTemplates(); 

		switch($sItem){
 
			case "thankyou":
	 
				$sTemplate = "modzzz_charity_donation_thanks";
				$aTemplate = $oEmailTemplate->getTemplate($sTemplate, $iHero);
				$sMessage = $aTemplate['Body'];
				$sSubject = $aTemplate['Subject'];   
				$sSubject = str_replace("<SiteName>", $aPlus['SiteName'], $sSubject);
				$sSubject = str_replace("<CharityName>", $aPlus['CharityName'], $sSubject);
		  
				$vMailRes = sendMail( $sHeroEmail, $sSubject, $sMessage, $iHero, $aPlus, 'html' ); 
			break; 	 
			case "notify":
	 
				$sTemplate = "modzzz_charity_donation_notify";
				$aTemplate = $oEmailTemplate->getTemplate($sTemplate, $iOwner);
				$sMessage = $aTemplate['Body'];
				$sSubject = $aTemplate['Subject'];   
				$sSubject = str_replace("<SiteName>", $aPlus['SiteName'], $sSubject);
				$sSubject = str_replace("<CharityName>", $aPlus['CharityName'], $sSubject);
		  
				$vMailRes = sendMail( $sOwnerEmail, $sSubject, $sMessage, $iOwner, $aPlus, 'html' ); 
			break; 				
 
			default:
				//
		}
	 

	}

 //[END] DONATION



}
