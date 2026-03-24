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

bx_import('BxDolModuleDb');

class AqbAutomailerDb extends BxDolModuleDb {
	/*
	 * Constructor.
	 */
	var $_oConfig;
	function AqbAutomailerDb(&$oConfig) {
		parent::BxDolModuleDb($oConfig);
		$this->_oConfig = $oConfig;
	}

	function getCronTime() {
		$sTime = $this->getOne("SELECT `time` FROM `sys_cron_jobs` WHERE `name` = 'aqb_automailer' LIMIT 1");
		$aParts = explode(' ', $sTime);
		return array('h' => intval($aParts[1]), 'm' => intval($aParts[0]));
	}
	function setCronTime($h, $m) {
		$sTime = "{$m} {$h} * * *";
		$this->query("UPDATE `sys_cron_jobs` SET `time` = '{$sTime}' WHERE `name` = 'aqb_automailer' LIMIT 1");
		if ($GLOBALS['site']['build'] > 2) {
			$oCache = $GLOBALS['MySQL']->getDbCacheObject();
        	$oCache->removeAllByPrefix('db_sys_cron_jobs_');
		} else {
        	@unlink(BX_DIRECTORY_PATH_CACHE.'db_sys_cron_jobs.php');
		}
	}
	function saveEmailData($aData) {
		$sSetString = "
			`Name` = '{$aData['Name']}',
			`Subject` = '".mysql_real_escape_string(serialize($aData['Subject']))."',
			`Body` = '".mysql_real_escape_string(serialize($aData['Body']))."',
			`Filter` = '".serialize($aData['Filter'])."',
			`FilterQuery` = '".$aData['FilterQuery']."',
			`Schedule` = '".serialize($aData['Schedule'])."',
			`Options` = '".serialize($aData['Options'])."'
		";

		if ($aData['ID']) {
			$sQuery = "UPDATE `{$this->_sPrefix}mails` SET {$sSetString} WHERE `ID` = {$aData['ID']} LIMIT 1";
		} else {
			$sQuery = "INSERT INTO `{$this->_sPrefix}mails` SET {$sSetString}";
		}
		$this->query($sQuery);
	}
	function getMaillist($bActiveoOnly = false) {
		if ($bActiveoOnly) {
			$sQuery = "SELECT * FROM `{$this->_sPrefix}mails` WHERE `Active` = 1";
		} else {
			$sQuery = "SELECT * FROM `{$this->_sPrefix}mails` WHERE `Active` = 1 ";
			$sQuery .= "UNION ALL ";
			$sQuery .= "SELECT * FROM `{$this->_sPrefix}mails` WHERE `Active` = 0";
		}
		$aMails = $this->getAll($sQuery);

		foreach ($aMails as $iKey => $aMail) {
			$aMails[$iKey]['Subject'] = unserialize($aMail['Subject']);
			$aMails[$iKey]['Body'] = unserialize($aMail['Body']);
			$aMails[$iKey]['Filter'] = unserialize($aMail['Filter']);
			$aMails[$iKey]['Schedule'] = unserialize($aMail['Schedule']);
			$aMails[$iKey]['Options'] = unserialize($aMail['Options']);
		}
		return $aMails;
	}
	function setMailStatus($iId, $iStatus) {
		$this->query("UPDATE `{$this->_sPrefix}mails` SET `Active` = {$iStatus} WHERE `ID` = {$iId} LIMIT 1");
	}
	function deleteMail($iId) {
		$this->query("DELETE FROM `{$this->_sPrefix}mails` WHERE `ID` = {$iId} LIMIT 1");
	}
	function getMailData($iId) {
		$aData = $this->getRow("SELECT * FROM `{$this->_sPrefix}mails` WHERE `ID` = {$iId} LIMIT 1");
		$aData['Subject'] = unserialize($aData['Subject']);
		$aData['Body'] = unserialize($aData['Body']);
		$aData['Filter'] = unserialize($aData['Filter']);
		$aData['Schedule'] = unserialize($aData['Schedule']);
		$aData['Options'] = unserialize($aData['Options']);
		return $aData;
	}
	function queueMail($sEmail, $sSubject, $sBody) {
		$sEmail = addslashes($sEmail);
		$sSubject = addslashes($sSubject);
		$sBody = addslashes($sBody);
		$this->query("INSERT INTO `sys_sbs_queue` SET `email` = '{$sEmail}', `subject` = '{$sSubject}', `body` = '{$sBody}'");
	}
	function sendMessage($iRecipient, $iSender, $sSubject, $sBody) {
		$sSubject = addslashes($sSubject);
		$sBody = addslashes($sBody);
		$sQuery =
        "
            INSERT INTO
                `sys_messages`
            SET
                `Sender`       = {$iSender},
                `Recipient`    = {$iRecipient},
                `Subject`      =  '{$sSubject}',
                `Text`         =  '{$sBody}',
                `Date`         = NOW(),
                `New`          = '1',
                `Type`         = 'letter'
        ";
        $this->query($sQuery);

        //--- create system event
        bx_import('BxDolAlerts');
        $aAlertData = array(
            'msg_id'          => db_last_id(),
            'subject'         => $sSubject,
            'body'            => $sBody,
        );
        $oZ = new BxDolAlerts('profile', 'send_mail_internal', $iSender, $iRecipient, $aAlertData);
        $oZ -> alert();
	}
	function addAqbPoints($iProfile, $iPoints, $sReason) {
		$this->query("UPDATE `Profiles` SET `AqbPoints` = `AqbPoints` + {$iPoints} WHERE `ID` = {$iProfile} LIMIT 1");
		createUserDataFile($iProfile);
		if (isset($GLOBALS['aUser'][$iProfile])) $GLOBALS['aUser'][$iProfile]['AqbPoints'] += $iPoints;
		$this->query("INSERT INTO `aqb_points_history` SET `profile_id` = {$iProfile}, `reason` = '".addslashes($sReason)."', `points` = {$iPoints}, `time` = ".time());
	}
}
?>