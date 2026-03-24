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
require_once( '../../../inc/header.inc.php' );
$owner=$_POST['owner'];
$hashis=$_POST['currenthash'];
$positionx=$_POST['PositionX'];
$positiony=$_POST['PositionY'];
$boxwidth=$_POST['boxwidth'];
$SchoolID=$_POST['SchoolID'];
$UriSelect="SELECT uri From modzzz_schools_main where id=".$SchoolID;
$getUri=mysql_query($UriSelect);
$UriRetrieve=mysql_fetch_assoc($getUri);
$Uri= $UriRetrieve['uri'];
$update = "UPDATE ibdw_school_cover SET PositionX=0, PositionY=".$positiony.",width=".$boxwidth." WHERE Owner=".$owner." AND Hash='".$hashis."' AND Uri='".$Uri."'";
$runquery = mysql_query($update);


//update position into the spy record
$recordis="SELECT id, params FROM bx_spy_data WHERE (lang_key='_ibdw_schoolcover_update' OR lang_key='_ibdw_schoolcover_update_male' OR lang_key='_ibdw_schoolcover_update_female') AND sender_id=".$owner." AND params LIKE '%".$hashis."%' ORDER BY id DESC LIMIT 1";
$runrecord=mysql_query($recordis);
$getif=mysql_num_rows($runrecord);
if ($getif>0)
{
 $resultis=mysql_fetch_assoc($runrecord);
 $recordid=$resultis['id'];
 $parameters=unserialize($resultis['params']);
 $parameters['position'] = $positiony;
 $parameters['width'] = $boxwidth;
 $newdata=serialize($parameters); 
 $update="UPDATE bx_spy_data SET params='".$newdata."' WHERE id=".$recordid;
 mysql_query($update); 
}
?>

