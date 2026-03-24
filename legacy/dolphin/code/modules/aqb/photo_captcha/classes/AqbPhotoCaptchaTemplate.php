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

bx_import('BxDolModuleTemplate');
bx_import('BxTemplFormView');

class AqbCheckerHelper extends BxDolFormCheckerHelper {
    function checkPhoto($s) {
        if (empty($_FILES['photo']['tmp_name']) || $_FILES['photo']['error'] != 0) return false;

        $scan = getimagesize($_FILES['photo']['tmp_name']);
        if ( $scan['mime'] != 'image/jpeg' && $scan['mime'] != 'image/gif' && $scan['mime'] != 'image/png') return false;

        return true;
    }
}


class AqbPhotoCaptchaTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function __construct(&$oConfig, &$oDb) {
	    parent::__construct($oConfig, $oDb);
	}

	function getAddCaptchaForm($bReset = false) {
		$aLangs = getLangsArr(false, false);
		$sDefault = getParam('lang_default');

		$aForm = array(
			'form_attrs' => array(
				'method' => 'post',
				'action' => BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri().'admin/',
				'enctype' => 'multipart/form-data',
			),
			'params' => array(
				'db' => array(
					'submit_name' => 'add',
				),
				'checker_helper' => 'AqbCheckerHelper',
			),
			'inputs' => array(
				'photo' => array(
					'type' => 'file',
					'name' => 'photo',
					'caption' => _t('_aqb_photo_captcha_photo'),
					'required' => true,
					'checker' => array(
            			'func' => 'photo',
            			'error' => _t('_aqb_photo_captcha_photo_error'),
            		),
				),
			),
		);

		foreach ($aLangs as $sLangISO => $sLangName) {
			$aForm['inputs']['question_'.$sLangISO] = array(
				'type' => 'textarea',
				'name' => 'question_'.$sLangISO,
				'caption' => _t('_aqb_photo_captcha_question', $sLangName),
				'required' => $sLangISO == $sDefault,
				'info' => $sLangISO != $sDefault ? _t('_aqb_photo_captcha_question_info', $aLangs[$sDefault]) : '',
				'checker' => $sLangISO == $sDefault ? array(
					'func' => 'avail',
					'error' => _t('_aqb_photo_captcha_question_required'),
				) : null,
				'attrs' => array(
					'style' => 'height: 52px',

				),
			);

			$aForm['inputs']['answer_'.$sLangISO] = array(
				'type' => 'text',
				'name' => 'answer_'.$sLangISO,
				'caption' => _t('_aqb_photo_captcha_answer', $sLangName),
				'required' => $sLangISO == $sDefault,
				'info' => _t('_aqb_photo_captcha_answer_info').($sLangISO != $sDefault ? ' '._t('_aqb_photo_captcha_question_info', $aLangs[$sDefault]) : ''),
				'checker' => $sLangISO == $sDefault ? array(
					'func' => 'avail',
					'error' => _t('_aqb_photo_captcha_answer_required'),
				) : null,
			);
		}


		$aForm['inputs']['submit'] = array(
			'type' => 'submit',
			'name' => 'add',
			'value' => _t('_aqb_photo_captcha_add'),
		);

		$oFrom = new BxTemplFormView($aForm);
		if (!$bReset) $oFrom->initChecker($_POST);
		return $oFrom;
	}

	function listCaptchas($aCaptchas) {
		$this->addJsTranslation('_aqb_photo_captcha_confirm_delete');

		$aCaptchasTmpl = array();

		foreach ($aCaptchas as $aCaptcha) {
			$aCaptchasTmpl[] = array(
				'module_url' => BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri(),
				'id' => $aCaptcha['ID'],
				'question' => $this->displayQuestion($aCaptcha['Question']),
			);
		}

		return $this->parseHtmlByName('captchas.html', array(
			'module_url' => BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri(),
			'bx_repeat:captchas' => $aCaptchasTmpl,
		));
	}

	function displayQuestion($sQuestion) {
		static $aLangs;
		if (!$aLangs) $aLangs = getLangsArr(false, false);
		$sDefault = getParam('lang_default');

		$aQuestion = unserialize($sQuestion);

		$aLangsQuestion = array();
		foreach ($aQuestion as $sLangISO => $aQA) {
			$aLangsQuestion[] = array(
				'q_cpt' => _t('_aqb_photo_captcha_question', $aLangs[$sLangISO]),
				'q' => htmlspecialchars($aQA['q']),
				'a_cpt' => _t('_aqb_photo_captcha_answer', $aLangs[$sLangISO]),
				'a' => htmlspecialchars($aQA['a'] ? $aQA['a'] : $aQuestion[$sDefault]['a']),
			);
		}

		return $this->parseHtmlByName('question.html', array(
			'bx_repeat:langs' => $aLangsQuestion,
		));
	}

	function displayCaptcha($aCaptcha) {
		$sCurLang = $GLOBALS['sCurrentLanguage'];
		$sDefault = getParam('lang_default');

		$aQuestions = unserialize($aCaptcha['Question']);
		if (isset($aQuestions[$sCurLang])) $aQuestion = $aQuestions[$sCurLang];
		elseif (isset($aQuestions[$sDefault])) $aQuestion = $aQuestions[$sDefault];
		else $aQuestion = array_shift($aQuestions);

		return $this->parseHtmlByName('captcha.html', array(
			'id' => $aCaptcha['ID'],
			'module_url' => BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri(),
			'q' => htmlspecialchars($aQuestion['q']),
		));
	}
}