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

class BxChurchFaqFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxChurchFaqFormAdd ($oMain, $iProfileId, $iChurchId = 0, $iFaqId = 0) { 
        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
 
        // generate templates for form custom elements
         $aCustomFaqTemplates = $this->generateCustomFaqTemplate ($iChurchId);
  
		$aDataEntry = $this->_oDb->getEntryById($iChurchId);

		$sTitle = $aDataEntry[$this->_oDb->_sFieldTitle] .' '. _t('_modzzz_church_faq');
  
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_faqs',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_church_faq_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_church_form_header_faq_info')
                ),  
                'church_id' => array(
                    'type' => 'hidden',
                    'name' => 'church_id', 
                    'value' => $iChurchId, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ), 
                 ), 
                'title' => array(
                    'type' => 'hidden',
                    'name' => 'title', 
                    'value' => $sTitle, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ), 
                 ),	 
                'question' => array(
                    'type' => 'text',
                    'name' => 'question[]',
					'attrs' => array(
						'id' => "question",
						'class' => "question",
 					),  
                    'caption' => _t('_modzzz_church_form_caption_question'),
                    'required' => false,  
                ),  
				'answer' => array(
                    'type' => 'text',
                    'name' => 'answer[]',
					'attrs' => array(
						'id' => "answer",
						'class' => "answer",
 					),  
                    'caption' => _t('_modzzz_church_form_caption_answer'),
                    'required' => false,  
                ), 				
				'addbutton' => array(
					'type' => 'custom', 
                    'content' => '<input class="bx-btn bx-btn-small bx-btn-img bx-btn-ifont addbutton" type="button" id="addbutton" 
						 value="Add Question" onClick="addQuestion()" />',  
                    'name' => 'addbutton',
                    'caption' => _t('_modzzz_church_add_another_question'),  
                ),
 
               'header_church_faq' => array(
                   'type' => 'block_header',
                   'caption' => _t('_modzzz_church_form_header_church_faq'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
               'church_faq_choice' => array(
                   'type' => 'custom',
                   'content' => $aCustomFaqTemplates['church_faq_choice'],
                   'name' => 'church_faq_choice[]',
                   'caption' => _t('_modzzz_church_form_caption_church_faq_choice'),
                   'info' => _t('_modzzz_church_form_info_church_faq_choice'),
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
 
		if(empty($aCustomFaqTemplates['church_faq_choice'])){ 
            unset ($aCustomForm['inputs']['header_church_faq']);
            unset ($aCustomForm['inputs']['church_faq_choice']);
		}
  
        parent::BxDolFormMedia ($aCustomForm);
    }
  
	function generateCustomFaqTemplate ($iChurchId) {
	 
		$aTemplates = array ();
		$aTemplates['church_faq_choice'] = array();
   
		//church faq
		$aAllFaqs = $this->_oDb->getFaqItems ($iChurchId); 
 
		$aFaqs = array();
		foreach ($aAllFaqs as $k => $r) {
			$aFaqs[$k] = array();
			$aFaqs[$k]['id'] = $r['id'];
			$aFaqs[$k]['question'] = $r['question'];
		}

		$aVarsChoice = array ( 
			'bx_if:empty' => array(
				'condition' => empty($aFaqs),
				'content' => array ()
			),

			'bx_repeat:faqs' => $aFaqs,
		);  
  
		if (!empty($aFaqs)){ 
			$aTemplates['church_faq_choice'] =  $this->_oMain->_oTemplate->parseHtmlByName('form_field_church_faq_choice', $aVarsChoice);
		}

		return $aTemplates;
	}  
  

}
