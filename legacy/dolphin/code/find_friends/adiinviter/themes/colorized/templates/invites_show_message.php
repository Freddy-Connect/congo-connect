<div class="adiinviter adi_ih_error_table_out" style="{adi:if (isset($adi_ih_show_message) && !$adi_ih_show_message)}display:none;{/adi:if}">
	<div class="adi_nc_inpage_panel_outer adi_nc_orientation_{adi:var $adiinviter->current_orientation}">

	<div class="adi_head2 adi_mb4 adi_tac">{adi:phrase adi_invite_history_no_conts_msg_txt}</div>

	<center>
	<form class="adi_clt" action="{adi:var $adiinviter->inpage_model_url_rel}" method="GET">
		<input type="submit" class="adi_btn1" value="{adi:phrase adi_ih_goto_inpage_model_link_txt}">
		{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
			<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
		{/adi:foreach}
	</form>
	</center>

	</div>
</div>