
	DROP TABLE IF EXISTS `[db_prefix]pages`;

	DELETE FROM  
	    `sys_injections` 
	WHERE
	    `name` = 'tscala_splash_builder';

    -- 
    -- `sys_menu_admin`;
    --

    DELETE FROM  
        `sys_menu_admin` 
    WHERE 
        `title` = '_tscala_splash_builder';

    --
    -- permalink
    --

    DELETE FROM 
        `sys_permalinks` 
    WHERE
        `standard`  = 'modules/?r=splash_builder/';

    --
    -- settings
    --

    DELETE FROM 
        `sys_options` 
    WHERE
        `Name` = 'tscala_splash_builder_permalinks'
            AND
        `kateg` = 26;