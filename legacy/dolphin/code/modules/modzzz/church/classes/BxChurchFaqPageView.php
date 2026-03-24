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

bx_import('BxDolTwigPageView');

class BxChurchFaqPageView extends BxDolTwigPageView {	

    function BxChurchFaqPageView(&$oFaqsMain, &$aFaq) {
        parent::BxDolTwigPageView('modzzz_church_faqs_view', $oFaqsMain, $aFaq); 
	}
   
	function getBlockCode_FaqItems() { 

		$sCode = '';
		$iLimit = 30;//$this->_oDb->getParam('modzzz_church_perpage_faqs');
		$sUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home' ;
  
		$iCount = $this->_oDb->getFaqItemCount($this->aDataEntry['id']);  

		if(!$iCount){
			$sCode = MsgBox(_t('_modzzz_church_faqs_no_items'));
			return array($sCode, array() ); 
		}
        $sPaginate = ''; 
        if ($iCount) {
            $iPages = ceil($iCount/ $iLimit);
            $iPage = ( isset($_GET['page']) ) ? (int) $_GET['page'] : 1;

            if ( $iPage < 1 ) {
                $iPage = 1;
            }
            if ( $iPage > $iPages ) {
                $iPage = $iPages;
            }    

            $sqlFrom = ($iPage - 1) * $iLimit;
            $sqlLimit = "LIMIT {$sqlFrom}, {$iLimit}";  
		}
		$aAllEntries = $this->_oDb->getFaqItems($this->aDataEntry['id'], $sqlLimit);
 
		foreach($aAllEntries as $aEntry){ 
 			$sCode .= $this->_oTemplate->faq_item_unit($aEntry, 'faq_item_unit');
		} 
   
		if ($iPages > 1) {
		   $oPaginate = new BxDolPaginate(array(
				'page_url' => $sUrl,
				'count' => $iCount,
				'per_page' => $iLimit,
				'page' => $iPage,
				'per_page_changer' => true,
				'page_reloader' => true,
				'on_change_page' => 'return !loadDynamicBlock({id}, \''.$sUrl.'?page={page}&per_page={per_page}\');',
				'on_change_per_page' => ''
			)); 
			
			$sAjaxPaginate = $oPaginate->getSimplePaginate('',-1,-1, false); 
		}   	
  
		return array($sCode, array(), $sAjaxPaginate);
	}
 
    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
			$aFaqEntry = $this->_oDb->getFaqEntryById($this->aDataEntry['id']);
			$iEntryId = $aFaqEntry['church_id'];
	 
		    $aDataEntry = $this->_oDb->getEntryById($iEntryId);
   
            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'TitleDelete' => $this->_oMain->isAllowedSubDelete($aDataEntry, $aFaqEntry) ? _t('_modzzz_church_action_title_delete') : '',
             );

            if (!$this->aInfo['TitleDelete'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'modzzz_church_faq');
        } 

        return '';
    }    
  
 
    function getCode() { 
        return parent::getCode();
    }    
}
