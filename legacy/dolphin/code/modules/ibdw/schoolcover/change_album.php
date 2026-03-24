<?php
  require_once( '../../../inc/header.inc.php' );
  require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
  
  $ewsa = $_COOKIE['memberID']; 
  $hash = $_POST['hashe'];
  $id= (int)$_POST['id'];
  $UriSelect="SELECT uri From modzzz_schools_main where id=".$id;
  $getUri=mysql_query($UriSelect);
  $UriRetrieve=mysql_fetch_assoc($getUri);
  $Uri= $UriRetrieve['uri'];
  
    
  $verifica = "SELECT ID FROM ibdw_school_cover WHERE Owner = ".$ewsa." AND Uri='".$Uri."' LIMIT 1";
  $exeverifica = mysql_query($verifica);
  $num_rows = mysql_num_rows($exeverifica);
    
  if($num_rows==0){ 
    $insequery = "INSERT INTO ibdw_school_cover (Owner,Hash,Uri) VALUES ('".$ewsa."','".$hash."','".$Uri."')";
    $runquery = mysql_query($insequery);
  }
  else { 
    $num_fetch = mysql_fetch_assoc($exeverifica);
    $id_elemento = $num_fetch['ID']; 
    $insequery = "UPDATE `ibdw_school_cover` SET `Hash` = '".$hash."' WHERE `ID` = ".$id_elemento. " AND Uri='".$Uri."'";
    $runquery = mysql_query($insequery);
  }
  
  $slx = "SELECT uri FROM bx_photos_main WHERE `Hash` = '".$hash."'";
  $exeslx = mysql_query($slx);
  $fetchslx = mysql_fetch_assoc($exeslx);
  $namefile = $fetchslx['uri'];
  


  $profilevector = getProfileInfo($ewsa);
  $ProfileNameis=getNickname($ewsa);
  
  
  
  $array["profile_link"] = BX_DOL_URL_ROOT.$profilevector['NickName'];
  $array["profile_nick"] = $ProfileNameis;
  $array["entry_url"] = BX_DOL_URL_ROOT.'m/photos/view/'.$namefile;
  $array["recipient_p_link"] = BX_DOL_URL_ROOT.$profilevector['NickName'];
  $array["recipient_p_nick"] = $ProfileNameis;
  $array["school_uri"] = BX_DOL_URL_ROOT."m/schools/view/".$Uri;
  $array["currenthash"] = $hash;

  $str = serialize($array);
  
  if ($profilevector['Sex']=="male") $key='_ibdw_schoolcover_update_male';
  elseif ($profilevector['Sex']=="female") $key='_ibdw_schoolcover_update_female';
  else $key='_ibdw_schoolcover_update';
  $insequery = "INSERT INTO bx_spy_data (sender_id,lang_key,params) VALUES('".$ewsa."','".$key."','".$str."')";
  $runquery = mysql_query($insequery);
?>