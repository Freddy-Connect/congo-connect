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

bx_import('BxDolModuleDb');
bx_import('BxDolModule');
bx_import('BxDolInstallerUtils');

class evowallModule extends BxDolModule 
{
 var $aModuleInfo;
 var $sPathToModule;
 var $sHomeUrl;

 function evowallModule(&$aModule) 
 {        
  parent::BxDolModule($aModule);
  // prepare the location link ;
  $this -> sPathToModule  = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();
  $this -> aModuleInfo    = $aModule;
  $this -> sHomeUrl       = $this ->_oConfig -> _sHomeUrl;
  // Settings
  $this -> aCoreSettings = array (
   'LicenseKey' 				        => $this -> _oConfig -> iKeyCode,
   'DefaultProfilePrivacy'  	  => $this -> _oConfig -> iDefaultProfilePrivacy,
   'DefaultAccountPrivacy'  	  => $this -> _oConfig -> iDefaultAccountPrivacy,
   'DefaultHomePrivacy' 		    => $this -> _oConfig -> iDefaultHomePrivacy,
   'TemplateColor' 				      => $this -> _oConfig -> iTemplateColor,
   'AllowPhotos'       			    => $this -> _oConfig -> iAllowPhotos,
   'AllowVideos'       			    => $this -> _oConfig -> iAllowVideos,
   'AllowGroups'       		     	=> $this -> _oConfig -> iAllowGroups,
   'AllowPages'       			    => $this -> _oConfig -> iAllowGroups,
   'AllowEvents'       			    => $this -> _oConfig -> iAllowEvents,
   'EventMod'          			    => $this -> _oConfig -> iEventMod,
   'AllowSites'        			    => $this -> _oConfig -> iAllowSites,
   'AllowPolls'        			    => $this -> _oConfig -> iAllowPolls,
   'PollsMod'          			    => $this -> _oConfig -> iPollsMod,
   'AllowAds'          			    => $this -> _oConfig -> iAllowAds,
   'AllowBlogs'        			    => $this -> _oConfig -> iAllowBlogs,
   'AllowSounds'       			    => $this -> _oConfig -> iAllowSounds,
   'ModzzzProperty'    			    => $this -> _oConfig -> iModzzzProperty,
   'UE30Locations'     			    => $this -> _oConfig -> iUE30Locations,
   'AllowShareNotification'   	=> $this -> _oConfig -> iAllowShareNotification,
   'AllowLikeNotification'    	=> $this -> _oConfig -> iAllowLikeNotification,
   'AllowCommentNotification' 	=> $this -> _oConfig -> iAllowCommentNotification,
   'AllowMessageNotification' 	=> $this -> _oConfig -> iAllowMessageNotification,
   'TypeofOrder'	   			      => $this -> _oConfig -> iTypeofOrder,
   'DaysMostPopular'   			    => $this -> _oConfig -> iDaysMostPopular,
   'MessageLenght'     			    => $this -> _oConfig -> iMessageLenght,
   'CommentLenght'     			    => $this -> _oConfig -> iCommentLenght,
   'CommentPrevNum'    			    => $this -> _oConfig -> iCommentPrevNum,
   'CommentNum'        			    => $this -> _oConfig -> iCommentNum,
   'NewLine'           			    => $this -> _oConfig -> iNewLine,
   'DisplayProfileUpdate'  		  => $this -> _oConfig -> iDisplayProfileUpdate,
   'DisplayMessageStatusUpdate'	=> $this -> _oConfig -> iDisplayMessageStatusUpdate,
   'CommentOrder'      			    => $this -> _oConfig -> iCommentOrder,
   'WallDefaultPrivacy'			    => $this -> _oConfig -> iWallDefaultPrivacy,
   'VideoSizeWidth'    			    => $this -> _oConfig -> iVideoSizeWidth,
   'VideoSizeHeight'   			    => $this -> _oConfig -> iVideoSizeHeight,
   'DisplayNewsNumber' 			    => $this -> _oConfig -> iDisplayNewsNumber,
   'ProfileViewedBy'   		   	  => $this -> _oConfig -> iProfileViewedBy,
   'DenyAccessToUnconfirmed'   	=> $this -> _oConfig -> iDenyAccessToUnconfirmed,
   'RefreshType'   				      => $this -> _oConfig -> iRefreshType,
   'Refreshrate'   				      => $this -> _oConfig -> iRefreshrate,
   'AutoScroll'   				      => $this -> _oConfig -> iAutoScroll,
   'AutoScrollTime'   			    => $this -> _oConfig -> iAutoScrollTime,
   'HideMoreNewsButton'   		  => $this -> _oConfig -> iHideMoreNewsButton,
   'DateFormat'   				      => $this -> _oConfig -> iDateFormat,
   'Offset'   					        => $this -> _oConfig -> iOffset,
   'WelcomeMessage'   			    => $this -> _oConfig -> iWelcomeMessage,
   'WelcomeNPost'   			      => $this -> _oConfig -> iWelcomeNPost,
   'WelcomeCLife'   			      => $this -> _oConfig -> iWelcomeCLife,
   'UrlPlugin'   				        => $this -> _oConfig -> iUrlPlugin,
   'Grouping'  					        => $this -> _oConfig -> iGrouping,
   'TimingSimilar'   			      => $this -> _oConfig -> iTimingSimilar,
   'PhotoMaxPreview'   			    => $this -> _oConfig -> iPhotoMaxPreview,
   'PhotoRegularM'   			      => $this -> _oConfig -> iPhotoRegularM,
   'PhotoFlashM'   				      => $this -> _oConfig -> iPhotoFlashM,
   'PhotoOtherM'   				      => $this -> _oConfig -> iPhotoOtherM,
   'VideoYoutubeM'   			      => $this -> _oConfig -> iVideoYoutubeM,
   'VideoFalshM'   				      => $this -> _oConfig -> iVideoFalshM,
   'VideoOtherM'   				      => $this -> _oConfig -> iVideoOtherM,
   'DefaultPhotoM'   			      => $this -> _oConfig -> iDefaultPhotoM,
   'DefaultVideoM'   			      => $this -> _oConfig -> iDefaultVideoM,
   'AllowSocial'				        => $this -> _oConfig -> iAllowSocial,
   'AllowFacebook'   			      => $this -> _oConfig -> iAllowFacebook,
   'FacebookKID'   			        => $this -> _oConfig -> iFacebookKID,
   'AllowGoogle'   				      => $this -> _oConfig -> iAllowGoogle,
   'AllowTwitter'   			      => $this -> _oConfig -> iAllowTwitter,
   'AllowLinkedIn'   			      => $this -> _oConfig -> iAllowLinkedIn,
   'AllowPinterest'   			    => $this -> _oConfig -> iAllowPinterest,
   'AllowBaido'   				      => $this -> _oConfig -> iAllowBaido,
   'AllowWeibo'   				      => $this -> _oConfig -> iAllowWeibo,
   'AllowQzone'   				      => $this -> _oConfig -> iAllowQzone,
   'BadWordsFilter'   				  => $this -> _oConfig -> iBadWordsFilter,
   'BadWordsKeys'   				    => $this -> _oConfig -> iBadWordsKeys,
  );
 }

 function actionAdministration()
 {
  if(!isAdmin()) {header('location: '.BX_DOL_URL_ROOT);}
  // get sys_option's category id;
  $iCatId=$this-> _oDb -> getSettingsCategory();
  if(!$iCatId) $sOptions=MsgBox(_t('_Empty'));
  else 
  { 
   bx_import('BxDolAdminSettings');
   $oSettings=new BxDolAdminSettings($iCatId);               
   $mixedResult='';
   if(isset($_POST['save']) && isset($_POST['cat'])) {$mixedResult = $oSettings -> saveChanges($_POST);}
   // get option's form;
   $sOptions=$oSettings -> getForm();
   if($mixedResult!==true && !empty($mixedResult)) $sOptions=$mixedResult.$sOptions;
   $this-> _oDb -> updateProfilePrivacy();
   $this-> _oDb -> updateSocialNetworks();
   $this-> _oDb -> updateEvoEmailTemplate();
  }
  $this -> _oTemplate -> addCss('forms_adv.css');
  $this -> _oTemplate-> pageCodeAdminStart();
  echo DesignBoxAdmin(_t('_ibdw_evowall_informations'), $GLOBALS['oSysTemplate'] -> parseHtmlByName('default_padding.html', array('content' => _t('_ibdw_evowall_information_block', BX_DOL_URL_ROOT))));
  echo DesignBoxAdmin(_t('_Settings'), $GLOBALS['oSysTemplate'] -> parseHtmlByName('default_padding.html', array('content' => $sOptions)));
  $this -> _oTemplate->pageCodeAdmin('EVO Wall');
 } 
}
?>