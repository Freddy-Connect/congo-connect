<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
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

class BxJobsConfig extends BxDolConfig {

    var $_oDb;
    var $_sIconsFolder;
 
	var $_sPurchaseBaseUrl;
	var $_sPurchaseCurrency;
	var $_sPurchaseCallbackUrl;
	var $_sFeaturedCallbackUrl;
	var $_sReturnUrl; 
 	var $_sCurrencyCode;
	var $_sCurrencySign;
	var $sMediaPath;
	var $sMediaUrl;
	
	//Freddy ajout integration du logo de business listing
      var $sMediaBusinessListingUrl;



	/**
	 * Constructor
	 */
	function BxJobsConfig($aModule) {
	    parent::BxDolConfig($aModule);

	     $this->_oDb = null; 
	     $this->_sIconsFolder = 'modzzz/jobs/media/images/company/';
	     $this->_sResumeFolder = 'modzzz/jobs/media/resume/'; 

        $this->sMediaPath = BX_DIRECTORY_PATH_MODULES . "modzzz/jobs/media/"; 
		$this->sMediaUrl =  BX_DOL_URL_MODULES . "modzzz/jobs/media/"; 
		
		//Freddy ajout integration du logo de business listing
		$this->sMediaBusinessListingUrl =  BX_DOL_URL_MODULES . "modzzz/listing/media/";
		///////////////////////////////////////////////////////////////// 

    }
 
	function init(&$oDb) {
	    $this->_oDb = &$oDb;
	     
	    $this->_sCurrencySign = $this->_oDb->getParam('modzzz_jobs_currency_sign');
	    $this->_sCurrencyCode = $this->_oDb->getParam('modzzz_jobs_currency_code');
        $this->_sPurchaseCurrency = $this->_oDb->getParam('modzzz_jobs_currency_code'); 
        $this->_sPurchaseBaseUrl = 'https://www.paypal.com/cgi-bin/webscr'; 
		$this->_sPurchaseCallbackUrl = BX_DOL_URL_ROOT . $this -> getBaseUri() . 'paypal_process/';  
 		$this->_sFeaturedCallbackUrl = BX_DOL_URL_ROOT . $this -> getBaseUri() . 'paypal_featured_process/';   
		$this->_sReturnUrl = BX_DOL_URL_ROOT . $this -> getBaseUri() . 'view/';   
	}

	function getMediaPath() {
	    return $this->sMediaPath;
	}

	function getMediaUrl() {
	    return $this->sMediaUrl;
	}
	
	// Freddy ajout integration du logo de business listing
	function getMediaLogoBusinessListingUrl() {
	    return $this->sMediaBusinessListingUrl;
	}
	/////////////////////////////////////////////////////////////
	
	
 
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

	function getCompanyIconsUrl() {
	    return BX_DOL_URL_MODULES . $this->_sIconsFolder;
	}

	function getCompanyIconsPath() {
	    return BX_DIRECTORY_PATH_MODULES . $this->_sIconsFolder;
	}

	function getResumeUrl() {
	    return BX_DOL_URL_MODULES . $this->_sResumeFolder;
	}

	function getResumePath() {
	    return BX_DIRECTORY_PATH_MODULES . $this->_sResumeFolder;
	}
 

}
