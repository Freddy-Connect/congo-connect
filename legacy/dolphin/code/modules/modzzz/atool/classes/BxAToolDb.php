<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Confession
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
bx_import('BxTemplTags');
bx_import('BxTemplFunctions');
bx_import('BxDolAlerts');
bx_import('BxDolEmailTemplates');

/*
 *  module Data
 */
class BxAToolDb extends BxDolTwigModuleDb {	

	var $_oConfig;

	/*
	 * Constructor.
	 */
	function BxAToolDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);
		$this->_oConfig = $oConfig;

        $this->_sTableSiteStatArchive = 'site_stat_archive';  
        $this->_sTableSiteMapArchive = 'sitemap_archive';  
		$this->_sTableFans = '';  
        $this->_sTablePage = 'page_content';  
        $this->_sFieldId = 'id'; 
	}
  
    function getModules()
    {
        $sSql = "SELECT DISTINCT
                    `tm`.`uri` AS `module_uri`,
                    `tm`.`title` AS `module_title`  
                FROM `sys_privacy_actions` AS `ta`
                 INNER JOIN `sys_modules` AS `tm` ON `ta`.`module_uri`=`tm`.`uri`
                WHERE 1
                ORDER BY `tm`.`title`";
        $aOptions = $this->getAll($sSql);
 
		$aModules = array(''=>_t('_Select'));
		foreach($aOptions as $aEachOption){ 
			$sEachKey = $aEachOption['module_uri'];
			$sEachVal = ucwords(strip_tags($aEachOption['module_title']));
			$aModules[$sEachKey] = $sEachVal;
		}

		return $aModules;
    }
 
    function getFormModuleActions($sModule='')
    {
        $sSql = "SELECT DISTINCT
                     `name`,
					 `title` 
                FROM `sys_privacy_actions` 
                WHERE `module_uri` = '$sModule'
                ORDER BY `title`";
        $aOptions = $this->getAll($sSql);

		$aModules = array(''=>_t('_Select'));
		foreach($aOptions as $aEachOption){ 
			$sEachKey = $aEachOption['name'];
			$sEachVal = _t($aEachOption['title']);
			$aModules[$sEachKey] = $sEachVal;
		}

		return $aModules;
    }

    function getModuleActions($sModule='')
    {
        $sSql = "SELECT DISTINCT
                     `name`,
					 `title` 
                FROM `sys_privacy_actions` 
                WHERE `module_uri` = '$sModule'
                ORDER BY `title`";
        $aOptions = $this->getAll($sSql);
 
		$sOptions = "<option value=''>"._t('_Select')."</option>";
		foreach($aOptions as $aEachOption){ 
			$sEachKey = $aEachOption['name'];
			$sEachVal = _t($aEachOption['title']);
			$sOptions .= "<option value='{$sEachKey}'>{$sEachVal}</option>";
		}

		return $sOptions;
    }

    function getFormGroupChooser($iOwnerId, $sModuleUri, $sActionName, $aDynamicGroups = array(), $sTitle = "")
    {
        if(empty($sActionName))
            return array();

        bx_import ('BxDolPrivacy'); 
		$oPrivacy = new BxDolPrivacy();

        $sValue = $oPrivacy->_oDb->getDefaultValue($iOwnerId, $sModuleUri, $sActionName);

        if(empty($sValue))
            $sValue = $oPrivacy->_oDb->getDefaultValueModule($sModuleUri, $sActionName);

        $aValues = array(''=>_t('_Select'));
        $aGroups = $oPrivacy->_oDb->getGroupsBy(array('type' => 'owner', 'owner_id' => $iOwnerId, 'full' => true));
        foreach($aGroups as $aGroup) {
            if((int)$aGroup['owner_id'] == 0 && $oPrivacy->_oDb->getParam('sys_ps_enabled_group_' . $aGroup['id']) != 'on')
               continue;

            $aValues[$aGroup['id']] = (int)$aGroup['owner_id'] == 0 ? _t('_ps_group_' . $aGroup['id'] . '_title') : $aGroup['title'];
        }
        //$aValues = array_merge($aValues, $aDynamicGroups);

        $aValues['f'] = _t('_modzzz_atool_privacy_fans');
        $aValues['a'] = _t('_modzzz_atool_privacy_admins_only');
  
		return $aValues; 
    }

    function getGroupChooser($iOwnerId, $sModuleUri, $sActionName, $aDynamicGroups = array(), $sTitle = "")
    {
        if(empty($sActionName))
            return array();

        bx_import ('BxDolPrivacy'); 
		$oPrivacy = new BxDolPrivacy();

        $sValue = $oPrivacy->_oDb->getDefaultValue($iOwnerId, $sModuleUri, $sActionName);

        if(empty($sValue))
            $sValue = $oPrivacy->_oDb->getDefaultValueModule($sModuleUri, $sActionName);

        $aValues = array();
        $aGroups = $oPrivacy->_oDb->getGroupsBy(array('type' => 'owner', 'owner_id' => $iOwnerId, 'full' => true));
        foreach($aGroups as $aGroup) {
            if((int)$aGroup['owner_id'] == 0 && $oPrivacy->_oDb->getParam('sys_ps_enabled_group_' . $aGroup['id']) != 'on')
               continue;

            $aValues[$aGroup['id']] = (int)$aGroup['owner_id'] == 0 ? _t('_ps_group_' . $aGroup['id'] . '_title') : $aGroup['title'];
        }
        $aValues = $aValues + $aDynamicGroups;

		$sOptions = "<option value=''>"._t('_Select')."</option>";
		foreach($aValues as $sEachKey=>$sEachVal){  
			$sSelected = ($sValue==$sEachKey) ? "selected='selected'" : '';
			$sOptions .= "<option {$sSelected} value='{$sEachKey}'>{$sEachVal}</option>";
		}
 
		//$sOptions .= "<option value=''>-------------</option>";
		$sOptions .= "<option value='f'>"._t('_modzzz_atool_privacy_fans')."</option>";
		$sOptions .= "<option value='a'>"._t('_modzzz_atool_privacy_admins_only')."</option>";
 
		return $sOptions;
 
        //$sName = $oPrivacy->getFieldAction($sActionName);
        //$sCaption = $oPrivacy->_oDb->getFieldActionTitle($sModuleUri, $sActionName); 
    }

    function setDefaultValue($sDefault, $sModuleUri, $sActionName)
    {
        bx_import ('BxDolPrivacy'); 
		$oPrivacy = new BxDolPrivacy();
 
         $this->query ("UPDATE `sys_privacy_actions` SET `default_group`='$sDefault'
            WHERE `module_uri`='" . $sModuleUri . "' AND `name`='" . $sActionName . "'
          ");
 
		 $sCacheName = $oPrivacy->_oDb->_sActionCache . $sModuleUri . '_' . $sActionName;
		 $this->cleanCache($sCacheName);   
    }
  
    function getCommentEntryById($iId) {
		$iId = (int)$iId;
 
        return $this->getRow ("SELECT * FROM `sys_cmts_profile` WHERE  `cmt_id` = $iId");
    }
 
    function deleteCommentById($iId) {
		$iId = (int)$iId;
 
        $this->query ("DELETE FROM `sys_cmts_profile` WHERE  `cmt_id` = $iId");
    }

    function getStatusEntryById($iId) {
		$iId = (int)$iId;
 
        return $this->getRow ("SELECT `ID`,`UserStatusMessage`,`UserStatusMessageWhen` FROM `Profiles` WHERE  `ID` = $iId");
    }
 
    function deleteStatusById($iId) {
		$iId = (int)$iId;
 
        $this->query ("UPDATE `Profiles` SET `UserStatusMessage` = '' WHERE `ID` = $iId");
    }

 
    function getMessageEntryById($iId) {
		$iId = (int)$iId;
 
        return $this->getRow ("SELECT * FROM `sys_messages` WHERE  `ID` = $iId");
    }

    function deleteMessageById($iId) {
		$iId = (int)$iId;
 
        $this->query ("DELETE FROM `sys_messages` WHERE  `ID` = $iId");
    }

	function getLanguages() {
		return $this->getPairs("SELECT `ID`, `Title` FROM `sys_localization_languages` ORDER BY `Name`", 'ID','Title');
	}

	function isValidLanguage($iId) {
		$iId = (int)$iId;

		return (int)$this->getOne("SELECT `ID` FROM `sys_localization_languages` WHERE  `ID` = $iId LIMIT 1");
	}

    function getPages ($iLang=0) {
		$iLang = (int)$iLang;

		if(!$iLang){
			$sLang = getParam( 'lang_default' );
			$iLang = getLangIdByName($sLang);
		}

		return $this->getAll ("SELECT DISTINCT `page`, `identifier` FROM `" . $this->_sPrefix . $this->_sTablePage . "`"); 
	}

    function getFirstEntryByPage ($sPage) {
 
		return $this->getRow ("SELECT `page`, `identifier` FROM `" . $this->_sPrefix . $this->_sTablePage . "` WHERE `page`='$sPage' LIMIT 1"); 
	}

    function getEntryByPage ($sPage, $iLang) {
		$iLang = (int)$iLang;

		return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTablePage . "` WHERE `page`='$sPage' AND `language`=$iLang"); 
	}

    function getEmailTemplates ($iLang=0) {
		$iLang = (int)$iLang;

		return $this->getAll ("SELECT `ID`, `Name`, `Desc` FROM `sys_email_templates` WHERE `LangID`=$iLang ORDER BY `Name`"); 
	}

    function getEmailTemplateByName ($sName, $iLang, $iNumLanguages) {
		$iLang = (int)$iLang;
 
		$aData = $this->getRow ("SELECT * FROM `sys_email_templates` WHERE `Name`='$sName' AND `LangID`=$iLang"); 

		if($iNumLanguages==1 && empty($aData))
			$aData = $this->getRow ("SELECT * FROM `sys_email_templates` WHERE `Name`='$sName' AND `LangID`=0"); 

		return $aData;
	}
 
	function cleanup(){
		// 
	}

	function initialize(){
 
		$aPage = array();
		$aPage['faq'] = array('identifier'=>'FAQ Page', 'title'=>'_FAQ_H1', 'desc'=>'_FAQ_INFO');
		$aPage['terms'] = array('identifier'=>'Terms Page', 'title'=>'_TERMS_OF_USE_H1', 'desc'=>'_TERMS_OF_USE');
		$aPage['advice'] = array('identifier'=>'Advice Page', 'title'=>'_ADVICE_H1', 'desc'=>'_ADVICE');
		$aPage['about'] = array('identifier'=>'About Us Page', 'title'=>'_ABOUT_US_H', 'desc'=>'_ABOUT_US');
 		$aPage['privacy'] = array('identifier'=>'Privacy Page', 'title'=>'_PRIVACY_H1', 'desc'=>'_PRIVACY');
		$aPage['help'] = array('identifier'=>'Help Page', 'title'=>'_HELP_H1', 'desc'=>'_HELP');
		$aPage['chat'] = array('identifier'=>'Chat Rules Page', 'title'=>'_modzzz_atool_chat_page_rules_caption', 'desc'=>'_modzzz_atool_chat_rules');

		$aLangs = $this->getLanguages(); 
		foreach ($aLangs as $iEachLangId=>$sEachLangName) {  
  
			foreach ($aPage as $sPage=>$aEachPage) {  

				if($bExists = $this->getOne ("SELECT `id` FROM `" . $this->_sPrefix . $this->_sTablePage . "` WHERE `language`=$iEachLangId AND `page`='$sPage' LIMIT 1")) continue;

				$sIdentifier = $aEachPage['identifier'];
				$sTitle = $aEachPage['title'];
				$sDesc = $aEachPage['desc'];

			    $sLangTitle = $this->getOne ("SELECT `String` FROM `sys_localization_strings` str INNER JOIN `sys_localization_keys` ky WHERE str.`IDKey`=ky.`ID` AND `Key`='$sTitle' AND `IDLanguage`=$iEachLangId");
			   
			    $sLangDesc = $this->getOne ("SELECT `String` FROM `sys_localization_strings` str INNER JOIN `sys_localization_keys` ky WHERE str.`IDKey`=ky.`ID` AND `Key`='$sDesc' AND `IDLanguage`=$iEachLangId");
	 
				$this->query ("INSERT INTO `" . $this->_sPrefix . $this->_sTablePage . "` (`language`,`page`,`identifier`,`title`,`desc`) VALUES ($iEachLangId, '$sPage', '$sIdentifier', '".process_db_input($sLangTitle)."', '".process_db_input($sLangDesc)."')");
			}
		}
	}

	function isFeatured($iProfileId){
         $iProfileId = (int)$iProfileId;
 
         return $this->getOne ("SELECT `ID` FROM `Profiles` WHERE `Featured`=1 AND `ID` = $iProfileId LIMIT 1"); 
	}
 
	function makeFeatured($iProfileId, $iFeatured){
		 $iProfileId = (int)$iProfileId;
		 $iFeatured = (int)$iFeatured;
  
		 $bProcessed = $this->query ("UPDATE `Profiles` SET `Featured`=$iFeatured  WHERE `ID` = $iProfileId");   

		 createUserDataFile($iProfileId); 

		return $bProcessed;
	}
  
	function isProfileUnconfirmed($iProfileId){
         $iProfileId = (int)$iProfileId;
 
         return $this->getOne ("SELECT `ID` FROM `Profiles` WHERE `Status`='Unconfirmed' AND `ID` = $iProfileId LIMIT 1"); 
	}
 
	function isProfileActivated($iProfileId){
         $iProfileId = (int)$iProfileId;
 
         return $this->getOne ("SELECT `ID` FROM `Profiles` WHERE `Status`='Active' AND `ID` = $iProfileId LIMIT 1"); 
	}
 
	function changeProfileStatus($iProfileId, $bActivate){
		$iProfileId = (int)$iProfileId;

		if($bActivate){
			$GLOBALS['MySQL']->query("UPDATE `Profiles` SET `Status`='Active' WHERE `ID`=$iProfileId");

			$oEmailTemplate = new BxDolEmailTemplates();
			createUserDataFile((int)$iProfileId);
			reparseObjTags('profile', (int)$iProfileId);

			$aProfile = getProfileInfo($iProfileId);
			$aMail = $oEmailTemplate->parseTemplate('t_Activation', array(), $iProfileId);
			sendMail($aProfile['Email'], $aMail['subject'], $aMail['body'], $iProfileId, array(), 'html', false, true);

			$oAlert = new BxDolAlerts('profile', 'change_status', (int)$iProfileId, 0, array('status' => 'Active'));
			$oAlert->alert();
		}else{
			$GLOBALS['MySQL']->query("UPDATE `Profiles` SET `Status`='Approval' WHERE `ID`=$iProfileId");
			createUserDataFile((int)$iProfileId);
			reparseObjTags('profile', (int)$iProfileId);
			$oAlert = new BxDolAlerts('profile', 'change_status', (int)$iProfileId, 0, array('status' => 'Approval'));
			$oAlert->alert();
		}
   
		return true;
	}
 
	function isProfileBanned($iProfileId){
        $iProfileId = (int)$iProfileId;
 
		$sWhereClause .= " AND (`tbl`.`Time`='0' OR (`tbl`.`Time`<>'0' AND DATE_ADD(`tbl`.`DateTime`, INTERVAL `tbl`.`Time` HOUR)>NOW()))";

        return $this->getOne ("SELECT `tp`.`ID` FROM `Profiles` AS `tp`
        INNER JOIN `sys_admin_ban_list` AS `tbl` ON `tp`.`ID`=`tbl`.`ProfID` WHERE `tp`.`ID`=$iProfileId" . $sWhereClause . " LIMIT 1"); 
	}
 
	function markBan($iProfileId, $bBanProfile){
		$iProfileId = (int)$iProfileId;

		if($bBanProfile){
			$GLOBALS['MySQL']->query("REPLACE INTO `sys_admin_ban_list` SET `ProfID`=$iProfileId, `Time`='0', `DateTime`=NOW()");
		}else{
			$GLOBALS['MySQL']->query("DELETE FROM `sys_admin_ban_list` WHERE `ProfID`=$iProfileId"); 
		}
   
		return true;
	}
 
    function getSiteStatArchiveById ($iId) {
    	$iId = (int)$iId;
 
        return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableSiteStatArchive . "` WHERE `{$this->_sFieldId}` = $iId LIMIT 1");
    }
 
    function deleteSiteStatById ($iId) {
 
        if (!($iRet = $this->query ("DELETE FROM `sys_stat_site` WHERE `{$this->_sFieldId}` = $iId LIMIT 1")))
            return false;
 
        return true;
    }

    function deleteSiteStatArchiveById ($iId) {
 
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSiteStatArchive . "` WHERE `{$this->_sFieldId}` = $iId LIMIT 1")))
            return false;
 
        return true;
    }
 
	function archiveSiteStatEntry($iId) {
		$iId = (int)$iId;
		$iTime = time();

        $bSuccess = $this->query ("INSERT INTO `" . $this->_sPrefix . $this->_sTableSiteStatArchive . "` SELECT *, '$iTime' FROM `sys_stat_site` WHERE `{$this->_sFieldId}` = $iId");
		
		if($bSuccess) {
			$this->deleteSiteStatById($iId);
		}

		$this->cleanCache('sys_stat_site');   
	}

    function getSiteStatEntryById ($iId ) {
   		$iId = (int)$iId;
  
        return $this->getRow ("SELECT * FROM `sys_stat_site` WHERE `{$this->_sFieldId}` = $iId  LIMIT 1");
    }

    function deleteSiteStatEntryById ($iId, $iOwner=0, $isAdmin=0) {
 		$iId = (int)$iId;

        if (!($iRet = $this->query ("DELETE FROM `sys_stat_site` WHERE `{$this->_sFieldId}` = $iId LIMIT 1")))
            return false;
 
 		$this->cleanCache('sys_stat_site');   

        return true;
    }        
  
    function activateSiteStatEntry ($iId) {
 		$iId = (int)$iId;
  
        $bSuccess = $this->query ("INSERT INTO `sys_stat_site` (`Name`,`Title`,`UserLink`,`UserQuery`,`AdminLink`,`AdminQuery`,`StatOrder`) SELECT `Name`,`Title`,`UserLink`,`UserQuery`,`AdminLink`,`AdminQuery`,`StatOrder` FROM `" . $this->_sPrefix . $this->_sTableSiteStatArchive . "` WHERE `{$this->_sFieldId}` = $iId");
		
		if($bSuccess) {
			$this->deleteSiteStatArchiveById($iId);
		}

		$this->cleanCache('sys_stat_site');   
	}

    function getFormSiteStatUnits () {
		return $this->getPairs ("SELECT `ID`,`Title` FROM `sys_stat_site`", 'ID', 'Title'); 
	}

    function getSiteStatUnits () {
		return $this->getAll ("SELECT `ID`,`Title` FROM `sys_stat_site`"); 
	}

    function sortSiteStat($iEntryId, $iOrder) {
 		$iEntryId = (int)$iEntryId;
 		$iOrder = (int)$iOrder;

		$bResult = $this->query ("UPDATE `sys_stat_site` SET `StatOrder`=$iOrder WHERE `ID`=$iEntryId");   
		$this->cleanCache('sys_stat_site'); 
		
		return $bResult;
	}

	//end site stat

	//begin sitemap
   function getSiteMapArchiveById ($iId) {
    	$iId = (int)$iId;
 
        return $this->getRow ("SELECT * FROM `" . $this->_sPrefix . $this->_sTableSiteMapArchive . "` WHERE `{$this->_sFieldId}` = $iId LIMIT 1");
    }
 
    function deleteSiteMapById ($iId) {
 
        if (!($iRet = $this->query ("DELETE FROM `sys_objects_site_maps` WHERE `{$this->_sFieldId}` = $iId LIMIT 1")))
            return false;
 
        return true;
    }

    function deleteSiteMapArchiveById ($iId) {
 
        if (!($iRet = $this->query ("DELETE FROM `" . $this->_sPrefix . $this->_sTableSiteMapArchive . "` WHERE `{$this->_sFieldId}` = $iId LIMIT 1")))
            return false;
 
        return true;
    }
 
	function archiveSiteMapEntry($iId) {
		$iId = (int)$iId;
		$iTime = time();

        $bSuccess = $this->query ("INSERT INTO `" . $this->_sPrefix . $this->_sTableSiteMapArchive . "` SELECT *, '$iTime' FROM `sys_objects_site_maps` WHERE `{$this->_sFieldId}` = $iId");
		
		if($bSuccess) {
			$this->deleteSiteMapById($iId);
		}

		$this->cleanCache('sys_objects_site_maps');   
	}

    function getSiteMapEntryById ($iId ) {
   		$iId = (int)$iId;
  
        return $this->getRow ("SELECT * FROM `sys_objects_site_maps` WHERE `{$this->_sFieldId}` = $iId  LIMIT 1");
    }

    function deleteSiteMapEntryById ($iId, $iOwner=0, $isAdmin=0) {
 		$iId = (int)$iId;

        if (!($iRet = $this->query ("DELETE FROM `sys_objects_site_maps` WHERE `{$this->_sFieldId}` = $iId LIMIT 1")))
            return false;
 
 		$this->cleanCache('sys_objects_site_maps');   

        return true;
    }        
 
    function activateSiteMapEntry ($iId) {
 		$iId = (int)$iId;
  
        $bSuccess = $this->query ("INSERT INTO `sys_objects_site_maps` (`object`,`title`,`priority`,`changefreq`,`class_name`,`class_file`,`order`,`active`) SELECT `object`,`title`,`priority`,`changefreq`,`class_name`,`class_file`,`order`,`active` FROM `" . $this->_sPrefix . $this->_sTableSiteMapArchive . "` WHERE `{$this->_sFieldId}` = $iId");
		
		if($bSuccess) {
			$this->deleteSiteMapArchiveById($iId);
		}

		$this->cleanCache('sys_objects_site_maps');   
	}

    function getFormSiteMapUnits () {
		return $this->getPairs ("SELECT `id`,`title` FROM `sys_objects_site_maps`", 'id', 'title'); 
	}

    function getSiteMapUnits () {
		return $this->getAll ("SELECT `id`,`title` FROM `sys_objects_site_maps`"); 
	}

    function sortSiteMap($iEntryId, $iOrder) {
 		$iEntryId = (int)$iEntryId;
 		$iOrder = (int)$iOrder;

		$bResult = $this->query ("UPDATE `sys_objects_site_maps` SET `order`=$iOrder WHERE `id`=$iEntryId");   
		$this->cleanCache('sys_objects_site_maps'); 
		
		return $bResult;
	}
	//end sitemap
 
	function makeAdmin($iProfileId, $bAdmin){
		 $iProfileId = (int)$iProfileId;
		 $iRole = ($bAdmin) ? 3 : 1;
  
		 $bProcessed = $this->query ("UPDATE `Profiles` SET `Role`=$iRole  WHERE `ID` = $iProfileId");    
		 createUserDataFile($iProfileId); 

		 return $bProcessed;
	}
    
    function cleanCache ($sName)
    {
        if (!getParam('sys_db_cache_enable'))
            return true;

        $oCache = $this->getDbCacheObject ();

        $sKey = $this->genDbCacheKey($sName);
 
        return $oCache->delData($sKey);
    }


}
