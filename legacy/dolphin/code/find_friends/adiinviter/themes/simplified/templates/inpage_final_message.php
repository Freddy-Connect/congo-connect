
<div class="adiinviter">
	<div class="adi_nc_inpage_panel_outer">
		<center>
			<div class="adi_head2 adi_tac adi_mb4">{adi:var $block_message_text}</div>

			<div class="adi_msg_btn_out">
				<input type="submit" class="adi_btn1 adi_mb4" name="adi_invite_more" value="{adi:phrase adi_invite_more}" onclick="return adi_redirect(1);" style="margin-bottom: 20px;">
				{adi:if ($adiinviter->userid == 0)}
					<input type="submit" class="adi_btn1 adi_btn_spc" name="adi_website_register" value="{adi:phrase adi_register_btn_label}" onclick="return adi_redirect(2);">
				{adi:else/}
					<input type="submit" class="adi_btn1 adi_btn_spc" name="adi_invite_history" value="{adi:phrase adi_invite_history_btn_label}" onclick="return adi_redirect(3);">
				{/adi:if}
			</div>
		</center>
	</div>
</div>


<script type="text/javascript">
function adi_redirect(i)
{
	if(i==1){ window.location.href=window.location.href; }
	else if(i==2) { window.location.href='{adi:var $adiinviter->settings['adiinviter_website_register_url']}'; }
	else { window.location.href='{adi:var $adiinviter->invite_history_url_rel}'; }
}
adi.call_event('final_message_displayed');
</script>
