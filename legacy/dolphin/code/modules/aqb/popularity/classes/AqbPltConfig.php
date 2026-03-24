<?php
/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*      
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY. 
* To be able to use this product for another domain names you have to order another copy of this product (license).
* 
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
* 
* This notice may not be removed from the source code.
* 
***************************************************************************/

bx_import('BxDolConfig');

class AqbPltConfig extends BxDolConfig {
    var $_oDb;

    var $_iPerPage;
    var $_sDefaultDuration;

    var $_aHtmlIds;
	var $_aJsObjects;
    var $_sAnimationEffect;
    var $_iAnimationSpeed;

	/**
	 * Constructor
	 */
	function AqbPltConfig($aModule) {
	    parent::BxDolConfig($aModule);
	}

	function init(&$oDb) {
	    $this->_oDb = &$oDb;

	    $this->_iPerPage = (int)$this->_oDb->getParam('aqb_popularity_per_page');
	    $this->_sDefaultDuration = $this->_oDb->getParam('aqb_popularity_def_duration');

		$this->_aHtmlIds = array(
			'profiles_' . AQB_PLT_TYPE_VIEWED_ME => 'aqb-profiles-viewed-me',
			'profiles_' . AQB_PLT_TYPE_FAVORITED_ME => 'aqb-profiles-favorited-me',
			'profiles_' . AQB_PLT_TYPE_SUBSCRIBED_ME => 'aqb-profiles-subscribed-me',
			'loading_' . AQB_PLT_TYPE_VIEWED_ME => 'aqb-gst-loading-viewed-me',
			'loading_' . AQB_PLT_TYPE_FAVORITED_ME => 'aqb-gst-loading-favorited-me',
			'loading_' . AQB_PLT_TYPE_SUBSCRIBED_ME => 'aqb-gst-loading-subscribed-me'
		);

		$this->_aJsObjects = array(
			'main' => 'oAqbPltMain'
		);

	    $this->_sAnimationEffect = 'fade';
	    $this->_iAnimationSpeed = 'slow';
	}
	function getPerPage() {
		return $this->_iPerPage;
	}
	function getDefaultDuration() {
		return $this->_sDefaultDuration;
	}
	function getJsObject($sType = '') {
		if(empty($sType))
			return $this->_aJsObjects;

		return $this->_aJsObjects[$sType];
	}
	function getHtmlId($sType = '') {
		if(empty($sType))
			return $this->_aHtmlIds;

		return $this->_aHtmlIds[$sType];
	}
	function getAnimationEffect() {
	    return $this->_sAnimationEffect;
	}
	function getAnimationSpeed() {
	    return $this->_iAnimationSpeed;
	}
}
?>