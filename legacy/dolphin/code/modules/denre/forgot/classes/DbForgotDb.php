<?php

bx_import('BxDolModuleDb');

class DbForgotDb extends BxDolModuleDb
{

    function DbForgotDb(&$oConfig)
    {
        parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }

    function getSettingsCategory()
    {
        $ret = $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'DB Advanced Forgot' LIMIT 1");
        if(isset($ret))
            return $ret;
        else
            return false;
    }

    function deleteUnconfirmedRetrievals($iDays=0)
    {
        $iDays = (int) $iDays;
        if ($iDays < 1)
            return 0;

        $iAffectedRows = $this->query("DELETE FROM `db_tmp_password` WHERE `db_tmp_password`.`date` < DATE_SUB(NOW(), INTERVAL $iDays DAY)");
        $this->query("OPTIMIZE TABLE `db_tmp_password`");

        return $iAffectedRows;
    }

}
?>