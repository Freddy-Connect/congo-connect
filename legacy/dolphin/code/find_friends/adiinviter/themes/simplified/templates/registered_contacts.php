<div class="adi_bd_sp adi_tal adi_reset_search_out adi_dn">
	<div class="adi_txt adi_mb1">{adi:phrase adi_reg_search_results_txt}</div>
	<div class="adi_mb5"><input type="button" class="adi_btn1 adi_reset_search_data" value="{adi:phrase adi_reg_reset_search_btn_txt}" onclick="return adirs_cd.reset_search_results(event);"></div>
</div>


<!-- Registered Contacts HTML -->


{adi:if (count($registered_contacts) > 0)}

<div class="adi_nc_conts_panel adi_contacts_out_{adi:var $adiinviter->current_orientation}">

	<div class="adi_conts_altern adi_tac adi_dn">
		<div class="adi_head1 adi_tac adi_mb3">{adi:phrase adi_reg_no_results_txt}</div>
		<input type="button" class="adi_btn1 adi_reset_search_data" value="{adi:phrase adi_reg_reset_search_btn_txt}" onclick="return adirs_cd.reset_search_results(event);">
	</div>

	<div class="adi_nc_conts_html"></div>

	<script type="text/javascript">
		adirs_cd.is_data = {adi:var $adi_conts_data_json};
	</script>

</div>


<!-- Contacts Pagination -->
<div class="adi_tac adi_mb3 adi_pagination_lnks adi_dn">

	<table class="adi_clt" style="width:auto;margin:0 auto;">
		<tr class="adi_clt">
			<td class="adi_clt adi_vam adi_pagi_link_out">
				<img src="{adi:const THEME_URL}/images/left_arrow.png" class="adi_clt adi_pagi_next_lnk adi_pagi_link" data-page="0">
			</td>
			<td class="adi_clt adi_vam">
				<span class="adi_txt">{adi:var adi_replace_vars($adiinviter->phrases['adi_reg_pagination_txt'], array('page_no' => $adi_page_no, 'total_pages' => $adi_total_pages))}</span>
			</td>
			<td class="adi_clt adi_vam adi_pagi_link_out">
				<img src="{adi:const THEME_URL}/images/right_arrow.png" class="adi_clt adi_pagi_prev_lnk adi_pagi_link" data-page="0">
			</td>
		</tr>
	</table>

</div>


<script type="text/javascript">
function adi_add_as_friend(e, frid)
{
	if(e.preventDefault != undefined) { e.preventDefault(); }
	document.getElementById('adi_fr_requests_div').src = '{adi:var $adiinviter->settings['adiinviter_root_url_rel']}/adiinviter_ajax.php?adi_do=add_user_as_friend&adi_list_id={adi:var $adi_conts_list_id}&frid='+frid; 

	document.getElementById('adi_frd_added_'+frid).style.display = 'block';
	document.getElementById('adi_add_link_'+frid).style.display = 'none';
	document.getElementById('adi_fr_added_blk_'+frid).className += ' adi_invite_sent';

	return false;
}
</script>

{/adi:if}


