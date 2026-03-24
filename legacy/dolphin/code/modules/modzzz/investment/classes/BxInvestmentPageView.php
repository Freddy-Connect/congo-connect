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

bx_import('BxDolTwigPageView');

class BxInvestmentPageView extends BxDolTwigPageView {	

	function BxInvestmentPageView(&$oMain, &$aDataEntry) {
		parent::BxDolTwigPageView('modzzz_investment_view', $oMain, $aDataEntry);
	
        $this->sSearchResultClassName = 'BxInvestmentSearchResult';
        $this->sFilterName = 'filter';

        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/'. $this->aDataEntry['uri'];
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  
 
		switch($_GET['ajax']) {  
			case 'delete_inquiry':
				$iEntryId = $_GET['id'];
				$this->_oDb->deleteInquiry($iEntryId); 
				echo '';
				exit;
			break; 
		}

	}
  
	function getBlockCode_Info() {
        return array($this->_blockInfo ($this->aDataEntry));
    }

	//override the similar mod in the parent class
    function _blockInfo ($aData) {
        $iAuthorId = (int)$aData['author_id'];
        
		$aAuthor = getProfileInfo($aData['author_id']);
 
		$sAuthorName =  $aAuthor['NickName'];
		$sAuthorLink = getProfileLink($aAuthor['ID']);	
		$icoThumb = get_member_thumbnail($aAuthor['ID'], 'none', true);
		
		
		 $sBusinessFlag = genFlag($aData['country']);
	    $sBusinessCountry= $aData['country'];
         $sBusinessProvince = ($aData['state']) ? $this->_oMain->_oDb->getStateName($sBusinessCountry, $aData['state']) : '';
		
		
		//Freddy ajout Befirend
		$iUserId = getLoggedId();
		$iId = $aData['author_id'];
		$aFriends = getMyFriendsEx($iUserId);
		$aFriendsIds = array_keys($aFriends);
		// [END] Freddy ajout Befirend
		$sUrl_compose =  BX_DOL_URL_ROOT  . 'mail.php?mode=compose&recipient_id='.$iAuthorId ;
	 
        $aVars = array (
		
		 //Freddy ajout Befirend
		 
	 'bx_if:befriend' => array(
	'condition' => !in_array($iId, $aFriendsIds) && !isFriendRequest($iUserId, $iId) &&  !$this->_oMain->isEntryAdmin($aData),
	 'content' => array(
	  'id' => $iId
					    ),
				           ),
		// [END] Freddy ajout Befirend
		'message_url'=>  $sUrl_compose,
		  
		  
		     'views'  => $aData['views'],
		    'city' => $aData['city'],
			'selleremail'  => $aData['selleremail'],
			'sellertelephone'  => $aData['sellertelephone'],
			
			 'bx_if:sellerwebsite' => array( 
								'condition' =>$aData['sellerwebsite'],
								'content' => array(
              'sellerwebsite' => $aData['sellerwebsite'],  
								), 
							),
			
			'country' => _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']),
			'province' => $sBusinessProvince,
			'drapeau' => $sBusinessFlag,
			
            'author_unit' => $icoThumb,
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
            'cats' => $this->_oTemplate->parseCategories($aData['categories']),
			'tags' => $this->_oTemplate->parseTags($aData['tags']),
            'fields' => '',
 
			'bx_if:owner' => array( 
				'condition' =>  $this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($aData),
				'content' => array(   
					'expire' => $aData['expiry_date'] ? date('M d, Y', $aData['expiry_date']) : _t('_modzzz_investment_never'), 
					'featured' => $aData['featured'] ? ($aData['featured_expiry_date'] ? _t('_modzzz_investment_featured_until') .' '. $this->_oTemplate->filterCustomDate($aData['featured_expiry_date']) : _t('_modzzz_investment_featured_listing')) : _t('_modzzz_investment_not_featured'),   
				),  		
			),

        );
        return $this->_oTemplate->parseHtmlByName('entry_view_block_info', $aVars);
    }
   
	function getBlockCode_Desc() {
        return array($this->_oTemplate->blockDesc ($this->aDataEntry));
    }

	function getBlockCode_Contact() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'contact');
    }

	function getBlockCode_Location() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'location');
    }
 
 	function getBlockCode_Additional() { 
        return $this->_blockCustomDisplay ($this->aDataEntry, 'additional');
    }

	function _blockCustomDisplay($aDataEntry, $sType) {
		
		switch($sType)
		{  
			case "contact":
				$aAllow = array('sellername','sellerwebsite','selleremail','sellertelephone','sellerfax');
			break;
			case "location":
				$aAllow = array('address1','address2','city','state','country','zip');
			break; 
			case "additional":
				$aAllow = array('custom_field1','custom_field2','custom_field3','custom_field4','custom_field5','custom_field6','custom_field7','custom_field8','custom_field9','custom_field10','custom_sub_field1','custom_sub_field2','custom_sub_field3','custom_sub_field4','custom_sub_field5','custom_sub_field6','custom_sub_field7','custom_sub_field8','custom_sub_field9','custom_sub_field10');
			break; 
		}
  
		$sFields = $this->_oTemplate->blockCustomFields($aDataEntry,$aAllow);

		if(!$sFields) return;

		$aVars = array ( 
            'fields' => $sFields, 
        );

        return array($this->_oTemplate->parseHtmlByName('custom_block_info', $aVars));   
    }
 
	function getBlockCode_VideoEmbed() { 
		$sBlock = '';
	    $sVideoEmbed = trim($this->aDataEntry['video_embed']);
		
		if(!$sVideoEmbed)
			  return;
	 
 		$pos = (strpos($sVideoEmbed, 'youtube.com') || strpos($sVideoEmbed, 'youtu.be'));

		if ($pos === false) {  
			$sBlock = 'block_embed';  
		}else{ 
			$pos = strpos($sVideoEmbed, 'iframe');
			if ($pos === false) {  
				$sBlock = 'block_youtube'; 
				$sVideoEmbed = $this->youtubeId($sVideoEmbed);
			}else{
				$sBlock = 'block_embed';  
			}
		}
 
		if(!$sVideoEmbed)
			  return;

		$aVars = array ('embed' => $sVideoEmbed); 
			  
	    return $this->_oTemplate->parseHtmlByName($sBlock, $aVars);  
	}
 
	function youtubeId($url) {
		$url = str_replace('&amp;', '&', $url); 

		if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
			$sVideoId = $match[1];  
		}else{  
			$sVideoId = substr( parse_url($url, PHP_URL_PATH), 1 );
			$sVideoId = ltrim( $sVideoId, '/' ); 
		} 

		return $sVideoId;  
	}
   
	function getBlockCode_Photo() {
        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Video() {
        return $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Sound() {
        return $this->_blockSound ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'sounds'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Files() {

		if(!$iLoggedId = getLoggedId()) return;

        return $this->_blockFiles ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'files'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Rate() {
        modzzz_investment_import('Voting');
        $o = new BxInvestmentVoting ('modzzz_investment', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_investment_import('Cmts');
        $o = new BxInvestmentCmts ('modzzz_investment', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

            $oSubscription = new BxDolSubscription();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'modzzz_investment', '', (int)$this->aDataEntry['id']);

            $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'TitleSubscribe' => $aSubscribeButton['title'], 
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_modzzz_investment_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_investment_action_title_delete') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_investment_action_title_share') : '',
				'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_modzzz_investment_action_title_promote') : '',  
                'TitleInquire' => $this->_oMain->isAllowedInquire($this->aDataEntry) ? _t('_modzzz_investment_action_title_inquire') : '',
                'TitleBuy' => '',/*$this->_oMain->isAllowedBuy($this->aDataEntry) ? _t('_modzzz_investment_action_title_buy') : '',*/
				'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_investment_action_remove_from_featured') : _t('_modzzz_investment_action_add_to_featured')) : '',
				
				
				'AddToFavorite' => $this->_oMain->isAllowedMarkAsFavorite($this->aDataEntry) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $this->aDataEntry['id']) ? _t('_modzzz_investment_action_remove_from_favorite') : _t('_modzzz_investment_action_add_to_favorite')) : '',
				
				
				
				'TitlePurchaseFeatured' => $this->_oMain->isAllowedPurchaseFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_investment_action_title_extend_featured') : _t('_modzzz_investment_action_title_purchase_featured')) : '',
		
                'TitleRelist' => $this->_oMain->isAllowedRelist($this->aDataEntry) ? _t('_modzzz_investment_action_title_relist') : '',
                'TitleExtend' => $this->_oMain->isPaidInvestment($this->aDataEntry['id']) ? ($this->_oMain->isAllowedExtend($this->aDataEntry) ? _t('_modzzz_investment_action_title_extend') : '') : '',
                'TitlePremium' => $this->_oMain->isAllowedPremium($this->aDataEntry) ? ($this->_oMain->isPaidInvestment($this->aDataEntry['id']) ? '' : _t('_modzzz_investment_action_title_premium')) : '',
 
				'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_modzzz_investment_action_upload_photos') : '',
                'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_modzzz_investment_action_upload_videos') : '',
                'TitleUploadSounds' => $this->_oMain->isAllowedUploadSounds($this->aDataEntry) ? _t('_modzzz_investment_action_upload_sounds') : '',
                'TitleUploadFiles' => $this->_oMain->isAllowedUploadFiles($this->aDataEntry) ? _t('_modzzz_investment_action_upload_files') : '',
            );

            if (!$aInfo['TitleEdit'] && !$aInfo['TitleDelete'] && !$aInfo['TitleShare'] && !$aInfo['AddToFeatured'] && !$aInfo['TitleUploadPhotos'] && !$aInfo['TitleUploadVideos'] && !$aInfo['TitleUploadSounds'] && !$aInfo['TitleUploadFiles'] && !$aInfo['TitleInquire']) 
                return '';

            return $oSubscription->getData() . $oFunctions->genObjectsActions($aInfo, 'modzzz_investment');
        } 

        return '';
    }    
  
	function getBlockCode_Capital() {  
 
 		// freddy modif
		//$iMaxInvestment = ((int)$this->aDataEntry['max_investment']) ? $this->_oDb->getCurrencySign($this->aDataEntry['currency']).number_format($this->aDataEntry['max_investment'],2) : '';

       $iMaxInvestment = ($this->aDataEntry['max_investment']) ? $this->_oDb->getCurrencySign($this->aDataEntry['currency']).($this->aDataEntry['max_investment']) : '';
	   
		$aVars = array (

			'bx_if:min' => array( 
				'condition' => $this->aDataEntry['min_investment'],
				'content' => array( 
					// freddy modif 
			//'min_investment' => 		 $this->_oDb->getCurrencySign($this->aDataEntry['currency']).number_format($this->aDataEntry['min_investment'],2),
			'min_investment' => 	 $this->_oDb->getCurrencySign($this->aDataEntry['currency']).$this->aDataEntry['min_investment'],
				), 
			), 

			'bx_if:max' => array( 
				'condition' => $this->aDataEntry['max_investment'],
				'content' => array( 
					'max_investment' => $iMaxInvestment,
				), 
			),

		);
		return array($this->_oTemplate->parseHtmlByName('block_capital', $aVars));	 
	}


	function getBlockCode_Local() {    

		return $this->ajaxBrowse('other_local', $this->_oDb->getParam('modzzz_investment_perpage_main_recent'),array(),$this->aDataEntry['id'],$this->aDataEntry['city'],$this->aDataEntry['state']);  
	}

	function getBlockCode_Other() {    
		return $this->ajaxBrowse('other', $this->_oDb->getParam('modzzz_investment_perpage_main_recent'),array(),$this->aDataEntry['author_id'],$this->aDataEntry['id']); 
	}

	function getBlockCode_Inquiry() {  
  
		if(!($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)))
			return;
 
	    $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'view/' . $this->aDataEntry['uri'] . '?';

        $this->_oTemplate->addCss(array('unit.css', 'twig.css'));

        return $this->ajaxBrowseSubProfile(
            'inquiry', 'inquiry', 5, array(), $this->aDataEntry['id'], true, false  
        ); 
   }

   function ajaxBrowseSubProfile($sType, $sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = true, $isPublicOnly = true) {

        bx_import ('SearchResult', $this->_oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);
 
        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);
 
        if (!($s = $o->displaySubProfileResultBlock($sType))) {
             return '';
		} 

        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate('', -1, -1, false);

        return array($s, $aMenu, $sAjaxPaginate, '');  
    }    

    function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $sValue2 = '', $isDisableRss = false, $isPublicOnly = true) {
        $oMain = BxDolModule::getInstance('BxInvestmentModule');

        bx_import ('SearchResult', $oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue, $sValue2);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);

        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => getTemplateIcon('rss.png'))));

        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);

        if (!($s = $o->displayResultBlock())) 
           // freddy modif  return $isPublicOnly ? array(MsgBox(_t('_Empty')), $aMenu) : '';
		   return ;


        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->_oConfig->getBaseUri() . $o->sBrowseUrl);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    }   


    function getCode() {
 
        return parent::getCode();
    }
	
	
	
	
	function getBlockCode_ManageMyInvestment() {
		
    
		if(!$this->_oMain->isAllowedEdit($this->aDataEntry)) return; 
		
		if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
		//$add_staff_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'staff/add/' . $this->aDataEntry['id']; 
		
		$add_photos_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'upload_photos/' . $this->aDataEntry['uri']; 
		$add_files_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'upload_files/' . $this->aDataEntry['uri']; 
		$modifier_edit_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'edit/' . $this->aDataEntry['id'];
		$supprimer_edit_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'delete/' . $this->aDataEntry['id'];
		
		 

		
		
		$aVars = array(  
			
			   'add_photos_url' => $add_photos_url, 
			   'add_files_url' => $add_files_url, 
			   'modifier_edit_url' =>  $modifier_edit_url,
			   'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_investment_action_title_delete') : '',
			    'supprimer_edit_url' =>  $supprimer_edit_url, 
			  'id' =>$this->aDataEntry['id'],
			  
			   
			 
  		);
		}
		
		return $this->_oTemplate->parseHtmlByName('block_manage_investment', $aVars);   
	}
	/******************FIN FREDDY MANAGE classified****************/
	
	////////////////////////////////////////////////////////////////////////////////////////////
	
	
	function getBlockCode_ManageInvestmentUser() {
	 if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxInvestmentModule');
    
		 
		 $aData = $this->aDataEntry;
		 
	 
	  global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

            $oSubscription = new BxDolSubscription();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'modzzz_investment', '', (int)$aData['id']);
			$iAuthorId = (int)$aData['author_id'];
			
			//$isFan = $this->_oDb->isFan((int)$aData['id'], $this->_oMain->_iProfileId, 0) || $this->_oDb->isFan((int)$aData['id'], $this->_oMain->_iProfileId, 1);
	 
		
	$add_favorite_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'mark_favorite/' . $aData['id'];
	
	//$showPopupAnyHtml = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'share_popup/' . $aData['id'];
	
	//$Join_Suivre= BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'join/'. $aData['id'] . '/$this->_oMain->_iProfileId';
	
	$Renseignement_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'inquire/' . $aData['id'];
	$Inviter_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'invite/' . $aData['id'];
    $sUrl_compose =  BX_DOL_URL_ROOT  . 'mail.php?mode=compose&recipient_id='.$iAuthorId ;
  
		$aVars = array(  
		
		       'thumbnail_url' => $sUserThumbnail,
			   'Profile_Link' => $sAuthorLink,
			   
			   'TitleSubscribe' => $aSubscribeButton['title'], 
			   'ScriptSubscribe' => $aSubscribeButton['script'],
			   
				 'TitleInquire' => $this->_oMain->isAllowedInquire($this->aDataEntry) ? _t('_modzzz_investment_action_title_inquire') : '',
				  //'InquireUrl' =>$Renseignement_url,
				   'InquireUrl' =>$sUrl_compose,
				  
				  'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_modzzz_investment_action_title_promote') : '',  
				   'InviteUrl' =>$Inviter_url,

			   
			   
			   //'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_jobs_action_title_share') : '',
			   //'SharePopup' => $showPopupAnyHtml,
			   
			   
			    //'iViewer' => $this->_oMain->_iProfileId,
	// 'TitleJoin' => $this->_oMain->isAllowedJoin($aData) ? ($isFan ? _t('_modzzz_listing_action_title_leave') : _t('_modzzz_listing_action_title_join')) : '',
			   
			   //'Join_Suivre' => $Join_Suivre,
			   
			    
			   
			 'AddToFavorite' => $this->_oMain->isAllowedMarkAsFavorite($aData) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $aData['id']) ? _t('_modzzz_investment_action_remove_from_favorite') : _t('_modzzz_investment_action_add_to_favorite')) : '',
			 
			  'Favorite_Url' =>  $add_favorite_url, 
			  'id' => $aData['id'],
			  
			  
			  
			  
			 

  		);
		
  
		return $this->_oTemplate->parseHtmlByName('block_manage_page', $aVars);   
	}
	
	
	
	}
	

	
	
	//////////////////////////////////////////////
	
	
	///////////////////////////////////////////////////
}
