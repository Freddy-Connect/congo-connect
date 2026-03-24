<?php

if(!empty($adi_post_id) && is_numeric($adi_post_id))
{
	global $adiinviter;
	if(is_null($adiinviter)) 
	{
		include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'adiinviter'.DIRECTORY_SEPARATOR.'adiinviter_bootstrap.php');
	}
	if(!is_null($adiinviter) && $adiinviter->adiinviter_installed)
	{
		$adiinviter->requireSettingsList(array('global', 'db_info'));
		$adiinviter->init_user();

		$campaign_id = 'blog_share';
		$content_id = $adi_post_id;
		if($adiinviter->is_campaign_allowed($campaign_id, $content_id))
		{
			$adi_ret_val = "adi_open_popup_model({ campaign_id : '".$campaign_id."', content_id : ".$adi_post_id." });";
		}
	}
}

?>