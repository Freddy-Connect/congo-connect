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

class BxGroupsFormSearch extends BxTemplFormView {

    function BxGroupsFormSearch () {

		//[begin] - ultimate groups mod from modzzz  
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
        $aCountries = array_merge (array('' => _t('_bx_groups_all_countries')), $aCountries);
  
        $oMain = BxDolModule::getInstance('BxGroupsModule');

		$sStateUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'home/?ajax=state&country=' ;
		//[end] - ultimate groups mod from modzzz 
	  
 
        bx_import('BxDolCategories');
        $oCategories = new BxDolCategories();
        $oCategories->getTagObjectConfig ();
        $aCategories = $oCategories->getCategoriesList('bx_groups', (int)$iProfileId, true);

		//[begin] - ultimate groups mod from modzzz  
		$aCategories[''] = _t('_bx_groups_all_categories');  
		//[end] - ultimate groups mod from modzzz 

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_search_groups',
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
                    'caption' => _t('_bx_groups_form_caption_keyword'),
					'required' => false,
                    /*
					'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_bx_groups_form_err_keyword'),
                    ),
					*/
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),                
                'Category' => array(
                    'type' => 'select_box',
                    'name' => 'Category',
                    'caption' => _t('_bx_groups_form_caption_category'),
                    'values' => $aCategories,
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ),

				//[begin] - ultimate groups mod from modzzz   
				'Country' => array(
					'type' => 'select',
					'name' => 'Country',
					'caption' => _t('_bx_groups_form_caption_country'),
					'values' => $aCountries,
					'required' => false, 
					'attrs' => array(
					'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					), 
					'db' => array (
					'pass' => 'Preg', 
					'params' => array('/([a-zA-Z]{0,2})/'),
					),                    
				),

				'State' => array(
					'type' => 'select',
					'name' => 'State', 
					'caption' => _t('_bx_groups_form_caption_state'),
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
					'caption' => _t('_bx_groups_form_caption_city'),
					'required' => false, 
					'db' => array (
					'pass' => 'Xss', 
					),                
				), 
				//[end] - ultimate groups mod from modzzz  
  
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
