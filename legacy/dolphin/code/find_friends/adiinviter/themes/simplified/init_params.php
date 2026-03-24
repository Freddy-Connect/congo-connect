<?php

$adi_type                 = '';
$adi_search_query         = '';
$adi_search_prev_query    = '';
$adi_page_no              = 1;
$adi_page_offset          = 0;
$adi_total_pages          = 1;
$adi_search_results_count = 0;
$adi_friends_added_count  = 0;
$added_friends_ids        = array();
$invitation_sent_ids      = array();
$adi_conts_model          = 1;

// Get Cache ID from $_POST
$list_cache = array();
$adi_conts_list_id = AdiInviterPro::POST('adi_list_id', ADI_STRING_VARS);
if(!empty($adi_conts_list_id))
{
	$list_cache = adimt_get_cache_data($adi_conts_list_id);

	$list_userid = isset($list_cache['userid']) ? $list_cache['userid']+0 : 0;

	if(is_array($list_cache) && count($list_cache) > 0 && isset($list_cache['data']) && 
		isset($list_cache['userid']) && $list_cache['userid']+0 === $adiinviter->userid)
	{
		$contacts            = $list_cache['data']['conts'];
		$registered_contacts = $list_cache['data']['reg_conts'];
		$info                = $list_cache['data']['reg_info'];
		$config              = $list_cache['data']['config'];
		$adi_conts_model     = $list_cache['data']['conts_model'];
		$added_friends_ids   = $list_cache['data']['fr_adds'];
		$invitation_sent_ids = $list_cache['data']['sent_ids'];

		$adi_enable_pagination = true;
		$adi_enable_typeahead = false;
		if($adi_conts_model == 2)
		{
			$adi_enable_pagination = false;
			$adi_enable_typeahead = true;
		}

		$adi_type = AdiInviterPro::POST('adi_type', ADI_STRING_VARS);
		if(!empty($adi_type) && !in_array($adi_type, array('reg_conts', 'conts')))
		{
			$adi_type = '';
		}
		if($adi_type == 'reg_conts' && (count($registered_contacts) == 0 || $adiinviter->settings['adiinviter_show_already_registered'] == 0))
		{
			$adi_type = 'conts';
		}

		$adi_search_prev_query = AdiInviterPro::POST('adi_search_prev_query' , ADI_STRING_VARS);
		$adi_search_query      = AdiInviterPro::POST('adi_search_query'      , ADI_STRING_VARS);

		if($adiinviter->friends_system)
		{
			$add_all_friends = AdiInviterPro::POST('add_all_friends' , ADI_STRING_VARS);
			$add_as_friend   = AdiInviterPro::POST('add_as_friend'   , ADI_STRING_VARS);
			$adi_reg_ids     = AdiInviterPro::POST('adi_reg_ids'     , ADI_ARRAY_VARS);

			$add_new_friends = false; $add_new_friends_ids = array();
			if(!empty($add_all_friends) && count($registered_contacts) > 0) {
				$add_new_friends_ids = array_diff(array_keys($registered_contacts), $added_friends_ids);
			}
			else if(!empty($add_as_friend) && count($adi_reg_ids) > 0) {
				$add_new_friends_ids = $adi_reg_ids;
			}
			if(count($add_new_friends_ids) > 0 && count($registered_contacts) > 0)
			{
				$reg_ids = array_map('intval', $add_new_friends_ids);
				if(count($reg_ids) > 0)
				{
					$result = $adiinviter->send_friend_request($adiinviter->userid, $reg_ids);
					$adi_friends_added_count = count($result);
					$adi_ip_add_friends_success_msg = adi_replace_vars($adiinviter->phrases['adi_ip_add_friends_success_msg'], array(
						'friends_count' => $adi_friends_added_count,
					));
					$added_friends_ids = array_unique(array_merge($added_friends_ids, $reg_ids));
					$list_cache['data']['fr_adds'] = $added_friends_ids;
					$result = adimt_update_list_cache($adi_conts_list_id, $list_cache['data']);
				}
			}
		}

		if(!empty($adi_search_query) && !empty($adi_type))
		{
			$adi_sq = strtolower(trim($adi_search_query));
			if($adi_type == 'reg_conts')
			{
				$pag_registered_contacts = array();
				foreach($registered_contacts as $reg_id => $reg_details)
				{
					$haystack = strtolower(' '.$reg_details['name'].' '.$info[$reg_id]['username'].' '.$reg_details['email']);
					if(strpos($haystack, ' '.$adi_sq) !== false)
					{
						$pag_registered_contacts[$reg_id] = $reg_details;
						$adi_search_results_count++;
					}
				}
				$registered_contacts = $pag_registered_contacts;
			}
			else if($adi_type == 'conts')
			{
				$pag_contacts = array();
				foreach($contacts as $email_id => $details)
				{
					$haystack = strtolower(' '.$details['name']);
					if($config['email'] == 1) { $haystack .= ' '.$email_id; }
					if(strpos($haystack, ' '.$adi_sq) !== false)
					{
						$pag_contacts[$email_id] = $details;
						$adi_search_results_count++;
					}
				}
				$contacts = $pag_contacts;
			}
		}

		$adi_page_no = AdiInviterPro::POST('adi_page_no', ADI_INT_VARS);
		$adi_page_no = max(1, $adi_page_no);
	}
}
if(count($list_cache) == 0)
{
	$adi_conts_list_id = '';
}

?>