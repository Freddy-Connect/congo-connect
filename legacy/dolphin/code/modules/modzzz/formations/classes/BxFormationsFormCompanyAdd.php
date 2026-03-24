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

class BxFormationsFormCompanyAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxFormationsFormCompanyAdd ($oMain, $iProfileId, $iEntryId = 0, $iThumb = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
 
		$aDataEntry = $this->_oDb->getCompanyEntryById($iEntryId); 
		$sSelState = ($_POST['company_state']) ? $_POST['company_state'] : $aDataEntry['company_state']; 
		$sSelCountry = ($_POST['company_country']) ? $_POST['company_country'] : $aDataEntry['company_country']; 
		$aStates = $this->_oDb->getStateArray($sSelCountry);  
 
		$sStateUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_formations_permalinks') ? '?' : '&').'ajax=state&country=' ; 
 
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
 
		$sIconName = $aDataEntry['company_icon'];
		$sPresentIcon = $this->_oDb->getCompanyIcon($iEntryId, $sIconName);

        // privacy

        $aInputPrivacyCustom = array ();
        $aInputPrivacyCustom[] = array ('key' => '', 'value' => '----');
        $aInputPrivacyCustom[] = array ('key' => 'f', 'value' => _t('_modzzz_formations_privacy_fans_only'));
        $aInputPrivacyCustomPass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9f]+)$/'),
        );

        $aInputPrivacyCustom2 = array (
            array('key' => 'f', 'value' => _t('_modzzz_formations_privacy_fans')),
            array('key' => 'a', 'value' => _t('_modzzz_formations_privacy_admins_only'))
        );
        $aInputPrivacyCustom2Pass = array (
            'pass' => 'Preg', 
            'params' => array('/^([fa]+)$/'),
        );
 
        $aInputPrivacyComment = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'formations', 'comment');
        $aInputPrivacyComment['values'] = array_merge($aInputPrivacyComment['values'], $aInputPrivacyCustom);
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'formations', 'rate');
        $aInputPrivacyRate['values'] = array_merge($aInputPrivacyRate['values'], $aInputPrivacyCustom);
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;
 
  
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_formations',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_formations_companies',
                    'key' => 'id',
                    'uri' => 'company_uri',
                    'uri_title' => 'company_name',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(
  
                'header_company' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_formations_form_header_company_edit'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'company_name' => array(
                    'type' => 'text',
                    'name' => 'company_name',
                    'caption' => _t('_modzzz_formations_form_caption_name'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'company_desc' => array(
                    'type' => 'textarea',
                    'name' => 'company_desc',
                    'caption' => _t('_modzzz_formations_form_caption_desc'),
                    'required' => false,
                    'html' => 2, 
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),    
                'company_country' => array(
                    'type' => 'select',
                    'name' => 'company_country',
                    'caption' => _t('_modzzz_formations_caption_country'),
                    'values' => $aCountries,
                    'required' => true,
                    'checker' => array (
                        'func' => 'preg',
                        'params' => array('/^[a-zA-Z]{2}$/'),
                        'error' => _t ('_modzzz_formations_err_country'),
                    ),      
 					'value' => $sSelCountry,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					),	
                     'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{2})/'),
                    ),
					'display' => true 
                ), 
 				'company_state' => array(
					'type' => 'select',
					'name' => 'company_state',
					'value' => $sSelState,  
					'values'=> $aStates,
					'caption' => _t('_modzzz_formations_caption_state'),
					'attrs' => array(
						'id' => 'substate',
					), 
					'db' => array (
						'pass' => 'Preg', 
						'params' => array('/([a-zA-Z]+)/'),
					), 
					'display' => 'getStateName',  
				), 
 	            'company_city' => array(
                    'type' => 'text',
                    'name' => 'company_city',
                    'caption' => _t('_modzzz_formations_form_caption_city'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
	            'company_address' => array(
                    'type' => 'text',
                    'name' => 'company_address',
                    'caption' => _t('_modzzz_formations_form_caption_address'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  				
                'company_zip' => array(
                    'type' => 'text',
                    'name' => 'company_zip',
                    'caption' => _t('_modzzz_formations_form_caption_zip'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'company_telephone' => array(
                    'type' => 'text',
                    'name' => 'company_telephone',
                    'caption' => _t('_modzzz_formations_form_caption_telephone'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'company_fax' => array(
                    'type' => 'text',
                    'name' => 'company_fax',
                    'caption' => _t('_modzzz_formations_form_caption_fax'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'company_website' => array(
                    'type' => 'text',
                    'name' => 'company_website',
                    'caption' => _t('_modzzz_formations_form_caption_website'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
					'display' => 'getWebsiteLink',  
                ), 
                'company_email' => array(
                    'type' => 'text',
                    'name' => 'company_email',
                    'caption' => _t('_modzzz_formations_form_caption_email'),
                    'required' => false, 
					'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
				/*
                'business_type' => array(
                    'type' => 'text',
                    'name' => 'business_type',
                    'caption' => _t('_modzzz_formations_form_caption_business_type'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),*/
                'employee_count' => array(
                    'type' => 'text',
                    'name' => 'employee_count',
                    'caption' => _t('_modzzz_formations_form_caption_employee_count'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'office_count' => array(
                    'type' => 'text',
                    'name' => 'office_count',
                    'caption' => _t('_modzzz_formations_form_caption_office_count'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
				'present_icon' => array(
					'type' => 'custom',
					'name' => "present_icon", 
					'caption' => _t('_modzzz_formations_present_icon'), 
					'content' =>  $sPresentIcon  
				), 

				'company_icon' => array(
					'type' => 'custom',
					'name' => "company_icon",
					'caption' => _t('_modzzz_formations_form_icon'), 
					'content' =>  "<input type=file  name='company_icon'>",  
				), 

/*
                // privacy 
                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_formations_form_header_privacy'),
                ),

                'allow_view_company_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'formations', 'view_formation'),
 
                'allow_comment_to' => $aInputPrivacyComment,

                'allow_rate_to' => $aInputPrivacyRate, 
*/ 

                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                    'colspan' => false,
                ),                            
            ),            
        );
   
		if(!$sPresentIcon)
			unset ($aCustomForm['inputs']['present_icon']);

        parent::BxDolFormMedia ($aCustomForm);
    }

}
