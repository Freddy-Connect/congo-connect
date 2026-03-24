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

bx_import('BxDolTwigSearchResult');

class BxAToolSearchResult extends BxDolTwigSearchResult {
  
	 var $aCurrent = array(
		'name' => 'modzzz_atool',
		'title' => '_modzzz_atool_page_title_browse',
		'table' => 'sys_objects_actions', 
		'ownFields' => array('ID', 'Caption', 'Icon', 'Url', 'Script', 'Eval', 'Order', 'Type', 'bDisplayInSubMenuHeader'), 
		'searchFields' => array(),
		'join' => array(),  
		'restriction' => array(), 
		'paginate' => array('perPage' => 14, 'page' => 1, 'totalNum' => 0, 'totalPages' => 1),
		'sorting' => 'last',
		'rss' => array(),
		'ident' => 'ID'
	 );

     function BxAToolSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '') {   

        $oMain = $this->getMain();

		unset($this->aCurrent['join']);

        switch ($sMode) {
 

             case 'sitemap_active':
 
				$this->_sMode = 'sitemap';

 				$this->aCurrent['table'] = 'sys_objects_site_maps';
				
				$this->aCurrent['ownFields'] = array('id','title', 'priority','changefreq','order','active'); 

				break;
 
            case 'sitemap_archive':
			 
				$this->_sMode = 'sitemap_archive';
 
				$this->aCurrent['table'] = 'modzzz_atool_sitemap_archive';  

				$this->aCurrent['ownFields'] = array('id','title', 'priority','changefreq','order','active','date'); 
 
				//$this->aCurrent['restriction']['type'] = array('value' => $sValue, 'field' => 'Type', 'operator' => '=' );  

				break;

             case 'stat_active':
 
				$this->_sMode = 'site_stat';

 				$this->aCurrent['table'] = 'sys_stat_site';
				
				$this->aCurrent['ownFields'] = array('ID','Name','Title','UserLink','UserQuery','AdminLink','AdminQuery','IconName','StatOrder'); 

				break;
 
            case 'stat_archive':
			 
				$this->_sMode = 'site_stat_archive';
 
				$this->aCurrent['table'] = 'modzzz_atool_site_stat_archive';  

				$this->aCurrent['ownFields'] = array('ID','Name','Title','UserLink','UserQuery','AdminLink','AdminQuery','IconName','StatOrder','Date'); 
 
				//$this->aCurrent['restriction']['type'] = array('value' => $sValue, 'field' => 'Type', 'operator' => '=' );  

				break;
 
            case 'status':

				$this->_sMode = 'status';

                if (false !== bx_get('param1') && bx_get('param1')){

					$iOwnerId = $oMain->_oDb->getProfileIdByNickName (process_db_input(bx_get('param1'), BX_TAGS_STRIP), false); 
					$iOwnerId = ($iOwnerId) ? $iOwnerId : -999; 
				 
                    $this->aCurrent['restriction']['profile_id'] = array('value' => $iOwnerId, 'field' => 'ID', 'operator' => '=');
				}

                if (false !== bx_get('param2') && bx_get('param2')){
 
					$sKeyword = process_db_input(bx_get('param2'), BX_TAGS_STRIP); 
 				 
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sKeyword, 'field' => 'UserStatusMessage', 'operator' => 'like');
				}
 
				$this->aCurrent['restriction']['status'] = array('value' => '', 'field' => 'UserStatusMessage', 'operator' => '!=');
 
				$this->aCurrent['table'] = 'Profiles';
 
				$this->aCurrent['ownFields'] = array('ID', 'UserStatusMessage', 'UserStatusMessageWhen');
   
 				$this->sBrowseUrl = "administration/status";
				$this->aCurrent['title'] = _t('_modzzz_atool_caption_browse_atool_status');
				break;
 
            case 'comment':

                if (false !== bx_get('param1') && bx_get('param1')){

					$iSenderId = $oMain->_oDb->getProfileIdByNickName (process_db_input(bx_get('param1'), BX_TAGS_STRIP), false); 
					$iSenderId = ($iSenderId) ? $iSenderId : -999; 
				 
                    $this->aCurrent['restriction']['profile_id'] = array('value' => $iSenderId, 'field' => 'cmt_author_id', 'operator' => '=');
				}

                if (false !== bx_get('param2') && bx_get('param2')){

					$sKeyword = process_db_input(bx_get('param2'), BX_TAGS_STRIP); 
 				 
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sKeyword, 'field' => 'cmt_text', 'operator' => 'like');
				}
 
				$this->_sMode = 'comment';
 
				$this->aCurrent['table'] = 'sys_cmts_profile';
 
				$this->aCurrent['ownFields'] = array('cmt_id', 'cmt_parent_id', 'cmt_object_id', 'cmt_author_id', 'cmt_text', 'cmt_time');
   
 				$this->sBrowseUrl = "administration/comment";
				$this->aCurrent['title'] = _t('_modzzz_atool_caption_browse_atool_comment');
				break;

            case 'message':

                if (false !== bx_get('param1') && bx_get('param1')){

					$iSenderId = $oMain->_oDb->getProfileIdByNickName (process_db_input(bx_get('param1'), BX_TAGS_STRIP), false); 
					$iSenderId = ($iSenderId) ? $iSenderId : -999; 
				 
                    $this->aCurrent['restriction']['profile_id'] = array('value' => $iSenderId, 'field' => 'Sender', 'operator' => '=');
				}

                if (false !== bx_get('param2') && bx_get('param2')){

					$sKeyword = process_db_input(bx_get('param2'), BX_TAGS_STRIP); 
 				 
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sKeyword, 'field' => 'Text', 'operator' => 'like');
				}

				$this->_sMode = 'message';
 
				$this->aCurrent['table'] = 'sys_messages';
 
				$this->aCurrent['ownFields'] = array('ID', 'Date', 'Sender', 'Recipient', 'Text', 'Subject', 'New', 'Type', 'Trash', 'TrashNotView');
 
 				$this->aCurrent['restriction']['type'] = array('value' => 'letter', 'field' => 'Type', 'operator' => '=' );  

 				$this->sBrowseUrl = "administration/message";
				$this->aCurrent['title'] = _t('_modzzz_atool_caption_browse_atool_message');
				break;

             case 'active':
				  
 				$this->_sMode = 'active';

				$this->aCurrent['restriction']['type'] = array('value' => $sValue, 'field' => 'Type', 'operator' => '=' );  
				break;

            case 'archive':
			 
				$this->_sMode = 'archive';
 
				$this->aCurrent['table'] = 'modzzz_atool_archive';  

				$this->aCurrent['ownFields'] = array('ID', 'Caption', 'Icon', 'Url', 'Script', 'Eval', 'Order', 'Type', 'bDisplayInSubMenuHeader', 'Date'); 
 
				$this->aCurrent['restriction']['type'] = array('value' => $sValue, 'field' => 'Type', 'operator' => '=' );  

				break;
 
	         default:
                $this->isError = true;
        }
 
        $this->aCurrent['paginate']['perPage'] = 200;
 
        $this->sFilterName = 'modzzz_atool_filter';

        parent::BxDolTwigSearchResult();
    }

    /*
     * Check restriction params and make condition part of query
     * return $sqlWhere sql code of query for WHERE part
     */

    function getRestriction ()
    {
        $sqlWhere = '';
        if (isset($this->aCurrent['restriction'])) {
            $aWhere[] = '1';
            foreach ($this->aCurrent['restriction'] as $sKey => $aValue) {
                $sqlCondition = '';
                //if (isset($aValue['operator']) && !empty($aValue['value'])) {
                if (isset($aValue['operator'])) {
                   $sFieldTable = isset($aValue['table']) ? $aValue['table'] : $this->aCurrent['table'];
                   $sqlCondition = "`{$sFieldTable}`.`{$aValue['field']}` ";
                   if (!isset($aValue['no_quote_value']))
                       $aValue['value'] = process_db_input($aValue['value'], BX_TAGS_STRIP);
                   switch ($aValue['operator']) {
                       case 'against':
                            $aCond = isset($aValue['field']) && strlen($aValue['field']) > 0 ? $aValue['field'] : $this->aCurrent['searchFields'];
                            $sqlCondition = !empty($aCond) ? $this->getSearchFieldsCond($aCond, $aValue['value']) : "";
                            break;
                       case 'like':
                            $sqlCondition .= "LIKE '%" . $aValue['value'] . "%'";
                            break;
                       case 'in':
                       case 'not in':
                            $sValuesString = $this->getMultiValues($aValue['value']);
                            $sqlCondition .= strtoupper($aValue['operator']) . '('.$sValuesString.')';
                            break;
                       default:
                               $sqlCondition .= $aValue['operator'] . (isset($aValue['no_quote_value']) && $aValue['no_quote_value'] ?  $aValue['value'] : "'" . $aValue['value'] . "'");
                       break;
                    }
                }
                if (strlen($sqlCondition) > 0)
                    $aWhere[] = $sqlCondition;
            }
            $sqlWhere .= "WHERE ". implode(' AND ', $aWhere);
        }
        return $sqlWhere;
    }
 
    function getAlterOrder() {
 
		$aSql = array();
		if($this->_sMode == 'message')
			$aSql['order'] = " ORDER BY `Date` DESC";
		elseif($this->_sMode == 'sitemap')
			$aSql['order'] = " ORDER BY `order` ASC";
		elseif($this->_sMode == 'site_stat')
			$aSql['order'] = " ORDER BY `StatOrder` ASC";
		elseif($this->_sMode == 'site_stat_archive')
			$aSql['order'] = " ORDER BY `Date` DESC";
		elseif($this->_sMode == 'status')
			$aSql['order'] = " ORDER BY `UserStatusMessageWhen` DESC"; 
		else
			$aSql['order'] = "";

		return $aSql;  
    }

    function displaySearchUnit ($aData)
    {
        $oMain = $this->getMain();
		if($this->_sMode == 'message') 
			return $oMain->_oTemplate->message_unit($aData, $this->sUnitTemplate);
		elseif($this->_sMode == 'status') 
			return $oMain->_oTemplate->status_unit($aData, $this->sUnitTemplate);
		elseif($this->_sMode == 'site_stat') 
			return $oMain->_oTemplate->stat_unit($aData, $this->sUnitTemplate);		
		elseif($this->_sMode == 'site_stat_archive') 
			return $oMain->_oTemplate->archive_stat_unit($aData, $this->sUnitTemplate);
 
		elseif($this->_sMode == 'sitemap') 
			return $oMain->_oTemplate->sitemap_unit($aData, $this->sUnitTemplate);		
		elseif($this->_sMode == 'sitemap_archive') 
			return $oMain->_oTemplate->archive_sitemap_unit($aData, $this->sUnitTemplate);

		elseif($this->_sMode == 'comment') 
			return $oMain->_oTemplate->comment_unit($aData, $this->sUnitTemplate);
		else
			return $oMain->_oTemplate->unit($aData, $this->sUnitTemplate);
    }
 
    function displayResultBlock () {
        global $oFunctions;
		$oMain = $this->getMain();
 
		if($this->_sMode == 'site_stat'){
			$s = $this->displayParentResultBlock ();
			if ($s) {
	 
				$sSortUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'sort/sitestat';

				$sJsContent = $GLOBALS['oBxAToolModule']->_oTemplate->parseHtmlByName('jscript.html', array('url'=>$sSortUrl));
	 
				$s = $sJsContent . $s; 
			}

		}elseif($this->_sMode == 'sitemap'){
			$s = $this->displayParentResultBlock ();
			if ($s) {
	 
				$sSortUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'sort/sitemap';

				$sJsContent = $GLOBALS['oBxAToolModule']->_oTemplate->parseHtmlByName('jscript.html', array('url'=>$sSortUrl));
	 
				$s = $sJsContent . $s; 
			} 

		}else{
			$s = parent::displayResultBlock ();
		}

        if ($s) {

            $GLOBALS['oSysTemplate']->addDynamicLocation($oMain->_oConfig->getHomePath(), $oMain->_oConfig->getHomeUrl());
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
        }
        return '';
    }

    function displayParentResultBlock ()
    { 
        $sCode = '';
        $aData = $this->getSearchData();
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $sCode .= $this->addCustomParts();
            foreach ($aData as $aValue) {
                $sCode .= $this->displaySearchUnit($aValue);
            }
			$sCode = '<ul id="sortid">'.$sCode.'</ul>';
 
            $sCode = '<div class="result_block">' . $sCode . '<div class="clear_both"></div></div>';
        }
        return $sCode;
    }

    function getMain() {
        return BxDolModule::getInstance('BxAToolModule');
    }
 
    
    function _getPseud () {
        return array();
    }
  
}
