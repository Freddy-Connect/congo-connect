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

bx_import('BxBaseSearchProfile');

class AqbAffSearchProfile extends BxBaseSearchProfile {
	var $_oMain = null;
	
	function AqbAffSearchProfile(&$oMain) {
		parent::BxBaseSearchProfile();
		$this -> _oMain = $oMain;
	}
	
	function displaySearchUnit($aData, $aExtendedCss = array()) {
		$sCode = '';
	
		$sTemplateName = 'search_profiles_sim';

		if ($sTemplateName) {
			if ($aData['Couple'] > 0) {
				$aProfileInfoC = getProfileInfo( $aData['Couple'] );
				$sCode .= $this -> PrintSearhResult( $aData, $aProfileInfoC, $aExtendedCss, $sTemplateName, $this -> _oMain -> _oTemplate);
			} else {
				$sCode .= $this -> PrintSearhResult( $aData, array(), $aExtendedCss, $sTemplateName, $this -> _oMain -> _oTemplate);
			}
		}
		return $sCode;
	}
}
?>