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

bx_import('BxDolProfileFields');

class BxCharityFormSearch extends BxTemplFormView {

    function BxCharityFormSearch () {

        bx_import('BxDolCategories');
        $oCategories = new BxDolCategories();
        $oCategories->getTagObjectConfig ();
        $aCategories = $oCategories->getCategoriesList('modzzz_charity', (int)$iProfileId, true);	    
 

		$oMain = BxDolModule::getInstance('BxCharityModule');
 
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
		$aCountries[''] = "";  
		asort($aCountries);
		$aProfile = getProfileInfo((int)$iProfileId);
		$sDefaultCountry = $aProfile['Country'];
		$aStates = $oMain->_oDb->getStateArray($sDefaultCountry);  
 
		$sStateUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'home/?ajax=state&country=' ;
   
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_search_charity',
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
                    'caption' => _t('_modzzz_charity_form_caption_keyword'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),   
                'Category' => array(
                    'type' => 'select_box',
                    'name' => 'Category',
                    'caption' => _t('_modzzz_charity_form_caption_category'),
                    'values' => $aCategories,
                    'required' => false,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ), 
                'Country' => array(
					'type' => 'select',
                    'name' => 'Country',
                    'caption' => _t('_modzzz_charity_form_caption_country'),
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
					'caption' => _t('_modzzz_charity_caption_state'),
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
                    'caption' => _t('_modzzz_charity_form_caption_city'),
                    'required' => false,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),    
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                    'colspan' => false,
                ),
            ),            
        );

        parent::BxTemplFormView ($aCustomForm);
    }
}
