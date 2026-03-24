<?php
require_once( '../../../inc/header.inc.php' );
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
mysql_query("SET NAMES 'utf8'");

function readyshare($str)
{
 $str=str_replace('doppiequot','"',$str);         
 $str=str_replace('ecommercial','&',$str);    
 $str=str_replace('xeamp','&amp;',$str);
 return $str;
}
$bt_share_params['1']=(int)$_POST['1']; //Sender 
$bt_share_params['2']=(int)$_POST['2']; //Recipient
$bt_share_params['3']=$_POST['3']; //Lang
$bt_share_params['4']=readyshare($_POST['4']); //Params
$query="INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params) VALUES ('".$bt_share_params['1']."','".$bt_share_params['2']."','".$bt_share_params['3']."','".$bt_share_params['4']."')";
$result=mysql_query($query);

//ottengo l'id dell'azione
$unserializeaction=unserialize(readyshare($_POST['4']));
$idaction=$unserializeaction['id_action'];
$pageaddress=$unserializeaction['url_page']."?id_mode=".$idaction;
if($AllowShareNotification=="on" and $bt_share_params['2']!=0 and $bt_share_params['2']<>$bt_share_params['1']) 
{
 //invio email
 bx_import('BxDolEmailTemplates');
 $oEmailTemplate = new BxDolEmailTemplates();
 $aTemplate = $oEmailTemplate -> getTemplate($bt_share_params['3']);
 $infoamico=getProfileInfo($bt_share_params['2']);
 $usermailadd=trim($infoamico['Email']);
 $sitenameis=getParam('site_title');
 $execactionname=getNickname($bt_share_params['2']);
 $authorname=getNickname($bt_share_params['1']);
 $aTemplate['Body']=str_replace('<SiteName>',$sitenameis,$aTemplate['Body']);
 $aTemplate['Body']=str_replace('<SenderNickName>',$authorname,$aTemplate['Body']);
 $aTemplate['Body']=str_replace('<RecipientNickName>',$execactionname,$aTemplate['Body']);
 $aTemplate['Body']=str_replace('{post}',$pageaddress,$aTemplate['Body']);
 $aTemplate['Subject']=str_replace('<SenderNickName>',$authorname,$aTemplate['Subject']);
 if ($infoamico['EmailNotify']==1) sendMail($usermailadd, $aTemplate['Subject'], $aTemplate['Body'],$bt_share_params['2'], 'html');
 //fine invio email
}
?>