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
bx_import ('BxDolCategories');
  
class BxEventsBanForm extends BxTemplFormView {

    function BxEventsBanForm ($oMain, $iEntryId) {
  
        $sActionsUrl = bx_append_url_params(BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'ban_member/'. $iEntryId,  'ajax_action=');
  
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'frm_event_ban',
                'action'   => '',
                'method'   => 'post',
            ),      

            'params' => array ( 
                'db' => array( 
                    'submit_name' => 'submit_form',
                ), 
            ),
                  
            'inputs' => array( 
 			
 				'member' => array(
					'type' => 'text',
					'name' => 'member', 
					'caption' => _t('_bx_events_form_caption_enter_member'), 
					/*'info' => _t('_bx_events_form_info_enter_member'),*/ 
                    'db' => array (
                        'pass' => 'Xss', 
                    ), 
				), 
                'Submit' => array (
                    'type' => 'button',
                    'name' => 'submit_form',
					'attrs' => array (
						'onclick' => "getHtmlData('event_member_content', '{$sActionsUrl}ban_member&member=' + document.frm_event_ban.member.value, false, 'post'); return false;",  
					), 
                    'value' => _t('_Submit'),
                    'colspan' => false,
                ),
            ),            
        );

        parent::BxTemplFormView ($aCustomForm);
    }
}
