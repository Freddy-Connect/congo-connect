<?php 
$sBaseUrl = BX_DOL_URL_ROOT; 
global $dir, $site;
$slideimg1 = getParam('slideimg_one');

  if($slideimg1) {
  $strmod=(substr($slideimg1,5)); 
  $sliderimages= explode("&bse&", $strmod); 
  }
  
    $visible = getParam('slide_visibility');
   //$home =  basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
  $home='http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
  $home1=explode("?", $home);
if( $visible == $home || $visible.'index.php' == $home1[0]) {
	if($sliderimages){ 
?>

<div class="slidermain">

    <link rel="stylesheet" type="text/css" href="<?php echo $sBaseUrl ?>modules/bse/bsecustom_theme/templates/base/css/style.css" media="screen" />		
	
	<script type="text/javascript" src="<?php echo $sBaseUrl ?>modules/bse/bsecustom_theme/js/jquery.themepunch.plugins.min.js"></script>			
    <script type="text/javascript" src="<?php echo $sBaseUrl ?>modules/bse/bsecustom_theme/js/jquery.themepunch.revolution.min.js"></script>			
	

	<link rel="stylesheet" type="text/css" href="<?php echo $sBaseUrl ?>modules/bse/bsecustom_theme/templates/base/css/settings.css" media="screen" />	

	<link href='http://fonts.googleapis.com/css?family=Share' rel='stylesheet' type='text/css' />

        





		
		<!--
				HEADER	
		-->
	
		
		<!--
		#################################
			- THEMEPUNCH BANNER -
		#################################
		-->
								
						<div class="bannercontainer responsive">					
					<div class="banner">
						<ul>	
							
							
 <?php $i=0; foreach($sliderimages as $sliderimage) { 
$i++;
$slidercontentrows=explode(",",$sliderimage);
?>
<?php if($i==1) { ?>
<li data-transition="boxfade" data-slotamount="10" data-link="<?php if($slidercontentrows[4]!="none"){ echo $slidercontentrows[4]; } ?>" data-thumb="<?php echo $site['mediaImages'] . $slidercontentrows[0] ?>"> 
								<img src="<?php echo $site['mediaImages'] . $slidercontentrows[0] ?>" />
								<div class="caption lfl very_big_white" data-x="150" data-y="100" data-speed="300" data-start="1200" data-easing="easeOutExpo"><?php if($slidercontentrows[2]!="none"){ echo $slidercontentrows[2]; } ?></div>										
							</li>
                            
 <?php } else if($i==2) { ?>

<!-- SLIDE LEFT -->
<li data-transition="slideleft" data-slotamount="10" data-link="<?php if($slidercontentrows[4]!="none"){ echo $slidercontentrows[4]; } ?>" data-thumb="<?php echo $site['mediaImages'] . $slidercontentrows[0] ?>"> 
								<img src="<?php echo $site['mediaImages'] . $slidercontentrows[0] ?>" />
											<div class="caption lfl very_big_white" data-x="150" data-y="100" data-speed="300" data-start="1200" data-easing="easeOutExpo"><?php if($slidercontentrows[2]!="none"){ echo $slidercontentrows[2]; } ?></div>	
            										
</li>
	 
<?php } else if ($i==3){ ?>
 
 							<!-- SLIDE DOWN -->
							<li data-transition="slidedown" data-slotamount="1" data-link="<?php if($slidercontentrows[4]!="none"){ echo $slidercontentrows[4]; } ?>" data-thumb="<?php echo $site['mediaImages'] . $slidercontentrows[0] ?>"> 
							<img src="<?php echo $site['mediaImages'] . $slidercontentrows[0] ?>" />
										<div class="caption lfl very_big_white" data-x="150" data-y="100" data-speed="300" data-start="1200" data-easing="easeOutExpo"><?php if($slidercontentrows[2]!="none"){ echo $slidercontentrows[2]; } ?></div>											
								</li>
 
<?php  } else if($i==4){  ?>

							<!-- SLOTFADE HORIZONTAL -->
							<li data-transition="slotfade-horizontal" data-slotamount="20"  data-link="<?php if($slidercontentrows[4]!="none"){ echo $slidercontentrows[4]; } ?>" data-thumb="<?php echo $site['mediaImages'] . $slidercontentrows[0] ?>"> 
							<img src="<?php echo $site['mediaImages'] . $slidercontentrows[0] ?>" />	
											<div class="caption lfl very_big_white" data-x="150" data-y="100" data-speed="300" data-start="1200" data-easing="easeOutExpo"><?php if($slidercontentrows[2]!="none"){ echo $slidercontentrows[2]; } ?></div>	
                														
							</li>


<?php } else if($i==5){ ?>

							<!-- SLIDE UP -->
							<li data-transition="slideup" data-slotamount="20" data-delay="25000" data-link="<?php if($slidercontentrows[4]!="none"){ echo $slidercontentrows[4]; } ?>" data-thumb="<?php echo $site['mediaImages'] . $slidercontentrows[0] ?>"> 
								<img src="<?php echo $site['mediaImages'] . $slidercontentrows[0] ?>" />
										<div class="caption lfl very_big_white" data-x="150" data-y="100" data-speed="300" data-start="1200" data-easing="easeOutExpo"><?php if($slidercontentrows[2]!="none"){ echo $slidercontentrows[2]; } ?></div>	
                														
									</li>
                            
	
<?php	} else {   ?>        
 
 <li data-transition="boxfade" data-slotamount="10" data-link="<?php if($slidercontentrows[4]!="none"){ echo $slidercontentrows[4]; } ?>" data-thumb="<?php echo $site['mediaImages'] . $slidercontentrows[0] ?>"> 
								<img src="<?php echo $site['mediaImages'] . $slidercontentrows[0] ?>" />
								<div class="caption lfl very_big_white" data-x="150" data-y="100" data-speed="300" data-start="1200" data-easing="easeOutExpo"><?php if($slidercontentrows[2]!="none"){ echo $slidercontentrows[2]; } ?></div>	
                														
 </li>                

<?php }  } ?>       


							

							

							


							
							
							

						</ul>		
						<div class="tp-bannertimer"></div>												
					</div>					
				</div>					
				
		<div id="unvisible_button"></div>
		
		
		<!--<div id="go" style="position:fixed;top:10px; left:10px; width:40px; height:20px; background-color:#00ff00;"></div>-->
	
		
		<?php
		
   $slide_transition = getParam('slide_transition');
   $slide_navtype = getParam('slide_navtype');
   $slide_navstyle = getParam('slide_navstyle');
	$slide_bullethori = getParam('slide_bullethori');
   $slide_bulletvert = getParam('slide_bulletvert');
   $slide_trantime = getParam('slide_trantime');
   $slide_navarrow = getParam('slide_navarrow');		
	$slide_trantime = $slide_trantime * 1000;	
		?>	

			<!--
			##############################
			 - ACTIVATE THE BANNER HERE -
			##############################
			-->
			<script type="text/javascript">
								
				
				jQuery(document).ready(function() {
				
				if ($.fn.cssOriginal!=undefined)
					$.fn.css = $.fn.cssOriginal;

					jQuery('.banner').revolution(
						{	
													
							delay:<?php if($slide_trantime){ echo $slide_trantime; } else { echo "9000"; } ?>,												
							startwidth:872,
							startheight:285,
							
							onHoverStop:"on",						// Stop Banner Timet at Hover on Slide on/off
							
							thumbWidth:100,							// Thumb With and Height and Amount (only if navigation Tyope set to thumb !)
							thumbHeight:50,
							thumbAmount:4,
							
							hideThumbs:200,
							navigationType:"<?php if($slide_navtype){ echo $slide_navtype; } else { echo "none"; } ?>",					
							//bullet, thumb, none, both	 (No Shadow in Fullwidth Version !)
							navigationArrows:"verticalcentered",		                             //nexttobullets, verticalcentered, none
							navigationStyle:"<?php if($slide_navstyle){ echo $slide_navstyle; } else { echo "round"; }?>",				//round,square,navbar
							
							touchenabled:"on",						// Enable Swipe Function : on/off
							
							navOffsetHorizontal:<?php if($slide_bullethori){ echo $slide_bullethori; } else { echo "0"; } ?>,
							navOffsetVertical:<?php if($slide_bulletvert){  echo $slide_bulletvert; } else {echo "20"; } ?>,
							
							fullWidth:"on",
							
							shadow:0							// Turns On or Off the Fullwidth Image Centering in FullWidth Modus
														
						});	

					});
					
			</script>
		
		

</div>
<?php 
	}
} 
?>



