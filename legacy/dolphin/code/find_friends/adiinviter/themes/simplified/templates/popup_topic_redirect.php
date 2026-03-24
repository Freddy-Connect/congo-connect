<div class="adiinviter adi_nc_popup_container">

	<!-- Redirect Message -->
	<div class="adi_txt adi_tac adi_mb3">{adi:var $cs_redirect_top_message}</div>

	<!-- Content Title -->
	<div class="adi_txt adi_tac adi_mb5" style="white-space:nowrap;text-overflow:ellipsis;overflow:hidden;width: 430px;"><a class="adi_link" href="{adi:var $content_url}">{adi:var $content_title}</a></div>

	<!-- Auto-Redirect Message -->
	<div class="adi_txt adi_tac adi_mb5">{adi:phrase cs_redirect_autoredirect_message}</div>

	<div class="adi_mb5 adi_tac"><img class="adi_clt" src="{adi:const THEME_URL}/images/loading.gif"/></div>

	<!-- Action Buttons -->
	<center>
		<input type="button" class="adi_btn1 adi_popup_ok" value="{adi:phrase adi_cancel_btn_label}">
		<input type="button" class="adi_btn1 adi_btn_spc" onclick="window.location='{adi:var $content_url}';" value="{adi:phrase cs_redirect_view_content_btn_label}">
	</center>

</div>

<script type="text/javascript">
adipps.tr.red_url = "{adi:var $content_url}";
</script>


