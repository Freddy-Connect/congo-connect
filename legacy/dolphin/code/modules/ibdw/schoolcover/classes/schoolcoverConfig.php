<?php
/**********************************************************************************
*                            IBDW School Cover for Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mar 18 2010
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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolConfig.php');
class schoolcoverConfig extends BxDolConfig 
{
 var $iKeyCodeSCH;
 var $iAlbumCoverNameSCH;
 var $DisplaytitleSCH;
 var $DisplayauthorSCH;
 var $maxfilesizeSCH;
 var $xyfactorSCH;
 
  
 function schoolcoverConfig($aModule) 
 {
  parent::BxDolConfig($aModule);
  $this -> iKeyCodeSCH = getParam('KeyCodeSCH');
  $this -> iAlbumCoverNameSCH = getParam('AlbumCoverNameSCH');
  $this -> iDisplaytitleSCH  = getParam('DisplaytitleSCH') ? true : false;
  $this -> iDisplayauthorSCH  = getParam('DisplayauthorSCH') ? true : false;
  $this -> imaxfilesizeSCH = getParam('maxfilesizeSCH');
  $this -> ixyfactorSCH = getParam('xyfactorSCH');
 }
}
?>