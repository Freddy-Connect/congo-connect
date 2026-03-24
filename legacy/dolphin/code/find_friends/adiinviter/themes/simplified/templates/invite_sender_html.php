<div class="adi_bd_sp adi_tal">

	{adi:if ($adi_friends_added_count > 0)}
	<div class="adi_txt adi_mb2">{adi:var $adi_ip_add_friends_success_msg}</div>
	{/adi:if}

	<div class="adi_rnb470">
		<div class="adi_head2 adi_mb1">{adi:phrase adi_invite_sender_top_message}</div>
		<div class="adi_txt adi_mb3 adi_tphead" style="color:#787878;">
		{adi:phrase adimt_choose_another_service_msg}
		<span class="adi_rni650">{adi:phrase adi_view_sample_invitation_link_txt}</span></div>
	</div>
	<div class="adi_rbn470">
		<div class="adi_txt adi_mb3 adi_tphead">{adi:phrase adi_invite_sender_sub_head_txt}</div>
	</div>

	<!-- Search in Contacts -->
	<table class="adi_cltb adi_ftb adi_mb4">
	<tr class="adi_clt">
		<td class="adi_clt" style="width:auto; overflow:hidden; white-space:nowrap;">
			<input type="textbox" name="adi_search_query" class="adi_inp adirs_deftxt adi_search_friend" value="{adi:var $adi_search_query}" autocomplete="off" data-default="{adi:phrase adi_search_user_default_text}">
		</td>
		<td class="adi_clt" style="width: 6px;"></td>
		<td class="adi_clt adi_search_conts_out">
			<input type="button" class="adi_btn2 adi_search_conts" value="{adi:phrase adi_search_user_submit_btn_text}" onclick="return adirs_cd.search_is_conts();">
		</td>
	</tr>
	</table>
</div>

<iframe id="adi_send_invites_ifrm" style="display:none;"></iframe>


<!-- Contacts Display -->	
<div class="adi_conts_container">
	{adi:template non_registered_contacts}
</div>
<script type="text/javascript">
	adjq('.adi_nc_conts_html').html(adirs_cd.chtml);
</script>



<div class="adi_bd_sp adi_tal" style="margin-top:30px;">
	<div class="adi_login_btn_out adi_mb3 adi_tal">
		<div class="adi_fl adi_tal" style="width:40%;">
			<input type="button" class="adi_btn1" value="{adi:phrase adi_reimport_btn_txt}" onclick="return adi_submit_back_form(event);">
		</div>
		<div class="adi_fl adi_tar" style="width:60%;">
			<!-- Invite All contacts -->
			<form action="" method="POST" class="adi_clear_form">
				<input type="hidden" name="adi_do" value="submit_invite_sender">
				<input type="hidden" name="adi_page_no" value="{adi:var $adi_page_no}">
				<input type="hidden" name="adi_type" value="conts">
				<input type="hidden" name="adi_list_id" value="{adi:var $adi_conts_list_id}">
				<input type="hidden" name="adi_search_query" value="">
				<input type="hidden" name="adi_search_prev_query" value="">
				{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">{/adi:foreach}
				<input type="submit" class="adi_btn1" name="invite_all_contacts" value="{adi:phrase adi_invite_all_contacts_btn_txt}" data-listid="{adi:var $adi_conts_list_id}">
			</form>
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


<script type="text/javascript">
function adi_submit_back_form(e)
{
	if(e.preventDefault != undefined)
	{
		e.preventDefault();
	}
	document.getElementById('adi_back_form').submit();
	return false;
}
adipps.ip.dt={
	service: '{adi:var $config["service_key"]}',
	campaign_id: '{adi:var $config["campaign_id"]}',
	content_id: '{adi:var $config["content_id"]}',
	attach_note:'',
};
adipps.ip.init();

</script>
