{adi:if ($adi_current_model == 'popup')}

	adirs_cf_response('{adi:var $adi_conts_list_id}')

{adi:elseif ($adi_current_model == 'inpage')}

<script type="text/javascript">
adirs_cd.pgsz = {adi:var $adi_page_size};
adirs_cd.cache_id = '{adi:var $adi_conts_list_id}';
</script>


{adi:if ($show_friend_adder)}

	<script type="text/javascript">
	adirs_cd.cd_id = 'fa';
	</script>

	<!-- Go to Inviter page again -->
	<form action="" method="POST" class="adi_back_to_inviter" style="display:none;">
	<input type="hidden" name="adi_no_captcha_display" value="1">
	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}
	</form>

	<div class="adiinviter">
		<div class="adi_nc_inpage_panel_outer adi_conts_model_{adi:var $adi_conts_model}">
			{adi:template friend_adder_html}
		</div>
	</div>

	<script type="text/javascript">
	adirs_cd.reg_friend_sender();
	</script>

	{adi:set $load_default_template = false;}

{adi:elseif ($show_invites_sender)}

	<!-- Go to inviter page form -->
	<form action="" method="POST" class="adi_back_to_inviter" style="display:none;">
		<input type="hidden" name="adi_no_captcha_display" value="1">
		{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
			<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
		{/adi:foreach}
	</form>

	<div class="adiinviter">
		<div class="adi_nc_inpage_panel_outer adi_conts_model_{adi:var $adi_conts_model}">
			{adi:template invite_sender_html}
		</div>
	</div>

	<script type="text/javascript">
	adirs_cd.reg_invite_sender();
	</script>

	{adi:set $load_default_template = false;}

{/adi:if}


{/adi:if}