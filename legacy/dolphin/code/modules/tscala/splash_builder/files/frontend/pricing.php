
<?php if($settings['pricing']['enabled']) { 

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
<div id="pricing">
	<h1 id="pricing-header-1"><?php echo $settings['pricing']['header']; ?></h1>
	<h2 id="pricing-header-2"><?php echo $settings['pricing']['sub_header']; ?></h2>
	<div id="pricing-tables-container">
<?php	if($settings['pricing']['left_table_visible']) {

?>
		<div class="pricing-table-container" id="pricing-table-1">
			<div class="pricing-table">
				<div class="pricing-table-header" <?php echo $header_1 ?>>
					<div class="plan-name"><?php echo $settings['pricing']['left_table_header']; ?></div>
					<div class="plan-price"><span class="plan-price-currency"><?php echo $settings['pricing']['left_table_price_currency']; ?></span><span class="plan-price-amount"><?php echo $settings['pricing']['left_table_price_amount']; ?></span></div>
					<div class="plan-period"><?php echo $settings['pricing']['left_table_plan']; ?></div>
				</div>
				<div class="pricing-table-all-features" <?php echo $table_bg_1; ?>>
						<?php
							for($i=0; $i<10; $i++) {	
								if(strlen($settings['pricing']['left_table_features'][$i])>0) {
						?>
						<div class="pricing-table-feature-container" <?php echo $table_cell_colors; ?>>
						<div class="pricing-table-feature"><?php echo $settings['pricing']['left_table_features'][$i]; ?></div>
						</div><?php	}	}?>
				</div><?php if(strlen($settings['pricing']['left_table_link_name'])>0) { ?>
				<div class="pricing-table-button-container">
					<?php if(strlen($settings['pricing']['left_table_custom'])==0): ?>
						<a class="pricing-table-button" <?php echo $header_1 ?> href="<?php echo $settings['pricing']['left_table_url']; ?>"><?php echo $settings['pricing']['left_table_link_name']; ?></a>
					<?php else: 
						echo $settings['pricing']['left_table_custom'];
						  endif; ?>
				</div><?php } ?>
			</div>
		</div><?php } if($settings['pricing']['mid_table_visible']) {?><!--
		
	 --><div class="pricing-table-container" id="pricing-table-2">
			<div class="pricing-table">
				<div class="pricing-table-header" <?php echo $header_2 ?>>
					<div class="plan-name"><?php echo $settings['pricing']['mid_table_header']; ?></div>
					<div class="plan-price"><span class="plan-price-currency"><?php echo $settings['pricing']['mid_table_price_currency']; ?></span><span class="plan-price-amount"><?php echo $settings['pricing']['mid_table_price_amount']; ?></span></div>
					<div class="plan-period"><?php echo $settings['pricing']['mid_table_plan']; ?></div>
				</div>
				<div class="pricing-table-all-features" <?php echo $table_bg_23; ?>>
<?php				for($i=0; $i<10; $i++) {	
						if(strlen($settings['pricing']['mid_table_features'][$i])>0) {
	?>
					<div class="pricing-table-feature-container" <?php echo $table_cell_colors; ?>>
						<div class="pricing-table-feature"><?php echo $settings['pricing']['mid_table_features'][$i]; ?></div>
					</div><?php } } ?>
				</div><?php if(strlen($settings['pricing']['mid_table_link_name'])>0) { ?>
				<div class="pricing-table-button-container">
					<?php if(strlen($settings['pricing']['mid_table_custom'])==0): ?>
						<a class="pricing-table-button" <?php echo $header_2 ?> href="<?php echo $settings['pricing']['mid_table_url']; ?>"><?php echo $settings['pricing']['mid_table_link_name']; ?></a>
					<?php else: 
						echo $settings['pricing']['mid_table_custom'];
						  endif; ?>					
				</div><?php } ?>
			</div>
		</div><!--
		
	 --><?php
			}
			if($settings['pricing']['right_table_visible']) {
		?><div class="pricing-table-container" id="pricing-table-3">
			<div class="pricing-table">
				<div class="pricing-table-header" <?php echo $header_3 ?>>
					<div class="plan-name"><?php echo $settings['pricing']['right_table_header']; ?></div>
					<div class="plan-price"><span class="plan-price-currency"><?php echo $settings['pricing']['right_table_price_currency']; ?></span><span class="plan-price-amount"><?php echo $settings['pricing']['right_table_price_amount']; ?></span></div>
					<div class="plan-period"><?php echo $settings['pricing']['right_table_plan']; ?></div>
				</div>
				<div class="pricing-table-all-features" <?php echo $table_bg_23; ?>><?php for($i=0; $i<10; $i++) {	?>
					<?php if(strlen($settings['pricing']['right_table_features'][$i])>0) { ?><div class="pricing-table-feature-container" <?php echo $table_cell_colors; ?>>
						<div class="pricing-table-feature"><?php echo $settings['pricing']['right_table_features'][$i]; ?></div>
					</div><?php }	} ?>
				</div>
				<div class="pricing-table-button-container">
					<?php if(strlen($settings['pricing']['right_table_custom'])==0): ?>
						<a class="pricing-table-button" <?php echo $header_3 ?> href="<?php echo $settings['pricing']['right_table_url']; ?>"><?php echo $settings['pricing']['right_table_link_name']; ?></a>
					<?php else: 
						echo $settings['pricing']['right_table_custom'];
						  endif; ?>
				</div>
			</div>
		</div><?php	} ?>
	</div>
</div>
<?php } ?>