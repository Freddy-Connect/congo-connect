<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -----------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2006 BoonEx Group
*     website              : http://www.boonex.com/
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 
* http://creativecommons.org/licenses/by/3.0/
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the Creative Commons Attribution 3.0 License for more details. 
* You should have received a copy of the Creative Commons Attribution 3.0 License along with Dolphin, 
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

require_once( BX_DIRECTORY_PATH_INC . "design.inc.php");
bx_import('BxDolAlerts');
bx_import('BxDolDb');
bx_import('BxDolModule');

class BxEventsResponse extends BxDolAlertsResponse {

	function BxEventsResponse() {
	    parent::BxDolAlertsResponse();
	}

    function response ($oAlert) {
  
		$oEvents = BxDolModule::getInstance('BxEventsModule');
		  
		switch ($oAlert->sAction) {

			case 'join':  
			case 'join_confirm':  
   
				if($oAlert->sUnit == 'bx_groups'){
					$aEvents = $oEvents->_oDb->eventsForMonitoredGroup ($oAlert->iObject);
					foreach($aEvents as $iEventId){
						$isFan = $oEvents->_oDb->isFan ($iEventId, $oAlert->iSender, true) || $oEvents->_oDb->isFan ($iEventId, $oAlert->iSender, false);

						if(!$isFan){
							$oEvents->_oDb->joinEntry($iEventId, $oAlert->iSender, true); 
						} 
					}
				} 
			break; 
 
		}//end switch  
	
	}



}
