<?php

bx_import('BxDolCron');

require_once('DbBruteforceModule.php');

class DbBruteforceCron extends BxDolCron
{
    var $oBruteforceObject;
    var $iDaysForRows;

    /**
     * Class constructor;
     */
    function DbBruteforceCron()
    {
        $this -> oBruteforceObject = BxDolModule::getInstance('DbBruteforceModule');
        $this -> iLockTime = getParam('db_bruteforce_time');
    }

    /**
     * Function will delete old entries;
     */
    function processing()
    {
        $this -> oBruteforceObject -> _oDb -> deleteEntries($this -> iLockTime);
    }
}


