<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/1col/myconfig.php';
$userid = (int)$_COOKIE['memberID'];
if(!isAdmin()) { exit;}
mysql_query("SET NAMES 'utf8'");
$photo = $_POST['photo'];
$video = $_POST['video'];
$group = $_POST['group'];
$page = $_POST['page'];
$event = $_POST['event'];
$site = $_POST['site'];
$poll = $_POST['poll'];
$ads = $_POST['ads'];
$blog = $_POST['blog'];
$file = $_POST['file'];
$sound = $_POST['sound'];
$emailad = $_POST['emailad'];
$status = $_POST['status'];
$city = $_POST['city'];
$slide = $_POST['slide'];
$numbermaxfriend = $_POST['numbermaxfriend'];
$timereload = $_POST['timereload'];
$mainmenuvar = $_POST['mainmenuvar'];
$mediavar = $_POST['mediavar'];
$acceditvar = $_POST['acceditvar'];
$onlinefriendvar = $_POST['onlinefriendvar'];
$deletebutton = $_POST['deletebutton'];
$avaset = $_POST['avaset'];
$privasett = $_POST['privasett'];
$sottoscrizione  = $_POST['sottoscrizione'];
$mailset = $_POST['mailset'];
$amiciset = $_POST['amiciset'];
if(trim($_POST['customlink_1']!='')){$customlink_1 = trim($_POST['customlink_1']);} else { $customlink_1 = ''; }
if(trim($_POST['customlink_2']!='')){$customlink_2 = trim($_POST['customlink_2']);} else { $customlink_2 = ''; }
if(trim($_POST['customlink_3']!='')){$customlink_3 = trim($_POST['customlink_3']);} else { $customlink_3 = ''; }
if(trim($_POST['customlink_4']!='')){$customlink_4 = trim($_POST['customlink_4']);} else { $customlink_4 = ''; }
if(trim($_POST['customlink_5']!='')){$customlink_5 = trim($_POST['customlink_5']);} else { $customlink_5 = ''; }
if(trim($_POST['customlinksect1']!='')){$customsect1 = trim($_POST['customlinksect1']);} else { $customsect1 = ''; }
if(trim($_POST['customlinksect2']!='')){$customsect2 = trim($_POST['customlinksect2']);} else { $customsect2 = ''; }
if(trim($_POST['customlinksect3']!='')){$customsect3 = trim($_POST['customlinksect3']);} else { $customsect3 = ''; }
if(trim($_POST['customlinksect4']!='')){$customsect4 = trim($_POST['customlinksect4']);} else { $customsect4 = ''; }
if(trim($_POST['customlinksect5']!='')){$customsect5 = trim($_POST['customlinksect5']);} else { $customsect5 = ''; }
$mailurl = addslashes($_POST['mailurl']);
$groupurl = addslashes($_POST['groupurl']);
$addgroupurl = addslashes($_POST['addgroupurl']);
$pageurl = addslashes($_POST['pageurl']);
$addpageurl = addslashes($_POST['addpageurl']);
$eventurl = addslashes($_POST['eventurl']);
$addeventurl = addslashes($_POST['addeventurl']);
$pollurl = addslashes($_POST['pollurl']);
$addpollurl = addslashes($_POST['addpollurl']);
$adsurl = addslashes($_POST['adsurl']);
$addadsurl = addslashes($_POST['addadsurl']);
$siteurl = addslashes($_POST['siteurl']);
$fileurl = addslashes($_POST['fileurl']);
$addfileurl = addslashes($_POST['addfileurl']);
$addsiteurl = addslashes($_POST['addsiteurl']);
$photourl = addslashes($_POST['photourl']);
$videourl = addslashes($_POST['videourl']);
$soundurl = addslashes($_POST['soundurl']);
$avatarurl = addslashes($_POST['avatarurl']);
$blogurl = addslashes($_POST['blogurl']);
$addblogurl = addslashes($_POST['addblogurl']);
$inserimento="UPDATE 1col_config SET foto='".$photo."', video='".$video."', gruppi='".$group."', eventi='".$event."', siti='".$site."', sondaggi='".$poll."', annunci='".$ads."', blog='".$blog."', file='".$file."', suoni='".$sound."', pagine='".$page."', emailad='".$emailad."', status='".$status."', city='".$city."', slide='".$slide."', numbermaxfriend='".$numbermaxfriend."', timereload='".$timereload."', mainmenuvar='".$mainmenuvar."', mediavar='".$mediavar."', acceditvar='".$acceditvar."', onlinefriendvar='".$onlinefriendvar."', deletebutton='".$deletebutton."', mailurl='".$mailurl."', addgroupurl='".$addgroupurl."', addpageurl='".$addpageurl."',eventurl='".$eventurl."', addeventurl='".$addeventurl."', pollurl='".$pollurl."', addpollurl='".$addpollurl."', adsurl='".$adsurl."', addadsurl='".$addadsurl."', siteurl='".$siteurl."', fileurl='".$fileurl."', addfileurl='".$addfileurl."', addsiteurl='".$addsiteurl."', photourl='".$photourl."', videourl='".$videourl."', soundurl='".$soundurl."', avatarurl='".$avatarurl."', groupurl='".$groupurl."', pageurl='".$pageurl."', avaset='".$avaset."', privasett='".$privasett."', sottoscrizione='".$sottoscrizione."', mailset='".$mailset."', blogurl='".$blogurl."', addblogurl='".$addblogurl."', customlink1 = '".$customlink_1."', customlink5='".$customlink_5."', customlink2='".$customlink_2."', customlink3='".$customlink_3."', customlink4='".$customlink_4."', customsectn='".$customsectn."', customsect1='".$customsect1."', customsect2='".$customsect2."', customsect3='".$customsect3."', customsect4='".$customsect4."', customsect5='".$customsect5."', amiciset='".$amiciset."'";
$resultquery = mysql_query($inserimento) or die(mysql_error());
?>
<style>
body, td, th {
}
a {
color:#000000;
text-decoration:none;
}
a:hover {
color:#FFFFFF;
text-decoration:none;
}
body  {
background:none repeat scroll 0 0 #334962;
font-family:Verdana;
font-size:11px;
margin:0;
text-align:center; 
}
#pagina  {
background:url("css/immagini/spyconfiglogo.png") no-repeat scroll 35px 22px #283B51;
border:7px solid #FFFFFF;
color:#FFFFFF;
height:1082px;
margin:30px auto auto;
padding:20px;
width:900px; }

#form_invio {
float:left;
font-size:15px;
line-height:34px;
margin-left:201px;
margin-top:44px;
width:500px;
}
#form_conferma {
float:left;
font-size:16px;
line-height:45px;
margin-left:225px;
margin-top:25px;
width:429px;
}
.title {
font-size:27px;
text-transform:uppercase;
}
.dett_activ {
color:#FFFFFF;
font-size:10px;
line-height:15px;
}
#introright {
float:right;
text-align:right;
}
#notifica {
color:#FFFFFF;
font-size:18px;
margin:135px;
}
#boxgeneraleconfigurazione  {
float:left;
margin-top:101px;
padding:20px;
text-align:left;
width:854px;
}
.introtitle {
font-size:17px;
font-weight:bold;
}
.introdesc  {
color:#5381E1;
font-size:11px;
font-style:italic;
}
#contentbox {
border:3px double #FFFFFF;
float:left;
line-height:15px;
margin:10px;
padding:10px;
width:365px; }

#return  {
border:1px solid #FFFFFF;
color:#FFFFFF;
font-size:15px;
height:31px;
line-height:27px;
width:315px;
margin-left:285px; }

#return:hover {
background:none repeat scroll 0 0 #999999;}

#return a { color:#FFF; }

</style>

<html>
<body>
  <div id="pagina">

  <div id="notifica">Update completed successfully</div>

    <div id="return"><a href="../../../<?php echo $admin_dir;?>"">Return to the main administration</a></div>  <br/>   <br/>
    <div id="return"><a href="configurazione.php">Return to the 1Col Configuration</a></div>
    </div>
</body>
</html>
