<div class="adiinviter adi_orientation_{adi:var $adiinviter->current_orientation}">
	<div class="adi_nc_inpage_panel_outer">

<div class="adi_bd_sp">
	
	<div class="adi_bb adi_mb3">
		<div class="adi_head1 adi_mb2">Enter Your Contacts</div>
		<div class="adi_txt adi_mb3" style="line-height: 1.25em;">Enter comma-separated email addresses of your contacts.</div>
	</div>

	<form action="" method="POST" class="adi_clt adi_nc_manual_form">

	<!-- Required Parameters -->
	<input type="hidden" name="adi_do" value="get_contacts">
	<input type="hidden" name="importer_type" value="manual_inviter">
	<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
	<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">

	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}

	<div class="adi_login_form_out adi_tal">

		<div class="adi_login_field_out adi_mb3">
			<textarea name="adi_contacts_list" class="adi_inp adi_textarea adirs_deftxt" spellcheck="false" data-default="{adi:phrase adi_mi_contact_list_field_default_text}"></textarea>
		</div>

		<div class="adi_login_btn_out adi_off_effect adi_mb1">
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
adirs.set_serv_list();
function adi_submit_back_form(e)
{
	if(e.preventDefault != undefined) {
		e.preventDefault();
	}
	document.getElementById('adi_back_form').submit();
	return false;
}
</script>


<form class="adi_clt" action="" method="get" id="adi_back_form">
{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
	<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
{/adi:foreach}
</form>


</div>
</div>
</div>