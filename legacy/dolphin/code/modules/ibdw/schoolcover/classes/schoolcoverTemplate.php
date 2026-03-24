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
bx_import('BxDolModuleTemplate');
class schoolcoverTemplate extends BxDolModuleTemplate 
{
 /*
 Class constructor
 */
 
 function schoolcoverTemplate(&$oConfig, &$oDb) 
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
            $_page['name_index'] = 9; 
            $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
            $_page['header_text'] = $sTitle;
            $_page_cont[$_page['name_index']]['page_main_code'] = ob_get_clean();
            PageCodeAdmin();
        }
}
?>