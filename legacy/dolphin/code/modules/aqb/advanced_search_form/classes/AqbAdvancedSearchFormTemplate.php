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

bx_import('BxDolModuleTemplate');
bx_import('BxTemplFormView');
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

class AqbAdvancedSearchFormTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function __construct(&$oConfig, &$oDb) {
	    parent::__construct($oConfig, $oDb);
	}

	function getSearchFields($aFields) {
		$this->addAdminCss('forms_adv.css');
		$sBaseUri = BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri();
		return $this->parseHtmlByName('fields.html', array(
			'base_url' => BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri(),
			'aqb_loading_field' => LoadingBox('aqb_loading_field'),
			'bx_repeat:fields' => $aFields
		));
	}

	function getPropertiesForm($aField) {
		$aForm = array(
			'form_attrs' => array(
				'method' => 'post',
				'action' => '',
				'onsubmit' => 'saveFieldProperties(this); return false;',
			),
			'inputs' => array(
				'id' => array(
					'type' => 'hidden',
					'name' => 'id',
					'value' => $aField['ID'],
				),
				'control' => array(
					'type' => 'select',
					'name' => 'control',
					'caption' => _t('_aqb_advanced_search_form_control'),
					'values' => array(
						'default' => _t('_aqb_advanced_search_form_control_default'),
						'select_box' => _t('_aqb_advanced_search_form_control_boxes'),
						'select_multiple' => _t('_aqb_advanced_search_form_control_multiselectable'),
						'checkbox_set' => _t('_aqb_advanced_search_form_control_checkboxes')
					),
					'value' => $aField['Control'],
				),
				'empty_value' => array(
					'type' => 'checkbox',
					'name' => 'show_empty_value',
					'caption' => _t('_aqb_advanced_search_form_empty_value'),
					'info' => _t('_aqb_advanced_search_form_empty_value_info'),
					'checked' => $aField['ShowEmptyValue'],
				),
				'submit' => array(
					'type' => 'submit',
					'name' => 'save',
					'value' => _t('_Save'),
				),
			),
		);

		$oFrom = new BxTemplFormView($aForm);
		return '<div style="position: relative;" id="aqb_asf_form">'.$oFrom->getCode().LoadingBox('aqb_form_loading').'</div>';
	}
}