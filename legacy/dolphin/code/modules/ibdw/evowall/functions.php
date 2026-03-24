<?php
class swfunc 
{
 //bad words filter
 function ReplaceBadWords($comment,$wordlist)
 {
  $wordlist=trim($wordlist);
  $badword = array();
  $replacementword = array();
  $words = explode(",", $wordlist);
  foreach ($words as $key => $word) 
  {
   $word=trim($word);  
   $badword[$key] = $word;
   $length = strlen($word);
   $replacementword[$key] = str_repeat("*", $length);
   $badword[$key] = "/\b{$badword[$key]}\b/i";
  }
  $comment = preg_replace($badword, $replacementword, $comment);
  return $comment;
 }
 
 //add html tag on the text replace
 function urlreplace($text) 
 {
  $text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)','<a target=\'_blank\'  href="\\1">\\1</a>', $text);
  $text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)','\\1<a target=\'_blank\'  href="http://\\2">\\2</a>', $text);
  $text = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})','<a target=\'_blank\'  href="mailto:\\1">\\1</a>', $text);
  $text = ereg_replace("www+[^<>[:space:]]+[[:alnum:]/].fan-club.it","<a target=\'_blank\'  href=\"\\0\">\\0</a>", $text);
  $text = ereg_replace("(^| )(www([.]?[a-zA-Z0-9_/-])*)", "\\1<a target=\'_blank\'  href=\"http://\\2\">\\2</a>", $text);
  return $text;
 } 
 //clean text removing some tags
 function cleartesto($text)
 {
  $text=str_replace("&lt;br /&gt;"," ",strip_tags(str_replace('<p>','',str_replace('</p>',' ',$text)),"<i>"));
  return $text;
 }
 //cut string after x char
  function tagliaz($testo,$maxCaratteri,$post_id) 
 {
  $caratteri=strlen($testo);
  $ellips="<i id='seemore' onclick='extext_".$post_id."()'>"._t('_ibdw_evowall_see_more')."</i>";
  if($caratteri>$maxCaratteri) 
  {
   return mb_strimwidth($testo, 0, $maxCaratteri,$ellips);
  }
  else return $testo;
 }

 //remove single and double quote
 //to write
 function solveslash($testo)
 {
  $testo=str_replace("'","codapos1",$testo);
  $testo=str_replace("&apos;","codapos1",$testo);
  $testo=str_replace('"',"codapos2",$testo);
  $testo=str_replace('&quot;',"codapos2",$testo);
  return $testo;
 }
 //to read
 function resetslash($testo)
 {
  $testo=str_replace("codapos1","'",$testo);
  $testo=str_replace("codapos1","&apos;",$testo);
  $testo=str_replace("codapos2",'"',$testo);
  $testo=str_replace("codapos2",'&quot;',$testo);
  return $testo;
 }
 
 //get date for news and comments
 Function TempoPost($inputdata,$datadaconf,$offset)
 { 
  $differenza=intval((time()-(strtotime ($inputdata)+$offset))/60);
  if ($differenza<60)
  {
   switch ($differenza)
   {
	  case 0: $miadata=_t('_ibdw_evowall_now');
	  case 1: $miadata=_t('_ibdw_evowall_oneminute');
	  break;
	  default: $miadata=_t('_ibdw_evowall_plusminutefirst')."$differenza"._t('_ibdw_evowall_plusminutesecond'); 
	  break;
   }
  }
  else
  {
   $ore=intval($differenza/60);
   if ($ore>=1 && $ore<2) $miadata=_t('_ibdw_evowall_onehour');
   // FREDDY MODIF
   //elseif ($ore>=2 && $ore<25) $miadata=_t('_ibdw_evowall_plushourfirst').$ore._t('_ibdw_evowall_plushoursecond');
    elseif ($ore>=2 && $ore<25) $miadata= ' <i class="sys-icon clock-o"></i>'.$ore._t('_ibdw_evowall_plushoursecond');
   elseif ($differenza>=1440 && $differenza<1500) $miadata=_t('_ibdw_evowall_today');
   elseif ($differenza>=1500 && $differenza<2880) $miadata=_t('_ibdw_evowall_yesterday');
   else 
   {
	  if ($inputdata==NULL) $miadata="";
    else $miadata=_t('_ibdw_evowall_otherday').date($datadaconf, strtotime ($inputdata));
   }
  }
  $miadata='<span>'.$miadata.'</span>';
  return $miadata;
 }

//Date formatting with the relative icon
function formaticondate($inputdata,$langkeytype,$path)
{
 if ($langkeytype=="_bx_spy_profile_has_joined") $risultform=$inputdata.'<i alt="" class="sys-icon user"></i>';
 elseif ($langkeytype=="_bx_photoalbumshare" or $langkeytype=="bx_photo_deluxe_tag" or $langkeytype=="bx_photo_deluxe_commentofoto" or $langkeytype=="bx_photo_deluxe_commentoalbum" or $langkeytype=="_bx_photos_spy_added"  or $langkeytype=="_bx_photos_spy_comment_posted" or $langkeytype=="_bx_photos_spy_rated" or $langkeytype=="_ibdw_evowall_bx_photo_add_condivisione" or $langkeytype=="_ibdw_pagecover_update" or $langkeytype=="_ibdw_pagecover_update_male" or $langkeytype=="_ibdw_pagecover_update_female" or $langkeytype=="_ibdw_groupcover_update" or $langkeytype=="_ibdw_groupcover_update_male" or $langkeytype=="_ibdw_groupcover_update_female" or $langkeytype=="_ibdw_profilecover_update" or $langkeytype=="_ibdw_profilecover_update_male" or $langkeytype=="_ibdw_profilecover_update_female") $risultform=$inputdata.'<i alt="" class="sys-icon picture-o"></i>';
 elseif ($langkeytype=="_bx_sites_poll_add" or $langkeytype=="_bx_sites_poll_rate" or $langkeytype=="_bx_sites_poll_commentPost" or $langkeytype=="_bx_sites_poll_change" or $langkeytype=="_ibdw_evowall_bx_site_add_condivisione") $risultform=$inputdata.'<i alt="" class="sys-icon link"></i>';
 elseif ($langkeytype=="_bx_videos_spy_added" or $langkeytype=="_bx_videos_spy_rated" or $langkeytype=="_bx_videos_spy_comment_posted" or $langkeytype=="_ibdw_evowall_bx_video_add_condivisione") $risultform=$inputdata.'<i alt="" class="sys-icon film"></i>';
 elseif ($langkeytype=="_bx_ads_added_spy" or $langkeytype=="_bx_ads_rated_spy" or $langkeytype=="_ibdw_evowall_bx_ads_add_condivisione") $risultform=$inputdata.'<i alt="" class="sys-icon money"></i>';
 elseif ($langkeytype=="_bx_blog_added_spy" or $langkeytype=="_bx_blog_rated_spy" or $langkeytype=="_ibdw_evowall_bx_blogs_add_condivisione") $risultform=$inputdata.'<i alt="" class="sys-icon book"></i>';
 elseif ($langkeytype=="_bx_sounds_spy_added" or $langkeytype=="_bx_sounds_spy_comment_posted" or $langkeytype=="_bx_sounds_spy_rated" or $langkeytype=="_ibdw_evowall_bx_sounds_add_condivisione") $risultform=$inputdata.'<i alt="" class="sys-icon music"></i>';
 elseif ($langkeytype=="_bx_groups_spy_post" or $langkeytype=="_bx_groups_spy_post_change" or $langkeytype=="_bx_groups_spy_join" or $langkeytype=="_bx_groups_spy_rate" or $langkeytype=="_bx_groups_spy_comment" or $langkeytype=="_ibdw_evowall_bx_gruppo_add_condivisione") $risultform=$inputdata.'<i alt="" class="sys-icon group"></i>';
 elseif ($langkeytype=="_bx_pages_spy_post" or $langkeytype=="_bx_pages_spy_post_change" or $langkeytype=="_bx_pages_spy_join" or $langkeytype=="_bx_pages_spy_rate" or $langkeytype=="_bx_pages_spy_comment" or $langkeytype=="_ibdw_evowall_bx_pagina_add_condivisione") $risultform=$inputdata.'<i alt="" class="sys-icon book"></i>';
 elseif ($langkeytype=="_bx_events_spy_post" or $langkeytype=="_bx_events_spy_join" or $langkeytype=="_bx_events_spy_rate" or $langkeytype=="_bx_events_spy_comment" or $langkeytype=="_bx_events_spy_post_change" or $langkeytype=="_ibdw_evowall_bx_event_add_condivisione" or $langkeytype=="_ue30_event_spy_post" or $langkeytype=="_ue30_event_spy_join" or $langkeytype=="_ue30_event_spy_rate" or $langkeytype=="_ue30_event_spy_comment" or $langkeytype=="_ue30_event_spy_post_change" or $langkeytype=="_ibdw_evowall_ue30_event_add_condivisione") $risultform=$inputdata.'<i alt="" class="sys-icon calendar"></i>';
 elseif ($langkeytype=="_bx_poll_added" or $langkeytype=="_bx_poll_answered" or $langkeytype=="_bx_poll_rated" or $langkeytype=="_bx_poll_commented" or $langkeytype=="_ibdw_evowall_bx_poll_add_condivisione") $risultform=$inputdata.'<i alt="" class="sys-icon tasks"></i>';
 elseif ($langkeytype=="_ibdw_evowall_bx_url_share" or $langkeytype=="_ibdw_evowall_bx_url_add") $risultform=$inputdata.'<i alt="" class="sys-icon link"></i>';
 elseif ($langkeytype=="_bx_spy_profile_friend_accept") $risultform=$inputdata.'<i alt="" class="sys-icon plus"></i>';
 elseif ($langkeytype=="_ibdw_evowall_bx_evowall_message" or $langkeytype=="_ibdw_evowall_bx_evowall_messageseitu") $risultform=$inputdata.'<i alt="" class="sys-icon comments"></i>';
 elseif ($langkeytype=="_modzzz_property_spy_post" or $langkeytype=="_modzzz_property_spy_post_change" or $langkeytype=="_modzzz_property_spy_join" or $langkeytype=="_modzzz_property_spy_rate" or $langkeytype=="_modzzz_property_spy_comment" or $langkeytype=="_ibdw_evowall_modzzz_property_share" or $langkeytype=="_are_spy_create_re_post" or $langkeytype=="_are_spy_edit_re_post" or $langkeytype=="_ibdw_evowall_bx_areal_add_condivisione") $risultform=$inputdata.'<i alt="" class="sys-icon home"></i>';
 elseif ($langkeytype=="_ue30_location_spy_post" or $langkeytype=="_ue30_location_spy_post_change" or $langkeytype=="_ue30_location_spy_join" or $langkeytype=="_ue30_location_spy_rate" or $langkeytype=="_ue30_location_spy_comment" or $langkeytype=="_ibdw_evowall_ue30_locations_add_condivisione") $risultform=$inputdata.'<i alt="" class="sys-icon circle-arrow-down"></i>';
 elseif ($langkeytype=="_abl_spy_create_bl_post" or $langkeytype=="_abl_spy_edit_bl_post" or $langkeytype=="_ibdw_evowall_bx_abl_add_condivisione") $risultform=$inputdata.'<i alt="" class="sys-icon money"></i>';
 elseif ($langkeytype=="_ajb_wall_add_job_vacancy_spy" or $langkeytype=="_ibdw_evowall_bx_ajb_add_condivisione") $risultform=$inputdata.'<i class="sys-icon briefcase"></i>';
 elseif ($langkeytype=="_aca_spy_create_re_post" or $langkeytype=="_aca_spy_edit_re_post" or $langkeytype=="_ibdw_evowall_bx_aca_add_condivisione") $risultform=$inputdata.'<i class="sys-icon truck"></i>';
 elseif ($langkeytype=="_modzzz_bands_spy_post" or $langkeytype=="_modzzz_bands_spy_post_change" or $langkeytype=="_modzzz_bands_spy_join" or $langkeytype=="_modzzz_bands_spy_rate" or $langkeytype=="_modzzz_bands_spy_comment" or $langkeytype=="_ibdw_evowall_bx_band_add_condivisione") $risultform=$inputdata.'<i class="sys-icon music"></i>';
 elseif ($langkeytype=="_modzzz_jobs_spy_join" or $langkeytype=="_modzzz_jobs_spy_post" or $langkeytype=="_modzzz_jobs_spy_post_change" or $langkeytype=="_modzzz_jobs_spy_rate" or $langkeytype=="_modzzz_jobs_spy_comment" or $langkeytype=="_ibdw_evowall_bx_job_add_condivisione") $risultform=$inputdata.'<i class="sys-icon briefcase"></i>';
 
 elseif ($langkeytype=="_modzzz_articles_spy_join" or $langkeytype=="_modzzz_articles_spy_post" or $langkeytype=="_modzzz_articles_spy_post_change" or $langkeytype=="_modzzz_articles_spy_rate" or $langkeytype=="_modzzz_articles_spy_comment" or $langkeytype=="_ibdw_evowall_bx_article_add_condivisione") $risultform=$inputdata.'<i class="sys-icon briefcase"></i>';
 
 
 elseif ($langkeytype=="_modzzz_investment_spy_join" or $langkeytype=="_modzzz_investment_spy_post" or $langkeytype=="_modzzz_investment_spy_post_change" or $langkeytype=="_modzzz_investment_spy_rate" or $langkeytype=="_modzzz_investment_spy_comment" or $langkeytype=="_ibdw_evowall_bx_investment_add_condivisione") $risultform=$inputdata.'<i class="sys-icon briefcase"></i>';
 
 
  elseif ($langkeytype=="_modzzz_formations_spy_join" or $langkeytype=="_modzzz_formations_spy_post" or $langkeytype=="_modzzz_formations_spy_post_change" or $langkeytype=="_modzzz_formations_spy_rate" or $langkeytype=="_modzzz_formations_spy_comment" or $langkeytype=="_ibdw_evowall_bx_formation_add_condivisione") $risultform=$inputdata.'<i class="sys-icon briefcase"></i>';
 
 elseif ($langkeytype=="_modzzz_family_spy_post") $risultform=$inputdata.'<i class="sys-icon group"></i>';
 elseif ($langkeytype=="_modzzz_relation_spy_post" or $langkeytype=="_modzzz_relation_spy_post_remove") $risultform=$inputdata.'<i class="sys-icon heart"></i>';
 elseif ($langkeytype=="_modzzz_polls_spy_post" or $langkeytype=="_modzzz_polls_spy_post_change" or $langkeytype=="_modzzz_polls_spy_rate" or $langkeytype=="_modzzz_polls_spy_comment" or $langkeytype=="_ibdw_evowall_modzzz_poll_add_condivisione") $risultform=$inputdata.'<i alt="" class="sys-icon tasks"></i>';
 else $risultform=$inputdata.'<i alt="" class="sys-icon time"></i>';
 return $risultform;
}
//Privacy management
//Displays the post if privacy is friends or fave and I'm fave or if the privacy is default and default is one of this value (friends or fave)
//$allowvalue is the album privacy value, $toc is the type of content (video, site, ecc..), $author is the sender, userid is the logged member.
//example:
//SITE: $tov='view',$toc='bx_sites'
//ADS: $tov='view',$toc='ads'
//PHOTO: $tov=NULL, $toc='photos'
//VIDEO: $tov=NULL,$toc='videos'
//GRUOPS: $tov='view_group', $toc='groups'
//EVENTS: $tov='view_event',$toc='events'
function privstate($allowvalue,$toc, $author, $accountid, $num_fave, $tov)
{
 if (($allowvalue==5 and is_friends($author,$accountid)) OR ($allowvalue==6 and $num_fave==1) OR ($allowvalue==3) OR ($allowvalue==4) OR ($author==$accountid)) $allowview=1;
 elseif ($allowvalue==1 or $allowvalue==0)
 {
  //get default album privacy for a specific album of this member. group_id is the default privacy level for this album album of this member
  $privdefault="select group_id from sys_privacy_defaults inner join sys_privacy_actions on sys_privacy_defaults.action_id=sys_privacy_actions.id where (sys_privacy_actions.module_uri='".$toc."'";
  if ($tov<>"") {$privdefault=$privdefault." and sys_privacy_actions.name='".$tov."'";}
  $privdefault=$privdefault.") and sys_privacy_defaults.owner_id=".$author;
  $resultdefault = mysql_query($privdefault);
  $rowdefpriv = mysql_fetch_row($resultdefault);
  //if privacy value is 5-> display, or if is 6 check if Im on the his favorite list
  if ((((($rowdefpriv[0]==5 and is_friends($author,$accountid)) OR ($rowdefpriv[0]==6 and $num_fave==1)) OR ($rowdefpriv[0]==3)) OR ($rowdefpriv[0]==4)) OR ($author==$accountid)) $allowview=1;
  elseif($rowdefpriv[0]=="") 
  {
   //get the default site privacy for a specific type of content
   $getdefaultvalue="SELECT * FROM sys_privacy_actions WHERE module_uri='".$toc."'";
   $resultdefault = mysql_query($getdefaultvalue);
   $rowdefpriv = mysql_fetch_row($resultdefault);
   if ((((($rowdefpriv[0]==5 and is_friends($author,$accountid)) OR ($rowdefpriv[0]==6 and $num_fave==1)) OR ($rowdefpriv[0]==3)) OR ($rowdefpriv[0]==4)) OR ($author==$accountid)) $allowview=1;
   else $allowview=0;
  };
 }
 else
 {
  //get the id of the custom privacy level for the sender
  $querycustom="SELECT id FROM sys_privacy_groups WHERE owner_id=".$author;
  $getcustomid=mysql_query($querycustom);
  $getnump= mysql_num_rows($getcustomid);
  if ($getnump>0) 
  {
   $getidcustomprivacy=mysql_fetch_assoc($getcustomid);
   $verifiam="SELECT ID FROM Profiles WHERE ID=".$accountid." AND ID IN (SELECT member_id FROM sys_privacy_members WHERE group_id=".$getidcustomprivacy['id'].")";
   $getifiam=mysql_query($verifiam);
   $numberisoneifiam=mysql_num_rows($getifiam);
   if ($numberisoneifiam==1) $allowview=1;
   else $allowview=0;
  }
  else $allowview=0;
 }
 return $allowview;
}

function checkprivacyevo($inviatore,$accountid,$actionswall)
{ 
 //check post by post the privacy for the member for this specific action
 $queryprivacypost="SELECT * FROM `sys_privacy_defaults` INNER JOIN sys_privacy_actions ON sys_privacy_defaults.action_id=sys_privacy_actions.id WHERE owner_id=$inviatore and (sys_privacy_actions.module_uri='evowall' AND sys_privacy_actions.name='".$actionswall."')";
 $eseguiprivacypost=mysql_query($queryprivacypost);
 $rowprivpost=mysql_fetch_assoc($eseguiprivacypost);
 $valoreprivacypost=$rowprivpost['group_id'];
 //if this member have NOT specified the privacy for comments, then it's used the default
 if ($valoreprivacypost==null)
 {
  $queryprivacypostpred="SELECT * FROM sys_privacy_actions WHERE sys_privacy_actions.module_uri='evowall' AND sys_privacy_actions.name='allowcomment'";
  $eseguiprivacypostpred=mysql_query($queryprivacypostpred);
  $rowprivpostpred=mysql_fetch_assoc($eseguiprivacypostpred);
  $valoreprivacypost=$rowprivpostpred['default_group'];
 }
 //if this member have NOT specified the privacy for Ilike, then it's used the default
 if ($valoreprivacypost==null)
 {
  $queryprivacypostpred="SELECT * FROM sys_privacy_actions WHERE sys_privacy_actions.module_uri='evowall' AND sys_privacy_actions.name='allowlike'";
  $eseguiprivacypostpred=mysql_query($queryprivacypostpred);
  $rowprivpostpred=mysql_fetch_assoc($eseguiprivacypostpred);
  $valoreprivacypost=$rowprivpostpred['default_group'];
 }
 //se sono il sender del post (vale anche se impostata su solo io), se la privacy è public, oppure members oppure amici ed io sono amico
 if ($inviatore==$accountid or $valoreprivacypost==3 or $valoreprivacypost==4 or ($valoreprivacypost==5 and is_friends($inviatore, $accountid)) or ($valoreprivacypost==6 and $num_fave==1)) $valoreprivacypost=true;
 else $valoreprivacypost=false;
 return $valoreprivacypost;
}

//Ottengo true se per la membership è abilitata l'azione
function ActionVerify($profilemembership,$actionname)
{
 $result=false;
 $getmembershipallowed="SELECT IDLevel FROM sys_acl_matrix WHERE IDAction IN (SELECT ID FROM sys_acl_actions WHERE Name='".$actionname."')";
 $resultcheck=mysql_query($getmembershipallowed);
 $numresults=mysql_num_rows($resultcheck);
 for ($i=0;$i<$numresults;$i++)
 {
  $rowaction = mysql_fetch_row($resultcheck);
  if ($rowaction[0]==$profilemembership) $result=true;
 }
 return $result;
}
function isFaved2($iId, $iProfileId)
{
    $iId = (int)$iId;
    $iProfileId = (int)$iProfileId;
    $getvalueF="SELECT count(*) FROM `sys_fave_list` WHERE ID=".$iId." AND Profile=".$iProfileId;
    $getit=mysql_query($getvalueF);
    $favedis=mysql_num_rows($getit);
    return $favedis;
}
//FINE CLASSE
}
function criptcodex($numero) 
{
 $id0=rand(1,9);
 $id1=rand(10000,99999);    
 $mx=$numero*$id0;
 $generacriptcodex=$id0.$id1.$mx;
 return ($generacriptcodex); 
}
function decriptcodex($code) 
{
 $estrazione0=substr($code,0,1); 
 $estrazione=substr($code,6);   
 $mxestrazione=$estrazione/$estrazione0; 
 return ($mxestrazione); 
}
function readyshare($str)
{
 $str=str_replace('"','doppiequot',$str);
 $str=str_replace('&','ecommercial',$str);
 return $str;
}
function estrai_foto_parent($id,$lang,$sender,$recordfound,$photo_max_preview,$integrazione,$datepost)
{
 $accountid=(int)$_COOKIE['memberID'];
 if ($recordfound<$photo_max_preview) $displaylimit=$recordfound;
 else $displaylimit=$photo_max_preview-1;
 $query="SELECT * FROM bx_spy_data WHERE (id<".$id." AND lang_key='".$lang."' AND sender_id=".$sender." AND (DAY(bx_spy_data.date)=DAY('".$datepost."') AND MONTH(bx_spy_data.date)=MONTH('".$datepost."') AND YEAR(bx_spy_data.date)=YEAR('".$datepost."') )) ORDER BY id DESC LIMIT ".$displaylimit;
 $exeverifica=mysql_query($query);
 $numgetimages=mysql_num_rows($exeverifica);
 $todisplay=$numgetimages+1;
 $stampafoto="";
 while($row=mysql_fetch_array($exeverifica))
 {
  $unserialize=unserialize($row['params']);
  $urlimg=$unserialize['entry_url'];
  $trovaslash=substr_count($unserialize['entry_url'], "/");
  $verificauri=explode ("/",$unserialize['entry_url']);
  $verificauri=$verificauri[$trovaslash];
  $pdxrecuperofoto="SELECT ID,Owner FROM bx_photos_main WHERE Uri='".$verificauri."'";
  $pdxeseguirecuperofoto=mysql_query($pdxrecuperofoto);
  $getifexistsimage=mysql_num_rows($pdxeseguirecuperofoto);
  if ($getifexistsimage>0)
  {
   //inizio nuovo controllo
   $pdxrowrecuperfoto=mysql_fetch_assoc($pdxeseguirecuperofoto);
   $pdxidfoto=$pdxrowrecuperfoto['ID'];
   $pdxuserid=$pdxrowrecuperfoto['Owner'];
   $pdxrecuperoalbum="SELECT id_album FROM sys_albums_objects WHERE id_object=".$pdxidfoto;
   $pdxeseguirecuperoalbum=mysql_query($pdxrecuperoalbum);
   $pdxrowrecuperoalbum=mysql_fetch_assoc($pdxeseguirecuperoalbum); 
   $pdxidalbums=$pdxrowrecuperoalbum['id_album']; 
   if($integrazione==1) $indirizzopdx=BX_DOL_URL_ROOT.'page/photoview?iff='.criptcodex($pdxidfoto).'&ia='.criptcodex($pdxidalbums).'&ui='.criptcodex($pdxuserid);
    
   //getalbumprivacy
   $querypriva="SELECT AllowAlbumView,Owner FROM sys_albums WHERE ID=".$pdxidalbums." AND TYPE='bx_photos'";
  
   $resultpriva=mysql_query($querypriva);
   $itsexistes=mysql_num_rows($resultpriva);
   if ($itsexistes>0)
   {
    $rowpriva=mysql_fetch_row($resultpriva);
    $p=new swfunc();
    $isfavorite=$p->isFaved2($rowpriva[1],$accountid);
    $okvista=$p->privstate($rowpriva[0],'photos',$rowpriva[1],$accountid,isFaved($rowpriva[1],$accountid),'');
    if($okvista==1)
    {
     $queryfoto="SELECT * FROM bx_photos_main WHERE Uri='".$verificauri."'";     
     $resultfoto=mysql_query($queryfoto);
     $rowfoto=mysql_fetch_row($resultfoto);
     if($rowfoto[4]==FALSE) {$dlt = "DELETE FROM `bx_spy_data` WHERE `id` = ".$row['id']; $dlt_exe = mysql_query($dlt);}
     else 
     {
      echo '
      <script>
      $(document).ready(function()
	    {
       var foundimages='.$recordfound.';
	     var config={over: fade_evp_pop'.$row['id'].',out: out_evp_pop'.$row['id'].',interval:150};
       var maximumimagedisplaying='.$displaylimit.'+1;
	     $("#pop_evophoto'.$row['id'].'").hoverIntent(config);
	     $("#pop_evophoto'.$row['id'].' img").mouseover(function() {$(this).css("opacity","0.5");});	
	     $("#pop_evophoto'.$row['id'].' img").mouseout(function() {$(this).css("opacity","1");});
       if (foundimages==2)
       { 
        $("#pop_evophoto'.$id.'").css("max-width","50%");
        $("#pop_evophoto'.$id.'").css("max-width","calc(50% - 1px)");
        $("#pop_evophoto'.$id.'").css("max-width","-webkit-calc(50% - 1px)");
        $("#pop_evophoto'.$id.'").css("max-width","-moz-calc(50% - 1px)");
        $("#pop_evophoto'.$id.'").css("max-width","-o-calc(50% - 1px)");
        $("#pop_evophoto'.$row['id'].'").css("max-width","calc(50% - 1px)");    
        $("#pop_evophoto'.$id.' img").css("max-width","100%");
        $("#pop_evophoto'.$row['id'].' img").css("max-width","100%"); 
       }
       else if (foundimages==3)
       {
        $("#pop_evophoto'.$id.'").css("max-width","33%");
        $("#pop_evophoto'.$id.'").css("max-width","calc(33% - 1px)");
        $("#pop_evophoto'.$id.'").css("max-width","-webkit-calc(33% - 1px)");
        $("#pop_evophoto'.$id.'").css("max-width","-moz-calc(33% - 1px)");
        $("#pop_evophoto'.$id.'").css("max-width","-o-calc(33% - 1px)");
        $("#pop_evophoto'.$row['id'].'").css("max-width","calc(33% - 1px)");    
        $("#pop_evophoto'.$id.' img").css("max-width","100%");
        $("#pop_evophoto'.$row['id'].' img").css("max-width","100%");
       }
       else if(maximumimagedisplaying>3 && foundimages>3)
       {
        $("#pop_evophoto'.$id.'").css("max-width","25%");
        $("#pop_evophoto'.$id.'").css("max-width","calc(25% - 1px)");
        $("#pop_evophoto'.$id.'").css("max-width","-webkit-calc(25% - 1px)");
        $("#pop_evophoto'.$id.'").css("max-width","-moz-calc(25% - 1px)");
        $("#pop_evophoto'.$id.'").css("max-width","-o-calc(25% - 1px)");
        $("#pop_evophoto'.$row['id'].'").css("max-width","calc(25% - 1px)");    
        $("#pop_evophoto'.$id.' img").css("max-width","100%");
        $("#pop_evophoto'.$row['id'].' img").css("max-width","100%");
       }
      
       //display the thumb only after loading
       $("#pop_evophoto'.$id.'").find("img").load(function(){ $(this).closest("#pop_evophoto'.$id.'").fadeIn(1000); });
       $("#pop_evophoto'.$row['id'].'").find("img").load(function(){ $(this).closest("#pop_evophoto'.$row['id'].'").fadeIn(1000); });
       //end
	    });
	    function fade_evp_pop'.$row['id'].'()
	    {
	     var set_value_photozoom=$("#set_value_photozoom'.$id.'").val();
 	     if(set_value_photozoom!='.$rowfoto[0].') 
	     {
	      $("#popup'.$id.'").html("<img src=\"m/photos/get_image/file/'.$rowfoto[16].'.'.$rowfoto[3].'\">");
	      $("#popup'.$id.'").fadeIn(1);
		    $("#popup'.$id.' img").fadeIn(1000);  
		    $("#set_value_photozoom'.$id.'").val('.$rowfoto[0].');
	     }
	    }
      function out_evp_pop'.$row['id'].'()
      {
       $("#pop_evophoto'.$row['id'].' img").css("opacity","1");
      }   
      $(document).ready(function() 
      {
       var larg=$("#bloccoav").width();
       var width_foto=larg/'.$todisplay.';
       var width_foto=width_foto-6;
       $(".fadeMini'.$row['id'].' img").css("width",width_foto);
       var height=width_foto/1.5;
       $(".fadeMini'.$row['id'].'").css("height",height);
       $("#messaggio").css("height","100%");
       $(".pop_foto img").css("width","100%");
    	 });
     </script> ';
     if($integrazione==1) $stampafoto=$stampafoto.'<a id="pop_evophoto'.$row['id'].'" class="marginfix" href="'.$indirizzopdx.'">';
     elseif($lang=='_bx_photo_add_condivisione') $stampafoto=$stampafoto.'<a id="pop_evophoto'.$row['id'].'" href="'.BX_DOL_URL_ROOT.'m/photos/view/'.$urlimg.'" class="marginfix">';
     else $stampafoto=$stampafoto.'<a id="pop_evophoto'.$row['id'].'" href="'.$urlimg.'" class="marginfix">';
     $stampafoto=$stampafoto.'<img src="m/photos/get_image/browse/'.$rowfoto[16].'.'.$rowfoto[3].'"></a>';
    }
   } 
   else
   {
     
   }
  } 
  
  //quello seguente è il fine parentesi del nuovo controllo
  }
  else
  {
   
  }
  
 }
 return $stampafoto;
}
function hiddenlink_foto($id,$datepost,$ultimoid,$lang,$record,$sender,$currentpage)
{
 $num=$record-1;
 if ($num>1) 
 {
  $lang_key=_t("_ibdw_evowall_similarevonews_photo");
  $lang_key=str_replace('{number}',(int)$num,$lang_key);
 }
 else $lang_key = _t("_ibdw_evowall_similarevonews");
 $txt='<div class="latermar"><a class="link_hid" id="link_hid'.$id.'" href="javascript:downtown_foto('.(int)$id.','.(int)$ultimoid.',\''.$lang.'\',\''.(int)$record.'\','.(int)$sender.',\''.$datepost.'\',\''.$currentpage.'\');">'.$lang_key.'</a></div>';
 return $txt;
}
function SPChar($stringa) 
{
    $stringa = str_replace( 'À', '&#192;', $stringa );
    $stringa = str_replace( 'Á', '&#193;', $stringa );
    $stringa = str_replace( 'Â', '&#194;', $stringa );
    $stringa = str_replace( 'Ã', '&#195;', $stringa );
    $stringa = str_replace( 'Ä', '&#196;', $stringa );
    $stringa = str_replace( 'Å', '&#197;', $stringa );
    $stringa = str_replace( 'Æ', '&#198;', $stringa );
    $stringa = str_replace( 'Ç', '&#199;', $stringa );
    $stringa = str_replace( 'È', '&#200;', $stringa );
    $stringa = str_replace( 'É', '&#201;', $stringa );
    $stringa = str_replace( 'Ê', '&#202;', $stringa );
    $stringa = str_replace( 'Ë', '&#203;', $stringa );
    $stringa = str_replace( 'Ì', '&#204;', $stringa );
    $stringa = str_replace( 'Í', '&#205;', $stringa );
    $stringa = str_replace( 'Î', '&#206;', $stringa );
    $stringa = str_replace( 'Ï', '&#207;', $stringa );
    $stringa = str_replace( 'Ð', '&#208;', $stringa );
    $stringa = str_replace( 'Ñ', '&#209;', $stringa );
    $stringa = str_replace( 'Ò', '&#210;', $stringa );
    $stringa = str_replace( 'Ó', '&#211;', $stringa );
    $stringa = str_replace( 'Ô', '&#212;', $stringa );
    $stringa = str_replace( 'Õ', '&#213;', $stringa );
    $stringa = str_replace( 'Ö', '&#214;', $stringa );
    $stringa = str_replace( '×', '&#215;', $stringa );  // Yeah, I know.  But otherwise the gap is confusing.  --Kris
    $stringa = str_replace( 'Ø', '&#216;', $stringa );
    $stringa = str_replace( 'Ù', '&#217;', $stringa );
    $stringa = str_replace( 'Ú', '&#218;', $stringa );
    $stringa = str_replace( 'Û', '&#219;', $stringa );
    $stringa = str_replace( 'Ü', '&#220;', $stringa );
    $stringa = str_replace( 'Ý', '&#221;', $stringa );
    $stringa = str_replace( 'Þ', '&#222;', $stringa );
    $stringa = str_replace( 'ß', '&#223;', $stringa );
    $stringa = str_replace( 'à', '&#224;', $stringa );
    $stringa = str_replace( 'á', '&#225;', $stringa );
    $stringa = str_replace( 'â', '&#226;', $stringa );
    $stringa = str_replace( 'ã', '&#227;', $stringa );
    $stringa = str_replace( 'ä', '&#228;', $stringa );
    $stringa = str_replace( 'å', '&#229;', $stringa );
    $stringa = str_replace( 'æ', '&#230;', $stringa );
    $stringa = str_replace( 'ç', '&#231;', $stringa );
    $stringa = str_replace( 'è', '&#232;', $stringa );
    $stringa = str_replace( 'é', '&#233;', $stringa );
    $stringa = str_replace( 'ê', '&#234;', $stringa );
    $stringa = str_replace( 'ë', '&#235;', $stringa );
    $stringa = str_replace( 'ì', '&#236;', $stringa );
    $stringa = str_replace( 'í', '&#237;', $stringa );
    $stringa = str_replace( 'î', '&#238;', $stringa );
    $stringa = str_replace( 'ï', '&#239;', $stringa );
    $stringa = str_replace( 'ð', '&#240;', $stringa );
    $stringa = str_replace( 'ñ', '&#241;', $stringa );
    $stringa = str_replace( 'ò', '&#242;', $stringa );
    $stringa = str_replace( 'ó', '&#243;', $stringa );
    $stringa = str_replace( 'ô', '&#244;', $stringa );
    $stringa = str_replace( 'õ', '&#245;', $stringa );
    $stringa = str_replace( 'ö', '&#246;', $stringa );
    $stringa = str_replace( '÷', '&#247;', $stringa );  // Yeah, I know.  But otherwise the gap is confusing.  --Kris
    $stringa = str_replace( 'ø', '&#248;', $stringa );
    $stringa = str_replace( 'ù', '&#249;', $stringa );
    $stringa = str_replace( 'ú', '&#250;', $stringa );
    $stringa = str_replace( 'û', '&#251;', $stringa );
    $stringa = str_replace( 'ü', '&#252;', $stringa );
    $stringa = str_replace( 'ý', '&#253;', $stringa );
    $stringa = str_replace( 'þ', '&#254;', $stringa );
    $stringa = str_replace( 'ÿ', '&#255;', $stringa );
    $stringa=str_replace('â€™','&#146;',$stringa);
    $stringa=str_replace('â€¦','...',$stringa);
    $stringa=str_replace('â€“','-',$stringa);
    $stringa=str_replace('â€œ','“',$stringa);
    $stringa=str_replace('â€','”',$stringa);
    $stringa=str_replace('â€˜','&#146;',$stringa);
    $stringa=str_replace('â€”','-',$stringa);
    return $stringa;
}
?>






