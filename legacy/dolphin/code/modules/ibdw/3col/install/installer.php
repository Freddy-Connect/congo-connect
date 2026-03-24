<?php
/**********************************************************************************
*                            IBDW 3Col Dolphin Smart Community Builder
*                              -------------------
*     begin                : May 1 2010
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW 3Col is not free and you cannot redistribute and/or modify it.
* 
* IBDW 3Col is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/
bx_import("BxDolInstaller");
class ThirdColInstaller extends BxDolInstaller 
{
 function ThirdColInstaller($aConfig) 
 {
  parent::BxDolInstaller($aConfig);
 }
 function install($aParams) 
 {
  $aResult = parent::install($aParams);
  
  $selezioneindice = "SHOW INDEX FROM `suggerimenti`";
  $eseguiselezione = mysql_query($selezioneindice);
  $indice=0;
  //VERIFICO SE ESISTE L'INDICE
  while ($righeindici=mysql_fetch_array($eseguiselezione))
  {
   if($righeindici['Key_name']=="mioID")
   {
	//NON DEVE ALTERARE L'INDICE DEL SENDER
	$indice=1;
   }
  } 
  if ($indice==0)
  {
   //NON ESISTE L'INDICE DEL SENDER
   $altertable = "CREATE INDEX mioID ON suggerimenti(mioID)";
   $eseguialter = mysql_query($altertable);
  }
  return $aResult;
 }
}
?>