<?php
/**********************************************************************************
*                            IBDW 1Col Dolphin Smart Community Builder
*                              -------------------
*     begin                : May 1 2010
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW 1Col is not free and you cannot redistribute and/or modify it.
* 
* IBDW 1Col is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/
bx_import('BxDolModuleDb');
class FirstColDb extends BxDolModuleDb {
	function FirstColDb(&$oConfig) {
		parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }

    function getSettingsCategory() {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'My First Colon' LIMIT 1");
    }    
}
?>