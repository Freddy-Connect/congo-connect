<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Reward
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

bx_import ('BxDolFormMedia');

class BxClassifiedFormPromoAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxClassifiedFormPromoAdd ($oMain, $iEntryId=0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
  
		if($iEntryId) {
			$aDataEntry = $this->_oDb->getPromoData();  
		}
 
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_classified',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),  
            'params' => array (
                'db' => array(
                    'table' => 'modzzz_classified_promo',
                    'key' => 'id', 
                    'submit_name' => 'submit_form',
                ),
            ),    
            'inputs' => array( 
                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_classified_form_modify_promotion_details')
                ),                
                'details' => array(
                    'type' => 'textarea',
                    'name' => 'details',
                    'caption' => _t('_modzzz_classified_form_caption_details'),
                    'required' => true,
                    'html' => 2,   
					'checker' => array (
						'func' => 'avail',
 						'error' => _t ('_modzzz_classified_form_err_details'),
					),
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),  
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Save'),
                    'colspan' => false,
                ),    
            ),            
        );
  
 
        parent::BxDolFormMedia ($aCustomForm);
    }

}
