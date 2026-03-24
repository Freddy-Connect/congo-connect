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

class BxChurchSearchResult extends BxDolTwigSearchResult {

    var $aCurrent = array(
        'name' => 'modzzz_church',
        'title' => '_modzzz_church_page_title_browse',
        'table' => 'modzzz_church_main',
        'ownFields' => array('id', 'title', 'uri', 'created', 'author_id', 'thumb', 'rate', 'country', 'city', 'state', 'desc', 'category_id', 'tags', 'comments_count','invoice_no', 'video_embed', 'expiry_date', 'featured_expiry_date', 'membership_view_filter', 'fans_count', 'capacity','dress_code', 'history', 'believe', 'service_hours'),
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
            'category' => array('value' => '', 'field' => 'Category', 'operator' => '=', 'table' => 'sys_categories'),
            'public' => array('value' => '', 'field' => 'allow_view_church_to', 'operator' => '='),
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
    
 
    function BxChurchSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '', $sValue4 = '', $sValue5 = '', $sValue6 = '') {
	 
		$sValue = process_db_input($sValue);
		$sValue2 = process_db_input($sValue2);
		$sValue3 = process_db_input($sValue3);
		$sValue4 = process_db_input($sValue4);
		$sValue5 = process_db_input($sValue5);
		$sValue6 = process_db_input($sValue6);

		$oMain = $this->getMain();
		 
		$aAllowedLevels = array();
		$aAllowedLevels[] = '';
 
		if(!$oMain->isAdmin()) {
			if($oMain->_iProfileId){
				$aProfile = getProfileInfo(); 
				require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
				$aMembershipInfo = getMemberMembershipInfo($oMain->_iProfileId);
	   
				$aAllowedLevels[] = $aMembershipInfo['ID']; 
			}

			$this->aCurrent['restriction']['membership_filter'] = array( 'value' => $aAllowedLevels, 'field' => 'membership_view_filter', 'operator' => 'in');
		}

        switch ($sMode) {

            case 'pending':
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "administration/pending_approval";
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break; 
            case 'my_pending': 
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break;
            case 'my_expired':  
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'expired';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_expired');
                unset($this->aCurrent['rss']);
            break; 
            case 'order':  
     
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');

				$this->aCurrent['ownFields'] = array('title', 'uri', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'category_id', 'tags', 'comments_count','status','expiry_date');
   
                $this->sBrowseUrl = "administration/orders/";
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_orders');
                
				$this->aCurrent['join']['invoices'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_invoices',
					'mainField' => 'invoice_no',
					'onField' => 'invoice_no',
					'joinFields' => array('church_id', 'invoice_no','price','days','package_id','invoice_date','invoice_due_date','invoice_expiry_date','invoice_status'),   
				);	

				$this->aCurrent['join']['orders'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_orders',
					'mainField' => 'invoice_no',
					'onField' => 'invoice_no',
					'joinFields' => array('id','order_no','buyer_id','payment_method','order_date','order_status'),   
				); 

				unset($this->aCurrent['restriction']['activeStatus']);  

				unset($this->aCurrent['rss']);  
            break;
            case 'invoice':  
            
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');
 
				$this->aCurrent['ownFields'] = array('title', 'uri', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'category_id', 'tags', 'comments_count' );
  
                $this->sBrowseUrl = "administration/invoices/";
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_invoices');
                 
				$this->aCurrent['join']['invoices'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_invoices',
					'mainField' => 'id',
					'onField' => 'church_id',
					'joinFields' => array('id','church_id', 'invoice_no','price','days','package_id','invoice_status','invoice_date','invoice_due_date','invoice_expiry_date'),   
				);	
				
				unset($this->aCurrent['restriction']['activeStatus']);  
 
				unset($this->aCurrent['rss']);  
            break;
   
            case 'claim':  
              
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');
 
 
				$this->aCurrent['ownFields'] = array('title', 'uri', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'categories', 'tags', 'comments_count' );
  
                //$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "administration/claims/";
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_claims');
                
				$this->aCurrent['join']['claims'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_claim',
					'mainField' => 'id',
					'onField' => 'church_id',
					'joinFields' => array('id', 'church_id', 'member_id', 'claim_date', 'assign_date', 'message', 'processed'),   
				);	 
				$this->aCurrent['restriction']['subcat'] = array( 'value' => 1,'field' => 'processed','operator' => '!=', 'table' =>'modzzz_church_claim');

				unset($this->aCurrent['restriction']['activeStatus']);  

				unset($this->aCurrent['rss']);  
            break;
 
            case 'categories':
 
				$aCategory = $oMain->_oDb->getCategoryByUri($sValue); 
                $iCategoryId = $aCategory['id'];
                $sCategoryName = $aCategory['name']; 
                 $this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/categories/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_category').' - '.$sCategoryName;
				$this->aCurrent['restriction']['subcat'] = array( 'table' => 'modzzz_church_categ', 'value' => $iCategoryId,'field' => 'parent','operator' => '=');
				$this->aCurrent['join']['cat'] = array(
					'type' => 'left',
					'table' => 'modzzz_church_categ',
					'mainField' => 'category_id',
					'onField' => 'id',
					'joinFields' => '',
				);	        
 				 
				//unset($this->aCurrent['rss']);
            break;
            case 'subcategories': 
 
				$aCategory = $oMain->_oDb->getCategoryByUri($sValue); 
                $iCategoryId = $aCategory['id'];
                $sCategoryName = $aCategory['name'];
				$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/subcategories/$sValue" ;
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_category').' - '.$sCategoryName;
				$this->aCurrent['restriction']['subcat'] = array( 'value' => $iCategoryId,'field' => 'category_id','operator' => '=');
        
				$this->aCurrent['join']['sub'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_categ',
					'mainField' => 'category_id',
					'onField' => 'id',
					'joinFields' => '',
				);					

				//unset($this->aCurrent['rss']);
            break;
 
            case 'search':
 
                if ($sValue)
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');

                if ($sValue2) 
				   $this->aCurrent['restriction']['category'] = array('value' => $sValue2,'field' => 'category_id','operator' => 'against');

                if ($sValue3)  
                    $this->aCurrent['restriction']['city'] = array('value' => $sValue3,'field' => 'city','operator' => '=');
  
				if ($sValue4)  
                    $this->aCurrent['restriction']['state'] = array('value' => $sValue4,'field' => 'state','operator' => '=');
 
			/*		
				if (is_array($sValue5) && count($sValue5)) { 
					foreach($sValue5 as $val){
						if(trim($val))
							$bSetCountry = true;
					}
					if($bSetCountry)
						$this->aCurrent['restriction']['country'] = array('value' => $sValue5, 'field' => 'country', 'operator' => 'in');
				}
			
				$sCountryUrl = is_array($sValue5) ? implode(',',$sValue5) : $sValue5;
			*/

				if($sValue5)
					$this->aCurrent['restriction']['country'] = array('value' => $sValue5, 'field' => 'country', 'operator' => 'in');

                $this->sBrowseUrl = "search/$sValue/$sValue2/$sValue3/$sValue4/$sValue5";
   
				$sCountrySrch = $this->getPreListDisplay("Country", $sValue5);
				$sCategorySrch = $GLOBALS['oBxChurchModule']->_oDb->getCategoryName($sValue2);

                $this->aCurrent['title'] = _t('_modzzz_church_page_title_search_results');
                
				unset($this->aCurrent['rss']);

				$this->aCurrent['sorting'] = 'search';
 
                break; 

            case 'user':
                $iProfileId = $GLOBALS['oBxChurchModule']->_oDb->getProfileIdByNickName ($sValue);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                
				$this->sBrowseUrl = "browse/user/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_modzzz_church_page_title_browse_by_author');
                if (isset($_REQUEST['rss']) && $_REQUEST['rss']) {
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

                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');



                $this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "administration/admin_entries";
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_admin_church');
                break;

            case 'category':
            	$sValue = uri2title($sValue);

                $this->aCurrent['join']['category'] = array(
                    'type' => 'inner',
                    'table' => 'sys_categories',
                    'mainField' => 'id',
                    'onField' => 'ID',
                    'joinFields' => '',
                );
                $this->aCurrent['restriction']['category']['value'] = $sValue;
                $this->sBrowseUrl = "browse/category/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_browse_by_category') . ' ' . $sValue;
                break;

            case 'tag':
            	$sValue = uri2title($sValue);

                $this->aCurrent['restriction']['tag']['value'] = $sValue;
                $this->sBrowseUrl = "browse/tag/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_browse_by_tag') . ' ' . $sValue;
                break;

		   //[begin] - local 
			case 'local_country':         
				$this->aCurrent['restriction']['local_country'] = array('value' => $sValue, 'field' => 'country', 'operator' => '='); 
				$this->sBrowseUrl = "browse/local_country/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_local', $sValue);
				break; 
			case 'local_state':             
				$this->aCurrent['restriction']['local_state'] = array('value' => $sValue, 'field' => 'state', 'operator' => '='); 
				$this->sBrowseUrl = "browse/local_state/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_local', $sValue);
				break; 
			case 'local': 
				$this->aCurrent['restriction']['local'] = array('value' => $sValue, 'field' => 'city', 'operator' => '='); 
				$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/local/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_local', $sValue);
				break;

			case 'other_local': 
				$this->aCurrent['restriction']['item'] = array('value' => $sValue, 'field' => 'id', 'operator' => '!=');  
				$this->aCurrent['restriction']['local_city'] = array('value' => $sValue2, 'field' => 'city', 'operator' => '='); 
				$this->aCurrent['restriction']['local_state'] = array('value' => $sValue3, 'field' => 'state', 'operator' => '='); 

				$this->sBrowseUrl = "browse/other_local/$sValue/$sValue2/$sValue3";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_local', $sValue2);
				break;
			case 'other':
				$sNickName = getNickName($sValue);

				$this->aCurrent['restriction']['other'] = array('value' => $sValue, 'field' => 'author_id', 'operator' => '=');  
				$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/other/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_other', $sNickName);
				break;
  
            case 'recent':
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_browse_recent');
                break;

            case 'top':
                $this->sBrowseUrl = 'browse/top';
                $this->aCurrent['sorting'] = 'top';
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_browse_top_rated');
                break;

            case 'popular':
                $this->sBrowseUrl = 'browse/popular';
                $this->aCurrent['sorting'] = 'popular';
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_browse_popular');
                break;                

            case 'featured':
				$this->aCurrent['sorting'] = 'rand'; 
				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured';
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_browse_featured');
                break;                                

            case 'calendar':
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 00:00:00')", 'field' => 'created', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 23:59:59')", 'field' => 'created', 'operator' => '<=', 'no_quote_value' => true);
                $this->sBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->sEventsBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->aCurrent['title'] = _t('_modzzz_church_page_title_browse_by_day') . sprintf("%04u-%02u-%02u", $sValue, $sValue2, $sValue3);
                break;                                

            case 'news':
	 
				$aChurchEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iChurchId = (int)$aChurchEntry['id'];
				$sTitle = $aChurchEntry['title'];
 
				$this->aCurrent['join']['news'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_news_main',
					'mainField' => 'id',
					'onField' => 'church_id',
					'joinFields' => array('id', 'church_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
				);
 
				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['church_id'] = array('value' => $iChurchId, 'field' => 'church_id', 'operator' => '=', 'table' => 'modzzz_church_news_main'); 
				$this->sBrowseUrl = "news/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_church_news', $sTitle);
				break;

            case 'events':
 
				$aChurchEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iChurchId = (int)$aChurchEntry['id'];
				$sTitle = $aChurchEntry['title'];
 		 	  
				$this->aCurrent['join']['event'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_event_main',
					'mainField' => 'id',
					'onField' => 'church_id',
					'joinFields' => array('id', 'church_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'event_start', 'event_end', 'country', 'state', 'city', 'comments_count', 'author_id'),
				);

				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['church_id'] = array('value' => $iChurchId, 'field' => 'church_id', 'operator' => '=', 'table' => 'modzzz_church_event_main'); 
				$this->sBrowseUrl = "event/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_church_events', $sTitle);
				break;

			case 'staffs':
 
				$aChurchEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iChurchId = (int)$aChurchEntry['id'];
				$sTitle = $aChurchEntry['title'];
 		 	  
				$this->aCurrent['join']['staff'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_staff_main',
					'mainField' => 'id',
					'onField' => 'church_id',
					'joinFields' => array('id', 'church_id', 'title', 'uri', 'position',  'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
				);

				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['church_id'] = array('value' => $iChurchId, 'field' => 'church_id', 'operator' => '=', 'table' => 'modzzz_church_staff_main'); 
				$this->sBrowseUrl = "staff/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_church_staffs', $sTitle);
				break;

            case 'doctrines':
  
				$aChurchEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iChurchId = (int)$aChurchEntry['id'];
				$sTitle = $aChurchEntry['title'];
 		 	  
				$this->aCurrent['join']['doctrine'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_doctrine_main',
					'mainField' => 'id',
					'onField' => 'church_id',
					'joinFields' => array('id', 'church_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
				);

				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['church_id'] = array('value' => $iChurchId, 'field' => 'church_id', 'operator' => '=', 'table' => 'modzzz_church_doctrine_main'); 
				$this->sBrowseUrl = "doctrine/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_church_doctrines', $sTitle);
				break;
 
            case 'sermons':
 
				$aChurchEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iChurchId = (int)$aChurchEntry['id'];
				$sTitle = $aChurchEntry['title'];
 		 	  
				$this->aCurrent['join']['sermon'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_sermon_main',
					'mainField' => 'id',
					'onField' => 'church_id',
					'joinFields' => array('id', 'church_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
				);

				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['church_id'] = array('value' => $iChurchId, 'field' => 'church_id', 'operator' => '=', 'table' => 'modzzz_church_sermon_main'); 
				$this->sBrowseUrl = "sermon/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_church_sermons', $sTitle);
				break;

            case 'members':
 
				$aChurchEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iChurchId = (int)$aChurchEntry['id'];
				$sTitle = $aChurchEntry['title'];
 
				$this->aCurrent['join']['members'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_members_main',
					'mainField' => 'id',
					'onField' => 'church_id',
					'joinFields' => array('id', 'church_id', 'title', 'uri', 'position', 'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
				);
 
				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['church_id'] = array('value' => $iChurchId, 'field' => 'church_id', 'operator' => '=', 'table' => 'modzzz_church_members_main'); 
				$this->sBrowseUrl = "members/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_church_members', $sTitle);
				break;

            case 'branches':
 
				$aChurchEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iChurchId = (int)$aChurchEntry['id'];
				$sTitle = $aChurchEntry['title'];
 
				$this->aCurrent['join']['branches'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_branches_main',
					'mainField' => 'id',
					'onField' => 'church_id',
					'joinFields' => array('id', 'church_id', 'title', 'uri', 'created', 'country', 'state', 'city', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
				);
 
				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['church_id'] = array('value' => $iChurchId, 'field' => 'church_id', 'operator' => '=', 'table' => 'modzzz_church_branches_main'); 
				$this->sBrowseUrl = "branches/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_church_branches', $sTitle);
				break;

            case 'ministries':
 
				$aChurchEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iChurchId = (int)$aChurchEntry['id'];
				$sTitle = $aChurchEntry['title'];
 
				$this->aCurrent['join']['ministries'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_ministries_main',
					'mainField' => 'id',
					'onField' => 'church_id',
					'joinFields' => array('id', 'church_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
				);
 
				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['church_id'] = array('value' => $iChurchId, 'field' => 'church_id', 'operator' => '=', 'table' => 'modzzz_church_ministries_main'); 
				$this->sBrowseUrl = "ministries/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_church_ministries', $sTitle);
				break;

 			case 'reviews':
 
				$aEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iId = (int)$aEntry['id'];
				$sTitle = $aEntry['title'];
 
				$this->aCurrent['join']['review'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_review_main',
					'mainField' => 'id',
					'onField' => 'church_id',
					'joinFields' => array('id', 'church_id', 'title', 'uri', 'created', 'author_id', 'thumb', 'desc', 'rate', 'comments_count'),
				); 

				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['church_id'] = array('value' => $iId, 'field' => 'church_id', 'operator' => '=', 'table' => 'modzzz_church_review_main'); 

				$this->sBrowseUrl = "review/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_church_reviews', $sTitle);
				break;    
           case 'faqs':
 
				$aEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iId = (int)$aEntry['id'];
				$iAuthorId = (int)$aEntry['author_id'];
				$sTitle = $aEntry['title'];
 
				$this->aCurrent['join']['faq'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_faq_items',
					'mainField' => 'id',
					'onField' => 'church_id',
					'joinFields' => array('id', 'question', 'answer', 'church_id'),
				); 

				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['church_id'] = array('value' => $iId, 'field' => 'church_id', 'operator' => '=', 'table' => 'modzzz_church_faq_items'); 
		 
				$this->sBrowseUrl = "faq/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_church_faqs', $sTitle);
				break;  
           case 'donors':
 
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');

				$aEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iId = (int)$aEntry['id'];
				$iAuthorId = (int)$aEntry['author_id'];
				$sTitle = $aEntry['title'];
 
				$this->aCurrent['join']['donor'] = array(
					'type' => 'inner',
					'table' => 'modzzz_church_paypal_trans',
					'mainField' => 'id',
					'onField' => 'church_id',
					'joinFields' => array('id', 'amount', 'trans_id', 'donor_id', 'church_id', 'anonymous', 'first_name', 'last_name', 'created'),
				); 
   
				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['church_id'] = array('value' => $iId, 'field' => 'church_id', 'operator' => '=', 'table' => 'modzzz_church_paypal_trans'); 
		 
				$this->sBrowseUrl = "administration/donors/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_church_caption_browse_church_donors', $sTitle);
				break; 
            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_modzzz_church');
                break;

            default:
                $this->isError = true;
        }
 
        $this->aCurrent['paginate']['perPage'] =  $oMain->_oDb->getParam('modzzz_church_perpage_browse');

        if (isset($this->aCurrent['rss']))
            $this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . $this->sBrowseUrl;

        if (isset($_REQUEST['rss']) && $_REQUEST['rss']) {
            $this->aCurrent['ownFields'][] = 'desc';
            $this->aCurrent['ownFields'][] = 'created';
            $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_church_max_rss_num');
        }

        bx_import('Voting', $oMain->_aModule);
        $oVotingView = new BxChurchVoting ('modzzz_church', 0);
        $this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

        $this->sFilterName = 'filter';

        parent::BxDolTwigSearchResult();
    }

	function getPreListDisplay($sField, $sValue){ 
 		
		if(is_array($sValue)) {
			foreach($sValue as $sEachKey=>$sEachVal){
				$sValue[$sEachKey] = htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sField][$sEachVal]['LKey']) );
			}
			$sList = implode(', ',$sValue);
			return $sList;
		}else{ 
			return htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sField][$sValue]['LKey']) );
		}
	}

    function getAlterOrder() {
		if ($this->aCurrent['sorting'] == 'last') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_church_main`.`created` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'top') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_church_main`.`rate` DESC, `modzzz_church_main`.`rate_count` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'popular') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_church_main`.`views` DESC";
			return $aSql; 
		} elseif ($this->aCurrent['sorting'] == 'search') {
 
			//for search results, return featured churches first while randomizing all results
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_church_main`.`featured` DESC, rand()";
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
        return BxDolModule::getInstance('BxChurchModule');
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

	//orders and invoices
    function displayOrdersResultBlock ($sType) { 
        $aData = $this->getSearchData();
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $sCode .= $this->addCustomParts();
            foreach ($aData as $aValue) {
                $sCode .= $this->displayOrdersSearchUnit($sType, $aValue);
            }
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sCode));
        }
        return '';
    }

    function displayOrdersSearchUnit ($sType, $aData) {
        $oMain = $this->getMain();
		 
		if($sType=='order')
			return $oMain->_oTemplate->order_unit($aData, $this->sUnitTemplate, $this->oVotingView);
  		elseif($sType=='invoice')
			return $oMain->_oTemplate->invoice_unit($aData, $this->sUnitTemplate, $this->oVotingView);
	}
  
	//claim
     function displayClaimResultBlock ($sType) { 
        $aData = $this->getSearchData();
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $sCode .= $this->addCustomParts();
            foreach ($aData as $aValue) {
                $sCode .= $this->displayClaimSearchUnit($sType, $aValue);
            }
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sCode));
        }
        return '';
    }

    function displayClaimSearchUnit ($sType, $aData) {
        $oMain = $this->getMain();
	 
		return $oMain->_oTemplate->claim_unit($aData, $this->sUnitTemplate, $this->oVotingView); 
	}
 
	function processing () {
 
		if($this->aCurrent['join']['event']['table']=='modzzz_church_event_main'){
			$sCode = $this->displaySubProfileResultBlock('event'); 
		}elseif($this->aCurrent['join']['news']['table']=='modzzz_church_news_main'){ 
			$sCode = $this->displaySubProfileResultBlock('news');  
		}elseif($this->aCurrent['join']['sermon']['table']=='modzzz_church_sermon_main'){ 
			$sCode = $this->displaySubProfileResultBlock('sermon');   
		}elseif($this->aCurrent['join']['donor']['table']=='modzzz_church_paypal_trans'){ 
			$sCode = $this->displaySubProfileResultBlock('donors');   
		}else{
			$sCode = $this->displayResultBlock();
		}
 
		if ($this->aCurrent['paginate']['totalNum'] > 0) {
			$sPaginate = $this->showPagination();
			$sCode = $this->displaySearchBox($sCode, $sPaginate);
		}
		else
			$sCode = '';
		return $sCode;
	}

	//News etc.
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
			case 'news': 
				bx_import('NewsVoting', $oMain->_aModule);
				$oVotingView = new BxChurchNewsVoting ('modzzz_church_news', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->news_unit($aData, 'news_unit', $this->oVotingView); 
			break;
			case 'members': 
				bx_import('MembersVoting', $oMain->_aModule);
				$oVotingView = new BxChurchMembersVoting ('modzzz_church_members', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->members_unit($aData, 'members_unit', $this->oVotingView); 
			break;
			case 'branches': 
				bx_import('BranchesVoting', $oMain->_aModule);
				$oVotingView = new BxChurchBranchesVoting ('modzzz_church_branches', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->branches_unit($aData, 'branches_unit', $this->oVotingView); 
			break;
			case 'ministries': 
				bx_import('MinistriesVoting', $oMain->_aModule);
				$oVotingView = new BxChurchMinistriesVoting ('modzzz_church_ministries', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->ministries_unit($aData, 'ministries_unit', $this->oVotingView); 
			break; 
			case 'event': 
				bx_import('EventVoting', $oMain->_aModule);
				$oVotingView = new BxChurchEventVoting ('modzzz_church_event', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->event_unit($aData, 'event_unit', $this->oVotingView); 
			break;
			case 'staff': 
				bx_import('StaffVoting', $oMain->_aModule);
				$oVotingView = new BxChurchStaffVoting ('modzzz_church_staff', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->staff_unit($aData, 'staff_unit', $this->oVotingView); 
			break;
			case 'doctrine': 
				bx_import('DoctrineVoting', $oMain->_aModule);
				$oVotingView = new BxChurchDoctrineVoting ('modzzz_church_doctrine', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->doctrine_unit($aData, 'doctrine_unit', $this->oVotingView); 
			break; 
			case 'sermon': 
				bx_import('SermonVoting', $oMain->_aModule);
				$oVotingView = new BxChurchSermonVoting ('modzzz_church_sermon', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->sermon_unit($aData, 'sermon_unit', $this->oVotingView); 
			break; 
			case 'review':
				bx_import('ReviewVoting', $oMain->_aModule);
				$oVotingView = new BxChurchReviewVoting ('modzzz_church_review', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

				$sResult = $oMain->_oTemplate->review_unit($aData, 'review_unit', $this->oVotingView); 
		  	break;	 
			case 'faq': 
				$sResult = $oMain->_oTemplate->faq_unit($aData, 'faq_unit'); 
		  	break;  
			case 'donors': 
				$sResult = $oMain->_oTemplate->donors_unit($aData, 'donors_unit'); 
		  	break; 		
		}
		return $sResult;
	}

    function setPublicUnitsOnly($isPublic)
    { 
        if($iLoggedId = getLoggedId()){
            $this->aCurrent['restriction']['public']['value'] = $isPublic ? array(BX_DOL_PG_ALL,BX_DOL_PG_MEMBERS) : false;
            $this->aCurrent['restriction']['public']['operator'] = $isPublic ? 'in' : '=';
        }else{
            $this->aCurrent['restriction']['public']['value'] = $isPublic ? BX_DOL_PG_ALL : false;
        } 
    }

}
