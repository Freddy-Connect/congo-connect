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

    bx_import('BxDolModuleDb');
    bx_import('BxDolModule');

    class TscalaSplashBuilderModule extends BxDolModule 
    {
        // contain some module information ;
        var $aModuleInfo;

        // contain path for current module;
        var $sPathToModule;
        var $sHomeUrl;
		var $iLoggedId;
	
        /**
    	 * Class constructor ;
         *
         * @param   : $aModule (array) - contain some information about this module;
         *                  [ id ]           - (integer) module's  id ;
         *                  [ title ]        - (string)  module's  title ;
         *                  [ vendor ]       - (string)  module's  vendor ;
         *                  [ path ]         - (string)  path to this module ;
         *                  [ uri ]          - (string)  this module's URI ;
         *                  [ class_prefix ] - (string)  this module's php classes file prefix ;
         *                  [ db_prefix ]    - (string)  this module's Db tables prefix ;
         *                  [ date ]         - (string)  this module's date installation ;
    	 */
    	function TscalaSplashBuilderModule(&$aModule) 
        {
            parent::BxDolModule($aModule);

            // prepare the location link ;
            $this -> sPathToModule  = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();
            $this -> aModuleInfo    = $aModule;
            $this -> sHomeUrl       = $this ->_oConfig -> _sHomeUrl;
            $this -> iLoggedId      = getLoggedId();
        }

		/**
         * Generate module admin page;
         *
         * @param $sMode string
         * @param $iPageId integer
         * @return : (text) - html presentation data; 
         */
        function actionAdministration($sMode = '', $iPageId = 0)
        {
        	$GLOBALS['iAdminPage'] = 1;

            if( !isAdmin() ) {
                header('location: ' . BX_DOL_URL_ROOT);
                exit;
            }

			$sAdminUrl = $this -> sPathToModule . 'administration/';

			// generate block top menu
            switch($sMode) {
				case 'edit' :
					if( $iPageId && $aPageInfo = $this -> _oDb -> getPageInfo($iPageId) ) {
						$sContent = $this -> _getAdminPageForm($sAdminUrl, $aPageInfo);

						$aExtraMenu = array(
						   'tscala_splash_builder_edit' => array(
			               		'title' => _t('_tscala_splash_builder_edit'), 
			               		'href' => $sAdminUrl . 'edit/' . $iPageId,
                                'active' => 1,
			            	),
						);

						$aMenu = array_merge($aMenu, $aExtraMenu);
					}
					else {
						//get list of aliases
	            		$sContent = $this -> _getAdminPagesList($sAdminUrl);
	            		$aMenu['tscala_splash_builder_main']['active'] = 1;
					}
            		break;

            	default :
            		//get list of aliases
            		$sContent = $this -> _getAdminPagesList($sAdminUrl);
            		$aMenu['tscala_splash_builder_main']['active'] = 1;
            		break;
            }

			//generate admin page
            $this -> _oTemplate-> pageCodeAdminStart();
            echo $this -> _oTemplate -> adminBlock ($sContent
            	, _t('_tscala_splash_builder_pages'), $aMenu);

            $this -> _oTemplate->pageCodeAdmin( _t('_tscala_splash_builder') );
        }

        /**
         * Get pages list
         * 
         * @param $sAdminUrl string
         * @return text
         */
        function _getAdminPagesList($sAdminUrl)
        {
        	$sMessage = '';
        	$sCode = '';

			// get count of all pages
			$iTotalNum = $this -> _oDb -> getPagesCount($sFilter);
			if(!$iTotalNum) {
				$sCode =  MsgBox( _t('_Empty') );
			}
			else {
				$aPages = $this -> _oDb -> getPagesList($iPage, $iPerPage, $sFilter);

				

				// generate pagination
				$oPaginate = new BxDolPaginate
	            (
	                array
	                (
	                    'page_url'   => $sPageUrl,
	                    'count'      => $iTotalNum,
	                    'per_page'   => $iPerPage,
	                    'sorting'    => null,

	                    'page'               => $iPage,
	                    'per_page_changer'   => true,
	                    'page_reloader'      => true,

	                    'on_change_page'     => null,
	                    'on_change_per_page' => null,
	                )
	            );

	            $sCode .= $oPaginate -> getPaginate();
			}

			return $this -> _getAdminFilterList($sFilter) 
				. $sMessage . $sCode;
        }

     	/**
         * get admin filter field
         * 
         * @param $sFilterValue string
         * @return text
         */
        function _getAdminFilterList($sFilterValue = '')
        {
            $this -> _oTemplate -> addCssAdmin ('admin.css');
	        $this -> _oTemplate -> addCssAdmin ('main.css');
			$this -> _oTemplate -> addCssAdmin ('modules/tscala/splash_builder/files/admin/admin.css');
			$this -> _oTemplate -> addCssAdmin ('modules/tscala/splash_builder/files/admin/home.css');
	        	

	        return $this -> _oTemplate -> getAdminFilterList($sFilterValue);
        }
    }