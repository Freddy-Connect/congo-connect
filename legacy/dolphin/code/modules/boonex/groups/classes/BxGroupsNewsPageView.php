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

class BxGroupsNewsPageView extends BxDolTwigPageView {	

    function BxGroupsNewsPageView(&$oNewsMain, &$aNews) {
        parent::BxDolTwigPageView('bx_groups_news_view', $oNewsMain, $aNews);
	 
		$this->_oDb->_sFieldAllowViewTo = 'allow_view_to';
        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix; 
	}
   
	function getBlockCode_Info() {
        return array($this->_oTemplate->blockSubProfileInfo ('news', $this->aDataEntry));
    }

	function getBlockCode_Desc() {
        $aVars = array (
            'description' => $this->aDataEntry['desc'],
        );
        return array($this->_oTemplate->parseHtmlByName('block_description', $aVars));

    }

    function getBlockCode_Photos() {
        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
    }    
 
    function getBlockCode_Videos() {
        return $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Sounds() {
        return $this->_blockSound ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'sounds'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Files() {
        return $this->_blockFiles ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'files'), $this->aDataEntry['author_id']);
    }

	function _blockFiles ($aReadyMedia, $iAuthorId = 0) {        

        if (!$aReadyMedia)
            return '';

        $aVars = array (
            'bx_repeat:files' => array (),
        );

        foreach ($aReadyMedia as $iMediaId) {        

            $a = BxDolService::call('files', 'get_file_array', array($iMediaId), 'Search');
            if (!$a['date'])
                continue;

            bx_import('BxTemplFormView');
            $oForm = new BxTemplFormView(array());

            $aInputBtnDownload = array (
                'type' => 'submit',
                'name' => 'download', 
                'value' => _t ('_download'), 
                'attrs' => array(
                    'onclick' => "window.open ('" . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "news/download/".$this->aDataEntry[$this->_oDb->_sFieldId]."/{$iMediaId}','_self');",
                ),
            );

            $aVars['bx_repeat:files'][] = array (
                'id' => $iMediaId,
                'title' => $a['title'],
                'icon' => $a['file'],                
                'date' => defineTimeInterval($a['date']),
                'btn_download' => $oForm->genInputButton ($aInputBtnDownload),
            );            
        }

        if (!$aVars['bx_repeat:files'])
            return '';

        return $this->_oTemplate->parseHtmlByName('entry_view_block_files', $aVars);
    }

    function getBlockCode_Rate() {
        bx_groups_import('NewsVoting');
        $o = new BxGroupsNewsVoting ('bx_groups_news', (int)$this->aDataEntry['id']);
    	if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        bx_groups_import('NewsCmts');
        $o = new BxGroupsNewsCmts ('bx_groups_news', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
			$aNewsEntry = $this->_oDb->getNewsEntryById($this->aDataEntry['id']);
			$iEntryId = $aNewsEntry['group_id'];
	 
		    $aDataEntry = $this->_oDb->getEntryById($iEntryId);
   
            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'TitleEdit' => $this->_oMain->isAllowedSubEdit($aDataEntry, $aNewsEntry) ? _t('_bx_groups_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedSubDelete($aDataEntry, $aNewsEntry) ? _t('_bx_groups_action_title_delete') : ''

            );

            if (!$this->aInfo['TitleEdit'] && !$this->aInfo['TitleDelete'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'bx_groups_news');
        } 

        return '';
    }    
 

    function getCode() { 
        return parent::getCode();
    }    
}
