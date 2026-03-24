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

$aConfig = array(
	'title' => 'Advanced Password Forgot',
    'version' => '2.2.0',
	'vendor' => 'Denre',
	'update_url' => 'http://www.boonex.com/market/update_ckeck?product=advanced-password-forgot',
	'compatible_with' => array('7.x.x'),
	'home_dir' => 'denre/forgot/',
	'home_uri' => 'forgot',
	'db_prefix' => 'db_afo_',
    'class_prefix' => 'DbForgot',
	'install' => array(
        'check_dependencies' => 0,
        'execute_sql' => 1,
        'recompile_page_builder' => 0,
        'update_languages' => 1,
        'recompile_main_menu' => 0,
        'recompile_alerts' => 1,
        'clear_db_cache' => 1,
        'show_conclusion' => 0,
	),
	'uninstall' => array (
        'check_dependencies' => 0,
        'execute_sql' => 1,
        'recompile_page_builder' => 0,
        'update_languages' => 1,
        'recompile_main_menu' => 0,
        'recompile_alerts' => 1,
        'clear_db_cache' => 1,
        'show_conclusion' => 0,
    ),
	'language_category' => 'afo',
    'dependencies' => array(),
	'install_permissions' => array(),
    'uninstall_permissions' => array(),
	'install_info' => array(
		'introduction' => '',
		'conclusion' => '',
	),
	'uninstall_info' => array(
		'introduction' => '',
		'conclusion' => '',
	),
);

?>