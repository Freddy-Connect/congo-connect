<div id="header-container">
	<div id="header-inner">
		<div id="logo-container">
			<img src="modules/tscala/splash_builder/files/img/logo/<?php echo $settings['logo']; ?>" />
		</div>
		<div id="navigation-container" class="theme-home-text">
<?php		if($settings['features']['enabled']) {	?>
				<a class="navigation-link theme-link" href="#features" id="navigation-features"><?php echo __('menu_features'); ?></a>
<?php	}	if($settings['testimonials']['enabled']) {	?>
				<a class="navigation-link theme-link" href="#testimonials" id="navigation-testimonials"><?php echo __('menu_testimonials'); ?></a>
<?php	}	if($settings['pricing']['enabled']) {	?>
				<a class="navigation-link theme-link" href="#pricing" id="navigation-pricing"><?php echo __('menu_pricing'); ?></a>
						
<?php	}	if($settings['contact']['page_enabled']) {	?>
			<a class="navigation-link theme-link" href="#contact" id="navigation-contact"><?php echo __('menu_contact'); ?></a>
<a class="navigation-link theme-link" href="member.php" id="navigation-pricing"><?php echo __('member_login'); ?></a>
<?php	}	?>
		</div>
	</div>
</div>