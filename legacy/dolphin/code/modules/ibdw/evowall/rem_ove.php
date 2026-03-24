<script>function eliminazione<?php echo $codiceazione;?>() {}</script>
<?php
echo '
<a id="bottone_sub_elimina" class="bottone_sub_elimina'.$codiceazione.'" href="javascript:substratoeliminazione'.$codiceazione.'()"><i class="sys-icon remove" alt=""></i>'._t("_ibdw_evowall_delete").'</a>
<script>
    function substratoeliminazione'.$codiceazione.'() {$(".elimxx").fadeOut(); $(".condxx").fadeOut(); $(".eliminatoremessaggio'.$codiceazione.'").fadeIn(1); open_bt_list('.$codiceazione.');oscura();}
    function annulla_eliminazione'.$codiceazione.'() {$(".eliminatoremessaggio'.$codiceazione.'").fadeOut(1);schiarisci();}
    function conferma'.$codiceazione.'() 
      { 
          ajax_load_active();
          var id='.$codiceazione.';
          $.ajax({
          type: "POST", 
          url: "modules/ibdw/evowall/remove.php", 
          data: "id=" + id + "&user='.$profileid.'&pagina='.$pagina.'&ultimoid='.$ultimoid.'",
          success: function(data){ 
          $("#correzione").html(data);
          ajax_load_close();
          schiarisci();
          }
      });
      }
</script>';
?>