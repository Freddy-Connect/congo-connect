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

class BxAToolFormMessageRespond extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxAToolFormMessageRespond ($oMain, $iMessageId = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
 
		$aMessage = $this->_oDb->getMessageEntryById($iMessageId);
		$sSubject = $aMessage['Subject'];
		$sSenderName = getNickName($aMessage['Sender']);

		$aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_atool',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array( 
                    'submit_name' => 'submit_form',
                ),
            ),
               
            'inputs' => array(
                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_atool_form_header_respond_message')
                ), 
                'id' => array(
                    'type' => 'hidden',
                    'name' => 'id', 
                    'value' => $iMessageId,   
                    'db' => array (
                        'pass' => 'Int', 
                    ),  
                ),  				
                'subject' => array(
                    'type' => 'custom',
                    'caption' => _t('_modzzz_atool_form_caption_re'),
                    'content' => $sSubject,  
                ),  
                'sent_by' => array(
                    'type' => 'custom',
                    'caption' => _t('_modzzz_atool_form_caption_sent_by'),
                    'content' => $sSenderName,  
                ), 
                'message' => array(
                    'type' => 'textarea',
                    'name' => 'message',
                    'caption' => _t('_modzzz_atool_form_caption_message'),
                    'required' => true,
                    'html' => 2,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,64000),
                        'error' => _t ('_modzzz_atool_form_err_message'),
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
