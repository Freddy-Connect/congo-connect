<?php
/*if (isAdmin())  
{ 
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
}
*/
if(isset($_SERVER['HTTPS'])) 
{
    if ($_SERVER['HTTPS'] == "on") {
        $protocol = 'https';
    }
    else $protocol = 'http';
}
if ($protocol =='') $protocol = 'http';
if (strpos($pagina,'index.php') or substr($pagina, -1)=='/') $currentpageis="/";
elseif (strpos($pagina,'member.php')) $currentpageis="/member.php";
else $currentpageis="/profile.php";
$crpageaddress=$protocol."://".$_SERVER['HTTP_HOST'].$currentpageis;
$idn=0;
$tmpx=0;
$aInfomembers=getProfileInfo($accountid);
$nomedominio=dirname($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
if(!isset($hidden_intro)) echo '<div id="listanotizie">';
require_once (BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/functions.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
$funclass=new swfunc();

//mobile detection
$tablet_browser = 0;
$mobile_browser = 0;
if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
    $tablet_browser++;
}
 
if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
    $mobile_browser++;
}
 
if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
    $mobile_browser++;
}
 
$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
$mobile_agents = array(
    'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
    'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
    'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
    'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
    'newt','noki','palm','pana','pant','phil','play','port','prox',
    'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
    'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
    'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
    'wapr','webc','winw','winw','xda ','xda-');
 
if (in_array($mobile_ua,$mobile_agents)) {
    $mobile_browser++;
}
 
if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
    $mobile_browser++;
    //Check for tablets on opera mini alternative headers
    $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
    if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
      $tablet_browser++;
    }
}
 
if ($tablet_browser > 0 or $mobile_browser > 0) $mobiledetected=1;

//get if HTML5 module is active so to use it for media playing
$getinfhtml5="SELECT COUNT(*) FROM sys_modules WHERE uri='h5av'";
$gethtml5status=mysql_query($getinfhtml5);
$html5status=mysql_fetch_array($gethtml5status);

if ($html5status[0]==0) $playerused=0;
elseif ($html5status[0]==1) $playerused=1;
 
//Get the membership level
$infoMember=getMemberMembershipInfo($accountid);
$profilemembership=$infoMember['ID'];
$limitazionequery=0;
 
//PhotoDeluxe install check
$photodeluxe=0;
$verificaphotodeluxe="SELECT uri FROM sys_modules WHERE uri='photo_deluxe'";
$eseguiverificaphotodeluxe=mysql_query($verificaphotodeluxe);
$numerophotodeluxe=mysql_num_rows($eseguiverificaphotodeluxe);
if($numerophotodeluxe!=0) {$photodeluxe=1;}
 
//integration modules check 
if($photodeluxe==1) 
{
 $integrazionepdx="SELECT integrazionespywallevo,spywallpreview FROM photodeluxe_config WHERE ind=1";
 $eseguiintregazionepdx=mysql_query($integrazionepdx);
 $rowintegrazionepdx=mysql_fetch_assoc($eseguiintregazionepdx);
 $attivaintegrazione=$rowintegrazionepdx['integrazionespywallevo']; 
 $evowallpreview=$rowintegrazionepdx['spywallpreview']; 
}
$verifica_partent=0; 
while($row=mysql_fetch_array($result) and ($limitazionequery<=$limite-1))
{   
 if(!isset($off_parent)) $off_parent=0;
 if($off_parent==0 AND $grouping=='on' and !isset($_GET['id_mode'])) $verifica_partent=$row['recordsfound']-1;
 //if($vp>0 and $verifica_partent==0) $verifica_partent=$vp;  
 $limitazionequery++;
 if($limitazionequery==1 AND !isset($provavariabile)) {$ultimoid=$row['id']; $GLOBAL['ultimoid']=$row['id'];}
 $contanews++;
 $unserialize=unserialize($row['params']);
 $miadata0=$funclass->TempoPost($row['date'],$seldate,$offset);
 //DATE WITH ICON
 $miadata=$funclass->formaticondate($miadata0,$row['lang_key'],$imagepath);  
 $moduletype="";
 $alert_unit="";
 $singmodname="";
 $allowtocheck="";
 $sharelink=""; 
 $titletosearch="";	
 	     
 //COMMONS
 $inviatore=$row['sender_id'];
 $ricevitore=$row['recipient_id'];
 $infoamico=getProfileInfo($row['sender_id']);
 $Miniaturaamico=get_member_icon($infoamico['ID'],'none',false);
 $codiceazione=$row['id'];
 $assegnazione=$row['id'];         
 $parteintroduttiva='<script>$(document).ready(function() {var config={over: fade_BT'.$assegnazione.',out: out_BT'.$assegnazione.',interval:1};$("#azione'.$assegnazione.'").hoverIntent(config);';
 $parteintroduttiva=$parteintroduttiva.'}); function fade_BT'.$assegnazione.'() {$("#fade_bt_list'.$assegnazione.'").fadeIn(1);} function out_BT'.$assegnazione.'() {$("#fade_bt_list'.$assegnazione.'").fadeOut(1);$(".ibdw_bt_superlist").fadeOut(1); $(".mm_setmenu").val(0);$("#fade_bt_list'.$assegnazione.'").removeClass("fix_in_border");$("#menutop_ajax").removeClass("fix_in_border");}</script><div id="azione'.$assegnazione.'"';
 $parteintroduttiva=$parteintroduttiva.' class="azioni">';  
 $sharebutts='<div class="ibdw_evo_bt_list" onclick="open_bt_list('.$assegnazione.');" id="fade_bt_list'.$assegnazione.'"><input type="hidden" value="0" id="mm_setmenu'.$assegnazione.'" class="mm_setmenu" /><a class="bt_open'.$assegnazione.'" id="bt_open"><i alt="" class="sys-icon chevron-down"></i></a></div><div class="ibdw_bt_superlist" id="lista_bt'.$assegnazione.'">'; 
 $LinkUtente=getProfileLink($infoamico['ID']);
 $triangle='</div>';
 $myface='<div id="avatarsimple">'.get_member_thumbnail($infoamico['ID'], 'none', false);
 $parteintroduttiva=$parteintroduttiva.$myface.$triangle;
 unset($parametri_photo);
 $commentsarea="";
 $vp=0;
 if(($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view") or $funclass->ActionVerify($profilemembership,"EVO WALL - Like view") or $funclass->ActionVerify($profilemembership,"EVO WALL - Like add") or $funclass->ActionVerify($profilemembership,"EVO WALL - Comments add")) and ($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowcomment') or $funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlike')) and isLogged()) $commentsarea='scomment.php';
 else $commentsarea='empty.php';
 //END     

 $idn++; 
 $okvista=0;
 include 'customfeed.php';     

 if ($bkunconfirmed=='on' and $infoamico['Status']=='Unconfirmed') $tmpx++;
 else
 {
  //if getpermission is true the post can be displayed. Getpermission check if the post author is blocked or unconfirmed (if the unconfirmed profiles must be blocked)
 
//SHARE, COMMENT ALBUM PHOTODELUXE
if(($row['lang_key']=='_bx_photoalbumshare' or $row['lang_key']=='bx_photo_deluxe_commentoalbum') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Photos"))) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_photoalbumshare') $stampa=_t("_ibdw_evowall_share_album");
  elseif($row['lang_key']=='bx_photo_deluxe_commentoalbum') 
  {
   $stampa=_t("bx_photo_deluxe_commentoalbummsg");
   $commento=$unserialize['commento'];
  }
  $idalbum=$unserialize['idalbum'];
  $estrazione="SELECT ID,Caption,Owner,ObjCount,Description,AllowAlbumView FROM sys_albums WHERE ID=".$idalbum;
  $esecuzione=mysql_query($estrazione);
  
  $itsexistes=mysql_num_rows($esecuzione);
  if ($itsexistes>0)
  {
   $fetchassoc=mysql_fetch_assoc($esecuzione);
   $okvista=$funclass->privstate($fetchassoc['AllowAlbumView'],'photos',$fetchassoc['Owner'],$accountid,isFaved($fetchassoc['Owner'],$accountid),'');
   $tobedisplayed=$fetchassoc['ObjCount']; 
  }
  else 
  {
   $tobedisplayed=0; 
   $okvista=0;
  } 
  if ($tobedisplayed>0 and $okvista==1)
  {
   $generaurl=BX_DOL_URL_ROOT.'page/photodeluxe#ia='.criptcodex($idalbum).'&ui='.criptcodex($fetchassoc['Owner']);
   echo $parteintroduttiva;
   echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if (($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    echo $sharebutts;
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
 	  if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
    {
     /**Share System**/
     include('socialbuttons.php');
     $parametri_photo['Caption']=str_replace("'","xyapostrofos",$fetchassoc['Caption']);
     $parametri_photo['sender_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
     $parametri_photo['sender_p_nick']=getNickname($row['sender_id']);
     $parametri_photo['entry_url']=$generaurl;
     $parametri_photo['idalbum']=$idalbum;
	   $parametri_photo['id_action']=$row['id'];
     $parametri_photo['url_page']=$crpageaddress;
     $params_condivisione=serialize($parametri_photo);   
     $bt_share_params['1']=$accountid; //Sender
     $bt_share_params['2']=$row['sender_id']; 
     $bt_share_params['3']=$row['lang_key']; //Lang_Key_share
     $bt_share_params['4']=readyshare($params_condivisione); //Params
     include('bt_share.php');
     /**End**/
 	  }
	  echo '</div>'; //bt_list div close
	  if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
	  if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
   }
   $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);   
   $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
   $stampa=str_replace('{recipient_nick}',getNickname($row['recipient_id']),$stampa);   
   $stampa=str_replace('{recipient_link}',getUsername($row['recipient_id']),$stampa);
   $stampa=str_replace('{album_url}',$generaurl,$stampa);
   $stampa=str_replace('{album_caption}',$fetchassoc['Caption'],$stampa);
   $stampa=str_replace('xyapostrofos','\'s',$stampa);
   echo $stampa;
   echo '<div id="data">'.$miadata;
   include('like.php');
   echo '</div>';
   echo '</div>';
   echo '</div>';
   if($row['lang_key']=='bx_photo_deluxe_commentoalbum')
   {
    $numero_caratteri=256;
    $stringa_in_input=$commento;
    if(strlen(trim($stringa_in_input))>$numero_caratteri) $testo=substr($stringa_in_input,0,strpos($stringa_in_input,' ',$numero_caratteri)).'...';
    else $testo=$stringa_in_input;
    echo '<div id="commentos"><i alt="" class="sys-icon comments"></i>'.$testo.'</div>';
   }
   echo '<div id="content_album_share">';  
   $estrazione="SELECT sys_albums_objects.id_object,sys_albums.Caption,bx_photos_main.ID,bx_photos_main.Title,bx_photos_main.Hash,bx_photos_main.Ext FROM (sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album) INNER join bx_photos_main ON bx_photos_main.ID=sys_albums_objects.id_object WHERE sys_albums.ID=".$idalbum." ORDER BY ID DESC LIMIT 0,".$evowallpreview;
   $esegui=mysql_query($estrazione);
   $numerazioneal=mysql_num_rows($esegui);
   if($numerazioneal>0) 
   {        
    while($foto=mysql_fetch_array($esegui)) 
    {
     $id_generale_foto_dlx=$assegnazione.$foto['id_object'];
      $script_js='
      <script>
      $(document).ready(function()
 	    {
       var numberofphotos=parseInt('.$numerazioneal.');
       var singlewidth="";
       if(numberofphotos==1) singlewidth="100";
       else if(numberofphotos==2) singlewidth="50";
       else if(numberofphotos==3) singlewidth="33";
       else singlewidth="25";      
       $("#dlx_photozoom'.$id_generale_foto_dlx.'").css("width",singlewidth+"%");
       var newwidth=$("#dlx_photozoom'.$id_generale_foto_dlx.'").width();
       var newheight=Math.round(newwidth*0.6);
       $("#dlx_photozoom'.$id_generale_foto_dlx.'").css("height",newheight);
       var config={over: dlx_pop'.$id_generale_foto_dlx.',out: out_dlx_pop'.$id_generale_foto_dlx.',interval:150};
       $("#dlx_photozoom'.$id_generale_foto_dlx.'").hoverIntent(config);	
       $("#dlx_photozoom'.$id_generale_foto_dlx.'").mouseover(function() {$(this).css("opacity","0.5");});
       $("#dlx_photozoom'.$id_generale_foto_dlx.'").mouseout(function() {$(this).css("opacity","1");});	
      });
      function dlx_pop'.$id_generale_foto_dlx.'()
	    {
       var set_value_photozoom=$("#set_value_sharedlx'.$assegnazione.'").val();
       if(set_value_photozoom!='.$foto['id_object'].') 
	     {
        $("#dlx_photo'.$assegnazione.'").html("<img src=\"m/photos/get_image/file/'.$foto['Hash'].'.'.$foto['Ext'].'\">");
        $("#dlx_photozoom'.$id_generale_foto_dlx.' img").css("opacity","0.5");
        $("#dlx_photo'.$assegnazione.'").fadeIn(1);
        $("#dlx_photo'.$assegnazione.' img").fadeIn(1000);
        $("#set_value_sharedlx'.$assegnazione.'").val('.$foto['id_object'].'); 
       }
      }
      function out_dlx_pop'.$id_generale_foto_dlx.'() {$("#dlx_photozoom'.$id_generale_foto_dlx.' img").css("opacity","1");} 
      </script>';
     echo $script_js;
     if ($numerazioneal==1) echo '<img src="'.BX_DOL_URL_ROOT.'m/photos/get_image/file/'.$foto['Hash'].'.'.$foto['Ext'].'" class="singleimage">';
     elseif ($numerazioneal>1) echo '<div onclick="location.href=\''.$generaurl.'\'" class="albumshareevowall" id="dlx_photozoom'.$id_generale_foto_dlx.'" style="background-image:url(&quot;'.BX_DOL_URL_ROOT.'m/photos/get_image/browse/'.$foto['Hash'].'.'.$foto['Ext'].'&quot;);"></div>';
    }
   }
   echo '</div>';
   echo '<div id="dlx_photo'.$assegnazione.'" class="pop_foto"></div><input type="hidden" id="set_value_sharedlx'.$assegnazione.'" value="0">';
   include ($commentsarea);
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END

//PHOTODELUXE LIKE, TAG, COMMENT PHOTO 
elseif(($row['lang_key']=='_ibdw_photodeluxe_likeadd' OR $row['lang_key']=='bx_photo_deluxe_tag' OR $row['lang_key']=='bx_photo_deluxe_commentofoto') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Photos"))) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='bx_photo_deluxe_commentofoto') $idfoto=$unserialize['idalbum'];
  else $idfoto=$unserialize['idfoto'];
  //get image infos
  $estrazione="SELECT Title,Uri,Owner,Hash,Ext,bx_photos_main.Desc,Size FROM bx_photos_main WHERE ID=".$idfoto;
  $esecuzione=mysql_query($estrazione);
  $fetchassoc=mysql_fetch_assoc($esecuzione); 

  $hash=$fetchassoc['Hash'];
  $exte=$fetchassoc['Ext']; 
  $titolofoto=strmaxtextlen($fetchassoc['Title']);
  $descrizion=$fetchassoc['Desc'];
  ?>
  <script>function extext_<?php echo $row['id'];?>()
  {
   texttorender='<?php echo process_db_input(strip_tags($descrizion));?>';
   $('#desc<?php echo $row['id'];?>').text(texttorender);
  }
  </script>
  <?php
  $descrizionecondividiw=$funclass->tagliaz($funclass->cleartesto($descrizion),300,$row['id']);
  //get album infos
  $estrazionea="SELECT id_album FROM sys_albums_objects WHERE id_object=".$idfoto;
  $esecuzionea=mysql_query($estrazionea);
  $fetchassoca=mysql_fetch_assoc($esecuzionea);
  
  //images path and url to share
  $imageitis=BX_DOL_URL_ROOT.'m/photos/get_image/browse/'.$hash.'.'.$exte;
  $generaurl=BX_DOL_URL_ROOT.'page/photoview?iff='.criptcodex($idfoto).'&ia='.criptcodex($fetchassoca['id_album']).'&ui='.criptcodex($fetchassoc['Owner']);
  if($fetchassoc['Title']==FALSE) $tmpx++;
  else 
  {
   $querypriva="SELECT AllowAlbumView, Owner FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE id_object=".$idfoto." AND TYPE='bx_photos'";
   $resultpriva=mysql_query($querypriva);
   $itsexistes=mysql_num_rows($resultpriva);
   if ($itsexistes>0)
   {
    $rowpriva=mysql_fetch_row($resultpriva);
    $okvista=$funclass->privstate($rowpriva[0],'photos',$rowpriva[1],$accountid,isFaved($rowpriva[1],$accountid),'');
   }
   else $okvista=0; 
   $commenttext=0;	
   if ($okvista==1)
   {
    if($row['lang_key']=='_ibdw_photodeluxe_likeadd')
    {
     if($row['recipient_id']==0) $stampa=_t("_ibdw_evowall_notify_comment_like_yown");
     else $stampa=_t("bx_photo_deluxe_likesfotomsg");
    }
    elseif($row['lang_key']=='bx_photo_deluxe_tag')
    {
     $stampa=_t("bx_photo_deluxe_tag_title");
    }
    elseif($row['lang_key']=='bx_photo_deluxe_commentofoto')
    {
     if($row['sender_id']==$row['recipient_id']) $stampa=_t("_ibdw_evowall_notify_comment_photo_yown");
     else $stampa=_t("bx_photo_deluxe_commentofotomsg");
     $commenttext=1;
    }
    echo $parteintroduttiva;  
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
	  {
     echo $sharebutts;
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      $indirizzourl_true=BX_DOL_URL_ROOT.'m/photos/view/'.$fetchassoc['Uri'];
      include('socialbuttons.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName'];
      $parametri_photo['profile_nick']=$parametri_photo['profile_nick'];
      $parametri_photo['entry_url']=$indirizzourl_true;
      $parametri_photo['entry_caption']=$unserialize['title'];
	    $parametri_photo['id_action']=$row['id'];
	    $parametri_photo['url_page']=$crpageaddress;
      $params_condivisione=serialize($parametri_photo);   
      $bt_share_params['1']=$accountid; //Sender
      $bt_share_params['2']=$row['sender_id']; 
      $bt_share_params['3']='_ibdw_evowall_bx_photo_add_condivisione'; //Lang_Key_share
      $bt_share_params['4']=readyshare($params_condivisione); //Params
      include('bt_share.php');
      /**END SHARING BLOCK**/
	   }
	   echo '</div>'; //div che chiude il bt_list di evo
	   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
	   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';  
    }
    $stampa=str_replace('{sender_p_nick}',getNickname($row['sender_id']),str_replace('{sender_p_link}',getUsername($row['sender_id']),str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa)));
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{recipient_nick}',getNickname($row['recipient_id']),$stampa);
    $stampa=str_replace('{recipient_link}',getUsername($row['recipient_id']),$stampa);  
    $stampa=str_replace('{album_url}',$generaurl,$stampa);
    $stampa=str_replace('<b>','',$stampa);
    $stampa=str_replace('</b>','',$stampa);
    $stampa=str_replace('{album_caption}',$fetchassoc['Title'],$stampa);
    $stampa=str_replace('xyapostrofos','\'s',$stampa);
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div>';
    echo '</div>';
    if($commenttext==1)
    {
     $numero_caratteri=256;
     $stringa_in_input=$unserialize['commento'];
     if(strlen(trim($stringa_in_input))>$numero_caratteri) $testo=substr($stringa_in_input,0,strpos($stringa_in_input,' ',$numero_caratteri)).'...';
     else $textcomm=$stringa_in_input;
     echo '<div id="commentos"><i class="sys-icon comments" alt=""></i>'.$textcomm.'</div>';
    }
    echo '<div id="bloccoav"><div id="anteprima" class="fadeMini'.$assegnazione.'">';
    if ($rowpriva[0]==2) echo '<a href="'.$generaurl.'"><i class="sys-icon lock" alt="" style="font-size:16px;">'._t("_ibdw_evowall_bx_access_denied").'</i></div>';
    elseif ($idfoto==FALSE) echo '<a href="'.$generaurl.'"><img src=src="'.BX_DOL_URL_MODULES.'ibdw/evowall/templates/base/images/unk.png" class="unklockstyle2"></a>';
    else echo '<a href="'.$generaurl.'"><img onload="$(this).fadeIn(200);" src="m/photos/get_image/file/'.$hash.'.'.$exte.'" width="100%"></a></div><div id="descrizione"><a href="'.$generaurl.'"><h3>'.$titolofoto.'</h3></a><p id="desc'.$row['id'].'">'.$descrizion.'</p></div>';
    echo '</div>';
    include ($commentsarea);
    if($verifica_partent!=0)  echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
   }
   else $tmpx++;
  }
 }
 else $tmpx++; 
}
//END

//PROFILE RATED, EDITED, VIEWED, COMMENTED - FRIEND ACCEPT - CHANGED MESSAGE STATUS - JOINED
elseif($row['lang_key']=='_bx_spy_profile_has_rated' OR $row['lang_key']=='_bx_spy_profile_has_edited' OR $row['lang_key']=='_bx_spy_profile_has_viewed' OR $row['lang_key']=='_bx_spy_profile_has_commented' OR $row['lang_key']=='_bx_spy_profile_friend_accept' OR $row['lang_key']=='_bx_spy_profile_has_edited_status_message' OR $row['lang_key']=='_bx_spy_profile_has_joined') 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_spy_profile_has_rated') $stampa=_t("_ibdw_evowall_profile_rate");
  elseif ($row['lang_key']=='_bx_spy_profile_has_viewed') $stampa=_t("_ibdw_evowall_spyprofile");
  elseif ($row['lang_key']=='_bx_spy_profile_has_commented') $stampa=_t("_ibdw_evowall_comment_add");
  elseif ($row['lang_key']=='_bx_spy_profile_has_edited') $stampa=_t("_ibdw_evowall_profile_edit");
  elseif ($row['lang_key']=='_bx_spy_profile_friend_accept') $stampa=_t("_ibdw_evowall_isfriend");
  elseif ($row['lang_key']=='_bx_spy_profile_has_edited_status_message') $stampa=_t("_bx_spy_profile_has_edited_status_message");
  elseif ($row['lang_key']=='_bx_spy_profile_has_joined') $stampa=_t("_ibdw_evowall_member_subscribed");
  if($hideupdate=='' AND $row['lang_key']=='_bx_spy_profile_has_edited') $tmpx++;   
  else
  {
   echo $parteintroduttiva; 
   echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
   {
    echo $sharebutts;
	  include 'rem_ove.php';
    echo '</div>';
    include 'bt_delete.php'; 
   }
   if ($row['lang_key']=='_bx_spy_profile_friend_accept')
   {
    echo '<img class="addit" src="'.BX_DOL_URL_MODULES.'ibdw/evowall/templates/base/images/user_add.png">';
    $stampa=str_replace('{recipient_p_link}',getUsername($row['recipient_id']),$stampa);
    $stampa=str_replace('{recipient_p_nick}',getNickname($row['recipient_id']),$stampa);
    $stampa=str_replace('{sender_p_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{sender_p_nick}',getNickname($row['sender_id']),$stampa);
   }
   if ($row['lang_key']=='_bx_spy_profile_has_edited')
   {
    $getobjid="SELECT object_id FROM bx_spy_data WHERE id=".$row['id'];
    $getthisid=mysql_query($getobjid);
    if(mysql_num_rows($getthisid)>0)
    {
     $idobjx=mysql_fetch_assoc($getthisid);
     $idprf=$idobjx['object_id'];
     if($idprf<>$row['sender_id'])
     {
      $stampa=str_replace('{sender_p_nick}',getNickname($idprf),$stampa);
      $stampa=str_replace('{recipient_p_nick}',getNickname($row['recipient_id']),$stampa);
      $stampa=str_replace('{sender_p_link}',getUsername($idprf),$stampa);
      $stampa=str_replace('{recipient_p_link}',getUsername($row['recipient_id']),$stampa);
      $stampa=str_replace('{profile_nick}',getNickname($idprf),$stampa);
      $stampa=str_replace('{profile_link}',getUsername($idprf),$stampa);
     }
     else
     { 
      $stampa=str_replace('{sender_p_nick}',getNickname($row['sender_id']),$stampa);
      $stampa=str_replace('{recipient_p_nick}',getNickname($row['recipient_id']),$stampa);
      $stampa=str_replace('{sender_p_link}',getUsername($row['sender_id']),$stampa);
      $stampa=str_replace('{recipient_p_link}',getUsername($row['recipient_id']),$stampa);
      $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
      $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
     }
    } 
   }
   else
   { 
    $stampa=str_replace('{sender_p_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{recipient_p_nick}',getNickname($row['recipient_id']),$stampa);
    $stampa=str_replace('{sender_p_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{recipient_p_link}',getUsername($row['recipient_id']),$stampa);
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
   }
   echo $stampa;
   echo '<div id="data">'.$miadata;
   include('like.php');
   echo '</div>';
   echo'</div>';
   echo '</div>';
   if ($row['lang_key']=='_bx_spy_profile_has_edited_status_message') echo "<div class='messagestatus'>".$infoamico['UserStatusMessage']."</div>";
   include ($commentsarea);
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
  }
 }
 else $tmpx++;
}
//END

//SITE 
elseif(($row['lang_key']=='_bx_sites_poll_add' OR $row['lang_key']=='_bx_sites_poll_rate' OR $row['lang_key']=='_bx_sites_poll_commentPost' OR $row['lang_key']=='_bx_sites_poll_change' OR $row['lang_key']=='_ibdw_evowall_bx_site_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Sites"))) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_sites_poll_add') $stampa=_t("_ibdw_evowall_site_add");
  elseif ($row['lang_key']=='_bx_sites_poll_rate') $stampa=_t("_ibdw_evowall_site_rate");
  elseif ($row['lang_key']=='_bx_sites_poll_commentPost') $stampa=_t("_ibdw_evowall_site_comment");
  elseif ($row['lang_key']=='_bx_sites_poll_change') $stampa=_t("_ibdw_evowall_site_edit");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_site_add_condivisione') $stampa=_t("_ibdw_evowall_bx_site_add_condivisione");
  $idswiaz=$row['id'];
  $verificauri= explode ("/",$unserialize['site_url']);
  $namefoto=$unserialize['site_caption'];
  $queryrichiestasito="SELECT title,photo,ownerid,description,entryUri,id,allowView FROM bx_sites_main WHERE entryUri='$verificauri[3]'";
  $resultrichiestasito=mysql_query($queryrichiestasito); 
  $itsexistes=mysql_num_rows($resultrichiestasito);
  if ($itsexistes>0)
  {
    $rowrichiestasito=mysql_fetch_row($resultrichiestasito);
    $okvista=$funclass->privstate($rowrichiestasito['6'],'bx_sites',$rowrichiestasito['3'],$accountid,isFaved($rowrichiestasito['3'],$accountid),'view');
    $fotossito=$rowrichiestasito['1'];
  }
  else $okvista=0; 
  if ($okvista==1)
  {
   if($rowrichiestasito[0]==FALSE or $rowsito[7]=='pending') $tmpx++;
   else
   {
    if ($fotossito<>"")
    {
     $querysito="SELECT ID,Title,Tags,Owner,Hash,Ext,bx_photos_main.Desc,status FROM bx_photos_main WHERE ID=".$fotossito;
     $resultsito=mysql_query($querysito);
     $rowsito=mysql_fetch_row($resultsito);
    }
    $nomesito=$unserialize['site_caption'];
    $nomesito=strmaxtextlen($nomesito); 
    echo $parteintroduttiva;
    ?>
    <script>function extext_<?php echo $row['id'];?>()
    {
     texttorender='<?php echo process_db_input(strip_tags($rowrichiestasito[3]));?>';
     $('#desc<?php echo $row['id'];?>').text(texttorender);
    }
    </script>
    <?php
    $descrizioner=$funclass->tagliaz($funclass->cleartesto($rowrichiestasito[3]),300,$row['id']);
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
    {
     echo $sharebutts;
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';	 
 	   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
   	 {
      /**Share System**/   
      include('socialbuttons.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=getNickname($row['sender_id']);
      $parametri_photo['site_url']=$unserialize['site_url'];
      $parametri_photo['site_caption']=$unserialize['site_caption'];
	    $parametri_photo['id_action']=$row['id'];
	    $parametri_photo['url_page']=$crpageaddress;
      $params_condivisione=serialize($parametri_photo);   
      $bt_share_params['1']=$accountid; //Sender
      $bt_share_params['2']=$row['sender_id']; //Recipient 
      $bt_share_params['3']='_ibdw_evowall_bx_site_add_condivisione'; //Lang_Key_share 
      $bt_share_params['4']=readyshare($params_condivisione); //Params
      include('bt_share.php'); 
      /**End**/	 
     }
     echo '</div>'; //bt_list div close
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
    }
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{site_url}',$unserialize['site_url'],$stampa);
    $stampa=str_replace('{site_caption}',$nomesito,$stampa);          
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div>';
    echo '</div><div id="bloccoav">';
    if ($rowsito[4]!=FALSE) echo'<div id="videopreview"><a href="'.$unserialize['site_url'].'"><img class="webimage" src="m/photos/get_image/browse/'.$rowsito[4].'.'.$rowsito[5].'"></a></div>';
    echo '<div id="descrizione"><a href="'.$unserialize['site_url'].'"><h3>'.$unserialize['site_caption'].'</h3></a><p id="desc'.$row['id'].'">'.$descrizioner.'</p></div>';
    echo '</div>';
    include ($commentsarea);
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END

//VIDEO
elseif (($row['lang_key']=='_bx_videos_spy_added' OR $row['lang_key']=='_bx_videos_spy_rated' OR $row['lang_key']=='_bx_videos_spy_comment_posted' OR $row['lang_key']=='_ibdw_evowall_bx_video_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Videos")))
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_videos_spy_added') $stampa=_t("_ibdw_evowall_video_add");
  elseif ($row['lang_key']=='_bx_videos_spy_rated') $stampa=_t("_ibdw_evowall_rated_videos");
  elseif ($row['lang_key']=='_bx_videos_spy_comment_posted') $stampa=_t("_ibdw_evowall_comment_name");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_video_add_condivisione') $stampa=_t("_ibdw_evowall_bx_video_add_condivisione");
  $trovaslash=substr_count($unserialize['entry_url'],"/");
  $verificauri=explode ("/",$unserialize['entry_url']);
  $verificauri=$verificauri[$trovaslash];
  $queryvideo="SELECT ID,Title,Description,Owner,Source,Video,Uri,Status FROM RayVideoFiles WHERE Uri='$verificauri'";
  $resultvideo=mysql_query($queryvideo);
  $rowvideo=mysql_fetch_assoc($resultvideo);
  $nomevideo=strmaxtextlen($unserialize['entry_caption']);
  
  if($rowvideo['Title']==FALSE) {$tmpx++;$dlt="DELETE FROM `bx_spy_data` WHERE `id`=".$row['id']; $dlt_exe=mysql_query($dlt);}
  else 
  {
   $querypriva="SELECT AllowAlbumView,Owner FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE sys_albums_objects.id_object=".$rowvideo['ID']." AND sys_albums.Type='bx_videos'";
   $resultpriva=mysql_query($querypriva);
   $ifexstit=mysql_num_rows($resultpriva);
   if($ifexstit>0)
   {
    $rowpriva=mysql_fetch_row($resultpriva);
    $okvista=$funclass->privstate($rowpriva[0],'videos',$rowpriva[1],$accountid,isFaved($rowpriva[1],$accountid),'');
   }
   else $okvista=0;
   if ($okvista==1)
   {
    if($rowvideo['Status']!='approved') 
    {
     echo $parteintroduttiva.'<div id="messaggio"><div id="primariga">';
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
     {
	    echo $sharebutts;
      include 'rem_ove.php';
      echo '</div>';
     }
     echo '<a class="usernamestyle" href="'.$unserialize['profile_link'].'">';
     echo getNickname($row['sender_id']);
     echo '</a> <span class="actionx">'._t("_ibdw_evowall_bx_video_under").' '.$nomevideo.' '._t("_ibdw_evowall_bx_video_undernotify").'</span>';
	   echo '</div></div></div>';
    }
    else 
    {
     $idswial=$row['id']; 
     echo $parteintroduttiva;
     echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
     if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
	   {
	    echo $sharebutts;
      if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';  
 	    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
      {
       /**Share System**/
       include('socialbuttons.php');
       $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
       $parametri_photo['profile_nick']=getNickname($row['sender_id']);
       $parametri_photo['entry_url']=$unserialize['entry_url'];
       $parametri_photo['entry_caption']=$unserialize['entry_caption'];
	     $parametri_photo['id_action']=$row['id'];
 	     $parametri_photo['url_page']=$crpageaddress;
       $params_condivisione=serialize($parametri_photo);   
       $bt_share_params['1']=$accountid; //Sender
       $bt_share_params['2']=$row['sender_id']; //Recipient 
       $bt_share_params['3']='_ibdw_evowall_bx_video_add_condivisione'; //Lang_Key_share 
       $bt_share_params['4']=readyshare($params_condivisione); //Params
       include('bt_share.php'); 
       /**End**/
      }
      echo '</div>'; //bt_list div close
	    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
	    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
	   }
     
     
     
     if($row['sender_id']==$rowvideo['Owner'])
     {
      if ($row['lang_key']=='_bx_videos_spy_rated') $stampa=_t("_ibdw_evowall_rated_videos_own");
      elseif ($row['lang_key']=='_bx_videos_spy_comment_posted') $stampa=_t("_ibdw_evowall_comment_name_own");
     }
     $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
     $stampa=str_replace('{recipient_p_nick}',getNickname($rowvideo['Owner']),$stampa);
     $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
     $stampa=str_replace('{recipient_p_link}',getUsername($rowvideo['Owner']),$stampa);
     $stampa=str_replace('{video_url}',$unserialize['entry_url'],$stampa);
     $stampa=str_replace('{entry_caption}',$nomevideo,$stampa);
     $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
     echo $stampa;
     echo '<div id="data">'.$miadata;
     include('like.php');
     echo '</div>';
     echo '</div>';
     echo '</div>
     <div id="bloccoav"><div id="videopreview" class="video'.$idswial.'">
 	 <script>
	  function fadeTub'.$idswial.'() 
    {
     $("#tubeswich'.$idswial.'").fadeIn();
     $("#tubeswich'.$idswial.'").css("width","100%");
     $(".video'.$idswial.'").css("width","100%");
     var divh'.$idswial.'=$(".video'.$idswial.'").width();
     $(".video'.$idswial.'").css("height",divh'.$idswial.'*0.56);
     $("#tubeswich'.$idswial.'").css("height","100%");
    }
	  function fadeOutTub'.$idswial.'() 
	  {
	   $("#descrizione'.$idswial.' h3").css("float","left");
	   $("#descrizione'.$idswial.' h3").css("width","100%");
	   $("#descrizione'.$idswial.' p").css("float","left");
	  }
	  function scrollatube'.$idswial.'()
	  {
	   $("#imgtube'.$idswial.'").fadeOut(500);
	   $("#playbottone'.$idswial.'").fadeOut(500);
	   setTimeout("fadeTub'.$idswial.'()",1000);
     setTimeout("fadeOutTub'.$idswial.'()",2000);
     $(".video'.$idswial.'").css("margin-left","0");
     $(".video'.$idswial.'").css("width","100%");
     $("#descrizione'.$idswial.'").css("padding","0 10px");
	   ';
     
     if (($rowvideo['Source']=='youtube' or $rowvideo['Source']=='YouTube') and $playerused==1)
     {
      $frame='<iframe width="100%" height="100%" frameborder="0" allowfullscreen="" src="'.$protocol.'://www.youtube-nocookie.com/embed/'.$rowvideo["Video"].'?rel=0&amp;showinfo=0&amp;autoplay=1"></iframe>';
      echo "$('#plyYTB".$idswial."').replaceWith('".$frame."');";
     } 
     
	   if ($rowvideo['Source']=='') echo '$("#bx-media'.$idswial.'")[0].play();';
	   echo '
    }
	  </script>
    <style> #tubeswich'.$idswial.'{display:none;}</style>';
    if ($rowvideo['Source']=='youtube' or $rowvideo['Source']=='YouTube') 
    {
     if($rowvideo['Source']=='YouTube')
     {  
      $rowvideo['Video']=str_replace("&lt;iframe class=&quot;embedly-embed&quot; src=&quot;//cdn.embedly.com/widgets/media.html?src=http%3A%2F%2Fwww.youtube.com%2Fembed%2F","",$rowvideo['Video']);
      $str=explode('%3F', $rowvideo['Video']);
      $rowvideo['Video']=$str[0];
     }
     echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url('.$protocol.'://i.ytimg.com/vi/'.$rowvideo['Video'].'/default.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div id="tubeswich'.$idswial.'" class="tubeswich">';
     
     if ($playerused==0) echo '<object width="100%" height="100%" style="display: block;"><param name="movie" value="'.$protocol.'://www.youtube.com/v/'.$rowvideo['Video'].'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="'.$protocol.'://www.youtube.com/v/'.$rowvideo['Video'].'&autoplay=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="100%" height="100%" wmode="opaque"></embed></object>';
     elseif ($playerused==1)
     {
      echo '<div id="plyYTB'.$idswial.'"></div>';
     }
     echo '</div>';
    }
	  elseif ($rowvideo['Source']=='') 
	  {
	   echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'">';
	   if ($playerused==0)
	   {
	    echo '<object width="100%" style="display: block;"><param value="flash/modules/global/app/holder_as3.swf" name="movie"><param value="always" name="allowScriptAccess"><param value="true" name="allowFullScreen"><param value="flash/modules/video/" name="base"><param value="#FFFFFF" name="bgcolor"><param value="opaque" name="wmode"><param value="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=video&amp;app=player&amp;id='.$rowvideo['ID'].'&amp;user=&amp;password=" name="flashVars"><embed height="100%" width="100%" flashvars="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=video&amp;app=player&amp;id='.$rowvideo['ID'].'&amp;user=&amp;password=" wmode="opaque" bgcolor="#FFFFFF" base="flash/modules/video/" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" src="flash/modules/global/app/holder_as3.swf"></object>';
     }
	   elseif ($playerused==1)
	   {
	    $percorsofile=BX_DIRECTORY_PATH_ROOT."/flash/modules/video/files/";
	    if (file_exists($percorsofile . $rowvideo['ID'] . '.webm'))
	    {
	    //USE HTML5 PLAYER
		  $gettokenvideo="SELECT Token FROM RayVideoTokens WHERE ID=".$rowvideo['ID']." LIMIT 0,1";
	    $gettokenfromid=mysql_query($gettokenvideo);
	    $resulttok=mysql_fetch_assoc($gettokenfromid);
	    if ($resulttok['Token']==NULL) 
	    { 
	     $iCurrentTime = time();
       $sToken = md5($iCurrentTime);
	     $creaquery="INSERT INTO RayVideoTokens (ID,Token,Date) VALUES('".$rowvideo['ID']."','".$sToken."','".$iCurrentTime."')";
	 	   $creatoken=mysql_query($creaquery);
		   $gettokenvideo="SELECT Token FROM RayVideoTokens WHERE ID=".$rowvideo['ID']." LIMIT 0,1";
	     $gettokenfromid=mysql_query($gettokenvideo);
	     $resulttok=mysql_fetch_assoc($gettokenfromid);
	    }
	    echo '<video id="bx-media'.$assegnazione.'" style="width:100%;" autobuffer="" preload="auto" controls="" tabindex="0">
              <source src="'.BX_DOL_URL_ROOT.'flash/modules/video/get_file.php?id='.$rowvideo['ID'].'&amp;ext=webm&amp;token='.$resulttok['Token'].'" type="video/webm; codecs=&quot;vp8, vorbis&quot;"></source>
              <source src="'.BX_DOL_URL_ROOT.'flash/modules/video/get_file.php?id='.$rowvideo['ID'].'&amp;ext=m4v&amp;token='.$resulttok['Token'].'"></source>
              <script language="javascript" type="text/javascript">
        	  function reload() {location.href=\'/modules/index.php?r=videos/view/'.$rowvideo['Uri'].'&module=video&app=player\';}
    	 	  </script>
			  <div id="video_player">
			   <object width="100%" type="application/x-shockwave-flash" id="ray_flash_video_player_object" name="ray_flash_video_player_embed" style="display:block;" data="'.BX_DOL_URL_ROOT.'flash/modules/global/app/holder_as3.swf"><param name="allowScriptAccess" value="always"><param name="allowFullScreen" value="true"><param name="base" value="'.BX_DOL_URL_ROOT.'flash/modules/video/"><param name="bgcolor" value="#FFFFFF"><param name="wmode" value="opaque"><param name="flashvars" value="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=video&amp;app=player&amp;id=16&amp;user=&amp;password=">
			   </object>
			  </div>
			  <script language="javascript" type="text/javascript">
			   var flashvars={url:"'.BX_DOL_URL_ROOT.'flash/XML.php",module:"video",app:"player",id:"16",user:"",password:""};
			   var params={allowScriptAccess:"always",allowFullScreen:"true",base:"'.BX_DOL_URL_ROOT.'flash/modules/video/",bgcolor:"#FFFFFF",wmode:"opaque"};
			   var attributes = {id: "ray_flash_video_player_object",name: "ray_flash_video_player_embed",style: "display:block;"};
			   swfobject.embedSWF("'.BX_DOL_URL_ROOT.'flash/modules/global/app/holder_as3.swf", "video_player_1359543421", "100%", "", "9.0.0", "'.BX_DOL_URL_ROOT.'flash/modules/global/app/expressInstall.swf", flashvars, params, attributes);
			  </script>
             </video>';
	   }
	   else
	   {
	    //USE BOONEX FLASH PLAYER
		  echo '<object height="100%"" width="100%" style="display: block;"><param value="flash/modules/global/app/holder_as3.swf" name="movie"><param value="always" name="allowScriptAccess"><param value="true" name="allowFullScreen"><param value="flash/modules/video/" name="base"><param value="#FFFFFF" name="bgcolor"><param value="opaque" name="wmode"><param value="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=video&amp;app=player&amp;id='.$rowvideo['ID'].'&amp;user=&amp;password=" name="flashVars"><embed height="100%" width="100%" flashvars="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=video&amp;app=player&amp;id='.$rowvideo['ID'].'&amp;user=&amp;password=" wmode="opaque" bgcolor="#FFFFFF" base="flash/modules/video/" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" src="flash/modules/global/app/holder_as3.swf"></object>';
	   }
	  }
	  echo '</div>';
	 }
	 elseif ($rowvideo['Source']=='bliptv' or $rowvideo['Source']=='Blip') 
   {
    if($rowvideo['Source']=='Blip')
    {
     $str=urldecode($rowvideo['Video']); 
     $substr=explode('cdn.embedly.com/widgets/media.html?src=', $str); 
     $substr2=$substr[1];
     $substr3=explode('&amp;url=', $substr2); 
     $substr4=$substr3[0];
     $substr5=str_replace("http://blip.tv/play/","",$substr4);
     $substr6=explode('.', $substr5);
     $substr7=$substr6[0];
     echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'">
            <iframe src="'.$substr4.'" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>
            <embed type="application/x-shockwave-flash" src="http://a.blip.tv/api.swf#'.$substr7.'" style="display:none"></embed>
           </div>';
    }
    else echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><embed width="100%" height="100%" wmode="opaque" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" src="http://blip.tv/play/'.$rowvideo['Video'].'"></div>';
   }
   elseif ($rowvideo['Source']=='myspace') 
   {
    echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="100%" height="100%"><param value="true" name="allowFullScreen"><param value="opaque" name="wmode"><param value="http://mediaservices.myspace.com/services/media/embed.aspx/m='.$rowvideo['Video'].',t=1,mt=video,ap=true" name="movie"><embed width="100%" height="100%" wmode="opaque" type="application/x-shockwave-flash" allowfullscreen="true" src="http://mediaservices.myspace.com/services/media/embed.aspx/m='.$rowvideo['Video'].',t=1,mt=video,ap=true"></object></div>';
   }
   elseif ($rowvideo['Source']=='slutload') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="100%" height="100%" data="http://emb.slutload.com/'.$rowvideo['Video'].'" type="application/x-shockwave-flash"><param value="always" name="AllowScriptAccess"><param value="http://emb.slutload.com/'.$rowvideo['Video'].'" name="movie"><param value="opaque" name="wmode"><param value="true" name="allowfullscreen"><embed width="100%" height="100%" wmode="opaque" allowfullscreen="true" allowscriptaccess="always" src="http://emb.slutload.com/'.$rowvideo['Video'].'"></object></div>';
	 elseif ($rowvideo['Source']=='gaywatch') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="100%" height="100%" data="http://www.gaywatch.com/mediaplayer.swf" type="application/x-shockwave-flash"><param value="http://www.gaywatch.com/mediaplayer.swf" name="movie"><param value="true" name="allowFullScreen"><param value="opaque" name="wmode"><param value="config=http://www.gaywatch.com/video_config.php?id='.$rowvideo['Video'].'&amp;autostart=true" name="flashvars"></object></div>';
   elseif ($rowvideo['Source']=='movieclips') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="100%" height="100%" data="http://static.movieclips.com/embedplayer.swf?shortid='.$rowvideo['Video'].'" type="application/x-shockwave-flash" style="display:object;"><param value="http://static.movieclips.com/embedplayer.swf?shortid='.$rowvideo['Video'].'" name="movie"><param value="opaque" name="wmode"><param value="always" name="allowscriptaccess"><param value="true" name="allowfullscreen"><embed width="100%" height="100%" allowfullscreen="true" allowscriptaccess="always" wmode="opaque" type="application/x-shockwave-flash" src="http://static.movieclips.com/embedplayer.swf?shortid='.$rowvideo['Video'].'"></object></div>';
	 elseif ($rowvideo['Source']=='vimeo') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="100%" height="100%" data="http://vimeo.com/moogaloop.swf" type="application/x-shockwave-flash" style="display:object;"><param value="always" name="allowscriptaccess"><param value="true" name="allowfullscreen"><param value="http://vimeo.com/moogaloop.swf" name="movie"><param value="opaque" name="wmode"><param value="clip_id='.$rowvideo['Video'].'&amp;server=vimeo.com&amp;fullscreen=1&amp;show_title=0&amp;show_byline=1&amp;show_portrait=1&amp;color=00ADEF&amp;autoplay=0" name="flashvars"></object></div>';
	 elseif ($rowvideo['Source']=='dailymotion') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="100%" height="100%" style="display:object;"><param value="http://www.dailymotion.com/swf/'.$rowvideo['Video'].'?autoPlay=0&amp;related=0" name="movie"><param value="opaque" name="wmode"><param value="true" name="allowFullScreen"><param value="always" name="allowScriptAccess"><embed width="100%" height="100%" wmode="opaque" allowscriptaccess="always" allowfullscreen="true" type="application/x-shockwave-flash" src="http://www.dailymotion.com/swf/'.$rowvideo['Video'].'?autoPlay=0&amp;related=0"></object></div>'; 
	 elseif ($rowvideo['Source']=='godtube') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="100%" height="100%" data="http://www.godtube.com/resource/mediaplayer/5.3/player.swf" type="application/x-shockwave-flash"><param value="http://www.godtube.com/resource/mediaplayer/5.3/player.swf" name="movie"><param value="true" name="allowfullscreen"><param value="always" name="allowscriptaccess"><param value="opaque" name="wmode"><param value="file=http://www.godtube.com/resource/mediaplayer/'.$rowvideo['Video'].'.file&amp;image=http://www.godtube.com/resource/mediaplayer/'.$rowvideo['Video'].'.jpg&amp;screencolor=000000&amp;type=video&amp;autostart=false&amp;playonce=true&amp;skin=http://www.godtube.com//resource/mediaplayer/skin/carbon/carbon.zip&amp;logo.file=http://media.salemwebnetwork.com/godtube/theme/default/media/embed-logo.png&amp;logo.link=http://www.godtube.com/watch/%3Fv%3D'.$rowvideo['Video'].'&amp;logo.position=top-left&amp;logo.hide=false&amp;controlbar.position=over" name="flashvars"></object></div>';
   elseif ($rowvideo['Source']=='metacafe') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><embed width="100%" height="100%" allowfullscreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" wmode="opaque" src="http://www.metacafe.com/fplayer/'.$rowvideo['Video'].'/test.swf?playerVars=autoPlay=no"></div>';
   elseif ($rowvideo['Source']=='redtube') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="100%" height="100%" style="display:object;"><param value="http://embed.redtube.com/player/" name="movie"><param value="id='.$rowvideo['Video'].'&amp;style=redtube&amp;autostart=false" name="FlashVars"><param value="true" name="allowFullScreen"><param value="opaque" name="wmode"><embed width="100%" height="100%" wmode="opaque" allowfullscreen="true" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" flashvars="autostart=false" src="http://embed.redtube.com/player/?id='.$rowvideo['Video'].'&amp;style=redtube"></object></div>';
   elseif ($rowvideo['Source']=='sextube') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><embed width="100%" height="100%" flashvars="autostart=false&amp;provider=http&amp;logo.hide=true&amp;config=http://www.sextube.com/flv_player/data/playerConfig/'.$rowvideo['Video'].'.xml" wmode="opaque" allowscriptaccess="always" allowfullscreen="true" bgcolor="000000" src="http://www.sextube.com/flv_player/skins/new/player.swf"></div>';
   elseif ($rowvideo['Source']=='wattv') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="100%" height="100%" id="wat"><param value="http://www.wat.tv/swf2/'.$rowvideo['Video'].'" name="movie"><param value="true" name="allowFullScreen"><param value="always" name="allowScriptAccess"><param value="opaque" name="wmode"><embed width="100%" height="100%" wmode="opaque" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" src="http://www.wat.tv/swf2/'.$rowvideo['Video'].'"></object></div>';
   elseif ($rowvideo['Source']=='xtube') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="100%" height="100%" align="middle" id="slideshow" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><param value="always" name="allowScriptAccess"><param value="opaque" name="wmode"><param value="true" name="allowFullScreen"><param value="swfURL=http://cdn1.publicvideo.xtube.com&amp;autoplay=0&amp;video_id='.$rowvideo['Video'].'&amp;en_flash_lib_path=http://cdn1.static.xtube.com/embed/library.swf" name="flashVars"><param value="http://cdn1.static.xtube.com/embed/scenes_player.swf?xv=1" name="movie"><param value="high" name="quality"><param value="#000000" name="bgcolor"><param value="true" name="allowFullScreen"><param value="http://www.xtube.com/play_re.php?v='.$rowvideo['Video'].'" name="targetUrl"><embed width="100%" height="100%" align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="opaque" allowscriptaccess="always" name="slideshow" allowfullscreen="true" bgcolor="#000000" quality="high" src="http://cdn1.static.xtube.com/embed/scenes_player.swf?xv=1" flashvars="swfURL=http://cdn1.publicvideo.xtube.com&amp;autoplay=0&amp;video_id='.$rowvideo['Video'].'&amp;en_flash_lib_path=http://cdn1.static.xtube.com/embed/library.swf"></object></div>';
   elseif ($rowvideo['Source']=='xvideos') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="100%" height="100%" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><param value="high" name="quality"><param value="#000000" name="bgcolor"><param value="always" name="allowScriptAccess"><param value="opaque" name="wmode"><param value="http://static.xvideos.com/swf/flv_player_site_v4.swf" name="movie"><param value="true" name="allowFullScreen"><param value="id_video='.$rowvideo['Video'].'" name="flashvars"><embed width="100%" height="100%" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="opaque" flashvars="id_video='.$rowvideo['Video'].'" allowfullscreen="true" bgcolor="#000000" quality="high" menu="false" allowscriptaccess="always" src="http://static.xvideos.com/swf/flv_player_site_v4.swf"></object></div>';
   elseif ($rowvideo['Source']=='xxxbunker') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><object width="100%" height="100%" style="display:object;"><param value="http://xxxbunker.com/flash/player.swf" name="movie"><param value="opaque" name="wmode"><param value="true" name="allowfullscreen"><param value="always" name="allowscriptaccess"><param value="config=http://xxxbunker.com/playerConfig.php?videoid='.$rowvideo['Video'].'&amp;autoplay=false" name="flashvars"><embed width="100%" height="100%" flashvars="config=http://xxxbunker.com/playerConfig.php?videoid='.$rowvideo['Video'].'&amp;autoplay=false" wmode="opaque" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" src="http://xxxbunker.com/flash/player.swf"></object></div>';
	 elseif ($rowvideo['Source']=='youku') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'"><embed src="http://player.youku.com/player.php/sid/'.$rowvideo['Video'].'/v.swf" allowFullScreen="true" quality="high" width="100%" height="100%" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash"></embed></div>';
   elseif($rowvideo['Source']=='Justin.tv' or $rowvideo['Source']=='WAT') echo '<div class="imgtube" id="imgtube'.$idswial.'" style="background:url(flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg); width:120px; height:90px;"><div class="playbottone" id="playbottone'.$idswial.'" onclick="scrollatube'.$idswial.'()"></div></div><div class="tubeswich" id="tubeswich'.$idswial.'">'.htmlspecialchars_decode($rowvideo['Video']).'</div>';
   else $tmpx++;
   ?>
   <script>function extext_<?php echo $row['id'];?>()
  {
   texttorender='<?php echo process_db_input(strip_tags($rowvideo['Description']));?>';
   $('#desc<?php echo $row['id'];?>').text(texttorender);
  }
  </script>
  <?php
   $descrizionee=$funclass->tagliaz($funclass->cleartesto($rowvideo['Description']),300,$row['id']);
   echo '</div><div id="descrizione'.$idswial.'" class="descrizione"><a href="'.$unserialize['entry_url'].'"><h3>'.$rowvideo['Title'].'</h3></a><p id="desc'.$row['id'].'">'.$descrizionee.'</p></div>';
   echo '</div>';
   include ($commentsarea);
	 if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
    }
   }
   elseif ($okvista==0 and $verifica_partent!=0) {}
   else $tmpx++;
  }
 }
 else $tmpx++;
}
//END

// ADS
elseif (($row['lang_key']=='_bx_ads_added_spy' OR $row['lang_key']=='_bx_ads_commented_spy' OR $row['lang_key']=='_bx_ads_rated_spy' OR $row['lang_key']=='_ibdw_evowall_bx_ads_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Ads"))) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_ads_added_spy') $stampa=_t("_ibdw_evowall_ads_add");
  elseif ($row['lang_key']=='_bx_ads_rated_spy') $stampa=_t("_ibdw_evowall_ads_rate");
  elseif ($row['lang_key']=='_bx_ads_commented_spy') $stampa=_t("_ibdw_evowall_ads_comment");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_ads_add_condivisione') $stampa=_t("_ibdw_evowall_bx_ads_add_condivisione");
  $idswiah=$row['id'];
  $titoloannuncioa=strmaxtextlen($unserialize['ads_caption']);
  $trovaslash=substr_count($unserialize['ads_url'],"/");
  $verificauri=explode ("/",$unserialize['ads_url']);
  $verificauri=$verificauri[$trovaslash];
  $queryannuncio="SELECT ID,IDProfile,Subject,Message,Status,CustomFieldValue1,Media,AllowView FROM bx_ads_main WHERE EntryUri='$verificauri'";
  $resultqueryannuncio=mysql_query($queryannuncio);
  $rowqueryannuncio=mysql_fetch_assoc($resultqueryannuncio);
  $numeroseriale=$rowqueryannuncio['Media'];
  $queryannunciofoto="SELECT MediaID,MediaFile FROM bx_ads_main_media WHERE MediaID='$numeroseriale'";
  $resultqueryannunciofoto=mysql_query($queryannunciofoto);
  $rowqueryannunciofoto=mysql_fetch_row($resultqueryannunciofoto);
  ?>
  <script>function extext_<?php echo $row['id'];?>()
  {
   texttorender='<?php echo process_db_input(strip_tags($rowqueryannuncio['Message']));?>';
   $('#desc<?php echo $row['id'];?>').text(texttorender);
  }
  </script>
  <?php
  $descrizioneannuncio=$funclass->tagliaz($funclass->cleartesto($rowqueryannuncio['Message']),300,$row['id']);
  $okvista=$funclass->privstate($rowqueryannuncio['AllowView'],'ads',$rowqueryannuncio['IDProfile'],$accountid,isFaved($rowqueryannuncio['IDProfile'],$accountid),'view');
  if($rowqueryannuncio['Subject']==FALSE or $rowqueryannuncio['Status']=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   echo $parteintroduttiva;
   echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0))
   {
    echo $sharebutts;
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
    {
     /**Share System**/
     include('socialbuttons.php');
     $parametri_photo['recipient_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
     $parametri_photo['recipient_p_nick']=$aInfomembers['NickName'];
     $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
     $parametri_photo['profile_nick']=getNickname($row['sender_id']);
     $parametri_photo['ads_url']=$unserialize['ads_url'];
     $parametri_photo['ads_caption']=$unserialize['ads_caption'];
	   $parametri_photo['id_action']=$row['id'];
     $parametri_photo['url_page']=$crpageaddress;
     $params_condivisione=serialize($parametri_photo);
     $bt_share_params['1']=$accountid; //Sender
     $bt_share_params['2']=$row['sender_id']; //Recipient 
     $bt_share_params['3']='_ibdw_evowall_bx_ads_add_condivisione'; //Lang_Key_share 
     $bt_share_params['4']=readyshare($params_condivisione); //Params
     include('bt_share.php');
     /**End**/
    }       
    echo '</div>'; //bt_list div close
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
   }
   $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
   $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
   $stampa=str_replace('{ads_url}',$unserialize['ads_url'],$stampa);
   $titoloannuncios=$unserialize['ads_caption'];
   $titoloannuncios=strmaxtextlen($titoloannuncios);
   $stampa=str_replace('{ads_caption}',$titoloannuncios,$stampa);
   echo $stampa;
   echo '<div id="data">'.$miadata;
   include('like.php');
   echo '</div>';
   echo '</div></div><div id="bloccoav">';
   if ($rowqueryannunciofoto[1]!=FALSE) echo '<div id="videopreview"><a href="'.$unserialize['ads_url'].'"><img src="media/images/classifieds/thumb_'.$rowqueryannunciofoto[1].'" class="webimage"></a></div>';
   echo '<div id="descrizione"><a href="'.$unserialize['ads_url'].'"><h3>'.$titoloannuncioa.'</h3></a> <p id="desc'.$row['id'].'">'.$descrizioneannuncio.'</p>';
   if ($rowqueryannuncio['CustomFieldValue1']<>0) echo '<div id="adsdatacont"><strong>'._t("_adm_txt_mlevels_price").'</strong>: '._t("_ibdw_evowall_currency_price").number_format($rowqueryannuncio['CustomFieldValue1'],2,",",".").'</div>';
   echo '</div></div>';
   
   include ($commentsarea);
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END

// BLOGS
elseif (($row['lang_key']=='_bx_blog_added_spy' OR $row['lang_key']=='_bx_blog_rated_spy' OR $row['lang_key']=='_bx_blog_commented_spy' OR $row['lang_key']=='_ibdw_evowall_bx_blogs_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Blogs"))) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_blog_added_spy') {$stampa=_t("_ibdw_evowall_blogs_add");}
  elseif ($row['lang_key']=='_bx_blog_rated_spy') {$stampa=_t("_ibdw_evowall_blogs_rate");}
  elseif ($row['lang_key']=='_bx_blog_commented_spy') {$stampa=_t("_ibdw_evowall_blogs_comment");}
  elseif ($row['lang_key']=='_ibdw_evowall_bx_blogs_add_condivisione') {$stampa=_t("_ibdw_evowall_bx_blogs_add_condivisione");}
  $idswiah=$row['id'];
  $blogtitle=strmaxtextlen($unserialize['post_caption']);
  $trovaslash=substr_count($unserialize['post_url'],"/");
  $verificauri=explode ("/",$unserialize['post_url']);
  $verificauri=$verificauri[$trovaslash];
  $queryblog="SELECT * FROM bx_blogs_posts WHERE PostUri='$verificauri'";
  $resultqueryblog=mysql_query($queryblog);
  $getifthisex=mysql_num_rows($resultqueryblog);
  if ($getifthisex>0)
  {
   $rowqueryblog=mysql_fetch_assoc($resultqueryblog);
   $fotoblog=$rowqueryblog['PostPhoto'];
   ?>
   <script>function extext_<?php echo $row['id'];?>()
   {
    texttorender='<?php echo process_db_input(strip_tags($rowqueryblog['PostText']));?>';
    $('#desc<?php echo $row['id'];?>').text(texttorender);
   }
   </script>
   <?php
   $descrizioneblog=$funclass->tagliaz($funclass->cleartesto($rowqueryblog['PostText']),300,$row['id']);
   $okvista=$funclass->privstate($rowqueryblog['allowView'],'blogs',$rowqueryblog['OwnerID'],$accountid,isFaved($rowqueryblog['OwnerID'],$accountid),'view');
   if ($okvista==1) 
   {
    echo $parteintroduttiva;
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0))
    {
     echo $sharebutts;
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php'; 
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('socialbuttons.php');
      $parametri_photo['recipient_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['recipient_p_nick']=$aInfomembers['NickName'];
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=getNickname($row['sender_id']);
      $parametri_photo['post_url']=$unserialize['post_url'];
      $parametri_photo['post_caption']=$unserialize['post_caption'];
	    $parametri_photo['id_action']=$row['id'];
      $parametri_photo['url_page']=$crpageaddress;
      $params_condivisione=serialize($parametri_photo);
      $bt_share_params['1']=$accountid; //Sender
      $bt_share_params['2']=$row['sender_id']; //Recipient 
      $bt_share_params['3']='_ibdw_evowall_bx_blogs_add_condivisione'; //Lang_Key_share 
      $bt_share_params['4']=readyshare($params_condivisione); //Params
      include('bt_share.php');
      /**End**/
     }       
     echo '</div>'; //bt_list div close
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
    }
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{post_url}',$unserialize['post_url'],$stampa);
    $blogtitle=strmaxtextlen($unserialize['post_caption']);
    $stampa=str_replace('{post_caption}',$blogtitle,$stampa);
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div>';
    echo '</div><div id="bloccoav_contents">';
    if ($fotoblog!=FALSE) echo '<div id="anteprima_boxed_contents"><a href="'.$unserialize['post_url'].'"><img src="media/images/blog/big_'.$fotoblog.'" class="webimage"></a></div>';
    echo '<div id="descrizione"><a href="'.$unserialize['post_url'].'"><h3>'.$blogtitle.'</h3></a><p id="desc'.$row['id'].'">'.$descrizioneblog.'</p></div>'; 
    echo '</div>';
    include ($commentsarea);
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
   else $tmpx++;
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END

// SOUND
elseif (($row['lang_key']=='_bx_sounds_spy_added' OR $row['lang_key']=='_bx_sounds_spy_comment_posted' OR $row['lang_key']=='_bx_sounds_spy_rated' OR $row['lang_key']=='_ibdw_evowall_bx_sounds_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Sounds")))
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_sounds_spy_added') $stampa=_t("_ibdw_evowall_sounds_add");
  elseif ($row['lang_key']=='_bx_sounds_spy_comment_posted') $stampa=_t("_ibdw_evowall_sounds_comment");
  elseif ($row['lang_key']=='_bx_sounds_spy_rated') $stampa=_t("_ibdw_evowall_sounds_rate");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_sounds_add_condivisione') $stampa=_t("_ibdw_evowall_bx_sounds_add_condivisione");
  $idswiah=$row['id'];
  $soundtitle=strmaxtextlen($unserialize['entry_caption']);
  $trovaslash=substr_count($unserialize['entry_url'],"/");
  $verificauri=explode ("/",$unserialize['entry_url']);
  $verificauri=$verificauri[$trovaslash];
  $querysound="SELECT * FROM RayMp3Files WHERE Uri='$verificauri'";  
  $resultquerysound=mysql_query($querysound);
  $rowquerynum= mysql_num_rows($resultquerysound);
  if ($rowquerynum==0) $tmpx++;
  else
  {
   $rowquerysound=mysql_fetch_row($resultquerysound);
   ?>
   <script>function extext_<?php echo $row['id'];?>()
   {
    texttorender='<?php echo process_db_input(strip_tags($rowquerysound[5]));?>';
    $('#desc<?php echo $row['id'];?>').text(texttorender);
   }
   </script>
   <?php
   $descrizionesound=$funclass->tagliaz($funclass->cleartesto($rowquerysound[5]),300,$row['id']);
   $querypriva="SELECT AllowAlbumView,Owner FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE sys_albums_objects.id_object=".$rowquerysound['0']." AND sys_albums.Type='bx_sounds'";
   $resultpriva=mysql_query($querypriva);
   
   $itsexistes=mysql_num_rows($resultpriva);
   if ($itsexistes>0)
   {
    $rowpriva=mysql_fetch_row($resultpriva);
    $okvista=$funclass->privstate($rowpriva[0],'sounds',$rowpriva[1],$accountid,isFaved($rowpriva[1],$accountid),'view');
   }
   else $okvista=0; 
   if ($okvista==1) 
   {
    echo $parteintroduttiva;
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0))
    {
     echo $sharebutts;
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('socialbuttons.php');
      $parametri_photo['recipient_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['recipient_p_nick']=$aInfomembers['NickName'];
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=getNickname($row['sender_id']);
      $parametri_photo['entry_url']=$unserialize['entry_url'];
      $parametri_photo['entry_caption']=$unserialize['entry_caption'];
 	    $parametri_photo['id_action']=$row['id'];
      $parametri_photo['url_page']=$crpageaddress;
      $params_condivisione=serialize($parametri_photo);
      $bt_share_params['1']=$accountid; //Sender
      $bt_share_params['2']=$row['sender_id']; //Recipient 
      $bt_share_params['3']='_ibdw_evowall_bx_sounds_add_condivisione'; //Lang_Key_share 
      $bt_share_params['4']=readyshare($params_condivisione); //Params
      include('bt_share.php');
      /**End**/
     }       
     echo '</div>'; //bt_list div close
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
    }
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
	  $stampa=str_replace('{recipient_p_nick}',getNickname($row['recipient_id']),$stampa);
	  $stampa=str_replace('{recipient_p_link}',getUsername($row['recipient_id']),$stampa);
    $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
    $stampa=str_replace('{entry_caption}',$soundtitle,$stampa);
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div>';
    echo '</div><div id="bloccoav"><div id="videopreview"><div class="audio_play'.$assegnazione.'" id="player_audio" onclick="fadeAudioElement('.$assegnazione.');$(\'#azione'.$row['id'].' #videopreview\').css(\'float\',\'none\')"><i class="sys-icon volume-up" alt="'._t('_bx_sounds_view').'"></i></div><div class="object_player'.$assegnazione.'" id="object_audio">';
    if ($playerused==0) echo '<object width="100%" height="230" type="application/x-shockwave-flash" id="ray_flash_mp3_player_object" name="ray_flash_mp3_player_embed" style="display:block;" data="'.BX_DOL_URL_ROOT.'flash/modules/global/app/holder_as3.swf"><param name="allowScriptAccess" value="always"><param name="allowFullScreen" value="true"><param name="base" value="flash/modules/mp3/"><param name="bgcolor" value="#FFFFFF"><param name="wmode" value="opaque"><param name="flashvars" value="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=mp3&amp;app=player&amp;id='.$rowquerysound[0].'&amp;user=&amp;password="></object>';
    elseif ($playerused==1)
 	  {
	   $percorsofile=BX_DIRECTORY_PATH_ROOT."/flash/modules/mp3/files/";
	   if (file_exists($percorsofile . $rowquerysound[0] . '.ogg'))
	   {
	    //USE HTML5 PLAYER
	    $gettokensound="SELECT Token FROM RayMp3Tokens WHERE ID=".$rowquerysound[0]." LIMIT 0,1";
	    $gettokenfromid=mysql_query($gettokensound);
	    $resulttok=mysql_fetch_assoc($gettokenfromid);
	    if ($resulttok['Token']==NULL) 
	    {
	     $iCurrentTime=time();
       $sToken=md5($iCurrentTime);
	     $creaquery="INSERT INTO RayMp3Tokens (ID,Token,Date) VALUES('".$rowquerysound[0]."','".$sToken."','".$iCurrentTime."')";
	     $creatoken=mysql_query($creaquery);
	     $gettokensound="SELECT Token FROM RayMp3Tokens WHERE ID=".$rowquerysound[0]." LIMIT 0,1";
	     $gettokenfromid=mysql_query($gettokensound);
	     $resulttok=mysql_fetch_assoc($gettokenfromid);
	    }
	    echo '<audio id="bx-media'.$assegnazione.'" style="width:100%;height:38px;" autobuffer="" preload="auto" controls="" tabindex="0">
            <source src="'.BX_DOL_URL_ROOT.'flash/modules/mp3/get_file.php?id='.$rowquerysound[0].'&amp;token='.$resulttok['Token'].'" type="audio/mpeg; codecs=&quot;mp3&quot;"></source>
            <source src="'.BX_DOL_URL_ROOT.'flash/modules/mp3/get_file.php?id='.$rowquerysound[0].'&amp;token='.$resulttok['Token'].'&amp;ext=ogg" type="audio/ogg; codecs=&quot;vorbis&quot;"></source>
            <script language="javascript" type="text/javascript">
	           function reload() {location.href=\'/modules/index.php?r=sounds/view/'.addslashes($soundtitle).'&module=mp3&app=player\';}
			      </script>
			      <div id="mp3_player">
			       <object height="350" width="100%" type="application/x-shockwave-flash" id="ray_flash_mp3_player_object" name="ray_flash_mp3_player_embed" style="display:block;" data="'.BX_DOL_URL_ROOT.'flash/modules/global/app/holder_as3.swf">
			        <param name="allowScriptAccess" value="always"><param name="allowFullScreen" value="true"><param name="base" value="'.BX_DOL_URL_ROOT.'flash/modules/mp3/"><param name="bgcolor" value="#FFFFFF"><param name="wmode" value="opaque"><param name="flashvars" value="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=mp3&amp;app=player&amp;id='.$rowquerysound[0].'&amp;user=&amp;password=">
			       </object>
			      </div>
			      <script language="javascript" type="text/javascript">
			       var flashvars={url:"'.BX_DOL_URL_ROOT.'flash/XML.php",module:"mp3",app:"player",id:"3",user:"",password:""};
			       var params={allowScriptAccess:"always",allowFullScreen:"true",base:"'.BX_DOL_URL_ROOT.'flash/modules/mp3/",bgcolor:"#FFFFFF",wmode:"opaque"};
			       var attributes = {id: "ray_flash_mp3_player_object",name: "ray_flash_mp3_player_embed",style: "display:block;"};
			       swfobject.embedSWF("'.BX_DOL_URL_ROOT.'flash/modules/global/app/holder_as3.swf", "mp3_player_1359487231", "100%", "350", "9.0.0", "'.BX_DOL_URL_ROOT.'flash/modules/global/app/expressInstall.swf", flashvars, params, attributes);
			      </script>
           </audio>';
	   }
	   else
	   {
	    //USE BOONEX FLASH PLAYER
	    echo '<object width="260" height="230" type="application/x-shockwave-flash" id="ray_flash_mp3_player_object" name="ray_flash_mp3_player_embed" style="display:block;" data="'.BX_DOL_URL_ROOT.'flash/modules/global/app/holder_as3.swf"><param name="allowScriptAccess" value="always"><param name="allowFullScreen" value="true"><param name="base" value="flash/modules/mp3/"><param name="bgcolor" value="#FFFFFF"><param name="wmode" value="opaque"><param name="flashvars" value="url='.BX_DOL_URL_ROOT.'flash/XML.php&amp;module=mp3&amp;app=player&amp;id='.$rowquerysound[0].'&amp;user=&amp;password="></object>';
	   }
	  }
 	  echo '</div></div>';
    echo '<div id="descrizione"><a href="'.$unserialize['entry_url'].'"><h3>'.$soundtitle.'</h3></a><p id="desc'.$row['id'].'">'.$descrizionesound.'</p></div>';
    echo '</div>';
    include ($commentsarea);
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
   else $tmpx++;
  }
 } 
 else $tmpx++;
}
//END

//MARFEY PLACES
elseif ($row['lang_key']=='_Places spy add' OR $row['lang_key']=='_Places spy change' OR $row['lang_key']=='_Places spy add_photo' OR $row['lang_key']=='_Places spy add_video' OR $row['lang_key']=='_Places spy comment' OR $row['lang_key']=='_Places spy rate' OR $row['lang_key']=='_Places spy add_kml' OR $row['lang_key']=='_ibdw_evowall_bx_kplace_add_condivisione')
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $placetitle=strmaxtextlen($unserialize['entry_title']);
  $trovaslash=substr_count($unserialize['entry_url'],"/");
  $verificauri=explode ("/",$unserialize['entry_url']);
  $verificauri=$verificauri[$trovaslash];
  $queryplace="SELECT * FROM places_places WHERE pl_uri='$verificauri'";
  $resultqueryplace=mysql_query($queryplace);
  $rowqueryplace=mysql_fetch_assoc($resultqueryplace);
  $fotoplace=$rowqueryplace['pl_thumb'];
  ?>
  <script>function extext_<?php echo $row['id'];?>()
  {
   texttorender='<?php echo process_db_input(strip_tags($rowqueryplace['pl_desc']));?>';
   $('#desc<?php echo $row['id'];?>').text(texttorender);
  }
  </script>
  <?php
  $descrizioneplace=$rowqueryplace['pl_desc'];
  $descrizioneplace=$funclass->tagliaz($funclass->cleartesto($descrizioneplace),300,$row['id']);
  $stampa=_t($row['lang_key']);
  echo $parteintroduttiva;
  echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
  if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0))
  {
   echo $sharebutts;
   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php'; 
   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
   {
    /**Share System**/
    include('socialbuttons.php');
    $parametri_photo['recipient_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
    $parametri_photo['recipient_p_nick']=$aInfomembers['NickName'];
    $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
    $parametri_photo['profile_nick']=getNickname($row['sender_id']);
    $parametri_photo['entry_url']=$unserialize['entry_url'];
    $parametri_photo['entry_title']=$unserialize['entry_title'];
	  $parametri_photo['id_action']=$row['id'];
	  $parametri_photo['url_page']=$crpageaddress;
    $params_condivisione=serialize($parametri_photo);
    $bt_share_params['1']=$accountid; //Sender
    $bt_share_params['2']=$row['sender_id']; //Recipient 
    $bt_share_params['3']='_ibdw_evowall_bx_kplace_add_condivisione'; //Lang_Key_share 
    $bt_share_params['4']=readyshare($params_condivisione); //Params
    include('bt_share.php');
    /**End**/
   }       
   echo '</div>'; //bt_list div close
   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
  }
  $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
  $stampa=str_replace('{entry_title}',$unserialize['entry_title'],$stampa);
  $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
  $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
  echo $stampa;  
  echo '<div id="data">'.$miadata;
  include('like.php');
  echo '</div>';
  echo '</div><div id="bloccoav">';
   if ($fotoplace!=FALSE) echo '<div id="anteprima"><a href="'.$unserialize['entry_url'].'"><img src="modules/kolimarfey/places/application/photos/big/'.$fotoplace.'.jpg" class="dimfoto"></a></div>';
   echo '<div id="descrizione"><a href="'.$unserialize['entry_url'].'"><h3>'.$placetitle.'</h3></a> <p id="desc'.$row['id'].'">'.$descrizioneplace.'</p></div></div>'; 
  
  echo '</div>';
  include ($commentsarea);
  if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
 }
 else $tmpx++;
}	
//END

//RAYZ LIVE VIDEO
elseif ($row['lang_key']=='_rz_live_spy_post' OR $row['lang_key']=='_rz_live_spy_post_change' OR $row['lang_key']=='_rz_live_spy_join' OR $row['lang_key']=='_rz_live_spy_rate' OR $row['lang_key']=='_rz_live_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_rzlive_add_condivisione')
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $rlvtitle=strmaxtextlen($unserialize['entry_title']);
  $trovaslash=substr_count($unserialize['entry_url'],"/");
  $verificauri=explode ("/",$unserialize['entry_url']);
  $verificauri=$verificauri[$trovaslash];
  $queryrlv="SELECT * FROM rz_live_main WHERE uri='$verificauri'";
  $resultqueryrlv=mysql_query($queryrlv);
  $rowqueryrlv=mysql_fetch_assoc($resultqueryrlv);
  $fotorlv=$rowqueryrlv['thumb'];
  ?>
  <script>function extext_<?php echo $row['id'];?>()
  {
   texttorender='<?php echo process_db_input(strip_tags($rowqueryrlv['desc']));?>';
   $('#desc<?php echo $row['id'];?>').text(texttorender);
  }
  </script>
  <?php
  $descrizionerlv=$rowqueryrlv['desc'];
  $descrizionerlv=$funclass->tagliaz($funclass->cleartesto($descrizionerlv),300,$row['id']);
  
  $okvista=$funclass->privstate($rowqueryrlv['allow_show_view_to'],'rz_live',$rowqueryrlv['author_id'],$accountid,isFaved($rowqueryrlv['author_id'],$accountid),'live show start');
  if($rowqueryrlv['title']==FALSE) {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt); $tmpx++;}
  elseif($rowqueryrlv['status']=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   $queryfotogruppo="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowqueryrlv['thumb'];
   $resultfotogruppo=mysql_query($queryfotogruppo);
   $rowfotogruppo=mysql_fetch_assoc($resultfotogruppo);
   
   $stampa=_t($row['lang_key']);
   echo $parteintroduttiva;
   echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0))
   {
    echo $sharebutts;
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php'; 
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
    {
     /**Share System**/
     include('socialbuttons.php');
     $parametri_photo['recipient_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
     $parametri_photo['recipient_p_nick']=$aInfomembers['NickName'];
     $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
     $parametri_photo['profile_nick']=getNickname($row['sender_id']);
     $parametri_photo['entry_url']=$unserialize['entry_url'];
     $parametri_photo['entry_title']=$unserialize['entry_title'];
	   $parametri_photo['id_action']=$row['id'];
	   $parametri_photo['url_page']=$crpageaddress;
     $params_condivisione=serialize($parametri_photo);
     $bt_share_params['1']=$accountid; //Sender
     $bt_share_params['2']=$row['sender_id']; //Recipient 
     $bt_share_params['3']='_ibdw_evowall_bx_rzlive_add_condivisione'; //Lang_Key_share 
     $bt_share_params['4']=readyshare($params_condivisione); //Params
     include('bt_share.php');
     /**End**/
    }       
    echo '</div>'; //bt_list div close
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
   }
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
   $stampa=str_replace('{entry_title}',$unserialize['entry_title'],$stampa);
   $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
   $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
   echo $stampa;  
   echo '<div id="data">'.$miadata;
   include('like.php');
   echo '</div>';
   echo '</div><div id="bloccoav">';
    if ($fotorlv!=FALSE) echo '<div id="anteprima"><a href="'.$unserialize['entry_url'].'"><img src="'.BX_DOL_URL_ROOT.'m/photos/get_image/browse/'.$rowfotogruppo['Hash'].'.jpg" class="dimfoto"></a></div>';
    echo '<div id="descrizione"><a href="'.$unserialize['entry_url'].'"><h3>'.$rlvtitle.'</h3></a><p id="desc'.$row['id'].'">'.$descrizionerlv.'</p></div></div>'; 
   echo '</div>';
   include ($commentsarea);
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
  }
  else $tmpx++;
 }
 else $tmpx++;
}	
//END

// MODZZZ PROPERTY REAL ESTATE
elseif ($row['lang_key']=='_modzzz_property_spy_post' OR $row['lang_key']=='_modzzz_property_spy_post_change' OR $row['lang_key']=='_modzzz_property_spy_join' OR $row['lang_key']=='_modzzz_property_spy_rate' OR $row['lang_key']=='_modzzz_property_spy_comment' OR $row['lang_key']=='_ibdw_evowall_modzzz_property_share') 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t($row['lang_key']);
  $idswiah=$row['id'];
  $propertytitle=strmaxtextlen($unserialize['entry_title']);
  $trovaslash=substr_count($unserialize['entry_url'],"/");
  $verificauri=explode("/",$unserialize['entry_url']);
  $verificauri=$verificauri[$trovaslash];
  $queryproperty="SELECT title,uri,modzzz_property_main.desc,price,allow_view_property_to,thumb,id,status,author_id FROM modzzz_property_main WHERE uri='$verificauri'";
  $resultqueryproperty= mysql_query($queryproperty);
  $nmproperties=mysql_num_rows($resultqueryproperty);
  if ($nmproperties>0)
  { 
   $rowqueryproperty=mysql_fetch_assoc($resultqueryproperty);
   $getstatus=$rowqueryproperty['status'];
   if ($getstatus='approved')
   {
    $okvista=$funclass->privstate($rowqueryproperty['allow_view_property_to'],'property',$rowqueryproperty['author_id'],$accountid,isFaved($rowqueryproperty['author_id'],$accountid),'view');
    if ($okvista==1) 
    {
     ?>
     <script>function extext_<?php echo $row['id'];?>()
     {
      texttorender='<?php echo process_db_input(strip_tags($rowqueryproperty['desc']));?>';
      $('#desc<?php echo $row['id'];?>').text(texttorender);
     }
     </script>
     <?php
     $descrizioneproperty=$rowqueryproperty['desc'];
     $descrizioneproperty=$funclass->tagliaz($funclass->cleartesto($descrizioneproperty),300,$row['id']);
     //get pics of the property
     $getallpic="SELECT media_id FROM modzzz_property_images WHERE entry_id=".$rowqueryproperty['id'];
     $resultallpic=mysql_query($getallpic);
     $numberpics=mysql_num_rows($resultallpic);
     $iesima=0;
     while($extract=mysql_fetch_array($resultallpic))
     {
      $queryfotoproperty="SELECT Hash,Ext FROM bx_photos_main WHERE ID=".$extract['media_id'];
      $resultfotoproperty=mysql_query($queryfotoproperty);
      $rowfotoproperty=mysql_fetch_row($resultfotoproperty);
      //get name array of pics name (hash and extension)
      $fotoarray[$iesima]=$rowfotoproperty[0].".".$rowfotoproperty[1];
      $iesima++;
     }
     echo $parteintroduttiva;
     echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
     if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
     {
      echo $sharebutts;
      if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
      if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
      {
       /**Share System**/
       include('socialbuttons.php');
       $parametri_photo['recipient_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
       $parametri_photo['recipient_p_nick']=$aInfomembers['NickName'];
       $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
       $parametri_photo['profile_nick']=getNickname($row['sender_id']);
       $parametri_photo['entry_url']=$unserialize['entry_url'];
       $parametri_photo['entry_title']=$unserialize['entry_title'];
	     $parametri_photo['id_action']=$row['id'];
	     $parametri_photo['url_page']=$crpageaddress;
       $params_condivisione=serialize($parametri_photo);
       $bt_share_params['1']=$accountid; //Sender
       $bt_share_params['2']=$row['sender_id']; //Recipient 
       $bt_share_params['3']='_ibdw_evowall_modzzz_property_share'; //Lang_Key_share 
       $bt_share_params['4']=readyshare($params_condivisione); //Params
       include('bt_share.php');
       /**End**/
      }       
      echo '</div>';
      if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
      if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
     }
     $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
     $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
     $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
     $stampa=str_replace('{entry_title}',$propertytitle,$stampa);
     echo $stampa;
     echo '<div id="data">'.$miadata;
     include('like.php');
     echo '</div>';
     echo '</div>';	
     echo '</div><div id="bloccoav">';
     if ($numberpics>0) 
     {
  	  if ($numberpics==1) echo '<div id="videopreview"><a href="'.$unserialize['entry_url'].'"><img src="m/photos/get_image/browse/'.$fotoarray[0].'" class="webimage"></a></div>';
      else
	    {
	     //set width based on pics number
	     $widhtsingle=(200/$numberpics)-2;
	     echo '<div id="videopreview"><div id="contpics">';
	     for ($nump=0;$nump<$numberpics;$nump++) echo '<a href="'.$unserialize['entry_url'].'"><img class="miniproperty" width="'.$widhtsingle.'%" src="m/photos/get_image/browse/'.$fotoarray[$nump].'"></a> ';
 	     echo '</div></div>';
	    }
     }
     echo '<div id="descrizione"><a href="'.$unserialize['entry_url'].'"><h3>'.$propertytitle.'</h3></a><p id="desc'.$row['id'].'">'.$descrizioneproperty.'</p><p class="currency">'._t("_modzzz_property_price").": $ ".number_format($rowqueryproperty['price']).'</p></div>';
     echo '</div>';
     include ($commentsarea);
     if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
     echo '</div>';
     if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
    }
    else $tmpx++;
   }
   else $tmpx++;
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END

// MODZZZ FAMILY
elseif ($row['lang_key']=='_modzzz_family_spy_post')
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t($row['lang_key']);
  echo $parteintroduttiva;
  echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
  if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
  {
   echo $sharebutts;
   include 'rem_ove.php';   
   echo '</div>';
   include 'bt_delete.php'; 
  }
  echo '<i class="sys-icon group"></i>';
  $stampa=str_replace('{family_link}',$unserialize['family_link'],$stampa);
  $stampa=str_replace('{family_nick}',$unserialize['family_nick'],$stampa);
  $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
  $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
  echo $stampa;
  echo '<div id="data">'.$miadata;
  include('like.php');
  echo '</div>'; 
  echo'</div>';
  echo '</div>';
  include ($commentsarea);
  if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
 }
 else $tmpx++;
}	
//END


// MODZZZ RELATION
elseif ($row['lang_key']=='_modzzz_relation_spy_post' OR $row['lang_key']=='_modzzz_relation_spy_post_remove')
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t($row['lang_key']);
  echo $parteintroduttiva;
  echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
  if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
  {
   echo $sharebutts;
   include 'rem_ove.php';   
   echo '</div>';
   include 'bt_delete.php'; 
  }
  if ($row['lang_key']=='_modzzz_relation_spy_post') echo '<i class="sys-icon heart"></i>';
  elseif ($row['lang_key']=='_modzzz_relation_spy_post_remove') echo '<i class="sys-icon heart-empty"></i>';
  $stampa=str_replace('{relation_link}',$unserialize['relation_link'],$stampa);
  $stampa=str_replace('{relation_nick}',$unserialize['relation_nick'],$stampa);
  $stampa=str_replace('{profile_link}',$unserialize['profile_link'],$stampa);
  $stampa=str_replace('{profile_nick}',$unserialize['profile_nick'],$stampa);
  echo $stampa;
  echo '<div id="data">'.$miadata;
  include('like.php');
  echo '</div>';  
  echo'</div>';
  echo '</div>';
  include ($commentsarea);
  if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
 }
 else $tmpx++;
}	
//END


// ANDREWP CARS
elseif (($row['lang_key']=='_aca_spy_create_re_post' OR $row['lang_key']=='_aca_spy_edit_re_post' OR $row['lang_key']=='_ibdw_evowall_bx_aca_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//andrewp cars inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t($row['lang_key']);  
  $trovaslash=substr_count($unserialize['obj_url'],"/")-1;
  $verificauri=explode ("/",$unserialize['obj_url']);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT id,title,location,zip,city,state,uri,price,description,status,country,owner,listings_type FROM acars_units WHERE uri='$verificauri'"; 
  $resultgruppo=mysql_query($querygruppo);
  $getifexist1=mysql_num_rows($resultgruppo);
  if($getifexist1>0)
  {
   $rowgruppo=mysql_fetch_array($resultgruppo);
   if($rowgruppo['status']=='pending') $tmpx++;
   else 
   {
    $idswiax=$row['id'];
    echo $parteintroduttiva;
    ?>
    <script>function extext_<?php echo $row['id'];?>()
    {
     texttorender='<?php echo process_db_input(strip_tags($rowgruppo['description']));?>';
     $('#desc<?php echo $row['id'];?>').text(texttorender);
    }
    </script>
  <?php
    $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo['description']),300,$row['id']);
    $postowner=$rowgruppo['owner'];
    $stringtodisplay=_t('_ibdw_evowall_bx_aca_postedby');
    $stringtodisplay=str_replace('{profile_nick}',getNickname($postowner),$stringtodisplay);
    $stringtodisplay=str_replace('{profile_link}',getUsername($postowner),$stringtodisplay);
    if($rowgruppo['listings_type']==1) $listingtype=_t('_aca_new_cars');
    elseif($rowgruppo['listings_type']==2) $listingtype=_t('_aca_used_cars');
    $descrizionet=$descrizionet.'<div id="adsdatacont">'.$stringtodisplay.'<br>'._t("_aca_price").': '._t("_ibdw_evowall_currency_price").number_format($rowgruppo['price'],2,",",".").'<br>'._t('_aca_location').': '.$rowgruppo['location'].' - '.$rowgruppo['city'].' ('.$rowgruppo['country'].')'.'<br>'._t('_aca_listings_type').': '.$listingtype.'</div>';
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
    {
     $queryfotogruppo="SELECT filename FROM acars_media WHERE unit_id=".$rowgruppo['id'];
     $resultfotogruppo=mysql_query($queryfotogruppo);
     $getifexistsimage=mysql_num_rows($resultfotogruppo);
     if ($getifexistsimage>0) $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
     echo $sharebutts;
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('socialbuttons.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=getNickname($row['sender_id']);
      $parametri_photo['obj_url']=$unserialize['obj_url'];
      $parametri_photo['site_caption']=$unserialize['site_caption'];
	    $parametri_photo['id_action']=$row['id'];
	    $parametri_photo['url_page']=$crpageaddress;
      $params_condivisione=serialize($parametri_photo);
      $bt_share_params['1']=$accountid; //Sender
      $bt_share_params['2']=$row['sender_id']; //Recipient 
      $bt_share_params['3']='_ibdw_evowall_bx_aca_add_condivisione'; //Lang_Key_share 
      $bt_share_params['4']=readyshare($params_condivisione); //Params
      include('bt_share.php');
      /**End**/
     }
     echo '</div>'; //bt_list div close
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
    }
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{site_url}',$unserialize['obj_url'],$stampa);
    $stampa=str_replace('{site_caption}',$unserialize['site_caption'],$stampa);
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';  
    echo '</div><div id="bloccoav"><div id="anteprima"><a href="'.$unserialize['obj_url'].'"><img width="100%" src="'.BX_DOL_URL_ROOT.'modules/andrew/cars/data/files/'.$rowfotogruppo[0].'"></a></div><div id="descrizione"><a href="'.$unserialize['obj_url'].'"><h3>'.$unserialize['site_caption'].'</h3></a><p id="desc'.$row['id'].'">'.$descrizionet.'</p></div></div>';
    echo '</div>';
    include ($commentsarea); 
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
  }
  else {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt);$tmpx++;}
 }
 else $tmpx++;
}		 
//END

// ANDREWP BUSINESS LISTING
elseif (($row['lang_key']=='_abl_spy_create_bl_post' OR $row['lang_key']=='_abl_spy_edit_bl_post' OR $row['lang_key']=='_ibdw_evowall_bx_abl_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//andrewp business listing inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t($row['lang_key']);  
  $trovaslash=substr_count($unserialize['obj_url'],"/")-1;
  $verificauri=explode ("/",$unserialize['obj_url']);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT * FROM abusiness_listing_units WHERE uri='$verificauri'";
  $resultgruppo=mysql_query($querygruppo);
  $getifexist1=mysql_num_rows($resultgruppo);
  if($getifexist1>0)
  {
   $rowgruppo=mysql_fetch_array($resultgruppo);
   if($rowgruppo['status']=='pending') $tmpx++;
   else 
   {
    $idswiax=$row['id'];
    echo $parteintroduttiva;
    ?>
    <script>function extext_<?php echo $row['id'];?>()
    {
     texttorender='<?php echo process_db_input(strip_tags($rowgruppo['description']));?>';
     $('#desc<?php echo $row['id'];?>').text(texttorender);
    }
    </script>
    <?php
    $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo['description']),300,$row['id']);
    $postowner=$rowgruppo['owner'];
    $stringtodisplay=_t('_ibdw_evowall_bx_abl_postedby');
    $stringtodisplay=str_replace('{profile_nick}',getNickname($postowner),$stringtodisplay);
    $stringtodisplay=str_replace('{profile_link}',getUsername($postowner),$stringtodisplay);
    $descrizionet=$descrizionet.'<div id="adsdatacont">'.$stringtodisplay.'<br>'._t('_abl_location').': '.$rowgruppo['street'].' - '.$rowgruppo['city'].' ('.$rowgruppo['state'].' - '.$rowgruppo['country'].')</div>';
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
    {
     $queryfotogruppo="SELECT filename FROM abusiness_listing_media WHERE unit_id=".$rowgruppo['id'];
     $resultfotogruppo=mysql_query($queryfotogruppo);
     $getifexistsimage=mysql_num_rows($resultfotogruppo);
     if ($getifexistsimage>0) $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
     echo $sharebutts;
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('socialbuttons.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=getNickname($row['sender_id']);
      $parametri_photo['obj_url']=$unserialize['obj_url'];
      $parametri_photo['site_caption']=$unserialize['site_caption'];
	    $parametri_photo['id_action']=$row['id'];
	    $parametri_photo['url_page']=$crpageaddress;
      $params_condivisione=serialize($parametri_photo);
      $bt_share_params['1']=$accountid; //Sender
      $bt_share_params['2']=$row['sender_id']; //Recipient 
      $bt_share_params['3']='_ibdw_evowall_bx_abl_add_condivisione'; //Lang_Key_share 
      $bt_share_params['4']=readyshare($params_condivisione); //Params
      include('bt_share.php');
      /**End**/
     }
     echo '</div>'; //bt_list div close
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
    }
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{site_url}',$unserialize['obj_url'],$stampa);
    $stampa=str_replace('{site_caption}',$unserialize['site_caption'],$stampa);
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div><div id="bloccoav"><div id="anteprima"><a href="'.$unserialize['obj_url'].'"><img width="100%" src="'.BX_DOL_URL_ROOT.'modules/andrew/business_listing/data/files/i_'.$rowfotogruppo[0].'"></a></div><div id="descrizione"><a href="'.$unserialize['obj_url'].'"><h3>'.$unserialize['site_caption'].'</h3></a><p id="desc'.$row['id'].'">'.$descrizionet.'</p></div></div>';
    echo '</div>';
    include ($commentsarea); 
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
  }
  else {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt);$tmpx++;}
 }
 else $tmpx++;
}		 
//END

// ANDREWP REAL ESTATE
elseif (($row['lang_key']=='_are_spy_create_re_post' OR $row['lang_key']=='_are_spy_edit_re_post' OR $row['lang_key']=='_ibdw_evowall_bx_areal_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//andrewp business listing inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t($row['lang_key']);  
  $trovaslash=substr_count($unserialize['obj_url'],"/")-1;
  $verificauri=explode ("/",$unserialize['obj_url']);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT * FROM arealestate_units WHERE uri='$verificauri'";
  $resultgruppo=mysql_query($querygruppo);
  $getifexist1=mysql_num_rows($resultgruppo);
  if($getifexist1>0)
  {
   $rowgruppo=mysql_fetch_array($resultgruppo);
   if($rowgruppo['status']=='pending') $tmpx++;
   else 
   {
    $idswiax=$row['id'];
    echo $parteintroduttiva;
    ?>
    <script>function extext_<?php echo $row['id'];?>()
    {
     texttorender='<?php echo process_db_input(strip_tags($rowgruppo['description']));?>';
     $('#desc<?php echo $row['id'];?>').text(texttorender);
    }
    </script>
    <?php
    $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo['description']),300,$row['id']);
    $postowner=$rowgruppo['owner'];
    $stringtodisplay=_t('_ibdw_evowall_bx_areal_postedby');
    $stringtodisplay=str_replace('{profile_nick}',getNickname($postowner),$stringtodisplay);
    $stringtodisplay=str_replace('{profile_link}',getUsername($postowner),$stringtodisplay);
    $descrizionet=$descrizionet.'<div id="adsdatacont">'.$stringtodisplay.'<br>'._t('_are_location').': '.$rowgruppo['location'].' - '.$rowgruppo['city'].' ('.$rowgruppo['country'].')</div>';
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
    {
     $queryfotogruppo="SELECT filename FROM arealestate_media WHERE unit_id=".$rowgruppo['id'];
     $resultfotogruppo=mysql_query($queryfotogruppo);
     $getifexistsimage=mysql_num_rows($resultfotogruppo);
     if ($getifexistsimage>0) $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
     echo $sharebutts;
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('socialbuttons.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=getNickname($row['sender_id']);
      $parametri_photo['obj_url']=$unserialize['obj_url'];
      $parametri_photo['site_caption']=$unserialize['site_caption'];
	    $parametri_photo['id_action']=$row['id'];
	    $parametri_photo['url_page']=$crpageaddress;
      $params_condivisione=serialize($parametri_photo);
      $bt_share_params['1']=$accountid; //Sender
      $bt_share_params['2']=$row['sender_id']; //Recipient 
      $bt_share_params['3']='_ibdw_evowall_bx_areal_add_condivisione'; //Lang_Key_share 
      $bt_share_params['4']=readyshare($params_condivisione); //Params
      include('bt_share.php');
      /**End**/
     }
     echo '</div>'; //bt_list div close
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
    }
    $nomegruppo=strmaxtextlen($nomegruppo);
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{site_url}',$unserialize['obj_url'],$stampa);
    $stampa=str_replace('{site_caption}',$unserialize['site_caption'],$stampa); 
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div>';
    echo '</div><div id="bloccoav"><div id="anteprima"><a href="'.$unserialize['obj_url'].'"><img width="100%" src="'.BX_DOL_URL_ROOT.'modules/andrew/realestate/data/files/i_'.$rowfotogruppo[0].'"></a></div><div id="descrizione"><a href="'.$unserialize['obj_url'].'"><h3>'.$unserialize['site_caption'].'</h3></a><p id="desc'.$row['id'].'">'.$descrizionet.'</p></div>';
    echo '</div>';
    include ($commentsarea); 
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
  }
  else {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt);$tmpx++;}
 }
 else $tmpx++;
}		 
//END

// ANDREWP JOB
elseif (($row['lang_key']=='_ajb_wall_add_job_vacancy_spy' OR $row['lang_key']=='_ibdw_evowall_bx_ajb_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
{//andrewp job inherits the settings of boonex groups for memberships allowed and sharing key for EVO Wall
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t($row['lang_key']);  
  $trovaslash=substr_count($unserialize['obj_url'],"/")-1;
  $verificauri=explode ("/",$unserialize['obj_url']);
  $verificauri=$verificauri[$trovaslash];
  $querygruppo="SELECT * FROM ajob_vacancies WHERE uri='$verificauri'"; 
  $resultgruppo=mysql_query($querygruppo);
  $getifexist1=mysql_num_rows($resultgruppo);
  if($getifexist1>0)
  {
   $rowgruppo=mysql_fetch_array($resultgruppo);
   if($rowgruppo['status']=='pending') $tmpx++;
   else 
   {
    $idswiax=$row['id'];
    echo $parteintroduttiva;
    ?>
    <script>function extext_<?php echo $row['id'];?>()
    {
     texttorender='<?php echo process_db_input(strip_tags($rowgruppo['description']));?>';
     $('#desc<?php echo $row['id'];?>').text(texttorender);
    }
    </script>
    <?php
    $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo['description']),300,$row['id']);
    $postowner=$rowgruppo['owner'];
    $stringtodisplay=_t('_ibdw_evowall_bx_ajb_postedby');
    $stringtodisplay=str_replace('{profile_nick}',getNickname($postowner),$stringtodisplay);
    $stringtodisplay=str_replace('{profile_link}',getUsername($postowner),$stringtodisplay);
    $salarytipeis=_t('_ajb_salary_type_'.$rowgruppo['salary_type']);
    if($rowgruppo['currency']==0) $currencyis="$";
    elseif($rowgruppo['currency']==1) $currencyis="€";
    elseif($rowgruppo['currency']==2) $currencyis="roubles"; 
    $descrizionet=$descrizionet.'<div id="adsdatacont">'.$stringtodisplay.'<br>'._t('_ajb_city').': '.$rowgruppo['city'].'<br>'._t('_ajb_salary_f').': '.$currencyis.number_format($rowgruppo['salary_amount_f'],2,",",".").' - '._t('_ajb_salary_t').': '._t("_ibdw_evowall_currency_price").number_format($rowgruppo['salary_amount_t'],2,",",".").' '.$salarytipeis.'</div>';
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
    {
     echo $sharebutts;
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('socialbuttons.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=getNickname($row['sender_id']);
      $parametri_photo['obj_url']=$unserialize['obj_url'];
      $parametri_photo['obj_caption']=$unserialize['obj_caption'];
	    $parametri_photo['id_action']=$row['id'];
	    $parametri_photo['url_page']=$crpageaddress;
      $params_condivisione=serialize($parametri_photo);
      $bt_share_params['1']=$accountid; //Sender
      $bt_share_params['2']=$row['sender_id']; //Recipient 
      $bt_share_params['3']='_ibdw_evowall_bx_ajb_add_condivisione'; //Lang_Key_share 
      $bt_share_params['4']=readyshare($params_condivisione); //Params
      include('bt_share.php');
      /**End**/
     }
     echo '</div>'; //bt_list div close
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
    }
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{obj_caption}',$unserialize['obj_caption'],$stampa);
    $stampa=str_replace('{obj_url}',$unserialize['obj_url'],$stampa);
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div><div id="bloccoav"><div id="descrizione"><a href="'.$unserialize['obj_url'].'"><h3>'.$unserialize['obj_caption'].'</h3></a><p id="desc'.$row['id'].'">'.$descrizionet.'</p></div></div>';
    echo '</div>';
    include ($commentsarea); 
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
  }
  else {$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt);$tmpx++;}
 }
 else $tmpx++;
}		 
//END

// PHOTO
elseif (($row['lang_key']=='_bx_photos_spy_added' OR $row['lang_key']=='_bx_photos_spy_comment_posted' OR $row['lang_key']=='_bx_photos_spy_rated' OR $row['lang_key']=='_ibdw_evowall_bx_photo_add_condivisione' OR $row['lang_key']=='_bx_photo_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Photos"))) 
{               
 if (($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview')) OR $off_parent==1)
 {
  if ($row['lang_key']=='_bx_photos_spy_added') 
  {
   if($verifica_partent==0) $stampa=_t("_ibdw_evowall_photo_add");
   else $stampa=_t("_ibdw_evowall_parent_photo");
  }
  elseif ($row['lang_key']=='_bx_photos_spy_comment_posted') 
  {
   if($verifica_partent==0) $stampa=_t("_ibdw_evowall_comment_nphoto");
   else $stampa=_t("_ibdw_evowall_comment_nphoto_multi");
  }
  elseif ($row['lang_key']=='_bx_photos_spy_rated') 
  {
   if($verifica_partent==0) $stampa=_t("_ibdw_evowall_rate_photo");
   else $stampa=_t("_ibdw_evowall_rate_photo_multi");
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_photo_add_condivisione' OR $row['lang_key']=='_bx_photo_add_condivisione') $stampa=_t("_ibdw_evowall_bx_photo_add_condivisione");
  $nomeimg=strmaxtextlen($unserialize['entry_caption']);
  $urlimg=$unserialize['entry_url']; 
  $trovaslash=substr_count($unserialize['entry_url'],"/");
  $verificauri=explode ("/",$unserialize['entry_url']);
  $verificauri=$verificauri[$trovaslash];
  $queryfoto="SELECT * FROM bx_photos_main WHERE Uri='".$verificauri."'";                                                                   
  $resultfoto=mysql_query($queryfoto);
  $getifexistsp=mysql_num_rows($resultfoto);
  if ($getifexistsp>0)
  {
   $rowfoto=mysql_fetch_row($resultfoto); 
   $indirizzourl_true=BX_DOL_URL_ROOT.'m/photos/view/'.$rowfoto[6];
   //ottengo l'uri dell'album
   $queryhidden="SELECT Uri FROM sys_albums WHERE ID IN (SELECT id_album FROM sys_albums_objects WHERE id_object=".$rowfoto[0].")";
   $resultqueryh=mysql_query($queryhidden);
   $resultnumis=mysql_num_rows($resultqueryh);
   if ($resultnumis>0)
   { 
    $checkvaluealbum=mysql_fetch_row($resultqueryh); 
    //ottengo il nome predefinito per l'album hidden
    $getdefault="SELECT `VALUE` FROM `sys_options` WHERE `Name`='sys_album_default_name'";
    $resultqueryd=mysql_query($getdefault);
    $checkdefault=mysql_fetch_row($resultqueryd);
    //controllo quindi se l'uri è hidden,in tal caso scarto la notizia della foto
    if ($checkvaluealbum[0]==$checkdefault[0] or ($rowfoto[15]=='pending')) $tmpx++;
    else 
    {
     if($rowfoto[4]==FALSE) {$dlt="DELETE FROM `bx_spy_data` WHERE `id`=".$row['id']; $dlt_exe=mysql_query($dlt); $tmpx++;}
     else 
     {
      $idswiak=$row['id'];
      $querypriva="SELECT AllowAlbumView,Owner FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE id_object='$rowfoto[0]' AND TYPE='bx_photos'";
      $resultpriva=mysql_query($querypriva);
      $itsexistes=mysql_num_rows($resultpriva);
      if ($itsexistes>0)
      {
       $rowpriva=mysql_fetch_row($resultpriva);
       $pdxrecuperofoto="SELECT ID,Owner, Size FROM bx_photos_main WHERE Uri='".$verificauri."'";
       $pdxeseguirecuperofoto=mysql_query($pdxrecuperofoto);
       $pdxrowrecuperfoto=mysql_fetch_assoc($pdxeseguirecuperofoto);
       $pdxidfoto=$pdxrowrecuperfoto['ID'];
       $pdxuserid=$pdxrowrecuperfoto['Owner'];
       $pdxsize=explode("x",$pdxrowrecuperfoto['Size']);
       $pdxwidth=$pdxsize[0];
       $pdxhight=$pdxsize[1];
       
       if($attivaintegrazione==1) 
   	   {
        $pdxrecuperoalbum="SELECT id_album FROM sys_albums_objects WHERE id_object='".$pdxidfoto."'";
        $pdxeseguirecuperoalbum=mysql_query($pdxrecuperoalbum);
        $pdxrowrecuperoalbum=mysql_fetch_assoc($pdxeseguirecuperoalbum); 
        $pdxidalbums=$pdxrowrecuperoalbum['id_album'];  
        //controllo campi vuoti
        $selezionedlxvuoti="SELECT id_notizia FROM ibdw_likethis WHERE	id_notizia='".$assegnazione."' AND phdlxid='0'";
        $eseguiselezionedlxvuoti=mysql_query($selezionedlxvuoti);
        $numdlxvuoti=mysql_num_rows($eseguiselezionedlxvuoti);
        if($numdlxvuoti!=0) 
	      {
         $updatelike="UPDATE ibdw_likethis SET phdlxid='".$pdxrowrecuperfoto['ID']."',typelement='photo' WHERE id_notizia='$assegnazione' AND typelement !='phunsign'";
         $eseguiupdatelike=mysql_query($updatelike);
        }  
       }       
       $okvista=$funclass->privstate($rowpriva[0],'photos',$rowpriva[1],$accountid,isFaved($rowpriva[1],$accountid),'');
      }
      else $okvista=0;
      if ($okvista==1)
      {
       echo $parteintroduttiva;
       echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
       if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
	     {
        echo $sharebutts;
        if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
        if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
        {
         /**SHARING BLOCK**/
         include('socialbuttons.php');
         $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
         $parametri_photo['profile_nick']=getNickname($row['sender_id']);
         $parametri_photo['entry_url']=$indirizzourl_true;
         $parametri_photo['entry_caption']=$nomeimg;
	       $parametri_photo['id_action']=$row['id'];
	       $parametri_photo['url_page']=$crpageaddress;
         $params_condivisione=serialize($parametri_photo);   
         $bt_share_params['1']=$accountid; //Sender
         if($row['lang_key']=='_bx_photo_add_condivisione') $bt_share_params['2']=0; //Recipient
         else $bt_share_params['2']=$row['sender_id'];  
         $bt_share_params['3']='_ibdw_evowall_bx_photo_add_condivisione'; //Lang_Key_share
         $bt_share_params['4']=readyshare($params_condivisione); //Params
         include('bt_share.php');
	       /**END SHARING BLOCK**/
	      }
	      echo '</div>'; //bt_list div close
	      if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
	      if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';  
       }
       $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
	     $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
       $stampa=str_replace('{recipient_p_link}',getUsername($pdxuserid),$stampa);
       $stampa=str_replace('{recipient_p_nick}',getNickname($pdxuserid),$stampa);
	     if($attivaintegrazione==0) 
       {
        $stampa=str_replace('{entry_url}',$urlimg,$stampa);
        $urlimg=$indirizzourl_true;
       } 
	     else 
       {
        $indirizzopdx=BX_DOL_URL_ROOT.'page/photoview?iff='.criptcodex($pdxidfoto).'&ia='.criptcodex($pdxidalbums).'&ui='.criptcodex($pdxuserid);
        $stampa=str_replace('{entry_url}',$indirizzopdx,$stampa);
       }
       $stampa=str_replace('{entry_caption}',$nomeimg,$stampa);
       $stampa=str_replace('{number}',$verifica_partent+1,$stampa);
       echo $stampa;
       echo '<div id="data">'.$miadata;
       include('like.php');
       echo '</div>';
       echo '</div>';
        $script_js='
        <script>
        $(document).ready(function()
	      {
         var config={over: fade_evp_pop'.$assegnazione.',out: out_evp_pop'.$assegnazione.',interval:150};
         $("#pop_evophoto'.$assegnazione.'").hoverIntent(config);
         $("#pop_evophoto'.$assegnazione.' img").mouseover(function() {$(this).css("opacity","0.5");});
         $("#pop_evophoto'.$assegnazione.' img").mouseout(function() {$(this).css("opacity","1");});
        });
        function fade_evp_pop'.$assegnazione.'()
	      {
         $(".pop_foto").css("background-image","url(modules/ibdw/evowall/templates/base/images/big-loader.gif)"); 
         var set_value_photozoom=$("#set_value_photozoom'.$assegnazione.'").val();
         if(set_value_photozoom!='.$rowfoto[0].') 
	       {
	        $("#popup'.$assegnazione.'").html("<img src=\"m/photos/get_image/file/'.$rowfoto[16].'.'.$rowfoto[3].'\">");
          $("#popup'.$assegnazione.'").fadeIn(1); 
          $("#popup'.$assegnazione.' img").fadeIn(1000); 
          $("#set_value_photozoom'.$assegnazione.'").val('.$rowfoto[0].');
          $(".pop_foto").css("background-image","none");
         }
        }
        function out_evp_pop'.$assegnazione.'(){$("#pop_evophoto'.$assegnazione.' img").css("opacity","1");} 
        </script>';
        if($verifica_partent>1) $num_foto=$verifica_partent+1; 
        echo '</div><div id="bloccoav">';
        if($verifica_partent>1)
        {
         if($num_foto>$photo_max_preview) $div_foto=$photo_max_preview; 
         else $div_foto=$num_foto; 
        }
        //check dimension of the image and type Avatar. If Avatar or image size is too small apply the small format
        if ($unserialize['entry_caption']=="Avatar" or $pdxwidth<500)
        {
         $imagetodisplay='m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3];
         $imagewidthadj='';
         $antestyle='width:auto;margin-right:6px;';
        }
        else
        {
         $imagetodisplay='m/photos/get_image/file/'.$rowfoto[16].'.'.$rowfoto[3];
         $imagewidthadj='100%';
         $antestyle='';
        }
        echo '<div id="anteprima" style="'.$antestyle.'" class="fadeMini'.$assegnazione.'">';
        ?>
        <script>function extext_<?php echo $row['id'];?>()
        {
         texttorender='<?php echo process_db_input(strip_tags($rowfoto[7]));?>';
         $('#desc<?php echo $row['id'];?>').text(texttorender);
        }</script>
        <?php
        $descrizioneq=$funclass->tagliaz(strip_tags($rowfoto[7]),300,$row['id']); 
        if($attivaintegrazione==0)
        {
         if($verifica_partent==0) echo '<a href="'.$urlimg.'"><img src="'.$imagetodisplay.'" onload="$(this).fadeIn(200);" width="'.$imagewidthadj.'"></a>';
         else 
	       {
          echo $script_js.'<a id="pop_evophoto'.$assegnazione.'" class="marginfix" href="'.$urlimg.'"><img src="m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3].'"  onload="$(this).fadeIn(200);"></a>'; 
          echo estrai_foto_parent($row['id'],$row['lang_key'],$row['sender_id'],$row['recordsfound'],$photo_max_preview,$attivaintegrazione,$row['date']);
         }
         echo '</div>';
         if($verifica_partent==0) echo '<div id="descrizione"><a href="'.$urlimg.'"><h3>'.$rowfoto[5].'</h3></a> <p id="desc'.$row['id'].'">'.$funclass->cleartesto($descrizioneq).'</p></div>'; 
         echo '<div class="clear"></div>';
	      }
	      else 
	      {
	       if($verifica_partent==0) echo '<a href="'.$indirizzopdx.'"><img onload="$(this).fadeIn(200);" src="'.$imagetodisplay.'" width="'.$imagewidthadj.'"></a>';
         else
         {
          echo $script_js.'<a href="'.$indirizzopdx.'" id="pop_evophoto'.$assegnazione.'" class="marginfix"><img  onload="$(this).fadeIn(200);" src="m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3].'"></a>';
          echo estrai_foto_parent($row['id'],$row['lang_key'],$row['sender_id'],$row['recordsfound'],$photo_max_preview,$attivaintegrazione,$row['date']);
         }
         echo'</div>';
         if($verifica_partent==0) echo '<div id="descrizione"><a href="'.$indirizzopdx.'"><h3>'.$rowfoto[5].'</h3></a><p id="desc'.$row['id'].'">'.$funclass->cleartesto($descrizioneq).'</p></div>';
         echo '<div class="clear"></div>';
        }
	      echo '<input type="hidden" id="set_value_photozoom'.$assegnazione.'" value="0"/><div id="popup'.$assegnazione.'" class="pop_foto"></div>';
        echo '</div>';
        include ($commentsarea);
        $pdxidfoto=0; 
        if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
        echo '</div>';
        if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
       }
       elseif ($okvista==0) $tmpx++;  
      }
     }
    }
    else $tmpx++;
   }
   else $tmpx++;
  }
  else $tmpx++;
}     
//END


// BOONEX GROUPS - BOONEX PAGES By ZARCON - MODZZZ CLUBS, PETS, PETITIONS, BANDS, SCHOOLS, CLASSIFIEDS, JOBS, NOTICES, NEWS, LISTINGS
elseif (
(($row['alert_unit']=='bx_groups' OR $row['lang_key']=='_ibdw_evowall_bx_gruppo_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
OR 
(($row['alert_unit']=='bx_pages' OR $row['lang_key']=='_ibdw_evowall_bx_pagina_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Pages")))
OR
(($row['alert_unit']=='modzzz_club' OR $row['lang_key']=='_ibdw_evowall_bx_club_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
OR
(($row['alert_unit']=='modzzz_pets' OR $row['lang_key']=='_ibdw_evowall_bx_pet_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
OR
(($row['alert_unit']=='modzzz_petitions' OR $row['lang_key']=='_ibdw_evowall_bx_petition_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
OR
(($row['alert_unit']=='modzzz_bands' OR $row['lang_key']=='_ibdw_evowall_bx_band_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
OR
(($row['alert_unit']=='modzzz_schools' OR $row['lang_key']=='_ibdw_evowall_bx_school_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
OR
(($row['alert_unit']=='modzzz_classified' OR $row['lang_key']=='_ibdw_evowall_bx_classified_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
OR
(($row['alert_unit']=='modzzz_jobs' OR $row['lang_key']=='_ibdw_evowall_bx_job_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
OR
(($row['alert_unit']=='modzzz_articles' OR $row['lang_key']=='_ibdw_evowall_bx_article_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))

OR
(($row['alert_unit']=='modzzz_formations' OR $row['lang_key']=='_ibdw_evowall_bx_formation_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))



OR
(($row['alert_unit']=='modzzz_investment' OR $row['lang_key']=='_ibdw_evowall_bx_investment_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))



OR
(($row['alert_unit']=='modzzz_notices' OR $row['lang_key']=='_ibdw_evowall_bx_notice_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
OR
(($row['alert_unit']=='modzzz_news' OR $row['lang_key']=='_ibdw_evowall_bx_news_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
OR
(($row['alert_unit']=='modzzz_listing' OR $row['lang_key']=='_ibdw_evowall_bx_listing_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
OR
(($row['alert_unit']=='modzzz_deals' OR $row['lang_key']=='_ibdw_evowall_bx_deal_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
OR
(($row['alert_unit']=='modzzz_provider' OR $row['lang_key']=='_ibdw_evowall_bx_provider_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
OR
(($row['alert_unit']=='modzzz_resume' OR $row['lang_key']=='_ibdw_evowall_bx_resume_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Groups")))
)
{   
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $titletosearch="title";
  if ($row['lang_key']=='_ibdw_evowall_bx_gruppo_add_condivisione')
  {
   $moduletype="groups";
   $alert_unit="bx_groups";
   $singmodname="group";
   $sharelink="_ibdw_evowall_bx_gruppo_add_condivisione";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_pagina_add_condivisione')
  {
   $moduletype="pages";
   $alert_unit="bx_pages";
   $singmodname="page";
   $sharelink="_ibdw_evowall_bx_pagina_add_condivisione";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_club_add_condivisione')
  {
   $moduletype="clubs";
   $alert_unit="modzzz_club";
   $singmodname="club";
   $sharelink="_ibdw_evowall_bx_club_add_condivisione";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_pet_add_condivisione')
  {
   $moduletype="pets";
   $alert_unit="modzzz_pets";
   $singmodname="pet";
   $sharelink="_ibdw_evowall_bx_pet_add_condivisione";
   $titletosearch="petname";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_petition_add_condivisione')
  {
   $moduletype="petitions";
   $alert_unit="modzzz_petitions";
   $singmodname="petition";
   $sharelink="_ibdw_evowall_bx_petition_add_condivisione";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_band_add_condivisione')
  {
   $moduletype="bands";
   $alert_unit="modzzz_bands";
   $singmodname="band";
   $sharelink="_ibdw_evowall_bx_band_add_condivisione";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_school_add_condivisione')
  {
   $moduletype="schools";
   $alert_unit="modzzz_schools";
   $singmodname="school";
   $sharelink="_ibdw_evowall_bx_school_add_condivisione";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_job_add_condivisione')
  {
   $moduletype="jobs";
   $alert_unit="modzzz_jobs";
   $singmodname="job";
   $sharelink="_ibdw_evowall_bx_job_add_condivisione";
  }
  
   elseif ($row['lang_key']=='_ibdw_evowall_bx_article_add_condivisione')
  {
   $moduletype="articles";
   $alert_unit="modzzz_articles";
   $singmodname="article";
   $sharelink="_ibdw_evowall_bx_article_add_condivisione";
  }
  
  
  elseif ($row['lang_key']=='_ibdw_evowall_bx_investment_add_condivisione')
  {
   $moduletype="investments";
   $alert_unit="modzzz_investment";
   $singmodname="investment";
   $sharelink="_ibdw_evowall_bx_investment_add_condivisione";
  }
  
  
  
  elseif ($row['lang_key']=='_ibdw_evowall_bx_formation_add_condivisione')
  {
   $moduletype="formations";
   $alert_unit="modzzz_formations";
   $singmodname="formation";
   $sharelink="_ibdw_evowall_bx_formation_add_condivisione";
  }
  
  
  
  
  
  
  
  elseif ($row['lang_key']=='_ibdw_evowall_bx_notice_add_condivisione')
  {
   $moduletype="notices";
   $alert_unit="modzzz_notices";
   $singmodname="notice";
   $sharelink="_ibdw_evowall_bx_notice_add_condivisione";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_news_add_condivisione')
  {
   $moduletype="news";
   $alert_unit="modzzz_news";
   $singmodname="news";
   $sharelink="_ibdw_evowall_bx_news_add_condivisione";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_classified_add_condivisione')
  {
   $moduletype="classifieds";
   $alert_unit="modzzz_classified";
   $singmodname="classified";
   $sharelink="_ibdw_evowall_bx_classified_add_condivisione";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_listing_add_condivisione')
  {
   $moduletype="listings";
   $alert_unit="modzzz_listing";
   $singmodname="listing";
   $sharelink="_ibdw_evowall_bx_listing_add_condivisione";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_deal_add_condivisione')
  {
   $moduletype="deals";
   $alert_unit="modzzz_deals";
   $singmodname="deal";
   $sharelink="_ibdw_evowall_bx_deal_add_condivisione";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_provider_add_condivisione')
  {
   $moduletype="provider";
   $alert_unit="modzzz_provider";
   $singmodname="provider";
   $sharelink="_ibdw_evowall_bx_provider_add_condivisione";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_bx_resume_add_condivisione')
  {
   $moduletype="resume";
   $alert_unit="modzzz_resume";
   $singmodname="resume";
   $sharelink="_ibdw_evowall_bx_resume_add_condivisione";
  }
  else
  {
   $moduletype=str_replace("bx_","",$row['alert_unit']);
   $alert_unit=$row['alert_unit'];
   $singmodname=substr($moduletype,0,-1);
   if($alert_unit=="bx_groups") $sharelink="_ibdw_evowall_bx_gruppo_add_condivisione";
   elseif($alert_unit=="bx_pages") $sharelink="_ibdw_evowall_bx_pagina_add_condivisione";
   elseif($alert_unit=="modzzz_club") {$sharelink="_ibdw_evowall_bx_club_add_condivisione";$singmodname="club";}
   elseif($alert_unit=="modzzz_petitions") {$sharelink="_ibdw_evowall_bx_petition_add_condivisione";$singmodname="petition";}
   elseif($alert_unit=="modzzz_pets") {$moduletype="pets";$singmodname="pet";$sharelink="_ibdw_evowall_bx_pet_add_condivisione";$titletosearch="petname";}
   elseif($alert_unit=="modzzz_bands") {$sharelink="_ibdw_evowall_bx_band_add_condivisione";$singmodname="band";}
   elseif($alert_unit=="modzzz_schools") {$sharelink="_ibdw_evowall_bx_school_add_condivisione";$singmodname="school";}
   elseif($alert_unit=="modzzz_jobs") {$sharelink="_ibdw_evowall_bx_job_add_condivisione";$singmodname="job";}
   elseif($alert_unit=="modzzz_articles") {$sharelink="_ibdw_evowall_bx_article_add_condivisione";$singmodname="article";}
   
   
   elseif($alert_unit=="modzzz_formations") {$sharelink="_ibdw_evowall_bx_formation_add_condivisione";$singmodname="formation";}
   
   elseif($alert_unit=="modzzz_investment") {$sharelink="_ibdw_evowall_bx_investment_add_condivisione";$singmodname="investment";}
   
   elseif($alert_unit=="modzzz_notices") {$sharelink="_ibdw_evowall_bx_notice_add_condivisione";$singmodname="notice";}
   elseif($alert_unit=="modzzz_news") {$sharelink="_ibdw_evowall_bx_news_add_condivisione";$singmodname="news";}
   elseif($alert_unit=="modzzz_listing") {$sharelink="_ibdw_evowall_bx_listing_add_condivisione";$singmodname="listing";}
   elseif($alert_unit=="modzzz_classified") {$moduletype="classifieds";$sharelink="_ibdw_evowall_bx_classified_add_condivisione";$singmodname="classified";}
   elseif($alert_unit=="modzzz_deals") {$moduletype="deals";$sharelink="_ibdw_evowall_bx_deal_add_condivisione";$singmodname="deal";}
   elseif($alert_unit=="modzzz_provider") {$moduletype="provider";$sharelink="_ibdw_evowall_bx_provider_add_condivisione";$singmodname="provider";}
   elseif($alert_unit=="modzzz_resume") {$moduletype="resume";$sharelink="_ibdw_evowall_bx_resume_add_condivisione";$singmodname="resume";}
  }
  if ($row['lang_key']=='_'.$alert_unit.'_spy_post') $stampa=_t("_ibdw_evowall_".$singmodname."_add");
  elseif ($row['lang_key']=='_'.$alert_unit.'_spy_post_change') $stampa=_t("_ibdw_evowall_".$singmodname."_editaw");
  elseif ($row['lang_key']=='_'.$alert_unit.'_spy_join') $stampa=_t("_ibdw_evowall_".$singmodname."_join");
  elseif ($row['lang_key']=='_'.$alert_unit.'_spy_rate') $stampa=_t("_ibdw_evowall_".$singmodname."_rate");
  elseif ($row['lang_key']=='_'.$alert_unit.'_spy_comment') $stampa=_t("_ibdw_evowall_".$singmodname."_comment");
  else $stampa=_t($row['lang_key']);
  $trovaslash=substr_count($unserialize['entry_url'],"/");
  $verificauri=explode ("/",$unserialize['entry_url']);
  $verificauri=$verificauri[$trovaslash];
  if ($alert_unit=="modzzz_resume") $querygruppo="SELECT ".$titletosearch.",thumb,objective as `desc`,uri,id,status,allow_view_".$singmodname."_to,author_id FROM ".$alert_unit."_main WHERE uri='$verificauri'";
  else $querygruppo="SELECT ".$titletosearch.",thumb,".$alert_unit."_main.desc,uri,id,status,allow_view_".$singmodname."_to,author_id FROM ".$alert_unit."_main WHERE uri='$verificauri'";
  if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$alert_unit."_main'"))==1)
  {
  $resultgruppo=mysql_query($querygruppo);
  $rowgruppo=mysql_fetch_assoc($resultgruppo);
  $allowtocheck="allow_view_".$singmodname."_to";
  $viewtypemod='view_'.$singmodname;
  if($singmodname=="pet") $viewtypemod="view_pets";
  
  if ($rowgruppo[$allowtocheck]=="" or $rowgruppo['author_id']=="" or $viewtypemod=="") $okvista=0;
  else $okvista=$funclass->privstate($rowgruppo[$allowtocheck],$moduletype,$rowgruppo['author_id'],$accountid,isFaved($rowgruppo['author_id'],$accountid),$viewtypemod);
  if($rowgruppo[$titletosearch]==FALSE) {//$dlt="DELETE FROM bx_spy_data WHERE id=".$row['id'];$dlt_exe=mysql_query($dlt); $tmpx++;
  }
  elseif($rowgruppo['status']=='pending') $tmpx++;
  elseif ($okvista==1) 
  {
   $queryfotogruppo="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowgruppo['thumb'];
   $resultfotogruppo=mysql_query($queryfotogruppo);
   $rowfotogruppo=mysql_fetch_row($resultfotogruppo);
   $idswiax=$row['id'];
   echo $parteintroduttiva;
   ?>
   <script>function extext_<?php echo $row['id'];?>()
   {
    texttorender='<?php echo process_db_input(strip_tags($rowgruppo['desc']));?>';
    $('#desc<?php echo $row['id'];?>').text(texttorender);
   }
   </script>
   <?php
   $descrizionet=$funclass->tagliaz($funclass->cleartesto($rowgruppo['desc']),300,$row['id']);
   echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    echo $sharebutts;
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
    {
     /**Share System**/
     include('socialbuttons.php');
     $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
     $parametri_photo['profile_nick']=getNickname($row['sender_id']);
     $parametri_photo['entry_url']=$unserialize['entry_url'];
     $parametri_photo['entry_title']=$unserialize['entry_title'];
	   $parametri_photo['id_action']=$row['id'];
	   $parametri_photo['url_page']=$crpageaddress;
     $params_condivisione=serialize($parametri_photo);
     $bt_share_params['1']=$accountid; //Sender
     $bt_share_params['2']=$row['sender_id']; //Recipient 
     $bt_share_params['3']=$sharelink; //Lang_Key_share 
     $bt_share_params['4']=readyshare($params_condivisione); //Params
     include('bt_share.php');
     /**End**/
    }
    echo '</div>'; //bt_list div close
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
   }
   $nomegruppo=strmaxtextlen($unserialize['entry_title']);
   $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
   $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
   $stampa=str_replace('{entry_title}',$nomegruppo,$stampa);
   echo $stampa;
   echo '<div id="data">'.$miadata;
   include('like.php');
   echo '</div>';
   echo '</div>';      
   echo '</div><div id="bloccoav">';
   if ($rowfotogruppo[3]!=FALSE) echo '<div id="videopreview"><a href="'.$unserialize['entry_url'].'"><img src="m/photos/get_image/browse/'.$rowfotogruppo[3].'.'.$rowfotogruppo[1].'" class="webimage"></a></div>';
   echo '<div id="descrizione"><a href="'.$unserialize['entry_url'].'"><h3>'.$unserialize['entry_title'].'</h3></a><p id="desc'.$row['id'].'">'.$descrizionet.'</p></div>';
   echo '</div>';
   include ($commentsarea); 
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }  
  else $tmpx++;
 }
 else $tmpx++; 
 }
 else $tmpx++;
}		 
//END


// EVENT
elseif (
(($row['alert_unit']=='bx_events' OR $row['lang_key']=='_ibdw_evowall_bx_event_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Events")))
OR
(($row['alert_unit']=='ue30_event' OR $row['lang_key']=='_ibdw_evowall_ue30_event_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Events")))
) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_ibdw_evowall_bx_event_add_condivisione')
  {
   $alert_unit="bx_events";
   $sharelink="_ibdw_evowall_bx_event_add_condivisione";
  }
  elseif ($row['lang_key']=='_ibdw_evowall_ue30_event_add_condivisione')
  {
   $alert_unit="ue30_event";
   $sharelink="_ibdw_evowall_ue30_event_add_condivisione";
  }
  else
  {
   $alert_unit=$row['alert_unit'];
   if($alert_unit=="bx_events") $sharelink="_ibdw_evowall_bx_event_add_condivisione";
   elseif($alert_unit=="ue30_event") $sharelink="_ibdw_evowall_ue30_event_add_condivisione";
  }
  $singmodname="event";
 
  if ($row['lang_key']=='_'.$alert_unit.'_spy_post') {$stampa=_t("_ibdw_evowall_".$singmodname."_add");}
  elseif ($row['lang_key']=='_'.$alert_unit.'_spy_join') {$stampa=_t("_ibdw_evowall_".$singmodname."_join");}
  elseif ($row['lang_key']=='_'.$alert_unit.'_spy_rate') {$stampa=_t("_ibdw_evowall_".$singmodname."_rate");}
  elseif ($row['lang_key']=='_'.$alert_unit.'_spy_comment') {$stampa=_t("_ibdw_evowall_".$singmodname."_comment");}
  elseif ($row['lang_key']=='_'.$alert_unit.'_spy_post_change') {$stampa=_t("_ibdw_evowall_".$singmodname."_edit");}
  elseif ($row['lang_key']=='_ibdw_evowall_bx_event_add_condivisione') {$stampa=_t("_ibdw_evowall_bx_".$singmodname."_add_condivisione");}
  $trovaslash=substr_count($unserialize['entry_url'],"/");
  $verificauri=explode ("/",$unserialize['entry_url']);
  $verificauri=$verificauri[$trovaslash];
  $queryevento="SELECT Title,PrimPhoto,Description,EntryUri,ID,EventStart,EventEnd,Status,allow_view_event_to,ResponsibleID FROM ".$alert_unit."_main WHERE EntryUri='$verificauri'";
  $resultevento=mysql_query($queryevento);
  $rowevento=mysql_fetch_assoc($resultevento);     
  $queryfotoevento="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowevento['PrimPhoto'];
  $resultfotoevento=mysql_query($queryfotoevento);
  $rowfotoevento=mysql_fetch_row($resultfotoevento);
  $idswiaee=$row['id'];
  $idswiab=$row['id'];
  $okvista=$funclass->privstate($rowevento['allow_view_event_to'],'events',$rowevento['ResponsibleID'],$accountid,isFaved($rowevento['ResponsibleID'],$accountid),'view_event'); 
  if($rowevento['Title']==FALSE or $rowevento['Status']=='pending') $tmpx++;
  elseif ($okvista==1)
  {
   echo $parteintroduttiva;
   echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
   {
    echo $sharebutts;
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
    {
     /**Share System**/
     include('socialbuttons.php');
     $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
     $parametri_photo['profile_nick']=getNickname($row['sender_id']);
     $parametri_photo['entry_url']=$unserialize['entry_url'];
     $parametri_photo['entry_title']=$unserialize['entry_title'];
     $parametri_photo['id_action']=$row['id'];
     $parametri_photo['url_page']=$crpageaddress;
     $params_condivisione=serialize($parametri_photo);
     $bt_share_params['1']=$accountid; //Sender
     $bt_share_params['2']=$row['sender_id']; //Recipient 
     $bt_share_params['3']=$sharelink; //Lang_Key_share 
     $bt_share_params['4']=readyshare($params_condivisione); //Params
     include('bt_share.php');
     /**End**/
    }
    echo '</div>'; //bt_list div close
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
   }
   $nomeevento=strmaxtextlen($unserialize['entry_title']);
   $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
   $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);             
   $stampa=str_replace('{entry_title}',$nomeevento,$stampa);
   echo $stampa;
   echo '<div id="data">'.$miadata;
   include('like.php');
   echo '</div>';
   echo '</div>';
   echo '</div><div id="bloccoav"><div id="videopreview">';
   if ($rowfotoevento[3]==FALSE) echo '<a href="'.$unserialize['entry_url'].'"><img src="'.BX_DOL_URL_MODULES.'ibdw/evowall/templates/base/images/unk.png" class="webimage"></a>';
   else echo '<a href="'.$unserialize['entry_url'].'"><img src="m/photos/get_image/browse/'.$rowfotoevento[3].'.'.$rowfotoevento[1].'" class="webimage" onload="$(this).fadeIn(200);"></a>';
   $descrizionei=$rowevento['Description'];
   ?>
   <script>function extext_<?php echo $row['id'];?>()
   {
    texttorender='<?php echo process_db_input(strip_tags($rowevento['Description']));?>';
    $('#desc<?php echo $row['id'];?>').text(texttorender);
   }</script>
   <?php
   
   $descrizionei=$funclass->tagliaz($descrizionei,300,$row['id']);
	 if ($seldate=="d/m/Y H:i:s")
	 {
	  $dateeventstart=date("d/m/Y H:i:s",($rowevento['EventStart']+$offset));
	  $dateeventend=date("d/m/Y H:i",($rowevento['EventEnd']+$offset));
	 }
	 else
	 {
	  $dateeventstart=date("m/d/Y H:i",($rowevento['EventStart']+$offset));
	  $dateeventend=date("m/d/Y H:i",($rowevento['EventEnd']+$offset));
	 }
   echo '</div><div id="descrizione"><a href="'.$unserialize['entry_url'].'"><h3>'.$rowevento['Title'].'</h3></a> <p id="desc'.$row['id'].'">'.$funclass->cleartesto($descrizionei).'</p><div id="eventmulticont"><div id="eventdatacont"><p class="eventsdate">'._t("_ibdw_evowall_event_start").'</p><div class="eventvalue">'.$dateeventstart.'</div></div><div id="eventdatacont"><p class="eventsdate">'._t("_ibdw_evowall_event_end").'</p><div class="eventvalue">'.$dateeventend.'</div></div></div></div>';
   echo '</div>';
   include ($commentsarea);
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; ;
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END


// LOCATION UE30
elseif ($row['lang_key']=='_ue30_location_spy_post' OR $row['lang_key']=='_ue30_location_spy_post_change' OR $row['lang_key']=='_ue30_location_spy_join' OR $row['lang_key']=='_ue30_location_spy_rate' OR $row['lang_key']=='_ue30_location_spy_comment' OR $row['lang_key']=='_ibdw_evowall_ue30_locations_add_condivisione')  
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $stampa=_t($row['lang_key']);
  $trovaslash=substr_count($unserialize['entry_url'],"/");
  $verificauri=explode ("/",$unserialize['entry_url']);
  $verificauri=$verificauri[$trovaslash];
  $querylocation="SELECT title,thumb,ue30_location_main.desc,uri,id,country,city,allow_view_location_to,author_id FROM ue30_location_main WHERE uri='$verificauri'";
  $resultlocation=mysql_query($querylocation);
  $rowlocation=mysql_fetch_assoc($resultlocation);     
  $queryfotolocation="SELECT ID,Ext,Title,Hash FROM bx_photos_main WHERE ID=".$rowlocation['thumb'];
  $resultfotolocation=mysql_query($queryfotolocation);
  $rowfotolocation=mysql_fetch_row($resultfotolocation);
  $idswiaee=$row['id'];
  $idswiab=$row['id'];
  $okvista=$funclass->privstate($rowlocation['allow_view_location_to'],'locations',$rowlocation['author_id'],$accountid,isFaved($rowlocation['author_id'],$accountid),'allow_view_location_to');
  if($rowlocation['title']==FALSE) $tmpx++;
  elseif ($okvista==1)
  {
   echo $parteintroduttiva;
   echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0))
   {
    echo $sharebutts;
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
    {
     /**Share System**/
     include('socialbuttons.php');
     $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
     $parametri_photo['profile_nick']=getNickname($row['sender_id']);
     $parametri_photo['entry_url']=$unserialize['entry_url'];
     $parametri_photo['entry_title']=$unserialize['entry_title'];
 	   $parametri_photo['id_action']=$row['id'];
	   $parametri_photo['url_page']=$crpageaddress;
     $params_condivisione=serialize($parametri_photo);
     $bt_share_params['1']=$accountid; //Sender
     $bt_share_params['2']=$row['sender_id']; //Recipient 
     $bt_share_params['3']='_ibdw_evowall_ue30_locations_add_condivisione'; //Lang_Key_share 
     $bt_share_params['4']=readyshare($params_condivisione); //Params
     include('bt_share.php');
     /**End**/
    }
    echo '</div>'; //bt_list div close
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
    if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
   }
   $nomelocation=strmaxtextlen($unserialize['entry_title']);
   $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
   $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
   $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);             
   $stampa=str_replace('{entry_title}',$nomelocation,$stampa);
   echo $stampa;
   echo '<div id="data">'.$miadata;
   include('like.php');
   echo '</div>';
   echo '</div><div id="bloccoav">';
   if ($rowfotolocation[3]==FALSE) echo '<div id="anteprima"><a href="'.$unserialize['entry_url'].'"><img src=src="'.BX_DOL_URL_MODULES.'ibdw/evowall/templates/base/images/unk.png" class="unklockstyle2"></a>';
   else echo '<div id="videopreview"><a href="'.$unserialize['entry_url'].'"><img src="m/photos/get_image/browse/'.$rowfotolocation[3].'.'.$rowfotolocation[1].'"class="webimage" onload="$(this).fadeIn(200);"></a>';
   ?>
   <script>function extext_<?php echo $row['id'];?>()
   {
    texttorender='<?php echo process_db_input(strip_tags($rowlocation['desc']));?>';
    $('#desc<?php echo $row['id'];?>').text(texttorender);
   }
   </script>
   <?php
   $descrizionei=$rowlocation['desc'];
   $descrizionei=$funclass->tagliaz($descrizionei,300,$row['id']);
   echo '</div><div id="descrizione"><a href="'.$unserialize['entry_url'].'"><h3>'.$rowlocation['title'].'</h3></a><p id="desc'.$row['id'].'">'.$funclass->cleartesto($descrizionei).'</p></div></div>';
   echo '</div>';
   include ($commentsarea);
   if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
   echo '</div>';
   if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END

// BOONEX POLL
elseif (($row['lang_key']=='_bx_poll_added' OR $row['lang_key']=='_bx_poll_answered' OR $row['lang_key']=='_bx_poll_rated' OR $row['lang_key']=='_bx_poll_commented' OR $row['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Polls")))
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_bx_poll_added') $stampa=_t("_ibdw_evowall_poll_add");
  elseif ($row['lang_key']=='_bx_poll_answered') $stampa=_t("_ibdw_evowall_reply_polls");
  elseif ($row['lang_key']=='_bx_poll_rated') $stampa=_t("_ibdw_evowall_rated_polls");
  elseif ($row['lang_key']=='_bx_poll_commented') $stampa=_t("_ibdw_evowall_comment_polls");
  elseif ($row['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione') $stampa=_t("_ibdw_evowall_bx_poll_add_condivisione");
  $idswiaaa=$row['id'];
  if($unserialize['poll_link']=="") 
  {
   $arrayvarpoll=explode ("show_poll_info&id=", htmlspecialchars_decode($unserialize['poll_url']));
   $urltoshareis=$unserialize['poll_url'];
  }
  else 
  {
   $arrayvarpoll=explode ("show_poll_info&id=",$unserialize['poll_link']);
   $urltoshareis=$unserialize['poll_link'];
  }
  
  $idpollis=$arrayvarpoll[1];
  $getpriv="SELECT id_profile,poll_status,allow_view_to FROM bx_poll_data WHERE id_poll=".$idpollis;
  $rungetpriv=mysql_query($getpriv);
  $getifexists=mysql_num_rows($rungetpriv);
  if ($getifexists>0)
  {
   $rowpoll=mysql_fetch_assoc($rungetpriv);
   $okvista=$funclass->privstate($rowpoll['allow_view_to'],'poll',$rowpoll['id_profile'],$accountid,isFaved($rowpoll['id_profile'],$accountid),'view');
   if ($okvista==1)
   {
    echo $parteintroduttiva;
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare') AND $row['lang_key']=='_bx_poll_added' OR $row['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione') OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
    {
     echo $sharebutts;
	   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('socialbuttons.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=getNickname($row['sender_id']);
      $parametri_photo['poll_url']=str_replace('&','xeamp',$urltoshareis);
      $parametri_photo['poll_caption']=$unserialize['poll_caption'];
	    $parametri_photo['id_action']=$row['id'];
	    $parametri_photo['url_page']=$crpageaddress;
      $params_condivisione=serialize($parametri_photo);
      $bt_share_params['1']=$accountid; //Sender
      $bt_share_params['2']=$row['sender_id']; //Recipient 
      $bt_share_params['3']='_ibdw_evowall_bx_poll_add_condivisione'; //Lang_Key_share 
      $bt_share_params['4']=readyshare($params_condivisione); //Params
      include('bt_share.php');
      /**End**/
     }
     echo '</div>'; //bt_list div close
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
    }
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{recipient_p_link}',getUsername($row['recipient_id']),$stampa);
    $stampa=str_replace('{recipient_p_nick}',getNickname($row['recipient_id']),$stampa);
    $stampa=str_replace('{poll_url}',$urltoshareis,$stampa);             
    $stampa=str_replace('{poll_caption}',$unserialize['poll_caption'],$stampa);
    $stampa=str_replace('{entry_url}',$unserialize['poll_link'],$stampa);
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div>';
    echo '</div>';
    $getpollinfo="SELECT * FROM bx_poll_data WHERE id_poll=".$idpollis." AND poll_status='active'";  
    $rungetinfo=mysql_query($getpollinfo);
    $ifthereis=mysql_num_rows($rungetinfo);
    if($ifthereis>0)
    {
     $answer='';
     $percentage=0;
     $pollinformations=mysql_fetch_assoc($rungetinfo);
     $polltitle=$pollinformations['poll_question'];
     $pollanswers=$pollinformations['poll_answers'];
     $pollstats=$pollinformations['poll_results'];
     $total=$pollinformations['poll_total_votes'];
     $answersvector=explode('<delim>',$pollanswers);
     $arrlength=count($answersvector);
     $answersstatvector=explode(';',$pollstats);     
     for($x=0;$x<$arrlength-1;$x++)
     {
      $answerstat=$answersstatvector[$x];
      if ($total>0) $percentage=$answerstat/$total*100;
      else $percentage=0;
      if ($answerstat>0)
      { 
       $addstattext=" (".$answerstat.")";
       $totaltext=" (".$total.")";
      }
      else 
      {
       $addstattext="";
       $totaltext="";
      }
      $answer=$answer."<div id='a".$x."' class='singleanswer'>".$answersvector[$x].$addstattext."<div class='anspercentage' style='width:".$percentage."%;'>".round($percentage,2)."%</div></div>";
     }
     $fotourl='<div class="clear_both"></div><div class="pollbox"><div class="polltitle">'.$polltitle.$totaltext.'</div><div class="pollanswers" id="answ'.$row['id'].'">'.$answer.'</div></div>';
    }
    echo $fotourl;
    include ($commentsarea); 
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
   else $tmpx++;
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END


// MODZZZ POLL
elseif (($row['lang_key']=='_modzzz_polls_spy_post' OR $row['lang_key']=='_modzzz_polls_spy_post_change' OR $row['lang_key']=='_modzzz_polls_spy_rate' OR $row['lang_key']=='_modzzz_polls_spy_comment' OR $row['lang_key']=='_ibdw_evowall_modzzz_poll_add_condivisione') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Polls")))
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if ($row['lang_key']=='_modzzz_polls_spy_post') $stampa=_t("_ibdw_evowall_modzzz_poll_add");
  elseif ($row['lang_key']=='_modzzz_polls_spy_post_change') $stampa=_t("_ibdw_evowall_modzzz_polls_change");
  elseif ($row['lang_key']=='_modzzz_polls_spy_rate') $stampa=_t("_ibdw_evowall_modzzz_rated_polls");
  elseif ($row['lang_key']=='_modzzz_polls_spy_comment') $stampa=_t("_ibdw_evowall_modzzz_comment_polls");
  elseif ($row['lang_key']=='_ibdw_evowall_modzzz_poll_add_condivisione') $stampa=_t("_ibdw_evowall_modzzz_poll_add_condivisione");
  $idswiaaa=$row['id'];
  $arrayvarpoll=explode ("/polls/view/",$unserialize['entry_url']);
  $urltoshareis=$unserialize['entry_url'];
  $urlpollis=$arrayvarpoll[1];
  $getpriv="SELECT * FROM modzzz_polls_main WHERE uri='".$urlpollis."' AND status='approved'";
   
  $rungetpriv=mysql_query($getpriv);
  $getifexists=mysql_num_rows($rungetpriv);
  if ($getifexists>0)
  { 
   $rowpoll=mysql_fetch_assoc($rungetpriv);
   $okvista=$funclass->privstate($rowpoll['allow_view_poll_to'],'poll',$rowpoll['author_id'],$accountid,isFaved($rowpoll['author_id'],$accountid),'view');
   if ($okvista==1)
   {        
    echo $parteintroduttiva;
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare') AND $row['lang_key']=='_modzzz_polls_spy_post' OR $row['lang_key']=='_ibdw_evowall_modzzz_poll_add_condivisione') OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
    {
     echo $sharebutts;
	   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
     {
      /**Share System**/
      include('socialbuttons.php');
      $parametri_photo['profile_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
      $parametri_photo['profile_nick']=getNickname($row['sender_id']);
      $parametri_photo['entry_url']=str_replace('&','xeamp',$urltoshareis);
      $parametri_photo['poll_caption']=$unserialize['title'];
	    $parametri_photo['id_action']=$row['id'];
	    $parametri_photo['url_page']=$crpageaddress;
      $params_condivisione=serialize($parametri_photo);
      $bt_share_params['1']=$accountid; //Sender
      $bt_share_params['2']=$row['sender_id']; //Recipient 
      $bt_share_params['3']='_ibdw_evowall_modzzz_poll_add_condivisione'; //Lang_Key_share 
      $bt_share_params['4']=readyshare($params_condivisione); //Params
      include('bt_share.php');
      /**End**/
     }
     echo '</div>'; //bt_list div close
     if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php';
    }
    $polltitle=$unserialize['entry_title'];
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{recipient_p_link}',getUsername($row['recipient_id']),$stampa);
    $stampa=str_replace('{recipient_p_nick}',getNickname($row['recipient_id']),$stampa);
    $stampa=str_replace('{poll_url}',$urltoshareis,$stampa);             
    $stampa=str_replace('{poll_caption}',$polltitle,$stampa);
    $stampa=str_replace('{entry_title}',$polltitle,$stampa);
    $stampa=str_replace('{entry_url}',$unserialize['entry_url'],$stampa);
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div>';
    echo '</div>'; 
    $answer='';
    $percentage=0;
    
    $getanswer="SELECT * FROM modzzz_polls_answers WHERE poll_id=".$rowpoll['id'];
    $rungetanswer=mysql_query($getanswer);
    $ifthereis2=mysql_num_rows($rungetanswer);
    if($ifthereis2>0)
    {
     $total=0;
     for ($y=0;$y<$ifthereis2;$y++)
     {
      $get_ans_res=mysql_fetch_assoc($rungetanswer);
      $pollanswer[$y]=$get_ans_res['answer'];
      $pollstat=$get_ans_res['vote_count'];
      //$total return the total of votes for the poll
      $total=$total+$pollstat;
      //$singleanswervote is the total of vote of a specific answer
      $singleanswervote[$y]=$pollstat;
     }   
     for ($y=0;$y<$ifthereis2;$y++)
     {
      $answerstat=$singleanswervote[$y];
      if ($total>0) $percentage=$answerstat/$total*100;
      else $percentage=0;
      $addstattext=" (".$singleanswervote[$y].")";
      $totaltext=" (".$total.")";
      
      $answer=$answer."<div id='a".$y."' class='singleanswer'>".$pollanswer[$y].$addstattext."<div class='anspercentage' style='width:".$percentage."%;'>".round($percentage,2)."%</div></div>";
     }
     $fotourl='<div class="clear_both"></div><div class="pollbox"><div class="polltitle">'.$polltitle.$totaltext.'</div><div class="pollanswers" id="answ'.$row['id'].'">'.$answer.'</div></div>';
     echo $fotourl;
     include ($commentsarea); 
     if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
     echo '</div>';
     if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
    }
    else $tmpx++;
   }
   else $tmpx++;
  }
  else $tmpx++;
 }
 else $tmpx++;
}
//END

// PROFILE COVER
elseif ($row['lang_key']=='_ibdw_profilecover_update' or $row['lang_key']=='_ibdw_profilecover_update_male' or $row['lang_key']=='_ibdw_profilecover_update_female')
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $unserialize=unserialize($row['params']);
  $hash=$unserialize['currenthash']; 
  if (!isset($unserialize['width'])) $width="";
  else $width=$unserialize['width'];
  if (!isset($unserialize['position'])) $position="";
  else $position=$unserialize['position'];
  $getmediumformat="SELECT ID FROM bx_photos_main WHERE Hash='".$hash."'";
  $runitformat=mysql_query($getmediumformat);
  $getifexists=mysql_num_rows($runitformat);
  if ($getifexists>0)
  {
   $rowfoto=mysql_fetch_row($runitformat);
   $indirizzourl_true=BX_DOL_URL_ROOT.'m/photos/view/'.$hash;
   $querypriva="SELECT AllowAlbumView,Owner FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE id_object='$rowfoto[0]' AND TYPE='bx_photos'";
   $resultpriva=mysql_query($querypriva);
   if (mysql_num_rows($resultpriva)>0)
   {
    $rowpriva=mysql_fetch_assoc($resultpriva); 
    $okvista=$funclass->privstate($rowpriva['AllowAlbumView'],'photos',$rowpriva['Owner'],$accountid,isFaved($rowpriva['Owner'],$accountid),'view');
   }
   else $okvista=0;
   if($okvista==1)
   {
    $stampa=_t($row['lang_key']);
    $idswiaaa=$row['id'];
    echo $parteintroduttiva;
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
    {
     echo $sharebutts;
     include 'rem_ove.php';
     echo '</div>';
     include 'bt_delete.php';
    }
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div>';
    $getidph=mysql_fetch_assoc($runitformat);
    $imageidis=$getidph['ID'];
    $defaultimage='m/photos/get_image/file/'.$hash.'.jpg';
    echo '</div>';
    echo '<div id="bloccoavcover"><div id="anteprimacover">
    <div id="boximg'.$row['id'].'" class="coverboximage" style="background-image:url('.BX_DOL_URL_ROOT.$defaultimage.');background-repeat: no-repeat;background-position: 0 0;background-size: cover;background-color:transparent;" id="coverbox"></div>
    </div></div>';
    ?>
    <script type="text/javascript">
    var w=document.getElementById("boximg<?php echo $row['id'];?>").offsetWidth;
    <?php 
    if ($width=="") echo "var h=150;";
    else
    {
     ?>
     var h=Math.round((w*300)/<?php echo $width;?>);
     <?php 
    }
    if ($position=="") echo "var newposition=0;";
    else
    {
     if ($width=="")  echo "var newposition=0;";
     else echo "var newposition=".$position."/".$width."*w;";
    }
    ?>
    $("#boximg"+<?php echo $row['id']?>).css("height",h);
    $("#boximg"+<?php echo $row['id']?>).css("background-position","0 "+newposition+"px");
    </script> 
    <?php
    include ($commentsarea); 
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina); 
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
   else $tmpx++;
  }
  else {$tmpx++;$dlt="DELETE FROM `bx_spy_data` WHERE `id`=".$row['id']; $dlt_exe=mysql_query($dlt);}
 }
 else $tmpx++;
}
//END

// GROUP COVER
elseif ($row['lang_key']=='_ibdw_groupcover_update' or $row['lang_key']=='_ibdw_groupcover_update_male' or $row['lang_key']=='_ibdw_groupcover_update_female')
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $unserialize=unserialize($row['params']);
  $hash=$unserialize['currenthash'];
  if (!isset($unserialize['width'])) $width="";
  else $width=$unserialize['width'];
  if (!isset($unserialize['position'])) $position="";
  else $position=$unserialize['position'];
  $getmediumformat="SELECT ID FROM bx_photos_main WHERE Hash='".$hash."'";
  $runitformat=mysql_query($getmediumformat);
  $getifexists=mysql_num_rows($runitformat);
  if ($getifexists>0)
  {
   $rowfoto=mysql_fetch_row($runitformat);
   $indirizzourl_true=BX_DOL_URL_ROOT.'m/photos/view/'.$hash;
   $querypriva="SELECT AllowAlbumView,Owner FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE id_object='$rowfoto[0]' AND TYPE='bx_photos'";
   $resultpriva=mysql_query($querypriva);
   if (mysql_num_rows($resultpriva)>0)
   {
    $rowpriva=mysql_fetch_assoc($resultpriva); 
    $okvista=$funclass->privstate($rowpriva['AllowAlbumView'],'photos',$rowpriva['Owner'],$accountid,isFaved($rowpriva['Owner'],$accountid),'view');
   }
   else $okvista=0;
   if($okvista==1)
   {
    $stampa=_t($row['lang_key']);
    $idswiaaa=$row['id'];
    echo $parteintroduttiva;
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
    {
     echo $sharebutts;
     include 'rem_ove.php';
     echo '</div>';
     include 'bt_delete.php'; 
    }
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{group_uri}',$unserialize['group_uri'],$stampa);
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div>'; 
    $getidph=mysql_fetch_assoc($runitformat);
    $imageidis=$getidph['ID'];
    $defaultimage='m/photos/get_image/file/'.$hash.'.jpg';
    
    echo '</div>';
    echo '<div id="bloccoavcover"><div id="anteprimacover">
    <div id="boximg'.$row['id'].'" class="coverboximage" style="background-image:url('.BX_DOL_URL_ROOT.$defaultimage.');background-repeat: no-repeat;background-position: 0 0;background-size: cover;background-color:transparent;" id="coverbox"></div>
    </div></div>';
    ?>
    <script type="text/javascript">
    var w=document.getElementById("boximg<?php echo $row['id'];?>").offsetWidth;
    <?php 
    if ($width=="") echo "var h=150;";
    else
    {
    ?>
     var h=Math.round((w*300)/<?php echo $width;?>);
    <?php 
    }
    if ($position=="") echo "var newposition=0;";
    else
    {
     if ($width=="")  echo "var newposition=0;";
     else echo "var newposition=".$position."/".$width."*w;";
    }
    ?>
    $("#boximg"+<?php echo $row['id']?>).css("height",h);
    $("#boximg"+<?php echo $row['id']?>).css("background-position","0 "+newposition+"px");
    </script> 
    <?php
    include ($commentsarea); 
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
   else $tmpx++;
  }
  else {$tmpx++;$dlt="DELETE FROM `bx_spy_data` WHERE `id`=".$row['id']; $dlt_exe=mysql_query($dlt);}
 }
 else $tmpx++;
}
//END

// PAGE COVER
elseif ($row['lang_key']=='_ibdw_pagecover_update' or $row['lang_key']=='_ibdw_pagecover_update_male' or $row['lang_key']=='_ibdw_pagecover_update_female')
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $unserialize=unserialize($row['params']);
  $hash=$unserialize['currenthash'];
  if (!isset($unserialize['width'])) $width="";
  else $width=$unserialize['width'];
  if (!isset($unserialize['position'])) $position="";
  else $position=$unserialize['position']; 
  $getmediumformat="SELECT ID FROM bx_photos_main WHERE Hash='".$hash."'";
  $runitformat=mysql_query($getmediumformat);
  $getifexists=mysql_num_rows($runitformat);
  if ($getifexists>0)
  {
  $rowfoto=mysql_fetch_row($runitformat);
   $indirizzourl_true=BX_DOL_URL_ROOT.'m/photos/view/'.$hash;
   $querypriva="SELECT AllowAlbumView,Owner FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE id_object='$rowfoto[0]' AND TYPE='bx_photos'";
   $resultpriva=mysql_query($querypriva);
   if (mysql_num_rows($resultpriva)>0)
   {
    $rowpriva=mysql_fetch_assoc($resultpriva); 
    $okvista=$funclass->privstate($rowpriva['AllowAlbumView'],'photos',$rowpriva['Owner'],$accountid,isFaved($rowpriva['Owner'],$accountid),'view');
   }
   else $okvista=0;
   if($okvista==1)
   { 
    $stampa=_t($row['lang_key']);
    $idswiaaa=$row['id'];
    echo $parteintroduttiva;
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
    {
     echo $sharebutts;
     include 'rem_ove.php';
     echo '</div>';
     include 'bt_delete.php'; 
    }
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{page_uri}',$unserialize['page_uri'],$stampa);
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div>';
    $getidph=mysql_fetch_assoc($runitformat);
    $imageidis=$getidph['ID'];
    $defaultimage='m/photos/get_image/file/'.$hash.'.jpg';
    echo '</div>';
    echo '<div id="bloccoavcover"><div id="anteprimacover">
    <div id="boximg'.$row['id'].'" class="coverboximage" style="background-image:url('.BX_DOL_URL_ROOT.$defaultimage.');background-repeat: no-repeat;background-position: 0 0;background-size: cover;background-color:transparent;" id="coverbox"></div>
    </div></div>';
    ?>
    <script type="text/javascript">
    var w=document.getElementById("boximg<?php echo $row['id'];?>").offsetWidth;
    <?php 
    if ($width=="") echo "var h=150;";
    else
    {
     ?>
     var h=Math.round((w*300)/<?php echo $width;?>);
     <?php 
    }
    if ($position=="") echo "var newposition=0;";
    else
    {
     if ($width=="")  echo "var newposition=0;";
     else echo "var newposition=".$position."/".$width."*w;";
    }
    ?>
    $("#boximg"+<?php echo $row['id']?>).css("height",h);
    $("#boximg"+<?php echo $row['id']?>).css("background-position","0 "+newposition+"px");
    </script> 
    <?php
    include ($commentsarea); 
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
   else $tmpx++;
  }
  else {$tmpx++;$dlt="DELETE FROM `bx_spy_data` WHERE `id`=".$row['id']; $dlt_exe=mysql_query($dlt);}
 }
 else $tmpx++;
}
//END

// EVENTS COVER
elseif ($row['lang_key']=='_ibdw_eventcover_update' or $row['lang_key']=='_ibdw_eventcover_update_male' or $row['lang_key']=='_ibdw_eventcover_update_female')
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $unserialize=unserialize($row['params']);
  $hash=$unserialize['currenthash'];
  if (!isset($unserialize['width'])) $width="";
  else $width=$unserialize['width'];
  if (!isset($unserialize['position'])) $position="";
  else $position=$unserialize['position'];
  $getmediumformat="SELECT ID FROM bx_photos_main WHERE Hash='".$hash."'";
  $runitformat=mysql_query($getmediumformat);
  $getifexists=mysql_num_rows($runitformat);
  if ($getifexists>0)
  {
   $rowfoto=mysql_fetch_row($runitformat);
   $indirizzourl_true=BX_DOL_URL_ROOT.'m/photos/view/'.$hash;
   $querypriva="SELECT AllowAlbumView,Owner FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE id_object='$rowfoto[0]' AND TYPE='bx_photos'";
   $resultpriva=mysql_query($querypriva);
   if (mysql_num_rows($resultpriva)>0)
   {
    $rowpriva=mysql_fetch_assoc($resultpriva); 
    $okvista=$funclass->privstate($rowpriva['AllowAlbumView'],'photos',$rowpriva['Owner'],$accountid,isFaved($rowpriva['Owner'],$accountid),'view');
   }
   else $okvista=0;
   if($okvista==1)
   {
    $stampa=_t($row['lang_key']);
    $idswiaaa=$row['id'];
    echo $parteintroduttiva;
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
    {
     echo $sharebutts;
     include 'rem_ove.php';
     echo '</div>';
     include 'bt_delete.php'; 
    }
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{event_uri}',$unserialize['event_uri'],$stampa);
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div>';
    $getidph=mysql_fetch_assoc($runitformat);
    $imageidis=$getidph['ID'];
    $defaultimage='m/photos/get_image/file/'.$hash.'.jpg';
    
    echo '</div>';
    echo '<div id="bloccoavcover"><div id="anteprimacover">
    <div id="boximg'.$row['id'].'" class="coverboximage" style="background-image:url('.BX_DOL_URL_ROOT.$defaultimage.');background-repeat: no-repeat;background-position: 0 0;background-size: cover;background-color:transparent;" id="coverbox"></div>
    </div></div>';
    ?>
    <script type="text/javascript">
    var w=document.getElementById("boximg<?php echo $row['id'];?>").offsetWidth;
    <?php 
    if ($width=="") echo "var h=150;";
    else
    {
    ?>
     var h=Math.round((w*300)/<?php echo $width;?>);
    <?php 
    }
    if ($position=="") echo "var newposition=0;";
    else
    {
     if ($width=="")  echo "var newposition=0;";
     else echo "var newposition=".$position."/".$width."*w;";
    }
    ?>
    $("#boximg"+<?php echo $row['id']?>).css("height",h);
    $("#boximg"+<?php echo $row['id']?>).css("background-position","0 "+newposition+"px");
    </script> 
    <?php
    include ($commentsarea); 
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
   else $tmpx++;
  }
  else {$tmpx++;$dlt="DELETE FROM `bx_spy_data` WHERE `id`=".$row['id']; $dlt_exe=mysql_query($dlt);}
 }
 else $tmpx++;
}
//END

// BLOGS COVER
elseif ($row['lang_key']=='_ibdw_blogcover_update' or $row['lang_key']=='_ibdw_blogcover_update_male' or $row['lang_key']=='_ibdw_blogcover_update_female')
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $unserialize=unserialize($row['params']);
  $hash=$unserialize['currenthash'];
  if (!isset($unserialize['width'])) $width="";
  else $width=$unserialize['width'];
  if (!isset($unserialize['position'])) $position="";
  else $position=$unserialize['position'];
  $getmediumformat="SELECT ID FROM bx_photos_main WHERE Hash='".$hash."'";
  $runitformat=mysql_query($getmediumformat);
  $getifexists=mysql_num_rows($runitformat);
  if ($getifexists>0)
  {
   $rowfoto=mysql_fetch_row($runitformat);
   $indirizzourl_true=BX_DOL_URL_ROOT.'m/photos/view/'.$hash;
   $querypriva="SELECT AllowAlbumView,Owner FROM sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album WHERE id_object='$rowfoto[0]' AND TYPE='bx_photos'";
   $resultpriva=mysql_query($querypriva);
   if (mysql_num_rows($resultpriva)>0)
   {
    $rowpriva=mysql_fetch_assoc($resultpriva); 
    $okvista=$funclass->privstate($rowpriva['AllowAlbumView'],'photos',$rowpriva['Owner'],$accountid,isFaved($rowpriva['Owner'],$accountid),'view');
   }
   else $okvista=0;
   if($okvista==1)
   {
    $stampa=_t($row['lang_key']);
    $idswiaaa=$row['id'];
    echo $parteintroduttiva;
    echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
    if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
    {
     echo $sharebutts;
     include 'rem_ove.php';
     echo '</div>';
     include 'bt_delete.php'; 
    }
    $stampa=str_replace('{profile_nick}',getNickname($row['sender_id']),$stampa);
    $stampa=str_replace('{profile_link}',getUsername($row['sender_id']),$stampa);
    $stampa=str_replace('{blog_uri}',$unserialize['blog_uri'],$stampa);
    $stampa=str_replace('m/blogs','blogs',$stampa);
    echo $stampa;
    echo '<div id="data">'.$miadata;
    include('like.php');
    echo '</div>';
    echo '</div>';
    $getidph=mysql_fetch_assoc($runitformat);
    $imageidis=$getidph['ID'];
    $defaultimage='m/photos/get_image/file/'.$hash.'.jpg';
    
    echo '</div>';
    echo '<div id="bloccoavcover"><div id="anteprimacover">
    <div id="boximg'.$row['id'].'" class="coverboximage" style="background-image:url('.BX_DOL_URL_ROOT.$defaultimage.');background-repeat: no-repeat;background-position: 0 0;background-size: cover;background-color:transparent;" id="coverbox"></div>
    </div></div>';
    ?>
    <script type="text/javascript">
    var w=document.getElementById("boximg<?php echo $row['id'];?>").offsetWidth;
    <?php 
    if ($width=="") echo "var h=150;";
    else
    {
    ?>
     var h=Math.round((w*300)/<?php echo $width;?>);
    <?php 
    }
    if ($position=="") echo "var newposition=0;";
    else
    {
     if ($width=="")  echo "var newposition=0;";
     else echo "var newposition=".$position."/".$width."*w;";
    }
    ?>
    $("#boximg"+<?php echo $row['id']?>).css("height",h);
    $("#boximg"+<?php echo $row['id']?>).css("background-position","0 "+newposition+"px");
    </script> 
    <?php
    include ($commentsarea); 
    if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
    echo '</div>';
    if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>'; 
   }
   else $tmpx++;
  }
  else {$tmpx++;$dlt="DELETE FROM `bx_spy_data` WHERE `id`=".$row['id']; $dlt_exe=mysql_query($dlt);}
 }
 else $tmpx++;
}
//END

//URL AUTODETECTION
elseif (($row['lang_key']=='_ibdw_evowall_bx_url_add' or $row['lang_key']=='_ibdw_evowall_bx_url_share') and ($funclass->ActionVerify($profilemembership,"EVO WALL - Sites")))
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  $parametri_photo['immagine']=$unserialize['immagine'];
  echo $parteintroduttiva;
  echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
  if(($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) OR (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0)) 
  {
   echo $sharebutts;
   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'rem_ove.php';
   if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare'))
   {
    /**Share System**/
    include('socialbuttons.php');
    $parametri_photo['sender_p_link']=BX_DOL_URL_ROOT.$aInfomembers['NickName']; 
    $parametri_photo['sender_p_nick']=getNickname($row['sender_id']);
    $parametri_photo['indirizzo']=$unserialize['indirizzo'];
    $parametri_photo['titolosito']=str_replace("persaccento","'",str_replace(array("\n","\r"),"",urldecode($unserialize['titolosito']))); 
	  $parametri_photo['descrizione']=str_replace("persaccento","'",str_replace(array("\n","\r"),"",trim(urldecode($unserialize['descrizione']))));
    $parametri_photo['immagine']=preg_replace("/\s*/m","",$unserialize['immagine']);
    $parametri_photo['anteprimano']=$unserialize['anteprimano'];
	  $parametri_photo['id_action']=$row['id'];
	  $parametri_photo['url_page']=$crpageaddress;
    $params_condivisione=serialize($parametri_photo);   
    $bt_share_params['1']=$accountid; //Sender
	  if ($profileid<>$row['sender_id']) $bt_share_params['2']=$row['sender_id']; //Recipient
	  else $bt_share_params['2']=0; //Recipient	 
    $bt_share_params['3']='_ibdw_evowall_bx_url_share'; //Lang_Key_share
    $bt_share_params['4']=readyshare($params_condivisione); //Params
	  include('bt_share.php');
    /**End**/
	 }
	 echo '</div>'; //bt_list div close
	 if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowshare')) include 'bt_external_share.php'; 
	 if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) include 'bt_delete.php';
  }
  echo '<a class="usernamestyle" href="'.getUsername($row['sender_id']).'">';
  echo getNickname($row['sender_id']); 
  $indirizzourl=$unserialize['indirizzo'];
  $messaggiourl=$unserialize['messaggio'];
  echo '</a>';
  if ($row['sender_id']<>$row['recipient_id'] and $row['recipient_id']<>0)
  { 
   echo '<i class="sys-icon chevron-right" alt=""></i>';
   echo '<a class="usernamestyle" href="'.getUsername($row['recipient_id']).'">';
   echo getNickname($row['recipient_id']);
   echo '</a>';
  }
  if ($messaggiourl<>"" and $messaggiourl<>$indirizzourl) 
  {
   $messaggiourl=str_replace("codapos1","'",$messaggiourl);
   $messaggiourl=str_replace("%26","&",$messaggiourl);
   
   echo '<div id="data">'.$miadata;
   include('like.php');
   echo '</div></div></div>';
   echo '<div class="persmess"><div id="mstyle">'.$messaggiourl.'</div></div>';
  }
  else 
  {    
   
   echo '<div id="data">'.$miadata;
   include('like.php');
   echo '</div></div></div>';
   if ($row['lang_key']=='_ibdw_evowall_bx_url_add') echo '<div class="persmess"><div id="mstyle">'._t("_ibdw_evowall_urlmess").'<a target="_blank" href="'.$indirizzourl.'">'.$indirizzourl.'</a></div></div>';
   elseif($row['lang_key']=='_ibdw_evowall_bx_url_share') echo '<div class="persmess">'._t("_ibdw_evowall_surlmess").'<a target="_blank" href="'.$indirizzourl.'">'.$indirizzourl.'</a></div>';
  }
  echo '<div id="bloccoav_boxed">';
  if($unserialize['anteprimano']==1 OR $unserialize['immagine']=='undefined') echo '<style>#webint'.$assegnazione.'{width:96%;}</style>'; 
  else 
  {
   //Check image. If not exists (error 404) is not displayed
   $ch=curl_init($unserialize['immagine']);
   curl_setopt($ch,CURLOPT_NOBODY,true);
   curl_exec($ch);
   $retcode=curl_getinfo($ch,CURLINFO_HTTP_CODE);
   curl_close($ch);
   if($unserialize['immagine']<>'') $imageis='<div id="videopreview"><a target="_blank" href="'.$indirizzourl.'"><img src="'.$unserialize['immagine'].'" class="webimage"></a></div>';
   else $imageis=''; 
   if ($retcode<>404) echo $imageis;
  }
  $titolodefinitivo=str_replace("codapos1","'",urldecode(str_replace("persaccento","'",str_replace(array("\n","\r"), "",$unserialize['titolosito']))));
  $titolodefinitivo=str_replace("codapos2","&quot;",$titolodefinitivo);
  $titolodefinitivo=html_entity_decode($titolodefinitivo);
  $descrizionedefinitiva=$funclass->resetslash(str_replace("&apos;","'",urldecode($unserialize['descrizione'])));
  $descrizionedefinitiva=str_replace("ibdwbackslashibdwbackslash","&#92;",$descrizionedefinitiva);
  $descrizionedefinitiva=str_replace("codapos2","&quot;",$descrizionedefinitiva);
  echo '<div class="webint'.$assegnazione.'" id="descrizione"><a target="_blank" href="'.$indirizzourl.'"><h3>';
  echo $titolodefinitivo;
  echo '</h3></a><p class="stylewebsite"><a href="'.$indirizzourl.'" target="_blank" >'.$indirizzourl.'</a></p><p>'.$descrizionedefinitiva.'</p></div><div class="clear"></div>';
  echo '</div>';
  include ($commentsarea);
  if($verifica_partent!=0) echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
 }
 else $tmpx++;
}
//END

// PERSONAL MESSAGE
elseif (($row['lang_key']=='_ibdw_evowall_bx_evowall_message') OR ($row['lang_key']=='_ibdw_evowall_bx_evowall_messageseitu')) 
{
 if ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowview'))
 {
  if($row['lang_key']=='_ibdw_evowall_bx_evowall_messageseitu')
  {
   echo $parteintroduttiva;
   echo '<div id="messaggio"><div id="'.$idn.'" class="zerz"></div><div id="primariga">';
   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
   {
    echo $sharebutts;
    include 'rem_ove.php';
    echo '</div>';
    include 'bt_delete.php';
   }
   $parmessaggio=$unserialize['messaggioo'];
   $parmessaggio=$funclass->urlreplace($parmessaggio);
   $parmessaggio=str_replace("`","'",$parmessaggio);
   $parmessaggio=strip_tags($parmessaggio,'<br/><a><br>');
   if ($newline=="") $parmessaggio=str_replace("<br/>"," ",$parmessaggio);
   echo '<a class="usernamestyle" href="'.getUsername($row['sender_id']).'">';
   echo getNickname($row['sender_id']);
   echo '</a>';
   echo '<div id="data">'.$miadata;
   include('like.php');
   echo '</div></div></div>';
   echo '<div class="persmess">'.$parmessaggio.'</div>';  
  }
  else 
  {
   echo $parteintroduttiva;
   echo '<div id="messaggio"><div id="primariga">';
   if (($inviatore==$accountid OR isAdmin() OR isModerator()) AND $accountid!=0) 
   {
    echo $sharebutts;
    include 'rem_ove.php';
    echo '</div>';
    include 'bt_delete.php';
   }
   $parmessaggio=$unserialize['messaggioo'];
   $parmessaggio=strip_tags($parmessaggio,'<br/><a>');
   $parmessaggio=$funclass->urlreplace($parmessaggio);
   $parmessaggio=str_replace("`","'",$parmessaggio);
   if ($newline=="") $parmessaggio=str_replace("<br/>"," ",$parmessaggio);
   echo '<a class="usernamestyle" href="'.getUsername($row['sender_id']).'">';
   echo getNickname($row['sender_id']);
   echo '</a><i class="sys-icon chevron-right" alt=""></i>';
   echo '<a class="usernamestyle" href="'.getUsername($row['recipient_id']).'">';
   echo getNickname($row['recipient_id']);
   echo'</a>';
   echo '<div id="data">'.$miadata;
   include('like.php');
   echo '</div></div></div>';
   echo '<div class="persmess">'.$parmessaggio.'</div>';  
  }
  include ($commentsarea);
  if($verifica_partent!=0)  echo hiddenlink_foto($row['id'],$row['date'],$GLOBAL['ultimoid'],$row['lang_key'],$row['recordsfound'],$row['sender_id'],$pagina);
  echo '</div>';
  if($verifica_partent!=0) echo '<div id="downtown'.$row['id'].'"></div>';
 }
 else $tmpx++;
}

} //end of check profile is blocked or unconfirmed (if unconfirmed must be blocked)

//END of all posts


echo '<div class="clear_both"></div>';

}//while
//div listanotizie

if(!isset($hidden_intro)) echo '</div>';  
if(isset($typeoforder)) $typeoforder=$typeoforder;
else 
{
 if(isset($_COOKIE["typeoforder"]))
 {
  if($_COOKIE["typeoforder"]=='1') $typeoforder="Popular";
  else $typeoforder="";
 }
 else $typeoforder="";
}
?>
<script>
function delaycustom() {ajax_load_close();}
function agg_ajax() 
{
 ajax_load_active();
 <?php
  if(!isset($ultimoid)) $ultimoid=$GLOBAL['ultimoid'];
  echo 'verificaupdate('.$ultimoid.',\''.$pagina.'\',\''.$contanews.'\');';
 ?> 
 setTimeout('delaycustom()',2000);
}
<?php
if ($typeoforder!="Popular" AND $miapag!='profile' AND !isset($_GET['id_mode'])) {
?>
window.onfocus=function () 
{
 tempodelta= new Date().getTime();
 if (((tempodelta-tempoinit)><?php echo $refreshtime;?>) && (<?php echo $refreshtype;?>='Auto'))
 {
  <?php
  if(!isset($ultimoid)) $ultimoid=$GLOBAL['ultimoid'];
  echo 'verificaupdate('.$ultimoid.',\''.$pagina.'\',\''.$contanews.'\');';?>
  tempoinit= new Date().getTime();
 }
}
<?php } ?>

function verificaupdate(ultimoid,pagina,contanews) {
    $.ajax({
      type: 'POST',
      url: 'modules/ibdw/evowall/verificaupdate.php',
      data: "ultimoid=" + ultimoid + "&pagina=" + pagina + "&contanews=" + contanews + "&idrichiesto=<?php echo $accountid;?>",
      success: function(html){
      $('#updateajax').prepend(html);
      $('#updateajax').fadeIn(1000);
      }
    });
}
function aggiornabottonealtrenews(contanews,limite,pagina,idrichiesto,ultimoid) {
  $.ajax({
      url: 'modules/ibdw/evowall/bt_more_news.php',
      type: 'POST',
      data: "contanews=" + contanews + "&limite=" + limite + "&pagina=" + pagina + "&idrichiesto=" + idrichiesto+ "&ultimoid=" + ultimoid,
      success: function(data) {
            $('#altro').html(data);
      }
    });
}
$(document).ready(function(){
			$("#mstyle").emoticonize({
				//delay: 800,
				//animate: false,
				//exclude: 'pre, code, .no-emoticons
			});
      
      
      $(".textual").emoticonize({
				//delay: 800,
				//animate: false,
				//exclude: pre, code, .no-emoticons
			});
      
			$("#toggle-headline").toggle(
				function(){
					$("#large").unemoticonize({
						//delay: 800,
						//animate: false
					})
				}, 
				function(){
					$("#large").emoticonize({
						//delay: 800,
						//animate: false
					})
				}
			);
		})
</script>