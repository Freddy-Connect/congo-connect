<?php
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';

//$namevideoalbum=trim($_POST['namevideoalbum']);
$idtoken=$_COOKIE['memberSession'];
$estraitoken="SELECT id,data FROM sys_sessions WHERE id='".$idtoken."'";
$eseguitoken=mysql_query($estraitoken);
if ($eseguitoken!=FALSE) 
{
 $associazione=mysql_fetch_assoc($eseguitoken);
 $unserialize=unserialize($associazione['data']);
 $tokenfinale=$unserialize['csrf_token'];
}
else $tokenfinale = 0;
$idprofile=getID($_REQUEST['ID']);

//Ottengo il nome predefinito per l'album dell'utente
$richiestanome="SELECT VALUE FROM sys_options WHERE Name='bx_videos_profile_album_name'";
$resultrichiestanome = mysql_query($richiestanome);
$rowrichiestanome = mysql_fetch_row($resultrichiestanome); 


$namevideoalbum=trim($_POST['namevideoalbum']);


?>
<div class="ibdw_evo_bt_list_choose" onclick="open_bt_list('x_evo_list');" id="fade_bt_listx_evo_list">
      <input type="hidden" value="0" id="mm_setmenux_evo_list" />
      <a class="bt_openx_evo_list" id="bt_open"><i alt="" class="sys-icon chevron-down"></i></a>
     
    
      <div class="ibdw_bt_superlist_swt" id="lista_btx_evo_list">
      <?php if($VideoFalshM == 'on' ) { echo '<a id="bottone_sub_elimina" href="javascript:lanciaclassic(\''.$namevideoalbum.'\');">'._t('_ibdw_evowall_uploadfrompc').'</a>';}?>
      <?php if($VideoOtherM=='on') { echo '<a id="bottone_sub_elimina" href="m/videos/albums/my/add_objects/'.$namevideoalbum.'">'._t('_ibdw_evowall_altrimetodi').'</a>'; } ?> 
     </div>
</div> 
<div id="videocontembed">
<?php 
 echo BxDolService::call('videos', 'get_uploader_form', array(array('mode' => 'embed', 'album'=>title2uri($namevideoalbum))), 'Uploader');
?>
</div>