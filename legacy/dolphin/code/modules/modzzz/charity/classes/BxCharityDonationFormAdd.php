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

bx_import ('BxDolProfileFields');
bx_import ('BxDolFormMedia');

class BxCharityDonationFormAdd extends BxDolFormMedia {
  
    function BxCharityDonationFormAdd ($oMain, $iProfileId, $iEntryId = 0 , $sMessage='') { 
   
        $aCustomForm = array(

            'form_attrs' => array(
                'name' => 'payment_form',
                'method' => 'post', 
                'action' => '',
            ),      

            'params' => array (
                'db' => array(
                     'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_charity_subprofile_form_header_info')
                ),
				'message' => array(
                    'caption'  => _t('_modzzz_charity_form_caption_error_message'),
                    'type'   => 'custom',
                    'content' => $sMessage, 
                ),	 
               'first_name' => array(
                    'caption'  => _t('_modzzz_charity_form_caption_first_name'),
                    'type'   => 'text',
                    'name' => 'first_name',
                    'required' => false,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'last_name' => array(
                    'caption'  => _t('_modzzz_charity_form_caption_last_name'),
                    'type'   => 'text',
                    'name' => 'last_name',
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
				'anonymous' => array(
					'type' => 'select',
					'name' => 'anonymous',
					'caption' => _t('_modzzz_charity_form_caption_anonymous'),
					'info' => _t('_modzzz_charity_form_info_anonymous'),
					'values' => array('0'=>_t('_modzzz_charity_no'),'1'=>_t('_modzzz_charity_yes')),
					'required' => false, 
					'db' => array (
					'pass' => 'Xss', 
					),
				 ),  
                'amount' => array(
                    'caption'  => _t('_modzzz_charity_form_caption_amount'),
                    'type'   => 'text',
                    'name' => 'amount',
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9.]+$/'),
                        'error'  => _t('_modzzz_charity_form_error_amount'),
                    ),
                ), 
				'Submit' => array (
					'type' => 'submit',
					'name' => 'submit_form',
					'value' => _t('_Submit') 
				),  

            ),            
        );

	    if(!$sMessage)
			unset($aCustomForm['inputs']['message']);

		if($this->_iProfileId) {
			unset($aForm['inputs']['first_name']);
			unset($aForm['inputs']['last_name']);
		} 


        parent::BxDolFormMedia ($aCustomForm);
    }
 
}
