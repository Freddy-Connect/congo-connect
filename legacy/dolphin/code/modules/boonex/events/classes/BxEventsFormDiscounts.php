<?php
/***************************************************************************
*                            Dolphin Smart Page Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Page Builder
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
 
bx_import ('BxDolFormMedia');

class BxEventsFormDiscounts extends BxDolFormMedia {
  
    function BxEventsFormDiscounts ($oMain, $iEntryId = 0) { 
 
        $this->_oMain = $oMain;
   
		$aDiscountTemplates = $this->generateDiscountsTemplate ($iEntryId);
 
        $aCustomForm = array(

            'form_attrs' => array(
                'name' => 'discount_form',
                'method' => 'post', 
                'action' => '',
            ),      

            'params' => array (
                'db' => array(
                     'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

               'header_discount' => array(
                   'type' => 'block_header',
                   'caption' => _t('_bx_events_form_header_discount_choices'), 
               ), 
               'discount_choice' => array(
                   'type' => 'custom',
                   'content' => $aDiscountTemplates['choice'],
                   'name' => 'discount_choice[]',
                   'caption' => _t('_bx_events_form_caption_discount_choice'),
                   'info' => _t('_bx_events_form_info_discount_choice'),
                   'required' => false,
               ),  
               'discount_upload' => array(
                   'type' => 'custom',
                   'content' => $aDiscountTemplates['upload'],
                   'name' => 'discount_upload[]',
                   'caption' => _t('_bx_events_form_caption_discounts'),
                   'info' => _t('_bx_events_form_info_discounts'),
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
  
	    if(!$aDiscountTemplates['choice']){
			unset($aCustomForm['inputs']['discount_choice']);
	    }

	    if(!$aDiscountTemplates['upload']){
			unset($aCustomForm['inputs']['discount_upload']);
	    }

        parent::BxDolFormMedia ($aCustomForm);
    }
 
	function generateDiscountsTemplate ($iEntryId) {
	 
		$aTemplates = array ();
		$aTemplates['discount_choice'] = '';
  
		//discount discounts
		$aAllDiscounts = $this->_oMain->_oDb->getAllDiscounts ($iEntryId); 
 
		$aDiscounts = array();
		foreach ($aAllDiscounts as $k => $r) { 
			$aDiscounts[$k] = array();
			$aDiscounts[$k]['id'] = $r['id'];
			$aDiscounts[$k]['discount'] = $r['cost'] .' - '. $r['tickets']; 
		}
 
		$aVarsChoice = array ( 
			'bx_if:empty' => array(
				'condition' => empty($aDiscounts),
				'content' => array ()
			), 
			'bx_repeat:discounts' => $aDiscounts,
		);  
  
		if (!empty($aDiscounts)){  
			$aTemplates['choice'] = $this->_oMain->_oTemplate->parseHtmlByName('form_field_discount_choice', $aVarsChoice);
		}
   
		for($iter=1; $iter<=31; $iter++){ 
			$sKey = ($iter<10) ? $iter : '0'.$iter; 
			$sDays .= '<option value="'.$sKey.'">'.$iter.'</option>'; 
 		}
    
		$aMonthData = array(
			'01'=> _t('_January'),
			'02'=> _t('_February'),
			'03'=> _t('_March'),
			'04'=> _t('_April'),
			'05'=> _t('_May'),
			'06'=> _t('_June'),
			'07'=> _t('_July'),
			'08'=> _t('_August'),
			'09'=> _t('_September'),
			'10'=> _t('_October'),
			'11'=> _t('_November'),
			'12'=> _t('_December') 
		);
 
		foreach($aMonthData as $sKey=>$sValue){ 
			$sMonths .= '<option value="'.$sKey.'">'.$sValue.'</option>'; 
 		}
 
		for($iYear=date('Y');$iYear<=date('Y')+10;$iYear++){ 
			$sYears .= '<option value="'.$iYear.'">'.$iYear.'</option>'; 
  		}
 
		// upload form
		$aVarsUpload = array (
			'day_options' => $sDays,
			'month_options' => $sMonths,
			'year_options' => $sYears,
		);   
		 
		$aTemplates['upload'] = $this->_oMain->_oTemplate->parseHtmlByName('form_field_discounts', $aVarsUpload);

		return $aTemplates;
	}
 
}
