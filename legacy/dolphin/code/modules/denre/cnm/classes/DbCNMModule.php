<?php
bx_import('BxDolModule');

class DbCNMModule extends BxDolModule
{
    // contain some module information ;
    var $aModuleInfo;
    // contain path for current module;
    var $sPathToModule;

    function DbCNMModule(&$aModule)
    {
        parent::BxDolModule($aModule);

    }

    function actionAdministration()
    {
        if (!isAdmin())
        { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the admin design

        $iId = $this->_oDb->getSettingsCategory(); // get our setting category id
        if(empty($iId))
        { // if category is not found display page not found
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_db_cnm'));
            return;
        }

        bx_import('BxDolAdminSettings'); // import class

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat']))
        { // save settings
            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId); // get display form code
        $sResult = $oSettings->getForm();

        if($mixedResult !== true && !empty($mixedResult)) // attach any resulted messages at the form beginning
            $sResult = $mixedResult . $sResult;

        echo DesignBoxAdmin (_t('_db_cnm'), $sResult); // dsiplay box

        $this->_oTemplate->pageCodeAdmin (_t('_db_cnm')); // output is completed, admin page will be displaed here
    }

    function serviceShowMessage()
    {
//        if(getParam('db_cnm_on') != 'on' || 'accepted' == $_COOKIE['cb-enabled'])
//            return;

        $sSiteName = getParam('site_title');
        $sPrivacyPolicy = _t('db_cnm_policy_txt');
        $sAcceptCookies = _t('db_cnm_accept_txt');

        $aReplacement = array(
            '{SiteName}' => $sSiteName,
            '{AcceptCookies}' => $sAcceptCookies,
            '{PrivacyPolicy}' => $sPrivacyPolicy,
        );

        $sMessageTxt =  _t('db_cnm_message');

       $sMessage = str_replace(array_keys($aReplacement), array_values($aReplacement), $sMessageTxt);

        $aOptions = array(
            'message' => "'" . $sMessage . "'",
            'acceptText' => "'" . _t('db_cnm_accept_txt') . "'",
            'policyButton' => (getParam('db_cnm_display_privacy_btn') == 'on') ? 'true' : 'false',
            'policyText' => "'" . _t('db_cnm_policy_txt') . "'",
            'policyURL' => "'" . getParam('db_cnm_policy_uri') . "'",
            'autoEnable' => (getParam('db_cnm_auto_enable') == 'on') ? 'true' : 'false',
            'acceptOnContinue' => (getParam('db_cnm_accept_continue') == 'on') ? 'true' : 'false',
            'acceptOnScroll' => (getParam('db_cnm_accept_scroll') == 'on') ? 'true' : 'false',
            'acceptAnyClick' => (getParam('db_cnm_accept_any_click') == 'on') ? 'true' : 'false',
            'expireDays' => (getParam('db_cnm_cookie_expire') == 0) ? 'false' : getParam('db_cnm_cookie_expire'),
            'renewOnVisit' => (getParam('db_cnm_cookie_renew') == 'on') ? 'true' : 'false',
            'forceShow' => (getParam('db_cnm_forse_show') == 'on') ? 'true' : 'false',
            'effect' => "'" . getParam('db_cnm_effect') . "'",
            'element' => "'" . getParam('db_cnm_element') . "'",
            'append' => (getParam('db_cnm_element_append') == 'append') ? 'true' : 'false',
            'fixed' => (getParam('db_cnm_element_fixed') == 'on') ? 'true' : 'false',
            'bottom' => (getParam('db_cnm_element_bottom') == 'on') ? 'true' : 'false',
            'zindex' => (getParam('db_cnm_element_zindex') == '') ? "''" : getParam('db_cnm_element_zindex'),
            'disableClick' => (getParam('db_cnm_disable_click') == 'on') ? 'true' : 'false',
        );

        $this->_oTemplate->getMessageBlock($aOptions);
    }

}

?>
