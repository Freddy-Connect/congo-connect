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

class BxNotifierFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxNotifierFormAdd ($oMain) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
  
  		$aModules = $this->_oDb->getModules();
 
		$aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_notifier',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'key' => 'id',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_notifier_form_header_add_info')
                ),   
                'unit' => array(
                    'type' => 'select',
                    'name' => 'unit',
                    'caption' => _t('_modzzz_notifier_form_caption_unit'),
                    'required' => true,
                    'values' => $aModules,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_notifier_form_err_unit'),
                    ), 
                ),
                'action' => array(
                    'type' => 'text',
                    'name' => 'action',
                    'caption' => _t('_modzzz_notifier_form_caption_action'),
                    'info' => _t('_modzzz_notifier_form_info_action'),
                    'required' => true,
					'value' => 'add',
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,255),
                        'error' => _t ('_modzzz_notifier_form_err_action'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'action_desc' => array(
                    'type' => 'text',
                    'name' => 'action_desc',
                    'caption' => _t('_modzzz_notifier_form_caption_action_desc'),
                    'info' => _t('_modzzz_notifier_form_info_action_desc'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,255),
                        'error' => _t ('_modzzz_notifier_form_err_action_desc'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'table' => array(
                    'type' => 'text',
                    'name' => 'table',
                    'caption' => _t('_modzzz_notifier_form_caption_table'),
                    'info' => _t('_modzzz_notifier_form_info_table'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_notifier_form_err_table'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'pending_value' => array(
                    'type' => 'text',
                    'name' => 'pending_value',
                    'caption' => _t('_modzzz_notifier_form_caption_pending_value'),
                    'info' => _t('_modzzz_notifier_form_info_pending_value'),
                    'required' => true,
					'value' => 'pending',
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_notifier_form_err_pending_value'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),  
                'id_field' => array(
                    'type' => 'text',
                    'name' => 'id_field',
                    'caption' => _t('_modzzz_notifier_form_caption_id_field'),
                    'info' => _t('_modzzz_notifier_form_info_id_field'),
                    'required' => true,
					'value' => 'id',
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_notifier_form_err_id_field'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'author_field' => array(
                    'type' => 'text',
                    'name' => 'author_field',
                    'caption' => _t('_modzzz_notifier_form_caption_author_field'),
                    'info' => _t('_modzzz_notifier_form_info_owner_field'),
                    'required' => true,
					'value' => 'author_id',
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_notifier_form_err_author_field'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'title_field' => array(
                    'type' => 'text',
                    'name' => 'title_field',
                    'caption' => _t('_modzzz_notifier_form_caption_title_field'),
                    'info' => _t('_modzzz_notifier_form_info_title_field'),
                    'required' => true,
					'value' => 'title',
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_notifier_form_err_title_field'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'uri_field' => array(
                    'type' => 'text',
                    'name' => 'uri_field',
                    'caption' => _t('_modzzz_notifier_form_caption_uri_field'),
                    'info' => _t('_modzzz_notifier_form_info_uri_field'),
                    'required' => true,
                    'value' => 'uri',
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_notifier_form_err_uri_field'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'status_field' => array(
                    'type' => 'text',
                    'name' => 'status_field',
                    'caption' => _t('_modzzz_notifier_form_caption_status_field'),
                    'info' => _t('_modzzz_notifier_form_info_status_field'),
                    'required' => true,
					'value' => 'status',
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_notifier_form_err_status_field'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'view_page_url' => array(
                    'type' => 'text',
                    'name' => 'view_page_url',
                    'caption' => _t('_modzzz_notifier_form_caption_view_page_url'),
                    'info' => _t('_modzzz_notifier_form_info_view_page_url'),
                    'required' => true,
					'value' => '{module_url}view/{uri}',
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,100),
                        'error' => _t ('_modzzz_notifier_form_err_view_page_url'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
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
