<?php
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
mysql_query("SET NAMES 'utf8'");
require_once (BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/functions.php');
$accountid=(int)$_COOKIE['memberID'];
$pagina=$_POST['pagina'];
//id comment owner
$idwriter=(int)$_POST['writer'];
//id post owner
$idowner=(int)$_POST['owner'];
//id action
$assegnazione=(int)$_POST['idaction'];
//comment content
$commento=mysql_real_escape_string($_POST['commento']);
if ($badwords=="on") 
{
  $funclass=new swfunc();
  $commento=$funclass->ReplaceBadWords($commento,$wordlist);
}

$funclass=new swfunc();

//particular lang key for particular comments messages
$vectlk=array('_bx_photos_spy_added','_bx_videos_spy_added','_bx_poll_added','_bx_groups_spy_post','_bx_events_spy_post','_bx_sites_poll_add','_bx_ads_added_spy','_ibdw_evowall_bx_photo_add_condivisione' 
  ,'_ibdw_evowall_bx_video_add_condivisione','_ibdw_evowall_bx_url_share','_ibdw_evowall_bx_url_add','_ibdw_evowall_bx_poll_add_condivisione','_ibdw_evowall_bx_gruppo_add_condivisione'
  ,'_ibdw_evowall_bx_pagina_add_condivisione','_ibdw_evowall_bx_event_add_condivisione','_ibdw_evowall_bx_site_add_condivisione','_ibdw_evowall_bx_ads_add_condivisione'
  ,'_ibdw_evowall_bx_blogs_add_condivisione','_bx_blog_added_spy','_ibdw_evowall_bx_sounds_add_condivisione','_bx_sounds_spy_added','_ue30_event_spy_post','_ue30_event_add_condivisione'
  ,'_ue30_location_spy_post','_ibdw_evowall_ue30_locations_add_condivisione','_modzzz_property_spy_post','_ibdw_evowall_modzzz_property_share','_modzzz_club_spy_post' 
  ,'_ibdw_evowall_bx_club_add_condivisione','_modzzz_petitions_spy_post','_ibdw_evowall_bx_petition_add_condivisione','_modzzz_bands_spy_post','_ibdw_evowall_bx_band_add_condivisione'
  ,'_modzzz_pets_spy_post','_ibdw_evowall_bx_pet_add_condivisione','_modzzz_schools_spy_post','_ibdw_evowall_bx_school_add_condivisione','_modzzz_notices_spy_post','_ibdw_evowall_bx_notice_add_condivisione'
  ,'_modzzz_classified_spy_post','_ibdw_evowall_bx_classified_add_condivisione','_modzzz_news_spy_post','_ibdw_evowall_bx_news_add_condivisione','_modzzz_jobs_spy_post','_ibdw_evowall_bx_job_add_condivisione'
  ,'_modzzz_polls_spy_post','_ibdw_evowall_modzzz_poll_add_condivisione'
  ,'_modzzz_articles_spy_post','_ibdw_evowall_bx_article_add_condivisione'
  
  ,'_modzzz_formations_spy_post','_ibdw_evowall_bx_formation_add_condivisione'
  ,'_modzzz_investment_spy_post','_ibdw_evowall_bx_investment_add_condivisione'
  
  ,'_modzzz_listing_spy_post','_ibdw_evowall_bx_listing_add_condivisione')
  ;

$infoMember=getMemberMembershipInfo($accountid);
$profilemembership=$infoMember['ID'];
$limitecommento=$commnum;
if ($idwriter==$accountid) 
{
 $commentopost=str_replace("'", "`", $commento);
 $queryinfo="SELECT sender_id FROM bx_spy_data WHERE id=".(int)$assegnazione;
 $resultinfo=mysql_query($queryinfo);
 $rowsenderid=mysql_fetch_row($resultinfo);
 $query="INSERT INTO commenti_spy_data (data,user,commento) VALUES ('".(int)$assegnazione."', '".(int)$accountid."', '".$commentopost."')";
 $result=mysql_query($query);
 $newid=mysql_insert_id(); 
 $queryc="UPDATE bx_spy_data SET PostCommentsN=PostCommentsN+1 WHERE id=".(int)$assegnazione;
 $resultc=mysql_query($queryc);
 $controllo_prop="SELECT sender_id,params,lang_key FROM bx_spy_data WHERE id = ".(int)$assegnazione." LIMIT 1";
 $execontrollo_prop=mysql_query($controllo_prop);
 $fetch_assoc=mysql_fetch_assoc($execontrollo_prop);
 $sender_id_prop=$fetch_assoc['sender_id']; 
 $miosenderlink=getUsername($accountid);
 $miosendernick=getNickname($accountid);
 $miorecipientlink=getUsername($sender_id_prop);
 $miorecipientnick=getNickname($sender_id_prop);
 if (in_array($fetch_assoc['lang_key'],$vectlk))
 {
  $unserialize=unserialize($fetch_assoc['params']);
  $url_specifico=1;	
  if($fetch_assoc['lang_key']=='_bx_photos_spy_added' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_photo_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_photo'; $url_element=$unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_bx_videos_spy_added' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_video_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_video'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_bx_poll_added' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_poll'; $url_element = $unserialize['poll_url'];}
  elseif($fetch_assoc['lang_key']=='_bx_groups_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_gruppo_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_groups'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_bx_pages_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_pagina_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_pages'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_bx_events_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_event_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_events'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_bx_sites_poll_add' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_site_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_sites'; $url_element = $unserialize['site_url'];}                   
  elseif($fetch_assoc['lang_key']=='_bx_ads_added_spy' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_ads_add_condivisione') { $lang_string = '_ibdw_evowall_notify_comment_ads'; $url_element = $unserialize['ads_url'];}
  elseif($fetch_assoc['lang_key']=='_ibdw_evowall_bx_blogs_add_condivisione' OR $fetch_assoc['lang_key']=='_bx_blog_added_spy') {$lang_string='_ibdw_evowall_notify_comment_blogs'; $url_element = $unserialize['post_url'];}
  elseif($fetch_assoc['lang_key']=='_ibdw_evowall_bx_sounds_add_condivisione' OR $fetch_assoc['lang_key']=='_bx_sounds_spy_added') {$lang_string='_ibdw_evowall_notify_comment_sounds'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_ue30_event_spy_post' OR $fetch_assoc['lang_key']=='_ue30_event_add_condivisione') {$lang_string = '_ibdw_evowall_notify_comment_events'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_ue30_location_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_ue30_locations_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_ue30location'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_property_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_modzzz_property_share') {$lang_string='_ibdw_evowall_notify_comment_modzzz_property'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_club_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_club_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_clubs'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_petitions_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_petition_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_petitions'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_bands_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_band_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_bands'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_pets_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_pet_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_pets'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_schools_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_school_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_schools'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_notices_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_notice_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_notices'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_classified_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_classified_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_classified'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_news_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_news_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_news'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_jobs_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_job_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_jobs'; $url_element = $unserialize['entry_url'];}
  
  
   elseif($fetch_assoc['lang_key']=='_modzzz_formations_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_formation_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_formations'; $url_element = $unserialize['entry_url'];}
   
   
    elseif($fetch_assoc['lang_key']=='_modzzz_investment_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_investment_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_investment'; $url_element = $unserialize['entry_url'];}
	
	
  
  elseif($fetch_assoc['lang_key']=='_modzzz_articles_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_article_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_articles'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_listing_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_listing_add_condivisione') {$lang_string='_ibdw_evowall_notify_comment_modzzz_listing'; $url_element = $unserialize['entry_url'];}
  elseif($fetch_assoc['lang_key']=='_ibdw_evowall_bx_url_share' OR $fetch_assoc['lang_key']=='_ibdw_evowall_bx_url_add') {$lang_string='_ibdw_evowall_notify_comment_url'; $url_element = $unserialize['indirizzo'];}
  elseif($fetch_assoc['lang_key']=='_modzzz_polls_spy_post' OR $fetch_assoc['lang_key']=='_ibdw_evowall_modzzz_poll_add_condivisione') {$lang_string='_ibdw_evowall_modzzz_notify_comment_poll'; $url_element = $unserialize['entry_url'];}
 } 
 else 
 {
  $lang_string='_ibdw_evowall_notify_comment';
  $url_specifico=0;
 }
 $array["sender_p_link"]=BX_DOL_URL_ROOT.$miosenderlink;
 $array["sender_p_nick"]=$miosendernick;
 $array["recipient_p_link"]=BX_DOL_URL_ROOT.$miorecipientlink;
 $array["recipient_p_nick"]=$miorecipientnick;
 if($url_specifico==1) $array["url"]=$url_element;
 $str=serialize($array);
 if($accountid!=$sender_id_prop)
 {
  $query_wall="INSERT INTO bx_spy_data (sender_id,recipient_id,lang_key,params,type) VALUES ('".(int)$accountid."','".(int)$sender_id_prop."','".$lang_string."','".$str."','profiles_activity')";
  $result_wall=mysql_query($query_wall);
 }
 $query2="INSERT INTO datacommenti (IDCommento) VALUES ('".(int)$newid."')";
 $result2=mysql_query($query2);
 $proprietario="SELECT id,sender_id FROM bx_spy_data WHERE id='".(int)$assegnazione."'";
 $esegui=mysql_query($proprietario);
 $fetch=mysql_fetch_assoc($esegui);
 $newpro=$fetch['sender_id'];
 $parametriass=$assegnazione.'###'.$newid;
 if($newpro!=$accountid and $AllowCommentNotification=="on") 
 {
  //invio email
  $senderemail=$idwriter;
  $recipientemail=$rowsenderid[0];
  if(isset($_SERVER['HTTPS'])) 
  {
    if ($_SERVER['HTTPS'] == "on") {
        $protocol = 'https';
    }
    else $protocol = 'http';
  }
  if ($protocol =='') $protocol = 'http';
  $pageaddress=$protocol."://".$_SERVER['HTTP_HOST'].$_POST['pagina']."?id_mode=".$assegnazione;
  $sitenameis=getParam('site_title');
  bx_import('BxDolEmailTemplates');
  $oEmailTemplate = new BxDolEmailTemplates();
  $aTemplate=$oEmailTemplate -> getTemplate($lang_string);
  $infoamico=getProfileInfo($recipientemail);
  $usermailadd=trim($infoamico['Email']);
  $execactionname=getNickname($recipientemail);
  $authorname=getNickname($senderemail);
  $aTemplate['Body']=str_replace('<SenderNickName>',$authorname,$aTemplate['Body']);
  $aTemplate['Body']=str_replace('<RecipientNickName>',$execactionname,$aTemplate['Body']);
  $aTemplate['Body']=str_replace('{post}',$pageaddress,$aTemplate['Body']);
  $aTemplate['Body']=str_replace('<SiteName>',$sitenameis,$aTemplate['Body']);
  $aTemplate['Subject']=str_replace('<SenderNickName>',$authorname,$aTemplate['Subject']);
  if ($infoamico['EmailNotify']==1) sendMail($usermailadd, $aTemplate['Subject'], $aTemplate['Body'], $recipientemail, 'html');
   //fine invio email
 }
}
include 'templatesw.php';
if(($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) and ($funclass->checkprivacyevo($idowner,$accountid,'allowcomment')))
{
 echo '<script>
		function altricommenti'.$assegnazione.'(assegnazione,limitecommento,pagina) 
		{
		 $("#swich_comment'.$assegnazione.' .othnews").css({"background-image" : "url('.$imagepath.'load.gif)" , "background-repeat" : "no-repeat" , "background-position" : "left","padding-left" : "20px"});
		 $.ajax({type: "POST",url: "modules/ibdw/evowall/morecomments.php",data: "assegnazione=" + assegnazione + "&limitecommento=" + limitecommento + "&pagina=" + pagina,
		 success: function(html)
		 			{
					 $("#commenti'.$assegnazione.'").empty();
	         $("#commenti'.$assegnazione.'").append(html);
           $(".textual").emoticonize();
					}
		});
		};
		</script>';
		//ottengo la data di pubblicazione del post per confrontarla con le date dei commenti che devono essere successivi al post ovviamente
        $querydatapost="SELECT bx_spy_data.date FROM bx_spy_data WHERE ID='".(int)$assegnazione."'";
        $resultdatapost=mysql_query($querydatapost);
        $valdata=mysql_fetch_assoc($resultdatapost);

		$querycontacommenti="SELECT commenti_spy_data.data FROM commenti_spy_data LEFT JOIN datacommenti ON commenti_spy_data.id=datacommenti.IDCommento WHERE commenti_spy_data.data='".(int)$assegnazione."' and datacommenti.date>'".$valdata['date']."'";
	  $resultcontacommenti=mysql_query($querycontacommenti);
		$rowcontacommenti=mysql_num_rows($resultcontacommenti);
		if ($rowcontacommenti>0)
		{
		 if ($rowcontacommenti==1) {$titlecommentis=_t('_ibdw_evowall_comment_title1');$endcomment=_t('_ibdw_evowall_comment_titlef1');}
		 else {$titlecommentis=_t('_ibdw_evowall_comment_title2');$endcomment=_t('_ibdw_evowall_comment_titlef2');}
		 echo '<div class="commentreport" id="rep'.$assegnazione.'"><div class="comm">'.$titlecommentis.' <span class="numerocommenti'.$assegnazione.'">'.$rowcontacommenti.'</span> '.$endcomment.'</b></div>';
		 if($rowcontacommenti>$limitecommento) echo '<div class="vedialtro"><i class="sys-icon chevron-down" alt=""></i><a href="javascript:altricommenti'.$assegnazione.'('.$assegnazione.','.$limitecommento.',\''.$pagina.'\')" class="othnews">'._t("_ibdw_evowall_altricommenti").'</a></div>';
		 echo '</div>';
		}
		if($ordinec=='Last') $tipoordine="DESC";
		else $tipoordine="ASC";
		$querydelcommento="SELECT * FROM (SELECT t1.*,t2.date, Profiles.ID as MYID, Profiles.NickName, Profiles.Avatar FROM (commenti_spy_data AS t1 LEFT JOIN datacommenti as t2 ON t1.id=t2.IDCommento) INNER JOIN Profiles ON t1.user = Profiles.ID WHERE data='".(int)$assegnazione."' ORDER BY t1.id DESC LIMIT 0,".(int)$limitecommento.") AS t3 WHERE date>'".$valdata['date']."' ORDER BY t3.id ASC";
    $resultdelcommento = mysql_query($querydelcommento);
		echo '<div id="nuovocommento'.$assegnazione.'"> </div>';
		echo '<div id="swich_comment'. $assegnazione.'" class="swichwidth">';
		while($rowdelcommento=mysql_fetch_array($resultdelcommento))
		{
		 echo '<div id="commentario" class="super_commento'.$rowdelcommento['id'].'">';
		 if ($rowdelcommento['user']==$accountid ) 
		 {
		  echo '
        <script>
        $(document).ready(function(){
          var configs = {    
          over: fade_cmnt_BT'.$rowdelcommento['id'].',  
          out: out_cmnt_BT'.$rowdelcommento['id'].',
          interval:1
        };
        $(".super_commento'.$rowdelcommento['id'].'").hoverIntent(configs);		
      });
      function fade_cmnt_BT'.$rowdelcommento['id'].'()
        {
          $("#elimina'.$rowdelcommento['id'].'").fadeIn(1);
        }
      function out_cmnt_BT'.$rowdelcommento['id'].'()
        {
          $("#elimina'.$rowdelcommento['id'].'").css(\'display\',\'none\');
        }
      </script>
      ';
		 }
		 $cmn=str_replace("-->","--&gt;",str_replace("<--","&lt;--",$rowdelcommento['commento']));
		 $cmn=strip_tags($cmn);
		 $cmn=$funclass->urlreplace($cmn);
		 $cmn=str_replace("`", "'", $cmn);
	   $miadatac=$funclass->TempoPost($rowdelcommento['date'],$seldate,$offset);
	   echo '<div id="single_comment'.$rowdelcommento['id'].'" class="single_comment"><div id="contentcomm"><div id="avacomm">';
		 echo get_member_icon($rowdelcommento['MYID'],'none',false);
		 echo '</div><div id="commcomm"><div class="textual">';
     echo '<a href="'.$rowdelcommento['NickName'].'">'.getNickname($rowdelcommento['user']).'</a>: '.$cmn.'<div class="stydata">'.$miadatac.'</div></div><div id="elimina'.$rowdelcommento['id'].'" class="eliminab" style="display:none;">
		  	<form id="elimina'.$rowdelcommento['id'].'" action="javascript:elimina();">
				 <input id="id'.$rowdelcommento['id'].'" type="hidden" name="id" value="'.$rowdelcommento['id'].'">
				 <input id="assegnazione'.$assegnazione.'" type="hidden" name="id" value="'.$assegnazione.'">
				 <input id="pagina'.$assegnazione.'" type="hidden" name="id" value="'.$pagina.'">
				 <input id="limite'.$assegnazione.'" type="hidden" name="id" value="'.$limitecommento.'">         
				</form>
        <div id="idcont'.$rowdelcommento['id'].'" title="'._t('_ibdw_evowall_delete').'" class="removecomment"><i class="sys-icon remove" alt=""></i></div>
				<script>
		$("#idcont'.$rowdelcommento['id'].'").click(function() 
		{
		 var id=$("#id'.$rowdelcommento['id'].'").attr("value");
		 var numero_commento = $(".numerocommenti'.$assegnazione.'").html(); 
		 var def_numero_commento = parseInt(numero_commento)-1;
		 $.ajax({type: "POST", url: "modules/ibdw/evowall/deletecomment.php", data: "id=" + id + "&idpost='.$assegnazione.'",
		 success: function(html)
		 {
		  $(".super_commento'.$rowdelcommento['id'].'").fadeOut(1);
      $(".numerocommenti'.$assegnazione.'").html(def_numero_commento);
      if(def_numero_commento==0) $("#rep'.$assegnazione.'").fadeOut(1);
      else if(def_numero_commento==1) $("#rep'.$assegnazione.'").html("<div class=\'comm\'><i class=\'sys-icon comment\'></i>'._t("_ibdw_evowall_comment_title1").' <span class=\'numerocommenti'.$assegnazione.'\'>1</span> '._t("_ibdw_evowall_comment_titlef1").'</b></div>");
		 }});
		 });
		 </script>
     </div></div>
     </div></div></div>';
		}
    echo '<div id="altricommenti'.$assegnazione.'"></div></div>';
   }
?>