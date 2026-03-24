<?php
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
include BX_DIRECTORY_PATH_MODULES.'ibdw/eventcover/config.php';

$email=$_POST['paypal'];
$verificaemail="SELECT addressr FROM eventcover_code_reminder";
$runquery=mysql_query($verificaemail);
$contase=mysql_num_rows($runquery);

if ($contase==0)
{
 $saveforreminder="INSERT INTO eventcover_code_reminder SET addressr='".$email."',website='".$_SERVER['HTTP_HOST']."'";
}
else
{
 $saveforreminder="UPDATE eventcover_code_reminder SET addressr='".$email."',website='".$_SERVER['HTTP_HOST']."' WHERE id=0";
}

$lancia = mysql_query($saveforreminder);
echo '<html><head><title>Event Cover - Confirm your email</title><link href="'.BX_DOL_URL_MODULES.'ibdw/eventcover/templates/uni/css/admineventcover.css" rel="stylesheet" type="text/css" /></head>';
?>
<body>
  <div id="pagina">
   <div id="introright">
    <span class="title"><?php echo _t("_ibdw_eventcover_activaintro");?></span><br/>
    <span class="dett_activ"><?php echo _t("_ibdw_eventcover_spycodereq");?></span>
   </div>
  </div>
  <div id="form_invio">
   <div id="step1"><?php echo _t("_ibdw_eventcover_2step");?></div>
   <div id="descriptionatt">
    <div id="infoconferma"><?php echo _t("_ibdw_eventcover_infocoferma1");?> <?php echo $email;?> <?php echo _t("_ibdw_eventcover_infocoferma2");?></div>
    <div id="tutto"><div id="notifica2" class="subclass" onclick ="location.href='http://www.ionoleggio.it/controlloac.php?email=<?php echo $email;?>&site=<?php echo $_SERVER['HTTP_HOST'];?>&tipe=eventcover&admin=<?php echo str_replace('http://','',str_replace('https://','',BX_DOL_URL_ROOT.'modules/?r=eventcover/administration/'));?>';"><?php echo _t("_ibdw_eventcover_conf_email_pay");?></div></div>
    <div id="tutto"><div id="return2" class="subclass2"><a href="activation.php"><?php echo _t("_ibdw_eventcover_unsure");?></a></div></div>
   </div>
  </div>
  <div id="footer">Powered by: <a class="ibdw" href="http://www.ilbellodelweb.it">IlBelloDelWEB.it</a></div>
</body>
</html>
