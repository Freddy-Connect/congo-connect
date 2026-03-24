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

<?php 
	if($settings['home']['page-title'] != "") :
?>
<title><?php echo $settings['home']['page-title']; ?></title>
<?php 
	endif;

	if($settings['home']['page-description'] != "") :
?>
<meta name="description" content="<?php echo $settings['home']['page-description']; ?>" />
<?php
	endif;
?>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300' rel='stylesheet' type='text/css' />
<link href='../css/font-awesome.css' rel='stylesheet' type='text/css' />
<link href='css/admin.css' rel='stylesheet' type='text/css' />
<link href='css/home.css' rel='stylesheet' type='text/css' />
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
	<div id="upload-title"><label for="upload-image-title"><?php echo __('image_title_label'); ?></label><input id="upload-image-title" class="lightbox-text" type="text" value="<?php echo $settings['logo-title'];?>"/></div>	
	<div id="upload-alt"><label for="upload-image-alt"><?php echo __('image_alt_label'); ?></label><input id="upload-image-alt" class="lightbox-text" type="text" value="<?php echo $settings['logo-alt'];?>"/></div>
	<div id="lightbox-error"></div>
	<div id="lightbox-close"><?php echo __('upload_lightbox_close_return_button'); ?></div>
</div>

<div id="app-buttons">
	<div id="app-navigation-buttons">
		<a href="home.php" class="app-navigation-button-active"><?php echo __('menu_home'); ?></a>
		<a href="features.php"><?php echo __('menu_features'); ?></a>
		<a href="testimonials.php"><?php echo __('menu_testimonials'); ?></a>
		<a href="pricing.php"><?php echo __('menu_pricing'); ?></a>
		<a href="contact.php"><?php echo __('menu_contact'); ?></a>
		<a href="footer.php"><?php echo __('menu_footer'); ?></a>
		<a href="mics.php"><?php echo __('menu_mics'); ?></a>
	</div>
	<div id="app-save-button">
		<div id="save-button"><?php echo __('save_changes_button'); ?></div>
	</div>
</div>
	
<div id="page-seo">
	<div class="page-seo-text"><label><?php echo __('seo_page_title'); ?></label><input id="page-title" type="text" value="<?php echo $settings['home']['page-title'];?>" placeholder="<?php echo __('seo_page_title_hint'); ?>"/></div>
	<div class="page-seo-text"><label><?php echo __('seo_page_description'); ?></label><input id="page-description" type="text" value="<?php echo $settings['home']['page-description'];?>" maxlength="156" placeholder="<?php echo __('seo_page_description_hint'); ?>"/></div>
</div>

<div id="section-options">
	<div class="section-option-text">
		<input id="slider-show" type="checkbox" <?php echo ($settings['home']['show-slider']=='1') ? 'checked' : ''; ?> /><label for="slider-show">Show Slider </label>
	</div>
	<div id="slider-options" class="section-option-text">
		<?php
			$autoplay = $settings['home']['slider-autoplay']?'checked':'';
		?>
		<input id="slider-autoplay" type="checkbox" <?php echo $autoplay; ?>/>
		<label for="slider-autoplay"><?php echo __('sliders_autoplay_with_label'); ?> </label>
		<select id="slider-interval" onchange='saveButtonToggle();'>
			<?php
				for($i=1; $i<=10; $i++) {
					$selected = ($i==$settings['home']['slider-interval'])?'selected':'';
					echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
				}
			?>
		</select> <?php echo __('sliders_autoplay_seconds_label'); ?>
	</div>
</div>

<div id="home" data-present-slide="1" style="display:<?php echo ($settings['home']['show-slider']=='1') ? 'block' : 'none'; ?>">
	<div id="header-container">
		<div id="header-inner">
			<div id="logo-container">
				<img src="../img/logo/<?php echo $settings['logo']; ?>" title="<?php echo __('home_change_website_logo_tooltip'); ?>" />
			</div>
			<div id="navigation-container" class="theme-home-text">
			<?php
				if($settings['features']['enabled']) {?>
				<div class="navigation-link theme-link"><?php echo __('menu_features'); ?></div>
			<?php }
				if($settings['testimonials']['enabled']) {?>
				<div class="navigation-link theme-link"><?php echo __('menu_testimonials'); ?></div>
			<?php }
				if($settings['pricing']['enabled']) {?>
				<div class="navigation-link theme-link"><?php echo __('menu_pricing'); ?></div>
			<?php }
				if($settings['contact']['page_enabled']) {?>
				<div class="navigation-link theme-link"><?php echo __('menu_contact'); ?></div>
			<?php	} ?>
				<div class="header-enabled" data-header-enabled="<?php echo $settings['nav_enabled']; ?>" title="<?php echo __('header_change_visibility_tooltip'); ?>"><i class="fa <?php echo ($settings['nav_enabled']==1)?'fa-check':'fa-times'; ?>"></i></div>
			</div>
		</div>
	</div>
	<div id="slide-prev"><i class="fa fa-chevron-circle-left"></i></div>
	<div id="slide-next"><i class="fa fa-chevron-circle-right"></i></div>	
	<div id="all-slides-container">
	<?php
		$slideActivated = false;
		for($i=0; $i<4; $i++) {
			$filter = ($settings['home']['slide'][$i]['filter']==1)?'block':'none';
			$slideEnabled = ($settings['home']['slide'][$i]['enabled']==1)?'fa-check':'fa-times';
			$buttonEnabled = ($settings['home']['slide'][$i]['button-enabled']==1)?'fa-check':'fa-times';
			$slideActive = ($settings['home']['slide'][$i]['enabled']==1 && !$slideActivated)?'slide-active ':'';
			if($slideActive == 'slide-active ') {
				$slideActivated = TRUE;
			}
			
			if(strlen($settings['home']['slide'][$i]['header'])>0) {
				$header = ' style="display: block";';
				$headerEdit = ' style="display: none";';
			}
			else {
				$header = ' style="display: none";';
				$headerEdit = ' style="display: block";';
			}

			if(strlen($settings['home']['slide'][$i]['sub-header'])>0) {
				$subHeaderDisplay = ' style="display: block";';
				$subHeaderEditDisplay = ' style="display: none";';
			}
			else {
				$subHeaderDisplay = ' style="display: none";';
				$subHeaderEditDisplay = ' style="display: block";';
			}

			if(strlen($settings['home']['slide'][$i]['button-url'])>0 && strlen($settings['home']['slide'][$i]['button-name'])>0) {
				$urlLink = ' style="display: block";';
				$urlLinkEdit = ' style="display: none";';
			}
			else {
				$urlLink = ' style="display: none";';
				$urlLinkEdit = ' style="display: block";';
			}

			echo '
			<div class="slide ' . $slideActive . 'theme-home-text" id="slide-' . ($i+1) . '">
				<div class="slide-background" style="background-image: url(\'../img/background/' . $settings['home']['slide'][$i]['background'] . '\')"></div>
				
				<div class="home-background" data-visible="' . $settings['home']['slide'][$i]['filter'] . '" style="background-image: url(\'../img/home-bg.png\');display: ' . $filter . ';"></div>
				<div class="slide-container">
					<div class="slide-other-buttons">
						<div class="slide-enabled" data-slide-enabled="' . $settings['home']['slide'][$i]['enabled'] . '" title="'. __('slider_change_slide_visibility_tooltip') .'"><i class="fa ' . $slideEnabled . '"></i></div>
						<div class="slide-background-image" title="'. __('slider_change_background_image_tooltip') .'" data-background-image="' . $settings['home']['slide'][$i]['background'] . '"><i class="fa fa-picture-o"></i></div>
						<div class="slide-filter" title="'. __('slider_change_image_filter_tooltip') .'"><i class="fa fa-square"></i></div>
					</div>
					<h1 class="slide-header-1" title="'. __('double_click_tooltip') .'" ' . $header .  '>' . $settings['home']['slide'][$i]['header'] . '</h1>
					<input type="text" class="slide-header-1-edit" ' . $headerEdit . '/>
					<h2 class="slide-header-2" title="'. __('double_click_tooltip') .'" ' . $subHeaderDisplay . '>' . $settings['home']['slide'][$i]['sub-header'] . '</h2>
					<input type="text" class="slide-header-2-edit" ' . $subHeaderEditDisplay . '/>
					<div class="slide-button theme-button" data-link="' . $settings['home']['slide'][$i]['button-url'] . '" title="'. __('double_click_tooltip') .'" ' . $urlLink . '>' . $settings['home']['slide'][$i]['button-name'] . '</div>
					<div class="slide-button-edit" ' . $urlLinkEdit . '>
						<input type="text" class="slide-button-name" />
						<input type="text" class="slide-button-link" />
					</div>
					<div class="slide-button-other-buttons">
						<div class="slide-button-enabled" data-slide-button-enabled="' . $settings['home']['slide'][$i]['button-enabled'] . '" title="'. __('slider_button_change_slide_visibility_tooltip') .'"><i class="fa ' . $buttonEnabled . '"></i></div>
					</div>
				</div>
			</div>';
		}
	?>
	</div>
</div>

<div id="home-custom" style="display:<?php echo ($settings['home']['show-slider']=='1') ? 'none' : 'block'; ?>">
	<div id="header-container">
		<div id="header-inner">
			<div id="logo-container">
				<img src="../img/logo/<?php echo $settings['logo']; ?>" title="<?php echo __('home_change_website_logo_tooltip'); ?>" />
			</div>
			<div id="navigation-container" class="theme-home-text">
			<?php
				if($settings['features']['enabled']) {?>
				<div class="navigation-link theme-link"><?php echo __('menu_features'); ?></div>
			<?php }
				if($settings['testimonials']['enabled']) {?>
				<div class="navigation-link theme-link"><?php echo __('menu_testimonials'); ?></div>
			<?php }
				if($settings['pricing']['enabled']) {?>
				<div class="navigation-link theme-link"><?php echo __('menu_pricing'); ?></div>
			<?php }
				if($settings['contact']['page_enabled']) {?>
				<div class="navigation-link theme-link"><?php echo __('menu_contact'); ?></div>
			<?php	} ?>
			</div>
		</div>
	</div>	
	<div id="home-custom-hint" style="display:<?php echo ($settings['home']['custom-html'] != '') ? 'none' : 'block' ?>"><?php echo __('home_custom_html_hint'); ?></div>
	<div id="home-custom-html" style="display:<?php echo ($settings['home']['custom-html'] != '') ? 'block' : 'none' ?>"><?php echo ($settings['home']['custom-html'] != '') ? $settings['home']['custom-html'] : '' ?></div>
	<textarea id="home-custom-edit"></textarea>
</div>

<div id="usage">
	
	<div class="usage-header" style="margin-top:30px"><?php echo __('double_click_legend'); ?></div>
	
</div>


<script type="text/javascript">

$("#home").css('height', ($(window).height() + $("#app-buttons").outerHeight()) + 'px');
$("#home-custom").css('height', ($(window).height() + $("#app-buttons").outerHeight()) + 'px');

$("#slider-show").change(function() {
	$("#slider-options").toggle(this.checked);
	$("#home").toggle(this.checked);
	$("#home-custom").toggle(!this.checked);
});

$("#home-custom-hint").on('dblclick', function() {
	$(this).hide();
	$('#home-custom-edit').css('display','block');
	$('#home-custom-edit').focus();
});

$("#home-custom-edit").on('dblclick', function() {
	$(this).hide();
	if($(this).val() == '') {
		$('#home-custom-hint').show();
	} else {
		$('#home-custom-html').html($(this).val()).show();
	}
});

$("#home-custom-html").on('dblclick', function() {
	$(this).hide();
	$('#home-custom-edit').val($(this).html());
	$('#home-custom-edit').css('display','block');
	$('#home-custom-edit').focus();
});


$('.slide-header-1, .slide-header-2, .slide-button, #logo-container img').tooltipsy({
	alignTo: 'element',
    offset: [0, 1]
});

$('.slide-enabled, .slide-background-image, .slide-filter, .slide-button-enabled, .header-enabled').tooltipsy({
	alignTo: 'element',
    offset: [0, -3]
});

$(".header-enabled").on('click', function() {
	if($(this).attr('data-header-enabled') == 1) {
		$(this).attr('data-header-enabled', 0) 
		$(this).html('<i class="fa fa-times"></i>')
	}
	else if($(this).attr('data-header-enabled') == 0) {
		$(this).attr('data-header-enabled', 1) 
		$(this).html('<i class="fa fa-check"></i>')
	}
});

$(".slide-enabled").on('click', function() { 
	if($(this).attr('data-slide-enabled') == 1) {
		$(this).attr('data-slide-enabled', 0) 
		$(this).html('<i class="fa fa-times"></i>')
	}
	else if($(this).attr('data-slide-enabled') == 0) {
		$(this).attr('data-slide-enabled', 1) 
		$(this).html('<i class="fa fa-check"></i>')
	}
});

$(".slide-button-enabled").on('click', function() { 
	if($(this).attr('data-slide-button-enabled') == 1) {
		$(this).attr('data-slide-button-enabled', 0) 
		$(this).html('<i class="fa fa-times"></i>')
	}
	else if($(this).attr('data-slide-button-enabled') == 0) {
		$(this).attr('data-slide-button-enabled', 1) 
		$(this).html('<i class="fa fa-check"></i>')
	}
});

$("#logo-container img").on('click', function() { 
	ShowLightbox('logo');
});

$(".slide-background-image").on('click', function() { 
	ShowLightbox('background');
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
	fd.append('type', $("#upload-button").attr('data-type'));
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
					$("#slide-" + $("#home").attr('data-present-slide')).find('.slide-background').css('background-image', 'url("../img/background/' + response.src + '")');
					$("#slide-" + $("#home").attr('data-present-slide')).find(".slide-background-image").attr('data-background-image', response.src);
				}
				else if($("#upload-button").attr('data-type') == 'logo') {
					$("#logo-container img").attr('src', '../img/logo/' + response.src).attr('data-logo', response.src);
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

$(".slide-filter").on('click', function() { 
	var slide_filter = $("#slide-" + $("#home").attr('data-present-slide')).find(".home-background");
	if(slide_filter.attr('data-visible') == 1) {
		slide_filter.hide();
		slide_filter.attr('data-visible', 0);
	}
	else {
		slide_filter.show()
		slide_filter.attr('data-visible', 1)
	}
});

$(".slide-header-1").on('dblclick', function() { 
	$(this).hide();
	$(this).next().val($(this).text()).css('display', 'block');
});

$(".slide-header-1-edit").on('dblclick', function() { 
	if($(this).val() == '') 
		return false;

	$(this).prev().text($(this).val()).show();
	$(this).hide();
});

$(".slide-header-2").on('dblclick', function() { 
	$(this).hide();
	$(this).next().val($(this).text()).css('display', 'block');
});

$(".slide-header-2-edit").on('dblclick', function() { 
	if($(this).val() == '') 
		return false;

	$(this).prev().text($(this).val()).show();
	$(this).hide();
});

$(".slide-button").on('dblclick', function() { 
	$(this).hide();

	$(this).next().show();
	$(this).next().find(".slide-button-name").val($(this).text());
	$(this).next().find(".slide-button-link").val($(this).attr('data-link'));
});

$(".slide-button-name").on('dblclick', function() { 
	if($(this).val() == '') 
		return false;

	$(this).parent().hide();
	$(this).parent().prev().show();
	$(this).parent().prev().text($(this).val()).attr('data-link', $(this).next().val());
});

$(".slide-button-link").on('dblclick', function() { 
	if($(this).prev().val() == '') 
		return false;

	$(this).parent().hide();
	$(this).parent().prev().show();
	$(this).parent().prev().text($(this).prev().val()).attr('data-link', $(this).val());
});

$("#slide-next").on('click', function() { 
	var present_slide = parseInt($("#all-slides-container").children(".slide-active").index(), 10) + 1,
		total_slides = $(".slide").length;

	if(present_slide != total_slides) {
		next_slide = present_slide + 1;
	}
	else {
		next_slide = 1;
	}

	$("#slide-" + present_slide).removeClass('slide-active');
	$("#slide-" + next_slide).addClass('slide-active');

	$("#all-slides-container").css('transform', 'translate(-' + ((next_slide-1)*100) + '%, 0)');

	$("#home").attr('data-present-slide', next_slide);
});

$("#slide-prev").on('click', function() { 
	var present_slide = parseInt($("#all-slides-container").children(".slide-active").index(), 10) + 1,
		total_slides = $(".slide").length;

	if(present_slide != 1) {
		next_slide = present_slide - 1;
	}
	else {
		next_slide = total_slides;
	}

	$("#slide-" + present_slide).removeClass('slide-active');
	$("#slide-" + next_slide).addClass('slide-active');

	$("#all-slides-container").css('transform', 'translate(-' + ((next_slide-1)*100) + '%, 0)');

	$("#home").attr('data-present-slide', next_slide);
});

function ShowLightbox(type) {
	$("#upload-lightbox").height($(document).height() + $(document).scrollTop()).show();

	if(type == 'background') {
		$("#upload-button").text("<?php echo __('upload_lightbox_background_button'); ?>").attr('data-type', type);
		$("#upload-button-message").html("<?php echo __('upload_lightbox_background_usage'); ?>")
		$("#upload-title, #upload-alt").hide();
	}
	else if(type == 'logo') {
		$("#upload-button").text("<?php echo __('upload_lightbox_logo_button'); ?>").attr('data-type', type);
		$("#upload-button-message").html("<?php echo __('upload_lightbox_logo_usage'); ?>")
		$("#upload-title, #upload-alt").show();
	}
}

/******************************************************************************************************
*																									  *
*											Save Data function										  *
*																									  *
*******************************************************************************************************/
var settings = <?php echo json_encode($settings); ?>;
var tempSettings = <?php echo json_encode($settings); ?>;

$(document).ready(function() {

	$('#page-title').on('keyup', function() {
		saveButtonToggle();
	});

	$('#page-description').on('keyup', function() {
		saveButtonToggle();
	});	

	$("#slider-show").change(function() {
		saveButtonToggle();
	});

	$('#home-custom-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('#home-custom-edit').on('dblclick', function() {
		saveButtonToggle();
	});	

	$('#slider-autoplay').click(function() {
		saveButtonToggle();
	});

	$('.header-enabled').click(function() {
		saveButtonToggle();
	});

	$('.slide-enabled').click(function() {
		saveButtonToggle();
	});

	$('.slide-button-enabled').click(function() {
		saveButtonToggle();
	});	

	$('.slide-filter').click(function() {
		saveButtonToggle();
	});

	$('.slide-header-1-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('.slide-header-1-edit').on('dblclick', function() {
		saveButtonToggle();
	});

	$('.slide-header-2-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('.slide-header-2-edit').on('dblclick', function() {
		saveButtonToggle();
	});

	$('.slide-button-name').on('keyup', function() {
		saveButtonToggle();
	});

	$('.slide-button-link').on('keyup', function() {
		saveButtonToggle();
	});

	$('#upload-image-title').on('keyup', function() {
		saveButtonToggle();
	});

	$('#upload-image-alt').on('keyup', function() {
		saveButtonToggle();
	});	

	$('#save-button').on('click', function() {
		saveData();
	});

});

$(document).ajaxComplete(function() {
	
	var str = $("#logo-container").find('img').attr('src');
	var logo = str.split('/');
	tempSettings['logo'] = logo[(logo.length)-1];

	for(i=0; i<4; i++) {
		var str = $('.slide-background').eq(i).css('background-image');
		var url = str.replace(/(url\(|\))/gi, '');
		url = url.replace(/(\"|\')/g, '');
		var img = url.split("/");
		tempSettings.home.slide[i]['background'] = img[(img.length)-1];
	}
	saveButtonToggle();
});

function saveButtonToggle() {
	var saveChanges = 0;

	var str = $("#logo-container").find('img').attr('src');
	var logo = str.split('/');
	tempSettings['logo'] = logo[(logo.length)-1];

	for(i=0; i<4; i++) {
		if(settings.home.slide[i]['background'] != tempSettings.home.slide[i]['background']) {
			saveChanges = 1;
		}
	}

	if(tempSettings['logo'] != settings['logo']) {
		saveChanges = 1;
	}

	var pageTitle = $('#page-title').val();
	if(pageTitle != settings['home']['page-title']) {
		tempSettings['home']['page-title'] = pageTitle;
		saveChanges = 1;
	}

	var pageDescription = $('#page-description').val();
	if(pageDescription != settings['home']['page-description']) {
		tempSettings['home']['page-description'] = pageDescription;
		saveChanges = 1;
	}	

	var imageTitle = $('#upload-image-title').val();
	if(imageTitle != settings['logo-title']) {
		tempSettings['logo-title'] = imageTitle;
		saveChanges = 1;
	}

	var imageAlt = $('#upload-image-alt').val();
	if(imageAlt != settings['logo-alt']) {
		tempSettings['logo-alt'] = imageAlt;
		saveChanges = 1;
	}

	var headerEnabled = $('.header-enabled').attr('data-header-enabled');
	if(headerEnabled != settings['nav_enabled']) {
		tempSettings['nav_enabled'] = headerEnabled;
		saveChanges = 1;
	}

	var showSlider = ($('#slider-show').is(':checked')) ? 1 : 0;
	if(showSlider != settings['home']['show-slider']) {
		tempSettings.home['show-slider'] = showSlider;
		saveChanges = 1;
	}

	var homeCustomHTML = ($('#home-custom-hint').css('display')=='block')?$('#home-custom-hint').html():$('#home-custom-edit').val();
	if(homeCustomHTML != settings['home']['custom-html'] && homeCustomHTML != '<?php echo __("home_custom_html_hint"); ?>') {
		tempSettings.home['custom-html'] = homeCustomHTML;
		saveChanges = 1;
	}		

	var slideAutoplay = $('#slider-autoplay').prop('checked')?1:0;
	if(slideAutoplay != settings['home']['slider-autoplay']) {
		tempSettings.home['slider-autoplay'] = parseInt(slideAutoplay);
		saveChanges = 1;
	}
		
	var sliderInterval = $('#slider-interval').val();
	if(sliderInterval != settings['home']['slider-interval']) {
		tempSettings.home['slider-interval'] = parseInt(sliderInterval);
		saveChanges = 1;
	}

	for(i=0; i<4; i++) {
		var slideEnabled = $('.slide').eq(i).find('.slide-enabled').attr('data-slide-enabled');
		if(slideEnabled != settings['home']['slide'][i]['enabled']) {
			tempSettings.home.slide[i]['enabled'] = slideEnabled;
			saveChanges = 1;
		}

		var filter = $('.slide').eq(i).find('.home-background').attr('data-visible');
		if(filter != settings['home']['slide'][i]['filter']) {
			tempSettings.home.slide[i]['filter'] = filter;
			saveChanges = 1;
		}

		if(tempSettings['home']['slide'][i]['background'] != settings['home']['slide'][i]['background']) {
			saveChanges = 1;
		}

		var header = ($('.slide-header-1').eq(i).css('display')=='block')?$('.slide-header-1').eq(i).html():$('.slide-header-1-edit').eq(i).val();
		if(header != settings['home']['slide'][i]['header']) {
			tempSettings.home.slide[i]['header'] = header;
			saveChanges = 1;
		}

		var subHeader = ($('.slide-header-2').eq(i).css('display')=='block')?$('.slide-header-2').eq(i).html():$('.slide-header-2-edit').eq(i).val();
		if(subHeader != settings['home']['slide'][i]['sub-header']) {
			tempSettings.home.slide[i]['sub-header'] = subHeader;
			saveChanges = 1;
		}

		var buttonName = ($('.slide-button').eq(i).css('display')=='block')?$('.slide-button').eq(i).html():$('.slide-button-name').eq(i).val();
		if(buttonName != settings['home']['slide'][i]['button-name']) {
			tempSettings.home.slide[i]['button-name'] = buttonName;
			saveChanges = 1;
		}

		var buttonLink = ($('.slide-button').eq(i).css('display')=='block')?$('.slide-button').eq(i).attr('data-link'):$('.slide-button-link').eq(i).val();
		if(buttonLink != settings['home']['slide'][i]['button-url']) {
			tempSettings.home.slide[i]['button-url'] = buttonLink;
			saveChanges = 1;
		}

		var slideButtonEnabled = $('.slide').eq(i).find('.slide-button-enabled').attr('data-slide-button-enabled');
		if(slideButtonEnabled != settings['home']['slide'][i]['button-enabled']) {
			tempSettings.home.slide[i]['button-enabled'] = slideButtonEnabled;
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

</script>

</body>
</html>