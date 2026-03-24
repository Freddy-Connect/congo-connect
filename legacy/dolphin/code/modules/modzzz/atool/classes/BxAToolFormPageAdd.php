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

class BxAToolFormPageAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxAToolFormPageAdd ($oMain,  $sPage='', $iLang='', $sEditor='', $aDataEntry) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
    
		$sIdentifier = $aDataEntry['identifier'];

		$aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_atool',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_atool_page_content',
                    'key' => 'id',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(
                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_atool_form_header_info')
                ),    
                'page' => array(
                    'type' => 'hidden',
                    'name' => 'page', 
                    'value' => $sPage,   
                    'db' => array (
                        'pass' => 'Xss', 
                    ),  
                ),
                'identifier' => array(
                    'type' => 'hidden',
                    'name' => 'identifier', 
                    'value' => $sIdentifier,   
                    'db' => array (
                        'pass' => 'Xss', 
                    ),  
                ), 				
                'language' => array(
                    'type' => 'hidden',
                    'name' => 'language', 
                    'value' => $iLang,   
                    'db' => array (
                        'pass' => 'Xss', 
                    ),  
                ),   
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_atool_form_caption_title'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_modzzz_atool_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'desc' => array(
                    'type' => 'textarea',
                    'name' => 'desc',
                    'caption' => _t('_modzzz_atool_form_caption_desc'),
                    'required' => true,
                    'html' => ($sEditor=='plain') ? 0 : 2,
                    'checker' => array (
                        'func' => 'avail', 
                        'error' => _t ('_modzzz_atool_form_err_desc'),
                    ), 
					'attrs' => array (
                        'style' => 'width:500px;height:400px;', 
                    ), 		
                    'db' => array (
                        'pass' => 'XssHtml', 
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
