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

bx_import('BxDolModuleTemplate');
class evowallTemplate extends BxDolModuleTemplate 
{
 /*Class constructor*/
 function evowallTemplate(&$oConfig, &$oDb) 
 {
  parent::BxDolModuleTemplate($oConfig, $oDb); 
 }
 
 function pageCodeAdminStart()
 {
  ob_start();
 }
 
 function pageCodeAdmin ($sTitle) 
 {
  global $_page;        
  global $_page_cont;
  $_page['name_index']=9; 
  $_page['header']=$sTitle ? $sTitle : $GLOBALS['site']['title'];
  $_page['header_text']=$sTitle;
  $_page_cont[$_page['name_index']]['page_main_code']=ob_get_clean();
  PageCodeAdmin();
 }
}
?>