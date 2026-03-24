<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx RssInfo
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
 * RssInfo module Data
 */
class BxRssInfoDb extends BxDolTwigModuleDb {	

	/*
	 * Constructor.
	 */
	function BxRssInfoDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);
        
		$this->_sTableFans = ''; 
        $this->_sTableMain = 'main';
        $this->_sTableMediaPrefix = '';
        $this->_sFieldId = 'id';
        $this->_sFieldUri = 'uri';
        $this->_sFieldTitle = 'title'; 
        $this->_sFieldStatus = 'status'; 
        $this->_sFieldCreated = 'created';
        $this->_sFieldUpdated = 'updated'; 
        $this->_sFieldPublished = 'when'; 
        $this->_sFieldOwnerId = 'owner_id';   
        $this->_sFieldLink = 'link';   
        $this->_sFieldCategoryId = 'category_id'; 
        $this->_sFieldParentCategoryId = 'parent_category_id'; 
        $this->_sFieldTag = 'tag'; 
        $this->_sFieldFetchCount = 'fetch_count'; 
        $this->_sFieldSourceLink = 'source_link'; 
        $this->_sFieldPublish = 'publish';  
        $this->_sFieldLanguage = 'language';  


		$oModuleDb = new BxDolModuleDb(); 
		if($oModuleDb->isModule('mnews')){
			$this->_sNewsPrefix = "modzzz_mnews";
		}else{
			$this->_sNewsPrefix = "modzzz_news";
		} 

	}
  
    function publishEntry ($iId) {
       
		$this->query ("UPDATE `" . $this->_sPrefix . $this->_sTableMain . "` SET `{$this->_sFieldStatus}`='approved' WHERE `{$this->_sFieldId}` = $iId"); 
    }  

    function unPublishEntry ($iId) {
       
		$this->query ("UPDATE `" . $this->_sPrefix . $this->_sTableMain . "` SET `{$this->_sFieldStatus}`='pending' WHERE `{$this->_sFieldId}` = $iId"); 
    }  

    function deleteEntryByIdAndOwner ($iId) {
       
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `{$this->_sFieldId}` = $iId LIMIT 1")))
            return false;
         return true;
    }  
  
    function fetchFeed () {

		$iTime = time();

		$oModuleDb = new BxDolModuleDb(); 
		if($oModuleDb->isModule('mnews'))  
			$oNews = BxDolModule::getInstance('BxMNewsModule');
		else
			$oNews = BxDolModule::getInstance('BxNewsModule');

		$aFeeders =  $this->getAll ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableMain . "` WHERE `{$this->_sFieldStatus}` = 'approved'");
		 
		foreach($aFeeders as $aEachFeeder){
			$sLink = trim($aEachFeeder[$this->_sFieldLink]);
			$iProfileId = (int)$aEachFeeder[$this->_sFieldOwnerId];
		    $iParentCategoryId = $aEachFeeder[$this->_sFieldParentCategoryId];
		    $iCategoryId = $aEachFeeder[$this->_sFieldCategoryId];
		    $sTags = $aEachFeeder[$this->_sFieldTag];
		    $iFetchCount = (int)$aEachFeeder[$this->_sFieldFetchCount];
		    $iSourceLink = (int)$aEachFeeder[$this->_sFieldSourceLink];
		    $iPublish = (int)$aEachFeeder[$this->_sFieldPublish]; 
		    $iLanguageId = (int)$aEachFeeder[$this->_sFieldLanguage];

			$iProfileId = ($iProfileId) ? $iProfileId : getLoggedId();
  
			$doc = new DOMDocument(); 
			@$doc->load($sLink);

			$iter = 0;
			foreach ($doc->getElementsByTagName('item') as $node) {
  
				if($iFetchCount && ($iter == $iFetchCount)) break;
 
				/*
				if(getParam('modzzz_rssinfo_use_timestamp')=='on'){
					if($node->getElementsByTagName('pubDate')->item(0)->nodeValue)
						$iTime = $this->rssToTime($node->getElementsByTagName('pubDate')->item(0)->nodeValue);
				}*/

				$sPostCaption = process_db_input(strip_tags($node->getElementsByTagName('title')->item(0)->nodeValue));
				 
				$sUri = uriGenerate($sPostCaption, $this->_sNewsPrefix.'_main', 'uri');

				if(!$bExists = $this->getOne("SELECT `id` FROM `".$this->_sNewsPrefix."_main` WHERE `title`='$sPostCaption' OR `uri`='$sUri' LIMIT 1")){ 
	   	
					$sDesc = process_db_input($node->getElementsByTagName('encoded')->item(0)->nodeValue); 

					if(!$sDesc){
						$sDesc = process_db_input($node->getElementsByTagName('description')->item(0)->nodeValue); 
					}

					//begin - add media
					if ($node->getElementsByTagName("content")->length != 0) {
						$mediaNode = $node->getElementsByTagName('content');
						$sMedium = $mediaNode->item(0)->getAttribute('medium');
						if($sMedium!='image'){
							$mediaNode = $node->getElementsByTagName('thumbnail'); 
						} 

						if($mediaNode->length > 0) {
				
							$objImg = $mediaNode->item(0);
							
							if ($img === null) {
								//invalid image object
							}else{
								$sUrl = $objImg->getAttribute('url');
								$sAlt = $objImg->getAttribute('alt');
								$sHeight = $objImg->getAttribute('height');
								$sWidth = $objImg->getAttribute('width');

								if($sUrl){
									$sDesc .= " <div class=\"bx-def-padding-top\"><img src=\"{$sUrl}\" alt=\"{$sAlt}\" height=\"{$sHeight}\" width=\"{$sWidth}\" /></div>";
								} 
							}
						}
					}
					//end - add media
 
 					$sPostSnippet = strmaxtextlen($sDesc, $oNews->_oConfig->getSnippetLength()); 
 
					$sSourceUrl = process_db_input($node->getElementsByTagName('link')->item(0)->nodeValue); 
	 
					$aSiteInfo = $this->getSiteInfo($sSourceUrl);
					
					if($aSiteInfo['tags']){
						$aSourceTags = explode(',', $aSiteInfo['tags']);
						array_walk($aSourceTags, create_function('&$val', '$val = trim($val);')); 
						
						$aFilteredTags = array();
						foreach($aSourceTags as $sEachTag){
							if(strlen($sEachTag)<=32)
								$aFilteredTags[] = $sEachTag;
						}

						$sTags = implode(',', $aFilteredTags);
						$sTags = process_db_input($sTags);
					}
			 
					$sLetter = strtolower(substr(preg_replace('/[^a-zA-Z0-9]/s', '', $sPostCaption), 0, 1));
	 
					$sStatus = ($iPublish) ? "approved" : "pending";

					if($iSourceLink){ 
						$sDesc .= '<div class="bx-def-padding-top">' . _t('_modzzz_rssinfo_source') . ': <a target=_blank href="' . $sSourceUrl . '">' . $sSourceUrl . '</a></div>';
					}
	  
					$this->query("INSERT INTO `".$this->_sNewsPrefix."_main` SET `author_id`=$iProfileId, `uri`='$sUri', `title`='$sPostCaption', `letter`='$sLetter', `snippet`='$sPostSnippet',  `desc`='$sDesc', `parent_category_id`='$iParentCategoryId', `category_id`='$iCategoryId', `language`=$iLanguageId, `tags`='$sTags', `created`=$iTime, `when`=$iTime, `status`='$sStatus', `post_type`='feed', `allow_view_news_to`=3, `allow_rate_to`=3, `allow_comment_to`=3"); 
					
					$iEntryId = $this->lastId();
 
					$aDataEntry = $oNews->_oDb->getEntryById($iEntryId);

					$oNews->onEventCreate($iEntryId, $sStatus, $aDataEntry);
 
					$iter++;
				}
			}
		} 
    }

    function reparseCategories ($iProfileId, $iEntryId)
    {
        $iEntryId = (int)$iEntryId;
        $iProfileId = (int)$iProfileId;
        bx_import('BxDolCategories');
        $o = new BxDolCategories ($iProfileId);
        $o->reparseObjTags($this->_sNewsPrefix, $iEntryId);
    }
 
	function getSiteInfo($sSourceUrl){
	
		$aResult = array();
		$sContent = bx_file_get_contents($sSourceUrl);
		
		if (strlen($sContent)){ 
		
			// create the DOMDocument object, and load HTML from a string
			$dochtml = new DOMDocument();
			@$dochtml->loadHTML($sContent);

			$aMetas = @$dochtml->getElementsByTagName('meta');
     
			foreach($aMetas as $aMeta) {  
				if($aMeta->hasAttribute('name') && $aMeta->getAttribute('name') == 'keywords') { 
					$aResult['tags'] = trim($aMeta->getAttribute('content')); 
				} 
			}
  
			if(!$aResult['tags']){
				preg_match("/<meta.*name[='\" ]+keywords['\"].*content[='\" ]+(.*)['\"].*><\/meta>/", $sContent, $aMatch);
				$aResult['tags'] = trim($aMatch[1]);
			}

			if(!$aResult['tags']){
				preg_match("/<meta.*name[='\" ]+keywords['\"].*content[='\" ]+(.*)['\"].*\/>/", $sContent, $aMatch);
				$aResult['tags'] = trim($aMatch[1]);
			}

			if(!$aResult['tags']){
				preg_match("/<meta.*name[='\" ]+keywords['\"].*content[='\" ]+(.*)['\"].*>/", $sContent, $aMatch);
				$aResult['tags'] = trim($aMatch[1]);
			} 
		}
		  
		return $aResult;
	}
	 
	function deleteItemEntries($aDataEntry){

		$iProfileId = (int)$aDataEntry['owner_id'];

		$this->query("DELETE FROM `".$this->_sNewsPrefix."_main` WHERE `author_id` = $iProfileId AND `post_type`='feed'");    
	} 

	function getAdmins(){
		return $this->getPairs("SELECT `ID`, `NickName` FROM `Profiles` WHERE `Role` = 3 AND `Status`='Active'", 'ID', 'NickName');    
	}

	function initialize(){
		global $db;

		$fields = mysql_list_fields($db['db'], $this->_sNewsPrefix."_main"); 
		$columns = mysql_num_fields($fields);
			   
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
			   
		if (!in_array('post_type', $field_array)) {
			$this->query("ALTER TABLE `".$this->_sNewsPrefix."_main` CHANGE `title` `title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''"); 
			$this->query("ALTER TABLE `".$this->_sNewsPrefix."_main` ADD  `post_type` varchar(255) NOT NULL");    
		} 
	}

	function cleanup(){
		global $db;

		$fields = mysql_list_fields($db['db'], $this->_sNewsPrefix."_main"); 
		$columns = mysql_num_fields($fields);
			   
		for ($i = 0; $i < $columns; $i++) {
			$field_array[] = mysql_field_name($fields, $i);
		}
			   
		if (in_array('post_type', $field_array)) {
			$this->query("ALTER TABLE `".$this->_sNewsPrefix."_main` DROP `post_type`");    
		} 
	}

    function rssToTime($rss_time) {
        $day = substr($rss_time, 5, 2);
        $month = substr($rss_time, 8, 3);
        $month = date('m', strtotime("$month 1 2011"));
        $year = substr($rss_time, 12, 4);
        $hour = substr($rss_time, 17, 2);
        $min = substr($rss_time, 20, 2);
        $second = substr($rss_time, 23, 2);
        $timezone = substr($rss_time, 26);
  
        date_default_timezone_set('UTC');

        if(is_numeric($timezone)) {
            $hours_mod = $mins_mod = 0;
            //$modifier = substr($timezone, 0, 1);

			$modifier = substr($timezone, 0, 1);        
			if($modifier == "+"){ $modifier = "-"; } 
			elseif($modifier == "-"){ $modifier = "+"; }
 
            $hours_mod = (int) substr($timezone, 1, 2);
            $mins_mod = (int) substr($timezone, 3, 2);
            $hour_label = $hours_mod>1 ? 'hours' : 'hour';
            $strtotimearg = $modifier.$hours_mod.' '.$hour_label;
            if($mins_mod) {
                $mins_label = $mins_mod>1 ? 'minutes' : 'minute';
                $strtotimearg .= ' '.$mins_mod.' '.$mins_label;
            }
            $timestamp = strtotime($strtotimearg, $timestamp);
        
		}else{ 
			if($timezone){date_default_timezone_set($timezone);}

			$timestamp = mktime($hour, $min, $second, $month, $day, $year);
		}
 
        return $timestamp;
	}
 
	function getLanguages(){
		return $this->getPairs("SELECT `ID`,`Title` FROM `sys_localization_languages` ORDER BY `Name`", 'ID','Title');
	}


}