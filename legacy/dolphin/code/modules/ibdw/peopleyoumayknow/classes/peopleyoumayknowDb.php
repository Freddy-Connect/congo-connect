<?php
/**********************************************************************************
*                            IBDW PeopleYouMayKnow Dolphin Smart Community Builder
*                              -------------------
*     begin                : June 5 2011
*     copyright            : (C) 2010 IlBelloDelWEB di Ferraro Raffaele
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW peopleyoumayknow is not free and you cannot redistribute and/or modify it.
* 
* IBDW peopleyoumayknow is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php' );

class peopleyoumayknowDb extends BxDolModuleDb 
{
 var $_oConfig;
 var $sTablePrefix;
 
 /*
 Constructor
 */
 
 function peopleyoumayknowDb(&$oConfig) 
 {
  parent::BxDolModuleDb();
  $this -> _oConfig = $oConfig;
  $this -> sTablePrefix = $oConfig -> getDbPrefix();
 }
 
 function getSettingsCategory() 
 {
  return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'peopleyoumayknow' LIMIT 1");
 }    
}
?>