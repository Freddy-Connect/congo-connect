<?php
/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*      
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY. 
* To be able to use this product for another domain names you have to order another copy of this product (license).
* 
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
* 
* This notice may not be removed from the source code.
* 
***************************************************************************/

bx_import('BxDolModuleTemplate');

class AqbGstTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function AqbGstTemplate(&$oConfig, &$oDb) {
	    parent::BxDolModuleTemplate($oConfig, $oDb);
	}

	function getBlockGuests() {
		$iUserId = getLoggedId();
		$sJsObject = $this->_oConfig->getJsObject('main');

		$iStart = 0;
		$iPerPage = $this->_oConfig->getPerPage();
		$sFilter = $this->_oConfig->getDefaultDuration();
		$aDurations = array(AQB_GST_DURATION_TODAY, AQB_GST_DURATION_WEEK, AQB_GST_DURATION_ALL);

		$aTopMenu = array();
		foreach($aDurations as $sDuration)
			$aTopMenu['aqb-' . $sDuration] = array('href' => 'javascript:void(0)', 'title' => _t('_aqb_gst_tab_' . $sDuration), 'onclick' => $sJsObject . ".changeFilter('" . $sDuration . "', this);", 'active' => $sDuration == $sFilter ? 1 : 0);

		$sContent = $this->parseHtmlByName('guests_block.html', array(
			'html_id' => $this->_oConfig->getHtmlId('guests'),
			'guests' => $this->getGuests($iUserId, $iStart, $iPerPage, $sFilter),
			'loading' => LoadingBox($this->_oConfig->getHtmlId('loading')),
			'script' => $this->getJsInclude('main', true)
		));

		$this->addJs(array('main.js'));
		$this->addCss(array('main.css'));
		return array($sContent, $aTopMenu, array(), true, 'getBlockCaptionMenu');
	}
	function getGuests($iUserId, $iStart, $iPerPage, $sDuration) {
		$sJsObject = $this->_oConfig->getJsObject('main');
		$aGuests = $this->_oDb->getGuests($iUserId, $sDuration, $iStart, $iPerPage);
		if((int)$aGuests['count'] == 0)	
			return MsgBox(_t('_aqb_gst_msg_no_results'));
			
			
			//--- AQB Soft: Befriend
		$aFriends = getMyFriendsEx($iUserId);
		$aFriendsIds = array_keys($aFriends);

		$aTmplVarsGuests = array();
		foreach($aGuests['ids'] as $iId) {
			$aTmplVarsGuests[] = array(
				'guest' => get_member_thumbnail($iId, 'none', true),
				'bx_if:befriend' => array(
					'condition' => !in_array($iId, $aFriendsIds) && !isFriendRequest($iUserId, $iId),
					'content' => array(
						'id' => $iId
					)
				)
			);
		}
		//--- AQB Soft: Befriend
			

	

		$oPaginate = new BxDolPaginate(array(
            'start' => $iStart,
            'per_page' => $iPerPage,
			'count' => $aGuests['count'],
			'on_change_page' => $sJsObject . ".changePage({start}, {per_page});"
        ));
        $sPaginate = $oPaginate->getSimplePaginate('', $iStart, $iPerPage, false);
        
		return $this->parseHtmlByName('guests.html', array(
			'bx_repeat:guests' => $aTmplVarsGuests,
			'paginate' => $sPaginate
		));
	}
	function getPageCode(&$aParams) {
		global $_page;
		global $_page_cont;

		$iIndex = isset($aParams['index']) ? (int)$aParams['index'] : 0;
		$_page['name_index'] = $iIndex;
		$_page['js_name'] = isset($aParams['js']) ? $aParams['js'] : '';
		$_page['css_name'] = isset($aParams['css']) ? $aParams['css'] : '';
		$_page['extra_js'] = isset($aParams['extra_js']) ? $aParams['extra_js'] : '';

		check_logged();

		if(isset($aParams['content']))
			foreach($aParams['content'] as $sKey => $sValue)
				$_page_cont[$iIndex][$sKey] = $sValue;

		if(isset($aParams['title']['page']))
			$this->setPageTitle($aParams['title']['page']);
        if(isset($aParams['title']['header']))
            $GLOBALS['oTopMenu']->setCustomSubHeader($aParams['title']['header']);
		if(isset($aParams['title']['block']))
			$this->setPageMainBoxTitle($aParams['title']['block']);

		if(isset($aParams['breadcrumb']) && method_exists($GLOBALS['oTopMenu'], 'setCustomBreadcrumbs'))
			$GLOBALS['oTopMenu']->setCustomBreadcrumbs($aParams['breadcrumb']);

        if(isset($aParams['actions']) && method_exists($GLOBALS['oTopMenu'], 'setCustomSubActions')) {
            $aParams = array(
            	'BaseUri' => $this->_oConfig->getBaseUri()
            );
            $GLOBALS['oTopMenu']->setCustomSubActions($aParams, $this->_oConfig->getUri() . '-header');
        }

		PageCode($this);
	}
	function getPageCodeAdmin(&$aParams) {
		global $_page;
		global $_page_cont;

		$iIndex = isset($aParams['index']) ? (int)$aParams['index'] : 9;
		$_page['name_index'] = $iIndex;
		$_page['js_name'] = isset($aParams['js']) ? $aParams['js'] : '';
		$_page['css_name'] = isset($aParams['css']) ? $aParams['css'] : '';
		$_page['header'] = isset($aParams['title']['page']) ? $aParams['title']['page'] : '';

		if(isset($aParams['content']))
			foreach($aParams['content'] as $sKey => $sValue)
				$_page_cont[$iIndex][$sKey] = $sValue;

		PageCodeAdmin();
	}
	function getJsInclude($sType, $bWrapped = false) {
		$oJson = new Services_JSON();

		ob_start();
		if($sType == 'main') {
			$sJsObject = $this->_oConfig->getJsObject('main');
?>
			var <?php echo $sJsObject; ?> = new AqbGstMain({
				sActionUrl: '<?php echo BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri(); ?>',
				sObjName: '<?php echo $sJsObject; ?>',
				sAnimationEffect: '<?php echo $this->_oConfig->getAnimationEffect(); ?>',
            	iAnimationSpeed: '<?php echo $this->_oConfig->getAnimationSpeed(); ?>',
            	oHtmlIds: <?php echo $oJson->encode($this->_oConfig->getHtmlId()); ?>
			});
<?
		}
		$sContent = ob_get_clean();

		return $bWrapped ? $this->_wrapInTagJsCode($sContent) : $sContent;
	}
}
?>