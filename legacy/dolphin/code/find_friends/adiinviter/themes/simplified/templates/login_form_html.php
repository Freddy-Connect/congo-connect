<!-- Default Interface -->
<div class="adi_alt_frame adi_dflt_inter">
<div class="adi_bd_sp adi_mb3">
	<div class="adi_head1 adi_mb3 adi_sw_b390">{adi:phrase adi_import_top_head_txt}</div>
	<div class="adi_head1 adi_mb3 adi_sw_a390">{adi:phrase adi_import_top_subhead_txt}</div>
	<div class="adi_bb adi_mb5"></div>

	<table class="adi_cltb" style="margin-bottom:20px;">
	<tr class="adi_clt">
		<td class="adi_clt adi_vat">
			<img src="{adi:const THEME_URL}/images/lock.png" class="adi_sec_ico">
		</td>
		<td class="adi_clt adi_vam">
			<div class="adi_txt adi_sec_txt">{adi:phrase adi_lg_secutiry_head_txt}</div>
		</td>
	</tr>
	<tr class="adi_clt">
		<td class="adi_clt"></td>
		<td class="adi_clt">
			<div class="adi_txt adi_mt1 adi_sec_txt_desc">{adi:phrase adi_lg_secutiry_paragraph_txt}</div>
		</td>
	</tr>
	</table>
</div>


<script type="text/javascript">
function adi_inIframe () {
    try {
        return window.self !== window.top;
    } catch (e) {
        return true;
    }
}
function adi_oauth_login(sk)
{
	var tp = adi.services[sk][0][2];
	if(tp == 2)
	{
		adi_service_form(sk);
		return false;
	}
	var url = '{adi:var $adiinviter->adi_root_url}/adiinviter_ajax.php?adi_do=oauth_login&adi_s=start&adi_service='+sk+'&adi_campaign_id={adi:var $campaign_id}&adi_content_id={adi:var $content_id}';
	if(adi_inIframe()) {
		var w = 750, h = 492;
		var left = (adjq(window).width()/2)-(w/2);
		var top = (adjq(window).height()/2)-(h/2);
		left += window.screenLeft;
		top += window.screenTop + 70;
		var title = adi.phrases['adi_oauth_service_submit_btn_label'] || '';
		title = title.replace(/\[service_name\]/g, adi.services[sk][0][1]);
		adjq('.adi_oauth_service_key').val(sk);
		window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
	}
	else {
		window.location.href = url;
	}
}
</script>


<form id="adi_ouath_form" action="" method="post">
	<input type="hidden" name="adi_do" value="get_contacts">
	<input type="hidden" name="adi_oauth" value="show_contacts">
	<input type="hidden" name="importer_type" value="addressbook">
	<input type="hidden" name="adi_service_key_val" value="" class="adi_oauth_service_key">

	<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
	<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">

	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}
</form>



{adi:if (in_array('gmail', $on_services))}
<div class="adi_serv_expand_alt_block">
	<div class="adi_serv_item" onclick="return adi_oauth_login('gmail');">
	<table class="adi_cltb" style="width:100%;cursor:pointer;">
	<tr class="adi_clt">
		<td class="adi_clt adi_serv_ico">
			<img class="adi_clt" src="{adi:const THEME_URL}/images/google_rs.png">
		</td>
		<td class="adi_clt adi_vam">
			<div class="adi_head2">Google</div>
			<div class="adi_txt adi_servtxt">{adi:phrase adi_google_subhead_txt}</div>
		</td>
		<td class="adi_clt adi_vam adi_tar adi_serv_impbtn">
			<input type="button" name="" class="adi_button" value="{adi:phrase adi_expand_serv_btn_txt}">
		</td>
	</tr>
	</table>
	</div>
</div>

	<div class="adi_serv_hsep"></div>
{/adi:if}
	
{adi:if (in_array('yahoo', $on_services))}
<div class="adi_serv_expand_alt_block">
	<div class="adi_serv_item" onclick="return adi_oauth_login('yahoo');">
	<table class="adi_cltb" style="width:100%;cursor:pointer;">
	<tr class="adi_clt">
		<td class="adi_clt adi_serv_ico">
			<img class="adi_clt" src="{adi:const THEME_URL}/images/yahoo_rs.png">
		</td>
		<td class="adi_clt adi_vam">
			<div class="adi_head2">Yahoo!</div>
			<div class="adi_txt adi_servtxt">{adi:phrase adi_yahoo_subhead_txt}</div>
		</td>
		<td class="adi_clt adi_vam adi_tar adi_serv_impbtn">
			<input type="button" name="" class="adi_button" value="{adi:phrase adi_expand_serv_btn_txt}">
		</td>
	</tr>
	</table>
	</div>
</div>


	<div class="adi_serv_hsep"></div>
{/adi:if}
	
{adi:if (in_array('hotmail', $on_services))}
<div class="adi_serv_expand_alt_block">
	<div class="adi_serv_item" onclick="return adi_oauth_login('hotmail');">
	<table class="adi_cltb" style="width:100%;cursor:pointer;">
	<tr class="adi_clt">
		<td class="adi_clt adi_serv_ico">
			<img class="adi_clt" src="{adi:const THEME_URL}/images/outlook_rs.png">
		</td>
		<td class="adi_clt adi_vam">
			<div class="adi_head2">Outlook</div>
			<div class="adi_txt adi_servtxt">{adi:phrase adi_outlook_subhead_txt}</div>
		</td>
		<td class="adi_clt adi_vam adi_tar adi_serv_impbtn">
			<input type="button" name="" class="adi_button" value="{adi:phrase adi_expand_serv_btn_txt}">
		</td>
	</tr>
	</table>
	</div>
</div>


	<div class="adi_serv_hsep"></div>
{/adi:if}
	
{adi:if (in_array('aol', $on_services))}
<div class="adi_serv_expand_alt_block">
	<div class="adi_serv_item" onclick="return adi_oauth_login('aol');">
	<table class="adi_cltb" style="width:100%;cursor:pointer;">
	<tr class="adi_clt">
		<td class="adi_clt adi_serv_ico">
			<img class="adi_clt" src="{adi:const THEME_URL}/images/aol_rs.png">
		</td>
		<td class="adi_clt adi_vam">
			<div class="adi_head2">AOL</div>
			<div class="adi_txt adi_servtxt">{adi:phrase adi_aol_subhead_txt}</div>
		</td>
		<td class="adi_clt adi_vam adi_tar adi_serv_impbtn">
			<input type="button" name="" class="adi_button" value="{adi:phrase adi_expand_serv_btn_txt}">
		</td>
	</tr>
	</table>
	</div>
</div>


	<div class="adi_serv_hsep"></div>
{/adi:if}
	
{adi:if (in_array('twitter', $on_services))}
<div class="adi_serv_expand_alt_block">
	<div class="adi_serv_item" onclick="return adi_oauth_login('twitter');">
	<table class="adi_cltb" style="width:100%;cursor:pointer;">
	<tr class="adi_clt">
		<td class="adi_clt adi_serv_ico">
			<img class="adi_clt" src="{adi:const THEME_URL}/images/twitter_rs.png">
		</td>
		<td class="adi_clt adi_vam">
			<div class="adi_head2">Twitter</div>
			<div class="adi_txt adi_servtxt">{adi:phrase adi_twitter_subhead_txt}</div>
		</td>
		<td class="adi_clt adi_vam adi_tar adi_serv_impbtn">
			<input type="button" name="" class="adi_button" value="{adi:phrase adi_expand_serv_btn_txt}">
		</td>
	</tr>
	</table>
	</div>
</div>


	<div class="adi_serv_hsep"></div>
{/adi:if}
	



{adi:if (in_array('linkedin', $on_services))}
	<!-- Mail.com -->
	<div class="adi_serv_expand_block">
		<div class="adi_serv_item">
		<table class="adi_cltb" style="width:100%;cursor:pointer;">
		<tr class="adi_clt">
			<td class="adi_clt adi_serv_ico">
				<img class="adi_clt" src="{adi:const THEME_URL}/images/linkedin_rs.png">
			</td>
			<td class="adi_clt adi_vam">
				<div class="adi_head2 adi_rnb330">Linkedin</div>
				<div class="adi_head2 adi_rbn330"><a href="#" class="adi_head2">Linkedin</a></div>
				<div class="adi_txt adi_servtxt">{adi:phrase adi_linkedin_subhead_txt}</div>
			</td>
			<td class="adi_clt adi_vam adi_tar adi_serv_impbtn">
				<input type="button" name="" class="adi_button" value="{adi:phrase adi_expand_serv_btn_txt}">
			</td>
		</tr>
		</table>
		</div>

		<form method="POST" enctype="multipart/form-data" class="adi_nc_type2_form" action="" target="adi_submit_linkedin_form">

		<!-- Required Parameters -->
		<input type="hidden" name="adi_do" value="get_contacts">
		<input type="hidden" name="importer_type" value="contact_file">
		<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
		<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">
		<input type="hidden" name="adi_conts_model" value="1" class="adi_conts_model_val">

		{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
			<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
		{/adi:foreach}

		<div class="adi_serv_inner_sect adi_dn">
		<div class="adi_block_section_outer adi_type2_sect_outer">
			<table width="100%" cellpadding="0" cellspacing="0" class=" adi_linkedin_sect_tb" style="margin-bottom: 35px;">
			<tr>
				<td  style="width:20px;"><div class="adi_plain_text"><b>{adi:phrase adi_linkedin_imp_step1_alt_label}</b></div></td>
				<td><div class="adi_plain_text"> {adi:phrase adi_linkedin_imp_step1_desc}</div></td>
			</tr>
			<tr>
				<td></td>
				<td style="padding-bottom: 5px"><a href="https://www.linkedin.com/addressBookExport?exportNetwork=Export&outputType=microsoft_outlook" class="adi_link1" target="_blank">{adi:phrase adi_linkedin_imp_download_redirect_text}</a></td>
			</tr>
			<tr>
				<td></td>
				<td><div class="adi_plain_text" style="margin-bottom:5px;">{adi:phrase adi_linkedin_imp_step1_note}</div></td>
			</tr>
			<tr>
				<td  style="width:20px;"><div class="adi_plain_text"><b>{adi:phrase adi_linkedin_imp_step2_alt_label}</b></div></td>
				<td><div class="adi_plain_text"> {adi:phrase adi_linkedin_imp_step2_desc}</div></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<div class="adi_type2_chfile" style="margin-bottom:5px;">
						<a href="" class="adi_link1">{adi:phrase adi_linkedin_imp_select_csv_btn_txt}</a>
						<input type="file" name="adi_contact_file" size="20" class="adi_file_input adi_type2_file_input">
						<div class="adi_type2_selected_file"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td  style="width:20px;"><div class="adi_plain_text"><b>{adi:phrase adi_linkedin_imp_step3_alt_label}</b></div></td>
				<td><div class="adi_plain_text"> {adi:phrase adi_linkedin_imp_step3_desc}</div></td>
			</tr>
			</table>

			<div class="adi_action_buttons adi_nc_submit_action">
				<div class="adi_lnkd_error_msg"></div>
				<div class="adi_off_effect">
					<input type="submit" name="" class="adi_btn1" value="{adi:phrase adi_import_contacts_btn_txt}">
					<input type="button" class="adi_button adi_close_block adi_btn_spc" value="{adi:phrase adi_cancel_btn_txt}" style="width: 7.307em;" onclick="return adi_show_default();">
				</div>
				<div class="adi_on_effect adi_dn">
					<table class="adi_cltb" style="margin-top:5px;"><tr class="adi_clt">
						<td class="adi_clt adi_vam"><img class="adi_clt adi_loading_ico" src="{adi:const THEME_URL}/images/loading_circle.gif"></td>
						<td class="adi_clt adi_vam"><span class="adi_txt">{adi:phrase adi_importi_contacts_loading_text}</span></td>
					</tr></table>
				</div>
				<div class="adi_txt adi_mt3 adi_err_msg" style="display:none;"></div>
			</div>

			<div class="adi_action_buttons adi_nc_submit_effect" style="display:none;"><div class="adi_proc_effect">{adi:phrase adi_linkedin_imp_submit_msg_txt}</div></div>
		</div>
		</div>
		</form>
		
	</div>

	<script type="text/javascript">
	adjq('.adi_nc_type2_form').attr('action', adi.ajaxUrl('adi_do=get_contacts'));
	</script>

	<!-- Iframe fro submiting contact file from popup -->
	<iframe id="adi_submit_linkedin_form" name="adi_submit_linkedin_form" src="" style="width:0;height:0;border:0px solid #fff;padding:0;margin:0;display:none;"></iframe>

	<div class="adi_serv_hsep"></div>
{/adi:if}
	



{adi:if (in_array('qq_com', $on_services))}
	<div class="adi_serv_expand_block">
		<div class="adi_serv_item">
		<table class="adi_cltb" style="width:100%;cursor:pointer;">
		<tr class="adi_clt">
			<td class="adi_clt adi_serv_ico">
				<img class="adi_clt" src="{adi:const THEME_URL}/images/qq_com_rs.png">
			</td>
			<td class="adi_clt adi_vam">
				<div class="adi_head2 adi_rnb330">QQ Mail</div>
				<div class="adi_head2 adi_rbn330"><a href="#" class="adi_head2">QQ Mail</a></div>
				<div class="adi_txt adi_servtxt">{adi:phrase adi_qq_com_subhead_txt}</div>
			</td>
			<td class="adi_clt adi_vam adi_tar adi_serv_impbtn">
				<input type="button" name="" class="adi_button" value="{adi:phrase adi_expand_serv_btn_txt}">
			</td>
		</tr>
		</table>
		</div>

		<form method="POST" enctype="multipart/form-data" class="adi_nc_type2_form" action="" target="adi_submit_qq_com_form">

		<!-- Required Parameters -->
		<input type="hidden" name="adi_do" value="get_contacts">
		<input type="hidden" name="importer_type" value="contact_file">
		<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
		<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">
		<input type="hidden" name="adi_conts_model" value="1" class="adi_conts_model_val">

		{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
			<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
		{/adi:foreach}

		<div class="adi_serv_inner_sect adi_dn">
		<div class="adi_block_section_outer adi_type2_sect_outer">
			<table width="100%" cellpadding="0" cellspacing="0" class=" adi_linkedin_sect_tb" style="margin-bottom: 35px;">
			<tr>
				<td  style="width:20px;"><div class="adi_plain_text"><b>{adi:phrase adi_linkedin_imp_step1_alt_label}</b></div></td>
				<td><div class="adi_plain_text"> {adi:phrase adi_qq_com_imp_step1_desc}</div></td>
			</tr>
			<tr>
				<td></td>
				<td style="padding-bottom: 5px"><a href="http://kf.qq.com/faq/120511z22Uzq130902E7ji6v.html" class="adi_link1" target="_blank">{adi:phrase adi_qq_com_imp_download_redirect_text}</a></td>
			</tr>
			<tr>
				<td></td>
				<td><div class="adi_plain_text" style="margin-bottom:5px;">{adi:phrase adi_qq_com_imp_step1_note}</div></td>
			</tr>
			<tr>
				<td  style="width:20px;"><div class="adi_plain_text"><b>{adi:phrase adi_linkedin_imp_step2_alt_label}</b></div></td>
				<td><div class="adi_plain_text"> {adi:phrase adi_qq_com_imp_step2_desc}</div></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<div class="adi_type2_chfile" style="margin-bottom:5px;">
						<a href="" class="adi_link1" onclick="return false;">{adi:phrase adi_qq_com_imp_select_csv_btn_txt}</a>
						<input type="file" name="adi_contact_file" size="20" class="adi_file_input adi_type2_file_input">
						<div class="adi_type2_selected_file"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td  style="width:20px;"><div class="adi_plain_text"><b>{adi:phrase adi_linkedin_imp_step3_alt_label}</b></div></td>
				<td><div class="adi_plain_text"> {adi:phrase adi_qq_com_imp_step3_desc}</div></td>
			</tr>
			</table>

			<div class="adi_action_buttons adi_nc_submit_action">
				<div class="adi_lnkd_error_msg"></div>
				<div class="adi_off_effect">
					<input type="submit" name="" class="adi_btn1" value="{adi:phrase adi_import_contacts_btn_txt}">
					<input type="button" class="adi_button adi_close_block adi_btn_spc" value="{adi:phrase adi_cancel_btn_txt}" style="width: 7.307em;" onclick="return adi_show_default();">
				</div>
				<div class="adi_on_effect adi_dn">
					<table class="adi_cltb" style="margin-top:5px;"><tr class="adi_clt">
						<td class="adi_clt adi_vam"><img class="adi_clt adi_loading_ico" src="{adi:const THEME_URL}/images/loading_circle.gif"></td>
						<td class="adi_clt adi_vam"><span class="adi_txt">{adi:phrase adi_importi_contacts_loading_text}</span></td>
					</tr></table>
				</div>
				<div class="adi_txt adi_mt3 adi_err_msg" style="display:none;"></div>
			</div>

			<div class="adi_action_buttons adi_nc_submit_effect" style="display:none;"><div class="adi_proc_effect">{adi:phrase adi_qq_com_imp_submit_msg_txt}</div></div>
		</div>
		</div>
		</form>
		
	</div>

	<script type="text/javascript">
	adjq('.adi_nc_type2_form').attr('action', adi.ajaxUrl('adi_do=get_contacts'));
	</script>

	<!-- Iframe fro submiting contact file from popup -->
	<iframe id="adi_submit_qq_com_form" name="adi_submit_qq_com_form" src="" style="width:0;height:0;border:0px solid #fff;padding:0;margin:0;display:none;"></iframe>

	<div class="adi_serv_hsep"></div>
{/adi:if}



	
{adi:if (in_array('mailchimp', $on_services))}
<div class="adi_serv_expand_alt_block">
	<div class="adi_serv_item" onclick="return adi_oauth_login('mailchimp');">
	<table class="adi_cltb" style="width:100%;cursor:pointer;">
	<tr class="adi_clt">
		<td class="adi_clt adi_serv_ico">
			<img class="adi_clt" src="{adi:const THEME_URL}/images/mailchimp_rs.png">
		</td>
		<td class="adi_clt adi_vam">
			<div class="adi_head2">MailChimp</div>
			<div class="adi_txt adi_servtxt">{adi:phrase adi_mailchimp_subhead_txt}</div>
		</td>
		<td class="adi_clt adi_vam adi_tar adi_serv_impbtn">
			<input type="button" name="" class="adi_button" value="{adi:phrase adi_expand_serv_btn_txt}">
		</td>
	</tr>
	</table>
	</div>
</div>


	<div class="adi_serv_hsep"></div>
{/adi:if}
	
	
{adi:if (in_array('mail_com', $on_services))}
	<!-- Mail.com -->
	<div class="adi_serv_expand_block">
		<div class="adi_serv_item">
		<table class="adi_cltb" style="width:100%;cursor:pointer;">
		<tr class="adi_clt">
			<td class="adi_clt adi_serv_ico">
				<img class="adi_clt" src="{adi:const THEME_URL}/images/mail_com_rs.png">
			</td>
			<td class="adi_clt adi_vam">
				<div class="adi_head2 adi_rnb330">Mail.com</div>
				<div class="adi_head2 adi_rbn330"><a href="#" class="adi_head2">Mail.com</a></div>
				<div class="adi_txt adi_servtxt">{adi:phrase adi_mail_com_subhead_txt}</div>
			</td>
			<td class="adi_clt adi_vam adi_tar adi_serv_impbtn">
				<input type="button" name="" class="adi_button" value="{adi:phrase adi_expand_serv_btn_txt}">
			</td>
		</tr>
		</table>
		</div>

		<div class="adi_serv_inner_sect adi_dn">
			<form action="" method="POST" class="adi_clear_form adi_nc_addressbook_form">

			<!-- Required Parameters -->
			<input type="hidden" name="adi_service_key_val" class="adi_service_key_val" value="mail_com">
			<input type="hidden" name="adi_do" value="get_contacts">
			<input type="hidden" name="importer_type" value="addressbook">
			<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
			<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">
			<input type="hidden" name="adi_conts_model" value="1" class="adi_conts_model_val">

			{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
				<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
			{/adi:foreach}

			<table class="adi_cltb">
				<tr class="adi_clt">
					<td class="adi_clt adi_vam">
						<input type="textbox" class="adi_inp adirs_deftxt adi_user_email" name="adi_user_email" data-default="{adi:phrase adi_email_field_default_text}" autocomplete="off">
					</td>
				</tr>
				<tr class="adi_clt"><td class="adi_clt adi_isect_hsep"></td></tr>
				<tr class="adi_clt">
					<td class="adi_clt adi_vam">
						<input type="textbox" class="adi_inp adirs_deftxt adirs_nc_shownode" name="adi_password_note" data-default="{adi:phrase adi_password_field_default_text}">
						<input type="password" class="adi_inp adirs_deftxt adi_dn adirs_nc_editnode adi_user_password" name="adi_user_password" data-default="{adi:phrase adi_password_field_default_text}">
					</td>
				</tr>
				<tr class="adi_clt"><td class="adi_clt adi_isect_hsep"></td></tr>
				<tr class="adi_clt" style="height: 32px;">
					<td class="adi_clt">
						<div class="adi_off_effect">
							<input type="submit" name="adi_submit_addressbook" class="adi_btn1" value="{adi:phrase adi_import_contacts_btn_txt}">
							<input type="button" class="adi_button adi_close_block adi_btn_spc" value="{adi:phrase adi_cancel_btn_txt}" style="width: 7.307em;">
						</div>
						<div class="adi_on_effect adi_dn">
							<table class="adi_cltb" style="margin-top:5px;"><tr class="adi_clt">
								<td class="adi_clt adi_vam"><img class="adi_clt adi_loading_ico" src="{adi:const THEME_URL}/images/loading_circle.gif"></td>
								<td class="adi_clt adi_vam"><span class="adi_txt">{adi:phrase adi_importi_contacts_loading_text}</span></td>
							</tr></table>
						</div>
					</td>
				</tr>
				<tr class="adi_clt">
					<td class="adi_clt">
						<div class="adi_txt adi_mt3 adi_err_msg" style="display:none;"></div>
					</td>
				</tr>
			</table>
			<input type="hidden" class="adi_oauth_submit" value="0">
			</form>
		</div>
	</div>

	<div class="adi_serv_hsep"></div>
{/adi:if}
	
	<!-- Other Account -->
	<!--mise en commentaire freddy mise en commentaire parce que aucune messagerie ne fonctione
    
    <div class="adi_serv_expand_block">
		<div class="adi_serv_item">
		<table class="adi_cltb" style="width:100%;cursor:pointer;">
		<tr class="adi_clt">
			<td class="adi_clt adi_serv_ico">
				<img class="adi_clt" src="{adi:const THEME_URL}/images/other_email_rs.png">
			</td>
			<td class="adi_clt adi_vam">
				<div class="adi_head2 adi_rnb330">{adi:phrase adi_other_service_head_txt}</div>
				<div class="adi_head2 adi_rbn330"><a href="#" class="adi_head2">{adi:phrase adi_other_service_head_txt}</a></div>
				<div class="adi_txt adi_servtxt">{adi:phrase adi_other_service_subhead_txt}</div>
			</td>
			<td class="adi_clt adi_vam adi_tar adi_serv_impbtn">
				<input type="button" name="" class="adi_button" value="{adi:phrase adi_expand_serv_btn_txt}">
			</td>
		</tr>
		</table>
		</div>

		<div class="adi_serv_inner_sect adi_dn">
			<form action="" method="POST" class="adi_clear_form adi_nc_addressbook_form  adi_nc_irc_parent_form">

			
			<input type="hidden" name="adi_service_key_val" class="adi_service_key_val">
			<input type="hidden" name="adi_do" value="get_contacts">
			<input type="hidden" name="importer_type" value="addressbook">
			<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
			<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">
			<input type="hidden" name="adi_captcha_text" class="adi_captcha_text_cls">
			<div class="adi_importer_cap_info_pass" style="display:none;"></div>
			<input type="hidden" name="adi_conts_model" value="1" class="adi_conts_model_val">

			{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
				<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
			{/adi:foreach}

			<table class="adi_cltb">
				<tr class="adi_clt">
					<td class="adi_clt adi_vam">
						<input type="textbox" class="adi_inp adirs_deftxt adi_user_email adi_nc_user_email_input" name="adi_user_email" data-default="{adi:phrase adi_email_field_default_text}" autocomplete="off">
					</td>
				</tr>
				<tr class="adi_clt"><td class="adi_clt adi_isect_hsep"></td></tr>
				<tr class="adi_clt">
					<td class="adi_clt adi_vam">
						<input type="textbox" class="adi_inp adirs_deftxt adirs_nc_shownode" name="adi_password_note" data-default="{adi:phrase adi_password_field_default_text}">
						<input type="password" class="adi_inp adirs_deftxt adi_dn adirs_nc_editnode adi_user_password" name="adi_user_password" data-default="{adi:phrase adi_password_field_default_text}">
					</td>
				</tr>
				<tr class="adi_clt"><td class="adi_clt adi_isect_hsep"></td></tr>
				<tr class="adi_clt">
					<td class="adi_clear_td adi_service_input_out">

					<div class="adi_input_form_field adi_nc_service_name_outer">
						<img class="adi_cli adi_search_icon" src="{adi:const THEME_URL}/images/find_icon.png">
						<img class="adi_clear_img adi_nc_down_arrow" src="{adi:const THEME_URL}/images/dropdown_arrow.gif" data="{adi:var $adi_current_model}" style="display:block;">
						<img class="adi_clear_img adi_nc_up_arrow" src="{adi:const THEME_URL}/images/up_arrow.gif" data="{adi:var $adi_current_model}">
						<input type="textbox" name="adi_service_name" data="{adi:var $adi_current_model}" autocomeplete="off" size="20" class="adi_sinp adi_nc_service_input adi_service_input_{adi:var $adiinviter->current_orientation} adi_nc_service_note" value="{adi:phrase adi_ab_service_field_default_txt}" autocomplete="off">
						{adi:set $adi_services = adi_allocate_pack('Adi_Services')}
						{adi:set $adiinviter_services = $adi_services->get_service_details('all', 'info')}
					</div>
					
					<div class="adi_nc_services_panel_out adi_dn">
						{adi:foreach $adiinviter->settings['services_onoff']['on'], $ind, $service_id} {adi:if ($service_id !== 'linkedin' && $service_id !== 'qq_com')} <div class="adi_nc_service_select_out" data="{adi:var $service_id}"><div class="adi_nc_service_select adi_sserv_{adi:var $service_id}"><div class="adi_service_select_name {adi:var $service_id}_si">{adi:var $adiinviter_services[$service_id]['info']['service']}</div></div></div>{/adi:if}{/adi:foreach}
						<div style="clear:both;"></div>
					</div>

					</td>
				</tr>
				<tr class="adi_clt"><td class="adi_clt adi_isect_hsep"></td></tr>
				<tr class="adi_clt">
					<td class="adi_clt">
						<div class="adi_off_effect">
							<input type="submit" name="adi_submit_addressbook" class="adi_btn1" value="{adi:phrase adi_import_contacts_btn_txt}">
							<input type="button" class="adi_button adi_close_block adi_btn_spc" value="{adi:phrase adi_cancel_btn_txt}" style="width: 7.307em;">
						</div>
						<div class="adi_on_effect adi_dn">
							<table class="adi_cltb" style="margin-top:5px;"><tr class="adi_clt">
								<td class="adi_clt adi_vam"><img class="adi_clt adi_loading_ico" src="{adi:const THEME_URL}/images/loading_circle.gif"></td>
								<td class="adi_clt adi_vam"><span class="adi_txt">{adi:phrase adi_importi_contacts_loading_text}</span></td>
							</tr></table>
						</div>
					</td>
				</tr>
				<tr class="adi_clt">
					<td class="adi_clt">
						<div class="adi_txt adi_mt3 adi_err_msg" style="display:none;"></div>
					</td>
				</tr>
			</table>
			<input type="hidden" class="adi_oauth_submit" value="0">
			</form>
		</div>
	</div>

	<div class="adi_serv_hsep"></div>
    
    -->

	<!-- Contact File -->
	<div class="adi_serv_expand_block">
		<div class="adi_serv_item">
		<table class="adi_cltb" style="width:100%;cursor:pointer;">
		<tr class="adi_clt">
			<td class="adi_clt adi_serv_ico">
				<img class="adi_clt" src="{adi:const THEME_URL}/images/contact_file_rs.png">
			</td>
			<td class="adi_clt adi_vam">
				<div class="adi_head2 adi_rnb330">{adi:phrase adi_contfile_head_txt}</div>
				<div class="adi_head2 adi_rbn330"><a href="#" class="adi_head2">{adi:phrase adi_contfile_head_txt}</a></div>
				<div class="adi_txt adi_servtxt">{adi:phrase adi_contfile_subhead_txt}</div>
			</td>
			<td class="adi_clt adi_vam adi_tar adi_serv_impbtn">
				<input type="button" name="" class="adi_button" value="{adi:phrase adi_expand_serv_btn_txt}">
			</td>
		</tr>
		</table>
		</div>

		<div class="adi_serv_inner_sect adi_dn">
			<div class="adi_mb3">
				<a class="adi_link adi_expand_instr" href="#" onclick="return adi_show_cf_instructs(event);">{adi:phrase adi_cf_show_instructions_link}</a>
			</div>

			<form method="POST" enctype="multipart/form-data" class="adi_nc_contact_file_form" action="" target="adi_submit_contact_file">

			<!-- Required Parameters -->
			<input type="hidden" name="adi_do" value="get_contacts">
			<input type="hidden" name="importer_type" value="contact_file">
			<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
			<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">
			<input type="hidden" name="adi_conts_model" value="1" class="adi_conts_model_val">

			{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
				<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
			{/adi:foreach}

			<table class="adi_cltb">
				<tr class="adi_clt">
					<td class="adi_clt adi_vam">
						<input type="file" class="adi_txt adi_contact_file" name="adi_contact_file">
					</td>
				</tr>
				<tr class="adi_clt"><td class="adi_clt adi_isect_hsep"></td></tr>
				<tr class="adi_clt">
					<td class="adi_clt">
						<div class="adi_off_effect">
							<input type="submit" name="" class="adi_btn1" value="{adi:phrase adi_import_contacts_btn_txt}">
							<input type="button" class="adi_button adi_close_block adi_btn_spc" value="{adi:phrase adi_cancel_btn_txt}" style="width: 7.307em;">
						</div>
						<div class="adi_on_effect adi_dn">
							<table class="adi_cltb" style="margin-top:5px;"><tr class="adi_clt">
								<td class="adi_clt adi_vam"><img class="adi_clt adi_loading_ico" src="{adi:const THEME_URL}/images/loading_circle.gif"></td>
								<td class="adi_clt adi_vam"><span class="adi_txt">{adi:phrase adi_importi_contacts_loading_text}</span></td>
							</tr></table>
						</div>
					</td>
				</tr>
				<tr class="adi_clt">
					<td class="adi_clt">
						<div class="adi_txt adi_mt3 adi_err_msg" style="display:none;"></div>
					</td>
				</tr>
			</table>
			</form>
			<script type="text/javascript">
			adjq('.adi_nc_contact_file_form').attr('action', adi.ajaxUrl('adi_do=get_contacts'));
			</script>
			<!-- Iframe fro submiting contact file from popup -->
			<iframe id="adi_submit_contact_file" name="adi_submit_contact_file" src="" style="width:0;height:0;border:0px solid #fff;padding:0;margin:0;display:none;"></iframe>
		</div>
	</div>

	<div class="adi_serv_hsep"></div>

	<!-- Manual Inviter -->
	<div class="adi_serv_expand_block adi_nc_manual_block">
		<div class="adi_serv_item">
		<table class="adi_cltb" style="width:100%;cursor:pointer;">
		<tr class="adi_clt">
			<td class="adi_clt adi_serv_ico">
				<img class="adi_clt" src="{adi:const THEME_URL}/images/manual_rs.png">
			</td>
			<td class="adi_clt adi_vam">
				<div class="adi_head2 adi_rnb330">{adi:phrase adi_manualinv_head_txt}</div>
				<div class="adi_head2 adi_rbn330"><a href="#" class="adi_head2">{adi:phrase adi_manualinv_head_txt}</a></div>
				<div class="adi_txt adi_servtxt">{adi:phrase adi_manualinv_subhead_txt}</div>
			</td>
			<td class="adi_clt adi_vam adi_tar adi_serv_impbtn">
				<input type="button" name="" class="adi_button" value="{adi:phrase adi_expand_serv_btn_txt}">
			</td>
		</tr>
		</table>
		</div>

		<div class="adi_serv_inner_sect adi_dn">
			<form action="" method="POST" class="adi_nc_manual_form">

			<!-- Required Parameters -->
			<input type="hidden" name="adi_do" value="get_contacts">
			<input type="hidden" name="importer_type" value="manual_inviter">
			<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
			<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">
			<input type="hidden" name="adi_conts_model" value="1" class="adi_conts_model_val">

			{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
				<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
			{/adi:foreach}

			<table class="adi_cltb" style="width:100%;">
				<tr class="adi_clt">
					<td class="adi_clt adi_vam">
						<textarea class="adi_inp adi_textarea adirs_deftxt" name="adi_contacts_list" spellcheck="false" data-default="{adi:phrase adi_mi_contact_list_field_default_text}"></textarea>
					</td>
				</tr>
				<tr class="adi_clt"><td class="adi_clt adi_isect_hsep"></td></tr>
				<tr class="adi_clt">
					<td class="adi_clt">
						<div class="adi_off_effect">
							<input type="submit" name="" class="adi_btn1" value="{adi:phrase adi_import_contacts_btn_txt}">
							<input type="button" class="adi_button adi_close_block adi_btn_spc" value="{adi:phrase adi_cancel_btn_txt}" style="width: 7.307em;">
						</div>
						<div class="adi_on_effect adi_dn">
							<table class="adi_cltb" style="margin-top:5px;"><tr class="adi_clt">
								<td class="adi_clt adi_vam"><img class="adi_clt adi_loading_ico" src="{adi:const THEME_URL}/images/loading_circle.gif"></td>
								<td class="adi_clt adi_vam"><span class="adi_txt">{adi:phrase adi_importi_contacts_loading_text}</span></td>
							</tr></table>
						</div>
					</td>
				</tr>
				<tr class="adi_clt">
					<td class="adi_clt">
						<div class="adi_txt adi_mt3 adi_err_msg" style="display:none;"></div>
					</td>
				</tr>
			</table>
			</form>
		</div>
	</div>





<form class="adi_clt adirs_show_conts_form" action="" method="post">
	<input type="hidden" name="adi_do" value="paginate_conts">
	<input type="hidden" name="adi_page_no" value="1">
	<input type="hidden" name="adi_type" value="reg_conts">
	<input type="hidden" name="adi_list_id" value="" class="adi_list_cache_id">
	<input type="hidden" name="adi_search_query" value="">
	<input type="hidden" name="adi_search_prev_query" value="">
	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">{/adi:foreach}
</form>


{adi:template inpage_error_display}


</div>


<div class="adi_bd_sp adi_importer_captcha_frm adi_dn">
	<div class="adi_head1 adi_mb3 adi_serv_head">{adi:phrase adi_importer_captcha_header}</div>
	<div class="adi_bb adi_mb5" style="margin-bottom:35px;"></div>
	<div class="adi_irc_loading_out">
		<div class="adi_irc_loading_txt">{adi:phrase adi_default_message_for_all_popups}</div>
		<img src="{adi:const THEME_URL}/images/loading.gif" class="adi_cli">
	</div>

	<div class="adi_irc_form_out adi_dn">
		
	</div>
</div>







<div class="adi_bd_sp adi_alt_frame adi_contfile_instr_block adi_dn">
{adi:template contact_file}
</div>

<script type="text/javascript">
adirs.set_serv_list();
function adi_show_default()
{
	adjq('.adi_alt_frame').hide();
	adjq('.adi_dflt_inter').show();
}
function adi_show_cf_instructs(e)
{
	if(e.preventDefault) { e.preventDefault(); }
	adjq('.adi_alt_frame').hide();
	adjq('.adi_contfile_instr_block').show(200);
}

function set_linkedin_form()
{
	adjq('.adi_type2_file_input').change(function(){
		var fn='';
		if(this.files && this.files[0] && this.files[0]['name']) {
			fn=this.files[0]['name'];
		}
		else {
			var mt = adjq(this).val().match(/[^\\\/]+\..+/);
			if(mt) {
				fn = mt[0];
			}
		}
		if(fn != '' && fn.length > 40) {
			fn =fn.slice(0,15)+'...'+fn.slice(-7);
		}
		adjq('.adi_type2_selected_file').html(fn);
	});

	adjq('.adi_nc_type2_form').submit(function(e){
		adjq('.adi_err_msg').html('');
		var cl = adjq('.adi_type2_file_input', this).val();
		if(adi.trim(cl) == '')
		{
			adi.show_pp_err(adi.phrases['adi_msg_contact_file_not_selected']);
		}
		else if(adjq('.adi_type2_file_input', this).get(0).files[0].size > adi.cflt) {
			adi.show_pp_err(adi.phrases['adi_msg_contact_file_size_limit_exceeded']);
		}
		else if(!cl.toLowerCase().match(/\.csv$|\.ldif$|\.vcf$|\.txt$/))
		{
			adi.show_pp_err(cl+adi.phrases['adi_msg_invalid_contact_file_format']);
		}
		else 
		{
			adirs_send_effect(this);
			return true;
		}
		e.preventDefault();
		return false;
	});
}
set_linkedin_form();
adi_search.init();
</script>
