<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

bx_import('BxDolModule');

class BxMoxieModule extends BxDolModule
{
    function BxMoxieModule($aModule)
    {
        parent::BxDolModule($aModule);
    }

    function serviceResponse($oAlert)
    {
    	if($oAlert->sUnit != 'system' || $oAlert->sAction != 'attach_editor')
    		return;

    	$oAlert->aExtras['modules'][] = "moxiemanager: '" . $this->_oConfig->getDataUrl() . "moxiemanager/plugin.min.js'";
    }
}
