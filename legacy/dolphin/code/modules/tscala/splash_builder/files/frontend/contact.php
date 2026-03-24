<?php if($settings['contact']['page_enabled']) { 
	$location_css = ($settings['contact']['form-enabled']==0)?'style="float:none; margin-left:auto; margin-right:auto;"':'';
?>
<div id="contact">
	<div id="contact-inner">
		<div id="contact-details" <?php echo $location_css; ?>>
			<div id="contact-info">
				<div id="contact-header"><?php echo $settings['contact']['contact-header']; ?></div>
				<div id="contact-types">
					<div class="contact-type-container" id="phone-contact"><i class="fa fa-phone"></i><span class="contact-type"><?php echo $settings['contact']['contact-phone']; ?></span></div><!--
				 --><div class="contact-type-container" id="email-contact"><i class="fa fa-envelope"></i><span class="contact-type"><?php echo $settings['contact']['contact-email']; ?></span></div><!--
				 --><div class="contact-type-container" id="address-contact"><i class="fa fa-map-marker"></i><span class="contact-type"><?php echo $settings['contact']['contact-address']; ?></span></div>
				</div>
			</div>
			<div id="contact-location"></div>
		</div>
		<?php
			if ($settings['contact']['form-enabled']==1):
		?>
		<div id="contact-form" class="contact-section">
			<div class="form-element-container">
				<input type="text" id="form-name" class="form-text-element" autocomplete="off" />
				<div class="form-element-label-container">
					<label class="form-element-label"><?php echo $settings['contact']['form-name']; ?></label>
				</div>
			</div>
			<div class="form-element-container">
				<input type="text" id="form-email" class="form-text-element" autocomplete="off" />
				<div class="form-element-label-container">
					<label class="form-element-label"><?php echo $settings['contact']['form-email']; ?></label>
				</div>
			</div>
			<div class="form-element-container">
				<input type="text" id="form-phone" class="form-text-element" autocomplete="off" />
				<div class="form-element-label-container">
					<label class="form-element-label"><?php echo $settings['contact']['form-phone']; ?></label>
				</div>
			</div>
			<div class="form-element-container">
				<textarea id="form-message" class="form-text-element" autocomplete="off"></textarea>
				<div class="form-element-label-container">
					<label class="form-element-label"><?php echo $settings['contact']['form-message']; ?></label>
				</div>
			</div>
			<div id="send-message-button" data-in-progress="0"><?php echo $settings['contact']['send-message-button']; ?></div>
			<div id="form-error"><?php echo __('contact_send_msg_error'); ?></div>
			<div id="form-success"><?php echo __('contact_send_msg_success'); ?></div>
		</div>
		<?php
			endif;
		?>
	</div>
</div>
<?php  } ?>