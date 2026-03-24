<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
mysql_query("SET NAMES 'utf8'");
$userid = (int)$_COOKIE['memberID'];
$richiesta=$_POST['richiestaamiciaz'];
$userid=$_POST['ottieniid'];
$scelta=$_POST['scelta'];
$array["sender_p_link"]=getUsername($richiesta);
$array["recipient_p_link"]=getUsername($userid);
$array["sender_p_nick"]=getNickname($richiesta);
$array["recipient_p_nick"]=getNickname($userid);
$aParams=serialize($array);
if ($scelta=="ok") 
{ 
 $querydecidi="UPDATE sys_friend_list SET sys_friend_list.Check=1 WHERE ID =".$richiesta." AND Profile=".$userid;
 $resultdecidi=mysql_query($querydecidi);
 $inseriscispy="INSERT INTO `bx_spy_data` (sender_id,recipient_id,lang_key,params,type) VALUES ('$userid','$richiesta','_bx_spy_profile_friend_accept','$aParams','profiles_activity')";
 $esecuzione=mysql_query($inseriscispy);
}
elseif ($scelta=="no") 
{ 
 $querydecidi="DELETE FROM `sys_friend_list` WHERE Profile=".$userid." AND ID=".$richiesta;
 $resultdecidi=mysql_query($querydecidi);
}
?>