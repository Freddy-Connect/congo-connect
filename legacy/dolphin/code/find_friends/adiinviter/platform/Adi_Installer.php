<?php



class Adi_Installer_Platform extends Adi_Installer_Base
{
	public $default_settings    = array();
	public $campaigns_list = array();
	function get_default_settings()
	{
		$site_name = getParam('site_title');
		$webmaster_name  = getParam('site_title');
		$webmaster_email = getParam('site_email');

		$website_root_url = trim(BX_DOL_URL_ROOT, ' /');
		$adiinviter_root_url = $website_root_url.'/find_friends';
		
		$table_prefix = '';

		bx_import('BxDolModule');
		$avatar_posts_tbname = 'bx_blogs_avatar';
		$module = adi_fetch_array(adi_query_read("SELECT * FROM sys_modules WHERE uri = 'avatar'"));
		if($module && count($module) > 0)
		{
			$ss = new BxDolModule($module);
			$avatar_posts_tbname = $ss->_oConfig->_sDbPrefix.'images';
		}

		$this->default_settings = array(
			'global' => array(
				'adiinviter_theme'                => $this->adi->default_themeid,
				'adiinviter_website_name'         => $site_name,
				'adiinviter_root_url'             => $adiinviter_root_url,
				'adiinviter_website_root_url'     => $website_root_url,
				'adiinviter_website_register_url' => $website_root_url.'/join.php?invitation_id=[invitation_id]',
				'adiinviter_website_login_url'    => $website_root_url,
				'adiinviter_sender_name'          => $webmaster_name,
				'adiinviter_email_address'        => $webmaster_email,
			),
			'db_info' => array(
				'avatar_table' => array(
					'table_name' => $avatar_posts_tbname,
					'userid'     => 'id',
					'avatar'     => 'author_id',
				),
				'usergroup_mapping' => array(
					'table_name'  => '',
					'userid'      => '',
					'usergroupid' => '',
				),
				'user_table' => array(
					'table_name'   => 'profiles',
					'userid'       => 'ID',
					'username'     => 'NickName',
					'userfullname' => 'FirstName,LastName',
					'email'        => 'Email',
					'usergroupid'  => 'Role',
					'avatar'       => 'Avatar',
				),
				'adiinviter_avatar_url' => $website_root_url.'/modules/boonex/avatar/data/images/[avatar_value].jpg',
				'adiinviter_profile_page_url' => $website_root_url.'/profile.php?ID=[userid]/',
				'usergroup_table' => array(
					'table_name'  => '',
					'usergroupid' => '',
					'name'        => '',
				),
				'friends_table' => array(
					'table_name'    => 'sys_friend_list',
					'userid'        => 'ID',
					'friend_id'     => 'Profile',
					'status'        => 'Check',
					'yes_value'     => '1',
					'pending_value' => '0',
				),
			),
		);

		return $this->default_settings;
	}

	function finish_installation()
	{
		// Initiate Content share installation sequence
		$this->install_default_campaigns();
	}

	function install_default_campaigns()
	{
		$website_root_url = trim(BX_DOL_URL_ROOT, ' /');

		$blogs_posts_tbname = 'bx_blogs_posts';
		$module = adi_fetch_array(adi_query_read("SELECT * FROM sys_modules WHERE uri = 'blogs'"));
		if($module && count($module) > 0)
		{
			bx_import('BxDolModule');
			$ss = new BxDolModule($module);
			$blogs_posts_tbname = $ss->_oConfig->sSQLPostsTable;

			$this->campaigns_list = array(
				'blog_share' => array(
					'title'                    => 'Blog Share',
					'content_desc'             => 'For sharing blog post contents.',
					'campaign_on_off'          => '0',
					'redirection_on_off'       => '1',
					'word_limit'               => '200',
					'content_page_url'         => $website_root_url . '/modules/boonex/blogs/blogs.php?action=show_member_post&post_id=[content_id]',
					'restricted_usergroup_ids' => '',
					'restricted_user_ids'      => '',
					'attachment'               => '1',
					'content_table' => array(
						'table_name'    => $blogs_posts_tbname,
						'content_id'    => 'PostID',
						'content_body'  => 'PostText',
						'content_title' => 'PostCaption',
						'category_id'   => 'Categories',
						'url_alias'     => 'PostUri',
					),
				),
			);
			$this->installCampaign($this->campaigns_list);
		}
	}

	function get_campaigns_list() {}
	
	function before_installation()
	{
		$this->update_admin_settings(array(
			'adiinviter_db_type'      => 'mysql',
			'adiinviter_hostname'     => DATABASE_HOST,
			'adiinviter_username'     => DATABASE_USER,
			'adiinviter_password'     => DATABASE_PASS,
			'adiinviter_dbname'       => DATABASE_NAME,
			'adiinviter_table_prefix' => '',
		));
	}
}


?>