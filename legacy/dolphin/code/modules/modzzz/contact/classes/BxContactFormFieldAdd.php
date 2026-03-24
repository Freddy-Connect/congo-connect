<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Contact
*     action              : http://www.boonex.com
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

class BxContactFormFieldAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxContactFormFieldAdd ($oMain, $iEntryId = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
 
		$sDefaultTitle = process_db_input($_REQUEST['title']);
    
		$aTypes = $this->_oDb->getSelectType();
 
		if($iEntryId) { 
			$aTypeData = $this->_oDb->getTypeData($iEntryId);
			$sEditTitle = ($_POST['edit_title']) ? $_POST['edit_title'] : _t($aTypeData['title']);  
 		} 
 
        $aCustomActionTemplates = $this->generateCustomActionTemplate ($iEntryId);
  
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_contact',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_contact_action_types',
                    'key' => 'id',
                     'submit_name' => 'submit_form',
                ),
            ),
                
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_contact_form_custom_field_info')
                ),                

                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_contact_form_caption_title'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,255),
                        'error' => _t ('_modzzz_contact_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ), 
                ),  
                'edit_title' => array(
                    'type' => 'text',
                    'name' => 'edit_title',
                    'caption' => _t('_modzzz_contact_form_caption_title'),
                    'required' => true,
                    'value' => $sEditTitle,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,255),
                        'error' => _t ('_modzzz_contact_form_err_title'),
                    ), 
                ),	 
                'select_type' => array(
                    'type' => 'select',
                    'name' => 'select_type',
					'values'=> $aTypes, 
                    'caption' => _t('_modzzz_contact_form_caption_select_type'), 
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_contact_form_err_select_type'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),
                'compulsory' => array(
                    'type' => 'select',
                    'name' => 'compulsory',
					'values'=> array('1'=>_t('_modzzz_contact_yes'),'0'=>_t('_modzzz_contact_no')), 
                    'caption' => _t('_modzzz_contact_form_caption_compulsory'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),
 
				//Actions
               'header_action' => array(
                   'type' => 'block_header',
                   'caption' => _t('_modzzz_contact_form_header_action'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
               'action_choice' => array(
                   'type' => 'custom',
                   'content' => $aCustomActionTemplates['choice'],
                   'name' => 'action_choice[]',
                   'caption' => _t('_modzzz_contact_form_caption_action_choice'),
                   'info' => _t('_modzzz_contact_form_info_action_choice'),
                   'required' => false,
               ), 
               'action_attach' => array(
                   'type' => 'custom',
                   'content' => $aCustomActionTemplates['upload'],
                   'name' => 'action_upload[]',
                   'caption' => _t('_modzzz_contact_form_caption_action_attach'),
                   'info' => _t('_modzzz_contact_form_info_action_attach'),
                   'required' => false,
               ),
 
			   'Submit' => array (
					'type' => 'submit',
					'name' => 'submit_form',
					'value' => _t('_Submit'),
					'colspan' => false,
				),  
            ),            
        );
  
	    if($iEntryId){
			unset( $aCustomForm['inputs']['title'] );
	    }else{
			unset( $aCustomForm['inputs']['edit_title'] ); 
		}

        parent::BxDolFormMedia ($aCustomForm);
    }
 
	function generateCustomActionTemplate ($iEntryId) {
 
		$aTemplates = array ();
	 
		$aActions = $this->_oDb->getActions ($iEntryId); 
 
		$aFeeds = array();
		foreach ($aActions as $k => $r) {
			$aFeeds[$k] = array();
			$aFeeds[$k]['id'] = $r['id'];
			$aFeeds[$k]['name'] = _t($r['name']);
		}

		$aVarsChoice = array ( 
			'bx_if:empty' => array(
				'condition' => empty($aFeeds),
				'content' => array ()
			),

			'bx_repeat:actions' => $aFeeds,
		);                               
		$aTemplates['choice'] =  $this->_oMain->_oTemplate->parseHtmlByName('form_field_action_choice', $aVarsChoice);
		
		// upload form
		$aVarsUpload = array ();            
		$aTemplates['upload'] = $this->_oMain->_oTemplate->parseHtmlByName('form_field_action', $aVarsUpload);
 
		return $aTemplates;
	} 


}
