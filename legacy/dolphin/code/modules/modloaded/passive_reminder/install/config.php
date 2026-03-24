<?
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

$aConfig = array(
	/**
	 * Main Section.
	 */	
	'title' => 'Passive Member Reminder', // module title, this name will be displayed in the modules list
    'version' => '1.0.0', // module version, change this number everytime you publish your mod
	'vendor' => 'ModLoaded', // vendor name, also it is a folder name in modules folder
	'update_url' => '', // url to get info about available module updates
	
	'compatible_with' => array( // module compatibility
            '7.3.x'
    ),

    /**
	 * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
	 */
	'home_dir' => 'modloaded/passive_reminder/', // folder where module files are located, it describes path from /modules/ folder.
	'home_uri' => 'passive_reminder', // module URI, so the module will be accessable via the following urls: m/bloggie/ or modules/?r=bloggie/
	
	'db_prefix' => 'ml_passive_reminder', // database prefix for all module tables, it is better to compose it from vendor prefix + module prefix, in out case it is me (vendor prefix) and blgg(Bloggie module prefix)
    'class_prefix' => 'MlPMR', // class prefix for all module classes, it is better to compose it from vendor prefix + module prefix, in out case it is Me (vendor prefix) and Blgg(Bloggie module prefix)

	/**
	 * Installation instructions, for complete list refer to BxDolInstaller Dolphin class
	 */
    	'install' => array(
            'check_dependencies' => 0,
    		'show_introduction' => 0,
    		'change_permissions' => 0,
    		'execute_sql' => 1,
    		'update_languages' => 1,
    		'recompile_page_builder' => 1,
    		'recompile_permalinks' => 1,
    		'clear_db_cache' => 1,
    	),
    	'uninstall' => array (
           'check_dependencies' => 0,
    		'show_introduction' => 0,
    		'change_permissions' => 0,
    		'execute_sql' => 1,
    		'update_languages' => 1,
    		'recompile_page_builder' => 1,
    		'recompile_permalinks' => 1,
    		'clear_db_cache' => 1,
    	),

	/**
	 * Category for language keys, all language keys will be places to this category, but it is still good practive to name each language key with module prefix, to avoid conflicts with other mods.
	 */
	'language_category' => 'ModLoaded Passive Reminder',

	/**
	 * Permissions Section, list all permissions here which need to be changed before install and after uninstall, see examples in other BoonEx modules
	 */
	'install_permissions' => array(),
    'uninstall_permissions' => array(),

	/**
	 * Introduction and Conclusion Section, reclare files with info here, see examples in other BoonEx modules
	 */
	'install_info' => array(
		'introduction' => '',
		'conclusion' => ''
	),
	'uninstall_info' => array(
		'introduction' => '',
		'conclusion' => ''
	)
);

?>
