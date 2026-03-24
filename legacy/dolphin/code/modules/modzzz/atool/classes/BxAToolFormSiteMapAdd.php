<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Confession
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

class BxAToolFormSiteMapAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxAToolFormSiteMapAdd ($oMain, $iEntryId = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
		
		$aType = $this->_oDb->getFormSiteMapUnits();

		$aFrequency = array(
			'always' => _t('_modzzz_atool_always'),
			'hourly' => _t('_modzzz_atool_hourly'),
			'daily' => _t('_modzzz_atool_daily'),
			'weekly' => _t('_modzzz_atool_weekly'),
			'monthly' => _t('_modzzz_atool_monthly'),
			'yearly' => _t('_modzzz_atool_yearly'),
			'never' => _t('_modzzz_atool_never'),
			'auto' => _t('_modzzz_atool_auto')
		);

		$aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_atool',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'sys_objects_site_maps',
                    'key' => 'id',
                    'submit_name' => 'submit_form',
                ),
            ),
 
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_atool_form_header_sitemap')
                ),  
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_atool_form_caption_title'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,50),
                        'error' => _t ('_modzzz_atool_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'changefreq' => array(
                    'type' => 'select',
                    'name' => 'changefreq',
                    'caption' => _t('_modzzz_atool_form_caption_frequency'),
                    'values' => $aFrequency,
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,255),
                        'error' => _t ('_modzzz_atool_form_err_frequency'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'priority' => array(
                    'type' => 'text',
                    'name' => 'priority',
                    'caption' => _t('_modzzz_atool_form_caption_priority'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,255),
                        'error' => _t ('_modzzz_atool_form_err_priority'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
				'active' => array(
                    'type' => 'select',
                    'name' => 'active',
                    'caption' => _t('_modzzz_atool_form_caption_active'),
                    'info' => _t('_modzzz_atool_form_info_active'),
                    'caption' => _t('_modzzz_atool_form_caption_active'),
                    'values' => array(1=>_t('_modzzz_atool_yes'), 0=>_t('_modzzz_atool_no')),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,255),
                        'error' => _t ('_modzzz_atool_form_err_active'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 		
				/* 
                'Order' => array(
                    'type' => 'text',
                    'name' => 'Order',
                    'caption' => _t('_modzzz_atool_form_caption_order'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),*/ 
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
