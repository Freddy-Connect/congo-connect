<?php
	session_start();
	if(file_exists("credentials.php")) {
		require_once('credentials.php');
		if(strlen(Email)>0 && strlen(Password)>0) {
			
		}
		else {
			header('Location: install.php');	
		}
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
<link href='css/mics.css' rel='stylesheet' type='text/css' />
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
		<a href="footer.php"><?php echo __('menu_footer'); ?></a>
		<a href="mics.php" class="app-navigation-button-active"><?php echo __('menu_mics'); ?></a>
	</div>
	<div id="app-save-button">
		<div id="save-button"><?php echo __('save_changes_button'); ?></div>
	</div>
</div>
	
<div id="mics">
	<div class="mics-section">
		<div class="mics-section-header"><?php echo __('mics_theme_label'); ?></div>
		<select id="theme-select">
<option value="black-theme.css"><?php echo __('mics_theme_black_option'); ?></option>
<option value="blue-theme.css"><?php echo __('mics_theme_blue_option'); ?></option>
<option value="darkblue-theme.css"><?php echo __('mics_theme_darkblue_option'); ?></option>
<option value="gray-theme.css"><?php echo __('mics_theme_gray_option'); ?></option>
<option value="green-theme.css"><?php echo __('mics_theme_green_option'); ?></option>
<option value="gold-theme.css"><?php echo __('mics_theme_gold_option'); ?></option>
<option value="lime-theme.css"><?php echo __('mics_theme_lime_option'); ?></option>
<option value="orange-theme.css"><?php echo __('mics_theme_orange_option'); ?></option>
<option value="pink-theme.css"><?php echo __('mics_theme_pink_option'); ?></option>
<option value="purple-theme.css"><?php echo __('mics_theme_purple_option'); ?></option>	
<option value="red-theme.css"><?php echo __('mics_theme_red_option'); ?></option>
<option value="yellow-theme.css"><?php echo __('mics_theme_yellow_option'); ?></option>
		</select>
	</div>
	<div class="mics-section">

		<div class="mics-section-header"><?php echo __('mics_admin_login_label'); ?></div>
		<div class="form-element">
			<label><?php echo __('mics_email_label'); ?></label>
			<input type="hidden" id="admin-email" value="<?php echo Email; ?>"/>
		</div>
		<div class="form-element">
			<label><?php echo __('mics_new_password_label'); ?></label>
			<input type="hidden" id="admin-password" />
		</div>
		<div class="form-element">
			<label><?php echo __('mics_confirm_password_label'); ?></label>
			<input type="hidden" id="admin-confirm-password" />
		</div>
	</div>
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
	if($(this).val() == '') 
		return false;

	$(this).hide();
});

$(".footer-message span").on('dblclick', function() { 
	$(this).hide();
	$(this).next().show();
}); 

$(".footer-message input[type='text']").on('dblclick', function() { 
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
var setPassword = 1;
var setEmail = 1;
var email = '<?php echo Email;?>';
var password = '<?php echo Password; ?>';
var newPassword = password;
var newEmail = email;

$(document).ready(function() {
	if(settings.theme != '' || settings.theme != 'undefined') {
		$('#theme-select').val(settings.theme);
	}

	$('#theme-select').change(function() {
		saveButtonToggle();
	});

	$('#admin-email').on('keyup', function() {
		saveButtonToggle();
	});

	$('#admin-password').on('keyup', function() {
		$('#app-save-button').fadeIn('slow');/*var regExBlank = /^((\s)+)$/i;
		if(($('#admin-confirm-password').val() == $('#admin-password').val()) && regExBlank.test($('#admin-confirm-password').val())) {
			$('#error-box').html('Password cannot be only spaces.').fadeIn('slow').delay(1000).fadeOut('slow');
		}

		var regEx = /^(((\s)+)([\w|\W]+)((\s)+))$/i;
		if(($('#admin-confirm-password').val() == $('#admin-password').val()) && regEx.test($('#admin-confirm-password').val())) {
			$('#error-box').html('Password cannot start or/and end with whitespaces.').fadeIn('slow').delay(1000).fadeOut('slow');
		}

		var regEx = /(((\S)+(\S|\s)+(\S)+){2,})/i;
		if(regEx.test($('#admin-confirm-password').val())) {
			if($('#admin-confirm-password').val() == $('#admin-password').val()) {
				$('#error-box').fadeOut('slow');
				setPassword = 1;
				newPassword = $('#admin-confirm-password').val();
				saveButtonToggle();
			}
			else {
				$('#error-box').html('Passwords do not match.').fadeIn('slow');
				setPassword = 0;
				newPassword = password;
				saveButtonToggle();
			}
		}
		else {
			setPassword = 0;
			newPassword = password;
			saveButtonToggle();
		}*/
	});

	$('#save-button').on('click', function() {
		saveData();
	});
});

function saveButtonToggle() {
	var saveChanges = 0;
	var regExEmail = /^([a-zA-z0-9]{1,}(?:([\._-]{0,1}[a-zA-Z0-9]{1,}))+@{1}([a-zA-Z0-9-]{2,}(?:([\.]{1}[a-zA-Z]{2,}))+))$/;

	var theme = $('#theme-select').val();
	if(settings.theme != theme) {
		tempSettings.theme = theme;
		saveChanges = 1;
	}
	else {
		tempSettings.theme = settings.theme;
	}

	if($('#admin-email').val() != email && regExEmail.test($('#admin-email').val())) {
		saveChanges = 1;
		setEmail = 1;
		newEmail = $('#admin-email').val();
	}

	if(!regExEmail.test($('#admin-email').val())) {
		$('#error-box').html("<?php echo __('mics_proper_email_error'); ?>").fadeIn('slow').delay(1000).fadeOut('slow');
	}

	if(saveChanges == 1) {
		$('#app-save-button').fadeIn('slow');
	}
	else {
		$('#app-save-button').fadeOut('slow');
	}
}

function saveData() {
	
	if($('#admin-confirm-password').val().length>0 || $('#admin-password').val().length>0) {

		if($('#admin-password').val().length<6) {
			$('#error-box').html("<?php echo __('mics_less_than_6_error'); ?>").fadeIn('slow').delay(2000).fadeOut('slow');
			return false;
		}

		if($('#admin-confirm-password').val() != $('#admin-password').val()) {
			$('#error-box').html("<?php echo __('mics_doesnt_match_error'); ?>").fadeIn('slow').delay(2000).fadeOut('slow');
			return false;
		}
		var regExBlank = /^((\s)+)$/i;
		if(($('#admin-confirm-password').val() == $('#admin-password').val()) && regExBlank.test($('#admin-confirm-password').val())) {
			$('#error-box').html("<?php echo __('mics_only_spaces_error'); ?>").fadeIn('slow').delay(1000).fadeOut('slow');
			return false;
		}


		var regEx1 = /^(((\s)+)([\w|\W]+))$/i;
		var regEx2 = /^(([\w|\W]+)((\s)+))$/i;
		if(($('#admin-confirm-password').val() == $('#admin-password').val()) && regEx1.test($('#admin-confirm-password').val()) || regEx2.test($('#admin-confirm-password').val())) {
			$('#error-box').html("<?php echo __('mics_start_end_spaces_error'); ?>").fadeIn('slow').delay(1000).fadeOut('slow');
			return false;
		}

		/* Replaced with the code above because it wasn't working */
		/*
		var regEx = /^(((\s)+)([\w|\W]+)((\s)+))$/i;
		if(($('#admin-confirm-password').val() == $('#admin-password').val()) && regEx.test($('#admin-confirm-password').val())) {
			$('#error-box').html("<?php echo __('mics_start_end_spaces_error'); ?>").fadeIn('slow').delay(1000).fadeOut('slow');
			return false;
		}
		*/

		var regEx = /(((\S)+(\S|\s)+(\S)+){2,})/i;
		if(regEx.test($('#admin-confirm-password').val())) {
			if($('#admin-confirm-password').val() == $('#admin-password').val()) {
				$('#error-box').fadeOut('slow');
				setPassword = 1;
				newPassword = $('#admin-confirm-password').val();
			}
			else {
				$('#error-box').html("<?php echo __('mics_doesnt_match_error'); ?>").fadeIn('slow');
				setPassword = 0;
				newPassword = password;
				return false;
			}
		}
		else {
			setPassword = 0;
			newPassword = password;
			return false;
		}
	}

	var theme = $('#theme-select').val();
	if(setPassword || setEmail) {
		var email = newEmail;
		var password = newPassword;
		payload = {'payload': tempSettings, 'email': email, 'password': password};
	}
	else {
		payload = {'payload': tempSettings};
	}

	$('#save-button').css('opacity', '0.65');
	$.ajax({
		url: 'control.php',
		type:'POST',
		dataType: 'json',
		data: payload,
		success: function(response) {
			$('#save-button').css('opacity', '1.0');
			if(response.error == 0) {
				$('#success-box').slideDown('slow').delay(1500).slideUp('slow');
				settings = JSON.parse(JSON.stringify(tempSettings));
				$('#app-save-button').fadeOut('slow');
			}
			else {
				$('#error-box').html("<?php echo __('error_box'); ?>").slideDown('slow').delay(1500).slideUp('slow');	
			}
		}
	});
}
</script>

</body>
</html>