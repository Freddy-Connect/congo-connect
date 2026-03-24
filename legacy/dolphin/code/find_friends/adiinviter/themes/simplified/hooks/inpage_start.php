<?php

$show_contacts_interface  = false;
$show_direct_contacts = false;

$adi_initparams_path = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'init_params.php';
include($adi_initparams_path);



if(!empty($adi_conts_list_id))
switch($do)
{
	case 'paginate_conts':
		$show_contacts_interface = true;

		if($adi_type == 'conts' && empty($adi_search_query) && count($contacts) == 0)
		{
			if($adi_friends_added_count > 0)
			{
				$block_header_text  = $adiinviter->phrases['adi_invite_block_header'];
				$block_message_text = $adi_friends_added_count.' friend requests sent successfully.';
			}
			else
			{
				$block_header_text  = $adiinviter->phrases['adi_invite_block_header'];
				$block_message_text = $adiinviter->phrases['adi_ip_block_default_message'];
			}

			$contents .= eval(adi_get_template('inpage_final_message'));
			$show_contacts_interface = false;
			$load_default_template = false;
		}
	break;

	case 'submit_invite_sender':
		$adi_conts = array();
		if(isset($contacts) && count($contacts) > 0)
		{
			foreach($contacts as $email_id => $details)
			{
				if(!in_array($email_id, $invitation_sent_ids))
				{
					$adi_conts[$email_id] = $details['name'];
				}
			}
		}
		$_POST['adi_conts'] = isset($adi_conts) ? $adi_conts : array();
		$_POST['adi_service_key_val'] = isset($config['service_key']) ? $config['service_key'] : '';

		$adi_reset_list_id = AdiInviterPro::POST('adi_reset_list_id', ADI_STRING_VARS);
		if(!empty($adi_reset_list_id))
		{
			adimt_clear_list_cache($adi_reset_list_id);
		}
	break;

	default:

	break;
}



$adi_initparams_path = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'format_contacts.php';
include($adi_initparams_path);

?>