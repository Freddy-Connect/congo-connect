<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Investment
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

class BxInvestmentSearchResult extends BxDolTwigSearchResult {
 
    var $aCurrent = array(
        'name' => 'modzzz_investment',
        'title' => '_modzzz_investment_page_title_browse',
        'table' => 'modzzz_investment_main',
        'ownFields' => array('id', 'title', 'uri', 'created', 'author_id', 'thumb', 'rate','views','fans_count', 'country', 'city', 'state', 'desc','stade','financement_recu', 'tags', 'comments_count','invoice_no', 'video_embed', 'expiry_date', 'featured_expiry_date', 'membership_view_filter', 'price', 'saleprice', 'quantity', 'investment_type', 'min_investment', 'max_investment', 'why','currency','categories', 'min_investment', 'max_investment', 'aide', 'terme','sellername','selleremail','sellertelephone','sellerwebsite','address2','zip','sellerfax','address1'),
        'searchFields' => array('title', 'tags', 'desc','categories'),
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
            'public' => array('value' => '', 'field' => 'allow_view_investment_to', 'operator' => '='),
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
    
 
    function BxInvestmentSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '', $sValue4 = '', $sValue5 = '', $sValue6 = '') {
	 
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
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break; 
            case 'my_pending':
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break;
            case 'my_expired': 
                $oMain = $this->getMain();
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'expired';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_expired');
                unset($this->aCurrent['rss']);
            break; 
  
            case 'order':  
                 
				if (isset($_REQUEST['filter'])) 
 					$this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;

				$this->aCurrent['ownFields'] = array('title', 'uri', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'categories', 'tags', 'comments_count','status','expiry_date');
   
                $this->sBrowseUrl = "browse/order/";
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_orders');
                
				$this->aCurrent['join']['invoices'] = array(
					'type' => 'inner',
					'table' => 'modzzz_investment_invoices',
					'mainField' => 'invoice_no',
					'onField' => 'invoice_no',
					'joinFields' => array('investment_id', 'invoice_no','price','days','package_id','invoice_date','invoice_due_date','invoice_expiry_date','invoice_status'),   
				);	

				$this->aCurrent['join']['orders'] = array(
					'type' => 'inner',
					'table' => 'modzzz_investment_orders',
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
 
				$this->aCurrent['ownFields'] = array('title', 'uri', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'categories', 'tags', 'comments_count' );
  
                //$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/invoice/";
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_invoices');
                 
				$this->aCurrent['join']['invoices'] = array(
					'type' => 'inner',
					'table' => 'modzzz_investment_invoices',
					'mainField' => 'id',
					'onField' => 'investment_id',
					'joinFields' => array('id','investment_id', 'invoice_no','price','days','package_id','invoice_status','invoice_date','invoice_due_date','invoice_expiry_date'),   
				);	
				
				unset($this->aCurrent['restriction']['activeStatus']);  
 
				unset($this->aCurrent['rss']);  
            break;
 
            case 'search':
 
                if ($sValue)
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');
  
                if ($sValue2!='all')  
                    $this->aCurrent['restriction']['investment_type'] = array('value' => $sValue2,'field' => 'investment_type','operator' => '=');

				if (is_array($sValue3) && count($sValue3)) {  
					foreach($sValue3 as $val){
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

						$this->aCurrent['restriction']['category']['value'] = $sValue3;
						$this->aCurrent['restriction']['category']['operator'] = 'in';
					}
				}
 
                if ($sValue4)  
                    $this->aCurrent['restriction']['city'] = array('value' => $sValue4,'field' => 'city','operator' => '=');
  
				if ($sValue5)  
                    $this->aCurrent['restriction']['state'] = array('value' => $sValue5,'field' => 'state','operator' => '=');

				if($sValue6)
					$this->aCurrent['restriction']['country'] = array('value' => $sValue6, 'field' => 'country', 'operator' => 'in');

                $this->sBrowseUrl = "search/$sValue/$sValue2/$sValue3/$sValue4/$sValue5/$sValue6";
   
				$sCountrySrch = $this->getPreListDisplay("Country", $sValue6); 
 
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_search_results');
                
				unset($this->aCurrent['rss']);

				$this->aCurrent['sorting'] = 'search';

                break; 

            case 'user':
                $iProfileId = $GLOBALS['oBxInvestmentModule']->_oDb->getProfileIdByNickName ($sValue);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                
				$this->sBrowseUrl = "browse/user/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_modzzz_investment_page_title_browse_by_author');
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
               if (false !== bx_get('filter'))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input(bx_get('filter'), BX_TAGS_STRIP), 'field' => '','operator' => 'against');

                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId; 
                $this->sBrowseUrl = "browse/admin";
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_admin_investment');
                break;
  
            case 'category':
                $this->aCurrent['join']['category'] = array(
                    'type' => 'inner',
                    'table' => 'sys_categories',
                    'mainField' => 'id',
                    'onField' => 'ID',
                    'joinFields' => '',
                );
                $this->aCurrent['restriction']['category']['value'] = $sValue;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/category/" . title2uri($sValue);
                $this->aCurrent['title'] = _t('_modzzz_fashion_page_title_browse_by_category') . ' ' . $sValue;
                break;

            case 'tag':
            	$sValue = uri2title($sValue);

                $this->aCurrent['restriction']['tag']['value'] = $sValue;
                $this->sBrowseUrl = "browse/tag/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_browse_by_tag') . ' ' . $sValue;
                break;

		   //[begin] - local 
			case 'local_country':          
				$this->aCurrent['restriction']['local_country'] = array('value' => $sValue, 'field' => 'country', 'operator' => '='); 
				$this->sBrowseUrl = "local_country/$sValue";
				
 				$sCountryName = htmlspecialchars_adv( _t($GLOBALS['aPreValues']['Country'][$sValue]['LKey']) );
				
				$sTitleStr = $sCountryName;
   
				$this->aCurrent['title'] = _t('_modzzz_investment_caption_browse_local', $sTitleStr);

				break; 
			case 'local_state':   
				
				$this->aCurrent['restriction']['local_country'] = array('value' => $sValue, 'field' => 'country', 'operator' => '='); 

				$this->aCurrent['restriction']['local_state'] = array('value' => $sValue2, 'field' => 'state', 'operator' => '='); 
 
 				$this->sBrowseUrl = "browse/local_state/$sValue/$sValue2";
				
 				$sCountryName = htmlspecialchars_adv( _t($GLOBALS['aPreValues']['Country'][$sValue]['LKey']) );
				$sStateName = $oMain->_oDb->getStateName($sValue, $sValue2); 
				$sTitleStr = $sCountryName . ' - ' . $sStateName;
  
				$this->aCurrent['title'] = _t('_modzzz_investment_caption_browse_local', $sTitleStr);
				break; 
				
			case 'local': 
				$this->aCurrent['restriction']['local'] = array('value' => $sValue, 'field' => 'city', 'operator' => '='); 
				$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/local/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_investment_caption_browse_local', $sValue);
				break;
			case 'other_local': 
				$this->aCurrent['restriction']['item'] = array('value' => $sValue, 'field' => 'id', 'operator' => '!=');  
				$this->aCurrent['restriction']['local_city'] = array('value' => $sValue2, 'field' => 'city', 'operator' => '='); 
				$this->aCurrent['restriction']['local_state'] = array('value' => $sValue3, 'field' => 'state', 'operator' => '='); 

				$this->sBrowseUrl = "browse/other_local/$sValue/$sValue2/$sValue3";
				$this->aCurrent['title'] = _t('_modzzz_investment_caption_browse_local', $sValue2);
				break;
			case 'other':
				$sNickName = getNickName($sValue);

				$this->aCurrent['restriction']['other'] = array('value' => $sValue, 'field' => 'author_id', 'operator' => '=');  
				$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/other/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_investment_caption_browse_other', $sNickName);
				break;
  
            case 'recent':
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_browse_recent');
                break;
 
            case 'recent_investors':
                $this->sBrowseUrl = 'browse/recent_investors';
				$this->aCurrent['restriction']['investment_type'] = array('value' => 'investor', 'field' => 'investment_type', 'operator' => '=');
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_browse_recent_investors');
                break;
            case 'recent_entrepreneurs':
                $this->sBrowseUrl = 'browse/recent_entrepreneurs';
				$this->aCurrent['restriction']['investment_type'] = array('value' => 'entrepreneur', 'field' => 'investment_type', 'operator' => '=');
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_browse_recent_entrepreneurs');
                break;
            case 'recent_professionals':
                $this->sBrowseUrl = 'browse/recent_professionals';
				$this->aCurrent['restriction']['investment_type'] = array('value' => 'professional', 'field' => 'investment_type', 'operator' => '=');
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_browse_recent_professionals');
                break;
  
            case 'top':
                $this->sBrowseUrl = 'browse/top';
                $this->aCurrent['sorting'] = 'top';
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_browse_top_rated');
                break;

            case 'popular':
                $this->sBrowseUrl = 'browse/popular';
                $this->aCurrent['sorting'] = 'popular';
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_browse_popular');
                break;   
				
			 case 'favorite':
 
				$this->aCurrent['join']['favorites'] = array(
					'type' => 'left',
					'table' => 'modzzz_investment_favorites',
					'mainField' => 'id',
					'onField' => 'id_entry',
					'joinFields' => '',
				);	        
 				$iProfileId = $oMain->_iProfileId;
				$iProfileId = ($iProfileId) ? $iProfileId : -999;
				$this->aCurrent['restriction']['favorite'] = array('value' => $iProfileId, 'field' => 'id_profile', 'operator' => '=', 'table' => 'modzzz_investment_favorites');
                $this->sBrowseUrl = 'browse/favorite';
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_browse_favorite');
                break;
				             

            case 'featured':
				$this->aCurrent['sorting'] = 'rand'; 
				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured';
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_browse_featured');
                break;  

            case 'featured_investors':
				$this->aCurrent['sorting'] = 'rand'; 
				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
				$this->aCurrent['restriction']['investment_type'] = array('value' => 'investor', 'field' => 'investment_type', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured_investors';
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_browse_featured_investors');
                break; 				
            case 'featured_professionals':
				$this->aCurrent['sorting'] = 'rand'; 
				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
				$this->aCurrent['restriction']['investment_type'] = array('value' => 'professional', 'field' => 'investment_type', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured_professionals';
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_browse_featured_professionals');
                break; 
            case 'featured_entrepreneurs':
				$this->aCurrent['sorting'] = 'rand'; 
				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
				$this->aCurrent['restriction']['investment_type'] = array('value' => 'entrepreneur', 'field' => 'investment_type', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured_entrepreneurs';
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_browse_featured_entrepreneurs');
                break; 

            case 'calendar':
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 00:00:00')", 'field' => 'created', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 23:59:59')", 'field' => 'created', 'operator' => '<=', 'no_quote_value' => true);
                $this->sBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->sEventsBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->aCurrent['title'] = _t('_modzzz_investment_page_title_browse_by_day') . sprintf("%04u-%02u-%02u", $sValue, $sValue2, $sValue3);
                break;                                

            case 'inquiry':
				$this->aCurrent['sorting'] = 'inquiry';
				$this->bInquiry = true;

				$this->aCurrent['join']['inquiry'] = array(
					'type' => 'inner',
					'table' => 'modzzz_investment_inquiry',
					'mainField' => 'id',
					'onField' => 'id_entry',
					'joinFields' => array('id', 'id_entry', 'id_profile', 'desc', 'created'),
				);
 
				$this->aCurrent['ownFields'] = array('uri');
 
				$this->aCurrent['restriction']['investment_id'] = array('value' => $sValue, 'field' => 'id_entry', 'operator' => '=', 'table' => 'modzzz_investment_inquiry'); 
				$this->sBrowseUrl = "browse/inquiry/$sValue";
				$this->aCurrent['title'] = '';
				break; 

            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_modzzz_investment');
                break;

            default:
                $this->isError = true;
        }

        $oMain = $this->getMain();

        $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_investment_perpage_browse');

        if (isset($this->aCurrent['rss']))
            $this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . $this->sBrowseUrl;

        if (isset($_REQUEST['rss']) && $_REQUEST['rss']) {
            $this->aCurrent['ownFields'][] = 'desc';
            $this->aCurrent['ownFields'][] = 'created';
            $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_investment_max_rss_num');
        }

        bx_import('Voting', $oMain->_aModule);
        $oVotingView = new BxInvestmentVoting ('modzzz_investment', 0);
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
			$aSql['order'] = " ORDER BY `modzzz_investment_main`.`created` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'top') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_investment_main`.`rate` DESC, `modzzz_investment_main`.`rate_count` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'popular') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_investment_main`.`views` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'search') {
 
			//for search results, return featured listings first while randomizing all results
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_investment_main`.`featured` DESC, rand()";
			return $aSql; 
		
		} elseif ($this->aCurrent['sorting'] == 'inquiry') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_investment_inquiry`.`created` DESC";
			return $aSql;		
		}
  
	    return array();
    }

    function displayResultBlock () {
        global $oFunctions;

		if($this->bInquiry)
			$s = $this->displaySubProfileResultBlock ('inquiry');
		else
			$s = parent::displayResultBlock ();
        if ($s) {
            $oMain = $this->getMain();
            $GLOBALS['oSysTemplate']->addDynamicLocation($oMain->_oConfig->getHomePath(), $oMain->_oConfig->getHomeUrl());
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
        }
        return ''; 
    }

	//inquiry etc.
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
			case 'inquiry': 
				$sResult = $oMain->_oTemplate->inquiry_unit($aData, 'inquiry_unit'); 
		  	break; 	
		} 
		return $sResult;
	}
 

    function getMain() {
        return BxDolModule::getInstance('BxInvestmentModule');
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
 
}
