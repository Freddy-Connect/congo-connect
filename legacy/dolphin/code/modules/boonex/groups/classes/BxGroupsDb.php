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
 * Groups module Data
 */
class BxGroupsDb extends BxDolTwigModuleDb {	

	/*
	 * Constructor.
	 */
	function BxGroupsDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);
 
		$this->_oConfig = $oConfig;


        $this->_sTableBanned = 'banned';
 
		/********Sponsor ************/
        $this->_sTableSponsor = 'sponsor_main';
        $this->_sSponsorPrefix = 'bx_groups_sponsor';
        $this->_sTableSponsorMediaPrefix = 'sponsor_';

		/********Blog ************/
        $this->_sTableBlog = 'blog_main';
        $this->_sBlogPrefix = 'bx_groups_blog';
        $this->_sTableBlogMediaPrefix = 'blog_';

		/********Event ************/
        $this->_sTableEvent = 'event_main';
        $this->_sEventPrefix = 'bx_groups_event';
        $this->_sTableEventMediaPrefix = 'event_';
 
		/********Venue ************/
        $this->_sTableVenue = 'venue_main';
        $this->_sVenuePrefix = 'bx_groups_venue';
        $this->_sTableVenueMediaPrefix = 'venue_';

		/********News ************/
        $this->_sTableNews = 'news_main';
        $this->_sNewsPrefix = 'bx_groups_news';
        $this->_sTableNewsMediaPrefix = 'news_';
 
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
        $this->_sFieldJoinConfirmation = 'join_confirmation';
        $this->_sFieldFansCount = 'fans_count';
        $this->_sTableFans = 'fans';
        $this->_sTableAdmins = 'admins';
        $this->_sFieldAllowViewTo = 'allow_view_group_to';
	}
	
	
///////////// freddy integration classified , jobs, events
	function getBoonexEventsCount($iId){ 
		return $this->getOne("SELECT COUNT(`ID`) FROM `bx_events_main` WHERE `group_id`='$iId'  AND `Status`='approved'");  
	}

    function deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin) {
        if ($iRet = parent::deleteEntryByIdAndOwner ($iId, $iOwner, $isAdmin)) {
            $this->query ("DELETE FROM `" . $this->_sPrefix . "fans` WHERE `id_entry` = $iId");
            $this->query ("DELETE FROM `" . $this->_sPrefix . "admins` WHERE `id_entry` = $iId");
            $this->deleteEntryMediaAll ($iId, 'images');
            $this->deleteEntryMediaAll ($iId, 'videos');
            $this->deleteEntryMediaAll ($iId, 'sounds');
            $this->deleteEntryMediaAll ($iId, 'files');

			$this->removeEntryYoutube($iId); 
 
            $this->query ("DELETE FROM `" . $this->_sPrefix . "featured_orders` WHERE `buyer_id` = $iOwner"); 

        }
        return $iRet;
    }
  
	function isValidInvite($iEntryId, $sCode=''){
  
		return (int)$this->getOne ("SELECT COUNT(`id`) FROM `" . $this->_sPrefix . "invite` WHERE `id_entry` = '$iEntryId' AND `code` = '$sCode'");
	}

	function isExistsInvite($iEntryId, $iProfileId=0){
 
		return (int)$this->getOne ("SELECT COUNT(`id`) FROM `" . $this->_sPrefix . "invite` WHERE `id_entry` = '$iEntryId' AND `id_profile` = '$iProfileId'");
	}

	function removeInvite($iEntryId, $iProfileId){
		$this->query ("DELETE FROM `" . $this->_sPrefix . "invite` WHERE `id_entry` = '$iEntryId' AND `id_profile` = '$iProfileId'");
	}

	function addInvite($iEntryId, $iProfileId=0){ 
		if(!$this->isExistsInvite($iEntryId, $iProfileId)){
			$sInviteCode = $this->_getCode();

			$this->query ("INSERT INTO `" . $this->_sPrefix . "invite` SET `code` = '$sInviteCode' ,`id_entry` = '$iEntryId', `id_profile` = '$iProfileId'");

			return $sInviteCode;
		}
	}

	function _getCode() {
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
 
 	function getStateCount($sState){
		
		if (!$GLOBALS['logged']['admin']){ 
			if ($GLOBALS['logged']['member']){ 
				$aProfile = getProfileInfo($_COOKIE['memberID']); 
				require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
				$aMembershipInfo = getMemberMembershipInfo($_COOKIE['memberID']); 
				$iMembershipId = $aMembershipInfo['ID']; 

			    $sExtraCheck = " AND `group_membership_view_filter` IN ('', '$iMembershipId')"; 
			}else{
				$sExtraCheck = "AND `group_membership_view_filter`=''";
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
 
	function getCategories($sType)
	{ 
 		$aAllEntries = $this->getAll("SELECT `Category` FROM `sys_categories` WHERE `Type` = '{$sType}' AND `Status`='active' AND Owner=0 ORDER BY `Category`"); 
		
		return $aAllEntries; 
	}

	function getLocalCategoryCount($sType, $sCategory, $sCountry='', $sState=''){
  
		if($sCountry)
			$sExtraCheck .= " AND `country`='$sCountry'";

		if($sState)
			$sExtraCheck .= " AND `state`='$sState'";
 
		$sCategory = process_db_input($sCategory); 
 
		$iNumCategory = $this->getOne("SELECT count(`" . $this->_sPrefix . "main`.`id`) FROM `" . $this->_sPrefix . "main`  inner JOIN `sys_categories` ON `sys_categories`.`ID`=`" . $this->_sPrefix . "main`.`id` WHERE `sys_categories`.`Category` IN('{$sCategory}') AND `sys_categories`.`Type` = '{$sType}' AND `" . $this->_sPrefix . "main`.`status`='approved' {$sExtraCheck}");
		
		return $iNumCategory;
	}
  
	function getCategoryCount($sType,$sCategory)
	{ 
		$sCategory = process_db_input($sCategory); 
		$iNumCategory = $this->getOne("SELECT count(`" . $this->_sPrefix . "main`.`id`) FROM `" . $this->_sPrefix . "main`  inner JOIN `sys_categories` ON `sys_categories`.`ID`=`" . $this->_sPrefix . "main`.`id` WHERE 1 AND  `sys_categories`.`Category` IN('{$sCategory}') AND `sys_categories`.`Type` = '{$sType}' AND `" . $this->_sPrefix . "main`.`status`='approved'"); 
		
		return $iNumCategory;
	}

	function flagActivity($sType, $iEntryId, $iProfileId, $aParams=array()){
 
		if(!$iEntryId)
			return;

		$aDataEntry = $this->getEntryById($iEntryId);
		
		if( !($aDataEntry[$this->_sFieldAllowViewTo]==BX_DOL_PG_ALL || $aDataEntry[$this->_sFieldAllowViewTo]==BX_DOL_PG_MEMBERS) )
			return;

		switch($sType){ 
			case 'mark_as_featured':
				foreach($aParams as $sKey=>$iValue){ 
					if($sKey=='Featured'){
						if($iValue)
							$sType = "unfeatured";
						else
							$sType = "featured"; 
					}
				}
			break; 
		}

		$aTypes = array(
			'add' => '_bx_groups_feed_post',
			'delete' => '_bx_groups_feed_delete',
			'change' => '_bx_groups_feed_post_change',
			'join' => '_bx_groups_feed_join',
			'unjoin' => '_bx_groups_feed_unjoin',
			'remove' => '_bx_groups_feed_remove',
			'rate' => '_bx_groups_feed_rate',
			'commentPost' => '_bx_groups_feed_comment',
			'featured' => '_bx_groups_feed_featured',
			'unfeatured' => '_bx_groups_feed_unfeatured',
			'makeAdmin' => '_bx_groups_feed_make_admin',
			'removeAdmin' => '_bx_groups_feed_remove_admin'  
		);
   
		$aDataEntry = $this->getEntryById($iEntryId);
		
		$sProfileNick = getNickName($iProfileId);
		$sProfileLink = getProfileLink($iProfileId);
		$sGroupUri = $aDataEntry['uri'];
		$sGroupTitle = process_db_input($aDataEntry['title']);
		$sGroupUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $sGroupUri;
	
		$sParams = "profile_id|{$iProfileId};profile_link|{$sProfileLink};profile_nick|{$sProfileNick};entry_url|{$sGroupUrl};entry_title|{$sGroupTitle}";

		$sLangKey = $aTypes[$sType];
 
		$this->query("INSERT INTO bx_groups_activity(`group_id`,`lang_key`,`params`,`type`) VALUES ($iEntryId,'$sLangKey','$sParams','$sType')");

	}

	function getActivityFeed($iLimit=5){
		$iLimit = ($iLimit) ? $iLimit : 5;
/*
		if(getLoggedId()){ 
			$sSQLExtra = " m.`".$this->_sFieldAllowViewTo."` = '". BX_DOL_PG_MEMBERS ."'";
		}
*/
		return $this->getAll("SELECT a.`group_id`,a.`lang_key`,a.`params`,a.`type`, UNIX_TIMESTAMP(a.`date`) AS `date` FROM `" . $this->_sPrefix . "activity` a INNER JOIN `" . $this->_sPrefix . "main` m ON a.`group_id`=m.`id` WHERE m.`".$this->_sFieldAllowViewTo."` = '". BX_DOL_PG_ALL ."' {$sSQLExtra} ORDER BY a.`date` DESC LIMIT $iLimit"); 
	}
  
	function getLatestComments($iLimit=5){

		$iLimit = ($iLimit) ? $iLimit : 5;

		return $this->getAll("SELECT `cmt_object_id`,`cmt_author_id`,`cmt_text`, UNIX_TIMESTAMP(`cmt_time`) AS `date` FROM `" . $this->_sPrefix . "cmts` cmt INNER JOIN `" . $this->_sPrefix . "main` m  ON cmt.`cmt_object_id`= m.`id` WHERE m.`allow_view_group_to` = '". BX_DOL_PG_ALL ."' AND m.`allow_view_comment_to` = '". BX_DOL_PG_ALL ."' AND `cmt_text` NOT LIKE '<object%' ORDER BY `cmt_time` DESC LIMIT $iLimit"); 
	}  

	function getLatestForumPosts($iLimit=5){

		$iLimit = ($iLimit) ? $iLimit : 5;
 
		return $this->getAll("SELECT e.`title`, f.`forum_uri`, p.`user`, p.`post_text`, t.`topic_uri`, t.`topic_title`,p.`when` FROM `" . $this->_sPrefix . "forum` f, `" . $this->_sPrefix . "forum_topic` t, `" . $this->_sPrefix . "forum_post` p, `" . $this->_sPrefix . "main` e WHERE p.`topic_id`=t.`topic_id` AND t.`forum_id`=f.`forum_id` AND e.`id`=f.`entry_id` AND e.`allow_view_group_to`='". BX_DOL_PG_ALL ."' AND e.`allow_view_forum_to`='". BX_DOL_PG_ALL ."'  ORDER BY  p.`when` LIMIT $iLimit");
	}  

	function getItemForumPosts($iLimit=5, $iEntryId=0){

		$iEntryId = (int)$iEntryId;

		if($iEntryId)
			$sQueryId = "t.`forum_id`=$iEntryId AND ";

		return $this->getAll("SELECT f.`forum_uri`, p.`user`, p.`post_text`, t.`topic_uri`, t.`topic_title`, p.`when` FROM `" . $this->_sPrefix . "forum` f, `" . $this->_sPrefix . "forum_topic` t, `" . $this->_sPrefix . "forum_post` p, `" . $this->_sPrefix . "main` e WHERE {$sQueryId}  p.`topic_id`=t.`topic_id` AND t.`forum_id`=f.`forum_id` AND e.`id`=f.`entry_id` ORDER BY  p.`when` LIMIT $iLimit"); 
	}  
 

	/***** SPONSOR **************************************/
    function getSponsorEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableSponsor . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getSponsorEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableSponsor . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteSponsorByIdAndOwner ($iId, $iGroupId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSponsor . "` WHERE `id` = $iId AND `group_id`=$iGroupId $sWhere LIMIT 1")))
            return false;
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSponsorMediaPrefix . "images` WHERE `entry_id` = $iId");
  
        return true;
    } 

    function deleteSponsors ($iGroupId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
       
		$aSponsor = $this->getAllSubItems('sponsor', $iGroupId);

		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSponsor . "` WHERE `group_id`=$iGroupId $sWhere")))
            return false;

		foreach($aSponsor as $aEachSponsor){
			
			$iId = (int)$aEachSponsor['id'];

			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSponsorMediaPrefix . "images` WHERE `entry_id` = $iId");
 		}

        return true;
    }

	/***** BLOG **************************************/
    function getBlogEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableBlog . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getBlogEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableBlog . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteBlogByIdAndOwner ($iId, $iGroupId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableBlog . "` WHERE `id` = $iId AND `group_id`=$iGroupId $sWhere LIMIT 1")))
            return false;
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableBlogMediaPrefix . "images` WHERE `entry_id` = $iId");
  
        return true;
    } 

    function deleteBlogs ($iGroupId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
       
		$aBlog = $this->getAllSubItems('blog', $iGroupId);

		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableBlog . "` WHERE `group_id`=$iGroupId $sWhere")))
            return false;

		foreach($aBlog as $aEachBlog){
			
			$iId = (int)$aEachBlog['id'];

			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableBlogMediaPrefix . "images` WHERE `entry_id` = $iId");
 		}

        return true;
    }
	
	/***** VENUE **************************************/
    function getVenueEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableVenue . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getVenueEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableVenue . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteVenueByIdAndOwner ($iId, $iGroupId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableVenue . "` WHERE `id` = $iId AND `group_id`=$iGroupId $sWhere LIMIT 1")))
            return false;
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableVenueMediaPrefix . "images` WHERE `entry_id` = $iId");
  
        return true;
    } 
 
    function deleteVenues ($iGroupId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
       
		$aVenue = $this->getAllSubItems('venue', $iGroupId);

		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableVenue . "` WHERE `group_id`=$iGroupId $sWhere")))
            return false;

		foreach($aVenue as $aEachVenue){
			
			$iId = (int)$aEachVenue['id'];

			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableVenueMediaPrefix . "images` WHERE `entry_id` = $iId");
 		}

        return true;
    }  


	/***** NEWS **************************************/
    function getNewsEntryById($iId) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getNewsEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteNewsByIdAndOwner ($iId, $iGroupId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `id` = $iId AND `group_id`=$iGroupId $sWhere LIMIT 1")))
            return false;
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableNewsMediaPrefix . "images` WHERE `entry_id` = $iId");
  
        return true;
    }
	
	function deleteNews ($iGroupId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
       
		$aNews = $this->getAllSubItems('news', $iGroupId);

		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `group_id`=$iGroupId $sWhere")))
            return false;

		foreach($aNews as $aEachNews){
			
			$iId = (int)$aEachNews['id'];

			$this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableNewsMediaPrefix . "images` WHERE `entry_id` = $iId");
 		}

        return true;
    }  

	/***** EVENT **************************************/
    function getEventEntryById($iId) {
		$iId = (int)$iId;

         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableEvent . "` WHERE `id` = $iId LIMIT 1");
    }
 
    function getEventEntryByUri($sUri) {
         return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableEvent . "` WHERE `uri` = '$sUri' LIMIT 1");
    }
 
    function deleteEventByIdAndOwner ($iId, $iGroupId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEvent . "` WHERE `id` = $iId AND `group_id`=$iGroupId $sWhere LIMIT 1")))
            return false;
        $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEventMediaPrefix . "images` WHERE `entry_id` = $iId");
  
        return true;
    } 

    function deleteEvents ($iGroupId,  $iOwner, $isAdmin) {
        $sWhere = '';
        if (!$isAdmin) 
            $sWhere = " AND `author_id` = '$iOwner' ";
       
		$aEvents = $this->getAllSubItems('event', $iGroupId);

		if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableEvent . "` WHERE `group_id`=$iGroupId $sWhere")))
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


	function getAllSubItems($sSubItem, $iGroupId){
		$aSubItems = array();
		$iGroupId = (int)$iGroupId;

		switch($sSubItem){
			case 'event':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableEvent . "` WHERE `group_id`=$iGroupId");
			break;
			case 'news':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableNews . "` WHERE `group_id`=$iGroupId");
			break;
 			case 'venue':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableVenue . "` WHERE `group_id`=$iGroupId");
			break; 
			case 'blog':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableBlog . "` WHERE `group_id`=$iGroupId");
			break; 
			case 'sponsor':
				$aSubItems = $this->getAll ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTableSponsor . "` WHERE `group_id`=$iGroupId");
			break; 
		}
 
		return $aSubItems;	
	}
   

	//BEGIN RSS 
	function getRss($iEntryId, $iLimit=0){

		if($iLimit)
			$sQuery = "LIMIT 0, {$iLimit}";

		return $this->getAll("SELECT `id`, `id_entry`, `url`, `name` FROM `" . $this->_sPrefix . "rss` WHERE `id_entry`={$iEntryId} {$sQuery}");
	}

 	function addRss($iEntryId){
		if(is_array($_POST['rsslink'])){ 
			foreach($_POST['rsslink'] as $iKey=>$sValue){
			
				$sRssLink = process_db_input($sValue);
				$sRssName = process_db_input($_POST['rsscaption'][$iKey]);
	 
				if(trim($sRssLink)){  
					$this->query("INSERT INTO `" . $this->_sPrefix . "rss` SET `id_entry`=$iEntryId, `url`='$sRssLink', `name`='$sRssName'");
				}
			} 
		}
	}

	function removeEntryRss($iEntryId){ 
	   
		$this->query("DELETE FROM `" . $this->_sPrefix . "rss` WHERE `id_entry`='$iEntryId'");   
	}

	function removeRss($iEntryId, $iFeedId){ 
	   
		$this->query("DELETE FROM `" . $this->_sPrefix . "rss` WHERE `id_entry`='$iEntryId' AND `id`='$iFeedId'");   
	}

    function getRssIds ($iEntryId) {
		return $this->getPairs ("SELECT `id` FROM `" . $this->_sPrefix . "rss`  WHERE `id_entry` = '$iEntryId'", 'id', 'id');
    }
	//END RSS
  
	/*featured functions*/ 
    function getCurrencySign($sKey){ 
		
		$sKey = ($sKey) ? $sKey : $this->_oConfig->_sCurrencySign;
		
		return $sKey;  
    }

	function setItemStatus($iItemId, $sStatus) {
		
 		 $this->query("UPDATE `" . $this->_sPrefix . "main` SET `status`='$sStatus' WHERE `id`='$iItemId'"); 
	}
  
   function generateFeaturedPaymentUrl($iGroupId, $iQuantity, $fCost) {
        $fCost   = number_format($fCost, 2);
 
        return  $this ->_oConfig->getPurchaseBaseUrl() . '?cmd=_xclick'
			    . '&rm=2'
                . '&no_shipping=0'      
				. '&currency_code='.$this->_oConfig->getPurchaseCurrency()
                . '&return=' .  $this->_oConfig->getFeaturedCallbackUrl() . $iGroupId
				. '&business='  . getParam('bx_groups_paypal_email')
                . '&item_name=' . getParam('bx_groups_featured_purchase_desc')
                . '&item_number=' . $iGroupId  
                . '&amount=' . $fCost;
    }
 
    function saveFeaturedTransactionRecord($iBuyerId, $iGroupId, $iQuantity, $fPrice, $sTransId, $sTransType) {
        $iBuyerId    = (int)$iBuyerId;
        $iGroupId  = (int)$iGroupId; 
        $iQuantity  = (int)$iQuantity; 
		$iTime = time();

        $bProcessed = $this->query("INSERT INTO `" . $this->_sPrefix . "featured_orders` 
							SET `buyer_id` = {$iBuyerId}, 
							`price` = '{$fPrice}',
							`days` = {$iQuantity},
							`item_id` =  {$iGroupId},
 							`trans_id` = '{$sTransId}',  
							`trans_type` = '{$sTransType}', 
  							`created` = $iTime  
        "); 

 		$iOrderId = $this->lastId();

		if($iOrderId){
			$this->alertOnAction('bx_groups_featured_admin_notify', $iGroupId, $iBuyerId, $iQuantity, true);

			$this->alertOnAction('bx_groups_featured_buyer_notify', $iGroupId, $iBuyerId, $iQuantity);
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

	function processGroups(){ 
  
		$this->processFeaturedGroups();
	}
 
	function processFeaturedGroups(){
		
		if(getParam('bx_groups_buy_featured') != 'on')
			return;

		$iTime = time();
   
        $aGroups = $this->getAll("SELECT `id`, `author_id`, `featured`, `featured_expiry_date` FROM `" . $this->_sPrefix . "main` WHERE `featured`=1 AND `featured_expiry_date` <= $iTime"); 

		foreach($aGroups as $aEachList){

		    $iExistExpireDate = (int)$aEachList['featured_expiry_date']; 
		    if($iExistExpireDate==0)
				continue;

			$iGroupId = (int)$aEachList['id'];
			$iRecipientId = (int)$aEachList['author_id'];

			$this->alertOnAction('bx_groups_featured_expire_notify', $iGroupId, $iRecipientId  );
		
	        $this->query("UPDATE `" . $this->_sPrefix . "main` SET `featured`=0, `featured_expiry_date`=0, `featured_date`=0  WHERE `id`=$iGroupId"); 
		}

	}

	function alertOnAction($sTemplate, $iGroupId, $iRecipientId=0, $iDays=0, $bAdmin=false) {
	   
		$aPlus = array();

		if($iGroupId){
			$aDataEntry = $this->getEntryById($iGroupId);
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

	//BEGIN Youtube 
	function getYoutubeVideos($iEntryId, $iLimit=0){

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

	function removeEntryYoutube($iEntryId){ 
	   
		$this->query("DELETE FROM `" . $this->_sPrefix . "youtube` WHERE `id_entry`='$iEntryId'");   
	}

	function removeYoutube($iEntryId, $iYoutubeId){ 
	   
		$this->query("DELETE FROM `" . $this->_sPrefix . "youtube` WHERE `id_entry`='$iEntryId' AND `id`='$iYoutubeId'");   
	}

    function getYoutubeIds ($iEntryId) {
		return $this->getPairs ("SELECT `id` FROM `" . $this->_sPrefix . "youtube`  WHERE `id_entry` = '$iEntryId'", 'id', 'id');
    }
	//END Youtube

    function removeAdmins ($iEntryId, $aProfileIds)
    {
        if (!$aProfileIds)
            return false;
        $s = implode (' OR `id_profile` = ', $aProfileIds);
 
        if ($this->_sTableAdmins)
            $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableAdmins . "` WHERE `id_entry` = '$iEntryId' AND `id_profile` = $s");
        return $iRet;
    }

    function isSubProfileFan($sTable, $iSubEntryId, $iProfileId, $isConfirmed) {
        $isConfirmed = $isConfirmed ? 1 : 0;

        $iEntryId = (int)$this->getOne ("SELECT `group_id` FROM `" . $this->_sPrefix . $sTable . "` WHERE `id` = '$iSubEntryId' LIMIT 1");
 
        return $this->getOne ("SELECT `when` FROM `" . $this->_sPrefix . $this->_sTableFans . "` WHERE `id_entry` = '$iEntryId' AND `id_profile` = '$iProfileId' AND `confirmed` = '$isConfirmed' LIMIT 1");
    }
 
    function isFan($iEntryId, $iProfileId, $isConfirmed)
    {
        $isConfirmed = $isConfirmed ? 1 : 0;
 
        return $this->getOne ("SELECT `when` FROM `" . $this->_sPrefix . $this->_sTableFans . "` WHERE `id_entry` = '$iEntryId' AND `id_profile` = '$iProfileId' AND `confirmed` = '$isConfirmed' LIMIT 1"); 
    }
 
	function getSubItemEntry($iObjectId, $sTable){
		$iObjectId = (int)$iObjectId;

       return $this->getRow("SELECT * FROM `" . $sTable . "` WHERE `id` = $iObjectId LIMIT 1"); 
	}

	function queueMessage($sEmail, $sSubject, $sMessage){
		$this->query("INSERT INTO `sys_sbs_queue`(`email`, `subject`, `body`) VALUES('" . $sEmail . "', '" . process_db_input($sSubject) . "', '" . process_db_input($sMessage) . "')"); 
	}

	function getModzzzNews($iId){
		return $this->getAll("SELECT * FROM `modzzz_news_main` WHERE `group_id`='$iId'"); 
	}

	/*begin 2.1.9*/
	function emptyMembershipRecords() {
  
		 $this->query("TRUNCATE TABLE `" . $this->_sPrefix . "memberships`");
	}

	function saveMembershipRecords($iLevelId, $iThreshold) {
  		$iLevelId = (int)$iLevelId;
  		$iThreshold = (int)$iThreshold;

		$this->query("INSERT INTO `" . $this->_sPrefix . "memberships` SET `threshold` =$iThreshold, `level_id` =$iLevelId");  
	}

	function getMembershipsBy($aParams = array()) {
	    $sMethod = "getAll";
	    $sSelectClause = $sJoinClause = $sWhereClause = "";
        if(isset($aParams['type']))
            switch($aParams['type']) {                
                case 'price_id':
                    $sMethod = "getRow";
                    $sSelectClause .= ", `tlp`.`id` AS `price_id`, `tlp`.`Days` AS `price_days`, `tlp`.`Price` AS `price_amount`";
                    $sJoinClause .= "LEFT JOIN `sys_acl_level_prices` AS `tlp` ON `tl`.`ID`=`tlp`.`IDLevel`";
                    $sWhereClause .= " AND `tl`.`Active`='yes' AND `tl`.`Purchasable`='yes' AND `tlp`.`id`='" . $aParams['id'] . "'";
                    break;
                case 'price_all':
                      $sWhereClause = " AND `tl`.`Active`='yes' AND (`tl`.`Purchasable`='yes' OR `tl`.`Removable`='no') AND `tl`.`ID` != 1";
                    break;
                case 'level_id':
                    $sMethod = "getRow";
                    $sWhereClause .= " AND `tl`.`ID`='" . $aParams['id'] . "'";
                    break;
            }
        
        $sSql = "SELECT
                `tl`.`ID` AS `mem_id`,
                `tl`.`Name` AS `mem_name`,
                `tl`.`Icon` AS `mem_icon`,
                `tl`.`Description` AS `mem_description` " . $sSelectClause . "
            FROM `sys_acl_levels` AS `tl` " . $sJoinClause . "
            WHERE 1" . $sWhereClause;

	   return $this->$sMethod($sSql);
	} 

	function getMembershipThreshold($iId) {
  		$iId = (int)$iId;

		return (int)$this->getOne("SELECT `ml`.`threshold` FROM `" . $this->_sPrefix . "memberships` ml 
		INNER JOIN `sys_acl_levels` AS `acl` ON `ml`.`level_id`=`acl`.`ID` 
		WHERE `ml`.`level_id` =$iId LIMIT 1"); 
	} 

    function getFanCount( $iEntryId, $bCheckConfirmed=false, $isConfirmed=0)
    {
        $isConfirmed = $isConfirmed ? 1 : 0;
		$sFilter = ($bCheckConfirmed) ? "AND `confirmed` = $isConfirmed" : "";
        
		return $this->getOne ("SELECT COUNT(`id_entry`) FROM `" . $this->_sPrefix . $this->_sTableFans . "` WHERE `id_entry` = '$iEntryId' {$sFilter}");
    }
 
	function banMember($iEntryId, $iProfileId, $iAdminId){
  		$iEntryId = (int)$iEntryId;
  		$iProfileId = (int)$iProfileId;
  		$iAdminId = (int)$iAdminId; 
		$iTime = time();
  
		$this->removeFans($iEntryId, array($iProfileId));
 
		return $this->query ("INSERT INTO `" . $this->_sPrefix . $this->_sTableBanned . "` SET `group_id`=$iEntryId, `profile_id`=$iProfileId, `banned_by`=$iAdminId, `date`=$iTime"); 
	}
 
    function isBanned ($iEntryId, $iProfileId){
  		$iEntryId = (int)$iEntryId;
  		$iProfileId = (int)$iProfileId;
 
        return (int)$this->getOne ("SELECT `group_id` FROM `" . $this->_sPrefix . $this->_sTableBanned . "` WHERE `group_id` = $iEntryId AND `profile_id` = $iProfileId LIMIT 1");
    }
 
    function unbanMembers ($iEntryId, $aProfileIds)
    {
        if (!$aProfileIds)
            return false;
        $s = implode (' OR `profile_id` = ', $aProfileIds);
        return $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableBanned . "` WHERE `group_id` = '$iEntryId' AND (`profile_id` = $s)");
    }

    function getBanned(&$aProfiles, $iEntryId)
    { 
        $aProfiles = $this->getAll ("SELECT SQL_CALC_FOUND_ROWS `p`.* FROM `Profiles` AS `p` INNER JOIN `" . $this->_sPrefix . $this->_sTableBanned . "` AS `f` ON (`f`.`group_id` = '$iEntryId' AND `f`.`profile_id` = `p`.`ID`) ORDER BY `f`.`date` DESC");
        return $this->getOne("SELECT FOUND_ROWS()");
    }
 
    function confirmFans ($iEntryId, $aProfileIds)
    {
        if (!$aProfileIds)
            return false;
        $s = implode (' OR `id_profile` = ', $aProfileIds);
        $iRet = $this->query ("UPDATE `" . $this->_sPrefix . $this->_sTableFans . "` SET `confirmed` = 1 WHERE `id_entry` = '$iEntryId' AND `confirmed` = 0 AND (`id_profile` = $s)");
        if ($iRet)
            $this->query ("UPDATE `" . $this->_sPrefix . "main` SET `" . $this->_sFieldFansCount . "` = `" . $this->_sFieldFansCount . "` + $iRet WHERE `id` = '$iEntryId'");
        return $iRet;
    }
	/*end 2.1.9*/

	//[begin] logo modification
	function getLogo($iId, $sName, $bUrlOnly=false, $bUseAsIcon=false){
 		$sUrl = $this->_oConfig->getMediaUrl() . $sName;

		if($bUseAsIcon){
			$iWidth = getParam("bx_photos_browse_width");
			$iHeight = getParam("bx_photos_browse_height"); 

			return "<img width='{$iWidth}px' height='{$iHeight}px' src='{$sUrl}' class='bx-twig-unit-thumb bx-def-round-corners bx-def-shadow'>";  
		}else{
			$iWidth = getParam("bx_groups_icon_width");
			$iHeight = getParam("bx_groups_icon_height");
		}
  
		if($bUrlOnly)
			return $sUrl;
		else
			return "<img src='{$sUrl}' class='bx-twig-unit-thumb bx-def-round-corners bx-def-shadow'>";  
	}	
    
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
