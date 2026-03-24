<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Investment
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
 * Investment module Data
 */
class BxInvestmentDb extends BxDolTwigModuleDb {	
	
	var $_oConfig;

	/*
	 * Constructor.
	 */
	function BxInvestmentDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);

		$this->_oConfig = $oConfig;

		$this->_sTableFans = '';  
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
        $this->_sFieldAllowViewTo = 'allow_view_investment_to';
		 $this->_sTableFavorite = 'favorites';
	}
	
	// freddy ajout
	function getFactureInvoiceCount ($iProfileId ) {
                return $this->getOne ("SELECT  COUNT(`f`.`id`) FROM  `modzzz_investment_invoices` AS  `f` RIGHT JOIN  `modzzz_investment_main` AS  `l` ON  `f`.`investment_id` =  `l`.`id`  and `l`.`author_id` = '$iProfileId' and `f`.`invoice_status` = 'pending'");
    } 
	
	// Freddy ajout  Total investment pour chaque membre
	function getModzzzCountInvestment($iAuthorId){ 
		return $this->getOne("SELECT COUNT(`id`) FROM `modzzz_investment_main` WHERE `author_id`= '$iAuthorId'  AND `status`='approved' ");  
	}
	
	// Freddy nombre des investment favorite et candidatures  pour un membre
  /* function CountFavoriteMemberInvestment($iProfileId){
	    $iProfileId = (int)$iProfileId;
		return $this->getOne ("SELECT COUNT(`id_entry`) FROM `" . $this->_sPrefix . $this->_sTableFavorite. "` WHERE `id_profile`=$iProfileId LIMIT 1"); 
	
	} 
	*/
	
	
	function CountFavoriteMemberInvestment($iProfileId){
	    $iProfileId = (int)$iProfileId;
		return $this->getOne ("SELECT COUNT(`id_entry`) FROM `modzzz_investment_favorites` AS  `f` RIGHT JOIN  `modzzz_investment_main` AS  `l` ON  `f`.`id_entry` =  `l`.`id`  and  `id_profile`=$iProfileId and `l`.`status` ='approved'  LIMIT 1"); 
	
	} 
	
	
  
    function getCurrencySign($sKey){ 
		
		$sKey = ($sKey) ? $sKey : $this->_oConfig->_sCurrencySign;
		
		return $sKey;  
    }

	function setItemStatus($iItemId, $sStatus) {
		
 		 $this->query("UPDATE `" . $this->_sPrefix . "main` SET `status`='$sStatus' WHERE `id`='$iItemId'"); 
	}

	function setInvoiceStatus($iItemId, $sStatus) {
		
 		 $this->query("UPDATE `" . $this->_sPrefix . "invoices` SET `invoice_status`='$sStatus' WHERE `investment_id`='$iItemId'"); 
	}
  
 	function getAdmins()
	{ 
 		$aAllAdmins = $this->getAll("SELECT `ID` FROM `Profiles` WHERE `Role` & " . BX_DOL_ROLE_ADMIN . " OR `Role` & " . BX_DOL_ROLE_MODERATOR . " AND `Status`='Active'"); 
		
		return $aAllAdmins; 
	}

 	function isOwnerAdmin($iProfileId)
	{ 
 		$bAdmin = $this->getOne("SELECT `ID` FROM `Profiles` WHERE `ID`='$iProfileId' AND (`Role` & " . BX_DOL_ROLE_ADMIN . " OR `Role` & " . BX_DOL_ROLE_MODERATOR . ") AND `Status`='Active'"); 
		
		return $bAdmin; 
	}
  
    /**begin- package functions **/  
  
	function getPackageIdByInvoiceNo($sInvoiceNo){
		$aInvoice = $this->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];
		
		return $iPackageId;
	}

	function isPaidPackage($iPackageId){
        $iPackageId  = (int)$iPackageId;
 
        return $this->getOne("SELECT `id` FROM `" . $this->_sPrefix . "packages` 
            WHERE `price` > 0 AND `id` =  '{$iPackageId}'  
        ");  
	}
 
	function getAllPackages() {
		
 		$aPackage = $this->getAll("SELECT `id`, `name`, `description`, `price`, `days`, `videos`, `photos`, `photos` as `images`, `sounds`, `files`, `featured`, `status` FROM `" . $this->_sPrefix . "packages`"); 
  
		return $aPackage;
	}

	function getPackageById($iId) {
		
 		$aPackage = $this->getRow("SELECT `id`, `name`, `description`, `price`, `days`, `videos`, `photos`, `photos` as `images`, `sounds`, `files`, `featured`, `status` FROM `" . $this->_sPrefix . "packages` WHERE `id`='$iId' LIMIT 1"); 
  
		return $aPackage;
	}

	function isPackageAllowedVideos($sInvoiceNo) {
		
		if (getParam('modzzz_investment_paid_active')!='on') 
            return true;	

 		$aInvoice = $this->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];

 		$aPackage = $this->getPackageById($iPackageId);	

		return (int)$aPackage['videos'];
	}

	function isPackageAllowedPhotos($sInvoiceNo) {
		
		if (getParam('modzzz_investment_paid_active')!='on') 
            return true;	

 		$aInvoice = $this->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];
 		$aPackage = $this->getPackageById($iPackageId);
 		 
		return (int)$aPackage['photos'];
	}

	function isPackageAllowedSounds($sInvoiceNo) {
		
		if (getParam('modzzz_investment_paid_active')!='on') 
            return true;	

 		$aInvoice = $this->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];

 		$aPackage = $this->getPackageById($iPackageId);

		return (int)$aPackage['sounds'];
	}

	function isPackageAllowedFiles($sInvoiceNo) {
		
		if (getParam('modzzz_investment_paid_active')!='on') 
            return true;	

 		$aInvoice = $this->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];

 		$aPackage = $this->getPackageById($iPackageId);

		return (int)$aPackage['files'];
	}
 
	function getPackageName($iId) {
		
 		$sPackageName = $this->getOne("SELECT `name` FROM `" . $this->_sPrefix . "packages` WHERE `id`='$iId' LIMIT 1"); 
  
		return $sPackageName;
	}
 
	function getPackageList($bPremiumOnly=false) {
		
		$sPremiumFilter = ($bPremiumOnly) ? 'AND `price` > 0' : '';
 		$aPackages = $this->getAll("SELECT `id`, `name` FROM `" . $this->_sPrefix . "packages` WHERE `status`='active' {$sPremiumFilter} ORDER BY `price` DESC"); 
 
		$arr = array();
 		foreach($aPackages as $aEachPackage){
			$iId = $aEachPackage['id'];
			$sName = $aEachPackage['name'];
			$arr[$iId] = $sName;
		}

		return $arr;
	}

	function getInitPackage() {
		
 		return $this->getOne("SELECT `id` FROM `" . $this->_sPrefix . "packages` WHERE `status`='active' ORDER BY `price` DESC");  
	}
 
	function getPackagePrice($iPackageId){
	
		$iPrice = $this->getOne("SELECT `price` FROM `" . $this->_sPrefix . "packages` WHERE `id`='$iPackageId'");
		
		return $iPrice;
	}
 
 	function getPackages(){
	 
 		$aAllEntries = $this->getAll("SELECT `id`, `name` FROM `" . $this->_sPrefix . "packages` WHERE  `status`='active'   ORDER BY `name` ASC"); 
		
		return $aAllEntries; 
	}
	 
	function SavePackage($iParent=0){
	  
		$sName = process_db_input($_POST['package_name']);
		$sDescription = process_db_input($_POST['package_description']);
		$fPrice = floatval(process_db_input($_POST['package_price']));
		$iDays = (int)process_db_input($_POST['package_days']);
		$iVideos = (int)process_db_input($_POST['package_videos']);
		$iPhotos = (int)process_db_input($_POST['package_photos']);
		$iSounds = (int)process_db_input($_POST['package_sounds']);
		$iFiles = (int)process_db_input($_POST['package_files']);
		$iFeatured = (int)process_db_input($_POST['package_featured']);


		if(!trim($sName)){
			return false;
		}
	 
		db_res("INSERT INTO `" . $this->_sPrefix . "packages` SET `name`='$sName', `description`='$sDescription', `price`=$fPrice, `days`=$iDays, `sounds`='$iSounds', `files`='$iFiles', `videos`='$iVideos', `photos`='$iPhotos', `featured`=$iFeatured");
	 
		return true;
	}
	  
	function UpdatePackage(){  
	
		$iId = process_db_input($_POST['id']);
		$sName = process_db_input($_POST['package_name']);
		$sDescription = process_db_input($_POST['package_description']);
		$fPrice = (float)process_db_input($_POST['package_price']);
		$iDays = (int)process_db_input($_POST['package_days']);	 
	 	$iVideos = (int)process_db_input($_POST['package_videos']);
		$iPhotos = (int)process_db_input($_POST['package_photos']);
		$iSounds = (int)process_db_input($_POST['package_sounds']);
		$iFiles = (int)process_db_input($_POST['package_files']);
	 	$iFeatured = (int)process_db_input($_POST['package_featured']);
	
		if(!trim($sName)){
			return false;   
		}
	 
		return $this->query("UPDATE `" . $this->_sPrefix . "packages` SET `name`='$sName', `description`='$sDescription', `price`=$fPrice, `days`=$iDays, `sounds`='$iSounds', `files`='$iFiles', `videos`='$iVideos', `photos`='$iPhotos', `featured`=$iFeatured WHERE `id`=$iId");
	}
	 
	function DeletePackage(){ 
		$iId = process_db_input($_POST['id']);
	 
		return $this->query("DELETE FROM `" . $this->_sPrefix . "packages` WHERE `id`='$iId'"); 
	}
    /** end - package functions **/

  
    function deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin) {
        if ($iRet = parent::deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin)) {
            $this->query ("DELETE FROM `" . $this->_sPrefix . "admins` WHERE `id_entry` = $iId");
            $this->deleteEntryMediaAll ($iId, 'images');
            $this->deleteEntryMediaAll ($iId, 'videos');
            $this->deleteEntryMediaAll ($iId, 'sounds');
            $this->deleteEntryMediaAll ($iId, 'files');
 
            $this->query ("DELETE FROM `" . $this->_sPrefix . "invoices` WHERE `investment_id` = $iId");
            $this->query ("DELETE FROM `" . $this->_sPrefix . "orders` WHERE `buyer_id` = $iOwner");
            $this->query ("DELETE FROM `" . $this->_sPrefix . "featured_orders` WHERE `buyer_id` = $iOwner"); 
			$this->removeFavorite($iOwner, $iId); //remove favorite

        }
        return $iRet;
    }
 
	function getNumberList($iStart, $iEnd, $iIncrement=1)
	{
		$aVals=array();
		$aVals[0] = ''; 
		for($iter=$iStart; $iter<=$iEnd; $iter+=$iIncrement)
		{
			$aVals[$iter] = $iter; 
		}

		return $aVals;
	}

	function getYearList($iNumYears)
	{
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
        $sPrivacyInsert = "`allow_view_location_to` = '" . BX_DESTINATION_DEFAULT_PRIVACY . "',";
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
 
    function saveTransactionRecord($iBuyerId, $iInvestmentId, $sTransNo, $sTransType) {
        $iBuyerId    = (int)$iBuyerId;
        $iInvestmentId  = (int)$iInvestmentId; 
   		$iTime = time();

		$aDataEntry = $this->getEntryById($iInvestmentId);
        $sInvoiceNo  = $aDataEntry['invoice_no']; 
  
        $bProcessed = $this->query("INSERT INTO `" . $this->_sPrefix . "orders` 
							SET `buyer_id` = {$iBuyerId}, 
							`invoice_no` =  '{$sInvoiceNo}', 
							`order_no` =  '{$sTransNo}',  
							`payment_method`  = '{$sTransType}', 
  							`order_date`  = $iTime  
							"); 

		$iOrderId = $this->lastId();
 
		 //if order successful, update expiration date of investment
		 if($iOrderId){
			$aInvoice = $this->getInvoiceByNo($sInvoiceNo, false);
			$iDays = (int)$aInvoice['days'];
			$this->updateEntryExpiration($iInvestmentId, $iDays);
		  
			$iPackageId = (int)$aInvoice['package_id'];
			$aPackage = $this->getPackageById($iPackageId); 
			$iFeatured = (int)$aPackage['featured'];
			if($iFeatured){
				$this->updateFeaturedStatus($iInvestmentId);
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

		 $iInvoiceActiveDays = (int)getParam('modzzz_investment_invoice_valid_days');
 		 if($iInvoiceActiveDays) {
			 $SECONDS_IN_DAY = 86400; 
			 $iCreated = time();
			 $iExpireDate = $iCreated + ($SECONDS_IN_DAY * $iInvoiceActiveDays);
		 }

		 $this->query("INSERT INTO `" . $this->_sPrefix . "invoices` 
							SET `invoice_no` = '{$sInvoiceNo}',  
							`price` = {$fPrice},
							`days` = {$iDays},
							`investment_id` = {$iEntryId},
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

    function getInvoiceByNo($sInvoiceNo, $bCheckStatus=true) {  
        
		if($bCheckStatus)
			$sStatusCheck = " AND `invoice_status`='paid'";
		
		return $this->getRow("SELECT `invoice_no`, `price`, `days`, `investment_id`, `package_id`, `invoice_status`, `invoice_due_date`, `invoice_expiry_date`, `invoice_date` FROM `" . $this->_sPrefix . "invoices` WHERE `invoice_no` = '{$sInvoiceNo}' {$sStatusCheck}"); 
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
 		$aAllEntries = $this->getAll("SELECT `Category` FROM `sys_categories` WHERE `Type` = '{$sType}' AND `Status`='active' AND Owner=0 ORDER BY `Category` ASC"); 
		
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
   function generateFeaturedPaymentUrl($iInvestmentId, $iQuantity, $fCost) {
        $fCost   = number_format($fCost, 2);
 
        return  $this ->_oConfig->getPurchaseBaseUrl() . '?cmd=_xclick'
			    . '&rm=2'
                . '&no_shipping=0'      
				. '&currency_code='.$this->_oConfig->getPurchaseCurrency()
                . '&return=' .  $this->_oConfig->getFeaturedCallbackUrl() . $iInvestmentId
				. '&business='  . getParam('modzzz_investment_paypal_email')
                . '&item_name=' . getParam('modzzz_investment_featured_purchase_desc')
                . '&item_number=' . $iInvestmentId  
                . '&amount=' . $fCost;
    }
 
    function saveFeaturedTransactionRecord($iBuyerId, $iInvestmentId, $iQuantity, $fPrice, $sTransId, $sTransType) {
        $iBuyerId    = (int)$iBuyerId;
        $iInvestmentId  = (int)$iInvestmentId; 
        $iQuantity  = (int)$iQuantity; 
		$iTime = time();

        $bProcessed = $this->query("INSERT INTO `" . $this->_sPrefix . "featured_orders` 
							SET `buyer_id` = {$iBuyerId}, 
							`price` = '{$fPrice}',
							`days` = {$iQuantity},
							`item_id` =  {$iInvestmentId},
 							`trans_id` = '{$sTransId}',  
							`trans_type` = '{$sTransType}', 
  							`created` = $iTime  
        "); 

 		$iOrderId = $this->lastId();

		if($iOrderId){
			$this->alertOnAction('modzzz_investment_featured_admin_notify', $iInvestmentId, $iBuyerId, $iQuantity, true);

			$this->alertOnAction('modzzz_investment_featured_buyer_notify', $iInvestmentId, $iBuyerId, $iQuantity);
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

	function processInvestments(){ 
		$this->processExpiredInvestments();
		
		$this->processExpiredInvoices();

		$this->processFeaturedInvestments();
	}


	function processExpiredInvoices() { 
 
	    $iExpireTime = time();
 
		$aListings = $this->getAll("SELECT `investment_id` FROM `" . $this->_sPrefix . "invoices` WHERE `invoice_status`='pending' AND `invoice_expiry_date`>0 AND `invoice_expiry_date`<={$iExpireTime}");
		 
		foreach ($aListings as $aEachListing) {
			$iEntryId = (int)$aEachListing['investment_id']; 
 
			$this->query("DELETE FROM `" . $this->_sPrefix . "main` WHERE `id`=$iEntryId");  
			
			$this->query("DELETE FROM `" . $this->_sPrefix . "invoices` WHERE `investment_id`=$iEntryId");  
 		}  
 
	}
 

	function processExpiredInvestments() { 
 
 		$iTime = time();
 		$SECONDS_IN_DAY = 86400; 
	 
        $bFreeListing = (getParam('modzzz_investment_paid_active')=='on') ? false : true;  
        $iNumActiveDays = (int)getParam("modzzz_investment_free_expired");
        if($bFreeListing && (!$iNumActiveDays))
            return; 
 

		if(getParam('modzzz_investment_activate_expired') == 'on') {
  			 
			$aInvestments = $this->getAll("SELECT `id`, `expiry_date`, `author_id` FROM `" . $this->_sPrefix . "main` WHERE `status`='approved' AND `expiry_date`>0 AND `expiry_date`<={$iTime}");
			 
			foreach ($aInvestments as $aEachInvestment) {
				$iEntryId = (int)$aEachInvestment['id']; 
				$iRecipientId = (int)$aEachInvestment['author_id']; 

				$this->query("UPDATE `" . $this->_sPrefix . "main` SET `status`='expired'  WHERE `id`=$iEntryId");  
 
		 		$this->alertOnAction('modzzz_investment_expired', $iEntryId, $iRecipientId);
			}  
		}else{ 
  			$this->query("UPDATE `" . $this->_sPrefix . "main` SET `status`='expired' WHERE  `status`='approved' AND `expiry_date`>0 AND `expiry_date`<=$iTime");  
		}
  
		$iNumNotifyDays = (int)getParam("modzzz_investment_email_expiring");
		if($iNumNotifyDays){
			$iNotifyTime = $SECONDS_IN_DAY * $iNumNotifyDays;
			$iTriggerTime = $iTime - $iNotifyTime;

			$aInvestments = $this->getAll("SELECT `id`, `expiry_date`, `author_id` FROM `" . $this->_sPrefix . "main` WHERE `status`='approved' AND `expiry_date` >= {$iTriggerTime} AND `expiry_date` < {$iTime}  AND `pre_expire_notify`=0");
			 
			foreach ($aInvestments as $aEachInvestment) {
				$iEntryId = $aEachInvestment['id'];
				$iRecipientId = $aEachInvestment['author_id'];
		 
				$this->query("UPDATE `" . $this->_sPrefix . "main` SET `pre_expire_notify`=1 WHERE `id` = $iEntryId");

				if(getParam("modzzz_investment_activate_expiring")=='on') { 
					$this->alertOnAction('modzzz_investment_expiring', $iEntryId, $iRecipientId, $iNumNotifyDays);
				}
			}
		}
 			
		$iNumNotifyDays = (int)getParam("modzzz_investment_email_expired");
		if($iNumNotifyDays){
			$iNotifyTime = $SECONDS_IN_DAY * $iNumNotifyDays;
			$iTriggerTime = $iTime - $iNotifyTime;

			$aInvestments = $this->getAll("SELECT `id`, `expiry_date`, `author_id` FROM `" . $this->_sPrefix . "main` WHERE `status`='expired' AND `expiry_date` <= {$iNotifyTime} AND `post_expire_notify`=0");
			 
			foreach ($aInvestments as $aEachInvestment) {
				$iEntryId = $aEachInvestment['id']; 
				$iRecipientId = $aEachInvestment['author_id'];
			 
				$this->query("UPDATE `" . $this->_sPrefix . "main` SET `post_expire_notify`=1 WHERE `id` = $iEntryId");
				
				if(getParam("modzzz_investment_activate_expired")=='on') { 
					$this->alertOnAction('modzzz_investment_post_expired', $iEntryId, $iRecipientId, $iNumNotifyDays);
				}
			}
		}
 
	}
 

	function processFeaturedInvestments(){
		
		if(getParam('modzzz_investment_buy_featured') != 'on')
			return;

		$iTime = time();
   
        $aInvestments = $this->getAll("SELECT `id`, `author_id`, `featured`, `featured_expiry_date` FROM `" . $this->_sPrefix . "main` WHERE `featured`=1 AND `featured_expiry_date` <= $iTime"); 

		foreach($aInvestments as $aEachList){

		    $iExistExpireDate = (int)$aEachList['featured_expiry_date']; 
		    if($iExistExpireDate==0)
				continue;

			$iInvestmentId = (int)$aEachList['id'];
			$iRecipientId = (int)$aEachList['author_id'];

			$this->alertOnAction('modzzz_investment_featured_expire_notify', $iInvestmentId, $iRecipientId  );
		
	        $this->query("UPDATE `" . $this->_sPrefix . "main` SET `featured`=0, `featured_expiry_date`=0, `featured_date`=0  WHERE `id`=$iInvestmentId"); 
		}

	}

	function alertOnAction($sTemplate, $iInvestmentId, $iRecipientId=0, $iDays=0, $bAdmin=false) {
	   
		$aPlus = array();

		if($iInvestmentId){
			$aDataEntry = $this->getEntryById($iInvestmentId);
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

	function getCategoryCustomData($iCategoryId){

        $aInvestments = $this->getRow("SELECT `custom_field1`, `custom_field2`, `custom_field3`, `custom_field4`, `custom_field5`, `custom_field6`, `custom_field7`, `custom_field8`, `custom_field9`, `custom_field10`, `custom_sub_field1`, `custom_sub_field2`, `custom_sub_field3`, `custom_sub_field4`, `custom_sub_field5`, `custom_sub_field6`, `custom_sub_field7`, `custom_sub_field8`, `custom_sub_field9`, `custom_sub_field10` FROM `" . $this->_sPrefix . "categ` WHERE `id` = $iCategoryId");  

		return $aInvestments;
	}

	function deleteSaleOffer($iId) {
		$iId = (int)$iId;
		$this->query("DELETE FROM `" . $this->_sPrefix . "offers` WHERE `id` = $iId"); 
	} 
 
    function markSaleCompleted($iId) {

 		$iEntryId = (int)$this->getOne("SELECT `investment_id` FROM `" . $this->_sPrefix . "offers` WHERE `id` = $iId LIMIT 1");  
		
		$aDataEntry = $this->getEntryById($iEntryId); 
		$iQuantity = (int)$aDataEntry['quantity'];

		$this->query("UPDATE `" . $this->_sPrefix . "offers` SET `offer_status`='accepted' WHERE `id` = $iId");  

		if($iQuantity>1){
			$this->query("UPDATE `" . $this->_sPrefix . "main` SET `quantity`=`quantity`-1 WHERE `id` = $iEntryId");   
		}else{
			$this->query("UPDATE `" . $this->_sPrefix . "main` SET `quantity`=0, `status`='sold' WHERE `id` = $iEntryId");   
		}
	}


	function getLatestComments($iLimit=5){

		return $this->getAll("SELECT `cmt_object_id`,`cmt_author_id`,`cmt_text`, UNIX_TIMESTAMP(`cmt_time`) AS `date` FROM `" . $this->_sPrefix . "cmts` WHERE `cmt_text` NOT LIKE '<object%' ORDER BY `cmt_time` DESC LIMIT $iLimit");

	} 

	function getLatestForumPosts($iLimit=5, $iEntryId=0){

		if($iEntryId)
			$sQueryId = "t.`forum_id`=$iEntryId AND ";

		return $this->getAll("SELECT e.`title`,  e.`thumb`, f.`forum_uri`, p.`user`, p.`post_text`, t.`topic_uri`, t.`topic_title`,p.`when` FROM `" . $this->_sPrefix . "forum` f, `" . $this->_sPrefix . "forum_topic` t, `" . $this->_sPrefix . "forum_post` p, `" . $this->_sPrefix . "main` e WHERE {$sQueryId}  p.`topic_id`=t.`topic_id` AND t.`forum_id`=f.`forum_id` AND e.`ID`=f.`entry_id` ORDER BY  p.`when` LIMIT $iLimit"); 

	} 
 
 
 
 
 //[begin] favorites
	function isFavorite($iProfileId, $iEntryId){
	    $iProfileId = (int)$iProfileId;
	    $iEntryId = (int)$iEntryId;      
		
		$bExists = (int)$this->getOne("SELECT `id_entry` FROM `" . $this->_sPrefix . $this->_sTableFavorite. "` WHERE `id_profile`=$iProfileId AND `id_entry`=$iEntryId LIMIT 1"); 
	
		return $bExists;
	} 

	function markAsFavorite($iProfileId, $iEntryId){

		if($this->isFavorite($iProfileId, $iEntryId)){
			return $this->removeFavorite($iProfileId, $iEntryId);
		}else{
			return $this->addFavorite($iProfileId, $iEntryId);
		}
	}
 
	function addFavorite($iProfileId, $iEntryId){
	    $iProfileId = (int)$iProfileId;
	    $iEntryId = (int)$iEntryId;      
		$iTime = time();

		return $this->query("INSERT INTO `" . $this->_sPrefix . $this->_sTableFavorite. "` SET `id_profile`=$iProfileId, `id_entry`=$iEntryId, `when`=$iTime");  
	} 

	function removeFavorite($iProfileId, $iEntryId){
	    $iProfileId = (int)$iProfileId;
	    $iEntryId = (int)$iEntryId;      
 
		return $this->query("DELETE FROM `" . $this->_sPrefix . $this->_sTableFavorite. "` WHERE `id_profile`=$iProfileId AND `id_entry`=$iEntryId");  
	} 
	//[end] favorites
 
 
 
 
	function getPromoData(){
        return $this->getRow("SELECT `id`, `details` FROM `" . $this->_sPrefix . "promo` LIMIT 1"); 
	}

	function getPromoText(){
        return $this->getOne("SELECT `details` FROM `" . $this->_sPrefix . "promo` LIMIT 1"); 
	}
 
	function deleteInquiry($iInquiryId){
 
 		$bSuccess = $this->query("DELETE FROM `" . $this->_sPrefix . "inquiry` WHERE   `id`='{$iInquiryId}'");
  
		return $bSuccess;
	}
 
	function postInquiry($iEntryId, $iProfileId, $sDesc) { 

		 $iTime = time();
		 $sDesc = process_db_input($sDesc);
 
		 return $this->query("INSERT INTO `" . $this->_sPrefix . "inquiry` SET `id_entry`='$iEntryId', `id_profile`='$iProfileId', `desc`='$sDesc',  `created`=$iTime"); 
	} 
 

}
