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

bx_import ('BxDolProfileFields');
bx_import ('BxDolFormMedia');

class BxManagerFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxManagerFormAdd ($oMain, $iEntryId = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
		
		$aType = $this->_oDb->getFormUnits();

		$aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_manager',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'sys_objects_actions',
                    'key' => 'ID',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_manager_form_header_info')
                ),                

                'Caption' => array(
                    'type' => 'text',
                    'name' => 'Caption',
                    'caption' => _t('_modzzz_manager_form_caption_caption'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_manager_form_err_caption'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
		        'Type' => array(
                    'type' => 'select',
                    'name' => 'Type',
                    'caption' => _t('_modzzz_manager_form_caption_type'),
                    'values' => $aType,
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,20),
                        'error' => _t ('_modzzz_manager_form_err_type'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),			
                'Icon' => array(
                    'type' => 'text',
                    'name' => 'Icon',
                    'caption' => _t('_modzzz_manager_form_caption_icon'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_manager_form_err_icon'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'Url' => array(
                    'type' => 'text',
                    'name' => 'Url',
                    'caption' => _t('_modzzz_manager_form_caption_url'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'Script' => array(
                    'type' => 'text',
                    'name' => 'Script',
                    'caption' => _t('_modzzz_manager_form_caption_script'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'Eval' => array(
                    'type' => 'textarea',
                    'name' => 'Eval',
                    'caption' => _t('_modzzz_manager_form_caption_eval'),
                    'required' => false,
                    'html' => 0, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ),  
                'Order' => array(
                    'type' => 'text',
                    'name' => 'Order',
                    'caption' => _t('_modzzz_manager_form_caption_order'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'bDisplayInSubMenuHeader' => array(
                    'type' => 'text',
                    'name' => 'bDisplayInSubMenuHeader',
                    'caption' => _t('_modzzz_manager_form_caption_display_submenuheader'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
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
