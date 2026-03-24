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

bx_import('BxDolTwigSearchResult');

class BxGroupsSearchResult extends BxDolTwigSearchResult {

    var $aCurrent = array(
        'name' => 'bx_groups',
        'title' => '_bx_groups_page_title_browse',
        'table' => 'bx_groups_main',
        //'ownFields' => array('id', 'title', 'uri', 'created', 'author_id', 'thumb', 'rate', 'fans_count', 'country', 'city'), 
		'ownFields' => array('id', 'title', 'uri', 'created', 'author_id', 'thumb', 'icon', 'rate', 'fans_count', 'country', 'city', 'state', 'categories', 'tags','comments_count', 'views', 'desc', 'group_membership_view_filter', 'featured', 'allow_view_group_to'), 
		 
        'searchFields' => array('title', 'desc', 'tags', 'categories'),
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
            'category' => array('value' => '', 'field' => 'Category', 'operator' => '=', 'table' => 'sys_categories'),
            'public' => array('value' => '', 'field' => 'allow_view_group_to', 'operator' => '='),
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
                'DateTimeUTS' => 'created',
                'Desc' => 'desc',
                'Photo' => '',
            ),
        ),
        'ident' => 'id'
    );
    
    
    //function BxGroupsSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '') {        

    //[begin] - ultimate groups mod from modzzz  
    function BxGroupsSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '', $sValue4 = '', $sValue5 = '', $sValue6 = '') {

		$sValue = process_db_input($sValue);
		$sValue2 = process_db_input($sValue2);
		$sValue3 = process_db_input($sValue3);
		$sValue4 = process_db_input($sValue4);
		$sValue5 = process_db_input($sValue5);
		$sValue6 = process_db_input($sValue6);
		//[end] - ultimate groups mod from modzzz 
 
	    $oMain = $this->getMain();
 
        switch ($sMode) {

			case 'survey':
	   
				$aGroupEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iGroupId = (int)$aGroupEntry['id'];
				$sTitle = $aGroupEntry['title'];
   
				$this->aCurrent['sorting'] = 'survey';

				$this->aCurrent['table'] = 'modzzz_survey_main';
				
				$this->aCurrent['ownFields'] = array('id', 'title', 'uri', 'author_id', 'created');

				$this->aCurrent['join'] = array(
					'profile' => array(
							'type' => 'left',
							'table' => 'Profiles',
							'mainField' => 'author_id',
							'onField' => 'ID',
							'joinFields' => array('NickName'),
					),
				);
 
				$this->aCurrent['restriction']['public']['field'] = 'allow_view_survey_to';

				$this->aCurrent['restriction']['group_id'] = array('value' => $iGroupId, 'field' => 'group_id', 'operator' => '=', 'table' => 'modzzz_survey_main'); 
  
				$this->sBrowseUrl = "browse/survey/$sValue";
				$this->aCurrent['title'] = _t('_bx_groups_caption_browse_group_survey', $sTitle);
				break; 
 
			case 'city': 
			
				$aProfileInfo = getProfileInfo(getLoggedId()); 
				$sCity = $aProfileInfo['City'];
 
				$this->aCurrent['restriction']['city'] = array('value' => $sCity, 'field' => 'city', 'operator' => '='); 
 
				$this->sBrowseUrl = "browse/city/$sCity";
				$this->aCurrent['title'] = _t('_bx_groups_caption_browse_local', $sCity);
				break;
 
            case 'pending':
                if (false !== bx_get('bx_groups_filter'))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input(bx_get('bx_groups_filter'), BX_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "administration";
                $this->aCurrent['title'] = _t('_bx_groups_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break;

            case 'my_pending':
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_bx_groups_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break;

            case 'search':
/* 
                if ($sValue)
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');

                if ($sValue2) {

                    $this->aCurrent['join']['category'] = array(
                        'type' => 'inner',
                        'table' => 'sys_categories',
                        'mainField' => 'id',
                        'onField' => 'ID',
                        'joinFields' => '',
                    );

                    $this->aCurrent['restriction']['category']['value'] = $sValue2;
                    if (is_array($sValue2)) {
                        $this->aCurrent['restriction']['category']['operator'] = 'in';
                    } 
                }

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $sValue2 = $GLOBALS['MySQL']->unescape($sValue2);
                $this->sBrowseUrl = "search/$sValue/" . (is_array($sValue2) ? implode(',',$sValue2) : $sValue2);
                $this->aCurrent['title'] = _t('_bx_groups_page_title_search_results') . ' ' . (is_array($sValue2) ? implode(', ',$sValue2) : $sValue2) . ' ' . $sValue;
                unset($this->aCurrent['rss']);
                break;
*/

			//[begin] - ultimate groups mod from modzzz 
			if ($sValue)
				$this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');

            if (is_array($sValue2) && count($sValue2)) {  
                foreach($sValue2 as $val){
                    if(trim($val))
                        $bSetCategory = true;
                }
                if($bSetCategory){  
                    $this->aCurrent['join']['category'] = array(
                        'type' => 'inner',
                        'table' => 'sys_categories',
                        'mainField' => 'id',
                        'onField' => 'ID',
                        'joinFields' => '',
                    );
					$this->aCurrent['restriction']['category_type'] = array('table' => 'sys_categories', 'value' => 'bx_groups', 'field' => 'Type', 'operator' => '='); 

 				    $this->aCurrent['restriction']['category']['table'] = 'sys_categories';
                    $this->aCurrent['restriction']['category']['value'] = $sValue2;
                    if (is_array($sValue2)) {
                        $this->aCurrent['restriction']['category']['operator'] = 'in';
                    }  
                }
            }

			if($sValue3){
				$this->aCurrent['restriction']['country'] = array('value' => $sValue3, 'field' => 'Country', 'operator' => '='); 
			}
		 
			if ($sValue4)
				$this->aCurrent['restriction']['state'] = array('value' => $sValue4,'field' => 'state','operator' => '=');

			if ($sValue5)
				$this->aCurrent['restriction']['city'] = array('value' => $sValue5,'field' => 'city','operator' => '=');
		   
			$sValue = $GLOBALS['MySQL']->unescape($sValue);
			$sValue2 = $GLOBALS['MySQL']->unescape($sValue2);
			$sValue3 = $GLOBALS['MySQL']->unescape($sValue3);
			$sValue4 = $GLOBALS['MySQL']->unescape($sValue4);
			$sValue5 = $GLOBALS['MySQL']->unescape($sValue5);
			 
			$sCategory = is_array($sValue2) ? implode(',',$sValue2) : '-'; 
			$sCategory = ($sCategory) ? $sCategory : '-'; 
			 
			$this->sBrowseUrl = "search/$sValue/" . $sCategory . '/' . ($sValue3 ? $sValue3 : '-'). '/' . ($sValue4 ? $sValue4 : '-'). '/' . $sValue5;
  
			$this->aCurrent['title'] = _t('_bx_groups_page_title_search_results');
			unset($this->aCurrent['rss']);
			break;

			case 'quick': 
				if ($sValue)
					$this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');
				if ($sValue2)
					$this->aCurrent['restriction']['city'] = array('value' => $sValue2,'field' => 'city','operator' => '=');  
				
                $this->sBrowseUrl = "browse/quick/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_bx_groups_page_title_search_results');
				unset($this->aCurrent['rss']); 
			break; 
  
            case 'user':
				$sValue = ($sValue) ? $sValue : getNickName($oMain->_iProfileId);

                $iProfileId = $GLOBALS['oBxGroupsModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/user/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_bx_groups_page_title_browse_by_author');
                if (bx_get('rss')) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['Avatar']) {
                        $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
                        $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    } 
                }
                break;

            case 'joined':

				$sValue = ($sValue) ? $sValue : getNickName($oMain->_iProfileId);
 
                $iProfileId = $GLOBALS['oBxGroupsModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                

                if (!$iProfileId) {

                    $this->isError = true;

                } else {

					$this->aCurrent['join']['fans'] = array(
						'type' => 'inner',
						'table' => 'bx_groups_fans',
						'mainField' => 'id',
						'onField' => 'id_entry',
						'joinFields' => array('id_profile'),
					);
					$this->aCurrent['restriction']['fans'] = array(
						'value' => $iProfileId, 
						'field' => 'id_profile', 
						'operator' => '=', 
						'table' => 'bx_groups_fans',
					);
					$this->aCurrent['restriction']['confirmed_fans'] = array(
						'value' => 1, 
						'field' => 'confirmed', 
						'operator' => '=', 
						'table' => 'bx_groups_fans',
					);
				}

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/joined/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_bx_groups_page_title_browse_by_author_joined_groups');

                if (bx_get('rss')) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['Avatar']) {
                        $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
                        $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    } 
                }
                break;

            case 'admin':
 
                if (isset($_REQUEST['bx_groups_filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['bx_groups_filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');
 
                //this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId; 
                 $this->aCurrent['restriction']['activeStatus']['value'] = 'approved';

                $this->sBrowseUrl = "browse/admin";
                $this->aCurrent['title'] = _t('_bx_groups_page_title_admin_groups');
                break;

            case 'category':
                $this->aCurrent['join']['category'] = array(
                    'type' => 'inner',
                    'table' => 'sys_categories',
                    'mainField' => 'id',
                    'onField' => 'ID',
                    'joinFields' => '',
                );
				$this->aCurrent['restriction']['category_type'] = array('value' => 'bx_groups', 'field' => 'type', 'operator' => '=', 'table' => 'sys_categories'); 
		 
                $this->aCurrent['restriction']['category']['value'] = $sValue;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/category/" . title2uri($sValue);
                $this->aCurrent['title'] = _t('_bx_groups_page_title_browse_by_category') . ' ' . $sValue;
                break;

            case 'tag':
                $this->aCurrent['restriction']['tag']['value'] = $sValue;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/tag/" . title2uri($sValue);
                $this->aCurrent['title'] = _t('_bx_groups_page_title_browse_by_tag') . ' ' . $sValue;
                break; 
            case 'country':            
                $this->aCurrent['restriction'][$sMode]['value'] = $sValue;
                $this->sBrowseUrl = "browse/$sMode/$sValue";
                $this->aCurrent['title'] = _t('_bx_groups_caption_browse_by_'.$sMode) . ' ' . $sValue;
                break; 
			case 'local_country':          
				$this->aCurrent['restriction']['local_country'] = array('value' => $sValue, 'field' => 'country', 'operator' => '='); 
				$this->sBrowseUrl = "local_country/$sValue";
		 
				if($sValue2){
 					$sCategory = uri2title($sValue2);

					$this->aCurrent['join']['category'] = array(
						'type' => 'inner',
						'table' => 'sys_categories',
						'mainField' => 'id',
						'onField' => 'ID',
						'joinFields' => '',
					);
					$this->aCurrent['restriction']['category']['value'] = $sCategory;
					$this->aCurrent['restriction']['category_type'] = array('value' => 'bx_groups', 'field' => 'type', 'operator' => '=', 'table' => 'sys_categories'); 

					$sValue2 = $GLOBALS['MySQL']->unescape($sValue2);
					$this->sBrowseUrl = "local_country/$sValue/$sValue2";
   				} 
				break; 
			case 'local_state':   
				
				$this->aCurrent['restriction']['local_country'] = array('value' => $sValue, 'field' => 'country', 'operator' => '='); 

				$this->aCurrent['restriction']['local_state'] = array('value' => $sValue2, 'field' => 'state', 'operator' => '='); 
 
 				$this->sBrowseUrl = "browse/local_state/$sValue/$sValue2";
				 
				if($sValue3){
					$sCategory = uri2title($sValue3);
 
					$this->aCurrent['join']['category'] = array(
						'type' => 'inner',
						'table' => 'sys_categories',
						'mainField' => 'id',
						'onField' => 'ID',
						'joinFields' => '',
					);
					$this->aCurrent['restriction']['category']['value'] = $sCategory;
					$this->aCurrent['restriction']['category_type'] = array('value' => 'bx_groups', 'field' => 'type', 'operator' => '=', 'table' => 'sys_categories'); 

					$sValue3 = $GLOBALS['MySQL']->unescape($sValue3);
					$this->sBrowseUrl = "browse/local_state/$sValue/$sValue2/$sValue3"; 
 				} 
 				break; 
			case 'local': 
				$this->aCurrent['restriction']['local'] = array('value' => $sValue, 'field' => 'city', 'operator' => '='); 
 
				$this->sBrowseUrl = "browse/local/$sValue";
				$this->aCurrent['title'] = _t('_bx_groups_caption_browse_local', $sValue);
				break;
			case 'ilocal': 
  
				$bFiltered=false;

				if($oMain->_iProfileId){ 
					$aProfile = getProfileInfo($oMain->_iProfileId);
			  /*
					if($aProfile['zip']){
						$bFiltered=true;
						$this->aCurrent['restriction']['zip'] = array('value' => $aProfile['zip'], 'field' => 'zip', 'operator' => '=' );  
					}
 			 */
					if($aProfile['City'] && (!$bFiltered)){
						$bFiltered=true;

						$this->aCurrent['restriction']['country'] = array('value' => $aProfile['Country'], 'field' => 'country', 'operator' => '=' ); 

						$this->aCurrent['restriction']['city'] = array('value' => $aProfile['City'], 'field' => 'city', 'operator' => '=' ); 
					}
 
					if(!$bFiltered){

						$this->aCurrent['restriction']['country'] = array('value' => $aProfile['Country'], 'field' => 'country', 'operator' => '=' ); 
						
						$sStateField = getParam('bx_groups_state_field'); 
						if($aProfile[$sStateField]){
							$this->aCurrent['restriction']['state'] = array('value' => $aProfile[$sStateField], 'field' => 'state', 'operator' => '='); 
						}
					}

					$this->aCurrent['restriction']['author'] = array('value' => $oMain->_iProfileId, 'field' => 'author_id', 'operator' => '!=' );  

				}else{  
					$this->aCurrent['restriction']['city'] = array('value' => '-not-set-', 'field' => 'city', 'operator' => '=' );  
				}
 
				$this->sBrowseUrl = "browse/ilocal";
				$this->aCurrent['title'] = _t('_bx_groups_caption_browse_my_local');
				break;
 
			case 'other_local': 
				$this->aCurrent['restriction']['item'] = array('value' => $sValue, 'field' => 'id', 'operator' => '!=');  
				$this->aCurrent['restriction']['local_city'] = array('value' => $sValue2, 'field' => 'city', 'operator' => '='); 
				if($sValue3)
				$this->aCurrent['restriction']['local_state'] = array('value' => $sValue3, 'field' => 'state', 'operator' => '='); 

				$this->sBrowseUrl = "browse/other_local/$sValue/$sValue2/$sValue3";
				$this->aCurrent['title'] = _t('_bx_groups_caption_browse_local', $sValue2);
				break; 
			case 'other':
				$sNickName = getNickName($sValue);

				$this->aCurrent['restriction']['other'] = array('value' => $sValue, 'field' => 'author_id', 'operator' => '=');  
				$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/other/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_bx_groups_caption_browse_other', $sNickName); 
				break;
 
            case 'recent':
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_bx_groups_page_title_browse_recent');
                break;

            case 'top':
                $this->sBrowseUrl = 'browse/top';
                $this->aCurrent['sorting'] = 'top';
                $this->aCurrent['title'] = _t('_bx_groups_page_title_browse_top_rated');
                break;

            case 'popular':
                $this->sBrowseUrl = 'browse/popular';
                $this->aCurrent['sorting'] = 'popular';
                $this->aCurrent['title'] = _t('_bx_groups_page_title_browse_popular');
                break;                

            case 'featured':
				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured';
                $this->aCurrent['title'] = _t('_bx_groups_page_title_browse_featured');
                break;                                

            case 'calendar':
  
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 00:00:00')", 'field' => 'created', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 23:59:59')", 'field' => 'created', 'operator' => '<=', 'no_quote_value' => true);
                $this->sBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->sGroupsBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->aCurrent['title'] = _t('_bx_groups_page_title_browse_by_day') . sprintf("%04u-%02u-%02u", $sValue, $sValue2, $sValue3);
                break;                                

            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_bx_groups');
                break;

            case 'sponsors':
					
				$this->aCurrent['sorting'] = 'sponsors';
	 
				$oMain = $this->getMain();
				$aGroupEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iGroupId = (int)$aGroupEntry['id'];
				$sTitle = $aGroupEntry['title'];
 
				$this->aCurrent['join']['sponsor'] = array(
					'type' => 'inner',
					'table' => 'bx_groups_sponsor_main',
					'mainField' => 'id',
					'onField' => 'group_id',
					'joinFields' => array('id', 'group_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
				);

				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['group_id'] = array('value' => $iGroupId, 'field' => 'group_id', 'operator' => '=', 'table' => 'bx_groups_sponsor_main'); 
				$this->sBrowseUrl = "sponsor/browse/$sValue";
				$this->aCurrent['title'] = _t('_bx_groups_caption_browse_group_sponsors', $sTitle);
				break; 

          case 'blogs':
					
				$this->aCurrent['sorting'] = 'blogs';
	 
				$oMain = $this->getMain();
				$aGroupEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iGroupId = (int)$aGroupEntry['id'];
				$sTitle = $aGroupEntry['title'];
 
				$this->aCurrent['join']['blog'] = array(
					'type' => 'inner',
					'table' => 'bx_groups_blog_main',
					'mainField' => 'id',
					'onField' => 'group_id',
					'joinFields' => array('id', 'group_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
				);

				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['group_id'] = array('value' => $iGroupId, 'field' => 'group_id', 'operator' => '=', 'table' => 'bx_groups_blog_main'); 
				$this->sBrowseUrl = "blog/browse/$sValue";
				$this->aCurrent['title'] = _t('_bx_groups_caption_browse_group_blogs', $sTitle);
				break; 

				
            case 'venues':
				
				$this->aCurrent['sorting'] = 'venues';
	
				$oMain = $this->getMain();
				$aGroupEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iGroupId = (int)$aGroupEntry['id'];
				$sTitle = $aGroupEntry['title'];
 
				$this->aCurrent['join']['venue'] = array(
					'type' => 'inner',
					'table' => 'bx_groups_venue_main',
					'mainField' => 'id',
					'onField' => 'group_id',
					'joinFields' => array('id', 'group_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
				);

				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['group_id'] = array('value' => $iGroupId, 'field' => 'group_id', 'operator' => '=', 'table' => 'bx_groups_venue_main'); 
				$this->sBrowseUrl = "venue/browse/$sValue";
				$this->aCurrent['title'] = _t('_bx_groups_caption_browse_group_venues', $sTitle);
				break;

            case 'news':
			 
				$this->aCurrent['sorting'] = 'news';
			
				$oMain = $this->getMain();
				$aGroupEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iGroupId = (int)$aGroupEntry['id'];
				$sTitle = $aGroupEntry['title'];
 
				//[begin] news integration - modzzz
				if(getParam('bx_groups_modzzz_news')=='on'){ 
					$oNews = BxDolModule::getInstance('BxNewsModule');

					$this->aCurrent['sorting'] = 'modzzz_news';

					$this->aCurrent['table'] = 'modzzz_news_main';
					
					$this->aCurrent['ownFields'] = array('id', 'title', 'uri', 'created', 'when', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'snippet', 'parent_category_id', 'category_id', 'tags', 'when', 'comments_count', 'video_embed', 'group_id');
    
					$this->aCurrent['join'] = array(
						'profile' => array(
								'type' => 'left',
								'table' => 'Profiles',
								'mainField' => 'author_id',
								'onField' => 'ID',
								'joinFields' => array('NickName'),
						),
					);

					$this->aCurrent['restriction']['public']['field'] = 'allow_view_news_to';
		  
					$this->aCurrent['restriction']['group_id'] = array('value' => $iGroupId, 'field' => 'group_id', 'operator' => '=', 'table' => 'modzzz_news_main');
					
					$this->sBrowseUrl = "news/browse/$sValue"; 

				}else{
 
					$this->aCurrent['join']['news'] = array(
						'type' => 'inner',
						'table' => 'bx_groups_news_main',
						'mainField' => 'id',
						'onField' => 'group_id',
						'joinFields' => array('id', 'group_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
					);
	 
					$this->aCurrent['ownFields'] = array();
	 
					$this->aCurrent['restriction']['group_id'] = array('value' => $iGroupId, 'field' => 'group_id', 'operator' => '=', 'table' => 'bx_groups_news_main'); 
					$this->sBrowseUrl = "news/browse/$sValue";
				}
				$this->aCurrent['title'] = _t('_bx_groups_caption_browse_group_news', $sTitle);
				break;

            case 'events':
						
				$this->aCurrent['sorting'] = 'events';
			 
				$oMain = $this->getMain();
				$aGroupEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iGroupId = (int)$aGroupEntry['id'];
				$sTitle = $aGroupEntry['title'];
 
				//[begin] group integration - modzzz
				if(getParam('bx_groups_boonex_events')=='on'){  
					$oEvent = BxDolModule::getInstance('BxEventsModule');

					$this->aCurrent['sorting'] = 'event';

					$this->aCurrent['table'] = 'bx_events_main';
					
					$this->aCurrent['ownFields'] = array('ID', 'Title','Description', 'EntryUri', 'Country', 'City', 'Place', 'EventStart', 'EventEnd', 'ResponsibleID', 'PrimPhoto', 'FansCount', 'Rate');
    
					$this->aCurrent['join'] = array(
						'profile' => array(
								'type' => 'left',
								'table' => 'Profiles',
								'mainField' => 'ResponsibleID',
								'onField' => 'ID',
								'joinFields' => array('NickName'),
						),
					);

					$this->aCurrent['restriction']['activeStatus']['field'] = 'Status';
					$this->aCurrent['restriction']['owner']['field'] = 'ResponsibleID';
					$this->aCurrent['restriction']['public']['field'] = 'allow_view_event_to';
  
					$this->aCurrent['restriction']['group_id'] = array('value' => $iGroupId, 'field' => 'group_id', 'operator' => '=', 'table' => 'bx_events_main'); 
			 
					$this->aCurrent['restriction']['end'] = array('value' => time(), 'field' => 'EventEnd', 'operator' => '>', 'table' => 'bx_events_main'); 

				}else{
					//[end] group integration - modzzz 
					$this->aCurrent['join']['event'] = array(
						'type' => 'inner',
						'table' => 'bx_groups_event_main',
						'mainField' => 'id',
						'onField' => 'group_id',
						'joinFields' => array('id', 'group_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'event_start', 'country', 'city', 'comments_count', 'author_id'),
					);

					$this->aCurrent['ownFields'] = array();
 
					$this->aCurrent['restriction']['group_id'] = array('value' => $iGroupId, 'field' => 'group_id', 'operator' => '=', 'table' => 'bx_groups_event_main'); 
 
					$this->aCurrent['restriction']['end'] = array('value' => time(), 'field' => 'event_end', 'operator' => '>', 'table' => 'bx_groups_event_main');  
				}

				$this->sBrowseUrl = "event/browse/$sValue";
				$this->aCurrent['title'] = _t('_bx_groups_caption_browse_group_events', $sTitle);
				break; 
            default:
                $this->isError = true;
        }

        $oMain = $this->getMain();

		if(($sMode=='events') && getParam('bx_groups_boonex_events')=='on'){
			
			$oEvent = BxDolModule::getInstance('BxEventsModule');

			$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('bx_events_perpage_browse');

			if (isset($this->aCurrent['rss']))
				$this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oEvent->_oConfig->getBaseUri() . $this->sBrowseUrl;

			if (bx_get('rss')) {
				$this->aCurrent['ownFields'][] = 'desc';
				$this->aCurrent['ownFields'][] = 'created';
				$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('bx_events_max_rss_num');
			}

			bx_import('Voting', $oEvent->_aModule);
			$oVotingView = new BxEventsVoting ('bx_events', 0);
			$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

			$this->sFilterName = 'bx_events_filter';

		}elseif(in_array($sMode, array('news','view_news')) && getParam('bx_groups_modzzz_news')=='on'){
			
			$oNews = BxDolModule::getInstance('BxNewsModule');

			$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_news_perpage_browse');

			if (isset($this->aCurrent['rss']))
				$this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oNews->_oConfig->getBaseUri() . $this->sBrowseUrl;

			if (bx_get('rss')) {
				$this->aCurrent['ownFields'][] = 'desc';
				$this->aCurrent['ownFields'][] = 'created';
				$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_news_max_rss_num');
			}

			bx_import('Voting', $oNews->_aModule);
			$oVotingView = new BxNewsVoting ('modzzz_news', 0);
			$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

			$this->sFilterName = 'filter';
 
		}else{
			//begin- membership filter
			if(!$oMain->isAdmin()){ 
				if(!$oMain->_iProfileId){
					 $this->aCurrent['restriction']['membership'] = array('value' => "", 'field' => 'group_membership_view_filter', 'operator' => '=' ); 
				}else{
					require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
					$aMembershipInfo = getMemberMembershipInfo($oMain->_iProfileId);
					
					if($aMembershipInfo['DateExpires']){ 
						$bCheckMembership = (($aMembershipInfo['DateStarts'] < time()) && ($aMembershipInfo['DateExpires'] > time())) ? true : false;

						if($bCheckMembership)
							$this->aCurrent['restriction']['membership'] = array('value' => array('',$aMembershipInfo['ID']), 'field' => 'group_membership_view_filter', 'operator' => 'in' );  
						else
							$this->aCurrent['restriction']['membership'] = array('value' => "", 'field' => 'group_membership_view_filter', 'operator' => '=' ); 

					}else{
						$this->aCurrent['restriction']['membership'] = array('value' => array('',$aMembershipInfo['ID']), 'field' => 'group_membership_view_filter', 'operator' => 'in' );  
					}
				}
			}
			//end- membership filter
 
			$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('bx_groups_perpage_browse');

			if (isset($this->aCurrent['rss']))
				$this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . $this->sBrowseUrl;

			if (bx_get('rss')) {
				$this->aCurrent['ownFields'][] = 'desc';
				$this->aCurrent['ownFields'][] = 'created';
				$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('bx_groups_max_rss_num');
			}

			bx_import('Voting', $oMain->_aModule);
			$oVotingView = new BxGroupsVoting ('bx_groups', 0);
			$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

			$this->sFilterName = 'bx_groups_filter';
		}

        parent::BxDolTwigSearchResult();
    }

    function getAlterOrder() {
		if ($this->aCurrent['sorting'] == 'last') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `bx_groups_main`.`created` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'top') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `bx_groups_main`.`rate` DESC, `bx_groups_main`.`rate_count` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'popular') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `bx_groups_main`.`views` DESC";
			return $aSql;
		} elseif($this->aCurrent['sorting'] == 'venues') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `bx_groups_venue_main`.`created` DESC";
			return $aSql;
		} elseif($this->aCurrent['sorting'] == 'sponsors') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `bx_groups_sponsor_main`.`created` DESC";
			return $aSql;
		} elseif($this->aCurrent['sorting'] == 'blogs') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `bx_groups_blog_main`.`created` DESC";
			return $aSql;
		} elseif($this->aCurrent['sorting'] == 'news') {
			$aSql = array(); 
			$aSql['order'] = " ORDER BY `bx_groups_news_main`.`created` DESC";
			return $aSql;
		} elseif($this->aCurrent['sorting'] == 'events') {

			if(getParam('bx_groups_boonex_events')=='on'){  
				$aSql = array();
				$aSql['order'] = " ORDER BY `bx_events_main`.`EventStart` ASC";
				return $aSql;
			}else{
				$aSql = array();
				$aSql['order'] = " ORDER BY `bx_groups_event_main`.`event_start` ASC";
				return $aSql; 
			}
		//[begin] news integration - modzzz
		} elseif ($this->aCurrent['sorting'] == 'modzzz_news') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_news_main`.`created` DESC";
			return $aSql;
		//[end] news integration - modzzz

		//[begin] survey integration - modzzz
		} elseif ($this->aCurrent['sorting'] == 'survey') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_survey_main`.`created` DESC";
			return $aSql;
		//[end] survey integration - modzzz

		}
 
	    return array();
    }

    function displayResultBlock ()
    {
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
        return BxDolModule::getInstance('BxGroupsModule');
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
        );
    }

	//event etc.
     function displaySubProfileResultBlock ($sType) { 
        $aData = $this->getSearchData();  
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $sCode .= $this->addCustomParts();
            foreach ($aData as $aValue) {
                $sCode .= $this->displaySubProfileSearchUnit($sType, $aValue);
            }
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sCode));
        }
        return '';
    }

    function displaySubProfileSearchUnit ($sType, $aData) {
        $oMain = $this->getMain();
		
		$sResult = '';

		switch($sType){ 
 			case 'sponsor':
 
				bx_import('SponsorVoting', $oMain->_aModule);
				$oVotingView = new BxGroupsSponsorVoting ('bx_groups_sponsor', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->sponsor_unit($aData, 'sponsor_unit', $this->oVotingView); 
			break;
			case 'blog':
 
				bx_import('BlogVoting', $oMain->_aModule);
				$oVotingView = new BxGroupsBlogVoting ('bx_groups_blog', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->blog_unit($aData, 'blog_unit', $this->oVotingView); 
			break;
			case 'venue':
 
				bx_import('VenueVoting', $oMain->_aModule);
				$oVotingView = new BxGroupsVenueVoting ('bx_groups_venue', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->venue_unit($aData, 'venue_unit', $this->oVotingView); 
			break;
			case 'news':
 
 				if(getParam('bx_groups_modzzz_news')=='on'){  
					$oNews = BxDolModule::getInstance('BxNewsModule');
					bx_import('Voting', $oNews->_aModule);
					$oVotingView = new BxNewsVoting ('modzzz_news', 0);
					$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
	 
					$sResult = $oNews->_oTemplate->unit($aData, 'unit', $this->oVotingView); 
				}else{
					bx_import('NewsVoting', $oMain->_aModule);
					$oVotingView = new BxGroupsNewsVoting ('bx_groups_news', 0);
					$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
	 
					$sResult = $oMain->_oTemplate->news_unit($aData, 'news_unit', $this->oVotingView);
				}
			break;
			case 'survey':
  
				$oSurvey = BxDolModule::getInstance('BxSurveyModule');
				bx_import('Voting', $oSurvey->_aModule);
				$oVotingView = new BxSurveyVoting ('modzzz_survey', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oSurvey->_oTemplate->unit($aData, 'unit', $this->oVotingView); 
				 
			break;

			case 'event':
  				//[begin] event integration - modzzz
				if(getParam('bx_groups_boonex_events')=='on'){  
					$oEvents = BxDolModule::getInstance('BxEventsModule');
					bx_import('Voting', $oEvents->_aModule);
					$oVotingView = new BxEventsVoting ('bx_events', 0);
					$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
	 
					$sResult = $oEvents->_oTemplate->unit($aData, 'unit', $this->oVotingView); 
				
 				//[end] event integration - modzzz
				}else{
					bx_import('EventVoting', $oMain->_aModule);
					$oVotingView = new BxGroupsEventVoting ('bx_groups_event', 0);
					$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
	 
					$sResult = $oMain->_oTemplate->event_unit($aData, 'event_unit', $this->oVotingView);
				}
				break;

		}

		return $sResult;
	}
 


	/*
	 * Check restriction params and make condition part of query
	 * return $sqlWhere sql code of query for WHERE part  
	 */
	
	function getRestriction () {
		$sqlWhere = '';
	    if (isset($this->aCurrent['restriction'])) {       
            $aAndWhere[] = '1';
            $aOrWhere = array();
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

                if (strlen($sqlCondition) > 0){
					if($aValue['filter']=='or')
						$aOrWhere[] = $sqlCondition;
					else
						$aAndWhere[] = $sqlCondition; 
				} 

            }
  
            $sqlWhere .= "WHERE ". implode(' AND ', $aAndWhere);

			if(count($aOrWhere)){
				$sqlWhere .= " AND (". implode(' OR ', $aOrWhere) .")";
			}
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

}
