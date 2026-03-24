<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/3col/config.php';
$userid = (int)$_COOKIE['memberID'];
$reset = $_POST['resettrue'];
if(isAdmin() and $reset==1) 
{
 $query  = "TRUNCATE `suggerimenti`";
 $result = mysql_query($query);
 echo "Suggestions deleted!";
} 
?>