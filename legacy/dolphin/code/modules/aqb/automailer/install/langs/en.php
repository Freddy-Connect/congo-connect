<?php
/***************************************************************************
*
*     copyright            : (C) 2011 AQB Soft
*     website              : http://www.aqbsoft.com
*
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
*
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
*
* This notice may not be removed from the source code.
*
***************************************************************************/

$aLangContent = array(
	'_aqb_automailer' => 'Automailer',
	'_aqb_automailer_mails' => 'Current automails',
	'_aqb_automailer_add' => 'Add automail',
	'_aqb_automailer_edit' => 'Add automail',
	'_aqb_automailer_save_cpt' => 'Save',
	'_aqb_automailer_add_cpt' => 'Add',
	'_aqb_automailer_name_cpt' => 'Name',
	'_aqb_automailer_name_info' => 'Automail name (for internal use only)',
	'_aqb_automailer_name_err' => 'Name is required',
	'_aqb_automailer_language_cpt' => 'Language',
	'_aqb_automailer_subject_cpt' => 'Subject ({0})',
	'_aqb_automailer_subject_err' => 'Default subject is required',
	'_aqb_automailer_language_default' => 'Default',
	'_aqb_automailer_body_cpt' => 'Body ({0})',
	'_aqb_automailer_body_err' => 'Default body is required',
	'_aqb_automailer_body_info' => 'Placeholders allowed:<br /><br /><strong>::recipientID::</strong> - Recipient\'s profile ID<br /><br /><strong>::RealName::</strong> - Recipient\'s FirsName and LastName<br /><br /><strong>::NickName::</strong> - Recipient\'s NickName<br /><br /><strong>::Domain::</strong> - Site\'s URL<br /><br /><strong>::SiteName::</strong> - Site\'s title<br />',
	'_aqb_automailer_automail' => '<strong>Email template</strong>',
	'_aqb_automailer_filter' => '<strong>Filter</strong> (if not specified then all profiles will be affected)',
	'_aqb_automailer_schedule' => '<strong>Schedule</strong> (if not specified then schedule is "Every day")',
	'_aqb_automailer_filter_every_x_days' => 'Every X days (since registration)',
	'_aqb_automailer_filter_on_day_x_since_registration' => '<strong>OR</strong> on day X since registration',
	'_aqb_automailer_filter_on_day_x_since_latest_activity' => '<strong>OR</strong> on day X since latest activity',
	'_aqb_automailer_filter_on_day_x_before_membership_expiration' => '<strong>OR</strong> on day X before membership expiration',
	'_aqb_automailer_filter_exact_date' => '<strong>OR</strong> at exact date',
	'_aqb_automailer_filter_exact_date_info' => 'To ignore this filter leave the date in the past',
	'_aqb_automailer_filter_birthday' => '<strong>OR</strong> on birthday',
	'_aqb_automailer_filter_annually' => 'Annually',
	'_aqb_automailer_filter_annually_info' => 'Makes effect only if <strong>at exact date</strong> is specified',
	'_aqb_automailer_filter_status' => 'Profile Status',
	'_aqb_automailer_filter_memlevel' => 'Membership level',
	'_aqb_automailer_filter_age' => 'Age',
	'_aqb_automailer_filter_noava' => 'No avatar',
	'_aqb_automailer_added' => 'Added',
	'_aqb_automailer_edited' => 'Edited',
	'_aqb_automailer_set_time' => 'Set time',
	'_aqb_automailer_set_time_info' => 'The script which schedules emails runs every day. Emails are then being sent by portions via Dolphin\'s internal <a href="/administration/notifies.php" target="_blank">massmailer</a>. Here you can configure time at which scheduler should run.',
	'_aqb_automailer_local_time' => 'Local time',
	'_aqb_automailer_sched_time' => 'Run script time (local)',
	'_aqb_automailer_sched_time_info' => 'This <i>time</i> is automatically calculated from server\'s time and timezone difference bewteen your local time and server system time',
	'_aqb_automailer_name' => 'Name',
	'_aqb_automailer_subject' => 'Subject',
	'_aqb_automailer_filter_t' => 'Filter',
	'_aqb_automailer_schedule_t' => 'Schedule',
	'_aqb_automailer_activate' => 'Activate',
	'_aqb_automailer_deactivate' => 'Deactivate',
	'_aqb_automailer_schedule_every_x_days' => 'Every {0} days',
	'_aqb_automailer_schedule_every_1_day' => 'Every day',
	'_aqb_automailer_schedule_day_x_since_reg' => 'At {0} day since registration',
	'_aqb_automailer_schedule_day_x_since_latest_act' => 'At {0} day since latest activity',
	'_aqb_automailer_schedule_day_x_before_membership_exp' => 'At {0} day before membership expiration',
	'_aqb_automailer_schedule_on_birthday' => 'At member\'s birthday',
	'_aqb_automailer_schedule_at' => 'At {0}',
	'_aqb_automailer_schedule_at_annually' => 'Annually at {0}',
	'_aqb_automailer_delete_confirm' => 'Are you sure you want to permanently delete this automail?',
	'_aqb_automailer_back' => 'Back',
	'_aqb_automailer_options' => 'Options',
	'_aqb_automailer_options_sendto' => 'Send via',
	'_aqb_automailer_options_sendto_err' => 'You must select at least one item',
	'_aqb_automailer_options_sendto_email' => 'Email',
	'_aqb_automailer_options_sendto_inbox' => 'Site\'s inbox',
	'_aqb_automailer_options_sendto_info' => 'If "site\'s inbox" option checked, then your admin\'s account will be used as sender of that message',
	'_aqb_automailer_test' => 'Test',
	'_aqb_automailer_test_message' => 'Check your email and site\'s inbox',
	'_aqb_automailer_points_reason' => 'Gift',
	'_aqb_automailer_points_add' => 'Add points',
);
?>