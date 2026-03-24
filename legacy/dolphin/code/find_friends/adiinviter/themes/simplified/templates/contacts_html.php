<?php

// Contact HTML for popup display

// Registered Contacts
/*
 - Enter HTML code for registered Contact which has no mutual friends with inviter.
 - Params
 	[member_username] : Username
 */
$member_without_mutual_friends = '
<div class="adi_cont_blk adi_reg_cont_blk [is_sent]" id="adi_sent_invite_[contact_no]">
	<input type="checkbox" name="adi_reg_ids[]" value="[member_userid]" class="adi_dn" id="adi_contact_[contact_no]">
	<div class="adi_cont_details adi_fleft">
	<table class="adi_cltb" style="width:100%; table-layout:fixed;">
	<tr class="adi_clt">
		<td class="adi_clt adi_avatar_cont">
			<img class="adi_clt adi_cont_avatar" src="[member_avatar]">
		</td>
		<td class="adi_clt adi_cont_details adi_vam" style="width:auto; overflow:hidden; white-space:nowrap;">
			<p class="adi_clt adi_txt adi_cont_name">[member_name]</p>
			<p class="adi_clt adi_txt adi_cont_email">[member_email]</p>
		</td>';
	if($adiinviter->friends_system && $adiinviter->userid != 0)
	{
		$member_without_mutual_friends .= '<td class="adi_clt adi_vam adi_cont_act">
			<center>
			<input type="button" class="adi_invite adi_send_invite" value="'.$adiinviter->phrases['adimt_add_friend_btn_txt'] .'" data-adiid="[member_userid]" data-listid="[contact_cacheid]" onclick="return adirs_cd.add_friend(this);" data-locid="[contact_no]">
			<div class="adi_working"></div>
			<div class="adi_done"></div>
			</center>
		</td>';
	}
	$member_without_mutual_friends .= '</tr>
	</table>
	</div>
	<div style="clear:both;"></div>
</div>';

/*
 - Enter HTML code for registered Contact which has one or more mutual friends with inviter.
 - Params
 	[member_username] : Username
 */
$member_with_mutual_friends = '
<div class="adi_cont_blk adi_reg_cont_blk [is_sent]" id="adi_sent_invite_[contact_no]">
	<input type="checkbox" name="adi_reg_ids[]" value="[member_userid]" class="adi_dn" id="adi_contact_[contact_no]">
	<div class="adi_cont_details adi_fleft">
	<table class="adi_cltb" style="width:100%; table-layout:fixed;">
	<tr class="adi_clt">
		<td class="adi_clt adi_avatar_cont">
			<img class="adi_clt adi_cont_avatar" src="[member_avatar]">
		</td>
		<td class="adi_clt adi_cont_details" style="width:auto; overflow:hidden; white-space:nowrap;">
			<p class="adi_clt adi_txt adi_cont_name">[member_name]</p>
			<p class="adi_clt adi_txt adi_cont_email">[member_email]</p>
		</td>';
	if($adiinviter->friends_system && $adiinviter->userid != 0)
	{
		$member_with_mutual_friends .= '<td class="adi_clt adi_vam adi_cont_act">
			<center>
				<input type="button" class="adi_invite adi_send_invite" value="'.$adiinviter->phrases['adimt_add_friend_btn_txt'] .'" data-adiid="[member_email]" data-listid="[contact_cacheid]" onclick="return adirs_cd.add_friend(this);" data-locid="[contact_no]">
				<div class="adi_working"></div>
				<div class="adi_done"></div>
			</center>
		</td>';
	}
	$member_with_mutual_friends .= '</tr>
	</table>
	</div>
	<div style="clear:both;"></div>
</div>';


/*
 - Enter HTML code for displaying single mutual friend. 
 - This code will be repeated for every single mutual friend.
 - After that, this list will be replaced in registered contact html(specified above) by replacing the {adi_var:mf_list}
 */

// Mutual friend HTMl with profile page system
$mutual_friends_list_with_profile_page = '
<div class="adi_ip_mf_prof_lnk">
	<a href="[mf_profile_page]" target="_blank">
		<img class="adi_mf_avatar" src="[mf_avatar]" data="[mf_username]">
	</a>
</div>
';

// Mutual friend HTMl without profile page system
$mutual_friends_list_without_profile_page = '
<div class="adi_ip_mf_prof_lnk">
	<img class="adi_mf_avatar" src="[mf_avatar]" data="[mf_username]">
</div>
';




// Non-registered Contacts

/*
 - Following code will be used for displaying non-registered contacts according to the contact type.
 - [contact_status]
 */

/*
 - Enter HTML code for displaying social contact with Avatar URL.
 */
$social_contact_with_avatar = '
<div class="adi_cont_blk adi_nonreg_cont_blk [is_sent]" id="adi_sent_invite_[contact_no]">
	<input type="checkbox" name="adi_conts[[contact_id]]" value="[contact_name]" class="adi_dn" id="adi_contact_[contact_no]">
	<div class="adi_cont_details adi_fleft">
	<table class="adi_cltb" style="width:100%; table-layout:fixed;">
	<tr class="adi_clt">
		<td class="adi_clt adi_avatar_cont">
			<img class="adi_clt adi_cont_avatar" src="[contact_avatar]">
		</td>
		<td class="adi_clt adi_cont_details" style="width:auto; overflow:hidden; white-space:nowrap;">
			<p class="adi_clt adi_txt adi_cont_name">[contact_name]</p>
		</td>
		<td class="adi_clt adi_vam adi_cont_act">
			<center>
				<input type="button" class="adi_invite adi_send_invite" value="'.$adiinviter->phrases['adimt_add_invite_btn_txt'] .'" data-adiid="[contact_id]" data-listid="[contact_cacheid]" onclick="return adirs_cd.send_invite(this);" data-locid="[contact_no]">
				<div class="adi_working"></div>
				<div class="adi_done"></div>
			</center>
		</td>
	</tr>
	</table>
	</div>
	<div style="clear:both;"></div>
</div>';
/*
 - Enter HTML code for displaying social contact without Avatar URL.
 */
$social_contact_without_avatar = '
<div class="adi_cont_blk adi_nonreg_cont_blk [is_sent]" id="adi_sent_invite_[contact_no]">
	<input type="checkbox" name="adi_conts[[contact_id]]" value="[contact_name]" class="adi_dn" id="adi_contact_[contact_no]">
	<div class="adi_cont_details adi_fleft">
	<table class="adi_cltb" style="width:100%; table-layout:fixed;">
	<tr class="adi_clt">
		<td class="adi_clt adi_cont_details" style="width:auto; overflow:hidden; white-space:nowrap;">
			<p class="adi_clt adi_txt adi_cont_name">[contact_name]</p>
		</td>
		<td class="adi_clt adi_vam adi_cont_act">
			<center>
				<input type="button" class="adi_invite adi_send_invite" value="'.$adiinviter->phrases['adimt_add_invite_btn_txt'] .'" data-adiid="[contact_id]" data-listid="[contact_cacheid]" onclick="return adirs_cd.send_invite(this);" data-locid="[contact_no]">
				<div class="adi_working"></div>
				<div class="adi_done"></div>
			</center>
		</td>
	</tr>
	</table>
	</div>
	<div style="clear:both;"></div>
</div>';

/*
 - Enter HTML code for displaying Webmail contact(email contact) with Avatar URL.
 */
$email_contact_with_avatar = '
<div class="adi_cont_blk adi_nonreg_cont_blk [is_sent]" id="adi_sent_invite_[contact_no]">
	<input type="checkbox" name="adi_conts[[contact_id]]" value="[contact_name]" class="adi_dn" id="adi_contact_[contact_no]">
	<div class="adi_cont_details adi_fleft">
	<table class="adi_cltb" style="width:100%; table-layout:fixed;">
	<tr class="adi_clt">
		<td class="adi_clt adi_avatar_cont">
			<img class="adi_clt adi_cont_avatar" src="[contact_avatar]">
		</td>
		<td class="adi_clt adi_cont_details adi_vam" style="width:auto; overflow:hidden; white-space:nowrap;">
			<p class="adi_clt adi_txt adi_cont_name">[contact_name]</p>
			<p class="adi_clt adi_txt adi_cont_email">[contact_id]</p>
		</td>
		<td class="adi_clt adi_vam adi_cont_act">
			<center>
				<input type="button" class="adi_invite adi_send_invite" value="'.$adiinviter->phrases['adimt_add_invite_btn_txt'] .'" data-adiid="[contact_id]" data-listid="[contact_cacheid]" onclick="return adirs_cd.send_invite(this);" data-locid="[contact_no]">
				<div class="adi_working"></div>
				<div class="adi_done"></div>
			</center>
		</td>
	</tr>
	</table>
	</div>
	<div style="clear:both;"></div>
</div>';



/*
 - Enter HTML code for displaying Webmail contact(email contact) without Avatar URL.
*/
$email_contact_without_avatar = '
<div class="adi_cont_blk adi_nonreg_cont_blk [is_sent]" id="adi_sent_invite_[contact_no]">
	<input type="checkbox" name="adi_conts[[contact_id]]" value="[contact_name]" class="adi_dn" id="adi_contact_[contact_no]">
	<div class="adi_cont_details adi_fleft">
	<table class="adi_cltb" style="width:100%; table-layout:fixed;">
	<tr class="adi_clt">
		<td class="adi_clt adi_cont_details" style="width:auto; overflow:hidden; white-space:nowrap;">
			<p class="adi_clt adi_txt adi_cont_name">[contact_name]</p>
			<p class="adi_clt adi_txt adi_cont_email">[contact_id]</p>
		</td>
		<td class="adi_clt adi_vam adi_cont_act">
			<center>
				<input type="button" class="adi_invite adi_send_invite" value="'.$adiinviter->phrases['adimt_add_invite_btn_txt'] .'" data-adiid="[contact_id]" data-listid="[contact_cacheid]" onclick="return adirs_cd.send_invite(this);" data-locid="[contact_no]">
				<div class="adi_working"></div>
				<div class="adi_done"></div>
			</center>
		</td>
	</tr>
	</table>
	</div>
	<div style="clear:both;"></div>
</div>';





?>