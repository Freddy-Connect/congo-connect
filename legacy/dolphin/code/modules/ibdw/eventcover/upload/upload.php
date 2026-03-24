<?php
require_once( '../../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
       
function findexts ($filename) 
{ $filename = strtolower($filename) ; $exts = split("[/\\.]", $filename) ; $n = count($exts)-1; $exts = $exts[$n]; return $exts; }


function imagetranstowhite($trans) 
{
	// Create a new true color image with the same size
	$w = imagesx($trans);
	$h = imagesy($trans);
	$white = imagecreatetruecolor($w, $h);
 
	// Fill the new image with white background
	$bg = imagecolorallocate($white, 255, 255, 255);
	imagefill($white, 0, 0, $bg);
 
	// Copy original transparent image onto the new image
	imagecopy($white, $trans, 0, 0, 0, 0, $w, $h);
	return $white;
}

function resampimagejpg( $forcedwidth, $forcedheight, $sourcefile, $destfile)
{
  $fw=$forcedwidth;
  $fh=$forcedheight;
  $is=getimagesize($sourcefile);
  $scala=min($fw/$is[0],$fh/$is[1]);
  $iw=floor($scala*$is[0]);
  $ih=floor($scala*$is[1]);

  $img_src = imagecreatefromjpeg( $sourcefile );
  $img_dst = imagecreatetruecolor( $iw, $ih );
  imagecopyresampled( $img_dst, $img_src, 0, 0, 0, 0, $iw, $ih, $is[0], $is[1] );
  if(!imagejpeg( $img_dst, $destfile, 90 )) exit();
}

function cropImage($nw, $nh, $source, $dest) {
	$size = getimagesize($source);
	$w = $size[0];
	$h = $size[1];
	$simg = imagecreatefromjpeg($source);
	$dimg = imagecreatetruecolor($nw, $nh);   
	$wm = $w/$nw;
	$hm = $h/$nh;
	$h_height = $nh/2;
	$w_height = $nw/2;
	if($w> $h) {
		$adjusted_width = $w / $hm;
		$half_width = $adjusted_width / 2;
		$int_width = $half_width - $w_height;
		imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$nh,$w,$h);
	} elseif(($w <$h) || ($w == $h)) {
		$adjusted_height = $h / $wm;
		$half_height = $adjusted_height / 2;
		$int_height = $half_height - $h_height;
		imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$nw,$adjusted_height,$w,$h);
	} else {
		imagecopyresampled($dimg,$simg,0,0,0,0,$nw,$nh,$w,$h);
	}
	imagejpeg($dimg,$dest,100);
}

//get last PHOTO id
  mysql_query("SET NAMES 'utf8'");
  $cover_query = "SELECT id_object FROM sys_albums_objects ORDER BY id_object DESC LIMIT 0,1";
  $qrun = mysql_query($cover_query);
  $idextract = mysql_fetch_assoc($qrun);
  $lastid = $idextract['id_object'];
  $lastid++;

//size options for Boonex Photos

  //medium size width and height
  $getwidthdefault="SELECT sys_options.VALUE from sys_options WHERE sys_options.Name='bx_photos_file_width'";
  $runq=mysql_query($getwidthdefault);
  $getdefaultw=mysql_fetch_row($runq);
  $m_width=$getdefaultw[0];
  
  $getheightdefault="SELECT sys_options.VALUE from sys_options WHERE sys_options.Name='bx_photos_file_height'";
  $runq=mysql_query($getheightdefault);
  $getdefaultw=mysql_fetch_row($runq);
  $m_height=$getdefaultw[0];
    

$tempFile = $_POST['file']; 
$ext = findexts ($tempFile);
$namefile = $lastid.'.jpg';
$destination= BX_DIRECTORY_PATH_ROOT.'modules/boonex/photos/data/files/'.$lastid.'.jpg';
list($width, $height) = getimagesize($tempFile);

//convert image in jpg e locate it in the photos files folder
function moveandconvertoJPG($originalfile,$dest,$nwt,$nht,$wt,$ht,$type)
{
 $oSource=$originalfile;
 $oDestin=$dest;
 switch($type) {
		case 'gif':
		$oCreateImg = imagecreatefromgif($oSource);
		break;
		case 'jpg':
		$oCreateImg = imagecreatefromjpeg($oSource);
		break;
    case 'jpeg':
		$oCreateImg = imagecreatefromjpeg($oSource);
		break;
		case 'png':
		$oCreateImg1 = imagecreatefrompng($oSource); 
    $oCreateImg = imagetranstowhite($oCreateImg1);          
		break;
 }
 $oImg=imagecreatetruecolor($nwt, $nht);
 imagecopyresized($oImg, $oCreateImg, 0, 0, 0, 0, $nwt, $nht, $wt, $ht);
 imagejpeg($oImg, $oDestin, 75);
}


moveandconvertoJPG($_POST['file'],$destination,$width,$height,$width,$height,$ext);
$newpath=BX_DIRECTORY_PATH_ROOT.'modules/boonex/photos/data/files/';
resampimagejpg($m_width, $m_height, $newpath.'/'.$lastid.'.jpg', $newpath.'/'.$lastid.'_m.jpg');
cropImage(480, 480, $newpath.'/'.$lastid.'_m.jpg',$newpath.'/'.$lastid.'_t_2x.jpg');
cropImage(240, 240, $newpath.'/'.$lastid.'_m.jpg',$newpath.'/'.$lastid.'_t.jpg');
cropImage(64, 64, $newpath.'/'.$lastid.'_m.jpg',$newpath.'/'.$lastid.'_rt.jpg');
cropImage(32, 32, $newpath.'/'.$lastid.'_m.jpg',$newpath.'/'.$lastid.'_ri.jpg');

include BX_DIRECTORY_PATH_MODULES.'ibdw/eventcover/config.php';
$userid = $_POST['user']; 
$idalbum = $_POST['album'];
$EventID=$_POST['EventID'];
$UriSelect="SELECT EntryUri From ".$eventstableis." where ID=".$EventID;
$getUri=mysql_query($UriSelect);
$UriRetrieve=mysql_fetch_assoc($getUri);
$Uri= $UriRetrieve['EntryUri'];

$temptitle= str_replace(".".$ext,"",$_POST['filename']);
$querynamefile=title2uri($temptitle);

$hash = md5(RAND());
$size = $width.'x'.$height;
$insequery = "INSERT INTO bx_photos_main (ID,Owner,Ext,Size,Title,Uri,Status,Hash,bx_photos_main.Date) VALUES('$lastid','$userid','$ext','$size','$temptitle','$querynamefile','approved','$hash',".time().")";
$runquery = mysql_query($insequery);
    
$insequery = "INSERT INTO sys_albums_objects (id_album,id_object) VALUES('$idalbum','$lastid')";
$runquery = mysql_query($insequery);
    
$update = "SELECT ObjCount FROM sys_albums WHERE ID = ".$idalbum." LIMIT 1";
$exeupdate = mysql_query($update);
$fetchupdate = mysql_fetch_assoc($exeupdate);
    
$objcout = (int)$fetchupdate['ObjCount'];
$newobjcout = $objcout+1;
    
$updaten = "UPDATE sys_albums SET ObjCount = ".$newobjcout." WHERE ID = ".$idalbum;
$exeupdaten = mysql_query($updaten); 
    
$verifica = "SELECT ID FROM ibdw_event_cover WHERE Owner = ".$userid." AND Uri='".$Uri."' LIMIT 1";
$exeverifica = mysql_query($verifica);
$num_rows = mysql_num_rows($exeverifica);
    
if($num_rows==0)
{ 
 $insequery = "INSERT INTO ibdw_event_cover (Owner,Hash,Uri) VALUES('".$userid."','".$hash."','".$Uri."')";
 $runquery = mysql_query($insequery);
}
else
{ 
 $num_fetch = mysql_fetch_assoc($exeverifica);
 $id_elemento = $num_fetch['ID']; 
 $insequery = "UPDATE `ibdw_event_cover` SET `Hash` = '".$hash."' WHERE `ID` = ".$id_elemento." AND Uri='".$Uri."'";
 $runquery = mysql_query($insequery);
}
    

    $profilevector = getProfileInfo($userid);
    $ProfileNameis=getNickname($userid);
    
    $array["profile_link"] = BX_DOL_URL_ROOT.getUsername($userid);
    $array["profile_nick"] = $ProfileNameis;
    $array["entry_url"] = BX_DOL_URL_ROOT.'m/photos/view/'.$querynamefile;
    $array["recipient_p_link"] = BX_DOL_URL_ROOT.getUsername($userid);
    $array["recipient_p_nick"] = $ProfileNameis;
    $array["event_uri"] = BX_DOL_URL_ROOT.$eventpath."view/".$Uri;
    $array["currenthash"] = $hash;

    $str = serialize($array);
    if ($profilevector['Sex']=="male") $key='_ibdw_eventcover_update_male';
    elseif ($profilevector['Sex']=="female") $key='_ibdw_eventcover_update_female';
    else $key='_ibdw_eventcover_update';
    $insequery = "INSERT INTO bx_spy_data (sender_id,lang_key,params) VALUES('".$userid."','".$key."','".$str."')";
    $runquery = mysql_query($insequery);

unlink($_POST['file']);
unlink($_POST['tempfile']);
?>