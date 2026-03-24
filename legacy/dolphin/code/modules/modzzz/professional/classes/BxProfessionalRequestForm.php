<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
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

class BxProfessionalRequestForm extends BxTemplFormView {
    
	var $_oMain, $_oDb;

    function BxProfessionalRequestForm ($oMain, $aServiceEntry) {
  
        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
  
		$sServiceName = $aServiceEntry['title'];

		$aPhoneType = array(
			'cell' => _t('_modzzz_professional_phone_type_cell'),
			'home' => _t('_modzzz_professional_phone_type_home'),
			'work' => _t('_modzzz_professional_phone_type_work') 
		);

		if($this->_oMain->_iProfileId){
			$aProfileInfo = getProfileInfo($this->_oMain->_iProfileId);
			$sFirstName = $aProfileInfo['FirstName'];
			$sLastName = $aProfileInfo['LastName'];
			$sEmail = $aProfileInfo['Email'];
		}
 
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_request',
                'action'   => '',
                'method'   => 'post',
            ),   
            'params' => array (
                'db' => array(
                    'table' => 'modzzz_professional_request_main',
                    'key' => 'id', 
                    'submit_name' => 'submit_form',
                ),
            ),   
            'inputs' => array( 
 
                'service' => array(
                    'type' => 'custom', 
                    'caption' => _t('_modzzz_professional_form_caption_requested_service'), 
                    'content' => $sServiceName,
                ), 
                'first_name' => array(
                    'type' => 'text',
                    'name' => 'first_name',
                    'caption' => _t('_modzzz_professional_form_caption_firstname'),
                    'required' => true, 
                    'value' => $sFirstName, 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_professional_form_err_firstname'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'last_name' => array(
                    'type' => 'text',
                    'name' => 'last_name',
                    'caption' => _t('_modzzz_professional_form_caption_lastname'),
                    'required' => true, 
                    'value' => $sLastName, 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_professional_form_err_lastname'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'email' => array(
                    'type' => 'text',
                    'name' => 'email',
                    'caption' => _t('_modzzz_professional_form_caption_email'),
                    'required' => true, 
                    'value' => $sEmail, 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_professional_form_err_email'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'telephone' => array(
                    'type' => 'text',
                    'name' => 'telephone',
                    'caption' => _t('_modzzz_professional_form_caption_telephone'),
                    'required' => true, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'phone_type' => array(
                    'type' => 'select',
                    'name' => 'phone_type',
                    'caption' => _t('_modzzz_professional_form_caption_phone_type'),
                    'values' => $aPhoneType, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'note' => array(
                    'type' => 'textarea',
                    'name' => 'note',
                    'caption' => _t('_modzzz_professional_form_caption_note'),
                    'required' => true, 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,64000),
                        'error' => _t ('_modzzz_professional_form_err_note'),
                    ),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),         
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                    'colspan' => false,
                ),
            ),            
        );

        parent::BxTemplFormView ($aCustomForm);
    }
}
