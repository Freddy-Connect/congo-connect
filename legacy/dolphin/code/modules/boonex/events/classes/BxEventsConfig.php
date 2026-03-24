<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Confession
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/
 
bx_import('BxDolConfig');

class BxEventsConfig extends BxDolConfig {

	var $_sPurchaseBaseUrl;
	var $_sPurchaseCurrency;
	var $_sPurchaseCallbackUrl;
	var $_sFeaturedCallbackUrl;
	var $_sReturnUrl; 
 	var $_sCurrencyCode;
	var $_sCurrencySign;
 	var $sMediaPath;
	var $sMediaUrl;

	/**
	 * Constructor
	 */
	function BxEventsConfig($aModule) {
	    parent::BxDolConfig($aModule);

	     $this->_oDb = null; 
    }

	function init(&$oDb) {
	    $this->_oDb = &$oDb;
	     
	    $this->_sCurrencySign = $this->_oDb->getParam('bx_events_currency_sign');
	    $this->_sCurrencyCode = $this->_oDb->getParam('bx_events_currency_code');
        $this->_sPurchaseCurrency = $this->_oDb->getParam('bx_events_currency_code'); 
        $this->_sPurchaseBaseUrl = 'https://www.paypal.com/cgi-bin/webscr'; 
		$this->_sPurchaseCallbackUrl = BX_DOL_URL_ROOT . $this -> getBaseUri() . 'paypal_process/';  
 		$this->_sFeaturedCallbackUrl = BX_DOL_URL_ROOT . $this -> getBaseUri() . 'paypal_featured_process/';   
		$this->_sReturnUrl = BX_DOL_URL_ROOT . $this -> getBaseUri() . 'browse/my';  
        $this->sMediaPath = BX_DIRECTORY_PATH_MODULES . "boonex/events/media/"; 
		$this->sMediaUrl =  BX_DOL_URL_MODULES . "boonex/events/media/";   
	}

	function getMediaPath() {
	    return $this->sMediaPath;
	}

	function getMediaUrl() {
	    return $this->sMediaUrl;
	}

	function getReturnUrl() {
	    return $this->_sReturnUrl;
	}

	function getPurchaseBaseUrl() {
	    return $this->_sPurchaseBaseUrl;
	}

	function getPurchaseCallbackUrl() {
	    return $this->_sPurchaseCallbackUrl;
	}

 	function getFeaturedCallbackUrl() {
	    return $this->_sFeaturedCallbackUrl;
	}
 
	function getCurrencySign() {
	    return $this->_sCurrencySign;
	}

	function getCurrencyCode() {
	    return $this->_sCurrencyCode;
	}

	function getPurchaseCurrency() {
	    return $this->_sPurchaseCurrency;
	}




}
