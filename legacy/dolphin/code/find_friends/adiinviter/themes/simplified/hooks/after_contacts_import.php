<?php

/*$adi_conts_model = AdiInviterPro::POST('adi_conts_model', ADI_INT_VARS);
$adi_conts_model = $adi_conts_model != 2 ? 1 : 2;

$adi_enable_pagination = $adi_enable_typeahead = false;
if($adi_conts_model == 2)
{
	$adi_enable_pagination = false;
	$adi_enable_typeahead = true;
}*/

/*$adi_conts_list_id = '';
$cache_data = $added_friends_ids = $invitation_sent_ids = array();
if( $adiinviter->error->get_error_count() == 0 && (count($contacts) > 0 || count($registered_contacts) > 0 ) )
{
	$cache_data = array(
		'conts'       => $contacts,
		'reg_conts'   => $registered_contacts,
		'reg_info'    => $info,
		'config'      => $config,
		'conts_model' => $adi_conts_model,
		'fr_adds'     => $added_friends_ids,
		'sent_ids'    => $invitation_sent_ids,
	);
	$result = adimt_cache_contacts($cache_data);
	if($result)
	{
		$adi_conts_list_id = $result;
	}
}*/

if(AdiInviterPro::isPOST('adi_oauth') && AdiInviterPro::POST('adi_oauth', ADI_STRING_VARS) == 'show_contacts')
{
	$contents = eval(adi_get_template('oauth_redirect'));
	echo $contents; exit;
}

?>