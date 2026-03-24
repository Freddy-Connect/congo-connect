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

require_once(BX_DIRECTORY_PATH_INC . 'admin_design.inc.php');

bx_import('Module', $aModule);

global $_page;
global $_page_cont;

$iIndex = 9;
$_page['name_index'] = $iIndex;
$_page['header'] = _t('_aqb_automailer');

if(!@isAdmin()) {
    send_headers_page_changed();
	login_form("", 1);
	exit;
}

$oModule = new AqbAutomailerModule($aModule);
if (isset($_POST['action'])) {
	if ($_POST['action'] == 'activate' && $_POST['id']) {$oModule->_oDb->setMailStatus(intval($_POST['id']), 1); unset($_POST['id']);}
	elseif ($_POST['action'] == 'deactivate' && $_POST['id']) {$oModule->_oDb->setMailStatus(intval($_POST['id']), 0);  unset($_POST['id']);}
	elseif ($_POST['action'] == 'test' && $_POST['id']) {
		$oModule->testAutomail(intval($_POST['id']));
		exit;
	}elseif ($_POST['action'] == 'delete' && $_POST['id']) {$oModule->_oDb->deleteMail(intval($_POST['id'])); unset($_POST['id']);}
}

$aTopButons = array(
	'add' => array(
		'title' => '_aqb_automailer_add',
		'href' => '#',
		'onclick' => isset($_POST['action']) && $_POST['action'] == 'edit' || $_POST['save'] && $_POST['id']
						? 'window.location.href = window.location.href + \'?save=1\'; return false;'
						: '$(\'#body0_tbl\').css({\'width\':\'100%\', \'height\':\'100%\'}); $(\'#aqb_automailer_add_form\').toggle(); $(\'#aqb_automailer_maillist\').toggle(); return false;'
	),
	'time' => array(
		'title' => '_aqb_automailer_set_time',
		'href' => '#',
		'onclick' => 'getTimePopUp(); return false;'
	)
);
$aTopButons2 = array(
	'back' => array(
		'title' => '_aqb_automailer_back',
		'href' => '#',
		'onclick' => '$(\'#aqb_automailer_add_form\').toggle(); $(\'#aqb_automailer_maillist\').toggle(); return false;'
	),
);


$sBaseURL = BX_DOL_URL_ROOT . $oModule->_oConfig->getBaseUri();
$bShowAddFormBlock = isset($_REQUEST['save']) || isset($_POST['action']) && $_POST['action'] == 'edit';
$sAddForm = $oModule->getAddForm();
if (!empty($oModule->_sUglySolutionMessage)) $bShowAddFormBlock = false;

$_page_cont[$iIndex]['page_main_code'] = '<div id="aqb_automailer_maillist" style="display:'.($bShowAddFormBlock ? 'none' : 'block').';">'.DesignBoxAdmin(_t('_aqb_automailer_mails'), $oModule->getMailsList(), $aTopButons, '', 11).'</div>';
$_page_cont[$iIndex]['page_main_code'] .= '<div id="aqb_automailer_add_form" style="display:'.(!$bShowAddFormBlock ? 'none' : 'block').';">'.DesignBoxAdmin(_t('_aqb_automailer_add'), $sAddForm, $aTopButons2, '', 11).'</div>';
$_page_cont[$iIndex]['page_main_code'] .= <<<EOF
<script language="javascript">
function getTimePopUp() {
	if (!$('#aqb_automailer_time_form').length) {
        $('<div id="aqb_automailer_time_form" style="display:none;"></div>').prependTo('body');
	}

	$('#aqb_automailer_time_form').html('<h1>Loading...</h1>');
	$('#aqb_automailer_time_form').dolPopup({fog: {color: '#444', opacity: .7}, closeOnOuterClick: false});
	var oDate = new Date();
	$.get('{$sBaseURL}' + 'action_get_time_form/', {time:oDate.getTime(), offset:oDate.getTimezoneOffset()}, function(sResponse) {
		$('#aqb_automailer_time_form').html(sResponse);
		$('#aqb_automailer_time_form_popup div .dbClose a').click(function() {
			$('#aqb_automailer_time_form').dolPopupHide();
		});
		$('#aqb_automailer_time_form')._dolPopupSetPosition({position:'centered'});
	}, 'html');
}
function setTime(oForm) {
	$('#aqbAutomailerFormLoading').bx_loading();

	var sQueryString = $(oForm).formSerialize();
	var oDate = new Date();
	$.post('{$sBaseURL}' + 'action_set_time/', sQueryString+'&time='+oDate.getTime()+'&offset='+oDate.getTimezoneOffset(), function(sResponse){
        $('#aqb_automailer_time_form').html(sResponse);
		$('#aqb_automailer_time_form_popup div .dbClose a').click(function() {
			$('#aqb_automailer_time_form').dolPopupHide();
		});
		$('#aqb_automailer_time_form')._dolPopupSetPosition({position:'centered'});
    }, 'html');
}
</script>
EOF;

PageCodeAdmin();
?>