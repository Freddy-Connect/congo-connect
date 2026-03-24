<?


bx_import('BxDolModuleDb');

class MlPMRDb extends BxDolModuleDb {

	function MlPMRDb(&$oConfig) {
		parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }
    	function insertSentDate($iProfileId, $sDate)
    	{
 				//$this->query("INSERT INTO `ml_passive_reminder_profiles`(`user_id`, `date_sent`) VALUES ('{$iProfileId}', '{$sDate}') ON DUPLICATE KEY UPDATE `date_sent`='{$sDate}'");
				$this->query("INSERT INTO `ml_passive_reminder_profiles`(`user_id`, `date_sent`) VALUES ('{$iProfileId}', '{$sDate}')");
    	}
    	function updateSentDate($iProfileId, $sDate)
    	{
    		$this->query("UPDATE `ml_passive_reminder_profiles` SET `date_sent` = '{$sDate}' WHERE `user_id` = {$iProfileId} LIMIT 1");
    	}
    	function getSentDate($iProfileId)
    	{
 				return $this->getOne("SELECT `date_sent` FROM `ml_passive_reminder_profiles` WHERE `user_id` = {$iProfileId} LIMIT 1");
 				//$this->getOne("SELECT `date_sent` FROM `ml_passive_reminder_profiles` LEFT JOIN `Profiles` ON `Profiles`.`ID` = `user_id` WHERE `user_id` = {$iProfileId} AND `Profiles`.`DateLastNav` < SUBDATE(NOW(), INTERVAL {$iSendInterval} DAY)");
    	}
			function getAllPassiveMembers($iDaysInterval)
			{
				return $this->getAll("SELECT Profiles.`ID`, `NickName`, `Email`, `DateLastNav` FROM Profiles WHERE `Status` = 'Active' AND `DateLastNav` < SUBDATE(NOW(), INTERVAL {$iDaysInterval} DAY)");
			}

			function getEmailTemplate()
			{
				return $this->getRow("SELECT `Subject`, `Body` FROM `sys_email_templates` WHERE `Name` ='t_PassiveMemberReminder' LIMIT 1");

			}
			function insertEmailQueue($sEmail, $sMailSubject, $sMailBody)
			{
				$this->query("INSERT INTO `sys_sbs_queue` SET `email`='{$sEmail}', `subject`='{$sMailSubject}', `body`='{$sMailBody}'");
			}
			function getBlockId(){ 
				return $this->getOne("SELECT `ID` FROM `sys_page_compose` WHERE `Content` = 'return BxDolService::call(\'passive_reminder\', \'passive_reminder_friends_block\', array());' AND `Func`='PHP'");
			}
			function getMyPassiveFriends($iMemberID, $sWhereParam = '', $sqlLimit = '') 
			{
				return $this->getAll("
					SELECT p.NickName, p.DateLastNav, p.ID
					FROM `Profiles` AS p
					LEFT JOIN `sys_friend_list` AS f1 ON (f1.`ID` = p.`ID` AND f1.`Profile` ='{$iMemberID}' AND `f1`.`Check` = 1)
					LEFT JOIN `sys_friend_list` AS f2 ON (f2.`Profile` = p.`ID` AND f2.`ID` ='{$iMemberID}' AND `f2`.`Check` = 1)
					WHERE 1
					AND (f1.`ID` IS NOT NULL OR f2.`ID` IS NOT NULL)
					{$sWhereParam}
					ORDER BY p.`DateLastNav` DESC
					{$sqlLimit}
				");
			
			}
       				           
    function getSettingsCategory() {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Modloaded Passive Member Reminder' LIMIT 1");
    }    

    
}

?>
