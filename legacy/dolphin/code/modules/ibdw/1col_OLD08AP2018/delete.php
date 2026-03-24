<?php
header("refresh: 1; ../../../administration");
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/1col/myconfig.php';
$userid = (int)$_COOKIE['memberID'];
if(!isAdmin()) { exit;}
mysql_query("SET NAMES 'utf8'");
$queryx  = "UPDATE sys_menu_admin SET name = '1Col Config' , title = '1Col Config', url = '{siteUrl}modules/ibdw/1col/configurazione.php', icon = 'modules/ibdw/1col/templates/base/images/|gearspy.png' WHERE name = 'Activation 1COL'";
$resultx = mysql_query($queryx);
?>