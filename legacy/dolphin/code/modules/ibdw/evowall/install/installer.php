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

require_once(BX_DIRECTORY_PATH_CLASSES . "BxDolInstaller.php");

class evowallInstaller extends BxDolInstaller 
{
 function evowallInstaller($aConfig) 
 {
  parent::BxDolInstaller($aConfig);
 }

 function install($aParams) 
 {
  $aResult = parent::install($aParams);

  //BX SPY DATA
  $selezioneindici = "SHOW INDEX FROM `bx_spy_data`";
  $eseguiselezione = mysql_query($selezioneindici);
  $indicedelsender=0;
  $indicedelrecipient=0;
  
  //DATACOMMENTI
  $seldatacommenti="SHOW INDEX FROM `datacommenti`";
  $eseguiseldc = mysql_query($seldatacommenti);
  $indiceidcommento=0;
  
  //VERIFICO L'ESISTENZA DELLE COLONNE AGGIUNTE DA EVO
  if (mysql_num_rows(mysql_query("SHOW COLUMNS FROM `bx_spy_data` LIKE 'PostCommentsN' "))==0) mysql_query("ALTER TABLE `bx_spy_data` ADD `PostCommentsN` MEDIUMINT NOT NULL");
  if (mysql_num_rows(mysql_query("SHOW COLUMNS FROM `bx_spy_data` LIKE 'PostLikeN' "))==0) mysql_query("ALTER TABLE `bx_spy_data` ADD `PostLikeN` MEDIUMINT NOT NULL");
  if (mysql_num_rows(mysql_query("SHOW COLUMNS FROM `bx_spy_data` LIKE 'CPostPrivacy' "))==0) mysql_query("ALTER TABLE `bx_spy_data` ADD `CPostPrivacy` TINYINT NOT NULL");

  //VERIFICO GLI INDICI DI DATACOMMENTI
  while ($righeindiciC=mysql_fetch_array($eseguiseldc))
  {
   if($righeindiciC['Key_name']=="IDCommento")
   {
    //NON DEVE ALTERARE L'INDICE
    $indiceC=1;
   }
   else $indiceC=0;
  } 
  if ($indiceC==0)
  {
   //NON ESISTE L'INDICE
   $altertableC = "ALTER TABLE `datacommenti` ADD INDEX `IDCommento` ( `IDCommento` ) ";
   $eseguialterC = mysql_query($altertableC);
  }

  //VERIFICO GLI INDICI DI BX SPYDATA
  while ($righeindici=mysql_fetch_array($eseguiselezione))
  {
   if($righeindici['Key_name']=="sender_id")
   {
    //NON DEVE ALTERARE L'INDICE DEL SENDER
    $indicedelsender=1;
   }
   elseif ($righeindici['Key_name'] == "recipient_id")	    
   {
    if ($righeindici['Column_name'] == "sender_id")
    {
     //NON DEVE ALTERARE L'INDICE DEL RECIPIENT
 	$indicedelrecipient=1;
    }
    else $indicedelrecipient=0;
   }
   else $indicedelrecipient=2;
  } 
  if ($indicedelsender==0)
  {
   //NON ESISTE L'INDICE DEL SENDER
   $altertable = "CREATE INDEX sender_id ON bx_spy_data(sender_id)";
   $eseguialter = mysql_query($altertable);
  }
  if ($indicedelrecipient==0)
  {
   //NON ESISTE IL CAMPO SENDER NELL'INDICE DEL RECIPIENT
   $altertable = "ALTER TABLE `bx_spy_data` DROP INDEX `recipient_id`, ADD INDEX `recipient_id` (`recipient_id`,`sender_id`)";
   $eseguialter = mysql_query($altertable);
  }
  if ($indicedelrecipient==2)
  {
   //NON ESISTE L'INDICE DEL RECIPIENT
   $altertable = "ALTER TABLE `bx_spy_data` ADD INDEX `recipient_id` (`recipient_id`,`sender_id`) ";
   $eseguialter = mysql_query($altertable);
  }
  return $aResult;
 }
 function uninstall($aParams) {
  return parent::uninstall($aParams);
 }
}
?>