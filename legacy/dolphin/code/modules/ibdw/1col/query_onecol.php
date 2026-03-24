<script>
function SetCookie(cookieName,cookieValue,nDays) 
{
 var today=new Date();
 var expire=new Date();
 if (nDays==null || nDays==0) nDays=1;
 expire.setTime(today.getTime()+3600000*24*nDays);
 document.cookie=cookieName+"="+escape(cookieValue)+";expires="+expire.toGMTString();
}
</script>
<?php

$paginadicontrollo=$_SERVER['PHP_SELF'];
$ajaxactive=strpos($paginadicontrollo,'member.php');
$ajaxactivehome=strpos($paginadicontrollo,'index.php');
if ($ajaxactive==0 AND $ajaxactivehome==0) 
{
 require_once('../../../inc/header.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
 require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
}
include BX_DIRECTORY_PATH_MODULES.'ibdw/1col/myconfig.php';


$ottieniID=(int)$_COOKIE['memberID'];
$photodeluxe=0;
//chek photodeluxe installed or not
$verificaphotodeluxe="SELECT uri FROM sys_modules WHERE uri='photo_deluxe'";
$eseguiverificaphotodeluxe=mysql_query($verificaphotodeluxe);
$numerophotodeluxe=mysql_num_rows($eseguiverificaphotodeluxe);
if($numerophotodeluxe!=0) $photodeluxe=1;
if($photodeluxe==1)
{ 
 //check integration with photodeluxe and 1col 
 $integrazionepdx="SELECT integrazione1col FROM photodeluxe_config WHERE ind=1";
 $eseguiintregazionepdx=mysql_query($integrazionepdx);
 $rowintegrazionepdx=mysql_fetch_assoc($eseguiintregazionepdx);
 $attivaintegrazione=$rowintegrazionepdx['integrazione1col']; 
}
if($attivaintegrazione==1) $photourl = BX_DOL_URL_ROOT."page/photodeluxe";

require_once(BX_DIRECTORY_PATH_INC .'match.inc.php');

$sMessaggioStato='';
switch ($MioStato)
 
{ 
 case 'Unconfirmed':$sMessaggioStato=_t('_ibdw_1col_unconfirmed');break;
 case 'Approval':$sMessaggioStato=_t('_ibdw_1col_approval');break;
 case 'Active':$sMessaggioStato=_t('_ibdw_1col_active');break;
 case 'Rejected':$sMessaggioStato=_t('_ibdw_1col_rejected');break;
 case 'Suspended':$sMessaggioStato=_t('_ibdw_1col_suspended');break;
}





mysql_query("SET NAMES 'utf8'");
//$queryverifica="SELECT uri FROM sys_modules WHERE uri='groups'";
$queryverifica="SELECT uri FROM sys_modules WHERE uri='formations'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_group=mysql_num_rows($queryverifica_exe);
if ($groupsvar=="ON" AND $numero_ver_group !=0)
{ 
 //groups number // FORMATIONS
 $query="SELECT * FROM `modzzz_formations_main` WHERE `author_id`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contagruppi=mysql_num_rows($result);
}


$queryverifica="SELECT uri FROM sys_modules WHERE uri='investment'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_file=mysql_num_rows($queryverifica_exe);
if ($filesvar=="ON" AND $numero_ver_file!=0)
{ 
 //files number
 $query="SELECT * FROM `modzzz_investment_main` WHERE `author_id`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contafile=mysql_num_rows($result);
}
$queryverifica="SELECT uri FROM sys_modules WHERE uri='events'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_event=mysql_num_rows($queryverifica_exe);
if ($evntvar=="ON" AND $numero_ver_event!=0)
{
 //events number
 $query="SELECT * FROM `bx_events_main` WHERE `ResponsibleID`=".$ottieniID." AND `Status`='approved'";
 $result=mysql_query($query);
 $contaeventi=mysql_num_rows($result);
 }
$queryverifica="SELECT uri FROM sys_modules WHERE uri='classified'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_classified=mysql_num_rows($queryverifica_exe);
if ($classifiedsvar=="ON" AND $numero_ver_classified != 0)
{
 //classifieds number
 $query="SELECT * FROM `modzzz_classified_main` WHERE `author_id`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contasiti=mysql_num_rows($result);
}
$queryverifica="SELECT uri FROM sys_modules WHERE uri='poll'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_poll=mysql_num_rows($queryverifica_exe);
// freddy if ($pollsvar=="ON" AND $numero_ver_poll!=0)
if ($pollsvar=="ON")
{
 //FREDDY changement pour profile view polls number 
/* $query="SELECT * FROM `bx_poll_data` WHERE `id_profile`=".$ottieniID." AND `poll_approval`=1";
 $result=mysql_query($query);
 $contasondaggi=mysql_num_rows($result);
 */
 //$query="SELECT `Views`  FROM `Profiles` WHERE `ID`=".$ottieniID." AND `Status`='Active'";
$ProfileDolphin=getProfileInfo($ottieniID);
 $contasondaggi=$ProfileDolphin['Views'];
 }
 
 
$queryverifica="SELECT uri FROM sys_modules WHERE uri='jobs'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_jobs=mysql_num_rows($queryverifica_exe);
if ($jobsvar=="ON" AND $numero_ver_jobs!=0)
{
 //jobs number
 $query="SELECT * FROM `modzzz_jobs_main` WHERE `author_id`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contaannunci=mysql_num_rows($result);
}

$queryverifica="SELECT uri FROM sys_modules WHERE uri='formations'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_formations=mysql_num_rows($queryverifica_exe);
if ($formationsvar=="ON" AND $numero_ver_formations!=0)
{
 //formations number
 $query="SELECT * FROM `modzzz_formations_main` WHERE `author_id`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $contaformation=mysql_num_rows($result);
}

$queryverifica="SELECT uri FROM sys_modules WHERE uri='investment'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_investment=mysql_num_rows($queryverifica_exe);
if ($investmentvar=="ON" AND $numero_ver_investment!=0)
{
 //investment number
 $query="SELECT * FROM `modzzz_investment_main` WHERE `author_id`=".$ottieniID." AND `status`='approved'";
 $result=mysql_query($query);
 $containvestment=mysql_num_rows($result);
}





$queryverifica="SELECT uri FROM sys_modules WHERE uri='blogs'";
$queryverifica_exe=mysql_query($queryverifica);
$numero_ver_blog=mysql_num_rows($queryverifica_exe);
if ($blogvar=="ON" AND $numero_ver_blog!=0)
{
 //blogs number
 $query="SELECT * FROM `bx_blogs_posts` WHERE `OwnerID`=".$ottieniID;
 $result=mysql_query($query);
 $contablog=mysql_num_rows($result);
}

//change from Zarcon to AQB if you want to use Page by AQB Soft
$listingmod='Zarcon';
if ($listingmod=='Zarcon')
{
 $queryverifica="SELECT uri FROM sys_modules WHERE uri='listing'";
 $queryverifica_exe=mysql_query($queryverifica);
 $numero_ver_listing=mysql_num_rows($queryverifica_exe);
 if ($listingsvar=="ON" AND $numero_ver_listing !=0)
 { 
  //listings number
  $query="SELECT * FROM `modzzz_listing_main` WHERE `author_id`=".$ottieniID." AND `status`='approved'";
  $result=mysql_query($query);
  $contapagine=mysql_num_rows($result);
 }
}
elseif ($listingmod=='AQB')
{
 $queryverifica="SELECT uri FROM sys_modules WHERE uri='aqb_pages'";
 $queryverifica_exe=mysql_query($queryverifica);
 $numero_ver_page=mysql_num_rows($queryverifica_exe);
 if ($listingsvar=="ON" AND $numero_ver_listing !=0)
 { 
  //listings number
  $query="SELECT * FROM `aqb_pages_main` WHERE `author_id`=".$ottieniID." AND `status`='approved'";
  $result=mysql_query($query);
  $contapagine=mysql_num_rows($result);
 }
}
//friends number
$query="SELECT * FROM `sys_friend_list` WHERE ((`Profile`=".$ottieniID." AND `Check`='1') OR (`ID`=".$ottieniID." AND `Check`='1'))";
$resultfriends=mysql_query($query);
$contaamici=mysql_num_rows($resultfriends);





//new messages
$query="SELECT * FROM `sys_messages` WHERE `Recipient`=".$ottieniID." AND `New`='1' AND NOT FIND_IN_SET('Recipient', `sys_messages`.`Trash`)";
$result=mysql_query($query);
$contanuovimessaggi=mysql_num_rows($result);
if ($contanuovimessaggi>0) $spaziatoreemail='<div class="col_account">';
 else $spaziatoreemail ='<div class="centerspaceempty">';
 if ($contagruppi>0) $spaziatoregroup='<div class="col_account">';
 else $spaziatoregroup ='<div class="centerspaceempty">';
 if ($contaeventi>0) $spaziatoreevent='<div class="col_account">';
 else $spaziatoreevent ='<div class="centerspaceempty">';
 if ($contaamici>0) $spaziatorefriend='<div class="col_account">';
 else $spaziatorefriend ='<div class="centerspaceempty">';


 if ($contasondaggi>0) $spaziatorepoll=' <div class="Profile_Views"> <i class="sys-icon eye"></i>';
 else $spaziatorepoll ='<div class="centerspaceempty">';
 
 
 if ($contaannunci>0) $spaziatoread='<div class="col_account">';
 else $spaziatoread ='<div class="centerspaceempty">';
 if ($contablog>0) $spaziatoreblog='<div class="col_account">';
 else $spaziatoreblog ='<div class="centerspaceempty">';
 if ($contasiti>0) $spaziatoreclassified='<div class="col_account">';
 else $spaziatoreclassified ='<div class="centerspaceempty">';
 if ($contafile>0) $spaziatorefile='<div class="col_account">';
 else $spaziatorefile ='<div class="centerspaceempty">';
 if ($contapagine>0) $spaziatorelisting='<div class="col_account">';
 else $spaziatorelisting ='<div class="centerspaceempty">';
if ($mainmenuvar=="ON")
{
 echo '<div id="rigamenu"><div class="titlemenuitem">'._t('_ibdw_1col_home').'</div></div>';
 


 if ($mailset=="ON") echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e1\').style.visibility = \'visible\';" onmouseout="document.getElementById(\'e1\').style.visibility = \'hidden\';"><div class="rightspace" id="e1"><a href="mail.php?mode=compose" title="'._t('_ibdw_1col_write').'"><i class="sys-icon pencil"></i></a></div><div class="leftspace"><i class="sys-icon envelope"></i><a href="mail.php?mode=inbox" class="titleitem">'._t('_ibdw_1col_mail').'</a>'.$spaziatoreemail.'<a href="'.$mailurl.'" class="mailbubble"><b>' . $contanuovimessaggi . '</b></a></div></div></div>';
 
 if ($amiciset=="ON") echo '<div id="rigamenu1"><div class="leftspace"><i class="sys-icon users"></i><a href="viewFriends.php?iUser=' . $ottieniID . '" class="titleitem">'._t('_ibdw_1col_friends').'</a>'.$spaziatorefriend.'<b>'.$contaamici.'</b></div></div></div>';
 
 
 if ($listingsvar=="ON" AND $numero_ver_listing!=0) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e9\').style.visibility = \'visible\';" onmouseout="document.getElementById(\'e9\').style.visibility = \'hidden\';"><div class="rightspace" id="e9"><a href="'.$addlistingurl.'" title="'._t(
 '_ibdw_1col_listing_ins').'"><i class="sys-icon pencil"></i></a></div><div class="leftspace"><i class="sys-icon building"></i><a href="'.$listingurl.'" class="titleitem">'._t('_ibdw_1col_listings').'</a>'.$spaziatorelisting.'<b>' . $contapagine . '</b></div></div></div>';
 
 
  if ($jobsvar=="ON" AND $numero_ver_jobs!=0) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e5\').style.visibility = \'visible\';" onmouseout="document.getElementById(\'e5\').style.visibility = \'hidden\';"><div class="rightspace" id="e5"><a href="'.$addjobsurl.'" title="'._t('_ibdw_1col_job_insert').'"><i class="sys-icon pencil"></i></a></div><div class="leftspace"><i class="sys-icon black-tie"></i><a href="'.$jobsurl.'" class="titleitem">'._t('_ibdw_1col_jobs').'</a>'.$spaziatoread.'<b>' . $contaannunci . '</b></div></div></div>';


 if ($groupsvar=="ON" AND $numero_ver_group!=0) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e2\').style.visibility = \'visible\';" onmouseout="document.getElementById(\'e2\').style.visibility = \'hidden\';"><div class="rightspace" id="e2"><a  href="'.$addgroupurl.'" title="'._t('_ibdw_1col_group_make').'"><i class="sys-icon pencil"></i></a></div><div class="leftspace"><i class="sys-icon graduation-cap"></i><a href="'.$groupurl.'" class="titleitem">'._t('_ibdw_1col_groups').'</a>'.$spaziatoregroup.'<b>' . $contagruppi . '</b></div></div></div>';
 
 

 
  if ($classifiedsvar=="ON" AND $numero_ver_classified!=0) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e7\').style.visibility = \'visible\';" onmouseout="document.getElementById(\'e7\').style.visibility = \'hidden\';"><div class="rightspace" id="e7"><a href="'.$addclassifiedurl.'" title="'._t(
'_ibdw_1col_classified_ins').'"><i class="sys-icon pencil"></i></a></div><div class="leftspace"><i class="sys-icon briefcase"></i><a href="'.$classifiedurl.'" class="titleitem">'._t('_ibdw_1col_classified').'</a>'.$spaziatoreclassified.'<b>'.$contasiti.'</b></div></div></div>';

 




 if ($filesvar=="ON" AND $numero_ver_file!=0) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e8\').style.visibility = \'visible\';" onmouseout="document.getElementById(\'e8\').style.visibility = \'hidden\';"><div class="rightspace" id="e8"><a href="'.$addfileurl.'" title="'._t('_ibdw_1col_classified_ins').'"><i class="sys-icon pencil"></i></a></div><div class="leftspace"><i class="sys-icon sliders"></i><a href="'.$fileurl.'" class="titleitem">'._t('_ibdw_1col_files').'</a>'.$spaziatorefile.'<b>' . $contafile . '</b></div></div></div>';
 
  if ($evntvar=="ON" AND $numero_ver_event!=0) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e3\').style.visibility = \'visible\';" onmouseout="document.getElementById(\'e3\').style.visibility = \'hidden\';"><div class="rightspace" id="e3"><a href="'.$addeventurl.'" title="'._t('_ibdw_1col_event_add').'"><i class="sys-icon pencil"></i></a></div><div class="leftspace"><i class="sys-icon calendar"></i><a href="'.$eventurl.'" class="titleitem">'._t('_ibdw_1col_events').'</a>'.$spaziatoreevent.'<b>'.$contaeventi.'</b></div></div></div>';
 
/* freddy modif
if ($blogvar=="ON" AND $numero_ver_blog!=0) */
if ($blogvar=="ON" )
echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e6\').style.visibility = \'visible\';" onmouseout="document.getElementById(\'e6\').style.visibility = \'hidden\';"><div class="rightspace" id="e6"><a href="'.$addblogurl.'" title="'._t('_ibdw_1col_blog_insert').'"><i class="sys-icon pencil"></i></a></div><div class="leftspace"><i class="sys-icon bell"></i><a href="'.$blogurl.getUsername($ottieniID).'" class="titleitem">'._t('_ibdw_1col_blogs').'</a>'.$spaziatoreblog.'<b>' . $contablog . '</b></div></div></div>';

/*freddy modif  if ($pollsvar=="ON" AND $numero_ver_poll!=0  */
 if ($pollsvar=="ON" ) echo '<div id="rigamenu1" onmouseover="document.getElementById(\'e4\').style.visibility = \'visible\';" onmouseout="document.getElementById(\'e4\').style.visibility = \'hidden\';"><div class="rightspace" id="e4"><a href="'.$addpollurl.'" title="'._t('_ibdw_1col_poll_add').'"><i class="sys-icon pencil"></i></a></div><div class="leftspace"><i class="sys-icon desktop"></i><a href="'.$pollurl.'" class="titleitem">'._t('_ibdw_1col_polls').'</a>'.$spaziatorepoll.'<b>' . $contasondaggi . '</b></div></div></div>';


 if($cs1 != '0') echo '<div id="rigamenu1"><div class="leftspace"><i alt="" class="sys-icon chevron-right"></i><a href="'.$cs1.'" class="titleitem">'._t('_ibdw_1col_customlink1').'</a></div></div>'; 
 if($cs2 != '0') echo '<div id="rigamenu1"><div class="leftspace"><i alt="" class="sys-icon chevron-right"></i><a href="'.$cs2.'" class="titleitem">'._t('_ibdw_1col_customlink2').'</a></div></div>';
 if($cs3 != '0') echo '<div id="rigamenu1"><div class="leftspace"><i alt="" class="sys-icon chevron-right"></i><a href="'.$cs3.'" class="titleitem">'._t('_ibdw_1col_customlink3').'</a></div></div>';
 if($cs4 != '0') echo '<div id="rigamenu1"><div class="leftspace"><i alt="" class="sys-icon chevron-right"></i><a href="'.$cs4.'" class="titleitem">'._t('_ibdw_1col_customlink4').'</a></div></div>';
 if($cs5 != '0') echo '<div id="rigamenu1"><div class="leftspace"><i alt="" class="sys-icon chevron-right"></i><a href="'.$cs5.'" class="titleitem">'._t('_ibdw_1col_customlink5').'</a></div></div>';
}



 if($slideotherinfo=="ON")
 { 
  echo '<div id="ibdw_altrosty" class="ibdw_bottid1" ';
  if($_COOKIE['slidedown']==1) echo 'style="display:none;"'; 
  echo '><a href="javascript:ibdw_mostra();">'._t('_ibdw_1col_altrobottone').'</a></div>
  <script>
  function ibdw_mostra()
  { 
   $(".ibdw_onecol_altro").css("display","block");
   $(".ibdw_bottid1").fadeOut(10);
   $(".colsep").fadeOut(10);
   SetCookie("slidedown","1","1");
  }
  </script>';
  echo '<div class="ibdw_onecol_altro"';
  if($_COOKIE['slidedown']==1) echo 'style="display:block"';
 }
 if ($slideotherinfo=="ON") echo'>';
 
 if ($mediavar=="ON")
 {
  echo '<div id="rigamenu"><div class="titlemenuitem">'._t('_ibdw_1col_media').'</div></div>';
  if ($photosvar=="ON") echo '<div id="rigamenu1"><div class="leftspace"><i class="sys-icon picture-o"></i><i class="sys-icon picture"></i><a href="'.$photourl.'" class="titleitem">'._t('_ibdw_1col_photos').'</a></div></div>';
  if ($videosvar=="ON") echo '<div id="rigamenu1"><div class="leftspace"><i class="sys-icon film"></i><a href="'.$videosurl.'" class="titleitem">'._t('_ibdw_1col_videos').'</a></div></div>';
  if ($soundvar=="ON")  echo '<div id="rigamenu1"><div class="leftspace"><i class="sys-icon music"></i><a href="'.$soundurl.'" class="titleitem">'._t('_ibdw_1col_sound').'</a></div></div>';
 }
 if($customnamesect1!='0' or $customnamesect2!='0' or $customnamesect3!='0' or $customnamesect4!='0' or $customnamesect5!='0') 
 { 
  echo '<div id="rigamenu"><div class="titlemenuitem">'._t('_ibdw_1col_customsectname').'</div></div>';
  if($customnamesect1!='0') echo '<div id="rigamenu1"><div class="leftspace"><i alt="" class="sys-icon chevron-right"></i><a href="'.$customnamesect1.'" class="titleitem">'._t('_ibdw_1col_customsect1').'</a></div></div>';
 
  if($customnamesect2!='0') echo '<div id="rigamenu1"><div class="leftspace"><i alt="" class="sys-icon chevron-right"></i><a href="'.$customnamesect2.'" class="titleitem">'._t('_ibdw_1col_customsect2').'</a></div></div>';
  
  if($customnamesect3!='0') echo '<div id="rigamenu1"><div class="leftspace"><i alt="" class="sys-icon chevron-right"></i><a href="'.$customnamesect3.'" class="titleitem">'._t('_ibdw_1col_customsect3').'</a></div></div>';
  if($customnamesect4!='0') echo '<div id="rigamenu1"><div class="leftspace"><i alt="" class="sys-icon chevron-right"></i><a href="'.$customnamesect4.'" class="titleitem">'._t('_ibdw_1col_customsect4').'</a></div></div>';
  if($customnamesect5!='0') echo '<div id="rigamenu1"><div class="leftspace"><i alt="" class="sys-icon chevron-right"></i><a href="'.$customnamesect5.'" class="titleitem">'._t('_ibdw_1col_customsect5').'</a></div></div>';
 }
 if ($accounteditvar=="ON")
 {
  echo '<div id="rigamenu"><div class="titlemenuitem">'._t('_ibdw_1col_account_set').'</div></div>';
/* Freddy commentaire changement ordre
  if($avaset=='ON') echo '<div id="rigamenu1"><div class="leftspace"><i class="sys-icon camera"></i><a href="'.$avatarurl.'" class="titleitem">'._t('_ibdw_1col_avatar').' '.'<i class="sys-icon pencil"></i>'.'</a></div></div>';
  
*/
////Freddy ajout
$valoriutente=getProfileInfo($ottieniID);
 $MiaEmail=$valoriutente['Email'];
 
  

  $sRegistration = date('d-m-Y  H:i'  ,strtotime($valoriutente['DateReg']));
   $sLastLogin = date('d-m-Y  H:i'  ,strtotime($valoriutente['DateLastLogin']));
       
 //////////////////
 
 if ($shemaila=="ON") echo '<div class="spacer5">'.'<i class="sys-icon envelope"></i>'/* freddy commentaire*._t('_ibdw_1col_email')*/. ' '.$MiaEmail.'</div>';
 echo '<div style="clear:both;"></div>';

////////////// Freddy ajout membership
echo _t('_MEMBERSHIP_EXPIRES_NEVER');

////////////////////////////

////////////// Freddy ajout last login & registration
echo '<div class="spacer5">'.'<i class="sys-icon sign-out"></i>'. ' '._t('_ibdw_1col_last_login').' <br>'.'<strong>'.$sLastLogin.'</strong>'.'</div>';

echo '<div class="spacer5">'.'<i class="sys-icon calendar"></i>'. ' '._t('_ibdw_1col_registration').' <br>'.'<strong>'.$sRegistration.'</strong>'.'</div>';
 

////////////////////////////


  if($privasett=='ON') echo '<div id="rigamenu1"><div class="leftspace"><i class="sys-icon key"></i><a href="member_privacy.php" class="titleitem">'._t('_ibdw_1col_privacy').'</a></div></div>';
  if($sottoscrizione=='ON') echo '<div id="rigamenu1"><div class="leftspace"><i class="sys-icon paperclip"></i><i class="sys-icon paper-clip"></i><a href="member_subscriptions.php" class="titleitem">'._t('_ibdw_1col_subscr').'</a></div></div>';
  if($deleteaccount=='ON') echo '<div id="rigamenu1"><div class="leftspace"><i alt="" class="sys-icon remove"></i><a href="unregister.php" class="titleitem">'._t('_ibdw_1col_del').'</a></div></div>';
 }


 if($slideotherinfo == "ON") 
 { 
  echo '<div id="ibdw_altrosty"><a href="javascript:ibdw_nasco();">'._t('_ibdw_1col_altroriduci').'</a></div>
        <script>
        function ibdw_nasco()
		{ 
         $(".ibdw_onecol_altro").css("display","none");
         $(".ibdw_bottid1").fadeIn(10);
         $(".colsep").fadeIn(10);
         SetCookie("slidedown","0","1");
        }
        </script> 
        </div>';
 }
 if (($sonlinefriends=="ON") and (get_user_online_status($ottieniID)==1))
 {
  $contatoreb=0;
  for($contatore=0;$contatore<$contaamici;$contatore++)
  {
   $listaamici=mysql_fetch_array($resultfriends);
   if ($listaamici[0]<>$ottieniID) $amico= $listaamici[0];
   else $amico= $listaamici[1];
   $stato=get_user_online_status($amico);
   if ($stato==1 and $amico<>$ottieniID)
   {
	$contatoreb++;
	if ($contatoreb==1) echo '<div class="recallbg"><div id="rigamenu"><div class="titlefriends">'._t('_ibdw_1col_onlinefriends').'</div></div></div><div id="menuelementfriends1">';
	
  $Miniaturaamico=get_member_icon($amico,'none',false);
  $NomeAmico=getNickname($amico);
  echo '<div id="rigamenuamico1"><div class="mioavatarsmall1">';
	//visualizzo l'avatar in stile dolphin oppure semplice
	echo $Miniaturaamico;
	echo '</div><div class="mioutentesmall1"><div class="nameof"><a href="'.getUsername($amico).'">'.$NomeAmico.'</a></div><input type="text" class="miachat" onkeyup="if ( typeof oSimpleMessenger != \'undefined\' ){oSimpleMessenger.sendMessage(event, this, ' . $amico . ')}" onclick="stopaggiornamento();this.value=\'\';" onblur="aggiornajx();" value="'._t('_ibdw_1col_chat_with') . " " .$NomeAmico . '" name="status_message"></div></div>';
   }
   if($contatoreb==$maxnumberonlinef) break;
  }
  if ($contatoreb>0) echo "</div>"; 
}
 echo '</div></div>';
 
?>