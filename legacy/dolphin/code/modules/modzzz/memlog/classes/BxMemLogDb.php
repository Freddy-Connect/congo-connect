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
 
/*
 * MemLog module Data
 */
class BxMemLogDb extends BxDolTwigModuleDb {	

	var $_oConfig;
 
	/*
	 * Constructor.
	 */
	function BxMemLogDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);
		$this->_oConfig = $oConfig; 

		$this->_sTableFans = '';
	}

  	function getAdmin() { 
		 return (int)$this->getOne("SELECT `ID` FROM `Profiles` WHERE `Role` = 3 LIMIT 1");  
	}

	function removeProfileEntries($iProfileId) { 
		 $this->query("DELETE FROM `" . $this->_sPrefix . "main` WHERE `member_id` = '$iProfileId' OR `moderator_id` = '$iProfileId'");  
	}
 
	function isModerator($iProfileId, $iModeratorId){
 
		 return (int)$this->getOne("SELECT `id` FROM `" . $this->_sPrefix . "main` WHERE `member_id`='$iProfileId' AND `moderator_id`='$iModeratorId' LIMIT 1");   
	}
  
	function addModerator($iProfileId, $iModeratorId){

		 $iTime = time();
 
		 return $this->query("INSERT INTO `" . $this->_sPrefix . "main` SET `member_id` = '$iProfileId', `moderator_id` = '$iModeratorId', `created` = $iTime"); 
	}

	function updateMembershipAction($iActionId, $iProfileId){
		$iActionId = (int)$iActionId;
		$iProfileId = (int)$iProfileId;

		$this->query("
			UPDATE `sys_acl_actions_track`
			SET `ActionsLeft` = `ActionsLeft` + 1
			WHERE `IDAction` = $iActionId AND `IDMember` = $iProfileId");
	}

	function removeModerator($iProfileId, $iModeratorId){
 
		 return $this->query("DELETE FROM `" . $this->_sPrefix . "main` WHERE `member_id` = '$iProfileId' AND `moderator_id` = '$iModeratorId'"); 
	}
 
 	function insertBottomMenu($iModeratorId){
   
		$iMenuMemberOrder = (int)$this->getOne("SELECT `Order` + 1 FROM `sys_menu_member` WHERE 1 ORDER BY `Order` DESC LIMIT 1");
		$this->query("INSERT INTO `sys_menu_member` ( `Caption`, `Name`, `Icon`, `Link`, `Script`, `Eval`, `PopupMenu`, `Order`, `Active`, `Movable`, `Clonable`, `Editable`, `Deletable`, `Target`, `Position`, `Type`, `Parent`, `Bubble`, `Description`) VALUES   
		('{system}', 'memlog', '', 'm/memlog/return', '', 'return BxDolService::call(''memlog'', ''get_return_caption'', array());', '', $iMenuMemberOrder, '1', 3, 1, 0, 0, '', 'top', 'link', 0, '', '_modzzz_memlog_return_msg_desc')"); 

		$GLOBALS['MySQL']->cleanCache ('sys_menu_member'); 
	}

 	function clearBottomMenu(){ 
 		$this->query("DELETE FROM `sys_menu_member` WHERE `Link` LIKE 'm/memlog/return%'");

		$GLOBALS['MySQL']->cleanCache ('sys_menu_member');
	}

}
