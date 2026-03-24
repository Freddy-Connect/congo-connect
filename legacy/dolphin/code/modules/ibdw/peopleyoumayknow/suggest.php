<?php
 //get Design Box of this module for the current page
 $currentpage = $_SERVER["REQUEST_URI"];
 if (strpos($currentpage,'index.php') or substr($currentpage, -1)=='/') $page_is="index";
 elseif (strpos($currentpage,'member.php')) $page_is="member";
 $design="SELECT `DesignBox` FROM `sys_page_compose` WHERE (Caption='_ibdw_peopleyoumayknow_titlemodule' AND `Page`='".$page_is."')"; 
 $rund=mysql_query($design);
 $Dvalue=mysql_fetch_assoc($rund);
 $DesignBox=$Dvalue['DesignBox'];
?>
<script>
var lastid=0;
var coverflag=0;
$(document).ready(function(){
    hoverbuttons=0;
    slider=$('.slider1').bxSlider({
    slideWidth: 150,
    slideHeight: 150,
    minSlides: 1,
    maxSlides: 100,
    moveSlides: 0,
    captions: true,
    infiniteLoop: false,
    pager:false,
    responsive:true,
    preloadImages:'visible',
    hideControlOnEnd: true,  
    slideMargin: 16,
    touchEnabled: true,
		swipeThreshold: 50,
		oneToOneTouch: true,
		preventDefaultSwipeX: true,
		preventDefaultSwipeY: true,
    onSliderLoad: function(){
        jQuery(".sk-fading-circle").css("display","none");
        jQuery(".maincontr").css("opacity", "1");
    }
  });
  $('.bx-controls-direction a').hover(function() {
    $("#pymkcover").css("display","none");
    coverflag=0;
  });
  if ('.boxContent'.length)
  {
   $('.boxContent').hover(function() {
    $("#pymkcover").css("display","none");
    coverflag=0;
   });
  }
  $('#slideheader').hover(function() {
    $("#pymkcover").css("display","none");
    coverflag=0;
  });
  if ('.page_block_container'.length)
  {
   $('.page_block_container').mouseleave(function() {  
     $(".pymkcover").css("display","none");
     coverflag=0;
   });
  }

 });
    
  
  function opencover(id,username,profileimg,coverimage,ypos,w,b1,b2,b3,path,franswer,commonfriends,citytext)
  { 
   if (id!=lastid || coverflag==0)
   {
    if (id!=lastid && coverflag==1) {$(".pymkcover").css("display","none");}
    lastid=id;
    coverflag=1;
    
    var buttons;
    var profileinfo;
    profileinfo="";
    buttons="";
    var newleft;
    var answer;
    friendstext="<?php echo _t('_Friends');?>";
    befriendtext="<?php echo _t('_ibdw_peopleyoumayknow_Befriend');?>";
    MessText="<?php echo _t('_Message');?>";
    var positionresized;
    
    var defaultboxwidth=$("#pymkcover").width();
    var defaultcontwidth=$("#slideheader").width();
    semiwidth= defaultboxwidth/2;
    <?php
    if ($DesignBox==0)
    { 
    ?>
     var position = $("#sld"+id).offset();
     absolutetop= position.top+144;
     absoluteleft=position.left;
     var position2 = $("#slideheader").offset();
    <?php
    }
    else
    {
    ?>
     var position = $("#sld"+id).offset();
     absolutetop=210;
     var position2 = $("#slideheader").position();
     absoluteleft=position.left+10-$(".bx-viewport").offset().left;
    <?php
    }
    ?>
    absoluteright= absoluteleft+ $("#sld"+id).outerWidth();
    
    $(".pymkcover").css("top",absolutetop+"px"); 
    absoluteleft2=position2.left;
  
    if (b1==1) {
     buttons=buttons+'<button onclick="$.post(\'list_pop.php?action=friend\', {ID: '+id+'}, function(){$(\'#ibdwajaxy_popup_result_div_'+id+'\').html(\''+franswer+'\');$(\'#pymkcover\').css(\'display\',\'none\');}); return false;" class="bx-btn bx-btn-small bx-btn-ifont"><i class="sys-icon plus"></i>'+befriendtext+'</button>';
    } else if(b1==2) {
     buttons=buttons+'<button onclick="$(\'#ibdwajaxy_popup_result_div_'+id+'\').html(\''+franswer+'\');$(\'#pymkcover\').css(\'display\',\'none\');" class="bx-btn bx-btn-small bx-btn-ifont"><i class="sys-icon plus"></i>'+befriendtext+'</button>';   
    }
    if (b2==1) buttons=buttons+'<button class="bx-btn bx-btn-small bx-btn-ifont" onclick="window.open (\''+path+'viewFriends.php?iUser='+id+'\');"><i class="sys-icon user"></i>'+friendstext+'</button>';
    if (b3==1) buttons=buttons+'<button class="bx-btn bx-btn-small bx-btn-ifont" onclick="window.open (\''+path+'mail.php?mode=compose&amp;recipient_id='+id+'\',\'_self\');"><i class="sys-icon envelope"></i>'+MessText+'</button>';
    //var delimited=absoluteleft2+semiwidth;
    
    //borderpopup è il bordo destro della popup
    var borderpopup=absoluteleft+defaultboxwidth;
    //borderbox è il bordo destro dello slider
    var borderbox=position2.left+defaultcontwidth;    
    var differenceEx=borderpopup-borderbox;
    
    if(differenceEx>0 && absoluteleft<borderbox)  
    {
     //if exceded right(se sborda a destra)
     if((absoluteright-defaultboxwidth)>position2.left) 
     {  
      //if not exceded left (se ribaltando il box non sbordo a sinistra, ribalto)
      newleft=absoluteleft-defaultboxwidth+(absoluteright-absoluteleft);
      if((newleft+defaultboxwidth)>(position2.left+defaultcontwidth)) 
      {
       //if exceded left after (se ribaltando, sbordo a destra, shifto il box di tanti pixel pari a quanto si sborda a destra)
       newleft=absoluteleft-differenceEx;
      }
     }
     else {newleft=absoluteleft-differenceEx;
     }
    }
    else {newleft=absoluteleft;}
    if(newleft<position2.left) {newleft=position2.left;}
    
    
    $(".pymkcover").css("left",newleft+"px");
    if (commonfriends==1) profileinfo='<div id="cmnf"><i class="sys-icon group" alt=""><span class="textinfo">'+commonfriends+'<?php echo _t("_ibdw_peopleyoumayknow_onefriend");?></span></i></div>';
    if (commonfriends>1) profileinfo='<div id="cmnf"><i class="sys-icon group" alt=""><span class="textinfo">'+commonfriends+'<?php echo _t("_ibdw_peopleyoumayknow_morefriend");?></span></i></div>';
    if (citytext!=null) profileinfo=profileinfo+citytext;  
    strhtml= "<div id='covercont'><div id='profilecoversmall'><img id='img"+id+"' src='"+coverimage+"'/></div><div id='firstline'><div id='imgcnt'><img src="+profileimg+"></div><div id='usrn'>"+username+"</div><div id='prfinfo'>"+profileinfo+"</div></div><div id='btnarea'>"+buttons+"</div>";
    $(".pymkcover").html(strhtml);
    

    
    $("#img"+id).load(function()
    { 
     $("#img"+id).attr('src', coverimage);
     $(this).closest("#img"+id).show(0);
     var width=$("#profilecoversmall").width();
     var height=width*0.35;
     if (w>0) {positionresized= width*ypos/w;}
     else {positionresized=0;}
     $("#img"+id).css("top",positionresized+"px");
     $("#profilecoversmall").css("height",height+"px"); 
    });
    lastid=id;      
    setTimeout('$(".pymkcover").css("display","block");',500);
    }
    
   }
   
   
   
</script>
<div class="sk-fading-circle">
  <div class="sk-circle1 sk-circle"></div>
  <div class="sk-circle2 sk-circle"></div>
  <div class="sk-circle3 sk-circle"></div>
  <div class="sk-circle4 sk-circle"></div>
  <div class="sk-circle5 sk-circle"></div>
  <div class="sk-circle6 sk-circle"></div>
  <div class="sk-circle7 sk-circle"></div>
  <div class="sk-circle8 sk-circle"></div>
  <div class="sk-circle9 sk-circle"></div>
  <div class="sk-circle10 sk-circle"></div>
  <div class="sk-circle11 sk-circle"></div>
  <div class="sk-circle12 sk-circle"></div>
</div>
<?php
  include 'Mobile_Detect.php';
  $mobileaccess=0;
  $detect = new Mobile_Detect_PYMK;
  if(!$_SESSION['isMobile'])
  {
    $_SESSION['isMobile'] = $detect->isMobile();
    if($_SESSION['isMobile']) { echo "<style>.bx-controls.bx-has-controls-direction {display: none;}</style>"; $mobileaccess=1; }
  }
  $suggested=[];
  $finallysuggested=[];
  $totalget=0;
  $hasimage=0;
  
  function get_img_profile150($id)
  {
   //Get Profile's photo
   $user_name = getUsername($id);
   $getalbum = "SELECT VALUE FROM sys_options WHERE Name='bx_photos_profile_album_name'"; 
   $runalbum = mysql_query($getalbum);
   $fetchalbum= mysql_fetch_assoc($runalbum);
   $nalbumm = $fetchalbum['VALUE'];
   $namealbum = uriFilter(str_replace("{nickname}",$user_name,$nalbumm));
   $namealbumtrue = str_replace("{nickname}",$namealbum,$nalbumm); 
   $namealbumtrue=addslashes($namealbumtrue);
   $queryprofils="SELECT bx_photos_main.Hash FROM bx_photos_main INNER JOIN sys_albums ON bx_photos_main.ID=sys_albums.LastObjId WHERE bx_photos_main.Owner=".$id." AND sys_albums.Uri='$namealbum' Limit 0,1";
   $risultphoto=mysql_query($queryprofils);
   $countarisphoto=mysql_num_rows($risultphoto);
   if ($countarisphoto>0) 
   {
    $gethash=mysql_fetch_assoc($risultphoto);
    $hashis=$gethash[Hash];
    $mainphoto=BX_DOL_URL_ROOT."m/photos/get_image/browse/".$hashis.".jpg"; 
   }
   else $mainphoto=BX_DOL_URL_MODULES.'ibdw/peopleyoumayknow/templates/base/images/anonymous.png';
   return $mainphoto;
  }
  $myidis=(int)$_COOKIE['memberID'];
  $finalstring="";
  
  function get_friends_of_my_friends($myidis,$limit,$finallysuggested) 
  {
   $queryp="(SELECT ID as FRN FROM sys_friend_list WHERE Profile=".$myidis." AND sys_friend_list.Check=1) UNION (SELECT Profile AS FRN FROM sys_friend_list WHERE ID=".$myidis." AND sys_friend_list.Check=1)";
   $runqueryp=mysql_query($queryp);
   $countresult=mysql_num_rows($runqueryp);
   if ($countresult>0)
   {
    for($i=0;$i<$countresult;$i++)
    {   
     $suggestion=[];
     $friendis=mysql_fetch_assoc($runqueryp);
     //id of my friend $i
     $myfriendis=$friendis[FRN];
     //populate the array of my friends
     $friendsarray[$i]=$myfriendis;
     //the following query return the friends of my friend "with id $i" excluding me
     //for current friend get ids of his friends 
     $queryp2="(SELECT ID as FRN FROM sys_friend_list WHERE Profile=".$myfriendis." AND ID<>".$myidis." AND sys_friend_list.Check=1 LIMIT ".$limit.") UNION (SELECT Profile AS FRN FROM sys_friend_list WHERE ID=".$myfriendis." AND Profile<>".$myidis." AND sys_friend_list.Check=1 LIMIT ".$limit.")";
     $runqueryp2=mysql_query($queryp2);
     $countresult2=mysql_num_rows($runqueryp2);
     if ($countresult2>0)
     {
      //get friends of my friends 
      for($j=0;$j<$countresult2;$j++)
      {
       //get friends id of my friend X
       $friendsOfMyfriendis=mysql_fetch_assoc($runqueryp2);
       //get friends from my friends
       if(!is_friends($myidis,$friendsOfMyfriendis[FRN]))
       {
        $tempvarame='prf'.$friendsOfMyfriendis[FRN];
        $friendsmyfriendis=$friendsOfMyfriendis[FRN];
        if (!isset($_COOKIE[$tempvarame])) 
        {
         if(!in_array($friendsmyfriendis, $finallysuggested))  
         {
          $suggestion[$j]=$friendsmyfriendis;
          array_push($finallysuggested,$suggestion[$j]);
         }
        }
       }
      }
     }    
    }
    $suggested=array_unique($finallysuggested);
  }
  else $suggested=[];
  return $suggested;
 }
 
 $suggested=get_friends_of_my_friends($myidis,$minprofiletoload,$finallysuggested);
 $totalget=count($suggested);
 
  //get more profile if suggested profiles are lower than the default value. This results by the matching of profiles
  if ($totalget< $minprofiletoload)
  {
   //if the previous profiles number is lower than the min profile to suggest imposed in the config you try to suggest a friend using the match results
   $aProfiles=getMatchProfiles($myidis, true, 'none');
   $indexA=0;
   for ($g=0;$g<count($aProfiles);$g++)
   {    
    //get id of profile match and add this id only if not already present in my friends list and in the the list of friends of my friends 
    $idpm=$aProfiles[$g];
    //check if this id is already in the suggested profile array
    if (!empty($suggested)) $inarrayofsuggested=in_array($idpm,$suggested);
    else $inarrayofsuggested=false;
    if (!is_friends($myidis,$idpm) && !$inarrayofsuggested && !isset($_COOKIE['prf'.$idpm])) array_push($suggested,$idpm); 
   }
   $totalget=count($suggested);
  } 
  $unsortedsuggestion=[];
  for ($k=0;$k<$totalget;$k++)
  {
    $unsortedsuggestion[$k][1]=$suggested[$k];
    $unsortedsuggestion[$k][2]=getMutualFriendsCount($myidis,$suggested[$k]);
    if(get_img_profile150($suggested[$k])==BX_DOL_URL_MODULES.'ibdw/peopleyoumayknow/templates/base/images/anonymous.png') $hasimage=0;
    else $hasimage=1;
    $unsortedsuggestion[$k][3]=$hasimage;
  }
  
  //sort array by common friends number value
  function cmp($a, $b)
  {
    return $b[2] - $a[2];
  }
  usort($unsortedsuggestion, "cmp");

  $suggested=$unsortedsuggestion;
  $limitresult=0;
  foreach ( $suggested as $val) 
  {
   if ($limitresult++>$minprofiletoload-1) break;
   $newtempvar=$val[1];
   if(!isset($_COOKIE['prf'.$newtempvar]))
   {                 
    //Profile's photo
    $getnumberofmutualfriends=getMutualFriendsCount($myidis,$val[1]);
    if($getnumberofmutualfriends>0) 
    {
     if($getnumberofmutualfriends==1) $txtmutual="<div class='commonfrndz'>".str_replace("<NUMBER>",$getnumberofmutualfriends,_t('_ibdw_peopleyoumayknow_singlemutualfriends'))."</div>";
     else $txtmutual="<div class='commonfrndz'>".str_replace("<NUMBER>",$getnumberofmutualfriends,_t('_ibdw_peopleyoumayknow_mutualfriends'))."</div>";
    }
    else $txtmutual="";
    $checkifrequestsent="SELECT * FROM `sys_friend_list` WHERE (ID=$myidis AND Profile=$val[1] AND `sys_friend_list`.`Check`=0)";
    $runthinsquery=mysql_query($checkifrequestsent);
    $getifexists=mysql_num_rows($runthinsquery);
    $profileimage=get_img_profile150($val[1]);       
    if($IBDWProfileCover=='on')
    {
     //get the profilecover
     $qpf="SELECT Hash, PositionY, ibdw_profile_cover.width FROM ibdw_profile_cover WHERE Owner=".$val[1]. " ORDER BY ID DESC Limit 0,1"; 
     $resultpf=mysql_query($qpf);
     $contarespf=mysql_num_rows($resultpf);
     if ($contarespf==0)
     {
      $defaultimage='modules/ibdw/peopleyoumayknow/templates/base/images/default.jpg';
      $defaultposition=0;
      $widthimg=0;
     }
     else
     {
      $mainpf = mysql_fetch_assoc($resultpf);
      $defaultimagetwidth=$mainpf['width'];
      $checkifexistimage="SELECT ID FROM bx_photos_main WHERE Hash='".$mainpf['Hash']."'";
      $resultchk = mysql_query($checkifexistimage);
      $contachk = mysql_num_rows($resultchk);
      if($contachk>0) 
      {
       $defY=$mainpf['PositionY'];
       $defaultimage='m/photos/get_image/file/'.$mainpf['Hash'].'.jpg';
       $defaultposition= $defY;
       $widthimg=$mainpf['width'];
      }
      else
      {
       $defaultimage='modules/ibdw/peopleyoumayknow/templates/base/images/default.jpg';
       $defaultposition=0;
       $contarespf=0;
       $widthimg=0;
      }
     }      
     $coverimage=BX_DOL_URL_ROOT.$defaultimage;
     $buttonsextension1=0;
     $buttonsextension2=0;
     $buttonsextension3=0;
     $getcity="SELECT City FROM Profiles WHERE ID=$val[1]";
     $runcityq=mysql_query($getcity);
     $cityexists=mysql_num_rows($runcityq);
     if($cityexists>0) 
     {
      $city=mysql_fetch_assoc($runcityq);
      $cityname=$city['City'];
      if (trim($cityname)<>"") $citytext=addslashes(htmlspecialchars(("<div id='citybox'><i class='sys-icon home'></i>"._t('_ibdw_peopleyoumayknow_lives').$cityname."</div>")));
      else $citytext="";
     }
     else $citytext="";
    }
    if ($getifexists==0) {$friendrequestbuttonnaswer= addslashes(htmlspecialchars(_t("_pending_friend_request")));$button='<button onclick="$.post(\'list_pop.php?action=friend\', {ID: '.$val[1].'}, function(){$(\'#ibdwajaxy_popup_result_div_'.$val[1].'\').html(\''.$friendrequestbuttonnaswer.'\');}); return false;" class="bx-btn bx-btn-small bx-btn-ifont"><i class="sys-icon plus"></i>'._t('_ibdw_peopleyoumayknow_Befriend').'</button>';}
    else {$friendrequestbuttonnaswer= addslashes(htmlspecialchars(_t("_ibdw_peopleyoumayknow_Befriend_nomore")));$button='<button onclick="$(\'#ibdwajaxy_popup_result_div_'.$val[1].'\').html(\''.$friendrequestbuttonnaswer.'\');" class="bx-btn bx-btn-small bx-btn-ifont"><i class="sys-icon plus"></i>'._t('_ibdw_peopleyoumayknow_Befriend').'</button>';} 
    $button2='<button class="bx-btn bx-btn-small bx-btn-ifont" onclick="window.open (\''.BX_DOL_URL_ROOT.'viewFriends.php?iUser='.$val[1].'\');"><i class="sys-icon user"></i>'._t('_Friends').'</button>';
    $button3='<button class="bx-btn bx-btn-small bx-btn-ifont" onclick="window.open (\''.BX_DOL_URL_ROOT.'mail.php?mode=compose&amp;recipient_id='.$val[1].'\',\'_self\');"><i class="sys-icon envelope"></i>'._t('_Message').'</button>';
    if($displaybefriend=='on') 
    {
     if ($getifexists==0) $buttonsextension1=1;
     else $buttonsextension1=2;
    }
    if($displayfriends=='on') $buttonsextension2=1;
    if($displayfmessage=='on') $buttonsextension3=1;
    if($IBDWProfileCover=='on' and $mobileaccess==0) $finalstring=$finalstring."<div class='slide' id='sld".$val[1]."'><div class='slideseparator'></div><div class='imgcutter' onmouseover=\"opencover(".$val[1].",'".getNickname($val[1])."','".$profileimage."','".$coverimage."','".$defaultposition."',".$widthimg.",".$buttonsextension1.",".$buttonsextension2.",".$buttonsextension3.",'".BX_DOL_URL_ROOT."','".$friendrequestbuttonnaswer."',".$getnumberofmutualfriends.",'".$citytext."')\" onclick='location.href=\"".BX_DOL_URL_ROOT.getUsername($val[1])."\"'><img title='".getNickname($val[1])."' src='".$profileimage."'></div><div class='titleu'>".$txtmutual."</div><div class='frqst'><div id='ibdwajaxy_popup_result_div_".$val[1]."' class='ownstyle'>";
    else $finalstring=$finalstring."<div class='slide' id='sld".$val[1]."'><div class='slideseparator'></div><div class='imgcutter' onclick='location.href=\"".BX_DOL_URL_ROOT.getUsername($val[1])."\"'><img title='".getNickname($val[1])."' src='".$profileimage."'></div><div class='titleu'>".$txtmutual."</div><div class='frqst'><div id='ibdwajaxy_popup_result_div_".$val[1]."' class='ownstyle'>";
    if($displaybefriend=='on') $finalstring=$finalstring.$button;
    if($displayfriends=='on') $finalstring=$finalstring.$button2;
    if($displayfmessage=='on') $finalstring=$finalstring.$button3;
    $button4='<button id="rembutt" onclick="$(\'#sld'.$val[1].'\').remove();slider.reloadSlider({
        startSlide: slider.getCurrentSlide(),
        slideWidth: 150,
        slideHeight: 150,
        minSlides: 1,
        maxSlides: 100,
        moveSlides: 0,
        captions: true,
        infiniteLoop: false,
        pager:false,
        responsive:true,
        preloadImages:\'visible\',
        hideControlOnEnd: true,  
        touchEnabled: true,
		    swipeThreshold: 50,
		    oneToOneTouch: true,
		    preventDefaultSwipeX: true,
		    preventDefaultSwipeY: false,
        slideMargin: 16,
        onSliderLoad: function(){
        jQuery(\'.maincontr\').css(\'visibility\', \'visible\');
    }
    });suggested--;if(suggested==0) {$(\'.maincontr\').replaceWith(\'<div class=msgbox_noprofile>'._t('_ibdw_peopleyoumayknow_nosuggestion').'</div>\');setTimeout(function() {$(\'.msgbox_noprofile\').fadeOut(\'fast\');}, 2000);  $(\'.outside\').replaceWith(\'\');};document.cookie=\'prf'.$val[1].'=1;\'" class="bx-btn bx-btn-small bx-btn-ifont"><i class="sys-icon remove" alt="'._t('_Hide').'"></i></button>';
    $finalstring=$finalstring.$button4;
    $finalstring=$finalstring."</div></div></div>";
    $txtmutual="";
    //totalget is the number of profile suggested as friends of my friends
    $totalget=count($suggested);
   }
  }   
  if ($totalget==0) echo "<script>jQuery('.sk-fading-circle').css('display','none');</script>";
  else
  {
   echo "<div class='maincontr'><div id='slideheader'><i class='sys-icon male' alt=''></i><div class='slidetitle'>"._t('_ibdw_peopleyoumayknow_slideheader')."</div><div class='slidesubtitle'>"._t('_ibdw_peopleyoumayknow_slidesubheader')."</div></div><div class='slider1'>";
   echo $finalstring;
   echo "</div></div><div class='clear_both'></div>";
   echo "<script>var suggested=".$totalget."</script>";
  }
  //echo "<div class='pymkcover' id='pymkcover' onmouseleave='$(\"#pymkcover\").css(\"display\",\"none\");'></div>";
  echo "<div class='pymkcover' id='pymkcover'></div>";
?>