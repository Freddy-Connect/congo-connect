<?php 

class AdiInviterPro_Platform extends AdiInviterPro_Base
{
	public $adi_admincp_folder       = 'administration/adi_admincp';
	public $default_themeid          = 'default';
	public $current_platform         = 'Boonex Dolphin';
	public $current_platform_version = '7.1.x';
	public $max_page_width           = 1040;
	
	public $verify_invitation_url = '[website_root_url]/verify_invitation.php';
	public $invite_history_url    = '[website_root_url]/invite_history.php';
	public $inpage_model_url      = '[website_root_url]/find_friends.php';
	public $popup_model_url       = '[website_root_url]/';

	function getLoggedInUserId()
	{
		return getLoggedId();
	}
	function getLoggedInUsergroupId()
	{
		$userid = getLoggedId();
		return ($userid > 0 ? 1 : 0);
	}
	function getGuestUsergroupId()
	{
		return 0;
	}
	function system_pre_init()
	{
		global $admin_dir;
		$this->website_root_path = dirname(ADI_BASE_PATH);
		if(isset($admin_dir)) {
			$this->adi_admincp_folder = $admin_dir.'/adi_admincp';
		}
		else {
			$this->adi_admincp_folder = 'administration/adi_admincp';
		}
	}
	function set_platform_admin_url()
	{
		$website_url = $this->getWebsiteURL();
		global $admin_dir;
		if(isset($admin_dir)) {
			$this->platform_admincp_url = $website_url.'/'.$admin_dir.'/index.php';
		}
		else {
			$this->platform_admincp_url = $website_url.'/administration/index.php';
		}
		return $this->platform_admincp_url;
	}

	function system_init()
	{
		$userid = getLoggedId();
		$this->dl_user = getProfileInfo($userid);
	}

	function settingsLoaded($sg_name)
	{
		if($sg_name == 'db_info')
		{
			$bx_modules = new BxDolModuleDb();
			if(!$bx_modules->isModule('avatar'))
			{
				$this->avatar_system = false;
			}
		}
	}
	function get_platform_lang_ids()
	{
		$lang_ids  = array();
		$query     = "SELECT Name FROM sys_localization_languages";
		$result    = adi_query_read($query);
		while($row = adi_fetch_array($result))
		{
			$lang_tag = $row['Name'];
			if(strpos($lang_tag, '-') !== false)
			{
				$t = explode('-', $lang_tag);
				$lang_tag = $t[0];
			}
			if(!empty($lang_tag) && strlen($lang_tag) == 2)
			{
				$lang_ids[] = $lang_tag;
			}
		}
		return $lang_ids;
	}

	// Specific functions for Dolphin 
	function getUserAvatarUrl($userid, $username = NULL, $email = NULL, $avatar_value = NULL)
	{
		$avatar_url = $this->default_no_avatar;
		$user_info = getProfileInfo($userid);
		if($user_info['Avatar'] != 0)
		{
			include_once (BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/include.php');
	 		$avatar_url = BX_AVA_URL_USER_AVATARS . $user_info['Avatar'] . BX_AVA_EXT;
		}
		return $avatar_url;
	}

	function add_friend_request_record($my_id , $friend_id)
	{
		$fr_table  = $this->friends_table;
		$fr_fields = $this->friends_fields;
		$query = "INSERT INTO `sys_friend_list` SET `ID` = '".$my_id."', `Profile` = '".$friend_id."', `Check` = '1'";
		return adi_query_write($query);
	}
	function getAllUsergroupsInfo()
	{
		$usergroups = array(
			BX_DOL_ROLE_GUEST     => 'Guest',
			BX_DOL_ROLE_MEMBER    => 'Member',
			BX_DOL_ROLE_ADMIN     => 'Administrator',
			BX_DOL_ROLE_AFFILIATE => 'Affiliate',
		);
		return $usergroups;
	}
}

?>