<!DOCTYPE html>
<html>
<head>
	<title>Redirecting..</title>
</head>
<body>

<form class="adi_clt adirs_show_conts_form" action="" method="post" id="adi_redirect_form">
	<input type="hidden" name="adi_do" value="paginate_conts">
	<input type="hidden" name="adi_page_no" value="1">
	<input type="hidden" name="adi_type" value="reg_conts">
	<input type="hidden" name="adi_list_id" value="{adi:var $adi_conts_list_id}" class="adi_list_cache_id">
	<input type="hidden" name="adi_search_query" value="">
	<input type="hidden" name="adi_search_prev_query" value="">
	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">{/adi:foreach}
</form>


<script type="text/javascript">
document.getElementById('adi_redirect_form').submit();
</script>

</body>
</html>