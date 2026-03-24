{adi:if count($ih_records) > 0}

<!-- For Screens upto 500px width -->
<div class="adiih_list_out">

{adi:set $adi_alt_row = true}

{adi:foreach $ih_records, $ind, $record}

	{adi:set $adi_alt_row = !$adi_alt_row}

	<div class="adiih_cont_blk {adi:if ($adi_alt_row)}adiih_odd{adi:else}adiih_even{/adi:if}" onclick="return adiih.expand_cont(this);">
		<table class="adi_cltb" style="width:100%; table-layout:fixed;">
		<tr class="adi_clt">
		<td class="adi_clt adi_vam" style="width:auto; overflow:hidden; white-space:nowrap;">
			<p class="adi_clt adi_txt adi_mb1 adiih_cont_name">
				{adi:if (!empty($adiinviter->settings['adiinviter_profile_page_url']) && $record['invitation_status'] == 'accepted')}
					<a class="adi_link" target="_blank" href="{adi:var $record['profile_page_url']}">{adi:var $record['userfullname']}</a>
				{adi:else/}
					{adi:var $record['receiver_name']}
				{/adi:if}
			</p>

			{adi:if ($record['invitation_status'] == 'invitation_sent')}
				{adi:set $selecter_class = 'adi_invite_status_pending'}
			{adi:elseif ($record['invitation_status'] == 'blocked')}
				{adi:set $selecter_class = 'adi_invite_status_blocked'}
			{adi:elseif ($record['invitation_status'] == 'accepted')}
				{adi:set $selecter_class = 'adi_invite_status_accepted'}
			{adi:elseif ($record['invitation_status'] == 'waiting')}
				{adi:set $selecter_class = 'adi_invite_status_waiting'}
			{/adi:if}
			<p class="adi_clt adi_txt adi_mb1 adiih_cont_status {adi:var $selecter_class}">{adi:var $record['status_text']}</p>
			
			{adi:if ($record['service_email'] == 1)}
				<p class="adi_clt adi_txt adi_mb1 adiih_cont_email">{adi:var $record['receiver_email']}</p>
			{/adi:if}

			<p class="adi_clt adi_txt adi_mb1 adiih_cont_issued">{adi:var $record['issued_date']}</p>
		</td>
		<td class="adi_clt adi_vam adiih_cont_act">
			<center>
			<img src="{adi:const THEME_URL}/images/down_arrow.png" class="adiih_expand">
			<img src="{adi:const THEME_URL}/images/up_arrow.png" class="adiih_shrink adi_fr">
			{adi:if $adiinviter->can_delete_invites}
			<img src="{adi:const THEME_URL}/images/delete.png" class="adiih_remove adi_fr" onclick="adiih.remove_invite(event,this);" data-cid="{adi:var $record['invitation_id']}" data-pno="{adi:var $adi_invites_page_no}">
			{/adi:if}
			<img src="{adi:const THEME_URL}/images/loading_circle.gif" class="adiih_removing adi_fr">
			</center>
		</td>
		</tr>
		</table>
	</div>
{/adi:foreach}

</div>





<div class="adiih_inv_table_out">
<form class="adi_clt" action="" method="POST">
{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
	<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
{/adi:foreach}
<table class="adi_cltb adiih_invite_tab" style="width:100%;table-layout:fixed;">
	<tr class="adi_clt">
		<th class="adi_clt adiih_col_th adiih_col_name"></th>
		<th class="adi_clt adiih_col_th adiih_col_status">{adi:phrase adi_ih_status_column_txt}</th>
		<th class="adi_clt adiih_col_th adiih_col_issued">{adi:phrase adi_ih_issued_date_column_txt}</th>
		<th class="adi_clt adiih_col_th adiih_col_check"></th>
	</tr>

{adi:set $adi_alt_row = true}

{adi:foreach $ih_records, $ind, $record}

	{adi:set $adi_alt_row = !$adi_alt_row}

	<tr class="adi_clt adiih_invite_tr {adi:if ($adi_alt_row)}adiih_odd{adi:else}adiih_even{/adi:if}">
		<td class="adi_clt adiih_cont_details">
			<div class="adi_txt adiih_cont_name">
			{adi:if (!empty($adiinviter->settings['adiinviter_profile_page_url']) && $record['invitation_status'] == 'accepted')}
				<a class="adi_link" target="_blank" href="{adi:var $record['profile_page_url']}">{adi:var $record['userfullname']}</a>
			{adi:else/}
				{adi:var $record['receiver_name']}
			{/adi:if}
			</div>
			{adi:if ($record['service_email'] == 1)}
				<div class="adi_clt adi_txt adi_mb1 adiih_cont_email">{adi:var $record['receiver_email']}</div>
			{/adi:if}
		</td>
		{adi:if ($record['invitation_status'] == 'invitation_sent')}
			{adi:set $selecter_class = 'adi_invite_status_pending'}
		{adi:elseif ($record['invitation_status'] == 'blocked')}
			{adi:set $selecter_class = 'adi_invite_status_blocked'}
		{adi:elseif ($record['invitation_status'] == 'accepted')}
			{adi:set $selecter_class = 'adi_invite_status_accepted'}
		{adi:elseif ($record['invitation_status'] == 'waiting')}
			{adi:set $selecter_class = 'adi_invite_status_waiting'}
		{/adi:if}
		<td class="adi_clt adi_txt adi_vam adiih_cont_status {adi:var $selecter_class}">{adi:var $record['status_text']}</td>
		<td class="adi_clt adi_txt adi_vam adiih_cont_issued">{adi:var $record['issued_date']}</td>
		{adi:if ($adiinviter->can_delete_invites)}
		<td class="adi_clt adi_vam">
			<img src="{adi:const THEME_URL}/images/delete.png" class="adiih_remove adi_fr" style="display:block;width:22px;height:22px;" onclick="adiih.remove_dk_invite(event,this);" data-cid="{adi:var $record['invitation_id']}" data-pno="{adi:var $adi_invites_page_no}">
			<img src="{adi:const THEME_URL}/images/loading_circle.gif" class="adiih_removing adi_fr">
		</td>
		{/adi:if}
	</tr>
{/adi:foreach}
</table>
<input type="hidden" name="page_no" value="{adi:var $adi_invites_page_no}">
</form>
</div>




<!-- Contacts Pagination -->
{adi:if ($total_pages > 0)}
<div class="adi_tac adi_mt3 adi_mb3 adi_pagination_lnks">
	<table class="adi_clt" style="width:auto;margin:0 auto;">
		<tr class="adi_clt">
			<td class="adi_clt adi_vam adi_pagi_link_out">
				{adi:if ($adi_invites_page_no > 1)}
					<img src="{adi:const THEME_URL}/images/left_arrow.png" class="adi_clt adi_pagi_prev_lnk adi_pagi_link" onclick="return adiih.paginate_invites({adi:var ($adi_invites_page_no-1)});">
				{adi:else}
					<img src="{adi:const THEME_URL}/images/left_arrow_off.png" class="adi_clt adi_pagi_prevoff_lnk">
				{/adi:if}
			</td>
			<td class="adi_clt adi_vam">
				<span class="adi_txt">{adi:var adi_replace_vars($adiinviter->phrases['adi_ih_pagination_note_txt'], array('page_no' => $adi_invites_page_no, 'total_pages' => $total_pages))}</span>
			</td>
			<td class="adi_clt adi_vam adi_pagi_link_out">
				{adi:if ($adi_invites_page_no < $total_pages)}
					<img src="{adi:const THEME_URL}/images/right_arrow.png" class="adi_clt adi_pagi_next_lnk adi_pagi_link" onclick="return adiih.paginate_invites({adi:var ($adi_invites_page_no+1)});">
				{adi:else}
					<img src="{adi:const THEME_URL}/images/right_arrow_off.png" class="adi_clt adi_pagi_nextoff_lnk">
				{/adi:if}
			</td>
		</tr>
	</table>
</div>
<div class="adi_tac adi_mt3 adi_dn adiih_loading_invites">
	<table class="adi_clt" style="width:auto;margin:0 auto;">
	<tr class="adi_clt">
		<td class="adi_clt adi_vam" class="adi_clt">
			<img src="{adi:const THEME_URL}/images/loading_circle.gif">
		</td>
		<td class="adi_clt adi_vam adi_txt adiih_loading_txt">{adi:phrase adi_ih_pagination_loading_txt}</td>
	</tr>
	</table>
</div>
{/adi:if}


{/adi:if}



<script type="text/javascript">
	adi.call_event('invite_history_loaded');
</script>
