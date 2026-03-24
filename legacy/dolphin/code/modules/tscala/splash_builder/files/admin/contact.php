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
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300' rel='stylesheet' type='text/css' />
<link href='../css/font-awesome.css' rel='stylesheet' type='text/css' />
<link href='css/admin.css' rel='stylesheet' type='text/css' />
<link href='css/contact.css' rel='stylesheet' type='text/css' />
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
		<a href="contact.php" class="app-navigation-button-active"><?php echo __('menu_contact'); ?></a>
		<a href="footer.php"><?php echo __('menu_footer'); ?></a>
		<a href="mics.php"><?php echo __('menu_mics'); ?></a>
	</div>
	<div id="app-save-button">
		<div id="save-button"><?php echo __('save_changes_button'); ?></div>
	</div>
</div>
<?php
	$pageEnabled = $settings['contact']['page_enabled']==1?'checked="checked"':'';
?>
<div id="section-enabled"><div id="section-enabled-text"><input id="section-enabled-checkbox" type="checkbox" <?php echo $pageEnabled; ?>/><label for="section-enabled-checkbox"><?php echo __('enable_page_label'); ?></label></div></div>
<?php
	
	if(strlen(trim($settings['contact']['contact-header']))>0) {
		$contactHeader = ' style="display: block;"';
		$contactHeaderEdit = ' style="display: none;"';
	}
	else {
		$contactHeader = ' style="display: none;"';
		$contactHeaderEdit = ' style="display: block;"';
	}

	if(strlen(trim($settings['contact']['contact-phone']))>0) {
		$contactPhone = ' style="display: inline;"';
		$contactPhoneEdit = ' style="display: none;"';
	}
	else {
		$contactPhone = ' style="display: none;"';
		$contactPhoneEdit = ' style="display: inline;"';
	}
	
	if(strlen(trim($settings['contact']['contact-email']))>0) {
		$contactEmail = ' style="display: inline;"';
		$contactEmailEdit = ' style="display: none;"';
	}
	else {
		$contactEmail = ' style="display: none;"';
		$contactEmailEdit = ' style="display: inline;"';
	}
	
	if(strlen(trim($settings['contact']['contact-address']))>0) {
		$contactAddress = ' style="display: inline;"';
		$contactAddressEdit = ' style="display: none;"';
	}
	else {
		$contactAddress = ' style="display: none;"';
		$contactAddressEdit = ' style="display: inline;"';
	}
	
	if(strlen(trim($settings['contact']['contact-location-latitude']))>0) {
		$latitudeValue = $settings['contact']['contact-location-latitude'];
	}
	else {
		$latitudeValue = '';
	}
	
	if(strlen(trim($settings['contact']['contact-location-longitude']))>0) {
		$longitudeValue = $settings['contact']['contact-location-longitude'];
	}
	else {
		$longitudeValue = '';
	}
	
	if(strlen($settings['contact']['form-name'])>0) {
		$fieldName = ' style="display: inline;"';
		$fieldNameEdit = ' style="display: none;"';
	}
	else {
		$fieldName = ' style="display: none;"';
		$fieldNameEdit = ' style="display: inline;"';
	}

	if(strlen(trim($settings['contact']['form-email']))>0) {
		$fieldEmail = ' style="display: inline;"';
		$fieldEmailEdit = ' style="display: none;"';
	}
	else {
		$fieldEmail = ' style="display: none;"';
		$fieldEmailEdit = ' style="display: inline;"';
	}

	if(strlen(trim($settings['contact']['form-phone']))>0) {
		$fieldPhone = ' style="display: inline;"';
		$fieldPhoneEdit = ' style="display: none;"';
	}
	else {
		$fieldPhone = ' style="display: none;"';
		$fieldPhoneEdit = ' style="display: inline;"';
	}

	if(strlen(trim($settings['contact']['form-message']))>0) {
		$formMessage = ' style="display: inline;"';
		$formMessageEdit = ' style="display: none;"';
	}
	else {
		$formMessage = ' style="display: none;"';
		$formMessageEdit = ' style="display: inline;"';	
	}

	if(strlen(trim($settings['contact']['send-message-button']))>0) {
		$formButton = ' style="display: block;"';
		$formButtonEdit = ' style="display: none;"';
	}
	else {
		$formButton = ' style="display: none;"';
		$formButtonEdit = ' style="display: block;"';	
	}	


?>
<div id="contact">
	<div id="contact-inner">
		<div id="contact-details">
			<div id="contact-info">
				<div id="contact-header" title="<?php echo __('double_click_tooltip'); ?>"<?php echo $contactHeader; ?>><?php echo $settings['contact']['contact-header']; ?></div>
				<input type="text" id="contact-header-edit" value="Contact Us"<?php echo $contactHeaderEdit; ?>/>
				<div id="contact-types">
					<div class="contact-type-container" id="phone-contact"><i class="fa fa-phone"></i><span class="contact-type" title="<?php echo __('double_click_tooltip'); ?>"<?php echo $contactPhone; ?>><?php echo $settings['contact']['contact-phone']; ?></span><input type="text" value="<?php echo $settings['contact']['contact-phone']; ?>" id="phone-contact-edit"<?php echo $contactPhoneEdit; ?>/></div>
				 	<div class="contact-type-container" id="email-contact"><i class="fa fa-envelope"></i><span class="contact-type" title="<?php echo __('double_click_tooltip'); ?>"<?php echo $contactEmail; ?>><?php echo $settings['contact']['contact-email']; ?></span><input type="text" value="<?php echo $settings['contact']['contact-email']; ?>" id="email-contact-edit"<?php echo $contactEmailEdit; ?>/></div>
					<div class="contact-type-container" id="address-contact"><i class="fa fa-map-marker"></i><span class="contact-type" title="<?php echo __('double_click_tooltip'); ?>"<?php echo $contactAddress; ?>><?php echo $settings['contact']['contact-address']; ?></span><input type="text" value="<?php echo $settings['contact']['contact-address']; ?>" id="address-contact-edit" <?php echo $contactAddressEdit; ?>/></div>
				</div>
			</div>
			<div id="contact-location">
				<div class="contact-location-form-element">
					<label><?php echo __('contact_location_enable_label'); ?></label>
					<input type="checkbox" id="contact-location-enable-checkbox" <?php echo ($settings['contact']['contact-location-enabled']==1)?'checked':''; ?>>
				</div>
				<div class="contact-location-form-element">
					<label for="contact-location-latitude"><?php echo __('contact_latitude_label'); ?></label>
					<input type="text" id="contact-location-latitude" value="<?php echo $settings['contact']['contact-location-latitude']; ?>" />
				</div>
				<div class="contact-location-form-element">
					<label for="contact-location-longitude"><?php echo __('contact_longitud_label'); ?></label>
					<input type="text" id="contact-location-longitude" value="<?php echo $settings['contact']['contact-location-longitude']; ?>" />
				</div>
			</div>
		</div>
		<div id="contact-form" class="contact-section">
			<div class="form-element-container">
				<input type="text" id="form-name" class="form-text-element" disabled />
				<div class="form-element-label-container">
					<label class="form-element-label" title="<?php echo __('double_click_tooltip'); ?>"<?php echo $fieldName; ?>><?php echo $settings['contact']['form-name']; ?></label>
					<input type="text" id="form-name-edit" value="<?php echo $settings['contact']['form-name']; ?>"<?php echo $fieldNameEdit; ?>/>
				</div>
			</div>
			<div class="form-element-container">
				<input type="text" id="form-email" class="form-text-element" disabled />
				<div class="form-element-label-container">
					<label class="form-element-label" title="<?php echo __('double_click_tooltip'); ?>"<?php echo $fieldEmail; ?>><?php echo $settings['contact']['form-email']; ?></label>
					<input type="text" id="form-email-edit" value="<?php echo $settings['contact']['form-email']; ?>"<?php echo $fieldEmailEdit; ?>/>
				</div>
			</div>
			<div class="form-element-container">
				<input type="text" id="form-phone" class="form-text-element" disabled />
				<div class="form-element-label-container">
					<label class="form-element-label" title="<?php echo __('double_click_tooltip'); ?>"<?php echo $fieldPhone; ?>><?php echo $settings['contact']['form-phone']; ?></label>
					<input type="text" id="form-phone-edit" value="<?php echo $settings['contact']['form-phone']; ?>"<?php echo $fieldPhoneEdit; ?>/>
				</div>
			</div>
			<div class="form-element-container">
				<textarea id="form-message" class="form-text-element" disabled></textarea>
				<div class="form-element-label-container">
					<label class="form-element-label" title="<?php echo __('double_click_tooltip'); ?>"<?php echo $formMessage; ?>><?php echo $settings['contact']['form-message']; ?></label>
					<input type="text" id="form-message-edit" value="<?php echo $settings['contact']['form-message']; ?>"<?php echo $formMessageEdit; ?>/>
				</div>
			</div>
			<div id="send-message-button" title="<?php echo __('double_click_tooltip'); ?>"<?php echo $formButton; ?>><?php echo $settings['contact']['send-message-button'];  ?></div>
			<input type="text" value="<?php echo $settings['contact']['send-message-button'];  ?>" id="send-message-button-edit"<?php echo $formButtonEdit; ?>/>
			<label for="contact-form-enable-checkbox" id="contact-form-enable-label"><?php echo __('contact_form_enable_label'); ?></label>
			<input type="checkbox" id="contact-form-enable-checkbox" <?php echo ($settings['contact']['form-enabled']==1)?'checked':''; ?>>
		</div>
	</div>
</div>



<div id="usage">
	<div class="usage-header" style="margin-top:30px"><?php echo __('double_click_legend'); ?></div>

	<ul style="margin-top:40px">
		<li><?php echo __('contact_usage_1'); ?></li>
		<li><?php echo __('contact_usage_2'); ?></li>
		<li><?php echo __('contact_usage_3'); ?><img style="margin-left:0" src="img/maps-help.jpg" /></li>
	</ul>
</div>

<script type="text/javascript">

$("#contact-location").css('height', ($("#contact-inner").height() - $("#contact-info").outerHeight() - 20) + 'px');

$("#form-message").css('height', $("#form-left-elements").height() + 'px');

$('#contact-background-image, #contact-header, #send-message-button').tooltipsy({
	alignTo: 'element',
    offset: [0, 1]
});

$('.form-element-label, .contact-type').tooltipsy({
	alignTo: 'element',
    offset: [1, 0]
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

$(".contact-type, .form-element-label").on('dblclick', function() { 
	$(this).hide();
	$(this).next().val($(this).text()).show();
}); 

$("#contact-types input[type='text'], .form-element-label-container input[type='text']").on('dblclick', function() { 
	if($(this).val() == '') 
		return false;

	$(this).prev().text($(this).val()).show();
	$(this).hide();
});

/******************************************************************************************************
*																									  *
*											Save Data function										  *
*																									  *
*******************************************************************************************************/

var settings = <?php echo json_encode($settings); ?>;
var tempSettings = <?php echo json_encode($settings); ?>;

$(document).ready(function() {

	$('#section-enabled-checkbox').on('click', function() {
		saveButtonToggle();
	});

	$('#form-name-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('#form-email-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('#form-phone-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('#form-message-edit').on('keyup', function(){
		saveButtonToggle();
	});

	$('#contact-form-enable-checkbox').on('change', function(){
		saveButtonToggle();
	});

	$('#send-message-button-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('#contact-header-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('#phone-contact-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('#email-contact-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('#address-contact-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('#contact-location-enable-checkbox').on('change', function() {
		saveButtonToggle();
	});

	$('#contact-location-latitude').on('keyup', function() {
		saveButtonToggle();
	});

	$('#contact-location-longitude').on('keyup', function() {
		saveButtonToggle();
	});

	$('#save-button').on('click', function() {
		saveData();
	});
});

function saveButtonToggle() {
	var pageEnabled = $('#section-enabled-checkbox').prop('checked')?1:0;
	var saveChanges = 0;
	if(settings.contact['page_enabled'] != pageEnabled) {
		tempSettings.contact['page_enabled'] = pageEnabled;
		saveChanges = 1;
	}
	else {
		tempSettings.contact['page_enabled'] = settings.contact['page_enabled'];
	}

	var formName = $('#form-name-edit').val();
	if(settings.contact['form-name'] != formName) {
		tempSettings.contact['form-name'] = formName;
		saveChanges = 1;
	}
	else {
		tempSettings.contact['form-name'] = settings.contact['form-name'];
	}

	var formEmail = $('#form-email-edit').val();
	if(settings.contact['form-email'] != formEmail) {
		tempSettings.contact['form-email'] = formEmail;
		saveChanges = 1;
	}
	else {
		tempSettings.contact['form-email'] = settings.contact['form-email'];
	}

	var formPhone = $('#form-phone-edit').val();
	if(settings.contact['form-phone'] != formPhone) {
		tempSettings.contact['form-phone'] = formPhone;
		saveChanges = 1;
	}
	else {
		tempSettings.contact['form-phone'] = settings.contact['form-phone'];
	}

	var formMessage = $('#form-message-edit').val();
	if(settings.contact['form-message'] != formMessage) {
		tempSettings.contact['form-message'] = formMessage;
		saveChanges = 1;
	}
	else {
		tempSettings.contact['form-message'] = settings.contact['form-message'];
	}

	var formButton = $('#send-message-button-edit').val();
	if(settings.contact['send-message-button'] != formButton) {
		tempSettings.contact['send-message-button'] = formButton;
		saveChanges = 1;
		console.log(settings.contact['send-message-button'] + '::' + formButton);
	}
	else {
		tempSettings.contact['send-message-button'] = settings.contact['send-message-button'];
	}

	var formEnabled = ($('#contact-form-enable-checkbox').is(':checked'))?1:0;
	if(settings.contact['form-enabled'] != formEnabled) {
		tempSettings.contact['form-enabled'] = formEnabled;
		saveChanges = 1;
	}

	var contactHeader = $('#contact-header-edit').val();
	if(settings.contact['contact-header'] != contactHeader) {
		tempSettings.contact['contact-header'] = contactHeader;
		saveChanges = 1;
	}
	else {
		tempSettings.contact['contact-header'] = settings.contact['contact-header'];
	}

	var contactPhone = $('#phone-contact-edit').val();
	if(settings.contact['contact-phone'] != contactPhone) {
		tempSettings.contact['contact-phone'] = contactPhone;
		saveChanges = 1;
	}
	else {
		tempSettings.contact['contact-phone'] = settings.contact['contact-phone'];
	}

	var contactEmail = $('#email-contact-edit').val();
	if(settings.contact['contact-email'] != contactEmail) {
		tempSettings.contact['contact-email'] = contactEmail;
		saveChanges = 1;
	}
	else {
		tempSettings.contact['contact-email'] = settings.contact['contact-email'];
	}

	var contactAddress = $('#address-contact-edit').val();
	if(settings.contact['contact-address'] != contactAddress) {
		tempSettings.contact['contact-address'] = contactAddress;
		saveChanges = 1;
	}
	else {
		tempSettings.contact['contact-address'] = settings.contact['contact-address'];
	}

	var locationEnabled = ($('#contact-location-enable-checkbox').is(':checked'))?1:0;
	if(settings.contact['contact-location-enabled'] != locationEnabled) {
		tempSettings.contact['contact-location-enabled'] = locationEnabled;
		saveChanges = 1;
	}

	var latitude = $('#contact-location-latitude').val();
	if(settings.contact['contact-location-latitude'] != latitude) {
		tempSettings.contact['contact-location-latitude'] = latitude;
		saveChanges = 1;
	}
	else {
		tempSettings.contact['contact-location-latitude'] = settings.contact['contact-location-latitude'];
	}

	var longitude = $('#contact-location-longitude').val();
	if(settings.contact['contact-location-longitude'] != longitude) {
		tempSettings.contact['contact-location-longitude'] = longitude;
		saveChanges = 1;
	}
	else {
		tempSettings.contact['contact-location-longitude'] = settings.contact['contact-location-longitude'];
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
				settings = tempSettings;
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