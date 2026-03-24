<?php
/***************************************************************************
*
*     copyright            : (C) 2011 AQB Soft
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

bx_import('BxDolConfig');

class AqbAutomailerConfig extends BxDolConfig {
	/**
	 * Constructor
	 */
	function AqbAutomailerConfig($aModule) {
	    parent::BxDolConfig($aModule);
	}

	function isFieldAvailable($sFieldName) {
		switch ($sFieldName) {
			case 'DateOfBirth' : $sType = 'date'; break;
			case 'AqbPoints' : $sType = 'num'; break;
			default : $sType = 'select_one';
		}
		return $GLOBALS['MySQL']->getOne("SELECT `ID` FROM `sys_profile_fields` WHERE `Name` = '{$sFieldName}' AND `Type` = '{$sType}' LIMIT 1");
	}
	function getDefaultTemplate() {
		return <<<EOF
<p><b>Dear ::RealName::</b>,</p>
<p><b>Thank you for using our services!</b></p>
<p>--</p>
<p style="font: bold 10px Verdana; color:red">::SiteName:: mail delivery system!!!
<br />Auto-generated e-mail, please, do not reply!!!</p>
EOF;
	}
}
?>