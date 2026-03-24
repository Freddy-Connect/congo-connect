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
if(!isAdmin()) { exit;}
mysql_query("SET NAMES 'utf8'");
          
$controllorilevanza= "SELECT * FROM `one_code` LIMIT 0 , 30";
$resultax = mysql_query($controllorilevanza);
$conteggio = mysql_num_rows($resultax);
if ($conteggio == 0) { 
$inserimentoprimorecord = "INSERT INTO one_code (id,code) 
  VALUES ('1','0')";
$resultrec = mysql_query($inserimentoprimorecord);
}

  $controlloattivazione = "SELECT * FROM `one_code` WHERE id =1 LIMIT 0 , 30";
  $resultaa = mysql_query($controlloattivazione);
  $rowa = mysql_fetch_assoc($resultaa);
  $confronto = $rowa['code'];
  $onecript = "dsjfspfbdisbfs82342432pbdfuibfuidsbfur7384476353453432dasddsfsfsds";
  $twocript = $_SERVER['HTTP_HOST'];
  $trecript = "dsfsfd7875474g3yuewyrfoggogtoreyut7834733429362dd6sfisgfffegregege803";
  $genera = $onecript.$twocript.$trecript;
  
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
height:645px;
margin:30px auto auto;
padding:20px;
width:900px;
}
#form_invio {
border:1px solid #FFFFFF;
float:left;
font-size:15px;
line-height:34px;
margin-left:187px;
margin-top:44px;
padding:20px;
width:500px;
}
#form_conferma {
border:1px solid #666666;
float:left;
font-size:16px;
height:189px;
line-height:45px;
margin-left:187px;
margin-top:14px;
padding:20px;
width:500px;
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
.classeform1 {
color:#999999;
font-size:14px;
height:36px;
width:294px;
}
.classeform2 {
color:#999999;
font-size:11px;
height:36px;
width:358px;
}
.subclass {
background:none repeat scroll 0 0 #000000;
border:medium none;
color:#FFFFFF;
font-size:11px;
margin:13px 13px 0;
padding:7px;
text-transform:uppercase;
}
#return {
border:1px solid #FFFFFF;
color:#FFFFFF;
font-size:15px;
height:31px;
line-height:27px;
margin-left:93px;
margin-top:48px;
width:315px;
}
#return:hover {
background:none repeat scroll 0 0 #333333;
}
#step1 {
font-size:24px;
}
#step2 {
font-size:24px;}

</style>

<html>
<body>
  <div id="pagina">
  <div id="introright">
    <span class="title"><?php echo _t("_ibdw_1col_activaintro");?></span>   <br/>
    <span class="dett_activ"><?php echo _t("_ibdw_1col_spycodereq");?></span>
  </div>
    <?php if(md5($genera) === $confronto) { 
    echo '<div id="notifica">'._t("_ibdw_1col_yosattiva").' </div> <div id="introswich">
    <a href="delete.php">'._t("_ibdw_1col_sostituiscibott").'</a></div> </div></div></body> </html>'; exit; } 
    ?>
    
    
    <div id="form_invio">
    <div id="step1"> Step 1 </div>
    <form action="requirex.php" method="post">
    <span class="dett_activ"><?php echo _t("_ibdw_1col_introattivazione");?></span>  <br/><br/>
    <input type="text" name="paypal" value="Insert your email address (Paypal/Echeck)" size="37" id="reset" onclick="resetta();" class="classeform1"> <br/> 
    <input type="submit" value="Send Request" class="subclass">
    
    <script>
    function resetta(){
    $jqspywall("#reset").val("");
    $jqspywall(".classeform1").css("color","black");
    }
    </script>
    <br/>
    </form>
    </div>
    
    <div id="form_conferma">
    <div id="step2"> Step 2 </div>
    <form action ="redirect.php" method="post">
    CODE ACTIVATION<br/>
    <span class="dett_activ"><?php echo _t("_ibdw_1col_entrecode");?></span>
    <input type="text" size="62" name="code" value="Insert the activation code you receive via email (ACTIVATION CODE)" id="resettwo" onclick="resettatwo();" class="classeform2"><br/>
    <input type="submit" value="ACTIVATE" class="subclass">
    </form>
    <script>
    function resettatwo(){
    $jqspywall("#resettwo").val("");
    $jqspywall(".classeform2").css("color","black");
    
    }
    </script>
    <div id="return"><a href="../../../<?php echo $admin_dir;?>"><?php echo _t("_ibdw_1col_backadmin");?></a></div>  <br/>   <br/>
    </div>
  </div>
</body>
</html>