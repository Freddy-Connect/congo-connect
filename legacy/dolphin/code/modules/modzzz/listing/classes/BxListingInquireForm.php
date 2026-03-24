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

class BxListingInquireForm extends BxTemplFormView {

    function BxListingInquireForm ($oMain ) {

		 $sFullName = $sEmail = '';
		 if($oMain->_iProfileId){
			$aProfileInfo = getProfileInfo($oMain->_iProfileId);
			$sFullName = $aProfileInfo['FullName'];
			$sEmail = $aProfileInfo['Email'];
		 }

		$iLanguageId = getLangIdByName(getCurrentLangName());

		/* Freddy commentaire pour désactiver les category par langue
		$aCategory = $oMain->_oDb->getFormCategoryArray($iLanguageId, $oMain->isAdmin());
		*/
		$aCategory = $oMain->_oDb->getFormCategoryArray();
		///////////////Fin Freddy modif  ///////////

		$aEmployees = $oMain->_oTemplate-> getEmployees();
		$aEmployees = array(''=>_t('_Select')) + $aEmployees;

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_inquire',
                'action'   => '',
                'method'   => 'post',
            ),      

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array( 
 
              /*  'name' => array(
                    'type' => 'text',
                    'name' => 'name',
                    'caption' => _t('_modzzz_listing_form_caption_name'),
                    'value' => $sFullName, 
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3, 255),
                        'error' => _t ('_modzzz_listing_form_err_name'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'companyname' => array(
                    'type' => 'text',
                    'name' => 'companyname',
                    'caption' => _t('_modzzz_listing_form_caption_companyname'),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'industry' => array(
                    'type' => 'select',
                    'name' => 'industry',
					'values'=> $aCategory,
                    'caption' => _t('_modzzz_listing_form_caption_review_industry'), 
                    'db' => array (
                        'pass' => 'Xss', 
                    ), 
                ),  
                'employee_count' => array(
                    'type' => 'select',
                    'name' => 'employee_count',
                    'caption' => _t('_modzzz_listing_form_caption_review_employee_count'),
					'values'=> $aEmployees,
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'email' => array(
                    'type' => 'text',
                    'name' => 'email',
                    'caption' => _t('_modzzz_listing_form_caption_review_email'),
                    'value' => $sEmail, 
                    'required' => true,  
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_listing_form_err_review_email'),
                    ), 
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'phone' => array(
                    'type' => 'text',
                    'name' => 'phone',
                    'caption' => _t('_modzzz_listing_form_caption_review_phone'),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'cellphone' => array(
                    'type' => 'text',
                    'name' => 'cellphone',
                    'caption' => _t('_modzzz_listing_form_caption_review_cell'),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
				*/
                'inquire_text' => array(
                    'type' => 'textarea',
                    'name' => 'inquire_text',
                    'caption' => _t('_modzzz_listing_form_caption_review_inquiry'),
                    'info' => _t('_modzzz_listing_form_info_review_inquiry'),
                    'required' => true, 
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(20,64000),
                        'error' => _t ('_modzzz_listing_form_err_review_inquiry'),
                    ), 
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),      
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                    'colspan' => true,
                ),
            ),            
        );

        parent::BxTemplFormView ($aCustomForm);
    }
}
