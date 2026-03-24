<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/3col/config.php';
mysql_query("SET NAMES 'utf8'");
$userid = getID($_REQUEST['ID']);
$id_user=$_POST['id_user'];


if($userid!=$id_user) 
{
 //inserisco il suggerimento
 $query="INSERT IGNORE INTO sys_friend_list (ID,Profile,sys_friend_list.Check) VALUES (".$userid.",".$id_user.",0)";
 $resultquery = mysql_query($query);

 //invio email
 $senderemail=$userid;
 $recipientemail=$id_user;
 $protocol=strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https')=== FALSE ? 'http' : 'https';
 $pageaddress=$protocol."://".$_SERVER['HTTP_HOST']."/communicator.php?communicator_mode=friends_requests";
 bx_import('BxDolEmailTemplates');
 $oEmailTemplate = new BxDolEmailTemplates();
 $aTemplate = $oEmailTemplate -> getTemplate('t_FriendRequest');
 $infoamico=getProfileInfo($recipientemail);
 $authorname=getNickname($senderemail);
 $usermailadd=trim($infoamico['Email']);
 $aInfomembers=getProfileInfo($senderemail);
 $senderlinkis=$protocol."://".$_SERVER['HTTP_HOST']."/".$aInfomembers['NickName'];
 $sitenameis=getParam('site_title');
 $aTemplate['Body']=str_replace('<Sender>',$authorname,$aTemplate['Body']);
 $aTemplate['Body']=str_replace('<Recipient>',$execactionname,$aTemplate['Body']);
 $aTemplate['Body']=str_replace('<RequestLink>',$pageaddress,$aTemplate['Body']);
 $aTemplate['Body']=str_replace('<SenderLink>',$senderlinkis,$aTemplate['Body']);
 $aTemplate['Body']=str_replace('<SiteName>',$sitenameis,$aTemplate['Body']);
 if ($infoamico['EmailNotify']==1) sendMail($usermailadd, $aTemplate['Subject'], $aTemplate['Body'], $recipientemail, 'html');
 //fine invio email
}

$queryx="UPDATE suggerimenti SET rifiutato=1 WHERE mioID=$userid AND friendID=$id_user";
$resultqueryx = mysql_query($queryx);

//Conto le richieste fatte in questa giornata dal determinato utente
$querycontarichieste="SELECT COUNT(*) FROM logrichieste where IdUtente=".$userid;
$resultcontar = mysql_query($querycontarichieste) or die(mysql_error());
$contaconta = mysql_fetch_array($resultcontar); 
if ($contaconta[0]==0)
{    
 $queryins="INSERT INTO logrichieste (ID,IdUtente,Contaric,logrichieste.When) VALUES (NULL,".$userid.",1,CURRENT_TIMESTAMP)";
 $lanciainserisci = mysql_query($queryins) or die(mysql_error());
}
else
{
 $queryverificachei = "SELECT Contaric,logrichieste.When FROM logrichieste WHERE IdUtente=".$userid;
 $resultveri = mysql_query($queryverificachei) or die(mysql_error());
 $feccio=mysql_fetch_array($resultveri);
 $valattuale=$feccio[0]+1;
 $queryupd="UPDATE logrichieste SET Contaric=".$valattuale.",logrichieste.When=CURRENT_TIMESTAMP WHERE IdUtente=".$userid;
 $lanciaaggiorna = mysql_query($queryupd) or die(mysql_error());	
}
?>