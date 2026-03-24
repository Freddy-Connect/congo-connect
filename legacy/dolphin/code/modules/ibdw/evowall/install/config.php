<?php
/**********************************************************************************
*                            IBDW EvoWall for Dolphin Smart Community Builder
*                              -------------------
*     begin                : July 23 2012
*     copyright            : (C) 2012 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW EvoWall is not free and you cannot redistribute and/or modify it.
* 
* IBDW EvoWall is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

$aConfig = array(
	'title' => 'EVO Wall - News Feed',
	'version' => '2.1.3',
	'vendor' => 'IlBelloDelWeb.it',
	'update_url' => '',	
	'compatible_with' => array( // module compatibility
        '7.3.x','7.2.x'
    ),
	'home_dir' => 'ibdw/evowall/',
	'home_uri' => 'evowall',
	'db_prefix' => 'evowall',
  'class_prefix' => 'evowall',

	/**
	 * Installation instructions, for complete list refer to BxDolInstaller Dolphin class
	 */
	 'install' => array(
    'show_introduction' => 1,
	  'check_dependencies' => 1,
		'execute_sql' => 1,
    'recompile_permalinks' => 1, 	
		'recompile_global_paramaters' => 1,
		'clear_db_cache' => 1,
		'update_languages' => 1,
    'show_conclusion' => 1
	),
	/**
	 * Uninstallation instructions, for complete list refer to BxDolInstaller Dolphin class
	 */    
	 'uninstall' => array (
    'show_introduction' => 1,
	  'execute_sql' => 1,
		'update_languages' => 1,
  	'recompile_permalinks' => 1,
		'recompile_global_paramaters' => 1,
		'clear_db_cache' => 1,
    'show_conclusion' => 1        
    ),
    
   /**
	 * Dependencies Section
	 */
   'dependencies' => array(
    'spy' => 'BoonEx Spy Module',
	),  

	/**
	 * Category for language keys, all language keys will be places to this category, but it is still good practive to name each language key with module prefix, to avoid conflicts with other mods.
	 */
	'language_category' => 'evowall',

	/**
	 * Permissions Section, list all permissions here which need to be changed before install and after uninstall, see examples in other BoonEx modules
	 */
	'install_permissions' => array(),
  'uninstall_permissions' => array(),

	/**
	 * Introduction and Conclusion Section, reclare files with info here, see examples in other BoonEx modules
	 */
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