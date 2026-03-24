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

bx_import('BxDolCron');
bx_import('BxDolAlerts');
  
class BxRssInfoCron extends BxDolCron {
	var $_oModule;

	function BxRssInfoCron() {
		parent::BxDolCron();

		$this -> _oModule = BxDolModule::getInstance('BxRssInfoModule');   
	}
  
	function processing() { 
		if(getParam('modzzz_rssinfo_feed_ping')=='hour'){
			$this->_oModule->_oDb->fetchFeed(); 
		}

		if(getParam('modzzz_rssinfo_feed_ping')=='daily'){ 
			$iHour = (int)date('H');
			if($iHour==0){
				$this->_oModule->_oDb->fetchFeed(); 
			}
		} 
    }
}