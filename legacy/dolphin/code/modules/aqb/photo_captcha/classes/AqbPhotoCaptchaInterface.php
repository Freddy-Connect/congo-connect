<?php
/***************************************************************************
*
*     copyright            : (C) 2014 AQB Soft
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

bx_import('BxDolCaptcha');
bx_import('BxDolModule');

class AqbPhotoCaptchaInterface extends BxDolCaptcha {
    var $_oModule;

    function __construct($aObject) {
        parent::__construct ($aObject);
        $this->_oModule = BxDolModule::getInstance('AqbPhotoCaptchaModule');
    }

    function display() {
        return $this->_oModule->displayCaptcha();
    }

    function check() {
        return $this->_oModule->checkCaptcha($this->getUserResponse());
    }

    function getUserResponse() {
        return process_pass_data(bx_get('aqb_photo_captcha_answer'));
    }

    function isAvailable() {
    	return $this->_oModule->isAvailable();
    }

    function _addJsCss($bDynamicMode = false) {
        return '';
    }
}