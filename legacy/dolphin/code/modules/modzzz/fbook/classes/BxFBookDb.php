<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx FBook
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

bx_import('BxDolTwigModuleDb');
bx_import('BxDolPageViewAdmin');

/*
 * FBook module Data
 */
class BxFBookDb extends BxDolTwigModuleDb {	

    var $oBlocksCacheObject;

	/*
	 * Constructor.
	 */
	function BxFBookDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);

		$this->_oConfig = $oConfig; 
	} 

	function checkForNewPage(){
		$aDbPages = $this->getAll("SELECT `Name` FROM `sys_page_compose_pages`");
 
		foreach($aDbPages as $aEachPage){
			$sPage = $aEachPage['Name'];

			if(!($iPageId = $this->getOne("SELECT `ID` FROM `sys_page_compose` WHERE `Page`='$sPage' AND `Caption`='_modzzz_fbook_block_comments' LIMIT 1"))){

				$this->query("INSERT INTO `sys_page_compose` (`Page`, `Desc`, `Caption`, `Func`,`Content`) VALUES 
				 ('$sPage', 'Facebook Comments', '_modzzz_fbook_block_comments', 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''fbook'', ''comments_block'');')");  
			}
		}//end foreach
 
	}


}
