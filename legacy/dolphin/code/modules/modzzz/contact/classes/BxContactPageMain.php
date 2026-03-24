<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Review
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

bx_import('BxDolTwigPageMain');
bx_import('BxTemplCategories');

class BxContactPageMain extends BxDolTwigPageMain {

    function BxContactPageMain(&$oMain) {
		
		parent::BxDolTwigPageMain('modzzz_contact_main', $oMain);

        $this->oMain = $oMain ;

        $this->sUrlStart = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home';
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&'); 
 
        $this->sSearchResultClassName = 'BxContactSearchResult';
        $this->sFilterName = 'filter';
	}
 
	function getBlockCode_ContactUs() { 

       //$this->oTemplate->pageStart();
   
		$aVars = array('content' => _t('_modzzz_contact_msg_header')); 
		$sHeader = $this->oTemplate->parseHtmlByName('contact_header_padding', $aVars);
 
        modzzz_contact_import ('FormSubmit');
        $oForm = new BxContactFormSubmit ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$iDepartmentId = (int)$oForm->getCleanValue('department_id');

			if($iDepartmentId){
				$aDepartment = $this->oDb->getDepartmentById($iDepartmentId);
				$sRecipientEmail = $aDepartment['email'];
			}else{
				$sRecipientEmail = $GLOBALS['site']['email']; 
			}

			$oEmailTemplate = new BxDolEmailTemplates(); 
			$aTemplate = $oEmailTemplate->getTemplate('modzzz_contact_msg', 0);
			$sLetterSubject = $aTemplate['Subject']; 
			$sLetterBody = $aTemplate['Body']; 
 
 			$aTmplItems = array ();
 
			$aPlus = array();
			$aPlus['Email'] = $oForm->getCleanValue('email');
			$aPlus['Name'] = $oForm->getCleanValue('name');
 
			$aTmplItems[] = array (
				'caption' => _t('_message_subject'),
				'value' => $oForm->getCleanValue('subject'),
			); 

			$aTmplItems[] = array (
				'caption' => _t('_Message text'),
				'value' => $oForm->getCleanValue('desc'),
			); 
 
			$aActionType = $this->oDb->getActionTypes();
			foreach($aActionType as $aEachType){
				$sFieldName = $aEachType['field_name'];	
				$sFieldValue = trim($oForm->getCleanValue($sFieldName));
				if($sFieldValue){
					$sFieldCaption = _t($aEachType['title']);
					
					switch($aEachType['select_type']){
						case 'date':
							$sFieldValue = date('M d, Y', $sFieldValue);
						break;
						case 'single-select':
							$sFieldValue = $this->oTemplate->getActionName($sFieldValue);
						break;
						case 'multi-select':
							$sFieldValue = $this->oTemplate->getMultiActionName($sFieldValue);
						break; 
					}
				}
   
				$aTmplItems[] = array (
					'caption' => $sFieldCaption,
					'value' => $sFieldValue,
				); 
			 
			}
 
			$aTmplVars = array('bx_repeat:fields' => $aTmplItems);   
			$aPlus['Message'] = $this->oTemplate->parseHtmlByName('block_custom_fields', $aTmplVars);
 
			if (sendMail($sRecipientEmail, $sLetterSubject, $sLetterBody, 0, $aPlus , 'html', true, true)) {
				$sActionKey = '_modzzz_contact_msg_success';
			} else {
				$sActionKey = '_modzzz_contact_msg_fail';
			}
			$sActionText = MsgBox(_t($sActionKey));
 
            $aValsAdd = array (
				$this->oDb->_sFieldUri => $oForm->generateUri(),
                $this->oDb->_sFieldCreated => time() 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);

        } 

        return array($sActionText . $sHeader . $oForm->getCode ());

		//$this->oTemplate->addCss (array('unit.css', 'main.css', 'twig.css')); 
        //$this->oTemplate->pageCode(_t('_modzzz_contact_caption_search'));
    } 
  
    function getBlockCode_Departments() {
   
        return $this->ajaxBrowse( 
            'departments', 10, array(), '', true, false 
        ); 
    }

	function getBlockCode_Help() {
 
		$aTmplVars = array(
			'content' => str_replace( '<site_url>', $GLOBALS['site']['url'], _t( "_HELP" ))
		);

		return $this->oTemplate->parseHtmlByName('default_padding', $aTmplVars); 
	}

 	function getContactMain() {
        return BxDolModule::getInstance('BxContactModule');
    }

}