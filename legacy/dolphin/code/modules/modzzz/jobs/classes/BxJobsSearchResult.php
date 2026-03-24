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

class BxJobsSearchResult extends BxDolTwigSearchResult {

    var $aCurrent = array(
        'name' => 'modzzz_jobs',
        'title' => '_modzzz_jobs_page_title_browse',
        'table' => 'modzzz_jobs_main',
        'ownFields' => array('id', 'title', 'desc', 'uri', 'created', 'author_id', 'thumb', 'icon', 'rate', 'zip', 'parent_category_id','category_id','company_id','company_type', 'role','vacancies','experience','qualification','skills','min_salary','max_salary','salary_type','currency','career_level','job_type','job_status','comments_count', 'tags','video_embed','membership_view_filter','post_type', 'currency', 'invoice_no','expiry_date', 'featured_expiry_date', 'country', 'city', 'state', 'address1', 'application_link'),
        'searchFields' => array('title', 'desc', 'tags'),
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
            'public' => array('value' => '', 'field' => 'allow_view_job_to', 'operator' => '='),

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
     
    function BxJobsSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '', $sValue4 = '', $sValue5 = '', $sValue6 = '', $sValue7 = '') {        

		$sValue = process_db_input($sValue);
		$sValue2 = process_db_input($sValue2);
		$sValue3 = process_db_input($sValue3);
		$sValue4 = process_db_input($sValue4);
		$sValue5 = process_db_input($sValue5);
		$sValue6 = process_db_input($sValue6);
  		$sValue7 = process_db_input($sValue6);

		$oMain = $this->getMain();
 
        switch ($sMode) {

            case 'categories':
                $oMain = $this->getMain();
				$aCategory = $oMain->_oDb->getCategoryByUri($sValue); 
                $iCategoryId = $aCategory['id'];
                $sCategoryName = $aCategory['name']; 
                 $this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/categories/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_category').' - '.$sCategoryName;
				$this->aCurrent['restriction']['subcat'] = array( 'table' => 'modzzz_jobs_categ', 'value' => $iCategoryId,'field' => 'parent','operator' => '=');
				$this->aCurrent['join']['cat'] = array(
					'type' => 'left',
					'table' => 'modzzz_jobs_categ',
					'mainField' => 'category_id',
					'onField' => 'id',
					'joinFields' => '',
				);	        
 				 
				//unset($this->aCurrent['rss']);
            break;
            case 'subcategories': 
                $oMain = $this->getMain(); 
				$aCategory = $oMain->_oDb->getCategoryByUri($sValue); 
                $iCategoryId = $aCategory['id'];
                $sCategoryName = $aCategory['name'];
				$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/subcategories/$sValue" ;
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_category').' - '.$sCategoryName;
				$this->aCurrent['restriction']['subcat'] = array( 'value' => $iCategoryId,'field' => 'category_id','operator' => '=');
        
				$this->aCurrent['join']['sub'] = array(
					'type' => 'inner',
					'table' => 'modzzz_jobs_categ',
					'mainField' => 'category_id',
					'onField' => 'id',
					'joinFields' => '',
				);					

				//unset($this->aCurrent['rss']);
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

					$this->aCurrent['restriction']['category'] = array( 'table' => 'modzzz_jobs_categ', 'value' => $iCategoryId, 'field' => 'parent','operator' => '=');

					$this->aCurrent['join']['cat'] = array(
						'type' => 'left',
						'table' => 'modzzz_jobs_categ',
						'mainField' => 'category_id',
						'onField' => 'id',
						'joinFields' => '',
					);	 
 				}
 				
				$this->aCurrent['title'] = _t('_modzzz_jobs_caption_browse_local', $sTitleStr);

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

					$this->aCurrent['restriction']['category'] = array( 'table' => 'modzzz_jobs_categ', 'value' => $iCategoryId, 'field' => 'parent','operator' => '=');

					$this->aCurrent['join']['cat'] = array(
						'type' => 'left',
						'table' => 'modzzz_jobs_categ',
						'mainField' => 'category_id',
						'onField' => 'id',
						'joinFields' => '',
					);	 
 				}

				$this->aCurrent['title'] = _t('_modzzz_jobs_caption_browse_local', $sTitleStr);
				break; 

			case 'apply':
				$this->aCurrent['join']['application'] = array(
					'type' => 'inner',
					'table' => 'modzzz_jobs_apply',
					'mainField' => 'id',
					'onField' => 'job_id',
					'joinFields' => '',
				);	
   
                $iProfileId = $GLOBALS['oBxJobsModule']->_oDb->getProfileIdByNickName ($sValue, false);
               
                if (!$iProfileId)
                    $this->isError = true;
                else
					$this->aCurrent['restriction']['application'] = array('value' => $iProfileId, 'field' => 'member_id', 'operator' => '=', 'table' => 'modzzz_jobs_apply'); 

				$sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/apply/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_modzzz_jobs_page_title_applications_by_author');
 
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

			case 'local':
				
				$this->aCurrent['sorting'] = 'last';

				$this->aCurrent['join']['company'] = array(
					'type' => 'inner',
					'table' => 'modzzz_jobs_companies',
					'mainField' => 'company_id',
					'onField' => 'id',
					'joinFields' => '',
				);	

				$this->aCurrent['restriction']['local'] = array('value' => $sValue, 'field' => 'company_city', 'operator' => '=', 'table' => 'modzzz_jobs_companies'); 
				$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/local/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_jobs_caption_browse_local', $sValue);
				break;

			case 'other':
				$oMain = $this->getMain();
				$sCompanyName = $oMain->_oDb->getCompanyName($sValue);

				$this->aCurrent['restriction']['other'] = array('value' => $sValue, 'field' => 'company_id', 'operator' => '=');  
				$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/other/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_jobs_caption_browse_other', $sCompanyName);
				break;

			case 'company_jobs':
				$oMain = $this->getMain();
				$sCompanyName = $oMain->_oDb->getCompanyName($sValue);

				$this->aCurrent['restriction']['id'] = array('value' => $sValue, 'field' => 'company_id', 'operator' => '=');  
				// Freddy modif 
				//$this->aCurrent['restriction']['type'] = array('value' => 'job', 'field' => 'company_type', 'operator' => '=');  
				$this->aCurrent['restriction']['type'] = array('value' => 'listing', 'field' => 'company_type', 'operator' => '=');

				$this->sBrowseUrl = "browse/company_jobs/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_jobs_caption_browse_company_jobs', $sCompanyName);
				break;
				
				
				
				
				
   
			case 'other_member':
				$sNickName = getNickName($sValue);

				$this->aCurrent['restriction']['other'] = array('value' => $sValue, 'field' => 'author_id', 'operator' => '=');  
				$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/other_member/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_jobs_caption_browse_other', $sNickName);
				break;

            case 'pending':
                if (false !== bx_get('filter'))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input(bx_get('filter'), BX_TAGS_STRIP), 'field' => '','operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "administration";
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break;

            case 'my_pending':

                $oMain = $this->getMain();
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_pending_approval');
                unset($this->aCurrent['rss']);
            break;
            case 'my_expired': 

                $oMain = $this->getMain();
                $this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
                $this->aCurrent['restriction']['activeStatus']['value'] = 'expired';
                $this->sBrowseUrl = "browse/user/" . getNickName($oMain->_iProfileId);
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_expired');
                unset($this->aCurrent['rss']);
            break;
            case 'order':  
                $oMain = $this->getMain();
                
				if (isset($_REQUEST['filter'])) 
 					$this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;

				$this->aCurrent['ownFields'] = array('title', 'uri', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'category_id', 'tags', 'comments_count','status','expiry_date');
   
                $this->sBrowseUrl = "browse/order/";
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_orders');
                
				$this->aCurrent['join']['invoices'] = array(
					'type' => 'inner',
					'table' => 'modzzz_jobs_invoices',
					'mainField' => 'invoice_no',
					'onField' => 'invoice_no',
					'joinFields' => array('job_id', 'invoice_no','price','days','package_id','invoice_date','invoice_due_date','invoice_expiry_date','invoice_status'),   
				);	

				$this->aCurrent['join']['orders'] = array(
					'type' => 'inner',
					'table' => 'modzzz_jobs_orders',
					'mainField' => 'invoice_no',
					'onField' => 'invoice_no',
					'joinFields' => array('id','order_no','buyer_id','payment_method','order_date','order_status'),   
				); 

				unset($this->aCurrent['restriction']['activeStatus']);  

				unset($this->aCurrent['rss']);  
            break;
            case 'invoice':  
                $oMain = $this->getMain();
 
                 if (isset($_REQUEST['filter'])) 
 					$this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
 
				$this->aCurrent['ownFields'] = array('title', 'uri', 'author_id', 'thumb', 'rate', 'country', 'city', 'desc', 'category_id', 'tags', 'comments_count' );
  
                //$this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/invoice/";
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_invoices');
                 
				$this->aCurrent['join']['invoices'] = array(
					'type' => 'inner',
					'table' => 'modzzz_jobs_invoices',
					'mainField' => 'id',
					'onField' => 'job_id',
					'joinFields' => array('id','job_id', 'invoice_no','price','days','package_id','invoice_status','invoice_date','invoice_due_date','invoice_expiry_date'),   
				);	
				
				unset($this->aCurrent['restriction']['activeStatus']);  
 
				unset($this->aCurrent['rss']);  
            break;

			case 'quick': 
				if ($sValue)
					$this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against'); 

                $this->sBrowseUrl = "browse/quick/" . $sValue;
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_browse') .' - '. $sValue; 
				break;
		   //[begin] - local 
			case 'local_country':         
				$this->aCurrent['restriction']['local_country'] = array('value' => $sValue, 'field' => 'country', 'operator' => '='); 
				$this->sBrowseUrl = "browse/local_country/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_jobs_caption_browse_local', $sValue);
				break; 
			case 'local_state':             
				$this->aCurrent['restriction']['local_state'] = array('value' => $sValue, 'field' => 'state', 'operator' => '='); 
				$this->sBrowseUrl = "browse/local_state/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_jobs_caption_browse_local', $sValue);
				break; 
			case 'local': 
				$this->aCurrent['restriction']['local'] = array('value' => $sValue, 'field' => 'city', 'operator' => '='); 
				$this->aCurrent['restriction']['item'] = array('value' => $sValue2, 'field' => 'id', 'operator' => '!=');  

				$this->sBrowseUrl = "browse/local/$sValue/$sValue2";
				$this->aCurrent['title'] = _t('_modzzz_jobs_caption_browse_local', $sValue);
				break;
 
            case 'search':
                
                if ($sValue)
                    $this->aCurrent['restriction']['keyword'] = array('value' => $sValue,'field' => '','operator' => 'against');

                if ($sValue2) 
				   $this->aCurrent['restriction']['category'] = array('value' => $sValue2,'field' => 'category_id','operator' => '=');

                if ($sValue3)  
                    $this->aCurrent['restriction']['city'] = array('value' => $sValue3,'field' => 'city','operator' => '=');
  
				if ($sValue4)  
                    $this->aCurrent['restriction']['state'] = array('value' => $sValue4,'field' => 'state','operator' => '=');

				if ($sValue5)  
                    $this->aCurrent['restriction']['country'] = array('value' => $sValue5,'field' => 'country','operator' => '=');
 
				   
                $this->sBrowseUrl = "search/$sValue/$sValue2/$sValue3/$sValue4/$sValue5";
 
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_search_results');
                unset($this->aCurrent['rss']);
                break; 

            case 'user':
 
                $iProfileId = $GLOBALS['oBxJobsModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                
                if (!$iProfileId)
                    $this->isError = true;
                else
                    $this->aCurrent['restriction']['owner']['value'] = $iProfileId;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/user/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_modzzz_jobs_page_title_browse_by_author');
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
 
                $iProfileId = $GLOBALS['oBxJobsModule']->_oDb->getProfileIdByNickName ($sValue, false);
                $GLOBALS['oTopMenu']->setCurrentProfileID($iProfileId); // select profile subtab, instead of module tab                

                if (!$iProfileId) {

                    $this->isError = true;

                } else {

					$this->aCurrent['join']['fans'] = array(
						'type' => 'inner',
						'table' => 'modzzz_jobs_fans',
						'mainField' => 'id',
						'onField' => 'id_entry',
						'joinFields' => array('id_profile'),
					);
					$this->aCurrent['restriction']['fans'] = array(
						'value' => $iProfileId, 
						'field' => 'id_profile', 
						'operator' => '=', 
						'table' => 'modzzz_jobs_fans',
					);
					$this->aCurrent['restriction']['confirmed_fans'] = array(
						'value' => 1, 
						'field' => 'confirmed', 
						'operator' => '=', 
						'table' => 'modzzz_jobs_fans',
					);
				}

                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/joined/$sValue";
                $this->aCurrent['title'] = ucfirst(strtolower($sValue)) . _t('_modzzz_jobs_page_title_browse_by_author_joined_jobs');

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
 
                $this->aCurrent['restriction']['owner']['value'] = 0;
                $this->sBrowseUrl = "browse/admin";
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_admin_jobs');
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
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_browse_by_category') . ' ' . $sValue;
                break;

            case 'tag':
 
                $this->aCurrent['restriction']['tag']['value'] = $sValue;
                $sValue = $GLOBALS['MySQL']->unescape($sValue);
                $this->sBrowseUrl = "browse/tag/" . title2uri($sValue);
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_browse_by_tag') . ' ' . $sValue;
                break;

            case 'seeking':
 
  				$this->aCurrent['restriction']['posttype'] =  array('table' => 'modzzz_jobs_main', 'value' => 'seeker', 'field' => 'post_type', 'operator' => '=' ); 

                $this->sBrowseUrl = "browse/seeking";
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_browse_seeking');
                break;

            case 'expired':
                $this->aCurrent['restriction']['activeStatus']['value'] = 'expired';
                $this->sBrowseUrl = "browse/expired";
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_expired');

            case 'recent':
  				$this->aCurrent['restriction']['posttype'] =  array('table' => 'modzzz_jobs_main', 'value' => 'provider', 'field' => 'post_type', 'operator' => '=' ); 
                $this->sBrowseUrl = 'browse/recent';
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_browse_recent');
                break;

            case 'top':
  				$this->aCurrent['restriction']['posttype'] =  array('table' => 'modzzz_jobs_main', 'value' => 'provider', 'field' => 'post_type', 'operator' => '=' );  
                $this->sBrowseUrl = 'browse/top';
                $this->aCurrent['sorting'] = 'top';
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_browse_top_rated');
                break;

            case 'popular':
  				$this->aCurrent['restriction']['posttype'] =  array('table' => 'modzzz_jobs_main', 'value' => 'provider', 'field' => 'post_type', 'operator' => '=' ); 
                $this->sBrowseUrl = 'browse/popular';
                $this->aCurrent['sorting'] = 'popular';
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_browse_popular');
                break;                
 
            case 'today':
 
				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'today_job', 'operator' => '=');
                $this->sBrowseUrl = 'browse/today';  
                break;  
				
				
		  case 'favorite':
 
				$this->aCurrent['join']['favorites'] = array(
					'type' => 'left',
					'table' => 'modzzz_jobs_favorites',
					'mainField' => 'id',
					'onField' => 'id_entry',
					'joinFields' => '',
				);	        
 				$iProfileId = $oMain->_iProfileId;
				$iProfileId = ($iProfileId) ? $iProfileId : -999;
				$this->aCurrent['restriction']['favorite'] = array('value' => $iProfileId, 'field' => 'id_profile', 'operator' => '=', 'table' => 'modzzz_jobs_favorites');
                $this->sBrowseUrl = 'browse/favorite';
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_browse_favorite');
                break;
				
				
				// Freddy candidatures---Apply
		 case 'application':
 
				$this->aCurrent['join']['application'] = array(
					'type' => 'left',
					'table' => 'modzzz_jobs_apply',
					'mainField' => 'id',
					'onField' => 'job_id',
					'joinFields' => '',
				);	        
 				$iProfileId = $oMain->_iProfileId;
				$iProfileId = ($iProfileId) ? $iProfileId : -999;
				$this->aCurrent['restriction']['application'] = array('value' => $iProfileId, 'field' => 'member_id', 'operator' => '=', 'table' => 'modzzz_jobs_apply');
                $this->sBrowseUrl = 'browse/application';
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_browse_application');
                break;
				
		
		
		////////////////////////
				

            case 'featured':

   				$this->aCurrent['restriction']['posttype'] =  array('table' => 'modzzz_jobs_main', 'value' => 'provider', 'field' => 'post_type', 'operator' => '=' ); 

				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured';
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_browse_featured');
				$this->aCurrent['sorting'] = 'rand';
                break;  

            case 'featured_wanted':
 				$this->aCurrent['restriction']['posttype'] =  array('table' => 'modzzz_jobs_main', 'value' => 'seeker', 'field' => 'post_type', 'operator' => '=' ); 

				$this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
                $this->sBrowseUrl = 'browse/featured_wanted';
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_browse_featured_wanted');
				$this->aCurrent['sorting'] = 'rand';
				break;    

            case 'company_featured':
                $oMain = $this->getMain();
				$this->sBrowseUrl = 'browse/company_featured';
                $this->aCurrent['title'] = _t('_modzzz_jobs_company_page_title_browse_featured');
         
 		        $this->aCurrent['restriction']['featured'] = array('value' => 1, 'field' => 'featured', 'operator' => '=');
				$this->aCurrent['table'] = 'modzzz_jobs_companies'; 
				$this->aCurrent['ownFields'] = array('id','company_name','company_uri','company_desc','company_tags','company_address','company_country','company_city','company_state','company_zip','company_website','company_email','company_telephone','company_fax','company_author_id','company_icon','business_type','employee_count','office_count','job_count','rate','status','created'); 
  
				$this->aCurrent['searchFields'] = array('company_name', 'company_desc', 'company_tags');
                $this->aCurrent['restriction']['activeStatus'] = array('value' => 'approved', 'field' => 'status', 'operator' => '=');
 
				$this->aCurrent['restriction']['public']['field'] ='allow_view_company_to';
				$this->aCurrent['restriction']['owner']['field'] ='company_author_id';

  				$this->aCurrent['rss']['fields']['Title'] ='company_name';
  				$this->aCurrent['rss']['fields']['Desc'] ='company_desc';
 
 				$this->aCurrent['sorting'] = 'rand';

 				unset($this->aCurrent['join']);  
				break;  

            case 'calendar':
 
                $this->aCurrent['restriction']['calendar-min'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 00:00:00')", 'field' => 'created', 'operator' => '>=', 'no_quote_value' => true);
                $this->aCurrent['restriction']['calendar-max'] = array('value' => "UNIX_TIMESTAMP('{$sValue}-{$sValue2}-{$sValue3} 23:59:59')", 'field' => 'created', 'operator' => '<=', 'no_quote_value' => true);
                $this->sBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->sEventsBrowseUrl = "browse/calendar/{$sValue}/{$sValue2}/{$sValue3}";
                $this->aCurrent['title'] = _t('_modzzz_jobs_page_title_browse_by_day') . sprintf("%04u-%02u-%02u", $sValue, $sValue2, $sValue3);
                break;                                

             case 'companies':  
                $oMain = $this->getMain();
                
				if (isset($_REQUEST['filter'])) 
 					$this->aCurrent['restriction']['owner']['value'] = $oMain->_iProfileId;
 
                $this->sBrowseUrl = "browse/companies/";
                $this->aCurrent['title'] = _t('_modzzz_jobs_company_page_title_browse');
			    $this->aCurrent['table'] = 'modzzz_jobs_companies'; 
				$this->aCurrent['ownFields'] = array('id','company_name','company_uri','company_desc','company_tags','company_address','company_country','company_city','company_state','company_zip','company_website','company_email','company_telephone','company_fax','company_author_id','company_icon','job_count','rate','status','created'); 
				$this->aCurrent['searchFields'] = array('company_name', 'company_desc', 'company_tags');
                $this->aCurrent['restriction']['activeStatus'] = array('value' => 'approved', 'field' => 'status', 'operator' => '=');
 
				$this->aCurrent['restriction']['public']['field'] ='allow_view_company_to';
				$this->aCurrent['restriction']['owner']['field'] ='company_author_id';

  				$this->aCurrent['rss']['fields']['Title'] ='company_name';
  				$this->aCurrent['rss']['fields']['Desc'] ='company_desc';
 
 				unset($this->aCurrent['join']);  
 
				//unset($this->aCurrent['rss']);  
            break;
 
            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_modzzz_jobs');
                break;

            default:
                $this->isError = true;
        }

        $oMain = $this->getMain();

        $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_jobs_perpage_browse');

        if (isset($this->aCurrent['rss']))
            $this->aCurrent['rss']['link'] = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . $this->sBrowseUrl;
 
		if(in_array($sMode, array('companies','company_featured'))){
			
			if (bx_get('rss')) { 
				$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_jobs_max_rss_num');
			}			
			
			bx_import('CompanyVoting', $oMain->_aModule);
			$oVotingView = new BxJobsCompanyVoting ('modzzz_jobs_company', 0);
			$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null; 
		}else{

			if (bx_get('rss')) {
				$this->aCurrent['ownFields'][] = 'desc';
				$this->aCurrent['ownFields'][] = 'created';
				$this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_jobs_max_rss_num');
			}

			bx_import('Voting', $oMain->_aModule);
			$oVotingView = new BxJobsVoting ('modzzz_jobs', 0);
			$this->oVotingView = $oVotingView->isEnabled() ? $oVotingView : null;
		}

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
			$aSql['order'] = " ORDER BY `".$this->aCurrent['table']."`.`created` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'top') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `".$this->aCurrent['table']."`.`rate` DESC, `modzzz_jobs_main`.`rate_count` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'popular') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `".$this->aCurrent['table']."`.`views` DESC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'rand') {
			$aSql = array();
			$aSql['order'] = " ORDER BY rand()";
			return $aSql;  
		}
	    return array();
    }
 
    function displayCompanyResultBlock () { 
		global $oFunctions;

        $aData = $this->getSearchData();
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $s = $this->addCustomParts();
            foreach ($aData as $aValue) {
                $s .= $this->displayCompanySearchUnit($sType, $aValue);
            }
 
            $oMain = $this->getMain();
            $GLOBALS['oSysTemplate']->addDynamicLocation($oMain->_oConfig->getHomePath(), $oMain->_oConfig->getHomeUrl());
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
        }
        return '';
    }

    function displayCompanySearchUnit ($sType, $aData) {
        $oMain = $this->getMain();
	 
		return $oMain->_oTemplate->company_unit($aData, $this->sUnitTemplate, $this->oVotingView);
 	}
 
    function displayResultBlock () {

		if($this->aCurrent['table'] == 'modzzz_jobs_companies'){ 
			return $this->displayCompanyResultBlock ();
		}

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
        return BxDolModule::getInstance('BxJobsModule');
    }

    function getRssUnitLink (&$a) {
        $oMain = $this->getMain();

		if($this->aCurrent['table'] == 'modzzz_jobs_companies'){ 
			return BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'companyview/' . $a['company_uri'];
		}else{
			return BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'view/' . $a['uri'];
		}
    }
    
    function _getPseud () {
        return array(    
            'id' => 'id',
            'title' => 'title',
            'uri' => 'uri',
            'created' => 'created',
            'author_id' => 'author_id',
            'NickName' => 'NickName',
            'thumb' => 'thumb'
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
 
}