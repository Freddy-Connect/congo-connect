<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Classified
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

bx_import('BxDolProfileFields');

class BxClassifiedFormSearch extends BxTemplFormView {

    function BxClassifiedFormSearch () {
 
		$oMain = BxDolModule::getInstance('BxClassifiedModule');
  
        $oProfileFields = new BxDolProfileFields(0);
        $aDefCountries = $oProfileFields->convertValues4Input('#!Country');
		asort($aDefCountries);
		$aChooseCountries = array('-'=>_t("_Select"));   
		$aCountries = array_merge($aChooseCountries, $aDefCountries);
 
		$aStates = $oMain->_oDb->getStateArray($sDefaultCountry);  

		$sStateUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'home/'.($oMain->isPermalinkEnabled() ? '?' : '&').'ajax=state&country=' ;
 
		$sCatUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'home/'.($oMain->isPermalinkEnabled() ? '?' : '&').'ajax=cat&parent=' ;

		$aCategories = $oMain->_oDb->getFormCategoryArray();
 
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_search_classified',
                'action'   => '',
                'method'   => 'get',
            ),      

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
                'csrf' => array(
                    'disable' => true,
                ),
            ),
                  
            'inputs' => array(
                'Keyword' => array(
                    'type' => 'text',
                    'name' => 'Keyword',
                    'caption' => _t('_modzzz_classified_form_caption_keyword'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),                
                'parent' => array(
                    'type' => 'select',
                    'name' => 'parent',
					'values'=> $aCategories,
                    'caption' => _t('_modzzz_classified_parent_categories'),
					'attrs' => array(
						'onchange' => "getHtmlData('subcat','$sCatUrl'+this.value)",
                        'id' => 'parentcat',
					),
                    'required' => false, 
                ), 
                'Category' => array(
                    'type' => 'select',
                    'name' => 'Category',
					'values'=> array(),
                    'caption' => _t('_modzzz_classified_form_caption_subcategories'),
					'attrs' => array(
                        'id' => 'subcat',
					),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ),
                'Country' => array(
					'type' => 'select',
                    'name' => 'Country',
                    'caption' => _t('_modzzz_classified_form_caption_country'),
                    'values' => $aCountries,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					), 
					'value' => $sDefaultCountry, 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{0,2})/'),
                    ),  
                ),
				'State' => array(
					'type' => 'select',
					'name' => 'State', 
					'caption' => _t('_modzzz_classified_caption_state'),
					'values'=> $aStates, 
					'attrs' => array(
						'id' => 'substate',
					), 
				    'db' => array (
						'pass' => 'Preg', 
						'params' => array('/([a-zA-Z]+)/'),
					), 
				), 
                'City' => array(
                    'type' => 'text',
                    'name' => 'City',
                    'caption' => _t('_modzzz_classified_form_caption_city'),
                    'required' => false,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ), 
                ),    
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'), 
                ),
            ),            
        );

        parent::BxTemplFormView ($aCustomForm);
    }
}
