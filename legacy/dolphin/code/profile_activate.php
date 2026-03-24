<?php

/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( 'inc/header.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolEmailTemplates.php' );

// --------------- page variables

$_page['name_index'] = 40;

$ID = bx_get('ConfID');
$ConfCode = bx_get('ConfCode');

if (!$ID && !$ConfCode)
    exit;

$logged['member']	= member_auth(0, false);

$_page['header'] = _t("_Email confirmation");
$_page['header_text'] = _t("_Email confirmation Ex");

// --------------- page components

$_ni = $_page['name_index'];
$_page_cont[$_ni]['page_main_code'] = PageCompPageMainCode($ID, $ConfCode);

// --------------- [END] page components

PageCode();

// --------------- page components functions

/**
 * page code function
 */
function PageCompPageMainCode($iID, $sConfCode)
{
    global $site;

    $ID = (int)$iID;
    $ConfCode = clear_xss($sConfCode);
    $p_arr = getProfileInfo($ID);

    if (!$p_arr) {
        $_page['header'] = _t("_Error");
        $_page['header_text'] = _t("_Profile Not found");
        return MsgBox(_t('_Profile Not found Ex'));
    }

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
/*freddy Modif 7 janvier 2026 SALT aligné avec sendConfMail() qui utilise "secret_ph"
    if ($p_arr['Status'] == 'Unconfirmed') {
        $ConfCodeReal = base64_encode( base64_encode( crypt( $p_arr['Email'], CRYPT_EXT_DES ? "secret_co" : "se" ) ) );
	*/	
	
	if ($p_arr['Status'] == 'Unconfirmed') {

        // ✅ IMPORTANT : SALT aligné avec sendConfMail() qui utilise "secret_ph"
        $ConfCodeReal = base64_encode(base64_encode(
            crypt($p_arr['Email'], CRYPT_EXT_DES ? "secret_ph" : "se")
        ));
		
		/****freddy fin modif  7 janvier 2026 */
		
		
		
		
		
        if (strcmp($ConfCode, $ConfCodeReal) !== 0) {
            $aForm = array(
                'form_attrs' => array (
                    'action' =>  BX_DOL_URL_ROOT . 'profile_activate.php',
                    'method' => 'post',
                    'name' => 'form_change_status'
                ),

                'inputs' => array(
                    'conf_id' => array (
                        'type'     => 'hidden',
                        'name'     => 'ConfID',
                        'value'    => $ID,
                    ),
                    'conf_code' => array (
                        'type'     => 'text',
                        'name'     => 'ConfCode',
                        'value'    => '',
                        'caption'  => _t("_Confirmation code")
                    ),
                    'submit' => array (
                        'type'     => 'submit',
                        'name'     => 'submit',
                        'value'    => _t("_Submit"),
                    ),
                ),
            );
            $oForm = new BxTemplFormView($aForm);
            $aCode['message_status'] = _t("_Profile activation failed");
            $aCode['message_info'] = _t("_EMAIL_CONF_FAILED_EX");
            $aCode['bx_if:form']['condition'] = true;
            $aCode['bx_if:form']['content']['form'] = $oForm->getCode();
        } else {
            $aCode['bx_if:next']['condition'] = true;
            $aCode['bx_if:next']['content']['next_url'] = BX_DOL_URL_ROOT . 'member.php';

            $send_act_mail = FALSE;
            if (getParam('autoApproval_ifJoin') == 'on' && !(getParam('sys_dnsbl_enable') && 'approval' == getParam('sys_dnsbl_behaviour') && bx_is_ip_dns_blacklisted('', 'join'))) {
                $status = 'Active';
                $send_act_mail = TRUE;
                $aCode['message_info'] = _t( "_PROFILE_CONFIRM" );
            } else {
                $status = 'Approval';
                $aCode['message_info'] = _t("_EMAIL_CONF_SUCCEEDED", $site['title']);
				    }
			
	


 $update = bx_admin_profile_change_status($ID, $status, $send_act_mail);
			
		
		
		
		
		
		//8 janvier 2026 ajout freddy chatgpt ✅ UX : afficher confirmation e + progression + ✔️ puis redirection vers CV numérique
if ($status == 'Active' || $status == 'Approval') {

    // ✅ Connexion automatique
    bx_login((int)$ID);

    // ✅ Redirection vers CV numérique
    $redirectUrl = BX_DOL_URL_ROOT . "pedit.php?ID=" . (int)$ID;
    
	$aRecipient = getProfileInfo((int)$ID);
	if($aRecipient['Sex']== 'male'){
			$aMonsieurMadame = _t( '_MonsieurJoin' );
		}
		else{
			$aMonsieurMadame = _t( '_MadameJoin' );
		}

    // ✅ Texte multilingue (tu gères la traduction via _t)
    $message = '<b>'.$aMonsieurMadame.' '.$aRecipient['FirstName'].' ,'.'</b>'.'<br>'. _t("_Messa_Confirmation_Email");
	
	// ✅ recuperer le nom du site
	$aSiteName = isset($GLOBALS['site']['title']) ? $GLOBALS['site']['title'] : getParam('site_title');
	
	

    // ✅ Logo Jeunesse 243 (adapte le chemin si besoin)
    $logoUrl = BX_DOL_URL_ROOT . "images/logo/logo_email.png";

    // ✅ Durée avant redirection (ms) : 3000 = 3s, 5000 = 5s
    $delayMs = 20000;

    echo '
    <!DOCTYPE html>
    <html lang="'.getCurrentLangName().'">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirmation</title>

        <style>
            body{
                margin:0;
                padding:16px;
                min-height:100vh;
                display:flex;
                align-items:center;
                justify-content:center;
                background:#f4f6f8;
                font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;
            }
            .card{
                width:100%;
                max-width:420px;
                background:#fff;
                border-radius:16px;
                box-shadow:0 14px 35px rgba(0,0,0,.12);
                padding:22px 18px;
                text-align:center;
            }
            .brand{
                display:flex;
                align-items:center;
                justify-content:center;
                gap:10px;
                margin-bottom:12px;
            }
            .brand img{
                width:64px;
                height:64px;
                object-fit:contain;
                border-radius:12px;
            }
            .brand-name{
                font-weight:900;
                font-size:22px;
                color:#111827;
            }
            .msg{
                font-size:18px;
                color:#475569;
                line-height:1.45;
                margin-top:8px;
            }

            .progress-wrap{
                margin:18px auto 0;
                width:100%;
                max-width:320px;
                height:10px;
                background:#e5e7eb;
                border-radius:999px;
                overflow:hidden;
            }
            .progress-bar{
                height:100%;
                width:0%;
                background:linear-gradient(90deg,#16a34a,#22c55e);
                border-radius:999px;
            }
            .progress-text{
                font-size:15px;
                font-weight:600;
                color:#374151;
                margin-top:10px;
                display:flex;
                align-items:center;
                justify-content:center;
                gap:6px;
            }

            /* ✅ Animation du check */
            .check-show{
                animation: popCheck .35s ease-out forwards;
            }
            @keyframes popCheck{
                0%{ transform:scale(0.6); opacity:0; }
                100%{ transform:scale(1); opacity:1; }
            }
        </style>

        <script>
            (function(){
                var delay = '.(int)$delayMs.';
                var redirectUrl = '.json_encode($redirectUrl).';
                var bar, percentText, checkIcon;
                var start = Date.now();

                function update(){
                    var elapsed = Date.now() - start;
                    var percent = Math.min(100, Math.round((elapsed / delay) * 100));

                    bar.style.width = percent + "%";
                    percentText.textContent = percent + "%";

                    if(elapsed >= delay){
                        // ✅ Forcer 100% + afficher ✔️ puis redirection douce
                        bar.style.width = "100%";
                        percentText.textContent = "100%";

                        checkIcon.style.display = "inline-block";
                        checkIcon.classList.add("check-show");

                        setTimeout(function(){
                            window.location.href = redirectUrl;
                        }, 600);

                        return;
                    }
                    requestAnimationFrame(update);
                }

                window.addEventListener("load", function(){
                    bar = document.getElementById("progressBar");
                    percentText = document.getElementById("progressPercent");
                    checkIcon = document.getElementById("checkIcon");
                    update();
                });
            })();
        </script>
    </head>
    <body>

        <div class="card">
            <div class="brand">
                <img src="'.$logoUrl.'" alt="'.$aSiteName.'">
                <div class="brand-name">'.$aSiteName.'</div>
            </div>

            <div class="msg">'.$message.'</div>

            <div class="progress-wrap">
                <div class="progress-bar" id="progressBar"></div>
            </div>

            <div class="progress-text">
                <span id="progressPercent">0%</span>
                <span id="checkIcon" style="display:none;color:#16a34a;font-size:22px;">✔️</span>
                <span>· '._t("_Redirecting").'</span>
            </div>
        </div>

    </body>
    </html>';
    exit;
}

		
	///////Fin ajout	
		
		
		
		
		
		
		
		
			

            // Promotional membership
            if (getParam('enable_promotion_membership') == 'on') {
                $memership_days = getParam('promotion_membership_days');
                setMembership( $p_arr['ID'], MEMBERSHIP_ID_PROMOTION, $memership_days, true );
            }

            // check couple profile;
            if ($p_arr['Couple']) {
                $update = bx_admin_profile_change_status($p_arr['Couple'], $status);

                //Promotional membership
                if (getParam('enable_promotion_membership') == 'on') {
                    $memership_days = getParam('promotion_membership_days');
                    setMembership( $p_arr['Couple'], MEMBERSHIP_ID_PROMOTION, $memership_days, true );
                }
            }
            if (getParam('newusernotify')) {
                $oEmailTemplates = new BxDolEmailTemplates();
                $aTemplate = $oEmailTemplates->getTemplate('t_UserConfirmed', $p_arr['ID']);

                sendMail($site['email_notify'], $aTemplate['Subject'], $aTemplate['Body'], $p_arr['ID']);
            }
        }
    } else
        $aCode['message_info'] = _t('_ALREADY_ACTIVATED');
    return $GLOBALS['oSysTemplate']->parseHtmlByName('profile_activate.html', $aCode);
}
