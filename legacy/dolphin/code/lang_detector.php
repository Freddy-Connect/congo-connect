<?php
set_time_limit(9999999);
/***************************************************************************

*                            Dolphin Smart Community Builder

*                              -----------------

*     begin                : Mon Mar 23 2006

*     copyright            : (C) 2006 BoonEx Group

*     website              : http://www.boonex.com/

* This file is part of Dolphin - Smart Community Builder

*

* Dolphin is free software. This work is licensed under a Creative Commons Attribution 3.0 License. 

* http://creativecommons.org/licenses/by/3.0/

*

* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;

* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

* See the Creative Commons Attribution 3.0 License for more details. 

* You should have received a copy of the Creative Commons Attribution 3.0 License along with Dolphin, 

* see license.txt file; if not, write to marketing@boonex.com

***************************************************************************/
 
require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'languages.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
 


//echo "START .....<br /><br />";

echo "<b>The following languages exists on your Site :</b>";
echo "<br /><br />";
 
$res = db_res("SELECT * FROM `sys_localization_languages` ORDER BY `ID`");
while($arr= mysql_fetch_array($res) ) 
{
	$sName = $arr['Name'];
	$sTitle = $arr['Title'];
 	
	if($sName=="en"){
		echo "{$sTitle} - <b>{$sName}.php</b> exists already. Copy this file to create the others";
		echo "<br /><br />";
	}else{
		echo "{$sTitle} - Create a file called <b>{$sName}.php</b>";
		echo "<br /><br />";
	} 
}