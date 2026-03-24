<?php
/***************************************************************************
* Date				: Monday October 4, 2010
* Copywrite			: (c) 2010 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Site Stat Manager
* Product Version	: 1.1
*
* IMPORTANT: This is a commercial product made by Dean Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean Bassett Jr.
*
***************************************************************************/

bx_import('BxDolModuleTemplate');

/*
 * Quotes module View
 */
class BxSiteStatManagerTemplate extends BxDolModuleTemplate {
	/**
	* Constructor
	*/
	function BxSiteStatManagerTemplate(&$oConfig, &$oDb) {
		parent::BxDolModuleTemplate($oConfig, $oDb);

		$this->_aTemplates = array('unit', 'adm_unit');
	}

	function loadTemplates() {
	    parent::loadTemplates();
	}

	function parseHtmlByName ($sName, &$aVars) {        
		return parent::parseHtmlByName ($sName.'.html', $aVars);
	}
}

?>