<div class="adiinviter adi_orientation_{adi:var $adiinviter->current_orientation}">
	<div class="adi_nc_inpage_panel_outer">
		<div class="adi_bd_sp adi_tac adi_txt">{adi:var adi_replace_vars($adiinviter->phrases['redirect_block_message'], array('redirect_url' => $adi_global_redirect_url,))}</div>
	</div>
</div>

<script type="text/javascript">
setTimeout(function(){
	window.location = '{adi:var $adi_global_redirect_url}';
});
</script>