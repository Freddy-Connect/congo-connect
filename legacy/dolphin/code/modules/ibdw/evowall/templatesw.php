<?php
if ($_GET['skin']<>"" OR isset($_COOKIE['skin']))
{
 if ($_GET['skin']<>"") $skinname=$_GET['skin'];
 elseif(isset($_COOKIE['skin'])) $skinname=$_COOKIE['skin'];
 if(file_exists(BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/templates/'.$skinname.'/css/evowallstyleUNI.css'))
 {
  $mytemplatepath=BX_DOL_URL_MODULES.'ibdw/evowall/templates/'.$_GET['skin'].'/css/';
  $imagepath=BX_DOL_URL_MODULES.'ibdw/evowall/templates/'.$_GET['skin'].'/images/';
 }
 else
 {
  $mytemplatepath=BX_DOL_URL_MODULES.'ibdw/evowall/templates/base/css/';
  $imagepath=BX_DOL_URL_MODULES.'ibdw/evowall/templates/base/images/';
 }
}
else
{
 $mytemplname="SELECT VALUE from sys_options WHERE Name='template'";
 $resultempl=mysql_query($mytemplname);
 $estratempl=mysql_fetch_assoc($resultempl);
 $mytemplatepath=BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/templates/'.$estratempl['VALUE'].'/css/';
 if(file_exists($mytemplatepath.'evowallstyleUNI.css')) 
 {
  $mytemplatepath=BX_DOL_URL_MODULES.'ibdw/evowall/templates/'.$estratempl['VALUE'].'/css/';
  $imagepath=BX_DOL_URL_MODULES.'ibdw/evowall/templates/'.$estratempl['VALUE'].'/images/';
 }
 else
 {
  $mytemplatepath=BX_DOL_URL_MODULES.'ibdw/evowall/templates/base/css/';
  $imagepath=BX_DOL_URL_MODULES.'ibdw/evowall/templates/base/images/';
 }
}
?>
