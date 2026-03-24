<?php
/***************************************************************************
* Date				: Wednesday August 21, 2013
* Copywrite			: (c) 2013 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Dropdown Date Selector
* Product Version	: 1.0.3
*
* IMPORTANT: This is a commercial product made by Dean J. Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean J. Bassett Jr.
*
***************************************************************************/

bx_import('BxDolModule');

class deanoDropdownDateSelectorModule extends BxDolModule {

    function deanoDropdownDateSelectorModule(&$aModule) {        
        parent::BxDolModule($aModule);
    }

    function actionHome () {
        $this->_oTemplate->pageStart();

        $sDateFormat = getParam ('me_blgg_date_format');
        $isShowUserTime = getParam('me_blgg_enable_js_date') ? true : false;
        $aVars = array (
            'server_time' => date($sDateFormat),
            'bx_if:show_user_time' => array(
                'condition' => $isShowUserTime,
                'content' => array(),
            ),
        );
        echo $this->_oTemplate->parseHtmlByName('main', $aVars);
        $this->_oTemplate->pageCode(_t('_deano_dropdown_date_selector'), true);
    }

    function actionAdministration () {

        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

	    $iId = $this->_oDb->getSettingsCategory();
	    if(empty($iId)) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_deano_dropdown_date_selector'));
            return;
        }

        bx_import('BxDolAdminSettings');

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) {
	        $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();
        	       
        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;

		if($GLOBALS['site']['ver'] == '7.1') {
			$sResult = '<div class="bx-def-bc-margin">' . $sResult . '</div>';
		} else {
			$sResult = '<div class="bx_sys_default_padding">' . $sResult . '</div>';
		}
        echo DesignBoxAdmin (_t('_deano_dropdown_date_selector'), $sResult);
       
        $this->_oTemplate->pageCodeAdmin (_t('_deano_dropdown_date_selector'));
    }    

	function serviceGetScript($oAlert) {
		//return;

		//$iMemID = $oAlert->iSender;
		$iMemID = getID( $_GET['ID'] );
		$iAreaID = $oAlert->aExtras['oProfileFields']->iAreaID;
		$bOK = false;
		//echo $iAreaID;
		//exit;
		if($iAreaID == 1 && getParam('deano_dropdown_date_selector_enable_join')) $bOK = true;
		if($iAreaID == 2 && getParam('deano_dropdown_date_selector_enable_edit')) $bOK = true;
		if($iAreaID == 3 && getParam('deano_dropdown_date_selector_enable_edit')) $bOK = true;
		if($iAreaID == 4 && getParam('deano_dropdown_date_selector_enable_edit')) $bOK = true;
		if(!$bOK) return;
		$sStyle = '
			<style>
			.dbt td {
				border-bottom: 0px solid #DADADA;
				padding: 0px;
				line-height: 19px;
			}
			.wrap {
				width: auto;
				height: auto;
				padding: 1px 2px 1px 4px;
			}
			</style>		
		';
		$sFormInsertSingle = $this -> getSelector($iMemID, 0);
		$sFormInsertCouple = $this -> getSelector($iMemID, 1);
		$oAlert->aExtras['sCustomHtmlBefore'] = $sStyle;
		$oAlert->aExtras['sCustomHtmlAfter'] = <<<CODE
		<script>
		$('.form_input_date').each(function() {
			var name = $(this).attr('name');
			if(name == 'DateOfBirth[0]') {
				$(this).closest('div').hide();
				$('{$sFormInsertSingle}').insertBefore($(this).closest('div'));
				set0();
			}

			if(name == 'DateOfBirth[1]') {
				$(this).closest('div').hide();
				$('{$sFormInsertCouple}').insertBefore($(this).closest('div'));
				set1();
			}

		});
			function set0() {
				if($("#month0").val() != 0 && $("#day0").val() != 0 && $("#year0").val() != 0) $("input[name='DateOfBirth\\[0\\]']").val($("#year0").val() + '-' + $("#month0").val() + '-' + $("#day0").val());
			}

			function set1() {
				if($("#month1").val() != 0 && $("#day1").val() != 0 && $("#year1").val() != 0) $("input[name='DateOfBirth\\[1\\]']").val($("#year1").val() + '-' + $("#month1").val() + '-' + $("#day1").val());
			}
		</script>
CODE;
	}


	function getSelector($iMemID, $iArea = 0) {
		$sFormat = getParam('deano_dropdown_date_selector_date_order');
		if($iMemID > 0) {
			// We have a member id, so not a join. So pull current date of birth.
			$sDOB = db_value("SELECT `DateOfBirth` FROM `Profiles` WHERE `ID`=" . $iMemID);
			$aDOB = explode('-', $sDOB);
			$unixdob = mktime(0,0,0,$aDOB[1],$aDOB[2],$aDOB[0]);
			if($iArea == 1) {
				$iCouple = db_value("SELECT `Couple` FROM `Profiles` WHERE `ID`=" . $iMemID);
				if($iCouple > 0) {
					$sDOB = db_value("SELECT `DateOfBirth` FROM `Profiles` WHERE `ID`=" . $iCouple);
					$aDOB = explode('-', $sDOB);
					$unixdob = mktime(0,0,0,$aDOB[1],$aDOB[2],$aDOB[0]);
				}
			}
		} else {
			//$iDiff = (int)getParam('search_end_age')-(int)getParam('search_start_age')/2;
			//$unixdob = mktime(0,0,0,date("n"),date("j"),date("Y")-$iDiff+(int)getParam('search_start_age'));
			$unixdob = 0;
		}
		$sMonthOptions = '';
		if($unixdob == 0) $sMonthOptions .= '<option value="0" selected="selected">--</option>';
		for($x=1; $x <= 12; $x++) {
			if($x == date("n", $unixdob) && $unixdob != 0) {
				$sMonthOptions .= '<option value="' . $x . '" selected="selected">' . $x . '</option>';
			} else {
				$sMonthOptions .= '<option value="' . $x . '">' . $x . '</option>';
			}
		}
		$sDayOptions = '';
		if($unixdob == 0) $sDayOptions .= '<option value="0" selected="selected">--</option>';
		for($x=1; $x <= 31; $x++) {
			if($x == date("j", $unixdob) && $unixdob != 0) {
				$sDayOptions .= '<option value="' . $x . '" selected="selected">' . $x . '</option>';
			} else {
				$sDayOptions .= '<option value="' . $x . '">' . $x . '</option>';
			}
		}
		$sYearOptions = '';
		if($unixdob == 0) $sYearOptions .= '<option value="0" selected="selected">--</option>';
		$iStart = date("Y")-(int)getParam('search_end_age');
		$iEnd = date("Y")-(int)getParam('search_start_age');
		for($x=$iStart; $x <= $iEnd; $x++) {
			if($x == date("Y", $unixdob) && $unixdob != 0) {
				$sYearOptions .= '<option value="' . $x . '" selected="selected">' . $x . '</option>';
			} else {
				$sYearOptions .= '<option value="' . $x . '">' . $x . '</option>';
			}
		}

		switch ($sFormat) {
			case 'Month-Day-Year':
		$sFormInsert = '\
			<div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border wrap">\
			<table class="dbt">\
				  <tr>\
					<td>' . _t('_deano_dropdown_date_selector_month') . '</td>\
					<td><select id="month' . $iArea . '" class="form_input_select" style="padding: 0px; width: auto;" size="1" name="month' . $iArea . '" onchange="javascript: set' . $iArea . '();">' . $sMonthOptions . '\
					  </select></td>\
					<td width="10">&nbsp;</td>\
					<td>' . _t('_deano_dropdown_date_selector_day') . '</td>\
					<td><select id="day' . $iArea . '" class="form_input_select" style="padding: 0px; width: auto;" size="1" name="day' . $iArea . '" onchange="javascript: set' . $iArea . '();">' . $sDayOptions . '\
					  </select></td>\
					<td width="10">&nbsp;</td>\
					<td>' . _t('_deano_dropdown_date_selector_year') . '</td>\
					<td><select id="year' . $iArea . '" class="form_input_select" style="padding: 0px; width: auto;" size="1" name="year' . $iArea . '" onchange="javascript: set' . $iArea . '();">' . $sYearOptions . '\
					  </select></td>\
				  </tr>\
				</table>\
			</div>\
		';
			break;
			case 'Year-Month-Day':
		$sFormInsert = '\
			<div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border wrap">\
			<table class="dbt">\
				  <tr>\
					<td>' . _t('_deano_dropdown_date_selector_year') . '</td>\
					<td><select id="year' . $iArea . '" class="form_input_select" style="padding: 0px; width: auto;" size="1" name="year' . $iArea . '" onchange="javascript: set' . $iArea . '();">' . $sYearOptions . '\
					  </select></td>\
					<td width="10">&nbsp;</td>\
					<td>' . _t('_deano_dropdown_date_selector_month') . '</td>\
					<td><select id="month' . $iArea . '" class="form_input_select" style="padding: 0px; width: auto;" size="1" name="month' . $iArea . '" onchange="javascript: set' . $iArea . '();">' . $sMonthOptions . '\
					  </select></td>\
					<td width="10">&nbsp;</td>\
					<td>' . _t('_deano_dropdown_date_selector_day') . '</td>\
					<td><select id="day' . $iArea . '" class="form_input_select" style="padding: 0px; width: auto;" size="1" name="day' . $iArea . '" onchange="javascript: set' . $iArea . '();">' . $sDayOptions . '\
					  </select></td>\
					<td width="10">&nbsp;</td>\
				  </tr>\
				</table>\
			</div>\
		';
				break;
			case 'Day-Month-Year':
		$sFormInsert = '\
			<div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border wrap">\
			<table class="dbt">\
				  <tr>\
					<td>' . _t('_deano_dropdown_date_selector_day') . '</td>\
					<td><select id="day' . $iArea . '" class="form_input_select" style="padding: 2px; width: auto;" size="1" name="day' . $iArea . '" onchange="javascript: set' . $iArea . '();">' . $sDayOptions . '\
					  </select></td>\
					<td width="10">&nbsp;</td>\
					<td>' . _t('_deano_dropdown_date_selector_month') . '</td>\
					<td><select id="month' . $iArea . '" class="form_input_select" style="padding: 2px; width: auto;" size="1" name="month' . $iArea . '" onchange="javascript: set' . $iArea . '();">' . $sMonthOptions . '\
					  </select></td>\
					<td width="10">&nbsp;</td>\
					<td>' . _t('_deano_dropdown_date_selector_year') . '</td>\
					<td><select id="year' . $iArea . '" class="form_input_select" style="padding: 2px; width: auto;" size="1" name="year' . $iArea . '" onchange="javascript: set' . $iArea . '();">' . $sYearOptions . '\
					  </select></td>\
				  </tr>\
				</table>\
			</div>\
		';
				break;
		}
		return $sFormInsert;
	}

}

?>