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

$retriveideventcover="SELECT ID FROM sys_options_cats WHERE name='Event Cover'";
$risultatoid = mysql_query($retriveideventcover);
$idmodulo=mysql_fetch_assoc($risultatoid);
$querydiconfigurazione="SELECT Name, VALUE FROM sys_options WHERE kateg=".$idmodulo['ID'];
$risultato = mysql_query($querydiconfigurazione);
while ($feccia=mysql_fetch_array($risultato))
{
 $name = $feccia['Name'];
 $riga[$name]=$feccia['VALUE'];
}
$KeyCodeEV=$riga['KeyCodeEV'];
$coveralbumname=str_replace(" ","-",preg_replace('/\s{2,}/',' ',$riga['AlbumCoverNameEV']));
$Displaytitle=$riga['DisplaytitleEV'];
$Displayauthor=$riga['DisplayauthorEV'];
$maxfilesize = $riga['maxfilesizeEV'];
$eventmoduleis = $riga['eventmoduleis'];
//set the events table
 if ($eventmoduleis=="Boonex" or $eventmoduleis=="Modzzz") 
 {
  $eventstableis="bx_events_main";
  $eventpath="m/events/";
 }
 elseif ($eventmoduleis=="UE30") 
 {
  $eventstableis="ue30_event_main";
  $eventpath="m/event/";
 }
 $xyfactorG=$riga['xyfactorEV']; 
?>

