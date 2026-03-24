<?php

/***************************************************************************
*                           Expertzkris Admin Protection Plugin
*                              -------------------
*     begin                : Mon Mar 26 2012
*     copyright            : (C) 2012 Dexpertz Website Solutions
*     website              : http://www.Dexpertz.net
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* Application/Profile Tabs is not free and you cannot redistribute and/or modify it.
* 
* Application/Profile Tabs is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@Dexpertz.net. 
* 
* For more details please write to info@Dexpertz.net
**********************************************************************************/

bx_import ('BxDolTwigTemplate');class RwProfileTabTemplate extends BxDolTwigTemplate {    var $oDb;		function RwProfileTabTemplate(&$oConfig, &$oDb) {	    parent::BxDolTwigTemplate($oConfig, $oDb);    }		function unit($aData, $sTemplateName) {        if (null == $this->_oMain)            $this->_oMain = BxDolModule::getInstance('RwProfileTabModule');        $aResult = $this->_getUnit($aData);        return $this->parseHtmlByName($sTemplateName . '.html', $aResult);    }}?>