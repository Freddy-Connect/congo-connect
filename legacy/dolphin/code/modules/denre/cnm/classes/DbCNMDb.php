<?php
bx_import('BxDolModuleDb');

class DbCNMDb extends BxDolModuleDb
{
	function DbCNMDb(&$oConfig)
    {
		parent::BxDolModuleDb();
    }

    function getSettingsCategory()
    {
        $ret = $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'DbCNM' LIMIT 1");
        if(isset($ret))
            return $ret;
        else
            return false;
    }

}

?>