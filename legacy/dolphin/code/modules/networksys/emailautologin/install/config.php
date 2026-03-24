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
	'title' => 'Email Auto Login',
    'version' => '1.0.0',
	'vendor' => 'NetworkSys',
	'update_url' => '', 
	
	'compatible_with' => array( 
        '7.x.x' 
    ),

    /**
	 * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
	 */
	'home_dir' => 'networksys/emailautologin/', // folder where module files are located, it describes path from /modules/ folder.
	'home_uri' => 'emailautologin',
	
	'db_prefix' => 'ns_al_', 
    'class_prefix' => 'NsysEmailAutoLogin',

	'install' => array(
		'check_dependencies' => 1,
		'show_introduction' => 1,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_global_paramaters' => 1,
		'recompile_main_menu' => 0,
		'recompile_member_menu' => 0,
		'recompile_site_stats' => 0,
		'recompile_page_builder' => 0,
		'recompile_profile_fields' => 0,
		'recompile_comments' => 0,
		'recompile_member_actions' => 0,
		'recompile_tags' => 0,
		'recompile_votes' => 0,
		'recompile_categories' => 1,
		'recompile_search' => 0,
		'recompile_injections' => 1,
		'recompile_permalinks' => 1,
		'recompile_alerts' => 0,
		'clear_db_cache' => 1,
		'show_conclusion' => 1
	),
 
	'uninstall' => array (
		'check_dependencies' => 0,
		'show_introduction' => 1,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_global_paramaters' => 1,
		'recompile_main_menu' => 0,
		'recompile_member_menu' => 0,
		'recompile_site_stats' => 0,
		'recompile_page_builder' => 0,
		'recompile_profile_fields' => 0,
		'recompile_comments' => 0,
		'recompile_member_actions' => 0,
		'recompile_tags' => 0,
		'recompile_votes' => 0,
		'recompile_categories' => 1,
		'recompile_search' => 0,
		'recompile_injections' => 1,
		'recompile_permalinks' => 1,
		'recompile_alerts' => 0,
		'clear_db_cache' => 1,
		'show_conclusion' => 1
	),


	'language_category' => 'EmailAutoLogin',

	'install_permissions' => array(),
    'uninstall_permissions' => array(),

	'install_info' => array(
		'introduction' => 'inst_intro.html',
		'conclusion' => 'inst_concl.html'
	),
	'uninstall_info' => array(
		'introduction' => 'uninst_intro.html',
		'conclusion' => 'uninst_concl.html'
	)
);

?>
