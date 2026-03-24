<?php
/***************************************************************************
Membership Management Pro v.3.0
Created by Martinboi
http://www.martinboi.com
***************************************************************************/
bx_import('BxDolPageView');

class MbMmpPageMain extends BxDolPageView {
    var $_oMain;
    var $_oTemplate;
    var $_oConfig;
    var $_oDb;

    function MbMmpPageMain(&$oMain) {
		global $_page;
        $this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oConfig = $oMain->_oConfig;
        $this->_oDb = $oMain->_oDb;
		parent::BxDolPageView('mmp_main');

        $GLOBALS['oTopMenu']->setCurrentProfileID($this->_oMain->_iProfileId);
	}

    function getBlockCode_Tight() {
		$iId = getLoggedId();
		
		return array(BxDolService::call('memberships', 'current_membership', array($iId)), array(), array(), false);
    }

    function getBlockCode_Wide() {
		
		if($_POST['joined_free'] == 'success'){
			$aAvailLevels = MsgBox(_t('_dol_subs_joined_free'),3);
		}
		$aLevels = $this->_oDb->getMemberships();

        if (is_array($aLevels) && count($aLevels) > 0) { 
    		$aAvailLevels.= $this->_oTemplate->availMemLevels(); 
 		}else{
			$aAvailLevels = MsgBox('_dol_subs_no_levels');
		}
        return array($aAvailLevels, array(), array(), false);
	}
}