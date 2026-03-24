<?php 
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );  
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
$accountid=(int)$_COOKIE['memberID'];
$pagina=$_POST['pagina'];
$varpage=strpos($pagina,'?');
$paginamia=0;
if ($pagina==" / " or $pagina=="/" or substr($pagina, -1)=="/") $pagina="/index.php";
if ($varpage==FALSE) $pagina=$pagina.'?';
else $pagina=$pagina;

$profileid=(int)$_POST['id'];
mysql_query("SET NAMES 'utf8'");
include 'templatesw.php';
if (strpos($pagina, 'member.php') or strpos($pagina, 'index.php')) {$miapag="account";$paginamia=1;}
else 
{
 $miapag="profile";
 if($accountid==$profileid) $paginamia=1;
}

$contanews=(int)$_POST['inizio'];
$fine=(int)$_POST['fine'];

//variabili limite per query
$parami=$contanews;
$paramf=$fine;
$cont=$fine+1;

include 'masterquery.php';
$result=mysql_query($query);
$contazioni=mysql_num_rows($result);

$titolocommenti=_t('_ibdw_evowall_comment_title');
$titolocommenti_2=_t('_ibdw_evowall_comment_title_first');
$idn=0;
$provavariabile="YES";
$GLOBAL['ultimoid']=(int)$_POST['ultimoid'];
include 'basecore.php';
include 'checking.php';
if ($contazioni>$limite) 
{   
 echo '<div id="altro">';
 if($hidemorenews=='') echo '<a href="javascript:altrenews('.$contanews.','.$limite.',\' '.$pagina.' \','.$profileid.','.$GLOBAL['ultimoid'].');" class="primoaltre"> <i class="fa fa-desktop"></i><i class="sys-icon chevron-down" alt=""></i>'._t('_ibdw_evowall_altrenews').'</a>';
 echo '<img id="liload" src="'.$imagepath.'load.gif" style="display:none;">'; 
 echo '<input type="hidden" id="ajx_contanews" value="'.$contanews.'" /><input type="hidden" id="ajx_limite" value="'.$limite.'" /><input type="hidden" id="ajx_pagina" value="'.$pagina.'" /><input type="hidden" id="ajx_mioid" value="'.$profileid.'" /><input type="hidden" id="ajx_ultimoid" value="'.$ultimoid.'" /></div>';
}
?>
<!-- Freddy ajout  
<link href="https://netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
-->
<link href="https://netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">