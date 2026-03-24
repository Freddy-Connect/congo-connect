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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolConfig.php');
class eventcoverConfig extends BxDolConfig 
{
 var $iKeyCodeEV;
 var $iAlbumCoverNameEV;
 var $DisplaytitleEV;
 var $DisplayauthorEV;
 var $maxfilesizeEV;
 var $eventmoduleis;
 var $ixyfactorEV;
 
 function eventcoverConfig($aModule) 
 {
  parent::BxDolConfig($aModule);
  $this -> iKeyCodeEV = getParam('KeyCodeEV');
  $this -> iAlbumCoverNameEV = getParam('AlbumCoverNameEV');
  $this -> iDisplaytitleEV  = getParam('DisplaytitleEV') ? true : false;
  $this -> iDisplayauthorEV  = getParam('DisplayauthorEV') ? true : false;
  $this -> imaxfilesizeEV = getParam('maxfilesizeEV');
  $this -> ieventmoduleis = getParam('eventmoduleis');
  $this -> ixyfactorEV = getParam('xyfactorEV');
 }
}
?>