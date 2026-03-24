<?php
/***************************************************************************
*
*     copyright            : (C) 2016 AQB Soft
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


class AqbConditionalFieldsTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function __construct(&$oConfig, &$oDb) {
	    parent::__construct($oConfig, $oDb);
	}

	function getConditionalFieldsList() {
		$aFields = $this->_oDb->getConditionalFields();
		if (!$aFields) return MsgBox(_t('_Empty'));

		$sResult = '<ul>';
		foreach ($aFields as $aData) {
			$sResult .=
				'<li>'.
					_t('_aqb_conditional_fields_the').' '.
					($aData['type'] == 'block' ? _t('_aqb_conditional_fields_block') : _t('_aqb_conditional_fields_field')).' '.
					'<strong>'.$aData['field'].'</strong>'.' '.
					_t('_aqb_conditional_fields_depends_on').' '.
					'<strong>'.$aData['depends_on'].'</strong>'.' '.
					_t('_aqb_conditional_fields_show_if_value').' '.
					'<strong>'.$this->getUFValue($aData['values'], $aData['show_if_value']).'</strong>'.
					'&nbsp;&nbsp;&nbsp;<a href="#" title="Remove" onclick="aqb_conditional_fields_remove(\''.base64_encode($aData['field']).'\', \''.base64_encode($aData['depends_on']).'\', \''.base64_encode($aData['show_if_value']).'\'); return false;"><i class="sys-icon remove"></i></a>'.
				'</li>';
		}
		$sResult .= '</ul>';
		return $sResult;
	}

	function getUFValue($sValues, $sValue) {
		if (strncmp($sValues, '#!', 2) === 0) {
			$aValues = $GLOBALS['aPreValues'][substr($sValues, 2)];
			return !isset($aValues[$sValue]) ? 'Undefined' : htmlspecialchars(_t($aValues[$sValue]['LKey']));
		} else {
			$aValues = explode("\n", $sValues);
			return !in_array($sValue, $aValues) ? 'Undefined' : htmlspecialchars(_t('_FieldValues_'.$sValue));
		}
	}

	function getAddForm() {
		$aFieldsAndSections = $this->_oDb->getFieldsAndSections();
		$aSelectorFields = $this->_oDb->getSelectorFields();

		$aForm = array(
            'form_attrs' => array(
                'action' => BX_DOL_URL_ROOT.$this->_oConfig->getBaseUri().'action_get_add_popup/',
                'method' => 'post',
                'onsubmit' => 'aqb_conditional_fields_add(this); return false;',
            ),
            'params' => array(
                'db' => array(
                    'submit_name' => 'add',
                ),
            ),
            'inputs' => array(
                'add' => array(
                    'type' => 'hidden',
                    'name' => 'add',
                    'value' => 1,
                ),
                'fields' => array(
                    'type' => 'select',
                    'name' => 'field',
                    'values' => $aFieldsAndSections,
                    'caption' => _t('_aqb_conditional_fields_the'),
                    'required' => true,
                    'checker' => array(
                    	'func' => 'avail',
                    	'error' => 'required',
                    ),
                ),
                'depends_on' => array(
                    'type' => 'select',
                    'name' => 'depends_on',
                    'values' => array_merge(array('' => ' - '._t('_aqb_conditional_fields_field').' - '), $aSelectorFields),
                    'caption' => _t('_aqb_conditional_fields_depends_on'),
                    'required' => true,
                    'checker' => array(
                    	'func' => 'avail',
                    	'error' => 'required',
                    ),
                    'attrs' => array(
                    	'onchange' => 'aqb_conditional_fields_fill_values_list(this.value);',
                    ),
                ),
                'show_if_value' => array(
                    'type' => 'select',
                    'name' => 'show_if_value',
                    'caption' => _t('_aqb_conditional_fields_show_if_value'),
                    'required' => true,
                    'checker' => array(
                    	'func' => 'avail',
                    	'error' => 'required',
                    ),
                    'attrs' => array(
                    	'id' => 'aqb_conditional_fields_values_selector',
                    	'disabled' => 'disabled',
                    ),
                ),
                'submit' => array(
                    'type' => 'submit',
                    'name' => 'add',
                    'value' => _t('_aqb_conditional_fields_add_btn'),
                ),
            ),
        );

        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker($_REQUEST);
        return $oForm;
	}
}
