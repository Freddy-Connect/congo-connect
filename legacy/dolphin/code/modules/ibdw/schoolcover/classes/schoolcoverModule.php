<?php
/**********************************************************************************
*                            IBDW School Cover for Dolphin Smart Community Builder
*                              -------------------
*     begin                : October 11 2015
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW School Cover is not free and you cannot redistribute and/or modify it.
* 
* IBDW School Cover is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

bx_import('BxDolModuleDb');
bx_import('BxDolModule');
bx_import('BxDolInstallerUtils');

class schoolcoverModule extends BxDolModule 
{
 var $aModuleInfo;
 var $sPathToModule;
 var $sHomeUrl;
 
 function schoolcoverModule(&$aModule) 
 {        
  parent::BxDolModule($aModule);
  // prepare the location link ;
  $this -> sPathToModule  = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();
  $this -> aModuleInfo    = $aModule;
  $this -> sHomeUrl       = $this ->_oConfig -> _sHomeUrl;
  // Settings
  $this -> aCoreSettings = array (
   'KeyCodeSCH' 				=> $this -> _oConfig -> iKeyCodeSCH,
   'AlbumCoverNameSCH'  	=> $this -> _oConfig -> iAlbumCoverNameSCH,
   'DisplaytitleSCH'  	=> $this -> _oConfig -> iDisplaytitleSCH,
   'DisplayauthorSCH'  	=> $this -> _oConfig -> iDisplayauthorSCH,
   'maxfilesizeSCH'  	=> $this -> _oConfig -> imaxfilesizeSCH,
   'xyfactorSCH'  	=> $this -> _oConfig -> ixyfactorSCH,
   );
 }
 
function actionAdministration()
 {
  if( !isAdmin() ) {header('location: ' . BX_DOL_URL_ROOT);}
  // get sys_option's category id;
  $iCatId = $this-> _oDb -> getSettingsCategory();
  if(!$iCatId) {$sOptions = MsgBox( _t('_Empty') );}
  else 
  {
   bx_import('BxDolAdminSettings');
   $oSettings = new BxDolAdminSettings($iCatId);               
   $mixedResult = '';
   if(isset($_POST['save']) && isset($_POST['cat'])) {$mixedResult = $oSettings -> saveChanges($_POST);}
   // get option's form;
   $sOptions = $oSettings -> getForm();
   if($mixedResult !== true && !empty($mixedResult)) {$sOptions = $mixedResult . $sOptions;}
  }
  $this -> _oTemplate -> addCss('forms_adv.css');
  $this -> _oTemplate-> pageCodeAdminStart();
  echo DesignBoxAdmin( _t('_ibdw_schoolcover_informations')
                        , $GLOBALS['oSysTemplate'] -> parseHtmlByName('default_padding.html', array('content' => _t('_ibdw_schoolcover_information_block', BX_DOL_URL_ROOT))) );
  echo DesignBoxAdmin( _t('_Settings'), $GLOBALS['oSysTemplate'] -> parseHtmlByName('default_padding.html', array('content' => $sOptions) ));
  $this -> _oTemplate->pageCodeAdmin( 'School Cover' );
 }     
}
?>