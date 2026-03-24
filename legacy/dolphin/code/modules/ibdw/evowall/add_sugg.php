<?php
 require_once('../../../inc/header.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
 mysql_query("SET NAMES 'utf8'");
 include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
 
 $accountid=(int)$_COOKIE['memberID'];
 $id_user=(int)$_POST['id_user'];
 $query="INSERT INTO sys_friend_list (ID,Profile,sys_friend_list.Check) VALUES (".$accountid.",".$id_user.",0)";
 $resultquery=mysql_query($query);
 if($id_user!=$accountid) 
 {
  $aInfomember2=getProfileInfo($id_user);
  $miosendername=getNickname($accountid);
  $miorecipientname=getNickname($id_user);
  //invio email
  if(isset($_SERVER['HTTPS'])) 
  {
    if ($_SERVER['HTTPS'] == "on") {
        $protocol = 'https';
    }
    else $protocol = 'http';
  }
  if ($protocol =='') $protocol = 'http';
  $pageaddress=$protocol."://".$_SERVER['HTTP_HOST']."/communicator.php?communicator_mode=friends_requests";
  bx_import('BxDolEmailTemplates');
  $oEmailTemplate = new BxDolEmailTemplates();
  $aTemplate = $oEmailTemplate -> getTemplate('t_FriendRequest');
  $usermailadd=trim($aInfomember2['Email']);
  $senderlinkis=$protocol."://".$_SERVER['HTTP_HOST']."/".getUsername($accountid);
  $sitenameis=getParam('site_title');
  $aTemplate['Body']=str_replace('<Sender>',$miosendername,$aTemplate['Body']);
  $aTemplate['Body']=str_replace('<Recipient>',$miorecipientname,$aTemplate['Body']);
  $aTemplate['Body']=str_replace('<RequestLink>',$pageaddress,$aTemplate['Body']);
  $aTemplate['Body']=str_replace('<SenderLink>',$senderlinkis,$aTemplate['Body']);
  $aTemplate['Body']=str_replace('<SiteName>',$sitenameis,$aTemplate['Body']);
  sendMail($usermailadd, $aTemplate['Subject'], $aTemplate['Body'], $id_user, 'html');
 //fine invio email
 }
?>