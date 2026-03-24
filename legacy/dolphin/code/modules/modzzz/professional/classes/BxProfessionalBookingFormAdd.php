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

class BxProfessionalBookingFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxProfessionalBookingFormAdd ($oMain, $iProfileId, $iServiceId = 0, $iEntryId = 0, $iThumb = 0) { 
        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableBookingMediaPrefix;
  		bx_import ('SubPrivacy', $this->_oMain->_aModule);
		$GLOBALS['oBxProfessionalModule']->_oSubPrivacy = new BxProfessionalSubPrivacy($this->_oMain, $this->_oDb->_sTableBooking); 
 
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
		$aCountries = array(''=>_t('_Select')) + $aCountries;

		$aService = $this->_oDb->getServiceEntryById($iServiceId);
		$iProfessionalId = (int)$aService['professional_id'];

		if($iEntryId) {
			$aDataEntry = $this->_oDb->getBookingEntryById($iEntryId);
  
			$sSelState =  ($_POST['state']) ? $_POST['state'] : $aDataEntry['state']; 
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : $aDataEntry['country'];  
			$aStates = $this->_oDb->getStateArray($sSelCountry);  
		}else {
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : ''; 
			$sSelState = ($_POST['state']) ? $_POST['state'] : ''; 
			$aStates = $this->_oDb->getStateArray($sSelCountry);   
 		}
  
		$sStateUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home/?ajax=state&country=' ; 
    

        // privacy
 
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_bookings',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_professional_booking_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_booking_header_info')
                ),                
                'professional_id' => array(
                    'type' => 'hidden',
                    'name' => 'professional_id', 
                    'value' => $iProfessionalId,
                    'db' => array (
                        'pass' => 'Int' 
                    ) 
                 ),
                'service_id' => array(
                    'type' => 'hidden',
                    'name' => 'service_id', 
                    'value' => $iServiceId,
                    'db' => array (
                        'pass' => 'Int' 
                    ) 
                 ),

                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_professional_form_caption_title'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3, $this->_oMain->_oConfig->getTitleLength()),
                        'error' => _t ('_modzzz_professional_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => false,
                ),    
                'desc' => array(
                    'type' => 'textarea',
                    'name' => 'desc',
                    'caption' => _t('_modzzz_professional_form_caption_booking_details'),
                    'required' => true,
                    'html' => 2,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(20,64000),
                        'error' => _t ('_modzzz_professional_err_desc'),
                    ),                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),  
                'header_booking_schedule' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_booking_schedule')
                ),  
                'start_time' => array(
                    'type' => 'datetime',
                    'name' => 'start_time',
                    'caption' => _t('_modzzz_professional_form_caption_start_time'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'DateTime',
                        'error' => _t ('_modzzz_professional_form_err_start_time'),
                    ),
                    'db' => array (
                        'pass' => 'DateTime', 
                    ),    
                    'infodisplay' => 'filterDate',
                ),                                
                'end_time' => array(
                    'type' => 'datetime',
                    'name' => 'end_time',
                    'caption' => _t('_modzzz_professional_form_caption_end_time'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'DateTime',
                        'error' => _t ('_modzzz_professional_form_err_end_time'),
                    ),
                    'db' => array (
                        'pass' => 'DateTime', 
                    ),    
                    'infodisplay' => 'filterDate',
                ),  
 
                'header_location' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_location')
                ),  
				'country' => array(
                    'type' => 'select',
                    'name' => 'country',
					'listname' => 'Country',
                    'caption' => _t('_modzzz_professional_form_caption_country'),
                    'values' => $aCountries,
 					'value' => $sSelCountry,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					),	 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{2})/'),
                    ),
					'display' => 'getPreListDisplay', 
                ),
				'state' => array(
					'type' => 'select',
					'name' => 'state',
					'value' => $sSelState,  
					'values'=> $aStates,
					'caption' => _t('_modzzz_professional_caption_state'),
					'attrs' => array(
						'id' => 'substate',
					), 
					'db' => array (
					'pass' => 'Preg', 
					'params' => array('/([0-9a-zA-Z]+)/'),
					), 
					'display' => 'getStateName',  
				), 
                'city' => array(
                    'type' => 'text',
                    'name' => 'city',
                    'caption' => _t('_modzzz_professional_form_caption_city'),
                    'required' => false,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => false,
                ),    
                'address1' => array(
                    'type' => 'text',
                    'name' => 'address1',
                    'caption' => _t('_modzzz_professional_form_caption_address1'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),   				
                'address2' => array(
                    'type' => 'text',
                    'name' => 'address2',
                    'caption' => _t('_modzzz_professional_form_caption_address2'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'zip' => array(
                    'type' => 'text',
                    'name' => 'zip',
                    'caption' => _t('_modzzz_professional_form_caption_zip'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  
					
                'header_contact' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_contact')
                ), 
                'name' => array(
                    'type' => 'text',
                    'name' => 'name',
                    'caption' => _t('_modzzz_professional_form_caption_name'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  
                'email' => array(
                    'type' => 'email',
                    'name' => 'email',
                    'caption' => _t('_modzzz_professional_form_caption_email'),
                    'required' => false,  
					'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'telephone' => array(
                    'type' => 'text',
                    'name' => 'telephone',
                    'caption' => _t('_modzzz_professional_form_caption_telephone'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'mobile' => array(
                    'type' => 'text',
                    'name' => 'mobile',
                    'caption' => _t('_modzzz_professional_form_caption_mobile'),
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
 
        parent::BxDolFormMedia ($aCustomForm);
    }
 

}

