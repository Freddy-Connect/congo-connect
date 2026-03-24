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
<link href='css/spectrum.css' rel='stylesheet' type='text/css' />
<link href='css/admin.css' rel='stylesheet' type='text/css' />
<link href='css/pricing.css' rel='stylesheet' type='text/css' />
<link href='../css/themes/<?php echo $settings["theme"]; ?>' rel='stylesheet' type='text/css' />
<script type="text/javascript" src="../js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="js/tooltipsy.min.js"></script>
<script type="text/javascript" src="js/spectrum.js"></script>
</head>

<body>
<?php require_once('header.php'); ?>	

<div id="error-box"><?php echo __('error_box'); ?></div>
<div id="success-box"><?php echo __('success_box'); ?></div>

<div id="colors-lightbox">
	<div id="colors-lightbox-title"><?php echo __('colors_lightbox_title'); ?></div>
	<div id="colors-lightbox-message"></div>

	<div id="colors-options">
		<div class="colors-option">
			<label for="header_footer_bg_1"><?php echo __('color_header_footer_bg_1'); ?></label>
			<input id="header_footer_bg_1" class="colors-lightbox-text" type="text" value="<?php echo $settings['pricing']['custom_colors']['header_footer_bg_1'];?>"/>
			<input type="text" class="color-picker" value="<?php echo $settings['pricing']['custom_colors']['header_footer_bg_1'];?>"/>
		</div>	
		<div class="colors-option">
			<label for="header_footer_fg_1"><?php echo __('color_header_footer_fg_1'); ?></label>
			<input id="header_footer_fg_1" class="colors-lightbox-text" type="text" value="<?php echo $settings['pricing']['custom_colors']['header_footer_fg_1'];?>"/>
			<input type="text" class="color-picker" value="<?php echo $settings['pricing']['custom_colors']['header_footer_fg_1'];?>"/>
		</div>
		<div class="colors-option">
			<label for="header_footer_bg_2"><?php echo __('color_header_footer_bg_2'); ?></label>
			<input id="header_footer_bg_2" class="colors-lightbox-text" type="text" value="<?php echo $settings['pricing']['custom_colors']['header_footer_bg_2'];?>"/>
			<input type="text" class="color-picker" value="<?php echo $settings['pricing']['custom_colors']['header_footer_bg_2'];?>"/>
		</div>	
		<div class="colors-option">
			<label for="header_footer_fg_2"><?php echo __('color_header_footer_fg_2'); ?></label>
			<input id="header_footer_fg_2" class="colors-lightbox-text" type="text" value="<?php echo $settings['pricing']['custom_colors']['header_footer_fg_2'];?>"/>
			<input type="text" class="color-picker" value="<?php echo $settings['pricing']['custom_colors']['header_footer_fg_2'];?>"/>
		</div>
		<div class="colors-option">
			<label for="header_footer_bg_3"><?php echo __('color_header_footer_bg_3'); ?></label>
			<input id="header_footer_bg_3" class="colors-lightbox-text" type="text" value="<?php echo $settings['pricing']['custom_colors']['header_footer_bg_3'];?>"/>
			<input type="text" class="color-picker" value="<?php echo $settings['pricing']['custom_colors']['header_footer_bg_3'];?>"/>
		</div>	
		<div class="colors-option">
			<label for="header_footer_fg_3"><?php echo __('color_header_footer_fg_3'); ?></label>
			<input id="header_footer_fg_3" class="colors-lightbox-text" type="text" value="<?php echo $settings['pricing']['custom_colors']['header_footer_fg_3'];?>"/>
			<input type="text" class="color-picker" value="<?php echo $settings['pricing']['custom_colors']['header_footer_fg_3'];?>"/>
		</div>
		<div class="colors-option">
			<label for="table_background"><?php echo __('color_table_background'); ?></label>
			<input id="table_background" class="colors-lightbox-text" type="text" value="<?php echo $settings['pricing']['custom_colors']['table_background'];?>"/>
			<input type="text" class="color-picker" value="<?php echo $settings['pricing']['custom_colors']['table_background'];?>"/>
		</div>
		<div class="colors-option">
			<label for="table_text_color"><?php echo __('color_table_text_color'); ?></label>
			<input id="table_text_color" class="colors-lightbox-text" type="text" value="<?php echo $settings['pricing']['custom_colors']['table_text_color'];?>"/>
			<input type="text" class="color-picker" value="<?php echo $settings['pricing']['custom_colors']['table_text_color'];?>"/>
		</div>
		<div class="colors-option">
			<label for="table_lines"><?php echo __('color_table_lines'); ?></label>
			<input id="table_lines" class="colors-lightbox-text" type="text" value="<?php echo $settings['pricing']['custom_colors']['table_lines'];?>"/>
			<input type="text" class="color-picker" value="<?php echo $settings['pricing']['custom_colors']['table_lines'];?>"/>
		</div>
	</div>

	<div id="lightbox-error"></div>
	<div id="lightbox-close"><?php echo __('upload_lightbox_close_return_button'); ?></div>
</div>

<div id="app-buttons">
	<div id="app-navigation-buttons">
		<a href="home.php"><?php echo __('menu_home'); ?></a>
		<a href="features.php"><?php echo __('menu_features'); ?></a>
		<a href="testimonials.php"><?php echo __('menu_testimonials'); ?></a>
		<a href="pricing.php" class="app-navigation-button-active"><?php echo __('menu_pricing'); ?></a>
		<a href="contact.php"><?php echo __('menu_contact'); ?></a>
		<a href="footer.php"><?php echo __('menu_footer'); ?></a>
		<a href="mics.php"><?php echo __('menu_mics'); ?></a>
	</div>
	<div id="app-save-button">
		<div id="save-button"><?php echo __('save_changes_button'); ?></div>
	</div>
</div>
<?php
	$pageEnabled = ($settings['pricing']['enabled']==1)?'checked':'';
?>

<div id="section-enabled"><div id="section-enabled-text"><input id="section-enabled-checkbox" type="checkbox" <?php echo $pageEnabled; ?>/><label for="section-enabled-checkbox"><?php echo __('enable_page_label'); ?></label></div></div>
	
<div id="pricing">
<?php
	if(strlen(trim($settings['pricing']['header']))>0) {
		$header = 'style="display:block;"';
		$headerEdit = 'style="display:none;"';
	}
	else {
		$header = 'style="display:none;"';
		$headerEdit = 'style="display:block;"';
	}

	if(strlen(trim($settings['pricing']['sub_header']))>0) {
		$subHeader = 'style="display:block;"';
		$subHeaderEdit = 'style="display:none;"';
	}
	else {
		$subHeader = 'style="display:none;"';
		$subHeaderEdit = 'style="display:block;"';
	}

	if(strlen(trim($settings['pricing']['left_table_header']))>0) {
		$tableHeader = 'style="display:block;"';
		$tableHeaderEdit = 'style="display:none;"';
	}
	else {
		$tableHeader = 'style="display:none;"';
		$tableHeaderEdit = 'style="display:block;"';
	}

	if(strlen(trim($settings['pricing']['left_table_price_currency']))>0) {
		$currencyAmount = 'style="display:block;"';
		$currencyAmountEdit = 'style="display: none;"';
	}
	else {
		$currencyAmount = 'style="display:none;"';
		$currencyAmountEdit = 'style="display:block;"';
	}

	/*if(strlen(trim($settings['pricing']['left_table_price_amount']))>0) {
		$amount = 'style="display:inline-block;"';
	}
	else {
		$amount = 'style="display:none;"';
		$amountEdit = 'style="display:inline-block;"';
	}*/

	if(strlen(trim($settings['pricing']['left_table_plan']))>0) {
		$plan = 'style="display:block;"';
		$planEdit = 'style="display:none;"';
	}
	else {
		$plan = 'style="display:none;"';
		$planEdit = 'style="display:block;"';
	}

	if((strlen( $settings['pricing']['left_table_url'])>0 && strlen( $settings['pricing']['left_table_link_name'])>0) || (strlen($settings['pricing']['left_table_custom'])>0)) {
		$urlLink = 'style="display: block;"';
		$urlLinkEdit = 'style="display:none;"';
	}
	else {
		$urlLink = 'style="display:none;"';
		$urlLinkEdit = 	'style="display: block;"';
	}

	if(strlen($settings['pricing']['left_table_custom'])==0) {
		$customButton = 'style="display:none;"';
		if($settings['pricing']['use_custom_colors']==0) {
			$tableButton = 'style="display:block;"';	
		} 
		else {
			$tableButton = 'style="display:block; background-color:'.$settings['pricing']['custom_colors']['header_footer_bg_1'] .'; color:'.$settings['pricing']['custom_colors']['header_footer_fg_1'] .'; "';	
		}
		
	}
	else {
		$customButton = 'style="display:block;"';	
		$tableButton = 'style="display:none;"';
	}

	if($settings['pricing']['use_custom_colors']==1) {
		$header_1 = 'style="background-color:'.$settings['pricing']['custom_colors']['header_footer_bg_1'].'; color:'.$settings['pricing']['custom_colors']['header_footer_fg_1'].'; "'; 
		$header_2 = 'style="background-color:'.$settings['pricing']['custom_colors']['header_footer_bg_2'].'; color:'.$settings['pricing']['custom_colors']['header_footer_fg_2'].'; "'; 
		$header_3 = 'style="background-color:'.$settings['pricing']['custom_colors']['header_footer_bg_3'].'; color:'.$settings['pricing']['custom_colors']['header_footer_fg_3'].'; "'; 
		$table_bg_1 = 'style="background-color:'.$settings['pricing']['custom_colors']['table_background'].';"';
		$table_bg_23 = 'style="background-color:'.$settings['pricing']['custom_colors']['table_background'].'; border-left-color:'.$settings['pricing']['custom_colors']['table_lines'].'; "'; 
		$table_cell_colors = 'style="color:'.$settings['pricing']['custom_colors']['table_text_color'].'; border-bottom-color:'.$settings['pricing']['custom_colors']['table_lines'].'; "'; 
	}
	else {
		$header_1 = '';
		$header_2 = '';
		$header_3 = '';
		$table_bg_1 = '';
		$table_bg_23 = '';
		$table_cell_colors = '';	
	}
    

?>
	<h1 id="pricing-header-1" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $header; ?>><?php echo $settings['pricing']['header']; ?></h1>
	<input type="text" id="pricing-header-1-edit" value="<?php echo $settings['pricing']['header']; ?>" <?php echo $headerEdit; ?>/>
	<h2 id="pricing-header-2" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $subHeader; ?>><?php echo $settings['pricing']['sub_header']; ?></h2>
	<input type="text" id="pricing-header-2-edit" value="<?php echo $settings['pricing']['sub_header']; ?>" <?php echo $subHeaderEdit; ?>/>
	<div id="pricing-tables-container">
		<div class="pricing-table-container" id="pricing-table-1">
		<?php
			$tableVisible = $settings['pricing']['left_table_visible']==1?'fa-check':'fa-times'; 
		?>
			<div class="pricing-table-enabled" data-table-enabled="1" title="<?php echo __('pricing_change_visibility'); ?>"><i class="fa <?php echo $tableVisible; ?>"></i></div>
			<div class="pricing-table">
				<div class="pricing-table-header" <?php echo $header_1 ?>>
					<div class="plan-name" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $tableHeader; ?>><?php echo $settings['pricing']['left_table_header']; ?></div>
					<input type="text" class="plan-name-edit" <?php echo $tableHeaderEdit; ?>/>
					<div class="plan-price" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $currencyAmount; ?>><span class="plan-price-currency"><?php echo $settings['pricing']['left_table_price_currency']; ?></span><span class="plan-price-amount"><?php echo $settings['pricing']['left_table_price_amount']; ?></span></div>
					<div class="plan-price-edit" <?php echo $currencyAmountEdit; ?>><input type="text" class="plan-price-currency-edit" value="<?php echo $settings['pricing']['left_table_price_currency']; ?>" <?php echo $currencyEdit; ?>/><input type="text" class="plan-price-amount-edit" value="<?php echo $settings['pricing']['left_table_price_amount']; ?>" <?php echo $amountEdit; ?>/></div>
					<div class="plan-period" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $plan; ?>><?php echo $settings['pricing']['left_table_plan']; ?></div>
					<input type="text" class="plan-period-edit" <?php echo $planEdit; ?>/>
				</div>
				<div class="pricing-table-all-features" <?php echo $table_bg_1; ?>>
				<?php
					for($i=0; $i<10; $i++) {
						if(strlen(trim($settings['pricing']['left_table_features'][$i]))>0) {
							$feature = 'style="display: block;"';
							$featureEdit = 'style="display: none;"';
						}
						else {
							$feature = 'style="display: none;"';
							$featureEdit = 'style="display: block;"';
						}
				?>
					<div class="pricing-table-feature-container" <?php echo $table_cell_colors; ?>>
						<div class="pricing-table-feature" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $feature; ?>><?php echo $settings['pricing']['left_table_features'][$i]; ?></div>
						<input type="text" class="pricing-table-feature-edit" value="<?php echo $settings['pricing']['left_table_features'][$i]; ?>" <?php echo $featureEdit; ?>/>
					</div>
				<?php
					}
				?>
				</div>
				<div class="pricing-table-button-container">
					<div class="pricing-table-custom-button" <?php echo $customButton; ?> title="<?php echo __('double_click_tooltip'); ?>"><?php echo $settings['pricing']['left_table_custom']; ?></div>
					<div class="pricing-table-button" <?php echo $tableButton; ?> data-link="<?php echo $settings['pricing']['left_table_url']; ?>" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $urlLink; ?>><?php echo $settings['pricing']['left_table_link_name']; ?></div>
					<div class="pricing-table-button-edit" <?php echo $urlLinkEdit; ?>>
						<input type="text" class="pricing-table-button-edit-name" value="<?php echo $settings['pricing']['left_table_link_name']; ?>" />
						<input type="text" class="pricing-table-button-edit-link" value="<?php echo $settings['pricing']['left_table_url']; ?>" />
						<textarea class="pricing-table-button-edit-custom" placeholder="<?php echo __('pricing_table_button_custom_hint'); ?>" title="<?php echo __('pricing_table_button_custom_tooltip'); ?>"><?php echo $settings['pricing']['left_table_custom']; ?></textarea>
					</div>
				</div>
			</div>
		</div><!--
<?php
	if(strlen(trim($settings['pricing']['mid_table_header']))>0) {
		$tableHeader = 'style="display:block;"';
		$tableHeaderEdit = 'style="display:none;"';
	}
	else {
		$tableHeader = 'style="display:none;"';
		$tableHeaderEdit = 'style="display:block;"';
	}

	if(strlen(trim($settings['pricing']['mid_table_price_currency']))>0) {
		$currencyAmount = 'style="display:block;"';
		$currencyAmountEdit = 'style="display: none;"';
	}
	else {
		$currencyAmount = 'style="display:none;"';
		$currencyAmountEdit = 'style="display:block;"';
	}

	if(strlen(trim($settings['pricing']['mid_table_plan']))>0) {
		$plan = 'style="display:block;"';
		$planEdit = 'style="display:none;"';
	}
	else {
		$plan = 'style="display:none;"';
		$planEdit = 'style="display:block;"';
	}

	
	if((strlen( $settings['pricing']['mid_table_url'])>0 && strlen( $settings['pricing']['mid_table_link_name'])>0) || (strlen($settings['pricing']['mid_table_custom'])>0)) {
		$urlLink = 'style="display: block;"';
		$urlLinkEdit = 'style="display:none;"';
	}
	else {
		$urlLink = 'style="display:none;"';
		$urlLinkEdit = 	'style="display: block;"';
	}

	if(strlen($settings['pricing']['left_table_custom'])==0) {
		$customButton = 'style="display:none;"';
		if($settings['pricing']['use_custom_colors']==0) {
			$tableButton = 'style="display:block;"';	
		} 
		else {
			$tableButton = 'style="display:block; background-color:'.$settings['pricing']['custom_colors']['header_footer_bg_2'] .'; color:'.$settings['pricing']['custom_colors']['header_footer_fg_2'] .'; "';	
		}
		
	}	
	else {
		$customButton = 'style="display:block;"';	
		$tableButton = 'style="display:none;"';
	}	
?>
	 	--><div class="pricing-table-container" id="pricing-table-2">
			<?php $tableVisible = $settings['pricing']['mid_table_visible']==1?'fa-check':'fa-times'; ?>
			<div class="pricing-table-enabled" data-table-enabled="1" title="<?php echo __('pricing_change_visibility'); ?>"><i class="fa <?php echo $tableVisible; ?>"></i></div>
			<div class="pricing-table">
				<div class="pricing-table-header" <?php echo $header_2 ?>>
					<div class="plan-name" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $tableHeader; ?>><?php echo $settings['pricing']['mid_table_header']; ?></div>
					<input type="text" class="plan-name-edit" <?php echo $tableHeaderEdit; ?>/>
					<div class="plan-price" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $currencyAmount; ?>><span class="plan-price-currency"><?php echo $settings['pricing']['mid_table_price_currency']; ?></span><span class="plan-price-amount"><?php echo $settings['pricing']['mid_table_price_amount']; ?></span></div>
					<div class="plan-price-edit" <?php echo $currencyAmountEdit; ?>><input type="text" class="plan-price-currency-edit" value="<?php echo $settings['pricing']['mid_table_price_currency']; ?>"/><input type="text" class="plan-price-amount-edit" value="<?php echo $settings['pricing']['mid_table_price_amount']; ?>"/></div>
					<div class="plan-period" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $plan; ?>><?php echo $settings['pricing']['mid_table_plan']; ?></div>
					<input type="text" class="plan-period-edit" <?php echo $planEdit; ?>/>
				</div>
				<div class="pricing-table-all-features" <?php echo $table_bg_23; ?>>
				<?php
					for($i=0; $i<10; $i++) {
						if(strlen(trim($settings['pricing']['mid_table_features'][$i]))>0) {
							$feature = 'style="display: block;"';
							$featureEdit = 'style="display: none;"';
						}
						else {
							$feature = 'style="display: none;"';
							$featureEdit = 'style="display: block;"';
						}
				?>
					<div class="pricing-table-feature-container" <?php echo $table_cell_colors; ?>>
						<div class="pricing-table-feature" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $feature; ?>><?php echo $settings['pricing']['mid_table_features'][$i]; ?></div>
						<input type="text" class="pricing-table-feature-edit" value="<?php echo $settings['pricing']['mid_table_features'][$i]; ?>" <?php echo $featureEdit; ?>/>
					</div>
				<?php
					}
				?>
				</div>
				<div class="pricing-table-button-container">
					<div class="pricing-table-custom-button" <?php echo $customButton; ?> title="<?php echo __('double_click_tooltip'); ?>"><?php echo $settings['pricing']['mid_table_custom']; ?></div>
					<div class="pricing-table-button" <?php echo $tableButton; ?> data-link="<?php echo $settings['pricing']['mid_table_url']; ?>" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $urlLink; ?>><?php echo $settings['pricing']['mid_table_link_name']; ?></div>
					<div class="pricing-table-button-edit" <?php echo $urlLinkEdit; ?>>
						<input type="text" class="pricing-table-button-edit-name" value="<?php echo $settings['pricing']['mid_table_link_name']; ?>" />
						<input type="text" class="pricing-table-button-edit-link" value="<?php echo $settings['pricing']['mid_table_url']; ?>" />
						<textarea class="pricing-table-button-edit-custom" placeholder="<?php echo __('pricing_table_button_custom_hint'); ?>" title="<?php echo __('pricing_table_button_custom_tooltip'); ?>"><?php echo $settings['pricing']['mid_table_custom']; ?></textarea>
					</div>
				</div>
			</div>
		</div><!--
<?php
	if(strlen(trim($settings['pricing']['right_table_header']))>0) {
		$tableHeader = 'style="display:block;"';
		$tableHeaderEdit = 'style="display:none;"';
	}
	else {
		$tableHeader = 'style="display:none;"';
		$tableHeaderEdit = 'style="display:block;"';
	}

	if(strlen(trim($settings['pricing']['right_table_price_currency']))>0) {
		$currencyAmount = 'style="display:block;"';
		$currencyAmountEdit = 'style="display: none;"';
	}
	else {
		$currencyAmount = 'style="display:none;"';
		$currencyAmountEdit = 'style="display:block;"';
	}

	if(strlen(trim($settings['pricing']['right_table_plan']))>0) {
		$plan = 'style="display:block;"';
		$planEdit = 'style="display:none;"';
	}
	else {
		$plan = 'style="display:none;"';
		$planEdit = 'style="display:block;"';
	}

	if((strlen( $settings['pricing']['right_table_url'])>0 && strlen( $settings['pricing']['right_table_link_name'])>0) || (strlen($settings['pricing']['right_table_custom'])>0)) {
		$urlLink = 'style="display: block;"';
		$urlLinkEdit = 'style="display:none;"';
	}
	else {
		$urlLink = 'style="display:none;"';
		$urlLinkEdit = 	'style="display: block;"';
	}

	if(strlen($settings['pricing']['left_table_custom'])==0) {
		$customButton = 'style="display:none;"';
		if($settings['pricing']['use_custom_colors']==0) {
			$tableButton = 'style="display:block;"';	
		} 
		else {
			$tableButton = 'style="display:block; background-color:'.$settings['pricing']['custom_colors']['header_footer_bg_3'] .'; color:'.$settings['pricing']['custom_colors']['header_footer_fg_3'] .'; "';	
		}
		
	}
	else {
		$customButton = 'style="display:block;"';	
		$tableButton = 'style="display:none;"';
	}	
?>
	 --><div class="pricing-table-container" id="pricing-table-3">
		<?php $tableVisible = $settings['pricing']['right_table_visible']?'fa-check':'fa-times'; ?>
			<div class="pricing-table-enabled" data-table-enabled="1" title="<?php echo __('pricing_change_visibility'); ?>"><i class="fa <?php echo $tableVisible; ?>"></i></div>
			<div class="pricing-table">
				<div class="pricing-table-header" <?php echo $header_3 ?>>
					<div class="plan-name" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $tableHeader; ?>><?php echo $settings['pricing']['right_table_header']; ?></div>
					<input type="text" class="plan-name-edit" <?php echo $tableHeaderEdit; ?>/>
					<div class="plan-price" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $currencyAmount; ?>><span class="plan-price-currency"><?php echo $settings['pricing']['right_table_price_currency']; ?></span><span class="plan-price-amount"><?php echo $settings['pricing']['right_table_price_amount']; ?></span></div>
					<div class="plan-price-edit" <?php echo $currencyAmountEdit; ?>><input type="text" class="plan-price-currency-edit" value="<?php echo $settings['pricing']['right_table_price_currency']; ?>" /><input type="text" class="plan-price-amount-edit" value="<?php echo $settings['pricing']['right_table_price_amount']; ?>" /></div>
					<div class="plan-period" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $plan; ?>><?php echo $settings['pricing']['right_table_plan']; ?></div>
					<input type="text" class="plan-period-edit" <?php echo $planEdit; ?>/>
				</div>
				<div class="pricing-table-all-features" <?php echo $table_bg_23; ?>>
				<?php
					for($i=0; $i<10; $i++) {
						if(strlen(trim($settings['pricing']['right_table_features'][$i]))>0) {
							$feature = 'style="display: block;"';
							$featureEdit = 'style="display: none;"';
						}
						else {
							$feature = 'style="display: none;"';
							$featureEdit = 'style="display: block;"';
						}
				?>
					<div class="pricing-table-feature-container" <?php echo $table_cell_colors; ?>>
						<div class="pricing-table-feature" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $feature; ?>><?php echo $settings['pricing']['right_table_features'][$i]; ?></div>
						<input type="text" class="pricing-table-feature-edit" value="<?php echo $settings['pricing']['right_table_features'][$i]; ?>" <?php echo $featureEdit; ?>/>
					</div>
				<?php
					}
				?>
				</div>
				<div class="pricing-table-button-container">
					<div class="pricing-table-custom-button" <?php echo $customButton; ?> title="<?php echo __('double_click_tooltip'); ?>"><?php echo $settings['pricing']['right_table_custom']; ?></div>
					<div class="pricing-table-button" <?php echo $tableButton; ?> data-link="<?php echo $settings['pricing']['right_table_url']; ?>" title="<?php echo __('double_click_tooltip'); ?>" <?php echo $urlLink; ?>><?php echo $settings['pricing']['right_table_link_name']; ?></div>
					<div class="pricing-table-button-edit" <?php echo $urlLinkEdit; ?>>
						<input type="text" class="pricing-table-button-edit-name" value="<?php echo $settings['pricing']['right_table_link_name']; ?>" />
						<input type="text" class="pricing-table-button-edit-link" value="<?php echo $settings['pricing']['right_table_url']; ?>" />
						<textarea class="pricing-table-button-edit-custom" placeholder="<?php echo __('pricing_table_button_custom_hint'); ?>" title="<?php echo __('pricing_table_button_custom_tooltip'); ?>"><?php echo $settings['pricing']['right_table_custom']; ?></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="pricing-table-colors">
		<div class="pricing-table-colors-checkbox"><input id="custom-colors-checkbox" type="checkbox" <?php echo ($settings['pricing']['use_custom_colors']==1)?'checked':''; ?>><label><?php echo __('pricing_table_colors_label'); ?></label></div>
		<div class="pricing-table-colors-picker" title="<?php echo __('pricing_table_colors_picker_tooltip'); ?>"><i class="fa fa-gears"></i></div>
	</div>
	
</div>

<div id="usage">
	
	<div class="usage-header" style="margin-top:30px"><?php echo __('double_click_legend'); ?></div>
	
</div>

<script type="text/javascript">

$('#pricing-header-1, #pricing-header-2, .pricing-table-enabled, .plan-name, .plan-price, .plan-price, .plan-period, .pricing-table-feature, .pricing-table-button, .pricing-table-custom-button, .pricing-table-button-edit-custom, .pricing-table-colors-picker').tooltipsy({
	alignTo: 'element',
    offset: [0, 1]
});

$(".pricing-table-enabled").on('click', function() { 
	if($(this).attr('data-table-enabled') == 1) {
		$(this).attr('data-table-enabled', 0) 
		$(this).html('<i class="fa fa-times"></i>')
	}
	else if($(this).attr('data-table-enabled') == 0) {
		$(this).attr('data-table-enabled', 1) 
		$(this).html('<i class="fa fa-check"></i>')
	}
});

$("#pricing-header-1, #pricing-header-2, .plan-name, .plan-period, .pricing-table-feature").on('dblclick', function() { 
	$(this).hide();
	$(this).next().val($(this).text()).css('display', 'block');
});

$("#pricing-header-1-edit, #pricing-header-2-edit, .plan-name-edit, .plan-period-edit, .pricing-table-feature-edit").on('dblclick', function() { 
	if($(this).val() == '') 
		return false;

	$(this).prev().text($(this).val()).show();
	$(this).hide();
});

$(".plan-price").on('dblclick', function() { 
	$(this).hide();
	$(this).next().find('.plan-price-currency-edit').val($(this).find('.plan-price-currency').html());
	$(this).next().find('.plan-price-amount-edit').val($(this).find('.plan-price-amount').text());
	$(this).next().show();
});

$(".plan-price-currency-edit").on('dblclick', function() { 
	if($(this).next().val() == '') 
		return;

	$(this).parent().prev().find('.plan-price-currency').html($(this).val());
	$(this).parent().prev().find('.plan-price-amount').text($(this).next().val());
	
	$(this).parent().hide();
	$(this).parent().prev().show();
});

$(".plan-price-amount-edit").on('dblclick', function() { 
	if($(this).val() == '') 
		return;

	$(this).parent().prev().find('.plan-price-currency').html($(this).prev().val());
	$(this).parent().prev().find('.plan-price-amount').text($(this).val());
	
	$(this).parent().hide();
	$(this).parent().prev().show();
});


$(".pricing-table-button").on('dblclick', function() { 
	$(this).hide();

	$(this).next().show();
	$(this).next().find('.pricing-table-button-edit-name').val($(this).text()).focus();
	$(this).next().find('.pricing-table-button-edit-link').val($(this).attr('data-link')).show();
});

$(".pricing-table-custom-button").on('dblclick', function() { 
	$(this).hide();

	$(this).next().next().show();
	$(this).next().next().find('.pricing-table-button-edit-custom').val($(this).html()).focus();
	//$(this).next().next().find('.pricing-table-button-edit-link').val($(this).next().attr('data-link')).show();
});


$(".pricing-table-button-edit-name").on('dblclick', function() { 
	if($(this).val() == '' && $(this).parent().find('.pricing-table-button-edit-custom').val() == '')
		return false;

	$(this).parent().hide();
	if($(this).parent().find('.pricing-table-button-edit-custom').val() == '') {
		$(this).parent().prev().text($(this).val()).attr('data-link', $(this).next().val()).show();
	} else {
		$(this).parent().prev().prev().html($(this).parent().find('.pricing-table-button-edit-custom').val());
		$(this).parent().prev().prev().show();
	}


});

$(".pricing-table-button-edit-link").on('dblclick', function() { 
	if($(this).prev().val() == '' && $(this).parent().find('.pricing-table-button-edit-custom').val() == '') 
		return false;

	$(this).parent().hide();
	if($(this).parent().find('.pricing-table-button-edit-custom').val() == '') {
		$(this).parent().prev().text($(this).prev().val()).attr('data-link', $(this).val()).show();
	} else {
		$(this).parent().prev().prev().html($(this).parent().find('.pricing-table-button-edit-custom').val());
		$(this).parent().prev().prev().show();		
	}
	
});

$(".pricing-table-button-edit-custom").on('dblclick', function() { 
	if($(this).prev().prev().val() == '' && $(this).val() == '') 
		return false;

	$(this).parent().hide();
	if($(this).val() == '') {
		$(this).parent().prev().text($(this).prev().prev().val()).attr('data-link', $(this).prev().val()).show();
	} else {
		$(this).parent().prev().prev().html($(this).val());
		$(this).parent().prev().prev().show();		
	}
	
});

$(".pricing-table-colors-picker").on('click', function() { 
	$("#colors-lightbox").height($(document).height() + $(document).scrollTop()).show();
	$(document).scrollTop(0);
});

$("#lightbox-close").on('click', function() { 
	$("#colors-lightbox").hide();
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

	$('#pricing-header-1-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('#pricing-header-2-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('.pricing-table-enabled').on('click', function() {
		saveButtonToggle();
	});

	$('.plan-name-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('.plan-price-currency-edit').on('keyup', function() {
		console.log('keyup');
		saveButtonToggle();
	});

	$('.plan-price-amount-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('.plan-period-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('.pricing-table-feature-edit').on('keyup', function() {
		saveButtonToggle();
	});

	$('.pricing-table-button-edit-name').on('keyup', function() {
		saveButtonToggle();
	});

	$('.pricing-table-button-edit-link').on('keyup', function() {
		saveButtonToggle();
	});

	$('.pricing-table-button-edit-custom').on('keyup', function() {
		saveButtonToggle();
	});

	$('#save-button').on('click', function() {
		saveData();
	});

	$('#custom-colors-checkbox').on('change', function() {
		applyColors($(this).is(':checked'));
		saveButtonToggle();
	});

	$('.color-picker').spectrum({
		preferredFormat: "hex",
		showInput: true,
		change: function(color) {
			$(this).prev().val(color.toHexString());
			if($('#custom-colors-checkbox').is(':checked')) {
				applyColors(true);				
			}
			saveButtonToggle();
		}
	});

	$('.colors-lightbox-text').on('blur', function() {
		$(this).next().spectrum("set", $(this).val());
		if($('#custom-colors-checkbox').is(':checked')) {
			applyColors(true);				
		}		
		saveButtonToggle();
	});

});

function applyColors(apply) {
	if(apply) {
		$('#pricing-table-1 .pricing-table-header, #pricing-table-1 .pricing-table-button').css('background-color',$('#header_footer_bg_1').val());
		$('#pricing-table-2 .pricing-table-header, #pricing-table-2 .pricing-table-button').css('background-color',$('#header_footer_bg_2').val());
		$('#pricing-table-3 .pricing-table-header, #pricing-table-3 .pricing-table-button').css('background-color',$('#header_footer_bg_3').val());
		$('#pricing-table-1 .pricing-table-header, #pricing-table-1 .pricing-table-button').css('color',$('#header_footer_fg_1').val());
		$('#pricing-table-2 .pricing-table-header, #pricing-table-2 .pricing-table-button').css('color',$('#header_footer_fg_2').val());
		$('#pricing-table-3 .pricing-table-header, #pricing-table-3 .pricing-table-button').css('color',$('#header_footer_fg_3').val());
		$('.pricing-table-all-features').css('background-color',$('#table_background').val());
		$('.pricing-table-feature-container').css('color',$('#table_text_color').val());
		$('.pricing-table-feature-container').css('border-bottom-color',$('#table_lines').val());
		$('.pricing-table-container:nth-child(2) .pricing-table-all-features, .pricing-table-container:nth-child(3) .pricing-table-all-features').css('border-left-color',$('#table_lines').val());			
	} else {
		$('#pricing-table-1 .pricing-table-header, #pricing-table-1 .pricing-table-button').css('background-color','');
		$('#pricing-table-2 .pricing-table-header, #pricing-table-2 .pricing-table-button').css('background-color','');
		$('#pricing-table-3 .pricing-table-header, #pricing-table-3 .pricing-table-button').css('background-color','');
		$('#pricing-table-1 .pricing-table-header, #pricing-table-1 .pricing-table-button').css('color','');
		$('#pricing-table-2 .pricing-table-header, #pricing-table-2 .pricing-table-button').css('color','');
		$('#pricing-table-3 .pricing-table-header, #pricing-table-3 .pricing-table-button').css('color','');
		$('.pricing-table-all-features').css('background-color','');
		$('.pricing-table-feature-container').css('color','');
		$('.pricing-table-feature-container').css('border-bottom-color','');
		$('.pricing-table-container:nth-child(2) .pricing-table-all-features, .pricing-table-container:nth-child(3) .pricing-table-all-features').css('border-left-color','');		
	}
}

function saveButtonToggle() {
	var saveChanges = 0;
	var counter = 0;
	var tableNames = ['left_table', 'mid_table', 'right_table'];

	var pageEnabled = parseInt($('#section-enabled-checkbox').attr('checked')?1:0);
	if(settings.pricing.enabled != pageEnabled) {
		tempSettings.pricing.enabled = pageEnabled;
		saveChanges = 1;
	}
	else {
		tempSettings.pricing.enabled = settings.pricing.enabled;
	}

	var header = ($('#pricing-header-1-edit').css('display')=='block')?$('#pricing-header-1-edit').val():$('#pricing-header-1').html();
	if(settings.pricing.header != header) {
		tempSettings.pricing.header = header;
		saveChanges = 1;
	}
	else {
		tempSettings.pricing.header = settings.pricing.header;
	}

	var subHeader = ($('#pricing-header-2-edit').css('display')=='block')?$('#pricing-header-2-edit').val():$('#pricing-header-2').html();
	if(settings.pricing.sub_header != subHeader) {
		tempSettings.pricing.sub_header = subHeader;
		saveChanges = 1;
	}
	else {
		tempSettings.pricing.sub_header = settings.pricing.sub_header;
	}

	for(i=0; i<3; i++) {
		var tableEnabled = $('.pricing-table-enabled').eq(i).attr('data-table-enabled');
		if(settings.pricing[tableNames[i]+'_visible'] != tableEnabled) {
			tempSettings.pricing[tableNames[i]+'_visible'] = tableEnabled;
			saveChanges = 1;
		}
		else {
			tempSettings.pricing[tableNames[i]+'_visible'] = settings.pricing[tableNames[i]+'_visible'];
		}
	}

	for(i=0; i<3; i++) {
		var header = ($('.plan-name-edit').eq(i).css('display')=='block')?$('.plan-name-edit').eq(i).val():$('.plan-name').eq(i).html();
		if(settings.pricing[tableNames[i]+'_header'] != header) {
			tempSettings.pricing[tableNames[i]+'_header'] = header;
			saveChanges = 1;
		}
		else {
			tempSettings.pricing[tableNames[i]+'_header'] = settings.pricing[tableNames[i]+'_header'];
		}

		var currency = ($('.plan-price-edit').eq(i).css('display')=='block')?$('.plan-price-currency-edit').eq(i).val():$('.plan-price-currency').eq(i).html();
		if(settings.pricing[tableNames[i]+'_price_currency'] != currency) {
			tempSettings.pricing[tableNames[i]+'_price_currency'] = currency;
			saveChanges = 1;
		}
		else {
			tempSettings.pricing[tableNames[i]+'_price_currency'] = settings.pricing[tableNames[i]+'_price_currency'];
		}

		var amount = ($('.plan-price-edit').eq(i).css('display')=='block')?$('.plan-price-amount-edit').eq(i).val():$('.plan-price-amount').eq(i).html();
		if(settings.pricing[tableNames[i]+'_price_amount'] != amount) {
			tempSettings.pricing[tableNames[i]+'_price_amount'] = amount;
			saveChanges = 1;
		}
		else {
			tempSettings.pricing[tableNames[i]+'_price_amount'] = settings.pricing[tableNames[i]+'_price_amount'];
		}

		var planPeriod = ($('.plan-period-edit').eq(i).css('display')=='block')?$('.plan-period-edit').eq(i).val():$('.plan-period').eq(i).html();
		if(settings.pricing[tableNames[i]+'_plan'] != planPeriod) {
			tempSettings.pricing[tableNames[i]+'_plan'] = planPeriod;
			saveChanges = 1;
		}
		else {
			tempSettings.pricing[tableNames[i]+'_plan'] = settings.pricing[tableNames[i]+'_plan'];
		}

		for(j=0; j<10; j++) {
			var feature = ( $('.pricing-table-feature-edit').eq(counter).css('display')=='block')?$('.pricing-table-feature-edit').eq(counter).val(): $('.pricing-table-feature').eq(counter).html();
			if(settings.pricing[tableNames[i]+'_features'][j] != feature) {
				tempSettings.pricing[tableNames[i]+'_features'][j] = feature;
				saveChanges = 1;
			}
			else {
				tempSettings.pricing[tableNames[i]+'_features'][j] = settings.pricing[tableNames[i]+'_features'][j];
			}
			counter ++;
		}

		var linkName = ($('.pricing-table-button-edit-name').eq(i).css('display')=='block')?$('.pricing-table-button-edit-name').eq(i).val():$('.pricing-table-button').eq(i).html();
		if(settings.pricing[tableNames[i]+'_link_name'] != linkName) {
			tempSettings.pricing[tableNames[i]+'_link_name'] = linkName;
			saveChanges = 1;
		}
		else {
			tempSettings.pricing[tableNames[i]+'_link_name'] = settings.pricing[tableNames[i]+'_link_name'];
		}

		var url = ($('.pricing-table-button-edit-link').eq(i).css('display')=='block')?$('.pricing-table-button-edit-link').eq(i).val():$('.pricing-table-button').eq(i).attr('data-link');
		if(settings.pricing[tableNames[i]+'_url'] != url) {
			tempSettings.pricing[tableNames[i]+'_url'] = url;
			saveChanges = 1;
		}

		var customButton = ($('.pricing-table-button-edit').eq(i).css('display')=='block')?$('.pricing-table-button-edit-custom').eq(i).val():$('.pricing-table-custom-button').eq(i).html();
		if(settings.pricing[tableNames[i]+'_custom'] != customButton) {
			tempSettings.pricing[tableNames[i]+'_custom'] = customButton;
			saveChanges = 1;
		}

		var useCustomColors = ($('#custom-colors-checkbox').is(':checked')) ? 1 : 0;
		if(useCustomColors != settings['pricing']['use_custom_colors']) {
			tempSettings.pricing['use_custom_colors'] = useCustomColors;
			saveChanges = 1;
		}		

		for(j=0; j<9; j++) {
			var color = $('.color-picker').eq(j).val();
			if(settings.pricing['custom_colors'][j] != color) {
				tempSettings.pricing['custom_colors'][$('.color-picker').prev().eq(j).attr('id')] = color;
				saveChanges = 1;
			}
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
                            