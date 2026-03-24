<?php

    /***************************************************************************
    *                            Dolphin Smart Community Builder
    *                              -------------------
    *     begin                : Mon Mar 23 2006
    *     copyright            : (C) 2007 BoonEx Group
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

    bx_import('BxDolModuleTemplate');

    class TscalaSplashBuilderTemplate extends BxDolModuleTemplate 
    {
    	/**
    	 * Class constructor
    	 */
    	function TscalaSplashBuilderTemplate(&$oConfig, &$oDb) 
        {
    	   parent::BxDolModuleTemplate($oConfig, $oDb);
    	}

        function pageCodeAdminStart()
        {
            ob_start();
        }

        function adminBlock ($sContent, $sTitle, $aMenu = array()) 
        {
            return DesignBoxAdmin($sTitle, $sContent, $aMenu);
        }

        function pageCodeAdmin ($sTitle) 
        {
            global $_page;        
            global $_page_cont;

            $_page['name_index'] = 9; 

            $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
            $_page['header_text'] = $sTitle;
            
            $_page_cont[$_page['name_index']]['page_main_code'] = ob_get_clean();

            PageCodeAdmin();
        }

        

       

		/**
		 * Get design box
		 * 
		 * @param $sTitle string
		 * @param $sContent text
		 * @param $aTopMenu array
		 * @param $sWrapper string
		 * @param $sIcon string
		 * @return text
		 */
        function getDesignBox($sTitle, $sContent, $aTopMenu = array(), $sWrapper = '', $sIcon = '')
        {
        	if($sWrapper) {
        		$sContent = '<div id="' . $sWrapper . '">' . $sContent . '</div>';
        	}

        	if($sIcon) {
        		$sTitle = '<img src="' . $this -> getIconUrl($sIcon) . '" />' . $sTitle;
        	}

            return DesignBoxContent($sTitle, $sContent, 1, $this -> _getBoxTopMenuItems($aTopMenu) );
        }

       

        /**
         * Function will generate default dolphin's page;
         *
         * @param  : $sPageCaption   (string) - page's title;
         * @param  : $sPageContent   (string) - page's content;
         * @param  : $sPageIcon      (string) - page's icon;
         * @param $iPageIndex integer
         * @return : (text) html presentation data;
         */
        function getPage($sPageCaption, $sPageContent, $sPageIcon = '', $sCssFile = '', $iPageIndex = 54)
        {
            global $_page;
            global $_page_cont;

            $_page['name_index']	= $iPageIndex;

            // set module's icon;
            if($sPageIcon) {
                $GLOBALS['oTopMenu'] -> setCustomSubIconUrl( $this -> getIconUrl($sPageIcon) ); 
            }

            $GLOBALS['oTopMenu'] -> setCustomSubHeader($sPageCaption);

            $_page['header']        = $sPageCaption ;
            $_page['header_text']   = $sPageCaption ;
            
            if($sCssFile) {
                $_page['css_name']  = $sCssFile;
            }    

            $_page_cont[$iPageIndex]['page_main_code'] = $sPageContent;
            PageCode($this);
        }

        /**
         * Get sort block
         * 
         * @param $sField string
         * @return text
         */
        function getSortBlock($sField)
        {
        	$aKeys = array(
        		'content' => $sField,
        	);

        	return $this -> parseHtmlByName('init_splash_builder.html', $aKeys);
        }

		function addCssAdmin ($sName) {        
	        if (empty($GLOBALS['oAdmTemplate'])) 
	            return;        
	        $GLOBALS['oAdmTemplate']->addCss ($sName);
    	}

        /**
         * Get pages list
         * 
         * @param $aPages array
         * @param $sPageUrl string
         * @param $sButtons string
         * @return text
         */
        function getAliasesList($aPages, $sPageUrl, $sButtons)
        {
        	$aKeys = array(
        		'page_url' => $sPageUrl,
        		'bx_repeat:pages' => $aPages,
        		'buttons' => $sButtons,
        	);

			return $this -> parseHtmlByName('init_splash_builder.html', $aKeys)
				. $this -> addCss('splash_builder_admin.css', true);
        }

    	/**
    	 * Generate admin filter field
    	 * 
    	 * @param $sFilter string
    	 * @return text
    	 */
    	function getAdminFilterList($sFilter = '')
    	{
    		$aKeys = array(
    			'filter' => $sFilter,
   			);

    		return  $this -> parseHtmlByName('init_splash_builder.html', $aKeys);
    	}
    }