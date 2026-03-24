<?php
if($id_mode==1) $query="SELECT * FROM bx_spy_data WHERE id=".$id_relativo;
else
{
 include 'config.php';
 //Get type of grouping
 if ($timing_similar=="Day") $groupcond=", DAY(date)";
 elseif ($timing_similar=="Week") $groupcond=", WEEK(date)";
 elseif ($timing_similar=="Month") $groupcond=", MONTH(date)";
 
 //get page
 if (strpos($pagina,'index.php') or substr($pagina, -1)=='/') $miapag="home";
 elseif (strpos($pagina,'member.php')) $miapag="account";
 else $miapag="profile";
 
 

 if(isset($typeoforder)) 
 {
  $typeoforder=$typeoforder;
 }
 else
 { 
  if(isset($_COOKIE["typeoforder"]))
  {
   if($_COOKIE["typeoforder"]=='1') $typeoforder="Popular"; 
   else $typeoforder=""; 
  } 
  else $typeoforder="";
 }
 if ($miapag=="account" or $miapag=="home") 
 {
  include('mqconditions.php');
  if (($miapag=='account' and $DefaultAccountPrivacy=='Friends') or ($miapag=='home' and $DefaultHomePrivacy=='Friends')) $friendfilter="(bx_spy_data.sender_id=".$accountid." OR bx_spy_data.recipient_id=".$accountid." OR bx_spy_data.sender_id IN (SELECT ID FROM sys_friend_list WHERE Profile=".$accountid." AND sys_friend_list.Check=1 UNION SELECT Profile FROM sys_friend_list WHERE ID=".$accountid." AND sys_friend_list.Check=1) OR bx_spy_data.recipient_id IN (SELECT ID FROM sys_friend_list WHERE Profile=".$accountid." AND sys_friend_list.Check=1 UNION SELECT Profile FROM sys_friend_list WHERE ID=".$accountid." AND sys_friend_list.Check=1)) AND ";
  //MEMBER (Normal news with grouping)
  if ($typeoforder!="Popular")
  {
   if ($grouping=='on') 
   {
    $query="SELECT *,COUNT(*) as recordsfound FROM (SELECT * FROM bx_spy_data WHERE (".$friendfilter." (PostCommentsN + PostLikeN)=0 AND ".$conditions.") GROUP BY id ORDER BY id DESC) AS T1";
	if ($soloup=="true") $query=$query." WHERE (id>$ultimoid) AND date>(NOW()- INTERVAL ".$daysmostpopular." DAY) ";
	$query=$query."
	          GROUP BY sender_id, lang_key ".$groupcond."  
			  UNION  
			  SELECT *,COUNT(*) as recordsfound FROM (SELECT * FROM bx_spy_data WHERE (".$friendfilter." (PostCommentsN + PostLikeN)>0 AND ".$conditions.") GROUP BY id ORDER BY id DESC) AS T1";
	if ($soloup=="true") $query=$query." WHERE (id>$ultimoid) AND date>(NOW()- INTERVAL ".$daysmostpopular." DAY) ";	  
	$query=$query." GROUP BY sender_id, lang_key ".$groupcond;
	$query=$query." ORDER BY id DESC LIMIT $parami,$cont";
   }
   else 
   {//MEMBER (Normal news without grouping)
    $query="SELECT * FROM bx_spy_data WHERE (".$friendfilter.$conditions.")"; 
	if ($soloup=="true") $query=$query." AND (id>$ultimoid) AND date>(NOW()- INTERVAL ".$daysmostpopular." DAY)";
    $query=$query." ORDER BY id DESC LIMIT $parami,$cont";
   }
  }
  elseif ($typeoforder=="Popular") 
  {
   if ($grouping=='on') 
   { //MEMBER (Most Popular with)
    $query="SELECT *,COUNT(*) as recordsfound FROM (SELECT * FROM bx_spy_data WHERE (".$friendfilter." (PostCommentsN + PostLikeN)=0 AND ".$conditions.") GROUP BY id ORDER BY id DESC) AS T1";
	  if ($soloup=="true") $query=$query." WHERE (id>$ultimoid) AND date>(NOW()- INTERVAL ".$daysmostpopular." DAY) ";
	  $query=$query." GROUP BY sender_id, lang_key ".$groupcond;
    $query=$query." UNION ";
    $query=$query."SELECT *,COUNT(*) as recordsfound FROM (SELECT * FROM bx_spy_data WHERE (".$friendfilter." (PostCommentsN + PostLikeN)>0 AND ".$conditions.") GROUP BY id ORDER BY id DESC) AS T1";
	  if ($soloup=="true") $query=$query." WHERE (id>$ultimoid) AND date>(NOW()- INTERVAL ".$daysmostpopular." DAY) ";
    else $query=$query." WHERE date>(NOW()- INTERVAL ".$daysmostpopular." DAY) ";
	  $query=$query." GROUP BY sender_id, lang_key ".$groupcond;
   }
   else {$query="SELECT * FROM bx_spy_data WHERE (".$friendfilter.$conditions." AND date>(NOW()- INTERVAL ".$daysmostpopular." DAY))";} //MEMBER (Most popular without grouping)
   $query=$query." ORDER BY (PostCommentsN + PostLikeN) DESC,id DESC LIMIT $parami,$cont";
  }
 }
 else 
 { //QUERY PROFILO
  include('mqconditions2.php');
  if ($grouping=='on')
  {
   $query="SELECT *,COUNT(*) recordsfound FROM (SELECT * FROM bx_spy_data WHERE ((sender_id=".$profileid." OR recipient_id=".$profileid.") AND (PostCommentsN + PostLikeN)=0 AND ".$conditions.") GROUP BY id ORDER BY id DESC) AS T1";
   if ($soloup=="true") $query=$query." WHERE (id>$ultimoid)";
   $query=$query." GROUP BY sender_id, lang_key ".$groupcond;  
   $query=$query." UNION ";
   $query=$query."SELECT *,COUNT(*) recordsfound FROM (SELECT * FROM bx_spy_data WHERE ((sender_id=".$profileid." OR recipient_id=".$profileid.") AND (PostCommentsN + PostLikeN)>0 AND ".$conditions.") GROUP BY id ORDER BY id DESC) AS T1";
   if ($soloup=="true") $query=$query." WHERE (id>$ultimoid)";
   $query=$query." GROUP BY sender_id, lang_key ".$groupcond;
  }
  else {$query="SELECT * FROM bx_spy_data WHERE ((sender_id=".$profileid." OR recipient_id=".$profileid.") AND ".$conditions.")";}
  $query=$query." ORDER BY id DESC LIMIT $parami,$cont";
 }
}
?>