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
 * Jobs module Data
 */
class BxJobsDb extends BxDolTwigModuleDb {	

	/*
	 * Constructor.
	 */
	function BxJobsDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);

		$this->_oConfig = $oConfig;

        $this->_sTableMain = 'main';
        $this->_sTableApply = 'apply';
        $this->_sTableCompanies = 'companies';
		
		// Freddy integration favorite
		$this->_sTableFavorite = 'favorites';
 
        $this->_sTableMediaPrefix = '';
        $this->_sFieldId = 'id';

        $this->_sFieldAuthorId = 'author_id';
        $this->_sFieldUri = 'uri';
        $this->_sFieldTitle = 'title'; 
        $this->_sFieldDescription = 'desc';

        $this->_sFieldCompanyAuthorId = 'company_author_id';
        $this->_sFieldCompanyUri = 'company_uri';
        $this->_sFieldCompanyTitle = 'company_name'; 
        $this->_sFieldCompanyDescription = 'company_desc';
        $this->_sFieldCompanyStatus = 'status';

        $this->_sFieldTags = 'tags';
        $this->_sFieldThumb = 'thumb';
        $this->_sFieldStatus = 'status';
        $this->_sFieldFeatured = 'featured';
        $this->_sFieldDailyJob = 'today_job';
        $this->_sFieldDailyCompany = 'today_company';

        $this->_sFieldCreated = 'created';
        $this->_sFieldJoinConfirmation = 'join_confirmation';
        $this->_sFieldFansCount = 'fans_count';
        $this->_sTableFans = 'fans';
        $this->_sTableApplicants = 'apply';

        $this->_sTableAdmins = 'admins';
        $this->_sFieldAllowViewTo = 'allow_view_job_to'; 
        $this->_sFieldCompanyAllowViewTo = 'allow_view_company_to';

	}
	
	// FREDDY ajout
	
	 function getProfile($iId)
    {
    	return $this->getRow("SELECT * FROM `Profiles` WHERE `ID` =".$iId);
    }
	
	
	
	// Freddy Postuler le candidation doit remploir au moins une experience dans son profil
	
		
	 function count_plinkin_skills($iProfileId){
	   
		return $this->getOne ("SELECT COUNT(`user_skill_id`) FROM `abs_plinkin_user_skills` WHERE `skill_user_id`='$iProfileId' "); 
		
	}
	
	
	
	
	 function count_plinkin_experience($iProfileId){
	   
		return $this->getOne ("SELECT COUNT(`exp_id`) FROM `abs_plinkin_experience` WHERE `exp_user_id`='$iProfileId' "); 
		
	}
	 function count_plinkin_education($iProfileId){
	   
		return $this->getOne ("SELECT COUNT(`edu_id`) FROM `abs_plinkin_education` WHERE `edu_user_id`='$iProfileId' "); 
		
	}

	 function plinkin_headline_postuler($iProfileId){
	   
		return $this->getOne ("SELECT `abs_plinkin_user_title` FROM `Profiles` WHERE `ID`='$iProfileId' "); 
		
	}
	//////////////////////////////////////////////////////////////////////////
	
	
	
	//Freddy  Business Location  --- Business Listing
	function getBusinessState($iAuthorId=0) {
  
        
         $aBusinessstate = $this->getOne ("SELECT  `state` FROM `modzzz_listing_main` WHERE `status` = 'approved' AND `author_id` = '$iAuthorId' LIMIT 1");
 
     return $aBusinessstate;
        
    }
	///
	function getBusinessCountry($iAuthorId=0) {
  
        
         $aBusinesscountry = $this->getOne ("SELECT  `country` FROM `modzzz_listing_main` WHERE `status` = 'approved' AND `author_id` = '$iAuthorId' LIMIT 1");
 
     return $aBusinessscountry;
        
    }
	///
	
	function getBusinessCity($iAuthorId=0) {
         $aBusinessCity = $this->getOne ("SELECT  `city` FROM `modzzz_listing_main` WHERE `status` = 'approved' AND `author_id` = '$iAuthorId' LIMIT 1");
       return $aBusinessCity; }
	
	///
	function getBusinessZip($iAuthorId=0) {
      $aBusinessZip = $this->getOne ("SELECT  `zip` FROM `modzzz_listing_main` WHERE `status` = 'approved' AND `author_id` = '$iAuthorId' LIMIT 1");
      return $aBusinessZip;
	 }
	 
	 function getBusinessEmail($iAuthorId=0) {
      $aBusinessEmail = $this->getOne ("SELECT  `businessemail` FROM `modzzz_listing_main` WHERE `status` = 'approved' AND `author_id` = '$iAuthorId' LIMIT 1");
      return $aBusinessEmail;
	 }
	 
	 
	 /////////////////////////////////////////////////////////////////////////////////////////////////////////


	
	 // Freddy nombre des jobs favorite et candidatures  pour un membre
   function CountFavoriteMember($iProfileId){
	    $iProfileId = (int)$iProfileId;
		return $this->getOne ("SELECT COUNT(`id_entry`) FROM `" . $this->_sPrefix . $this->_sTableFavorite. "` WHERE `id_profile`=$iProfileId LIMIT 1"); 
	
	} 
	
	function CountJobApplyMember($iProfileId){
	    $iProfileId = (int)$iProfileId;
		return $this->getOne ("SELECT COUNT(`job_id`) FROM `" . $this->_sPrefix . "apply`  WHERE `member_id`=$iProfileId LIMIT 1"); 
	
	} 

   //////////////////////////////////////////////////
	
	// freddy recruteur
	
	function MembreBusinessLsting($iAuthorId){ 
		return $this->getRow("SELECT * FROM `modzzz_listing_main` WHERE `author_id`= '$iAuthorId' AND `status`='approved'");  
	}
	
	function CountMembreBusinessLsting($iAuthorId){ 
		return $this->getOne("SELECT COUNT(`id`) FROM `modzzz_listing_main` WHERE `author_id`= '$iAuthorId' AND `status`='approved'");  
	}
	
	function getRecruteurApplicationCount ($iProfileId ) {
        return $this->getOne ("SELECT  COUNT(`a`.`job_id`) FROM  `modzzz_jobs_apply` AS  `a` RIGHT JOIN  `modzzz_jobs_main` AS  `j` ON  `a`.`job_id` =  `j`.`id`  and `j`.`author_id` = '$iProfileId'");
    }
	
	function getRecruteur () {
        return $this->getAll ("SELECT DISTINCT `b`.`icon` , `b`.`id` ,`b`.`title`,`b`.`uri`, `b`.`author_id`,`b`.`thumb`,`b`.`city`,`b`.`zip`,`b`.`country`,`b`.`state`,`b`.`employees_count`,`b`.`category_id`,`b`.`parent_category_id` FROM  `modzzz_listing_main` AS  `b` INNER JOIN  `modzzz_jobs_main` AS  `j` ON  `b`.`id` =  `j`.`company_id` 
		ORDER BY `j`.`created` DESC LIMIT 15");
    }
	
	function getGoToYourSpace($iAuthorId){ 
		return $this->getOne("SELECT COUNT(`id`) FROM `modzzz_jobs_main` WHERE `author_id`= '$iAuthorId' AND `status`='approved'");  
	}
	function getCompanyCategoryById($iId){
		return $this->getRow("SELECT `id`,`name`,`uri` FROM `modzzz_listing_categ` WHERE `id` = '$iId'"); 
	}
    
	
	///////////// freddy integration classified , jobs, events
	function getBoonexEventsCount($iId){ 
		return $this->getOne("SELECT COUNT(`ID`) FROM `bx_events_main` WHERE `company_id`='$iId'  AND `Status`='approved'");  
	}
	
	
	
	 
	function getModzzzxJobsCount($iId){ 
		return $this->getOne("SELECT COUNT(`id`) FROM `modzzz_jobs_main` WHERE `company_id`='$iId' AND `status`='approved'");  
	}
	
		function getModzzzxClassifiedCount($iId){ 
		return $this->getOne("SELECT COUNT(`id`) FROM `modzzz_classified_main` WHERE `company_id`='$iId' AND `status`='approved'");  
	}
	///////////////////////[END] freddy integration
	// [END]recruteur
	


	
	// Freddy ajout 
	function getModzzzCountJobsEmploi($iAuthorId){ 
		return $this->getOne("SELECT COUNT(`id`) FROM `modzzz_jobs_main` WHERE `author_id`= '$iAuthorId' AND `post_type`='provider' AND `status`='approved' ");  
	}
	
	function getModzzzCountJobsStage($iAuthorId){ 
		return $this->getOne("SELECT COUNT(`id`) FROM `modzzz_jobs_main` WHERE `author_id`= '$iAuthorId' AND `post_type`='seeker' AND `status`='approved' ");  
	}
	

	function getBusinessJobsById($iAuthorId){ 
		$iAuthorId = (int)$iAuthorId;
		return $this->getAll ("SELECT `id`,`title`,`uri` FROM `modzzz_jobs_main` WHERE `company_type`='listing' AND `author_id`=$iAuthorId AND `post_type`='provider' AND `status`='approved' ORDER BY `created` DESC"); 
	}  
	
	function getBusinessStageById($iAuthorId){ 
		$iAuthorId = (int)$iAuthorId;
		return $this->getAll ("SELECT `id`,`title`,`uri` FROM `modzzz_jobs_main` WHERE `company_type`='listing' AND `author_id`=$iAuthorId AND `post_type`='seeker' AND `status`='approved' ORDER BY `created` DESC"); 
	}  
	////
	
	// Freddy ajout
	function getNumberList($iStart, $iEnd, $iIncrement=1){
	
		$aVals=array();
		$aVals[0] = ''; 
		for($iter=$iStart; $iter<=$iEnd; $iter+=$iIncrement)
		{
			$aVals[$iter] = $iter; 
		}

		return $aVals;
	}
	
	
	
	
		//Freddy 06/07/2015  Obtenir le nombre d'application par job
	function getApplicationsCount($iJob_id)
	{
		return (int)$this->getOne("SELECT COUNT(`id`) FROM `" . $this->_sPrefix . "apply` WHERE `job_id`='$iJob_id'");
	}
  
   
  
    function isJobAdmin($iEntryId, $iProfileId) {
        return $this->getOne ("SELECT `when` FROM `" . $this->_sPrefix . $this->_sTableAdmins . "` WHERE `id_entry` = '$iEntryId' AND `id_profile` = '$iProfileId' LIMIT 1");
    }

   function getCompanyName($iId) {
        return $this->getOne ("SELECT `company_name` FROM `" . $this->_sPrefix . $this->_sTableCompanies . "` WHERE  `id` = '$iId' LIMIT 1");
    }
 
    function getCompanyById($iId) {
        return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableCompanies . "` WHERE  `id` = '$iId' LIMIT 1");
    }

    function addJobAdmin($iEntryId, $aProfileIds) {
        if (is_array($aProfileIds)) {
            $iRet = 0;
            foreach ($aProfileIds AS $iProfileId)
                $iRet += $this->query ("INSERT IGNORE INTO `" . $this->_sPrefix . $this->_sTableAdmins . "` SET `id_entry` = '$iEntryId', `id_profile` = '$iProfileId', `when` = '" . time() . "'");
            return $iRet;
        } else {
            return $this->query ("INSERT IGNORE INTO `" . $this->_sPrefix . $this->_sTableAdmins . "` SET `id_entry` = '$iEntryId', `id_profile` = '$aProfileIds', `when` = '" . time() . "'");
        }
    }        

    function removeJobAdmin($iEntryId, $aProfileIds) {
		if (!$aProfileIds) 
			return false;
        if (is_array($aProfileIds)) {
            $s = implode (' OR `id_profile` = ', $aProfileIds);
            return $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableAdmins . "` WHERE `id_entry` = '$iEntryId' AND (`id_profile` = $s)");
        } else {
            return $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableAdmins . "` WHERE `id_entry` = '$iEntryId' AND `id_profile` = '$aProfileIds'");
        }
    }
   
    function getCompanyEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . "companies` WHERE `{$this->_sFieldId}` = $iId LIMIT 1");
    }

    function getCompanyEntryByIdAndOwner ($iId, $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `company_author_id` = '$iOwner' ";
        return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableCompanies . "` WHERE `{$this->_sFieldId}` = $iId $sWhere LIMIT 1");
    }
 
    function getCompanyEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . "companies` WHERE `company_uri` = '$sUri' LIMIT 1");
    }

   function deleteCompanyEntryByIdAndOwner ($iId, $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `company_author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableCompanies . "` WHERE `{$this->_sFieldId}` = $iId $sWhere LIMIT 1")))
            return false;
 
        return true;
    }        

    function markCompanyAsFeatured ($iId) {
        return $this->query ("UPDATE `" . $this->_sPrefix . $this->_sTableCompanies . "` SET `{$this->_sFieldFeatured}` = (`{$this->_sFieldFeatured}` - 1)*(`{$this->_sFieldFeatured}` - 1) WHERE `{$this->_sFieldId}` = $iId LIMIT 1");
    }
 
    function getCompanyList($iAuthorId=0) {
  
		 if($iAuthorId){
			$sExtraSQL = 'AND `company_author_id` = ' . $iAuthorId;
		 }

		 $aCompanies = $this->getAll ("SELECT `id`, `company_name` FROM `" . $this->_sPrefix . "companies` WHERE `status` = 'approved' {$sExtraSQL} ORDER BY `company_name`");

		 $arr = array();
		//Freddy
		 //$arr['0'] = _t('_modzzz_jobs_select_company');
		 foreach($aCompanies as $aEachCompany){
			$arr['job|'.$aEachCompany['id']] = $aEachCompany['company_name'];
		 }
  		  //Freddy
		// $arr['-99'] = _t('_modzzz_jobs_add_company');

		 return $arr;
    }

    function getMergedCompanyList($iAuthorId=0) {
  
        $arr = array();
          // Freddy
		 //$arr['0'] = ' ' ._t('_modzzz_jobs_select_company');

         $aCompanies = $this->getAll ("SELECT `id`, `company_name` FROM `" . $this->_sPrefix . "companies` WHERE `status` = 'approved' AND `company_author_id` = '$iAuthorId' ORDER BY `company_name`");

         foreach($aCompanies as $aEachCompany){
            $arr['job|'.$aEachCompany['id']] = $aEachCompany['company_name'];
         }
 
         /* Freddy suppression where WHERE `status` = 'approved' pour afficher un message de patience avant validation de Business listing
		 $aListings = $this->getAll ("SELECT `id`, `title` FROM `modzzz_listing_main` WHERE `status` = 'approved' AND `author_id` = '$iAuthorId' ORDER BY `title`");
		 */
		  $aListings = $this->getAll ("SELECT `id`, `title`, `status` FROM `modzzz_listing_main` WHERE  `author_id` = '$iAuthorId' ORDER BY `title`");

         foreach($aListings as $aEachListing){
            $arr['listing|'.$aEachListing['id']] = $aEachListing['title'];
         }
 
		 asort($arr);

		// Freddy 
		// $arr['-99'] = _t('_modzzz_jobs_add_company');

         return $arr;
    }
 

	/** state functions*/ 
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
		
		$aStates[''] = '';
		foreach ($aDbStates as $aEachState){
			$sState = $aEachState['State'];
			$sStateCode = $aEachState['StateCode'];
			
			$aStates[$sStateCode] = $sState;
  		} 
		return $aStates;
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
		$iParentId = (int)$this->getOne("SELECT `parent` FROM `" . $this->_sPrefix . "categ` WHERE `id` = '$iId'");
 
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
			$aFormatCats[$aEachCat['id']] = $aEachCat['name'];
		}

		return $aFormatCats;
	}

	function getCategoryInfoByUri ($sUri){ 
		return $this->getRow("SELECT `id`, `parent`, `name`, `uri` FROM `" . $this->_sPrefix . "categ` WHERE `active` = 1 AND `uri`='$sUri'"); 
	}
 
	function getCategoryUrl ($sUri){
		return BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'categories/'.$sUri;
	}

 	function getSubCategoryUrl ($sUri){
		return BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'subcategories/'.$sUri;
	}

	function getLocalCategoryCount($iParent, $sCountry='', $sState=''){
		
		if (!$GLOBALS['logged']['admin']){ 
			if ($GLOBALS['logged']['member']){ 
				$aProfile = getProfileInfo($_COOKIE['memberID']); 
				require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
				$aMembershipInfo = getMemberMembershipInfo($_COOKIE['memberID']); 
				$iMembershipId = $aMembershipInfo['ID']; 

			    //$sExtraCheck = " AND job.`membership_view_filter` IN ('', '$iMembershipId')"; 
			}else{
				//$sExtraCheck = "AND job.`membership_view_filter`=''";
			}
		}
 
		if($sCountry)
			$sExtraCheck .= " AND job.`country`='$sCountry'";

		if($sState)
			$sExtraCheck .= " AND job.`state`='$sState'";

		return  $this->getOne("SELECT count(job.`id`) FROM `" . $this->_sPrefix . "categ` cat LEFT JOIN `" . $this->_sPrefix . "main` job ON cat.id=job.`category_id` WHERE cat.`active` = 1 AND cat.`parent`=$iParent AND job.`status`='approved' {$sExtraCheck}"); 
	}
 
	function getParentCategoryCount($iParent){
		
		if (!$GLOBALS['logged']['admin']){ 
			if ($GLOBALS['logged']['member']){ 
				$aProfile = getProfileInfo($_COOKIE['memberID']); 
				require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
				$aMembershipInfo = getMemberMembershipInfo($_COOKIE['memberID']); 
				$iMembershipId = $aMembershipInfo['ID']; 

			    //$sExtraCheck = " AND job.`membership_view_filter` IN ('', '$iMembershipId')"; 
			}else{
				//$sExtraCheck = "AND job.`membership_view_filter`=''";
			}
		}
 
		return  $this->getOne("SELECT count(job.`id`) FROM `" . $this->_sPrefix . "categ` cat LEFT JOIN `" . $this->_sPrefix . "main` job ON cat.id=job.`category_id` WHERE cat.`active` = 1 AND cat.`parent`=$iParent AND job.`status`='approved' {$sExtraCheck}"); 
	}

	function getStateCount($sState){
		/*
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
 */
		return  $this->getOne("SELECT COUNT(main.`id`) FROM `" . $this->_sPrefix . "main`  main WHERE `state` = '$sState'  AND  `status`='approved' {$sExtraCheck}"); 
	}
  
	function getCategoryCount($iCategoryId){

		if (!$GLOBALS['logged']['admin']){ 
			if ($GLOBALS['logged']['member']){ 
				$aProfile = getProfileInfo($_COOKIE['memberID']); 
				require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
				$aMembershipInfo = getMemberMembershipInfo($_COOKIE['memberID']); 
				$iMembershipId = $aMembershipInfo['ID']; 

			    //$sExtraCheck = " AND job.`membership_view_filter` IN ('', '$iMembershipId')"; 
			}else{
				//$sExtraCheck = "AND job.`membership_view_filter`=''";
			}
		}

		return  $this->getOne("SELECT count(job.`id`) FROM `" . $this->_sPrefix . "categ` cat LEFT JOIN `" . $this->_sPrefix . "main` job ON cat.id=job.`category_id` WHERE cat.`active` = 1 AND cat.`id`=$iCategoryId AND job.`status`='approved' {$sExtraCheck}"); 
	}
 	
	function SaveCategory($iParent=0)
	{  
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

	function getJobCount($iCompanyId)
	{
		return (int)$this->getOne("SELECT COUNT(`id`) FROM `" . $this->_sPrefix . "main` WHERE `company_id`='$iCompanyId' AND `status`='approved'");
	}
 
 	function postCompany($iEntryId, $iProfileId) {

		$sCompanyName = trim(process_db_input($_POST['company_name'])); 
		$sCompanyUri = uriGenerate($sCompanyName, 'modzzz_jobs_companies', 'company_uri'); 
		$sCompanyDesc = process_db_input($_POST['company_desc']);
		$sCompanyAddress = process_db_input($_POST['company_address']);
 		$sCompanyCountry = process_db_input($_POST['company_country']);
		$sCompanyCity = process_db_input($_POST['company_city']);
		$sCompanyState = process_db_input($_POST['company_state']);
		$sCompanyZip = process_db_input($_POST['company_zip']);
		$sCompanyWebsite = process_db_input($_POST['company_website']);
		$sCompanyEmail = process_db_input($_POST['company_email']);
		$sCompanyTelephone = process_db_input($_POST['company_telephone']);
		$sCompanyFax = process_db_input($_POST['company_fax']);
		$sCompanyEmployeeCount = process_db_input($_POST['employee_count']);
		$sCompanyOfficeCount = process_db_input($_POST['office_count']);
		//$sCompanyBusinessType = process_db_input($_POST['business_type']);
	  
		if(!$sCompanyName)
			 return;

		$iCompanyCreated = time();

		$this->query("INSERT INTO `" . $this->_sPrefix . "companies`  SET  
			  `company_name`='$sCompanyName', 
			  `company_uri`='$sCompanyUri',
 			  `company_desc`='$sCompanyDesc',
			  `company_address`='$sCompanyAddress',
			  `company_country`='$sCompanyCountry',
			  `company_city`='$sCompanyCity',
			  `company_state`='$sCompanyState',
			  `company_zip`='$sCompanyZip',
			  `company_website`='$sCompanyWebsite',
			  `company_email`='$sCompanyEmail',
			  `company_telephone`='$sCompanyTelephone', 
			  `company_fax`='$sCompanyFax',  
			  `company_author_id`='$iProfileId', 
			  `employee_count`='$sCompanyEmployeeCount', 
			  `office_count`='$sCompanyOfficeCount',  
			  `created`='$iCompanyCreated' 
			"); 
  
		$iCompanyId = (int)$this->lastId();

		if($iCompanyId){
			$this->query("UPDATE `" . $this->_sPrefix . "main`  SET  `company_id`=$iCompanyId
						  WHERE `id`=$iEntryId");
		}

		return $iCompanyId;  
	}

 	function companyUpdate($iEntryId) {

		$iEntryId = (int)$iEntryId;

		$sCompanyName = process_db_input($_POST['company_name']); 
 		$sCompanyDesc = process_db_input($_POST['company_desc']);
		$sCompanyAddress = process_db_input($_POST['company_address']);
 		$sCompanyCountry = process_db_input($_POST['company_country']);
		$sCompanyCity = process_db_input($_POST['company_city']);
		$sCompanyState = process_db_input($_POST['company_state']);
		$sCompanyZip = process_db_input($_POST['company_zip']);
		$sCompanyWebsite = process_db_input($_POST['company_website']);
		$sCompanyEmail = process_db_input($_POST['company_email']);
		$sCompanyTelephone = process_db_input($_POST['company_telephone']); 
		$sCompanyFax = process_db_input($_POST['company_fax']); 
		$sCompanyEmployeeCount = process_db_input($_POST['employee_count']);
		$sCompanyOfficeCount = process_db_input($_POST['office_count']);
		//$sCompanyBusinessType = process_db_input($_POST['business_type']);

 
		return db_res("UPDATE `" . $this->_sPrefix . "companies`  SET  
			  `company_name`='$sCompanyName', 
  			  `company_desc`='$sCompanyDesc',
			  `company_address`='$sCompanyAddress',
			  `company_country`='$sCompanyCountry',
			  `company_city`='$sCompanyCity',
			  `company_state`='$sCompanyState',
			  `company_zip`='$sCompanyZip',
			  `company_website`='$sCompanyWebsite',
			  `company_email`='$sCompanyEmail',
			  `company_telephone`='$sCompanyTelephone', 
			  `company_fax`='$sCompanyFax', 
			  `employee_count`='$sCompanyEmployeeCount', 
			  `office_count`='$sCompanyOfficeCount'   
			   WHERE `id`=$iEntryId
			");   
	}
 

	function getCompanyIcon($iId, $sName, $bUrlOnly=false, $bLarge=false ) {
	 
		if($bLarge){
			$iWidth = getParam("modzzz_jobs_company_image_width");
			$iWidth = getParam("modzzz_jobs_company_image_height"); 
		}else{ 
			$iWidth = getParam("modzzz_jobs_company_icon_width");
			$iWidth = getParam("modzzz_jobs_company_icon_height"); 
		}

		$sName = ($sName) ? $sName : 'default.jpg';  
		$sName = ($bLarge) ? 'large_'.$sName : $sName; 
		$sFileUrl = $this->_oConfig->getCompanyIconsUrl() . $sName ;
		$sFilePath = $this->_oConfig->getCompanyIconsPath() . $sName ;
		
		if($bUrlOnly){ 
			return $sFileUrl;
		}
 
		if(!file_exists($sFilePath))
			return;

		$sIcon = "<img id='img_{$iId}' src='{$sFileUrl}' width='$iWidth' height='$iHeight'>";  
	  
		return $sIcon;
	}
  
	 
	function UpdateCompanyMedia($iCompanyId, $sIcon) {
 
		$this->query("UPDATE `" . $this->_sPrefix . "companies` SET `company_icon`='$sIcon' WHERE `id`='$iCompanyId'"); 
	}
  
    function getCompanyBroadcastRecipients ($iEntryId) {
        return $this->getAll ("SELECT DISTINCT `p`.`ID`, `p`.`Email` FROM `" . $this->_sPrefix . $this->_sTableFans . "` AS `f` INNER JOIN `Profiles` as `p` ON (`f`.`id_profile` = `p`.`ID` AND `f`.`confirmed` = 1 AND `p`.`Status` = 'Active')
		INNER JOIN  `" . $this->_sPrefix . $this->_sTableMain . "` as main ON `f`.`id_entry` = main.`id` AND  main.`company_id`='$iEntryId' 
		");
    }   

    function getDailyJobItem () {
        $sWhere = " AND `{$this->_sFieldDailyJob}` = '1' ";   
        return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `{$this->_sFieldStatus}` = 'approved' AND `{$this->_sFieldAllowViewTo}` = '" . BX_DOL_PG_ALL . "' $sWhere ORDER BY `{$this->_sFieldCreated}` DESC LIMIT 1");
    }

    function getDailyCompanyItem () {
        $sWhere = " AND `{$this->_sFieldDailyCompany}` = '1' ";   
        return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableCompanies . "` WHERE `{$this->_sFieldStatus}` = 'approved' AND `{$this->_sFieldCompanyAllowViewTo}` = '" . BX_DOL_PG_ALL . "' $sWhere ORDER BY `{$this->_sFieldCreated}` DESC LIMIT 1");
    }

	function markAsToday ($iId) {
		
		$this->query ("UPDATE `" . $this->_sPrefix . $this->_sTableMain . "` SET `{$this->_sFieldDailyJob}` = 0 WHERE `{$this->_sFieldId}` != $iId");
		
		return $this->query ("UPDATE `" . $this->_sPrefix . $this->_sTableMain . "` SET `{$this->_sFieldDailyJob}` = (`{$this->_sFieldDailyJob}` - 1)*(`{$this->_sFieldDailyJob}` - 1) WHERE `{$this->_sFieldId}` = $iId LIMIT 1");   
    }
 
	function saveApplication($iProfileId, $iEntryId, $sMessage,$sEmail, $sTelephone, $sResume, $iResumeId=0){
		
		$iTime = time();
		
		// Freddy add Email and Telephone
		$this->query ("INSERT INTO `" . $this->_sPrefix . $this->_sTableApply . "` SET `member_id`=$iProfileId, `job_id` =$iEntryId,  `resume_id` =$iResumeId, `letter`='$sMessage', `resume`='$sResume',  `telephone`='$sTelephone' , `Email`='$sEmail', `created`=$iTime"); 
	}
 
    function getApplication($iResumeId) {
      
        return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableApply . "` WHERE `id` = '$iResumeId'"); 
    }
 
    function getApplications(&$aProfiles, $iEntryId, $iStart, $iMaxNum) {
      
        $aProfiles = $this->getAll ("SELECT SQL_CALC_FOUND_ROWS * FROM `" . $this->_sPrefix . $this->_sTableApply . "` WHERE `job_id` = '$iEntryId'  ORDER BY `created` DESC LIMIT $iStart, $iMaxNum");
        return $this->getOne("SELECT FOUND_ROWS()");
    }

	
	
	function hasApplied($iProfileId, $iEntryId){
		
		return $this->getOne ("SELECT COUNT(`member_id`) FROM `" . $this->_sPrefix . $this->_sTableApply . "` WHERE `member_id`='$iProfileId' AND `job_id` ='$iEntryId'");   
	}
 
	function getResumeLink($sResume){
		return $this->_oConfig->getResumeUrl() . $sResume;
	}
   
	function removeApplications($iEntryId, $iAppId=0){
		
		$sResumePath = $this->_oConfig->getResumePath();

		if($iAppId)
			$sQuery = 'AND `id`='. $iAppId;
		
		$aEntries = $this->getAll ("SELECT `resume` FROM `" . $this->_sPrefix . $this->_sTableApply . "` WHERE `job_id`=$iEntryId {$sQuery}"); 

		foreach($aEntries as $aEachEntry){
			$sResumeIcon = $sResumePath . trim($aEachEntry['resume']);

			if (file_exists($sResumeIcon) && !is_dir($sResumeIcon)) {
				@unlink( $sResumeIcon ); 
			}  
		}

		$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableApply . "` WHERE `job_id`=$iEntryId {$sQuery}");   
	}


    function deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin) {
        if ($iRet = parent::deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin)) {
            $this->query ("DELETE FROM `" . $this->_sPrefix . "fans` WHERE `id_entry` = $iId");
            $this->query ("DELETE FROM `" . $this->_sPrefix . "admins` WHERE `id_entry` = $iId");
 			 
			$this->removeApplications($iId);  
			$this->deleteEntryMediaAll ($iId, 'images');
            $this->deleteEntryMediaAll ($iId, 'videos');
            $this->deleteEntryMediaAll ($iId, 'sounds');
            $this->deleteEntryMediaAll ($iId, 'files');

            $this->query ("DELETE FROM `" . $this->_sPrefix . "invoices` WHERE `job_id` = $iId");
            $this->query ("DELETE FROM `" . $this->_sPrefix . "orders` WHERE `buyer_id` = $iOwner");
            $this->query ("DELETE FROM `" . $this->_sPrefix . "featured_orders` WHERE `buyer_id` = $iOwner"); 
           
		   // Freddy favorite integration
			$this->removeFavorite($iOwner, $iId); //remove favorite
		   
		   
			$this->removeYoutubeEntries($iId);  
        }
        return $iRet;
    }
  
	function updateFreeEntryExpired($iId){
		$iId = (int)$iId;
		$iTime = time(); 
  
		$this->query("UPDATE `" . $this->_sPrefix . "main` SET `status`='expired', `expiry_date`=$iTime WHERE `id` = {$iId}"); 
	}

	function getAllExpiredEntries(){
		
		$iTime = time(); 
  		$SECONDS_IN_DAY = 86400; 

		$iExpireDays = (int)getParam("modzzz_jobs_free_expired"); 

		$iExpireTime = $SECONDS_IN_DAY * $iExpireDays; 
		$iThresholdTime = $iTime - $iExpireTime;

		$aEntries = $this->getAll("SELECT `id`, `author_id` FROM `" . $this->_sPrefix . "main` WHERE  `status`='approved' AND `created` <= {$iThresholdTime}"); 
 
		return $aEntries;
	}
 
	function processExpiringJobs() { 
 
 		$iTime = time();
 		$SECONDS_IN_DAY = 86400; 
		 
  		$iExpireDays = (int)getParam("modzzz_jobs_free_expired"); 
		$iExpireTime = $SECONDS_IN_DAY * $iExpireDays; 

		$iNumNotifyDays = (int)getParam("modzzz_jobs_email_expiring");
		if($iNumNotifyDays){
			$iNotifyTime = $SECONDS_IN_DAY * $iNumNotifyDays;
			$iTriggerTime = $iTime - $iExpireTime + $iNotifyTime;

			$aJobs = $this->getAll("SELECT `id`, `author_id` FROM `" . $this->_sPrefix . "main` WHERE `status`='approved' AND  `created` <= {$iTriggerTime} AND `pre_expire_notify`=0");
  
			foreach ($aJobs as $aEachJob) {
				$iEntryId = $aEachJob['id'];
				$iRecipientId = $aEachJob['author_id'];
		  
				if(getParam("modzzz_jobs_activate_expiring")=='on') { 
					$this->alertOnAction('modzzz_jobs_expiring', $iEntryId, $iRecipientId, $iNumNotifyDays);
				}

				$this->query("UPDATE `" . $this->_sPrefix . "main` SET `pre_expire_notify`=1 WHERE `id` = $iEntryId");

			}
		} 
 
	}
   
	function getJobApplicants ($iEntryId) {
        return $this->getAll ("SELECT DISTINCT `p`.`ID`, `p`.`Email` FROM `" . $this->_sPrefix . $this->_sTableApplicants . "` AS `app` INNER JOIN `Profiles` as `p` ON (`app`.`job_id` = '$iEntryId' AND `app`.`member_id` = `p`.`ID` AND `p`.`Status` = 'Active')");
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
 
	//[begin] - paid jobs 
	function setItemStatus($iItemId, $sStatus) {
		
 		 $this->query("UPDATE `" . $this->_sPrefix . "main` SET `status`='$sStatus' WHERE `id`='$iItemId'"); 
	}

	function setInvoiceStatus($iItemId, $sStatus) {
		
 		 $this->query("UPDATE `" . $this->_sPrefix . "invoices` SET `invoice_status`='$sStatus' WHERE `job_id`='$iItemId'"); 
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

	function getPackageByEntryId($iEntryId){
  
		$aEntry = $this->getEntryById($iEntryId);
		$sInvoiceNo = $aEntry['invoice_no'];

 		$aInvoice = $this->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];
 
		return $this->getPackageById($iPackageId);  
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
		
		if (getParam('modzzz_jobs_paid_active')!='on') 
            return true;	

 		$aInvoice = $this->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];

 		$aPackage = $this->getPackageById($iPackageId);	

		return (int)$aPackage['videos'];
	}

	function isPackageAllowedPhotos($sInvoiceNo) {
		
		if (getParam('modzzz_jobs_paid_active')!='on') 
            return true;	

 		$aInvoice = $this->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];
 		$aPackage = $this->getPackageById($iPackageId);
 		 
		return (int)$aPackage['photos'];
	}

	function isPackageAllowedSounds($sInvoiceNo) {
		
		if (getParam('modzzz_jobs_paid_active')!='on') 
            return true;	

 		$aInvoice = $this->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];

 		$aPackage = $this->getPackageById($iPackageId);

		return (int)$aPackage['sounds'];
	}

	function isPackageAllowedFiles($sInvoiceNo) {
		
		if (getParam('modzzz_jobs_paid_active')!='on') 
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
   
    function saveTransactionRecord($iBuyerId, $iJobId, $sTransNo, $sTransType) {
        $iBuyerId    = (int)$iBuyerId;
        $iJobId  = (int)$iJobId; 
   
		$aDataEntry = $this->getEntryById($iJobId);
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
 
		 //if order successful, update expiration date of job
		 if($iOrderId){
			$aInvoice = $this->getInvoiceByNo($sInvoiceNo, false);
			$iDays = (int)$aInvoice['days'];
			$this->updateEntryExpiration($iJobId, $iDays);

			$iPackageId = (int)$aInvoice['package_id'];
			$aPackage = $this->getPackageById($iPackageId); 
			$iFeatured = (int)$aPackage['featured'];
			if($iFeatured){
				$this->updateFeaturedStatus($iJobId);
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

		 $iInvoiceActiveDays = (int)getParam('modzzz_jobs_invoice_valid_days');
 		 if($iInvoiceActiveDays) {
			 $SECONDS_IN_DAY = 86400; 
			 $iCreated = time();
			 $iExpireDate = $iCreated + ($SECONDS_IN_DAY * $iInvoiceActiveDays);
		 }

		 $this->query("INSERT INTO `" . $this->_sPrefix . "invoices` 
							SET `invoice_no` = '{$sInvoiceNo}',  
							`price` = {$fPrice},
							`days` = {$iDays},
							`job_id` = {$iEntryId},
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
 
	function updateFeaturedStatus($iEntryId) {  
		$this->query("UPDATE `" . $this->_sPrefix . "main` SET `featured`=1 WHERE `id`=$iEntryId");
	}

    function updateEntryInvoice($iEntryId, $sInvoiceNo) { 
        return $this->query("UPDATE `" . $this->_sPrefix . "main` SET `invoice_no` = '$sInvoiceNo' WHERE `id` = '{$iEntryId}'"); 
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

        return $this->getRow("SELECT `invoice_no`, `price`, `days`, `job_id`, `package_id`, `invoice_status`, `invoice_due_date`, `invoice_expiry_date`, `invoice_date` FROM `" . $this->_sPrefix . "invoices` WHERE `invoice_no` = '{$sInvoiceNo}' {$sStatusCheck}"); 
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

	/*featured functions*/ 
    function saveFeaturedTransactionRecord($iBuyerId, $iJobId, $iQuantity, $fPrice, $sTransId, $sTransType) {
        $iBuyerId    = (int)$iBuyerId;
        $iJobId  = (int)$iJobId; 
        $iQuantity  = (int)$iQuantity; 
		$iTime = time();

		$aDataEntry = $this->getEntryById($iJobId);
   
        $bProcessed = $this->query("INSERT INTO `" . $this->_sPrefix . "featured_orders` 
							SET `buyer_id` = {$iBuyerId}, 
							`price` = {$fPrice},
							`days` = {$iQuantity},
							`item_id` =  {$iJobId},
 							`trans_id` = '{$sTransId}',  
							`trans_type` = '{$sTransType}', 
  							`created` = $iTime  
        "); 
 
		if($bProcessed){
			$this->alertOnAction('modzzz_jobs_featured_admin_notify', $iJobId, $iBuyerId, $iQuantity, true);
  
			$this->alertOnAction('modzzz_jobs_featured_buyer_notify', $iJobId, $iBuyerId, $iQuantity);
		}

		return $bProcessed; 
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

	function processJobs(){ 
		$this->processExpiredJobs();
			
		$this->processExpiredInvoices();
 
		$this->processFeaturedJob(); 
	}
 
	function processExpiredInvoices() { 
 
	    $iExpireTime = time();
 
		$aJob = $this->getAll("SELECT `job_id` FROM `" . $this->_sPrefix . "invoices` WHERE `invoice_status`='pending' AND `invoice_expiry_date`>0 AND `invoice_expiry_date`<={$iExpireTime}");
		 
		foreach ($aJob as $aEachJob) {
			$iEntryId = (int)$aEachJob['job_id']; 
 
			$this->query("DELETE FROM `" . $this->_sPrefix . "main` WHERE `id`=$iEntryId");  
			
			$this->query("DELETE FROM `" . $this->_sPrefix . "invoices` WHERE `job_id`=$iEntryId");  
 		}  
 
	}
 
	function processExpiredJobs() { 
 
 		$iTime = time();
 		$SECONDS_IN_DAY = 86400; 
		
        $bFreeListing = (getParam('modzzz_jobs_paid_active')=='on') ? false : true;  
        $iNumActiveDays = (int)getParam("modzzz_jobs_free_expired");
        if($bFreeListing && (!$iNumActiveDays))
            return; 

		if(getParam('modzzz_jobs_activate_expired') == 'on') {
  			 
			$aJob = $this->getAll("SELECT `id`, `expiry_date`, `author_id` FROM `" . $this->_sPrefix . "main` WHERE `status`='approved' AND `featured`=0 AND `expiry_date`>0 AND `expiry_date`<={$iTime}");
			 
			foreach ($aJob as $aEachJob) {
				$iEntryId = (int)$aEachJob['id']; 
				$iRecipientId = (int)$aEachJob['author_id']; 

				$this->query("UPDATE `" . $this->_sPrefix . "main` SET `status`='expired'  WHERE `id`=$iEntryId");  
 
		 		$this->alertOnAction('modzzz_jobs_expired', $iEntryId, $iRecipientId);
			}  
		}else{ 
  			$this->query("UPDATE `" . $this->_sPrefix . "main` SET `Status`='expired' WHERE  `Status`='approved' AND `expiry_date`>0 AND `expiry_date`<=$iTime");  
		}
  
		$iNumNotifyDays = (int)getParam("modzzz_jobs_email_expiring");
		if($iNumNotifyDays){
			$iNotifyTime = $SECONDS_IN_DAY * $iNumNotifyDays;
			$iTriggerTime = $iTime - $iNotifyTime;

			$aJob = $this->getAll("SELECT `id`, `expiry_date`, `author_id` FROM `" . $this->_sPrefix . "main` WHERE `status`='approved' AND `expiry_date` >= {$iTriggerTime} AND `expiry_date` < {$iTime}  AND `pre_expire_notify`=0");
			 
			foreach ($aJob as $aEachJob) {
				$iEntryId = $aEachJob['id'];
				$iRecipientId = $aEachJob['author_id'];
		 
				$this->query("UPDATE `" . $this->_sPrefix . "main` SET `pre_expire_notify`=1 WHERE `id` = $iEntryId");

				if(getParam("modzzz_jobs_activate_expiring")=='on') { 
					$this->alertOnAction('modzzz_jobs_expiring', $iEntryId, $iRecipientId, $iNumNotifyDays);
				}
			}
		}
 			
		$iNumNotifyDays = (int)getParam("modzzz_jobs_email_expired");
		if($iNumNotifyDays){
			$iNotifyTime = $SECONDS_IN_DAY * $iNumNotifyDays;
			$iTriggerTime = $iTime - $iNotifyTime;

			$aJob = $this->getAll("SELECT `id`, `expiry_date`, `author_id` FROM `" . $this->_sPrefix . "main` WHERE `status`='expired' AND `expiry_date` <= {$iNotifyTime} AND `post_expire_notify`=0");
			 
			foreach ($aJob as $aEachJob) {
				$iEntryId = $aEachJob['id']; 
				$iRecipientId = $aEachJob['author_id'];
			 
				$this->query("UPDATE `" . $this->_sPrefix . "main` SET `post_expire_notify`=1 WHERE `id` = $iEntryId");
				
				if(getParam("modzzz_jobs_activate_expired")=='on') { 
					$this->alertOnAction('modzzz_jobs_post_expired', $iEntryId, $iRecipientId, $iNumNotifyDays);
				}
			}
		}
 
	}
 
	function processFeaturedJob(){
		
		if(getParam('modzzz_jobs_buy_featured') != 'on') return;
			 
		$iTime = time();
   
        $aJob = $this->getAll("SELECT `id`, `author_id`, `featured`, `featured_expiry_date` FROM `" . $this->_sPrefix . "main` WHERE `featured`=1 AND `featured_expiry_date`>0 AND `featured_expiry_date` <= $iTime"); 

		foreach($aJob as $aEachList){
  
			$iJobId = (int)$aEachList['id'];
			$iRecipientId = (int)$aEachList['author_id'];

			$this->alertOnAction('modzzz_jobs_featured_expire_notify', $iJobId, $iRecipientId  );
		
	        $this->query("UPDATE `" . $this->_sPrefix . "main` SET `featured`=0, `featured_expiry_date`=0, `featured_date`=0  WHERE `id`=$iJobId"); 
		}

	}

	function alertOnAction($sTemplate, $iJobId, $iRecipientId=0, $iDays=0, $bAdmin=false) {
	   
		$aPlus = array();

		if($iJobId){
			$aDataEntry = $this->getEntryById($iJobId);
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


	function getResumeList($iAuthorId){
		$iAuthorId = (int)$iAuthorId;
	
		$aList = $this->getAll("SELECT `id`, `title` FROM `modzzz_resume_main` WHERE `status`='approved' AND `author_id`=$iAuthorId");  
		
		$aComboList = array();
		$aComboList[0] = _t('_Select');
		foreach($aList as $aEachList){ 
			$aComboList[$aEachList['id']] = $aEachList['title']; 
		}

		return $aComboList; 
	}

	function getJobList($iAuthorId){
		$iAuthorId = (int)$iAuthorId;

		$aList = $this->getAll("SELECT `id`, `title` FROM `modzzz_jobs_main` WHERE `post_type`='provider' AND `status`='approved' AND `author_id`=$iAuthorId");  
		
		$aComboList = array();
		$aComboList[0] = _t('_Select');
		foreach($aList as $aEachList){
			$aComboList[$aEachList['id']] = $aEachList['title']; 
		}

		return $aComboList; 
	}
 
	function saveEmbed($iEntryId, $sEmbedCode){
		$iEntryId = (int)$iEntryId;

        return $this->query ("UPDATE `" . $this->_sPrefix . $this->_sTableMain . "` SET `video_embed` = '$sEmbedCode' WHERE `{$this->_sFieldId}` = $iEntryId"); 
	}
 
	function hasJobListings($iProfileId){
		$iProfileId = (int)$iProfileId;

		return (int)$this->getOne("SELECT COUNT(`id`) FROM `" . $this->_sPrefix . "main` WHERE `post_type`='provider' AND `status`='approved' AND `author_id`=$iProfileId");
	}
 
	function deductPayment($sAction, $sPaymentType, $iProfileId, $iEntryId, $iDays, $iPrice){
		//integration with points
		if($sPaymentType=="points") {  
			$oPoint = BxDolModule::getInstance('BxPointModule'); 
 			$oPoint->assignPoints($iProfileId, 'modzzz_jobs', $sAction, "subtract", time(), abs($iPrice)); 
		}
		
		//integration with credits
		if($sPaymentType=="credits") {  
			$oCredit = BxDolModule::getInstance('BxCreditModule'); 
 			$oCredit->assignCredits($iProfileId, 'modzzz_jobs', $sAction, "subtract", time(), abs($iPrice));  
		} 
		
		$iCreated = time();

		$this->query("INSERT INTO `" . $this->_sPrefix . "payment_track` SET `item_id`=$iEntryId, `action`='$sAction', `payment_type`='$sPaymentType', `days`=$iDays, `profile_id`=$iProfileId, `created`=$iCreated"); 
	}

	function isValidAccess($iEntryId, $iProfileId, $sAction){
	
		return $this->getOne("SELECT `id` FROM `" . $this->_sPrefix . "payment_track` WHERE `item_id`=$iEntryId AND `profile_id`=$iProfileId AND `action`='$sAction' AND `status`='active' LIMIT 1"); 
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
 
	function removeYoutubeEntry($iYoutubeId){ 
	   
		$this->query("DELETE FROM `" . $this->_sPrefix . "youtube` WHERE `id`='$iYoutubeId'");   
	}

    function getYoutubeIds ($iEntryId) {
	 
		return $this->getPairs ("SELECT `id` FROM `" . $this->_sPrefix . "youtube`  WHERE `id_entry` = '$iEntryId'", 'id', 'id'); 
    }

	function removeYoutubeEntries($iEntryId){ 
	   
		$this->query("DELETE FROM `" . $this->_sPrefix . "youtube` WHERE `id_entry`='$iEntryId'");   
	}
	//END Youtube

	//[begin] logo modification
	function getLogo($iId, $sName, $bUrlOnly=false, $bUseAsIcon=false){
 		$sUrl = $this->_oConfig->getMediaUrl() . $sName;

		if($bUseAsIcon){
			$iWidth = $iHeight = 240;

			return "<img width='{$iWidth}px' height='{$iHeight}px' src='{$sUrl}' class='bx-twig-unit-thumb bx-def-round-corners bx-def-shadow'>";  
		}else{
			$iWidth = getParam("modzzz_jobs_icon_width");
			$iHeight = getParam("modzzz_jobs_icon_height");
		}
  
		if($bUrlOnly)
			return $sUrl;
		else
			return "<img src='{$sUrl}' class='bx-twig-unit-thumb bx-def-round-corners bx-def-shadow'>";  
	}	
    
	
	/// Freddy ajout integration du logo de business listing
	function getLogoBusinessListing($iId, $sName, $bUrlOnly=false, $bUseAsIcon=false){
 		$sUrl = $this->_oConfig->getMediaLogoBusinessListingUrl() . $sName;

		if($bUseAsIcon){
			$iWidth = $iHeight = 240;

			return "<img width='{$iWidth}px' height='{$iHeight}px' src='{$sUrl}' class='bx-twig-unit-thumb bx-def-round-corners bx-def-shadow'>";  
		}else{
			$iWidth = getParam("modzzz_jobs_icon_width");
			$iHeight = getParam("modzzz_jobs_icon_height");
		}
  
		if($bUrlOnly)
			return $sUrl;
		else
			return "<img src='{$sUrl}' class='bx-twig-unit-thumb bx-def-round-corners bx-def-shadow'>";  
	}	
	//////////////////////////////////////////////////////
	
	
	
	
	function updatePostWithLogo($iEntryId, $sIcon='') { 
		$iEntryId = (int)$iEntryId;
		
		$this->query("UPDATE `" . $this->_sPrefix . "main` SET `icon`='$sIcon' WHERE `id`=$iEntryId");  
	}

	function _actionRemoveIcon($iEntryId) {
	    $iEntryId  = (int)$iEntryId;
	  
		$aDataEntry = $this->getEntryById($iEntryId); 
 		$sIcon = $aDataEntry['icon'];
 
		if(!$sIcon) return;

		$sIconObject = $this->_oConfig->getMediaPath() . $sIcon; 
		if (file_exists($sIconObject) && !is_dir($sIconObject)) {
			unlink( $sIconObject );
		} 
 
		return true;
	}  
	//[end] logo modification

}
 