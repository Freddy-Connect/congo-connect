<?php

/**
* Copyright (c) 2012-2016 Andreas Pachler - http://www.paan-systems.com
* This is a commercial product made by Andreas Pachler and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Andreas Pachler.
* This notice may not be removed from the source code.
*/

bx_import('BxDolModuleDb');

class MPGoogleAnalyticsDb extends BxDolModuleDb
{
    var $sTablePrefix;

    /**
     *
     *
     * @param unknown $oConfig (reference)
     */
    function MPGoogleAnalyticsDb(&$oConfig)
    {
        parent::BxDolModuleDb();

        $this->sTablePrefix = $oConfig->getDbPrefix();
    }

    /**
     *
     *
     * @return unknown
     */
    function getSettingsCategory()
    {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = '{$this->sTablePrefix}' LIMIT 1");
    }

}
