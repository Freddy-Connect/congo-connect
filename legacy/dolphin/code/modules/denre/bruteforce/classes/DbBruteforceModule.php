<?php

bx_import('BxDolModule');

class DbBruteforceModule extends BxDolModule
{

    function DbBruteforceModule(&$aModule)
    {
        parent::BxDolModule($aModule);
    }

    function actionAdministration()
    {

        if (!$GLOBALS['logged']['admin']) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the admin design

	    $iId = $this->_oDb->getSettingsCategory(); // get our setting category id
	    if(empty($iId)) { // if category is not found display page not found
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_db_bruteforce'));
            return;
        }

        bx_import('BxDolAdminSettings'); // import class

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) { // save settings
            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId); // get display form code
        $sResult = $oSettings->getForm();
        	       
        if($mixedResult !== true && !empty($mixedResult)) // attach any resulted messages at the form beginning
            $sResult = $mixedResult . $sResult;

        echo DesignBoxAdmin (_t('_db_bruteforce'), $sResult); // dsiplay box
        
        $this->_oTemplate->pageCodeAdmin (_t('_db_bruteforce')); // output is completed, admin page will be displaed here
    }    

    function serviceBruteforceInit()
    {
        $this->_oTemplate->addJs ('bruteforce.js');
    }

    function serviceBruteforceLoginForm($oObject)
    {
        session_start();

        $oObject->aExtras['oForm']->aFormAttrs['onsubmit'] = "validateBruteforceLoginForm(this); return false;";

        if($_SESSION['bruteforce_id'])
        {
            $iUserId = (int) $_SESSION['bruteforce_id'];
            $iMaxCnt = getParam('db_bruteforce_cnt');

            $aCounter = $this->_oDb->getCntInfo($iUserId);
            $iCnt = $aCounter[0]['counter'];
            $sTime = $aCounter[0]['login_time'];

            $oDate = new DateTime();
            $oTime = new DateTime($sTime);
            $oLockedTime = $oDate->diff($oTime);
            $iLockedTime = getParam('db_bruteforce_time') - $oLockedTime->i;

            if($iCnt >= $iMaxCnt)
            {
                $oObject->aExtras['oForm']->aInputs = array(
                    'locked' => array(
                        'type' => 'custom',
                        'colspan' => 2,
                        'content' => _t('_db_bruteforce_account_locked', $iLockedTime)
                    ),
                    'forgot' => array(
                        'type' => 'custom',
                        'colspan' => 2,
                        'tr_attrs' => array(
                            'class' => 'bx-form-element-forgot'
                        ),
                        'content' => '<a href="' . BX_DOL_URL_ROOT . 'forgot.php">' . _t('_forgot_your_password') . '?</a>',
                    )
                );
            }
        }
    }

    function serviceBruteforceBeforeLogin($oObject = '')
    {
        $this->dwrite($oObject, "bruteforce.log");
        $this->dwrite(getParam('db_bruteforce_forgot'), "bruteforce.log");

        if($oObject->aExtras['module'] == 'forgot' && getParam('db_bruteforce_forgot') == 'on')
            return;

        $iMaxCnt = getParam('db_bruteforce_cnt');

        if(!is_numeric($_POST['ID']))
            $iUserId = $this->_oDb->getUserByNameEmail($_POST['ID']);
        else $iUserId = $_POST['ID'];

        if($iUserId)
        {
            $aCounter = $this->_oDb->getCntInfo($iUserId);
            $iCnt = $aCounter[0]['counter'];

            session_start();
            $_SESSION['bruteforce_id'] = $iUserId;

            if($iCnt >= $iMaxCnt)
            {
                echo 'blocked';
                exit;
            } else
                $this->_oDb->updateCnt($iUserId);
        }
    }

    function serviceBruteforceLogin()
    {
        $iUserId = getLoggedId();
        $this->_oDb->resetCnt($iUserId);
    }

    function dwrite($iwrite, $sFile = 'data.txt')
    {
        $op = print_r($iwrite, true);
        $fp = fopen(BX_DIRECTORY_PATH_ROOT . 'tmp/'.$sFile, 'a');
        fwrite($fp, $op."\n");
        fclose($fp);
    }

}
