<?php
/***************************************************************************
Membership Management Pro v.3.0
Created by Martinboi
http://www.martinboi.com
***************************************************************************/
class MbMmpHandler{
  var $_oMain, $iUserId = 0;
  
	function MbMmpHandler(){
		$this ->_oMain = BxDolModule::getInstance("MbMmpModule");
		if ($this ->_oMain->isLogged()) $this ->iUserId = (int)$this ->_oMain->getUserId();
		$this->iRole = $this->_oMain->_oDb->getUserRole($this->iUserId);
		$this->aProfile = $this->_oMain->_oDb->getProfileArr($this->iUserId);
		$this->aSettings = unserialize(getParam('dol_subs_config'));
		$this->sMembPage = BX_DOL_URL_ROOT.'m/memberships/';
		/* freddy modif remplacement de  de BX_DOL_URL_ROOT.'join.php' par BX_DOL_URL_ROOT; pour rediriger toujours vers la page d'accueil
		$this->sJoinPage = BX_DOL_URL_ROOT.'join.php';
		
		$this->sJoinPage = BX_DOL_URL_ROOT;
		*/
		$this->sJoinPage = BX_DOL_URL_ROOT.'join.php';
		
		/////////////fin freddy modif////////////////
		
		$sEMAILCONFSENT= BX_DOL_URL_ROOT.'m/avatar/&join_text=_EMAIL_CONF_SENT';
		
		$sBase = BX_DOL_URL_ROOT;  
		$this->sSecureBase = str_replace('http://', 'https://', $sBase);
	}
	
	function currentPage(&$oAlertInfo){
		$sPageCaption = $this->_oMain->_oDb->getPageCaption($oAlertInfo->iObject);
		$iId = (int)$this->iUserId;
		$sStatus = $this->aProfile['Status'];		
		$aSafe = $this->_oMain->_oConfig->safe_pages();
		$aSafeGuest = $this->_oMain->_oConfig->safe_pages_guest();
		$iIsAcl = $this->_oMain->_oDb->userAcl($iId);
		$iCurrentMemLevel = $this->_oMain->_oDb->getCurrentMembershipLevel($iId);
		$aMenuItemAccess = $this->_oMain->_oDb->getMenuAccessLevels($oAlertInfo->iObject);
		
		// Setup URL restriction
		$site_url = BX_DOL_URL_ROOT;
		$sCurrentURL = str_replace($site_url, '',$oAlertInfo->aExtras['current_url']);
		//$aRestrictedMemLevelsForURL = unserialize($this->_oMain->_oDb->getRestrictedMemLevelsForURL($sCurrentURL));

		// Handle Logged Members
		if($iId && $this->iRole != '3'){			
			if($this->aSettings['require_mem'] == 'on'){
				if(($sStatus != 'Active') || ($iIsAcl == '0')){
					if(!in_array($sPageCaption, $aSafe) && $oAlertInfo->iObject != '-1'){
						Redirect($this->sMembPage, array('login_text' => '_login_txt_not_active'), 'post');
					}
				}
				if(($sStatus != 'Active') && ($iIsAcl == '1')){					
					$sProfileCache = BX_DIRECTORY_PATH_CACHE . 'user' . $iId . '.php';
					$this->_oMain->_oDb->setUserStatus($iId,'Active');
					unlink($sProfileCache);
				}				
			}else{
				if(!in_array($sPageCaption, $aSafe) && $oAlertInfo->iObject != '-1'){						
					if(is_array($aMenuItemAccess)){	
						if(in_array($iCurrentMemLevel,$aMenuItemAccess)){
							$this->_oMain->_oTemplate->customDisplayAccessDenied();
							exit();
						}		
					}	
				}
			}

		}

		// Handle Guests
		if(!$iId && $this->iRole != '3' && !$_COOKIE['memberID']){			
			if($this->aSettings['redirect_guests'] == 'on' ){
				if(!in_array($sPageCaption, $aSafeGuest)  && $oAlertInfo->iObject != '-1')
				Redirect($this->sJoinPage, array('login_text' => '_login_txt_not_active'), 'post');
			}else{
				$iCurrentMemLevel = '1';
				if(!in_array($sPageCaption, $aSafeGuest) && $oAlertInfo->iObject != '-1'){
					if(is_array($aMenuItemAccess)){	
						if(in_array($iCurrentMemLevel,$aMenuItemAccess)){
							$this->_oMain->_oTemplate->customDisplayAccessDenied();
							exit();
						}		
					}
				}

				/* If URL restriction applies
				if(!empty($aRestrictedMemLevelsForURL) && in_array($iCurrentMemLevel,$aRestrictedMemLevelsForURL)){	
					$this->_oMain->_oTemplate->customDisplayAccessDenied();
					exit();
				} */
			}
		}
	}

	function handleLogin(&$oAlertInfo){
		$iID = (int)$this ->iUserId;
	}

	function handleJoin(&$oAlertInfo){
		$iId = $oAlertInfo->iObject;
		if($this->aSettings['require_mem'] == 'on'){
			$this->_oMain->_oDb->setUserStatus($iId,'Suspended');
			bx_login($id,true);
		}

		if(empty($this->aSettings['default_memID']) || $this->aSettings['default_memID'] != 3 ){
			  setMembership($iId, $this->aSettings['default_memID'], $iMemershipDays, true);
		}elseif($this->aSettings['default_memID'] == 2){
		  $this->oMain->_oDb->clearMembershipInfo($iId);		
		  deleteUserDataFile($iId);						
		}

	}
	
	

}