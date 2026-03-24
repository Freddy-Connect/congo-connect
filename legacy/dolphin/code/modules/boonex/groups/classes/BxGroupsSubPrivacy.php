<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Band
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

bx_import('BxDolPrivacy');

class BxGroupsSubPrivacy extends BxDolPrivacy {

    var $oModule;
    var $sTable;

	/**
	 * Constructor
	 */
    function BxGroupsSubPrivacy(&$oModule, $sTable) {
        $this->oModule = $oModule;
        $this->sTable = $sTable;
	    parent::BxDolPrivacy($oModule->_oDb->getPrefix() . $sTable, 'id', 'author_id');
    }
 
	/**
	 * Check whethere viewer is a member of dynamic Group.
	 *
	 * @param mixed $mixedId dynamic Group ID.
	 * @param integer $iObjectOwnerId object owner ID.
	 * @param integer $iViewerId viewer ID.
	 * @return boolean result of operation.
	 */
    function isDynamicGroupMember($mixedId, $iObjectOwnerId, $iViewerId, $iObjectId) {
   
        $aDataEntry = array ('id' => $iObjectId, 'author_id' => $iObjectOwnerId);
        if ('f' == $mixedId){  // fans only                       
            return $this->oModule->isSubProfileFan ($this->sTable, $aDataEntry, $iViewerId, true); 
        }elseif ('a' == $mixedId){// admins only
  
			$aSubEntry = $this->oModule->_oDb->getSubItemEntry($iObjectId, $this->oModule->_oDb->getPrefix() . $this->sTable);
 
            return $this->oModule->isSubEntryAdmin ($aSubEntry, $iViewerId); 
		}  
	    return false;
	} 

	 
}
