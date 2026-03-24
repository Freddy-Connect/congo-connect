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

class BxNotifyResponse extends BxDolAlertsResponse {

	function BxNotifyResponse() {
	    parent::BxDolAlertsResponse();
	}

    function response ($oAlert) {
		global $gConf;
 
		$oNotify = BxDolModule::getInstance('BxNotifyModule');  
		switch ($oAlert->sAction) {
		 
			case 'update':
				if($oAlert->sUnit == 'bx_wall'){
					if( $oAlert->iObject!=$oAlert->iSender && $oNotify->_oDb->AllowNotify($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject) ){		 
						$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject, $oAlert->iSender);
					}
				}
				break; 
            case 'post':
                if($oAlert->sUnit == 'bx_wall'){
                    if( $oNotify->_oDb->AllowNotify($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject) ){         
                        $oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject, $oAlert->iSender);
                    }
                }
                break;
			case 'reply':

				if($oAlert->sUnit == 'bx_forum'){
					$aPost = $oNotify->_oDb->getRow("SELECT `forum_id`, `last_post_user` FROM `" . $gConf['db']['prefix'] . "forum_topic` WHERE `topic_id` = '" . $oAlert->iObject . "' LIMIT 1"); 
  
					$iForumId = $aPost['forum_id']; 
					$iTopicId = $oAlert->iObject; 
					$sUser = $aPost['last_post_user'];  
  
					$oNotify->_oDb->alertTopicOwner($iForumId, $iTopicId, $sUser);  
					$oNotify->_oDb->alertTopicParticpants($iForumId, $iTopicId, $sUser);   
				}
				break; 

			case 'new_topic':
 
				if($oAlert->sUnit == 'bx_forum'){
					$aPost = $oNotify->_oDb->getRow("SELECT `forum_id`, `last_post_user` FROM `" . $gConf['db']['prefix'] . "forum_topic` WHERE `topic_id` = '" . $oAlert->iObject . "' LIMIT 1"); 
  
					$iForumId = $aPost['forum_id']; 
					$iTopicId = $oAlert->iObject; 
					$sUser = $aPost['last_post_user'];   
				} 
				break; 
			 
 			case 'post_report':

				break;  
			case 'flag': 
 
				break;  
			case 'edit_status_message':
				$aFriends = $oNotify->_oDb->AllFriendsNotify($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject);
				foreach($aFriends as $iEachFriend){
					$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $iEachFriend, $oAlert->iObject);
				}
				break;
			case 'view': 
				if( $oAlert->iSender && $oNotify->_oDb->AllowNotify($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject) && !$oNotify->_oDb->isViewedToday($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject, $oAlert->iSender) ){	
				
					if( $oNotify->_oDb->exposeView($oAlert->iSender) ){
						$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject, $oAlert->iSender);

						$oNotify->_oDb->trackViews($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject, $oAlert->iSender);
					}
				}
				break; 
			case 'join': 
				$iOwnerId = $oNotify->_oDb->getObjectOwner($oAlert->sUnit, $oAlert->iObject); 
				
				if( $oNotify->_oDb->AllowNotify($oAlert->sUnit, $oAlert->sAction, $iOwnerId) ){		 
					$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $iOwnerId, $oAlert->iSender, $oAlert->iObject);
				}
				break; 
			case 'add': 

				if( in_array($oAlert->sUnit, array('fave','block')) ){   
 
					if( $oNotify->_oDb->AllowNotify($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject) ){		 
						$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject, $oAlert->iSender);
					}
				}else{ 				
			 
					if( (!$oAlert->aExtras['status']) || in_array($oAlert->aExtras['status'],array('approved','approval','active')) ){
	 
						$aFriends = $oNotify->_oDb->AllFriendsNotify($oAlert->sUnit, $oAlert->sAction, $oAlert->iSender);
				 
						foreach($aFriends as $iEachFriend){ 
 
							$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $iEachFriend, $oAlert->iSender, $oAlert->iObject);
						} 
					} 
				}
				break; 
			case 'create':  

			case 'change':
				if( (!$oAlert->aExtras['status']) || in_array($oAlert->aExtras['status'],array('approved','approval','active')) ){

					$aFriends = $oNotify->_oDb->AllFriendsNotify($oAlert->sUnit, $oAlert->sAction, $oAlert->iSender);
					foreach($aFriends as $iEachFriend){
						$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $iEachFriend, $oAlert->iSender, $oAlert->iObject);
					}
				}
				break; 
			case 'answered':
 
				if($oAlert->iObject != $oAlert->iSender) {
					if( $oNotify->_oDb->AllowNotify($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject) ){		 
						$iPollId = $oAlert->aExtras['poll_id']; 
						$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject, $oAlert->iSender, $iPollId); 
					}
				}  
				break; 
			case 'commentPost':

			    $iCommentId = $oAlert->aExtras['comment_id'];
				$iOwnerId = $oNotify->_oDb->getObjectOwner($oAlert->sUnit, $oAlert->iObject);
 		   
				if($iOwnerId != $oAlert->iSender) { 
					if( $oNotify->_oDb->AllowNotify($oAlert->sUnit, $oAlert->sAction, $iOwnerId) ){		 
						$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $iOwnerId, $oAlert->iSender, $oAlert->iObject);  
 					}
				}
 				break; 
			case 'commentRated':
			 
			    $iCommentId = $oAlert->aExtras['comment_id'];
 				$iCmtAuthorId = $oNotify->_oDb->getCommentOwner($oAlert->sUnit, $iCommentId);
	 
				if($iCmtAuthorId != $oAlert->iSender) { 
					
					if( $oNotify->_oDb->AllowNotify($oAlert->sUnit, $oAlert->sAction, $iCmtAuthorId) ){	 
						$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $iCmtAuthorId, $oAlert->iSender, $oAlert->iObject);  
					}
				}
 				break; 
			case 'rate':

				$iOwnerId = $oNotify->_oDb->getObjectOwner($oAlert->sUnit, $oAlert->iObject);
				if($iOwnerId != $oAlert->iSender) { 
					if( $oNotify->_oDb->AllowNotify($oAlert->sUnit, $oAlert->sAction, $iOwnerId) ){	
						if($oAlert->sUnit=='profile'){
							$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $iOwnerId, $oAlert->iSender); 
						}else{
							$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $iOwnerId, $oAlert->iSender, $oAlert->iObject);  
						}
					}
				}
 				break;

			//case 'commentRemoved':
			//case 'delete_post': 
			//case 'delete_poll':  
			case 'delete': 
				if( in_array($oAlert->sUnit, array('friend')) ){    
					if( $oNotify->_oDb->AllowNotify($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject) ){	 
						$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject, $oAlert->iSender);
					}
				}
				if( in_array($oAlert->sUnit, array('fave','block')) ){  
					if( $oNotify->_oDb->AllowNotify($oAlert->sUnit, $oAlert->sAction, $oAlert->iSender) ){	 
						$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $oAlert->iSender, $oAlert->iObject);
					}
				} 
 				break; 
			case 'send_mail_internal': 
 
				if(!((int)$oAlert->aExtras['send_copy'] || (int)$oAlert->aExtras['notification'])) { 
					if( $oNotify->_oDb->AllowNotify($oAlert->sUnit, $oAlert->sAction, $oAlert->iSender) ){ 
						$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $oAlert->iSender, $oAlert->iObject);
					}
				} 
				break; 
			default:
 	 
				if( $oNotify->_oDb->AllowNotify($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject) ){		 
					$oNotify->_oDb->alertOwner($oAlert->sUnit, $oAlert->sAction, $oAlert->iObject, $oAlert->iSender);
				}
				break; 
		}
  
	
	}



}
