<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
mysql_query("SET NAMES 'utf8'");
$userid = (int)$_COOKIE['memberID'];
$id_user=$_POST['id_user'];
$query="UPDATE suggerimenti SET rifiutato=1 WHERE mioID=$userid AND friendID=$id_user";
$resultquery = mysql_query($query);
?>