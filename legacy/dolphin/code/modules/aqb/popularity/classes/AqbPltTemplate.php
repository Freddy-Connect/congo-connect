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

class AqbPltTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function AqbPltTemplate(&$oConfig, &$oDb) {
	    parent::BxDolModuleTemplate($oConfig, $oDb);
	}

	function getMemberMenuPopup($iUserId) {
		$aTmplVarsItems = array();

		$aTypes = array(AQB_PLT_TYPE_VIEWED_ME, AQB_PLT_TYPE_FAVORITED_ME, AQB_PLT_TYPE_SUBSCRIBED_ME);
		foreach($aTypes as $sType)
			$aTmplVarsItems[] = array(
				'item' => $this->getMemberMenuItem($sType, $iUserId)
			);

		$sCss = $this->addCss(array('menu.css'), true);
		return $sCss . $this->parseHtmlByName('member_menu.html', array(
			'bx_repeat:items' => $aTmplVarsItems
		));
	}

	function getMemberMenuItem($sType, $iUserId) {
		$aProfiles = $this->_oDb->getProfiles($sType, $iUserId);
		$aProfilesNew = $this->_oDb->getProfilesNew($sType, $iUserId);
		$bProfilesNew = isset($aProfilesNew['count']) && (int)$aProfilesNew['count'] > 0;

		$aTmplVarsProfiles = array();
		if($bProfilesNew)
			foreach($aProfilesNew['ids'] as $iId)
				$aTmplVarsProfiles[] = array(
					'profile' => get_member_icon($iId, 'none', true)
				);

		return $this->parseHtmlByName('member_menu_item.html', array(
			'type' => $sType,
			'count' => _t('_aqb_plt_txt_' . $sType, $aProfiles['count']),
			'bx_if:show_profiles_new' => array(
				'condition' => $bProfilesNew,
				'content' => array(
					'bx_repeat:profiles' => $aTmplVarsProfiles
				)
			)
		));
	}

	function getProfilesBlock($sType) {
		$iUserId = getLoggedId();
		$sJsObject = $this->_oConfig->getJsObject('main');

		$iStart = 0;
		$iPerPage = $this->_oConfig->getPerPage();
		$sFilter = $this->_oConfig->getDefaultDuration();
		$aDurations = array(AQB_PLT_DURATION_TODAY, AQB_PLT_DURATION_WEEK, AQB_PLT_DURATION_ALL);

		$aTopMenu = array();
		if($sType != AQB_PLT_TYPE_SUBSCRIBED_ME)
			foreach($aDurations as $sDuration)
				$aTopMenu['aqb-' . $sDuration] = array('href' => 'javascript:void(0)', 'title' => _t('_aqb_plt_tab_' . $sDuration), 'onclick' => $sJsObject . ".changeFilter('" . $sType . "', '" . $sDuration . "', this);", 'active' => $sDuration == $sFilter ? 1 : 0);

		$sContent = $this->parseHtmlByName('profiles_block.html', array(
			'html_id' => $this->_oConfig->getHtmlId('profiles_' . $sType),
			'profiles' => $this->getProfiles($sType, $iUserId, $iStart, $iPerPage, $sFilter),
			'loading' => LoadingBox($this->_oConfig->getHtmlId('loading_' . $sType)),
			'script' => $this->getJsInclude('main', true)
		));

		$this->addJs(array('main.js'));
		$this->addCss(array('main.css'));
		return array($sContent, $aTopMenu, array(), true, 'getBlockCaptionMenu');
	}
	function getProfiles($sType, $iUserId, $iStart, $iPerPage, $sDuration) {
		$sJsObject = $this->_oConfig->getJsObject('main');
		$aProfiles = $this->_oDb->getProfiles($sType, $iUserId, $sDuration, $iStart, $iPerPage);
		if((int)$aProfiles['count'] == 0)	
			// Freddy ne pas affiche quand c'est vide
			//return MsgBox(_t('_aqb_plt_msg_no_results'));
			return;

		$aTmplVarsProfiles = array();
		foreach($aProfiles['ids'] as $iId)
			$aTmplVarsProfiles[] = array(
				'profile' => get_member_thumbnail($iId, 'none', true)
			);

		$oPaginate = new BxDolPaginate(array(
            'start' => $iStart,
            'per_page' => $iPerPage,
			'count' => $aProfiles['count'],

			'on_change_page' => $sJsObject . ".changePage('" . $sType . "', {start}, {per_page});"
        ));
        $sPaginate = $oPaginate->getSimplePaginate('', $iStart, $iPerPage, false);
        
		return $this->parseHtmlByName('profiles.html', array(
			'bx_repeat:profiles' => $aTmplVarsProfiles,
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
			var <?php echo $sJsObject; ?> = new AqbPltMain({
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