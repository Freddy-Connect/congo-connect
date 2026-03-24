<?php
echo '<div id="eliminatore" class="elimxx eliminatoremessaggio'.$codiceazione.'">
 <div id="substratoeliminazione">
  <div id="titoloeliminazione"><h2>'._t("_ibdw_evowall_bott_elimina_1").'</h2></div>
  <p>'._t("_ibdw_evowall_bott_elimina_2").'</p>
  <div id="sceltaeliminazione" onclick="javascript:conferma'.$codiceazione.'()">'._t("_ibdw_evowall_bott_elimina_3").'</div>
  <div id="sceltaeliminazione" onclick="javascript:annulla_eliminazione'.$codiceazione.'()">'._t("_ibdw_evowall_bott_elimina_4").'</div>
 </div>  
</div>';
?>