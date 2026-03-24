<?php
/***************************************************************************
Membership Management Pro v.3.0
Created by Martinboi
http://www.martinboi.com
***************************************************************************/
class MbMmpPagePayments{
  var $_oMain, $iUserId = 0;
  
	function MbMmpPagePayments(){
		$this ->_oMain = BxDolModule::getInstance("MbMmpModule");
		$this->subs_url = BX_DOL_URL_MODULES.'martinboi/memberships/';
		$this->sBase = BX_DOL_URL_ROOT.'m/memberships/';
	}
	function getPaymentStats(){
		$sCur = getParam('currency_sign');
		
		$aVars = array(
			'all_time' => (!$this->getSales()) ? $sCur.'0' :  $sCur.$this->getSales(),
			'this_month' => (!$this->getMonthSales()) ?  $sCur.'0' :  $sCur.$this->getMonthSales(),
		);
		$sContent = $this->_oMain->_oTemplate->parseHtmlByName('admin_sales_stats',$aVars);
		return DesignBoxAdmin('Sales Statistics', $sContent);
	}
	function getPaymentsPageCode(){
		$sCur = getParam('currency_sign');
		if($_POST['adm_payments_del'] && is_array($_POST['payments'])){
			$this->removePayments($_POST['payments']);
			$sMsg = MsgBox('Successfully Removed',2);			
		}
	
		$aPayments = $this->getPayments();
		$sMsg = (count($aPayments) > '0') ?  '' : MsgBox('No Payments');
		
		if($_POST['adm_payments_del'] && !$_POST['payments'])
			$sMsg = MsgBox('Nothing Selected');

		$iStart = ($_GET['start']) ? $_GET['start'] : 0;
		$iPerPage = ($_GET['per_page']) ? $_GET['per_page'] : 10;
		$sDateFormat = getLocaleFormat(BX_DOL_LOCALE_DATE, BX_DOL_LOCALE_DB);
		$iCount = count($aPayments);
		$iCounter = '0';
		$aRange = range($iStart,$iStart+($iPerPage-1));
		foreach($aPayments as $aPayment){
			if(!in_array($iCounter++,$aRange)) continue;
			$sDateFixed = $this->_oMain->_oTemplate->getTimeStamp($aPayment['date']);
		   	$aItems[] = array(
		      	'id' 			=> $aPayment['id'],
		        'txn_id' 		=> $aPayment['txn_id'],
		        'amount' 		=> $sCur.$aPayment['amount'],
		        'user' 			=> $aPayment['NickName'],
		        'date' 			=> date('m-d-Y',$sDateFixed),
		        'status' 		=> $aPayment['status'],
				'processor'		=> $aPayment['processor'],
		    );
		}
	    $oPaginate = new BxDolPaginate(array(
	      	'start' => $iStart,
			'count' => $iCount,
			'per_page' => $iPerPage,
			'sorting'    => 'last',
			'per_page_changer'   => true,
			'page_reloader'      => true,
		    'page_url' => $this->sBase.'payments/?start={start}&per_page={per_page}',
	    ));
	    $sPaginate = $oPaginate->getPaginate();   

		$aButtons = array(
	        'adm_payments_del' 		=> 'Delete'
	    ); 
		$sControls = BxTemplSearchResult::showAdminActionsPanel('payments_form', $aButtons, 'payments', true ,false); 

		$aResult = array(
			'bx_repeat:items' => $aItems,
			'paginate' => $sPaginate,
			'controls' => $sControls,
			'message' => $sMsg
		);
	    return DesignBoxAdmin('Payments', $this->_oMain->_oTemplate->parseHtmlByName('admin_payments', $aResult));
	}

	//---------------------------- DB Methods ------------------------------------//
	function getSales(){
		$iAmount = db_value("SELECT SUM(`amount`) FROM `mmp_payments`");
		return $iAmount;
	}
	function getMonthSales(){
		$iMonth = date('n');
		$iAmount = db_value("SELECT SUM(`amount`) FROM `mmp_payments` WHERE MONTH(`date`) = '{$iMonth}'");
		return $iAmount;
	}
	function getPayments(){
		$aSubs = $this->_oMain->_oDb->getAll(
			"SELECT
				`Profiles`.`ID`,
				`Profiles`.`NickName`,
				`mmp_payments`.`id`,
				`mmp_payments`.`txn_id`,
				`mmp_payments`.`membership_id`,
				`mmp_payments`.`user_id`,
				`mmp_payments`.`amount`,
				`mmp_payments`.`date`,
				`mmp_payments`.`status`,
				`mmp_payments`.`processor`,
				`sys_acl_levels`.`ID`,
				`sys_acl_levels`.`Name`
			FROM
				`Profiles`,
				`mmp_payments`,
				`sys_acl_levels`
			WHERE
				`Profiles`.`ID` = `mmp_payments`.`user_id` AND
				`mmp_payments`.`membership_id` = `sys_acl_levels`.`ID`");
		return $aSubs;
	}
	function removePayments($aVars){
		foreach($aVars as $k => $v){
			db_res("DELETE FROM `mmp_payments` WHERE `id` = '{$v}'");
		}
	}
}