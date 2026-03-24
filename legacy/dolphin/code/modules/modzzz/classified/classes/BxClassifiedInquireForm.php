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

class BxClassifiedInquireForm extends BxTemplFormView {

    function BxClassifiedInquireForm ($oMain ) {
 
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_inquire',
                'action'   => '',
                'method'   => 'post',
            ),      

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array( 
 
                'name' => array(
                    'type' => 'text',
                    'name' => 'name',
                    'caption' => _t('_modzzz_classified_form_caption_name'),
 					'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,255),
                        'error' => _t ('_modzzz_classified_form_err_name'),
                    ),                    
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ), 
                'email' => array(
                    'type' => 'text',
                    'email' => 'email',
                    'caption' => _t('_modzzz_classified_form_caption_email'),
 					'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,255),
                        'error' => _t ('_modzzz_classified_form_err_email'),
                    ),                    
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ), 
                'inquire_text' => array(
                    'type' => 'textarea',
                    'name' => 'inquire_text',
                    'caption' => _t('_modzzz_classified_inquiry_text'),
                    'info' => _t('_modzzz_classified_inquiry_text_info'),
					'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,64000),
                        'error' => _t ('_modzzz_classified_form_err_inquiry'),
                    ),                    
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),  
		       /* 'captcha' => array (
					'type' => 'captcha',
					'caption' => _t('_Enter what you see'),
					'name' => 'securityImageValue',
					'required' => true,
					'checker' => array(
						'func' => 'captcha',
						'error' => _t( '_Incorrect Captcha' ),
					),
				),
				*/ 
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                    'colspan' => false,
                ),
            ),            
        );

		if(!$oMain->_iProfileId || getParam('sys_recaptcha_key_public')=='' || getParam('sys_recaptcha_key_private')=='')
			unset($aCustomForm['inputs']['captcha']);
 
		if($oMain->_iProfileId){
 			unset($aCustomForm['inputs']['name']);
			unset($aCustomForm['inputs']['email']);
		}

        parent::BxTemplFormView ($aCustomForm);
    }
}
