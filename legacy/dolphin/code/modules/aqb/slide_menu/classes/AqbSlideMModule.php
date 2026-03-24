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

bx_import('BxDolModule');
bx_import('BxDolPageView');
bx_import('BxDolAlbums');
		
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

class AqbSlideMModule extends BxDolModule {
	
	/**
	 * Constructor
	 */
	function AqbSlideMModule($aModule) {
		parent::BxDolModule($aModule);
		$this -> iUserId = $GLOBALS['logged']['member'] || $GLOBALS['logged']['admin'] ? $_COOKIE['memberID'] : 0;
	}
	
	function isAdmin(){
		return isAdmin($this->iUserId);
	}
	
	function actionAdministration() {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        
	
		$this -> _oTemplate -> pageStart();		
      
		$sContent = $this -> _oTemplate -> getSettingsPanel();
		echo DesignBoxAdmin($aMenu[$sUrl]['title'], $sContent, $aMenu, '', 11);        
		$this -> _oTemplate -> pageCodeAdmin(_t('_aqb_slidem_admin'));
	}
	
	
	function serviceGetPageCode($sParam = ''){
		if (!$this -> _oConfig -> isMenuEnabled() || stripos($_SERVER['QUERY_STRING'], 'member_logout') !== FALSE || (stripos($_SERVER['PHP_SELF'], 'member.php') && (isset($_POST['ID']) && $_POST['Password'])) || !$sParam) return '';
		
		if ($sParam == 'open'){
			$this -> _oTemplate -> addCss(array('slideout.css', 'multilevelpushmenu.css'));
			$this -> _oTemplate -> addJs(array('slideout.min.js', 'main.js','multilevelpushmenu.min.js')); 
		}
		
		return $this -> _oTemplate -> getMenuContent($sParam);
	}
	
	function actionLoadMenu(){
			if (!$this -> _oConfig -> isMenuEnabled()) echo '';
		else
		{
			header('Content-Type: text/html; charset=UTF-8');
			echo $this -> _oTemplate -> loadMenu($_GET['menu']);
		}
	   
		exit;
	}
}
?>