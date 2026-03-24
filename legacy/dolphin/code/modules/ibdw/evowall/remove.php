<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
$accountid=(int)$_COOKIE['memberID'];
$profileid=(int)$_POST['user'];
$ultimoid=(int)$_POST['ultimoid'];
$id=(int)$_POST['id'];
$pagina=$_POST['pagina'];  
mysql_query("SET NAMES 'utf8'");
$query="SELECT * FROM bx_spy_data WHERE id=$id";
$resultquery=mysql_query($query);
$rowquery=mysql_fetch_row($resultquery);

$querydx="DELETE FROM bx_spy_data WHERE id=$id";
$resultquerydx=mysql_query($querydx);
$query2="DELETE FROM commenti_spy_data WHERE data=$id";
$resultquery2=mysql_query($query2);
if ($attivaintegrazione==1)
{
 $query3="DELETE FROM ibdw_likethis WHERE id_notizia=$id AND typelement =''";
 $resultquery3=mysql_query($query3);
}
else
{
 $query3="DELETE FROM ibdw_likethis WHERE id_notizia=$id";
 $resultquery3=mysql_query($query3);
}
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
include 'templatesw.php';
$varpage=strpos($pagina, '?');
if (!$varpage) $pagina=$pagina.'?';

//DETERMINIAMO LA PAGINA IN CUI CI TROVIAMO
if (strpos($pagina, 'index.php') or substr($pagina, -1)=='/') {$miapag="home";$paginamia=1;}
elseif (strpos($pagina, 'member.php')) {$miapag="account";$paginamia=1;}
else 
{
 $miapag="profile";
 if($accountid==$profileid) $paginamia=1;
}

$cont=$limite+1;
$parami=0;
$paramf=$limite;
include 'masterquery.php';
$result=mysql_query($query);
$contazioni=mysql_num_rows($result);
$titolocommenti=_t('_ibdw_evowall_comment_title');
$titolocommenti_2=_t('_ibdw_evowall_comment_title_first');
$idn=0;
echo '<div id="correzione">';
echo '<div id="updateajax" style="display:none;"> </div>';
if($welcome=='on' AND (int)$_COOKIE['memberID']==getID($_REQUEST['ID'])) include 'welcome.php';
include 'basecore.php';
include 'checking.php';
if ($contazioni>$limite) 
{
 $inizio=$contazioni; 
 echo '<div id="altrenews"> </div>';
 $paginaajax=str_replace("?", "", $pagina);
 $paginaajax=str_replace("/", "", $paginaajax);
 echo '<div id="altro">';
 include 'bt_more_news.php';
 echo '</div>';
}
?>