<?php

/**
* Copyright (c) 2012-2016 Andreas Pachler - http://www.paan-systems.com
* This is a commercial product made by Andreas Pachler and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Andreas Pachler.
* This notice may not be removed from the source code.
*/

$aConfig = array(

    /**
     * Main Section
     */
    'title' => 'Google Analytics',
    'version' => '1.2.0',
    'vendor' => 'paan : solution systems',
    'update_url' => 'http://www.boonex.com/market/update_ckeck?product=MPGoogleAnalytics',
    'compatible_with' => array(
        '7.1.x', '7.2.x', '7.3.x'
    ),

    /**
     * Basic Section
     */
    'home_dir' => 'misterpopper/googleanalytics/',
    'home_uri' => 'googleanalytics',
    'db_prefix' => 'mp_googleanalytics',
    'class_prefix' => 'MPGoogleAnalytics',

    /**
     * Installation / Uninstall instructions
     */
    'install' => array(
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
        'recompile_categories' => 0,
        'recompile_search' => 0,
        'recompile_injections' => 1,
        'recompile_permalinks' => 1,
        'recompile_alerts' => 0,
        'clear_db_cache' => 1,
        'show_conclusion' => 1,
    ),
    'uninstall' => array (
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
        'recompile_categories' => 0,
        'recompile_search' => 0,
        'recompile_injections' => 1,
        'recompile_permalinks' => 1,
        'recompile_alerts' => 0,
        'clear_db_cache' => 1,
        'show_conclusion' => 1,
    ),

    /**
     * Dependencies Section
     */
    'dependencies' => array(),

    /**
     * Category for language keys
     */
    'language_category' => 'MPGoogleAnalytics',

    /**
     * Permissions Section
     */
    'install_permissions' => array(),
    'uninstall_permissions' => array(),

    /**
     * Introduction and Conclusion Section
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
