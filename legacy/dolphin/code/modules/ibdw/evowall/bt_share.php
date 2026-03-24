<script>
 function bt_share<?php echo $assegnazione;?>(){
 $.ajax({
  type: "POST",url: "modules/ibdw/evowall/share.php",data:"1=<?php echo  $bt_share_params['1'];?>&2=<?php echo  $bt_share_params['2'];?>&3=<?php echo $bt_share_params['3'];?>&4=<?php echo $bt_share_params['4'];?>", 
  success: function(){$(".condivisionemessaggio<?php echo $assegnazione;?>").fadeOut();<?php if ($paginamia==1) { echo "agg_ajax();";} ?>schiarisci();notifica_generale('<?php echo str_replace("'","&apos;",_t("_ibdw_evowall_bott_notifica_sharenotify"));?>');}});
}
</script>