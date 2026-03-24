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

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolConfig.php');
class peopleyoumayknowConfig extends BxDolConfig 
{
 var $iKeyCodePYMK;
 var $ipymktemplate;
 var $iIBDWProfileCover;

 function peopleyoumayknowConfig($aModule) 
 {
  parent::BxDolConfig($aModule);
  $this -> iKeyCodePYMK = getParam('KeyCodePYMK');
	$this -> ipymktemplate   = getParam('pymktemplate');
  $this -> iminprofiletoload   = getParam('minprofiletoload');
  $this -> idisplaybefriend   = getParam('displaybefriend') ? true : false;
  $this -> idisplayfriends   = getParam('displayfriends') ? true : false;
  $this -> idisplayfmessage   = getParam('displayfmessage') ? true : false;
  $this -> idisplayfmessage   = getParam('displayfmessage') ? true : false;
  $this -> iIBDWProfileCover   = getParam('IBDWProfileCover') ? true : false;
 }
}
?>