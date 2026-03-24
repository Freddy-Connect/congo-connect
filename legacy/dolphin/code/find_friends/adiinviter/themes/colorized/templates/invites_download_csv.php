<script type="text/javascript">
adjq('.adi_download_csv').click(function(e){
	if(e.preventDefault){ e.preventDefault(); }
	if(!adjq(this).hasClass('adi_downloading_csv'))
	{
		adjq(this).addClass('adi_downloading_csv');
		adjq('#adi_dcsv_window').attr('src', "{adi:const ADI_ROOT_URL_REL}/adi_invite_history.php?adi_do=download_csv");
	}
	return false;
});
</script>
<iframe id="adi_dcsv_window" src="" style="width:0;height:0;border:0px solid #fff;padding:0;margin:0;"></iframe>
