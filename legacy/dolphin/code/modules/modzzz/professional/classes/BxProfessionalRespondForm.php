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

class BxProfessionalRespondForm extends BxTemplFormView {
 
    function BxProfessionalRespondForm ($oMain, $aServiceEntry, $aRequestEntry) {
  
 
		$sFirstName = $aRequestEntry['first_name'];
		$sLastName = $aRequestEntry['last_name'];
		$sServiceName = $aServiceEntry['title'];

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
                'name' => array(
                    'type' => 'custom', 
                    'caption' => _t('_modzzz_professional_form_caption_response_to'), 
                    'content' => $sFirstName .' '. $sLastName, 
                ),  
                'responded' => array(
                    'type' => 'hidden', 
                    'name' => 'responded', 
                    'value' => '1', 
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),  
                'response' => array(
                    'type' => 'textarea',
                    'name' => 'response',
                    'caption' => _t('_modzzz_professional_form_caption_response'),
                    'required' => true, 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,64000),
                        'error' => _t ('_modzzz_professional_form_err_response'),
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
