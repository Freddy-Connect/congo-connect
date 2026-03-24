<?php

class AdiInviter_Registration_Handler extends BxDolAlertsResponse
{
	// class constructor
	function AdiInviter_Registration_Handler() {}

	// system event
	function response($o)
	{
		if ('profile' == $o->sUnit)
		{
			switch ($o->sAction) {

				case 'join':
					$iProfileId = $o->iObject;
					$adiinviter_userid = $iProfileId;
					if($adiinviter_userid != 0)
					{
						global $dir;
						global $adiinviter;
						if(is_null($adiinviter)) 
						{
							include_once($dir['root'].'find_friends'.DIRECTORY_SEPARATOR.'adiinviter'.DIRECTORY_SEPARATOR.'adiinviter_bootstrap.php');
						}
						$invitation_id = isset($_POST['adi_invitation_id']) ? $_POST['adi_invitation_id'] : '';
						$result = $adiinviter->set_as_registered($invitation_id, $adiinviter_userid);
					}
				break;	

			}
		}
	}
}
