<?php if($settings['testimonials']['enabled']) { ?>
<div id="testimonials" data-present-testimonial="1">
	<div id="testimonials-inner" style="background-image: url('modules/tscala/splash_builder/files/img/testimonial/<?php echo $settings['testimonials']['background']; ?>')">
		<div id="all-testimonials-outer-container">
			<div id="all-testimonials-inner-container">
<?php			$count = 1;
				for($i=0; $i<5; $i++) {
					if($settings['testimonials']['testimonial'][$i]['enabled'] && (strlen($settings['testimonials']['testimonial'][$i]['text'])>0 || strlen($settings['testimonials']['testimonial'][$i]['name'])>0 || strlen($settings['testimonials']['testimonial'][$i]['link'])>0)) {
						$image = $settings['testimonials']['testimonial'][$i]['image']=='default.jpg'?'':'<img class="testimonial-image" src="modules/tscala/splash_builder/files/img/testimonial/' . $settings['testimonials']['testimonial'][$i]['image'] . '" />';
						$link = strlen($settings['testimonials']['testimonial'][$i]['link'])>0?'<a href="' . $settings['testimonials']['testimonial'][$i]['link'] . '" class="customer-website">' . $settings['testimonials']['testimonial'][$i]['link'] . '</a>':'';
						$name_enabled = $settings['testimonials']['testimonial'][$i]['name_enabled'];
						$website_enabled = $settings['testimonials']['testimonial'][$i]['website_enabled'];
?>
				<div class="testimonial testimonial-active" id="testimonial-<?php echo $count; ?>">
					<div class="testimonial-comment"><i class="testimonial-quote-left fa fa-quote-left"></i><div class="customer-comment"><?php echo $settings['testimonials']['testimonial'][$i]['text']; ?></div><i class="testimonial-quote-right fa fa-quote-right"></i></div>
					
			<?php 
				if ($image != '' || $name_enabled == 1 || $website_enabled == 1):
			?>
					<div class="testimonial-customer-info">
						<?php echo $image; ?><span class="customer-name"><?php echo ($name_enabled==1)?$settings['testimonials']['testimonial'][$i]['name']:''; ?></span><?php echo ($website_enabled==1) ? $link : ''; ?>
					</div>
			<?php
				endif;
			?>
				</div>
<?php					$count++;
					}
				}	?>
			</div>
		</div>
<?php
	if($count > 2):
?>
		<div id="slide-controls">
		<?php
			for($i=0; $i<5; $i++) {
					if($settings['testimonials']['testimonial'][$i]['enabled'] && (strlen($settings['testimonials']['testimonial'][$i]['text'])>0 || strlen($settings['testimonials']['testimonial'][$i]['name'])>0 || strlen($settings['testimonials']['testimonial'][$i]['link'])>0)) {
						$slideControl = $i==0?' slide-control-active':'';
						?>
			<div class="slide-control<?php echo $slideControl; ?>" data-testimonial="<?php echo ($i+1);?>"></div>
			<?php } }?>
			
		</div>
<?php
	endif;
?>
	</div>
</div>
<?php } ?>