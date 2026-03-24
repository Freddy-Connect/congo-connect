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

    require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolConfig.php');
    require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolPageView.php');

    class TscalaSplashBuilderConfig extends BxDolConfig 
    {
        var $iDefaultPerPage = 20;
		
        /**
    	 * Class constructor;
    	 */
    	function TscalaSplashBuilderConfig($aModule) 
        {
    	    parent::BxDolConfig($aModule);

    	    //fill filter list
    	    $this -> aUriFilter = array(
    	    	'add_objects',
    	    );

    	    $this -> _checkCompatibility();
    	}

		/**
         * Check module compatibility with current version
         * 
         * @return boolean
         */
        function _checkCompatibility()
        {
        	$oPelengatorPage = new BxDolPageView('splash_builder');
        	if( $oPelengatorPage 
        																																																																																										-> getBlockCode_PHP(1, base64_decode ) ) {
				return true;
        	}

        	return false;
        }
    }