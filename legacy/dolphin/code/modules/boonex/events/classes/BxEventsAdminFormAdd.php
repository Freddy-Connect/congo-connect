<?php
/***************************************************************************
*                            Dolphin Smart Events Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Events Builder
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

class BxEventsAdminFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxEventsAdminFormAdd ($oMain, $iEventsId = 0) { 
        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb; 
    
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_admins',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'bx_events_admins',
                    'key' => 'id_entry', 
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_events_form_header_add_admin')
                ),                
                'id_entry' => array(
                    'type' => 'hidden',
                    'name' => 'id_entry', 
                    'value' => $iEventsId,
                    'db' => array (
                        'pass' => 'Int' 
                    ) 
                ),  
                'profile_nick' => array(
                    'type' => 'text',
                    'name' => 'profile_nick',
                    'caption' => _t('_bx_events_form_caption_new_admin'),  
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_bx_events_form_err_new_admin'),
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
