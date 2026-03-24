
	INSERT INTO 
	    `sys_injections` 
	SET
	    `name`       = 'tscala_splash_builder', 
	    `page_index` = '0', 
	    `key`        = 'injection_between_logo_top_menu', 
	    `type`       = 'php',
	    `data`       = 'return BxDolService::call(''splash_builder'', ''new_splash'');', 
	    `replace`    = 0, 
	    `active`     = 1;

    -- 
    -- `sys_menu_admin`;
    --

    INSERT INTO 
        `sys_menu_admin` 
    SET
        `name`           = 'Splash Builder',
        `title`          = '_tscala_splash_builder', 
        `url`            = '{siteUrl}modules/?r=splash_builder/administration/', 
        `description`    = 'Manage your Dolphin splash page', 
        `icon`           = 'modules/tscala/splash_builder/|splashbuilder.png',
        `parent_id`      = 2;

    --
    -- Table structure for table `[db_prefix]pages`
    --

    CREATE TABLE `[db_prefix]pages` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `uri` varchar(255) NOT NULL,
          `lang_key` varchar(150) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `uri` (`uri`)
    ) ENGINE=MyISAM;

    --
    -- permalink
    --

    INSERT INTO 
        `sys_permalinks` 
    SET
        `standard`  = 'modules/?r=splash_builder/', 
        `permalink` = 'm/splash_builder/', 
        `check`     = 'tscala_splash_builder_permalinks';

    --
    -- settings
    --

    INSERT INTO 
        `sys_options` 
    (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) 
        VALUES
    ('tscala_splash_builder_permalinks', 'on', 26, 'Enable friendly permalinks in splash_builder', 'checkbox', '', '', '0', '');
