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
$owner=$_POST['owner'];
$hashis=$_POST['currenthash'];
$id=$_POST['id'];
$eventstableis=$_POST['eventstableis'];
$UriSelect="SELECT EntryUri From ".$eventstableis." where ID=".$id;
$getUri=mysql_query($UriSelect);
$UriRetrieve=mysql_fetch_assoc($getUri);
$Uri= $UriRetrieve['EntryUri'];


$update = "DELETE FROM ibdw_event_cover WHERE Owner=".$owner." AND Hash='".$hashis."' AND Uri='".$Uri."'";
$esegui = mysql_query($update);
?>

