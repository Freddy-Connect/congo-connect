<?php
//force mobile devices to display the share button for the comment box
//$enablecommentbutton='on' or 'off'
//if on, the share button will be displayed on the mobile devices, otherwise
//the comment will be sent using the normal enter key
$enablecommentbutton='on';

$retriveidevowall="SELECT ID FROM sys_options_cats WHERE name='EVO Wall'";
$risultatoid = mysql_query($retriveidevowall);
$idmodulo=mysql_fetch_assoc($risultatoid);
$querydiconfigurazione="SELECT Name, VALUE FROM sys_options WHERE kateg=".$idmodulo['ID'];
$risultato = mysql_query($querydiconfigurazione);
while ($feccia=mysql_fetch_array($risultato))
{
 $name = $feccia['Name'];
 $riga[$name]=$feccia['VALUE'];
}
$color=$riga['TemplateColor'];
$licensekey=$riga['LicenseKey'];
$DefaultProfilePrivacy=$riga['DefaultProfilePrivacy'];
$DefaultAccountPrivacy=$riga['DefaultAccountPrivacy'];
$DefaultHomePrivacy=$riga['DefaultHomePrivacy'];
$photo=$riga['AllowPhotos'];
$video=$riga['AllowVideos'];
$group=$riga['AllowGroups'];
$bxpage=$riga['AllowPages'];
$event=$riga['AllowEvents'];
$eventmodule=$riga['EventMod'];
$bxsite=$riga['AllowSites'];
$poll=$riga['AllowPolls'];
$pollmodule=$riga['PollsMod'];
$ads=$riga['AllowAds'];
$blogs=$riga['AllowBlogs'];
$sounds=$riga['AllowSounds'];
$AllowShareNotification=$riga['AllowShareNotification'];
$AllowLikeNotification=$riga['AllowLikeNotification'];
$AllowCommentNotification=$riga['AllowCommentNotification'];
$AllowMessageNotification=$riga['AllowMessageNotification'];
$messlength=$riga['MessageLenght'];
$commlength=$riga['CommentLenght'];
$commprevnum=$riga['CommentPrevNum'];
$commnum=$riga['CommentNum'];
$newline=$riga['NewLine'];
$hideupdate=$riga['DisplayProfileUpdate'];
$displayMessageStatus=$riga['DisplayMessageStatusUpdate'];
$ordinec=$riga['CommentOrder'];
$namephotoalbum=str_replace(" ","-",preg_replace('/\s{2,}/',' ',$riga['WallPhotoName']));
$namevideoalbum=str_replace(" ","-",preg_replace('/\s{2,}/',' ',$riga['WallVideoName']));
$namephotoalbum=str_replace("'","-",$namephotoalbum);
$namevideoalbum=str_replace("'","-",$namevideoalbum); 
$privacyalbum=$riga['WallDefaultPrivacy'];
if($privacyalbum == 'Default') { $privacyalbum=1; }
elseif($privacyalbum == 'Me Only') { $privacyalbum=2; }
elseif($privacyalbum == 'Public') { $privacyalbum=3; }
elseif($privacyalbum == 'Members') { $privacyalbum=4; }
elseif($privacyalbum == 'Friends') { $privacyalbum=5; }
elseif($privacyalbum == 'Faves') { $privacyalbum=6; }            
$grouping=$riga['Grouping'];
$limite=$riga['DisplayNewsNumber'];
$spyprofileview=$riga['ProfileViewedBy'];
$bkunconfirmed=$riga['DenyAccessToUnconfirmed'];
$refreshtype=$riga['RefreshType'];
$refreshtime=$riga['Refreshrate']*60000;
$autoscroll=$riga['AutoScroll'];
$delaymillisecond = $riga['AutoScrollTime'];
$hidemorenews=$riga['HideMoreNewsButton'];
$seldate=$riga['DateFormat'];

if ($seldate=="mm/dd/yyyy 24H" or $seldate=="mm/dd/yyyy")
{
 $seldate="m/d/Y H:i:s";
}
elseif($seldate=="mm/dd/yyyy 24H (AM/PM)")
{
 $seldate="m/d/Y H:i:s:A";
}
elseif($seldate=="mm/dd/yyyy 12H")
{
 $seldate="m/d/Y g:i:s";
}
elseif($seldate=="mm/dd/yyyy 12H (AM/PM)")
{
 $seldate="m/d/Y g:i:s:A";
}
elseif ($seldate=="dd/mm/yyyy 24H" or $seldate=="dd/mm/yyyy")
{
 $seldate="d/m/Y H:i:s";
}
elseif ($seldate=="dd/mm/yyyy 24H (AM/PM)")
{
 $seldate="d/m/Y H:i:s:A";
}
elseif ($seldate=="dd/mm/yyyy 12H")
{
 $seldate="d/m/Y g:i:s";
}
elseif ($seldate=="dd/mm/yyyy 12H (AM/PM)")
{
 $seldate="d/m/Y g:i:s:A";
}
elseif ($seldate=="yyyy/mm/dd 24H" or $seldate=="yyyy/mm/dd")
{
 $seldate="Y/m/d H:i:s";
}
elseif ($seldate=="yyyy/mm/dd 24H (AM/PM)")
{
 $seldate="Y/m/d H:i:s:A";
}
elseif ($seldate=="yyyy/mm/dd 12H")
{
 $seldate="Y/m/d g:i:s";
}
elseif ($seldate=="yyyy/mm/dd 12H (AM/PM)")
{
 $seldate="Y/m/d g:i:s:A";
}

$offset=$riga['Offset']; 
$welcome=$riga['WelcomeMessage'];
$welcome_n_query = $riga['WelcomeNPost'];
$welcome_cookie_day = $riga['WelcomeCLife'];
$VideoYoutubeM =  $riga['VideoYoutubeM'];
$VideoFalshM =  $riga['VideoFalshM'];
$VideoOtherM =  $riga['VideoOtherM'];
$PhotoRegularM =  $riga['PhotoRegularM'];
$PhotoFlashM =  $riga['PhotoFlashM'];
$PhotoOtherM =  $riga['PhotoOtherM'];
$DefaultPhotoM =  $riga['DefaultPhotoM'];
$DefaultVideoM =  $riga['DefaultVideoM'];
$UrlPlugin =  $riga['UrlPlugin'];
$daysmostpopular =$riga['DaysMostPopular'];
$modzzzproperty =$riga['ModzzzProperty'];
$ue30locations =$riga['UE30Locations'];
$timing_similar = $riga['TimingSimilar'];
$photo_max_preview=$riga['PhotoMaxPreview'];
$fbenabled=$riga['AllowFacebook'];
$fbapikey=$riga['FacebookKID'];
$ggenabled=$riga['AllowGoogle'];
$twenabled=$riga['AllowTwitter'];
$lienabled=$riga['AllowLinkedIn'];
$psenabled=$riga['AllowPinterest'];
$bienabled=$riga['AllowBaidu'];
$wbenabled=$riga['AllowWeibo'];
$qzenabled=$riga['AllowQzone'];

//other 3d party modules
$modzzzclubs="on";
$modzzzpetitions="on";
$modzzzpets="on";
$modzzzbands="on";
$modzzzschools="on";
$modzzznotices="on";
$modzzzclassified="on";
$modzzznews="on";
$modzzzjobs="on";
$modzzzformations="on";
$modzzzinvestment="on";

$modzzzarticles="on";
$modzzzlist="on";
$modzzzfamily="on";
$modzzzrelation="on";
$modzzzdeal="on";
$modzzzpolls="on";
$modzzzprovider="on";
$modzzzresume="on";
$andrewpcars="on";
$andrewpjob="on";
$andrewpbuslist="on";
$andrewprealestate="on";
$kolimarfeyplaces="on";
$rayzlive="on";
$badwords=$riga['BadWordsFilter'];
if ($badwords=="on")
{
 $wordlist=trim ($riga['BadWordsKeys']);
}
?>