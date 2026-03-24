<?
$aConfig = array(
	'title'   => 'AdiInviter Pro',
	'version' => '2.0.0',
	'vendor'  => 'AdiInviter Pro', 
	'update_url' => '', 
	'compatible_with' => array( '7.x.x' ),

	'home_dir' => 'adiinviter/find_friends/',
	'home_uri' => 'find_friends',
	'class_prefix' => 'Adi_Dolphin_',

	'install' => array(
		'check_requirements' => 1,
		'show_introduction' => 1,
		'change_permissions' => 1,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_main_menu' => 1,
		'recompile_member_menu' => 0,
		'recompile_site_stats' => 1,
		'recompile_page_builder' => 1,
		'recompile_profile_fields' => 0,
		'recompile_comments' => 0,
		'recompile_member_actions' => 0,
		'recompile_tags' => 0,
		'recompile_votes' => 0,
		'recompile_categories' => 0,
		'recompile_search' => 0,
		'recompile_injections' => 0,
		'recompile_permalinks' => 0,
		'recompile_alerts' => 0,
		'clear_db_cache' => 1,
		'show_conclusion' => 1
	),

	'uninstall' => array(
		'show_introduction' => 1,
		'change_permissions' => 0,
		'execute_sql' => 1,
		'update_languages' => 1,
		'recompile_main_menu' => 1,
		'recompile_member_menu' => 0,
		'recompile_site_stats' => 1,
		'recompile_page_builder' => 1,
		'recompile_profile_fields' => 0,
		'recompile_comments' => 0,
		'recompile_member_actions' => 0,
		'recompile_tags' => 0,
		'recompile_votes' => 0,
		'recompile_categories' => 0,
		'recompile_search' => 0,
		'recompile_injections' => 0,
		'recompile_permalinks' => 0,
		'recompile_alerts' => 0,
		'clear_db_cache' => 1,
		'show_conclusion' => 1
	),

	'language_category' => 'AdiInviter Pro',
	
	'install_permissions'   => array(),
	'uninstall_permissions' => array(),

	'install_info' => array(
		'introduction' => 'inst_intro.html',
		'conclusion'   => 'inst_concl.html'
	),
	'uninstall_info'  => array(
		'introduction' => 'uninst_intro.html',
		'conclusion'   => 'uninst_concl.html'
	)

);

?>