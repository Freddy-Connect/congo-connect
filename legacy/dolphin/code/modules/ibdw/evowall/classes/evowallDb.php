<?php
/**********************************************************************************
*                            IBDW EvoWall for Dolphin Smart Community Builder
*                              -------------------
*     begin                : July 23 2012
*     copyright            : (C) 2012 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW EvoWall is not free and you cannot redistribute and/or modify it.
* 
* IBDW EvoWall is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php' );
class evowallDb extends BxDolModuleDb 
{
 var $_oConfig;
 var $sTablePrefix;
 
 /*Constructor*/
 
 function evowallDb(&$oConfig) 
 {
  parent::BxDolModuleDb();
  $this -> _oConfig = $oConfig;
  $this -> sTablePrefix = $oConfig -> getDbPrefix();
 }

 function getSettingsCategory() 
 {
  return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'EVO Wall' LIMIT 1");
 }
 
 function removeInexComments()
 {
  $this -> query("DELETE FROM `commenti_spy_data` WHERE `data` NOT IN (SELECT ID FROM `bx_spy_data`) OR `user` NOT IN (SELECT ID FROM `Profiles`)");
  $this -> query("OPTIMIZE TABLE `commenti_spy_data`");	   
  $this -> query("DELETE FROM `datacommenti` WHERE `IDCommento` NOT IN (SELECT id FROM `commenti_spy_data`)");
  $this -> query("OPTIMIZE TABLE `datacommenti`");
  $this -> query("DELETE FROM `ibdw_likethis` WHERE `id_utente` NOT IN (SELECT ID FROM `Profiles`) OR `id_utente`=''");
 }		
 
 function updateProfilePrivacy()
 {  
  $getprofileprivacydefault=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='DefaultProfilePrivacy'");
  $profileprivacyval=mysql_fetch_assoc($getprofileprivacydefault);
  if ($profileprivacyval['VALUE']=="Default") $privacyfornewprofile=1;
  elseif ($profileprivacyval['VALUE']=="Me Only") $privacyfornewprofile=2;
  elseif ($profileprivacyval['VALUE']=="Public") $privacyfornewprofile=3;
  elseif ($profileprivacyval['VALUE']=="Members") $privacyfornewprofile=4;
  elseif ($profileprivacyval['VALUE']=="Friends") $privacyfornewprofile=5;
  elseif ($profileprivacyval['VALUE']=="Faves") $privacyfornewprofile=6; 
  mysql_query("UPDATE sys_privacy_actions SET default_group = ".$privacyfornewprofile." WHERE sys_privacy_actions.module_uri ='evowall'");
 }
 
 function updateSocialNetworks()
 {
  //SOCIAL NETWORKS SHARING - get if social networks sharing is enabled
  $getsetvalsn=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowSocial' and order_in_kateg=61");
  $getvaluesns=mysql_fetch_assoc($getsetvalsn);
  if ($getvaluesns['VALUE']=="on")
  {
   //FACEBOOK SHARING - get if facebook sharing is enabled
   $getsetvalfb=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowFacebook' and order_in_kateg=62");
   $getvaluefbs=mysql_fetch_assoc($getsetvalfb);
   if ($getvaluefbs['VALUE']=="on")
   {
    $getfball=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowfbshare'");
 	  $getiffbpres=mysql_num_rows($getfball);
 	  if ($getiffbpres==0) $addfboption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowfbshare', '_ibdw_evowall_facebook_share_p', '4');");
   }
   else
   {
    $getfball=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowfbshare'");
	  $getiffbpres=mysql_num_rows($getfball);
	  if ($getiffbpres==1) $addfboption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowfbshare' AND title='_ibdw_evowall_facebook_share_p');");
   }
   //END
      
   //GOOGLE+ SHARING - get if google sharing is enabled
   $getsetvalgg=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowGoogle' and order_in_kateg=64");
   $getvalueggs=mysql_fetch_assoc($getsetvalgg);
   if ($getvalueggs['VALUE']=="on")
   {
    $getggall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowgoogleshare'");
	  $getifggpres=mysql_num_rows($getggall);
	  if ($getifggpres==0) $addggoption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowgoogleshare', '_ibdw_evowall_google_share_p', '4');");
   }
   else
   {
    $getggall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowgoogleshare'");
	  $getifggpres=mysql_num_rows($getggall);
	  if ($getifggpres==1) $addggoption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowgoogleshare' AND title='_ibdw_evowall_google_share_p');");
   }
   //END
   
   //TWITTER SHARING - get if twitter sharing is enabled
   $getsetvaltw=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowTwitter' and order_in_kateg=65");
   $getvaluetws=mysql_fetch_assoc($getsetvaltw);
   if ($getvaluetws['VALUE']=="on")
   {
    $gettwall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowtwshare'");
	  $getiftwpres=mysql_num_rows($gettwall);
	  if ($getiftwpres==0) $addtwoption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowtwshare', '_ibdw_evowall_twitter_share_p', '4');");
   }
   else
   {
    $gettwall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowtwshare'");
	  $getiftwpres=mysql_num_rows($gettwall);
	  if ($getiftwpres==1) $addtwoption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowtwshare' AND title='_ibdw_evowall_twitter_share_p');");
   }
   //END
   
   //LINKEDIN SHARING- get if linkedin sharing is enabled
   $getsetvalli=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowLinkedIn' and order_in_kateg=66");
   $getvaluelis=mysql_fetch_assoc($getsetvalli);
   if ($getvaluelis['VALUE']=="on")
   {
    $getliall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowlinkedinshare'");
	  $getiflipres=mysql_num_rows($getliall);
	  if ($getiflipres==0) $addlioption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowlinkedinshare', '_ibdw_evowall_linkedin_share_p', '4');");
   }
   else
   {
    $getliall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowlinkedinshare'");
	  $getiflipres=mysql_num_rows($getliall);
	  if ($getiflipres==1) $addlioption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowlinkedinshare' AND title='_ibdw_evowall_linkedin_share_p');");
   }
   //END
    
   //PINTEREST SHARING - get if pinterest sharing is enabled
   $getsetvalps=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowPinterest' and order_in_kateg=67");
   $getvaluepss=mysql_fetch_assoc($getsetvalps);
   if ($getvaluepss['VALUE']=="on")
   {
    $getpsall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowpinterestshare'");
	  $getifpspres=mysql_num_rows($getpsall);
	  if ($getifpspres==0) $addpsoption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowpinterestshare', '_ibdw_evowall_pinterest_share_p', '4');");
   }
   else
   {
    $getpsall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowpinterestshare'");
	  $getifpspres=mysql_num_rows($getpsall);
	  if ($getifpspres==1) $addpsoption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowpinterestshare' AND title='_ibdw_evowall_pinterest_share_p');");
   }
   //END
 
   //BAIDU SHARING - get if baidu sharing is enabled
   $getsetvalbi=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowBaidu' and order_in_kateg=68");
   $getvaluebis=mysql_fetch_assoc($getsetvalbi);
   if ($getvaluebis['VALUE']=="on")
   {
    $getbiall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowbaidushare'");
	  $getifbipres=mysql_num_rows($getbiall);
	  if ($getifbipres==0) $addbioption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowbaidushare', '_ibdw_evowall_baidu_share_p', '4');");
   }
   else
   {
    $getbiall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowbaidushare'");
	  $getifbipres=mysql_num_rows($getbiall);
	  if ($getifbipres==1) $addbioption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowbaidushare' AND title='_ibdw_evowall_baidu_share_p');");
   }
   //END
   
   //WEIBO SHARING - get if weibo sharing is enabled
   $getsetvalwb=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowWeibo' and order_in_kateg=69");
   $getvaluewbs=mysql_fetch_assoc($getsetvalwb);
   if ($getvaluewbs['VALUE']=="on")
   {
    $getwball=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowweiboshare'");
	  $getifwbpres=mysql_num_rows($getwball);
	  if ($getifwbpres==0) $addwboption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowweiboshare', '_ibdw_evowall_weibo_share_p', '4');");
   }
   else
   {
    $getwball=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowweiboshare'");
	  $getifwbpres=mysql_num_rows($getwball);
	  if ($getifwbpres==1) $addwboption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowweiboshare' AND title='_ibdw_evowall_weibo_share_p');");
   }
   //END
   
   //QZONE SHARING - get if qzone sharing is enabled
   $getsetvalqz=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowQzone' and order_in_kateg=70");
   $getvalueqzs=mysql_fetch_assoc($getsetvalqz);
   if ($getvalueqzs['VALUE']=="on")
   {
    $getqzall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowqzoneshare'");
	  $getifqzpres=mysql_num_rows($getqzall);
	  if ($getifqzpres==0) $addqzoption=mysql_query("INSERT INTO sys_privacy_actions (module_uri, name, title, default_group) VALUES ('evowall', 'allowqzoneshare', '_ibdw_evowall_qzone_share_p', '4');");
   }
   else
   {
    $getqzall=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowqzoneshare'");
	  $getifqzpres=mysql_num_rows($getqzall);
	  if ($getifqzpres==1) $addqzoption=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowqzoneshare' AND title='_ibdw_evowall_qzone_share_p');");
   }
   //END  
  }
  else
  {
   //FACEBOOK SHARING REMOVING
   $queryval=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowFacebook' and order_in_kateg=62");
   $runqueryval=mysql_fetch_assoc($queryval);
   //force this field to off (disabled this social because all social networks are disabled)
   if ($runqueryval['VALUE']=="on") $updateval=mysql_query("UPDATE sys_options SET sys_options.VALUE='' WHERE name='AllowFacebook' and order_in_kateg=62");
   //remove the record from the privacy of all members
   $getidval=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowfbshare'");
   $getidis=mysql_num_rows($getidval);
   if ($getidis==1) $removings=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowfbshare' AND title='_ibdw_evowall_facebook_share_p');");
     
   //GOOGLE+ SHARING REMOVING
   $queryval=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowGoogle' and order_in_kateg=64");
   $runqueryval=mysql_fetch_assoc($queryval);
   //force this field to off (disabled this social because all social networks are disabled)
   if ($runqueryval['VALUE']=="on") $updateval=mysql_query("UPDATE sys_options SET sys_options.VALUE='' WHERE name='AllowGoogle' and order_in_kateg=64");
   //remove the record from the privacy of all members
   $getidval=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowgoogleshare'");
   $getidis=mysql_num_rows($getidval);
   if ($getidis==1) $removings=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowgoogleshare' AND title='_ibdw_evowall_google_share_p');");
   
   //TWITTER SHARING REMOVING
   $queryval=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowTwitter' and order_in_kateg=65");
   $runqueryval=mysql_fetch_assoc($queryval);
   //force this field to off (disabled this social because all social networks are disabled)
   if ($runqueryval['VALUE']=="on") $updateval=mysql_query("UPDATE sys_options SET sys_options.VALUE='' WHERE name='AllowTwitter' and order_in_kateg=65");
   //remove the record from the privacy of all members
   $getidval=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowtwshare'");
   $getidis=mysql_num_rows($getidval);
   if ($getidis==1) $removings=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowtwshare' AND title='_ibdw_evowall_twitter_share_p');");
    
   //LINKEDIN SHARING REMOVING
   $queryval=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowLinkedIn' and order_in_kateg=66");
   $runqueryval=mysql_fetch_assoc($queryval);
   //force this field to off (disabled this social because all social networks are disabled)
   if ($runqueryval['VALUE']=="on") $updateval=mysql_query("UPDATE sys_options SET sys_options.VALUE='' WHERE name='AllowLinkedIn' and order_in_kateg=66");
   //remove the record from the privacy of all members
   $getidval=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowlinkedinshare'");
   $getidis=mysql_num_rows($getidval);
   if ($getidis==1) $removings=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowlinkedinshare' AND title='_ibdw_evowall_linkedin_share_p');");
     
   //PINTEREST SHARING REMOVING
   $queryval=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowPinterest' and order_in_kateg=67");
   $runqueryval=mysql_fetch_assoc($queryval);
   //force this field to off (disabled this social because all social networks are disabled)
   if ($runqueryval['VALUE']=="on") $updateval=mysql_query("UPDATE sys_options SET sys_options.VALUE='' WHERE name='AllowPinterest' and order_in_kateg=67");
   //remove the record from the privacy of all members
   $getidval=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowpinterestshare'");
   $getidis=mysql_num_rows($getidval);
   if ($getidis==1) $removings=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowpinterestshare' AND title='_ibdw_evowall_pinterest_share_p');");
    
   //BAIDU SHARING REMOVING
   $queryval=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowBaidu' and order_in_kateg=68");
   $runqueryval=mysql_fetch_assoc($queryval);
   //force this field to off (disabled this social because all social networks are disabled)
   if ($runqueryval['VALUE']=="on") $updateval=mysql_query("UPDATE sys_options SET sys_options.VALUE='' WHERE name='AllowBaidu' and order_in_kateg=68");
   //remove the record from the privacy of all members
   $getidval=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowbaidushare'");
   $getidis=mysql_num_rows($getidval);
   if ($getidis==1) $removings=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowbaidushare' AND title='_ibdw_evowall_baidu_share_p');");
   
   //WEIBO SHARING REMOVING
   $queryval=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowWeibo' and order_in_kateg=69");
   $runqueryval=mysql_fetch_assoc($queryval);
   //force this field to off (disabled this social because all social networks are disabled)
   if ($runqueryval['VALUE']=="on") $updateval=mysql_query("UPDATE sys_options SET sys_options.VALUE='' WHERE name='AllowWeibo' and order_in_kateg=69");
   //remove the record from the privacy of all members
   $getidval=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowweiboshare'");
   $getidis=mysql_num_rows($getidval);
   if ($getidis==1) $removings=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowweiboshare' AND title='_ibdw_evowall_weibo_share_p');");
    
   //QZONE SHARING REMOVING
   $queryval=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE name='AllowQzone' and order_in_kateg=70");
   $runqueryval=mysql_fetch_assoc($queryval);
   //force this field to off (disabled this social because all social networks are disabled)
   if ($runqueryval['VALUE']=="on") $updateval=mysql_query("UPDATE sys_options SET sys_options.VALUE='' WHERE name='AllowQzone' and order_in_kateg=70");
   //remove the record from the privacy of all members
   $getidval=mysql_query("SELECT id FROM sys_privacy_actions WHERE module_uri='evowall' and name='allowqzoneshare'");
   $getidis=mysql_num_rows($getidval);
   if ($getidis==1) $removings=mysql_query("DELETE FROM sys_privacy_actions WHERE (module_uri='evowall' AND name='allowqzoneshare' AND title='_ibdw_evowall_qzone_share_p');");
  } 
 }
 function updateEvoEmailTemplate()
 {
  //PHOTO:check if the module is installed, if yes the email template will be installed otherwise removed
  $modulestatusis=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='AllowPhotos' and order_in_kateg=6");
  $getstatus=mysql_fetch_assoc($modulestatusis);
  if ($getstatus['VALUE']=="on") 
  {
   $runinstalltemplate=mysql_query("INSERT IGNORE INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
   ('_ibdw_evowall_bx_photo_add_condivisione', '<SenderNickName> shared your Photo', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Photo<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Photo Post Shared', 0),
   ('_ibdw_evowall_notify_comment_photo', '<SenderNickName> commented your photo', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your photo<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Photo Post Commented', 0),
   ('_ibdw_evowall_notify_like_photo', '<SenderNickName> Likes your photo', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your photo<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Photo Post Like', 0);");
  }
  else $removingmail=mysql_query("DELETE IGNORE FROM `sys_email_templates` WHERE (`Name`='_ibdw_evowall_bx_photo_add_condivisione' OR `Name`='_ibdw_evowall_notify_comment_photo' OR `Name`='_ibdw_evowall_notify_like_photo');");
  
  //VIDEO:check if the module is installed, if yes the email template will be installed otherwise removed
  $modulestatusis=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='AllowVideos' and order_in_kateg=7");
  $getstatus=mysql_fetch_assoc($modulestatusis);
  if ($getstatus['VALUE']=="on") 
  {
   $runinstalltemplate=mysql_query("INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
   ('_ibdw_evowall_bx_video_add_condivisione', '<SenderNickName> shared your Video', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Video<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Video Post Shared', 0),
   ('_ibdw_evowall_notify_comment_video', '<SenderNickName> commented your video', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your video<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Video Post Commented', 0),
   ('_ibdw_evowall_notify_like_video', '<SenderNickName> Likes your video', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your video<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Video Post Like', 0);");
  }
  else $removingmail=mysql_query("DELETE IGNORE FROM `sys_email_templates` WHERE (`Name`='_ibdw_evowall_bx_video_add_condivisione' OR `Name`='_ibdw_evowall_notify_comment_video' OR `Name`='_ibdw_evowall_notify_like_video');");
  
  //GROUPS:check if the module is installed, if yes the email template will be installed otherwise removed
  $modulestatusis=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='AllowGroups' and order_in_kateg=8");
  $getstatus=mysql_fetch_assoc($modulestatusis);
  if ($getstatus['VALUE']=="on") 
  {
   $runinstalltemplate=mysql_query("INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
   ('_ibdw_evowall_bx_gruppo_add_condivisione', '<SenderNickName> shared your Group', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Group<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Group Post Shared', 0),
   ('_ibdw_evowall_notify_comment_groups', '<SenderNickName> commented your group', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your group<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Group Post Commented', 0),
   ('_ibdw_evowall_notify_like_groups', '<SenderNickName> Likes your group', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your group<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Group Post Like', 0);");
  }
  else $removingmail=mysql_query("DELETE IGNORE FROM `sys_email_templates` WHERE (`Name`='_ibdw_evowall_bx_gruppo_add_condivisione' OR `Name`='_ibdw_evowall_notify_comment_groups' OR `Name`='_ibdw_evowall_notify_like_groups');");
  
  //PAGES:check if the module is installed, if yes the email template will be installed otherwise removed
  $modulestatusis=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='AllowPages' and order_in_kateg=9");
  $getstatus=mysql_fetch_assoc($modulestatusis);
  if ($getstatus['VALUE']=="on") 
  {
   $runinstalltemplate=mysql_query("INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
   ('_ibdw_evowall_bx_pagina_add_condivisione', '<SenderNickName> shared your Page', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Page<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Page Post Shared', 0),
   ('_ibdw_evowall_notify_comment_pages', '<SenderNickName> commented your page', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your page<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Page Post Commented', 0),
   ('_ibdw_evowall_notify_like_pages', '<SenderNickName> Likes your page', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your page<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Page Post Like', 0);");
  }
  else $removingmail=mysql_query("DELETE IGNORE FROM `sys_email_templates` WHERE (`Name`='_ibdw_evowall_bx_pagina_add_condivisione' OR `Name`='_ibdw_evowall_notify_comment_pages' OR `Name`='_ibdw_evowall_notify_like_pages');");
    
  //EVENTS:check if the module is installed, if yes the email template will be installed otherwise removed
  $modulestatusis=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='AllowEvents' and order_in_kateg=10");
  $getstatus=mysql_fetch_assoc($modulestatusis);
  if ($getstatus['VALUE']=="on") 
  {
   $runinstalltemplate=mysql_query("INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
   ('_ibdw_evowall_bx_event_add_condivisione', '<SenderNickName> shared your Event', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Event<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Event Post Shared', 0),
   ('_ibdw_evowall_notify_comment_events', '<SenderNickName> commented your event', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your event<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Event Post Commented', 0),
   ('_ibdw_evowall_notify_like_events', '<SenderNickName> Likes your event', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your event<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Event Post Like', 0);");
  }
  else $removingmail=mysql_query("DELETE IGNORE FROM `sys_email_templates` WHERE (`Name`='_ibdw_evowall_bx_event_add_condivisione' OR `Name`='_ibdw_evowall_notify_comment_events' OR `Name`='_ibdw_evowall_notify_like_events');");
  
  //SITES:check if the module is installed, if yes the email template will be installed otherwise removed
  $modulestatusis=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='AllowSites' and order_in_kateg=12");
  $getstatus=mysql_fetch_assoc($modulestatusis);
  if ($getstatus['VALUE']=="on") 
  {
   $runinstalltemplate=mysql_query("INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
   ('_ibdw_evowall_bx_site_add_condivisione', '<SenderNickName> shared your Site', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Site<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Site Post Shared', 0),
   ('_ibdw_evowall_notify_comment_sites', '<SenderNickName> commented your site', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your site<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Site Post Commented', 0),
   ('_ibdw_evowall_notify_like_sites', '<SenderNickName> Likes your site', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your site<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Site Post Like', 0);");
  }
  else $removingmail=mysql_query("DELETE IGNORE FROM `sys_email_templates` WHERE (`Name`='_ibdw_evowall_bx_site_add_condivisione' OR `Name`='_ibdw_evowall_notify_comment_sites' OR `Name`='_ibdw_evowall_notify_like_sites');");
  
  //POLLS:check if the module is installed, if yes the email template will be installed otherwise removed
  $modulestatusis=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='AllowPolls' and order_in_kateg=13");
  $getstatus=mysql_fetch_assoc($modulestatusis);
  $moduletypeis=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='PollsMod' and order_in_kateg=14");
  $gettype=mysql_fetch_assoc($moduletypeis);
  
  if ($getstatus['VALUE']=="on" and $gettype['VALUE']="Boonex") 
  {
   //(Boonex)
   $runinstalltemplate=mysql_query("INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
   ('_ibdw_evowall_bx_poll_add_condivisione', '<SenderNickName> shared your Poll', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Poll<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Poll Post Shared', 0),
   ('_ibdw_evowall_notify_comment_poll', '<SenderNickName> commented your poll', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your poll<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Poll Post Commented', 0),
   ('_ibdw_evowall_notify_like_poll', '<SenderNickName> Likes your poll', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your poll<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Poll Post Like', 0);");
  }
  elseif($getstatus['VALUE']=="on" and $gettype['VALUE']="Modzzz")
  {
   //(Modzzz)
   $runinstalltemplate=mysql_query("INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
   ('_ibdw_evowall_modzzz_poll_add_condivisione', '<SenderNickName> shared your Poll', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Poll<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Poll Post Shared', 0),
   ('_ibdw_evowall_modzzz_notify_comment_poll', '<SenderNickName> commented your poll', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your poll<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Poll Post Commented', 0),
   ('_ibdw_evowall_modzzz_notify_like_poll', '<SenderNickName> Likes your poll', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your poll<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Poll Post Like', 0);");
  }
  else $removingmail=mysql_query("DELETE IGNORE FROM `sys_email_templates` WHERE (`Name`='_ibdw_evowall_bx_poll_add_condivisione' OR `Name`='_ibdw_evowall_notify_comment_poll' OR `Name`='_ibdw_evowall_notify_like_poll' OR `Name`='_ibdw_evowall_modzzz_poll_add_condivisione' OR `Name`='_ibdw_evowall_modzzz_notify_comment_poll' OR `Name`='_ibdw_evowall_modzzz_notify_like_poll');");
  
  //ADS:check if the module is installed, if yes the email template will be installed otherwise removed
  $modulestatusis=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='AllowAds' and order_in_kateg=15");
  $getstatus=mysql_fetch_assoc($modulestatusis);
  if ($getstatus['VALUE']=="on") 
  {
   $runinstalltemplate=mysql_query("INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
   ('_ibdw_evowall_bx_ads_add_condivisione', '<SenderNickName> shared your Ads', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Ad<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Ad Post Shared', 0),
   ('_ibdw_evowall_notify_comment_ads', '<SenderNickName> commented your ad', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your ad<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Ad Post Commented', 0),
   ('_ibdw_evowall_notify_like_ads', '<SenderNickName> Likes your ad', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your ad<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Ad Post Like', 0);");
  }
  else $removingmail=mysql_query("DELETE IGNORE FROM `sys_email_templates` WHERE (`Name`='_ibdw_evowall_bx_ads_add_condivisione' OR `Name`='_ibdw_evowall_notify_comment_ads' OR `Name`='_ibdw_evowall_notify_like_ads');");
  
  //BLOGS:check if the module is installed, if yes the email template will be installed otherwise removed
  $modulestatusis=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='AllowBlogs' and order_in_kateg=16");
  $getstatus=mysql_fetch_assoc($modulestatusis);
  if ($getstatus['VALUE']=="on") 
  {
   $runinstalltemplate=mysql_query("INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
   ('_ibdw_evowall_bx_blogs_add_condivisione', '<SenderNickName> shared your Blog', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Blog<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Blog Post Shared', 0),
   ('_ibdw_evowall_notify_comment_blogs', '<SenderNickName> commented your blog', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your blog<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Blog Post Commented', 0),
   ('_ibdw_evowall_notify_like_blogs', '<SenderNickName> Likes your blog', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your blog<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Blog Post Like', 0);");
  }
  else $removingmail=mysql_query("DELETE IGNORE FROM `sys_email_templates` WHERE (`Name`='_ibdw_evowall_bx_blogs_add_condivisione' OR `Name`='_ibdw_evowall_notify_comment_blogs' OR `Name`='_ibdw_evowall_notify_like_blogs');");
  
  //SOUNDS:check if the module is installed, if yes the email template will be installed otherwise removed
  $modulestatusis=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='AllowSounds' and order_in_kateg=17");
  $getstatus=mysql_fetch_assoc($modulestatusis);
  if ($getstatus['VALUE']=="on") 
  {
   $runinstalltemplate=mysql_query("INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
   ('_ibdw_evowall_bx_sounds_add_condivisione', '<SenderNickName> shared your Sound', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Sound<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Sound Post Shared', 0),
   ('_ibdw_evowall_notify_comment_sounds', '<SenderNickName> commented your sound', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your sound<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Sound Post Commented', 0),
   ('_ibdw_evowall_notify_like_sounds', '<SenderNickName> Likes your sound', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your sound<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Sound Post Like', 0);");
  }
  else $removingmail=mysql_query("DELETE IGNORE FROM `sys_email_templates` WHERE (`Name`='_ibdw_evowall_bx_sounds_add_condivisione' OR `Name`='_ibdw_evowall_notify_comment_sounds' OR `Name`='_ibdw_evowall_notify_like_sounds');");
  
  //URLS:check if the module is installed, if yes the email template will be installed otherwise removed
  $modulestatusis=mysql_query("SELECT sys_options.VALUE FROM sys_options WHERE Name='UrlPlugin' and order_in_kateg=52");
  $getstatus=mysql_fetch_assoc($modulestatusis);
  if ($getstatus['VALUE']=="on") 
  {
   $runinstalltemplate=mysql_query("INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
   ('_ibdw_evowall_bx_url_share', '<SenderNickName> shared your url', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Url<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: URL Post Shared', 0),
   ('_ibdw_evowall_notify_comment_url', '<SenderNickName> commented your url', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your url<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Url Post Commented', 0),
   ('_ibdw_evowall_notify_like_url', '<SenderNickName> Likes your url', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your url<br/> <br/> <a href=\"{post}\">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Url Post Like', 0);");
  }
  else $removingmail=mysql_query("DELETE IGNORE FROM `sys_email_templates` WHERE (`Name`='_ibdw_evowall_bx_url_share' OR `Name`='_ibdw_evowall_notify_comment_url' OR `Name`='_ibdw_evowall_notify_like_url');"); 
 }
}
?>