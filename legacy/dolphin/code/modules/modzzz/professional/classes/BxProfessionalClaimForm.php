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

class BxProfessionalClaimForm extends BxTemplFormView {

    function BxProfessionalClaimForm ($oMain ) {
 
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_claim',
                'action'   => '',
                'method'   => 'post',
            ),      

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array( 
 
                'claim_text' => array(
                    'type' => 'textarea',
                    'name' => 'claim_text',
                    'caption' => _t('_modzzz_professional_claim_text'),
                    'info' => _t('_modzzz_professional_claim_text_info'),
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
 