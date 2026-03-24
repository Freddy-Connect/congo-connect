<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

bx_import('BxDolConfig');

class BxMoxieConfig extends BxDolConfig
{
	var $_sDirectoryData;

    function BxMoxieConfig($aModule)
    {
        parent::BxDolConfig($aModule);

        $this->_sDirectoryData = 'data/';
    }
    
	function getDataPath()
    {
        return $this->_sHomePath . $this->_sDirectoryData;
    }

    function getDataUrl()
    {
        return $this->_sHomeUrl . $this->_sDirectoryData;
    }
}
