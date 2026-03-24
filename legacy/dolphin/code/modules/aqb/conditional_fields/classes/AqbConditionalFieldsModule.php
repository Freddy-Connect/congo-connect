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

bx_import('BxDolModule');
bx_import('BxDolAdminSettings');
bx_import('BxTemplCmtsView');

class AqbConditionalFieldsModule extends BxDolModule {
	/**
	 * Constructor
	 */
	function __construct($aModule) {
	    parent::__construct($aModule);
	}

	function actionGetAddPopup() {
		if (!isAdmin()) die;

		$oForm = $this->_oTemplate->getAddForm();

		if ($oForm->isSubmittedAndValid()) {
			$this->_oDb->addConditionalField($_POST['field'], $_POST['depends_on'], $_POST['show_if_value']);
			return json_encode(array(
				'status' => 'ok',
				'rows' => $this->_oTemplate->getConditionalFieldsList(),
			));
		}

		if ($oForm->isSubmitted()) {
			return json_encode(array(
				'status' => 'fail',
				'form' => $oForm->getCode().LoadingBox('aqb_cf_loading'),
			));
		}

		return PopupBox('aqb_conditional_fields_popup', _t('_aqb_conditional_fields_add'), '<div class="bx-def-padding" id="aqb_conditional_fields_form" style="position:relative;">'.$oForm->getCode().LoadingBox('aqb_cf_loading').'</div>');
	}

	function actionGetFieldValues($sField) {
		if (!isAdmin()) die;
		$aValues = $this->_oDb->getFieldValues($sField);

		echo "var values = new Array();\n";
		echo "values['name'] = new Array();\n";
		echo "values['value'] = new Array();\n";

		foreach ($aValues as $sValue => $sCaption) {
			$sValue = addslashes($sValue);
			$sCaption = addslashes($sCaption);
			echo "values['name'].push('{$sCaption}');\n";
			echo "values['value'].push('{$sValue}');\n";
		}
	}

	function actionRemove($sField, $sDependsOn, $sShowIfValue) {
		$sField = base64_decode($sField);
		$sDependsOn = base64_decode($sDependsOn);
		$sShowIfValue = base64_decode($sShowIfValue);
		if (!isAdmin()) die;

		$this->_oDb->remove($sField, $sDependsOn, $sShowIfValue);

		return $this->_oTemplate->getConditionalFieldsList();
	}

	function serviceGetFormObject($aForm) {
		$aConditionalFields = $this->_oDb->getConditionalFields();
		if (!$aConditionalFields) return new BxTemplFormView($aForm);

		$aParentFields = array();
		$aDependentFields = array();
		foreach ($aConditionalFields as $aData) {
			if (!$aParentFields[$aData['depends_on']]['value']) $aParentFields[$aData['depends_on']]['value'] = $this->getCurFieldValue($aData['depends_on'], $aForm['inputs']);
			$aParentFields[$aData['depends_on']]['dependent_fields'][] = $aData['field'];

			$aDependentFields[$aData['field']]['depends_on'] = $aData['depends_on'];
			$aDependentFields[$aData['field']]['show_if_value'][] = $aData['show_if_value'];
		}

        foreach ($aForm['inputs'] as $iIndex => $aField) {
        	$sName = '';

        	if ($aField['type'] == 'hidden') continue;
        	if ($aField['type'] == 'block_header') {
        		$sName = $this->_oDb->getBlockName($aField['caption']);
        	} else {
        		$sName = substr($aField['name'], -1) == ']' ? substr($aField['name'], 0, -3) : $aField['name'];
        	}

        	if (!$sName) continue;

        	//if it is a parent field
        	if (isset($aParentFields[$sName])) {
        		foreach ($aParentFields[$sName]['dependent_fields'] as $sFieldName) {
        			if ($aForm['inputs'][$iIndex]['type'] == 'select' || $aForm['inputs'][$iIndex]['type'] == 'select_box') {
        				$aForm['inputs'][$iIndex]['attrs']['onchange'] .= 'aqb_conditional_fields_change_select(this.value, \''.uriFilter($sFieldName).'\', '.json_encode($aDependentFields[$sFieldName]['show_if_value']).');';
        			} else {
        				$aForm['inputs'][$iIndex]['attrs']['onchange'] .= 'aqb_conditional_fields_change_radio_set(this, \''.uriFilter($sFieldName).'\', '.json_encode($aDependentFields[$sFieldName]['show_if_value']).');';
        			}
        		}
        	}


        	//if it is a conditional field
        	if (isset($aDependentFields[$sName])) {
        		if (!in_array($aParentFields[$aDependentFields[$sName]['depends_on']]['value'], $aDependentFields[$sName]['show_if_value'])) {
        			if ($aField['type'] == 'block_header') {
		        		$aForm['inputs'][$iIndex]['attrs']['style'] = 'display:none;';
		        		$aForm['inputs'][$iIndex]['collapsable'] = true;
		        		$aForm['inputs'][$iIndex]['collapsed'] = true;
		        	} else {
		            	$aForm['inputs'][$iIndex]['tr_attrs']['style'] = 'display:none;';
		            }
        		}


        		$sId = 'aqb_cf_'.uriFilter($sName);
	        	if ($aField['type'] == 'block_header') {
	        		$aForm['inputs'][$iIndex]['attrs']['id'] = $sId;
	        	} else {
	            	$aForm['inputs'][$iIndex]['tr_attrs']['id'] = $sId;
	            }
        	}
        }


		require_once($this->_oConfig->getClassPath().'AqbConditionalFieldsFormView.php');
		return new AqbConditionalFieldsFormView($aForm, $this->_oTemplate);
	}

	function getCurFieldValue($sFieldName, &$aInputs) {
		foreach ($aInputs as $aField) {
        	if ($aField['type'] == 'block_header') continue;

        	//$sName = substr($aField['name'], 0, -3);
        	if (strpos($aField['name'], '[')) $sName = substr($aField['name'], 0, strpos($aField['name'], '['));
        	else $sName = $aField['name'];
        	if ($sName != $sFieldName) continue;


        	if ($aField['value']) return $aField['value'];
        	elseif ($_REQUEST[$sName][0]) return $_REQUEST[$sName][0];
        	else {
        		return $aField['type'] == 'select' || $aField['type'] == 'select_box' ? reset($aField['values']) : false;
        	}
		}

		return false;
	}

	function serviceFilterPostData(&$aBlocks) {
		$aConditionalFields = $this->_oDb->getConditionalFields();
		if (!$aConditionalFields) return;

		$aParentFields = array();
		$aDependentFields = array();
		foreach ($aConditionalFields as $aData) {
			$aParentFields[$aData['depends_on']] = $_REQUEST[$aData['depends_on']][0];

			$aDependentFields[$aData['field']]['depends_on'] = $aData['depends_on'];
			$aDependentFields[$aData['field']]['show_if_value'][] = $aData['show_if_value'];
		}

		foreach($aBlocks as $iBlock => $aBlock) {
			$sBlockName = substr($aBlock['Caption'], 14, -5);

			if ($aDependentFields[$sBlockName] && !in_array($aParentFields[$aDependentFields[$sBlockName]['depends_on']], $aDependentFields[$sBlockName]['show_if_value'])) {
				foreach ($aBlock['Items'] as $iItemID => $aItem) {
					$sFieldName = $aItem['Name'];
					unset($aBlocks[$iBlock]['Items'][$iItemID]['Mandatory']);
					unset($aBlocks[$iBlock]['Items'][$iItemID]['Min']);
					unset($aBlocks[$iBlock]['Items'][$iItemID]['Max']);
					unset($aBlocks[$iBlock]['Items'][$iItemID]['Check']);
					unset($aBlocks[$iBlock]['Items'][$iItemID]['Unique']);
					unset($_REQUEST[$sFieldName][0]); unset($_POST[$sFieldName][0]);
					unset($_REQUEST[$sFieldName][1]); unset($_POST[$sFieldName][1]);
				}
				continue;
			}


			foreach ($aBlock['Items'] as $iItemID => $aItem) {
				$sFieldName = $aItem['Name'];
				if ($aDependentFields[$sFieldName] && !in_array($aParentFields[$aDependentFields[$sFieldName]['depends_on']], $aDependentFields[$sFieldName]['show_if_value'])) {
					unset($aBlocks[$iBlock]['Items'][$iItemID]['Mandatory']);
					unset($aBlocks[$iBlock]['Items'][$iItemID]['Min']);
					unset($aBlocks[$iBlock]['Items'][$iItemID]['Max']);
					unset($aBlocks[$iBlock]['Items'][$iItemID]['Check']);
					unset($aBlocks[$iBlock]['Items'][$iItemID]['Unique']);
					unset($_REQUEST[$sFieldName][0]); unset($_POST[$sFieldName][0]);
					unset($_REQUEST[$sFieldName][1]); unset($_POST[$sFieldName][1]);
				}
			}
		}
	}

	function serviceFilterViewData(&$aBlocks, $iProfile) {
		$aConditionalFields = $this->_oDb->getConditionalFields();
		if (!$aConditionalFields) return;

		$aProfile = getProfileInfo($iProfile);

		$aParentFields = array();
		$aDependentFields = array();
		foreach ($aConditionalFields as $aData) {
			$aParentFields[$aData['depends_on']] = $aProfile[$aData['depends_on']];

			$aDependentFields[$aData['field']]['depends_on'] = $aData['depends_on'];
			$aDependentFields[$aData['field']]['show_if_value'][] = $aData['show_if_value'];
		}

		foreach($aBlocks as $iBlock => $aBlock) {
			$sBlockName = substr($aBlock['Caption'], 14, -5);

			if ($aDependentFields[$sBlockName] && !in_array($aParentFields[$aDependentFields[$sBlockName]['depends_on']], $aDependentFields[$sBlockName]['show_if_value'])) {
				unset($aBlocks[$iBlock]);
				continue;
			}

			foreach ($aBlock['Items'] as $iItemID => $aItem) {
				$sFieldName = $aItem['Name'];
				if ($aDependentFields[$sFieldName] && !in_array($aParentFields[$aDependentFields[$sFieldName]['depends_on']], $aDependentFields[$sFieldName]['show_if_value'])) {
					unset($aBlocks[$iBlock]['Items'][$iItemID]);
				}
			}
		}
	}
}