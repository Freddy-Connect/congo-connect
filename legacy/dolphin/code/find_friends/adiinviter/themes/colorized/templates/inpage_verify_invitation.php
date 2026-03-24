<!-- Default Error Message -->
{adi:if empty($adi_global_verify_invitation_error)}
	{adi:set $adi_global_verify_invitation_error = $adiinviter->phrases['adi_vi_defualt_error_msg']}
{/adi:if}

<div class="adiinviter adi_orientation_{adi:var $adiinviter->current_orientation}">
	<div class="adi_nc_inpage_panel_outer">
		<div class="adi_bd_sp adi_tac adi_txt">
			{adi:if (!empty($adi_global_verify_invitation_message))}
				<div class="adi_txt adi_tac adi_verify_info_message">{adi:var $adi_global_verify_invitation_message}</div>
			{adi:else/}
				<div class="adi_txt adi_tac adi_verify_error_text">{adi:var $adi_global_verify_invitation_error}</div>
			{/adi:if}
		</div>
	</div>
</div>