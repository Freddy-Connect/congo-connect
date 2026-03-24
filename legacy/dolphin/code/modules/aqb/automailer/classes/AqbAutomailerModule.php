<?php
/***************************************************************************
*
*     copyright            : (C) 2011 AQB Soft
*     website              : http://www.aqbsoft.com
*
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
*
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
*
* This notice may not be removed from the source code.
*
***************************************************************************/

bx_import('BxDolModule');

class AqbAutomailerModule extends BxDolModule {
	/**
	 * Constructor
	 */
	var $_sUglySolutionMessage = '';
	function AqbAutomailerModule($aModule) {
	    parent::BxDolModule($aModule);
	}

	function getMailsList() {
		$aMails = $this->_oDb->getMaillist();
		return $this->_sUglySolutionMessage.$this->_oTemplate->getMailsList($aMails);
	}
	function getAddForm() {
		$sMessage = '';
		$aPreFill = array();
		if ($_POST['action'] && $_POST['action'] == 'edit' && $_POST['id']) $aPreFill = $this->_oDb->getMailData(intval($_POST['id']));
		$oForm = $this->_oTemplate->getAddForm($aPreFill);
		if ($oForm->isSubmittedAndValid()) {
			$iAutomail = intval($_POST['id']);
			$this->_sUglySolutionMessage = $iAutomail ? MsgBox(_t('_aqb_automailer_edited'), 3) : MsgBox(_t('_aqb_automailer_added'), 3);

			$aData = $this->prepareEmailData($_POST);
			$this->_oDb->saveEmailData($aData);

			unset($_POST);
			$oForm = $this->_oTemplate->getAddForm();
		}
		return $oForm->getCode();
	}

	function actionGetTimeForm() {
		if (!isAdmin()) return 'Only admin can access this page';
		$aSchedServTime = $this->_oDb->getCronTime();
		$iCurServTime = time()+date('Z'); //in UTC now
		$aCurServTime = array('h' => date('G', $iCurServTime), 'm' => intval(date('i', $iCurServTime)));
		$iCurLocalTime = intval($_REQUEST['time']/1000);
		$iCurLocalTime -= 60*intval($_REQUEST['offset']); //in UTC now

		$aCurLocalTime = array('h' => gmdate('G', $iCurLocalTime), 'm' => intval(gmdate('i', $iCurLocalTime)));
		$iDiff = $iCurLocalTime - $iCurServTime;
		$iDiff = 60 * round($iDiff / 60);
		if ($iDiff % 3600 < 300) $iDiff = 3600 * round($iDiff / 3600);

		$iLocalShedTime = mktime($aSchedServTime['h'], $aSchedServTime['m'], 0, 1,1, 2000) + $iDiff;
		$aShedLocalTime = array('h' => date('G', $iLocalShedTime), 'm' => intval(date('i', $iLocalShedTime)));

		$oForm = $this->_oTemplate->getTimeForm($aCurServTime, $aCurLocalTime, $aSchedServTime, $aShedLocalTime);
		return PopupBox('aqb_automailer_time_form_popup', _t('_aqb_automailer_set_time'), '<div style="position:relative;" class="bx-def-padding">'.$oForm->getCode().LoadingBox('aqbAutomailerFormLoading').'</div>');
	}
	function actionSetTime() {
		if (!isAdmin()) return 'Only admin can access this page';

		$iCurServTime = time()+date('Z'); //in UTC now
		$iCurLocalTime = intval($_REQUEST['time']/1000);
		$iCurLocalTime -= 60*intval($_REQUEST['offset']); //in UTC now
		$iDiff = $iCurLocalTime - $iCurServTime;
		$iDiff = 60 * round($iDiff / 60);
		if ($iDiff % 3600 < 300) $iDiff = 3600 * round($iDiff / 3600);

		$h = intval($_REQUEST['h']);
		$m = intval($_REQUEST['m']);
		if ($_REQUEST['p'] == 1 && $h == 12) $h = 0;
		elseif ($_REQUEST['p'] == 2 && $h != 12) $h += 12;

		$iServShedTime = mktime($h, $m, 0, 1,1, 2000) - $iDiff;
		$aShedServTime = array('h' => date('G', $iServShedTime), 'm' => intval(date('i', $iServShedTime)));

		$this->_oDb->setCronTime($aShedServTime['h'], $aShedServTime['m']);

		return $this->actionGetTimeForm();
	}

	function prepareEmailData($aPostData) {
		$aRet = array();
		$aRet['ID'] = intval($aPostData['id']);
		$aRet['Name'] = process_db_input($aPostData['name']);

		$aLangs = array(0 => 'def') + getLangsArr(false, true);
		foreach ($aLangs as $iLangID => $dummy) {
			if (get_magic_quotes_gpc()) {
				$aPostData['subject'.$iLangID] = stripslashes($aPostData['subject'.$iLangID]);
				$aPostData['body'.$iLangID] = stripslashes($aPostData['body'.$iLangID]);
			}
			$aRet['Subject'][$iLangID] = $aPostData['subject'.$iLangID];
			$aRet['Body'][$iLangID] = $aPostData['body'.$iLangID];
		}

		if ($aPostData['profile_status']) $aRet['Filter']['ProfileStatus'] = process_db_input($aPostData['profile_status']);
		if ($aPostData['profile_memlevel']) $aRet['Filter']['ProfileMemlevel'] = intval($aPostData['profile_memlevel']);
		if ($aPostData['profile_profile_type']) $aRet['Filter']['ProfileProfileType'] = process_db_input($aPostData['profile_profile_type']);
		if ($aPostData['profile_country']) $aRet['Filter']['ProfileCountry'] = process_db_input($aPostData['profile_country']);
		if ($aPostData['profile_sex']) $aRet['Filter']['ProfileSex'] = process_db_input($aPostData['profile_sex']);
		if ($aPostData['profile_noava']) $aRet['Filter']['ProfileNoAva'] = 1;
		if ($aPostData['profile_age']) {
			$sMaxRange = $this->_oDb->getparam('search_start_age').'-'.$this->_oDb->getparam('search_end_age');
			if ($aPostData['profile_age'] != $sMaxRange) $aRet['Filter']['ProfileAge'] = process_db_input($aPostData['profile_age']);
		}

		if ($aPostData['every_x_days']) $aRet['Schedule']['EveryXDays'] = intval($aPostData['every_x_days']);
		elseif ($aPostData['on_day_x_since_registration']) $aRet['Schedule']['DayXSinceRegistration'] = intval($aPostData['on_day_x_since_registration']);
		elseif ($aPostData['on_day_x_since_latest_activity']) $aRet['Schedule']['DayXSinceLatestActivity'] = intval($aPostData['on_day_x_since_latest_activity']);
		elseif ($aPostData['on_day_x_before_membership_expiration']) $aRet['Schedule']['DayXBeforeMembershipExpiration'] = intval($aPostData['on_day_x_before_membership_expiration']);
		elseif (strtotime($aPostData['exact_date']) > time() || $aPostData['annually']) {
			$aRet['Schedule']['ExactDate'] = strtotime($aPostData['exact_date']);
			if ($aPostData['annually']) $aRet['Schedule']['Annually'] = 1;
		}elseif ($aPostData['birthday']) $aRet['Schedule']['Birthday'] = 1;

		$aRet['FilterQuery'] = $this->getFilterQuery($aRet['Filter'], $aRet['Schedule']);

		$aRet['Options']['SendTo'] = $aPostData['sendto'];
		$aRet['Options']['AddPoints'] = intval($aPostData['add_points']);
		$aRet['Options']['AdminID'] = getLoggedId();

		return $aRet;
	}
	function getFilterQuery($aFilter, $aSchedule) {
		$sWhere = ' 1 ';
		$sJoin = '';

		if (!empty($aFilter)) {
			foreach ($aFilter as $sName => $sValue) {
				switch ($sName) {
					case 'ProfileMemlevel':
						$sJoin = " LEFT JOIN `sys_acl_levels_members` ON `Profiles`.`ID` = `sys_acl_levels_members`.`IDMember` AND `sys_acl_levels_members`.`DateStarts` < NOW() AND (`sys_acl_levels_members`.`DateExpires` > NOW() || ISNULL(`sys_acl_levels_members`.`DateExpires`)) ";
						$sWhere .= $sValue != 2 ? " AND `sys_acl_levels_members`.`IDLevel` = {$sValue} " : " AND `sys_acl_levels_members`.`IDLevel` IS NULL ";
					break;
					case 'ProfileAge':
						list($iStartAge, $iEndAge) = explode('-', $sValue);
						$iStartAge = intval($iStartAge); $iEndAge = intval($iEndAge);
						$sWhere .= "	AND (DATE_FORMAT( NOW() , '%Y' ) - DATE_FORMAT( `DateOfBirth` , '%Y' ) - ( DATE_FORMAT( NOW() , '00-%m-%d' ) < DATE_FORMAT( `DateOfBirth` , '00-%m-%d' ) )) >= {$iStartAge}
										AND (DATE_FORMAT( NOW() , '%Y' ) - DATE_FORMAT( `DateOfBirth` , '%Y' ) - ( DATE_FORMAT( NOW() , '00-%m-%d' ) < DATE_FORMAT( `DateOfBirth` , '00-%m-%d' ) )) <= {$iEndAge}	";
					break;
					case 'ProfileStatus':
					case 'ProfileProfileType':
					case 'ProfileCountry':
					case 'ProfileSex':
						$sWhere .= " AND `".substr($sName, 7)."` = '".addslashes($sValue)."' ";
					break;
					case 'ProfileNoAva':
						$sWhere .= " AND `Avatar` = 0 ";
					break;
				}
			}
		}

		if (!empty($aSchedule)) {
			if ($aSchedule['EveryXDays']) $sWhere .= " AND ((TO_DAYS(NOW()) - TO_DAYS(`DateReg`)) % {$aSchedule['EveryXDays']} = 0) ";
			elseif ($aSchedule['DayXSinceRegistration']) $sWhere .= " AND (TO_DAYS(NOW()) - TO_DAYS(`DateReg`) = {$aSchedule['DayXSinceRegistration']}) ";
			elseif ($aSchedule['DayXSinceLatestActivity']) $sWhere .= " AND (TO_DAYS(NOW()) - TO_DAYS(`DateLastNav`) = {$aSchedule['DayXSinceLatestActivity']}) ";
			elseif ($aSchedule['DayXBeforeMembershipExpiration']) {
				$sJoin = " JOIN `sys_acl_levels_members` ON `Profiles`.`ID` = `sys_acl_levels_members`.`IDMember` AND `sys_acl_levels_members`.`DateStarts` < NOW() AND (`sys_acl_levels_members`.`DateExpires` > NOW() || ISNULL(`sys_acl_levels_members`.`DateExpires`)) ";
				$sWhere .= " AND (TO_DAYS(`sys_acl_levels_members`.`DateExpires`) - TO_DAYS(NOW()) = {$aSchedule['DayXBeforeMembershipExpiration']}) ";
			}
			elseif ($aSchedule['ExactDate']) {
				if ($aSchedule['Annually']) $sWhere .= " AND (DATE_FORMAT(NOW() , '%m%d' ) = '".date('md', $aSchedule['ExactDate'])."') ";
				else $sWhere .= " AND (DATE_FORMAT(NOW() , '%Y%m%d' ) = '".date('Ymd', $aSchedule['ExactDate'])."') ";
			}
			elseif ($aSchedule['Birthday'])  $sWhere .= " AND (DATE_FORMAT(NOW(), '%m%d') = DATE_FORMAT(`DateOfBirth`, '%m%d')) ";
		}

		return addslashes("
			SELECT `ID`
			FROM `Profiles`
			{$sJoin}
			WHERE {$sWhere}
		");
	}

	function serviceQueueEmails() {
		$aMails = $this->_oDb->getMaillist(true);
		$iDebugCounter = 0;
		$bPointsModuleInstalled = $this->_oDb->getModuleByUri('aqb_points');
		$bAQBPointsFieldExist = $this->_oConfig->isFieldAvailable('AqbPoints');
		if ($aMails)
		foreach ($aMails as $aMail) {
			$aProfiles = $this->_oDb->getAll($aMail['FilterQuery']);
			if ($aProfiles) {
				foreach ($aProfiles as $aProfile) {
					if ($aMail['Schedule']['DayXBeforeMembershipExpiration']) { //query filer signifies only thos profiles whose current memlevel is being expired in 3 days. Now we must check whether there are no any other memlevels in the queue.
						$further_timestamp = time() + $aMail['Schedule']['DayXBeforeMembershipExpiration'] * 24 * 3600;
		                $further_membership_arr = getMemberMembershipInfo($aProfile['ID'], $further_timestamp);
		                if ( $further_membership_arr['ID'] != MEMBERSHIP_ID_STANDARD ) continue; //there is something in queue
					}
					$aProfile = getProfileInfo($aProfile['ID']);
					$sSubject = isset($aMail['Subject'][$aProfile['LangID']]) && !empty($aMail['Subject'][$aProfile['LangID']]) ? $aMail['Subject'][$aProfile['LangID']] : $aMail['Subject'][0];
					$sBody = isset($aMail['Body'][$aProfile['LangID']]) && !empty($aMail['Body'][$aProfile['LangID']]) ? $aMail['Body'][$aProfile['LangID']] : $aMail['Body'][0];
					$sBody = $this->parseEmailTemplate($sBody, $aProfile);
					if (in_array('email', $aMail['Options']['SendTo'])) $this->_oDb->queueMail($aProfile['Email'], $sSubject, $sBody);
					if (in_array('inbox', $aMail['Options']['SendTo'])) $this->_oDb->sendMessage($aProfile['ID'], $aMail['Options']['AdminID'], $sSubject, $sBody);

					if ($aMail['Options']['AddPoints'] && $bPointsModuleInstalled && $bAQBPointsFieldExist)
						$this->_oDb->addAqbPoints($aProfile['ID'], $aMail['Options']['AddPoints'], _t('_aqb_automailer_points_reason'));

					$iDebugCounter++;
					unset($GLOBALS['aUser'][$aProfile['ID']]);
					unset($aProfile);
				}
			}
			unset($aProfiles);
		}
		//echo $iDebugCounter.' queued!';
	}
	function testAutomail($iID) {
		$aMail = $this->_oDb->getMailData($iID);
		$aProfile = getProfileInfo(getLoggedId());

		$sSubject = isset($aMail['Subject'][$aProfile['LangID']]) && !empty($aMail['Subject'][$aProfile['LangID']]) ? $aMail['Subject'][$aProfile['LangID']] : $aMail['Subject'][0];
		$sBody = isset($aMail['Body'][$aProfile['LangID']]) && !empty($aMail['Body'][$aProfile['LangID']]) ? $aMail['Body'][$aProfile['LangID']] : $aMail['Body'][0];
		$sBody = $this->parseEmailTemplate($sBody, $aProfile);
		if (in_array('email', $aMail['Options']['SendTo'])) sendMail($aProfile['Email'], $sSubject, $sBody);
		if (in_array('inbox', $aMail['Options']['SendTo'])) $this->_oDb->sendMessage($aProfile['ID'], $aMail['Options']['AdminID'], $sSubject, $sBody);

		echo _t('_aqb_automailer_test_message');
	}

	function parseEmailTemplate($sBody, $aProfile) {
		$aPlaceholders = array(
			'::recipientID::',
			'::RealName::',
			'::NickName::',
			'::Domain::',
			'::SiteName::',
		);
		$aReplacers = array(
			$aProfile['ID'],
			$GLOBALS['site']['ver'] == '7.2' ? $aProfile['FullName'] : $aProfile['FirstName'].'&nbsp;'.$aProfile['LastName'],
			$aProfile['NickName'],
			$GLOBALS['site']['url'],
			getParam('site_title'),
		);

		return str_replace($aPlaceholders, $aReplacers, $sBody);
	}
	function serviceLangidBugfix() {
		// user roles
		define('BX_DOL_ROLE_GUEST',     0);
		define('BX_DOL_ROLE_MEMBER',    1);
		define('BX_DOL_ROLE_ADMIN',     2);
		define('BX_DOL_ROLE_AFFILIATE', 4);
		define('BX_DOL_ROLE_MODERATOR', 8);
		check_logged();
		$iID = getLoggedId();
		if (!$iID) return;

		if ($GLOBALS['sCurrentLanguage'] != $_COOKIE['lang']) setLangCookie($GLOBALS['sCurrentLanguage']);
	}
}
?>