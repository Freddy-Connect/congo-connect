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

class BxEventsSearchResult extends BxDolTwigSearchResult  {

    var $aCurrent = array(
        'name' => 'bx_events',
        'title' => '_bx_events_caption_browse',
        'table' => 'bx_events_main',
 
		'ownFields' => array('ID', 'Title', 'EntryUri', 'Zip','Country', 'City', 'State', 'Place','Street', 'EventStart',  'EventEnd', 'ResponsibleID', 'PrimPhoto', 'icon', 'FansCount', 'Rate', 'Categories', 'Tags', 'CommentsCount', 'Views', 'Description','VideoEmbed', 'ParticipantsInfo', 'EventMembershipViewFilter', 'currency', 'allow_join_after_start', 'invoice_no','expiry_date','featured_expiry_date','OrganizerName','OrganizerPhone','OrganizerEmail','OrganizerWebsite','allow_view_event_to','Parent', 'listing_id','group_id','company_type'),
  
		//'ownFields' => array('ID', 'Title', 'EntryUri', 'Country', 'City', 'Place', 'EventStart', 'ResponsibleID', 'PrimPhoto', 'FansCount', 'Rate'),
        'searchFields' => array('Title', 'Description', 'City', 'Place', 'Tags', 'Categories'),
        'join' => array(
            'profile' => array(
                    'type' => 'left',
                    'table' => 'Profiles',
                    'mainField' => 'ResponsibleID',
                    'onField' => 'ID',
                    'joinFields' => array('NickName'),
            ),
        ),
        'restriction' => array(
            'activeStatus' => array('value' => 'approved', 'field'=>'Status', 'operator'=>'='),
            'owner' => array('value' => '', 'field' => 'ResponsibleID', 'operator' => '='),
            'tag' => array('value' => '', 'field' => 'Tags', 'operator' => 'against'),
            'category' => array('value' => '', 'field' => 'Category', 'operator' => '='),
            'country' => array('value' => '', 'field' => 'Country', 'operator' => '='),
            'city' => array('value' => '', 'field' => 'City', 'operator' => '='),
            'public' => array('value' => '', 'field' => 'allow_view_event_to', 'operator' => '='),
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
                'Title' => 'Title',
                'DateTimeUTS' => 'Date',
                'Desc' => 'Description',
                'Photo' => '',
            ),
        ),
        'ident' => 'ID'
    );
 
	function BxEventsSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '', $sValue4 = '', $sValue5 = '', $sValue6 = '', $sValue7 = '' ) {
 
		$sValue = process_db_input($sValue);
		$sValue2 = process_db_input($sValue2);
		$sValue3 = process_db_input($sValue3);
		$sValue4 = process_db_input($sValue4);
		$sValue5 = process_db_input($sValue5);
		$sValue6 = process_db_input($sValue6);
		$sValue7 = process_db_input($sValue7);
 
        $oMain = $this->getMain();
 
		//[begin] band integration modzzz
		if( getParam('modzzz_bands_events_hide')=='on' && !in_array($sMode, array('admin','pending','my_pending','user')) ){ 
			$this->aCurrent['restriction']['band_event_hide'] = array('value' => 1, 'field' => 'band_id', 'operator' => '<');
		}
		if(getParam('modzzz_bands_boonex_events')=='on'){
			$this->aCurrent['ownFields'][] = 'band_id';
		} 
		//[end] band integration modzzz

		//[begin] listing integration modzzz
		if( getParam('modzzz_listing_events_hide')=='on' && !in_array($sMode, array('admin','pending','my_pending','user')) ){ 
			$this->aCurrent['restriction']['listing_event_hide'] = array('value' => 1, 'field' => 'listing_id', 'operator' => '<');
		}
		if(getParam('modzzz_listing_boonex_events')=='on'){
			$this->aCurrent['ownFields'][] = 'listing_id';
		} 
		//[end] listing integration modzzz

		//[begin] club integration modzzz
		if( getParam('modzzz_club_events_hide')=='on' && !in_array($sMode, array('admin','pending','my_pending')) ){ 
			$this->aCurrent['restriction']['club_event_hide'] = array('value' => 1, 'field' => 'club_id', 'operator' => '<');
		}
		if(getParam('modzzz_club_boonex_events')=='on'){
			$this->aCurrent['ownFields'][] = 'club_id';
		}
		//[end] club integration modzzz

		//[begin] groups integration modzzz
		if(getParam('bx_groups_events_hide')=='on' && !in_array($sMode, array('admin','pending','my_pending')) ){  
			$this->aCurrent['restriction']['groups_event_hide'] = array('value' => 1, 'field' => 'group_id', 'operator' => '<');
		}
		if(getParam('bx_groups_boonex_events')=='on'){
			$this->aCurrent['ownFields'][] = 'group_id';
		}
		//[end] groups integration modzzz

		//[begin] community integration modzzz
		if(getParam('modzzz_community_events_hide')=='on' && !in_array($sMode, array('admin','pending','my_pending')) ){  
			$this->aCurrent['restriction']['community_event_hide'] = array('value' => 1, 'field' => 'community_id', 'operator' => '<');
		}
		if(getParam('modzzz_community_events_hide')=='on'){
			$this->aCurrent['ownFields'][] = 'community_id';
		}
		//[end] community integration modzzz

		//[begin] charity integration modzzz
		if(getParam('modzzz_charity_events_hide')=='on' && !in_array($sMode, array('admin','pending','my_pending')) ){  
			$this->aCurrent['restriction']['charity_event_hide'] = array('value' => 1, 'field' => 'charity_id', 'operator' => '<');
		}
		if(getParam('modzzz_charity_events_hide')=='on'){
			$this->aCurrent['ownFields'][] = 'charity_id';
		}
		//[end] charity integration modzzz
 
		//[begin] location integration modzzz
		if( getParam('modzzz_location_events_hide')=='on' && !in_array($sMode, array('admin','pending','my_pending')) ){  
			$this->aCurrent['restriction']['location_event_hide'] = array('value' => 1, 'field' => 'location_id', 'operator' => '<');
		}
		if(getParam('modzzz_location_boonex_events')=='on'){
			$this->aCurrent['ownFields'][] = 'location_id';
		}
		//[end] location integration modzzz

		//[begin] school integration modzzz
		if( getParam('modzzz_schools_events_hide')=='on' && !in_array($sMode, array('admin','pending','my_pending')) ){  
			$this->aCurrent['restriction']['school_event_hide'] = array('value' => 1, 'field' => 'school_id', 'operator' => '<');
		}
		if(getParam('modzzz_schools_boonex_events')=='on'){
			$this->aCurrent['ownFields'][] = 'school_id';
		}
		//[end] school integration modzzz

        switch ($sMode) {

            case 'pending':
                if (false !== bx_get('bx_events_filter'))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input(bx_get('bx_events_filter'), BX_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "administration";
                $this->aCurrent['title'] = _t('_bx_events_pending_approval');
                unset($this->aCurrent['rss']);
            break;

            case 'my_pending':                
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_bx_events_pending_approval');
                unset($this->aCurrent['rss']);
            break;
            case 'my_expired': 
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'expired';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_bx_events_page_title_expired');
                unset($this->aCurrent['rss']);
            break;
            case 'order':  
                
				if (isset($_REQUEST['bx_events_filter'])) 
 					$this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;

				$this->aCurrent['ownFields'] = array('Title', 'EntryUri', 'ResponsibleID', 'PrimPhoto', 'Rate', 'Country', 'City', 'Description', 'Categories', 'Tags', 'CommentsCount','Status','expiry_date');
   
                $this->sBrowseUrl = "browse/order/";
                $this->aCurrent['title'] = _t('_bx_events_page_title_orders');
                
				$this->aCurrent['join']['invoices'] = array(
					'type' => 'inner',
					'table' => 'bx_events_invoices',
					'mainField' => 'invoice_no',
					'onField' => 'invoice_no',
					'joinFields' => array('event_id', 'invoice_no','price','days','package_id','invoice_date','invoice_due_date','invoice_expiry_date','invoice_status'),   
				);	

				$this->aCurrent['join']['orders'] = array(
					'type' => 'inner',
					'table' => 'bx_events_orders',
					'mainField' => 'invoice_no',
					'onField' => 'invoice_no',
					'joinFields' => array('id','order_no','buyer_id','payment_method','order_date','order_status'),   
				); 

				unset($this->aCurrent['restriction']['activeStatus']);  

				unset($this->aCurrent['rss']);  
            break;
            case 'invoice':  

				$this->bInvoiceUnit = true;
  
                if (isset($_REQUEST['bx_events_filter'])) {
 					$this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
				}
					
				$this->aCurrent['restriction']['invoice_status'] = array( 'value' => 'pending', 'field' => 'invoice_status', 'operator' => '=', 'table' => 'bx_events_invoices'); 
				 
				$this->aCurrent['ownFields'] = array('Title', 'EntryUri', 'ResponsibleID', 'PrimPhoto', 'Rate', 'Country', 'City', 'Description', 'Categories', 'Tags', 'CommentsCount' );
  
                //$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/invoice/";
                $this->aCurrent['title'] = _t('_bx_events_page_title_invoices');
                 
				$this->aCurrent['join']['invoices'] = array(
					'type' => 'inner',
					'table' => 'bx_events_invoices',
					'mainField' => 'ID',
					'onField' => 'event_id',
					'joinFields' => array('id','event_id', 'invoice_no','price','days','package_id','invoice_status','invoice_date','invoice_due_date','invoice_expiry_date'),   
				);	
				
				unset($this->aCurrent['restriction']['activeStatus']);  
 
				unset($this->aCurrent['rss']);  
            break;
 
            case 'search':
				
				//if ($sValue8)
				//	$this->aCurrent['restriction']['public']['value'] = BX_DOL_PG_ALL ;
		 
/*
                if ($sValue)
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');

                if ($sValue2)
                    if (is_array($sValue2)) {
                        $this->aCurrent['restriction']['country'] = array('value' => $sValue2, 'field' => 'Country', 'operator' => 'in');
                    } else {
                        $this->aCurrent['restriction']['country']['value'] = $sValue2;
                    }

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $sValue2 = $GLOBALS['MySQL']->unescape($sValue2);
                $this->sBrowseUrl = "search/$sValue/" . (is_array($sValue2) ? implode(',',$sValue2) : $sValue2);
                $this->aCurrent['title'] = _t('_bx_events_caption_search_results') . ' ' . (is_array($sValue2) ? implode(', ',$sValue2) : $sValue2) . ' ' . $sValue;
                unset($this->aCurrent['rss']);
                break;
*/
 
				if ($sValue){
					$this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');   
				}
 
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

						$this->aCurrent['restriction']['category_type'] = array('value' => 'bx_events', 'field' => 'type', 'operator' => '=', 'table' => 'sys_categories');  
		
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

				if ($sValue4){
					$this->aCurrent['restriction']['state'] = array('value' => $sValue4,'field' => 'State','operator' => '='); 
				}

				if ($sValue5){
					$this->aCurrent['restriction']['city'] = array('value' => $sValue5,'field' => 'City','operator' => '='); 
				} 

				if ($sValue6)
					$this->aCurrent['restriction']['start'] = array('value' => $sValue6,'field' => 'EventStart','operator' => '>=');

				if ($sValue7)
					$this->aCurrent['restriction']['end'] = array('value' => $sValue7,'field' => 'EventEnd','operator' => '<=');
			 
				$sValue = $GLOBALS['MySQL']->unescape($sValue);
				$sValue2 = $GLOBALS['MySQL']->unescape($sValue2);
				$sValue3 = $GLOBALS['MySQL']->unescape($sValue3);
				$sValue4 = $GLOBALS['MySQL']->unescape($sValue4);
				$sValue5 = $GLOBALS['MySQL']->unescape($sValue5);
				$sValue6 = $GLOBALS['MySQL']->unescape($sValue6);
				$sValue7 = $GLOBALS['MySQL']->unescape($sValue7);

				 
				$this->sBrowseUrl = "search/$sValue/" . (is_array($sValue2) ? implode(',',$sValue2) : $sValue2) . "$sValue3/$sValue4/$sValue5/$sValue6/$sValue7/";
				$this->aCurrent['title'] = _t('_bx_events_caption_search_results') . ' ' . (is_array($sValue2) ? implode(', ',$sValue2) : $sValue2) . ' ' . $sValue;
				unset($this->aCurrent['rss']);
				break;
  
            case 'user_short':

				$this->sUnitTemplate = 'unit_short';

            case 'user':

				$sValue = ($sValue) ? $sValue : getNickName($oMain->_iProfileId);

                $iProfileId = $GLOBALS['oBxEventsModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of events
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/user/$sValue";
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_by_author') . ' ' . $sValue;
                if (bx_get('rss')) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['PrimPhoto']) {
                        $a = array ('ID' => $aData['ResponsibleID'], 'Avatar' => $aData['PrimPhoto']);
                        $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    } 
                }

				$this->aCurrent['restriction']['activeStatus'] = array('value' => array('approved','past'), 'field'=>'Status', 'operator'=>'in');
	 
                break;

            case 'joined_short':

				$this->sUnitTemplate = 'unit_short';

            case 'joined':

				//$sValue = ($sValue) ? $sValue : getNickName($oMain->_iProfileId);
 
                $iProfileId = $GLOBALS['oBxEventsModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                

                if (!$iProfileId) {

                    $this->isError = true;

                } else {

					$this->aCurrent['join']['fans'] = array(
						'type' => 'inner',
						'table' => 'bx_events_participants',
						'mainField' => 'ID',
						'onField' => 'id_entry',
						'joinFields' => array('id_profile'),
					);
					$this->aCurrent['restriction']['fans'] = array(
						'value' => $iProfileId, 
						'field' => 'id_profile', 
						'operator' => '=', 
						'table' => 'bx_events_participants',
					);
					$this->aCurrent['restriction']['confirmed_fans'] = array(
						'value' => 1, 
						'field' => 'confirmed', 
						'operator' => '=', 
						'table' => 'bx_events_participants',
					);
				}

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/joined/$sValue";
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_by_author_joined_events') . ' ' . $sValue;

                if (bx_get('rss')) {
                    $aData = getProfileInfo($iProfileId);
                    if ($aData['Avatar']) {
                        $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
                        $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
                        if (!$aImage['no_image'])
                            $this->aCurrent['rss']['image'] = $aImage['file'];
                    } 
                }

				$this->aCurrent['restriction']['activeStatus'] = array('value' => array('approved','past'), 'field'=>'Status', 'operator'=>'in'); 

                break;

         case 'upcoming_group_events':
  
                $iProfileId = $GLOBALS['oBxEventsModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                

                if (!$iProfileId) {

                    $this->isError = true;

                } else {

					$this->aCurrent['join']['group_events'] = array(
						'type' => 'inner',
						'table' => 'bx_groups_main',
						'mainField' => 'group_id',
						'onField' => 'id',
						'joinFields' => array('allow_view_group_to'),
					);

					$this->aCurrent['join']['fans'] = array(
						'type' => 'inner',
						'mainTable' => 'bx_groups_main',
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

				$this->aCurrent['restriction']['upcoming'] = array('value' => time(), 'field' => 'EventEnd', 'operator' => '>');
                $this->aCurrent['sorting'] = 'upcoming';

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/my_group_events/$sValue";
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_by_author_joined_events') . ' ' . $sValue;

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
                if (bx_get('bx_events_filter'))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input(bx_get('bx_events_filter'), BX_TAGS_STRIP), 'field' => '','operator' => 'against');                
                //$this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/admin";
                $this->aCurrent['title'] = _t('_bx_events_admin_events');
                break;

            case 'category':
                $this->aCurrent['join']['category'] = array(
                    'type' => 'inner',
                    'table' => 'sys_categories',
                    'mainField' => 'ID',
                    'onField' => 'ID',
                    'joinFields' => '',
                );
				$this->aCurrent['restriction']['category_type'] = array('value' => 'bx_events', 'field' => 'type', 'operator' => '=', 'table' => 'sys_categories'); 

                $this->aCurrent['restriction']['category']['value'] = $sValue;                
                $this->aCurrent['restriction']['category']['table'] = 'sys_categories';
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/category/" . title2uri($sValue);
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_by_category') . ' ' . $sValue;
                break;

            case 'tag':
                $this->aCurrent['restriction'][$sMode]['value'] = $sValue;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/$sMode/" . title2uri($sValue);
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_by_'.$sMode) . ' ' . $sValue;
                break;

            case 'country':            
                $this->aCurrent['restriction'][$sMode]['value'] = $sValue;
                $this->sBrowseUrl = "browse/$sMode/$sValue";
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_by_'.$sMode) . ' ' . $sValue;
                break;

            case 'city':            
                $this->aCurrent['restriction'][$sMode]['value'] = $sValue;
                $this->sBrowseUrl = "browse/$sMode/$sValue";
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_by_'.$sMode) . ' ' . $sValue;
                break;

            case 'upcoming':
                //$this->aCurrent['restriction']['upcoming'] = array('value' => time(), 'field' => 'EventStart', 'operator' => '>');
				$this->aCurrent['restriction']['upcoming'] = array('value' => time(), 'field' => 'EventEnd', 'operator' => '>');
                $this->aCurrent['sorting'] = 'upcoming';
                $this->sBrowseUrl = 'browse/upcoming';
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_upcoming');
                break;

            case 'recurring':
 
				$this->aCurrent['restriction']['isrecurring'] = array('value' => $sValue, 'field' => 'Parent', 'operator' => '=', 'no_quote_value' => true, 'concat' => 'or', 'value2' => $sValue, 'field2' => 'ID', 'operator2' => '=', 'no_quote_value2' => true);
				
				$this->aCurrent['restriction']['previous'] = array('value' => $sValue2, 'field' => 'EventEnd', 'operator' => '<', 'no_quote_value' => true);

				//$this->aCurrent['restriction']['activeStatus']['value'] = 'expired';

                $this->aCurrent['sorting'] = 'past';
                $this->sBrowseUrl = "browse/recurring/$sValue/$sValue2";
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_recurring');
                break;

            case 'past':
                $this->aCurrent['restriction']['activeStatus']['value'] = 'past';
                $this->aCurrent['sorting'] = 'past';
                $this->sBrowseUrl = 'browse/past';
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_past');
                break;

			//[begin] - ultimate events mod from modzzz  
			case 'local_country':         
				$this->aCurrent['restriction']['local_country'] = array('value' => $sValue, 'field' => 'Country', 'operator' => '='); 
				$this->sBrowseUrl = "browse/local_country/$sValue";
				$this->aCurrent['title'] = _t('_bx_events_caption_browse_local', $sValue);
				break; 
			case 'local_state':             
				$this->aCurrent['restriction']['local_state'] = array('value' => $sValue, 'field' => 'State', 'operator' => '='); 
				$this->sBrowseUrl = "browse/local_state/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_bx_events_caption_browse_local', $sValue);
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

						$this->aCurrent['restriction']['country'] = array('value' => $aProfile['Country'], 'field' => 'Country', 'operator' => '=' ); 

						$this->aCurrent['restriction']['city'] = array('value' => $aProfile['City'], 'field' => 'City', 'operator' => '=' ); 
					}
 
					if(!$bFiltered){

						$this->aCurrent['restriction']['country'] = array('value' => $aProfile['Country'], 'field' => 'Country', 'operator' => '=' ); 
						
						$sStateField = getParam('bx_events_state_field'); 
						if($aProfile[$sStateField]){
							$this->aCurrent['restriction']['state'] = array('value' => $aProfile[$sStateField], 'field' => 'State', 'operator' => '='); 
						}
					}

					$this->aCurrent['restriction']['author'] = array('value' => $oMain->_iProfileId, 'field' => 'ResponsibleID', 'operator' => '!=' );  

				}else{  
					$this->aCurrent['restriction']['city'] = array('value' => '-not-set-', 'field' => 'city', 'operator' => '=' );  
				}
 
				$this->sBrowseUrl = "browse/ilocal";
				$this->aCurrent['title'] = _t('_bx_events_caption_browse_my_local');
				break;


			case 'local': 
				$this->aCurrent['restriction']['local'] = array('value' => $sValue, 'field' => 'City', 'operator' => '='); 
 
				$this->sBrowseUrl = "browse/local/$sValue";
				$this->aCurrent['title'] = _t('_bx_events_caption_browse_local', $sValue);
				break;
			case 'other_local': 
				$this->aCurrent['restriction']['item'] = array('value' => $sValue, 'field' => 'ID', 'operator' => '!=');  
				$this->aCurrent['restriction']['local_city'] = array('value' => $sValue2, 'field' => 'City', 'operator' => '='); 
				if($sValue3)
				$this->aCurrent['restriction']['local_state'] = array('value' => $sValue3, 'field' => 'State', 'operator' => '='); 

				$this->sBrowseUrl = "browse/other_local/$sValue/$sValue2/$sValue3";
				$this->aCurrent['title'] = _t('_bx_events_caption_browse_local', $sValue2);
				break;  
			case 'other':
				$sNickName = getNickName($sValue);

				$this->aCurrent['restriction']['other'] = array('value' => $sValue, 'field' => 'ResponsibleID', 'operator' => '=');  
				$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'ID', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/other/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_bx_events_caption_browse_other', $sNickName);
				break;
			//[end] - ultimate events mod from modzzz 

            case 'recent':
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_recently_added');
                break;

            case 'top_short':

				$this->sUnitTemplate = 'unit_short';

            case 'top':
                $this->sBrowseUrl = 'browse/top';
                $this->aCurrent['sorting'] = 'top';
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_top_rated');
                break;

            case 'popular_short':

				$this->sUnitTemplate = 'unit_short';

            case 'popular':
                $this->sBrowseUrl = 'browse/popular';
                $this->aCurrent['sorting'] = 'popular';
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_popular');
                break;                

            case 'featured':
                $this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'Featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured';
                $this->aCurrent['title'] = _t('_bx_events_caption_browse_featured');
                break;                                
 
            case 'calendar':
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 00:00:00')", 'field' => 'EventEnd', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 23:59:59')", 'field' => 'EventStart', 'operator' => '<=', 'no_quote_value' => true);
                $this->sBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";

                $this->aCurrent['title'] = _t('_bx_events_caption_browse_by_day')
                    . getLocaleDate( strtotime("{$sValue}-{$sValue2}-{$sValue3}"), BX_DOL_LOCALE_DATE_SHORT);
                break;
 
            case 'sponsors':
						 
				$oMain = $this->getMain();
				$aEventEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iEventId = (int)$aEventEntry['ID'];
				$sTitle = $aEventEntry['Title'];
 
				$this->aCurrent['join']['sponsor'] = array(
					'type' => 'inner',
					'table' => 'bx_events_sponsor_main',
					'mainField' => 'ID',
					'onField' => 'event_id',
					'joinFields' => array('id', 'event_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
				);

				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['event_id'] = array('value' => $iEventId, 'field' => 'event_id', 'operator' => '=', 'table' => 'bx_events_sponsor_main'); 
				$this->sBrowseUrl = "sponsor/browse/$sValue";
				$this->aCurrent['title'] = _t('_bx_events_caption_browse_event_sponsors', $sTitle);
				break; 
				
            case 'venues':
						 
				$oMain = $this->getMain();
				$aEventEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iEventId = (int)$aEventEntry['ID'];
				$sTitle = $aEventEntry['Title'];
 
				$this->aCurrent['join']['venue'] = array(
					'type' => 'inner',
					'table' => 'bx_events_venue_main',
					'mainField' => 'ID',
					'onField' => 'event_id',
					'joinFields' => array('id', 'event_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
				);

				$this->aCurrent['ownFields'] = array();
 
				$this->aCurrent['restriction']['event_id'] = array('value' => $iEventId, 'field' => 'event_id', 'operator' => '=', 'table' => 'bx_events_venue_main'); 
				$this->sBrowseUrl = "venue/browse/$sValue";
				$this->aCurrent['title'] = _t('_bx_events_caption_browse_event_venues', $sTitle);
				break;

            case 'news':
				
				$oMain = $this->getMain();
				$aEventEntry = $oMain->_oDb->getEntryByUri($sValue);
				$iEventId = (int)$aEventEntry['ID'];
				$sTitle = $aEventEntry['Title'];
 
				//[begin] news integration - modzzz
				if(getParam('bx_events_modzzz_news')=='on'){ 
					$oNews = BxDolModule::getInstance('BxNewsModule');

					$this->aCurrent['sorting'] = 'modzzz_news';

					$this->aCurrent['table'] = 'modzzz_news_main';
					
					$this->aCurrent['ownFields'] = array('id', 'title', 'uri', 'created', 'when', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'snippet', 'parent_category_id', 'category_id', 'tags', 'when', 'comments_count', 'video_embed', 'event_id');
    
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
		  
					$this->aCurrent['restriction']['event_id'] = array('value' => $iEventId, 'field' => 'event_id', 'operator' => '=', 'table' => 'modzzz_news_main');
					
					$this->sBrowseUrl = "news/browse/$sValue"; 

				}else{
 					
					$this->bNewsUnit = true;

					$this->aCurrent['join']['news'] = array(
						'type' => 'inner',
						'table' => 'bx_events_news_main',
						'mainField' => 'ID',
						'onField' => 'event_id',
						'joinFields' => array('id', 'event_id', 'title', 'uri', 'created', 'thumb', 'rate', 'desc', 'comments_count', 'author_id'),
					);

					$this->aCurrent['ownFields'] = array();
	 
					$this->aCurrent['restriction']['event_id'] = array('value' => $iEventId, 'field' => 'event_id', 'operator' => '=', 'table' => 'bx_events_news_main'); 
					$this->sBrowseUrl = "news/browse/$sValue";
				}

				$this->aCurrent['title'] = _t('_bx_events_caption_browse_event_news', $sTitle);
				break;

            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_bx_events');
                break;

            default:
                $this->isError = true;
        }        
 
		if(in_array($sMode, array('news','view_news')) && getParam('bx_events_modzzz_news')=='on'){
			
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
					 $this->aCurrent['restriction']['membership'] = array('value' => "", 'field' => 'EventMembershipViewFilter', 'operator' => '=' ); 
				}else{
					require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
					$aMembershipInfo = getMemberMembershipInfo($oMain->_iProfileId);
					
					if($aMembershipInfo['DateExpires']){ 
						$bCheckMembership = (($aMembershipInfo['DateStarts'] < time()) && ($aMembershipInfo['DateExpires'] > time())) ? true : false;

						if($bCheckMembership)
							$this->aCurrent['restriction']['membership'] = array('value' => array('',$aMembershipInfo['ID']), 'field' => 'EventMembershipViewFilter', 'operator' => 'in' );  
						else
							$this->aCurrent['restriction']['membership'] = array('value' => "", 'field' => 'EventMembershipViewFilter', 'operator' => '=' ); 

					}else{
						$this->aCurrent['restriction']['membership'] = array('value' => array('',$aMembershipInfo['ID']), 'field' => 'EventMembershipViewFilter', 'operator' => 'in' );  
					}
				}
			}
			//end- membership filter


			//[begin] - Group Events modzzz
			if((getParam("modzzz_gevent_event_active")=='on') && !in_array($sMode, array('pending','my_pending')) ){ 
				$this->aCurrent['restriction']['group_event'] = array('value' => 1, 'field' => 'group_id', 'operator' => '<', 'table' => 'bx_events_main');
			}
			//[end] - Group Events modzzz


			$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('bx_events_perpage_browse');

			if (isset($this->aCurrent['rss']))
				$this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . $this->sBrowseUrl;

			if (bx_get('rss')) {
				$this->aCurrent['ownFields'][] = 'Description';
				$this->aCurrent['ownFields'][] = 'Date';
				$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('bx_events_max_rss_num');
			}

			bx_events_import('Voting', $this->getModuleArray());
			$oVotingView = new BxEventsVoting ('bx_events', 0);
			$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;

		}


        parent::BxDolTwigSearchResult();
    }

    function getAlterOrder() {
		if ($this->aCurrent['sorting'] == 'last') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `bx_events_main`.`Date` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'upcoming') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `EventStart` ASC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'top') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `bx_events_main`.`Rate` DESC, `bx_events_main`.`RateCount` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'popular') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `bx_events_main`.`FansCount` DESC, `bx_events_main`.`Views` DESC";
			return $aSql;
		//[begin] news integration - modzzz
		} elseif ($this->aCurrent['sorting'] == 'modzzz_news') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_news_main`.`created` DESC";
			return $aSql;
		//[end] news integration - modzzz
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
 
    function getModuleArray() {
        return db_arr ("SELECT * FROM `sys_modules` WHERE `title` = 'Events' AND `class_prefix` = 'BxEvents' LIMIT 1");
    }

    function getMain() {
        return BxDolModule::getInstance('BxEventsModule');
    }

    function getRssUnitLink (&$a) {
        $oMain = $this->getMain();
        return BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'view/' . $a['EntryUri'];
    }
    
    function _getPseud () {
        return array(    
            'ID' => 'ID',
            'Title' => 'Title',
            'EntryUri' => 'EntryUri',
            'EventStart' => 'EventStart',
            'Place' => 'Place',        
            'Country' => 'Country',
            'City' => 'City',
            'ResponsibleID' => 'ResponsibleID',
            'NickName' => 'NickName',
            'PrimPhoto' => 'PrimPhoto', 
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


	//event etc.
     function displaySubProfileResultBlock ($sType) { 
        $aData = $this->getSearchData();  
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $s = $this->addCustomParts();
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
 
			case 'sponsor':
 
				bx_import('SponsorVoting', $oMain->_aModule);
				$oVotingView = new BxEventsSponsorVoting ('bx_events_sponsor', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->sponsor_unit($aData, 'sponsor_unit', $this->oVotingView); 
			break;
			case 'venue':
 
				bx_import('VenueVoting', $oMain->_aModule);
				$oVotingView = new BxEventsVenueVoting ('bx_events_venue', 0);
				$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
 
				$sResult = $oMain->_oTemplate->venue_unit($aData, 'venue_unit', $this->oVotingView); 
			break;
			case 'news':
 
 				if(getParam('bx_events_modzzz_news')=='on'){  
					$oNews = BxDolModule::getInstance('BxNewsModule');
					bx_import('Voting', $oNews->_aModule);
					$oVotingView = new BxNewsVoting ('modzzz_news', 0);
					$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
	 
					$sResult = $oNews->_oTemplate->unit($aData, 'unit', $this->oVotingView); 
				}else{
					bx_import('NewsVoting', $oMain->_aModule);
					$oVotingView = new BxEventsNewsVoting ('bx_events_news', 0);
					$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
	 
					$sResult = $oMain->_oTemplate->news_unit($aData, 'news_unit', $this->oVotingView); 
				}
			break; 
		}

		return $sResult;
	}


	/*
	 * Check restriction params and make condition part of query
	 * return $sqlWhere sql code of query for WHERE part  
	 */
	
	function getRestrictionOLD () {
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
 
                if (isset($aValue['concat']) && !empty($aValue['value'])) {
 
                   $sFieldTable = isset($aValue['table']) ? $aValue['table'] : $this->aCurrent['table'];
 
                   if (!isset($aValue['no_quote_value']))
                       $aValue['value'] = process_db_input($aValue['value'], BX_TAGS_STRIP);
 
                   if (!isset($aValue['no_quote_value2']))
                       $aValue['value2'] = process_db_input($aValue['value2'], BX_TAGS_STRIP);
 
				   $sqlCondition1 = "`{$sFieldTable}`.`{$aValue['field']}` " . $aValue['operator'] . (isset($aValue['no_quote_value']) && $aValue['no_quote_value'] ?  $aValue['value'] : "'" . $aValue['value'] . "'");
 
				   $sqlCondition2 = "`{$sFieldTable}`.`{$aValue['field2']}` " . $aValue['operator2'] . (isset($aValue['no_quote_value2']) && $aValue['no_quote_value2'] ?  $aValue['value2'] : "'" . $aValue['value2'] . "'");

				   $sqlCondition .= '('. $sqlCondition1 . " {$aValue['concat']} " . $sqlCondition2 . ')';
 
                }elseif (isset($aValue['operator']) && !empty($aValue['value'])) {
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
 
 

}
