<option value="youtube" selected="selected">Youtube</option>
<?php 
$selrayzemb="SELECT * FROM `sys_modules` WHERE `title`='Advanced Video Embed' and `vendor`='Rayz'";
$verrayz=mysql_query($selrayzemb);
$checkif=mysql_num_rows($verrayz);
if ($checkif>0) {
?>
<option value="bliptv">Blip.TV</option>
<option value="dailymotion">DailyMotion</option>
<!--<option value="gaywatch">GayWatch</option>-->
<option value="godtube">GodTube</option>
<option value="metacafe">Metacafe</option>
<option value="movieclips">MovieClips</option>
<!--<option value="myspace">MySpace</option>-->
<!--<option value="redtube">RedTube</option>-->
<!--<option value="sextube">SexTube</option>-->
<!--<option value="slutload">SlutLoad</option>-->
<option value="vimeo">Vimeo</option>
<option value="wattv">WAT.TV</option>
<!--<option value="xtube">XTube</option>-->
<!--<option value="xvideos">XVideos</option>-->
<!--<option value="xxxbunker">XXXBunker</option>-->
<!--<option value="youku">Youku</option>-->
<!--<option value="rai">Rai Video</option>-->
<!--<option value="custom"><?echo _t("_rz_embed_custom");?></option>-->
<option value="other" onclick="window.location.href ='<?php echo BX_DOL_URL_ROOT;?>m/videos/albums/my/add_objects/<?php echo $namevideoalbum;?>/owner/<?php echo getUsername($accountid);?>';"><?echo _t("__Other");?></option>
<?php
}
?>
