<?php
	$startSlide = 0;
	$startSlideClass = '';
	$count = 1;
?>
<div id="home">
<?php 
	if($settings['home']['show-slider']=='1'):
	/* Show the Slider */	
?>
	<div id="all-slides-container">
<?php	
		$slides_count = 0;
		for($i=0; $i<sizeof($settings['home']['slide']); $i++) {
			if($settings['home']['slide'][$i]['enabled'])	 {
				$slides_count++;
				if($settings['home']['slide'][$i]['enabled'] && !$startSlide) {
					$startSlideClass = 'slide-active';
					$startSlide = 1;
				}
				else {
					$startSlideClass = '';
				}			
	?>
			<div class="slide theme-home-text <?php echo $startSlideClass; ?>" id="slide-<?php echo $count; ?>">
				<div class="slide-background" style="background-image: url('modules/tscala/splash_builder/files/img/background/<?php echo $settings['home']['slide'][$i]['background']; ?>')"></div>
	<?php		if($settings['home']['slide'][$i]['filter']) {	?>
					<div class="home-background" style="background-image: url('modules/tscala/splash_builder/files/home-bg.png')"></div>
	<?php		}	?>
				<div class="slide-container">
					<h1 class="slide-header-1"><?php echo $settings['home']['slide'][$i]['header']; ?></h1>
					<h2 class="slide-header-2"><?php echo $settings['home']['slide'][$i]['sub-header']; ?></h2>
					
					<?php 
					/*
					if((strlen($settings['home']['slide'][$i]['button-name'])>0) && ($settings['home']['slide'][$i]['button-enabled'] == '1')) {?><a class="slide-button theme-button" href="<?php echo $settings['home']['slide'][$i]['button-url']; ?>"><?php echo $settings['home']['slide'][$i]['button-name']; ?></a><?php } 
					*/
					require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );

					
					if((strlen($settings['home']['slide'][$i]['button-name'])>0) && ($settings['home']['slide'][$i]['button-enabled'] == '1')) {?><a class="slide-button theme-button" href="javascript:void(0)" onclick="javascript: showPopupJoinForm(); return false; <?php echo $settings['home']['slide'][$i]; ?>"><?php echo $settings['home']['slide'][$i]['button-name']; ?></a><?php } 
					
					?>
                    
                    
                    
                   
				</div>
			</div>
	<?php	$count++;
			}	
		}	?>
	</div>

	<?php 
		/* mmoreyra: hide next/prev buttons when there is only one slide */
		if ( $slides_count > 1 ):
	?>	
		<div id="slide-prev"><i class="fa fa-chevron-left"></i></div>
		<div id="slide-next"><i class="fa fa-chevron-right"></i></div>
	<?php
		endif;
		
	else:
		/* Show the custom html header */
		echo $settings['home']['custom-html'];
	endif;

	?>	


</div>