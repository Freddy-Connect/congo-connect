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

bx_import('BxDolModuleTemplate');
bx_import('BxTemplFormView');

class AqbAutomailerTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function AqbAutomailerTemplate(&$oConfig, &$oDb) {
	    parent::BxDolModuleTemplate($oConfig, $oDb);
	}

	function getMailsList($aMails) {
		if (empty($aMails)) return MsgBox(_t('_Empty'));

		$aResult = array();
		foreach ($aMails as $aMail) {
			$aResult[] = array(
				'id' => $aMail['ID'],
				'inactive_row' => $aMail['Active'] ? '' : 'class="inactive_row"',
				'name' => htmlspecialchars($aMail['Name']),
				'subject' => htmlspecialchars($aMail['Subject'][0]),
				'filter' => $this->filterFormat($aMail['Filter']),
				'schedule' => $this->filterSchedule($aMail['Schedule']),
				'action_name' => $aMail['Active'] ? 'deactivate' : 'activate',
				'action_caption' => $aMail['Active'] ? _t('_aqb_automailer_deactivate') : _t('_aqb_automailer_activate'),
				'action_function' => $aMail['Active'] ? 'aqbAutomailerDeactivate' : 'aqbAutomailerActivate',
			);
		}

		return $this->parseHtmlByName('maillist.html', array('bx_repeat:mails' => $aResult, 'action_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri().'admin/'));
	}
	function filterFormat($aFilter) {
		if (empty($aFilter)) return _t('_Any');

		$sRet = '';
		foreach ($aFilter as $sFilterName => $sValue) {
			$sName = '';
			switch ($sFilterName) {
				case 'ProfileStatus':
					$sName = _t('_aqb_automailer_filter_status');
					$sValue = htmlspecialchars($sValue);
				break;
				case 'ProfileMemlevel':
					$sName = _t('_aqb_automailer_filter_memlevel');
					$aValues = getMemberships();
					$sValue = htmlspecialchars($aValues[$sValue]);
				break;
				case 'ProfileProfileType':
					$sName = _t('_FieldCaption_ProfileType_Join');
					$aValues = $this->getValuesArray('ProfileType');
					$sValue = htmlspecialchars($aValues[$sValue]);
				break;
				case 'ProfileCountry':
					$sName = _t('_FieldCaption_Country_View');
					$aValues = $this->getValuesArray('Country');
					$sValue = htmlspecialchars($aValues[$sValue]);
				break;
				case 'ProfileSex':
					$sName = _t('_FieldCaption_Sex_View');
					$aValues = $this->getValuesArray('Sex');
					$sValue = htmlspecialchars($aValues[$sValue]);
				break;
				case 'ProfileAge':
					$sName = _t('_aqb_automailer_filter_age');
					$sValue = htmlspecialchars($sValue);
				break;
				case 'ProfileNoAva':
					$sName = _t('_aqb_automailer_filter_noava');
					$sValue = _t('_Yes');
				break;
			}
			$sRet .= "<strong>{$sName}</strong>:<br /><p>{$sValue}</p>";
		}
		return $sRet;
	}

	function filterSchedule($aSchedule) {
		if (empty($aSchedule)) return _t('_aqb_automailer_schedule_every_1_day', 1);
		if ($aSchedule['EveryXDays']) return _t('_aqb_automailer_schedule_every_x_days', $aSchedule['EveryXDays']);
		if ($aSchedule['DayXSinceRegistration']) return _t('_aqb_automailer_schedule_day_x_since_reg', $aSchedule['DayXSinceRegistration']);
		if ($aSchedule['DayXSinceLatestActivity']) return _t('_aqb_automailer_schedule_day_x_since_latest_act', $aSchedule['DayXSinceLatestActivity']);
		if ($aSchedule['DayXBeforeMembershipExpiration']) return _t('_aqb_automailer_schedule_day_x_before_membership_exp', $aSchedule['DayXBeforeMembershipExpiration']);
		if ($aSchedule['ExactDate']) {
			if (!$aSchedule['Annually']) return _t('_aqb_automailer_schedule_at', getLocaleDate($aSchedule['ExactDate']));
			else return _t('_aqb_automailer_schedule_at_annually', substr(getLocaleDate($aSchedule['ExactDate']), 0, -5));
		}
		if ($aSchedule['Birthday']) return _t('_aqb_automailer_schedule_on_birthday');
	}

	function getAddForm($aAutoMail = array()) {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$aLangs = array(0 => _t('_aqb_automailer_language_default')) + getLangsArr(false, true);


		$aForm = array(
	    	'form_attrs' => array(
                'action' => $sBaseUrl . 'admin/',
                'method' => 'post',
                'id' => 'aqb_add_form'
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'save',
                ),
            ),
            'inputs' => array (
            	'id' => array(
            		'type' => 'hidden',
            		'name' => 'id',
            		'value' => $aAutoMail['ID'],
            	),
            	'header_mail' => array(
					'type' => 'block_header',
	                'caption' => _t('_aqb_automailer_automail'),
					'collapsable' => false,
				),
            	'name' => array(
            		'type' => 'text',
            		'name' => 'name',
            		'caption' => _t('_aqb_automailer_name_cpt'),
            		'info' => _t('_aqb_automailer_name_info'),
            		'value' => htmlspecialchars($aAutoMail['Name']),
            		'checker' => array(
            			'func' => 'avail',
            			'error' => _t('_aqb_automailer_name_err'),
            		),
            		'required' => true,
            	),
            	'language' => array(
            		'type' => 'select',
            		'name' => 'language',
            		'caption' => _t('_aqb_automailer_language_cpt'),
            		'values' => $aLangs,
            		'attrs' => array(
            			'onchange' => 'javascript:$(".lang_sections").hide(); $("#body"+this.value+"_tbl").css({"width":"100%", "height":"100%"}); $(".lang_section_"+this.value).show();',
            		),
            	),
            )
		);

		foreach ($aLangs as $iLang => $sCaption) {
			$aForm['inputs'] += array(
				'subject'.$iLang => array(
            		'type' => 'text',
            		'name' => 'subject'.$iLang,
            		'caption' => _t('_aqb_automailer_subject_cpt', $sCaption),
            		'value' => isset($aAutoMail['Subject'][$iLang]) ? htmlspecialchars($aAutoMail['Subject'][$iLang]) : htmlspecialchars($aAutoMail['Subject'][0]),
            		'checker' => $iLang == 0 ? array(
            			'func' => 'avail',
            			'error' => _t('_aqb_automailer_subject_err'),
            		) : array(),
            		'required' => $iLang == 0,
            		'tr_attrs' => array(
            			'class' => 'lang_sections lang_section_'.$iLang,
            			'style' => $iLang != 0 ? 'display:none;' : '',
            		),
            	),
				'body'.$iLang => array(
					'type' => 'textarea',
					'name' => 'body'.$iLang,
					'caption' => _t('_aqb_automailer_body_cpt', $sCaption),
					'info' => _t('_aqb_automailer_body_info'),
					'html' => 2,
	                'value' => isset($aAutoMail['Body'][$iLang]) ? $aAutoMail['Body'][$iLang] : (isset($aAutoMail['Body'][0]) || $iLang > 0 ? $aAutoMail['Body'][0] : $this->_oConfig->getDefaultTemplate()),
	                'required' => $iLang == 0,
            		'checker' => $iLang == 0 ? array(
            			'func' => 'avail',
            			'error' => _t('_aqb_automailer_body_err'),
            		) : array(),
            		'tr_attrs' => array(
            			'class' => 'lang_sections lang_section_'.$iLang,
            			'style' => $iLang != 0 ? 'display:none;' : '',
            		),
            		'attrs' => array(
            			'style' => 'height:300px;',
            		),
				)
			);
		}


		$aForm['inputs'] += array(
			'header_mail_end' => array(
				'type' => 'block_end'
			),
			'header_filter' => array(
				'type' => 'block_header',
                'caption' => _t('_aqb_automailer_filter'),
				'collapsable' => false,
			),
			'filter_status' => array(
        		'type' => 'select',
        		'name' => 'profile_status',
        		'caption' => _t('_aqb_automailer_filter_status'),
        		'values' => array('' => _t('_Any'), 'Active' => 'Active', 'Unconfirmed' => 'Unconfirmed', 'Approval' => 'Approval', 'Suspended' => 'Suspended', 'Rejected' => 'Rejected'),
        		'value' => $aAutoMail['Filter']['ProfileStatus']
        	),
        	'filter_memlevel' => array(
        		'type' => 'select',
        		'name' => 'profile_memlevel',
        		'caption' => _t('_aqb_automailer_filter_memlevel'),
        		'values' => array('' => _t('_Any')) + getMemberships(),
        		'value' => $aAutoMail['Filter']['ProfileMemlevel']
        	),
        	'filter_profile_type' => array(
        		'type' => 'select',
        		'name' => 'profile_profile_type',
        		'caption' => _t('_FieldCaption_ProfileType_Join'),
        		'values' => $this->getValuesArray('ProfileType'),
        		'value' => $aAutoMail['Filter']['ProfileProfileType']
        	),
        	'filter_country' => array(
        		'type' => 'select',
        		'name' => 'profile_country',
        		'caption' => _t('_FieldCaption_Country_View'),
        		'values' => $this->getValuesArray('Country'),
        		'value' => $aAutoMail['Filter']['ProfileCountry']
        	),
        	'filter_sex' => array(
        		'type' => 'select',
        		'name' => 'profile_sex',
        		'caption' => _t('_FieldCaption_Sex_View'),
        		'values' => $this->getValuesArray('Sex'),
        		'value' => $aAutoMail['Filter']['ProfileSex']
        	),
        	'filter_age' => array(
				'type' => 'doublerange',
        		'name' => 'profile_age',
        		'caption' => _t('_aqb_automailer_filter_age'),
        		'value' => $aAutoMail['Filter']['ProfileAge'] ? $aAutoMail['Filter']['ProfileAge'] : '',
        		'attrs' => array(
        			'min' => $this->_oDb->getparam('search_start_age'),
        			'max' => $this->_oDb->getparam('search_end_age')
        		)
			),
			'filter_noava' => array(
        		'type' => 'checkbox',
        		'name' => 'profile_noava',
        		'caption' => _t('_aqb_automailer_filter_noava'),
        		'checked' => $aAutoMail['Filter']['ProfileNoAva'] ? 'checked' : ''
        	),
			'header_filter_end' => array(
				'type' => 'block_end'
			),
			'header_schedule' => array(
				'type' => 'block_header',
                'caption' => _t('_aqb_automailer_schedule'),
				'collapsable' => false,
			),
			'every_x_days' => array(
				'type' => 'number',
        		'name' => 'every_x_days',
        		'caption' => _t('_aqb_automailer_filter_every_x_days'),
        		'value' => $aAutoMail['Schedule']['EveryXDays'] > 0 ? intval($aAutoMail['Schedule']['EveryXDays']) : '',
			),
			'on_day_x_since_registration' => array(
				'type' => 'number',
        		'name' => 'on_day_x_since_registration',
        		'caption' => _t('_aqb_automailer_filter_on_day_x_since_registration'),
        		'value' => $aAutoMail['Schedule']['DayXSinceRegistration'] > 0 ? intval($aAutoMail['Schedule']['DayXSinceRegistration']) : '',
			),
			'on_day_x_since_latest_activity' => array(
				'type' => 'number',
        		'name' => 'on_day_x_since_latest_activity',
        		'caption' => _t('_aqb_automailer_filter_on_day_x_since_latest_activity'),
        		'value' => $aAutoMail['Schedule']['DayXSinceLatestActivity'] > 0 ? intval($aAutoMail['Schedule']['DayXSinceLatestActivity']) : '',
			),
			'on_day_x_before_membership_expiration' => array(
				'type' => 'number',
        		'name' => 'on_day_x_before_membership_expiration',
        		'caption' => _t('_aqb_automailer_filter_on_day_x_before_membership_expiration'),
        		'value' => $aAutoMail['Schedule']['DayXBeforeMembershipExpiration'] > 0 ? intval($aAutoMail['Schedule']['DayXBeforeMembershipExpiration']) : '',
			),
			'exact_date' => array(
        		'type' => 'date',
        		'name' => 'exact_date',
        		'caption' => _t('_aqb_automailer_filter_exact_date'),
        		'info' => _t('_aqb_automailer_filter_exact_date_info'),
        		'value' => !empty($aAutoMail['Schedule']['ExactDate']) ? ($aAutoMail['Schedule']['Annually'] ? date('Y-').date('m-d', $aAutoMail['Schedule']['ExactDate']) : date('Y-m-d', $aAutoMail['Schedule']['ExactDate'])) : date('Y-01-01', time()),
        	),
        	'annually' => array(
        		'type' => 'checkbox',
        		'name' => 'annually',
        		'caption' => _t('_aqb_automailer_filter_annually'),
        		'info' => _t('_aqb_automailer_filter_annually_info'),
        		'value' => 'on',
        		'checked' => $_REQUEST['save'] ? $_REQUEST['annually'] : $aAutoMail['Schedule']['Annually']
        	),
        	'birthday' => array(
        		'type' => 'checkbox',
        		'name' => 'birthday',
        		'caption' => _t('_aqb_automailer_filter_birthday'),
        		'value' => 'on',
        		'checked' => $_REQUEST['save'] ? $_REQUEST['birthday'] : $aAutoMail['Schedule']['Birthday']
        	),
			'header_schedule_end' => array(
				'type' => 'block_end'
			),
			'header_options' => array(
				'type' => 'block_header',
                'caption' => _t('_aqb_automailer_options'),
				'collapsable' => false,
			),
			'sendto' => array(
        		'type' => 'checkbox_set',
        		'name' => 'sendto',
        		'caption' => _t('_aqb_automailer_options_sendto'),
        		'values' => array(
        			'email' => _t('_aqb_automailer_options_sendto_email'),
        			'inbox' => _t('_aqb_automailer_options_sendto_inbox'),
        		),
        		'value' => !empty($aAutoMail['Options']['SendTo']) ? $aAutoMail['Options']['SendTo'] : array('email'),
        		'info' => _t('_aqb_automailer_options_sendto_info'),
        		'required' => true,
        		'checker' => array(
        			'func' => 'avail',
        			'error' => _t('_aqb_automailer_options_sendto_err'),
        		),
        	),
        	'add_points' => array(
				'type' => 'number',
        		'name' => 'add_points',
        		'caption' => _t('_aqb_automailer_points_add'),
        		'value' => $aAutoMail['Options']['AddPoints'] > 0 ? intval($aAutoMail['Options']['AddPoints']) : '',
			),
			'header_options_end' => array(
				'type' => 'block_end'
			),
            'submit' => array(
				'type' => 'submit',
				'name' => 'save',
				'value' => !empty($aAutoMail['ID']) ? _t('_aqb_automailer_save_cpt') : _t('_aqb_automailer_add_cpt'),
				'colspan' => true,
            )
		);

		if (!$this->_oConfig->isFieldAvailable('ProfileType')) unset($aForm['inputs']['filter_profile_type']);
		if (!$this->_oConfig->isFieldAvailable('Country')) unset($aForm['inputs']['filter_country']);
		if (!$this->_oConfig->isFieldAvailable('Sex')) unset($aForm['inputs']['filter_sex']);
		if (!$this->_oConfig->isFieldAvailable('DateOfBirth')) unset($aForm['inputs']['filter_age']);
		if (!$this->_oDb->getModuleByUri('aqb_points') || !$this->_oConfig->isFieldAvailable('AqbPoints')) unset($aForm['inputs']['add_points']);

		$oForm = new BxTemplFormView($aForm);
		$oForm->initChecker($_POST);
		return $oForm;
	}

	function getTimeForm($aCurServTime, $aCurLocalTime, $aSchedServTime, $aShedLocalTime) {
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();

		$aLangs = array(0 => _t('_aqb_automailer_language_default')) + getLangsArr(false, true);


		$aForm = array(
	    	'form_attrs' => array(
                'action' => $sBaseUrl . 'action_set_time/',
                'method' => 'post',
                'onsubmit' => 'javascript:setTime(this); return false;'
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'save',
                ),
            ),
            'inputs' => array (
            	'message' => array(
            		'type' => 'value',
            		'colspan' => true,
            		'value' => _t('_aqb_automailer_set_time_info'),
            	),
            	'localtime' => array(
            		'type' => 'value',
            		'caption' => _t('_aqb_automailer_local_time'),
            		'value' => $this->printTime($aCurLocalTime)
            	),
            	/*'schedtime' => array(
            		'type' => 'value',
            		'caption' => _t('_aqb_automailer_sched_time'),
            		'info' => _t('_aqb_automailer_sched_time_info'),
            		'value' => $this->printTime($aShedLocalTime)
            	),*/
            	'schedtime' => array(
            		'type' => 'custom',
            		'caption' => _t('_aqb_automailer_sched_time'),
            		'info' => _t('_aqb_automailer_sched_time_info'),
            		'content' => $this->getTimePicker($aShedLocalTime['h'], $aShedLocalTime['m']),
            	),
	            'submit' => array(
					'type' => 'submit',
					'name' => 'save',
					'value' => _t('_aqb_automailer_save_cpt'),
					'colspan' => true,
					'attrs_wrapper' => array(
	            		'style' => 'margin-left: 180px;',
	            	),
	            )
			)
		);
		$oForm = new BxTemplFormView($aForm);
		return $oForm;
	}

	function getValuesArray($sListName) {
		$aRet = array('' => _t('_Any'));
		if (isset($GLOBALS['aPreValues'][$sListName]))
		foreach($GLOBALS['aPreValues'][$sListName] as $sKey => $aValue) {
			$aRet[$sKey] = _t($aValue['LKey']);
		}
		return $aRet;
	}
	function printTime($aTime) {
		$h = $aTime['h'];
		if ($h < 10) $zh = '0'.$h; else $zh = $h;
		if ($h > 12) {
			if ($h < 21) $zh = '0'.($h % 12); else $zh = ($h % 12);
		}

		$m = $aTime['m'];
		if ($m < 10) $m = '0'.$m;

		if ($h == 0) return '12:'.$m.' AM';
		elseif ($h < 12) return $zh.':'.$m.' AM';
		elseif ($h == 12) return '12:'.$m.' PM';
		else return $zh.':'.$m.' PM';
	}
	function getTimePicker($h = 0, $m = 0) {
		$sRet = '<select name="h">';
		$sRet .= '<option value="0">12</option>';
		for ($i = 1; $i < 12; $i++) {
			$si = $i < 10 ? '0'.$i : $i;
			$selected = ($h % 12) == $i ? 'selected="selected"' : '';
			$sRet .= '<option value="'.$i.'" '.$selected.'>'.$si.'</option>';
		}
		$sRet .= '</select>';

		$sRet .= ':<select name="m">';
		for ($i = 0; $i < 60; $i++) {
			$si = $i < 10 ? '0'.$i : $i;
			$selected = $m == $i ? 'selected="selected"' : '';
			$sRet .= '<option value="'.$i.'" '.$selected.'>'.$si.'</option>';
		}
		$sRet .= '</select>';

		$sRet .= '&nbsp;<select name="p">';
		$sRet .= '<option value="1" '.($h < 12 ? 'selected="selected"' : '').'>AM</option>';
		$sRet .= '<option value="2" '.($h >= 12 ? 'selected="selected"' : '').'>PM</option>';
		$sRet .= '</select>';
		return $sRet;
	}
}
?>