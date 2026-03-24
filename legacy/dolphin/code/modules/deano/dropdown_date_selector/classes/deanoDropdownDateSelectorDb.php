<?php
/***************************************************************************
* Date				: Wednesday August 21, 2013
* Copywrite			: (c) 2013 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Dropdown Date Selector
* Product Version	: 1.0.3
*
* IMPORTANT: This is a commercial product made by Dean J. Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean J. Bassett Jr.
*
***************************************************************************/

bx_import('BxDolModuleDb');

class deanoDropdownDateSelectorDb extends BxDolModuleDb {

	function deanoDropdownDateSelectorDb(&$oConfig) {
		parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }

    function getSettingsCategory() {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Deano - Dropdown Date Selector' LIMIT 1");
    }    
}

?>