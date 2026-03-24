<?php
 if ($funclass->ActionVerify($profilemembership,"EVO WALL - Comments add"))
 {
  //aggiungo un bordo separatore qualora la vista commenti sia disabilitata
  if (!$funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) echo '<div id="separatorbymessage"></div>';
  //fine
  echo '<div id="contenitorelikeajax'.$assegnazione.'" class="cajx">
        <div id="cont_bottoni">
        <div id="commcontainer">'; 
  echo '
      <div id="slide'.$assegnazione.'" class="slidecss">'.get_member_icon($accountid,'none',false).'
		    <form name="mioform'.$assegnazione.'" id="inseriscicommento'.$assegnazione.'" action="javascript:raffa();" class="theformcomment">
		      <textarea class="mycomm" name="commento" id="go'.$assegnazione.'" onkeypress="trimx(this,'.$assegnazione.');return imposeMaxLength(this, '.$commlength.');" onkeyup="trimx(this,'.$assegnazione.');">'._t('_ibdw_evowall_writeapost').'</textarea>
		      <input id="idwriter'.$assegnazione.'" type="hidden" name="idwriter" value="'.$accountid.'">
          <input id="idsender'.$assegnazione.'" type="hidden" name="idsender" value="'.$row['sender_id'].'">
		      <input id="pagina'.$assegnazione.'" type="hidden" name="pagina" value="'. $pagina.'">
		      <input id="idaction'.$assegnazione.'" type="hidden" name="ass" value="'.$assegnazione.'">
		      <input type="hidden" id="sub'.$assegnazione.'" name="sendr'.$assegnazione.'">';
          if($mobiledetected==1 and $enablecommentbutton='on')
          {
           echo '<script>
           function mobilesend'.$assegnazione.'()
           {
            var commi=$("#go'.$assegnazione.'").attr("value");
	          var commento=encodeURIComponentNew(commi);
	          var owner=$("#idsender'.$assegnazione.'").attr("value");
            var writer=$("#idwriter'.$assegnazione.'").attr("value");
	          var pagina=$("#pagina'.$assegnazione.'").attr("value");
	          var idaction=$("#idaction'.$assegnazione.'").attr("value");
	          $("#sub'.$assegnazione.'").css("background-color","#999");
	          $("#sub'.$assegnazione.'").val("'._t("_ibdw_evowall_wait_button").'");
	          $.ajax({type: "POST", url: "modules/ibdw/evowall/comment.php", data: "commento="+ commento +"&owner=" +owner+"&writer="+writer+"&pagina=" +pagina +"&idaction=" +idaction ,
	          success: function(html)
	          { 
	           $("#commenti'.$assegnazione.'").empty();
	           $("#commenti'.$assegnazione.'").prepend(html);
	           $("#sub'.$assegnazione.'").css("background-color","#888");
	           $("#sub'.$assegnazione.'").val("'._t("_ibdw_evowall_send").'");
	           $("#go'.$assegnazione.'").val("'._t('_ibdw_evowall_writeapost').'");
	           $("#go'.$assegnazione.'").text("'._t('_ibdw_evowall_writeapost').'");
             $("#go'.$assegnazione.'").css("height",defaultheight);
             $("#go'.$assegnazione.'").blur();
             $(".textual").emoticonize();
        	  }});
           }
           </script>';
           echo '<input type="button" onclick="mobilesend'.$assegnazione.'();" value="'._t('_ibdw_evowall_condividi').'" class="mobilebutton">';
          }
          echo'
		    </form>
	   </div>';
  echo '</div><div id="clear" style="clear:both"></div></div></div>';
 } 
  echo '
  <script>
   //get the default height value for the comment textarea
   var defaultheight=$(".mycomm").css("height");
   
   $("#go'.$assegnazione.'").expanding();
  
   $("#go'.$assegnazione.'").focus(function() 
   {
    var currenttest=$(this).val();
    if($.trim(currenttest)=="'._t('_ibdw_evowall_writeapost').'") { $(this).val("");}
   });
   
   $("#go'.$assegnazione.'").blur(function() {
    var currenttest=$(this).val();
    if($.trim(currenttest)=="") 
    {
     $(this).val("'._t('_ibdw_evowall_writeapost').'");
    }
   });
   
   $("#go'.$assegnazione.'").click(function() 
   {
    var currenttest=$(this).val();
    if($.trim(currenttest)=="'._t('_ibdw_evowall_writeapost').'") { $(this).val("");}
    $("#go'.$assegnazione.'").css("height","100%");
   });
   
   $("#go'.$assegnazione.'").keydown(function(e) 
	 {
    var comment= $("#go'.$assegnazione.'").attr("value");
    if(e.keyCode==13 && comment.trim()!=\'\') 
    {e.preventDefault();
	   var commi=$("#go'.$assegnazione.'").attr("value");
	   var commento=encodeURIComponentNew(commi);
	   var owner=$("#idsender'.$assegnazione.'").attr("value");
     var writer=$("#idwriter'.$assegnazione.'").attr("value");
	   var pagina=$("#pagina'.$assegnazione.'").attr("value");
	   var idaction=$("#idaction'.$assegnazione.'").attr("value");
	   $("#sub'.$assegnazione.'").css("background-color","#999");
	   $("#sub'.$assegnazione.'").val("'._t("_ibdw_evowall_wait_button").'");
	   $.ajax({type: "POST", url: "modules/ibdw/evowall/comment.php", data: "commento="+ commento +"&owner=" +owner+"&writer="+writer+"&pagina=" +pagina +"&idaction=" +idaction ,
	   success: function(html)
	   { 
	    $("#commenti'.$assegnazione.'").empty();
	    $("#commenti'.$assegnazione.'").prepend(html);
	    $("#sub'.$assegnazione.'").css("background-color","#888");
	    $("#sub'.$assegnazione.'").val("'._t("_ibdw_evowall_send").'");
	    $("#go'.$assegnazione.'").val("'._t('_ibdw_evowall_writeapost').'");
	    $("#go'.$assegnazione.'").text("'._t('_ibdw_evowall_writeapost').'");
      $("#go'.$assegnazione.'").css("height",defaultheight);
      $("#go'.$assegnazione.'").blur();
      $(".textual").emoticonize();
	   }});
    }
	 });
  </script>';
?>