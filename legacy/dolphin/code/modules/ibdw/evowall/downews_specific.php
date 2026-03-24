<?php 
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php' );
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php' );
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php' );  
$id=(int)$_POST['id'];
$lang=$_POST['lang'];
$recordfound=(int)$_POST['recordfound'];
$ultimoid=(int)$_POST['ultimoid']; 
$sender=(int)$_POST['sender'];
$pagina=$_POST['pagina'];
$datepost=$_POST['datepost'];
$profileid=(int)$_COOKIE['memberID'];
$accountid=(int)$_COOKIE['memberID'];
$paginamia=1;
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
include 'templatesw.php';
$query="SELECT * FROM bx_spy_data WHERE (id<=".$id." AND lang_key='".$lang."' AND sender_id=".$sender." AND (DAY(bx_spy_data.date)=DAY('".$datepost."') AND MONTH(bx_spy_data.date)=MONTH('".$datepost."') AND YEAR(bx_spy_data.date)=YEAR('".$datepost."'))) ORDER BY id DESC LIMIT ".$recordfound;
$result=mysql_query($query);
$contazioni=mysql_num_rows($result);
$idn=0;
$hidden_intro=1;
$off_parent=1;
$provavariabile="YES";
$ultimoid=(int)$_POST['ultimoid'];
$GLOBAL['ultimoid']=(int)$_POST['ultimoid'];
include 'basecore.php';
?>