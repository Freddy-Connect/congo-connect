<?php

bx_import('BxDolModuleDb');

class DbBruteforceDb extends BxDolModuleDb
{

	function DbBruteforceDb(&$oConfig)
    {
		parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }

    function getSettingsCategory()
    {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'DB Bruteforce' LIMIT 1;");
    }

    function getTimeInfo($iUserId)
    {
        (int) $iUserId;
        return $this->getOne("SELECT `login_time` FROM `db_bruteforce_cnt` WHERE `user_id` = $iUserId LIMIT 1;");
    }

    function getUserByNameEmail($sName)
    {
        $sNewName = mysql_escape_string($sName);
        return $this->getOne("SELECT `ID` FROM `Profiles` WHERE `NickName` = '$sNewName' OR `Email` = '$sNewName'");
    }

    function getCntInfo($iUserId)
    {
        (int) $iUserId;
        return $this->getAll("SELECT `counter`, `login_time` FROM `db_bruteforce_cnt` WHERE `user_id` = $iUserId LIMIT 1;");        
    }

    function updateCnt($iUserId)
    {
        (int) $iUserId;
        if($iUserId < 1)
            return;

        $this->query("INSERT INTO db_bruteforce_cnt (`user_id`, `counter`) VALUES ($iUserId, 1) ON DUPLICATE KEY UPDATE `counter` = `counter` + 1;");
    }

    function resetCnt($iUserId)
    {
        (int) $iUserId;
        $this->query("DELETE FROM `db_bruteforce_cnt` WHERE `user_id` = $iUserId;");        
    }

    function deleteEntries($iTime)
    {
        $this->query("DELETE FROM `db_bruteforce_cnt` WHERE `login_time` < NOW() - INTERVAL {$iTime} MINUTE;");
    }

}