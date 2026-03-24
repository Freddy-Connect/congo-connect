<?php
/**********************************************************************************
*                            IBDW School Cover for Dolphin Smart Community Builder
*                              -------------------
*     begin                : October 11 2015
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW School Cover is not free and you cannot redistribute and/or modify it.
* 
* IBDW School Cover is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

$retriveidschoolcover="SELECT ID FROM sys_options_cats WHERE name='School Cover'";
$risultatoid = mysql_query($retriveidschoolcover);
$idmodulo=mysql_fetch_assoc($risultatoid);
$querydiconfigurazione="SELECT Name, VALUE FROM sys_options WHERE kateg=".$idmodulo['ID'];
$risultato = mysql_query($querydiconfigurazione);
while ($feccia=mysql_fetch_array($risultato))
{
 $name = $feccia['Name'];
 $riga[$name]=$feccia['VALUE'];
}
$KeyCodeSCH=$riga['KeyCodeSCH'];
$coveralbumname=str_replace(" ","-",preg_replace('/\s{2,}/',' ',$riga['AlbumCoverNameSCH']));
$Displaytitle=$riga['DisplaytitleSCH'];
$Displayauthor=$riga['DisplayauthorSCH'];
$maxfilesize = $riga['maxfilesizeSCH'];
$xyfactorSCH = $riga['xyfactorSCH']; 
?>

