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

class BxListingReviewForm extends BxTemplFormView {

    function BxListingReviewForm ($oMain) {
 
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_review',
                'action'   => '',
                'method'   => 'post',
            ),      

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array( 
 
                'type' => array(
                    'type' => 'hidden',
                    'name' => 'type',
                    'value' => $sType, 
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),

                'review_text' => array(
                    'type' => 'textarea',
                    'name' => 'review_text',
                    'caption' => _t('_modzzz_listing_review_text'),
                    'info' => _t('_modzzz_listing_review_text_info'),
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
