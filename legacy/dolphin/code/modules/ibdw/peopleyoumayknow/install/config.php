<?php
/**********************************************************************************
*                            IBDW PeopleYouMayKnow Dolphin Smart Community Builder
*                              -------------------
*     begin                : June 5 2011
*     copyright            : (C) 2010 IlBelloDelWEB di Ferraro Raffaele
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW peopleyoumayknow is not free and you cannot redistribute and/or modify it.
* 
* IBDW peopleyoumayknow is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

$aConfig = array(
	'title' => 'Peoples You May Know',
	'version' => '1.0.2',
	'vendor' => 'IlBelloDelWeb.it',
	'update_url' => '',
	
	'compatible_with' => array( // module compatibility
        '7.1.x','7.2.x','7.3.x'
    ),
	'home_dir' => 'ibdw/peopleyoumayknow/',
	'home_uri' => 'peopleyoumayknow',
	'db_prefix' => 'peopleyoumayknow',
    'class_prefix' => 'peopleyoumayknow',

	/**
	 * Installation instructions, for complete list refer to BxDolInstaller Dolphin class
	 */
	'install' => array( 
	  'check_dependencies' => 1,
		'execute_sql' => 1,
    'recompile_permalinks' => 1, 	
		'recompile_global_paramaters' => 1,
		'clear_db_cache' => 1,
		'update_languages' => 1,
	),
	/**
	 * Uninstallation instructions, for complete list refer to BxDolInstaller Dolphin class
	 */    
	'uninstall' => array (
	  'execute_sql' => 1,
		'update_languages' => 1,
    'recompile_permalinks' => 1,
		'recompile_global_paramaters' => 1,
		'clear_db_cache' => 1,        
    ),
    
   /**
	 * Dependencies Section
	 */
    'dependencies' => array(),  

	/**
	 * Category for language keys, all language keys will be places to this category, but it is still good practive to name each language key with module prefix, to avoid conflicts with other mods.
	 */
	'language_category' => 'peopleyoumayknow',

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