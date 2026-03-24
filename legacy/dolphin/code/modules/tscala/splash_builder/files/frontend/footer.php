<?php if($settings['footer']['page_enabled']) { ?>

<?php if ($settings['footer']['footer_html_pre'] != '') : ?>
<div class="footer-custom-pre">
	<div id="footer-custom-content-pre"><?php echo $settings['footer']['footer_html_pre']; ?></div>
</div>
<?php endif; ?>

<?php 
if(strlen($settings['footer']['facebook_link'])>0 ||strlen($settings['footer']['twitter_link'])>0 || strlen($settings['footer']['google_link'])>0 || strlen($settings['footer']['footer_note'])>0):
?>
<div id="footer">
	<?php if(strlen($settings['footer']['facebook_link'])>0) { ?><div class="social-icon"><a class="theme-link footer-link" href="<?php echo $settings['footer']['facebook_link']; ?>"><i class="fa fa-facebook"></i></a></div> <?php } ?>
	<?php if(strlen($settings['footer']['twitter_link'])>0) { ?><div class="social-icon"><a class="theme-link footer-link" href="<?php echo $settings['footer']['twitter_link']; ?>"><i class="fa fa-twitter"></i></a></div><?php } ?>
	<?php if(strlen($settings['footer']['google_link'])>0) {?><div class="social-icon"><a class="theme-link footer-link" href="<?php echo $settings['footer']['google_link']; ?>"><i class="fa fa-google-plus"></i></a></div><?php } ?>
	<div class="footer-message"><?php echo $settings['footer']['footer_note']; ?></div>
</div>
<?php
	endif;
?>

<?php if ($settings['footer']['footer_html'] != '') : ?>
<div class="footer-custom">
	<div id="footer-custom-content"><?php echo $settings['footer']['footer_html']; ?></div>
</div>
<?php endif; ?>

<?php } ?>