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

class AqbSlideMConfig extends BxDolConfig {
    var $_sUri;
	var $_oDb;
	
	function AqbSlideMConfig($aModule) {
	    parent::BxDolConfig($aModule);
		$this -> _sUri = $this->getUri();		
	}
	
	function init(&$oDb) {
		$this -> _oDb = &$oDb;
	}
	
	function getMenuSide(){
		return $this -> _oDb -> getParam($this -> _sUri . '_side');
	}
	
	function getWidth(){
		return $this -> _oDb -> getParam($this -> _sUri . '_width');
	}
	
	function isMenuEnabled(){
		return $this -> _oDb -> getParam($this -> _sUri . '_enable') == 'on';
	}	
	
	function getMenuType(){
		return $this -> _oDb -> getParam($this -> _sUri . '_type');
	}	
	
}
?>