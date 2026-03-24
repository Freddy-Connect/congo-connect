<?php
	session_start();
	if(file_exists("credentials.php")) {
		require_once('credentials.php');
		
		
	}
	else {
		header('Location: install.php');
	}

	/* Include the language file or redirect to install page */
	if(file_exists("language.php")) {
		require_once('language.php');
	}
	else {
		header('Location: install.php');
	}	

	$settings = array_slug_replace(unserialize(file_get_contents("config.conf")));
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300' rel='stylesheet' type='text/css' />
<link href='../css/font-awesome.css' rel='stylesheet' type='text/css' />
<link href='css/admin.css' rel='stylesheet' type='text/css' />
<link href='css/footer.css' rel='stylesheet' type='text/css' />
<link href='../css/themes/<?php echo $settings["theme"]; ?>' rel='stylesheet' type='text/css' />
<script type="text/javascript" src="../js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="js/tooltipsy.min.js"></script>
</head>
<body>
<?php require_once('header.php'); ?>	

<div id="error-box"><?php echo __('error_box'); ?></div>
<div id="success-box"><?php echo __('success_box'); ?></div>

<div id="app-buttons">
	<div id="app-navigation-buttons">
		<a href="home.php"><?php echo __('menu_home'); ?></a>
		<a href="features.php"><?php echo __('menu_features'); ?></a>
		<a href="testimonials.php"><?php echo __('menu_testimonials'); ?></a>
		<a href="pricing.php"><?php echo __('menu_pricing'); ?></a>
		<a href="contact.php"><?php echo __('menu_contact'); ?></a>
		<a href="footer.php" class="app-navigation-button-active"><?php echo __('menu_footer'); ?></a>
		<a href="mics.php"><?php echo __('menu_mics'); ?></a>
	</div>
	<div id="app-save-button">
		<div id="save-button"><?php echo __('save_changes_button'); ?></div>
	</div>
</div>
<?php
	$pageEnabled = $settings['footer']['page_enabled']==1?'checked="checked"':'';
?>
<div id="section-enabled"><div id="section-enabled-text"><input id="section-enabled-checkbox" type="checkbox" <?php echo $pageEnabled; ?>/><label for="section-enabled-checkbox"><?php echo __('enable_page_label'); ?></label></div></div>
<?php
	if(strlen(trim($settings['footer']['facebook_link']))>0) {
		$fb = ' style="display: none;"';
	}
	else {
		$fb = ' style="display: inline;"';
	}

	if(strlen(trim($settings['footer']['twitter_link']))>0) {
		$tw = ' style="display: none;"';
	}
	else {
		$tw = ' style="display: inline;"';
	}

	if(strlen(trim($settings['footer']['google_link']))>0) {
		$gp = ' style="display: none;"';
	}
	else {
		$gp = ' style="display: inline;"';
	}

	if(strlen(trim($settings['footer']['footer_note']))>0) {
		$fn = ' style="display: inline;"';
		$fnE = ' style="display: none;"';
	}
	else {
		$fn = ' style="display: none;"';
		$fnE = ' style="display: inline;"';
	}
?>
<div class="footer-custom">
	<div id="footer-custom-content-pre"><?php echo ($settings['footer']['footer_html_pre'] != '') ? $settings['footer']['footer_html_pre'] : __('double_click_footer_tooltip'); ?></div>
	<textarea id="footer-custom-content-pre-edit"></textarea>
</div>
<div id="footer">
	<div class="social-icon"><i class="theme-link footer-link fa fa-facebook" title="<?php echo __('double_click_link_tooltip'); ?>"></i><input type="text" value="<?php echo $settings['footer']['facebook_link']; ?>" <?php echo $fb; ?>/></div>
	<div class="social-icon"><i class="theme-link footer-link fa fa-twitter" title="<?php echo __('double_click_link_tooltip'); ?>"></i><input type="text" value="<?php echo $settings['footer']['twitter_link']; ?>" <?php echo $tw; ?>/></div>
	<div class="social-icon"><i class="theme-link footer-link fa fa-google-plus" title="<?php echo __('double_click_link_tooltip'); ?>"></i><input type="text" value="<?php echo $settings['footer']['google_link']; ?>" <?php echo $gp; ?>/></div>
	<div class="footer-message"><span title="<?php echo __('double_click_tooltip'); ?>"<?php echo $fn; ?>><?php echo $settings['footer']['footer_note']; ?></span><input type="text" value="<?php echo $settings['footer']['footer_note']; ?>" <?php echo $fnE; ?>/></div>
</div>
<div class="footer-custom">
	<div id="footer-custom-content"><?php echo ($settings['footer']['footer_html'] != '') ? $settings['footer']['footer_html'] : __('double_click_footer_tooltip'); ?></div>
	<textarea id="footer-custom-content-edit"></textarea>
</div>


<div id="usage">
	
	<div class="usage-header" style="margin-top:30px"><?php echo __('double_click_legend'); ?></div>
	
</div>

<script type="text/javascript">

$('.social-icon i, .footer-message span').tooltipsy({
	alignTo: 'element',
    offset: [0, 1]
});

$("#contact-header, #send-message-button").on('dblclick', function() { 
	$(this).hide();
	$(this).next().val($(this).text()).css('display', 'block');
});

$("#contact-header-edit, #send-message-button-edit").on('dblclick', function() { 
	if($(this).val() == '') 
		return false;

	$(this).prev().text($(this).val()).show();
	$(this).hide();
});

$(".social-icon i").on('dblclick', function() { 
	$(this).next().show();
}); 

$(".social-icon input[type='text']").on('dblclick', function() { 
	$(this).hide();
});

$(".footer-message span").on('dblclick', function() { 
	$(this).hide();
	$(this).next().show();
}); 

$(".footer-message input[type='text']").on('dblclick', function() { 
	$(this).prev().text($(this).val()).show();
	$(this).hide();
});

$("#footer-custom-content, #footer-custom-content-pre").on('dblclick', function() {
	$(this).hide();
	if($(this).html() != '<?php echo __("double_click_footer_tooltip"); ?>') {
		$(this).next().val($(this).html());	
	}
	$(this).next().css('display','block');
	$(this).next().focus();
});

$("#footer-custom-content-edit, #footer-custom-content-pre-edit").on('dblclick', function() {
	$(this).hide();
	if($(this).val() == '') {
		$(this).prev().text('<?php echo __("double_click_footer_tooltip")?>').show();
	} else {
		$(this).prev().html($(this).val()).show();	
	}
});



/******************************************************************************************************
*																									  *
*											Save Data function										  *
*																									  *
*******************************************************************************************************/

var settings = <?php echo json_encode($settings); ?>;
var tempSettings = <?php echo json_encode($settings); ?>;

$(document).ready(function() {
	$('#section-enabled-text').on('click', function() {
		saveButtonToggle();
	});

	$('input[type="text"]').on('keyup', function() {
		saveButtonToggle();
	});

	$('#save-button').on('click', function() {
		saveData();
	});

	$('#footer-custom-content-edit, #footer-custom-content-pre-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('#footer-custom-content-edit, #footer-custom-content-pre-edit').on('dblclick', function() {
		saveButtonToggle();
	});	
});

function saveButtonToggle() {
	var saveChanges = 0;

	var pageEnabled = $('#section-enabled-checkbox').prop('checked')?1:0;
	if(settings.footer['page_enabled'] != pageEnabled) {
		tempSettings.footer['page_enabled'] = pageEnabled;
		saveChanges = 1;
	}
	else {
		tempSettings.footer['page_enabled'] = settings.footer['page_enabled'];
	}

	var fb = $('.fa-facebook').closest('.social-icon').find('input[type="text"]').val();
	if(settings.footer['facebook_link'] != fb) {
		tempSettings.footer['facebook_link'] = fb;
		saveChanges = 1;
	}
	else {
		tempSettings.footer['facebook_link'] = settings.footer['facebook_link'];
	}

	var tw = $('.fa-twitter').closest('.social-icon').find('input[type="text"]').val();
	if(settings.footer['twitter_link'] != tw) {
		tempSettings.footer['twitter_link'] = tw;
		saveChanges = 1;
	}
	else {
		tempSettings.footer['twitter_link'] = settings.footer['twitter_link'];
	}

	var gp = $('.fa-google-plus').closest('.social-icon').find('input[type="text"]').val();
	if(settings.footer['google_link'] != gp) {
		tempSettings.footer['google_link'] = gp;
		saveChanges = 1;
	}
	else {
		tempSettings.footer['google_link'] = settings.footer['google_link'];
	}

	var fn = $('.footer-message').find('input[type="text"]').val();
	if(settings.footer['footer_note'] != fn) {
		tempSettings.footer['footer_note'] = fn;
		saveChanges = 1;
	}
	else {
		tempSettings.footer['footer_note'] = settings.footer['footer_note'];
	}

	var footerHTML_pre = ($('#footer-custom-content-pre').css('display')=='block')?$('#footer-custom-content-pre').html():$('#footer-custom-content-pre-edit').val();
	if(footerHTML != settings['footer']['footer_html_pre'] && footerHTML_pre != '<?php echo __("double_click_footer_tooltip"); ?>') {
		tempSettings.footer['footer_html_pre'] = footerHTML_pre;
		saveChanges = 1;
	}

	var footerHTML = ($('#footer-custom-content').css('display')=='block')?$('#footer-custom-content').html():$('#footer-custom-content-edit').val();
	if(footerHTML != settings['footer']['footer_html'] && footerHTML != '<?php echo __("double_click_footer_tooltip"); ?>') {
		tempSettings.footer['footer_html'] = footerHTML;
		saveChanges = 1;
	}	

	if(saveChanges == 1) {
		$('#app-save-button').fadeIn('slow');
	}
	else {
		$('#app-save-button').fadeOut('slow');
	}
}

function saveData() {
	$('#save-button').css('opacity', '0.65');
	$.ajax({
		url: 'control.php',
		type:'POST',
		dataType: 'json',
		data: {'payload': tempSettings},
		success: function(response) {
			$('#save-button').css('opacity', '1.0');
			if(response.error == 0) {
				$('#success-box').slideDown('slow').delay(2000).slideUp('slow');
				settings = JSON.parse(JSON.stringify(tempSettings));
				$('#app-save-button').fadeOut('slow');
			}
			else {
				$('#error-box').slideDown('slow').delay(2000).slideUp('slow');	
			}
		}
	});
}
</script>

</body>
</html>