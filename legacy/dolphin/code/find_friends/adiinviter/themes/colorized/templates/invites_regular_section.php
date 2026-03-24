<div class="adiinviter">
	<div class="adi_nc_inpage_panel_outer adi_nc_ih_panel_outer adi_nc_orientation_{adi:var $adiinviter->current_orientation}">

	<div class="adiih_head_sp">
		<div class="adi_rnb360">
			<div class="adi_head2 adi_mb1">{adi:phrase adi_ih_block_header_txt}</div>
			<div class="adi_txt adi_mb4 adi_tphead" style="color: #787878;">{adi:phrase adi_invite_history_subhead2_txt} {adi:if ($adiinviter->can_download_csv)}{adi:phrase adi_invite_history_download_csv_txt}{/adi:if}</div>
		</div>
		<div class="adi_rbn360">
			<div class="adi_head2 adi_mb3 adi_tphead2">{adi:phrase adi_invite_history_header2_txt}</div>
		</div>
	</div>

	<div class="adi_nc_invites_table_out">
		{adi:template invites_table_contents}
	</div>

	<div class="adiih_act_btns">
		
	</div>

	</div>
</div>


{adi:if ($adi_show_download_button)}
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
{/adi:if}