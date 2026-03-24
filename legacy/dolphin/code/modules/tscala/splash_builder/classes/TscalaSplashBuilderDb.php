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

    require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php' );

    class TscalaSplashBuilderDb extends BxDolModuleDb 
    {
        var $_oConfig;
        var $sTablePrefix;

    	/**
    	 * Constructor.
    	 */
    	function TscalaSplashBuilderDb(&$oConfig)
        {
    		parent::BxDolModuleDb();		

            $this -> _oConfig = $oConfig;
    	    $this -> sTablePrefix = $oConfig -> getDbPrefix();
        }

        /**
         * Get count of pages
         * 
         * @param $sFilter string
         * @return integer
         */
        function getPagesCount($sFilter = '')
        {
        	$sFilter = process_db_input($sFilter, BX_TAGS_STRIP, BX_SLASHES_AUTO);

        	$sWhere = $sFilter 
        		? ' WHERE `uri` LIKE ("%' . $sFilter . '%") OR `lang_key` LIKE("%' . $sFilter . '%")'
        		: '';

        	$sQuery = "SELECT COUNT(*) FROM `{$this ->sTablePrefix}pages` {$sWhere}";	
        	return $this -> getOne($sQuery);
        }

    }