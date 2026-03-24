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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolConfig.php');

class BxSiteStatManagerConfig extends BxDolConfig {
	/**
	* Constructor
	*/
	function BxSiteStatManagerConfig($aModule) {
		parent::BxDolConfig($aModule);
	}
}

?>