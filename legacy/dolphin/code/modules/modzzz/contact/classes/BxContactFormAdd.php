<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Confession
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
 
bx_import ('BxDolFormMedia');

class BxContactFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxContactFormAdd ($oMain, $iEntryId = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
		
		$aStatus = array(
			'approved'=>_t('_modzzz_contact_yes'),
			'pending'=>_t('_modzzz_contact_no'),
		);

		$aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_contact',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_contact_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_contact_form_header_info')
                ),       
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_contact_form_caption_title'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_contact_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'email' => array(
                    'type' => 'text',
                    'name' => 'email',
                    'caption' => _t('_modzzz_contact_form_caption_email'),
                    'required' => true,  
					'checker' => array(
						'func' => 'email',
						'error' => _t( '_modzzz_contact_form_err_email' )
					), 
                    'db' => array (
                        'pass' => 'Xss', 
                    ), 
                ),
                'desc' => array(
                    'type' => 'textarea',
                    'name' => 'desc',
                    'caption' => _t('_modzzz_contact_form_caption_desc'),
                    'required' => true,
                    'html' => 0,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,64000),
                        'error' => _t ('_modzzz_contact_form_err_desc'),
                    ),                    
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ),  
                'status' => array(
                    'type' => 'select',
                    'name' => 'status',
                    'caption' => _t('_modzzz_contact_form_caption_active'), 
                    'values' => $aStatus,
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
 
        parent::BxDolFormMedia ($aCustomForm);
    }

}
