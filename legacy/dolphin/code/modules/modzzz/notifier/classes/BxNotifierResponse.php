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

class BxNotifierResponse extends BxDolAlertsResponse {

	function BxNotifierResponse() {
	    parent::BxDolAlertsResponse();
	}

    function response ($oAlert) {
 
		$oNotifier = BxDolModule::getInstance('BxNotifierModule');  
		switch ($oAlert->sAction) {

			case 'add': 
			case 'create':  
   
				$aAdmins = $oNotifier->_oDb->getNotifierMembers();
		 
				foreach($aAdmins as $aEachAdmin){ 
					$iEachAdmin=(int)$aEachAdmin['MemberID'];
 
					if($iEachAdmin != $oAlert->iSender)
						$oNotifier->_oDb->alertAdministrators($oAlert->sUnit, $oAlert->sAction, $iEachAdmin, $oAlert->iSender, $oAlert->iObject);
				} 
			 
				break; 
			case 'delete': 
/*
				if( $oNotifier->_oDb->AllowNotifier($oAlert->sUnit, $oAlert->sAction, $oAlert->iSender) ){	 
					$oNotifier->_oDb->alertAdministrators($oAlert->sUnit, $oAlert->sAction, $oAlert->iSender, $oAlert->iObject);
				}
 				break; 
*/
		}//end switch  
	
	}



}
