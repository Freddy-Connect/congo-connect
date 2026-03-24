<script language="JavaScript">
<!--
function utf8(wide) {
  var c, s;
  var enc = "";
  var i = 0;
  while(i<wide.length) {
    c= wide.charCodeAt(i++);
    // handle UTF-16 surrogates
    if (c>=0xDC00 && c<0xE000) continue;
    if (c>=0xD800 && c<0xDC00) {
      if (i>=wide.length) continue;
      s= wide.charCodeAt(i++);
      if (s<0xDC00 || c>=0xDE00) continue;
      c= ((c-0xD800)<<10)+(s-0xDC00)+0x10000;
    }
    // output value
    if (c<0x80) enc += String.fromCharCode(c);
    else if (c<0x800) enc += String.fromCharCode(0xC0+(c>>6),0x80+(c&0x3F));
    else if (c<0x10000) enc += String.fromCharCode(0xE0+(c>>12),0x80+(c>>6&0x3F),0x80+(c&0x3F));
    else enc += String.fromCharCode(0xF0+(c>>18),0x80+(c>>12&0x3F),0x80+(c>>6&0x3F),0x80+(c&0x3F));
  }
  return enc;
}
var hexchars="0123456789ABCDEF";
function toHex(n) {return hexchars.charAt(n>>4)+hexchars.charAt(n & 0xF);}
var okURIchars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-";
function encodeURIComponentNew(s) 
{
  var s = utf8(s);
  var c;
  var enc = "";
  for (var i= 0; i<s.length; i++) {
    if (okURIchars.indexOf(s.charAt(i))==-1)
      enc += "%"+toHex(s.charCodeAt(i));
    else
      enc += s.charAt(i);
  }
  return enc;
}
function buildURL(fld)
{
	if (fld == "") return false;
	var encodedField = "";
	var s = fld;
	if (typeof encodeURIComponent == "function")
	{
		// Use JavaScript built-in function
		// IE 5.5+ and Netscape 6+ and Mozilla
		encodedField = encodeURIComponent(s);
	}
	else 
	{
		// Need to mimic the JavaScript version
		// Netscape 4 and IE 4 and IE 5.0
		encodedField = encodeURIComponentNew(s);
	}
	alert ("New encoding: " + encodeURIComponentNew(fld) +
		 "\n           escape(): " + escape(fld));
	return true;
}
// -->
function imposeMaxLength(Object, MaxLen) {return (Object.value.length <= MaxLen);}
String.prototype.trima = function() 
{ 
 try 
 { 
  return this.replace(/^\s+|\s+$/g, ""); 
 } 
 catch(e) 
 { 
  return this; 
 } 
} 
function trimx(Object,numeroform)
{
 var stringa=Object.value.toString();
 var nomedelform='mioform'+numeroform;
 var nomedelsend='sendr'+numeroform;
 if(stringa.trima()=="") { document.forms[nomedelform].elements[nomedelsend].disabled=true; }
 else { document.forms[nomedelform].elements[nomedelsend].disabled=false; }
}
</script>
<?php
$assegnazione=$row['id'];//id dell'azione
$accountid=(int)$_COOKIE['memberID'];
include BX_DIRECTORY_PATH_MODULES.'ibdw/evowall/config.php';
$limiteanteprima=$commprevnum;
$limitecommento=$commnum;

if(($funclass->ActionVerify($profilemembership,"EVO WALL - Comments view")) and ($funclass->checkprivacyevo($row['sender_id'],$accountid,'allowcomment')))
{
 echo '<div id="commenti'.$assegnazione.'" class="commenti">
  <script>
	 function altricommenti'.$assegnazione.'(assegnazione,limitecommento,pagina) 
	 {
	  $("#swich_comment'.$assegnazione.' .othnews").css({"background-image" : "url('.$imagepath.'load.gif)" , "background-repeat" : "no-repeat" , "background-position" : "left","padding-left" : "20px"});
	  $.ajax({type: "POST", url: "modules/ibdw/evowall/morecomments.php", data: "assegnazione=" + assegnazione + "&limitecommento=" + limitecommento + "&pagina=" + pagina, 
	  success: function(html)
	  {
	   $("#commenti'.$assegnazione.'").empty();
	   $("#commenti'.$assegnazione.'").append(html);
     $(".textual").emoticonize();
	  }});
	 };
	</script>';
//ottengo la data di pubblicazione del post per confrontarla con le date dei commenti che devono essere successivi al post ovviamente
$querydatapost="SELECT bx_spy_data.date FROM bx_spy_data WHERE ID=$assegnazione";
$resultdatapost=mysql_query($querydatapost);
$valdata=mysql_fetch_assoc($resultdatapost);
$querycontacommenti="SELECT commenti_spy_data.data FROM commenti_spy_data LEFT JOIN datacommenti ON commenti_spy_data.id=datacommenti.IDCommento WHERE commenti_spy_data.data=$assegnazione and datacommenti.date>'".$valdata['date']."'";
$resultcontacommenti=mysql_query($querycontacommenti);
$rowcontacommenti=mysql_num_rows($resultcontacommenti);
if ($rowcontacommenti>0)
{
 if ($rowcontacommenti==1) {$titlecommentis=_t('_ibdw_evowall_comment_title1');$endcomment=_t('_ibdw_evowall_comment_titlef1');}
 else {$titlecommentis=_t('_ibdw_evowall_comment_title2');$endcomment=_t('_ibdw_evowall_comment_titlef2');}
 echo '<div class="commentreport" id="rep'.$assegnazione.'"><div class="comm"><i class="sys-icon comment"></i>'.$titlecommentis.' <span class="numerocommenti'.$assegnazione.'">'.$rowcontacommenti.'</span> '.$endcomment.'</b></div>';
 if($rowcontacommenti>$limiteanteprima) 
 {
  if ($rowcontacommenti<($limitecommento+$limiteanteprima+1)) echo '<div class="vedialtro"><i class="sys-icon chevron-down" alt=""></i><a href="javascript:altricommenti'.$assegnazione.'('.$assegnazione.','.$limitecommento.',\''.$pagina.'\')" class="othnews">'._t("_ibdw_evowall_allcomm").$rowcontacommenti._t("_ibdw_evowall_allcomm2").'</a></div>';
  else echo '<div class="vedialtro"><i class="sys-icon chevron-down" alt=""></i><a href="javascript:altricommenti'.$assegnazione.'('.$assegnazione.','.$limitecommento.',\''.$pagina.'\')" class="othnews">'._t("_ibdw_evowall_altricommenti").'</a></div>';
 }
 echo '</div>';
}
$querydelcommento="SELECT * FROM (SELECT t1.*,t2.date, Profiles.ID as MYID, Profiles.NickName, Profiles.Avatar FROM (commenti_spy_data AS t1 LEFT JOIN datacommenti as t2 ON t1.id=t2.IDCommento) INNER JOIN Profiles ON t1.user = Profiles.ID WHERE data=$assegnazione ORDER BY t1.id DESC LIMIT 0,".$limiteanteprima.") AS t3 WHERE date>'".$valdata['date']."' ORDER BY t3.id ASC";
$resultdelcommento = mysql_query($querydelcommento);
echo '<div id="nuovocommento'.$assegnazione.'"></div>';
echo '<div id="swich_comment'.$assegnazione.'" class="swichwidth">';
while($rowdelcommento=mysql_fetch_array($resultdelcommento))
{
 echo '<div id="commentario" class="super_commento'.$rowdelcommento['id'].'">';
 $proprietariosx = "SELECT bx_spy_data.id,sender_id FROM bx_spy_data WHERE bx_spy_data.id = '$assegnazione' LIMIT 0 , 1";
 $exe_propsx = mysql_query($proprietariosx);
 $assoc_prop = mysql_fetch_assoc($exe_propsx);
 $proprietarioevento = $assoc_prop['sender_id'];
 if ($rowdelcommento['user']==$accountid OR $proprietarioevento==$accountid) 
 {
  echo '
  <script>
   $(document).ready(function()
   {
    var configs={over: fade_cmnt_BT'.$rowdelcommento['id'].',out: out_cmnt_BT'.$rowdelcommento['id'].',interval:1};
    $(".super_commento'.$rowdelcommento['id'].'").hoverIntent(configs);		
   });
   function fade_cmnt_BT'.$rowdelcommento['id'].'() { $("#elimina'.$rowdelcommento['id'].'").fadeIn(1); }
   function out_cmnt_BT'.$rowdelcommento['id'].'() { $("#elimina'.$rowdelcommento['id'].'").css(\'display\',\'none\');}
  </script> 
  
	';
	}
	$cmn=str_replace("-->","--&gt;",str_replace("<--","&lt;--",$rowdelcommento['commento']));
	$cmn=strip_tags($cmn);
	$cmn=$funclass->urlreplace($cmn);
	$cmn=str_replace("`", "'", $cmn);	
	$miadatac=$funclass->TempoPost($rowdelcommento['date'],$seldate,$offset);															
	echo '<div id="single_comment'.$rowdelcommento['id'].'" class="single_comment"><div id="contentcomm"><div id="avacomm">';
	echo get_member_icon($rowdelcommento['MYID'],'none',false);
  echo '</div><div id="commcomm"><div class="textual">';
  echo '<a href="'.$rowdelcommento['NickName'].'">'.getNickname($rowdelcommento['user']).'</a>: '.$cmn.'<div class="stydata">'.$miadatac.'</div></div><div id="elimina'.$rowdelcommento['id'].'" class="eliminab" style="display:none;">
   <form id="elimina'.$rowdelcommento['id'].'" action="javascript:elimina();">
	  <input id="id'.$rowdelcommento['id'].'" type="hidden" name="id" value="'.$rowdelcommento['id'].'">
	  <input id="assegnazione'.$assegnazione.'" type="hidden" name="id" value="'.$assegnazione.'">
	  <input id="pagina'.$assegnazione.'" type="hidden" name="id" value="'.$pagina.'">
	  <input id="limite'.$assegnazione.'" type="hidden" name="id" value="'.$limitecommento.'">    
	 </form>
   <div id="idcont'.$rowdelcommento['id'].'" title="'._t('_ibdw_evowall_delete').'" class="removecomment"><i class="sys-icon remove" alt=""></i></div>
   <script>
	 $("#idcont'.$rowdelcommento['id'].'").click(function() 
	 {
	  var id=$("#id'.$rowdelcommento['id'].'").attr("value");
		var numero_commento=$(".numerocommenti'.$assegnazione.'").html(); 
		var def_numero_commento=parseInt(numero_commento)-1;    
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
 echo '<div id="altricommenti'.$assegnazione.'"> </div></div></div>';
}

//<< sommo punto focale dei commenti e delle funzioni ajax
include('newcomment.php');
?>