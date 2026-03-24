<script>
$(document).ready(function() 
{
 $('.areamsgnoava').val('<?php echo addslashes(_t("_ibdw_evowall_msgformhome"));?>');
 $('.submsg').attr("disabled","disabled");
 $('.areamsgnoava').keyup(function() 
 {
  var len=this.value.length;
  if (len>=<?php echo $messlength;?>) {this.value = this.value.substring(0, <?php echo $messlength;?>);}
 });
 $(function() 
 {  
  var txt=$('.areamsgnoava'),hiddenDiv=$(document.createElement('div')),content=null;  
  txt.addClass('noscroll');
  hiddenDiv.addClass('hiddendiv');  
  $('body').append(hiddenDiv);  
  txt.bind('keyup', function() 
  {  
   content=txt.val();  
   content=content.replace(/\n/g, '<br>');  
   hiddenDiv.html(content);  
   txt.css('height', hiddenDiv.height());  
  });  
 });
});

function isUrl(s) 
{
 var pattern = /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/; 
 return pattern.test(s);
}

function inviamessaggiopersonale(abilitazione) 
{ 
 var mess=$('.areamsgnoava').attr('value');
 var trovato=false;
 var part_num=0;
 var datratt=mess.replace(/\n/g," <br>");
 var indiri=datratt.split(" ");
 var daanalizzare;
 while (part_num<indiri.length)
 {
  if (isUrl(indiri[part_num].toLowerCase())==true && indiri[part_num].indexOf("@")==-1 && indiri[part_num].indexOf("..")==-1)
  {
   daanalizzare=indiri[part_num];
   trovato=true;
   break;
  }
  part_num+=1;
 }
 var messaggio=encodeURIComponentNew(mess);
 var user=$('#user').attr('value');
 var riceve=$('#riceve').attr('value');
 var pagina=$('#pagina').attr('value');
 var newline=$('#newline').attr('value');
 var messaggio=messaggio.replace(/%27/g, '`');         
 var messaggio=messaggio.replace(/%22/g, '`');
 var messaggio=messaggio.replace(/%5C/g, '/');
 if(trovato==true && abilitazione==1) {inviaurls(daanalizzare.replace(/&/g, "%26"),messaggio.replace(/&/g, "%26"),riceve);}
 else
 {
  $("#attendi").slideDown(200);
  $.ajax({type:"POST",url:"modules/ibdw/evowall/message.php",data: "messaggio="+messaggio+"&user="+ user +"&riceve="+ riceve+"&newline="+newline+"&pagina="+pagina+"&ultimoid=<?php echo $ultimoid;?>",success: 
  function(data)
  {
   $('#correzione').html(data);
   $("#attendi").slideUp(200);   
   ritornanormale2();
  }
  });
 }
 return false;
}

function justmess(messaggio)
{
 var user=$('#user').attr('value');
 var pagina=$('#pagina').attr('value');
 var riceve=$('#riceve').attr('value');
 var newline=$('#newline').attr('value');
 var messaggio=messaggio.replace(/%27/g, '`');         
 var messaggio=messaggio.replace(/%22/g, '`');
 var messaggio=messaggio.replace(/%5C/g, '/');
 $('#fremmentositoweb').fadeOut(1);
 $("#attendi").slideDown(200);
 $.ajax({type: "POST",url: "modules/ibdw/evowall/message.php",data: "messaggio="+messaggio+"&user="+user+"&riceve="+riceve+"&newline="+newline+"&pagina="+pagina+"&ultimoid=<?php echo $ultimoid;?>",success: 
 function(data) {
   $('#correzione').html(data);
   $("#attendi").slideUp(200);   
   ritornanormale2();
  }
 });
}
//inviaurls for link and message
function inviaurls(valore,testo,idprofile) 
{
 $("#attendi").slideDown(200);
 var indirizzo=valore;
 var idprofile=idprofile;
 $.ajax({
  type:'POST',data:"urlweb="+indirizzo+"&message="+testo+"&idprofile="+idprofile, url:'modules/ibdw/evowall/posturl.php',cache:false,success:function(data) 
  {
   $('#frammento_exe').html(data);
   $('#frammento_exe').slideDown(200);  
   $("#attendi").slideUp(200);
   ritornanormale2();
  }
 });
}
//inviaurl for link only
function inviaurl(idprofile) 
{
 var indirizzo=$("#urlspecial").val();
 var idprofile=idprofile;
 $('#frammento_url').slideUp(200);
 $("#attendi").slideDown(200);
 $.ajax({
  type:'POST',
  data:{urlweb:indirizzo,idprofile:idprofile,message:''},url:'modules/ibdw/evowall/posturl.php',cache:false,success: function(data) 
  {
   $('#frammento_exe').html(data);  
   $("#attendi").slideUp(200);
  }
 });
}

function imposeMaxLength(Object, MaxLen) {return (Object.value.length <= MaxLen);}

String.prototype.trim = function() 
{ 
 try {return this.replace(/^\s+|\s+$/g, "");} 
 catch(e) {return this;} 
}

function trim1(idog)
{
 var stringa = $(idog).val();
 var newstringa = stringa.trim();
 if(newstringa != '' && stringa != '<?php echo addslashes(_t("_ibdw_evowall_msgformhome"));?>') { $("#submsg").fadeIn(1); }
 else { $("#submsg").fadeOut(1); }
}

function trim2(idog)
{
 var stringa = $(idog).val();
 var newstringa = stringa.trim();
 if(newstringa != '' && stringa != '<?php echo addslashes(_t("_ibdw_evowall_msgformhome"));?>') { $("#submsg").fadeIn(1); }
 else { $("#submsg").fadeOut(1); }
}

function resettaform() 
{
 $('.areamsgnoava').val('');             
 $('.areamsgnoava').css('height','24px');
 $('.areamsgnoava').css('font-size','12px');
 $('.areamsgnoava').css('color','#333');
 $('.areamsgnoava').css('line-height','12px');
 $('.areamsgnoava').css('margin-bottom','5px');
 $("#submsg").fadeOut(1); 
}
       
function ritornanormale2() 
{ 
 $('.areamsgnoava').val('<?php echo addslashes(_t("_ibdw_evowall_msgformhome"));?>');  
 $('.areamsgnoava').css('height','24px');
 $('.areamsgnoava').css('font-size','15px');
 $('.areamsgnoava').css('color','#666');
 $('.areamsgnoava').css('line-height','22px');
 $('.areamsgnoava').css('margin-bottom','5px');
 $("#submsg").fadeOut(1); 
}



function attendi()
{
 $("#attendi").slideToggle("fast");
 $("#frammento_url").slideUp(); 
 $("#erroreurl").slideUp();
}
			
function chiuditutto()
{
 $("#frammento_url").slideUp(); 
 $("#erroreurl").slideUp();
}
      
function lanciaclassic(namevideoalbum)
{ 
 $("#lista_btx_evo_list").hide();
 $("#mm_setmenux_evo_list").val(0);
 $(".classicmethodbutton").css('display','none');
 $(".embedmethodbutton").css('display','block');
 $('#bloccotubox').html('<p style="text-align:center;"><img src="modules/ibdw/evowall/templates/base/images/big-loader.gif" class="attsa"></p>');
 
 $.ajax({type: "POST",url: "modules/ibdw/evowall/videoclassic.php",data: "namevideoalbum="+namevideoalbum,success: 
 function(data) {
   $('#bloccotubox').html(data);
   $("#attendi").slideUp(200);   
  }
 });
 
}

function lanciatube(namevideoalbum)
{
 $("#lista_btx_evo_list").hide();
 $("#mm_setmenux_evo_list").val(0);
 $(".classicmethodbutton").css('display','block');
 $(".embedmethodbutton").css('display','none');
 $('#bloccotubox').html('<p style="text-align:center;"><img src="modules/ibdw/evowall/templates/base/images/big-loader.gif" class="attsa"></p>');
 
 $.ajax({type: "POST",url: "modules/ibdw/evowall/videoembed.php",data: "namevideoalbum="+namevideoalbum,success: 
 function(data) {
   $('#bloccotubox').html(data);
   $("#attendi").slideUp(200);   
  }
 });
 
}       

function lanciahtml5foto(namephotoalbum)
{ 
 $("#lista_btx_evo_list").hide();
 $("#mm_setmenux_evo_list").val(0);
 $(".classicmethodbutton").css('display','block');
 $(".embedmethodbutton").css('display','none');
 $('#bloccotubox').html('<p style="text-align:center;"><img src="modules/ibdw/evowall/templates/base/images/big-loader.gif" class="attsa"></p>');
 
 $.ajax({type: "POST",url: "modules/ibdw/evowall/photohtml5.php",data: "namephotoalbum="+namephotoalbum,success: 
 function(data) {
   $('#bloccotubox').html(data);
   $("#attendi").slideUp(200);   
  }
 });
 
}
            
function lanciaregularfoto(namephotoalbum)
{ 
 $("#lista_btx_evo_list").hide();
 $("#mm_setmenux_evo_list").val(0);
 $(".classicmethodbutton").css('display','block');
 $(".embedmethodbutton").css('display','none');
 $('#bloccotubox').html('<p style="text-align:center;"><img src="modules/ibdw/evowall/templates/base/images/big-loader.gif" class="attsa"></p>');
 
 $.ajax({type: "POST",url: "modules/ibdw/evowall/photoregular.php",data: "namephotoalbum="+namephotoalbum,success: 
 function(data) {
   $('#bloccotubox').html(data);
   $("#attendi").slideUp(200);   
  }
 });
} 

function utf8(wide)
{
  var c, s;
  var enc = "";
  var i = 0;
  while(i<wide.length)
  {
   c= wide.charCodeAt(i++);
   // handle UTF-16 surrogates
   if (c>=0xDC00 && c<0xE000) continue;
   if (c>=0xD800 && c<0xDC00)
   {
    if (i>=wide.length) continue;
    s=wide.charCodeAt(i++);
    if (s<0xDC00 || c>=0xDE00) continue;
    c=((c-0xD800)<<10)+(s-0xDC00)+0x10000;
   }
   // output value
   if (c<0x80) enc += String.fromCharCode(c);
   else if (c<0x800) enc += String.fromCharCode(0xC0+(c>>6),0x80+(c&0x3F));
   else if (c<0x10000) enc += String.fromCharCode(0xE0+(c>>12),0x80+(c>>6&0x3F),0x80+(c&0x3F));
   else enc += String.fromCharCode(0xF0+(c>>18),0x80+(c>>12&0x3F),0x80+(c>>6&0x3F),0x80+(c&0x3F));
  }
 return enc;
}

var hexchars = "0123456789ABCDEF";

function toHex(n) {return hexchars.charAt(n>>4)+hexchars.charAt(n & 0xF);}

var okURIchars="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-";

function encodeURIComponentNew(s) 
{
 var s=utf8(s);
 var c;
 var enc="";
 for (var i=0; i<s.length; i++) 
 {
  if (okURIchars.indexOf(s.charAt(i))==-1) enc+="%"+toHex(s.charCodeAt(i));
  else enc+= s.charAt(i);
 }
 return enc;
}

function buildURL(fld)
{
 if (fld=="") return false;
 var encodedField="";
 var s=fld;
 if (typeof encodeURIComponent=="function")
 {
  encodedField=encodeURIComponent(s);
 }
 else 
 {
  encodedField=encodeURIComponentNew(s);
 }
 alert ("New encoding: "+encodeURIComponentNew(fld)+"\n           escape(): "+escape(fld));
 return true;
}

function closeweb_wall() {$('#fremmentositoweb').fadeOut(1);}

function focuss() 
{
 var testo=$("#urlspecial").val();
 if(testo=='<?php echo addslashes(_t('_ibdw_evowall_urlspecial'));?>'){ $("#urlspecial").val('');}
} 

function blurr() 
{ 
 var testo=$("#urlspecial").val();
 if(testo=='') {$("#urlspecial").val('<?php echo addslashes(_t('_ibdw_evowall_urlspecial'));?>');}
}
</script>
<?php 
$usercode=$_COOKIE['memberID'];
$nicknick=getNickname($mioid);
$idtoken=$_COOKIE['memberSession'];
$estraitoken="SELECT id,data FROM sys_sessions WHERE id='".$idtoken."'";
$eseguitoken=mysql_query($estraitoken);
if ($eseguitoken!=FALSE) 
{
 $associazione=mysql_fetch_assoc($eseguitoken);
 $unserialize=unserialize($associazione['data']);
 $tokenfinale=$unserialize['csrf_token'];
}
else $tokenfinale = 0;

//create photo album if it not exsists
$querycontrollo="SELECT Caption,Owner,AllowAlbumView FROM sys_albums WHERE Uri='".trim($namephotoalbum)."' AND Owner='$usercode'";
$resultquerycontrollo = mysql_query($querycontrollo);
$rowquerycontrollo = mysql_num_rows($resultquerycontrollo);
$rowqueryrow = mysql_fetch_row($resultquerycontrollo);

if ($rowquerycontrollo==0)
{ 
 $queryinserimento="INSERT INTO sys_albums (Caption,Uri,Location,Type,Owner,Status,Date,AllowAlbumView) VALUES ('".trim($namephotoalbum)."','".trim($namephotoalbum)."','Undefined','bx_photos','".$usercode."','Active','".time()."','".$privacyalbum."')";
 $resultquery = mysql_query($queryinserimento);
}

//create video album if it not exsists
$querycontrollov="SELECT Caption,Owner,AllowAlbumView FROM sys_albums WHERE Uri='".trim($namevideoalbum)."' AND Owner='$usercode'";
$resultquerycontrollov = mysql_query($querycontrollov);
$rowquerycontrollov = mysql_num_rows($resultquerycontrollov);
$rowqueryrowv = mysql_fetch_row($resultquerycontrollov);
if ($rowquerycontrollov==0)
{ 
 $queryinserimentov="INSERT INTO sys_albums (Caption,Uri,Location,Type,Owner,Status,Date,AllowAlbumView) VALUES ('".trim($namevideoalbum)."','".trim($namevideoalbum)."','Undefined','bx_videos','".$usercode."','Active','".time()."','".$privacyalbum."')";
 $resultqueryv = mysql_query($queryinserimentov);
}
echo '<div id="main_testata">';
$nicknick=getNickname($mioid);
if ($funclass->ActionVerify($profilemembership,"EVO WALL - Personal messages"))
{
 //check if url plugin is active
 if ($UrlPlugin=='on') $abilitazione=1;
 else $abilitazione=0;
 echo '
   <div id="main_form">
    <form name="frm1" id="messaggioajax" method="post"><textarea style="overflow: auto;" name="messaggio" class="areamsgnoava" onclick="chiuditutto();trim1(this);if ((this.value!==\'\') && (this.value==\''.addslashes(_t("_ibdw_evowall_msgformhome")).'\')){resettaform()};" onfocus="trim1(this);" onblur="if (this.value==\'\'){ritornanormale2()};trim1(this);" onkeypress="trim1(this);return" onkeyup="trim1(this);" onpaste="trim1(this);return"></textarea>
     <input id="user" type="hidden" name="user" value="'; echo $accountid; echo '"/>
     <input id="riceve" type="hidden" name="riceve" value="'; echo $profileid; echo '"/>
     <input id="pagina" type="hidden" name="pagina" value="'; echo $pagina; echo '"/>
     <input id="newline" type="hidden" name="newline" value="'; echo $newline; echo '"/>
     <div id="main_form_right"><div class="bx-btn" onclick="inviamessaggiopersonale('.$abilitazione.');" id="submsg">'._t('_ibdw_evowall_send').'</div></div>
    </form>
   </div>';
}
if($paginamia == 1)
{
 echo '<div id="cont_bt_wall">';        
 if ($photo=='on' and $funclass->ActionVerify($profilemembership,"EVO WALL - Photos")) echo '<a href="javascript:foto(0);" title="'._t('_ibdw_evowall_fotoup').'"><div id="bottone_generale"><i alt="" class="sys-icon camera"></i></div></a>'; 
 if ($video=='on' and $funclass->ActionVerify($profilemembership,"EVO WALL - Videos")) echo '<a href="javascript:video(0);" title="'._t('_ibdw_evowall_videoup').'"><div id="bottone_generale"><i alt="" class="sys-icon play-circle"></i></div></a>'; 
 if ($group=='on' and $funclass->ActionVerify($profilemembership,"EVO WALL - Groups")) echo '<a href="m/groups/browse/my&bx_groups_filter=add_group" title="'._t('_ibdw_evowall_gruppoup').'"><div id="bottone_generale"><i alt="" class="sys-icon group"></i></div></a>'; 
 if ($event=='on' and $funclass->ActionVerify($profilemembership,"EVO WALL - Events")) 
 {
  if ($eventmodule=='Boonex') echo '<a href="m/events/browse/my&bx_events';
  elseif ($eventmodule='UE30') echo '<a href="m/event/browse/my&ue30_event';
  echo '_filter=add_event" title="'._t('_ibdw_evowall_event_name').'"><div id="bottone_generale"><i alt="" class="sys-icon calendar"></i></div></a>';
 }
 if ($poll=='on' and $pollmodule=='Boonex' and $funclass->ActionVerify($profilemembership,"EVO WALL - Polls")) echo '<a href="m/poll/&action=my&mode=add" title="'._t('_ibdw_evowall_sondaggioup').'"><div id="bottone_generale"><i alt="" class="sys-icon tasks"></i></div></a>';
 elseif($poll=='on' and $pollmodule=='Modzzz' and $funclass->ActionVerify($profilemembership,"EVO WALL - Polls")) echo '<a href="m/polls/browse/my&modzzz_polls_filter=add_poll" title="'._t('_modzzz_polls').'"><div id="bottone_generale"><i alt="" class="sys-icon tasks"></i></div></a>';
 //uncomment that if you want to use the following buttons:
 //echo '<a href="m/sounds/albums/my/add_objects/" title="'._t('_bx_events_action_upload_sounds').'"><div id="bottone_generale"><i alt="" class="sys-icon music"></i></div></a>';
 //echo '<a href="blogs/my_page/add/" title="'._t('_bx_blog_Blogs').'"><div id="bottone_generale"><i alt="" class="sys-icon book"></i></div></a>'; 
 //end
 if ($ads=='on' and $funclass->ActionVerify($profilemembership,"EVO WALL - Ads")) echo '<a href="ads/my_page/add/" title="'._t('_ibdw_evowall_adsup').'"><div id="bottone_generale"><i alt="" class="sys-icon money"></i></div></a>';
 if ($UrlPlugin=='on' and $funclass->ActionVerify($profilemembership,"EVO WALL - Sites")) echo '<a href="javascript:url();" title="'._t('_ibdw_evowall_url').'"><div id="bottone_generale"><i alt="" class="sys-icon link"></i></div></a>'; 
 echo '</div>';
 include 'wallblocks.php';
}
echo '<div id="frammento_exe"></div><div id="attendi"><p style="text-align:center;"><img class="attsa" src="modules/ibdw/evowall/templates/base/images/big-loader.gif"></p></div><div class="clear"></div></div>';
?>

<script>
var fgeneralstatus=0;
var vgeneralstatus=0;

function video(status) 
{
 $("#frammento_video").slideToggle("fast"); 
 $("#frammento_foto").slideUp();
 $("#frammento_url").slideUp();
 $("#fremmentositoweb").slideUp();
 $("#erroreurl").slideUp();
 $("#attendi").slideUp();
 var videodefaultupld='<?php echo $DefaultVideoM;?>';
 var videohtml5status='<?php echo $VideoFalshM;?>';
 var videoembmtd='<?php echo $VideoYoutubeM;?>';
 if(status==0 && vgeneralstatus==0) 
 {
  if(videodefaultupld=='Youtube') 
  {
   if(videoembmtd=='on')
   {
    lanciatube('<?php echo trim($namevideoalbum);?>');
    vgeneralstatus=1;
    fgeneralstatus=0;
    $(".divspacer").css("display","block");
   }
   else alert("<?php echo _t('_ibdw_evowall_embed_status');?>");
  }
  else 
  { 
   if(videohtml5status=='on') 
   {
    lanciaclassic('<?php echo trim($namevideoalbum);?>');
    vgeneralstatus=1;
    fgeneralstatus=0;
    $(".divspacer").css("display","block");
   }
   else alert("<?php echo _t('_ibdw_evowall_html5_video_status');?>");
  }  
 }
 else 
 {
  $(".divspacer").css("display","none");
  $('#bloccotubox').html("");
  vgeneralstatus=0;
 }
}

function foto(status) 
{
 $("#frammento_foto").slideToggle("fast");
 $("#frammento_video").slideUp();
 $("#frammento_url").slideUp();
 $("#fremmentositoweb").slideUp();
 $("#erroreurl").slideUp();
 $("#attendi").slideUp();
 var defaultupld='<?php echo $DefaultPhotoM;?>';
 var html5status='<?php echo $PhotoFlashM;?>';
 var regularmd='<?php echo $PhotoRegularM;?>';
 if(status==0 && fgeneralstatus==0) 
 { 
  if(defaultupld=='Regular') 
  {
   if (regularmd=='on') 
   {
    lanciaregularfoto('<?php echo trim($namephotoalbum);?>');
    fgeneralstatus=1;
    vgeneralstatus=0;
    $(".divspacer").css("display","block");
   }
   else alert("<?php echo _t('_ibdw_evowall_regular_status');?>");
  }
  else 
  { 
   if(html5status=='on') 
   {
    lanciahtml5foto('<?php echo trim($namephotoalbum);?>');
    fgeneralstatus=1;
    vgeneralstatus=0;
    $(".divspacer").css("display","block");
   }
   else alert("<?php echo _t('_ibdw_evowall_html5_status');?>");
  }
  
 }
 else 
 {
  $(".divspacer").css("display","none");
  $('#bloccotubox').html("");
  fgeneralstatus=0;
 }
}

function url()
{
 $("#urlspecial").val("<?php echo _t('_ibdw_evowall_urlspecial');?>");
 $("#frammento_url").slideToggle("fast");
 $("#erroreurl").slideUp();
 $("#attendi").slideUp();
 $(".divspacer").css("display","none");
 $('#bloccotubox').html("");
 fgeneralstatus=0;
 vgeneralstatus=0;
}
</script>