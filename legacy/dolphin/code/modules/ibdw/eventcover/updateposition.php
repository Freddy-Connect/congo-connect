<?php
/**********************************************************************************
*                            IBDW Event Cover for Dolphin Smart Community Builder
*                              -------------------
*     begin                : Jan 20 2014
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW Event Cover is not free and you cannot redistribute and/or modify it.
* 
* IBDW Event Cover is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/
require_once( '../../../inc/header.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/eventcover/config.php';
$owner=(int)$_POST['owner'];
$hashis=$_POST['currenthash'];
$positionx=(int)$_POST['PositionX'];
$positiony=(int)$_POST['PositionY'];
$boxwidth=(int)$_POST['boxwidth'];
$EventID=(int)$_POST['EventID'];
$UriSelect="SELECT EntryUri From ".$eventstableis." where ID=".$EventID;
$getUri=mysql_query($UriSelect);
$UriRetrieve=mysql_fetch_assoc($getUri);
$Uri= $UriRetrieve['EntryUri'];
$update = "UPDATE ibdw_event_cover SET PositionX=0, PositionY=".$positiony.",width=".$boxwidth." WHERE Owner=".$owner." AND Hash='".$hashis."' AND Uri='".$Uri."'";
$esegui = mysql_query($update);


//update position into the spy record
$recordis="SELECT id, params FROM bx_spy_data WHERE (lang_key='_ibdw_eventcover_update' OR lang_key='_ibdw_eventcover_update_male' OR lang_key='_ibdw_eventcover_update_female') AND sender_id=".$owner." AND params LIKE '%".$hashis."%' ORDER BY id DESC LIMIT 1";
$runrecord=mysql_query($recordis);
$getif=mysql_num_rows($runrecord);
if ($getif>0)
{
 $resultis=mysql_fetch_assoc($runrecord);
 $recordid=$resultis['id'];
 $parameters=unserialize($resultis['params']);
 $parameters['position'] = $positiony;
 $parameters['width'] = $boxwidth;
 $newdata=serialize($parameters); 
 $update="UPDATE bx_spy_data SET params='".$newdata."' WHERE id=".$recordid;
 mysql_query($update); 
}
?>

