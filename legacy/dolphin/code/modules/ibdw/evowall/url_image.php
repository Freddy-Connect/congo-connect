<?php
require_once('../../../inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'design.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'profiles.inc.php');
require_once(BX_DIRECTORY_PATH_INC.'utils.inc.php');
require 'Fastimage.php';
$dom=new domdocument;
$baseurl=$_POST['urlweb'];
$url=$baseurl;

$isyoutube=0;
$verificastringa=strpos($baseurl, 'ttp://');
$verificastringas=strpos($baseurl, 'ttps://');

if($verificastringa==0 and $verificastringas==0) $newstringa = 'http://'.$baseurl;
else $newstringa=$baseurl;
$baseurl=$newstringa;
$url=$newstringa;

function urlreplace($text) {
  $text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
    '<a target=\'_blank\'  href="\\1">\\1</a>', $text);
  $text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)',
    '\\1<a target=\'_blank\'  href="http://\\2">\\2</a>', $text);
  $text = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})',
    '<a target=\'_blank\'  href="mailto:\\1">\\1</a>', $text);
  $text = ereg_replace("(^| )(www([.]?[a-zA-Z0-9_/-])*)", "\\1<a target=\'_blank\'  href=\"http://\\2\">\\2</a>", $text);
  return $text;
}
//get the id of a youtube video
function getYTid($ytURL)  
{  
 $url=parse_url($ytURL, PHP_URL_QUERY);  
 parse_str($url, $arr);  
 return isset($arr['v']) ? $arr['v'] : false;  
}
function checkRemoteFile($url)
{
 $ch=curl_init();
 curl_setopt($ch,CURLOPT_URL,$url);
 // don't download content
 curl_setopt($ch,CURLOPT_NOBODY, 1);
 curl_setopt($ch,CURLOPT_FAILONERROR, 1);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
 if(curl_exec($ch)!==FALSE) return true;
 else return false;
}
@$dom->loadHTMLFile($url);
//get facebook img
foreach( $dom->getElementsByTagName('meta') as $meta ) 
{ 
 $metaData[] = array('property' => $meta->getAttribute('property'),'content' => $meta->getAttribute('content'));
 if ($meta->getAttribute('property')=='og:image')
 {
  $imgfb=$meta->getAttribute('content');
 }
}

$xpath=new domxpath($dom);
if (strpos($url,'youtu')!==false) 
{
 $videocode=getYTid($url);
 $imgsito="http://i3.ytimg.com/vi/".$videocode."/mqdefault.jpg";
 $contatore=1;
 $isyoutube=1;
}
elseif($imgfb!="")
{ 
 $contatore=0;
 $contatore++;
 $immagepath[$contatore]=$imgfb;
 $widtharray[$contatore]="";
}
else
{
 $xNodes=$xpath->query('//img[@src]');
 $contatore=0;
 $minisys=0;
 $presenzemini=0;
 foreach ($xNodes as $xNode)
 {
  $imgsrc=htmlentities($xNode->getAttribute('src'));
  if(urlreplace($imgsrc)!=$imgsrc) $imgsrc=$imgsrc; 
  else $imgsrc=$baseurl.'/'.$imgsrc;
  $width=$xNode->getAttribute('width');
  if ($width=="")
  {
   $time = microtime(true);
   $image = new FastImage($imgsrc);
   list($width, $height) = $image->getSize();
   if ($width=="") $width=140;
  }
  if($width>139)
  {
   if (checkRemoteFile($imgsrc)==true)
   {
    $contatore++;
    $immagepath[$contatore]=$imgsrc;
    $widtharray[$contatore]=$width;
   }
  }
 }
}
$attuale=0;
echo '<div id="contenitore">';	
if ($contatore==0)
{
 echo '<div id="contatoreassoluto" style="margin-left:0 !important;">'. _t('_ibdw_evowall_totpreviewno').'</div><script>$(document).ready(function() {noimage();});</script>';
 $verificase=0;
}
else
{
 if ($contatore==1)
 {
  echo '<div id="contatoreassoluto">'. _t('_ibdw_evowall_totpreview1s') . $contatore . _t('_ibdw_evowall_totpreview2s').'</div>';
  $verificase=1;
 }
 else
 {
  echo '<div id="contatoreassoluto">'. _t('_ibdw_evowall_totpreview1') . $contatore . _t('_ibdw_evowall_totpreview2').'</div>';
  echo '<div id="absolutenonutilizzare"><input type="checkbox" id="anteprimano" onclick="setvaluecheck()" value="0">'. _t('_ibdw_evowall_nopreview').'</div>';
  echo '<div id="selezioneassoluta">'. _t('_ibdw_evowall_selezionapreview').'</div>';
  $verificase=2;
 }
}
for($i=1;$i<$contatore+1;$i++)
{
 if ($isyoutube==0) $imgsito=htmlentities($immagepath[$i]);
 $attuale++;
 if (strpos($imgsito,'/https://') !== false) 
 {
  $imgsito=explode("/https://",$imgsito);
  $imgsito="https://".$imgsito[1];
 }
 elseif (strpos($imgsito,'/http://') !== false) 
 {
  $imgsito=explode("/http://",$imgsito);
  $imgsito="http://".$imgsito[1];
 }
 //inserisco l'immagine attuale			 
 if($attuale==1) echo'<div id="immagine_attuale"><img class="selezionabile" src="'.$imgsito.'" width="120px"/></div><input type="hidden" value="'.$imgsito.'" id="img_'.$attuale.'"/>';
 else echo'<input type="hidden" value="'.$imgsito.'" id="img_'.$attuale.'"/>';
 if ($contatore>1)
 {
  $successiva=$attuale+1;
  $precedente=$attuale-1;
  echo '<div id="contenitorepulsanti">';
  if($attuale==1) echo '<div class="pulsantesx" id="bottsucc'.$attuale.'" onclick="sfoglia('.$successiva.');"><i class="sys-icon caret-right" alt=""></i></div>'; 
  elseif ($attuale>1)
  {
   echo '<div style="display:none;" class="pulsantedx" id="bottprev'.$attuale.'" onclick="sfoglia('.$precedente.');"><i class="sys-icon caret-left" alt=""></i></div>';
   if ($attuale<$contatore) echo '<div style="display:none;" id="bottsucc'.$attuale.'" onclick="sfoglia('.$successiva.');"><i class="sys-icon caret-right" alt=""></i></div>';
  }
  echo '</div>';
 }
}
?>