<?php

$adi_initparams_path = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'init_params.php';
include($adi_initparams_path);

$show_contacts_interface  = false;
$show_direct_contacts = false;

switch($do)
{
	case 'show_service_login':
		$service_key = AdiInviterPro::POST('adi_service_key_val' , ADI_STRING_VARS);
		$campaign_id = AdiInviterPro::POST('campaign_id'         , ADI_PLAIN_TEXT_VARS);
		$content_id  = AdiInviterPro::POST('content_id'          , ADI_INT_VARS);

		$allow_campaign_use = $adiinviter->is_campaign_allowed($campaign_id, $content_id);
		if(!$allow_campaign_use) {
			$campaign_id = ''; $content_id = 0;
		}

		$adi_service_name = '';
		if(!empty($service_key))
		{
			$adi_services = adi_allocate_pack('Adi_Services');
			$adiinviter_services = $adi_services->get_service_details($service_key, 'info');
			if(count($adiinviter_services) > 0) {
				$config = $adiinviter_services[$service_key]['info'];
				$config['service_key'] = $service_key;
				$adi_service_name = $config['service'];
			}
			else {
				$service_key = '';
			}
		}

		$contents = eval(adi_get_template('show_service_login'));
		$load_default_template = false;
	break;

	case 'show_contfile_import':
		$campaign_id  = AdiInviterPro::POST('campaign_id' , ADI_PLAIN_TEXT_VARS);
		$content_id  = AdiInviterPro::POST('content_id' , ADI_INT_VARS);

		$allow_campaign_use = $adiinviter->is_campaign_allowed($campaign_id, $content_id);
		if(!$allow_campaign_use) {
			$campaign_id = ''; $content_id = 0;
		}

		$contents = eval(adi_get_template('show_contfile_import'));
		$load_default_template = false;
	break;

	case 'show_manual_import':
		$campaign_id  = AdiInviterPro::POST('campaign_id' , ADI_PLAIN_TEXT_VARS);
		$content_id  = AdiInviterPro::POST('content_id' , ADI_INT_VARS);

		$allow_campaign_use = $adiinviter->is_campaign_allowed($campaign_id, $content_id);
		if(!$allow_campaign_use) {
			$campaign_id = ''; $content_id = 0;
		}
		
		$contents = eval(adi_get_template('show_manual_import'));
		$load_default_template = false;
	break;

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

		$_POST['campaign_id'] = $config['campaign_id'];
		$_POST['content_id'] = $config['content_id'];

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