<!-- Inpage Html Container -->
<div class="adiinviter adi_orientation_{adi:var $adiinviter->current_orientation}">
	<div class="adi_nc_inpage_panel_outer" id="adi_inpage_login_root">
		{adi:template login_form_html}
	</div>
</div>

<script type="text/javascript">
	adirs_cd.get_model();
	adjq('.adi_conts_model').val(adirs_cd.c_model);
</script>