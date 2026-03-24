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

bx_import('BxDolModuleDb');

class AqbPhotoCaptchaDb extends BxDolModuleDb {
	/*
	 * Constructor.
	 */
	var $_oConfig;
	function __construct(&$oConfig) {
		parent::__construct($oConfig);
		$this->_oConfig = $oConfig;
	}

	function addPhoto($sExt, $sQuestion) {
		$sQuestion = addslashes($sQuestion);
		$this->query("INSERT INTO `{$this->_sPrefix}questions` (`Question`, `ImageExt`) VALUES ('{$sQuestion}', '{$sExt}')");
		return $this->lastId();
	}

	function getAllCaptchas() {
		return $this->getAll("SELECT `ID`, `Question` FROM `{$this->_sPrefix}questions` ORDER BY `ID` DESC");
	}

	function getCaptchasExt($iID) {
		return $this->getOne("SELECT `ImageExt` FROM `{$this->_sPrefix}questions` WHERE `ID` = {$iID} LIMIT 1");
	}

	function deleteCaptcha($iID) {
		$this->query("DELETE FROM `{$this->_sPrefix}questions` WHERE `ID` = {$iID} LIMIT 1");
	}

	function getRandomQuestion() {
		return $this->getRow("SELECT `ID`, `Question` FROM `{$this->_sPrefix}questions` ORDER BY RAND() LIMIT 1");
	}

	function getQuestion($iID) {
		$iID = intval($iID);
		if (!$iID) return false;
		return $this->getOne("SELECT `Question` FROM `{$this->_sPrefix}questions` WHERE `ID` = {$iID} LIMIT 1");
	}

	function getCaptchasCount() {
		return $this->getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}questions`");
	}
}