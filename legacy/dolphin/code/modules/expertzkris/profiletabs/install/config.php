<?php
/***************************************************************************
*                           Expertzkris Admin Protection Plugin
*                              -------------------
*     begin                : Mon Mar 26 2012
*     copyright            : (C) 2012 Dexpertz Website Solutions
*     website              : http://www.Dexpertz.net
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* Application/Profile Tabs is not free and you cannot redistribute and/or modify it.
* 
* Application/Profile Tabs is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@Dexpertz.net. 
* 
* For more details please write to info@Dexpertz.net
**********************************************************************************/



$aConfig = array(

    /**
     * Main Section.
     */

    'vendor' => 'Expertzkris',

    'title' => 'Profile Tabs',  

    'version' => '2.0.0',

    'update_url' => '',

    

	'compatible_with' => array(

        '7.x.x'

    ),



    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */

    'home_dir' => 'expertzkris/profiletabs/',

    'home_uri' => 'ptabs',

    

    'db_prefix' => 'rw_ptabs',

    'class_prefix' => 'RwProfileTab',

    /**
     * Installation/Uninstallation Section.
     */

    'install' => array(
 
        'show_introduction' => 0,

        'execute_sql' => 1,

        'update_languages' => 1,

        'clear_db_cache' => 1,

        'recompile_injections' => 1,

        'show_conclusion' => 1,

    ),

    'uninstall' => array (

        'show_introduction' => 0,

        'execute_sql' => 1,

        'update_languages' => 1,

        'clear_db_cache' => 1,

        'recompile_injections' => 1,

        'show_conclusion' => 1,

    ),

	/**
	 * Dependencies Section
	 */

	'dependencies' => array(),

    /**
     * Category for language keys.
     */

    'language_category' => 'ProfileTabs',

    /**
     * Permissions Section
     */

	'install_permissions' => array(),

    'uninstall_permissions' => array(),

    /**
     * Introduction and Conclusion Section.
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