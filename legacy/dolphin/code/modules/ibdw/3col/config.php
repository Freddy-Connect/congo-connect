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

$querydiconfigurazione = "SELECT * FROM 3col_config LIMIT 0 , 1";
$risultato = mysql_query($querydiconfigurazione);
$riga = mysql_fetch_assoc($risultato);

//Set date format. Choose "uni" for mm/gg/aaaa format, "eur" for gg/mm/yyyy.
$dateFormatC=$riga['dateFormatc'];

//Enable/Disable show friend requests and greetings (make "OFF" to disable)
$showfriendsreq=$riga['friendrequest'];

//Enable/Disable who's watching your profile (make "OFF" to disable)
$showminispy=$riga['watchprofile'];
$timeminispy=$riga['timeminispy'];

//Enable/Disable birthdates (make "OFF" to disable)
$showbirthdate=$riga['birthdate'];

//Enable/Disable events (make "OFF" to disable)
$showevents=$riga['events'];

//Enable/Disable suggestion profile (make "OFF" to disable)
$showsuggprof=$riga['suggprofile'];

//Enable/Disable more info (make "OFF" to disable)
$showmoreinfo=$riga['moreinfo'];;

//days range of birthdays.. default value 60 if you want to show the birthdays of 2 months
$maxdaybirthdays=$riga['dayrange'];

//The MySQL timezone is set to MST (-7 hours GMT/UTC) and is not configurable by you.
//Set here difference between MySQL timezone and local time 
$deltahours=$riga['timezone'];

//Set refresh time of summary block
$timereload=$riga['refresh'];

//Set max number of events to show at time
$numeventmax=$riga['maxnumberevent'];

//Set the max number of day to consider for upcoming events
$maxdaysevent=$riga['maxnumberconsider'];

//Max Number of friend request / day
$maxnumberoffriendrequests=$riga['maxfriendrequest'];

//Number profiles suggested at a time
$limitsuggest=$riga['maxnumonline'];

//sets the threshold of similarity and mutual friends for the suggestion of a profile (percentage)
//Note:you can decide the thresholds for suggest a friend: $trsugg is about similarity of profile whit account profile, 
//$trfriends is the mutual friends number min to suggest the profile, $conditionto allows to decide if are needed only one 
//condition (make "OR" in this case) or between ("AND" in this case)
//ATTENTION: if you decrease the tresholds or you active the OR condition you can obtain many profile suggestions but this increments the resources requests

$trsugg=$riga['trsugg'];
$trfriends=$riga['trfriends'];
$conditionto=$riga['conditionton'];

//Set default or other inviter
//If you have an other inviter set $defaultinviter="OFF" and  replace text YOUR-INVITER-PATH with path of your inviter
$defaultinviter=$riga['defaultinviter'];
$linktoinviter='<a class="titleinvita" href="'.$riga["linktoinviter"].'">';
?>