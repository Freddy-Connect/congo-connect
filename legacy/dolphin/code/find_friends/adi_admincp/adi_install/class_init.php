<?php

$pre_settings = array(
	// Global Settings
	'global' => array(
		'adiinviter_store_guest_user_info'         => '1',
		'adiinviter_sender_name'                   => 'Default Sender Name',
		'adiinviter_onoff'                         => '1',
		'adiinviter_theme'                         => 'default',
		'adiinviter_themes_list'                   => '&adi_cBraceOpen;&adi_cBraceClose;',
		'adiinviter_root_url'                      => '',
		'adiinviter_website_root_url'              => '',
		'adiinviter_website_name'                  => 'Your Website Name',
		'check_for_updates_last_time'              => 0,
		'adiinviter_invite_already_invited'        => 1,
		'adiinviter_email_notification'            => '',
		'adiinviter_website_register_url'          => '',
		'adiinviter_website_logo'                  => '',
		'adiinviter_cookie_path'                   => '/tmp',
		'adiinviter_show_already_registered'       => '1',
		'adiinviter_show_already_invited_contacts' => '1',
		'adiinviter_website_login_url'             => '',
		'adiinviter_email_address'                 => '',
		'adiinviter_store_imported_contacts'       => '1',
		'language'                                 => 'en',
		'text_direction'                           => 'ltr',
		'captcha_public_key'                       => '',
		'captcha_private_key'                      => '',
		'max_contacts_count'                       => '2000',
		'contact_file_size_limit'                  => '1024',
		'contacts_list_length_limit'               => '50000',
		
		'services_onoff' => array(
			'on' => array("gmail", "yahoo", "hotmail", "aol", /*"linkedin",*/ "icloud", "twitter", "mailchimp", "mail_com", "eventbrite", "plaxo", "lycos", "viadeo", "laposte", "terra", "bol_com_br", "sapo", "iol_pt", "atlas", "gmx_net", "freenet_de", "web_de", "tonline", "xing", "wpl", "onet_pl", "interia", "o2", "virgilio", "libero", "email_it", "mynet", "citromail_hu", "india", "rediff", "qip", "mail_ru", "rambler", "yandex", "meta", "abv", "qq_com", "naver_com", "yeah", "ost_com", "ots_com", "daum_net", "sohu", "evite", "fastmail",),
			'off' => array(),
		),
	),

	// Database Information settings
	'db_info' => array(
		'avatar_table' => array(
			'table_name' => '',
			'userid'     => '',
			'avatar'     => '',
		),
		'usergroup_mapping' => array(
			'table_name'  => '',
			'userid'      => '',
			'usergroupid' => '',
		),
		'user_table' => array(
			'table_name'   => '',
			'userfullname' => '',
			'userid'       => '',
			'username'     => '',
			'email'        => '',
			'usergroupid'  => '',
			'avatar'       => '',
		),
		'adiinviter_avatar_url' => '',
		'adiinviter_profile_page_url' => '',
		'usergroup_table' => array(
			'table_name'  => '',
			'usergroupid' => '',
			'name'        => '',
		),
		'friends_table' => array(
			'table_name'    => '',
			'userid'        => '',
			'friend_id'     => '',
			'status'        => '',
			'yes_value'     => '',
			'pending_value' => '',
		),
		'usergroup_permisssions' => array(
			0 => array(1,1,1, 0,0,0, 'Unlimited'),
		),
	),

	// Invitation Settings
	'invitation' => array(
		'invitation_subject_en' => 'Invitation to Join [website_name]',
		'invitation_body_en' => '<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="format-detection" content="telephone=no"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
<title>Invitation to join [website_name]</title>

<style type="text/css">
@media all and (max-width: 400px) {
	.feature-column { width:100% !important; display: block; }
	.feature-text { margin-bottom: 50px; }
}
@media all and (max-width: 500px) {
	.feature-column { padding: 0px 10px !important; }
	.width-500-100 { width: 100% !important; }
	.mbot-30 { margin-bottom: 30px !important; }
	.leftright-text { padding-right: 0px !important; padding-left: 0px !important; }
}
</style>

</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" yahoo="fix" style="font-family: Verdana, Georgia, Times, serif; background-color:#FFFFFF; " bgcolor="FFFFFF">

<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
<tr><td align="center">

<div style="width:100%;max-width:600px;min-width:320px;">

	<table width="100%" border="0" cellpadding="0" cellspacing="0"  align="center" style="background-color: #FFFFFF;">

	<tr>
		<td style="background-color: #00BBF5;">

			<table border="0" cellpadding="0" cellspacing="0"  align="center" style="width:100%;">
			<tr>
				<td style="padding: 15px;"><img src="[invitation_assets_url]/default/logo.png"></td>
			</tr>
			<tr>
				<td align="center" style="padding: 15px;">
					<div style="font-size:14px; font-weight:bold;color: #ffffff;line-height: 21px;margin-bottom:25px;">INTRODUCING</div>
					<div style="font-size:38px;color: #ffffff;line-height: 25px;">Your Website</div>
				</td>
			</tr>
			<tr>
				<td align="center" style="padding: 15px;">
					<div style="width:100%;height:100%;max-width:190px;max-height:190px;background: url([invitation_assets_url]/default/avatar_bg.png) 0 0 no-repeat;margin: 0px auto;">
						<div style=""><img src="[sender_avatar_url]" style="margin: 20px;max-height:150px;max-heigh:150px;border-radius:50%;"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td align="center" style="padding: 15px;">
					<div style="font-size:18px;color: #ffffff;line-height: 25px;">Your friend [sender_name] invited you to Your Website.</div>
				</td>
			</tr>
			<tr>
				<td align="center" style="padding: 15px;">
					<a href="[verify_invitation_url]invitation_id=[invitation_id]&adi_do=accept" style="background-color:#007396;display:block;padding: 15px 15px;margin:0px 15px 20px 15px;width:100%;max-width:175px;font-size:15px;font-weight:bold;color: #ffffff;text-decoration: none;border-radius:4px;" class="">Get Started</a>
				</td>
			</tr>
			</table>

		</td>
	</tr>


	<tr>
		<td style="padding: 40px 20px;border: 1px solid #DDD;border-top: none;" align="center">
			<div style="display:inline;margin: 0 auto;">
			<table border="0" cellpadding="0" cellspacing="0"  align="center" style="width:100%;table-layout: fixed;">
			<tr>
				<td style="padding: 0px 20px;" align="center" class="feature-column">
					<table border="0" cellpadding="0" cellspacing="0"  align="center">
					<tr><td align="center">
						<img src="[invitation_assets_url]/default/feature1.png" style="margin-bottom: 10px;">
						<div style="font-size:16px;line-height: 22px;color: #68B975;" class="feature-text">Your Website<br>Feature #1</div>
					</td></tr>
					</table>
				</td>
				<td style="padding: 0px 20px;" align="center" class="feature-column">
					<table border="0" cellpadding="0" cellspacing="0"  align="center">
					<tr><td align="center">
					<img src="[invitation_assets_url]/default/feature2.png" style="margin-bottom: 10px;">
					<div style="font-size:16px;line-height: 22px;color: #ff7373;" class="feature-text">Your Website<br>Feature #2</div>
					</td></tr>
					</table>
				</td>
				<td style="padding: 0px 20px;" align="center" class="feature-column">
					<table border="0" cellpadding="0" cellspacing="0"  align="center">
					<tr><td align="center">
					<img src="[invitation_assets_url]/default/feature3.png" style="margin-bottom: 10px;">
					<div style="font-size:16px;line-height: 22px;color: #957bb7;" class="feature-text">Your Website<br>Feature #3</div>
					</td></tr>
					</table>
				</td>
			</tr>
			</table>
			</div>
		</td>
	</tr>


	<tr>
		<td style="padding: 50px 10px;border-left: 1px solid #DDD;border-right: 1px solid #DDD;" align="center">
			<table border="0" cellpadding="0" cellspacing="0"  align="center" style="width:100%;">
			<tr>
				<td style="padding: 10px;" align="center"><div style="text-align:center;">

					<table border="0" cellpadding="0" cellspacing="0"  align="right" width="44%" class="width-500-100 mbot-30">
					<tr><td align="center">
						<img src="[invitation_assets_url]/default/browser.png" style="width:100%;max-width:243px;">
					</td></tr>
					</table>

					<table border="0" cellpadding="0" cellspacing="0"  align="left" width="56%" class="width-500-100">
					<tr><td align="left" style="padding-right: 20px;" class="leftright-text">
						<div style="font-size:14px; font-weight: bold;color: #6a6a6a;margin-bottom: 10px;">Great Feature About Your Website</div>
						<div style="font-size:13px; line-height: 18px; color: #9d9d9d;margin-bottom: 30px;">Write a short description about some great features in your website. This is just a simple test.</div>
						<div style="font-size:14px; font-weight: bold;color: #6a6a6a;margin-bottom: 10px;">Great Feature About Your Website</div>
						<div style="font-size:13px; line-height: 18px; color: #9d9d9d;">Write a short description about some great features in your website. This is just a simple test.</div>
					</td></tr>
					</table>

				</div></td>
			</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td style="padding: 30px 10px;border: 1px solid #DDD;border-top:none;" align="center">
			<table border="0" cellpadding="0" cellspacing="0"  align="center" style="width:100%;">
			<tr>
				<td style="padding: 10px;" align="center"><div style="text-align:center;">

					<table border="0" cellpadding="0" cellspacing="0"  align="left" width="44%" class="width-500-100 mbot-30">
					<tr><td align="center">
						<img src="[invitation_assets_url]/default/browser.png" style="width:100%;max-width:243px;">
					</td></tr>
					</table>

					<table border="0" cellpadding="0" cellspacing="0"  align="right" width="56%" class="width-500-100">
					<tr><td align="left" style="padding-left: 20px;" class="leftright-text">
						<div style="font-size:14px; font-weight: bold;color: #6a6a6a;margin-bottom: 10px;">Great Feature About Your Website</div>
						<div style="font-size:13px; line-height: 18px; color: #9d9d9d;margin-bottom: 30px;">Write a short description about some great features in your website. This is just a simple test.</div>
						<div style="font-size:14px; font-weight: bold;color: #6a6a6a;margin-bottom: 10px;">Great Feature About Your Website</div>
						<div style="font-size:13px; line-height: 18px; color: #9d9d9d;">Write a short description about some great features in your website. This is just a simple test.</div>
					</td></tr>
					</table>

				</div></td>
			</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td style="padding: 45px 10px 20px 10px;border: 1px solid #DDD;border-top:none;" align="center">
			<div style="max-width:400px;">
				<a href=""><img src="[invitation_assets_url]/default/fb.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
				<a href=""><img src="[invitation_assets_url]/default/g.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
				<a href=""><img src="[invitation_assets_url]/default/tw.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
				<a href=""><img src="[invitation_assets_url]/default/in.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
				<a href=""><img src="[invitation_assets_url]/default/vm.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
				<a href=""><img src="[invitation_assets_url]/default/be.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
				<a href=""><img src="[invitation_assets_url]/default/db.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
			</div>
		</td>
	</tr>
	</table>

	<table border="0" cellpadding="0" cellspacing="0"  align="center" style="width:100%;max-width:600px;">
	<tr>
		<td style="padding: 20px 10px 0px 10px;">
			<div style="color: #ababab;font-family:Verdana,Arial;font-size: 12px;text-align:left;line-height:17px;">
				This email was sent to you on behalf of [sender_email]&#39;s request. You can safely <a href="[verify_invitation_url]invitation_id=[invitation_id]&adi_do=unsubscribe" style="text-decoration:none;color:#999999;color:#0084b4;text-decoration:underline;">unsubscribe</a> from these emails.<br><br>
				Your Website, Inc. 1003 Market Street, Palo Alto, CA 94001.
			</div>
		</td>
	</tr>
	</table>
</div>
</td></tr>
</table>

</body>
</html>',
		'invitation_attachment' => '1',
		'invitation_social_body_en' => 'AdiInviter Contacts Importer / Inviter is an addressbook importer script. It allows your users to invite their contacts from various webmail and social networks such as Gmail, Yahoo, Hotmail, AOL and many more.',
		'attach_note_length_limit' => '150',
		'twitter_invitation_body_en' => 'This is a sample invitation message sent using AdiInviter Pro Live Demo. Check out AdiInviter Pro 2.0 here : http://goo.gl/8PVxQ7',

		'A7C987C926E5B640308C6B930EA243C8C7C8B6E02A' => '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' => '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',


	),

	// OAuth Settings
	'oauth' => array(
		'google_consumer_key'        => '',
		'google_consumer_secret'     => '',

		'hotmail_consumer_key'       => '',
		'hotmail_consumer_secret'    => '',

		'yahoo_consumer_key'         => '',
		'yahoo_consumer_secret'      => '',
		
		'xing_consumer_key'          => '',
		'xing_consumer_secret'       => '',
		
		'aol_consumer_key'           => '',
		'aol_consumer_secret'        => '',
		
		'mailchimp_consumer_key'     => '',
		'mailchimp_consumer_secret'  => '',
		'mailchimp_consumer_api_key' => '',

		'eventbrite_consumer_key'    => '',
		'eventbrite_consumer_secret' => '',

		'twitter_consumer_key'       => '',
		'twitter_consumer_secret'    => '',

		'viadeo_consumer_key'        => '',
		'viadeo_consumer_secret'     => '',
	),

	// Campaign list
	'campaigns' => array(
		'campaigns_list' => array(),
	),

	// Updates
	'updates' => array(
		'adi_package_build_id' => '2000',
		'adi_updates_list' => '',
		'check_for_updates_link' => 'http://www.adiinviter.com/updates.php',
		'download_updates_link' => 'https://www.adiinviter.com/download',
		'adi_email_notification_subject' => 'AdiInviter Pro Update Notification : [updates_count] New Updates',
		'adi_email_notification_body' => '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="format-detection" content="telephone=no"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
<title>AdiInviter Pro Update Notification : [updates_count] New Updates</title>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" yahoo="fix" style="font-family: Verdana, Georgia, Times, serif; background-color:#FFF; " bgcolor="FFF">

	<p style="margin: 0px 0px 15px 0px;font-family: Verdana,Arial;font-size:13px;color: #181818;line-height:18px;">Hello,</p>
	<p style="margin: 0px 0px 15px 0px;font-family: Verdana,Arial;font-size:13px;color: #181818;line-height:18px;">There are [updates_count] new updates available for AdiInviter Pro. Please login to our <a href="http://www.adiinviter.com/download" style="text-decoration:none;color:#00B4FF;"><b>Members Area</b></a> to download the updates.</p>

	<div style="margin: 0px 0px 15px 0px;font-family: Verdana,Arial;font-size:13px;color: #181818;line-height:18px;"><span style="font-weight:bold; color:#FF0000;">Note :</span> Please do not reply to this email. Unfortunately, we are unable to respond to inquiries sent to this email address. If you have any questions then please send us an email at <a href="mailto:support@adiinviter.com" style="color:#00B4FF;text-decoration:none;">support@adiinviter.com</a>. You can also contact us by visiting our official <a href="http://www.adiinviter.com/support" target="_blank" style="color:#00B4FF;text-decoration:none;">Support Page</a>.</div>

	<p style="margin: 0px;font-family: Verdana,Arial;font-size:13px;color: #181818;line-height:18px;">Thank you,<br>AdiInviter Pro</p>

</body>
</html>',
	),

);

?>