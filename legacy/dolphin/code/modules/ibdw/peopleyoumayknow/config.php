<?php
$retriveidpeopleyoumayknow="SELECT ID FROM sys_options_cats WHERE name='peopleyoumayknow'";
$risultatoid = mysql_query($retriveidpeopleyoumayknow);
$idmodulo=mysql_fetch_assoc($risultatoid);
$querydiconfigurazione="SELECT Name, VALUE FROM sys_options WHERE kateg=".$idmodulo['ID'];
$risultato = mysql_query($querydiconfigurazione);
while ($feccia=mysql_fetch_array($risultato))
{
 $name = $feccia['Name'];
 $riga[$name]=$feccia['VALUE'];
}
$KeyCodePYMK=$riga['KeyCodePYMK'];
$pymktemplate=$riga['pymktemplate'];
if($pymktemplate=="UNI") $pymktemplate=1;
elseif($pymktemplate=="DARK") $pymktemplate=0;
$minprofiletoload=$riga['minprofiletoload'];      
$displaybefriend=$riga['displaybefriend'];
$displayfriends=$riga['displayfriends'];
$displayfmessage=$riga['displayfmessage'];
$IBDWProfileCover=$riga['IBDWProfileCover'];
?>
