<?php
if(isset($_SERVER['HTTPS'])) 
{
    if ($_SERVER['HTTPS'] == "on") {
        $protocol = 'https';
    }
    else $protocol = 'http';
}
if ($protocol =='') $protocol = 'http';
if ($funclass->ActionVerify($profilemembership,"EVO WALL - Post sharing"))
{
 echo '<a id="bottone_sub_elimina" class="bottone_sub_elimina'.$codiceazione.'" href="javascript:substratocondivisione'.$assegnazione.'()"><i class="sys-icon share" alt=""></i>'._t("_ibdw_evowall_condividi").'</a>
       <script>
        function substratocondivisione'.$assegnazione.'() {$(".elimxx").fadeOut(); $(".condxx").fadeOut(); $(".condivisionemessaggio'.$assegnazione.'").fadeIn(1);open_bt_list('.$codiceazione.');oscura();}
        function annulla_condivisione'.$assegnazione.'() {$(".condivisionemessaggio'.$assegnazione.'").fadeOut(1);schiarisci();}
       </script>';
       $sharefb=0;
       $sharegoogle=0;
       $sharetwitter=0;
	     $sharelinkedin=0;
	     $sharepinterest=0;
	     $sharebaidu=0;
	     $shareweibo=0;
	     $shareqzone=0;
	   
	     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowfbshare') and $fbenabled) $sharefb=1;
	     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowgoogleshare') and $ggenabled) $sharegoogle=1;
	     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowtwshare') and $twenabled) $sharetwitter=1;
	     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowlinkedinshare') and $lienabled) $sharelinkedin=1;
	     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowpinterestshare') and $psenabled) $sharepinterest=1;
	     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowbaidushare') and $bienabled) $sharebaidu=1;
	     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowweiboshare') and $wbenabled) $shareweibo=1;
	     if($funclass->checkprivacyevo($row['sender_id'],$profileid,'allowqzoneshare') and $qzenabled) $shareqzone=1;

       if($row['lang_key']=='_bx_photos_spy_added' OR $row['lang_key']=='_bx_photos_spy_comment_posted' OR $row['lang_key']=='_bx_photos_spy_rated' OR $row['lang_key']=='_ibdw_evowall_bx_photo_add_condivisione' OR $row['lang_key']=='_bx_photo_add_condivisione') 
	     {
        $titolo=$unserialize['entry_caption'];
        if($attivaintegrazione==0) $url=$unserialize['entry_url'];
		    else $url=BX_DOL_URL_ROOT.'page/photoview?iff='.$pdxidfoto;
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfoto[16].'.'.$rowfoto[3];
        $descrizione=$rowfoto[7];
       }
       
       if($row['lang_key']=='_ibdw_photodeluxe_likeadd') 
	     {
        $titolo=str_replace("xyapostrofos","'s",$fetchassoc['Title']);
		    $url=$generaurl;
        $immagine=$imageitis;
        $descrizione=$descrizionecondividiw;
       }
       
       if($row['lang_key']=='_bx_photoalbumshare' or $row['lang_key']=='bx_photo_deluxe_commentoalbum') 
	     {
        $titolo=str_replace("xyapostrofos","'s",$fetchassoc['Caption']);
        $url=$generaurl;
        $descrizione=readyshare($fetchassoc['Description']);
        $getidfirstphoto="SELECT sys_albums_objects.id_object,sys_albums.Caption,bx_photos_main.ID,bx_photos_main.Title,bx_photos_main.Hash,bx_photos_main.Ext  FROM (sys_albums INNER JOIN sys_albums_objects ON sys_albums.ID=sys_albums_objects.id_album) INNER join bx_photos_main ON bx_photos_main.ID=sys_albums_objects.id_object WHERE sys_albums.ID=".$idalbum." ORDER BY ID DESC LIMIT 0,1";
        $getidfphoto=mysql_query($getidfirstphoto);
        $runidphotoget=mysql_fetch_assoc($getidfphoto);
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$runidphotoget['Hash'].'.'.$runidphotoget['Ext'];
        
        $sharegoogle=0;
        $sharetwitter=0;
	      $sharelinkedin=0;
	      $sharepinterest=0;
	      $sharebaidu=0;
	      $shareweibo=0;
	      $shareqzone=0;
       }

       if($row['lang_key']=='_bx_videos_spy_added' OR $row['lang_key']=='_bx_videos_spy_rated' OR $row['lang_key']=='_bx_videos_spy_comment_posted' OR $row['lang_key']=='_ibdw_evowall_bx_video_add_condivisione') 
	     {
        $titolo=readyshare($unserialize['entry_caption']);
        $url=$unserialize['entry_url'];
        if($rowvideo[4]=='youtube') 
		    { 
         $immagine=$protocol.'://i.ytimg.com/vi/'.$rowvideo['Video'].'/0.jpg';
         $descrizione=$rowvideo['Description'];
        }
        else 
	      {
         $immagine=BX_DOL_URL_ROOT.'flash/modules/video/files/'.$rowvideo['ID'].'_small.jpg';
         $descrizione=$rowvideo['Description'];
        }
       }
       
       if($row['lang_key']=='_bx_ads_added_spy' OR $row['lang_key']=='_bx_ads_rated_spy' OR $row['lang_key']=='_ibdw_evowall_bx_ads_add_condivisione') 
	     { 
        $titolo=$unserialize['ads_caption'];
        $url=$unserialize['ads_url'];
        $immagine=BX_DOL_URL_ROOT.'media/images/classifieds/thumb_'.$rowqueryannunciofoto[1];
        $descrizione=$rowqueryannuncio[6];
       }
       
       if($row['lang_key']=='_bx_sites_poll_add' OR $row['lang_key']=='_bx_sites_poll_rate' OR $row['lang_key']=='_bx_sites_poll_commentPost' OR $row['lang_key']=='_bx_sites_poll_change' OR $row['lang_key']=='_ibdw_evowall_bx_site_add_condivisione')
	     {
		    $titolo=$unserialize['site_caption'];
        $url=$unserialize['site_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowsito[4].'.'. $rowsito[5];
        $descrizione=$rowrichiestasito[3];
       }
       
       if($row['lang_key']=='_bx_events_spy_post' OR $row['lang_key']=='_bx_events_spy_join' OR $row['lang_key']=='_bx_events_spy_rate' OR $row['lang_key']=='_bx_events_spy_comment' OR $row['lang_key']=='_bx_events_spy_post_change' OR $row['lang_key']=='_ibdw_evowall_bx_event_add_condivisione') 
	     {
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotoevento[3].'.'. $rowfotoevento[1];
        $descrizione=$rowevento[2];
       }
       
       if($row['lang_key']=='_bx_groups_spy_post' OR $row['lang_key']=='_bx_groups_spy_post_change' OR $row['lang_key']=='_bx_groups_spy_join' OR $row['lang_key']=='_bx_groups_spy_rate' OR $row['lang_key']=='_bx_groups_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_gruppo_add_condivisione') 
	     {
		    $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
       }
       
       if($row['lang_key']=='_bx_pages_spy_post' OR $row['lang_key']=='_bx_pages_spy_post_change' OR $row['lang_key']=='_bx_pages_spy_join' OR $row['lang_key']=='_bx_pages_spy_rate' OR $row['lang_key']=='_bx_pages_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_pagina_add_condivisione') 
	     {
		    $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
       }
       
       if($row['lang_key']=='_bx_poll_added' OR $row['lang_key']=='_bx_poll_answered' OR $row['lang_key']=='_bx_poll_rated' OR $row['lang_key']=='_bx_poll_commented' OR $row['lang_key']=='_ibdw_evowall_bx_poll_add_condivisione') 
	     { 
        $titolo=$unserialize['poll_caption'];
        $url=$urltoshareis;
        $immagine='';
        $descrizione='';
       }
	   
	     if($row['lang_key']=='_modzzz_property_spy_post' OR $row['lang_key']=='_modzzz_property_spy_post_change' OR $row['lang_key']=='_modzzz_property_spy_join' OR $row['lang_key']=='_modzzz_property_spy_rate' OR $row['lang_key']=='_modzzz_property_spy_comment' OR $row['lang_key']=='_ibdw_evowall_modzzz_property_share') 
	     { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$fotoarray[0];
        $descrizione=$descrizioneproperty." $".number_format($rowqueryproperty['price']);
       }
	   
	     if($row['lang_key']=='_bx_blog_added_spy' OR $row['lang_key']=='_bx_blog_rated_spy' OR $row['lang_key']=='_ibdw_evowall_bx_blogs_add_condivisione') 
	     { 
        $titolo=$unserialize['post_caption'];
        $url=$unserialize['post_url'];
        $immagine=BX_DOL_URL_ROOT.'media/images/blog/big_'.$fotoblog;
        $descrizione=$descrizioneblog;
       }
	   
	     if($row['lang_key']=='_bx_sounds_spy_added' OR $row['lang_key']=='_bx_sounds_spy_comment_posted' OR $row['lang_key']=='_bx_sounds_spy_rated' OR $row['lang_key']=='_ibdw_evowall_bx_sounds_add_condivisione') 
	     { 
        $titolo=$unserialize['entry_caption'];
        $url=$unserialize['entry_url'];
        $immagine='';
        $descrizione=$descrizionesound;
       }
	   
	     if($row['lang_key']=='_ibdw_evowall_bx_url_add' or $row['lang_key']=='_ibdw_evowall_bx_url_share') 
	     { 
        $titolo=$unserialize['titolosito'];
        $url=$unserialize['indirizzo'];
        $immagine=$unserialize['immagine'];
        $descrizione=$unserialize['descrizione'];
        $descrizione=str_replace('%2310','',$descrizione);
       }
	   
	     if($row['lang_key']=='_ue30_event_spy_post' OR $row['lang_key']=='_ue30_event_spy_join' OR $row['lang_key']=='_ue30_event_spy_rate' OR $row['lang_key']=='_ue30_event_spy_comment' OR $row['lang_key']=='_ue30_event_spy_post_change' OR $row['lang_key']=='_ibdw_evowall_ue30_event_add_condivisione') 
	     { 
        $titolo=$unserialize['entry_title'];
        $url= $unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotoevento[3].'.'. $rowfotoevento[1];
        $descrizione=$rowevento[2];
       }
	   
	   if($row['lang_key']=='_ue30_location_spy_post' OR $row['lang_key']=='_ue30_location_spy_post_change' OR $row['lang_key']=='_ue30_location_spy_join' OR $row['lang_key']=='_ue30_location_spy_rate' OR $row['lang_key']=='_ue30_location_spy_comment' OR $row['lang_key']=='_ibdw_evowall_ue30_locations_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotoevento[3].'.'. $rowfotoevento[1];
        $descrizione=$rowevento[2];
       }
	   
	   if($row['lang_key']=='_modzzz_club_spy_post' OR $row['lang_key']=='_modzzz_club_spy_post_change' OR $row['lang_key']=='_modzzz_club_spy_join' OR $row['lang_key']=='_modzzz_club_spy_rate' OR $row['lang_key']=='_modzzz_club_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_club_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
       }
	   
	   if($row['lang_key']=='_modzzz_pets_spy_post' OR $row['lang_key']=='_modzzz_pets_spy_post_change' OR $row['lang_key']=='_modzzz_pets_spy_rate' OR $row['lang_key']=='_modzzz_pets_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_pet_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
       }
	   
	   if($row['lang_key']=='_modzzz_petitions_spy_post' OR $row['lang_key']=='_modzzz_petitions_spy_post_change' OR $row['lang_key']=='_modzzz_petitions_spy_join' OR $row['lang_key']=='_modzzz_petitions_spy_rate' OR $row['lang_key']=='_modzzz_petitions_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_petition_add_condivisione') 
	   { 
      $titolo=$unserialize['entry_title'];
      $url=$unserialize['entry_url'];
      $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
      $descrizione=$descrizionet;
     }
     
     if($row['lang_key']=='_modzzz_deals_spy_post' OR $row['lang_key']=='_modzzz_deals_spy_post_change' OR $row['lang_key']=='_modzzz_deals_spy_rate' OR $row['lang_key']=='_modzzz_deals_spy_comment' OR $row['lang_key']=='_modzzz_petitions_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_deal_add_condivisione') 
	   { 
      $titolo=$unserialize['entry_title'];
      $url=$unserialize['entry_url'];
      $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
      $descrizione=$descrizionet;
     }
	   
	   if($row['lang_key']=='_modzzz_bands_spy_post' OR $row['lang_key']=='_modzzz_bands_spy_post_change' OR $row['lang_key']=='_modzzz_bands_spy_join' OR $row['lang_key']=='_modzzz_bands_spy_rate' OR $row['lang_key']=='_modzzz_bands_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_band_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
       }
	   
	   if($row['lang_key']=='_modzzz_schools_spy_post' OR $row['lang_key']=='_modzzz_schools_spy_post_change' OR $row['lang_key']=='_modzzz_schools_spy_join' OR $row['lang_key']=='_modzzz_schools_spy_rate' OR $row['lang_key']=='_modzzz_schools_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_school_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
       }
	   
	   if($row['lang_key']=='_modzzz_notices_spy_post' OR $row['lang_key']=='_modzzz_notices_spy_post_change' OR $row['lang_key']=='_modzzz_notices_spy_rate' OR $row['lang_key']=='_modzzz_notices_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_notice_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine='';
        $descrizione=$descrizionet;
       }
	   
	   if($row['lang_key']=='_modzzz_classified_spy_post' OR $row['lang_key']=='_modzzz_classified_spy_post_change' OR $row['lang_key']=='_modzzz_classified_spy_rate' OR $row['lang_key']=='_modzzz_classified_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_classified_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
       }
	   
	   if($row['lang_key']=='_modzzz_news_spy_post' OR $row['lang_key']=='_modzzz_news_spy_post_change' OR $row['lang_key']=='_modzzz_news_spy_rate' OR $row['lang_key']=='_modzzz_news_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_news_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$rowgruppo[2];
       }
	   
	   if($row['lang_key']=='_modzzz_jobs_spy_post' OR $row['lang_key']=='_modzzz_jobs_spy_post_change' OR $row['lang_key']=='_modzzz_jobs_spy_join' OR $row['lang_key']=='_modzzz_jobs_spy_rate' OR $row['lang_key']=='_modzzz_jobs_spy_comment' OR $row['lang_key']=='_ibdw_evowall_modzzz_job_share') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
       }
	   
	   
	    if($row['lang_key']=='_modzzz_articles_spy_post' OR $row['lang_key']=='_modzzz_articles_spy_post_change' OR $row['lang_key']=='_modzzz_articles_spy_join' OR $row['lang_key']=='_modzzz_articles_spy_rate' OR $row['lang_key']=='_modzzz_articles_spy_comment' OR $row['lang_key']=='_ibdw_evowall_modzzz_article_share') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
       }
	   
	   
	   
	   if($row['lang_key']=='_modzzz_formations_spy_post' OR $row['lang_key']=='_modzzz_formations_spy_post_change' OR $row['lang_key']=='_modzzz_formations_spy_join' OR $row['lang_key']=='_modzzz_formations_spy_rate' OR $row['lang_key']=='_modzzz_formations_spy_comment' OR $row['lang_key']=='_ibdw_evowall_modzzz_formation_share') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
       }
	   
	   
	   if($row['lang_key']=='_modzzz_investment_spy_post' OR $row['lang_key']=='_modzzz_investment_spy_post_change' OR $row['lang_key']=='_modzzz_investment_spy_join' OR $row['lang_key']=='_modzzz_investment_spy_rate' OR $row['lang_key']=='_modzzz_investment_spy_comment' OR $row['lang_key']=='_ibdw_evowall_modzzz_investment_share') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
       }
	   
	   
	   
	   if($row['lang_key']=='_modzzz_listing_spy_post' OR $row['lang_key']=='_modzzz_listing_spy_post_change' OR $row['lang_key']=='_modzzz_listing_spy_join' OR $row['lang_key']=='_modzzz_listing_spy_rate' OR $row['lang_key']=='_modzzz_listing_spy_comment' OR $row['lang_key']=='_ibdw_evowall_modzzz_listing_share') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
     } 
     
     if($row['lang_key']=='_modzzz_provider_spy_join' OR $row['lang_key']=='_modzzz_provider_spy_post' OR $row['lang_key']=='_modzzz_provider_spy_post_change' OR $row['lang_key']=='_modzzz_provider_spy_rate' OR $row['lang_key']=='_modzzz_provider_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_provider_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
     } 
     
     if($row['lang_key']=='_modzzz_resume_spy_join' OR $row['lang_key']=='_modzzz_resume_spy_post' OR $row['lang_key']=='_modzzz_resume_spy_post_change' OR $row['lang_key']=='_modzzz_resume_spy_rate' OR $row['lang_key']=='_modzzz_resume_spy_comment' OR $row['lang_key']=='_ibdw_evowall_bx_provider_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'m/photos/get_image/original/'.$rowfotogruppo[3].'.'. $rowfotogruppo[1];
        $descrizione=$descrizionet;
     }
     
     if($row['lang_key']=='_aca_spy_create_re_post' OR $row['lang_key']=='_aca_spy_edit_re_post' OR $row['lang_key']=='_ibdw_evowall_bx_aca_add_condivisione') 
	   { 
        $titolo=$unserialize['site_caption'];
        $url=$unserialize['obj_url'];
        $immagine=BX_DOL_URL_ROOT.'modules/andrew/cars/data/files/'.$rowfotogruppo[0];
        $descrizione=$descrizionet;
     }
     if($row['lang_key']=='_ajb_wall_add_job_vacancy_spy' OR $row['lang_key']=='_ibdw_evowall_bx_ajb_add_condivisione') 
	   { 
        $titolo=$unserialize['obj_caption'];
        $url=BX_DOL_URL_ROOT.$unserialize['obj_url'];
        $immagine='';
        $descrizione=$descrizionet;
     }
	   if($row['lang_key']=='_abl_spy_create_bl_post' OR $row['lang_key']=='_abl_spy_edit_bl_post' OR $row['lang_key']=='_ibdw_evowall_bx_abl_add_condivisione') 
	   { 
        $titolo=$unserialize['site_caption'];
        $url=$unserialize['obj_url'];
        $immagine=BX_DOL_URL_ROOT.'modules/andrew/business_listing/data/files/i_'.$rowfotogruppo[0];
        $descrizione=$descrizionet;
     }
     if($row['lang_key']=='_are_spy_create_re_post' OR $row['lang_key']=='_are_spy_edit_re_post' OR $row['lang_key']=='_ibdw_evowall_bx_areal_add_condivisione') 
	   { 
        $titolo=$unserialize['site_caption'];
        $url=$unserialize['obj_url'];
        $immagine=BX_DOL_URL_ROOT.'modules/andrew/realestate/data/files/i_'.$rowfotogruppo[0];
        $descrizione=$descrizionet;
     }
     
     
     if($row['lang_key']=='_Places spy add' OR $row['lang_key']=='_Places spy change' OR $row['lang_key']=='_Places spy add_photo' OR $row['lang_key']=='_Places spy add_video' OR $row['lang_key']=='_Places spy comment' OR $row['lang_key']=='_Places spy rate' OR $row['lang_key']=='_Places spy add_kml' OR $row['lang_key']=='_ibdw_evowall_bx_kplace_add_condivisione') 
	   { 
        $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine=BX_DOL_URL_ROOT.'modules/kolimarfey/places/application/photos/big/'.$fotoplace.'.jpg';
        $descrizione=$descrizioneplace;
     }
     
     if($row['lang_key']=='_modzzz_polls_spy_post' OR $row['lang_key']=='_modzzz_polls_spy_post_change' OR $row['lang_key']=='_modzzz_polls_spy_rate' OR $row['lang_key']=='_modzzz_polls_spy_comment' OR $row['lang_key']=='_ibdw_evowall_modzzz_poll_add_condivisione')
	     {
		    $titolo=$unserialize['entry_title'];
        $url=$unserialize['entry_url'];
        $immagine='';
        $descrizione=$rowrichiestasito[3];
       }
     
     
    $descrx=addslashes(htmlspecialchars(strip_tags ($descrizione)));
    $title=addslashes(strip_tags ($titolo));
    
    
    $descrx=str_replace('%0D','',$descrx);
    $descrx=str_replace('%0A','',$descrx);
    $descrx=str_replace('+',' ',$descrx);
    $descrx=str_replace('%27','XYZaPos',$descrx);
    
    $title=str_replace('%0D','',$title);
    $title=str_replace('%0A','',$title);
    $title=str_replace('+',' ',$title);
    $title=str_replace('%27','XYZaPos',$title);   
     
    if($sharefb==1) echo '<a id="bottone_sub_elimina" href="javascript:open_fb(\''.$title.'\',\''.$immagine.'\',\''.$descrx.'\',\''.$url.'\')"><i class="sys-icon facebook" alt=""></i>'._t("_ibdw_evowall_facebook_share").'</a>';
    if($sharegoogle==1) echo '<a id="bottone_sub_elimina" href="javascript:open_google(\''.$title.'\',\''.$url.'\',\''.$immagine.'\',\''.$descrx.'\')"><i class="sys-icon google-plus" alt=""></i>'._t("_ibdw_evowall_google_share").'</a>';
    if($sharetwitter==1) echo '<a id="bottone_sub_elimina" href="javascript:open_twitter(\''.$title.'\',\''.$url.'\',\''.$immagine.'\',\''.$descrx.'\')"><i class="sys-icon twitter" alt=""></i>'._t("_ibdw_evowall_twitter_share").'</a>';
    if($sharelinkedin==1) echo '<a id="bottone_sub_elimina" href="javascript:open_linkedin(\''.$title.'\',\''.$url.'\',\''.$immagine.'\',\''.$descrx.'\')"><i class="sys-icon linkedin" alt=""></i>'._t("_ibdw_evowall_linkedin_share").'</a>';
	  if($sharepinterest==1) echo '<a id="bottone_sub_elimina" href="javascript:open_pinterest(\''.$title.'\',\''.$url.'\',\''.$immagine.'\',\''.$descrx.'\')"><i class="sys-icon pinterest" alt=""></i>'._t("_ibdw_evowall_pinterest_share").'</a>';	
	  if($sharebaidu==1) echo '<a id="bottone_sub_elimina" href="javascript:open_baidu(\''.$title.'\',\''.$url.'\',\''.$immagine.'\',\''.$descrx.'\')"><i class="sys-icon chevron-right" alt=""></i>'._t("_ibdw_evowall_baidu_share").'</a>';
	  if($shareweibo==1) echo '<a id="bottone_sub_elimina" href="javascript:open_weibo(\''.$title.'\',\''.$url.'\',\''.$immagine.'\',\''.$descrx.'\')"><i class="sys-icon chevron-right" alt=""></i>'._t("_ibdw_evowall_weibo_share").'</a>';
	  if($shareqzone==1) echo '<a id="bottone_sub_elimina" href="javascript:open_qzone(\''.$title.'\',\''.$url.'\',\''.$immagine.'\',\''.$descrx.'\')"><i class="sys-icon chevron-right" alt=""></i>'._t("_ibdw_evowall_qzone_share").'</a>';
}
?>