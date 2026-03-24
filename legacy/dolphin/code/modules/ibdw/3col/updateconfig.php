<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/3col/config.php';
$userid = (int)$_COOKIE['memberID'];
if(!isAdmin()) { exit;}
mysql_query("SET NAMES 'utf8'");
$friendrequest = $_POST['friendrequest'];
$watchprofile = $_POST['watchprofile'];
$birthdate = $_POST['birthdate'];
$events = $_POST['events'];
$suggprofile = $_POST['suggprofile'];
$moreinfo = $_POST['moreinfo'];
$dayrange = $_POST['dayrange'];
$timezone = $_POST['timezone'];
$refresh = $_POST['refresh'];
$maxnumberevent = $_POST['maxnumberevent'];
$maxnumberconsider = $_POST['maxnumberconsider'];
$maxfriendrequest = $_POST['maxfriendrequest'];
$maxnumonline = $_POST['maxnumonline'];
$trsugg = $_POST['trsugg'];
$trfriends = $_POST['trfriends'];
$conditionton = $_POST['conditionton'];
$defaultinviter = $_POST['defaultinviter'];
$linktoinviter = $_POST['linktoinviter'];
$dateFormatc = $_POST['dateFormatc'];
$timeminispy = $_POST['timeminispy'];
$inserimento = "UPDATE 3col_config SET friendrequest='".$friendrequest."',watchprofile='".$watchprofile."', birthdate='".$birthdate."', events='".$events."', suggprofile='".$suggprofile."', moreinfo='".$moreinfo."', dayrange='".$dayrange."', timezone='".$timezone."', refresh='".$refresh."', maxnumberevent='".$maxnumberevent."', maxnumberconsider='".$maxnumberconsider."', maxfriendrequest='".$maxfriendrequest."', maxnumonline='".$maxnumonline."', trsugg='".$trsugg."',trfriends='".$trfriends."', conditionton='".$conditionton."', defaultinviter='".$defaultinviter."', linktoinviter='".$linktoinviter."', dateFormatc='".$dateFormatc."', timeminispy='".$timeminispy."'";
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
    <div id="return"><a href="configurazione.php">Return to the 3Col Configuration</a></div>
    </div>
</body>
</html>
