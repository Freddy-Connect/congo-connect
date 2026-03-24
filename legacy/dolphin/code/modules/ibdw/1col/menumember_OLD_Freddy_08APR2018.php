<?php 
include BX_DIRECTORY_PATH_MODULES.'ibdw/1col/myconfig.php'; ?>
<script>
var tf;
function aggiornajx() {
  $.ajax({
   url: 'modules/ibdw/1col/query_onecol.php',
   cache: false,
    success: function(data) {
     $('#maincont1col').html(data);
    }
});
tf=setTimeout('aggiornajx()',<?php echo $timereload; ?> );
}
tf=setTimeout('aggiornajx()',<?php echo $timereload; ?> );
function stopaggiornamento()
{
clearTimeout(tf);
}
</script>

<?php
echo '<link href="modules/ibdw/1col/templates/base/css/style.css" rel="stylesheet" type="text/css" />';
echo '<div id="ajaxload" style="display:none;"> </div>';
echo '<div id="richieste_ajx">';
mysql_query("SET NAMES 'utf8'");
$controllopass= "SELECT * FROM one_code LIMIT 0,1";
$risultato = mysql_query($controllopass) or die(mysql_error());
$estrazione = mysql_fetch_assoc($risultato);
$controllo = $estrazione['code'];
$onecript = "dsjfspfbdisbfs82342432pbdfuibfuidsbfur7384476353453432dasddsfsfsds";
$twocript = $_SERVER['HTTP_HOST'];
$trecript = "dsfsfd7875474g3yuewyrfoggogtoreyut7834733429362dd6sfisgfffegregege803";
$genera = $onecript.$twocript.$trecript;
if (md5($genera)!= $controllo and isAdmin()) echo '<b>'._t('_ibdw_1col_sicurity').'</b>'; 
else 
{
 $ottieniID=(int)$_COOKIE['memberID'];
 $valoriutente=getProfileInfo($ottieniID);
 $MiaCitta=$valoriutente['City'];
 $MioStato=$valoriutente['Status'];
 $visitato=(int)$valoriutente['Views'];
 $MiaEmail=$valoriutente['Email'];
 $NomeUtente=getNickname($ottieniID);
 $LinkUtente=getUsername($ottieniID);
 echo '<div><div class="menuelement1"><div class="infoutentecont"><div class="infoutente1">';
 echo '<div class="mioavatar1col">'.get_member_thumbnail($ottieniID, 'none', false).'</div>';
 echo '<div id="infomembercont">';
 echo '<div class="spacer1"><a href="'.$LinkUtente.'">'.$NomeUtente.'</a></div><div class="spacer2"><a href="pedit.php?ID=' . $ottieniID . '">'._t('_ibdw_1col_settings').'</a></div>';
 if($scity == 'ON') echo '<div class="spacer3">'.$MiaCitta.'</div>';
 if($status == 'ON')
 {
  echo '<div class="spacer4">'._t('_ibdw_1col_status').' <b>'.$sMessaggioStato.'</b> ';
 ?>
 (<a onclick="javascript:window.open('explanation.php?explain=<?php echo $MioStato;?>','','width=660,height=200,menubar=no,status=no,resizable=no,scrollbars=yes,toolbar=no, location=no' );" href="javascript:void(0);"><?php echo _t('_ibdw_1col_expl');?></a>, <a href="change_status.php"><?php echo _t('_ibdw_1col_suspend');?></a>)</div>
 <?php
 }
 if ($shemaila=="ON") echo '<div class="spacer5">'._t('_ibdw_1col_email'). ' '.$MiaEmail.'</div>';
 echo '</div></div></div><div style="clear:both;"></div>';
 echo '<div id="maincont1col">';
 include BX_DIRECTORY_PATH_MODULES.'ibdw/1col/query_onecol.php';
 echo '</div>';
}
echo '</div>';
?>
<div class="clear_both"></div>