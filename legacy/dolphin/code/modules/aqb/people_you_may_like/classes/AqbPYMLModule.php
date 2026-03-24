<?php
/***************************************************************************
*
*     copyright            : (C) 2017 AQB Soft
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
bx_import('BxDolAdminSettings');

class AqbPYMLModule extends BxDolModule {
	/**
	 * Constructor
	 */
	function __construct($aModule) {
	    parent::__construct($aModule);
	}

	function serviceGetProfilesBlock($sPage) {
		$iProfile = getLoggedId();
		if (!$iProfile) return;

		if ($sPage == 'profile' && getID($_GET['ID']) != $iProfile) return;

		$aKnownPages = array(
			'profile' => getProfileLink($iProfile),
			'index' => BX_DOL_URL_ROOT,
			'member' => BX_DOL_URL_ROOT.'member.php',
		);
		if (!$aKnownPages[$sPage]) return;

		return $this->_oTemplate->showMatches($iProfile, $aKnownPages[$sPage]);
	}

	function getSettingsForm() {
		$iCat = $this->_oDb->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name`='aqb_pyml'");
        $oSettings = new BxDolAdminSettings($iCat, BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri().'admin/');
        return $oSettings->getForm();
	}

	function saveSettings() {
		$iId = (int)$this->_oDb->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name`='aqb_pyml'");
	    $oSettings = new BxDolAdminSettings($iId);
	    $oSettings->saveChanges($_POST);
	}

	function saveFields() {
		$aFields = array();
		if ($_POST['fields'])
		foreach ($_POST['fields'] as $iField) {
			if (!$iField) continue;
			$aFields[] = array(
				'field_id' => $iField,
				'type' => $_POST['types'][$iField] == 'dontmatch' ? 'DONTMATCH' : 'MATCH',
			);
		}

		$this->_oDb->saveFields($aFields);
	}
}