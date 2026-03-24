<div class="adiinviter adi_orientation_{adi:var $adiinviter->current_orientation}">
	<div class="adi_nc_inpage_panel_outer">

<div class="adi_bd_sp">

	<div class="adi_bb adi_mb3">
		<div class="adi_lg_block1">
		{adi:if (empty($adi_service_name))}
			<div class="adi_head1 adi_mb2">Import Your Contacts</div>

			<div class="adi_txt adi_mb3 adi_sh1">Login to your email account.</div>
			<div class="adi_txt adi_mb3 adi_sh2">Enter your email account details.</div>
			<div class="adi_txt adi_mb3 adi_sh3">Enter your email account details to begin importing.</div>
		{adi:else}
			<div class="adi_head1 adi_mb2 adi_sh2 adi_sh1">Import Your Contacts</div>
			<div class="adi_head1 adi_mb2 adi_sh3">Import Your Contacts From {adi:var $adi_service_name} Account</div>

			<div class="adi_txt adi_mb3 adi_sh2 adi_sh1">Login to your <span class="adi_ser_name">{adi:var $adi_service_name}</span> account.</div>
			<div class="adi_txt adi_mb3 adi_sh3">Login to your <span class="adi_ser_name">{adi:var $adi_service_name}</span> account to begin importing.</div>
		{/adi:if}
		</div>
	</div>

	<form action="" method="POST" class="adi_clt adi_nc_oauth_submit_form adi_nc_addressbook_form">

	<!-- Required Parameters -->
	<input type="hidden" name="adi_service_key_val" class="adi_service_key_val" value="{adi:var $service_key}">
	<input type="hidden" name="adi_do" value="get_contacts">
	<input type="hidden" name="importer_type" value="addressbook">
	<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
	<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">

	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}

	<div class="adi_login_form_out adi_lg_block1 adi_tal">

		<div class="adi_login_field_out adi_mb2">
			<div class="adi_form_label adi_mb2 adi_tal">Email :</div>
			<input type="textbox" autocomplete="off" name="adi_user_email" class="adi_inp adirs_deftxt adi_user_email" data-default="Email">
			<div style="clear:both;"></div>
		</div>

		<div class="adi_login_field_out adi_mb4">
			<div class="adi_form_label adi_mb2 adi_tal">Password :</div>
			<input type="textbox" name="adi_password_note" class="adi_inp adirs_deftxt adirs_nc_shownode" data-default="Password">
			<input type="password" name="adi_user_password" class="adi_inp adi_dn adirs_nc_editnode adi_user_password">
			<div style="clear:both;"></div>
		</div>

		<div class="adi_login_btn_out adi_mb2 adi_off_effect">
			<input class="adi_btn1" type="submit" name="adi_submit_addressbook" value="Import">
			<input class="adi_button adi_btn_spc" type="button" value="Cancel" onclick="return adi_submit_back_form(event);">
		</div>
		<div class="adi_on_effect adi_dn">
			<table class="adi_cltb" style="margin-top:5px;"><tr class="adi_clt">
				<td class="adi_clt adi_vam"><img class="adi_clt" style="margin-right:5px;" src="{adi:const THEME_URL}/images/loading_circle.gif"></td>
				<td class="adi_clt adi_vam"><span class="adi_txt">Importing Contacts..</span></td>
			</tr></table>
		</div>

		<div class="adi_txt adi_mt3 adi_err_msg" style="display:none;"></div>

	</div>

	<!-- Startup Errors Display -->
	{adi:template inpage_error_display}

	</form>
</div>



<form class="adi_clt adirs_show_conts_form" action="" method="post">
	<input type="hidden" name="adi_do" value="paginate_conts">
	<input type="hidden" name="adi_page_no" value="1">
	<input type="hidden" name="adi_type" value="reg_conts">
	<input type="hidden" name="adi_list_id" value="" class="adi_list_cache_id">
	<input type="hidden" name="adi_search_query" value="">
	<input type="hidden" name="adi_search_prev_query" value="">
	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">{/adi:foreach}
</form>

<script type="text/javascript">
function adi_submit_back_form(e)
{
	if(e.preventDefault != undefined) {
		e.preventDefault();
	}
	document.getElementById('adi_back_form').submit();
	return false;
}
</script>

<script type="text/javascript">
	adirs.set_serv_list();
</script>

<form class="adi_clt" action="" method="post" id="adi_back_form">
{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
	<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
{/adi:foreach}
</form>


	</div>
</div>