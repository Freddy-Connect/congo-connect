<?php

bx_import('BxDolCron');

require_once('DbForgotModule.php');

class DbForgotCron extends BxDolCron
{
    var $oForgotObject;
    var $iDaysForRows;

    /**
     * Class constructor;
     */
    function DbForgotCron()
    {
        $this -> oForgotObject = BxDolModule::getInstance('DbForgotModule');
        $this -> iDaysForRows = $this -> oForgotObject -> _oConfig -> iDaysForRows;
    }

    /**
     * Function will delete unconfirmed password entries;
     */
    function processing()
    {
        if ($this -> iDaysForRows > 0)
            $this -> oForgotObject -> _oDb -> deleteUnconfirmedRetrievals($this -> iDaysForRows);
    }
}

?>