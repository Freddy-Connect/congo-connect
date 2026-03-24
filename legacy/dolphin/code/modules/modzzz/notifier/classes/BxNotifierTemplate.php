<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Confession
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

bx_import('BxDolTwigTemplate');
bx_import('BxDolCategories');

/*
 * Notifier module View
 */
class BxNotifierTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    
	/**
	 * Constructor
	 */
	function BxNotifierTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }

    function email_unit ($aData, $sTemplateName, $bShowHeader=false, $sPostDate='') {
  
        $aVars = array (
			'topic' => $aData['topic'], 
            'subject' => $aData['subject'],
            'message' => $aData['message'],  
			'bx_if:header' => array( 
				'condition' => $bShowHeader,
				'content' => array(
					'header' => _t('_modzzz_notifier_notifications_for_period', $sPostDate)  
				) 
			), 
        );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
     }
  
     function error_unit ($sMessage) {
  
        $aVars = array (
			'message' => _t($sMessage)   
        );
 
        return $this->parseHtmlByName('error_unit', $aVars);
     }


}
