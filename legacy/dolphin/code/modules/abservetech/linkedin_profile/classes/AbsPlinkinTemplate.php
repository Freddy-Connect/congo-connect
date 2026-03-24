<?php

/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

bx_import('BxDolTwigTemplate');

class AbsPlinkinTemplate extends BxDolTwigTemplate
{
    var $_oModule;

    /**
     * Constructor
     */
    function AbsPlinkinTemplate(&$oConfig, &$oDb)
    {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }

    function init(&$oModule)
    {
        $this->_oModule = $oModule;
    }
      
}