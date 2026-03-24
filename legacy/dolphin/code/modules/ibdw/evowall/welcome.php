<?php
  $sql = "SELECT COUNT(*) AS verifica FROM bx_spy_data WHERE sender_id = ".(int)$_COOKIE['memberID'];
  $exesql = mysql_query($sql);
  $fetchsql = mysql_fetch_assoc($exesql);
  $numerazione_query = $fetchsql['verifica'];
  if(($numerazione_query < $welcome_n_query+1) and (!isset($_COOKIE['welcomeoff']))) 
  { 
?> 
<div id="welcomebox">
<div id="avatarsimple">
<i class="info sys-icon info-sign"></i>
</div>
<div class="warning" id="messaggio">
<div id="primariga" class="warningrow">
<?php echo _t("_ibdw_evowall_welcome_title");?><?php echo _t("_ibdw_evowall_welcome_message");?>
</div>
<div id="cont_bottoni" style="margin-top:10px;">
<div id="matitaintro"><i alt="" class="sys-icon comments"></i><a href="javascript:nonmostrare();"><span class="comfx">
<?php echo _t("_ibdw_evowall_welcome_hidemessage");?>
</span></a></div>
</div>
</div>
</div>
<script>
  function setCookie(sNome, sValore, iGiorni) {
    var dtOggi = new Date()
    var dtExpires = new Date()
  dtExpires.setTime
    (dtOggi.getTime() + 24 * iGiorni * 3600000)
  document.cookie = sNome + "=" + escape(sValore) +
    "; expires=" + dtExpires.toGMTString();
  }
  function nonmostrare(){
  $("#welcomebox").fadeOut();
  setCookie('welcomeoff','1',<?php echo $welcome_cookie_day;?>);
  }
</script>
<?php } ?>