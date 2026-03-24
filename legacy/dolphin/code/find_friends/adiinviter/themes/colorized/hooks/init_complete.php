<?php

/*********************** Theme Config *************************/
// Number of contacts to be displayed on one page.
$adi_page_size = 10;

// Number of invites to be displayed on one page in Invite History.
$adi_invites_page_size = 10;

$adiinviter->default_no_avatar = $adiinviter->theme_url . '/images/adiinviter_no_avatar.png';

// Enable Contact Cache Mechanism
$adiinviter->enable_contacts_cache = true;
/**************************************************************/



// Turn off unsupported features
$adiinviter->show_recaptcha = false;

if(isset($adi_current_model) && in_array($adi_current_model, array('popup', 'inpage')))
{
	$adiinviter->requireSettingsList(array('oauth'));
	$on_services = $adiinviter->settings['services_onoff']['on'];
	if(!is_array($on_services))
	{
		$on_services = array();
	}
}


?>