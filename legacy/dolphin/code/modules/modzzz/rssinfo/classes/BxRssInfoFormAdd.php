<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx RssInfo
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

class BxRssInfoFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxRssInfoFormAdd ($oMain, $iProfileId, $iEntryId = 0 ) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
  
		$aOwner = $this->_oDb->getAdmins();

		$aYesNoOptions = array(
			'0' => _t('_modzzz_rssinfo_no'),
			'1' => _t('_modzzz_rssinfo_yes') 
		);

		$oModuleDb = new BxDolModuleDb(); 
		if($oModuleDb->isModule('mnews'))  
			$oNews = BxDolModule::getInstance('BxMNewsModule');
		else 
			$oNews = BxDolModule::getInstance('BxNewsModule');
 
		$aCategory = $oNews->_oDb->getFormCategoryArray();
		$aSubCategory = array();
   
 		$sCatUrl = bx_append_url_params(BX_DOL_URL_ROOT . $oNews->_oConfig->getBaseUri() . 'home', 'ajax=cat&parent='); 

		if($iEntryId){
			$aDataEntry = $this->_oDb->getEntryById($iEntryId);

			$iSelSubCategory = $aDataEntry['category_id']; 
			$iSelCategory = $aDataEntry['parent_category_id']; 
			$aSubCategory = $oNews->_oDb->getFormCategoryArray($iSelCategory); 
		}else{ 
			$aSubCategory = ($_POST['parent_category_id']) ? $oNews->_oDb->getFormCategoryArray($_POST['parent_category_id']) : array(); 
		}

	    $aLangs = $this->_oDb->getLanguages();
		$aLangs = array(''=>_t('_Select')) + $aLangs;
 
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_rssinfo',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_rssinfo_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_rssinfo_form_header_info')
                ),     
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_rssinfo_form_caption_title'), 
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_modzzz_rssinfo_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'owner_id' => array(
                    'type' => 'select',
                    'name' => 'owner_id',
                    'caption' => _t('_modzzz_rssinfo_form_caption_owner'), 
                    'info' => _t('_modzzz_rssinfo_form_info_owner'), 
                    'required' => true,
                    'values' => $aOwner,
                    'checker' => array (
                        'func' => 'avail', 
                        'error' => _t ('_modzzz_rssinfo_form_err_owner'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'link' => array(
                    'type' => 'text',
                    'name' => 'link',
                    'caption' => _t('_modzzz_rssinfo_form_caption_link'), 
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1,255),
                        'error' => _t ('_modzzz_rssinfo_form_err_link'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
				'parent_category_id' => array(
                    'type' => 'select',
                    'name' => 'parent_category_id',
					'values'=> $aCategory,
                    'value' => $iSelCategory,
                    'caption' => _t('_modzzz_news_form_caption_categories'),
					'attrs' => array(
						'onchange' => "getHtmlData('subcat','$sCatUrl'+this.value)",
                        'id' => 'parentcat',
					),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_news_form_err_category'),
                    ), 
                    'db' => array (
                        'pass' => 'Int', 
                    ), 
                ), 
                'category_id' => array(
                    'type' => 'select',
                    'name' => 'category_id',
					'values'=> $aSubCategory,
                    'value' => $iSelSubCategory, 
                    'caption' => _t('_modzzz_news_form_caption_subcategories'),
					'attrs' => array(
                        'id' => 'subcat',
					),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ), 
                'tag' => array(
                    'type' => 'text',
                    'name' => 'tag',
                    'caption' => _t('_modzzz_rssinfo_form_caption_tag'), 
                    'info' => _t('_modzzz_rssinfo_form_info_tag'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'language' => array(
                    'type' => 'select',
                    'name' => 'language',
                    'caption' => _t('_modzzz_news_form_caption_language'),
                    'values' => $aLangs, 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ), 
                ), 

	            'fetch_count' => array(
                    'type' => 'text',
                    'name' => 'fetch_count',
                    'caption' => _t('_modzzz_rssinfo_form_caption_fetch_count'), 
                    'info' => _t('_modzzz_rssinfo_form_info_fetch_count'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),	 
                'source_link' => array(
                    'type' => 'select',
                    'name' => 'source_link',
                    'caption' => _t('_modzzz_rssinfo_form_caption_source_link'),
                    'info' => _t('_modzzz_rssinfo_form_info_source_link'),
					'values' => array(0=>_t('_modzzz_rssinfo_no'), 1=>_t('_modzzz_rssinfo_yes')),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ),   
				'publish' => array(
                    'type' => 'select',
                    'name' => 'publish',
                    'caption' => _t('_modzzz_rssinfo_form_caption_publish_now'),
                    'info' => _t('_modzzz_rssinfo_form_info_publish_now'),
					'values' => array(1=>_t('_modzzz_rssinfo_yes'), 0=>_t('_modzzz_rssinfo_no')),
                    'required' => false,  
					'db' => array (
                        'pass' => 'Int',  
                    ),
                ),  
				'status' => array(
                    'type' => 'select',
                    'name' => 'status',
                    'caption' => _t('_modzzz_rssinfo_form_caption_active'),
					'values' => array(1=>_t('_modzzz_rssinfo_yes'), 0=>_t('_modzzz_rssinfo_no')),
                    'required' => false,  
					'db' => array (
                        'pass' => 'Int',  
                    ),
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