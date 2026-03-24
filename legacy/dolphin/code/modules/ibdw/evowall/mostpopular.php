<?php 
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');  
$set=(int)$_POST['setin'];
if($set==1) $typeoforder="Popular";
else $typeoforder="";
$accountid=(int)$_COOKIE['memberID'];
$pagina='##spydata##'.$_POST['pagina'];
if ($_POST['pagina']=="/") $pagina=$pagina."index.php";
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
include 'templatesw.php';
if (strpos($pagina, 'member.php') or strpos($pagina, 'index.php')) $miapag="account";
else $miapag="profile";

//variabili limite per query
$cont=$limite+1;
$parami=0;
$paramf=$limite;
include 'masterquery.php';
$result=mysql_query($query);
$contazioni=mysql_num_rows($result);
$titolocommenti=_t('_ibdw_evowall_comment_title');
$titolocommenti_2=_t('_ibdw_evowall_comment_title_first');
$idn=0;
include 'basecore.php';
include 'checking.php';
if ($contazioni>$limite) 
{
 $inizio=$contazioni;
 echo '<div id="altrenews"></div>';
 $paginaajax=str_replace("?","",$pagina);
 $paginaajax=str_replace("/","",$paginaajax);
 echo '<div id="altro">';
 include 'bt_more_news.php';
 echo '</div>';
}
?>