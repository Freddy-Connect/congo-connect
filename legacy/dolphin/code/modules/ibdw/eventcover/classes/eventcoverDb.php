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

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php' );

class eventcoverDb extends BxDolModuleDb 
{
 var $_oConfig;
 var $sTablePrefix;
 
 /*
 Constructor
 */
 
 function eventcoverDb(&$oConfig) 
 {
  parent::BxDolModuleDb();
  $this -> _oConfig = $oConfig;
  $this -> sTablePrefix = $oConfig -> getDbPrefix();
 }
 
 function getSettingsCategory() 
 {
  return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Event Cover' LIMIT 1");
 }
 function removeOldFiles()
 {//remove files uploaded more than 1 hour ago
  $folderName=BX_DIRECTORY_PATH_MODULES."ibdw/eventcover/temp/files";
  if (file_exists($folderName)) 
  {
   foreach (new DirectoryIterator($folderName) as $fileInfo) 
   {
    if ($fileInfo->isDot()) continue; 
    if ((time() - $fileInfo->getCTime() >= 1*1*60*60) and $fileInfo->getFilename()!=".htaccess" and $fileInfo->getFilename()!="thumbnail") unlink($fileInfo->getRealPath());
   } 
  }
  $thumbfolderName=BX_DIRECTORY_PATH_MODULES."ibdw/eventcover/temp/files/thumbnail";
  if (file_exists($thumbfolderName)) 
  {
   foreach (new DirectoryIterator($thumbfolderName) as $fileInfo) 
   {
    if ($fileInfo->isDot()) continue; 
    if ((time() - $fileInfo->getCTime() >= 1*1*60*60) and $fileInfo->getFilename()!=".htaccess") unlink($fileInfo->getRealPath());
   } 
  }
 }    
}
?>