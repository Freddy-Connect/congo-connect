<?php

if(AdiInviterPro::isPOST('adi_ih_ids_list'))
{
	$inv_ids = AdiInviterPro::POST('adi_ih_ids_list', ADI_ARRAY_VARS);
	if(is_array($inv_ids) && count($inv_ids) > 0 && $adiinviter->can_delete_invites)
	{
		$invite_history = adi_allocate_pack('Adi_Invite_History');
		$invite_history->delete_invites($inv_ids);
	}
}

?>