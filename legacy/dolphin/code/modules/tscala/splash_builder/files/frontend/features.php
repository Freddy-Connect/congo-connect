<?php if($settings['features']['enabled']) { ?>
<div id="features" <?php echo ($settings['features']['two_columns']==1)?'class="two-columns"':''; ?>>
<?php
	$count = 1;
	for($i=0; $i<sizeof($settings['features'])-2; $i++) {
		if($settings['features'][$i]['enabled']) {
?>
	<div class="feature <?php echo ($settings['features']['two_columns']==1)?'two-columns':''; ?>" id="feature-<?php echo $count; ?>">
		<div class="feature-text-container theme-color-border">
			<h1 class="feature-header"><?php echo $settings['features'][$i]['header']; ?></h1>
			<div class="feature-description"><?php echo $settings['features'][$i]['description']; ?></div> 
		</div>
		<div class="feature-image theme-color">
			<i class="fa <?php echo $settings['features'][$i]['image']; ?>"></i>
		</div>
	</div>
<?php
			$count++;
		}
	}
?>
</div>
<?php } ?>