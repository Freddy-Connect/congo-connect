<?php
$include_adiinviter = false;
global $adiinviter;
if(is_null($adiinviter))
{
	$base_path = dirname(__FILE__);
	include_once($base_path.DIRECTORY_SEPARATOR.'adiinviter'.DIRECTORY_SEPARATOR. 'adiinviter_bootstrap.php');

	if($adiinviter->adiinviter_installed)
	{
		$adiinviter->requireSettingsList(array('global','db_info'));
		$adiinviter->init_user();
	}
}
$include_adiinviter = ($adiinviter->adiinviter_installed && (bool)$adiinviter->can_use_adiinviter && (bool)$adiinviter->settings['adiinviter_onoff']);
?>