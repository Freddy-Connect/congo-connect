<?php 

$facebook_url=getParam('facebook_url');
$twitter_url=getParam('twitter_url');
$gplus_url=getParam('gplus_url');
$pinterest_url=getParam('pinterest_url');
$linkedIn_url=getParam('linkedIn_url');
$rss_url=getParam('rss_url');
$tumblr_url=getParam('tumblr_url');
$flickr_url=getParam('flickr_url');
$youtube_url=getParam('youtube_url');
$digg_url=getParam('digg_url');
$buffer_url=getParam('buffer_url');

		

if($facebook_url=='' && $twitter_url=='' &&  $gplus_url=='' &&  $pinterest_url=='' && $linkedIn_url=='' &&  $rss_url=='' &&  $tumblr_url=='' &&  $flickr_url=='' &&  $youtube_url=='' &&  $digg_url=='' &&  $buffer_url==''){

?>

<div class="social_icon clearfix">
    <ul>
    <li class="facebook"><a href="#">&nbsp;</a></li>
     <li class="flickr"><a href="#">&nbsp;</a></li>
      <li class="google"><a href="#">&nbsp;</a></li>
       <li class="linketn"><a href="#">&nbsp;</a></li>
        <li class="pintrest"><a href="#">&nbsp;</a></li>
         <li class="twiiter"><a href="#">&nbsp;</a></li>
         <li class="youtube"><a href="#">&nbsp;</a></li>
</ul>
</div>
<?php } else { ?>

<div class="social_icon clearfix">
    <ul>
    <?php if($facebook_url!='') {?>
    <li class="facebook"><a href="<?php echo $facebook_url; ?>">&nbsp;</a></li>
    <?php } ?>
    
    <?php if($flickr_url!='') {?>
    <li class="flickr"><a href="<?php echo $flickr_url; ?>">&nbsp;</a></li>
    <?php } ?>
    
    <?php if($gplus_url!='') {?>
    <li class="google"><a href="<?php echo $gplus_url; ?>">&nbsp;</a></li>
    <?php } ?>
    
    <?php if($linkedIn_url!='') {?>
    <li class="linketn"><a href="<?php echo $linkedIn_url; ?>">&nbsp;</a></li>
    <?php } ?>
    
    <?php if($pinterest_url!='') {?>
    <li class="pintrest"><a href="<?php echo $pinterest_url; ?>">&nbsp;</a></li>
    <?php } ?>
    
    <?php if($twitter_url!='') {?>
    <li class="twiiter"><a href="<?php echo $twitter_url; ?>">&nbsp;</a></li>
    <?php } ?>
    
    <?php if($youtube_url!='') {?>
    <li class="youtube"><a href="<?php echo $youtube_url; ?>">&nbsp;</a></li>
    <?php } ?>
    
    <?php if($rss_url!='') {?>
    <li class="rssico"><a href="<?php echo $rss_url; ?>">&nbsp;</a></li>
    <?php } ?>
    
    <?php if($tumblr_url!='') {?>
    <li class="tumblr"><a href="<?php echo $tumblr_url; ?>">&nbsp;</a></li>
    <?php } ?>
    
    




</ul>
</div>
	
<?php }?>