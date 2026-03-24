<script type="text/javascript" src="js/jmini.js" /></script>
<script>
 $jqspywall = jQuery.noConflict();
</script>
<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/1col/myconfig.php';
$userid = (int)$_COOKIE['memberID'];
$email=$_POST['paypal'];
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
body {
background:none repeat scroll 0 0 #333333;
font-family:Verdana;
font-size:11px;
margin:0;
text-align:center;
}
#pagina {
background:none repeat scroll 0 0 #999999;
height:320px;
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
border:1px solid #FFFFFF;
color:#FFFFFF;
font-size:18px;
height:36px;
line-height:33px;
margin:80px 135px 31px;
}
#return {
border:1px solid #FFFFFF;
color:#FFFFFF;
font-size:15px;
height:31px;
line-height:27px;
margin-left:285px;
width:315px;
}
#return:hover {
background:none repeat scroll 0 0 #333333;
}
#return a {
color:#FFFFFF;
}
#infoconferma {
font-size:14px;
font-weight:bold;
left:270px;
position:relative;
top:63px;
width:355px; }

#notifica:hover {
cursor:pointer;
background:#000;
color:#FFF; }

</style>

<html>
<head> </head>
<body>
  <div id="pagina">
  <div id="introright">
    <span class="title"><?php echo _t("_ibdw_1col_activaintro");?></span>   <br/>
    <span class="dett_activ"><?php echo _t("_ibdw_1col_spycodereq");?></span>
    </div>
    <div id="infoconferma"><?php echo _t("_ibdw_1col_infocoferma1");?> <?php echo $email;?> <?php echo _t("_ibdw_1col_infocoferma2");?></div>
    <div id="notifica" onclick =" location.href='http://www.ionoleggio.it/controlloac.php?email=<?php echo $email;?>&site=<?php echo $_SERVER['HTTP_HOST'];?>&tipe=1col&admin=<?php echo str_replace('http://','',str_replace('https://','',BX_DOL_URL_ROOT.$admin_dir));?>';">CONFIRM AND REQUIRE THE ACTIVACTION</div>
    <div id="return"><a href="../../../<?php echo $admin_dir;?>"><?php echo _t("_ibdw_1col_backadmin");?></a></div>  <br/>   <br/>
    <div id="return"><a href="activation.php"><?php echo _t("_ibdw_1col_backspymainactive");?></a></div>
  </div>
</body>
</html>