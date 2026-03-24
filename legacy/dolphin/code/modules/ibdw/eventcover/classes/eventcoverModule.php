<?php
/**********************************************************************************
*                            IBDW Event Cover for Dolphin Smart Community Builder
*                              -------------------
*     begin                : Jan 20 2014
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW Event Cover is not free and you cannot redistribute and/or modify it.
* 
* IBDW Event Cover is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

bx_import('BxDolModuleDb');
bx_import('BxDolModule');
bx_import('BxDolInstallerUtils');

class eventcoverModule extends BxDolModule 
{
 var $aModuleInfo;
 var $sPathToModule;
 var $sHomeUrl;
 
 function eventcoverModule(&$aModule) 
 {        
  parent::BxDolModule($aModule);
  // prepare the location link ;
  $this -> sPathToModule  = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();
  $this -> aModuleInfo    = $aModule;
  $this -> sHomeUrl       = $this ->_oConfig -> _sHomeUrl;
  // Settings
  $this -> aCoreSettings = array (
   'KeyCodeEV' 				=> $this -> _oConfig -> iKeyCodeEV,
   'AlbumCoverNameEV'  	=> $this -> _oConfig -> iAlbumCoverNameEV,
   'DisplaytitleEV'  	=> $this -> _oConfig -> iDisplaytitleEV,
   'DisplayauthorEV'  	=> $this -> _oConfig -> iDisplayauthorEV,
   'maxfilesizeEV'  	=> $this -> _oConfig -> imaxfilesizeEV,
   'eventmoduleis'  	=> $this -> _oConfig -> ieventmoduleis,
   'xyfactorEV'  	=> $this -> _oConfig -> ixyfactorEV,
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
  echo DesignBoxAdmin( _t('_ibdw_eventcover_informations')
                        , $GLOBALS['oSysTemplate'] -> parseHtmlByName('default_padding.html', array('content' => _t('_ibdw_eventcover_information_block', BX_DOL_URL_ROOT))) );
  echo DesignBoxAdmin( _t('_Settings'), $GLOBALS['oSysTemplate'] -> parseHtmlByName('default_padding.html', array('content' => $sOptions) ));
  $this -> _oTemplate->pageCodeAdmin( 'Event Cover' );
 }     
}
?>