<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Listing
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

class BxListingSearchResult extends BxDolTwigSearchResult {

    var $aCurrent = array(
        'name' => 'modzzz_listing',
        'title' => '_modzzz_listing_page_title_browse',
        'table' => 'modzzz_listing_main',
        'ownFields' => array('id', 'title', 'uri', 'created', 'author_id', 'thumb', 'icon', 'rate', 'country', 'city', 'state', 'desc', 'parent_category_id', 'category_id', 'tags', 'comments_count', 'invoice_no', 'expiry_date', 'featured_expiry_date', 'membership_view_filter','businesstelephone', 'businessfax', 'businesswebsite','employees_count','address2','address1','zip'),
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
            'public' => array('value' => '', 'field' => 'allow_view_listing_to', 'operator' => '='),
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
    
 
    function BxListingSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '', $sValue4 = '', $sValue5 = '', $sValue6 = '', $sValue7 = '') {
	 
        $oMain = $this->getMain();

		$sValue = process_db_input($sValue);
		$sValue2 = process_db_input($sValue2);
		$sValue3 = process_db_input($sValue3);
		$sValue4 = process_db_input($sValue4);
		$sValue5 = process_db_input($sValue5);
		$sValue6 = process_db_input($sValue6);
		$sValue7 = process_db_input($sValue7);
 
        switch ($sMode) {

            //// Freddy News remplacé par classified dans submenu de Business listing view
			case 'news':
						 
				$oMain = $this->getMain();
				$aListingEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iListingId = (int)$aListingEntry['id'];
				$sTitle = $aListingEntry['title'];
 
				 //[begin] freddy modzzz claissified bizmarket integration - modzzz
				if(getParam('modzzz_listing_classified_active')=='on'){ 
					$oClassified = BxDolModule::getInstance('BxClassifiedModule');

					$this->aCurrent['sorting'] = 'modzzz_classified';

					$this->aCurrent['table'] = 'modzzz_classified_main';
					
					$this->aCurrent['ownFields'] = array('id', 'title', 'desc', 'uri', 'created', 'author_id', 'thumb', 'rate',  'category_id','price','saleprice','payment_type','categories','currency','comments_count', 'tags','video_embed','membership_view_filter', 'currency', 'invoice_no','expiry_date', 'featured_expiry_date', 'country','zip', 'city', 'state', 'address1','company_type','classified_type');
    
					$this->aCurrent['join'] = array(
						'profile' => array(
								'type' => 'left',
								'table' => 'Profiles',
								'mainField' => 'author_id',
								'onField' => 'ID',
								'joinFields' => array('NickName'),
						),
					);

					$this->aCurrent['restriction']['public']['field'] = 'allow_view_classfied_to';
		  
					$this->aCurrent['restriction']['company_id'] = array('value' => $iListingId, 'field' => 'company_id', 'operator' => '=', 'table' => 'modzzz_classified_main');
					
					$this->sBrowseUrl = "news/browse/$sValue"; 

				}else{

					$this->aCurrent['join']['news'] = array(
						'type' => 'inner',
						'table' => 'modzzz_listing_news_main',
						'mainField' => 'id',
						'onField' => 'listing_id',
						'joinFields' => array('id', 'listing_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
					);
	 
					$this->aCurrent['ownFields'] = array();
	 
					$this->aCurrent['restriction']['listing_id'] = array('value' => $iListingId, 'field' => 'listing_id', 'operator' => '=', 'table' => 'modzzz_listing_news_main'); 
					$this->sBrowseUrl = "news/browse/$sValue";
				}
				$this->aCurrent['title'] = _t('_modzzz_listing_caption_browse_listing_classified', $sTitle);
				break;

           case 'events':
 				
				$oMain = $this->getMain();
				$aListingEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iListingId = (int)$aListingEntry['id'];
				$sTitle = $aListingEntry['title'];
 		 	   
				//[begin] boonex event integration - modzzz
				if(getParam('modzzz_listing_boonex_events')=='on'){  
					$oEvent = BxDolModule::getInstance('BxEventsModule');

					$this->aCurrent['sorting'] = 'event';

					$this->aCurrent['table'] = 'bx_events_main';
					
					$this->aCurrent['ownFields'] = array('ID', 'Title', 'EntryUri', 'Country', 'City', 'Place', 'EventStart', 'EventEnd', 'ResponsibleID', 'PrimPhoto', 'FansCount', 'Rate', 'Description', 'listing_id');
  
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
  
					$this->aCurrent['restriction']['listing_id'] = array('value' => $iListingId, 'field' => 'listing_id', 'operator' => '=', 'table' => 'bx_events_main'); 
				}else{
					//[end] listing integration - modzzz
					$this->aCurrent['join']['event'] = array(
						'type' => 'inner',
						'table' => 'modzzz_listing_event_main',
						'mainField' => 'id',
						'onField' => 'listing_id',
						'joinFields' => array('id', 'listing_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'event_start','event_end', 'country', 'city', 'state', 'address1', 'zip', 'comments_count', 'author_id'),
					);

					$this->aCurrent['ownFields'] = array();

					$this->aCurrent['restriction']['listing_id'] = array('value' => $iListingId, 'field' => 'listing_id', 'operator' => '=', 'table' => 'modzzz_listing_event_main');  
				}

				$this->sBrowseUrl = "event/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_listing_caption_browse_listing_events', $sTitle);
				break;

           case 'events_main':
 				
				$oMain = $this->getMain();
				$aListingEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iListingId = (int)$aListingEntry['id'];
				$sTitle = $aListingEntry['title'];
 		 	   
				//[begin] boonex event integration - modzzz
				if(getParam('modzzz_listing_boonex_events')=='on'){  
					$oEvent = BxDolModule::getInstance('BxEventsModule');

					$this->aCurrent['sorting'] = 'event';

					$this->aCurrent['table'] = 'bx_events_main';
					
					$this->aCurrent['ownFields'] = array('ID', 'Title', 'EntryUri', 'Country', 'City', 'Place', 'EventStart', 'EventEnd', 'ResponsibleID', 'PrimPhoto', 'FansCount', 'Rate', 'Description', 'listing_id');
  
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
  
					$this->aCurrent['restriction']['listing_id'] = array('value' => 1, 'field' => 'listing_id', 'operator' => '>=', 'table' => 'bx_events_main'); 
				}else{
					//[end] listing integration - modzzz
					$this->aCurrent['join']['event'] = array(
						'type' => 'inner',
						'table' => 'modzzz_listing_event_main',
						'mainField' => 'id',
						'onField' => 'listing_id',
						'joinFields' => array('id', 'listing_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'event_start','event_end', 'country', 'city', 'state', 'address1', 'zip', 'comments_count', 'author_id'),
					);

					$this->aCurrent['ownFields'] = array();

					$this->aCurrent['restriction']['listing_id'] = array('value' => 1, 'field' => 'listing_id', 'operator' => '>=', 'table' => 'modzzz_listing_event_main');  
				}

				$this->sBrowseUrl = "events/browse";
				$this->aCurrent['title'] = _t('_modzzz_listing_caption_browse_all_events');
				break;

			case 'joined':
 
                $iProfileId = $GLOBALS['oBxListingModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
 
                if (!$iProfileId) {

                    $this->isError = true;

                } else {

					$this->aCurrent['join']['fans'] = array(
						'type' => 'inner',
						'table' => 'modzzz_listing_fans',
						'mainField' => 'ID',
						'onField' => 'id_entry',
						'joinFields' => array('id_profile'),
					);
					$this->aCurrent['restriction']['fans'] = array(
						'value' => $iProfileId, 
						'field' => 'id_profile', 
						'operator' => '=', 
						'table' => 'modzzz_listing_fans',
					);
					$this->aCurrent['restriction']['confirmed_fans'] = array(
						'value' => 1, 
						'field' => 'confirmed', 
						'operator' => '=', 
						'table' => 'modzzz_listing_fans',
					);
				}

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/joined/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_listing_caption_browse_by_author_joined_listings') . ' ' . $sValue;

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
  
          case 'employers':
 
                $iProfileId = $GLOBALS['oBxListingModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
 
                if (!$iProfileId) {

                    $this->isError = true;

                } else {

					$this->aCurrent['join']['employees'] = array(
						'type' => 'inner',
						'table' => 'modzzz_listing_employees',
						'mainField' => 'ID',
						'onField' => 'id_entry',
						'joinFields' => array('id_profile'),
					);
					$this->aCurrent['restriction']['employees'] = array(
						'value' => $iProfileId, 
						'field' => 'id_profile', 
						'operator' => '=', 
						'table' => 'modzzz_listing_employees',
					);
					$this->aCurrent['restriction']['confirmed_employees'] = array(
						'value' => 1, 
						'field' => 'confirmed', 
						'operator' => '=', 
						'table' => 'modzzz_listing_employees',
					);
				}

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/employers/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_listing_caption_browse_by_author_employers_listings') . ' ' . $sValue;

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
 
            case 'pending':
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "administration";
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break; 
            case 'my_pending':
                $oMain = $this->getMain();
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break;
            case 'my_expired': 
                $oMain = $this->getMain();
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'expired';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_expired');
                unset($this->aCurrent['rss']);
            break;
             case 'order':  
                $oMain = $this->getMain();
                
				if (isset($_REQUEST['filter'])) 
 					$this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;

				$this->aCurrent['ownFields'] = array('title', 'uri', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'category_id', 'tags', 'comments_count','status','expiry_date');
   
                $this->sBrowseUrl = "browse/order/";
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_orders');
                
				$this->aCurrent['join']['invoices'] = array(
					'type' => 'inner',
					'table' => 'modzzz_listing_invoices',
					'mainField' => 'invoice_no',
					'onField' => 'invoice_no',
					'joinFields' => array('listing_id', 'invoice_no','price','days','package_id','invoice_date','invoice_due_date','invoice_expiry_date','invoice_status'),   
				);	

				$this->aCurrent['join']['orders'] = array(
					'type' => 'inner',
					'table' => 'modzzz_listing_orders',
					'mainField' => 'invoice_no',
					'onField' => 'invoice_no',
					'joinFields' => array('id','order_no','buyer_id','payment_method','order_date','order_status'),   
				); 

				unset($this->aCurrent['restriction']['activeStatus']);  

				unset($this->aCurrent['rss']);  
            break;
            case 'invoice':  
                $oMain = $this->getMain();
   
                if (isset($_REQUEST['filter'])) {
 					$this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
				}
					
				$this->aCurrent['restriction']['invoice_status'] = array( 'value' => 'pending', 'field' => 'invoice_status', 'operator' => '=', 'table' => 'modzzz_listing_invoices'); 
				 
				$this->aCurrent['ownFields'] = array('title', 'uri', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'category_id', 'tags', 'comments_count' );
  
                //$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/invoice/";
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_invoices');
                 
				$this->aCurrent['join']['invoices'] = array(
					'type' => 'inner',
					'table' => 'modzzz_listing_invoices',
					'mainField' => 'id',
					'onField' => 'listing_id',
					'joinFields' => array('id','listing_id', 'invoice_no','price','days','package_id','invoice_status','invoice_date','invoice_due_date','invoice_expiry_date'),   
				);	
				
				unset($this->aCurrent['restriction']['activeStatus']);  
 
				unset($this->aCurrent['rss']);  
            break;
   
            case 'claim':  
                $oMain = $this->getMain();
 
                 if (isset($_REQUEST['filter'])) 
 					$this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
 
				$this->aCurrent['ownFields'] = array('title', 'uri', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'parent_category_id', 'category_id', 'tags', 'comments_count' );
  
                //$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/claim/";
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_claims');
                
				$this->aCurrent['join']['claims'] = array(
					'type' => 'inner',
					'table' => 'modzzz_listing_claim',
					'mainField' => 'id',
					'onField' => 'listing_id',
					'joinFields' => array('id', 'listing_id', 'member_id', 'claim_date', 'assign_date', 'message', 'processed'),   
				);	 
				$this->aCurrent['restriction']['processed'] = array( 'value' => 1,'field' => 'processed','operator' => '!=', 'table' =>'modzzz_listing_claim');

				unset($this->aCurrent['restriction']['activeStatus']);  

				unset($this->aCurrent['rss']);  
            break;
 
           case 'categories':
                $oMain = $this->getMain();
				$aCategory = $oMain->_oDb->getCategoryByUri($sValue); 
                $iCategoryId = $aCategory['id'];
                $sCategoryName = $aCategory['name']; 
                
				$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
   				$this->aCurrent['restriction']['cat'] = array( 'value' => $iCategoryId,'field' => 'parent_category_id','operator' => '=');

                $this->sBrowseUrl = "browse/categories/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_category').' - '.$sCategoryName;
 
				//unset($this->aCurrent['rss']);
            break;
            case 'subcategories': 
                $oMain = $this->getMain(); 
				$aCategory = $oMain->_oDb->getCategoryByUri($sValue); 
                $iCategoryId = $aCategory['id'];
                $sCategoryName = $aCategory['name'];
  
				$this->aCurrent['join']['subcategories'] = array(
					'type' => 'inner',
					'table' => 'modzzz_listing_category',
					'mainField' => 'id',
					'onField' => 'id_entry',
					'joinFields' => array('id_category'),
				);
 
				$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
				$this->aCurrent['restriction']['subcat'] = array('table' => 'modzzz_listing_category', 'value' => $iCategoryId, 'field' => 'id_category', 'operator' => '=');

                $this->sBrowseUrl = "browse/subcategories/$sValue" ;
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_category').' - '.$sCategoryName;
          
				//unset($this->aCurrent['rss']);
            break;
   
            case 'search':
 
                if ($sValue && $sValue!='-')
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');

                if ($sValue2 && $sValue2!='-') 
				   $this->aCurrent['restriction']['parent'] = array('value' => $sValue2,'field' => 'parent_category_id','operator' => '=');
                 
				if ($sValue3 && $sValue3!='-') {
					$this->aCurrent['join']['subcategories'] = array(
						'type' => 'inner',
						'table' => 'modzzz_listing_category',
						'mainField' => 'id',
						'onField' => 'id_entry',
						'joinFields' => array('id_category'),
					);
 
					$this->aCurrent['restriction']['subcat'] = array('table' => 'modzzz_listing_category', 'value' => (int)$sValue3, 'field' => 'id_category', 'operator' => '='); 
				}  
				//$this->aCurrent['restriction']['category'] = array('value' => $sValue3,'field' => 'category_id','operator' => '=');

                if ($sValue4 && $sValue4!='-')  
                    $this->aCurrent['restriction']['city'] = array('value' => $sValue4,'field' => 'city','operator' => '=');
  
				if ($sValue5 && $sValue5!='-')  
                    $this->aCurrent['restriction']['state'] = array('value' => $sValue5,'field' => 'state','operator' => '=');
   
				if($sValue6 && $sValue6!='-')
					$this->aCurrent['restriction']['country'] = array('value' => $sValue6, 'field' => 'country', 'operator' => '=');
 
                $this->sBrowseUrl = 'search/'.($sValue ? $sValue : '-').'/'.($sValue2 ? $sValue2 : '-').'/'.($sValue3 ? $sValue3 : '-').'/'.($sValue4 ? $sValue4 : '-').'/'.($sValue5 ? $sValue5 : '-').'/'.($sValue6 ? $sValue6 : '-');
    
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_search_results');
                
				unset($this->aCurrent['rss']);

				$this->aCurrent['sorting'] = 'search';
 
                break; 

            case 'user':
                $iProfileId = $GLOBALS['oBxListingModule']->_oDb->getProfileIdByNickName ($sValue);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                
				$this->sBrowseUrl = "browse/user/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_modzzz_listing_page_title_browse_by_author');
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

            case 'employer':
                $iProfileId = $GLOBALS['oBxListingModule']->_oDb->getProfileIdByNickName ($sValue);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                
				$this->sBrowseUrl = "browse/employer/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_modzzz_listing_page_title_browse_by_author');
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

                if (bx_get('listing'))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input(bx_get('listing'), BX_TAGS_STRIP), 'field' => '','operator' => 'against');  

                $this->aCurrent['restriction']['owner']['value'] = 0;
 
                $this->sBrowseUrl = "browse/admin";
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_admin_listing');
                break;
 
            case 'tag':
            	$sValue = uri2title($sValue);

                $this->aCurrent['restriction']['tag']['value'] = $sValue;
                $this->sBrowseUrl = "browse/tag/$sValue";
              //freddy modif
			  // $this->aCurrent['title'] = _t('_modzzz_listing_page_title_browse_by_tag') . ' ' . $sValue;
			  
			   $this->aCurrent['title'] = _t('_modzzz_listing_page_title_browse_by_tag') . ' '. '<span style=" text-transform: capitalize; text-align:center;  background-color: #56687A;  box-shadow: 5px 5px 5px rgb(0 0 0 / 30%);
    padding: 5px; display: inline-block; font-size: 14px; font-weight:400; margin-right: 3px; margin-bottom: -10px; overflow: hidden;color:  #fff;
    border: 1px solid #56687A; white-space: normal; text-overflow: ellipsis;">' . $sValue .'</a>' ;
                break;

		   //[begin] - local 
			case 'local_country':          
				$this->aCurrent['restriction']['local_country'] = array('value' => $sValue, 'field' => 'country', 'operator' => '='); 
				$this->sBrowseUrl = "local_country/$sValue";
				
 				$sCountryName = htmlspecialchars_adv( _t($GLOBALS['aPreValues']['Country'][$sValue]['LKey']) );
				
				$sTitleStr = $sCountryName;
 
				if($sValue2){

					$this->sBrowseUrl = "local_country/$sValue/$sValue2";

					$aCategory = $oMain->_oDb->getCategoryByUri($sValue2); 
					$iCategoryId = $aCategory['id'];
					$sCategoryName = $aCategory['name'];
		
					$sTitleStr .= ' - ' . $sCategoryName;

					$this->aCurrent['restriction']['category'] = array('value' => $iCategoryId, 'field' => 'parent_category_id', 'operator' => '='); 
 				}
 				
				$this->aCurrent['title'] = _t('_modzzz_listing_caption_browse_local', $sTitleStr);

				break; 
			case 'local_state':   
				
				$this->aCurrent['restriction']['local_country'] = array('value' => $sValue, 'field' => 'country', 'operator' => '='); 

				$this->aCurrent['restriction']['local_state'] = array('value' => $sValue2, 'field' => 'state', 'operator' => '='); 
 
 				$this->sBrowseUrl = "browse/local_state/$sValue/$sValue2";
				
 				$sCountryName = htmlspecialchars_adv( _t($GLOBALS['aPreValues']['Country'][$sValue]['LKey']) );
				$sStateName = $oMain->_oDb->getStateName($sValue, $sValue2); 
				$sTitleStr = $sCountryName . ' - ' . $sStateName;
 
				if($sValue3){

					$this->sBrowseUrl = "browse/local_state/$sValue/$sValue2/$sValue3";

					$aCategory = $oMain->_oDb->getCategoryByUri($sValue3); 
					$iCategoryId = $aCategory['id'];
					$sCategoryName = $aCategory['name'];
		
					$sTitleStr .= ' - ' . $sCategoryName;

					$this->aCurrent['restriction']['category'] = array('value' => $iCategoryId, 'field' => 'parent_category_id', 'operator' => '='); 
 				}

				$this->aCurrent['title'] = _t('_modzzz_listing_caption_browse_local', $sTitleStr);
				break; 
			case 'ilocal': 
				$sMyCity = '';
				if($iProfileId = getLoggedId()){
					$aProfile = getProfileInfo($iProfileId); 
					$sMyCity = $aProfile['City'];
				}

				if($sMyCity){
					$this->aCurrent['restriction']['city'] = array('value' => $sMyCity, 'field' => 'City', 'operator' => '=');  
				}else{
					$this->aCurrent['restriction']['city'] = array('value' => 'n_o_n_e', 'field' => 'City', 'operator' => '=');  
				}

				$this->sBrowseUrl = "browse/ilocal";
				$this->aCurrent['title'] = _t('_modzzz_listing_caption_browse_area_listings');
				break; 
 
			case 'local':    

				$aProfileInfo = getProfileInfo($oMain->_iProfileId);
  
				if($aProfileInfo['zip']){ 
					$this->aCurrent['restriction']['local'] = array('value' => $sValue, 'field' => 'zip', 'operator' => '='); 
				}elseif($aProfileInfo['City']){ 
					$this->aCurrent['restriction']['local'] = array('value' => $sValue, 'field' => 'city', 'operator' => '='); 
				}else{
					$this->aCurrent['restriction']['local'] = array('value' => 'n_o_n_e', 'field' => 'city', 'operator' => '='); 
				}  
 
				$this->sBrowseUrl = "browse/local/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_listing_caption_browse_local', $sValue);
				break;
			case 'other_local': 
				$this->aCurrent['restriction']['item'] = array('value' => $sValue, 'field' => 'id', 'operator' => '!=');  
				$this->aCurrent['restriction']['local_city'] = array('value' => $sValue2, 'field' => 'city', 'operator' => '='); 
				if($sValue3)
				$this->aCurrent['restriction']['local_state'] = array('value' => $sValue3, 'field' => 'state', 'operator' => '='); 

				$this->sBrowseUrl = "browse/other_local/$sValue/$sValue2/$sValue3";
				$this->aCurrent['title'] = _t('_modzzz_listing_caption_browse_local', $sValue2);
				break; 
			case 'other':
				$sNickName = getNickName($sValue);

				$this->aCurrent['restriction']['other'] = array('value' => $sValue, 'field' => 'author_id', 'operator' => '=');  
				$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/other/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_listing_caption_browse_other', $sNickName);
				break;
  
            case 'recent':
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_browse_recent');
                break;

            case 'top':
                $this->sBrowseUrl = 'browse/top';
                $this->aCurrent['sorting'] = 'top';
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_browse_top_rated');
                break;

            case 'popular':
                $this->sBrowseUrl = 'browse/popular';
                $this->aCurrent['sorting'] = 'popular';
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_browse_popular');
                break;                
            case 'favorite':
 
				$this->aCurrent['join']['favorites'] = array(
					'type' => 'left',
					'table' => 'modzzz_listing_favorites',
					'mainField' => 'id',
					'onField' => 'id_entry',
					'joinFields' => '',
				);	        
 				$iProfileId = $oMain->_iProfileId;
				$iProfileId = ($iProfileId) ? $iProfileId : -999;
				$this->aCurrent['restriction']['favorite'] = array('value' => $iProfileId, 'field' => 'id_profile', 'operator' => '=', 'table' => 'modzzz_listing_favorites');
                $this->sBrowseUrl = 'browse/favorite';
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_browse_favorite');
                break;

            case 'featured':
				$this->aCurrent['sorting'] = 'rand'; 
				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured';
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_browse_featured');
                break;                                

            case 'calendar':
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 00:00:00')", 'field' => 'created', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 23:59:59')", 'field' => 'created', 'operator' => '<=', 'no_quote_value' => true);
                $this->sEventsBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->aCurrent['title'] = _t('_modzzz_listing_page_title_browse_by_day') . sprintf("%04u-%02u-%02u", $sValue, $sValue2, $sValue3);
                break;                                
 
           case 'jobs':
						 
				$oMain = $this->getMain();
				$aBusinessEntry = $oMain->_oDb->getEntryById($sValue);
				$iBusinessId = (int)$aBusinessEntry['id'];
 
				$this->aCurrent['join']['jobs'] = array(
					'type' => 'inner',
					'table' => 'modzzz_jobs_main',
					'mainField' => 'id',
					'onField' => 'company_id',
					'joinFields' => array('id', 'title', 'desc', 'uri', 'created', 'author_id', 'thumb', 'rate',  'category_id','company_id','company_type','role','vacancies','experience','qualification','skills','min_salary','max_salary','salary_type','currency','career_level','job_type','comments_count', 'tags', 'membership_view_filter','post_type', 'currency', 'invoice_no','expiry_date', 'featured_expiry_date', 'country', 'city', 'state', 'address1'),
				);
  
				$this->aCurrent['ownFields'] = array();
  				
				$this->aCurrent['restriction']['business_id'] = array('value' => 'listing', 'field' => 'company_type', 'operator' => '=', 'table' => 'modzzz_jobs_main'); 

				$this->aCurrent['restriction']['company_id'] = array('value' => $iBusinessId, 'field' => 'company_id', 'operator' => '=', 'table' => 'modzzz_jobs_main'); 
				$this->sBrowseUrl = "browse/jobs/$sValue";
				break;
				
				  //Freddy Integration modzzz articles
				
				case 'articles':
						 
				$oMain = $this->getMain();
				$aBusinessEntry = $oMain->_oDb->getEntryById($sValue);
				$iBusinessId = (int)$aBusinessEntry['id'];
 
				$this->aCurrent['join']['articles'] = array(
					'type' => 'inner',
					'table' => 'modzzz_articles_main',
					'mainField' => 'id',
					'onField' => 'company_id',
					'joinFields' => array('id', 'title', 'uri', 'created', 'when', 'author_id',  'company_id' ,'thumb', 'rate', 'country', 'city', 'anonymous', 'snippet', 'desc', 'parent_category_id', 'category_id', 'tags', 'video_embed', 'comments_count'),
				);
  
				$this->aCurrent['ownFields'] = array();
  				
				
				$this->aCurrent['restriction']['company_id'] = array('value' => $iBusinessId, 'field' => 'company_id', 'operator' => '=', 'table' => 'modzzz_articles_main'); 
				$this->sBrowseUrl = "browse/articles/$sValue";
				$this->aCurrent['title'] = _t('modzzz_listing_caption_browse_listing_articles', $sTitle);
				break;
				
				//Fin Freddy Integration modzzz articles
				// FreddyClassified
				   case 'classified':
						 
				$oMain = $this->getMain();
				$aBusinessEntry = $oMain->_oDb->getEntryById($sValue);
				$iBusinessId = (int)$aBusinessEntry['id'];
 
				$this->aCurrent['join']['classified'] = array(
					'type' => 'inner',
					'table' => 'modzzz_classified_main',
					'mainField' => 'id',
					'onField' => 'company_id',
					'joinFields' => array('id', 'title', 'desc', 'uri', 'created', 'author_id', 'thumb', 'rate',  'category_id','price','saleprice','payment_type','categories','currency','comments_count', 'tags','video_embed','membership_view_filter', 'currency', 'invoice_no','expiry_date', 'featured_expiry_date', 'country','zip', 'city', 'state', 'address1','company_type','classified_type'),
				);
  
				$this->aCurrent['ownFields'] = array();
  				
				$this->aCurrent['restriction']['business_id'] = array('value' => 'listing', 'field' => 'company_type', 'operator' => '=', 'table' => 'modzzz_classified_main'); 

				$this->aCurrent['restriction']['company_id'] = array('value' => $iBusinessId, 'field' => 'company_id', 'operator' => '=', 'table' => 'modzzz_classified_main'); 
				$this->sBrowseUrl = "browse/classified/$sValue";
				break;
				
		  

           case 'deals':
						 
				$oMain = $this->getMain();
				$aBusinessEntry = $oMain->_oDb->getEntryById($sValue);
				$iBusinessId = (int)$aBusinessEntry['id'];
  
				$this->aCurrent['join']['deals'] = array(
					'type' => 'inner',
					'table' => 'modzzz_deals_main',
					'mainField' => 'id',
					'onField' => 'store_id',
					'joinFields' => array('id', 'title', 'uri', 'created', 'author_id', 'thumb', 'rate', 'fans_count', 'employees_count', 'price', 'seen', 'sale_end', 'company_type', 'store_id', 'website', 'membership_view_filter'),
				);
  
				$this->aCurrent['ownFields'] = array();

 				$this->aCurrent['restriction']['business_id'] = array('value' => 'listing', 'field' => 'company_type', 'operator' => '=', 'table' => 'modzzz_deals_main'); 

				$this->aCurrent['restriction']['store_id'] = array('value' => $iBusinessId, 'field' => 'store_id', 'operator' => '=', 'table' => 'modzzz_deals_main'); 
				$this->sBrowseUrl = "browse/deals/$sValue";
				break;

            case 'coupons':
						 
				$oMain = $this->getMain();
				$aBusinessEntry = $oMain->_oDb->getEntryById($sValue);
				$iBusinessId = (int)$aBusinessEntry['id'];
  
				$this->aCurrent['join']['coupons'] = array(
					'type' => 'inner',
					'table' => 'modzzz_coupons_main',
					'mainField' => 'id',
					'onField' => 'business_id',
					'joinFields' => array('id', 'title', 'uri', 'thumb', 'created', 'author_id', 'barcode', 'rate',  'price', 'expire_date', 'club_id', 'business_id', 'business_name', 'business_website', 'membership_view_filter', 'terms', 'desc', 'print_count', 'prints_allowed', 'views','tags','category_id','business_name','business_desc','business_country','business_city','business_address','business_state','business_zip','business_website','business_email','business_telephone'),
				);
    
				$this->aCurrent['ownFields'] = array();
  
				$this->aCurrent['restriction']['business_id'] = array('value' => $iBusinessId, 'field' => 'business_id', 'operator' => '=', 'table' => 'modzzz_coupons_main'); 
				$this->sBrowseUrl = "browse/coupons/$sValue";
				break;

			case 'reviews':
 			    
				$aEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iId = (int)$aEntry['id'];
				$sTitle = $aEntry['title'];
				
					 //[begin] Jobs integration - modzzz
				if(getParam('modzzz_listing_jobs_active')=='on'){ 
					$oJobs = BxDolModule::getInstance('BxJobsModule');

					$this->aCurrent['sorting'] = 'modzzz_jobs';

					$this->aCurrent['table'] = 'modzzz_jobs_main';
					
					$this->aCurrent['ownFields'] = array('id', 'title', 'desc', 'uri', 'created', 'author_id', 'thumb', 'rate',  'category_id','company_type','role','vacancies','experience','qualification','skills','min_salary','max_salary','salary_type','currency','career_level','job_type','comments_count', 'tags', 'membership_view_filter','post_type', 'currency', 'invoice_no','expiry_date', 'featured_expiry_date', 'country', 'city', 'state', 'address1');
    
					$this->aCurrent['join'] = array(
						'profile' => array(
								'type' => 'left',
								'table' => 'Profiles',
								'mainField' => 'author_id',
								'onField' => 'ID',
								'joinFields' => array('NickName'),
						),
					);

					$this->aCurrent['restriction']['public']['field'] = 'allow_view_job_to';
		  
					$this->aCurrent['restriction']['company_id'] = array('value' => $iId, 'field' => 'company_id', 'operator' => '=', 'table' => 'modzzz_jobs_main');
					
					$this->sBrowseUrl = "news/browse/$sValue"; 

				}else{

					$this->aCurrent['join']['review'] = array(
					'type' => 'inner',
					'table' => 'modzzz_listing_review_main',
					'mainField' => 'id',
					'onField' => 'listing_id',
					'joinFields' => array('id', 'listing_id', 'title', 'uri', 'created', 'author_id', 'thumb', 'desc', 'rate', 'comments_count'),
				); 

				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['listing_id'] = array('value' => $iId, 'field' => 'listing_id', 'operator' => '=', 'table' => 'modzzz_listing_review_main'); 

				$this->sBrowseUrl = "review/browse/$sValue";
				}
			
				$this->aCurrent['title'] = _t('_modzzz_listing_caption_browse_listing_reviews', $sTitle);
				break;    

   
            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_modzzz_listing');
                break;

            default:
                $this->isError = true;
        }
 
		if(in_array($sMode, array('events','events_main')) && getParam('modzzz_listing_boonex_events')=='on'){
			
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

		}elseif(in_array($sMode, array('news','view_news')) && getParam('modzzz_listing_modzzz_mnews')=='on'){
			
			$oNews = BxDolModule::getInstance('BxMNewsModule');

			$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_mnews_perpage_browse');

			if (isset($this->aCurrent['rss']))
				$this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oNews->_oConfig->getBaseUri() . $this->sBrowseUrl;

			if (bx_get('rss')) {
				$this->aCurrent['ownFields'][] = 'desc';
				$this->aCurrent['ownFields'][] = 'created';
				$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_mnews_max_rss_num');
			}

			bx_import('Voting', $oNews->_aModule);
			$oVotingView = new BxMNewsVoting ('modzzz_mnews', 0);
			$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

			$this->sFilterName = 'filter';
 
		}else{

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

			$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_listing_perpage_browse');

			if (isset($this->aCurrent['rss']))
				$this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . $this->sBrowseUrl;

			if (isset($_REQUEST['rss']) && $_REQUEST['rss']) {
				$this->aCurrent['ownFields'][] = 'desc';
				$this->aCurrent['ownFields'][] = 'created';
				$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_listing_max_rss_num');
			}

			bx_import('Voting', $oMain->_aModule);
			$oVotingView = new BxListingVoting ('modzzz_listing', 0);
			$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

			$this->sFilterName = 'filter';
		}

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
			$aSql['order'] = " ORDER BY `modzzz_listing_main`.`created` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'top') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_listing_main`.`rate` DESC, `modzzz_listing_main`.`rate_count` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'popular') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_listing_main`.`views` DESC";
			return $aSql; 
		} elseif ($this->aCurrent['sorting'] == 'search') {
 
			//for search results, return featured listings first while randomizing all results
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_listing_main`.`featured` DESC, rand()";
			return $aSql; 
			
		//freddy  28/10/2016 [begin] articles integration - modzzz
		} elseif ($this->aCurrent['sorting'] == 'modzzz_articles') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_articles_main`.`created` DESC";
			return $aSql;
		//[end] articles  integration - modzzz
		
		//freddy  28/10/2016 [begin] jobs integration - modzzz
		} elseif ($this->aCurrent['sorting'] == 'modzzz_jobs') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_jobs_main`.`created` DESC";
			return $aSql;
		//[end] jobs  integration - modzzz
		
		//freddy  28/10/2016 [begin] classified integration - modzzz
		} elseif ($this->aCurrent['sorting'] == 'modzzz_classified') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_classified_main`.`created` DESC";
			return $aSql;
		//[end] classified  integration - modzzz

		//[begin] news integration - modzzz
		} elseif ($this->aCurrent['sorting'] == 'modzzz_news') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_news_main`.`created` DESC";
			return $aSql;
		//[end] news integration - modzzz
		

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
        return BxDolModule::getInstance('BxListingModule');
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
            $s = $this->addCustomParts();
            foreach ($aData as $aValue) {
                $s .= $this->displayOrdersSearchUnit($sType, $aValue);
            }

            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));

			return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));    
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
            $s = $this->addCustomParts();
            foreach ($aData as $aValue) {
                $s .= $this->displayClaimSearchUnit($sType, $aValue);
            }

            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));

			return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));    
 
        }
        return '';
    }

    function displayClaimSearchUnit ($sType, $aData) {
        $oMain = $this->getMain();
	 
		return $oMain->_oTemplate->claim_unit($aData, $this->sUnitTemplate, $this->oVotingView); 
	}
 
	//coupons etc.
    function displaySubProfileResultBlock ($sType) { 
        $aData = $this->getSearchData();  
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $s .= $this->addCustomParts();
            foreach ($aData as $aValue) {
                $s .= $this->displaySubProfileSearchUnit($sType, $aValue);
            }
             
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));

			return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));  
        }
        return '';
    }
  
    function displaySubProfileSearchUnit ($sType, $aData) {
        $oMain = $this->getMain();
		
		$sResult = '';

		switch($sType){ 
 
			////// freddy remplacement de News par Classified
			case 'news':
 
 				if(getParam('modzzz_listing_classified_active')=='on'){  
					$oClassified = BxDolModule::getInstance('BxClassifiedModule');
					bx_import('Voting', $oClassified->_aModule);
					$oVotingView = new BxClassifiedVoting ('modzzz_classified', 0);
					$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
	 
					$sResult = $oClassified->_oTemplate->unit($aData, 'unit', $this->oVotingView); 
				}else{
					bx_import('NewsVoting', $oMain->_aModule);
					$oVotingView = new BxListingNewsVoting ('modzzz_listing_news', 0);
					$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
	 
					$sResult = $oMain->_oTemplate->news_unit($aData, 'news_unit', $this->oVotingView); 
				}
		
			break;
			case 'event':
 				//[begin] event integration - modzzz
				if(getParam('modzzz_listing_boonex_events')=='on'){  
					$oEvents = BxDolModule::getInstance('BxEventsModule');
					bx_import('Voting', $oEvents->_aModule);
					$oVotingView = new BxEventsVoting ('bx_events', 0);
					$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
	 
					$sResult = $oEvents->_oTemplate->unit($aData, 'unit', $this->oVotingView); 
				
 				//[end] event integration - modzzz
				}else{
					bx_import('EventVoting', $oMain->_aModule);
					$oVotingView = new BxListingEventVoting ('modzzz_listing_event', 0);
					$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
	 
					$sResult = $oMain->_oTemplate->event_unit($aData, 'event_unit', $this->oVotingView); 
				}
			break;
			case 'deals':
 				$oDeals = BxDolModule::getInstance('BxDealsModule');

 				modzzz_deals_import('Voting');
				$oVotingView = new BxDealsVoting ('modzzz_deals', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oDeals->_oTemplate->unit($aData, 'unit', $this->oVotingView); 
			break;  
			case 'coupons':
				$oCoupons = BxDolModule::getInstance('BxCouponsModule');
				$sResult = $oCoupons->_oTemplate->unit($aData, 'unit', $this->oVotingView); 
			break;  
			case 'jobs':

 				modzzz_jobs_import('Voting'); 
				$oVotingView = new BxJobsVoting ('modzzz_jobs', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$oJobs = BxDolModule::getInstance('BxJobsModule');
				$sResult = $oJobs->_oTemplate->unit($aData, 'unit', $this->oVotingView); 
			break; 
			
			//Freddy Classified
			case 'classified':

 				modzzz_classified_import('Voting'); 
				$oVotingView = new BxClassifiedVoting ('modzzz_classified', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$oClassified = BxDolModule::getInstance('BxClassifiedModule');
				$sResult = $oClassified->_oTemplate->unit($aData, 'unit', $this->oVotingView); 
			break; 
			
			// Freddy modzzz articles
			case 'articles':

 				modzzz_articles_import('Voting'); 
				$oVotingView = new BxArticlesVoting ('modzzz_articles', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$oArticles = BxDolModule::getInstance('BxArticlesModule');
				$sResult = $oArticles->_oTemplate->unit($aData, 'unit', $this->oVotingView); 
			break; 
			//Fin Freddy modzzz articles

			case 'review':
			if(getParam('modzzz_listing_jobs_active')=='on'){  
					$oJobs = BxDolModule::getInstance('BxJobsModule');
					bx_import('Voting', $oJobs->_aModule);
					$oVotingView = new BxJobsVoting ('modzzz_jobs', 0);
					$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
	 
					$sResult = $oJobs->_oTemplate->unit($aData, 'unit', $this->oVotingView); 
				}else{
				bx_import('ReviewVoting', $oMain->_aModule);
				$oVotingView = new BxListingReviewVoting ('modzzz_listing_review', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

				$sResult = $oMain->_oTemplate->review_unit($aData, 'review_unit', $this->oVotingView); 
				}
		  	break;	

		}

		return $sResult;
	}
 
	function processing () {
 
		if($this->aCurrent['join']['review']['table']=='modzzz_listing_review_main'){
			$sCode = $this->displaySubProfileResultBlock('review');  
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
