<?php
header("refresh: 2; ../../../administration");
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/1col/myconfig.php';

$userid = (int)$_COOKIE['memberID'];
if(!isAdmin()) { exit;} ?>
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
body {
background:none repeat scroll 0 0 #333333;
font-family:Verdana;
font-size:11px;
margin:0;
text-align:center;
}
#pagina {
background:none repeat scroll 0 0 #999999;
height:370px;
margin:30px auto auto;
padding:20px;
width:900px;
}
#form_invio {
float:left;
font-size:15px;
line-height:34px;
margin-left:201px;
margin-top:44px;
width:500px;
}
#form_conferma  {
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
.dett_activ  {
color:#FFFFFF;
font-size:10px;
line-height:15px;
}
#introright {
float:right;
text-align:right; }

#notifica {
margin:135px;
font-size:18px;
color:#FFF; }

</style>

<html>
<body>
 <div id="pagina">
  <div id="introright">
   <span class="title"><?php echo _t("_ibdw_1col_activaintro"); ?> </span><br/>
  </div>
 <div id="notifica">
<?php
mysql_query("SET NAMES 'utf8'");
$codice = $_POST['code'];
$onecript = "dsjfspfbdisbfs82342432pbdfuibfuidsbfur7384476353453432dasddsfsfsds";
$twocript = $_SERVER['HTTP_HOST'];
$trecript = "dsfsfd7875474g3yuewyrfoggogtoreyut7834733429362dd6sfisgfffegregege803";
$genera = $onecript.$twocript.$trecript;
if(md5($genera) === $codice) 
{
 echo _t("_ibdw_1col_activyes"); echo'<br/><img src="templates/base/images/loaderact.gif" />'; 
 $query  = "UPDATE one_code SET id = '1', code = '$codice' WHERE id = '1'";
 $result = mysql_query($query);
 $queryx  = "UPDATE sys_menu_admin SET name = '1Col Config' , title = '1Col Config', url = '{siteUrl}modules/ibdw/1col/configurazione.php', icon = 'modules/ibdw/1col/templates/base/images/|gearspy.png' WHERE name = 'Activation 1COL'";
 $resultx = mysql_query($queryx);
}
else {echo _t("_ibdw_1col_activno"); echo'<br/><img src="templates/base/images/loaderact.gif" />';}         
?>
</div>
</div>
</body>
</html>