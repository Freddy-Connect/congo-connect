<script type="text/javascript" src="js/jmini.js" /></script>
<?php
require_once( '../../../inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
include BX_DIRECTORY_PATH_MODULES.'ibdw/3col/config.php';

$userid = (int)$_COOKIE['memberID'];
if(!isAdmin()) { exit;}
mysql_query("SET NAMES 'utf8'");
$configquery= "SELECT * FROM `3col_config` LIMIT 0 , 1";
$resultac = mysql_query($configquery);
$rowconfig = mysql_fetch_assoc($resultac);  
?>
<html>
<head>
<?php echo '<link href="'.BX_DOL_URL_MODULES.'ibdw/3col/templates/base/css/admin.css" rel="stylesheet" type="text/css" />'; ?>

</head>
<body>
<div id="pagina">
 <div id="boxgeneraleconfigurazione">
 <div id="return"><a href="../../../<?php echo $admin_dir;?>">Return to Administration</a></div>
 
 <div class="semicolumn">
  <div id="contentbox">
   <span class="introtitle">Contents Box</span><br/> 
   <span class="introdesc">Enable/Disable the bottons of the modules installed not installed</span>
   <div class="spazio"></div>
	<form action="updateconfig.php" method="POST">
	 <div class="contentcon">
	  <div class="unsesto1">Show the friend requests</div><div class="unquarto1"><input type="radio" name="friendrequest" value="ON" <?php if($rowconfig['friendrequest']=='ON') {echo 'checked';}?> />ON <input type="radio" name="friendrequest" value="OFF" <?php if($rowconfig['friendrequest']=='OFF') {echo 'checked';}?>/>OFF</div>
	 </div>
   <div class="contentcon">
    <div class="unsesto1">Show who watching your profile</div>
	 <div class="unquarto1"><input type="radio" name="watchprofile" value="ON" <?php if($rowconfig['watchprofile']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="watchprofile" value="OFF" <?php if($rowconfig['watchprofile']=='OFF') {echo 'checked';}?>/>OFF - <input type="text" name="timeminispy" value="<?php echo $rowconfig['timeminispy'];?>" size="3"/> Sec. ago</div>
    </div>
	 <div class="contentcon">
	  <div class="unsesto1">Show birthdates</div><div class="unquarto1"><input type="radio" name="birthdate" value="ON" <?php if($rowconfig['birthdate']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="birthdate" value="OFF" <?php if($rowconfig['birthdate']=='OFF') {echo 'checked';}?>/>OFF</div>
	  </div>
	  <div class="contentcon">
    <div class="unsesto1">Show events</div><div class="unquarto1"><input type="radio" name="events" value="ON" <?php if($rowconfig['events']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="events" value="OFF" <?php if($rowconfig['events']=='OFF') {echo 'checked';}?>/>OFF</div>
	 </div>
	 <div class="contentcon">
	 <div class="unsesto1">Show profile suggestion</div><div class="unquarto1"><input type="radio" name="suggprofile" value="ON" <?php if($rowconfig['suggprofile']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="suggprofile" value="OFF" <?php if($rowconfig['suggprofile']=='OFF') {echo 'checked';}?>/>OFF</div>
	 </div>
	 <div class="contentcon">
   <div class="unsesto1">Show more info</div><div class="unquarto1"><input type="radio" name="moreinfo" value="ON" <?php if($rowconfig['moreinfo']=='ON') {echo 'checked';}?>/>ON <input type="radio" name="moreinfo" value="OFF" <?php if($rowconfig['moreinfo']=='OFF') {echo 'checked';}?>/>OFF</div>
	 </div>
	 
	 <div class="spazio"></div>
	 <span class="introtitle">Preferences</span><br/>
   <span class="introdesc">Choose what should be appear</span>
   <div class="spazio"></div>
	 
	 
   <div class="unsesto1">Days range of Birthdays</div><div class="unquarto1"><input type="text" name="dayrange" value="<?php echo $rowconfig['dayrange'];?>" size="3"/></div>
   <div class="unsesto1">MySQL timezone</div><div class="unquarto1"><input type="text" name="timezone" value="<?php echo $rowconfig['timezone'];?>" size="3"/></div>    
   <div class="unsesto1">Refresh time</div><div class="unquarto1"><input type="text" size="6" name="refresh" value="<?php echo $rowconfig['refresh'];?>"/></div>    
     
  <div class="unsesto1">Max events number to show</div><div class="unquarto1"><input type="text" name="maxnumberevent" value="<?php echo $rowconfig['maxnumberevent'];?>" size="3"/></div>
  <div class="unsesto1">Max number of days to consider for upcoming events</div><div class="unquarto1"><input type="text" name="maxnumberconsider" value="<?php echo $rowconfig['maxnumberconsider'];?>" size="3"/></div>
  <div class="unsesto1">Max friend/day</div><div class="unquarto1"><input type="text" name="maxfriendrequest" value="<?php echo $rowconfig['maxfriendrequest'];?>" size="3"/></div>
  <div class="unsesto1">Number of suggestions at time</div><div class="unquarto1"><input type="text" name="maxnumonline" value="<?php echo $rowconfig['maxnumonline'];?>" size="3"/></div>
  <div class="unsesto1">Date format<br>Choose "UNI" for mm/gg/aaaa format, "EUR" for gg/mm/yyyy, "JPN" for yyyy/mm/dd.</div><div class="unquarto1"><select name="dateFormatc"><option value="uni" <?php if($rowconfig['dateFormatc']=='uni') {echo 'selected';}?>>UNI</option><option value="eur" <?php if($rowconfig['dateFormatc']=='eur') {echo 'selected';}?>>EUR</option><option value="jpn" <?php if($rowconfig['dateFormatc']=='jpn') {echo 'selected';}?>>JPN</option></select></div>


  
	 </div>
	 

  </div>
  
  <div class="semicolumn"> 
  
  <div id="contentbox">
  <div class="contentcon">
   <span class="introtitle">Thresholds</span><br/>
   <span class="introdesc"><b>Important:</b> You can manage these thresholds as you want but make sure that the changes
   dont overload the server. <br/><br/>"Match percentage" is the minimal profile match percentage needed to consider profile for the suggestions, 
   low values can overloads the server "Mutual friends" is the minimal of mutual friends needed to consider a profile for the suggestions,
   how values can overloads the server also these conditions can be used both AND or only one OR the AND condition means that 
   both the conditions must be satisfied.<br/><br/>This reduce the number of suggestions but it's raccomended for large community so, 
   if you decrease the thresholds or you active the OR conditions.</span>
      <br/><br/>
      <div class="unsesto2">Match percentage (%)</div><div class="unquarto2"><input type="text" name="trsugg" value="<?php echo $rowconfig['trsugg'];?>" size="2"/></div>
      <div class="unsesto2">Mutual friends</div><div class="unquarto2"><input type="text" name="trfriends" value="<?php echo $rowconfig['trfriends'];?>" size="2" /></div>
      <div class="unsesto2">Use of the threshold</div><div class="unquarto2">
      <select name="conditionton">
       <option value="OR" <?php if($rowconfig['conditionton']=='OR') {echo 'selected="selected"';}?>>OR</option>
       <option value="AND" <?php if($rowconfig['conditionton']=='AND') {echo 'selected="selected"';}?>>AND</option>
      </select></div>
	  <div id="litespg"><b>OR:</b> a profile will be suggested if any condition is verified. <br/><b>AND:</b> to suggest a profile must to verified both the conditions.</div>
      <div class="spazio"></div>
   </div>
 <div class="contentcon">
  <span class="introtitle">Default Inviter</span> <br/>
   <input type="radio" name="defaultinviter" value="ON" <?php if($rowconfig['defaultinviter']=='ON') {echo 'checked';}?>/>ON
   <input type="radio" name="defaultinviter" value="OFF" <?php if($rowconfig['defaultinviter']=='OFF') {echo 'checked';}?>/>OFF
   <br/>
   <span class="introtitle">Customize inviter link</span><br/>
   <input type="text" name="linktoinviter" value="<?php echo $rowconfig['linktoinviter'];?>"> (es. /custominviter/home/)
   <div class="spazio"></div>
 </div>
  <div class="contentcon">
   
   
  <span class="introtitle">Suggestions reset</span><br/>
  <span class="introdesc">Click to reset the profiles' suggestions table</span> <input type="button" value="Reset" onclick="resettable();"><br/>
    <div class=""><div id="result"></div></div>
  
 </div>
 
 </div>

</div>
 <input type="submit" value="Save"></form>
</div>
</body>
</html>
<script>
function resettable()
{
 var conf = confirm("Are you sure you want to delete all the profiles suggested profiles?");

    if(conf == true){
    $.ajax({
    type: "POST",
    url: 'resettable.php',
    data: "resettrue=1",
    cache: false,
    success: function(data) {
     $('#result').html(data);
    }
    });      
          
    }
    
}
</script>