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
<link href='css/testimonials.css' rel='stylesheet' type='text/css' />
<link href='../css/themes/<?php echo $settings["theme"]; ?>' rel='stylesheet' type='text/css' />
<script type="text/javascript" src="../js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="js/tooltipsy.min.js"></script>
</head>

<body>
<?php require_once('header.php'); ?>	

<div id="error-box"><?php echo __('error_box'); ?></div>
<div id="success-box"><?php echo __('success_box'); ?></div>

<div id="upload-lightbox">
	<div id="upload-button"><?php echo __('upload_lightbox_button'); ?></div>
	<div id="upload-button-message"></div>
	<input type="file" id="upload-file-input" accept="image/*" />
	<img src="img/loader.gif" id="lightbox-loader" />
	<div id="lightbox-error"></div>
	<div id="lightbox-close"><?php echo __('upload_lightbox_close_return_button'); ?></div>
</div>

<div id="app-buttons">
	<div id="app-navigation-buttons">
		<a href="home.php"><?php echo __('menu_home'); ?></a>
		<a href="features.php"><?php echo __('menu_features'); ?></a>
		<a href="testimonials.php" class="app-navigation-button-active"><?php echo __('menu_testimonials'); ?></a>
		<a href="pricing.php"><?php echo __('menu_pricing'); ?></a>
		<a href="contact.php"><?php echo __('menu_contact'); ?></a>
		<a href="footer.php"><?php echo __('menu_footer'); ?></a>
		<a href="mics.php"><?php echo __('menu_mics'); ?></a>
	</div>
	<div id="app-save-button">
		<div id="save-button"><?php echo __('save_changes_button'); ?></div>
	</div>
</div>
<?php
	$checked = $settings['testimonials']['enabled']?'checked':'';
?>
<div id="section-enabled"><div id="section-enabled-text"><input id="section-enabled-checkbox" type="checkbox" <?php echo $checked; ?>/><label for="section-enabled-checkbox"><?php echo __('enable_page_label'); ?></label></div></div>
	
<div id="testimonials" data-present-testimonial="1">
	<div id="testimonials-inner" style="background-image: url('../img/testimonial/<?php echo $settings['testimonials']['background']; ?>')">
		<div id="all-testimonials-outer-container">
			<div id="all-testimonials-inner-container">
			<?php
				for($i=0; $i<5; $i++) {
					$enabled = $settings['testimonials']['testimonial'][$i]['enabled']?'fa-check':'fa-times';

					if(strlen($settings['testimonials']['testimonial'][$i]['text'])>0) {
						$testimonialDisplay = ' style="display: inline-block;"';
						$testimonialEditDisplay = ' style="display: none;"';
					}
					else {
						$testimonialDisplay = ' style="display: none;"';
						$testimonialEditDisplay = ' style="display: inline-block;"';
					}

					if(strlen($settings['testimonials']['testimonial'][$i]['name'])>0) {
						$customerNameDisplay = ' style="display: inline;"';
						$customerNameEditDisplay = ' style="display: none;"';
					}
					else {
						$customerNameDisplay = ' style="display: none;"';
						$customerNameEditDisplay = ' style="display: inline;"';
					}

					if(strlen($settings['testimonials']['testimonial'][$i]['link'])>0) {
						$customerLink = ' style="display: inline;"';
						$customerLinkEdit = ' style="display: none;"';
					}
					else {
						$customerLink = ' style="display: none;"';
						$customerLinkEdit = ' style="display: inline;"';
					}

					if($settings['testimonials']['testimonial'][$i]['image'] != 'default.jpg') {
						$image = $settings['testimonials']['testimonial'][$i]['image'];
						$removeImage = ' style="display: inline;"';
					}
					else {
						$image = 'default.jpg';
						$removeImage = ' style="display: none;"';
					}

			?>
					<div class="testimonial testimonial-active" id="testimonial-<?php echo ($i+1); ?>">
						<div class="testimonial-enabled" data-testimonial-enabled="<?php echo $settings['testimonials']['testimonial'][$i]['enabled']; ?>" title="<?php echo __('testimonials_change_visibility'); ?>"><i class="fa <?php echo $enabled; ?>"></i></div>
						<div class="testimonial-comment">
							<i class="testimonial-quote-left fa fa-quote-left"></i>
							<div class="customer-comment" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $testimonialDisplay; ?>><?php echo $settings['testimonials']['testimonial'][$i]['text']; ?></div>
							<input type="text" class="customer-comment-edit" value="<?php echo $settings['testimonials']['testimonial'][$i]['text']; ?>" <?php echo $testimonialEditDisplay;?> />
							<i class="testimonial-quote-right fa fa-quote-right"></i>
						</div>
						<div class="testimonial-customer-info">
							<i <?php echo $removeImage; ?> class="remove-testimonial-image fa fa-times"></i>
							<img class="testimonial-image" title="<?php echo __('testimonials_change_customer_image'); ?>" src="../img/testimonial/<?php echo $image; ?>" />
							<span class="customer-name" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $customerNameDisplay; ?>><?php echo $settings['testimonials']['testimonial'][$i]['name']; ?></span>
							<input class="customer-name-edit" value="<?php echo $settings['testimonials']['testimonial'][$i]['name']; ?>" <?php echo $customerNameEditDisplay; ?>/>
							<div class="customer-name-enabled" data-name-enabled="<?php echo $settings['testimonials']['testimonial'][$i]['name_enabled']; ?>" title="<?php echo __('customer_name_change_visibility'); ?>"><i class="fa <?php echo $settings['testimonials']['testimonial'][$i]['name_enabled']?'fa-check':'fa-times'; ?>"></i></div>
							<span class="customer-website" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $customerLink; ?>><?php echo $settings['testimonials']['testimonial'][$i]['link']; ?></span>
							<input class="customer-website-edit" <?php echo $customerLinkEdit; ?> />
							<div class="customer-website-enabled" data-website-enabled="<?php echo $settings['testimonials']['testimonial'][$i]['website_enabled']; ?>" title="<?php echo __('customer_website_change_visibility'); ?>"><i class="fa <?php echo $settings['testimonials']['testimonial'][$i]['website_enabled']?'fa-check':'fa-times'; ?>"></i></div>
						</div>
					</div>
			<?php
				}
			?>
			</div>
		</div>
		<div id="slide-controls">
			<div class="slide-control slide-control-active" data-testimonial="1"></div>
			<div class="slide-control" data-testimonial="2"></div>
			<div class="slide-control" data-testimonial="3"></div>
			<div class="slide-control" data-testimonial="4"></div>
			<div class="slide-control" data-testimonial="5"></div>
		</div>
	</div>
	<div id="background-selector">
		<div class="slides-background-image" title="<?php echo __('slider_change_background_image_tooltip'); ?>" data-background-image="<?php echo $settings['testimonials']['background']; ?>"><i class="fa fa-picture-o"></i></div>
	</div>	
</div>

<div id="usage">
	
	<div class="usage-header" style="margin-top:30px"><?php echo __('double_click_legend'); ?></div>
	
</div>


<script type="text/javascript">

/* Initialize Testimonials first slide size */
$(document).ready( function() {
	ResizeTestimonials(1);
});

var settings = <?php echo json_encode($settings); ?>;
var tempSettings = <?php echo json_encode($settings); ?>;

$('.customer-comment, .customer-name, .customer-website, .testimonial-image, .testimonial-enabled, .customer-name-enabled, .customer-website-enabled, .slides-background-image').tooltipsy({
	alignTo: 'element',
    offset: [0, 1]
});

$('').tooltipsy({
	alignTo: 'element',
    offset: [3, 0]
});

$(".testimonial-enabled").on('click', function() { 
	if($(this).attr('data-testimonial-enabled') == 1) {
		$(this).attr('data-testimonial-enabled', 0) 
		$(this).html('<i class="fa fa-times"></i>')
	}
	else if($(this).attr('data-testimonial-enabled') == 0) {
		$(this).attr('data-testimonial-enabled', 1) 
		$(this).html('<i class="fa fa-check"></i>')
	}
});

$(".customer-name-enabled").on('click', function() { 
	if($(this).attr('data-name-enabled') == 1) {
		$(this).attr('data-name-enabled', 0) 
		$(this).html('<i class="fa fa-times"></i>')
	}
	else if($(this).attr('data-name-enabled') == 0) {
		$(this).attr('data-name-enabled', 1) 
		$(this).html('<i class="fa fa-check"></i>')
	}
});

$(".customer-website-enabled").on('click', function() { 
	if($(this).attr('data-website-enabled') == 1) {
		$(this).attr('data-website-enabled', 0) 
		$(this).html('<i class="fa fa-times"></i>')
	}
	else if($(this).attr('data-website-enabled') == 0) {
		$(this).attr('data-website-enabled', 1) 
		$(this).html('<i class="fa fa-check"></i>')
	}
});

$(".testimonial-image").on('click', function() { 
	ShowLightbox('testimonial');
});

$('.slides-background-image').on('click', function() {
	ShowLightbox('background');
});

$("#testimonials-background-image").on('click', function() { 
	ShowLightbox('testimonial-background');
});

$("#upload-button").on('click', function() { 
	$("#upload-file-input").trigger('click');
});

$("#upload-file-input").on('change', function() { 
	var file = $(this).get(0).files[0],
		fd = new FormData(),
		image_type = /image.jpeg|image.png/;
	
	$("#lightbox-error").hide();
	if(!file.type.match(image_type)) {
		$("#lightbox-error").text("<?php echo __('upload_lightbox_format_error'); ?>").show();
		return;
	}
	if(file.size > 500*1024) {
		$("#lightbox-error").text("<?php echo __('upload_lightbox_size_error'); ?>").show();
		return;
	}

	fd.append('image', file);
	fd.append('type', 'testimonial');
	fd.append('_', Math.random());
	
	$.ajax({
		type: 'POST',
		url: 'upload-image.php',
		data: fd,
		dataType: 'JSON',
		success: function (response) {
			if(response.error == 1) {

			}
			else if(response.error == 0) {
				if($("#upload-button").attr('data-type') == 'background') {
					$("#testimonials-inner").css('background-image', 'url("../img/testimonial/' + response.src + '")');
				}				
				if($("#upload-button").attr('data-type') == 'testimonial') {
					$("#testimonial-" + $("#testimonials").attr('data-present-testimonial')).find(".testimonial-image").attr('src', '../img/testimonial/' + response.src).attr('data-testimonial-image', response.src);
					$("#testimonial-" + $("#testimonials").attr('data-present-testimonial')).find(".testimonial-image").prev().show();
				}

				$("#upload-button, #upload-button-message, #lightbox-close").show();
				$("#lightbox-loader").hide();
				$("#upload-lightbox").hide();
			}
		},
		processData: false,
		contentType: false
	});

	$("#upload-button, #upload-button-message, #lightbox-close").hide();
	$("#lightbox-loader").css('display', 'block');
});

$("#lightbox-close").on('click', function() { 
	$("#upload-lightbox").hide();
});

$(".remove-testimonial-image").on('click', function() { 
	$(this).hide();
	$(this).next().attr('src', '../img/testimonial/default.jpg');
});

$(".customer-comment").on('dblclick', function() { 
	$(this).hide();
	$(this).next().val($(this).text()).show();
	
	var id = $(this).parent().parent().attr("id");
	ResizeTestimonials(id.substring(id.length - 1 ,id.length));	
});

$(" .customer-name, .customer-website").on('dblclick', function() { 
	$(this).hide();
	$(this).next().val($(this).text()).show();
});

$(".customer-comment-edit").on('dblclick', function() { 
	if($(this).val() == '') 
		return false;

	$(this).prev().text($(this).val()).show();
	$(this).hide();
	
	var id = $(this).parent().parent().attr("id");
	ResizeTestimonials(id.substring(id.length - 1 ,id.length));
});

$(".customer-name-edit, .customer-website-edit").on('dblclick', function() { 
	if($(this).val() == '') 
		return false;

	$(this).prev().text($(this).val()).show();
	$(this).hide();
});

$(".slide-control").on('click', function() { 
	$(".slide-control").removeClass('slide-control-active');
	$(this).addClass('slide-control-active');

	$("#testimonials").attr('data-present-testimonial', $(this).attr('data-testimonial'));

	$("#all-testimonials-inner-container").css('transform', 'translate(-' + ((parseInt($(this).attr('data-testimonial'), 10)-1)*100) + '%, 0)');

	ResizeTestimonials($(this).attr('data-testimonial'));
});

function ShowLightbox(type) {
	$("#upload-lightbox").height($(document).height() + $(document).scrollTop()).show();
	
	if(type == 'background') {
		$("#upload-button").text("<?php echo __('upload_lightbox_background_button'); ?>").attr('data-type', type);
		$("#upload-button-message").html("<?php echo __('upload_lightbox_background_usage'); ?>")
	}
	if(type == 'testimonial') {
		$("#upload-button").text("<?php echo __('testimonials_change_image_button'); ?>").attr('data-type', type);
		$("#upload-button-message").html("<?php echo __('testimonials_low_size_message'); ?>")
	}
}

/*	Save data 	*/

$(document).ready(function() {
	$('#section-enabled-text').click(function() {
		saveButtonToggle();
	});

	$('.testimonial-enabled').click(function(){
		saveButtonToggle();
	});

	$('.customer-comment-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('.customer-name-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('.customer-name-enabled, .customer-website-enabled').on('click', function() {
		saveButtonToggle();
	});

	$('.customer-website-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('#save-button').on('click', function() {
		saveData();
	});

	$('.remove-testimonial-image').on('click', function() {
		imageChange();
		saveButtonToggle();
	});

});

$(document).ajaxComplete(function() {	
	imageChange();
});

function imageChange() {
	var str = $('#testimonials-inner').css('background-image');
	var url = str.replace(/(url\(\"|\"\))/gi, '');
	var background = url.split("/");

	tempSettings.testimonials['background'] = background[(background.length)-1];

	for(i=0; i<5; i++) {
		var str = $('.testimonial-image').eq(i).attr('src');
		var url = str.replace(/(url\(\"|\"\))/gi, '');
		var img = url.split("/");
		tempSettings.testimonials.testimonial[i]['image'] = img[(img.length)-1];
		if(img[(img.length)-1] != 'default.jpg') {
			$('.remove-testimonial-image').eq(i).css('display', 'inline');
		}
	}
	saveButtonToggle();
}

function saveButtonToggle() {
	var saveChanges = 0;
	
	var pageEnabled = parseInt($('#section-enabled-checkbox').attr('checked')?1:0);
	if(pageEnabled != settings.testimonials.enabled) {
		tempSettings.testimonials.enabled = pageEnabled;
		saveChanges = 1;
	}
	else {
		tempSettings.testimonials.enabled = settings.testimonials.enabled;
	}

	if(settings.testimonials['background'] != tempSettings.testimonials['background']) {
		saveChanges = 1;
	}

	for(i=0; i<5; i++) {
		var testimonialEnabled = parseInt($('.testimonial').eq(i).find('.testimonial-enabled').attr('data-testimonial-enabled'));
		if(testimonialEnabled != settings.testimonials.testimonial[i]['enabled']) {
			tempSettings.testimonials.testimonial[i]['enabled'] = testimonialEnabled;
			saveChanges = 1;
		}

		var text = ($('.customer-comment-edit').eq(i).css('display')=='block')?$('.customer-comment-edit').eq(i).val():$('.customer-comment').eq(i).html();
		if(text != settings.testimonials.testimonial[i]['text']) {
			tempSettings.testimonials.testimonial[i]['text'] = text;
			saveChanges = 1;
		}

		if(tempSettings.testimonials.testimonial[i]['image'] != settings.testimonials.testimonial[i]['image']) {
			saveChanges = 1;
		}

		var name = ($('.customer-name-edit').eq(i).css('display')=='inline')?$('.customer-name-edit').eq(i).val():$('.customer-name').eq(i).html();
		if(name != settings.testimonials.testimonial[i]['name']) {
			tempSettings.testimonials.testimonial[i]['name'] = name;
			saveChanges = 1;
		}

		var link = ($('.customer-website-edit').eq(i).css('display')=='inline')?$('.customer-website-edit').eq(i).val():$('.customer-website').eq(i).html();
		if(link != settings.testimonials.testimonial[i]['link']) {
			tempSettings.testimonials.testimonial[i]['link'] = link;
			saveChanges = 1;
		}

		var nameEnabled = parseInt($('.testimonial').eq(i).find('.customer-name-enabled').attr('data-name-enabled'));
		if(nameEnabled != settings.testimonials.testimonial[i]['name_enabled']) {
			tempSettings.testimonials.testimonial[i]['name_enabled'] = nameEnabled;
			saveChanges = 1;
		}

		var websiteEnabled = parseInt($('.testimonial').eq(i).find('.customer-website-enabled').attr('data-website-enabled'));
		if(websiteEnabled != settings.testimonials.testimonial[i]['website_enabled']) {
			tempSettings.testimonials.testimonial[i]['website_enabled'] = websiteEnabled;
			saveChanges = 1;
		}		
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

function ResizeTestimonials(next_slide) {
	/* mmoreyra: resize testimonials slider depending on the slide size */
	var slide_height = parseInt($("#testimonial-"+(next_slide)).css('height').substring(0, $("#testimonial-"+(next_slide)).css('height').length -2));
	if(slide_height > 200) {
		$("#testimonials").css('height', (slide_height + 200) + "px");
	} else {
		$("#testimonials").css('height', '450px');
	}	
}

</script>

</body>
</html>