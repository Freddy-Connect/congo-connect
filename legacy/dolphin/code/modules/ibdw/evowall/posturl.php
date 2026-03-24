<?php
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');


//New code
$dom=new domdocument;
$url=str_replace("<br>","",trim($_POST['urlweb']));
$url=trim($url);

$idprofile=(int)$_POST['idprofile'];
$messagesent=trim(str_replace("\n"," <br>",str_replace("\'","codapos1",str_replace("&","%26",$_POST['message']))));

if (strpos($url,'youtu')!==false) 
{
 $url=str_replace('youtu.be/','www.youtube.com/watch?v=',$url);
 $url=str_replace('www.youtu.be/','www.youtube.com/watch?v=',$url);
}
$url=str_replace('<br>','',$url);
$url=str_replace('<br />','',$url);
$url=str_replace('<br/>','',$url);
$url=str_replace('\n','',$url);
$url=str_replace(' ','',$url);
$url=str_replace('https://','',$url);
$url=str_replace('http://','',$url);

//$pattern='|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i';
$pattern="/^((http(s)?:\/\/)|(www\.))([a-z0-9][-.a-z0-9]+)((\.[a-z]{2,4})[?\/]?)([?\/][-_~;:.,'*^+()=!|?£§$%&amp;#A-Za-z0-9\/]*)?$/";
if (preg_match($pattern, "http://".$url)) 
{
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $url);
 curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt ($ch, CURLOPT_FILETIME, 1);
 curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 2);
 curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
 $store = curl_exec ($ch);
 //Get response code 
 $response_code=curl_getinfo($ch,CURLINFO_HTTP_CODE); 
 //Not found?
 
 if ($response_code=='404' or $response_code=='0') $newurl=false;
 else $newurl= str_replace("HTTP","http",str_replace("HTTPS","https",(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL))));
 //else $newurl= strtolower(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
 if($ch!= null) curl_close($ch);
}
else $newurl=false;

if($newurl==false) {echo '<div id="erroreurl">'._t('_ibdw_evowall_erroreurl').'</div>'; exit();}
else
{
 if ($newurl<>$url)
 {
  if (preg_match($pattern,$newurl)) 
  {
   //$newurl is the real url with the own protocol http or https 
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $newurl);
   curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt ($ch, CURLOPT_FILETIME, 1);
   curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 2);
   curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
   $store = curl_exec ($ch);
   //Get response code 
   $response_code=curl_getinfo($ch,CURLINFO_HTTP_CODE); 
   //Not found?
   if ($response_code=='404' or $response_code=='0') $newurl=false;
   else $newurl= str_replace("HTTP","http",str_replace("HTTPS","https",(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL)))); 
   if($ch!= null) curl_close($ch);
  }
  else $newurl=false;
 }
 if($newurl==false) {echo '<div id="erroreurl">'._t('_ibdw_evowall_erroreurl').'</div>'; exit();}
 else
 {
  $newstringa=$newurl;
  /*We extract the Title from the head tags:*/
  preg_match("/<head.*>(.*)<\/head>/smUi",$store, $headers);
  if(count($headers) > 0)
  {
   /*Fetch the charset of the page*/    
   $trovato=strpos($headers[1],"charset=");
   if (preg_match('/charset\s*=\s*([a-zA-Z0-9- _]+)/', $headers[1], $results)) $charset= $results[1];
   elseif(!$trovato===false) 
   {
    $charset=str_replace("charset=","",substr($headers[1],$trovato,$trovato+10));
    $charset=str_replace('"','',$charset);
    $charset=str_replace("'","",$charset);
    $fine=strpos($charset,">");
    $charset=substr($charset,0,$fine);
    $charset=str_replace("<","",$charset);
    $charset=str_replace(">","",$charset);
   }
   else $charset='None';
   
   if(preg_match("/<title>(.*)<\/title>/smUi",$headers[1], $titles))
   {
    if(count($titles) > 0)
    {  
     /*If the charset information has been extracted, we convert it to UTF-8 - Otherwise we assume it's already UTF-8*/
     if($charset == 'None') $title=trim(strip_tags($titles[1]));
     else $title=trim(strip_tags(iconv($charset, "UTF-8", $titles[1])));
    }
   }
   else
   {
    /*If there is no title given we take the url as a title*/
    if(strlen($url) > 30) $title=trim(substr($url,30)).'...';		
    else $title= trim($url);
   }   
  }
  else
  {
   /*If there is no title given we take the url as a title*/
   if(strlen($url) > 30) $title=trim(substr($url,30)).'...';		
   else $title= trim($url);
  }
  /*Let's fetch the META description or give a description is there is not description available*/
  preg_match("|<meta[^>]*description[^>]*content=\"([^>]+)\"[^>]*>|smUi",$headers[1], $matches);
  if(count($matches) > 0)
  {
   if($charset != 'None') $description= trim(strip_tags(iconv($charset, "UTF-8",$matches[1])));
   else $description= trim(strip_tags($matches[1]));
  }
  else
  {
   //if the description is not retrieved using this method, try to use the php get_meta_tags function
   $metatags=get_meta_tags($newurl);
   $descr=$metatags['description'];
   if ($descr<>"")
   {
	if($charset != 'None') $description= trim(strip_tags(iconv($charset, "UTF-8",$descr)));
    else $description= trim(strip_tags($descr));
   }
   else
   {
    preg_match("/<body.*>(.*)<\/body>/smUi",$store, $matches);
    if(count($matches) > 0)
    {
     //$description = preg_replace('/<(script|style)(?:(?!<\/?\1).)*?<\/\1>/s',"",$matches[1]);
     if($charset != 'None') $description= trim(substr(trim(strip_tags(preg_replace('/<(script|style)(?:(?!<\/?\1).)*?<\/\1>/s',"",iconv($charset, "UTF-8",$matches[1])))),0,250));
     else $description= trim(substr(trim(strip_tags(preg_replace('/<(script|style)(?:(?!<\/?\1).)*?<\/\1>/s',"",$matches[1]))),0,250));
    }
    else 
    {
     if($charset != 'None') $description= trim(substr(trim(strip_tags(iconv($charset, "UTF-8",$store))),0,250));
     else $description= trim(substr(trim(strip_tags($store)),0,250));
    }
   }
  }
$titolosito=$title;
$descrizione=$description;
}
}


if(strpos($newurl, "http://") !== false) $finalurl="http://".$url;
else $finalurl="https://".$url;
?>

<div id="fremmentositoweb" class="blocco_url" style="display:block;">
<?php
echo '<div class="chiudiweb" onclick="closeweb_wall();" title="'. _t('_ibdw_evowall_closelink').'"><i alt="" class="sys-icon remove"></i></div>';
echo '<div id="imgload"><img src="modules/ibdw/evowall/templates/base/images/load.gif" class="load_image"/></div>';	   
echo '<div id="destra" class="visibilita2">';
if ($messagesent<>'' and str_replace('&','%26',$newstringa)<>str_replace('youtu.be/','www.youtube.com/watch?v=',$messagesent)) echo "<div id=messagepreview>".str_replace("codapos1","'",$messagesent)."</div>";
echo '<h2>'.$titolosito.'</h2>';
echo '<h3>'.$finalurl.'</h3>';
echo '<p>'.$descrizione.'</p>';
echo '<input type="hidden" id="nomesito" value="'.urlencode($titolosito).'">';
echo '<input type="hidden" id="descrizione" value="'.urlencode($descrizione).'">';
echo '<input type="hidden" id="indirizzo" value="'.$finalurl.'">';
echo '<div id="contatorebshare">';
if ($messagesent<>'' and str_replace('&','%26',$newstringa)<>$messagesent)
{
 echo '<div class="shareiturl" onclick="saveevowall('.$idprofile.');">'. _t('_ibdw_evowall_messageandpreview').'</div>';
 echo '<div class="justmessage" onclick="justmess(\''.$messagesent.'\');">'._t('_ibdw_evowall_justmessage').'</div>';
}
else
{
 $messagesent="";
 echo '<div class="shareiturlonly" onclick="saveevowall('.$idprofile.');">'. _t('_ibdw_evowall_bott_condividi_3').'</div>';
} 
echo '</div>';//Chiusura del contenitore dei pulsanti
echo '</div>';//Chiusura del lato destro
echo '</div>';//Chiusura del box contenitore
?>
<div class="clear"></div>
<script>
$(document).ready(function() {
  inviaimg();
});
function inviaimg() { 
  var indirizzo = '<?php echo $newurl;?>';
  $.ajax({
   type: 'POST',
   data: "urlweb=" + indirizzo,
   url: 'modules/ibdw/evowall/url_image.php',
   cache: false,
    success: function(data) {
     $('#imgload').html(data);
     $("#contatorebshare").fadeIn(1);
    }
});
}

function noimage(){
$("#destra").attr("class", "visibilita0");
$("#contatorebshare").fadeIn(1);
}
function sfoglia(foto) 
{
 var bottoneprec=foto-1;
 var bottonesucc=foto+1;
 var indirizzo_immagine = $("#img_"+foto).val();
 var nuova_immagine = '<img class="selezionabile" src="'+indirizzo_immagine+'" width="120px"/>';
 $("#immagine_attuale").html(nuova_immagine);
 $("#bottsucc"+bottoneprec).css('display','none');
 $("#bottsucc"+bottonesucc).css('display','none');
 $("#bottprev"+bottoneprec).css('display','none');
 $("#bottprev"+bottonesucc).css('display','none');
 $("#bottsucc"+foto).css('display','block');
 $("#bottsucc"+foto).addClass("pulsantesx");
 $("#bottprev"+foto).css('display','block');
 $("#bottprev"+foto).addClass("pulsantedx");
}
function indietrofoto(fotoprecedente) {}
function setvaluecheck() 
{ 
 var valore = $("#anteprimano").val(); 
 if(valore==0) {$("#anteprimano").val('1');}
 else if(valore==1) {$("#anteprimano").val('0');}
}
function saveevowall(idprofile) 
{     
 var nomesito=$("#nomesito").val();
 var descrizione=$("#descrizione").val();
 var indirizzo=$("#indirizzo").val();
 var immagine=$(".selezionabile").attr("src");
 var anteprimano=$("#anteprimano").val();
 var nomesito1=nomesito.replace("&","execommerciale");
 var nomesito2=nomesito.replace(/'/g,"persaccento");
 var descrizione1=descrizione.replace("&","execommerciale");
 var descrizione2=descrizione1.replace(/'/g,"persaccento");
 var messagesent="<?php echo $messagesent;?>";
 $.ajax
 ({
  type:'POST',data:{nomesito:nomesito2,descrizione:descrizione2,indirizzo:indirizzo,anteprimano:anteprimano,immagine:immagine,message:messagesent,idprofile:idprofile},
  url: 'modules/ibdw/evowall/exe_url.php',
  cache: false,success: function(data) 
  {
   $("#fremmentositoweb").slideUp();
   $("#frammento_siti").slideUp();
   $("#frammento_url").slideUp();
   agg_ajax();   
  }
 });
}
</script>