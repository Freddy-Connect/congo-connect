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
 
/* Freddy ajout de pour afficher le statut . on affecte à  $sProfileStatus la valeur de $MioStato = _t( "__$MioStato" ); */
$sProfileStatus = _t( "__$MioStato" );
///////////////////////////////////////////////////
/// freddy ajout 

$sMessaggioStato='';
switch ($MioStato)

/* Freddy modif 
{ 
 case 'Unconfirmed':$sMessaggioStato=_t('_ibdw_1col_unconfirmed');break;
 case 'Approval':$sMessaggioStato=_t('_ibdw_1col_approval');break;
 case 'Active':$sMessaggioStato=_t('_ibdw_1col_active');break;
 case 'Rejected':$sMessaggioStato=_t('_ibdw_1col_rejected');break;
 case 'Suspended':$sMessaggioStato=_t('_ibdw_1col_suspended');break;
}
*/
/////// Freddy recopier de BxBaseAccountView
{
 case 'Unconfirmed':$sMessaggioStato=_t("_ATT_UNCONFIRMED" , $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight);break;
 case 'Approval':$sMessaggioStato=_t("_ATT_APPROVAL", $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight);break;
 case 'Active':$sMessaggioStato=_t("_ibdw_1col_active" ,$valoriutente['ID'], $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight);break;
 case 'Rejected':$sMessaggioStato=_t("_ATT_REJECTED", $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight);break;
 case 'Suspended':$sMessaggioStato=_t("_ATT_SUSPENDED", $oTemplConfig -> popUpWindowWidth, $oTemplConfig -> popUpWindowHeight);break;
}

//Fin freddy modif
///////////////////////////////////////////////////////////////////////////////
 
 echo '<div><div class="menuelement1"><div class="infoutentecont"><div class="infoutente1">';
 echo '<div class="mioavatar1col">'.get_member_thumbnail($ottieniID, 'none', false).'</div>';
 echo '<div id="infomembercont">';
 echo '<div class="spacer1"><a href="'.$LinkUtente.'">'.$NomeUtente.'</a></div><div class="spacer2"><a href="pedit.php?ID=' . $ottieniID . '">'._t('_ibdw_1col_settings').'</a></div>';
 
  if($avaset=='ON') echo  '<div class="spacer2"><i class="sys-icon camera"></i><a href="'.$avatarurl.'" class="titleitem">'._t('_ibdw_1col_avatar').' '.'<i class="sys-icon pencil"></i>'.'</a></div>';
  
 
  
 if($scity == 'ON') echo '<div class="spacer3">'.$MiaCitta.'</div>';
 if($status == 'ON')
 {
  echo '<div class="spacer4">'._t('_ibdw_1col_status')/*    freddy commentaire.' <b>'.$sMessaggioStato.'</b> '*/;
 ?>

 <a onclick="javascript:window.open('explanation.php?explain=<?php echo $MioStato;?>','','width=660,height=200,menubar=no,status=no,resizable=no,scrollbars=yes,toolbar=no, location=no' );" href="javascript:void(0);"><?php /*echo 
 freddy modif ajout de $sProfileStatus pour afficher le Statut*/echo $sProfileStatus /* freddy comment .'<br> '._t('_ibdw_1col_expl') */; ?></a> <br /> <a href="change_status.php"><?php /* echo _t('_ibdw_1col_suspend');*/ echo $sMessaggioStato ?></a></div>
 <?php
 }
 
 echo '</div></div></div><div style="clear:both;"></div>';
 echo '<div id="maincont1col">';
 include BX_DIRECTORY_PATH_MODULES.'ibdw/1col/query_onecol.php';
 echo '</div>';
}
echo '</div>';
?>
<div class="clear_both"></div>