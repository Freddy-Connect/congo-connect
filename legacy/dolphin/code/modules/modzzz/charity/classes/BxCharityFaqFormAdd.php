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

class BxCharityFaqFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxCharityFaqFormAdd ($oMain, $iProfileId, $iCharityId = 0, $iFaqId = 0) { 
        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
 
        // generate templates for form custom elements
         $aCustomFaqTemplates = $this->generateCustomFaqTemplate ($iCharityId);
  
		$aDataEntry = $this->_oDb->getEntryById($iCharityId);

		$sTitle = $aDataEntry[$this->_oDb->_sFieldTitle] .' '. _t('_modzzz_charity_faq');
  
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_faqs',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_charity_faq_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_charity_form_header_faq_info')
                ),  
                'charity_id' => array(
                    'type' => 'hidden',
                    'name' => 'charity_id', 
                    'value' => $iCharityId, 
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
                    'caption' => _t('_modzzz_charity_form_caption_question'),
                    'required' => false,  
                ),  
				'answer' => array(
                    'type' => 'text',
                    'name' => 'answer[]',
					'attrs' => array(
						'id' => "answer",
						'class' => "answer",
 					),  
                    'caption' => _t('_modzzz_charity_form_caption_answer'),
                    'required' => false,  
                ), 				
				'addbutton' => array(
					'type' => 'custom', 
                    'content' => '<input type="button" id="addbutton" 
						class="addbutton" value="Add Question" onClick="addQuestion()" />',  
                    'name' => 'addbutton',
                    'caption' => _t('_modzzz_charity_add_another_question'),  
                ),
 
               'header_charity_faq' => array(
                   'type' => 'block_header',
                   'caption' => _t('_modzzz_charity_form_header_charity_faq'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
               'charity_faq_choice' => array(
                   'type' => 'custom',
                   'content' => $aCustomFaqTemplates['charity_faq_choice'],
                   'name' => 'charity_faq_choice[]',
                   'caption' => _t('_modzzz_charity_form_caption_charity_faq_choice'),
                   'info' => _t('_modzzz_charity_form_info_charity_faq_choice'),
                   'required' => false,
               ), 
  
			   'Submit' => array (
					'type' => 'submit',
					'name' => 'submit_form',
					'value' => _t('_Submit'),
					'colspan' => true,
				),  

            ),            
        );
 
		if(empty($aCustomFaqTemplates['charity_faq_choice'])){ 
            unset ($aCustomForm['inputs']['header_charity_faq']);
            unset ($aCustomForm['inputs']['charity_faq_choice']);
		}
  
        parent::BxDolFormMedia ($aCustomForm);
    }
  
	function generateCustomFaqTemplate ($iCharityId) {
	 
		$aTemplates = array ();
		$aTemplates['charity_faq_choice'] = array();
   
		//charity faq
		$aAllFaqs = $this->_oDb->getFaqItems ($iCharityId); 
 
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
			$aTemplates['charity_faq_choice'] =  $this->_oMain->_oTemplate->parseHtmlByName('form_field_charity_faq_choice', $aVarsChoice);
		}

		return $aTemplates;
	}  
  

}
