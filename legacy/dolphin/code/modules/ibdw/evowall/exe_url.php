<?php
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php' );
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
function sostcomm($string) 
{ 
 $elabora=str_replace("execommerciale","&amp;",$string);
 return htmlentities($elabora);
}
$utente=(int)$_COOKIE['memberID'];
$nomesito=sostcomm($_POST['nomesito']);
$descrizione=html_entity_decode(sostcomm($_POST['descrizione']));
$descrizione=str_replace("\\","ibdwbackslash",$descrizione);
$indirizzo=$_POST['indirizzo'];
$immagine=$_POST['immagine'];
$messaggio=$_POST['message'];
$anteprimano=$_POST['anteprimano'];
mysql_query("SET NAMES 'utf8'");
$idprofile=(int)$_POST['idprofile'];

//nickname/link del sender
$sendernick=getNickname($utente);
$senderlink=getUsername($utente);
$array["sender_p_link"]=BX_DOL_URL_ROOT.$senderlink;
$array["sender_p_nick"]=$sendernick;
$array["titolosito"]=$nomesito;
$array["indirizzo"]=$indirizzo;
$array["descrizione"]=$descrizione;
$array["immagine"]=$immagine;
$array["messaggio"]=$messaggio;
$array["anteprimano"]=$anteprimano;
$str=serialize($array);

if ($utente==$idprofile) $query="INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params,type) VALUES (".$utente.",0,'_ibdw_evowall_bx_url_add','$str','profiles_activity')";
else $query="INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params,type) VALUES (".$utente.",".$idprofile.",'_ibdw_evowall_bx_url_add','$str','profiles_activity')";
$result=mysql_query($query);
$ultimoid=mysql_insert_id();

//DETERMINIAMO LA PAGINA IN CUI CI TROVIAMO
 if (strpos($pagina,'index.php') or substr($pagina, -1)=='/') $miapag="home";
 elseif (strpos($pagina,'member.php')) $miapag="account";
 else $miapag="profile";
 
 if(isset($_SERVER['HTTPS'])) 
 {
  if ($_SERVER['HTTPS'] == "on") {
    $protocol = 'https';
  }
  else $protocol = 'http';
 }
 if ($protocol =='') $protocol = 'http';
 $pageaddress=$protocol."://".$_SERVER['HTTP_HOST'].$pagina."?id_mode=".$ultimoid;
 $domainis=$GLOBALS['site']['url'];


if($AllowMessageNotification=="on" and $utente!=$idprofile) 
 {
 //invio email
 bx_import('BxDolEmailTemplates');
 $oEmailTemplate = new BxDolEmailTemplates();
 $aTemplate = $oEmailTemplate -> getTemplate(_ibdw_evowall_bx_evowall_message);
 $infoamico=getProfileInfo($idprofile);
 $usermailadd=trim($infoamico['Email']);
 $sitenameis=getParam('site_title');
 $execactionname=getNickname($idprofile);
 $authorname=getNickname($utente);
  
 $aTemplate['Body']=str_replace('<SiteName>',$sitenameis,$aTemplate['Body']);
 $aTemplate['Body']=str_replace('<SenderNickName>',$authorname,$aTemplate['Body']);
 $aTemplate['Body']=str_replace('<RecipientNickName>',$execactionname,$aTemplate['Body']);
 $aTemplate['Body']=str_replace('<PersonalMessage>', htmlentities($messaggio,ENT_COMPAT, 'UTF-8'),$aTemplate['Body']);
 $aTemplate['Body']=str_replace('{post}',$pageaddress,$aTemplate['Body']);
 $aTemplate['Subject']=str_replace('<SenderNickName>',$authorname,$aTemplate['Subject']);
 if ($infoamico['EmailNotify']==1) sendMail($usermailadd, $aTemplate['Subject'], $aTemplate['Body'],$reciver, 'html');
 //fine invio email
}
?>