<?php
/***************************************************************************
*
*     copyright            : (C) 2013 AQB Soft
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
bx_import('BxDolPFM');

class AqbAdvancedSearchFormModule extends BxDolModule {
	/**
	 * Constructor
	 */
	function __construct($aModule) {
	    parent::__construct($aModule);
	}

	function getSearchFields() {
		$aFields = $this->_oDb->getSearchFields();
		if (!$aFields) return MsgBox(_t('_aqb_advanced_search_form_no_fields'));
		return $this->_oTemplate->getSearchFields($aFields);
	}

	function actionGetPropertiesForm() {
		$iFieldID = intval($_REQUEST['field']);
		$aField = $this->_oDb->getFieldProperties($iFieldID);
		if (!$aField) {
			PopUpBox('aqb_asf_popup', 'Error', 'Field not found. Reloading a page may solve the problem.');
		}
		return PopUpBox('aqb_asf_popup', $aField['Name'], '<div class="bx-def-padding">'.$this->_oTemplate->getPropertiesForm($aField).'</div><style>#aqb_asf_popup{display:none;}</style>');
	}

	function actionSaveProperties() {
		$iFieldID = intval($_REQUEST['id']);
		if (!$iFieldID) die('Field not found. Reloading a page may solve the problem.');

		$sControl = $_REQUEST['control'];

		$bShowEmptyValue = $_REQUEST['show_empty_value'] ? 1 : 0;

		$this->_oDb->saveProperties($iFieldID, $sControl, $bShowEmptyValue);
		return MsgBox(_t('_Saved'));
	}

	function serviceSetAdvancedProperties(&$aField) {
		static $aField2Override = null;
		if (is_null($aField2Override)) $aField2Override = $this->_oDb->getFields2Override();

		if (!$aField2Override || !$aField2Override[$aField['name']]) return;

		$aProperties = $aField2Override[$aField['name']];

		if ($aProperties['ShowEmptyValue'])
			$aField['values'] = array('' => _t('_aqb_advanced_search_form_empty')) + $aField['values'];

		if ($aProperties['Control'] != 'default') {
			$aField['type'] = $aProperties['Control'];
			if ($aProperties['Control'] == 'select_box') $aField['attrs']['add_other'] = 'false';
		}
	}
}