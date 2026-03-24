<?php
if(($funclass->ActionVerify($profilemembership,"EVO WALL - Like view")) and ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowlike')))
{
 $querylike= "SELECT id_utente FROM ibdw_likethis WHERE id_notizia=".$assegnazione;
 $querylikeresult= mysql_query($querylike);
 $rowquerylikeconta= mysql_num_rows($querylikeresult);
 
 echo '
      <script>
       function ajax_like_riepilogo'.$assegnazione.'()
       {
        $.ajax({
        type: "POST", url: "modules/ibdw/evowall/like_action.php", data: "user=0&id='.$assegnazione.'&set=2", success: function(data)
		    {
		     ajax_like_bt'.$assegnazione.'();
		     $("#element_likes'.$assegnazione.'").html(data);
		    }
       });
      }
      function ajax_like_bt'.$assegnazione.'()
      {
       $.ajax({
       type: "POST", url: "modules/ibdw/evowall/like_action.php", data: "user=0&id='.$assegnazione.'&set=3", success: function(data)
		   {
		    $("#cont_box_like_ajax'.$assegnazione.'").html(data);
		   }
      });
     }
    </script>';
 
 
 //zona B (pulsanti like)
 if(($funclass->ActionVerify($profilemembership,"EVO WALL - Like add")) and ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowlike')))
 {
  $querylikecontrollo="SELECT id_notizia,id_utente FROM ibdw_likethis WHERE id_notizia='$assegnazione' AND id_utente='$accountid'";
  $querylikecontrolloresult= mysql_query($querylikecontrollo);
  $rowquerylikecontrollo= mysql_num_rows($querylikecontrolloresult);
  echo '<div class="pointsepator"></div><div id="cont_box_like_ajax'.$assegnazione.'" class="boxlikez">';
  if($rowquerylikecontrollo>0) 
  {
   echo '<div id="nolike'.$assegnazione.'" class="nolike">
         <form name="likethis'.$assegnazione.'" id="nlikethis'.$assegnazione.'" action="javascript:raffa();">
		      <input id="nid_like'.$assegnazione.'" type="hidden" name="id" value="'. $assegnazione.'">
		      <input id="nuser_like'.$assegnazione.'" type="hidden" name="user" value="'. $accountid.'">
		      <input type="submit" value="'._t("_ibdw_evowall_unlikethis").'">
		     </form>
	       </div>
	       <script>
	        $("#nlikethis'.$assegnazione.'").submit(function() 
	        {
	         var nid_like=$("#nid_like'.$assegnazione.'").attr("value");
		       var nuser_like=$("#nuser_like'.$assegnazione.'").attr("value");
			     var pagina=$("#pagina'.$assegnazione.'").attr("value");
			     $.ajax({type: "POST", url: "modules/ibdw/evowall/like_action.php", data: "id=" + nid_like +"&user=" + nuser_like +"&set=1&pagina="+pagina,success: function(html){ajax_like_riepilogo'.$assegnazione.'();}});
          });
	       </script>';
  }
  else 
  {
   echo '<div id="like'.$assegnazione.'" class="boxlike">
 	  	   <form name="likethis'.$assegnazione.'" id="likethis'.$assegnazione.'" action="javascript:raffa();">
		      <input id="id_like'.$assegnazione.'" type="hidden" name="id" value="'. $assegnazione.'">
		      <input id="user_like'.$assegnazione.'" type="hidden" name="user" value="'.$accountid.'">
		      <input type="submit" value="'._t("_ibdw_evowall_likethis").'">
		     </form>
	       </div>
          <script>
           $("#likethis'.$assegnazione.'").submit(function() 
	         {
	          var id_like=$("#id_like'.$assegnazione.'").attr("value");
	          var user_like=$("#user_like'.$assegnazione.'").attr("value");
	          var pagina=$("#pagina'.$assegnazione.'").attr("value");
            $.ajax({type: "POST", url: "modules/ibdw/evowall/like_action.php", data: "id="+id_like+"&user="+user_like+"&set=0&pagina="+pagina,
	          success: function(html){ajax_like_riepilogo'.$assegnazione.'();}});
	         });
           
           //get the default height value for the comment textarea
           var defaultheight=$(".mycomm").css("height");
	        </script>';
  }
  echo '</div>';
 }
 //fine zona pulsanti mi piace
 
 
 // zona A - mostra i like avuti
 echo '
 <div id="element_likes'.$assegnazione.'" class="likezele">';
 
 //zona 1
 if ($rowquerylikeconta>2)
 {
  echo '
  <div id="element_like'.$assegnazione.'" class="elementlike">
   <i class="sys-icon thumbs-up"></i>
   <a href="javascript:slide_persone'.$assegnazione.'();">'.$rowquerylikeconta.' '._t("_ibdw_evowall_elementlike1").'</a> '._t("_ibdw_evowall_elementlike2").'
   <div id="slide_persone'.$assegnazione.'" class="slide_persone"><input type="hidden" id="slide_up'.$assegnazione.'" value="0">';
   while($rowquerylike = mysql_fetch_array($querylikeresult))
   {
    echo '  
    <div id="contbox_user_like">
     <div id="avat_like_box">';
      echo get_member_icon($rowquerylike['id_utente'],'none',false);
      echo '</div><div id="user_like_box"><a href="'.getUsername($rowquerylike['id_utente']).'" class="friend_like_name">'.getNickname($rowquerylike['id_utente']).'</a>';
      if (is_friends($accountid,$rowquerylike['id_utente'])==FALSE AND $rowquerylike['id_utente']!=$accountid) 
      {
       if (getMutualFriendsCount($accountid,$rowquerylike['id_utente'])>0) 
	     {
	      if (getMutualFriendsCount($accountid,$rowquerylike['id_utente'])>1) echo '<div class="box_likemutual">'.getMutualFriendsCount($accountid,$rowquerylike['id_utente']).' '._t("_ibdw_evowall_mutualfriend").'</div>';
	      else echo '<div class="box_likemutual">'.getMutualFriendsCount($accountid,$rowquerylike['id_utente']).' '._t("_ibdw_evowall_mutualfriendonlyone").'</div>';
	     }
       else echo '<div class="box_likemutual"></div>'; 
      }
      $idutenteidx=$rowquerylike['id_utente'];
      if(is_friends($accountid,$rowquerylike['id_utente'])==FALSE AND $rowquerylike['id_utente']!=$accountid)
      { 
       //controlla che la richiesta non sia inoltrata    
       $query="SELECT ID,Profile,sys_friend_list.Check FROM sys_friend_list WHERE ID='$accountid' AND Profile='$idutenteidx' AND sys_friend_list.Check=0";
       $esegui=mysql_query($query);
       $verifica=mysql_num_rows($esegui);
       if($verifica!=0) echo '<div class="box_aggiungilike" id="box_aggiungilike'.$idutenteidx.'">'._t("_ibdw_evowall_friendlike2").'</div>';
       else echo '<div class="box_aggiungilike" id="box_aggiungilike'.$idutenteidx.'"><i class="sys-icon user"></i><a href="javascript:aggiungi_friend'.$rowquerylike['id_utente'].'('.$idutenteidx.')">'._t("_ibdw_evowall_friendlike").'</a></div>';
      }
      echo '
     </div>
    </div>
    
    <script>
     function slide_persone'.$assegnazione.'() 
	   {
	    var slide_up = $("#slide_up'.$assegnazione.'").val();
	    if(slide_up==0)
	    {
       $("#slide_persone'.$assegnazione.'").fadeIn(1);
       $("#slide_up'.$assegnazione.'").val(1);
      }
      else 
	    {
       $("#slide_persone'.$assegnazione.'").css(\'display\',\'none\');
       $("#slide_up'.$assegnazione.'").val(0);
      }
     }
	   function aggiungi_friend'.$rowquerylike['id_utente'].'(idevento) 
	   {
	    var id_user='.$rowquerylike['id_utente'].';
	    $.ajax({ type: "POST", url: "modules/ibdw/evowall/add_sugg.php", data: "id_user=" + id_user, success: function(html){ notifica_generale("'._t("_ibdw_evowall_notificalikefriend").'");
	    $("#box_aggiungilike"+idevento).fadeOut();}});
	   };
	  </script>';
   }
   echo '
  </div>
 </div>';
 }
 
 //fine zona 1 (tutti i div interni sono chiusi)
  
 //zona 2 
 elseif($rowquerylikeconta>0)
 {
  $contapiace=0;
  echo '
  <div id="element_like'.$assegnazione.'" class="elementlike"><i class="sys-icon thumbs-up"></i>';
   while($rowquerylike=mysql_fetch_array($querylikeresult))
   {
    if (($rowquerylikeconta == 1) AND ($rowquerylike['id_utente']==$accountid )) echo _t("_ibdw_evowall_ilikeit");
    else 
    {
     $contapiace=$contapiace+1;
   	 if($contapiace==1) echo ' '._t("_ibdw_evowall_thiselementlike").' ';
     if ($contapiace>1) echo ' '._t("_ibdw_evowall_likeand").' ';
     if($rowquerylike['id_utente']==$accountid) echo '<a href="'.getUsername($rowquerylike['id_utente']).'"> '._t("_ibdw_evowall_likeyou").' </a>';
     else echo '<a href="'.getUsername($rowquerylike['id_utente']).'">'.getNickname($rowquerylike['id_utente']).' </a>';
    }
   }
   echo '
  </div>';
 }
 //fine zona 2 (tutti i div interni sono chiusi)
 
 echo '</div>';
 //zona A (chiudo il box che mostra i like avuti)
 echo '<script type="text/javascript">function raffa() {};</script>';
 }
?>