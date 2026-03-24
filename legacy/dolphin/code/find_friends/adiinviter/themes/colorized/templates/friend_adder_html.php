<div class="adi_bd_sp adi_tal">

	<div class="adi_rnb470">
		<div class="adi_head2 adi_mb1">{adi:var $fa_top_head}</div>
		<div class="adi_txt adi_mb3 adi_tphead" style="color:#787878;">{adi:phrase adimt_choose_another_service_msg}</div>
	</div>
	<div class="adi_rbn470">
		<div class="adi_txt adi_mb3 adi_tphead">{adi:var $fa_top_head2}</div>
	</div>

	<!-- Search in Contacts -->
	<table class="adi_cltb adi_ftb adi_mb4">
	<tr class="adi_clt">
		<td class="adi_clt" style="width:auto; overflow:hidden; white-space:nowrap;">
			<input type="textbox" name="adi_search_query" class="adi_inp adirs_deftxt adi_search_friend" data-default="{adi:phrase adi_search_user_default_text}" value="{adi:var $adi_search_query}" autocomplete="off">
		</td>
		<td class="adi_clt" style="width: 6px;"></td>
		<td class="adi_clt adi_search_conts_out">
			<input type="button" class="adi_btn2 adi_search_conts" value="{adi:phrase adi_search_user_submit_btn_text}" onclick="return adirs_cd.search_is_conts();">
		</td>
	</tr>
	</table>
</div>

<iframe id="adi_fr_requests_div" style="display:none;"></iframe>


<!-- Contacts Display -->
<div class="adi_conts_container">
	{adi:template registered_contacts}
</div>
<script type="text/javascript">
	adjq('.adi_nc_conts_html').html(adirs_cd.chtml);
</script>


<div class="adi_bd_sp adi_tal" style="margin-top:30px;">
	<div class="adi_login_btn_out adi_mb3 adi_tal">
		<div class="adi_fl adi_tal" style="width:40%;">
		{adi:if ($adiinviter->friends_system && count($contacts) > 0)}
			<input type="button" class="adi_btn2" value="{adi:phrase adi_goto_nonreg_conts_btn_txt}" onclick="return adi_submit_skip_form(event);">
		{adi:else}
			<input type="button" class="adi_btn2" value="{adi:phrase adi_reimport_btn_txt}" onclick="return adi_submit_back_form(event);">
		{/adi:if}
		</div>
		<div class="adi_fl adi_tar" style="width:60%;">
		{adi:if ($adiinviter->friends_system && $adiinviter->userid != 0)}
			<input type="button" class="adi_btn2" value="{adi:phrase adi_add_all_as_friends_btn_txt}" onclick="return adi_submit_add_all_friends_form(event);">
		{adi:else}
			<input type="button" class="adi_btn2" value="{adi:phrase adi_goto_nonreg_conts_btn_txt}" onclick="return adi_submit_skip_form(event);">
		{/adi:if}
		</div>
		<div style="clear:both;"></div>
	</div>
</div>

<form action="" method="POST" class="adi_clear_form" id="adi_back_form">
	<input type="hidden" name="adi_do" value="reset_listcache">
	<input type="hidden" name="adi_reset_list_id" value="{adi:var $adi_conts_list_id}">
	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}
</form>
 
<form action="" method="POST" class="adi_clear_form" id="adi_skip_form">
	<input type="hidden" name="adi_do" value="paginate_conts">
	<input type="hidden" name="adi_page_no" value="1">
	<input type="hidden" name="adi_type" value="conts">
	<input type="hidden" name="adi_list_id" value="{adi:var $adi_conts_list_id}">
	<input type="hidden" name="adi_search_query" value="">
	<input type="hidden" name="adi_search_prev_query" value="">
	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">{/adi:foreach}
</form>


<form action="" method="POST" class="adi_clear_form" id="adi_add_all_friends_form">
	<input type="hidden" name="adi_do" value="paginate_conts">
	<input type="hidden" name="adi_page_no" value="{adi:var $adi_page_no}">
	<input type="hidden" name="adi_type" value="conts">
	<input type="hidden" name="adi_list_id" value="{adi:var $adi_conts_list_id}">
	<input type="hidden" name="adi_search_query" value="">
	<input type="hidden" name="adi_search_prev_query" value="">
	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">{/adi:foreach}
	<input type="hidden" name="add_all_friends" value="Add All As Friends">
</form>



<script type="text/javascript">
function adi_submit_add_all_friends_form(e)
{
	if(e.preventDefault != undefined)
	{
		e.preventDefault();
	}
	document.getElementById('adi_add_all_friends_form').submit();
	return false;
}
function adi_submit_skip_form(e)
{
	if(e.preventDefault != undefined)
	{
		e.preventDefault();
	}
	document.getElementById('adi_skip_form').submit();
	return false;
}
function adi_submit_back_form(e)
{
	if(e.preventDefault != undefined)
	{
		e.preventDefault();
	}
	document.getElementById('adi_back_form').submit();
	return false;
}
</script>