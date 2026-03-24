<?php


global $adiinviter;
if(is_null($adiinviter))
{
	$file_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'adiinviter'.DIRECTORY_SEPARATOR.'adiinviter_bootstrap.php';
	include_once($file_path);
}

if(!is_null($adiinviter) && $adiinviter->adiinviter_installed)
{
	$adiinviter->requireSettingsList(array('global','db_info'));
	$adiinviter->init_user();
	if((int)$adiinviter->settings['adiinviter_onoff'] == 1)
	{
		global $site;
		$adiinviter_root_url_rel = $adiinviter->settings['adiinviter_root_url_rel'];
		echo '
		<link href="'.$adiinviter_root_url_rel.'/adiinviter_css.php" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="'.$adiinviter_root_url_rel.'/adiinviter/js/jquery.min.js"></script>
		<script type="text/javascript" src="'.$adiinviter_root_url_rel.'/adiinviter/js/adiinviter.js"></script>
		<script type="text/javascript" src="'.$adiinviter_root_url_rel.'/adiinviter_params.php"></script>
		';

		$embed_js = '';
		$adi_invitation_id = AdiInviterPro::GET('invitation_id', ADI_STRING_VARS);
		$adi_invitation_id = AdiInviterPro::isPOST('adi_invitation_id') ? AdiInviterPro::POST('adi_invitation_id', ADI_STRING_VARS) : $adi_invitation_id;
		$adi_invitation_id = stripslashes(trim($adi_invitation_id));

		if(!empty($adi_invitation_id))
		{
			$query = "SELECT receiver_email FROM adiinviter WHERE invitation_id = '".$adi_invitation_id."'";
			$row = mysql_fetch_array(mysql_query($query));
			if(count($row) > 0)
			{
				if(!empty($row['receiver_email']))
				{
					$embed_js .= '	jQuery(".form_input_text").each(function(){
				var nm = jQuery(this).attr("name");
				if(nm == "Email[0]" || nm == "Email[1]")
				{
					jQuery(this).val("'.$row['receiver_email'].'");
				}
			});';
				}

				//Update "visisted" flag
				$query = "UPDATE adiinviter set visited = 1 WHERE invitation_id = '".$adi_invitation_id."'";
				mysql_query($query);
			}

			$input_tag = ' <input type="hidden" name="adi_invitation_id" value="' . $adi_invitation_id . '" />';
			$embed_js .= '	jQuery("#join_form").append(\''.$input_tag.'\'); ';

			echo '<script type="text/javascript"> 
		jQuery(document).ready(function(){
		'.$embed_js.'
		});
		</script>';
		}
	}
}





?>