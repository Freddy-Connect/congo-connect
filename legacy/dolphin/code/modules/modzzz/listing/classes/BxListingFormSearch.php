<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Listing
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

class BxListingFormSearch extends BxTemplFormView {

    function BxListingFormSearch () {

        bx_import('BxDolCategories');
	    
		$oMain = BxDolModule::getInstance('BxListingModule');
 
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
		asort($aCountries);
		$aCountries = array(''=>_t("_Select")) + $aCountries;
		
		$aProfile = getProfileInfo((int)$iProfileId);
		//$sDefaultCountry = getParam('modzzz_listing_default_country');
		$sDefaultCountry = ($aProfile['Country']) ? $aProfile['Country'] : getParam('modzzz_listing_default_country');


		

		//$aStates = $oMain->_oDb->getStateArray($sDefaultCountry);  
		$aStates = array(); 
 
		$sCatUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=cat&parent=' ;
 
		$sStateUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_listing_permalinks') ? '?' : '&').'ajax=state&country=' ; 
 
		$iLanguageId = getLangIdByName(getCurrentLangName());

		/* Freddy commentaire pour désactiver les category par langue
		$aCategories = $oMain->_oDb->getFormCategoryArray($iLanguageId);
		*/
		$aCategories = $oMain->_oDb->getFormCategoryArray();
		///////////////Fin Freddy modif//////
 
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_search_listing',
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
                    'caption' => _t('_modzzz_listing_form_caption_keyword'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),                
               
                /*
				'Category' => array(
                    'type' => 'select',
                    'name' => 'Category',
					'values'=> array(),
                    'caption' => _t('_modzzz_listing_form_caption_subcategories'),
					'attrs' => array(
                        'id' => 'subcat',
					),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ),
				*/
                'Country' => array(
					'type' => 'select',
                    'name' => 'Country',
                    'caption' => _t('_modzzz_listing_form_caption_country'),
                    //'value' => $sDefaultCountry,
                    'values' => $aCountries,
					'attrs' => array(
					   'style' => 'width:200px',
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{0,2})/'),
                    ),  
                ),
				'State' => array(
					'type' => 'select',
					'name' => 'State', 
					'caption' => _t('_modzzz_listing_caption_state'),
					'values'=> $aStates, 
					'attrs' => array(
					   'style' => 'width:200px', 
						'id' => 'substate',
					), 
				    'db' => array (
						'pass' => 'Preg', 
						'params' => array('/([a-zA-Z]+)/'),
					), 
				), 
               /* 'City' => array(
                    'type' => 'text',
                    'name' => 'City',
                    'caption' => _t('_modzzz_listing_form_caption_city'),
                    'required' => false,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  
				*/ 
				 'Parent' => array(
                    'type' => 'select',
                    'name' => 'Parent',
					'values'=> $aCategories,
                    'caption' => _t('_modzzz_listing_form_caption_categories'),
					'attrs' => array(
					    'style' => 'width:200px',
						'onchange' => "getHtmlData('subcat','$sCatUrl'+this.value)",
                        'id' => 'parentcat',
					),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ),  
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit_lancer_recherche'),
                    'colspan' => false,
                ),
            ),            
        );

        parent::BxTemplFormView ($aCustomForm);
    }
}
