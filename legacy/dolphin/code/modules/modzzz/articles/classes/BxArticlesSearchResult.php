<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Article
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
* ***********************************************************************************
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

class BxArticlesSearchResult extends BxDolTwigSearchResult {

    var $aCurrent = array(
        'name' => 'modzzz_articles',
        'title' => '_modzzz_articles_page_title_browse',
        'table' => 'modzzz_articles_main',
        'ownFields' => array('id', 'title', 'uri', 'created', 'when', 'author_id', 'thumb', 'rate', 'country', 'city', 'anonymous', 'snippet', 'desc', 'parent_category_id', 'category_id', 'tags', 'video_embed', 'comments_count'),
        'searchFields' => array('title', 'desc', 'tags' ),
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
            'public' => array('value' => '', 'field' => 'allow_view_article_to', 'operator' => '='),
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
 
    function BxArticlesSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '') {   
		
		$oMain = $this->getMain(); 

        switch ($sMode) {

            case 'country':
                $this->aCurrent['restriction'][$sMode]['value'] = $sValue;
                $this->sBrowseUrl = "browse/$sMode/$sValue";

				$sCountryName = $oMain->_oTemplate->parsePreValues('Country', strtoupper($sValue)); 
                $this->aCurrent['title'] = _t('_modzzz_articles_caption_browse_by_'.$sMode) . ' - ' . $sCountryName;
                break;

			case 'related': 
				$aTags = explode (',', $sValue);
				foreach($aTags as $iEachKey=>$sEachValue){
					$this->aCurrent['restriction']['related_tags'][] = array('value' => $sEachValue, 'field' => 'tags', 'operator' => 'like');
				}
 
				$this->aCurrent['restriction']['related_tags']['id'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->aCurrent['restriction']['related_tags']['status'] = array('value' => 'approved', 'field' => 'status', 'operator' => '=');  
 
				$this->sBrowseUrl = "browse/related/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_articles_caption_browse_related');
				break;  
            case 'pending':
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "administration/pending_approval";
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break; 
            case 'admin':
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');

                $this->aCurrent['restriction']['owner']['value'] = 0;
                $this->sBrowseUrl = "administration/admin_entries";
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_admin_articles');
                break;
            case 'draft':
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'draft';
                $this->sBrowseUrl = "administration/pending_publish";
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_pending_publish');
                unset($this->aCurrent['rss']);
            break; 
            case 'my_draft':
                $oMain = $this->getMain();
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'draft';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_pending_publish');
                unset($this->aCurrent['rss']);
            break;
            case 'my_pending':
                $oMain = $this->getMain();
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break;
			case 'quick': 
				if ($sValue)
					$this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against'); 

                $this->sBrowseUrl = "browse/quick/" . $sValue;
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse') .' - '. $sValue; 
				break;
            case 'search':
                if ($sValue)
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');

                if ($sValue2 && $sValue2!='-') 
				   $this->aCurrent['restriction']['parent'] = array('value' => $sValue2,'field' => 'parent_category_id','operator' => '=');
                 
				if ($sValue3 && $sValue3!='-') 
				   $this->aCurrent['restriction']['category'] = array('value' => $sValue3,'field' => 'category_id','operator' => '=');

                $this->sBrowseUrl = 'search/'.($sValue ? $sValue : '-').'/'.($sValue2 ? $sValue2 : '-').'/'.($sValue3 ? $sValue3 : '-');
 
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_search_results') . ' ' .  $sValue;
                unset($this->aCurrent['rss']);
                break;

            case 'owner':
            case 'user':
                $iProfileId = $GLOBALS['oBxArticlesModule']->_oDb->getProfileIdByNickName ($sValue);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                $this->sBrowseUrl = "browse/$sMode/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_modzzz_articles_page_title_browse_by_author');
                if (isset($_REQUEST['rss']) && $_REQUEST['rss']) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['Avatar']) {
                        $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
                        $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    } 
                }
				
				if($iProfileId != (int)$_COOKIE['memberID'])
					$this->aCurrent['restriction']['anonymous'] = array('value' => 1, 'field' => 'anonymous', 'operator' => '!=');
                break;
  
           case 'categories':
                 
				$aCategory = $oMain->_oDb->getCategoryByUri($sValue); 
                $iCategoryId = $aCategory['id'];
                $sCategoryName = $aCategory['name']; 
                
				$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
   				$this->aCurrent['restriction']['cat'] = array( 'value' => $iCategoryId,'field' => 'parent_category_id','operator' => '=');

                $this->sBrowseUrl = "browse/categories/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_by_category').' - '.$sCategoryName;
 
				//unset($this->aCurrent['rss']);
            break;
            case 'subcategories': 
                 
				$aCategory = $oMain->_oDb->getCategoryByUri($sValue); 
                $iCategoryId = $aCategory['id'];
                $sCategoryName = $aCategory['name'];

				$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
				$this->aCurrent['restriction']['subcat'] = array( 'value' => $iCategoryId, 'field' => 'category_id', 'operator' => '=');
 
                $this->sBrowseUrl = "browse/subcategories/$sValue" ;
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_by_category').' - '.$sCategoryName;
          
				//unset($this->aCurrent['rss']);
            break;
  
            case 'tag':
            	$sValue = uri2title($sValue);

                $this->aCurrent['restriction']['tag']['value'] = $sValue;
                $this->sBrowseUrl = "browse/tag/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_by_tag') . ' ' . $sValue;
                break;
 
            case 'recent_main':
                $this->aCurrent['restriction']['public']['value'] = array(BX_DOL_PG_ALL,BX_DOL_PG_MEMBERS);
                $this->aCurrent['restriction']['public']['operator'] = 'in';
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_recent');
                break;

            case 'top_main':
                $this->aCurrent['restriction']['public']['value'] = array(BX_DOL_PG_ALL,BX_DOL_PG_MEMBERS);
                $this->aCurrent['restriction']['public']['operator'] = 'in';
                
				$this->sBrowseUrl = 'browse/top';
                $this->aCurrent['sorting'] = 'top';
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_top_rated');
                break;

            case 'popular_main':
                $this->aCurrent['restriction']['public']['value'] = array(BX_DOL_PG_ALL,BX_DOL_PG_MEMBERS);
                $this->aCurrent['restriction']['public']['operator'] = 'in';
                
				$this->sBrowseUrl = 'browse/popular';
                $this->aCurrent['sorting'] = 'popular';
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_popular');
                break;   
            case 'featured_main':
                $this->aCurrent['restriction']['public']['value'] = array(BX_DOL_PG_ALL,BX_DOL_PG_MEMBERS);
                $this->aCurrent['restriction']['public']['operator'] = 'in';
				
				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured';
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_featured');
                break;  
            case 'recent':
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_recent');
                break;

            case 'top':
                $this->sBrowseUrl = 'browse/top';
                $this->aCurrent['sorting'] = 'top';
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_top_rated');
                break;

            case 'popular':
  
				switch($sValue){
					case 'all':
						$this->aCurrent['extra_where'] = '';
					break;
					case 'today':
						$this->aCurrent['extra_where'] = ' AND MONTH(FROM_UNIXTIME(modzzz_articles_views_track.`ts`)) = MONTH(NOW()) AND DAYOFMONTH(FROM_UNIXTIME(modzzz_articles_views_track.`ts`)) = DAYOFMONTH(NOW())';
					break;
					case 'week':
						$this->aCurrent['extra_where'] = ' AND MONTH(FROM_UNIXTIME(modzzz_articles_views_track.`ts`)) = MONTH(NOW()) AND WEEK(FROM_UNIXTIME(modzzz_articles_views_track.`ts`)) = WEEK(NOW())';
					break;
					case 'month':
						$this->aCurrent['extra_where'] = ' AND MONTH(FROM_UNIXTIME(modzzz_articles_views_track.`ts`)) = MONTH(NOW())';
					break; 
				}
 
				$this->aCurrent['browsemode']='popular';

				//$this->aCurrent['join']['profile']['type'] = 'inner';

				$this->aCurrent['join']['views'] = array(
					'type' => 'inner',
					'table' => 'modzzz_articles_views_track',
					'mainField' => 'id',
					'onField' => 'id',
					'joinFields' => '',
					'groupTable' => 'modzzz_articles_views_track',
					'groupField' => 'id',
				);
   
				$this->aCurrent['extra_field']='COUNT(`modzzz_articles_views_track`.`id`),';
				
				if($sValue)
					$this->sBrowseUrl = 'browse/popular/'.$sValue;
				else
					$this->sBrowseUrl = 'browse/popular';

                $this->aCurrent['sorting'] = 'popular';
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_popular');
                break;                

            case 'favorite':
 
				$this->aCurrent['join']['favorites'] = array(
					'type' => 'left',
					'table' => 'modzzz_articles_favorites',
					'mainField' => 'id',
					'onField' => 'id_entry',
					'joinFields' => '',
				);	        
 				$iProfileId = $oMain->_iProfileId;
				$iProfileId = ($iProfileId) ? $iProfileId : -999;
				$this->aCurrent['restriction']['favorite'] = array('value' => $iProfileId, 'field' => 'id_profile', 'operator' => '=', 'table' => 'modzzz_articles_favorites');
                $this->sBrowseUrl = 'browse/favorite';
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_favorite');
                break;
 
            case 'featured':
 
				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured';
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_featured');
                break;                                

            case 'calendar':
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 00:00:00')", 'field' => 'when', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 23:59:59')", 'field' => 'when', 'operator' => '<=', 'no_quote_value' => true);
				$this->sBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
				$this->sEventsBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_by_day') . sprintf("%04u-%02u-%02u", $sValue, $sValue2, $sValue3);
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
				 
				$sPeriod = ($iMonth) ? $oMain->_oDb->getMonth($iMonth-1) .' '. $iYear : $iYear;

                $this->sBrowseUrl = "browse/archive/$sValue/$sValue2";
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_by_period') . $sPeriod;
 
                break;   

            case 'letter':
                $this->sBrowseUrl = 'browse/letter/'.$sValue;
                $this->aCurrent['title'] = _t('_modzzz_articles_page_title_browse_letter', ucwords($sValue));
			 	$this->aCurrent['restriction']['letter'] = array('value' => strtolower($sValue), 'field' => 'letter', 'operator' => '=');  
                break;
 
            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_modzzz_articles');
                unset($this->aCurrent['rss']); 
                break;

            default:
                $this->isError = true;
        }

        $sKeyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;

        $oMain = $this->getMain();

        $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_articles_perpage_browse');

        if (isset($this->aCurrent['rss']))
            $this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . $this->sBrowseUrl;

        if (isset($_REQUEST['rss']) && $_REQUEST['rss']) {
            $this->aCurrent['ownFields'][] = 'desc';
            $this->aCurrent['ownFields'][] = 'when';
            $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_articles_max_rss_num');
        }
  
        bx_import('Voting', $oMain->_aModule);
        $oVotingView = new BxArticlesVoting ('modzzz_articles', 0);
        $this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

        $this->sFilterName = 'filter';

        parent::BxDolTwigSearchResult();
    }

    function getAlterOrder() {
		if ($this->aCurrent['sorting'] == 'last') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_articles_main`.`when` DESC";
			return $aSql;
		/*[begin] added patch 2.1.2*/
		} elseif ($this->aCurrent['sorting'] == 'category') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_articles_main`.`letter` ASC";
			return $aSql;
		/*[end] added patch 2.1.2*/
		} elseif ($this->aCurrent['sorting'] == 'top') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_articles_main`.`rate` DESC, `modzzz_articles_main`.`rate_count` DESC";
			return $aSql; 
		} elseif ($this->aCurrent['sorting'] == 'popular') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_articles_main`.`views` DESC";
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
        return BxDolModule::getInstance('BxArticlesModule');
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
  
    /*
     * Concat sql parts of query, run it and return result array
     * @param $aParams addon param
     * return $aData multivariate array
     */

    function getSearchDataByParams ($aParams = '')
    {
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

        // execution
		if($this->aCurrent['browsemode']=='popular'){
			$sqlQuery = "SELECT {$aSql['ownFields']} {$this->aCurrent['extra_field']} {$aSql['joinFields']} {$aSql['from']} {$aSql['join']} {$aSql['where']} {$this->aCurrent['extra_where']} {$aSql['groupBy']} {$aSql['order']} {$aSql['limit']}";
	 
		}else{
			$sqlQuery = "SELECT {$aSql['ownFields']} {$aSql['joinFields']} {$aSql['from']} {$aSql['join']} {$aSql['where']} {$aSql['groupBy']} {$aSql['order']} {$aSql['limit']}";
		}
        //echoDbg($sqlQuery);
        $aRes = db_res_assoc_arr($sqlQuery);
        return $aRes;
    }
 
    function getCount ()
    {
        $aJoins = $this->getJoins(false);

		if($this->aCurrent['browsemode']=='popular'){
			$sqlQuery = "SELECT COUNT(DISTINCT `modzzz_articles_views_track`.`id`) FROM `{$this->aCurrent['table']}` {$aJoins['join']} " . $this->getRestriction() . " {$this->aCurrent['extra_where']}"; 
 		}else{
			$sqlQuery =  "SELECT COUNT(*) FROM `{$this->aCurrent['table']}` {$aJoins['join']} " . $this->getRestriction() . " {$aJoins['groupBy']}";
		}

        return (int)db_value($sqlQuery);
    }



    /*
     * Get html box of search results (usually used in grlobal search)
     * @return html code
     */

    function menu_processing ($aMenu)
    {
        $sCode = $this->displayResultBlock();
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $sPaginate = $this->showPagination();
            $sCode = $this->displayMenuSearchBox($sCode, $sPaginate, $aMenu);
        } else{
            $sCode = $this->displayMenuSearchBox(MsgBox(_t('_Empty')), $sPaginate, $aMenu, false);
		}
        return $sCode;
    }

    function displayMenuSearchBox ($sCode, $sPaginate = '', $aMenu, $bShowRss = true)
    {            
		bx_import('BxDolPageView');

        if ($bShowRss && isset($this->aCurrent['rss']) && $this->aCurrent['rss']['link']) { 
			$aMenu[_t('RSS')] = array(
				'href' => $this->aCurrent['rss']['link'] . (false === strpos($this->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 
				'icon' => 'rss'
			);
		}
		
		$sMenu = BxDolPageView::getBlockCaptionItemCode(time(), $aMenu);
        
        $sTitle = _t($this->aCurrent['title']);
        
		$sCode = DesignBoxContent($sTitle, $sCode. $sPaginate, 1, $sMenu);
 
        if (!isset($_GET['searchMode']))
            $sCode = '<div id="page_block_'.$this->id.'">' . $sCode . '<div class="clear_both"></div></div>';
        return $sCode;
    }




}
