<?php

bx_import('BxDolConfig');

class DbForgotConfig extends BxDolConfig
{
    var $iDaysForRows;

    function DbForgotConfig($aModule)
    {
        parent::BxDolConfig($aModule);
        $this -> iDaysForRows     = getParam('forgot_keep_rows_days');
    }
}

?>