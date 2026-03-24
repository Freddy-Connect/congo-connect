
	<form method="POST" action="" class="adi_clear_form adi_importer_captcha_form">
	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}

	<div class="adi_importer_cap_info">
	{adi:foreach $adi_captcha_info, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}
	</div>

	<div style="margin:25px 0px 15px 0px;">
		<img class="adi_clear_img" style="border: 1px solid #cdcdcd;" src="{adi:var $adi_captcha_url}">
	</div>

	<div style="margin:15px 0px 35px 0px;">
		<input type="textbox" autocomplete="off" name="" size="20" class="adi_inp adi_importer_captcha_text">
	</div>

	<div class="adi_action_btns_out adi_captcha_btn_out">
		<input type="submit" class="adi_btn1 adi_submit_captch_btn" name="submit_captcha" value="{adi:phrase adi_continue_btn_label}">
		<input type="button" class="adi_button adi_irc_cancel adi_btn_spc" value="{adi:phrase adi_cancel_btn_label}">
	</div>
	</form>

