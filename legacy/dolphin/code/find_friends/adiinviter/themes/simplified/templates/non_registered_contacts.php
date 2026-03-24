<div class="adi_bd_sp adi_tal adi_reset_search_out adi_dn">
	<div class="adi_txt adi_mb1">{adi:phrase adi_non_reg_search_results_txt}</div>
	<div class="adi_mb5"><input type="button" class="adi_btn1 adi_reset_search_data" value="{adi:phrase adi_non_reg_reset_search_btn_txt}" onclick="return adirs_cd.reset_search_results(event);"></div>
</div>


{adi:if ($contacts_count > 0)}

<!-- Non-Registered Contacts HTML -->	
<div class="adi_mb2 adi_nc_conts_panel adi_contacts_out_{adi:var $adiinviter->current_orientation}">

<!-- Set Contact type -->
{adi:if $config['email'] == 1}
	{adi:if $config['avatar'] == 1}
		{adi:set $contact_type = 1}
	{adi:else/}
		{adi:set $contact_type = 2}
	{/adi:if}
{adi:else/}
	{adi:if $config['avatar'] == 1}
		{adi:set $contact_type = 3}
	{adi:else/}
		{adi:set $contact_type = 4}
	{/adi:if}
{/adi:if}

	<div class="adi_conts_altern adi_tac adi_dn">
		<div class="adi_head1 adi_tac adi_mb3">{adi:phrase adi_non_reg_no_results_txt}</div>
		<input type="button" class="adi_btn1 adi_reset_search_data" value="{adi:phrase adi_non_reg_reset_search_btn_txt}" onclick="return adirs_cd.reset_search_results(event);">
	</div>

	<div class="adi_nc_conts_html"></div>

	<script type="text/javascript">
		adirs_cd.cont_typ = {adi:var $contact_type};
		adirs_cd.is_data = {adi:var $adi_conts_data_json};
	</script>

</div>

<!-- Contacts Pagination -->
<div class="adi_tac adi_mb3 adi_pagination_lnks adi_dn">

	<table class="adi_clt" style="width:auto;margin:0 auto;">
		<tr class="adi_clt">
			<td class="adi_clt adi_vam adi_pagi_link_out">
				<img src="{adi:const THEME_URL}/images/left_arrow.png" class="adi_clt adi_pagi_prev_lnk adi_pagi_link adi_dn" data-page="0">
				<img src="{adi:const THEME_URL}/images/left_arrow_off.png" class="adi_clt adi_pagi_prevoff_lnk">
			</td>
			<td class="adi_clt adi_vam">
				<span class="adi_txt">{adi:var adi_replace_vars($adiinviter->phrases['adi_non_reg_pagination_txt'], array('page_no' => $adi_page_no, 'total_pages' => $adi_total_pages))}</span>
			</td>
			<td class="adi_clt adi_vam adi_pagi_link_out">
				<img src="{adi:const THEME_URL}/images/right_arrow.png" class="adi_clt adi_pagi_next_lnk adi_pagi_link" data-page="0">
				<img src="{adi:const THEME_URL}/images/right_arrow_off.png" class="adi_clt adi_pagi_nextoff_lnk adi_dn">
			</td>
		</tr>
	</table>

</div>

{/adi:if}


<form action="" method="POST" class="adi_clear_form adi_page_data_form">
	<input type="hidden" name="adi_do" value="paginate_conts">
	<input type="hidden" name="adi_page_no" value="{adi:var $adi_page_no}">
	<input type="hidden" name="adi_type" value="conts">
	<input type="hidden" name="adi_list_id" value="{adi:var $adi_conts_list_id}">
	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">{/adi:foreach}
	<input type="hidden" name="adi_search_query" value="{adi:var $adi_search_query}">
	<input type="hidden" name="adi_search_prev_query" value="{adi:var $adi_search_query}">
</form>


{adi:if $adi_conts_model == 2}
<script type="text/javascript">
	adirs_cd.set_ta_search();
</script>
{/adi:if}