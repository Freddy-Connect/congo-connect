<?php
bx_import('BxDolModule');
bx_import( 'BxDolEmailTemplates' );
bx_import( 'BxTemplFormView' );

class BxDolAdvForgotCheckerHelper extends BxDolFormCheckerHelper
{
    // Denre - Added extra privacy functionality
    function checkEmail($s)
    {
        if (!preg_match( '/^[a-z0-9_\-]+(\.[_a-z0-9\-]+)*@([_a-z0-9\-]+\.)+([a-z]{2}|aero|arpa|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel)$/i', $s ))
            return false;

        $iID = (int)db_value( "SELECT `ID` FROM `Profiles` WHERE `Email` = '$s'" );

        /* check if extra privacy is set */
        if (!$iID && getParam('enable_extra_privacy'))
        {
            $GLOBALS['checkFailed'] = true;
            return true;
        }
        else if (!$iID && getParam('enable_extra_privacy') != 'on')
            return _t( '_MEMBER_NOT_RECOGNIZED', $site['title'] );

        return true;
    }

    function checkPassword($s)
    {
        $pwd = bx_get('Password');
        $new_pwd = $s;

        if($pwd == $new_pwd)
            return true;

    return false;
    }

}

class DbForgotModule extends BxDolModule
{
    function DbForgotModule(&$aModule)
    {
        parent::BxDolModule($aModule);
    }

    function actionHome()
    {
        $this->_oTemplate->pageStart();
        $_page['name_index']   = 37;
        $logged['member'] = member_auth( 0, false );
        $_page['header'] = _t( "_Forgot password?" );
        $_page['header_text'] = _t( "_Password retrieval", $site['title'] );

        // --------------- page components

        $_ni = $_page['name_index'];

        if(!(bx_get('ConfID') && bx_get(ConfCode)) )
            $aCode = $this->AdvForm();
        else
        {
            $aCode = array(
                'message_status' => '',
                'message_info' => '',
                'bx_if:form' => array(
                    'condition' => false,
                    'content' => array(
                        'form' => ''
                    )
                ),
                'bx_if:next' => array(
                    'condtion' => false,
                    'content' => array(
                        'next_url' => '',
                    )
                )
            );
        }
        $oForm = new BxTemplFormView($aCode);
            
        $oForm->initChecker($aInfo);

        // Denre - added check for ConfId and ConfCode
        if ($oForm->isSubmittedAndValid())
        {
            if(!(bx_get('ConfID') && bx_get(ConfCode)))
            {
                // Denre - Following three lines were after extra privacy functionality
                $_page['header'] = $action_result = _t( "_db_pwd_maybe_recognized" );
                $_page['header_text'] = _t( "_RECOGNIZED", $site['title'] );
                echo _t( "_db_pwd_maybe_sent", $site['title'] );

                // Check if entered email is in the base
                if(!$GLOBALS['checkFailed'])
                    $action_result1 = $this->doEmail();

                $sForm = '';
            }
        }
        else
        { // Denre new added code
            $iID = bx_get('ConfID');
            $sConfCode = bx_get('ConfCode');
            $ID = (int)$iID;
            $ConfCode = clear_xss($sConfCode);

            if ($ID && $ConfCode && !($ID == '' || $ConfCode == ''))
                $member = getProfileInfo($ID);

            $ConfCodeReal = md5($member['Password']);

            if ($member['Password'] <> '' && strcmp($ConfCode, $ConfCodeReal) == 0)
            {
                $_page['name_index'] = 81;
                    $_page['css_name'] = array(
                        'member_panel.css',
                        'categories.css',
                        'explanation.css'
                    );

                $_page['header'] = _t( "_My Account" );

                $this->updateUserNewPwd($member['ID'], $ConfCode);
                $member['Password'] = $member['New_Password'];

                require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
                $oZ = new BxDolAlerts('profile', 'before_login', 0, 0, array('login' => $member['ID'], 'password' => $member['Password'], 'ip' => getVisitorIP(), 'module' => 'forgot'));
                $oZ->alert();

                // Auto login after password change
                require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
                $rememberMe = true;

                //bx_login($member['ID'], $rememberMe); //
                if($p_arr = bx_login($member['ID'], $rememberMe))
                {
                    //Storing IP Address
                    if (getParam('enable_member_store_ip') == 'on' && $site['ver'] == '7.1')
                    {
                        $iCurLongIP = sprintf("%u", ip2long(getVisitorIP()));
                        db_res("INSERT INTO `sys_ip_members_visits` SET `MemberID` = '{$p_arr['ID']}', `From`='{$iCurLongIP}', `DateTime`=NOW()");
                    }

                    if(getParam('forgot_redirect') == 'profile')
                        $forgot_redirect = $member['NickName'];
                    else if(getParam('forgot_redirect') == 'profile edit')
                    {
                        Redirect(BX_DOL_URL_ROOT . 'pedit.php',  array('ID' => $member['ID']), 'post');
                        exit;
                    } else if(getParam('forgot_redirect') == 'account')
                        $forgot_redirect = 'member.php';

                    $sUrlRelocate = BX_DOL_URL_ROOT . $forgot_redirect;

                    $_page['name_index'] = 150;
                    $_page['css_name'] = '';

                    $_ni = $_page['name_index'];
                    $_page_cont[$_ni]['page_main_code'] = MsgBox( _t( '_Please Wait' ) );
                    $_page_cont[$_ni]['url_relocate'] = htmlspecialchars( $sUrlRelocate );

                    Redirect($sUrlRelocate);
                }
            } else
            {
                $_page['header'] = _t( "_FORGOT" );
                $_page['header_text'] = _t( "_FORGOT", $site['title'] );
                $action_result = _t( "_FORGOT", $site['title'] );
                $gCode = $oForm->getCode();

                $sCode = $action_result;
                $sCode .= $gCode;
            }
        }
        echo $gCode;
        $this->_oTemplate->pageCode($action_result);
    }

    function AdvForm()
    {
        $inputs = $this->genInputs();
        $aForm = array(
            'form_attrs' => array(
                'name'     => 'forgot_form',
                'action'   => BX_DOL_URL_MODULES . '?r=forgot',
                'method'   => 'post',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'do_submit',
                ),
                'checker_helper' => 'BxDolAdvForgotCheckerHelper',
            ),
                'inputs' => $inputs
        );
        return $aForm;
    }

    function genInputs()
    {
		
		  $inputs[] = array(
            'type' => 'email',
            'name' => 'Email',
            'caption' => _t('_My Email'),
			'info' => _t('_My Email_info'),
            'value' => isset($_POST['Email']) ? $_POST['Email'] : '',
            'required' => true,
            'checker' => array(
            'func' => 'email',
            'error' => _t( '_Incorrect Email' )
            ),
        );

        if(!getParam('enable_password_generation') == 'on')
        {
            $inputs[] = array(
                'type' => 'password',
                'name' => 'Password',
                'caption' => _t('_Nouveau_Password'),
                'value' => isset($_POST['Password']) ? $_POST['Password'] : '',
                'required' => true,
                'checker' => array(
                    'func' => 'Length',
                    'params' => array(5,16),
                    'error' => _t( '_db_pwd_length_err' )
                ),
            );

            $inputs[] = array(
                'type' => 'password',
                'name' => 'Password_Confirm',
                'caption' => _t('_Confirm password'),
                'value' => isset($_POST['Password_Confirm']) ? $_POST['Password_Confirm'] : '',
                'required' => true,
                'checker' => array(
                    'func' => 'Password',
                    'error' => _t( '_db_pwd_not_same_err' )
                ),
            );
        }

      
        $inputs[] = array(
            'type' => 'submit',
            'name' => 'do_submit',
            'value' => _t( "_Retrieve my information" ),
        );

        return $inputs;
    }

    function doEmail()
    {
        $result = _t( "_MEMBER_RECOGNIZED_MAIL_NOT_SENT", $site['title'] );

        $sEmail = process_db_input($_POST['Email'], BX_TAGS_STRIP);
        if($memb_arr = db_arr( "SELECT `ID` FROM `Profiles` WHERE `Email` = '$sEmail'" ))
        {
            $recipient = $sEmail;

            $rEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $rEmailTemplate -> getTemplate( 't_ConfirmForgot', $memb_arr['ID'] ) ;

            $aPlus = $this->generateUserNewPwd($memb_arr['ID']);

            $aProfile = getProfileInfo($memb_arr['ID']);
            $mail_ret = sendMail( $recipient, $aTemplate['Subject'], $aTemplate['Body'], $memb_arr['ID'], $aPlus, 'html', false, true );

            if (!$mail_ret) // Denre - Added email send check
                return _t( "_MEMBER_RECOGNIZED_MAIL_NOT_SENT", $site['title'] );

            $result = _t( "_MEMBER_RECOGNIZED_MAIL_SENT", $site['url'], $site['title'] );

            // create system event
            require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
            $oZ = new BxDolAlerts('profile', 'password_restore',  $memb_arr['ID']);
                $oZ->alert();
        }
        return $result;
    } // Denre - End of added if statement

    function generateUserNewPwd($ID)
    {
        $memberT = getProfileInfo($ID);
        $sPwd = array();
        $sPwd['Password']  = (getParam('enable_password_generation'))? genRndPwd() : bx_get('Password');
        $sPwd['ConfCode'] = MD5($memberT['Password']);

        $VerifyURL = BX_DOL_URL_MODULES . "?r=forgot&ConfID=$ID&amp;ConfCode=".$sPwd['ConfCode'];
        $sPwd['VerifyURL'] = '<a href="'.$VerifyURL.'">'.$VerifyURL.'</a>';

        $sSalt = genRndSalt();
        $dummy = genRndPwd();

        $sQuery = "
        INSERT INTO `db_tmp_password` (`ID`, `Password`, `Salt`, `pwd_key`) 
        VALUES ('" . $ID . "', '" . encryptUserPwd($sPwd['Password'], $sSalt) . "', '" . $sSalt . "', '".$sPwd['ConfCode']."')
        ON DUPLICATE KEY UPDATE `Password` = '" . encryptUserPwd($sPwd['Password'], $sSalt) . "', `Salt` = '" . $sSalt . "'";

        db_res($sQuery);
        createUserDataFile($ID);

        return $sPwd;
    }

    function updateUserNewPwd($ID, $PCode)
    {
        $sPwd = false;

        if($memb_arr = db_arr("SELECT `Password`, `Salt` FROM `db_tmp_password` WHERE `ID` = '" . $ID . "' AND `pwd_key` = '" . $PCode . "'"))
        {
            $new_password = $memb_arr['Password'];
            $new_salt = $memb_arr['Salt'];

            $sQuery = "
              UPDATE `Profiles`
              SET
                `Password` = '" . $new_password . "',
                `Salt` = '" . $new_salt . "'
              WHERE `ID`='" . $ID . "'
              LIMIT 1
            ";

            if(db_res($sQuery))
            {
                $sPwd = TRUE;
                $dQuery = "DELETE FROM `db_tmp_password` WHERE `ID` = '" . $ID . "'";

                if(db_res($dQuery))
                    unset($memb_arr, $sQuery, $dQuery, $ID, $PCode);
            }
        }

        return $sPwd;
    }

    function serviceInitAdvForgot($inp)
    {
        if(substr($inp->aExtras[form_object]->aFormAttrs[action], -10) == 'forgot.php')
        {
            $inp->aExtras[form_object]->_sCheckerHelper = 'BxDolAdvForgotCheckerHelper';
            $inp->aExtras[form_object]->aParams[checker_helper] = 'BxDolAdvForgotCheckerHelper';
            $inp->aExtras[form_object]->aFormAttrs[action] = BX_DOL_URL_MODULES . '?r=forgot';
            $inp->aExtras[form_attrs][action] = BX_DOL_URL_MODULES . '?r=forgot';

            if(!getParam('enable_user_pwd'))
                $inp->aExtras[form_object]->aInputs = $this->genInputs();
        }
    }

    function actionAdministration ()
    {

        if (!isAdmin()) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the admin design

        $iId = $this->_oDb->getSettingsCategory(); // get our setting category id
        if(empty($iId)) { // if category is not found display page not found
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_db_afo'));
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

        echo DesignBoxAdmin (_t('_db_afo'), $sResult); // dsiplay box
        
        $this->_oTemplate->pageCodeAdmin (_t('_db_afo')); // output is completed, admin page will be displaed here
    }   

    function isAdmin ()
    {
        return $GLOBALS['logged']['admin'] || $GLOBALS['logged']['moderator'];
    }

}
