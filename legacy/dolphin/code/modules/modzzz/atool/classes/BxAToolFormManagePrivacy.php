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

class BxAToolFormManagePrivacy extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxAToolFormManagePrivacy ($oMain) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
    
		$aModules = $this->_oDb->getModules();

		$aActions = array();
		$aDefaults = array();
		if($_REQUEST['module_uri']){
			$aActions = $this->_oDb->getFormModuleActions($_REQUEST['module_uri']);
  
			if($_REQUEST['action']){
				$aDefaults = $this->_oDb->getFormGroupChooser(0, $_REQUEST['module_uri'], $_REQUEST['action']);

				bx_import ('BxDolPrivacy'); 
				$oPrivacy = new BxDolPrivacy();

				$sDefaultValue = $oPrivacy->_oDb->getDefaultValue(0, $_REQUEST['module_uri'], $_REQUEST['action']);

				if(empty($sDefaultValue))
					$sDefaultValue = $oPrivacy->_oDb->getDefaultValueModule($_REQUEST['module_uri'], $_REQUEST['action']);
 
			}
		}
  
		$sActionUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'home/?ajax=privacy_action&module='; 

		$sDefaultUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'home/?ajax=privacy_default&module='; 

		$aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_atool',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array( 
                    'submit_name' => 'submit_form',
                ),
            ), 
            'inputs' => array(
                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_atool_form_header_manage_privacy')
                ),    
                'module_uri' => array(
                    'type' => 'select',
                    'name' => 'module_uri',
                    'caption' => _t('_modzzz_atool_form_caption_module'),
                    'values' => $aModules,   
					'attrs' => array(
						'onchange' => "getHtmlData('subaction','$sActionUrl'+this.value)",
					),	 
                ),  
                'action' => array(
                    'type' => 'select',
                    'name' => 'action',
                    'caption' => _t('_modzzz_atool_form_caption_action'),
					'values' => $aActions,
					'attrs' => array(
						'id' => 'subaction', 
						'onchange' => "getHtmlData('subdefault','$sDefaultUrl'+document.form_atool.module_uri.value+'&action='+this.value)",
					),	
                ),    
                'default' => array(
                    'type' => 'select',
                    'name' => 'default',  
					'value' => $sDefaultValue,
					'values' => $aDefaults,
                    'caption' => _t('_modzzz_atool_form_caption_default'), 
					'attrs' => array(
						'id' => 'subdefault',
					),  
                ),   
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_modzzz_atool_form_submit_default'),
                    'colspan' => false,
                ),                            
            ),            
        );
  
        parent::BxDolFormMedia ($aCustomForm);
    }

}
