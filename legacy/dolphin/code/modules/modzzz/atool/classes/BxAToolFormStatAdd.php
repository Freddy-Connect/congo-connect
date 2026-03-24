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

class BxAToolFormStatAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxAToolFormStatAdd ($oMain, $iEntryId = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
		
		$aType = $this->_oDb->getFormSiteStatUnits();

		$aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_atool',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'sys_stat_site',
                    'key' => 'ID',
                    'submit_name' => 'submit_form',
                ),
            ),
 
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_atool_form_header_site_stat')
                ),  
                'Name' => array(
                    'type' => 'text',
                    'name' => 'Name',
                    'caption' => _t('_modzzz_atool_form_caption_name'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,20),
                        'error' => _t ('_modzzz_atool_form_err_name'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'Title' => array(
                    'type' => 'text',
                    'name' => 'Title',
                    'caption' => _t('_modzzz_atool_form_caption_title'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,50),
                        'error' => _t ('_modzzz_atool_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'UserLink' => array(
                    'type' => 'text',
                    'name' => 'UserLink',
                    'caption' => _t('_modzzz_atool_form_caption_userlink'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,255),
                        'error' => _t ('_modzzz_atool_form_err_userlink'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'UserQuery' => array(
                    'type' => 'textarea',
                    'name' => 'UserQuery',
                    'caption' => _t('_modzzz_atool_form_caption_userquery'),
                    'required' => true,
                    'html' => 0,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,255),
                        'error' => _t ('_modzzz_atool_form_err_userquery'),
                    ),  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ), 
                'AdminLink' => array(
                    'type' => 'textarea',
                    'name' => 'AdminLink',
                    'caption' => _t('_modzzz_atool_form_caption_adminlink'),
                    'required' => true,
                    'html' => 0,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,255),
                        'error' => _t ('_modzzz_atool_form_err_adminlink'),
                    ),  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ), 
                'AdminQuery' => array(
                    'type' => 'textarea',
                    'name' => 'AdminQuery',
                    'caption' => _t('_modzzz_atool_form_caption_adminquery'),
                    'required' => true,
                    'html' => 0,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,255),
                        'error' => _t ('_modzzz_atool_form_err_adminquery'),
                    ),  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ),  
                'IconName' => array(
                    'type' => 'text',
                    'name' => 'IconName',
                    'caption' => _t('_modzzz_atool_form_caption_icon'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,50),
                        'error' => _t ('_modzzz_atool_form_err_icon'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),/* 
                'Order' => array(
                    'type' => 'text',
                    'name' => 'Order',
                    'caption' => _t('_modzzz_atool_form_caption_order'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),*/ 
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
