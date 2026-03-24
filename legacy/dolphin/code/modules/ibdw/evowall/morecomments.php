<?php 
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC.'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC.'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC.'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
require_once (BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/functions.php');
$funclass=new swfunc();
$accountid=(int)$_COOKIE['memberID'];
mysql_query("SET NAMES 'utf8'");
$pagina=$_POST['pagina'];
$assegnazione=(int)$_POST['assegnazione'];
$limitecommento=(int)$_POST['limitecommento'];
$limitecommento=$limitecommento+$commprevnum;
include 'templatesw.php';
echo '
       <script>
        function altricommenti'.$assegnazione.'(assegnazione,limitecommento,pagina) 
		    {
         $("#swich_comment'.$assegnazione.' .othnews").css({"background-image":"url('.$imagepath.'load.gif)","background-repeat":"no-repeat","background-position":"left","padding-left":"20px"});  
         $.ajax({
          type: "POST",url: "modules/ibdw/evowall/morecomments.php",data:"assegnazione="+assegnazione+"&limitecommento="+limitecommento+"&pagina="+pagina, success: function(html)
				  {
           $("#commenti'.$assegnazione.'").empty();
	         $("#commenti'.$assegnazione.'").append(html);
           $(".textual").emoticonize();
          }
         });
        };
       </script>';
//ottengo la data di pubblicazione del post per confrontarla con le date dei commenti che devono essere successivi al post ovviamente
$querydatapost="SELECT bx_spy_data.date FROM bx_spy_data WHERE ID=$assegnazione";
$resultdatapost=mysql_query($querydatapost);
$valdata=mysql_fetch_assoc($resultdatapost);	   
$querycontacommenti="SELECT commenti_spy_data.data FROM commenti_spy_data LEFT JOIN datacommenti ON commenti_spy_data.id=datacommenti.IDCommento WHERE data=$assegnazione and datacommenti.date>'".$valdata['date']."'";
$resultcontacommenti=mysql_query($querycontacommenti);
$rowcontacommenti=mysql_num_rows($resultcontacommenti);
if ($rowcontacommenti>0)
{
 if ($rowcontacommenti==1) {$titlecommentis=_t('_ibdw_evowall_comment_title1');$endcomment=_t('_ibdw_evowall_comment_titlef1');}
 else {$titlecommentis=_t('_ibdw_evowall_comment_title2');$endcomment=_t('_ibdw_evowall_comment_titlef2');}
 echo '<div class="commentreport" id="rep'.$assegnazione.'"><div class="comm">'.$titlecommentis.' <span class="numerocommenti'.$assegnazione.'">'.$rowcontacommenti.'</span> '.$endcomment.'</b></div>';
 if($rowcontacommenti>$limitecommento) echo '<div class="vedialtro"><i class="sys-icon chevron-down" alt=""></i><a href="javascript:altricommenti'.$assegnazione.'('.$assegnazione.','.$limitecommento.',\''.$pagina.'\')" class="othnews">'._t("_ibdw_evowall_altricommenti").'</a></div>';
 echo '</div>';
}
if($ordinec=='Last') $tipoordine="DESC";
else $tipoordine="ASC";
$querydelcommento="SELECT commenti_spy_data.*,datacommenti.date, Profiles.ID, Profiles.NickName, Profiles.Sex, Profiles.Avatar FROM (commenti_spy_data LEFT JOIN datacommenti ON commenti_spy_data.id=datacommenti.IDCommento) INNER JOIN Profiles ON commenti_spy_data.user = Profiles.ID WHERE date>'".$valdata['date']."' AND data=$assegnazione ORDER BY commenti_spy_data.id ".$tipoordine." LIMIT 0,$limitecommento";
$resultdelcommento=mysql_query($querydelcommento); 
echo '<div id="nuovocommento'.$assegnazione.'"></div><div id="swich_comment'. $assegnazione.'" class="swichwidth">';
while($rowdelcommento=mysql_fetch_array($resultdelcommento))
{
 echo '<div id="commentario" class="super_commento'.$rowdelcommento['id'].'">';
 if ($rowdelcommento['user']==$accountid) 
 {
  echo '
    <script>
    $(document).ready(function()
    {
     var configs={over: fade_cmnt_BT'.$rowdelcommento['id'].',out: out_cmnt_BT'.$rowdelcommento['id'].',interval:1};
     $(".super_commento'.$rowdelcommento['id'].'").hoverIntent(configs);		
    });
    function fade_cmnt_BT'.$rowdelcommento['id'].'()
    {
     $("#elimina'.$rowdelcommento['id'].'").fadeIn(1);
    }
    function out_cmnt_BT'.$rowdelcommento['id'].'()
    {
     $("#elimina'.$rowdelcommento['id'].'").css(\'display\',\'none\');
    }
    </script>
    ';         
 }
 $cmn=str_replace("-->","--&gt;",str_replace("<--","&lt;--",$rowdelcommento['commento']));
 $cmn=strip_tags($cmn);
 $cmn=$funclass->urlreplace($cmn);
 $cmn=str_replace("`", "'", $cmn);
 $miadatac=$funclass->TempoPost($rowdelcommento['date'],$seldate,$offset);
 echo '<div id="single_comment'.$rowdelcommento['id'].'" class="single_comment"><div id="contentcomm"><div id="avacomm">';
 echo get_member_icon($rowdelcommento['ID'],'none',false);
 echo '</div><div id="commcomm"><div class="textual">';
 echo '<a href="'.$rowdelcommento['NickName'].'">'.getNickname($rowdelcommento['user']).'</a>: '.$cmn.'<div class="stydata">'.$miadatac.'</div></div>
  <div id="elimina'.$rowdelcommento['id'].'" class="eliminab" style="display:none;"><form id="elimina'.$rowdelcommento['id'].'" action="javascript:elimina();"><input id="id'.$rowdelcommento['id'].'" type="hidden" name="id" value="'.$rowdelcommento['id'].'">
		<input id="assegnazione'.$assegnazione.'" type="hidden" name="id" value="'.$assegnazione.'"><input id="pagina'.$assegnazione.'" type="hidden" name="id" value="'.$pagina.'">
		<input id="limite'.$assegnazione.'" type="hidden" name="id" value="'.$limitecommento.'"><div id="idcont'.$rowdelcommento['id'].'" title="'._t('_ibdw_evowall_delete').'" class="removecomment"><i class="sys-icon remove" alt=""></i></div></form></div> 
		<script>
		$("#idcont'.$rowdelcommento['id'].'").click(function() 
		{
		 var id=$("#id'.$rowdelcommento['id'].'").attr("value");
		 var numero_commento = $(".numerocommenti'.$assegnazione.'").html(); 
		 var def_numero_commento = parseInt(numero_commento)-1;
		 $.ajax({type: "POST", url: "modules/ibdw/evowall/deletecomment.php", data: "id=" + id + "&idpost='.$assegnazione.'",
		 success: function(html)
		 {
		  $(".super_commento'.$rowdelcommento['id'].'").fadeOut(1);
      $(".numerocommenti'.$assegnazione.'").html(def_numero_commento);
      if(def_numero_commento==0) $("#rep'.$assegnazione.'").fadeOut(1);
      else if(def_numero_commento==1) $("#rep'.$assegnazione.'").html("<div class=\'comm\'><i class=\'sys-icon comment\'></i>'._t("_ibdw_evowall_comment_title1").' <span class=\'numerocommenti'.$assegnazione.'\'>1</span> '._t("_ibdw_evowall_comment_titlef1").'</b></div>");
		 }});
		});
		</script>
    </div></div>
 </div></div></div>';
} 
echo '<div id="altricommenti'.$assegnazione.'"></div></div>';
?>