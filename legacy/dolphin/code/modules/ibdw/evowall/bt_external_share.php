<?php
echo '<div id="eliminatore" class="condxx condivisionemessaggio'.$assegnazione.'" >
          <div id="substratoeliminazione">
          <div id="titoloeliminazione"><h2>'._t("_ibdw_evowall_bott_condividi_1").'</h2></div>
		      <p>'._t("_ibdw_evowall_bott_condividi_2").'</p>             
          <div id="sceltaeliminazione" onclick="javascript:bt_share'.$assegnazione.'();">'._t("_ibdw_evowall_bott_condividi_3").'</div>
          <div id="sceltaeliminazione" onclick="javascript:annulla_condivisione'.$assegnazione.'();">'._t("_ibdw_evowall_bott_condividi_4").'</div>
      </div></div>';
?>