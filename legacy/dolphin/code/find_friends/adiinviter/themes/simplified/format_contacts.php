<?php

if($show_contacts_interface === true)
{
	if(!isset($config) || count($config) == 0)
	{
		$show_contacts_interface = false;
	}
}
if($show_contacts_interface === true)
{
	$show_friend_adder = $show_invites_sender = false;

	$adi_phrase_vars = array(
		'website_url'         => $adiinviter->settings['adiinviter_root_url'],
		'adiinviter_root_url' => $adiinviter->settings['adiinviter_website_root_url'],
		'login_url'           => $adiinviter->settings['adiinviter_website_login_url'],
		'register_url'        => $adiinviter->settings['adiinviter_website_register_url'],

		'service_key'         => $config['service_key'],
		'service_name'        => $config['service'],

		'invitation_id'       => '',
		'registered_count'    => isset($registered_contacts) ? count($registered_contacts) : 0,
		'conts_count'         => isset($contacts) ? count($contacts) : 0,
	);

	if($adi_type == 'conts')
	{
		$show_invites_sender = true;
		$contacts_count = count($contacts);
		
		$adi_conts_data = array();
		foreach($contacts as $cont_id => $details)
		{
			$adi_conts_data[] = array(
				$cont_id,
				$details['name'],
				isset($details['avatar']) ? $details['avatar'] : '',
				in_array($cont_id, $invitation_sent_ids) ? 1 : 0,
			);
		}
		$adi_conts_data_json = @json_encode($adi_conts_data);
	}
	else if($adi_type == 'reg_conts')
	{
		$show_friend_adder = true;
		$contacts_count = count($registered_contacts);

		$adi_conts_data = array();
		foreach($registered_contacts as $reg_id => $reg_details)
		{
			$adi_conts_data[] = array(
				$reg_id,                       /* userid        */
				$info[$reg_id]['username'],    /* username      */
				$reg_details['email'],
				// $reg_details['email'],         /* email         */
				$reg_details['name'],          /* name          */
				$info[$reg_id]['avatar'],      /* avatar        */
				$reg_details['friends'],       /* friends       */
				$reg_details['friend_status'], /* friend_status */
				// adi_get_mutual_link_text(count($reg_details['friends'])), /* MF link text */
				in_array($reg_id, $added_friends_ids) ? 1 : 0,
			);
		}
		$adi_conts_data_json = @json_encode($adi_conts_data);

		if($adiinviter->userid != 0)
		{
			if($adiinviter->friends_system) {
				$fa_top_head = adi_replace_vars($adiinviter->phrases['fa_top_head_in_user_friend'], $adi_phrase_vars);
				$fa_top_head2 = adi_replace_vars($adiinviter->phrases['fa_top_head2_in_user_friend'], $adi_phrase_vars);
			}
			else {
				$fa_top_head = adi_replace_vars($adiinviter->phrases['fa_top_head_in_user'], $adi_phrase_vars);
				$fa_top_head2 = adi_replace_vars($adiinviter->phrases['fa_top_head2_in_user'], $adi_phrase_vars);
			}
		}
		else
		{
			if($adiinviter->friends_system) {
				$fa_top_head = adi_replace_vars($adiinviter->phrases['fa_top_head_in_guest_user_friend'], $adi_phrase_vars);
				$fa_top_head2 = adi_replace_vars($adiinviter->phrases['fa_top_head2_in_guest_user_friend'], $adi_phrase_vars);
			}
			else {
				if($adiinviter->user_registration_system == true) {
					$fa_top_head = adi_replace_vars($adiinviter->phrases['fa_top_head_in_guest_user'], $adi_phrase_vars);
					$fa_top_head2 = adi_replace_vars($adiinviter->phrases['fa_top_head2_in_guest_user'], $adi_phrase_vars);
				}
				else {
					$fa_top_head = adi_replace_vars($adiinviter->phrases['fa_top_head_in_user'], $adi_phrase_vars);
					$fa_top_head2 = adi_replace_vars($adiinviter->phrases['fa_top_head2_in_user'], $adi_phrase_vars);
				}
			}
		}
	}

	if($adiinviter->error->get_error_count() == 0)
	{
		if($show_direct_contacts)
		{
			if($adi_type == 'conts')
			$contents .= eval(adi_get_template('non_registered_contacts'));
		}
		else
		{
			$file_path = ADI_LIB_PATH.'get_contacts_phrases.php';
			include($file_path);
		}
		$load_default_template = false;
	}
}



?>