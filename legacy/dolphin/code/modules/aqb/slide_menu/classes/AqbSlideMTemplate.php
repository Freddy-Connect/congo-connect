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

bx_import('BxTemplFormView');
bx_import('BxTemplSearchResult');
bx_import('BxTemplSearchProfile');
bx_import('BxDolTwigTemplate');
bx_import('BxDolParams');

class AqbSlideMTemplate extends BxDolModuleTemplate {
	
	/**
	 * Constructor
	 */
	
	function AqbSlideMTemplate(&$oConfig, &$oDb) {
	    parent::BxDolModuleTemplate($oConfig, $oDb);
		$this -> _oConfig -> init($oDb);
	}		
    
	function pageCodeAdmin ($sTitle)
    {
        global $_page;
        global $_page_cont;

        $_page['name_index'] = 9;

        $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
        $_page['header_text'] = $sTitle;

        $_page_cont[$_page['name_index']]['page_main_code'] = $this->pageEnd();		
        PageCodeAdmin();
    }
	
	function parseHtmlByName ($sName, $aVars) {        
        return parent::parseHtmlByName ($sName.'.html', $aVars);
    }	
	
	function getSettingsPanel() {
        $iId = $this -> _oDb -> getSettingsCategory();

        if(empty($iId))
           return MsgBox(_t('_aqb_slidem_nothing_found'));

        bx_import('BxDolAdminSettings');

        $mixedResult = '';

        if(isset($_POST['save']) && isset($_POST['cat'])) {
            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings -> saveChanges($_POST);
			$oSettings -> _onSavePermalinks();
        }
        
        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings -> getForm();
                   
			
        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;
        return $sResult;
    }
	 
	private function getSumMenus($iTItemID){
		$aItems = $GLOBALS['oTopMenu'] -> aTopMenu;
		$aSumMenus = array();		
		
		foreach( $aItems as $iItemID => $aItem ) {
	            if( $aItem['Type'] != 'custom' )
	                continue;
	            if( $aItem['Parent'] != $iTItemID )
	                continue;
	            if( !$GLOBALS['oTopMenu'] -> checkToShow( $aItem ) )
	                continue;

	            list( $aItem['Link'] ) = explode( '|', $aItem['Link'] );

	            $aItem['Link']    = $GLOBALS['oTopMenu'] -> replaceMetas( $aItem['Link'] );
	            $aItem['Onclick'] = $GLOBALS['oTopMenu'] -> replaceMetas( $aItem['Onclick'] );

	            $bActive = ( $iItemID == $GLOBALS['oTopMenu'] -> aMenuInfo['currentCustom'] );
		
	            $aSumMenus[] = array(
										'sub_title' => _t( $aItem['Caption'] ), 
										'sub_link' =>  BX_DOL_URL_ROOT . $aItem['Link'], 
										'target' => $aItem['Target'] ? ' target="' . $aItem['Target'] . '"' : '',
										'sub_onclick' => $aItem['Onclick'] ? $aItem['Onclick'] : 'javascript:void(0);', 
										'active' => (int)$bActive ? 'class="active"' : ''
									);						
			}
		
		return $aSumMenus;
	}
	
	private function getMenuArray(&$aMenu){
		$aItems = $GLOBALS['oTopMenu'] -> aTopMenu;
		$GLOBALS['oTopMenu'] -> aMenuInfo = $aMenu;

		$aTopItems = array();
		$aSumMenus = array();
		
		foreach($aItems as $iItemID => $aItem ) {
			if( $aItem['Type'] != 'top' )
				continue;
			
			if( !$GLOBALS['oTopMenu'] -> checkToShow( $aItem ) )
				continue;

			if ($iItemID == $GLOBALS['oTopMenu'] -> aMenuInfo['currentTop']) continue;
				
			list( $aItem['Link'] ) = explode( '|', $aItem['Link'] );
	
			$aTopItem['Onclick'] = $GLOBALS['oTopMenu'] -> replaceMetas( $aItem['Onclick'] );


			$sPicture = $aItem['Icon'] ? $aItem['Icon'] : $aItem['Picture'];
			
			$sPictureEl = '';
			if (!empty($sPicture) && false === strpos($sPicture, '.'))
                $sPictureEl = '<i class="sys-icon ' . $sPicture . '"></i>';
            elseif (!empty($sPicture))
                $sPictureEl = '<i><img class="img_submenu" src="' . getTemplateIcon($sPicture) . '" alt="" /></i>';
			
			$aSubMenu = $this -> getSumMenus($iItemID);	

			
			$aTopItems[] = array(
									'main_title' => _t($aItem['Caption']), 
									'Icon' => $sPictureEl,
									'link' => $aItem['Link'] && empty($aSubMenu) ? $aItem['Link'] : 'javascript:void(0);',
									'onclick' => $aItem['Onclick'] ? $GLOBALS['oTopMenu'] -> replaceMetas( $aItem['Onclick'] ) : 'javascript:void(0);',
									'bx_if:sub_menu' => array(
																'condition' => !empty($aSubMenu),
																'content' => array(
																					'main_title' => _t($aItem['Caption']),
																					'Icon' => $sPictureEl,
																					'bx_repeat:sub_menu' => $aSubMenu
																				  )
															 )
								);
		}	

		return $aTopItems;
	}	 
	
	function loadMenu(&$aMenu){		
		return $this -> parseHtmlByName('settings_' . $this -> _oConfig -> getMenuType(), array('bx_repeat:menu' => $this -> getMenuArray($aMenu)));
	}
	
	function getMenuContent($sParam = 'open'){		
		$GLOBALS['oTopMenu'] -> getMenuInfo();	
		$sObject = json_encode($GLOBALS['oTopMenu'] -> aMenuInfo);
		$iWidth = (int)$this -> _oConfig -> getWidth();
		$sType = $this -> _oConfig -> getMenuType();
		$sSide = $this -> _oConfig -> getMenuSide();
		$sBackText = bx_js_string(_t('_aqb_slidem_back'));
		
		if ($sType == 'simple'){ 
			$sMainOpen = '<main id="panel" class="panel">';
 			$sMainClose = '</main>';
		}
		
		if ($sParam == 'open'){
		$sHTML .=<<<EOF
		<script>
			var aqbMMenu = null;
			$('.topMenu').ready(function(){
				aqbMMenu = new aqbMobileMenu({oMenuOptions:{$sObject}, iWidth:{$iWidth}, sType:'{$sType}', sSide:'{$sSide}', sBack: '{$sBackText}'});
				aqbMMenu.init();
			});			
		</script>
		{$sMainOpen}
EOF;
		}else 
		{ 
			$sHTML =<<<EOF
	    {$sMainClose}
EOF;
}		
		
		return $sHTML;
	 }
}
?>