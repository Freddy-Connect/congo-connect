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

bx_import('BxDolModule');

class AqbPhotoCaptchaModule extends BxDolModule {
	/**
	 * Constructor
	 */
	function __construct($aModule) {
	    parent::__construct($aModule);
	}

	function addCaptcha() {
		$oForm = $this->_oTemplate->getAddCaptchaForm();
		if ($oForm->isSubmittedAndValid()) {
			$scan = getimagesize($_FILES['photo']['tmp_name']);

			$aExt = array(
				'image/jpeg' => 'jpg',
				'image/gif' => 'gif',
				'image/png' => 'png',
			);
			$sExt = $aExt[$scan['mime']];

			$aQuestion = array();
			$aLangs = getLangsArr(false, false);
			foreach ($aLangs as $sLangISO => $sLangName) {
				$sQ = trim($_POST['question_'.$sLangISO]);
				$sA = trim($_POST['answer_'.$sLangISO]);
				if (!$sQ) continue;
				$aQuestion[$sLangISO] = array(
					'q' => $sQ,
					'a' => $sA,
				);
			}

			$sQuestion = serialize($aQuestion);

			$iId = $this->_oDb->addPhoto($sExt, $sQuestion);
			$sDst = $this->_oConfig->getHomePath().'images/'.$iId.'.'.$sExt;
			move_uploaded_file($_FILES['photo']['tmp_name'], $sDst);

			if ($scan[0] >= 256 || $scan[1] >= 256) imageResize($sDst, $sDst, 256, 256);

			$sMessage = MsgBox(_t('_aqb_photo_captcha_added'), 2);
			$oForm = $this->_oTemplate->getAddCaptchaForm(true);
		}
		return $sMessage.$oForm->getCode();
	}

	function listCaptchas() {
		$aCaptchas = $this->_oDb->getAllCaptchas();
		if (!$aCaptchas) return MsgBox(_t('_Empty'));
		else return $this->_oTemplate->listCaptchas($aCaptchas);
	}

	function actionGetCaptchaImage($iID) {
		$iID = intval($iID);
		if (!$iID) die('Captcha ID is missing');

		$sExt = $this->_oDb->getCaptchasExt($iID);

		$this->outCaptchasImageFile($iID, $sExt);
	}

	function actionDeleteCaptcha($iID) {
		$iID = intval($iID);
		if (!isAdmin()) die('Must be logged as admin');
		if (!$iID) die('Captcha ID is missing');

		$sExt = $this->_oDb->getCaptchasExt($iID);
		$sPath = $this->_oConfig->getHomePath().'images/'.$iID.'.'.$sExt;
		unlink($sPath);
		$this->_oDb->deleteCaptcha($iID);
	}

	function outCaptchasImageFile($iID, $sExt) {
		$aExt = array(
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'png' => 'image/x-png',
		);

        $sCntType = $aExt[$sExt];

        $sPath = $this->_oConfig->getHomePath().'images/'.$iID.'.'.$sExt;

        $iLastModTime = filemtime($sPath);

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0, max-age={$this->iHeaderCacheTime}, Last-Modified: " . gmdate("D, d M Y H:i:s", $iLastModTime) . " GMT");
        header("Content-Type:" . $sCntType);
        header("Content-Length: " . filesize($sPath));
        readfile($sPath);
	}

	function displayCaptcha() {
		$aQuestion = $this->_oDb->getRandomQuestion();
		if (!$aQuestion) return '';

    	bx_import('BxDolSession');
		$oSession = BxDolSession::getInstance();
		$oSession->setValue('aqb_photo_captcha_question_id', $aQuestion['ID']);

		return $this->_oTemplate->displayCaptcha($aQuestion);
	}

	function checkCaptcha($sAnswer) {
		$sAnswer = mb_strtolower(trim($sAnswer));

		bx_import('BxDolSession');
		$oSession = BxDolSession::getInstance();
		$iQuestionId = $oSession->getValue('aqb_photo_captcha_question_id');

		$aAnswers = array();
		$sQuestion = $this->_oDb->getQuestion($iQuestionId);
		if (!$sQuestion) return false;
		$aQuestions = unserialize($sQuestion);

		$sCurLang = $GLOBALS['sCurrentLanguage'];
		$sDefault = getParam('lang_default');

		if (isset($aQuestions[$sCurLang])) $aQuestion = $aQuestions[$sCurLang];
		elseif (isset($aQuestions[$sDefault])) $aQuestion = $aQuestions[$sDefault];
		else $aQuestion = array_shift($aQuestions);

		if (!empty($aQuestion['a'])) $aAnswers = explode(',', $aQuestion['a']);
		elseif (!empty($aQuestions[$sDefault]['a'])) $aAnswers = explode(',', $aQuestions[$sDefault]['a']);

		if (!$aAnswers) return false;

		foreach ($aAnswers as $iKey => $sVal) {
			if (mb_strtolower(trim($sVal)) == $sAnswer) return true;
		}

		return false;
	}

	function isAvailable() {
		return $this->_oDb->getCaptchasCount() > 0;
	}
}
