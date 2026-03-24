<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Article
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
 * Articles module Data
 */
class BxArticlesDb extends BxDolTwigModuleDb {	

	/*
	 * Constructor.
	 */
	function BxArticlesDb(&$oConfig) {
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
        $this->_sFieldAnonymous = 'anonymous'; 
        $this->_sFieldTags = 'tags';
        $this->_sFieldThumb = 'thumb';
        $this->_sFieldStatus = 'status';
        $this->_sFieldFeatured = 'featured';
        $this->_sFieldCreated = 'created';
        $this->_sFieldPublished = 'when';
        $this->_sTableAdmins = 'admins';
        $this->_sFieldAllowViewTo = 'allow_view_article_to';
        $this->_sTableFavorite = 'favorites';
        $this->_sFieldLetter = 'letter'; //added patch 2.1.2

	}
  
    function deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin) {
        if ($iRet = parent::deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin)) {
            $this->query ("DELETE FROM `" . $this->_sPrefix . "admins` WHERE `id_entry` = $iId");
            $this->deleteEntryMediaAll ($iId, 'images');
            $this->deleteEntryMediaAll ($iId, 'videos');
            $this->deleteEntryMediaAll ($iId, 'sounds');
            $this->deleteEntryMediaAll ($iId, 'files');
        }

		$this->removeYoutubeEntries($iId); 

		$this->removeFavorite($iOwner, $iId); //remove favorite

        return $iRet;
    }

	//override forum delete since there is no forum
	function deleteForum ($iEntryId){
		//
	}
 
	function getMonth($iMonth){
		$aMonths = array(
					0=>_t('_January'),
					1=>_t('_February'),
					2=>_t('_March'),
					3=>_t('_April'),
					4=>_t('_May'),
					5=>_t('_June'),
					6=>_t('_July'),
					7=>_t('_August'),
					8=>_t('_September'),
					9=>_t('_October'),
					10=>_t('_November'),
					11=>_t('_December') 
				);

		return $aMonths[$iMonth];
	}

	function publish(&$aIds) {
		$aIds = $this->getColumn("SELECT 
				`id`
			FROM `" . $this->_sPrefix . $this->_sTableMain . "`
			WHERE `status`='draft' AND `when`<=UNIX_TIMESTAMP()");
		if(empty($aIds))
			return false;

		return (int)$this->query("UPDATE `" . $this->_sPrefix . $this->_sTableMain . "`
			SET `status`='approved' 
			WHERE `id` IN ('" . implode("','", $aIds) . "')") > 0;
	}
 
    function publishEntry ($iId) {
        return $this->query ("UPDATE `" . $this->_sPrefix . $this->_sTableMain . "` SET `{$this->_sFieldStatus}` = 'approved', `publish`=1 WHERE `{$this->_sFieldId}` = $iId LIMIT 1");
    } 

	function getLatestComments($iLimit=5){

		return $this->getAll("SELECT `cmt_object_id`,`cmt_author_id`,`cmt_text`, UNIX_TIMESTAMP(`cmt_time`) AS `date` FROM `" . $this->_sPrefix . "cmts` WHERE `cmt_text` NOT LIKE '<object%' ORDER BY `cmt_time` DESC LIMIT $iLimit");

	} 

	//calendar override to get entries based on published date intead of date created
    function getEntriesByMonth ($iYear, $iMonth, $iNextYear, $iNextMonth) {
        return $this->getAll ("SELECT *, DAYOFMONTH(FROM_UNIXTIME(`{$this->_sFieldPublished}`)) AS `Day`
            FROM `" . $this->_sPrefix . $this->_sTableMain . "`
            WHERE `{$this->_sFieldPublished}` >= UNIX_TIMESTAMP('$iYear-$iMonth-1') AND `{$this->_sFieldPublished}` < UNIX_TIMESTAMP('$iNextYear-$iNextMonth-1') AND `{$this->_sFieldStatus}` = 'approved'");
    }


	function countMonthPosts($iYear, $iMonth=-1){
			
		$sSqlExtra = ($iMonth>=0) ? "AND MONTH(FROM_UNIXTIME(`when`))='".($iMonth+1)."'" : '';

		return (int)db_value("SELECT COUNT(`id`) FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE  YEAR(FROM_UNIXTIME(`when`))='{$iYear}' {$sSqlExtra} AND `status`='approved'"); 
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
	
	function getSubscribeCount($iEntryId){ 
	    $iEntryId = (int)$iEntryId;      
		
		return (int)$this->getOne("SELECT COUNT(DISTINCT `subscriber_id`) FROM `sys_sbs_entries` WHERE `object_id`='$iEntryId' AND `subscription_id` IN (SELECT `id` FROM `sys_sbs_types` WHERE `unit`='modzzz_articles')"); 
	}


   /** category functions*/
	function getCategoryName($iId){
		return $this->getOne("SELECT `name` FROM `" . $this->_sPrefix . "categ` WHERE `id` = '$iId' LIMIT 1"); 
	}

	function getParentCategories(){
		return $this->getAll("SELECT `id`, `parent`, `name`, `uri` FROM `" . $this->_sPrefix . "categ` WHERE `parent` = 0  ORDER BY `name` ASC"); 
	}

 	function getSubCategories($iId){
		return $this->getAll("SELECT `id`, `parent`, `name`, `uri`  FROM `" . $this->_sPrefix . "categ` WHERE `parent` = '$iId' ORDER BY `name` ASC"); 
	}

	function getCategoryInfo ($iId=0, $bGetAll=false){
		if($bGetAll){
			return $this->getAll("SELECT `id`, `name`, `uri` FROM `" . $this->_sPrefix . "categ` WHERE `active` = 1 AND `parent`=0 ORDER BY `name` ASC");
		}else{
			return $this->getRow("SELECT `id`, `name`, `uri` FROM `" . $this->_sPrefix . "categ` WHERE `id` = '$iId'");
		}
	}
 
	function getParentCategoryById($iId){
		return $this->getOne("SELECT `parent` FROM `" . $this->_sPrefix . "categ` WHERE `id` = '$iId'"); 
	}

	function getParentCategoryByUri($sUri){
		return $this->getOne("SELECT `parent` FROM `" . $this->_sPrefix . "categ` WHERE `uri` = '$sUri'"); 
	}

	function getCategoryUriById($iId){
		return $this->getOne("SELECT `uri` FROM `" . $this->_sPrefix . "categ` WHERE `id` = '$iId'"); 
	}

	function getCategoryIdByUri($sUri){
		return $this->getOne("SELECT `id` FROM `" . $this->_sPrefix . "categ` WHERE `uri` = '$sUri'"); 
	}

	function getCategoryById($iId){
		return $this->getRow("SELECT `id`,`name`,`uri` FROM `" . $this->_sPrefix . "categ` WHERE `id` = '$iId'"); 
	}

	function getCategoryByUri($sUri){
		return $this->getRow("SELECT `id`,`name`,`uri` FROM `" . $this->_sPrefix . "categ` WHERE `uri` = '$sUri'"); 
	}
 
	function getParentCategoryInfo ($iId){
		$iParentId = (int)$this->getOne("SELECT `parent` FROM `" . $this->_sPrefix . "categ` WHERE `id` = '$iId' ORDER BY `name` ASC");
 
		return $this->getRow("SELECT `id`,`name`,`uri` FROM `" . $this->_sPrefix . "categ` WHERE `id` = '$iParentId'"); 
	}

	function getSubCategoryInfo ($iParentId=0, $iId=0, $bGetAll=false){
		if($bGetAll){ 
			return $this->getAll("SELECT `id`, `name`, `uri` FROM `" . $this->_sPrefix . "categ` WHERE `active` = 1 AND `parent`='$iParentId' ORDER BY `name` ASC");
		}else{
			return $this->getRow("SELECT `id`, `name`, `uri` FROM `" . $this->_sPrefix . "categ` WHERE `id` = '$iId'");
		}
	}
 
	function getAjaxCategoryOptions($iParentId) {
		$aCats = $this->getSubCategoryInfo ($iParentId, 0, true);

		$sOptions = "<option value=''></option>";
		foreach($aCats as $aEachCat){
			$iId = $aEachCat['id'];
			$sName = $aEachCat['name'];
			$sOptions .= "<option value='{$iId}'>{$sName}</option>";
		}

		return $sOptions;
	}
 
	function getFormCategoryArray($iCategory=0) {
		
		if($iCategory){
			$aCats = $this->getSubCategoryInfo ($iCategory, 0, true);
		}else{
			$aCats = $this->getCategoryInfo (0, true);
		}
		
		$aFormatCats = array();
		$aFormatCats[0] = '';
		foreach($aCats as $aEachCat){
			$iId = $aEachCat['id'];
			$sName = $aEachCat['name'];
			$aFormatCats[$iId] = $sName;
		}

		return $aFormatCats;
	}

	function getCategoryInfoByUri ($sUri){ 

		$sUri = process_db_input($sUri);
 
		return $this->getRow("SELECT `id`, `parent`, `name`, `uri` FROM `" . $this->_sPrefix . "categ` WHERE `active` = 1 AND `uri`='$sUri'"); 
	}
 
	function getCategoryUrl ($sUri){
		return BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'categories/'.$sUri;
	}

 	function getSubCategoryUrl ($sUri){
		return BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'subcategories/'.$sUri;
	}

	function getLocalCategoryCount($iParent, $sCountry='', $sState=''){
		
		$sExtraCheck = '';
 
		if($sCountry)
			$sExtraCheck .= " AND listing.`country`='$sCountry'";

		if($sState)
			$sExtraCheck .= " AND listing.`state`='$sState'";

		return  $this->getOne("SELECT count(listing.`id`) FROM `" . $this->_sPrefix . "categ` cat LEFT JOIN `" . $this->_sPrefix . $this->_sTableMain . "` listing ON cat.id=listing.`category_id` WHERE cat.`active` = 1 AND cat.`parent`=$iParent AND listing.`status`='approved' {$sExtraCheck}"); 
	}
  
	function getParentCategoryCount($iCategoryId){
		$iCategoryId = (int)$iCategoryId;
		
		return $this->getOne("SELECT count(`id`) FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `parent_category_id`=$iCategoryId AND `status`='approved'"); 
	}
 
	function getCategoryCount($iCategoryId){ 
		$iCategoryId = (int)$iCategoryId;
		
		return $this->getOne("SELECT count(`id`) FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `category_id`=$iCategoryId AND `status`='approved'"); 
	}
 	
	function SaveCategory($iParent=0){
	  
		$sCategory = process_db_input($_POST['catname']);
		$sUri = process_pass_data(strtolower($_POST['catname']),1);
 
		if(!trim($sCategory)){
			return false;
		}
	 
		if($iParent){
			$sExtraAdd = "`parent`='$iParent',";
		}

		$sCatURI = uriGenerate ($sUri,  $this->_sPrefix . "categ", 'uri');
 
		db_res("INSERT INTO `" . $this->_sPrefix . "categ` SET $sExtraAdd `uri`='$sCatURI', `name`='$sCategory'");
	 
		return true;
	}
	  
	function UpdateCategory()
	{  
		$iId = process_db_input($_POST['id']);
		$sCategory = process_db_input($_POST['catname']);
	 
		if(!trim($sCategory)){
			return false;   
		}
	  
		return $this->query("UPDATE `" . $this->_sPrefix . "categ` SET `name`='$sCategory'  WHERE `id`=$iId");
	}
	 
	function DeleteCategory()
	{  
		$iId = process_db_input($_POST['id']);
	 
		return $this->query("DELETE FROM `" . $this->_sPrefix . "categ` WHERE `id`='$iId'"); 
	} 

    function isAnyPublicContent() {
    
		if($iLoggedId = getLoggedId())
			return $this->getOne ("SELECT `{$this->_sFieldId}` FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `{$this->_sFieldStatus}` = 'approved' AND `{$this->_sFieldAllowViewTo}` = '" . BX_DOL_PG_ALL . "' OR `{$this->_sFieldAllowViewTo}` = '" . BX_DOL_PG_MEMBERS . "' LIMIT 1"); 
		else
			return $this->getOne ("SELECT `{$this->_sFieldId}` FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `{$this->_sFieldStatus}` = 'approved' AND `{$this->_sFieldAllowViewTo}` = '" . BX_DOL_PG_ALL . "' LIMIT 1");
    }
 
	function getStateName($sCountry, $sState=''){
		$sState = $this->getOne("SELECT `State` FROM `States` WHERE `CountryCode`='{$sCountry}' AND `StateCode`='{$sState}' LIMIT 1");
 
		return $sState;
	}

	function getStateCount($sState){
  
		return  $this->getOne("SELECT COUNT(`id`) FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE  `state` = '$sState'  AND `status`='approved'"); 
	}

	function getStateOptions($sCountry='') {
		$aStates = $this->getStateArray ($sCountry);

		$sOptions = "<option value=''>--"._t('_Select')."--</option>";
		foreach($aStates as $aEachCode=>$aEachState){ 
			$sOptions .= "<option value='{$aEachCode}'>{$aEachState}</option>";
		}

		return $sOptions;
	}

 	function getStateArray($sCountry=''){
 
		$aStates = array();
		$aDbStates = $this->getAll("SELECT * FROM `States` WHERE `CountryCode`='{$sCountry}'  ORDER BY `State`");
		 
		foreach ($aDbStates as $aEachState){
			$sState = $aEachState['State'];
			$sStateCode = $aEachState['StateCode'];
			
			$aStates[$sStateCode] = $sState;
  		} 
		return $aStates;
	}
 
	//BEGIN Youtube 
	function getYoutubeVideos($iEntryId, $iLimit=0){
		$iEntryId = (int)$iEntryId;
		
		if($iLimit)
			$sQuery = "LIMIT 0, {$iLimit}";
  
		return $this->getAll("SELECT `id`, `id_entry`, `title`, `url` FROM `" . $this->_sPrefix . "youtube` WHERE `id_entry`={$iEntryId} {$sQuery}"); 
	}

 	function addYoutube($iEntryId){
		if(is_array($_POST['video_link'])){ 
			foreach($_POST['video_link'] as $iKey=>$sValue){
			
				$sVideoLink = process_db_input($sValue);
 				$sVideoTitle = process_db_input($_POST['video_title'][$iKey]); 
				 
				if(trim($sVideoLink)){  
					$this->query("INSERT INTO `" . $this->_sPrefix . "youtube` SET `id_entry`=$iEntryId, `url`='$sVideoLink', `title`='$sVideoTitle'");
				}
			} 
		}
	}

	function removeYoutubeEntries($iEntryId){ 
	   
		$this->query("DELETE FROM `" . $this->_sPrefix . "youtube` WHERE `id_entry`='$iEntryId'");   
	}
  
	function removeYoutubeEntry($iYoutubeId){ 
	   
		$this->query("DELETE FROM `" . $this->_sPrefix . "youtube` WHERE `id`='$iYoutubeId'");   
	}

    function getYoutubeIds ($iEntryId ) {
	 
		return $this->getPairs ("SELECT `id` FROM `" . $this->_sPrefix . "youtube`  WHERE `id_entry` = '$iEntryId'", 'id', 'id'); 
    }
	//END Youtube

	function sqlStates(){
 
		$this->query("
		 CREATE TABLE IF NOT EXISTS `States` (
			`Country` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`CountryCode` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`State` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`StateCode` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
		 ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci 
		");

		$bHasData = (int)$this->getOne("SELECT COUNT(`State`) FROM `States`"); 
		if($bHasData){
 
			if($bExists = $this->getOne("SELECT `StateCode` FROM `States` WHERE `CountryCode`='GB' AND `StateCode` IN ('ENG','NIR','SCO','WAL')  LIMIT 1")){
		 
				$this->query("DELETE FROM `States` WHERE `CountryCode`='GB' AND `StateCode` IN ('ENG','NIR','SCO','WAL')");
				 
				$this->query("
					INSERT INTO `States` (`Country`, `CountryCode`, `State`, `StateCode`) VALUES
					('United Kingdom', 'GB', 'Avon', 'AVN'),
					('United Kingdom', 'GB', 'Bedfordshire', 'BFE'),
					('United Kingdom', 'GB', 'Berkshire', 'BKE'),
					('United Kingdom', 'GB', 'Borders', 'BDE'),
					('United Kingdom', 'GB', 'Buckinghamshire', 'BHE'),
					('United Kingdom', 'GB', 'Cambridgeshire', 'CBE'),
					('United Kingdom', 'GB', 'Central', 'CTL'),
					('United Kingdom', 'GB', 'Cheshire', 'CHE'),
					('United Kingdom', 'GB', 'Cleveland', 'CVD'),
					('United Kingdom', 'GB', 'Clwyd', 'CLD'),
					('United Kingdom', 'GB', 'Cornwall', 'CWL'),
					('United Kingdom', 'GB', 'CountyAntrim', 'CAM'),
					('United Kingdom', 'GB', 'CountyArmagh', 'CAH'),
					('United Kingdom', 'GB', 'CountyDown', 'CDN'),
					('United Kingdom', 'GB', 'CountyFermanagh', 'CFH'),
					('United Kingdom', 'GB', 'CountyLondonderry', 'CLY'),
					('United Kingdom', 'GB', 'CountyTyrone', 'CTE'),
					('United Kingdom', 'GB', 'Cumbria', 'CUA'),
					('United Kingdom', 'GB', 'Derbyshire', 'DYE'),
					('United Kingdom', 'GB', 'Devon', 'DVN'),
					('United Kingdom', 'GB', 'Dorset', 'DST'),
					('United Kingdom', 'GB', 'DumfriesandGalloway', 'DGY'),
					('United Kingdom', 'GB', 'Durham', 'DRM'),
					('United Kingdom', 'GB', 'Dyfed', 'DYD'),
					('United Kingdom', 'GB', 'EastSussex', 'EAX'),
					('United Kingdom', 'GB', 'Essex', 'ESX'),
					('United Kingdom', 'GB', 'Fife', 'FFE'),
					('United Kingdom', 'GB', 'Gloucestershire', 'GTE'),
					('United Kingdom', 'GB', 'Grampian', 'GRN'),
					('United Kingdom', 'GB', 'GreaterManchester', 'GMR'),
					('United Kingdom', 'GB', 'Gwent', 'GWT'),
					('United Kingdom', 'GB', 'GwyneddCounty', 'GCY'),
					('United Kingdom', 'GB', 'Hampshire', 'HHE'),
					('United Kingdom', 'GB', 'Herefordshire', 'HEE'),
					('United Kingdom', 'GB', 'Hertfordshire', 'HTE'),
					('United Kingdom', 'GB', 'HighlandsandIslands', 'HSS'),
					('United Kingdom', 'GB', 'Humberside', 'HBE'),
					('United Kingdom', 'GB', 'IsleofWight', 'IWT'),
					('United Kingdom', 'GB', 'Kent', 'KET'),
					('United Kingdom', 'GB', 'Lancashire', 'LCE'),
					('United Kingdom', 'GB', 'Leicestershire', 'LEE'),
					('United Kingdom', 'GB', 'Lincolnshire', 'LIE'),
					('United Kingdom', 'GB', 'Lothian', 'LTN'),
					('United Kingdom', 'GB', 'Merseyside', 'MYE'),
					('United Kingdom', 'GB', 'MidGlamorgan', 'MGN'),
					('United Kingdom', 'GB', 'Norfolk', 'NFK'),
					('United Kingdom', 'GB', 'NorthYorkshire', 'NYE'),
					('United Kingdom', 'GB', 'Northamptonshire', 'NSE'),
					('United Kingdom', 'GB', 'Northumberland', 'NLD'),
					('United Kingdom', 'GB', 'Nottinghamshire', 'NHE'),
					('United Kingdom', 'GB', 'Oxfordshire', 'OSE'),
					('United Kingdom', 'GB', 'Powys', 'POS'),
					('United Kingdom', 'GB', 'Rutland', 'RTD'),
					('United Kingdom', 'GB', 'Shropshire', 'SSE'),
					('United Kingdom', 'GB', 'Somerset', 'SRT'),
					('United Kingdom', 'GB', 'SouthGlamorgan', 'SGN'),
					('United Kingdom', 'GB', 'SouthYorkshire', 'SYE'),
					('United Kingdom', 'GB', 'Staffordshire', 'SFE'),
					('United Kingdom', 'GB', 'Strathclyde', 'STE'),
					('United Kingdom', 'GB', 'Suffolk', 'SUK'),
					('United Kingdom', 'GB', 'Surrey', 'SRY'),
					('United Kingdom', 'GB', 'Tayside', 'TSE'),
					('United Kingdom', 'GB', 'TyneandWear', 'TWR'),
					('United Kingdom', 'GB', 'Warwickshire', 'WSE'),
					('United Kingdom', 'GB', 'WestGlamorgan', 'WGN'),
					('United Kingdom', 'GB', 'WestMidlands', 'WMS'),
					('United Kingdom', 'GB', 'WestSussex', 'WSX'),
					('United Kingdom', 'GB', 'WestYorkshire', 'WYE'),
					('United Kingdom', 'GB', 'Wiltshire', 'WHE'),
					('United Kingdom', 'GB', 'Worcestershire', 'WOE')
				");
			}

			if($bExists = $this->getOne("SELECT `StateCode` FROM `States` WHERE `CountryCode`='CH' AND `StateCode` = 'SWZ' LIMIT 1")){
		 
				$this->query("DELETE FROM `States` WHERE `StateCode` = 'SWZ'");

				$this->query("
					INSERT INTO `States` (`Country`, `CountryCode`, `State`, `StateCode`) VALUES
					('Switzerland', 'CH', 'Aargau', '1AG'), 
					('Switzerland', 'CH', 'Appenzell Ausserrhoden', '1AR'), 
					('Switzerland', 'CH', 'Appenzell Innerrhoden', '1AI'), 
					('Switzerland', 'CH', 'Basel-Landschaft', '1BL'), 
					('Switzerland', 'CH', 'Basel-Stadt', '1BS'),  
					('Switzerland', 'CH', 'Bern', '1BE'), 
					('Switzerland', 'CH', 'Fribourg', '1FR'), 
					('Switzerland', 'CH', 'Geneva', '1GE'), 
					('Switzerland', 'CH', 'Glarus', '1GL'), 
					('Switzerland', 'CH', 'Graubünden', '1GR'), 
					('Switzerland', 'CH', 'Jura', '1JU'), 
					('Switzerland', 'CH', 'Lucerne', '1LU'), 
					('Switzerland', 'CH', 'Neuchâtel', '1NE'), 
					('Switzerland', 'CH', 'Nidwalden', '1NW'), 
					('Switzerland', 'CH', 'Obwalden', '1OW'), 
					('Switzerland', 'CH', 'Schaffhausen', '1SH'), 
					('Switzerland', 'CH', 'Schwyz', '1SZ'), 
					('Switzerland', 'CH', 'Solothurn', '1SO'),  
					('Switzerland', 'CH', 'St. Gallen', '1SG'), 
					('Switzerland', 'CH', 'Thurgau', '1TG'), 
					('Switzerland', 'CH', 'Ticino', '1TI'), 
					('Switzerland', 'CH', 'Uri', '1UR'), 
					('Switzerland', 'CH', 'Valais', '1VS'), 
					('Switzerland', 'CH', 'Vaud', '1VD'),  
					('Switzerland', 'CH', 'Zug', '1ZG'), 
					('Switzerland', 'CH', 'Zurich', '1ZH') 
				"); 
			}
		 
			return;
		}

		$this->query("
		INSERT INTO `States` (`Country`, `CountryCode`, `State`, `StateCode`) VALUES
		('Afghanistan', 'AF', 'Badakhshan', 'BAD'),
		('Afghanistan', 'AF', 'Badghis', 'BAH'),
		('Afghanistan', 'AF', 'Baghlan', 'BAG'),
		('Afghanistan', 'AF', 'Balkh', 'BAL'),
		('Afghanistan', 'AF', 'Bamian', 'BAM'),
		('Afghanistan', 'AF', 'Farah', 'FAR'),
		('Afghanistan', 'AF', 'Faryab', 'FAY'),
		('Afghanistan', 'AF', 'Ghazni', 'GHA'),
		('Afghanistan', 'AF', 'Ghowr', 'GHO'),
		('Afghanistan', 'AF', 'Helmand', 'HEL'),
		('Afghanistan', 'AF', 'Herat', 'HER'),
		('Afghanistan', 'AF', 'Jowzjan', 'JOW'),
		('Afghanistan', 'AF', 'Kabol', 'KAB'),
		('Afghanistan', 'AF', 'Kandahar', 'KAN'),
		('Afghanistan', 'AF', 'Kapisa', 'KAP'),
		('Afghanistan', 'AF', 'Khowst', 'KHW'),
		('Afghanistan', 'AF', 'Konar', 'KNR'),
		('Afghanistan', 'AF', 'Kondoz', 'KON'),
		('Afghanistan', 'AF', 'Laghman', 'LAG'),
		('Afghanistan', 'AF', 'Lowgar', 'LOW'),
		('Afghanistan', 'AF', 'Nangarhar', 'NAN'),
		('Afghanistan', 'AF', 'Nimruz', 'NIM'),
		('Afghanistan', 'AF', 'Nurestan', 'NUR'),
		('Afghanistan', 'AF', 'Oruzgan', 'ORU'),
		('Afghanistan', 'AF', 'Paktia', 'PKT'),
		('Afghanistan', 'AF', 'Paktika', 'PAK'),
		('Afghanistan', 'AF', 'Parvan', 'PAR'),
		('Afghanistan', 'AF', 'Samangan', 'SAM'),
		('Afghanistan', 'AF', 'Sar-e Pol', 'SAR'),
		('Afghanistan', 'AF', 'Takhar', 'TAK'),
		('Afghanistan', 'AF', 'Unknown', 'UNK'),
		('Afghanistan', 'AF', 'Vardak', 'VAR'),
		('Afghanistan', 'AF', 'Zabol', 'ZAB'),
		('Albania', 'AL', 'Beratit', 'BER'),
		('Albania', 'AL', 'Dibres', 'DIB'),
		('Albania', 'AL', 'Durresit', 'DUR'),
		('Albania', 'AL', 'Elbasanit', 'ELB'),
		('Albania', 'AL', 'Fierit', 'FIE'),
		('Albania', 'AL', 'Gjirokastres', 'GJI'),
		('Albania', 'AL', 'Korces', 'KOR'),
		('Albania', 'AL', 'Kukesit', 'KUK'),
		('Albania', 'AL', 'Lezhes', 'LEZ'),
		('Albania', 'AL', 'Shkodres', 'SHK'),
		('Albania', 'AL', 'Tiranes', 'TIR'),
		('Albania', 'AL', 'Vlores', 'VLO'),
		('Algeria', 'DZ', 'Adrar', 'ADR'),
		('Algeria', 'DZ', 'Ain Defla', 'AID'),
		('Algeria', 'DZ', 'Ain Temouchent', 'AIT'),
		('Algeria', 'DZ', 'Alger', 'ALG'),
		('Algeria', 'DZ', 'Annaba', 'ANN'),
		('Algeria', 'DZ', 'Batna', 'BAT'),
		('Algeria', 'DZ', 'Bechar', 'BEC'),
		('Algeria', 'DZ', 'Bejaia', 'BEJ'),
		('Algeria', 'DZ', 'Biskra', 'BIS'),
		('Algeria', 'DZ', 'Blida', 'BLI'),
		('Algeria', 'DZ', 'Bordj Bou Arreridj', 'BOR'),
		('Algeria', 'DZ', 'Bouira', 'BOU'),
		('Algeria', 'DZ', 'Chlef', 'CHL'),
		('Algeria', 'DZ', 'Constantine', 'CON'),
		('Algeria', 'DZ', 'Djelfa', 'DJE'),
		('Algeria', 'DZ', 'El Bayadh', 'ELB'),
		('Algeria', 'DZ', 'El Oued', 'ELO'),
		('Algeria', 'DZ', 'Ghardaia', 'GHA'),
		('Algeria', 'DZ', 'Guelma', 'GUE'),
		('Algeria', 'DZ', 'Illizi', 'ILL'),
		('Algeria', 'DZ', 'Jijel', 'JIJ'),
		('Algeria', 'DZ', 'Khenchela', 'KHE'),
		('Algeria', 'DZ', 'Laghouat', 'LAG'),
		('Algeria', 'DZ', 'M''Sila', 'MSI'),
		('Algeria', 'DZ', 'Mascara', 'MAS'),
		('Algeria', 'DZ', 'Medea', 'MED'),
		('Algeria', 'DZ', 'Mila', 'MIL'),
		('Algeria', 'DZ', 'Mostaganem', 'MOS'),
		('Algeria', 'DZ', 'Naama', 'NAA'),
		('Algeria', 'DZ', 'Oran', 'ORA'),
		('Algeria', 'DZ', 'Ouargla', 'OUA'),
		('Algeria', 'DZ', 'Oum el Bouaghi', 'OUM'),
		('Algeria', 'DZ', 'Relizane', 'REL'),
		('Algeria', 'DZ', 'Saida', 'SAI'),
		('Algeria', 'DZ', 'Setif', 'SET'),
		('Algeria', 'DZ', 'Sidi Bel Abbes', 'SID'),
		('Algeria', 'DZ', 'Skikda', 'SKI'),
		('Algeria', 'DZ', 'Souk Ahras', 'SOU'),
		('Algeria', 'DZ', 'Tamanghasset', 'TAM'),
		('Algeria', 'DZ', 'Tebessa', 'TEB'),
		('Algeria', 'DZ', 'Tiaret', 'TIA'),
		('Algeria', 'DZ', 'Tindouf', 'TIN'),
		('Algeria', 'DZ', 'Tipaza', 'TIP'),
		('Algeria', 'DZ', 'Tissemsilt', 'TIS'),
		('Algeria', 'DZ', 'Tizi Ouzou', 'TIZ'),
		('Algeria', 'DZ', 'Tlemcen', 'TLE'),
		('Andorra', 'AD', 'Andorra', 'AND'),
		('Angola', 'AO', 'Benguela', 'BEN'),
		('Angola', 'AO', 'Huambo', 'HUA'),
		('Angola', 'AO', 'Luanda', 'LUA'),
		('Angola', 'AO', 'Lunda Sul', 'LUS'),
		('Anguilla', 'AI', 'Anguilla', 'ANG'),
		('Antarctica', 'AQ', 'Antarctica', 'ARC'),
		('Antigua and Barbuda', 'AG', 'Antigua & Barbuda', 'ANT'),
		('Argentina', 'AR', 'Buenos Aires', 'BA'),
		('Argentina', 'AR', 'Catamarca', 'CAT'),
		('Argentina', 'AR', 'Chaco', 'CHA'),
		('Argentina', 'AR', 'Chubut', 'CHU'),
		('Argentina', 'AR', 'Cordoba', 'CDB'),
		('Argentina', 'AR', 'Corrientes', 'COR'),
		('Argentina', 'AR', 'Distrito Federal', 'DF'),
		('Argentina', 'AR', 'Entre Rios', 'ERI'),
		('Argentina', 'AR', 'Formosa', 'FOR'),
		('Argentina', 'AR', 'Jujuy', 'JUJ'),
		('Argentina', 'AR', 'La Pampa', 'LPA'),
		('Argentina', 'AR', 'La Rioja', 'LRI'),
		('Argentina', 'AR', 'Mendoza', 'MEN'),
		('Argentina', 'AR', 'Misiones', 'MIS'),
		('Argentina', 'AR', 'Neuquen', 'NEU'),
		('Argentina', 'AR', 'Rio Negro', 'RNE'),
		('Argentina', 'AR', 'Salta', 'SAL'),
		('Argentina', 'AR', 'San Juan', 'SJU'),
		('Argentina', 'AR', 'San Luis', 'SLU'),
		('Argentina', 'AR', 'Santa Cruz', 'SCR'),
		('Argentina', 'AR', 'Santa Fe', 'SFE'),
		('Argentina', 'AR', 'Santiago del Estero', 'SDE'),
		('Argentina', 'AR', 'Tierra del Fuego', 'TDF'),
		('Argentina', 'AR', 'Tucuman', 'TUC'),
		('Armenia', 'AM', 'Aragatsotni', 'ARA'),
		('Armenia', 'AM', 'Ararati', 'ARR'),
		('Armenia', 'AM', 'Armaviri', 'ARM'),
		('Armenia', 'AM', 'Geghark''unik''i', 'GEG'),
		('Armenia', 'AM', 'K''aghak'' Yerevan', 'KAY'),
		('Armenia', 'AM', 'Kalininskiy Rayon', 'KAL'),
		('Armenia', 'AM', 'Kotayk''i', 'KOT'),
		('Armenia', 'AM', 'Lorru', 'KAL'),
		('Armenia', 'AM', 'Shiraki', 'SHI'),
		('Armenia', 'AM', 'Syunik''i', 'SYU'),
		('Armenia', 'AM', 'Tavushi', 'TAV'),
		('Armenia', 'AM', 'Vayots'' Dzori', 'VAY'),
		('Aruba', 'AW', 'Aruba', 'ARU'),
		('Australia', 'AU', 'Australian Capital Territory', 'ACT'),
		('Australia', 'AU', 'New South Wales', 'NSW'),
		('Australia', 'AU', 'Northern Territory', 'NTY'),
		('Australia', 'AU', 'Queensland', 'QLD'),
		('Australia', 'AU', 'South Australia', 'SAU'),
		('Australia', 'AU', 'Tasmania', 'TAS'),
		('Australia', 'AU', 'Victoria', 'VIC'),
		('Australia', 'AU', 'Western Australia', 'WAU'),
		('Austria', 'AT', 'Burgenland', 'BUR'),
		('Austria', 'AT', 'Karnten', 'KAR'),
		('Austria', 'AT', 'Niederosterreich', 'NIE'),
		('Austria', 'AT', 'Oberosterreich', 'OBE'),
		('Austria', 'AT', 'Salzburg', 'SAL'),
		('Austria', 'AT', 'Steiermark', 'STE'),
		('Austria', 'AT', 'Tirol', 'TIR'),
		('Austria', 'AT', 'Vorarlberg', 'VOR'),
		('Austria', 'AT', 'Wien', 'WIE'),
		('Azerbaijan', 'AZ', 'Abseron', 'ABS'),
		('Azerbaijan', 'AZ', 'Agcabadi', 'AGC'),
		('Azerbaijan', 'AZ', 'Agdam', 'AGM'),
		('Azerbaijan', 'AZ', 'Agdas', 'AGD'),
		('Azerbaijan', 'AZ', 'Agstafa', 'AGS'),
		('Azerbaijan', 'AZ', 'Agsu', 'AGU'),
		('Azerbaijan', 'AZ', 'Ali Bayramli Sahari', 'ALI'),
		('Azerbaijan', 'AZ', 'Astara', 'AST'),
		('Azerbaijan', 'AZ', 'Baki Sahari', 'BAK'),
		('Azerbaijan', 'AZ', 'Balakan', 'BAL'),
		('Azerbaijan', 'AZ', 'Barda', 'BAR'),
		('Azerbaijan', 'AZ', 'Beylaqan', 'BEY'),
		('Azerbaijan', 'AZ', 'Bilasuvar', 'BIL'),
		('Azerbaijan', 'AZ', 'Cabrayil', 'CAB'),
		('Azerbaijan', 'AZ', 'Calilabad', 'CAL'),
		('Azerbaijan', 'AZ', 'Daskasan', 'DAS'),
		('Azerbaijan', 'AZ', 'Davaci', 'DAV'),
		('Azerbaijan', 'AZ', 'Fuzuli', 'FUZ'),
		('Azerbaijan', 'AZ', 'Gadabay', 'GAD'),
		('Azerbaijan', 'AZ', 'Ganca Sahari', 'GAN'),
		('Azerbaijan', 'AZ', 'Goranboy', 'GOR'),
		('Azerbaijan', 'AZ', 'Goycay', 'GOY'),
		('Azerbaijan', 'AZ', 'Haciqabul', 'HAC'),
		('Azerbaijan', 'AZ', 'Imisli', 'IMI'),
		('Azerbaijan', 'AZ', 'Ismayilli', 'ISM'),
		('Azerbaijan', 'AZ', 'Kalbacar', 'KAL'),
		('Azerbaijan', 'AZ', 'Kurdamir', 'KUR'),
		('Azerbaijan', 'AZ', 'Lacin', 'LAC'),
		('Azerbaijan', 'AZ', 'Lankaran', 'LAN'),
		('Azerbaijan', 'AZ', 'Lankaran Sahari', 'LAs'),
		('Azerbaijan', 'AZ', 'Lerik', 'LER'),
		('Azerbaijan', 'AZ', 'Masalli', 'MAS'),
		('Azerbaijan', 'AZ', 'Mingacevir Sahari', 'MIN'),
		('Azerbaijan', 'AZ', 'Naftalan Sahari', 'NAF'),
		('Azerbaijan', 'AZ', 'Naxcivan Muxtar Respublikasi', 'NAX'),
		('Azerbaijan', 'AZ', 'Neftcala', 'NEF'),
		('Azerbaijan', 'AZ', 'Oguz', 'OGU'),
		('Azerbaijan', 'AZ', 'Qabala', 'QAB'),
		('Azerbaijan', 'AZ', 'Qax', 'QAX'),
		('Azerbaijan', 'AZ', 'Qazax', 'QAZ'),
		('Azerbaijan', 'AZ', 'Qobustan', 'QOB'),
		('Azerbaijan', 'AZ', 'Quba', 'QUB'),
		('Azerbaijan', 'AZ', 'Qubadli', 'QUL'),
		('Azerbaijan', 'AZ', 'Qusar', 'QUS'),
		('Azerbaijan', 'AZ', 'Saatli', 'SAA'),
		('Azerbaijan', 'AZ', 'Sabirabad', 'SAB'),
		('Azerbaijan', 'AZ', 'Saki', 'SAK'),
		('Azerbaijan', 'AZ', 'Saki Sahari', 'SAS'),
		('Azerbaijan', 'AZ', 'Salyan', 'SAL'),
		('Azerbaijan', 'AZ', 'Samaxi', 'SAM'),
		('Azerbaijan', 'AZ', 'Samkir', 'SAR'),
		('Azerbaijan', 'AZ', 'Samux', 'SAX'),
		('Azerbaijan', 'AZ', 'Siyazan', 'SIY'),
		('Azerbaijan', 'AZ', 'Susa', 'SUR'),
		('Azerbaijan', 'AZ', 'Tartar', 'TAR'),
		('Azerbaijan', 'AZ', 'Tovuz', 'TOV'),
		('Azerbaijan', 'AZ', 'Ucar', 'UCA'),
		('Azerbaijan', 'AZ', 'Xacmaz', 'XAC'),
		('Azerbaijan', 'AZ', 'Xankandi Sahari', 'XAN'),
		('Azerbaijan', 'AZ', 'Xanlar', 'XAL'),
		('Azerbaijan', 'AZ', 'Xizi', 'XIZ'),
		('Azerbaijan', 'AZ', 'Xocali', 'XOC'),
		('Azerbaijan', 'AZ', 'Xocavand', 'XOV'),
		('Azerbaijan', 'AZ', 'Yardimli', 'YAR'),
		('Azerbaijan', 'AZ', 'Yevlax', 'YEV'),
		('Azerbaijan', 'AZ', 'Yevlax Sahari', 'YES'),
		('Azerbaijan', 'AZ', 'Zangilan', 'ZAN'),
		('Azerbaijan', 'AZ', 'Zaqatala', 'ZAQ'),
		('Azerbaijan', 'AZ', 'Zardab', 'ZAR'),
		('Bahamas', 'BS', 'Abaco', 'ABA'),
		('Bahamas', 'BS', 'Andros', 'AND'),
		('Bahamas', 'BS', 'Bimini Islands', 'BIM'),
		('Bahamas', 'BS', 'Cat Island', 'CAT'),
		('Bahamas', 'BS', 'Eleuthera', 'ELE'),
		('Bahamas', 'BS', 'Exuma & Cays', 'EXU'),
		('Bahamas', 'BS', 'Grand Bahama', 'GBA'),
		('Bahamas', 'BS', 'Harbour Island & Spanish Wells', 'HIS'),
		('Bahamas', 'BS', 'Inagua', 'INA'),
		('Bahamas', 'BS', 'Long Island', 'LIS'),
		('Bahamas', 'BS', 'Mayaguana', 'MAY'),
		('Bahamas', 'BS', 'New Providence', 'NPR'),
		('Bahamas', 'BS', 'Ragged Islands', 'RAG'),
		('Bahrain', 'BH', 'Al Hadd', 'HAD'),
		('Bahrain', 'BH', 'Al Manamah', 'MAN'),
		('Bahrain', 'BH', 'Al Mintaqah al Gharbiyah', 'GHA'),
		('Bahrain', 'BH', 'Al Mintaqah al Wusta', 'WUS'),
		('Bahrain', 'BH', 'Al Mintaqah ash Shamaliyah', 'SHA'),
		('Bahrain', 'BH', 'Al Muharraq', 'MUH'),
		('Bahrain', 'BH', 'Ar Rifa` wa al Mintaqah al Janubiyah', 'RIF'),
		('Bahrain', 'BH', 'Jidd Hafs', 'JID'),
		('Bahrain', 'BH', 'Madinat Hamad', 'MAH'),
		('Bahrain', 'BH', 'Madinat `Isa', 'MAI'),
		('Bahrain', 'BH', 'Mintaqat Juzur Hawar', 'JUZ'),
		('Bahrain', 'BH', 'Sitrah', 'SIT'),
		('Bangladesh', 'BD', 'Chittagong', 'CHI'),
		('Bangladesh', 'BD', 'Dhaka', 'DHA'),
		('Bangladesh', 'BD', 'Khulna', 'KHU'),
		('Bangladesh', 'BD', 'Rajshahi', 'RAJ'),
		('Barbados', 'BB', 'Christ Church', 'CHR'),
		('Barbados', 'BB', 'Saint Andrew', 'AND'),
		('Barbados', 'BB', 'Saint George', 'GEO'),
		('Barbados', 'BB', 'Saint James', 'JAM'),
		('Barbados', 'BB', 'Saint John', 'JOH'),
		('Barbados', 'BB', 'Saint Joseph', 'JOS'),
		('Barbados', 'BB', 'Saint Lucy', 'LUC'),
		('Barbados', 'BB', 'Saint Michael', 'MIC'),
		('Barbados', 'BB', 'Saint Peter', 'PET'),
		('Barbados', 'BB', 'Saint Philip', 'PHI'),
		('Barbados', 'BB', 'Saint Thomas', 'THO'),
		('Belarus', 'BY', 'Brestskaya', 'BRE'),
		('Belarus', 'BY', 'Homyel''skaya', 'HOM'),
		('Belarus', 'BY', 'Hrodzyenskaya', 'HRO'),
		('Belarus', 'BY', 'Mahilyowskaya', 'MAH'),
		('Belarus', 'BY', 'Minskaya', 'MIN'),
		('Belarus', 'BY', 'Unknown', 'UNK'),
		('Belarus', 'BY', 'Vitsyebskaya', 'VIT'),
		('Belgium', 'BE', 'Antwerpen', 'ANT'),
		('Belgium', 'BE', 'Hainaut', 'HAI'),
		('Belgium', 'BE', 'Liege', 'LIE'),
		('Belgium', 'BE', 'Limburg', 'LIM'),
		('Belgium', 'BE', 'Luxembourg', 'LUX'),
		('Belgium', 'BE', 'Namur', 'NAM'),
		('Belgium', 'BE', 'Oost-Vlaanderen', 'OOV'),
		('Belgium', 'BE', 'Unknown', 'UNK'),
		('Belgium', 'BE', 'West-Vlaanderen', 'WEV'),
		('Belize', 'BZ', 'Belize', 'BEL'),
		('Belize', 'BZ', 'Cayo', 'CAY'),
		('Belize', 'BZ', 'Corozal', 'COR'),
		('Belize', 'BZ', 'Orange Walk', 'ORA'),
		('Belize', 'BZ', 'Stann Creek', 'STA'),
		('Belize', 'BZ', 'Toledo', 'TOL'),
		('Benin', 'BJ', 'Unknown', 'UNK'),
		('Bermuda', 'BM', 'Devonshire', 'DEV'),
		('Bermuda', 'BM', 'Hamilton', 'HAM'),
		('Bermuda', 'BM', 'Paget', 'PAG'),
		('Bermuda', 'BM', 'Pembroke', 'PEM'),
		('Bermuda', 'BM', 'Saint Georges', 'SGS'),
		('Bermuda', 'BM', 'Sandys', 'SAN'),
		('Bermuda', 'BM', 'Smiths', 'SMI'),
		('Bermuda', 'BM', 'Southampton', 'SOU'),
		('Bermuda', 'BM', 'Warwick', 'WAR'),
		('Bhutan', 'BT', 'Bhutan', 'BHU'),
		('Bolivia', 'BO', 'Beni', 'BEN'),
		('Bolivia', 'BO', 'Chuquisaca', 'CHU'),
		('Bolivia', 'BO', 'Cochabamba', 'COC'),
		('Bolivia', 'BO', 'La Paz', 'LAP'),
		('Bolivia', 'BO', 'Oruro', 'ORU'),
		('Bolivia', 'BO', 'Pando', 'PAN'),
		('Bolivia', 'BO', 'Potosi', 'POT'),
		('Bolivia', 'BO', 'Santa Cruz', 'SAN'),
		('Bolivia', 'BO', 'Tarija', 'TAR'),
		('Bosnia and Herzegovina', 'BA', 'Bosnia and Herzegovina', 'BOS'),
		('Bosnia and Herzegovina', 'BA', 'Republika Srpska', 'SRP'),
		('Bosnia and Herzegovina', 'BA', 'Unknown', 'UNK'),
		('Botswana', 'BW', 'Gaborone', 'GAB'),
		('Botswana', 'BW', 'Unknown', 'UNK'),
		('Brazil', 'BR', 'Acre', 'ACR'),
		('Brazil', 'BR', 'Alagoas', 'ALA'),
		('Brazil', 'BR', 'Amapa', 'AMP'),
		('Brazil', 'BR', 'Amazonas', 'AMZ'),
		('Brazil', 'BR', 'Bahia', 'BAH'),
		('Brazil', 'BR', 'Ceara', 'CEA'),
		('Brazil', 'BR', 'Distrito Federal', 'DF'),
		('Brazil', 'BR', 'Espirito Santo', 'ESA'),
		('Brazil', 'BR', 'Goias', 'GOI'),
		('Brazil', 'BR', 'Maranhao', 'MAR'),
		('Brazil', 'BR', 'Mato Grosso', 'MGR'),
		('Brazil', 'BR', 'Mato Grosso do Sul', 'MGS'),
		('Brazil', 'BR', 'Minas Gerais', 'MGE'),
		('Brazil', 'BR', 'Para', 'PAR'),
		('Brazil', 'BR', 'Paraiba', 'PRB'),
		('Brazil', 'BR', 'Parana', 'PRN'),
		('Brazil', 'BR', 'Pernambuco', 'PER'),
		('Brazil', 'BR', 'Piaui', 'PIA'),
		('Brazil', 'BR', 'Rio de Janeiro', 'RDJ'),
		('Brazil', 'BR', 'Rio Grande do Norte', 'RGN'),
		('Brazil', 'BR', 'Rio Grande do Sul', 'RGS'),
		('Brazil', 'BR', 'Rondonia', 'RON'),
		('Brazil', 'BR', 'Roraima', 'ROR'),
		('Brazil', 'BR', 'Santa Catarina', 'SCA'),
		('Brazil', 'BR', 'Sao Paulo', 'SPA'),
		('Brazil', 'BR', 'Sergipe', 'SER'),
		('Brazil', 'BR', 'Tocantins', 'TOC'),
		('British Virgin Islands', 'VG', 'British Virgin Islands', 'BVI'),
		('Brunei', 'BN', 'Brunei', 'BRU'),
		('Bulgaria', 'BG', 'Blagoevgrad', 'BLA'),
		('Bulgaria', 'BG', 'Burgas', 'BUR'),
		('Bulgaria', 'BG', 'Dobrich', 'DOB'),
		('Bulgaria', 'BG', 'Gabrovo', 'GAB'),
		('Bulgaria', 'BG', 'Khaskovo', 'KHA'),
		('Bulgaria', 'BG', 'Kurdzhali', 'KUR'),
		('Bulgaria', 'BG', 'Kyustendil', 'KYU'),
		('Bulgaria', 'BG', 'Lovech', 'LOV'),
		('Bulgaria', 'BG', 'Montana', 'MON'),
		('Bulgaria', 'BG', 'Pazardzhik', 'PAZ'),
		('Bulgaria', 'BG', 'Pernik', 'PER'),
		('Bulgaria', 'BG', 'Pleven', 'PLE'),
		('Bulgaria', 'BG', 'Plovdiv', 'PLO'),
		('Bulgaria', 'BG', 'Razgrad', 'RAZ'),
		('Bulgaria', 'BG', 'Ruse', 'RUS'),
		('Bulgaria', 'BG', 'Shumen', 'SHU'),
		('Bulgaria', 'BG', 'Silistra', 'SIL'),
		('Bulgaria', 'BG', 'Sliven', 'SLI'),
		('Bulgaria', 'BG', 'Smolyan', 'SMO'),
		('Bulgaria', 'BG', 'Sofiya', 'SOF'),
		('Bulgaria', 'BG', 'Sofiya-Grad', 'SFG'),
		('Bulgaria', 'BG', 'Stara Zagora', 'STA'),
		('Bulgaria', 'BG', 'Turgovishte', 'TUR'),
		('Bulgaria', 'BG', 'Varna', 'VAR'),
		('Bulgaria', 'BG', 'Veliko Turnovo', 'VEL'),
		('Bulgaria', 'BG', 'Vidin', 'VID'),
		('Bulgaria', 'BG', 'Vratsa', 'VRA'),
		('Bulgaria', 'BG', 'Yambol', 'YAM'),
		('Burkina Faso', 'BF', 'Burkina Faso', 'BUF') 
		");

		$this->query("
		INSERT INTO `States` (`Country`, `CountryCode`, `State`, `StateCode`) VALUES
		('Burundi', 'BN', 'Burundi', 'BUR'),
		('Cambodia', 'KH', 'Batdambang', 'BAT'),
		('Cambodia', 'KH', 'Kampong Cham', 'CHA'),
		('Cambodia', 'KH', 'Kampong Chhnang', 'CHH'),
		('Cambodia', 'KH', 'Kampong Spoe', 'SPO'),
		('Cambodia', 'KH', 'Kampong Thum', 'THU'),
		('Cambodia', 'KH', 'Kampot', 'KAM'),
		('Cambodia', 'KH', 'Kandal', 'KAN'),
		('Cambodia', 'KH', 'Kaoh Kong', 'KAO'),
		('Cambodia', 'KH', 'Kracheh', 'KRA'),
		('Cambodia', 'KH', 'Mondol Kiri', 'MON'),
		('Cambodia', 'KH', 'Pouthisat', 'POU'),
		('Cambodia', 'KH', 'Preah Vihear', 'PRE'),
		('Cambodia', 'KH', 'Prey Veng', 'PRV'),
		('Cambodia', 'KH', 'Rotanah Kiri', 'ROT'),
		('Cambodia', 'KH', 'Siem Reab', 'SIE'),
		('Cambodia', 'KH', 'Stoeng Treng', 'STO'),
		('Cambodia', 'KH', 'Svay Rieng', 'SVA'),
		('Cambodia', 'KH', 'Takev', 'TAK'),
		('Cambodia', 'KH', 'Unknown', 'UNK'),
		('Cameroon', 'CM', 'Cameroon', 'CAM'),
		('Canada', 'CA', 'Alberta', 'AB'),
		('Canada', 'CA', 'British Columbia', 'BC'),
		('Canada', 'CA', 'Manitoba', 'MB'),
		('Canada', 'CA', 'New Brunswick', 'NB'),
		('Canada', 'CA', 'Newfoundland and Labrador', 'NF'),
		('Canada', 'CA', 'Northwest Territories', 'NT'),
		('Canada', 'CA', 'Nova Scotia', 'NS'),
		('Canada', 'CA', 'Nunavut', 'NU'),
		('Canada', 'CA', 'Ontario', 'ON'),
		('Canada', 'CA', 'Prince Edward Island', 'PE'),
		('Canada', 'CA', 'Quebec', 'QC'),
		('Canada', 'CA', 'Saskatchewan', 'SK'),
		('Canada', 'CA', 'Yukon', 'YT'),
		('Cape Verde', 'CV', 'Cape Verde', 'CPV'),
		('Cayman Islands', 'KY', 'Cayman Islands', 'CYN'),
		('Central African Republic', 'CF', 'Central African Republic', 'CAR'),
		('Chad', 'TD', 'Batha', 'BAT'),
		('Chad', 'TD', 'Biltine', 'BIL'),
		('Chad', 'TD', 'Borkou-Ennedi-Tibesti', 'BET'),
		('Chad', 'TD', 'Chari-Baguirmi', 'CHB'),
		('Chad', 'TD', 'Guera', 'GUE'),
		('Chad', 'TD', 'Kanem', 'KAN'),
		('Chad', 'TD', 'Lac', 'LAC'),
		('Chad', 'TD', 'Logone Occidental', 'LOC'),
		('Chad', 'TD', 'Logone Oriental', 'LOR'),
		('Chad', 'TD', 'Mayo-Kebbi', 'MAK'),
		('Chad', 'TD', 'Moyen-Chari', 'MOC'),
		('Chad', 'TD', 'Ouaddai', 'OUA'),
		('Chad', 'TD', 'Salamat', 'SAL'),
		('Chad', 'TD', 'Tandjile', 'TAN'),
		('Chile', 'CL', 'Aisen del General Carlos Ibanez del Campo', 'AIS'),
		('Chile', 'CL', 'Antofagasta', 'ANT'),
		('Chile', 'CL', 'Araucania', 'ARA'),
		('Chile', 'CL', 'Atacama', 'ATA'),
		('Chile', 'CL', 'Bio-Bio', 'BIO'),
		('Chile', 'CL', 'Coquimbo', 'COQ'),
		('Chile', 'CL', 'Libertador G.B. O''Higgins', 'LIB'),
		('Chile', 'CL', 'Los Lagos', 'LAG'),
		('Chile', 'CL', 'Magallanes y de la Antartica Chilena', 'MAG'),
		('Chile', 'CL', 'Maule', 'MAU'),
		('Chile', 'CL', 'Region Metropolitana', 'MET'),
		('Chile', 'CL', 'Tarapaca', 'TAR'),
		('Chile', 'CL', 'Valparaiso', 'VAL'),
		('China', 'CN', 'Anhui', 'ANH'),
		('China', 'CN', 'Beijing Shi', 'BEJ'),
		('China', 'CN', 'Chongqing Shi', 'CHS'),
		('China', 'CN', 'Fujian', 'FUJ'),
		('China', 'CN', 'Gansu', 'GAN'),
		('China', 'CN', 'Guangdong', 'GUA'),
		('China', 'CN', 'Guangxi Zhuangzu', 'GUZ'),
		('China', 'CN', 'Guizhou', 'GUI'),
		('China', 'CN', 'Hainan', 'HAI'),
		('China', 'CN', 'Hebei', 'HEB'),
		('China', 'CN', 'Heilongjiang', 'HEI'),
		('China', 'CN', 'Henan', 'HEN'),
		('China', 'CN', 'Hubei', 'HUB'),
		('China', 'CN', 'Hunan', 'HUN'),
		('China', 'CN', 'Inner Mongolia', 'IMO'),
		('China', 'CN', 'Jiangsu', 'JIU'),
		('China', 'CN', 'Jiangxi', 'JII'),
		('China', 'CN', 'Jilin', 'JIN'),
		('China', 'CN', 'Liaoning', 'LIA'),
		('China', 'CN', 'Ningxia Huizu', 'NIH'),
		('China', 'CN', 'Qinghai', 'QIN'),
		('China', 'CN', 'Shaanxi', 'SHX'),
		('China', 'CN', 'Shandong', 'SHG'),
		('China', 'CN', 'Shanxi', 'SHI'),
		('China', 'CN', 'Sichuan', 'SIC'),
		('China', 'CN', 'Tibet', 'TIB'),
		('China', 'CN', 'Unknown', 'UNK'),
		('China', 'CN', 'Xinjiang Uygur', 'XIU'),
		('China', 'CN', 'Yunnan', 'YUN'),
		('China', 'CN', 'Zhejiang', 'ZHE'),
		('Christmas Island', 'CX', 'Christmas Island', 'CHI'),
		('Columbia', 'CO', 'Antioquia', 'ANT'),
		('Columbia', 'CO', 'Atlantico', 'ALT'),
		('Columbia', 'CO', 'Bogota', 'BOG'),
		('Columbia', 'CO', 'Bolivar', 'BOL'),
		('Columbia', 'CO', 'Cauca', 'CAU'),
		('Columbia', 'CO', 'Cundinamarca', 'CUN'),
		('Columbia', 'CO', 'Magdalena', 'MAG'),
		('Columbia', 'CO', 'Meta', 'MET'),
		('Columbia', 'CO', 'Santander', 'SAN'),
		('Columbia', 'CO', 'Valle del Cauca', 'VAL'),
		('Comoros', 'KM', 'Comoros', 'COM'),
		('Congo', 'CG', 'Bouenza', 'BOU'),
		('Congo', 'CG', 'Brazzaville', 'BRA'),
		('Congo', 'CG', 'Cuvette', 'CUV'),
		('Congo', 'CG', 'Kouilou', 'KOU'),
		('Congo', 'CG', 'Lekoumou', 'LEK'),
		('Congo', 'CG', 'Likouala', 'LIK'),
		('Congo', 'CG', 'Niari', 'NIA'),
		('Congo', 'CG', 'Plateaux', 'PLA'),
		('Congo', 'CG', 'Pool', 'POO'),
		('Congo', 'CG', 'Sangha', 'SAN'),
		('Cook Islands', 'CW', 'Cook Islands', 'CKI'),
		('Costa Rica', 'CR', 'Alajuela', 'ALA'),
		('Costa Rica', 'CR', 'Cartago', 'CAR'),
		('Costa Rica', 'CR', 'Guanacaste', 'GUA'),
		('Costa Rica', 'CR', 'Heredia', 'HER'),
		('Costa Rica', 'CR', 'Limon', 'LIM'),
		('Costa Rica', 'CR', 'Puntarenas', 'PUN'),
		('Costa Rica', 'CR', 'San Jose', 'SJO'),
		('Cote D''Ivoire', 'CI', 'Cote D''Ivoire', 'CDI'),
		('Croatia', 'HR', 'Bjelovarsko-Bilogorska', 'BJE'),
		('Croatia', 'HR', 'Brodsko-Posavska', 'BRO'),
		('Croatia', 'HR', 'Dubrovacko-Neretvanska', 'DUB'),
		('Croatia', 'HR', 'Grad Zagreb', 'GRA'),
		('Croatia', 'HR', 'Istarska', 'IST'),
		('Croatia', 'HR', 'Karlovacka', 'KAR'),
		('Croatia', 'HR', 'Koprivnicko-Krizevacka', 'KOP'),
		('Croatia', 'HR', 'Krapinsko-Zagorska', 'KRA'),
		('Croatia', 'HR', 'Licko-Senjska', 'LIC'),
		('Croatia', 'HR', 'Medimurska', 'MED'),
		('Croatia', 'HR', 'Osjecko-Baranjska', 'OSJ'),
		('Croatia', 'HR', 'Pozesko-Slavonska', 'POZ'),
		('Croatia', 'HR', 'Primorsko-Goranska', 'PRI'),
		('Croatia', 'HR', 'Sibensko-Kninska', 'SIB'),
		('Croatia', 'HR', 'Sisacko-Moslavacka', 'SIS'),
		('Croatia', 'HR', 'Splitsko-Dalmatinska', 'SPL'),
		('Croatia', 'HR', 'Varazdinska', 'VAR'),
		('Croatia', 'HR', 'Viroviticko-Podravska', 'VIR'),
		('Croatia', 'HR', 'Vukovarsko-Srijemska', 'VUK'),
		('Croatia', 'HR', 'Zagrebacka', 'ZAG'),
		('Cuba', 'CU', 'Camaguey', 'CAM'),
		('Cuba', 'CU', 'Ciego de Avila', 'CDA'),
		('Cuba', 'CU', 'Cienfuegos', 'CIE'),
		('Cuba', 'CU', 'Ciudad de la Habana', 'CLH'),
		('Cuba', 'CU', 'Granma', 'GRA'),
		('Cuba', 'CU', 'Guantanamo', 'GUA'),
		('Cuba', 'CU', 'Holguin', 'HOL'),
		('Cuba', 'CU', 'Isla de la Juventud', 'JUV'),
		('Cuba', 'CU', 'La Habana', 'HAB'),
		('Cuba', 'CU', 'Las Tunas', 'TUN'),
		('Cuba', 'CU', 'Matanzas', 'MAT'),
		('Cuba', 'CU', 'Pinar del Rio', 'PIN'),
		('Cuba', 'CU', 'Sancti Spiritus', 'SSP'),
		('Cuba', 'CU', 'Santiago de Cuba', 'SAN'),
		('Cuba', 'CU', 'Villa Clara', 'VIL'),
		('Cyprus', 'CY', 'Cyprus', 'CYP'),
		('Czech Republic', 'CZ', 'Czech Republic', 'CZE'),
		('Democratic Repubilic of Congo', 'CD', 'Bandundu', 'BAN'),
		('Democratic Repubilic of Congo', 'CD', 'Bas-Congo', 'BAS'),
		('Democratic Repubilic of Congo', 'CD', 'Equateur', 'EQU'),
		('Democratic Repubilic of Congo', 'CD', 'Kasai-Occidental', 'KOC'),
		('Democratic Repubilic of Congo', 'CD', 'Katanga', 'KAT'),
		('Democratic Repubilic of Congo', 'CD', 'Kinshasa', 'KIN'),
		('Democratic Repubilic of Congo', 'CD', 'Kivu', 'KIV'),
		('Democratic Repubilic of Congo', 'CD', 'Maniema', 'MAN'),
		('Democratic Repubilic of Congo', 'CD', 'Nord-Kivu', 'NKI'),
		('Democratic Repubilic of Congo', 'CD', 'Orientale', 'ORI'),
		('Democratic Repubilic of Congo', 'CD', 'Sud-Kivu', 'SKI'),
		('Denmark', 'DK', 'Arhus Amt', 'ARH'),
		('Denmark', 'DK', 'Bornholms Amt', 'BOR'),
		('Denmark', 'DK', 'Frederiksberg Kommune', 'FK'),
		('Denmark', 'DK', 'Frederiksborg Amt', 'FRE'),
		('Denmark', 'DK', 'Fyns Amt', 'FYN'),
		('Denmark', 'DK', 'Kobenhavns Amt', 'KOB'),
		('Denmark', 'DK', 'Kobenhavns Kommune', 'KK'),
		('Denmark', 'DK', 'Nordjyllands Amt', 'NJL'),
		('Denmark', 'DK', 'Ribe Amt', 'RIB'),
		('Denmark', 'DK', 'Ringkobing Amt', 'RIN'),
		('Denmark', 'DK', 'Roskilde Amt', 'ROS'),
		('Denmark', 'DK', 'Sonderjyllands Amt', 'SJL'),
		('Denmark', 'DK', 'Storstroms Amt', 'STO'),
		('Denmark', 'DK', 'Vejle Amt', 'VEJ'),
		('Denmark', 'DK', 'Vestsjaellands Amt', 'VSL'),
		('Denmark', 'DK', 'Viborg Amt', 'VIB'),
		('Djibouti', 'DJ', 'Djibouti', 'DJI'),
		('Dominica', 'DM', 'Dominica', 'DOM'),
		('Dominican Republic', 'DO', 'Azua', 'AZU'),
		('Dominican Republic', 'DO', 'Baoruco', 'BAH'),
		('Dominican Republic', 'DO', 'Barahona', 'BAR'),
		('Dominican Republic', 'DO', 'Dajabon', 'DAJ'),
		('Dominican Republic', 'DO', 'Distrito Nacional', 'DN'),
		('Dominican Republic', 'DO', 'Duarte', 'DUA'),
		('Dominican Republic', 'DO', 'El Seibo', 'ESE'),
		('Dominican Republic', 'DO', 'Elias Pina', 'EPA'),
		('Dominican Republic', 'DO', 'Espaillat', 'ESP'),
		('Dominican Republic', 'DO', 'Hato Mayor', 'HMA'),
		('Dominican Republic', 'DO', 'Independencia', 'IND'),
		('Dominican Republic', 'DO', 'La Altagracia', 'LAL'),
		('Dominican Republic', 'DO', 'La Romana', 'LRO'),
		('Dominican Republic', 'DO', 'La Vega', 'LVE'),
		('Dominican Republic', 'DO', 'Maria Trinidad Sanchez', 'MTS'),
		('Dominican Republic', 'DO', 'Monsenor Nouel', 'MSN'),
		('Dominican Republic', 'DO', 'Monte Cristi', 'MCR'),
		('Dominican Republic', 'DO', 'Monte Plata', 'MPL'),
		('Dominican Republic', 'DO', 'Pedernales', 'PED'),
		('Dominican Republic', 'DO', 'Peravia', 'PER'),
		('Dominican Republic', 'DO', 'Puerto Plata', 'PPL'),
		('Dominican Republic', 'DO', 'Salcedo', 'SAL'),
		('Dominican Republic', 'DO', 'Samana', 'SAM'),
		('Dominican Republic', 'DO', 'San Cristobal', 'SCR'),
		('Dominican Republic', 'DO', 'San Juan', 'SJU'),
		('Dominican Republic', 'DO', 'San Pedro de Macoris', 'SPM'),
		('Dominican Republic', 'DO', 'Sanchez Ramirez', 'SRA'),
		('Dominican Republic', 'DO', 'Santiago', 'SAN'),
		('Dominican Republic', 'DO', 'Santiago Rodriguez', 'SRO'),
		('Dominican Republic', 'DO', 'Valverde', 'VAL'),
		('East Timor', 'TL', 'East Timor', 'ETM'),
		('Ecuador', 'EC', 'Azuay', 'AZU'),
		('Ecuador', 'EC', 'Boliar', 'BOL'),
		('Ecuador', 'EC', 'Canar', 'CAN'),
		('Ecuador', 'EC', 'Carchi', 'CAR'),
		('Ecuador', 'EC', 'Chimborazo', 'CHI'),
		('Ecuador', 'EC', 'Cotopaxi', 'COT'),
		('Ecuador', 'EC', 'El Oro', 'EOR'),
		('Ecuador', 'EC', 'Esmeraldas', 'ESM'),
		('Ecuador', 'EC', 'Galapagos', 'GAL'),
		('Ecuador', 'EC', 'Guayas', 'GUA'),
		('Ecuador', 'EC', 'Imbabura', 'IMB'),
		('Ecuador', 'EC', 'Loja', 'LOJ'),
		('Ecuador', 'EC', 'Los Rios', 'LRI'),
		('Ecuador', 'EC', 'Manabi', 'MAN'),
		('Ecuador', 'EC', 'Morona-Santiago', 'MSA'),
		('Ecuador', 'EC', 'Napo', 'NAP'),
		('Ecuador', 'EC', 'Orellana', 'ORE'),
		('Ecuador', 'EC', 'Pastaza', 'PAS'),
		('Ecuador', 'EC', 'Pichincha', 'PIC'),
		('Ecuador', 'EC', 'Sucumbios', 'SUC'),
		('Ecuador', 'EC', 'Zamora-Chinchipe', 'ZCH'),
		('Egypt', 'EG', 'Ad-Daqahiyah', 'DAQ'),
		('Egypt', 'EG', 'Al-Bahr al-Ahmar', 'BAH'),
		('Egypt', 'EG', 'Al-Buhayrah', 'BUH'),
		('Egypt', 'EG', 'Al-Fayyum', 'FAY'),
		('Egypt', 'EG', 'Al-Gharbiyah', 'GHA'),
		('Egypt', 'EG', 'Al-Iskandariyah', 'ISK'),
		('Egypt', 'EG', 'Al-Isma''iliyah', 'ISM'),
		('Egypt', 'EG', 'Al-Jizah', 'JIZ'),
		('Egypt', 'EG', 'Al-Minufiyah', 'MNF'),
		('Egypt', 'EG', 'Al-Minya', 'MIN'),
		('Egypt', 'EG', 'Al-Qahirah', 'QAH'),
		('Egypt', 'EG', 'Al-Qalyubyah', 'QAL'),
		('Egypt', 'EG', 'Al-Wadi al-Jadid', 'WJA'),
		('Egypt', 'EG', 'As-Suways', 'SUW'),
		('Egypt', 'EG', 'Ash-Sharqiyah', 'SHA'),
		('Egypt', 'EG', 'Aswan', 'ASW'),
		('Egypt', 'EG', 'Asyut', 'ASY'),
		('Egypt', 'EG', 'Bani Suwayf', 'BSU'),
		('Egypt', 'EG', 'Bur Sa''id', 'BSA'),
		('Egypt', 'EG', 'Dumyat', 'DUM'),
		('Egypt', 'EG', 'Kafr ash-Shaykh', 'KSH'),
		('Egypt', 'EG', 'Marsa Matruh', 'MMA'),
		('Egypt', 'EG', 'Qina', 'QIN'),
		('Egypt', 'EG', 'Sawhaj', 'SAW'),
		('Egypt', 'EG', 'Sina'' al-Janubiyah', 'SJA'),
		('Egypt', 'EG', 'Sina'' ash-Shamaliyah', 'SSH'),
		('El Salvador', 'SV', 'Ahuachapan', 'AHU'),
		('El Salvador', 'SV', 'Cabanas', 'CAB'),
		('El Salvador', 'SV', 'Chalatenango', 'CHA'),
		('El Salvador', 'SV', 'Cuscatlan', 'CUS'),
		('El Salvador', 'SV', 'La Libertad', 'LLI'),
		('El Salvador', 'SV', 'La Paz', 'LPA'),
		('El Salvador', 'SV', 'La Union', 'LUN'),
		('El Salvador', 'SV', 'Morazan', 'MOR'),
		('El Salvador', 'SV', 'San Miguel', 'SMI'),
		('El Salvador', 'SV', 'San Salvador', 'SSA'),
		('El Salvador', 'SV', 'San Vicente', 'SVI'),
		('El Salvador', 'SV', 'Santa Ana', 'SAN'),
		('El Salvador', 'SV', 'Sonsonate', 'SON'),
		('El Salvador', 'SV', 'Usulutan', 'USU'),
		('Equatorial Guinea', 'GQ', 'Equatorial Guinea', 'EQG'),
		('Eritrea', 'ER', 'Eritrea', 'ERI'),
		('Estonia', 'EE', 'Estonia', 'EST'),
		('Ethiopia', 'ET', 'Ethiopia', 'ETH'),
		('Falkland Islands', 'FK', 'Falkland Islands', 'FAL'),
		('Faroe Islands', 'FO', 'Faroe Islands', 'FAR'),
		('Fiji', 'FJ', 'Fiji', 'FIJ'),
		('Finland', 'FI', 'Alands Lan', 'ALA'),
		('Finland', 'FI', 'Lapplands Lan', 'LPA'),
		('Finland', 'FI', 'Ostra Finlands Lan', 'OST'),
		('Finland', 'FI', 'Sodra Finlands Lan', 'SOD'),
		('Finland', 'FI', 'Uleaborgs Lan', 'ULE'),
		('Finland', 'FI', 'Vastra Finlands Lan', 'VAS'),
		('France', 'FR', 'Alsace', 'ALS'),
		('France', 'FR', 'Aquitaine', 'AQU'),
		('France', 'FR', 'Auvergne', 'AUV'),
		('France', 'FR', 'Basse-Normandie', 'BAS'),
		('France', 'FR', 'Bourgogne', 'BOU'),
		('France', 'FR', 'Bretagne', 'BRE'),
		('France', 'FR', 'Centre', 'CEN'),
		('France', 'FR', 'Champagne-Ardenne', 'CHA'),
		('France', 'FR', 'Corse', 'COR'),
		('France', 'FR', 'Franche-Comte', 'FRA'),
		('France', 'FR', 'Haute-Normandie', 'HAU'),
		('France', 'FR', 'Ile-de-France', 'ILE'),
		('France', 'FR', 'Languedoc-Roussillon', 'LAN'),
		('France', 'FR', 'Limousin', 'LIM'),
		('France', 'FR', 'Lorraine', 'LOR'),
		('France', 'FR', 'Midi-Pyrenees', 'MID'),
		('France', 'FR', 'Nord-Pas-de-Calais', 'NOR'),
		('France', 'FR', 'Pays-de-la Loire', 'PAY'),
		('France', 'FR', 'Picardie', 'PIC'),
		('France', 'FR', 'Poitou-Charentes', 'POI'),
		('France', 'FR', 'Provence-Alpes-Cote d''Azur', 'PRO'),
		('France', 'FR', 'Rhooe-Alpes', 'RHO'),
		('France', 'FR', 'Unknown', 'UNK'),
		('French Guiana', 'GF', 'French Guiana', 'FGU'),
		('French Polynesia', 'FP', 'French Polynesia', 'FPY'),
		('Gabon', 'GA', 'Gabon', 'GAB'),
		('Gambia', 'GM', 'Gambia', 'GAM'),
		('Georgia', 'GE', 'Georgia', 'GEO'),
		('Germany', 'DE', 'Baden-Wurttemberg', 'BW'),
		('Germany', 'DE', 'Bayern', 'BY'),
		('Germany', 'DE', 'Berlin', 'BE'),
		('Germany', 'DE', 'Brandenburg', 'BB'),
		('Germany', 'DE', 'Bremen', 'HB'),
		('Germany', 'DE', 'Hamburg', 'HH'),
		('Germany', 'DE', 'Hessen', 'HE'),
		('Germany', 'DE', 'Mecklenburg-Vorpommern', 'MV'),
		('Germany', 'DE', 'Niedersachsen', 'NI'),
		('Germany', 'DE', 'Nordrhein-Westfalen', 'NW'),
		('Germany', 'DE', 'Rheinland-Pfalz', 'RP'),
		('Germany', 'DE', 'Saarland', 'SL'),
		('Germany', 'DE', 'Sachsen', 'SN'),
		('Germany', 'DE', 'Sachsen-Anhalt', 'ST'),
		('Germany', 'DE', 'Schleswig-Holstein', 'SH'),
		('Germany', 'DE', 'Thuringen', 'TH'),
		('Ghana', 'GH', 'Kumasi', 'KUI'),
		('Ghana', 'GH', 'Sunyani', 'SUI'),
		('Ghana', 'GH', 'Cape Coast', 'CAT'),
		('Ghana', 'GH', 'Koforidua', 'KOA'),
		('Ghana', 'GH', 'Accra', 'ACA'),
		('Ghana', 'GH', 'Tamale', 'TAE'),
		('Ghana', 'GH', 'Bolgatanga', 'BOA'),
		('Ghana', 'GH', 'Wa', 'Wa'),
		('Ghana', 'GH', 'Ho', 'Ho'),
		('Ghana', 'GH', 'Sekondi-Takoradi', 'STI'), 
		('Gibraltar', 'GI', 'Gibraltar', 'GIB'),
		('Greece', 'GR', 'Aegean Islands', 'AIY'),
		('Greece', 'GR', 'Attiki', 'ATT'),
		('Greece', 'GR', 'Central Greece & Evvoia', 'SEE'),
		('Greece', 'GR', 'Crete', 'KRI'),
		('Greece', 'GR', 'Epirus', 'IPI'),
		('Greece', 'GR', 'Ionia Islands', 'ION'),
		('Greece', 'GR', 'Macedonia', 'MAK'),
		('Greece', 'GR', 'Peloponnesus', 'PEL'),
		('Greece', 'GR', 'Thessalia', 'THE'),
		('Greece', 'GR', 'Thrace', 'THR'),
		('Greenland', 'GL', 'Greenland', 'GL'),
		('Grenada', 'GD', 'Grenada', 'GJ'),
		('Guadeloupe', 'GP', 'Guadeloupe', 'GUA'),
		('Guatemala', 'GT', 'Alta Verapaz', 'AVE'),
		('Guatemala', 'GT', 'Baja Verapaz', 'BVE'),
		('Guatemala', 'GT', 'Chimaltenango', 'CMT'),
		('Guatemala', 'GT', 'Chiquimula', 'CQM'),
		('Guatemala', 'GT', 'El Progreso', 'EPR'),
		('Guatemala', 'GT', 'Escuintla', 'ESC'),
		('Guatemala', 'GT', 'Guatemala', 'GUA'),
		('Guatemala', 'GT', 'Huehuetenango', 'HUE'),
		('Guatemala', 'GT', 'Izabal', 'IZA'),
		('Guatemala', 'GT', 'Jalapa', 'JAL'),
		('Guatemala', 'GT', 'Jutiapa', 'JUT'),
		('Guatemala', 'GT', 'Peten', 'PET'),
		('Guatemala', 'GT', 'Quetzaltenango', 'QUE'),
		('Guatemala', 'GT', 'Quiche', 'QUI'),
		('Guatemala', 'GT', 'Retalhuleu', 'RET'),
		('Guatemala', 'GT', 'Sacatepequez', 'SAC'),
		('Guatemala', 'GT', 'San Marcos', 'SMA'),
		('Guatemala', 'GT', 'Santa Rosa', 'SRO'),
		('Guatemala', 'GT', 'Solola', 'SOL'),
		('Guatemala', 'GT', 'Suchitepequez', 'SUC'),
		('Guatemala', 'GT', 'Totonicapan', 'TOT'),
		('Guatemala', 'GT', 'Zacapa', 'ZAC'),
		('Guernsey', 'GG', 'Guernsey', 'GUR'),
		('Guinea', 'GN', 'Guinea', 'GUI'),
		('Guinea-Bissau', 'GW', 'Guinea-Bissau', 'GUB'),
		('Guyana', 'GY', 'Guyana', 'GUY') 
		");

		$this->query("
		INSERT INTO `States` (`Country`, `CountryCode`, `State`, `StateCode`) VALUES
		('Haiti', 'HT', 'Haiti', 'HAI'),
		('Honduras', 'HN', 'Atlantida', 'ATL'),
		('Honduras', 'HN', 'Choluteca', 'CHO'),
		('Honduras', 'HN', 'Colon', 'COL'),
		('Honduras', 'HN', 'Comayagua', 'COM'),
		('Honduras', 'HN', 'Copan', 'COP'),
		('Honduras', 'HN', 'El Paraiso', 'EPA'),
		('Honduras', 'HN', 'Francisco Morazan', 'FMO'),
		('Honduras', 'HN', 'Gracias a Dios', 'GAD'),
		('Honduras', 'HN', 'Intibuca', 'INT'),
		('Honduras', 'HN', 'Islas de la Bahia', 'IBA'),
		('Honduras', 'HN', 'La Paz', 'LPA'),
		('Honduras', 'HN', 'Lempira', 'LEM'),
		('Honduras', 'HN', 'Ocotepeque', 'OCO'),
		('Honduras', 'HN', 'Olancho', 'OLA'),
		('Honduras', 'HN', 'Santa Barbara', 'SBA'),
		('Honduras', 'HN', 'Valle', 'VAL'),
		('Honduras', 'HN', 'Yoro', 'YOR'),
		('Hungary', 'HU', 'Bacs-Kiskun Megye', 'BAC'),
		('Hungary', 'HU', 'Baranya Megye', 'BAR'),
		('Hungary', 'HU', 'Bekes Megye', 'BEK'),
		('Hungary', 'HU', 'Borsod-Abauj-Zemplen Megye', 'BOR'),
		('Hungary', 'HU', 'Budapest Fovaros', 'BUD'),
		('Hungary', 'HU', 'Csongrad Megye', 'CSO'),
		('Hungary', 'HU', 'Debrecen Megyei Varos', 'DEB'),
		('Hungary', 'HU', 'Fejer Megye', 'FEJ'),
		('Hungary', 'HU', 'Gyor Megyei Varos', 'GYO'),
		('Hungary', 'HU', 'Gyor-Moson-Sopron Megye', 'GYO'),
		('Hungary', 'HU', 'Hajdu-Bihar Megye', 'HAJ'),
		('Hungary', 'HU', 'Heves Megye', 'HEV'),
		('Hungary', 'HU', 'Jasz-Nagykun-Szolnok Megye', 'JAS'),
		('Hungary', 'HU', 'Komarom-Esztergom Megye', 'KOM'),
		('Hungary', 'HU', 'Miskolc Megyei Varos', 'MIS'),
		('Hungary', 'HU', 'Nograd Megye', 'NOG'),
		('Hungary', 'HU', 'Pecs Megyei Varos', 'PEC'),
		('Hungary', 'HU', 'Pest Megye', 'PES'),
		('Hungary', 'HU', 'Somogy Megye', 'SOM'),
		('Hungary', 'HU', 'Szabolcs-Szatmar-Bereg Megye', 'SZA'),
		('Hungary', 'HU', 'Szeged Megyei Varos', 'SZE'),
		('Hungary', 'HU', 'Tolna Megye', 'TOL'),
		('Hungary', 'HU', 'Vas Megye', 'VAS'),
		('Hungary', 'HU', 'Veszprem Megye', 'VES'),
		('Hungary', 'HU', 'Zala Megye', 'ZAL'),
		('Iceland', 'IS', 'Arnessysla', 'ARN'),
		('Iceland', 'IS', 'Austur-Bardhastrandarsysla', 'ABA'),
		('Iceland', 'IS', 'Austur-Hunavatnssysla', 'AHU'),
		('Iceland', 'IS', 'Austur-Skaftafellssysla', 'ASK'),
		('Iceland', 'IS', 'Borgarfjardharsysla', 'BOR'),
		('Iceland', 'IS', 'Dalasysla', 'DAL'),
		('Iceland', 'IS', 'Eyjafjardharsysla', 'EYJ'),
		('Iceland', 'IS', 'Gullbringusysla', 'GUL'),
		('Iceland', 'IS', 'Kjosarsysla', 'KJY'),
		('Iceland', 'IS', 'Myrasysla', 'MYR'),
		('Iceland', 'IS', 'Nordhur-Isafjardharsysla', 'NIS'),
		('Iceland', 'IS', 'Nordhur-Mulasysla', 'NMU'),
		('Iceland', 'IS', 'Nordhur-Thingeyjarsysla', 'NTY'),
		('Iceland', 'IS', 'Rangarvallasysla', 'RNG'),
		('Iceland', 'IS', 'Skagafjardharsysla', 'SKA'),
		('Iceland', 'IS', 'Snaefellsnessysla- og Hnappadalssysla', 'SHN'),
		('Iceland', 'IS', 'Strandasysla', 'STR'),
		('Iceland', 'IS', 'Sudhur-Mulasysla', 'SMU'),
		('Iceland', 'IS', 'Sudhur-Thingeijjar', 'STI'),
		('Iceland', 'IS', 'Vestur-Bardhastrandarsysla', 'VBA'),
		('Iceland', 'IS', 'Vestur-Hunavatnssysla', 'VHU'),
		('Iceland', 'IS', 'Vestur-Isafjardharsysla', 'VIS'),
		('Iceland', 'IS', 'Vestur-Skaftafellssysla', 'VSK'),
		('India', 'IN', 'Andaman & Nicobar Islands', 'ANI'),
		('India', 'IN', 'Andhra Pradesh', 'AND'),
		('India', 'IN', 'Arunachal Pradesh', 'ARU'),
		('India', 'IN', 'Assam', 'ASS'),
		('India', 'IN', 'Bihar', 'BIH'),
		('India', 'IN', 'Chandigarh', 'CHA'),
		('India', 'IN', 'Dadra & Nagar Haveli', 'DAD'),
		('India', 'IN', 'Delhi', 'DEL'),
		('India', 'IN', 'Goa', 'GOA'),
		('India', 'IN', 'Gujarat', 'GUJ'),
		('India', 'IN', 'Haryana', 'HAR'),
		('India', 'IN', 'Himachal Pradesh', 'HIM'),
		('India', 'IN', 'Jammu & Kashmir', 'JAM'),
		('India', 'IN', 'Jharkhand', 'JHA'),
		('India', 'IN', 'Karnataka', 'KAR'),
		('India', 'IN', 'Kerala', 'KER'),
		('India', 'IN', 'Lakshadweep', 'LAK'),
		('India', 'IN', 'Madhya Pradesh', 'MAD'),
		('India', 'IN', 'Maharashtra', 'MAH'),
		('India', 'IN', 'Manipur', 'MAN'),
		('India', 'IN', 'Meghalaya', 'MEG'),
		('India', 'IN', 'Mizoram', 'MIZ'),
		('India', 'IN', 'Nagaland', 'NAG'),
		('India', 'IN', 'Orissa', 'ORI'),
		('India', 'IN', 'Pondicherry', 'PON'),
		('India', 'IN', 'Punjab', 'PUN'),
		('India', 'IN', 'Rajasthan', 'RAJ'),
		('India', 'IN', 'Sikkim', 'SIK'),
		('India', 'IN', 'Tamil Nadu', 'TAM'),
		('India', 'IN', 'Tripura', 'TRI'),
		('India', 'IN', 'Uttar Pradesh', 'UTT'),
		('India', 'IN', 'Uttaranchal', 'UAR'),
		('India', 'IN', 'West Bengal', 'WES'),
		('Indonesia', 'ID', 'Aceh', 'ACE'),
		('Indonesia', 'ID', 'Bali', 'BAL'),
		('Indonesia', 'ID', 'Bengkulu', 'BEN'),
		('Indonesia', 'ID', 'Jambi', 'JAM'),
		('Indonesia', 'ID', 'Jawa Barat', 'JBA'),
		('Indonesia', 'ID', 'Jawa Tengah', 'JTE'),
		('Indonesia', 'ID', 'Jawa Timur', 'JTI'),
		('Indonesia', 'ID', 'Kalimantan Barat', 'BAR'),
		('Indonesia', 'ID', 'Kalimantan Selatan', 'SEL'),
		('Indonesia', 'ID', 'Kalimantan Tengah', 'TEN'),
		('Indonesia', 'ID', 'Kalimantan Timur', 'TIM'),
		('Indonesia', 'ID', 'Lampung', 'LAM'),
		('Indonesia', 'ID', 'Maluku', 'MAL'),
		('Indonesia', 'ID', 'Nusa Tenggara Barat', 'NTB'),
		('Indonesia', 'ID', 'Nusa Tenggara Timur', 'NTT'),
		('Indonesia', 'ID', 'Papua', 'PAP'),
		('Indonesia', 'ID', 'Riau', 'RIA'),
		('Indonesia', 'ID', 'Sulawesi Selatan', 'SSE'),
		('Indonesia', 'ID', 'Sulawesi Tengah', 'STE'),
		('Indonesia', 'ID', 'Sulawesi Tenggara', 'STG'),
		('Indonesia', 'ID', 'Sulawesi Utara', 'SLU'),
		('Indonesia', 'ID', 'Sumatera Barat', 'SBA'),
		('Indonesia', 'ID', 'Sumatera Utara', 'SUT'),
		('Indonesia', 'ID', 'Unknown', 'UNK'),
		('Indonesia', 'ID', 'Yogyakarta', 'YOG'),
		('Iran', 'IR', 'a Baluchestan', 'BAL'),
		('Iran', 'IR', 'ahall va Bakhtiari', 'BAK'),
		('Iran', 'IR', 'an-e Gharbi', 'GHA'),
		('Iran', 'IR', 'an-e Sharqi', 'SHA'),
		('Iran', 'IR', 'eh va Buyer Ahmad', 'KOH'),
		('Iran', 'IR', 'n', 'KOR'),
		('Iran', 'IR', 'n', 'KHU'),
		('Iran', 'IR', 'n', 'HOR'),
		('Iraq', 'IQ', 'Anbar', 'ANB'),
		('Iraq', 'IQ', 'Arbil', 'ARB'),
		('Iraq', 'IQ', 'Babil', 'BAB'),
		('Iraq', 'IQ', 'Baghdad', 'BAG'),
		('Iraq', 'IQ', 'Basrah', 'BAS'),
		('Iraq', 'IQ', 'Dahuk', 'DAH'),
		('Iraq', 'IQ', 'Dhi Qar', 'DHI'),
		('Iraq', 'IQ', 'Diyala', 'DIY'),
		('Iraq', 'IQ', 'Karbala''', 'KAR'),
		('Iraq', 'IQ', 'Maysan', 'MAY'),
		('Iraq', 'IQ', 'Muthanna', 'MUT'),
		('Iraq', 'IQ', 'Najaf', 'NAJ'),
		('Iraq', 'IQ', 'Ninawa', 'NIN'),
		('Iraq', 'IQ', 'Qadisiyah', 'QAD'),
		('Iraq', 'IQ', 'Salah ad Din', 'SAL'),
		('Iraq', 'IQ', 'Sulaymaniyah', 'SUL'),
		('Iraq', 'IQ', 'Ta''mim', 'TAM'),
		('Iraq', 'IQ', 'Wasit', 'WAS'),
		('Ireland', 'IE', 'Carlow', 'CAR'),
		('Ireland', 'IE', 'Cavan', 'CAV'),
		('Ireland', 'IE', 'Clare', 'CLA'),
		('Ireland', 'IE', 'Cork', 'COR'),
		('Ireland', 'IE', 'Donegal', 'DON'),
		('Ireland', 'IE', 'Dublin', 'DUB'),
		('Ireland', 'IE', 'Galway', 'GAL'),
		('Ireland', 'IE', 'Kerry', 'KRY'),
		('Ireland', 'IE', 'Kildare', 'KID'),
		('Ireland', 'IE', 'Kilkenny', 'KIK'),
		('Ireland', 'IE', 'Laois', 'LAO'),
		('Ireland', 'IE', 'Leitrim', 'LEI'),
		('Ireland', 'IE', 'Limerick', 'LIM'),
		('Ireland', 'IE', 'Longford', 'LON'),
		('Ireland', 'IE', 'Louth', 'LOU'),
		('Ireland', 'IE', 'Mayo', 'MAY'),
		('Ireland', 'IE', 'Meath', 'MEA'),
		('Ireland', 'IE', 'Monaghan', 'MON'),
		('Ireland', 'IE', 'Offaly', 'OFF'),
		('Ireland', 'IE', 'Roscommon', 'ROS'),
		('Ireland', 'IE', 'Sligo', 'SLI'),
		('Ireland', 'IE', 'Tipperary', 'TIP'),
		('Ireland', 'IE', 'Unknown', 'UNK'),
		('Ireland', 'IE', 'Waterford', 'WAT'),
		('Ireland', 'IE', 'Westmeath', 'WES'),
		('Ireland', 'IE', 'Wexford', 'WEX'),
		('Ireland', 'IE', 'Wicklow', 'WIC'),
		('Isle of Man', 'IM', 'Isle of Man', 'IOM'),
		('Israel', 'IL', 'Central District', 'CEN'),
		('Israel', 'IL', 'Haifa District', 'HAF'),
		('Israel', 'IL', 'Jerusalem District', 'JER'),
		('Israel', 'IL', 'Northern District', 'HAF'),
		('Israel', 'IL', 'Southern District', 'MEH'),
		('Israel', 'IL', 'Tel Aviv District', 'TEL'),
		('Italy', 'IT', 'Abruzzi', 'ABR'),
		('Italy', 'IT', 'Basilicata', 'BAS'),
		('Italy', 'IT', 'Calabria', 'CAL'),
		('Italy', 'IT', 'Campania', 'CAM'),
		('Italy', 'IT', 'Emilia-Romagna', 'EMI'),
		('Italy', 'IT', 'Friuli-Venezia Giulia', 'FRI'),
		('Italy', 'IT', 'Lazio', 'LAZ'),
		('Italy', 'IT', 'Liguria', 'LIG'),
		('Italy', 'IT', 'Lombardia', 'LOM'),
		('Italy', 'IT', 'Marche', 'MAR'),
		('Italy', 'IT', 'Molise', 'MOL'),
		('Italy', 'IT', 'Piemonte', 'PIE'),
		('Italy', 'IT', 'Puglia', 'PUG'),
		('Italy', 'IT', 'Sardegna', 'SAR'),
		('Italy', 'IT', 'Sicilia', 'SIC'),
		('Italy', 'IT', 'Toscana', 'TOS'),
		('Italy', 'IT', 'Trentino-Alto Adige', 'TRE'),
		('Italy', 'IT', 'Umbria', 'UMB'),
		('Italy', 'IT', 'Valle d''Aosta', 'VDA'),
		('Italy', 'IT', 'Veneto', 'VEN'),
		('Jamaica', 'JM', 'Clarendon', 'CLA'),
		('Jamaica', 'JM', 'Hanover', 'HAN'),
		('Jamaica', 'JM', 'Kingston', 'KIN'),
		('Jamaica', 'JM', 'Manchester', 'MAN'),
		('Jamaica', 'JM', 'Portland', 'POR'),
		('Jamaica', 'JM', 'Saint Andrews', 'AND'),
		('Jamaica', 'JM', 'Saint Ann', 'ANN'),
		('Jamaica', 'JM', 'Saint Catherine', 'CAT'),
		('Jamaica', 'JM', 'Saint Elizabeth', 'ELI'),
		('Jamaica', 'JM', 'Saint James', 'JAM'),
		('Jamaica', 'JM', 'Saint Mary', 'MAR'),
		('Jamaica', 'JM', 'Saint Thomas', 'THO'),
		('Jamaica', 'JM', 'Trelawny', 'TRE'),
		('Jamaica', 'JM', 'Westmoreland', 'WML'),
		('Japan', 'JP', 'Aichi', 'AIC'),
		('Japan', 'JP', 'Akita', 'AKI'),
		('Japan', 'JP', 'Aomori', 'AOM'),
		('Japan', 'JP', 'Chiba', 'CHI'),
		('Japan', 'JP', 'Ehime', 'EHI'),
		('Japan', 'JP', 'Fukui', 'FUI'),
		('Japan', 'JP', 'Fukuoka', 'FUA'),
		('Japan', 'JP', 'Fukushima', 'FUM'),
		('Japan', 'JP', 'Gifu', 'GIF'),
		('Japan', 'JP', 'Gumma', 'GUM'),
		('Japan', 'JP', 'Hiroshima', 'HIR'),
		('Japan', 'JP', 'Hokkaido', 'HOK'),
		('Japan', 'JP', 'Hyogo', 'HYO'),
		('Japan', 'JP', 'Ibaraki', 'IBA'),
		('Japan', 'JP', 'Ishikawa', 'ISH'),
		('Japan', 'JP', 'Iwate', 'IWA'),
		('Japan', 'JP', 'Kagawa', 'KAG'),
		('Japan', 'JP', 'Kagoshima', 'KAM'),
		('Japan', 'JP', 'Kanagawa', 'KAN'),
		('Japan', 'JP', 'Kochi', 'KOC'),
		('Japan', 'JP', 'Kumamoto', 'KUM'),
		('Japan', 'JP', 'Kyoto', 'KYO'),
		('Japan', 'JP', 'Mie', 'MIE'),
		('Japan', 'JP', 'Miyagi', 'MIG'),
		('Japan', 'JP', 'Miyazaki', 'MIZ'),
		('Japan', 'JP', 'Nagano', 'NAG'),
		('Japan', 'JP', 'Nagasaki', 'NAK'),
		('Japan', 'JP', 'Nara', 'NAR'),
		('Japan', 'JP', 'Niigata', 'NII'),
		('Japan', 'JP', 'Oita', 'OIT'),
		('Japan', 'JP', 'Okayama', 'OKA'),
		('Japan', 'JP', 'Okinawa', 'OKI'),
		('Japan', 'JP', 'Osaka', 'OSA'),
		('Japan', 'JP', 'Saga', 'SAG'),
		('Japan', 'JP', 'Saitama', 'SAI'),
		('Japan', 'JP', 'Shiga', 'SHG'),
		('Japan', 'JP', 'Shimane', 'SHM'),
		('Japan', 'JP', 'Shizuoka', 'SHZ'),
		('Japan', 'JP', 'Tochigi', 'TOC'),
		('Japan', 'JP', 'Tokushima', 'TOK'),
		('Japan', 'JP', 'Tokyo', 'TOY'),
		('Japan', 'JP', 'Tottori', 'TOT'),
		('Japan', 'JP', 'Toyama', 'TYM'),
		('Japan', 'JP', 'Wakayama', 'WAK'),
		('Japan', 'JP', 'Yamagata', 'YMT'),
		('Japan', 'JP', 'Yamaguchi', 'YMG'),
		('Japan', 'JP', 'Yamanashi', 'YMN'),
		('Jersey', 'JE', 'Jersey', 'JER'),
		('Jordan', 'JO', 'Jordan', 'JOR'),
		('Kazakhstan', 'KZ', 'Kazakhstan', 'KAZ'),
		('Kenya', 'KE', 'Central', 'CEN'),
		('Kenya', 'KE', 'Coast', 'CST'),
		('Kenya', 'KE', 'Eastern', 'EST'),
		('Kenya', 'KE', 'Nairobi', 'NAI'),
		('Kenya', 'KE', 'North Eastern', 'NEA'),
		('Kenya', 'KE', 'Nyanza', 'NYA'),
		('Kenya', 'KE', 'Rift Valley', 'RVY'),
		('Kenya', 'KE', 'Western', 'WST'),
		('Kiribati', 'KI', 'Kiribati', 'KIR'),
		('Kuwait', 'KW', 'Al-Ahmadi', 'AHM'),
		('Kuwait', 'KW', 'Al-Farwaniyah', 'FAR'),
		('Kuwait', 'KW', 'Al-Kuwayt', 'KUW'),
		('Kuwait', 'KW', 'Bubiyan & Warbah', 'BUB'),
		('Kuwait', 'KW', 'Hawalli', 'HAW'),
		('Kyrgyzstan', 'KG', 'Kyrgyzstan', 'KYR'),
		('Laos', 'LA', 'Laos', 'LAO'),
		('Latvia', 'LV', 'Latvia', 'LAT'),
		('Lebanon', 'LB', 'Lebanon', 'LEB'),
		('Lesotho', 'LS', 'Lesotho', 'LES'),
		('Liberia', 'LR', 'Liberia', 'LIB'),
		('Libya', 'LY', 'Libya', 'LIB'),
		('Liechtenstein', 'LI', 'Liechtenstein', 'LIE'),
		('Lithuania', 'LT', 'Lithuania', 'LIT'),
		('Luxembourg', 'LU', 'Luxembourg', 'LUX'),
		('Macau', 'MA', 'Macau', 'MAC'),
		('Macedonia', 'MK', 'Macedonia', 'MAC'),
		('Madagascar', 'MG', 'Antananarivo', 'ANT'),
		('Madagascar', 'MG', 'Antsiranana', 'ASI'),
		('Madagascar', 'MG', 'Fianarantsoa', 'FIA'),
		('Madagascar', 'MG', 'Mahajanga', 'MAH'),
		('Madagascar', 'MG', 'Toamasina', 'TOA'),
		('Madagascar', 'MG', 'Toliary', 'TOL'),
		('Malawi', 'MW', 'Malawi', 'MAL'),
		('Malaysia', 'MY', 'Johor', 'JOH'),
		('Malaysia', 'MY', 'Kedah', 'KED'),
		('Malaysia', 'MY', 'Kelantan', 'KEL'),
		('Malaysia', 'MY', 'Melaka', 'MEL'),
		('Malaysia', 'MY', 'Pahang', 'PAH'),
		('Malaysia', 'MY', 'Perak', 'PER'),
		('Malaysia', 'MY', 'Perlis', 'PES'),
		('Malaysia', 'MY', 'Pulau Pinang', 'PIN'),
		('Malaysia', 'MY', 'Sabah', 'SAB'),
		('Malaysia', 'MY', 'Sarawak', 'SAR'),
		('Malaysia', 'MY', 'Selangor', 'SEL'),
		('Malaysia', 'MY', 'Sembilan', 'SEM'),
		('Malaysia', 'MY', 'Terengganu', 'TER'),
		('Malaysia', 'MY', 'Unknown', 'UNK'),
		('Malaysia', 'MY', 'Wilayah Persekutuan', 'WIL'),
		('Maldives', 'MV', 'Maldives', 'MAL'),
		('Mali', 'ML', 'Mali', 'MAL'),
		('Malta', 'MT', 'Malta', 'MAL'),
		('Marshall Islands', 'MH', 'Marshall Islands', 'MRI'),
		('Martinique', 'MB', 'Martinique', 'MAR'),
		('Mauritania', 'MR', 'Mauritania', 'MAU'),
		('Mauritius', 'MU', 'Mauritius', 'MAU'),
		('Mayotte', 'YT', 'Mayotte', 'MAY'),
		('Mexico', 'MX', 'Aguascalientes', 'AGS'),
		('Mexico', 'MX', 'Baja California', 'BCN'),
		('Mexico', 'MX', 'Baja California Sur', 'BCS'),
		('Mexico', 'MX', 'Campeche', 'CAM'),
		('Mexico', 'MX', 'Chiapas', 'CHS'),
		('Mexico', 'MX', 'Chihuahua', 'CHI'),
		('Mexico', 'MX', 'Coahuila', 'COA'),
		('Mexico', 'MX', 'Colima', 'COL'),
		('Mexico', 'MX', 'Distrito Federal', 'DF'),
		('Mexico', 'MX', 'Durango', 'DGO'),
		('Mexico', 'MX', 'Guanajuato', 'GTO'),
		('Mexico', 'MX', 'Guerrero', 'GRO'),
		('Mexico', 'MX', 'Hidalgo', 'HGO'),
		('Mexico', 'MX', 'Jalisco', 'JAL'),
		('Mexico', 'MX', 'Mexico', 'MEX'),
		('Mexico', 'MX', 'Michoacan de Ocampo', 'MIC'),
		('Mexico', 'MX', 'Morelos', 'MOR'),
		('Mexico', 'MX', 'Nayarit', 'NAY'),
		('Mexico', 'MX', 'Nuevo Leon', 'NLN'),
		('Mexico', 'MX', 'Oaxaca', 'OAX'),
		('Mexico', 'MX', 'Puebla', 'PUE'),
		('Mexico', 'MX', 'Queretaro de Arteaga', 'QRO'),
		('Mexico', 'MX', 'Quintana Roo', 'QTR'),
		('Mexico', 'MX', 'San Luis Potosi', 'SLP'),
		('Mexico', 'MX', 'Sinaloa', 'SIN'),
		('Mexico', 'MX', 'Sonora', 'SON'),
		('Mexico', 'MX', 'Tabasco', 'TAB'),
		('Mexico', 'MX', 'Tamaulipas', 'TAM'),
		('Mexico', 'MX', 'Tlaxcala', 'TLA'),
		('Mexico', 'MX', 'Veracruz-Llave', 'VER'),
		('Mexico', 'MX', 'Yucatan', 'YUC'),
		('Mexico', 'MX', 'Zacatecas', 'ZAC'),
		('Micronesia', 'FM', 'Micronesia', 'MIC'),
		('Moldova', 'MD', 'Moldova', 'MOL'),
		('Monaco', 'MC', 'Monaco', 'MON'),
		('Mongolia', 'MN', 'Mongolia', 'MNG'),
		('Montserrat', 'MS', 'Montserrat', 'MON'),
		('Morocco', 'MA', 'Chaouia-Ouardigha', 'CHO'),
		('Morocco', 'MA', 'Doukkala-Abda', 'DOA'),
		('Morocco', 'MA', 'Fes-Boulemane', 'FEB'),
		('Morocco', 'MA', 'Gharb-Chrarda-Beni Hsen', 'GCB'),
		('Morocco', 'MA', 'Grand Casablanca', 'CAS'),
		('Morocco', 'MA', 'Marrakech-Tensift-El Haouz', 'MTH'),
		('Morocco', 'MA', 'Meknes-Tafilalt', 'MET'),
		('Morocco', 'MA', 'Rabat-Sale-Zemmour-Zaer', 'RSZ'),
		('Morocco', 'MA', 'Sous-Massa-Draa', 'SMD'),
		('Morocco', 'MA', 'Tanger-Tetouan', 'TAT'),
		('Morocco', 'MA', 'Taza-Al Hoceima-Taounate', 'THT'),
		('Mozambique', 'MZ', 'Mozambique', 'MOZ'),
		('Myanmar', 'MM', 'Ayeyarwady', 'AYE'),
		('Myanmar', 'MM', 'Bago', 'BAG'),
		('Myanmar', 'MM', 'Chin', 'CHI'),
		('Myanmar', 'MM', 'Kachin', 'KAC'),
		('Myanmar', 'MM', 'Kayah', 'KAH'),
		('Myanmar', 'MM', 'Kayin', 'KAN'),
		('Myanmar', 'MM', 'Magway', 'MAG'),
		('Myanmar', 'MM', 'Mandalay', 'MAN'),
		('Myanmar', 'MM', 'Mon', 'MON'),
		('Myanmar', 'MM', 'Rakhine', 'RAK'),
		('Myanmar', 'MM', 'Sagaing', 'SAG'),
		('Myanmar', 'MM', 'Shan', 'SHA'),
		('Myanmar', 'MM', 'Tanintharyi', 'TAN'),
		('Myanmar', 'MM', 'Unknown', 'UNK'),
		('Namibia', 'NA', 'Namibia', 'NAM'),
		('Nauru', 'NR', 'Nauru', 'NAU'),
		('Nepal', 'NP', 'Nepal', 'NEP'),
		('Netherlands', 'NL', 'Drenthe', 'DRE'),
		('Netherlands', 'NL', 'Flevoland', 'FLE'),
		('Netherlands', 'NL', 'Friesland', 'FRI'),
		('Netherlands', 'NL', 'Gelderland', 'GEL'),
		('Netherlands', 'NL', 'Groningen', 'GRO'),
		('Netherlands', 'NL', 'Limburg', 'LIM'),
		('Netherlands', 'NL', 'Noord-Brabant', 'NBR'),
		('Netherlands', 'NL', 'Noord-Holland', 'NHL'),
		('Netherlands', 'NL', 'Overijssel', 'OVE'),
		('Netherlands', 'NL', 'Utrecht', 'UTR'),
		('Netherlands', 'NL', 'Zeeland', 'ZEE'),
		('Netherlands', 'NL', 'Zuid-Holland', 'ZHO'),
		('Netherlands Antilles', 'AN', 'Netherlands Antilles', 'NTA'),
		('New Caledonia', 'NC', 'New Caledonia', 'NWC'),
		('New Zealand', 'NZ', 'Chatham Islands', 'CHI'),
		('New Zealand', 'NZ', 'North Island', 'NIS'),
		('New Zealand', 'NZ', 'South Island', 'SIS'),
		('New Zealand', 'NZ', 'Stewart Island', 'STI'),
		('Nicaragua', 'NI', 'Atlantico Norte', 'ATN'),
		('Nicaragua', 'NI', 'Atlantico Sur', 'ATS'),
		('Nicaragua', 'NI', 'Boaco', 'BOA'),
		('Nicaragua', 'NI', 'Carazo', 'CAR'),
		('Nicaragua', 'NI', 'Chinandega', 'CHI'),
		('Nicaragua', 'NI', 'Chontales', 'CHO'),
		('Nicaragua', 'NI', 'Esteli', 'EST'),
		('Nicaragua', 'NI', 'Granada', 'GRA'),
		('Nicaragua', 'NI', 'Jinotega', 'JIN'),
		('Nicaragua', 'NI', 'Leon', 'LEO'),
		('Nicaragua', 'NI', 'Madriz', 'MAD'),
		('Nicaragua', 'NI', 'Managua', 'MAN'),
		('Nicaragua', 'NI', 'Masaya', 'MAS'),
		('Nicaragua', 'NI', 'Matagalpa', 'MAT'),
		('Nicaragua', 'NI', 'Nueva Segovia', 'NSE'),
		('Nicaragua', 'NI', 'Rio San Juan', 'RSJ'),
		('Nicaragua', 'NI', 'Rivas', 'RIV'),
		('Niger', 'NE', 'Agadez', 'AGA'),
		('Niger', 'NE', 'Diffa', 'DIF'),
		('Niger', 'NE', 'Dosso', 'DOS'),
		('Niger', 'NE', 'Maradi', 'MAR'),
		('Niger', 'NE', 'Niamey', 'NIA'),
		('Niger', 'NE', 'Tahoua', 'TAH'),
		('Niger', 'NE', 'Tillaberi', 'TIL'),
		('Niger', 'NE', 'Zinder', 'ZIN'),
		('Nigeria', 'NG', 'Abuja', 'FC'),
		('Nigeria', 'NG', 'Adamawa', 'AD'),
		('Nigeria', 'NG', 'Bauchi', 'BA'),
		('Nigeria', 'NG', 'Benue', 'BE'),
		('Nigeria', 'NG', 'Borno', 'BO'),
		('Nigeria', 'NG', 'Delta', 'DE'),
		('Nigeria', 'NG', 'Gombe', 'GO'),
		('Nigeria', 'NG', 'Gongola', 'UNK'),
		('Nigeria', 'NG', 'Jigawa', 'JI'),
		('Nigeria', 'NG', 'Kaduna', 'KD'),
		('Nigeria', 'NG', 'Kano', 'KN'),
		('Nigeria', 'NG', 'Katsina', 'KT'),
		('Nigeria', 'NG', 'Kwara', 'KW'),
		('Nigeria', 'NG', 'Lagos', 'LA'),
		('Nigeria', 'NG', 'Nassarawa', 'NA'),
		('Nigeria', 'NG', 'Niger', 'NI'),
		('Nigeria', 'NG', 'Ogun', 'OG'),
		('Nigeria', 'NG', 'Oyo', 'OY'),
		('Nigeria', 'NG', 'Plateau', 'PL'),
		('Nigeria', 'NG', 'Sokoto', 'SO'),
		('Nigeria', 'NG', 'Unknown', 'UNK'),
		('Nigeria', 'NG', 'Zamfara', 'ZA'),
		('Niue', 'NU', 'Niue', 'NIU'),
		('Norfolk Island', 'NF', 'Norfolk Island', 'NFI'),
		('North Korea', 'KP', 'Chagang-do', 'CHA'),
		('North Korea', 'KP', 'Hamgyong-bukto', 'HYP'),
		('North Korea', 'KP', 'Hamgyong-namdo', 'HYN'),
		('North Korea', 'KP', 'Hwanghae-bukto', 'HWP'),
		('North Korea', 'KP', 'Hwanghae-namdo', 'HWN'),
		('North Korea', 'KP', 'Kaesong-si', 'KAE'),
		('North Korea', 'KP', 'Kangwon-do', 'KAN'),
		('North Korea', 'KP', 'Najin Sonbong-si', 'HAM'),
		('North Korea', 'KP', 'Namp''o-si', 'NAM'),
		('North Korea', 'KP', 'P''yongan-bukto', 'PYP'),
		('North Korea', 'KP', 'P''yongan-namdo', 'PYN'),
		('North Korea', 'KP', 'P''yongyang-si', 'PYY'),
		('North Korea', 'KP', 'Yanggang-do', 'YAN'),
		('Norway', 'NO', 'Akershus', 'AKE'),
		('Norway', 'NO', 'Aust-Agder', 'AAG'),
		('Norway', 'NO', 'Buskerud', 'BUS'),
		('Norway', 'NO', 'Finnmark', 'FIN'),
		('Norway', 'NO', 'Hedmark', 'HED'),
		('Norway', 'NO', 'Hordaland', 'HOR'),
		('Norway', 'NO', 'More og Romsdal', 'MOR'),
		('Norway', 'NO', 'Nord-Trondelag', 'NTR'),
		('Norway', 'NO', 'Nordland', 'NOR'),
		('Norway', 'NO', 'Oppland', 'OPP'),
		('Norway', 'NO', 'Oslo', 'OSL'),
		('Norway', 'NO', 'Ostfold', 'OFO'),
		('Norway', 'NO', 'Rogaland', 'ROG'),
		('Norway', 'NO', 'Sogn og Fjordane', 'SOF'),
		('Norway', 'NO', 'Sor-Trondelag', 'STR'),
		('Norway', 'NO', 'Telemark', 'TEL'),
		('Norway', 'NO', 'Troms', 'TRO'),
		('Norway', 'NO', 'Vest-Agder', 'VAG'),
		('Norway', 'NO', 'Vestfold', 'VFO'),
		('Oman', 'OM', 'Oman', 'OMN'),
		('Pakistan', 'PK', 'Balochistan', 'BAL'),
		('Pakistan', 'PK', 'Federally Administered Tribal Areas', 'TA'),
		('Pakistan', 'PK', 'Islamabad Capital Territory', 'FCA'),
		('Pakistan', 'PK', 'North-West Frontier Province', 'NWF'),
		('Pakistan', 'PK', 'Punjab', 'PUN'),
		('Pakistan', 'PK', 'Sind', 'SIN'),
		('Palau', 'PW', 'Palau', 'PAL'),
		('Panama', 'PA', 'Bocas del Toro', 'BDT'),
		('Panama', 'PA', 'Chiriqui', 'CHI'),
		('Panama', 'PA', 'Colon', 'COL'),
		('Panama', 'PA', 'Darien', 'DAR'),
		('Panama', 'PA', 'Herrera', 'HER'),
		('Panama', 'PA', 'Kuna Yala', 'KYA'),
		('Panama', 'PA', 'Los Santos', 'LSA'),
		('Panama', 'PA', 'Panama', 'PAN'),
		('Panama', 'PA', 'Veraguas', 'VER'),
		('Papua New Guinea', 'PG', 'Papua New Guinea', 'PNG'),
		('Paraguay', 'PY', 'Alto Paraguay', 'APG'),
		('Paraguay', 'PY', 'Alto Parana', 'APR'),
		('Paraguay', 'PY', 'Amambay', 'AMA'),
		('Paraguay', 'PY', 'Boqueron', 'BOQ'),
		('Paraguay', 'PY', 'Caaguazu', 'CGZ'),
		('Paraguay', 'PY', 'Caazapa', 'CZP'),
		('Paraguay', 'PY', 'Canindeyu', 'CAN'),
		('Paraguay', 'PY', 'Central', 'CEN'),
		('Paraguay', 'PY', 'Concepcion', 'CON'),
		('Paraguay', 'PY', 'Cordillera', 'COR'),
		('Paraguay', 'PY', 'Guaira', 'GUA'),
		('Paraguay', 'PY', 'Itapua', 'ITA'),
		('Paraguay', 'PY', 'Misiones', 'MIS'),
		('Paraguay', 'PY', 'Neembucu', 'NEE'),
		('Paraguay', 'PY', 'Paraguari', 'PAR'),
		('Paraguay', 'PY', 'Presidente Hayes', 'PHA'),
		('Paraguay', 'PY', 'San Pedro', 'SPE'),
		('Peru', 'PE', 'Amazonas', 'AMA'),
		('Peru', 'PE', 'Ancash', 'ANC'),
		('Peru', 'PE', 'Apurimac', 'APU'),
		('Peru', 'PE', 'Arequipa', 'ARE'),
		('Peru', 'PE', 'Ayacucho', 'AYA'),
		('Peru', 'PE', 'Cajamarca', 'CAJ'),
		('Peru', 'PE', 'Callao', 'CAL'),
		('Peru', 'PE', 'Cusco', 'CUS'),
		('Peru', 'PE', 'Huancavelica', 'HUA'),
		('Peru', 'PE', 'Huanuco', 'HUO'),
		('Peru', 'PE', 'Ica', 'ICA'),
		('Peru', 'PE', 'Junin', 'JUN'),
		('Peru', 'PE', 'La Libertad', 'LIB'),
		('Peru', 'PE', 'Lambayeque', 'LAM'),
		('Peru', 'PE', 'Lima', 'LIM'),
		('Peru', 'PE', 'Loreto', 'LOR'),
		('Peru', 'PE', 'Madre de Dios', 'MAD'),
		('Peru', 'PE', 'Moquegua', 'MOQ'),
		('Peru', 'PE', 'Pasco', 'PAS'),
		('Peru', 'PE', 'Piura', 'PIU'),
		('Peru', 'PE', 'Puno', 'PUN'),
		('Peru', 'PE', 'San Martin', 'MAR'),
		('Peru', 'PE', 'Tacna', 'TAC'),
		('Peru', 'PE', 'Tumbes', 'TUM'),
		('Peru', 'PE', 'Ucayali', 'UCA'),
		('Philippines', 'PH', 'Abra', '01'),
		('Philippines', 'PH', 'Agusan del Norte', '02'),
		('Philippines', 'PH', 'Agusan del Sur', '03'),
		('Philippines', 'PH', 'Aklan', '04');
		");

		$this->query("
		INSERT INTO `States` (`Country`, `CountryCode`, `State`, `StateCode`) VALUES
		('Philippines', 'PH', 'Albay', '05'),
		('Philippines', 'PH', 'Angeles City', 'A1'),
		('Philippines', 'PH', 'Antique', '06'),
		('Philippines', 'PH', 'Aurora', 'G8'),
		('Philippines', 'PH', 'Bacolod City', 'A2'),
		('Philippines', 'PH', 'Bago City', 'A3'),
		('Philippines', 'PH', 'Baguio City', 'A4'),
		('Philippines', 'PH', 'Basilan', '22'),
		('Philippines', 'PH', 'Bataan', '07'),
		('Philippines', 'PH', 'Batanes', '08'),
		('Philippines', 'PH', 'Batangas', '09'),
		('Philippines', 'PH', 'Batangas City', 'A7'),
		('Philippines', 'PH', 'Benguet', '10'),
		('Philippines', 'PH', 'Bohol', '11'),
		('Philippines', 'PH', 'Bukidnon', '12'),
		('Philippines', 'PH', 'Bulacan', '13'),
		('Philippines', 'PH', 'Butuan City', 'A8'),
		('Philippines', 'PH', 'Cabanatuan City', 'A9'),
		('Philippines', 'PH', 'Cadiz City', 'B1'),
		('Philippines', 'PH', 'Cagayan', '14'),
		('Philippines', 'PH', 'Cagayan de Oro City', 'B2'),
		('Philippines', 'PH', 'Calbayog City', 'B3'),
		('Philippines', 'PH', 'Caloocan City', 'B4'),
		('Philippines', 'PH', 'Camarines Norte', '15'),
		('Philippines', 'PH', 'Camarines Sur', '16'),
		('Philippines', 'PH', 'Camiguin', '17'),
		('Philippines', 'PH', 'Canlaon City', 'B5'),
		('Philippines', 'PH', 'Capiz', '18'),
		('Philippines', 'PH', 'Catanduanes', '19'),
		('Philippines', 'PH', 'Cavite', '20'),
		('Philippines', 'PH', 'Cavite City', 'B6'),
		('Philippines', 'PH', 'Cebu', '21'),
		('Philippines', 'PH', 'Cebu City', 'B7'),
		('Philippines', 'PH', 'City of Manila', 'D9'),
		('Philippines', 'PH', 'Cotabato City', 'B8'),
		('Philippines', 'PH', 'Dagupan City', 'B9'),
		('Philippines', 'PH', 'Danao City', 'C1'),
		('Philippines', 'PH', 'Dapitan City', 'C2'),
		('Philippines', 'PH', 'Davao City', 'C3'),
		('Philippines', 'PH', 'Davao del Norte', '24'),
		('Philippines', 'PH', 'Davao del Sur', '25'),
		('Philippines', 'PH', 'Davao Oriental', '26'),
		('Philippines', 'PH', 'Dipolog City', 'C4'),
		('Philippines', 'PH', 'Dumaguete City', 'C5'),
		('Philippines', 'PH', 'Eastern Samar', '23'),
		('Philippines', 'PH', 'General Santos City', 'C6'),
		('Philippines', 'PH', 'Gingoog City', 'C7'),
		('Philippines', 'PH', 'Ifugao', '27'),
		('Philippines', 'PH', 'Iligan City', 'C8'),
		('Philippines', 'PH', 'Ilocos Norte', '28'),
		('Philippines', 'PH', 'Ilocos Sur', '29'),
		('Philippines', 'PH', 'Iloilo', '30'),
		('Philippines', 'PH', 'Iloilo City', 'C9'),
		('Philippines', 'PH', 'Iriga City', 'D1'),
		('Philippines', 'PH', 'Isabela', '31'),
		('Philippines', 'PH', 'Kalinga-Apayao', '32'),
		('Philippines', 'PH', 'La Carlota City', 'D2'),
		('Philippines', 'PH', 'La Union', '36'),
		('Philippines', 'PH', 'Laguna', '33'),
		('Philippines', 'PH', 'Lanao del Norte', '34'),
		('Philippines', 'PH', 'Lanao del Sur', '35'),
		('Philippines', 'PH', 'Laoag City', 'D3'),
		('Philippines', 'PH', 'Lapu-Lapu City', 'D4'),
		('Philippines', 'PH', 'Legaspi City', 'D5'),
		('Philippines', 'PH', 'Leyte', '37'),
		('Philippines', 'PH', 'Lipa City', 'D6'),
		('Philippines', 'PH', 'Lucena City', 'D7'),
		('Philippines', 'PH', 'Maguindanao', '56'),
		('Philippines', 'PH', 'Mandaue City', 'D8'),
		('Philippines', 'PH', 'Marawi City', 'E1'),
		('Philippines', 'PH', 'Marinduque', '38'),
		('Philippines', 'PH', 'Masbate', '39'),
		('Philippines', 'PH', 'Mindoro Occidental', '40'),
		('Philippines', 'PH', 'Mindoro Oriental', '41'),
		('Philippines', 'PH', 'Misamis Occidental', '42'),
		('Philippines', 'PH', 'Misamis Oriental', '43'),
		('Philippines', 'PH', 'Mountain Province', '44'),
		('Philippines', 'PH', 'Naga City', 'E2'),
		('Philippines', 'PH', 'Negros Occidental', 'H3'),
		('Philippines', 'PH', 'Negros Oriental', '46'),
		('Philippines', 'PH', 'North Cotabato', '57'),
		('Philippines', 'PH', 'Northern Samar', '67'),
		('Philippines', 'PH', 'Nueva Ecija', '47'),
		('Philippines', 'PH', 'Nueva Vizcaya', '48'),
		('Philippines', 'PH', 'Olongapo City', 'E3'),
		('Philippines', 'PH', 'Ormoc City', 'E4'),
		('Philippines', 'PH', 'Oroquieta City', 'E5'),
		('Philippines', 'PH', 'Ozamis City', 'E6'),
		('Philippines', 'PH', 'Pagadian City', 'E7'),
		('Philippines', 'PH', 'Palawan', '49'),
		('Philippines', 'PH', 'Palayan City', 'E8'),
		('Philippines', 'PH', 'Pampanga', '50'),
		('Philippines', 'PH', 'Pangasinan', '51'),
		('Philippines', 'PH', 'Pasay City', 'E9'),
		('Philippines', 'PH', 'Puerto Princesa City', 'F1'),
		('Philippines', 'PH', 'Quezon', 'H2'),
		('Philippines', 'PH', 'Quezon City', 'F2'),
		('Philippines', 'PH', 'Quirino', '68'),
		('Philippines', 'PH', 'Rizal', '53'),
		('Philippines', 'PH', 'Romblon', '54'),
		('Philippines', 'PH', 'Roxas City', 'F3'),
		('Philippines', 'PH', 'Samar', '55'),
		('Philippines', 'PH', 'San Carlos City', 'F4'),
		('Philippines', 'PH', 'San Pablo City', 'F7'),
		('Philippines', 'PH', 'Silay City', 'F8'),
		('Philippines', 'PH', 'Siquijor', '69'),
		('Philippines', 'PH', 'Sorsogon', '58'),
		('Philippines', 'PH', 'South Cotabato', '70'),
		('Philippines', 'PH', 'Southern Leyte', '59'),
		('Philippines', 'PH', 'Sultan Kudarat', '71'),
		('Philippines', 'PH', 'Sulu', '60'),
		('Philippines', 'PH', 'Surigao City', 'F9'),
		('Philippines', 'PH', 'Surigao del Norte', '61'),
		('Philippines', 'PH', 'Surigao del Sur', '62'),
		('Philippines', 'PH', 'Tacloban City', 'G1'),
		('Philippines', 'PH', 'Tagaytay City', 'G2'),
		('Philippines', 'PH', 'Tagbilaran City', 'G3'),
		('Philippines', 'PH', 'Tangub City', 'G4'),
		('Philippines', 'PH', 'Tarlac', '63'),
		('Philippines', 'PH', 'Tawi-Tawi', '72'),
		('Philippines', 'PH', 'Toledo City', 'G5'),
		('Philippines', 'PH', 'Trece Martires City', 'G6'),
		('Philippines', 'PH', 'Zambales', '64'),
		('Philippines', 'PH', 'Zamboanga City', 'G7'),
		('Philippines', 'PH', 'Zamboanga del Norte', '65'),
		('Philippines', 'PH', 'Zamboanga del Sur', '66'),
		('Pitcairn Islands', 'PN', 'Pitcairn Islands', 'PIT'),
		('Poland', 'PL', 'Dolnoslaskie', 'DOL'),
		('Poland', 'PL', 'Kujawsko-Pomorskie', 'KUJ'),
		('Poland', 'PL', 'Lodzkie', 'LOD'),
		('Poland', 'PL', 'Lubelskie', 'LUB'),
		('Poland', 'PL', 'Lubuskie', 'LBU'),
		('Poland', 'PL', 'Malopolskie', 'MAL'),
		('Poland', 'PL', 'Mazowieckie', 'MAZ'),
		('Poland', 'PL', 'Opolskie', 'OPO'),
		('Poland', 'PL', 'Podkarpackie', 'PDK'),
		('Poland', 'PL', 'Podlaskie', 'POD'),
		('Poland', 'PL', 'Pomorskie', 'POM'),
		('Poland', 'PL', 'Slaskie', 'SLA'),
		('Poland', 'PL', 'Swietokrzyskie', 'SWI'),
		('Poland', 'PL', 'Warminsko-Mazurskie', 'WAR'),
		('Poland', 'PL', 'Wielkopolskie', 'WIE'),
		('Poland', 'PL', 'Zachodniopomorskie', 'ZAC'),
		('Portugal', 'PT', 'Acores', 'ACO'),
		('Portugal', 'PT', 'Alentejo', 'ALE'),
		('Portugal', 'PT', 'Algarve', 'ALG'),
		('Portugal', 'PT', 'Centro', 'CEN'),
		('Portugal', 'PT', 'Lisboa', 'LIS'),
		('Portugal', 'PT', 'Madeira', 'MAD'),
		('Portugal', 'PT', 'Norte', 'NOR'),
		('Qatar', 'QA', 'Qatar', 'QTR'),
		('Reunion', 'RE', 'Reunion', 'RE'),
		('Romania', 'RO', 'Alba', 'ALB'),
		('Romania', 'RO', 'Arad', 'ARA'),
		('Romania', 'RO', 'Arges', 'ARG'),
		('Romania', 'RO', 'Bacau', 'BAC'),
		('Romania', 'RO', 'Bihor', 'BIH'),
		('Romania', 'RO', 'Bistrita-Nasaud', 'BIS'),
		('Romania', 'RO', 'Botosani', 'BOT'),
		('Romania', 'RO', 'Braila', 'BRA'),
		('Romania', 'RO', 'Brasov', 'BRS'),
		('Romania', 'RO', 'Buzau', 'BUZ'),
		('Romania', 'RO', 'Calarasi', 'CAL'),
		('Romania', 'RO', 'Caras-Severin', 'CAR'),
		('Romania', 'RO', 'Cluj', 'CLU'),
		('Romania', 'RO', 'Constanta', 'CON'),
		('Romania', 'RO', 'Covasna', 'COV'),
		('Romania', 'RO', 'Dambovita', 'DAM'),
		('Romania', 'RO', 'Dolj', 'DOL'),
		('Romania', 'RO', 'Galati', 'GAL'),
		('Romania', 'RO', 'Giurgiu', 'GIU'),
		('Romania', 'RO', 'Gorj', 'GOR'),
		('Romania', 'RO', 'Harghita', 'HAR'),
		('Romania', 'RO', 'Hunedoara', 'HUN'),
		('Romania', 'RO', 'Ialomita', 'IAL'),
		('Romania', 'RO', 'Iasi', 'IAS'),
		('Romania', 'RO', 'Ilfov', 'ILF'),
		('Romania', 'RO', 'Maramures', 'MAR'),
		('Romania', 'RO', 'Mehedinti', 'MEH'),
		('Romania', 'RO', 'Municipiul Bucuresti', 'BUC'),
		('Romania', 'RO', 'Mures', 'MUR'),
		('Romania', 'RO', 'Neamt', 'NEA'),
		('Romania', 'RO', 'Olt', 'OLT'),
		('Romania', 'RO', 'Prahova', 'PRA'),
		('Romania', 'RO', 'Salaj', 'SAL'),
		('Romania', 'RO', 'Satu Mare', 'SAT'),
		('Romania', 'RO', 'Sibiu', 'SIB'),
		('Romania', 'RO', 'Suceava', 'SUC'),
		('Romania', 'RO', 'Teleorman', 'TEL'),
		('Romania', 'RO', 'Timis', 'TIM'),
		('Romania', 'RO', 'Tulcea', 'TUL'),
		('Romania', 'RO', 'Unknown', 'UNK'),
		('Romania', 'RO', 'Valcea', 'VAL'),
		('Romania', 'RO', 'Vaslui', 'VAS'),
		('Romania', 'RO', 'Vrancea', 'VRA'),
		('Russia', 'RU', 'Aginskiy Buryatskiy', '02'),
		('Russia', 'RU', 'Altayskiy Kray', '04'),
		('Russia', 'RU', 'Amurskaya', '05'),
		('Russia', 'RU', 'Arkhangel''skaya', '06'),
		('Russia', 'RU', 'Astrakhanskaya', '07'),
		('Russia', 'RU', 'Belgorodskaya', '09'),
		('Russia', 'RU', 'Bryanskaya', '10'),
		('Russia', 'RU', 'Chechenskaya', 'CI'),
		('Russia', 'RU', 'Chelyabinskaya', '13'),
		('Russia', 'RU', 'Chitinskaya', '14'),
		('Russia', 'RU', 'Chukotskiy', '15'),
		('Russia', 'RU', 'Chuvashskaya', '16'),
		('Russia', 'RU', 'Evenkiyskiy', '18'),
		('Russia', 'RU', 'Gorod Moskva', '48'),
		('Russia', 'RU', 'Gorod Sankt-Peterburg', '66'),
		('Russia', 'RU', 'Irkutskaya', '20'),
		('Russia', 'RU', 'Ivanovskaya', '21'),
		('Russia', 'RU', 'Kabardino-Balkarskaya', '22'),
		('Russia', 'RU', 'Kaliningradskaya', '23'),
		('Russia', 'RU', 'Kaluzhskaya', '25'),
		('Russia', 'RU', 'Kamchatskaya', '26'),
		('Russia', 'RU', 'Karachayevo-Cherkesskaya', '27'),
		('Russia', 'RU', 'Kemerovskaya', '29'),
		('Russia', 'RU', 'Khabarovskiy Kray', '30'),
		('Russia', 'RU', 'Khanty-Mansiyskiy', '32'),
		('Russia', 'RU', 'Kirovskaya', '33'),
		('Russia', 'RU', 'Komi-Permyatskiy', '35'),
		('Russia', 'RU', 'Koryakskiy', '36'),
		('Russia', 'RU', 'Kostromskaya', '37'),
		('Russia', 'RU', 'Krasnodarskiy Kray', '38'),
		('Russia', 'RU', 'Krasnoyarskiy Kray', '39'),
		('Russia', 'RU', 'Kurganskaya', '40'),
		('Russia', 'RU', 'Kurskaya', '41'),
		('Russia', 'RU', 'Leningradskaya', '42'),
		('Russia', 'RU', 'Lipetskaya', '43'),
		('Russia', 'RU', 'Magadanskaya', '44'),
		('Russia', 'RU', 'Moskovskaya', '47'),
		('Russia', 'RU', 'Murmanskaya', '49'),
		('Russia', 'RU', 'Nenetskiy', '50'),
		('Russia', 'RU', 'Nizhegorodskaya', '51'),
		('Russia', 'RU', 'Novgorodskaya', '52'),
		('Russia', 'RU', 'Novosibirskaya', '53'),
		('Russia', 'RU', 'Omskaya', '54'),
		('Russia', 'RU', 'Orenburgskaya', '55'),
		('Russia', 'RU', 'Orlovskaya', '56'),
		('Russia', 'RU', 'Penzenskaya', '57'),
		('Russia', 'RU', 'Permskaya', '58'),
		('Russia', 'RU', 'Primorskiy Kray', '59'),
		('Russia', 'RU', 'Pskovskaya', '60'),
		('Russia', 'RU', 'Respublika Adygeya', '01'),
		('Russia', 'RU', 'Respublika Altay', '03'),
		('Russia', 'RU', 'Respublika Bashkortostan', '08'),
		('Russia', 'RU', 'Respublika Buryatiya', '11'),
		('Russia', 'RU', 'Respublika Dagestan', '17'),
		('Russia', 'RU', 'Respublika Kalmykiya', '24'),
		('Russia', 'RU', 'Respublika Kareliya', '28'),
		('Russia', 'RU', 'Respublika Khakasiya', '31'),
		('Russia', 'RU', 'Respublika Komi', '34'),
		('Russia', 'RU', 'Respublika Mariy-El', '45'),
		('Russia', 'RU', 'Respublika Mordoviya', '46'),
		('Russia', 'RU', 'Respublika Sakha', 'Yakut'),
		('Russia', 'RU', 'Respublika Severnaya Osetiya-Alaniya', '68'),
		('Russia', 'RU', 'Respublika Tatarstan', '73'),
		('Russia', 'RU', 'Respublika Tyva', '79'),
		('Russia', 'RU', 'Rostovskaya', '61'),
		('Russia', 'RU', 'Ryazanskaya', '62'),
		('Russia', 'RU', 'Sakhalinskaya', '64'),
		('Russia', 'RU', 'Samarskaya', '65'),
		('Russia', 'RU', 'Saratovskaya', '67'),
		('Russia', 'RU', 'Smolenskaya', '69'),
		('Russia', 'RU', 'Stavropol''skiy Kray', '70'),
		('Russia', 'RU', 'Sverdlovskaya', '71'),
		('Russia', 'RU', 'Tambovskaya', '72'),
		('Russia', 'RU', 'Taymyrskiy', 'Dolga'),
		('Russia', 'RU', 'Tomskaya', '75'),
		('Russia', 'RU', 'Tul''skaya', '76'),
		('Russia', 'RU', 'Tverskaya', '77'),
		('Russia', 'RU', 'Tyumenskaya', '78'),
		('Russia', 'RU', 'Udmurtskaya', '80'),
		('Russia', 'RU', 'Ul''yanovskaya', '81'),
		('Russia', 'RU', 'Ust''-Ordynskiy Buryatskiy', '82'),
		('Russia', 'RU', 'Vladimirskaya', '83'),
		('Russia', 'RU', 'Volgogradskaya', '84'),
		('Russia', 'RU', 'Vologodskaya', '85'),
		('Russia', 'RU', 'Voronezhskaya', '86'),
		('Russia', 'RU', 'Yamalo-Nenetskiy', '87'),
		('Russia', 'RU', 'Yaroslavskaya', '88'),
		('Russia', 'RU', 'Yevreyskaya', '89'),
		('Rwanda', 'RW', 'Rwanda', 'RWA'),
		('Saint Helena', 'SH', 'Saint Helena', 'STH'),
		('Saint Kitts and Nevis', 'KN', 'Saint Kitts & Nevis', 'SKN'),
		('Saint Lucia', 'LC', 'Saint Lucia', 'SLC'),
		('Saint Pierre and Miquelon', 'PM', 'Saint Pierre & Miquelon', 'SPM'),
		('Saint Vincent and the Grenadines', 'VC', 'Saint Vincent & the Grenadines', 'STV'),
		('Samoa', 'WS', 'Samoa', 'SAM'),
		('San Marino', 'SM', 'San Marino', 'MAR'),
		('Sao Tome & Principe', 'ST', 'Sao Tome & Principe', 'SAO'),
		('Saudi Arabia', 'SA', 'Al Bahah', 'BAH'),
		('Saudi Arabia', 'SA', 'Al Hudud ash Shamaliyah', 'HUD'),
		('Saudi Arabia', 'SA', 'Al Madinah', 'MAD'),
		('Saudi Arabia', 'SA', 'Al Mintaqah ash Sharqiyah', 'MAS'),
		('Saudi Arabia', 'SA', 'Al Qasim', 'QAS'),
		('Saudi Arabia', 'SA', 'Al-Jawf', 'JAW'),
		('Saudi Arabia', 'SA', 'Ar Riyad', 'RIY'),
		('Saudi Arabia', 'SA', 'Ha''il', 'HAI'),
		('Saudi Arabia', 'SA', 'Jizan', 'JIZ'),
		('Saudi Arabia', 'SA', 'Makkah', 'MAK'),
		('Saudi Arabia', 'SA', 'Tabuk', 'TAB'),
		('Senegal', 'SN', 'Dakar', 'DAK'),
		('Senegal', 'SN', 'Saint-Louis', 'STL'),
		('Senegal', 'SN', 'Thies', 'THI'),
		('Serbia and Montenegro', 'CS', 'Serbia & Montenegro', 'SRB'),
		('Seychelles', 'SC', 'Seychelles', 'SEY'),
		('Sierra Leone', 'SL', 'Eastern Province', 'EAS'),
		('Sierra Leone', 'SL', 'Northern Province', 'NOR'),
		('Sierra Leone', 'SL', 'Southern Province', 'SOU'),
		('Sierra Leone', 'SL', 'Western Area', 'WES'),
		('Singapore', 'SG', 'Singapore', 'SNG'),
		('Slovakia', 'SK', 'Slovakia', 'SLO'),
		('Slovenia', 'SI', 'Slovenia', 'SLO'),
		('Solomon Islands', 'SB', 'Solomon Islands', 'SOL'),
		('Somalia', 'SO', 'Bakool', 'BAK'),
		('Somalia', 'SO', 'Banaadir', 'BAN'),
		('Somalia', 'SO', 'Bari', 'BAR'),
		('Somalia', 'SO', 'Bay', 'BAY'),
		('Somalia', 'SO', 'Gedo', 'GED'),
		('Somalia', 'SO', 'Jubbada Dhexe', 'JUD'),
		('Somalia', 'SO', 'Jubbada Hoose', 'JUH'),
		('Somalia', 'SO', 'Shabeellaha Hoose', 'SHH'),
		('South Africa', 'ZA', 'Eastern Cape', 'EAS'),
		('South Africa', 'ZA', 'Free State', 'FRE'),
		('South Africa', 'ZA', 'Gauteng', 'GAU'),
		('South Africa', 'ZA', 'KwaZulu-Natal', 'KWA'),
		('South Africa', 'ZA', 'Limpopo', 'LIM'),
		('South Africa', 'ZA', 'Mpumalanga', 'MPU'),
		('South Africa', 'ZA', 'North-West', 'NRW'),
		('South Africa', 'ZA', 'Northern Cape', 'NOR'),
		('South Africa', 'ZA', 'Unknown', 'UNK'),
		('South Africa', 'ZA', 'Western Cape', 'WES'),
		('South Georgia', 'GS', 'South Georgia & South Sandwich Islands', 'SGS'),
		('South Korea', 'KR', 'Ch''ungch''ong-bukto', 'CHU'),
		('South Korea', 'KR', 'Ch''ungch''ong-namdo', 'CON'),
		('South Korea', 'KR', 'Cheju-do', 'CHE'),
		('South Korea', 'KR', 'Cholla-bukto', 'CHO'),
		('South Korea', 'KR', 'Cholla-namdo', 'CHN'),
		('South Korea', 'KR', 'Inch''on-gwangyoksi', 'INC'),
		('South Korea', 'KR', 'Kangwon-do', 'KAN'),
		('South Korea', 'KR', 'Kwangju-gwangyoksi', 'KWA'),
		('South Korea', 'KR', 'Kyonggi-do', 'KYO'),
		('South Korea', 'KR', 'Kyongsang-bukto', 'KYB'),
		('South Korea', 'KR', 'Kyongsang-namdo', 'KGM'),
		('South Korea', 'KR', 'Pusan-gwangyoksi', 'PUS'),
		('South Korea', 'KR', 'Soul-t''ukpyolsi', 'SOU'),
		('South Korea', 'KR', 'Taegu-gwangyoksi', 'TAE'),
		('South Korea', 'KR', 'Taejon-gwangyoksi', 'TAG'),
		('South Korea', 'KR', 'Ulsan-gwangyoksi', 'ULS'),
		('Spain', 'ES', 'Andalucia', 'AND'),
		('Spain', 'ES', 'Aragon', 'ARA'),
		('Spain', 'ES', 'Asturias', 'AST'),
		('Spain', 'ES', 'Baleares', 'BAL'),
		('Spain', 'ES', 'Canarias', 'CAN'),
		('Spain', 'ES', 'Cantabria', 'CAR'),
		('Spain', 'ES', 'Castilla y Leon', 'CLE'),
		('Spain', 'ES', 'Castilla-La Mancha', 'CLM'),
		('Spain', 'ES', 'Cataluna', 'CAT'),
		('Spain', 'ES', 'Ceuta y Melilla', 'CM'),
		('Spain', 'ES', 'Extremadura', 'EXT'),
		('Spain', 'ES', 'Galicia', 'GAL'),
		('Spain', 'ES', 'La Rioja', 'LAR'),
		('Spain', 'ES', 'Madrid', 'MAD'),
		('Spain', 'ES', 'Murcia', 'MUR'),
		('Spain', 'ES', 'Navarra', 'NAV'),
		('Spain', 'ES', 'Pais Vasco', 'PAI'),
		('Spain', 'ES', 'Valencia', 'VAL'),
		('Sri Lanka', 'LK', 'Sri Lanka', 'SRI'),
		('Sudan', 'SD', 'Sudan', 'SUD'),
		('Suriname', 'SR', 'Suriname', 'SUR'),
		('Svalbard', 'SJ', 'Svalbard', 'SVL'),
		('Swaziland', 'SZ', 'Swaziland', 'SWZ'),
		('Sweden', 'SE', 'Blekinge lan', 'BLE'),
		('Sweden', 'SE', 'Dalarnas lan', 'DAL'),
		('Sweden', 'SE', 'Gavleborgs lan', 'GAV'),
		('Sweden', 'SE', 'Gotlands lan', 'GOT'),
		('Sweden', 'SE', 'Hallands lan', 'HAL'),
		('Sweden', 'SE', 'Jamtlands lan', 'JAM'),
		('Sweden', 'SE', 'Jonkopings lan', 'JON'),
		('Sweden', 'SE', 'Kalmar lan', 'KAL'),
		('Sweden', 'SE', 'Kronobergs lan', 'KRO'),
		('Sweden', 'SE', 'Norrbottens lan', 'NOR'),
		('Sweden', 'SE', 'Orebro lan', 'ORE'),
		('Sweden', 'SE', 'Ostergotlands lan', 'OST'),
		('Sweden', 'SE', 'Skane lan', 'SKA'),
		('Sweden', 'SE', 'Sodermanlands lan', 'SOD'),
		('Sweden', 'SE', 'Stockholms lan', 'STO'),
		('Sweden', 'SE', 'Uppsala lan', 'UPP'),
		('Sweden', 'SE', 'Varmlands lan', 'VAR'),
		('Sweden', 'SE', 'Vasterbottens lan', 'VAS'),
		('Sweden', 'SE', 'Vasternorrlands lan', 'VNL'),
		('Sweden', 'SE', 'Vastmanlands lan', 'VML'),
		('Sweden', 'SE', 'Vastra Gotalands lan', 'VGI'),
		  
		('Switzerland', 'CH', 'Aargau', '1AG'), 
		('Switzerland', 'CH', 'Appenzell Ausserrhoden', '1AR'), 
		('Switzerland', 'CH', 'Appenzell Innerrhoden', '1AI'), 
		('Switzerland', 'CH', 'Basel-Landschaft', '1BL'), 
		('Switzerland', 'CH', 'Basel-Stadt', '1BS'),  
		('Switzerland', 'CH', 'Bern', '1BE'), 
		('Switzerland', 'CH', 'Fribourg', '1FR'), 
		('Switzerland', 'CH', 'Geneva', '1GE'), 
		('Switzerland', 'CH', 'Glarus', '1GL'), 
		('Switzerland', 'CH', 'Graubünden', '1GR'), 
		('Switzerland', 'CH', 'Jura', '1JU'), 
		('Switzerland', 'CH', 'Lucerne', '1LU'), 
		('Switzerland', 'CH', 'Neuchâtel', '1NE'), 
		('Switzerland', 'CH', 'Nidwalden', '1NW'), 
		('Switzerland', 'CH', 'Obwalden', '1OW'), 
		('Switzerland', 'CH', 'Schaffhausen', '1SH'), 
		('Switzerland', 'CH', 'Schwyz', '1SZ'), 
		('Switzerland', 'CH', 'Solothurn', '1SO'),  
		('Switzerland', 'CH', 'St. Gallen', '1SG'), 
		('Switzerland', 'CH', 'Thurgau', '1TG'), 
		('Switzerland', 'CH', 'Ticino', '1TI'), 
		('Switzerland', 'CH', 'Uri', '1UR'), 
		('Switzerland', 'CH', 'Valais', '1VS'), 
		('Switzerland', 'CH', 'Vaud', '1VD'),  
		('Switzerland', 'CH', 'Zug', '1ZG'), 
		('Switzerland', 'CH', 'Zurich', '1ZH'),
		 

		('Syria', 'SY', 'Dar`a', 'DAR'),
		('Syria', 'SY', 'Dayr az Zawr', 'DAY'),
		('Syria', 'SY', 'Dimashq', 'DIM'),
		('Syria', 'SY', 'Hamah', 'HAM'),
		('Syria', 'SY', 'Hasakah', 'HAS'),
		('Syria', 'SY', 'Hims', 'HIM'),
		('Syria', 'SY', 'Ladhiqiyah', 'LAD'),
		('Syria', 'SY', 'Unknown', 'UNK'),
		('Taiwan', 'TW', 'Kao-hsiung', 'KAO'),
		('Taiwan', 'TW', 'T''ai-pei', 'TPS'),
		('Taiwan', 'TW', 'T''ai-wan', 'TWS'),
		('Tajikistan', 'TJ', 'Khatlon', 'KHA'),
		('Tajikistan', 'TJ', 'Mukhtori Kuhistoni Badakhshon', 'MKB'),
		('Tajikistan', 'TJ', 'Sughd', 'SUG'),
		('Tajikistan', 'TJ', 'Unknown', 'UNK'),
		('Tanzania', 'TZ', 'Kagera', 'KAG'),
		('Tanzania', 'TZ', 'Kigoma', 'KIG'),
		('Tanzania', 'TZ', 'Kilimanjaro', 'KIL'),
		('Tanzania', 'TZ', 'Mwanza', 'MWA'),
		('Tanzania', 'TZ', 'Rukwa', 'RUK'),
		('Tanzania', 'TZ', 'Shinyanga', 'SHI'),
		('Tanzania', 'TZ', 'Tabora', 'TAB'),
		('Tanzania', 'TZ', 'Unknown', 'UNK'),
		('Thailand', 'TH', 'Bangkok Metropolis', 'BKM'),
		('Thailand', 'TH', 'Central', 'CEN'),
		('Thailand', 'TH', 'Northeastern', 'NEA'),
		('Thailand', 'TH', 'Northern', 'NOR'),
		('Thailand', 'TH', 'Southern', 'SOU'),
		('Togo', 'TG', 'Togo', 'TOG'),
		('Tokelau', 'TK', 'Tokelau', 'TOK'),
		('Tonga', 'TO', 'Tonga', 'TNG'),
		('Trinidad and Tobago', 'TT', 'Trinidad & Tobago', 'TRT'),
		('Tunisia', 'TN', 'Ariana', 'ARI'),
		('Tunisia', 'TN', 'Mahdia', 'MAH'),
		('Tunisia', 'TN', 'Sousse', 'SOU'),
		('Tunisia', 'TN', 'Tunis', 'TUN'),
		('Tunisia', 'TN', 'Unknown', 'UNK'),
		('Turkey', 'TR', 'Adana', 'ADA'),
		('Turkey', 'TR', 'Ankara', 'ANK'),
		('Turkey', 'TR', 'Antalya', 'ANT'),
		('Turkey', 'TR', 'Aydin', 'AYD'),
		('Turkey', 'TR', 'Bilecik', 'BIL'),
		('Turkey', 'TR', 'Bursa', 'BUR'),
		('Turkey', 'TR', 'Diyarbakir', 'DIY'),
		('Turkey', 'TR', 'Erzurum', 'ERZ'),
		('Turkey', 'TR', 'Hakkari', 'HAK'),
		('Turkey', 'TR', 'Hatay', 'HAT'),
		('Turkey', 'TR', 'Icel', 'ICE'),
		('Turkey', 'TR', 'Isparta', 'ISP'),
		('Turkey', 'TR', 'Istanbul', 'IST'),
		('Turkey', 'TR', 'Izmir', 'IZM'),
		('Turkey', 'TR', 'Karaman', 'KAM'),
		('Turkey', 'TR', 'Kilis', 'KIL'),
		('Turkey', 'TR', 'Kocaeli', 'KOC'),
		('Turkey', 'TR', 'Konya', 'KON'),
		('Turkey', 'TR', 'Manisa', 'MAN'),
		('Turkey', 'TR', 'Nigde', 'NIG'),
		('Turkey', 'TR', 'Sirnak', 'SIR'),
		('Turkey', 'TR', 'Sivas', 'SIV'),
		('Turkey', 'TR', 'Yalova', 'YAL'),
		('Turkmenistan', 'TM', 'Ahal', 'AHL'),
		('Turkmenistan', 'TM', 'Balkan', 'BAL'),
		('Turkmenistan', 'TM', 'Dasoguz', 'DAS'),
		('Turkmenistan', 'TM', 'Lebap', 'LEB'),
		('Turkmenistan', 'TM', 'Mary', 'MAR'),
		('Turks and Caicos Islands', 'TC', 'Turks & Caicos Islands', 'TUK'),
		('Tuvalu', 'TV', 'Tuvalu', 'TUV'),
		('Uganda', 'UG', 'Uganda', 'UGA'),
		('Ukraine', 'UA', 'Cherkas''ka', 'CHK'),
		('Ukraine', 'UA', 'Chernihivs''ka', 'CHH'),
		('Ukraine', 'UA', 'Chernivets''ka', 'CHT'),
		('Ukraine', 'UA', 'Dnipropetrovs''ka', 'DNI'),
		('Ukraine', 'UA', 'Donets''ka', 'DON'),
		('Ukraine', 'UA', 'Ivano-Frankivs''ka', 'IVA'),
		('Ukraine', 'UA', 'Kharkivs''ka', 'KHA'),
		('Ukraine', 'UA', 'Khersons''ka', 'KHE'),
		('Ukraine', 'UA', 'Khmel''nyts''ka', 'KHM'),
		('Ukraine', 'UA', 'Kirovohrads''ka', 'KIR'),
		('Ukraine', 'UA', 'Kyrm', 'KRY'),
		('Ukraine', 'UA', 'Kyyivs''ka', 'KYY'),
		('Ukraine', 'UA', 'L''vivs''ka', 'LVI'),
		('Ukraine', 'UA', 'Luhans''ka', 'LUH'),
		('Ukraine', 'UA', 'Misto Kyyiv', 'KYC'),
		('Ukraine', 'UA', 'Misto Sevastopol''', 'SEV'),
		('Ukraine', 'UA', 'Mykolayivs''ka', 'MYK'),
		('Ukraine', 'UA', 'Odes''ka', 'ODE'),
		('Ukraine', 'UA', 'Poltavs''ka', 'POL'),
		('Ukraine', 'UA', 'Rivnens''ka', 'RIV'),
		('Ukraine', 'UA', 'Sums''ka', 'SUM'),
		('Ukraine', 'UA', 'Ternopil''s''ka', 'TER'),
		('Ukraine', 'UA', 'Vinnyts''ka', 'VIN'),
		('Ukraine', 'UA', 'Volyns''ka', 'VOL'),
		('Ukraine', 'UA', 'Zakarpats''ka', 'ZAK'),
		('Ukraine', 'UA', 'Zaporiz''ka', 'ZAP'),
		('Ukraine', 'UA', 'Zhytomyrs''ka', 'ZHY'),
		('United Arab Emirates', 'AE', 'United Arab Emirates', 'UAE'),
		('United Kingdom', 'GB', 'Avon', 'AVN'),
		('United Kingdom', 'GB', 'Bedfordshire', 'BFE'),
		('United Kingdom', 'GB', 'Berkshire', 'BKE'),
		('United Kingdom', 'GB', 'Borders', 'BDE'),
		('United Kingdom', 'GB', 'Buckinghamshire', 'BHE'),
		('United Kingdom', 'GB', 'Cambridgeshire', 'CBE'),
		('United Kingdom', 'GB', 'Central', 'CTL'),
		('United Kingdom', 'GB', 'Cheshire', 'CHE'),
		('United Kingdom', 'GB', 'Cleveland', 'CVD'),
		('United Kingdom', 'GB', 'Clwyd', 'CLD'),
		('United Kingdom', 'GB', 'Cornwall', 'CWL'),
		('United Kingdom', 'GB', 'CountyAntrim', 'CAM'),
		('United Kingdom', 'GB', 'CountyArmagh', 'CAH'),
		('United Kingdom', 'GB', 'CountyDown', 'CDN'),
		('United Kingdom', 'GB', 'CountyFermanagh', 'CFH'),
		('United Kingdom', 'GB', 'CountyLondonderry', 'CLY'),
		('United Kingdom', 'GB', 'CountyTyrone', 'CTE'),
		('United Kingdom', 'GB', 'Cumbria', 'CUA'),
		('United Kingdom', 'GB', 'Derbyshire', 'DYE'),
		('United Kingdom', 'GB', 'Devon', 'DVN'),
		('United Kingdom', 'GB', 'Dorset', 'DST'),
		('United Kingdom', 'GB', 'DumfriesandGalloway', 'DGY'),
		('United Kingdom', 'GB', 'Durham', 'DRM'),
		('United Kingdom', 'GB', 'Dyfed', 'DYD'),
		('United Kingdom', 'GB', 'EastSussex', 'EAX'),
		('United Kingdom', 'GB', 'Essex', 'ESX'),
		('United Kingdom', 'GB', 'Fife', 'FFE'),
		('United Kingdom', 'GB', 'Gloucestershire', 'GTE'),
		('United Kingdom', 'GB', 'Grampian', 'GRN'),
		('United Kingdom', 'GB', 'GreaterManchester', 'GMR'),
		('United Kingdom', 'GB', 'Gwent', 'GWT'),
		('United Kingdom', 'GB', 'GwyneddCounty', 'GCY'),
		('United Kingdom', 'GB', 'Hampshire', 'HHE'),
		('United Kingdom', 'GB', 'Herefordshire', 'HEE'),
		('United Kingdom', 'GB', 'Hertfordshire', 'HTE'),
		('United Kingdom', 'GB', 'HighlandsandIslands', 'HSS'),
		('United Kingdom', 'GB', 'Humberside', 'HBE'),
		('United Kingdom', 'GB', 'IsleofWight', 'IWT'),
		('United Kingdom', 'GB', 'Kent', 'KET'),
		('United Kingdom', 'GB', 'Lancashire', 'LCE'),
		('United Kingdom', 'GB', 'Leicestershire', 'LEE'),
		('United Kingdom', 'GB', 'Lincolnshire', 'LIE'),
		('United Kingdom', 'GB', 'Lothian', 'LTN'),
		('United Kingdom', 'GB', 'Merseyside', 'MYE'),
		('United Kingdom', 'GB', 'MidGlamorgan', 'MGN'),
		('United Kingdom', 'GB', 'Norfolk', 'NFK'),
		('United Kingdom', 'GB', 'NorthYorkshire', 'NYE'),
		('United Kingdom', 'GB', 'Northamptonshire', 'NSE'),
		('United Kingdom', 'GB', 'Northumberland', 'NLD'),
		('United Kingdom', 'GB', 'Nottinghamshire', 'NHE'),
		('United Kingdom', 'GB', 'Oxfordshire', 'OSE'),
		('United Kingdom', 'GB', 'Powys', 'POS'),
		('United Kingdom', 'GB', 'Rutland', 'RTD'),
		('United Kingdom', 'GB', 'Shropshire', 'SSE'),
		('United Kingdom', 'GB', 'Somerset', 'SRT'),
		('United Kingdom', 'GB', 'SouthGlamorgan', 'SGN'),
		('United Kingdom', 'GB', 'SouthYorkshire', 'SYE'),
		('United Kingdom', 'GB', 'Staffordshire', 'SFE'),
		('United Kingdom', 'GB', 'Strathclyde', 'STE'),
		('United Kingdom', 'GB', 'Suffolk', 'SUK'),
		('United Kingdom', 'GB', 'Surrey', 'SRY'),
		('United Kingdom', 'GB', 'Tayside', 'TSE'),
		('United Kingdom', 'GB', 'TyneandWear', 'TWR'),
		('United Kingdom', 'GB', 'Warwickshire', 'WSE'),
		('United Kingdom', 'GB', 'WestGlamorgan', 'WGN'),
		('United Kingdom', 'GB', 'WestMidlands', 'WMS'),
		('United Kingdom', 'GB', 'WestSussex', 'WSX'),
		('United Kingdom', 'GB', 'WestYorkshire', 'WYE'),
		('United Kingdom', 'GB', 'Wiltshire', 'WHE'),
		('United Kingdom', 'GB', 'Worcestershire', 'WOE'),
		('United States', 'US', 'Alabama', 'AL'),
		('United States', 'US', 'Alaska', 'AK'),
		('United States', 'US', 'American Samoa', 'AS'),
		('United States', 'US', 'Arizona', 'AZ'),
		('United States', 'US', 'Arkansas', 'AR'),
		('United States', 'US', 'California', 'CA'),
		('United States', 'US', 'Colorado', 'CO'),
		('United States', 'US', 'Connecticut', 'CT'),
		('United States', 'US', 'Delaware', 'DE'),
		('United States', 'US', 'District of Columbia', 'DC'),
		('United States', 'US', 'Florida', 'FL'),
		('United States', 'US', 'Georgia', 'GA'),
		('United States', 'US', 'Guam', 'GU'),
		('United States', 'US', 'Hawaii', 'HI'),
		('United States', 'US', 'Idaho', 'ID'),
		('United States', 'US', 'Illinois', 'IL'),
		('United States', 'US', 'Indiana', 'IN'),
		('United States', 'US', 'Iowa', 'IA'),
		('United States', 'US', 'Kansas', 'KS'),
		('United States', 'US', 'Kentucky', 'KY'),
		('United States', 'US', 'Louisiana', 'LA'),
		('United States', 'US', 'Maine', 'ME'),
		('United States', 'US', 'Maryland', 'MD'),
		('United States', 'US', 'Massachusetts', 'MA'),
		('United States', 'US', 'Michigan', 'MI'),
		('United States', 'US', 'Minnesota', 'MN'),
		('United States', 'US', 'Mississippi', 'MS'),
		('United States', 'US', 'Missouri', 'MO'),
		('United States', 'US', 'Montana', 'MT'),
		('United States', 'US', 'Nebraska', 'NE'),
		('United States', 'US', 'Nevada', 'NV'),
		('United States', 'US', 'New Hampshire', 'NH'),
		('United States', 'US', 'New Jersey', 'NJ'),
		('United States', 'US', 'New Mexico', 'NM'),
		('United States', 'US', 'New York', 'NY'),
		('United States', 'US', 'North Carolina', 'NC'),
		('United States', 'US', 'North Dakota', 'ND'),
		('United States', 'US', 'Northern Mariana Islands', 'MP'),
		('United States', 'US', 'Ohio', 'OH'),
		('United States', 'US', 'Oklahoma', 'OK'),
		('United States', 'US', 'Oregon', 'OR'),
		('United States', 'US', 'Pennsylvania', 'PA'),
		('United States', 'US', 'Puerto Rico', 'PR'),
		('United States', 'US', 'Rhode Island', 'RI'),
		('United States', 'US', 'South Carolina', 'SC'),
		('United States', 'US', 'South Dakota', 'SD'),
		('United States', 'US', 'Tennessee', 'TN'),
		('United States', 'US', 'Texas', 'TX'),
		('United States', 'US', 'Utah', 'UT'),
		('United States', 'US', 'Vermont', 'VT'),
		('United States', 'US', 'Virgin Islands', 'VI'),
		('United States', 'US', 'Virginia', 'VA'),
		('United States', 'US', 'Washington', 'WA'),
		('United States', 'US', 'West Virginia', 'WV'),
		('United States', 'US', 'Wisconsin', 'WI'),
		('United States', 'US', 'Wyoming', 'WY'),
		('Uruguay', 'UY', 'Artigas', 'ART'),
		('Uruguay', 'UY', 'Canelones', 'CAN'),
		('Uruguay', 'UY', 'Cerro Largo', 'CER'),
		('Uruguay', 'UY', 'Colonia', 'COL'),
		('Uruguay', 'UY', 'Durazno', 'DUR'),
		('Uruguay', 'UY', 'Florida', 'FLO'),
		('Uruguay', 'UY', 'Lavalleja', 'LAV'),
		('Uruguay', 'UY', 'Maldonado', 'MAL'),
		('Uruguay', 'UY', 'Montevideo', 'MON'),
		('Uruguay', 'UY', 'Paysandu', 'PAY'),
		('Uruguay', 'UY', 'Rio Negro', 'RNE'),
		('Uruguay', 'UY', 'Rivera', 'RIV'),
		('Uruguay', 'UY', 'Rocha', 'ROC'),
		('Uruguay', 'UY', 'Salto', 'SAL'),
		('Uruguay', 'UY', 'San Jose', 'SJO'),
		('Uruguay', 'UY', 'Soriano', 'SOR'),
		('Uruguay', 'UY', 'Tacuarembo', 'TAC'),
		('Uruguay', 'UY', 'Treinta y Tres', 'TYT'),
		('Uzbekistan', 'UZ', 'Andijon', 'AND'),
		('Uzbekistan', 'UZ', 'Buxoro', 'BUX'),
		('Uzbekistan', 'UZ', 'Jizzax', 'JIZ'),
		('Uzbekistan', 'UZ', 'Namangan', 'NAM'),
		('Uzbekistan', 'UZ', 'Navoiy', 'NAV'),
		('Uzbekistan', 'UZ', 'Qashqadaryo', 'QAS'),
		('Uzbekistan', 'UZ', 'Qoraqalpog`iston', 'QOR'),
		('Uzbekistan', 'UZ', 'Samarqand', 'SAM'),
		('Uzbekistan', 'UZ', 'Sirdaryo', 'SIR'),
		('Uzbekistan', 'UZ', 'Surxondaryo', 'SUR'),
		('Uzbekistan', 'UZ', 'Toshkent', 'TOS'),
		('Uzbekistan', 'UZ', 'Toshkent Shahri', 'TSH'),
		('Uzbekistan', 'UZ', 'Unknown', 'UNK'),
		('Uzbekistan', 'UZ', 'Xorazm', 'XOR'),
		('Vanuatu', 'VU', 'Vanuatu', 'VAN'),
		('Vatican City', 'VA', 'Vatican City', 'VTC'),
		('Venezuela', 'VE', 'Amazonas', 'AMA'),
		('Venezuela', 'VE', 'Anzoategui', 'ANZ'),
		('Venezuela', 'VE', 'Apure', 'APU'),
		('Venezuela', 'VE', 'Aragua', 'ARA'),
		('Venezuela', 'VE', 'Barinas', 'BAR'),
		('Venezuela', 'VE', 'Bolivar', 'BOL'),
		('Venezuela', 'VE', 'Carabobo', 'CAR'),
		('Venezuela', 'VE', 'Falcon', 'FAL'),
		('Venezuela', 'VE', 'Guarico', 'GUA'),
		('Venezuela', 'VE', 'Lara', 'LAR'),
		('Venezuela', 'VE', 'Merida', 'MER'),
		('Venezuela', 'VE', 'Miranda', 'MIR'),
		('Venezuela', 'VE', 'Monagas', 'MON'),
		('Venezuela', 'VE', 'Nueva Esparta', 'NES'),
		('Venezuela', 'VE', 'Sucre', 'SUC'),
		('Venezuela', 'VE', 'Tachira', 'TAC'),
		('Venezuela', 'VE', 'Trujillo', 'TRU'),
		('Venezuela', 'VE', 'Vargas', 'VAR'),
		('Venezuela', 'VE', 'Yaracuy', 'YAR'),
		('Venezuela', 'VE', 'Zulia', 'ZUL'),
		('Vietnam', 'VN', 'An Giang', 'ANG'),
		('Vietnam', 'VN', 'Ba Ria-Vung Tau', 'BRV'),
		('Vietnam', 'VN', 'Bac Giang', 'GIN'),
		('Vietnam', 'VN', 'Bac Kan', 'KAN'),
		('Vietnam', 'VN', 'Bac Lieu', 'LIU'),
		('Vietnam', 'VN', 'Bac Ninh', 'NIH'),
		('Vietnam', 'VN', 'Ben Tre', 'BEN'),
		('Vietnam', 'VN', 'Binh Dinh', 'BDI'),
		('Vietnam', 'VN', 'Binh Duong', 'DUO'),
		('Vietnam', 'VN', 'Binh Phuoc', 'BPC'),
		('Vietnam', 'VN', 'Binh Thuan', 'BTH'),
		('Vietnam', 'VN', 'Ca Mau', 'CAM'),
		('Vietnam', 'VN', 'Can Tho', 'CAN'),
		('Vietnam', 'VN', 'Cao Bang', 'CAO'),
		('Vietnam', 'VN', 'Da Nang', 'DAN'),
		('Vietnam', 'VN', 'Dac Lak', 'DAC'),
		('Vietnam', 'VN', 'Dong Nai', 'DNA'),
		('Vietnam', 'VN', 'Dong Thap', 'DON'),
		('Vietnam', 'VN', 'Gia Lai', 'GIA'),
		('Vietnam', 'VN', 'Ha Giang', 'HAG'),
		('Vietnam', 'VN', 'Ha Nam', 'HAM'),
		('Vietnam', 'VN', 'Ha Tay', 'HAT'),
		('Vietnam', 'VN', 'Ha Tinh', 'HAN'),
		('Vietnam', 'VN', 'Hai Duong', 'HDG'),
		('Vietnam', 'VN', 'Hoa Binh', 'HOA'),
		('Vietnam', 'VN', 'Hung Yen', 'HUY'),
		('Vietnam', 'VN', 'Khanh Hoa', 'KHH'),
		('Vietnam', 'VN', 'Kien Giang', 'KIE'),
		('Vietnam', 'VN', 'Kon Tum', 'KON'),
		('Vietnam', 'VN', 'Lai Chau', 'LAI'),
		('Vietnam', 'VN', 'Lam Dong', 'LAM'),
		('Vietnam', 'VN', 'Lang Son', 'LAN'),
		('Vietnam', 'VN', 'Lao Cai', 'LAC'),
		('Vietnam', 'VN', 'Long An', 'LON'),
		('Vietnam', 'VN', 'Nam Dinh', 'NAN'),
		('Vietnam', 'VN', 'Nghe An', 'NGH'),
		('Vietnam', 'VN', 'Ninh Binh', 'NBI'),
		('Vietnam', 'VN', 'Ninh Thuan', 'NIT'),
		('Vietnam', 'VN', 'Phu Tho', 'PTO'),
		('Vietnam', 'VN', 'Phu Yen', 'PHU'),
		('Vietnam', 'VN', 'Quang Binh', 'QBI'),
		('Vietnam', 'VN', 'Quang Nam', 'QNM'),
		('Vietnam', 'VN', 'Quang Ngai', 'QNG'),
		('Vietnam', 'VN', 'Quang Ninh', 'NIN'),
		('Vietnam', 'VN', 'Quang Tri', 'QTR'),
		('Vietnam', 'VN', 'Soc Trang', 'SOC'),
		('Vietnam', 'VN', 'Son La', 'SOL'),
		('Vietnam', 'VN', 'Tay Ninh', 'TAY'),
		('Vietnam', 'VN', 'Thai Binh', 'THB'),
		('Vietnam', 'VN', 'Thai Nguyen', 'TNY'),
		('Vietnam', 'VN', 'Thanh Hoa', 'THH'),
		('Vietnam', 'VN', 'Thanh Pho Hai Phong', 'PHP'),
		('Vietnam', 'VN', 'Thanh Pho Ho Chi Minh', 'PHC'),
		('Vietnam', 'VN', 'Thu Do Ha Noi', 'TDH'),
		('Vietnam', 'VN', 'Thua Thien-Hue', 'TTH'),
		('Vietnam', 'VN', 'Tien Giang', 'TGI'),
		('Vietnam', 'VN', 'Tra Vinh', 'TRA'),
		('Vietnam', 'VN', 'Tuyen Quang', 'TUY'),
		('Vietnam', 'VN', 'Vinh Long', 'VIL'),
		('Vietnam', 'VN', 'Vinh Phuc', 'VPC'),
		('Vietnam', 'VN', 'Yen Bai', 'YEN'),
		('Wallis and Futuna', 'WF', 'Wallis & Futuna', 'WAF'),
		('Yemen', 'YE', 'Yemen', 'YEM'),
		('Zambia', 'ZM', 'Central', 'CEN'),
		('Zambia', 'ZM', 'Eastern', 'EAS'),
		('Zambia', 'ZM', 'Lusaka', 'LUS'),
		('Zambia', 'ZM', 'Southern', 'SOU'),
		('Zambia', 'ZM', 'Unknown', 'UNK'),
		('Zambia', 'ZM', 'Western', 'WES'),
		('Zimbabwe', 'ZW', 'Harare', 'HAR'),
		('Zimbabwe', 'ZW', 'Manicaland', 'MNL'),
		('Zimbabwe', 'ZW', 'Mashonaland East', 'MSE'),
		('Zimbabwe', 'ZW', 'Mashonaland West', 'MSW'),
		('Zimbabwe', 'ZW', 'Masvingo', 'MVG'),
		('Zimbabwe', 'ZW', 'Matabeleland North', 'MBN'),
		('Zimbabwe', 'ZW', 'Matabeleland South', 'MBS'),
		('Zimbabwe', 'ZW', 'Midlands', 'MID'),
		('Zimbabwe', 'ZW', 'Unknown', 'UNK') 
		"); 

	}

	function sqlAfter(){

		$result = mysql_query("SHOW TABLES LIKE 'bx_arl_comments_bak'");
		$tableExists = mysql_num_rows($result) > 0;

		if(!$tableExists) return;
 
		$this->query("INSERT INTO `modzzz_articles_cmts` SELECT * FROM `bx_arl_comments_bak`");
		$this->query("INSERT INTO `modzzz_articles_cmts_track` SELECT * FROM `bx_arl_comments_track_bak`");
		$this->query("INSERT INTO `modzzz_articles_rating` SELECT * FROM `bx_arl_voting_bak`");
		$this->query("INSERT INTO `modzzz_articles_rating_track` SELECT * FROM `bx_arl_voting_track_bak`");
		$this->query("INSERT INTO `modzzz_articles_views_track` SELECT * FROM `bx_arl_views_track_bak`");
		 

		$this->query("  
		INSERT INTO `modzzz_articles_main` 
		(  `id`,`uri`,`tags`,`categories`,`author_id`,`title`,`snippet`,`desc`,`when`,`created`,`comments_count`,`views`,`rate`,`rate_count`,`featured`,`votes`  
		)  
		SELECT
		 `id`,`uri`,`tags`,`categories`,`author_id`,`caption`,`snippet`,`content`,`when`,`date`,`cmts_count`, `view_count`,`rate`,`rate_count`,`featured`,`vote` 
		FROM 
		`bx_arl_entries_bak`;
		");

		$this->query("
		UPDATE `modzzz_articles_main` SET
			`allow_view_article_to`=3, 		
			`allow_comment_to`=3, 		
			`allow_rate_to`=3 
		");
 
		$this->query("INSERT INTO `modzzz_articles_categ` (`parent`, `name`, `uri`, `icon`, `active`) VALUES (0, 'Main', 'main', '', 1)"); 

		$aCategories = $this->getAll("SELECT DISTINCT `Category` FROM `sys_categories` WHERE `Type`='bx_arl' AND `ID`=0");
		foreach($aCategories as $aEachCategory){
		 
			$sCategory = process_db_input($aEachCategory['Category']);
			$sCategoryUri = strtolower(uriGenerate($aEachCategory['Category'], 'modzzz_articles_categ', 'uri'));

			$this->query("INSERT INTO `modzzz_articles_categ` (`parent`, `name`, `uri`, `icon`, `active`) VALUES (1, '$sCategory', '$sCategoryUri', '', 1)"); 
		 
			$iSubCategoryId = $this->lastId();

			$this->query("UPDATE `modzzz_articles_main` SET `parent_category_id`=1, `category_id`=$iSubCategoryId WHERE `categories` LIKE '%$sCategory%' AND `parent_category_id`=0"); 
		}

		$iAdminId = (int)$this->getOne("SELECT `ID` FROM `Profiles` WHERE `Role`=3 LIMIT 1");
		$this->query("UPDATE `modzzz_articles_main` SET `author_id`=$iAdminId WHERE `author_id`=0"); 
	}
 
	//[begin] bg modification
	function getBackground($sName, $bUrlOnly=false, $iEntryId=0, $bRemove=false){
 		$sUrl = $this->_oConfig->getMediaUrl() . $sName;

		$sRemoveUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'bg/remove/'.$iEntryId;

		if($bUrlOnly) return $sUrl;

		$iWidth = $iHeight = 250;
   
		$sStr = "<img width='{$iWidth}px' height='{$iHeight}px' src='{$sUrl}' class='bx-twig-unit-thumb bx-def-round-corners bx-def-shadow'>";  
		$sStr .= "<br><a href='$sRemoveUrl'>" . _t('_modzzz_articles_remove'). "</a>";

		return $sStr;
	}	
    
	function updatePostWithBG($iEntryId, $sIcon='') { 
		$iEntryId = (int)$iEntryId;
		
		$this->query("UPDATE `" . $this->_sPrefix . "main` SET `background`='$sIcon' WHERE `id`=$iEntryId");  
	}

	function _actionRemoveBG($iEntryId) {
	    $iEntryId  = (int)$iEntryId;
	  
		$aDataEntry = $this->getEntryById($iEntryId); 
 		$sIcon = $aDataEntry['background'];
 
		if(!$sIcon) return;

		$sIconObject = $this->_oConfig->getMediaPath() . $sIcon; 
		if (file_exists($sIconObject) && !is_dir($sIconObject)) {
			unlink( $sIconObject );
		} 
 
		return true;
	}  
	//[end] logo modification


}
