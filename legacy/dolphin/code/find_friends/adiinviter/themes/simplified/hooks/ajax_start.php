<?php

$show_contacts_interface  = false;
$show_direct_contacts = false;

$adi_initparams_path = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'init_params.php';
include($adi_initparams_path);


switch ($do)
{
	case 'paginate_conts':
		if(!empty($adi_conts_list_id))
		{
			$show_contacts_interface = true;
			$show_direct_contacts = true;
		}
	break;

	case 'send_user_invitation':
		$cont_id = AdiInviterPro::POST('adi_cont_id', ADI_STRING_VARS);
		if(!empty($cont_id) &&is_array($list_cache) && count($list_cache) > 0 && isset($list_cache['data']) && isset($list_cache['userid']) && $list_cache['userid']+0 === $adiinviter->userid)
		{
			$list_contacts = $list_cache['data']['conts'];
			$list_config   = $list_cache['data']['config'];
			$list_invitation_sent_ids = $list_cache['data']['sent_ids'];

			if(isset($list_contacts[$cont_id]))
			{
				$inviting_contacts = array($cont_id => $list_contacts[$cont_id]['name']);
				$campaign_id = $list_config['campaign_id'];
				$content_id = $list_config['content_id'];
				$adiinviter->loadPhrases();
				$adi_sender_name = ''; $adi_sender_email = ''; $attach_note = '';

				$handler_file = ADI_LIB_PATH.'invitation_handler.php';
				require_once($handler_file);
				$inv_handler = adi_allocate('Adi_Invitations');
				$inv_handler->set_invitation_type($campaign_id, $content_id);
				$inv_handler->set_attached_note($attach_note);

				$inv_handler->init($list_config, $inviting_contacts);
				$success_count = $inv_handler->send_invitations();
				echo $success_count;

				$list_cache['data']['sent_ids'] = array_unique(array_merge($list_cache['data']['sent_ids'], array($cont_id)));
				$result = adimt_update_list_cache($adi_conts_list_id, $list_cache['data']);
			}
		}
	break;

	case 'get_importer_captcha':
		$service_key = AdiInviterPro::POST('adi_service_key_val' , ADI_STRING_VARS);
		$user_email  = AdiInviterPro::POST('adi_user_email'      , ADI_STRING_VARS);
		$service_key_match = '';
		if($adi_current_model == 'popup' && empty($service_key) && !empty($user_email))
		{
			$service_key_match = $adiinviter->get_service_from_email($user_email);
			if($service_key_match && !empty($service_key_match))
			{
				$_POST['adi_service_key_val'] = $service_key_match;
			}
		}
	break;

	case 'get_contacts':
		$service_key = AdiInviterPro::POST('adi_service_key_val' , ADI_STRING_VARS);
		$user_email  = AdiInviterPro::POST('adi_user_email'      , ADI_STRING_VARS);
		$service_key_match = '';
		if($adi_current_model == 'popup' && empty($service_key) && !empty($user_email))
		{
			$service_key_match = $adiinviter->get_service_from_email($user_email);
			if($service_key_match && !empty($service_key_match))
			{
				$_POST['adi_service_key_val'] = $service_key_match;
			}
		}
		$adi_captcha_text = AdiInviterPro::POST('adi_captcha_text', ADI_STRING_VARS);
		if(!empty($service_key_match) && empty($adi_captcha_text))
		{
			$adi_services = adi_allocate_pack('Adi_Services');
			$adi_services->get_service_details($service_key_match, 'info');
			if(in_array($service_key_match, $adi_services->captcha_services))
			{
				echo 'adi_irc.reset();';exit;
			}
		}
	break;
	
	default:
		break;
}



$adi_initparams_path = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'format_contacts.php';
include($adi_initparams_path);

?>