<?php
 if($tmpx>0)
 {
  $provavariabile="YES";
  $cont=$tmpx;
  $parami=$contanews;
  include ('masterquery.php');
  $result=mysql_query($query);
  $contazioni=$contazioni+$tmpx;    
  include 'templatesw.php';
  include ('basecore.php');
  include ('checking.php');
 }
?>