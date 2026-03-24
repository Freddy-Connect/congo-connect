<?php
/**********************************************************************************
*                            IBDW 1Col Dolphin Smart Community Builder
*                              -------------------
*     begin                : May 1 2010
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW SpyWall is not free and you cannot redistribute and/or modify it.
* 
* IBDW SpyWall is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

$querydiconfigurazione = "SELECT * FROM `1col_config` LIMIT 0 , 1";
$risultato = mysql_query($querydiconfigurazione);
$riga = mysql_fetch_assoc($risultato);

//EMAIL: Show or hide the email address
$shemaila=$riga['emailad'];

//STATUS: Show or hide the member status
$status = $riga['status'];

//CITY: Show or hide the member city
$scity = $riga['city'];

//SISTEMA SLIDEDOWN PER ALTRE INFORMAZIONI --- IMPOSTA ON PER ATTIVARLO --- OFF PER DISATTIVARLO

$slideotherinfo = $riga['slide'];

//max number of online friends
$maxnumberonlinef=$riga['numbermaxfriend'];;

//Set the time refresh in milliseconds
$timereload=$riga['timereload'];



//To show/hide sections (value ON/OFF)
$mainmenuvar=$riga['mainmenuvar'];
$mediavar=$riga['mediavar'];
$accounteditvar=$riga['acceditvar'];
$sonlinefriends=$riga['onlinefriendvar'];

//To show/hide link to modules in main menu (value ON/OFF).
//IMPORTANT: if some boonex module is not installed, you must turn off the corrisponding variable. 
//Also even if you have installed a module but you dont want show the link into the menu, 
//you can turn off the variable that refers at this mod

$sitesvar=$riga['siti'];
$adsvar=$riga['annunci'];
$evntvar=$riga['eventi'];
$groupsvar=$riga['gruppi'];
$pagesvar=$riga['pagine'];
$pollsvar=$riga['sondaggi'];
$photosvar=$riga['foto'];
$videosvar=$riga['video'];
$filesvar=$riga['file'];
$soundvar=$riga['suoni'];
$blogvar=$riga['blog'];




//Turn off to not show the Delete Account button
$deleteaccount = $riga['deletebutton'];;


// Management dynamic URLs
/*
You can change the link addresses of the menu. This can be usefully when you have installed the custom module.
Example
$photourl = 'm/photos/albums/my/main/';   >>  $photourl = 'm/photospersonal/my/';
*/

$mailurl = $riga['mailurl'];          //MAIL URL
$groupurl = $riga['groupurl'];        // GROUPS URL
$addgroupurl = $riga['addgroupurl'];  // ADD GROUPS URL
$eventurl = $riga['eventurl'];        // EVENT URL
$addeventurl = $riga['addeventurl'];  // AD EVENT URL
$pollurl = $riga['pollurl'];          // POLL URL
$addpollurl = $riga['addpollurl'];    // ADD POLL URL
$adsurl = $riga['adsurl'];            // AD URL
$addadsurl = $riga['addadsurl'];      // ADD AD URL
$blogurl = $riga['blogurl'];           // BLOG URL
$addblogurl = $riga['addblogurl'];     // ADD BLOG URL
$siteurl = $riga['siteurl'];          // SITE URL
$fileurl = $riga['fileurl'];          // FILE URL
$pageurl = $riga['pageurl'];          // PAGE URL
$addfileurl = $riga['addfileurl'];    // ADD FILE URL
$addpageurl = $riga['addpageurl'];    // ADD PAGE URL
$addsiteurl = $riga['addsiteurl'];    // ADD SITE URL
$photourl = $riga['photourl'];        // PHOTO URL
$videosurl = $riga['videourl'];       // VIDEO URL
$soundurl = $riga['soundurl'];        // SOUND URL
$avatarurl = $riga['avatarurl'];      // AVATAR URL

$avaset = $riga['avaset'];
$privasett = $riga['privasett'];
$sottoscrizione  = $riga['sottoscrizione'];
$mailset = $riga['mailset'];
$amiciset = $riga['amiciset'];

if ($riga['customlink1']!='') { $cs1 = $riga['customlink1']; } else { $cs1 = '0'; }
if ($riga['customlink2']!='') { $cs2 = $riga['customlink2']; } else { $cs2 = '0'; }
if ($riga['customlink3']!='') { $cs3 = $riga['customlink3']; } else { $cs3 = '0'; }
if ($riga['customlink4']!='') { $cs4 = $riga['customlink4']; } else { $cs4 = '0'; }
if ($riga['customlink5']!='') { $cs5 = $riga['customlink5']; } else { $cs5 = '0'; }

if ($riga['customsect1']!='') { $customnamesect1 = $riga['customsect1']; } else { $customnamesect1 = '0'; }
if ($riga['customsect2']!='') { $customnamesect2 = $riga['customsect2']; } else { $customnamesect2 = '0'; }
if ($riga['customsect3']!='') { $customnamesect3 = $riga['customsect3']; } else { $customnamesect3 = '0'; }
if ($riga['customsect4']!='') { $customnamesect4 = $riga['customsect4']; } else { $customnamesect4 = '0'; }
if ($riga['customsect5']!='') { $customnamesect5 = $riga['customsect5']; } else { $customnamesect5 = '0'; }
?>