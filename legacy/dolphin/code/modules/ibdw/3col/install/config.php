<?php
/**********************************************************************************
*                            IBDW 3Col Dolphin Smart Community Builder
*                              -------------------
*     begin                : May 1 2010
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW 3Col is not free and you cannot redistribute and/or modify it.
* 
* IBDW 3Col is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

$aConfig = array(
	'title' => '3Col',
    'version' => '7.0.2',
	'vendor' => 'IlBelloDelWeb.it',
	'update_url' => '',
	'compatible_with' => array( 
        '7.1.x','7.2.x','7.3.x'
    ),
	'home_dir' => 'ibdw/3col/',
	'home_uri' => '3col',
	'db_prefix' => 'me_tcol_',
    'class_prefix' => 'ThirdCol',
	'install' => array( 
        'clear_db_cache' => 1,
        'update_languages' => 1,
        'check_dependencies' => 1,
        'execute_sql' => 1,
        'recompile_permalinks' => 1,
        'recompile_global_paramaters' => 1,
        'clear_db_cache' => 1,
	),
	'uninstall' => array (
        'clear_db_cache' => 1,
        'update_languages' => 1,
        'execute_sql' => 1,
        'recompile_permalinks' => 1,
        'recompile_global_paramaters' => 1,
        'clear_cache' => 1,
        'update_languages' => 1,
    ),
	'language_category' => '3colonna',
    'install_permissions' => array(),
    'uninstall_permissions' => array(),
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