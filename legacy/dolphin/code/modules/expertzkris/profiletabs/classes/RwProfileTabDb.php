<?php

/***************************************************************************
*                           Expertzkris Admin Protection Plugin
*                              -------------------
*     begin                : Mon Mar 26 2012
*     copyright            : (C) 2012 Dexpertz Website Solutions
*     website              : http://www.Dexpertz.net
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* Application/Profile Tabs is not free and you cannot redistribute and/or modify it.
* 
* Application/Profile Tabs is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@Dexpertz.net. 
* 
* For more details please write to info@Dexpertz.net
**********************************************************************************/

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php' );class RwProfileTabDb extends BxDolModuleDb {      var $_oConfig;    function RwProfileTabDb(&$oConfig) {        parent::BxDolModuleDb();        $this->_oConfig = $oConfig;    }    function RemoveProfileTabItems($tabs) {    	return $this->query("DELETE FROM `rw_ptabs_profile_tabs` WHERE `profile_tabs_id` = '$tabs' LIMIT 1 ");    }    function DeleteProfileTab($tabs) {        $this->query("DELETE FROM `rw_ptabs_tabs` WHERE `tabs_id` = '$tabs' LIMIT 1 ");        $this->query("DELETE FROM `rw_ptabs_profile_tabs` WHERE `profile_tabs_type` = '$tabs' ");        return;    }    function AddProfileTabBlock($blockId, $tabType) {    	return $this->query("INSERT INTO `rw_ptabs_profile_tabs` SET `profile_tabs_block_id` = '$blockId', `profile_tabs_type` = '$tabType' ");    }    function AddProfileTab($Tabname) {        return $this->query("INSERT INTO `rw_ptabs_tabs` SET `tabs_caption` = '$Tabname' ");    }     function UpdateProfileTab($Tabname, $TabId) {        return $this->query("UPDATE `rw_ptabs_tabs` SET `tabs_caption` = '$Tabname' WHERE `tabs_id` = '$TabId' LIMIT 1");    }    function UpdateProfileTabBlocks($order, $tabsId, $type) {    	return $this->query("UPDATE `rw_ptabs_profile_tabs` SET `profile_tabs_order` = '$order', `profile_tabs_type` = '$type' WHERE `profile_tabs_id` = '$tabsId' LIMIT 1 ");    }    function getAllProfileBlocks() {    	return $this->getAll("SELECT * FROM `sys_page_compose`     						  LEFT JOIN `rw_ptabs_profile_tabs` ON `profile_tabs_block_id` = `ID`    						  WHERE `Page` = 'profile'  ORDER BY `Caption` ");    }    function getProfileTabs() {        return $this->getAll("SELECT * FROM `rw_ptabs_tabs` ORDER BY `tabs_order` ");    }    function getProfileTabsTwo($id) {        return $this->getAll("SELECT * FROM `sys_page_compose`                               INNER JOIN `rw_ptabs_profile_tabs` ON `profile_tabs_block_id` = `ID`                              INNER JOIN `rw_ptabs_tabs` ON `tabs_id` = `profile_tabs_type`                              WHERE `profile_tabs_type` = '$id' ORDER BY `profile_tabs_order` ");    }    function getAllProfiletabsItems($id) {    	return $this->getAll("SELECT * FROM `sys_page_compose` 				  			 INNER JOIN `rw_ptabs_profile_tabs` ON `profile_tabs_block_id` = `ID`                             WHERE `profile_tabs_type` = '$id'				  			 ORDER BY `profile_tabs_order` ");    }    function getSettingsCategory() {        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Profile Tabs' LIMIT 1");    }    function insertSortTabs ($key, $value) {                $results = $this->query("UPDATE rw_ptabs_tabs SET `tabs_order`='$key' WHERE `tabs_id`=$value");        }}?>