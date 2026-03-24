<?php
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';

$namephotoalbum=trim($_POST['namephotoalbum']);
?>


<div class="ibdw_evo_bt_list_choose" onclick="open_bt_list('x_evo_list');" id="fade_bt_listx_evo_list">
      <input type="hidden" value="0" id="mm_setmenux_evo_list" />
      <a class="bt_openx_evo_list" id="bt_open"><i alt="" class="sys-icon chevron-down"></i></a>
     
   
     <div class="ibdw_bt_superlist_swt" id="lista_btx_evo_list">
      <?php if($PhotoFlashM == 'on' ) { echo '<a id="bottone_sub_elimina" href="javascript:lanciahtml5foto(\''.$namephotoalbum.'\');">'._t('_ibdw_evowall_uploadhtml5').'</a>';}?>
      <?php if($PhotoOtherM=='on') { echo '<a id="bottone_sub_elimina" href="m/photos/albums/my/add_objects/'.$namephotoalbum.'">'._t('_ibdw_evowall_altrimetodi').'</a>'; } ?> 
     </div> 
</div>

<?php
echo BxDolService::call('photos', 'get_uploader_form', array(array('mode' => 'regular', 'album'=>$namephotoalbum)), 'Uploader');
?>