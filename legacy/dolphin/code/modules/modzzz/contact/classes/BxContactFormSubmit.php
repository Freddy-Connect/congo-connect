<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Listing
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

bx_import('BxDolProfileFields');

class BxContactFormSubmit extends BxTemplFormView {

    function BxContactFormSubmit () {
 
		$oMain = BxDolModule::getInstance('BxContactModule');
 
		$aDepartment = $oMain->_oDb->getFormDepartments();
 
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_contact',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_contact_feedback_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'name',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(
				'name' => array(
					'type' => 'text',
					'name' => 'name',
					'caption' => _t('_Your name'),
					'required' => true,
					'checker' => array(
						'func' => 'length',
						'params' => array(1, 150),
						'error' => _t( '_Name is required' )
					),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
				),
				'email' => array(
					'type' => 'text',
					'name' => 'email',
					'caption' => _t('_Your email'),
					'required' => true,
					'checker' => array(
						'func' => 'email',
						'error' => _t( '_modzzz_contact_form_err_email' )
					),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
				),
				'department' => array(
					'type' => 'select',
					'name' => 'department_id',
					'caption' => _t('_modzzz_contact_form_caption_department'),
					'values' => $aDepartment,
					'required' => false,
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
				), 
				'message_subject' => array(
					'type' => 'text',
					'name' => 'subject',
					'caption' => _t('_message_subject'),
					'required' => true,
					'checker' => array(
						'func' => 'length',
						'params' => array(1, 300),
						'error' => _t( '_modzzz_contact_form_err_subject', 300)
					),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
				),
				'message_text' => array(
					'type' => 'textarea',
					'name' => 'desc',
					'caption' => _t('_Message text'),
					'required' => true,
					'checker' => array(
						'func' => 'length',
						'params' => array(1, 5000),
						'error' => _t( '_modzzz_contact_form_err_message', 5000 )
					),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
				),
            ),            
        );

		if(count($aDepartment)==0)
			unset($aCustomForm['inputs']['department']);

		$aTypeIds = $oMain->_oDb->getActionTypes();
		foreach($aTypeIds as $aEachType){
			
			$bCompulsory = $aEachType['compulsory'];
			$sType = $aEachType['select_type'];
			$iId = $aEachType['id'];
 
			switch($sType){
				case 'text':
					$aCustomForm['inputs']['custom_type_'.$iId] = array (
						'type' => 'text',
						'name' => 'custom_type_'.$iId, 
						'caption' => _t('_modzzz_contact_custom_type_'.$iId),
						'required' => true, 
						'checker' => array (
							'func' => 'avail',
							'error' => _t ('_modzzz_contact_form_err_enter_a_value'),
						), 
						'db' => array (
							'pass' => 'Xss', 
						),  
						'display' => true
					); 
 
				break;
				case 'multi-text':
					$aCustomForm['inputs']['custom_type_'.$iId] = array (
						'type' => 'textarea',
						'name' => 'custom_type_'.$iId, 
						'html' => 0,
						'caption' => _t('_modzzz_contact_custom_type_'.$iId),
						'required' => true,  
						'checker' => array (
							'func' => 'avail',
							'error' => _t ('_modzzz_contact_form_err_enter_a_value'),
						), 
						'db' => array (
							'pass' => 'Xss', 
						),  
						'display' => true
					); 
				break; 
				case 'date':
					$aCustomForm['inputs']['custom_type_'.$iId] = array (
						'type' => 'datetime',
						'name' => 'custom_type_'.$iId, 
						'caption' => _t('_modzzz_contact_custom_type_'.$iId),
						'required' => true, 
						'checker' => array (
							'func' => 'DateTime',
							'error' => _t ('_modzzz_contact_form_err_select_a_date'),
						), 
						'db' => array (
							'pass' => 'DateTime', 
						),  
						'display' => 'filterDate'
					); 
				break;
				case 'single-select':
					
					$aSelectVals = array();
					$aDbVals = $oMain->_oDb->getTypeActions($iId); 
					foreach($aDbVals as $iKey=>$sName){
						$aSelectVals[$iKey] = _t($sName);
					}

					$aCustomForm['inputs']['custom_type_'.$iId] = array (
						'type' => 'select',
						'name' => 'custom_type_'.$iId, 
						'caption' => _t('_modzzz_contact_custom_type_'.$iId),
						'values' => $aSelectVals,
						'required' => true, 
						'checker' => array (
							'func' => 'avail', 
							'error' => _t ('_modzzz_contact_form_err_select_a_value'),
						), 
						'db' => array (
							'pass' => 'Xss', 
						), 
						'display' => 'getActionName'
					); 
				break;
				case 'multi-select':
					
					$aSelectVals = array();
					$aDbVals = $oMain->_oDb->getTypeActions($iId); 
					foreach($aDbVals as $iKey=>$sName){
						$aSelectVals[$iKey] = _t($sName);
					}
  
					$aCustomForm['inputs']['custom_type_'.$iId] = array (
						'type' => 'select_box',
						'name' => 'custom_type_'.$iId, 
						'caption' => _t('_modzzz_contact_custom_type_'.$iId),
						'required' => true, 
						'values' => $aSelectVals,  
						'checker' => array (
							'func' => 'avail', 
							'error' => _t ('_modzzz_contact_form_err_select_a_value'),
						), 
						'attrs' => array (
							'add_other' => false, 
						),  
						'db' => array (
							'pass' => 'Categories', 
						),  
						'display' => 'getMultiActionName'
					); 
				break;
			}  

			if(!$bCompulsory){
				$aCustomForm['inputs']['custom_type_'.$iId]['required'] = false;
				unset($aCustomForm['inputs']['custom_type_'.$iId]['checker']);
			}  
		}
 
	
		$aCustomForm['inputs']['captcha'] = array (
			'type' => 'captcha',
			'caption' => _t('_Enter what you see'),
			'name' => 'securityImageValue',
			'required' => true,
			'checker' => array(
				'func' => 'captcha',
				'error' => _t( '_Incorrect Captcha' ),
			),
		);
		
 
		$aCustomForm['inputs']['Submit'] = array (
			'type' => 'submit',
			'name' => 'submit_form',
			'value' => _t('_Submit'),
			'colspan' => false,
		);
 
		if(getParam('sys_recaptcha_key_public')=='' || getParam('sys_recaptcha_key_private')=='')
			unset($aCustomForm['inputs']['captcha']);

        parent::BxTemplFormView ($aCustomForm);
    }
}