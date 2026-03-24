<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Classified
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

class BxClassifiedSearchResult extends BxDolTwigSearchResult {

    var $aCurrent = array(
        'name' => 'modzzz_classified',
        'title' => '_modzzz_classified_page_title_browse',
        'table' => 'modzzz_classified_main',
        'ownFields' => array('id', 'title', 'uri', 'created', 'author_id', 'thumb', 'rate', 'country', 'city','zip', 'state', 'desc', 'category_id', 'tags', 'comments_count','invoice_no', 'expiry_date', 'featured_expiry_date', 'membership_view_filter', 'price', 'saleprice', 'quantity', 'classified_type','company_type','company_id', 'payment_type', 'why','currency','custom_field1','custom_field2','custom_field3','custom_field4','custom_field5','custom_field6','custom_field7','custom_field8','custom_field9','custom_field10','custom_sub_field1','custom_sub_field2','custom_sub_field3','custom_sub_field4','custom_sub_field5','custom_sub_field6','custom_sub_field7','custom_sub_field8','custom_sub_field9','custom_sub_field10'),
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
            'public' => array('value' => '', 'field' => 'allow_view_classified_to', 'operator' => '='),
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
    
 
    function BxClassifiedSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '', $sValue4 = '', $sValue5 = '', $sValue6 = '') {
	 
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
                $this->sBrowseUrl = "administration";
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break; 

            case 'expired':
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'expired';
                $this->sBrowseUrl = "administration/expired_entries/";
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_expired_listings');
                unset($this->aCurrent['rss']);
            break; 

            case 'my_pending':
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break;
            case 'my_expired': 
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'expired';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_expired');
                unset($this->aCurrent['rss']);
            break; 
            case 'sale_pending':
 
				$this->aCurrent['join']['sale_pending'] = array(
					'type' => 'inner',
					'table' => 'modzzz_classified_offers',
					'mainField' => 'id',
					'onField' => 'classified_id',
					'joinFields' => array('id','classified_id', 'buyer_id','offer_date', 'offer_status') 
				);	
 
				$this->aCurrent['ownFields'] = array('title', 'uri', 'created', 'author_id', 'thumb', 'rate', 'country', 'city', 'state', 'desc', 'category_id', 'tags', 'comments_count','invoice_no', 'expiry_date', 'featured_expiry_date', 'membership_view_filter', 'price', 'saleprice', 'quantity', 'classified_type', 'payment_type', 'why','currency');

				$this->aCurrent['restriction']['offer_status'] = array( 'table' => 'modzzz_classified_offers', 'value' => 'pending', 'field' => 'offer_status', 'operator' => '=');

                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_sale_pending');
                unset($this->aCurrent['rss']);
            break;
            case 'sold':
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'sold';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_sold');
                unset($this->aCurrent['rss']);
            break;
             case 'order':  
                
				if (isset($_REQUEST['filter'])) 
 					$this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;

				$this->aCurrent['ownFields'] = array('title', 'uri', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'category_id', 'tags', 'comments_count','status','expiry_date');
   
                $this->sBrowseUrl = "administration/orders/";
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_orders');
                
				$this->aCurrent['join']['invoices'] = array(
					'type' => 'inner',
					'table' => 'modzzz_classified_invoices',
					'mainField' => 'invoice_no',
					'onField' => 'invoice_no',
					'joinFields' => array('classified_id', 'invoice_no','price','days','package_id','invoice_date','invoice_due_date','invoice_expiry_date','invoice_status'),   
				);	

				$this->aCurrent['join']['orders'] = array(
					'type' => 'inner',
					'table' => 'modzzz_classified_orders',
					'mainField' => 'invoice_no',
					'onField' => 'invoice_no',
					'joinFields' => array('id','order_no','buyer_id','payment_method','order_date','order_status'),   
				); 

				unset($this->aCurrent['restriction']['activeStatus']);  

				unset($this->aCurrent['rss']);  
            break;
            case 'invoice':  
 
                 if (isset($_REQUEST['filter'])) 
 					$this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
 
				$this->aCurrent['ownFields'] = array('title', 'uri', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'category_id', 'tags', 'comments_count' );
  
                //$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "administration/invoices/";
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_invoices');
                 
				$this->aCurrent['join']['invoices'] = array(
					'type' => 'inner',
					'table' => 'modzzz_classified_invoices',
					'mainField' => 'id',
					'onField' => 'classified_id',
					'joinFields' => array('id','classified_id', 'invoice_no','price','days','package_id','invoice_status','invoice_date','invoice_due_date','invoice_expiry_date'),   
				);	
				
				unset($this->aCurrent['restriction']['activeStatus']);  
 
				unset($this->aCurrent['rss']);  
            break;
    
            case 'categories':
				$aCategory = $oMain->_oDb->getCategoryByUri($sValue); 
                $iCategoryId = $aCategory['id'];
                $sCategoryName = $aCategory['name']; 
                 $this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/categories/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_category').' - '.$sCategoryName;
				$this->aCurrent['restriction']['subcat'] = array( 'table' => 'modzzz_classified_categ', 'value' => $iCategoryId,'field' => 'parent','operator' => '=');
				$this->aCurrent['join']['cat'] = array(
					'type' => 'left',
					'table' => 'modzzz_classified_categ',
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
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_category').' - '.$sCategoryName;
				$this->aCurrent['restriction']['subcat'] = array( 'value' => $iCategoryId,'field' => 'category_id','operator' => '=');
        
				$this->aCurrent['join']['sub'] = array(
					'type' => 'inner',
					'table' => 'modzzz_classified_categ',
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

				if($sValue5)
					$this->aCurrent['restriction']['country'] = array('value' => $sValue5, 'field' => 'country', 'operator' => '=');
 
                $this->sBrowseUrl = "search/$sValue/$sValue2/$sValue3/$sValue4/$sValue5";
 
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_search_results');
                
				unset($this->aCurrent['rss']);

				$this->aCurrent['sorting'] = 'search';

                break; 

            case 'user':
                $iProfileId = $GLOBALS['oBxClassifiedModule']->_oDb->getProfileIdByNickName ($sValue);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                
				$this->sBrowseUrl = "browse/user/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_modzzz_classified_page_title_browse_by_author');
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
                $this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "administration/admin_entries";
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_admin_classified');
                break;
 
            case 'tag':
            	$sValue = uri2title($sValue);

                $this->aCurrent['restriction']['tag']['value'] = $sValue;
                $this->sBrowseUrl = "browse/tag/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_browse_by_tag') . ' ' . $sValue;
                break;

		   //[begin] - local 
			case 'local_country':          
				$this->aCurrent['restriction']['local_country'] = array('value' => $sValue, 'field' => 'country', 'operator' => '='); 
				$this->sBrowseUrl = "browse/local_country/$sValue";
				
 				$sCountryName = htmlspecialchars_adv( _t($GLOBALS['aPreValues']['Country'][$sValue]['LKey']) );
				
				$sTitleStr = $sCountryName;
 
				if($sValue2){

					$this->sBrowseUrl = "browse/local_country/$sValue/$sValue2";

					$aCategory = $oMain->_oDb->getCategoryByUri($sValue2); 
					$iCategoryId = $aCategory['id'];
					$sCategoryName = $aCategory['name'];
		
					$sTitleStr .= ' - ' . $sCategoryName;

					$this->aCurrent['restriction']['category'] = array( 'table' => 'modzzz_classified_categ', 'value' => $iCategoryId, 'field' => 'parent','operator' => '=');

					$this->aCurrent['join']['cat'] = array(
						'type' => 'left',
						'table' => 'modzzz_classified_categ',
						'mainField' => 'category_id',
						'onField' => 'id',
						'joinFields' => '',
					);	 
 				}
 				 
				$this->aCurrent['title'] = _t('_modzzz_classified_caption_browse_local', $sTitleStr);

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

					$this->aCurrent['restriction']['category'] = array( 'table' => 'modzzz_classified_categ', 'value' => $iCategoryId, 'field' => 'parent','operator' => '=');

					$this->aCurrent['join']['cat'] = array(
						'type' => 'left',
						'table' => 'modzzz_classified_categ',
						'mainField' => 'category_id',
						'onField' => 'id',
						'joinFields' => '',
					);	 
 				}

				$this->aCurrent['title'] = _t('_modzzz_classified_caption_browse_local', $sTitleStr);
				break; 
				
			case 'local': 
				$this->aCurrent['restriction']['local'] = array('value' => $sValue, 'field' => 'city', 'operator' => '='); 
				$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/local/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_classified_caption_browse_local', $sValue);
				break;
			case 'other_local': 
				$this->aCurrent['restriction']['item'] = array('value' => $sValue, 'field' => 'id', 'operator' => '!=');  
				$this->aCurrent['restriction']['local_city'] = array('value' => $sValue2, 'field' => 'city', 'operator' => '='); 
				$this->aCurrent['restriction']['local_state'] = array('value' => $sValue3, 'field' => 'state', 'operator' => '='); 

				$this->sBrowseUrl = "browse/other_local/$sValue/$sValue2/$sValue3";
				$this->aCurrent['title'] = _t('_modzzz_classified_caption_browse_local', $sValue2);
				break;
			case 'other':
				$sNickName = getNickName($sValue);

				$this->aCurrent['restriction']['other'] = array('value' => $sValue, 'field' => 'author_id', 'operator' => '=');  
				$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/other/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_classified_caption_browse_other', $sNickName);
				break;
  
            case 'recent':
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_browse_recent');
                break;

            case 'top':
                $this->sBrowseUrl = 'browse/top';
                $this->aCurrent['sorting'] = 'top';
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_browse_top_rated');
                break;

            case 'popular':
                $this->sBrowseUrl = 'browse/popular';
                $this->aCurrent['sorting'] = 'popular';
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_browse_popular');
                break;  
				
				 case 'favorite':
 
				$this->aCurrent['join']['favorites'] = array(
					'type' => 'left',
					'table' => 'modzzz_classified_favorites',
					'mainField' => 'id',
					'onField' => 'id_entry',
					'joinFields' => '',
				);	        
 				$iProfileId = $oMain->_iProfileId;
				$iProfileId = ($iProfileId) ? $iProfileId : -999;
				$this->aCurrent['restriction']['favorite'] = array('value' => $iProfileId, 'field' => 'id_profile', 'operator' => '=', 'table' => 'modzzz_classified_favorites');
                $this->sBrowseUrl = 'browse/favorite';
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_browse_favorite');
                break;
				
				              

            case 'featured':
				$this->aCurrent['sorting'] = 'rand'; 
				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured';
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_browse_featured');
                break;                                

            case 'calendar':
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 00:00:00')", 'field' => 'created', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 23:59:59')", 'field' => 'created', 'operator' => '<=', 'no_quote_value' => true);
                $this->sEventsBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->aCurrent['title'] = _t('_modzzz_classified_page_title_browse_by_day') . sprintf("%04u-%02u-%02u", $sValue, $sValue2, $sValue3);
                break;                                

            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_modzzz_classified');
                break;

            default:
                $this->isError = true;
        }

        $oMain = $this->getMain();

        $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_classified_perpage_browse');

        if (isset($this->aCurrent['rss']))
            $this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . $this->sBrowseUrl;

        if (isset($_REQUEST['rss']) && $_REQUEST['rss']) {
            $this->aCurrent['ownFields'][] = 'desc';
            $this->aCurrent['ownFields'][] = 'created';
            $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_classified_max_rss_num');
        }

        bx_import('Voting', $oMain->_aModule);
        $oVotingView = new BxClassifiedVoting ('modzzz_classified', 0);
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
			$aSql['order'] = " ORDER BY `modzzz_classified_main`.`created` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'top') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_classified_main`.`rate` DESC, `modzzz_classified_main`.`rate_count` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'popular') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_classified_main`.`views` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'search') {
 
			//for search results, return featured listings first while randomizing all results
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_classified_main`.`featured` DESC, rand()";
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
        return BxDolModule::getInstance('BxClassifiedModule');
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

    function displaySalePendingResultBlock ($sType) { 
        $aData = $this->getSearchData();
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $s = $this->addCustomParts();
            foreach ($aData as $aValue) {
                $s .= $this->displaySalePendingSearchUnit($sType, $aValue);
            }

            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
        }
        return ''; 
    }

    function displaySalePendingSearchUnit ($sType, $aData) {
        $oMain = $this->getMain();
 
		return $oMain->_oTemplate->sale_pending_unit($aData, $this->sUnitTemplate, $this->oVotingView);
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
