<?php
 require_once('../../../inc/header.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
 include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
  
 //Dichiarazione variabili
 $pagina='##spydata##'.$_POST['pagina'];
 $contanews=(int)$_POST['contanews'];
 $ultimoid=(int)$_POST['ultimoid']; 
 $accountid=(int)$_POST['idrichiesto'];
 $profileid=(int)$_POST['idrichiesto'];
 $paginamia=1; 
 //riutilizzo masterquery impostando i valori limite
 $parami=0;
 $cont=$limite;
 $soloup="true";
 include 'templatesw.php';
 include 'masterquery.php';
 $soloup="false";
 $result=mysql_query($query);
 $sviluppo=mysql_num_rows($result);
 if($sviluppo!=0) 
 {
  $GLOBAL['ultimoid']=$ultimoid;
  $off_parent = 1;
  include 'basecore.php';
  echo '<script> $(document).ready(function() {aggiornabottonealtrenews('.$contanews.','.$limite.',\''.$pagina.'\',\''.$accountid.'\','.$ultimoid.');});';
 }
?>