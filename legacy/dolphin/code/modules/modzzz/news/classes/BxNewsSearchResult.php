<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx News
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

class BxNewsSearchResult extends BxDolTwigSearchResult {

    var $aCurrent = array(
        'name' => 'modzzz_news',
        'title' => '_modzzz_news_page_title_browse',
        'table' => 'modzzz_news_main',
		'distinct' => false,
        'ownFields' => array('id', 'title', 'uri', 'created', 'when', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'snippet', 'parent_category_id', 'category_id', 'tags', 'when', 'comments_count', 'video_embed'),
        'searchFields' => array('title', 'tags', 'desc'),
        'join' => array(
            'profile' => array(
                    'type' => 'left',
                    'table' => 'Profiles',
                    'mainField' => 'author_id',
                    'onField' => 'ID',
                    'joinFields' => array('NickName'),
            ),
        ),
        'restriction' => array(
            'activeStatus' => array('value' => 'approved', 'field'=>'status', 'operator'=>'='),
            'owner' => array('value' => '', 'field' => 'author_id', 'operator' => '='),
            'tag' => array('value' => '', 'field' => 'tags', 'operator' => 'against'),
            'public' => array('value' => '', 'field' => 'allow_view_news_to', 'operator' => '='),
            'country' => array('value' => '', 'field' => 'country', 'operator' => '='),
        ),
        'paginate' => array('perPage' => 14, 'page' => 1, 'totalNum' => 0, 'totalPages' => 1),
        'sorting' => 'last',
        'rss' => array( 
            'title' => '',
            'link' => '',
            'image' => '',
            'profile' => 0,
            'fields' => array (
                'Link' => '',
                'Title' => 'title',
                'DateTimeUTS' => 'when',
                'Desc' => 'desc',
                'Photo' => '',
            ),
        ),
        'ident' => 'id'
    );
    
 	/*
	 * Check restriction params and make condition part of query
	 * return $sqlWhere sql code of query for WHERE part  
	 */
	
	function getRestrictionOLD () {
 
	    if (!isset($this->aCurrent['restriction']['related_tags'])) {       
			 return parent::getRestriction ();
 		}

		$sqlWhere = ''; 
		$aWhere = array();
		foreach ($this->aCurrent['restriction']['related_tags'] as $sKey => $aValue) {
			$sqlCondition = '';
			$sqlCondition2 = '';
		
		   $sFieldTable = isset($aValue['table']) ? $aValue['table'] : $this->aCurrent['table'];
		   $sqlCondition = "`{$sFieldTable}`.`{$aValue['field']}` ";
		   if (!isset($aValue['no_quote_value']))
			   $aValue['value'] = process_db_input($aValue['value'], BX_TAGS_STRIP);
		   switch ($aValue['operator']) { 
			   case 'like':
					$sqlCondition .= "LIKE '%" . $aValue['value'] . "%'";
					break; 
			   case '!=':
					$sqlCondition2 .= $sqlCondition ."!=" . $aValue['value'] . "";
				    $sqlCondition ='';
					break;  
		   }
		  
		   if (strlen($sqlCondition) > 0)
				$aWhere[] = $sqlCondition;
		}

		if(count($aWhere)==1)
			$sqlWhere .= "WHERE ". $aWhere[0] ." AND " . $sqlCondition2;
		else
			$sqlWhere .= "WHERE (". implode(' OR ', $aWhere) .") AND " . $sqlCondition2;
        
        return $sqlWhere;
	}   
 
    function BxNewsSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '') {   

		$oMain = $this->getMain();

		$aLangs = $oMain->_oDb->getLanguages();
 
		if(count($aLangs)>1 && !$oMain->isAdmin()){
  
			$sLang = getCurrentLangName();
			$iMemberId = getLoggedId();
	  
			$iDefaultLangId = $GLOBALS['MySQL']->getOne("SELECT `ID` FROM `sys_localization_languages` WHERE `Name`='" . $sLang . "' LIMIT 1");

			if($iMemberId){ 
				$aProfile = getProfileInfo($iMemberId);
				$iDefaultLangId = $aProfile['LangID'] ? $aProfile['LangID'] : $iDefaultLangId;
			}

			$this->aCurrent['restriction']['language'] = array('value' => array(0,$iDefaultLangId), 'field' => 'language', 'operator' => 'in');
		}

		//[begin] band integration modzzz
		if( getParam('modzzz_bands_news_hide')=='on' && !in_array($sMode, array('admin','pending','my_pending','user')) ){ 
			$this->aCurrent['restriction']['band_news_hide'] = array('value' => 1, 'field' => 'band_id', 'operator' => '<');
		}
		if(getParam('modzzz_bands_modzzz_news')=='on'){
			$this->aCurrent['ownFields'][] = 'band_id';
		} 
		//[end] band integration modzzz

		//[begin] listing integration modzzz
		if( getParam('modzzz_listing_news_hide')=='on' && !in_array($sMode, array('admin','pending','my_pending','user')) ){ 
			$this->aCurrent['restriction']['listing_news_hide'] = array('value' => 1, 'field' => 'business_id', 'operator' => '<');
		}
		if(getParam('modzzz_listing_modzzz_news')=='on'){
			$this->aCurrent['ownFields'][] = 'business_id';
		} 
		//[end] listing integration modzzz

		//[begin] event integration modzzz 
		if(getParam('bx_events_modzzz_news')=='on'){
			$this->aCurrent['ownFields'][] = 'event_id';
		}
		//[end] event integration modzzz

		//[begin] groups integration modzzz 
		if(getParam('bx_groups_modzzz_news')=='on'){
			$this->aCurrent['ownFields'][] = 'group_id';
		}
		//[end] groups integration modzzz
  
		//[begin] school integration modzzz 
		if(getParam('modzzz_schools_modzzz_news')=='on'){
			$this->aCurrent['ownFields'][] = 'school_id';
		}
		//[end] school integration modzzz
  
        switch ($sMode) {

           case 'categories':
                $oMain = $this->getMain();
				$aCategory = $oMain->_oDb->getCategoryByUri($sValue); 
                $iCategoryId = $aCategory['id'];
                $sCategoryName = $aCategory['name']; 
                
				$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
   				$this->aCurrent['restriction']['cat'] = array( 'value' => $iCategoryId,'field' => 'parent_category_id','operator' => '=');

                $this->sBrowseUrl = "browse/categories/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_browse_by_category').' - '.$sCategoryName;
 
				//unset($this->aCurrent['rss']);
            break;
            case 'subcategories': 
                $oMain = $this->getMain(); 
				$aCategory = $oMain->_oDb->getCategoryByUri($sValue); 
                $iCategoryId = $aCategory['id'];
                $sCategoryName = $aCategory['name'];

				$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
				$this->aCurrent['restriction']['subcat'] = array( 'value' => $iCategoryId, 'field' => 'category_id', 'operator' => '=');
 
                $this->sBrowseUrl = "browse/subcategories/$sValue" ;
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_browse_by_category').' - '.$sCategoryName;
          
				//unset($this->aCurrent['rss']);
            break;
  

            case 'country':
                $this->aCurrent['restriction'][$sMode]['value'] = $sValue;
                $this->sBrowseUrl = "browse/$sMode/$sValue";

				$sCountryName = $oMain->_oTemplate->parsePreValues('Country', strtoupper($sValue)); 
                $this->aCurrent['title'] = _t('_modzzz_news_caption_browse_by_'.$sMode) . ' - ' . $sCountryName;
                break;

			case 'related': 
				$aTags = explode (',', $sValue);
				foreach($aTags as $iEachKey=>$sEachValue){
					$this->aCurrent['restriction']['related_tags'][] = array('value' => $sEachValue, 'field' => 'tags', 'operator' => 'like');
				}
 
				$this->aCurrent['restriction']['related_tags']['id'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->aCurrent['restriction']['related_tags']['status'] = array('value' => 'approved', 'field' => 'status', 'operator' => '=');  
 
				$this->sBrowseUrl = "browse/related/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_news_caption_browse_related');
				break;  


			case 'relatedOLD':
			 
				$aTags = explode (',', $sValue);
				foreach($aTags as $iEachKey=>$sEachValue){
					$this->aCurrent['restriction']['related_tags'][] = array('value' => $sEachValue, 'field' => 'tags', 'operator' => 'like');
				}
  
				$this->aCurrent['restriction']['related_tags'][] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/related/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_news_caption_browse_related');
				break; 
				
            case 'pending':
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "administration/pending";
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_pending_approval');
                unset($this->aCurrent['rss']);  
            break; 
            case 'draft':
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'draft';
                $this->sBrowseUrl = "administration/draft";
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_pending_publish');
                unset($this->aCurrent['rss']);
            break; 

            case 'my_pending':
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_pending_approval');
                unset($this->aCurrent['rss']);
                unset($this->aCurrent['restriction']['language']);
            break;

            case 'my_draft':
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'draft';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_pending_publish');
                unset($this->aCurrent['rss']);
                unset($this->aCurrent['restriction']['language']);
            break;

			case 'quick': 
				if ($sValue)
					$this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');  

				$this->sBrowseUrl = "browse/quick/" . $sValue; 
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_browse'); 
				break;
            case 'search':
                if ($sValue)
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');

                if ($sValue2 && $sValue2!='-') 
				   $this->aCurrent['restriction']['parent'] = array('value' => $sValue2,'field' => 'parent_category_id','operator' => '=');
                 
				if ($sValue3 && $sValue3!='-') 
				   $this->aCurrent['restriction']['category'] = array('value' => $sValue3,'field' => 'category_id','operator' => '=');

                $this->sBrowseUrl = 'search/'.($sValue ? $sValue : '-').'/'.($sValue2 ? $sValue2 : '-').'/'.($sValue3 ? $sValue3 : '-');
 
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_search_results') . ' ' .  $sValue;
                unset($this->aCurrent['rss']);
                break;;
            
			case 'owner':
            case 'user':
                $iProfileId = $GLOBALS['oBxNewsModule']->_oDb->getProfileIdByNickName ($sValue);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                $this->sBrowseUrl = "browse/user/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_modzzz_news_page_title_browse_by_author');
                if (isset($_REQUEST['rss']) && $_REQUEST['rss']) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['Avatar']) {
                        $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
                        $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    } 
                }
                unset($this->aCurrent['restriction']['language']);

                break;

            case 'admin':

                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');

                $this->aCurrent['restriction']['owner']['value'] = 0;
                $this->sBrowseUrl = "administration/admin_entries";
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_admin_news');
                unset($this->aCurrent['restriction']['language']);
                break;

            case 'rel_category':
        
				//$this->aCurrent['distinct'] = true;

				$iEntryId = (int)$sValue;
				$iCategoryId = (int)$sValue2;
    
                $this->aCurrent['restriction']['entry'] = array('table' => 'modzzz_news_main', 'value' => $iEntryId, 'field' => 'id', 'operator' => '!='); 
                
				$this->aCurrent['restriction']['category'] = array('table' => 'modzzz_news_main', 'value' => $iCategoryId, 'field' => 'category_id', 'operator' => '=' );  
                break;
 
            case 'tag':
            	$sValue = uri2title($sValue);

                $this->aCurrent['restriction']['tag']['value'] = $sValue;
                $this->sBrowseUrl = "browse/tag/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_browse_by_tag') . ' ' . $sValue;
                break;

            case 'recent':
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_browse_recent');
                break;

            case 'top':
                $this->sBrowseUrl = 'browse/top';
                $this->aCurrent['sorting'] = 'top';
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_browse_top_rated');
                break;

            case 'popular':
                $this->sBrowseUrl = 'browse/popular';
                $this->aCurrent['sorting'] = 'popular';
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_browse_popular');
                break;                

            case 'featured':
				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured';
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_browse_featured');
                break;                                

            case 'calendar':
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 00:00:00')", 'field' => 'when', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 23:59:59')", 'field' => 'when', 'operator' => '<=', 'no_quote_value' => true);
				$this->sBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
				$this->sEventsBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_browse_by_day') . sprintf("%04u-%02u-%02u", $sValue, $sValue2, $sValue3);
                break; 
		
            case 'archive':
 
				$iYear = $sValue;
				$iMonth = $sValue2;

				if($iMonth){
					$iDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $iMonth, $iYear) ; 

					$this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$iYear}-{$iMonth}-1 00:00:00')", 'field' => 'when', 'operator' => '>=', 'no_quote_value' => true);

					$this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$iYear}-{$iMonth}-{$iDaysInMonth} 23:59:59')", 'field' => 'when', 'operator' => '<=', 'no_quote_value' => true);
				}else{
			 
					$this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$iYear}-1-1 00:00:00')", 'field' => 'when', 'operator' => '>=', 'no_quote_value' => true);

					$this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$iYear}-12-31 23:59:59')", 'field' => 'when', 'operator' => '<=', 'no_quote_value' => true); 
				}  
				
			    $oMain = $this->getMain(); 
  
				$sPeriod = ($iMonth) ? $oMain->_oDb->getMonth($iMonth-1) .' '. $iYear : $iYear;

                $this->sBrowseUrl = "browse/archive/$sValue/$sValue2";
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_browse_by_period') . $sPeriod;
 
                break;  
				
            case 'letter':
                $this->sBrowseUrl = 'browse/letter/'.$sValue;
                $this->aCurrent['title'] = _t('_modzzz_news_page_title_browse_letter', ucwords($sValue));
			 	$this->aCurrent['restriction']['letter'] = array('value' => strtolower($sValue), 'field' => 'letter', 'operator' => '=');  
                break;
 
            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_modzzz_news');
                break;

            default:
                $this->isError = true;
        }

 
        $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_news_perpage_browse');

        if (isset($this->aCurrent['rss']))
            $this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . $this->sBrowseUrl;

        if (isset($_REQUEST['rss']) && $_REQUEST['rss']) {
            $this->aCurrent['ownFields'][] = 'desc';
            $this->aCurrent['ownFields'][] = 'when';
            $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_news_max_rss_num');
        }

        bx_import('Voting', $oMain->_aModule);
        $oVotingView = new BxNewsVoting ('modzzz_news', 0);
        $this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

        $this->sFilterName = 'filter';

        parent::BxDolTwigSearchResult();
    }

    function getAlterOrder() {
		if ($this->aCurrent['sorting'] == 'last') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_news_main`.`when` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'top') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_news_main`.`rate` DESC, `modzzz_news_main`.`rate_count` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'popular') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_news_main`.`views` DESC";
			return $aSql; 
		} elseif ($this->aCurrent['sorting'] == 'category') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_news_main`.`letter` ASC";
			return $aSql; 
		}
	    return array();
    }

    function displayResultBlock () {
        global $oFunctions;
        $s = parent::displayResultBlock ();
        if ($s) {
            $oMain = $this->getMain();
            $GLOBALS['oSysTemplate']->addDynamicLocation($oMain->_oConfig->getHomePath(), $oMain->_oConfig->getHomeUrl());
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
        }
        return '';
    }

    function getMain() {
        return BxDolModule::getInstance('BxNewsModule');
    }

    function getRssUnitLink (&$a) {
        $oMain = $this->getMain();
        return BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'view/' . $a['uri'];
    }
    
    function _getPseud () {
        return array(    
            'id' => 'id',
            'title' => 'title',
            'uri' => 'uri',
            'created' => 'created',
            'author_id' => 'author_id',
            'NickName' => 'NickName',
            'thumb' => 'thumb', 
            'when' => 'when',
        );
    }
  


	/*
	 * Check restriction params and make condition part of query
	 * return $sqlWhere sql code of query for WHERE part  
	 */
	
	function getCustomRestriction () {
		$sqlWhere = '';
	    if (isset($this->aCurrent['restriction'])) {       
            $aWhere[] = '1';
            foreach ($this->aCurrent['restriction'] as $sKey => $aValue) {
                $sqlCondition = '';
                if (isset($aValue['operator']) && !empty($aValue['value'])) {
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
                       case 'like_right':
                            $sqlCondition .= "LIKE '" . $aValue['value'] . "%'";
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


 	/*
	 * Check restriction params and make condition part of query
	 * return $sqlWhere sql code of query for WHERE part  
	 */	
	function getRestriction () {
 
	    if (!isset($this->aCurrent['restriction']['related_tags'])) {       
			 return $this->getCustomRestriction ();
 		}

		$sqlWhere = ''; 
		$sqlCondition = '';
		$sqlCondition2 = '';
		$sqlCondition3 = '';
		$aWhere = array();
		foreach ($this->aCurrent['restriction']['related_tags'] as $sKey => $aValue) {
 
		   $sFieldTable = isset($aValue['table']) ? $aValue['table'] : $this->aCurrent['table'];
		   $sqlCondition = "`{$sFieldTable}`.`{$aValue['field']}` ";
		   if (!isset($aValue['no_quote_value']))
			   $aValue['value'] = process_db_input($aValue['value'], BX_TAGS_STRIP);
		   switch ($aValue['operator']) { 
			   case 'like':
					$sqlCondition .= "LIKE '%" . $aValue['value'] . "%'";
					break; 
			   case '!=':
					$sqlCondition2 = $sqlCondition ."!=" . $aValue['value'] . "";
				    $sqlCondition =''; 
					break;
			   case '=':
					$sqlCondition3 = $sqlCondition ."='" . $aValue['value'] . "'"; 
					$sqlCondition ='';
			   break;
 		   }
		  
		   if (strlen($sqlCondition) > 0)
				$aWhere[] = $sqlCondition;
		}

		$sqlCondition4 = $this->getStatusRestriction ();

		if(count($aWhere)==1)
			$sqlWhere .= "WHERE ". $aWhere[0] ." AND " . $sqlCondition2 . " AND " . $sqlCondition3 . $sqlCondition4;
		else
			$sqlWhere .= "WHERE (". implode(' OR ', $aWhere) .") AND " . $sqlCondition2 . " AND " . $sqlCondition3 . $sqlCondition4;
 
        return $sqlWhere;
	} 


	/*
	 * Check restriction params and make condition part of query
	 * return $sqlWhere sql code of query for WHERE part  
	 */	
	function getStatusRestriction () {
		$sqlWhere = '';
	    if (isset($this->aCurrent['restriction'])) {       
            $aWhere[] = '';
            foreach ($this->aCurrent['restriction'] as $sKey => $aValue) {

				if($sKey != 'public')
					continue;

                $sqlCondition = '';
                if (isset($aValue['operator']) && !empty($aValue['value'])) {
                   $sFieldTable = isset($aValue['table']) ? $aValue['table'] : $this->aCurrent['table'];
				   $sqlCondition = "`{$sFieldTable}`.`{$aValue['field']}` ";
                   if (!isset($aValue['no_quote_value']))
					   $aValue['value'] = process_db_input($aValue['value'], BX_TAGS_STRIP);
                   switch ($aValue['operator']) {
                       case 'in':
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
            $sqlWhere .= implode(' AND ', $aWhere);
        }
        return $sqlWhere;
	}


    function getSearchDataByParams ($aParams = '')
    {
		if(!$this->aCurrent['distinct']) return parent::getSearchDataByParams($aParams);
 
        $aSql = array('ownFields'=>'', 'joinFields'=>'', 'order'=>'');

        // searchFields
        foreach ($this->aCurrent['ownFields'] as $sValue) {
            $aSql['ownFields'] .= $this->setFieldUnit($sValue, $this->aCurrent['table']);
        }
        // joinFields & join
        $aJoins = $this->getJoins();
        if (!empty($aJoins)) {
            $aSql['ownFields'] .= $aJoins['ownFields'];
            $aSql['joinFields'] .= $aJoins['joinFields'];
            $aSql['join'] = $aJoins['join'];
            $aSql['groupBy'] = $aJoins['groupBy'];
        } else
            $aSql['ownFields'] = trim($aSql['ownFields'], ', ');
        // from
        $aSql['from'] = " FROM `{$this->aCurrent['table']}`";

        // where
        $aSql['where'] = $this->getRestriction();

        // limit
        $aSql['limit'] = $this->getLimit();

        // sorting
        $this->setSorting();

        $aSort = $this->getSorting($this->aCurrent['sorting']);
        foreach ($aSort as $sKey => $sValue)
            $aSql[$sKey] .= $sValue;

        // rate part
        $aRate = $this->getRatePart();
        if (is_array($aRate)) {
            foreach ($aRate as $sKey => $sValue)
               $aSql[$sKey] .= $sValue;
        }

		//$sDistinct = ($this->aCurrent['distinct']) ? 'DISTINCT' : '';
        // execution
        $sqlQuery = "SELECT DISTINCT {$aSql['ownFields']} {$aSql['joinFields']} {$aSql['from']} {$aSql['join']} {$aSql['where']} {$aSql['groupBy']} {$aSql['order']} {$aSql['limit']}";
        //echoDbg($sqlQuery);
        $aRes = db_res_assoc_arr($sqlQuery);
        return $aRes;
    }

    function getCount ()
    {
		if(!$this->aCurrent['distinct']) return parent::getCount();

		//$sDistinct = ($this->aCurrent['distinct']) ? 'DISTINCT' : '';

        $aJoins = $this->getJoins(false);
        $sqlQuery =  "SELECT DISTINCT COUNT(*) FROM `{$this->aCurrent['table']}` {$aJoins['join']} " . $this->getRestriction() . " {$aJoins['groupBy']}";
        return (int)db_value($sqlQuery);
    }

    function showPagination($sUrlAdmin = false)
    {
        $oMain = $this->getMain();
        $oConfig = $oMain->_oConfig;
        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ($sUrlAdmin ? $sUrlAdmin : $this->sBrowseUrl);
 
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');
		
		//[begin] added keyword modification (modzzz)
		$sKeyWord = bx_get('keyword');
		if ($sKeyWord !== false)
			$sLink = 'keyword=' . clear_xss($sKeyWord) . '&';
   
		$sUrlStart .= $sLink;
		//[end] added keyword modification (modzzz)

        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sUrlStart . 'page={page}&per_page={per_page}' . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) : ''),
            'count' => $this->aCurrent['paginate']['totalNum'],
            'per_page' => $this->aCurrent['paginate']['perPage'],
            'page' => $this->aCurrent['paginate']['page'],
            'per_page_changer' => true,
            'page_reloader' => true,
            'on_change_page' => '',
            'on_change_per_page' => "document.location='" . $sUrlStart . "page=1&per_page=' + this.value + '" . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) ."';": "';"),
        ));

        return '<div class="clear_both"></div>'.$oPaginate->getPaginate();
    }

    function setPublicUnitsOnly($isPublic) {
		if($isPublic){
			 $iLogged = getLoggedId();
			 if($iLogged){
				$this->aCurrent['restriction']['public']['operator'] = 'in';
				$this->aCurrent['restriction']['public']['value'] = array(BX_DOL_PG_ALL,BX_DOL_PG_MEMBERS);
			 }else{
				$this->aCurrent['restriction']['public']['value'] = BX_DOL_PG_ALL;
			 }
		}else{
			 $this->aCurrent['restriction']['public']['value'] = false;
		}
    }

}
