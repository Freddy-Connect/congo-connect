<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Article
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
 
bx_import('BxDolConfig');

class BxFAQConfig extends BxDolConfig {
 
	var $_oDb;
	var $_iSnippetLength;
	var $_iTitleLength;

	/**
	 * Constructor
	 */
	function BxFAQConfig($aModule) {
	    parent::BxDolConfig($aModule);
 
	     $this->_oDb = null; 
    }

	function init(&$oDb) {
	    $this->_oDb = &$oDb;
 
	    $this->_iSnippetLength = (int)$this->_oDb->getParam('modzzz_faq_snippet_length');
		$this->_iTitleLength = (int)$this->_oDb->getParam('modzzz_faq_title_length');	    
    
	}

	function getSnippetLength(){
		return $this->_iSnippetLength;
	}

	function getTitleLength(){
		return $this->_iTitleLength;
	}
}
