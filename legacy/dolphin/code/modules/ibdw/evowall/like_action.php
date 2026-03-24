<?php
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
mysql_query("SET NAMES 'utf8'");
include 'templatesw.php';
require_once (BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/functions.php');
$funclass=new swfunc();
$accountid=(int)$_COOKIE['memberID'];
$user=(int)$_POST['user'];
$idnotizia=(int)$_POST['id'];
$set=(int)$_POST['set'];
$pagina=$_POST['pagina'];
if($set==0)
{
 $controllo="SELECT id_notizia FROM ibdw_likethis WHERE id_notizia='".$idnotizia."' AND id_utente=".$user;
 $esegui=mysql_query($controllo);
 $numeri=mysql_num_rows($esegui);
 if ($user==$accountid AND $numeri==0) 
 {
  $query="INSERT INTO ibdw_likethis (id_notizia,id_utente,ibdw_likethis.like) VALUES ('".$idnotizia."', '".$user."', '1')";
  $result=mysql_query($query); 
  $queryc="UPDATE bx_spy_data SET PostLikeN=PostLikeN+1 WHERE id=".$idnotizia;
  $resultc=mysql_query($queryc);
  $proprietario="SELECT id,sender_id,params,lang_key FROM bx_spy_data WHERE id='".$idnotizia."'";
  $esegui=mysql_query($proprietario);
  $fetch=mysql_fetch_assoc($esegui);
  $newpro=$fetch['sender_id'];
  $aInfomember2=getProfileInfo($newpro);
  $miosendername=getNickname($accountid);
  $miorecipientname=getNickname($newpro);
  $miosendernickname=getUsername($accountid);
  $miorecipientnickname=getUsername($newpro);
  if 
  (
   $fetch['lang_key']=='_bx_photos_spy_added' 
   OR $fetch['lang_key']=='_bx_videos_spy_added' 
   OR $fetch['lang_key']=='_bx_poll_added'
   OR $fetch['lang_key']=='_bx_groups_spy_post'
   OR $fetch['lang_key']=='_bx_pages_spy_post' 
   OR $fetch['lang_key']=='_bx_events_spy_post' 
   OR $fetch['lang_key']=='_bx_sites_poll_add'
   OR $fetch['lang_key']=='_bx_ads_added_spy' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_photo_add_condivisione' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_video_add_condivisione'
   OR $fetch['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_gruppo_add_condivisione'
   OR $fetch['lang_key']=='_ibdw_evowall_bx_pagina_add_condivisione'
   OR $fetch['lang_key']=='_ibdw_evowall_bx_event_add_condivisione' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_site_add_condivisione' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_ads_add_condivisione'
   OR $fetch['lang_key']=='_ibdw_evowall_bx_blogs_add_condivisione' 
   OR $fetch['lang_key']=='_bx_blog_added_spy' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_sounds_add_condivisione' 
   OR $fetch['lang_key']=='_bx_sounds_spy_added'
   OR $fetch['lang_key']=='_ue30_event_spy_post' 
   OR $fetch['lang_key']=='_ue30_event_add_condivisione'
   OR $fetch['lang_key']=='_ibdw_evowall_bx_url_share'
   OR $fetch['lang_key']=='_ibdw_evowall_bx_url_add'
   OR $fetch['lang_key']=='_ue30_location_spy_post' 
   OR $fetch['lang_key']=='_ibdw_evowall_ue30_locations_add_condivisione'
   OR $fetch['lang_key']=='_modzzz_property_spy_post' 
   OR $fetch['lang_key']=='_ibdw_evowall_modzzz_property_share'
   OR $fetch['lang_key']=='_modzzz_club_spy_post' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_club_add_condivisione'
   OR $fetch['lang_key']=='_modzzz_petitions_spy_post' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_petition_add_condivisione'
   OR $fetch['lang_key']=='_modzzz_bands_spy_post' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_band_add_condivisione'
   OR $fetch['lang_key']=='_modzzz_pets_spy_post' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_pet_add_condivisione'
   OR $fetch['lang_key']=='_modzzz_schools_spy_post' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_school_add_condivisione'
   OR $fetch['lang_key']=='_modzzz_notices_spy_post' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_notice_add_condivisione'
   OR $fetch['lang_key']=='_modzzz_classified_spy_post' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_classified_add_condivisione'
   OR $fetch['lang_key']=='_modzzz_news_spy_post' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_news_add_condivisione'
   OR $fetch['lang_key']=='_modzzz_jobs_spy_post' 
     OR $fetch['lang_key']=='_ibdw_evowall_bx_job_add_condivisione'
    OR $fetch['lang_key']=='_modzzz_articles_spy_post' 
    OR $fetch['lang_key']=='_ibdw_evowall_bx_article_add_condivisione'
	
	 OR $fetch['lang_key']=='_modzzz_formations_spy_post' 
    OR $fetch['lang_key']=='_ibdw_evowall_bx_formation_add_condivisione'
	
	 OR $fetch['lang_key']=='_modzzz_investment_spy_post' 
    OR $fetch['lang_key']=='_ibdw_evowall_bx_investment_add_condivisione'
	
	
   OR $fetch['lang_key']=='_modzzz_listing_spy_post' 
   OR $fetch['lang_key']=='_ibdw_evowall_bx_listing_add_condivisione'
   OR $fetch['lang_key']=='_modzzz_polls_spy_post'
   OR $fetch['lang_key']=='_ibdw_evowall_modzzz_poll_add_condivisione'
  )
  {
    $unserialize=unserialize($fetch['params']);
    $url_specifico=1;	
    if($fetch['lang_key']=='_bx_photos_spy_added' OR $fetch['lang_key']=='_ibdw_evowall_bx_photo_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_photo'; $url_element = $unserialize['entry_url']; }
    elseif($fetch['lang_key']=='_bx_videos_spy_added'  OR $fetch['lang_key']=='_ibdw_evowall_bx_video_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_video'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_bx_poll_added' OR $fetch['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_poll'; $url_element = $unserialize['poll_url'];}
    elseif($fetch['lang_key']=='_bx_groups_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_gruppo_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_groups'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_bx_pages_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_pagina_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_pages'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_bx_events_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_event_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_events'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_bx_sites_poll_add' OR $fetch['lang_key']=='_ibdw_evowall_bx_site_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_sites'; $url_element = $unserialize['site_url'];}                   
    elseif($fetch['lang_key']=='_bx_ads_added_spy' OR $fetch['lang_key']=='_ibdw_evowall_bx_ads_add_condivisione') { $lang_string = '_ibdw_evowall_notify_like_ads'; $url_element = $unserialize['ads_url'];}
	  elseif($fetch['lang_key']=='_ibdw_evowall_bx_blogs_add_condivisione' OR $fetch['lang_key']=='_bx_blog_added_spy') {$lang_string='_ibdw_evowall_notify_like_blogs'; $url_element = $unserialize['post_url'];}
    elseif($fetch['lang_key']=='_ibdw_evowall_bx_sounds_add_condivisione' OR $fetch['lang_key']=='_bx_sounds_spy_added') {$lang_string='_ibdw_evowall_notify_like_sounds'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_ue30_event_spy_post' OR $fetch['lang_key']=='_ue30_event_add_condivisione') {$lang_string = '_ibdw_evowall_notify_like_events'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_ue30_location_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_ue30_locations_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_ue30location'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_modzzz_property_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_modzzz_property_share') {$lang_string='_ibdw_evowall_notify_like_modzzz_property'; $url_element = $unserialize['entry_url'];}
	  elseif($fetch['lang_key']=='_modzzz_club_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_club_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_clubs'; $url_element = $unserialize['entry_url'];}
    elseif($fetch['lang_key']=='_modzzz_petitions_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_petition_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_petitions'; $url_element = $unserialize['entry_url'];}
	  elseif($fetch['lang_key']=='_modzzz_bands_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_band_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_bands'; $url_element = $unserialize['entry_url'];}
	  elseif($fetch['lang_key']=='_modzzz_pets_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_pet_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_pets'; $url_element = $unserialize['entry_url'];}
	  elseif($fetch['lang_key']=='_modzzz_schools_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_school_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_schools'; $url_element = $unserialize['entry_url'];}
	  elseif($fetch['lang_key']=='_modzzz_notices_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_notice_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_notices'; $url_element = $unserialize['entry_url'];}
	  elseif($fetch['lang_key']=='_modzzz_classified_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_classified_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_classified'; $url_element = $unserialize['entry_url'];}
	  elseif($fetch['lang_key']=='_modzzz_news_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_news_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_news'; $url_element = $unserialize['entry_url'];}
  	elseif($fetch['lang_key']=='_modzzz_jobs_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_job_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_jobs'; $url_element = $unserialize['entry_url'];}
	
	elseif($fetch['lang_key']=='_modzzz_articles_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_article_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_articles'; $url_element = $unserialize['entry_url'];}
	
	
	elseif($fetch['lang_key']=='_modzzz_formations_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_formation_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_formations'; $url_element = $unserialize['entry_url'];}
	
	
	elseif($fetch['lang_key']=='_modzzz_investment_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_investment_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_investment'; $url_element = $unserialize['entry_url'];}
	
	  elseif($fetch['lang_key']=='_modzzz_listing_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_bx_listing_add_condivisione') {$lang_string='_ibdw_evowall_notify_like_modzzz_listing'; $url_element = $unserialize['entry_url'];}
	  elseif($fetch['lang_key']=='_ibdw_evowall_bx_url_share' OR $fetch['lang_key']=='_ibdw_evowall_bx_url_add') {$lang_string='_ibdw_evowall_notify_like_url'; $url_element = $unserialize['indirizzo'];}
    elseif($fetch['lang_key']=='_modzzz_polls_spy_post' OR $fetch['lang_key']=='_ibdw_evowall_modzzz_poll_add_condivisione') {$lang_string='_ibdw_evowall_modzzz_notify_like_poll'; $url_element = $unserialize['entry_url'];}
  } 
  else {$lang_string='_ibdw_evowall_notify_like';$url_specifico=0;}
  $array["sender_p_link"]= BX_DOL_URL_ROOT.$miosendernickname;
  $array["sender_p_nick"]= $miosendername;
  $array["recipient_p_link"]=BX_DOL_URL_ROOT.$miorecipientnickname;
  $array["recipient_p_nick"]=$miorecipientname;
  if($url_specifico==1) $array["url"]=$url_element;
  $str=serialize($array);	
  if($accountid!=$newpro) 
  {
   $query_wall="INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params,type) VALUES ('".$accountid."','".$newpro."','".$lang_string."','".$str."','profiles_activity')";
   $result_wall=mysql_query($query_wall);
  }
  if($AllowLikeNotification=="on" and $accountid<>$newpro)
  {
   //invio email
   if(isset($_SERVER['HTTPS'])) 
   {
    if ($_SERVER['HTTPS'] == "on") {
        $protocol = 'https';
    }
    else $protocol = 'http';
   }
   if ($protocol =='') $protocol = 'http';
   $pageaddress=$protocol."://".$_SERVER['HTTP_HOST'].$pagina."?id_mode=".$idnotizia;
   $domainis=$GLOBALS['site']['url'];
   bx_import('BxDolEmailTemplates');
   $oEmailTemplate = new BxDolEmailTemplates();
   $aTemplate = $oEmailTemplate -> getTemplate($lang_string);
   $usermailadd=trim($aInfomember2['Email']);
   $aTemplate['Body']=str_replace('<SenderNickName>',$miosendername,$aTemplate['Body']);
   $aTemplate['Body']=str_replace('<RecipientNickName>',$miorecipientname,$aTemplate['Body']);
   $aTemplate['Body']=str_replace('{post}',$pageaddress,$aTemplate['Body']);
   $aTemplate['Body']=str_replace('<SiteName>',$sitenameis,$aTemplate['Body']);
   $aTemplate['Subject']=str_replace('<SenderNickName>',$miosendername,$aTemplate['Subject']);
   if ($aInfomember2['EmailNotify']==1) sendMail($usermailadd, $aTemplate['Subject'], $aTemplate['Body'], $newpro, 'html');
   //fine invio email
  }
 }
}
elseif($set==1)
{
 $query="DELETE FROM ibdw_likethis WHERE id_notizia='".$idnotizia."' AND id_utente='".$user."'";
 $result=mysql_query($query);
 $queryc="UPDATE bx_spy_data SET PostLikeN=PostLikeN-1 WHERE id='".$idnotizia."'";
 $resultc=mysql_query($queryc);
}
elseif($set==2)
{
 $assegnazione=$idnotizia;
 $querylike="SELECT id_utente FROM ibdw_likethis WHERE id_notizia='".$assegnazione."'";
 $querylikeresult=mysql_query($querylike);
 $rowquerylikeconta=mysql_num_rows($querylikeresult);
 if ($rowquerylikeconta>2)
 {
  echo '<div id="element_like'.$assegnazione.'" class="elementlike">
         <i class="sys-icon thumbs-up"></i>
          <a href="javascript:slide_persone'.$assegnazione.'();">'.$rowquerylikeconta.' '._t("_ibdw_evowall_elementlike1").'</a> '._t("_ibdw_evowall_elementlike2").'
           <div id="slide_persone'.$assegnazione.'" class="slide_persone">
            <input type="hidden" id="slide_up'.$assegnazione.'" value="0">';
  while($rowquerylike = mysql_fetch_array($querylikeresult))
  {
   echo '<div id="contbox_user_like"><div id="avat_like_box">';
	 echo get_member_icon($rowquerylike['id_utente'],'none',false);
	 echo '</div><div id="user_like_box"><a href="'.getUsername($rowquerylike['id_utente']).'" class="friend_like_name">'.getNickname($rowquerylike['id_utente']).'</a>';
   if (is_friends($accountid,$rowquerylike['id_utente'])==FALSE AND $rowquerylike['id_utente']!=$accountid) 
   {
    if (getMutualFriendsCount($accountid,$rowquerylike['id_utente'])>0) 
	  {
		 if (getMutualFriendsCount($accountid,$rowquerylike['id_utente'])>1) echo '<div class="box_likemutual"> '.getMutualFriendsCount($accountid,$rowquerylike['id_utente']).' '._t("_ibdw_evowall_mutualfriend").'</div>';
		 else echo '<div class="box_likemutual">'.getMutualFriendsCount($accountid,$rowquerylike['id_utente']).' '._t("_ibdw_evowall_mutualfriendonlyone").'</div>';
		}
	  else echo '<div class="box_likemutual"></div>'; 
   }
   $idutenteidx = $rowquerylike['id_utente'];
   if(is_friends($accountid,$rowquerylike['id_utente'])==FALSE AND $rowquerylike['id_utente']!=$accountid) 
   { 
    //controlla che la richiesta non sia inoltrata    
    $query="SELECT ID,Profile,sys_friend_list.Check FROM sys_friend_list WHERE ID='".$accountid."' AND Profile='".$idutenteidx."' AND sys_friend_list.Check=0";
    $esegui=mysql_query($query);
    $verifica=mysql_num_rows($esegui);
    if($verifica!=0) echo '<div class="box_aggiungilike" id="box_aggiungilike'.$idutenteidx.'">'._t("_ibdw_evowall_friendlike2").'</div>';
    else echo '<div class="box_aggiungilike" id="box_aggiungilike'.$idutenteidx.'"><i class="sys-icon user"></i><a href="javascript:aggiungi_friend'.$rowquerylike['id_utente'].'('.$idutenteidx.')">'._t("_ibdw_evowall_friendlike").'</a></div>';
   }
   echo '</div></div>
  	<script>
		 function slide_persone'.$assegnazione.'() {
		 var slide_up = $("#slide_up'.$assegnazione.'").val();
		 if(slide_up==0){
     $("#slide_persone'.$assegnazione.'").fadeIn(1);
     $("#slide_up'.$assegnazione.'").val(1);
     }
     else {
     $("#slide_persone'.$assegnazione.'").css(\'display\',\'none\');
     $("#slide_up'.$assegnazione.'").val(0);
     }
     }
		</script>
		<script>
		 function aggiungi_friend'.$rowquerylike['id_utente'].'(idevento) 
		 {
		  var id_user='.$rowquerylike['id_utente'].';
		  $.ajax({ type: "POST", url: "modules/ibdw/evowall/add_sugg.php", data: "id_user=" + id_user, success: function(html){ notifica_generale("'._t("_ibdw_evowall_notificalikefriend").'"); $("#box_aggiungilike"+idevento).fadeOut();}});
		 };
		</script>';
  }
  echo '</div></div>';
 } 
 elseif($rowquerylikeconta>0)
 {
  $contapiace=0;
  echo '<div id="element_like'.$assegnazione.'" class="elementlike"><i class="sys-icon thumbs-up"></i> ';
  while($rowquerylike = mysql_fetch_array($querylikeresult))
  {
   if (($rowquerylikeconta == 1) AND ($rowquerylike['id_utente'] == $accountid )) {echo _t("_ibdw_evowall_ilikeit");}
   else 
   {
    $contapiace=$contapiace+1;
    if($contapiace==1) echo ' '._t("_ibdw_evowall_thiselementlike").' ';
    if ($contapiace>1) echo ' '._t("_ibdw_evowall_likeand").' ';
    if($rowquerylike['id_utente']==$accountid) { echo '<a href="'.getUsername($rowquerylike['id_utente']).'"> '._t("_ibdw_evowall_likeyou").' </a>';}
    else {echo '<a href="'.getUsername($rowquerylike['id_utente']).'">'.getNickname($rowquerylike['id_utente']).' </a>';}
   }
  }
  echo '</div>';
 }
}
elseif($set==3)
{ 
 $assegnazione=$idnotizia;
 $querylikecontrollo="SELECT id_notizia,id_utente FROM ibdw_likethis WHERE id_notizia='$assegnazione' AND id_utente='$accountid'";
 $querylikecontrolloresult=mysql_query($querylikecontrollo);
 $rowquerylikecontrollo=mysql_num_rows($querylikecontrolloresult);
 if($rowquerylikecontrollo>=1) 
 {
  echo '<div id="nolike'.$assegnazione.'" class="nolike">
 		     <form name="likethis'.$assegnazione.'" id="nlikethis'.$assegnazione.'" action="javascript:raffa();">
		      <input id="nid_like'.$assegnazione.'" type="hidden" name="id" value="'. $assegnazione.'">
		      <input id="nuser_like'.$assegnazione.'" type="hidden" name="user" value="'. $accountid.'">
		      <input type="submit" value="'._t("_ibdw_evowall_unlikethis").'">
		     </form>
	      </div>
	      <script>
	      $("#nlikethis'.$assegnazione.'").submit(function() 
	      {
	       var nid_like=$("#nid_like'.$assegnazione.'").attr("value");
		     var nuser_like=$("#nuser_like'.$assegnazione.'").attr("value");
				 var pagina=$("#pagina'.$assegnazione.'").attr("value");
		     $.ajax({type: "POST", url: "modules/ibdw/evowall/like_action.php", data: "id=" + nid_like +"&user=" + nuser_like +"&set=1&pagina="+pagina,
		     success: function(html){ajax_like_riepilogo'.$assegnazione.'();}});
        });
	      </script>';
 }
 else
 {
  echo '<div id="like'.$assegnazione.'" class="boxlike">
 		     <form name="likethis'.$assegnazione.'" id="likethis'.$assegnazione.'" action="javascript:raffa();">
		      <input id="id_like'.$assegnazione.'" type="hidden" name="id" value="'. $assegnazione.'">
		      <input id="user_like'.$assegnazione.'" type="hidden" name="user" value="'. $accountid.'">
		      <input type="submit" value="'._t("_ibdw_evowall_likethis").'">
		     </form>
	      </div>
	      <script>
	       $("#likethis'.$assegnazione.'").submit(function() 
	       {
	        var id_like=$("#id_like'.$assegnazione.'").attr("value");
		      var user_like=$("#user_like'.$assegnazione.'").attr("value");
				  var pagina=$("#pagina'.$assegnazione.'").attr("value");
		      $.ajax({type: "POST", url: "modules/ibdw/evowall/like_action.php", data: "id=" + id_like +"&user="+user_like+"&set=0&pagina="+pagina,
		      success: function(html){ajax_like_riepilogo'.$assegnazione.'();}});
         });
	       </script>';
 }
}
?>