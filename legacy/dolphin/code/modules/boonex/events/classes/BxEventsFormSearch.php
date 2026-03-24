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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolProfileFields.php');

class BxEventsFormSearch extends BxTemplFormView {

    function BxEventsFormSearch () {
		
         bx_import('BxDolCategories');
        $oCategories = new BxDolCategories();
        $oCategories->getTagObjectConfig ();
        $aCategories = $oCategories->getCategoriesList('bx_events', (int)$iProfileId, true);
		$aCategories[''] = _t('_bx_events_all_categories');  

        $oMain = BxDolModule::getInstance('BxEventsModule');

		$sStateUrl = bx_append_url_params(BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'home', 'ajax=state&country=');
 
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
        $aCountries = array_merge (array('' => _t('_bx_events_all_countries')), $aCountries);

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_search_events',
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
                    'caption' => _t('_bx_events_caption_keyword'),
                    'required' => false,
				/*
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_bx_events_err_keyword'),
                    ),
					*/
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),                
                'Country' => array(
 					'type' => 'select',
                    'name' => 'Country',
                    'caption' => _t('_bx_events_caption_country'),
                    'values' => $aCountries, 
					'attrs' => array(
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
					'caption' => _t('_bx_events_caption_state'),
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
					'caption' => _t('_bx_events_caption_city'),
					'required' => false, 
					'db' => array (
					'pass' => 'Xss', 
					),                
				), 
				'EventStart' => array(
					'type' => 'datetime',
					'name' => 'EventStart',
					'caption' => _t('_bx_events_caption_event_start'),
					'required' => false, 
					'db' => array (
					'pass' => 'DateTime', 
					),    
					'display' => 'filterDate',
				),                                
				'EventEnd' => array(
					'type' => 'datetime',
					'name' => 'EventEnd',
					'caption' => _t('_bx_events_caption_event_end'),
					'required' => false, 
					'db' => array (
					'pass' => 'DateTime', 
					),                    
					'display' => 'filterDate',
				),  
				'Categories' => array(
					'type' => 'select_box',
					'name' => 'Categories',
					'caption' => _t('_bx_events_caption_categories'),
					'values' => $aCategories,
					'required' => false, 
					'db' => array (
					'pass' => 'Xss', 
					),                    
				),
/*					
				'Public' => array(
					'type' => 'select',
					'name' => 'Public',
					'caption' => _t('_bx_events_caption_public_only'),
					'values' => array(
						1=>_t('_bx_events_yes'),
						0=>_t('_bx_events_no') 
					),
					'required' => false, 
					'db' => array (
						'pass' => 'Xss', 
					),                    
				),  
*/  
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
