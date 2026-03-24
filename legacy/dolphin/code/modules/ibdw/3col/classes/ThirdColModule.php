<?php
/**********************************************************************************
*                            IBDW 3Col Dolphin Smart Community Builder
*                              -------------------
*     begin                : May 1 2010
*     copyright            : (C) 2010 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW 3Col is not free and you cannot redistribute and/or modify it.
* 
* IBDW 3Col is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

bx_import('BxDolModule');

class ThirdColModule extends BxDolModule {
    function ThirdColModule(&$aModule) {        
        parent::BxDolModule($aModule);
    }

    function actionHome () {
        $this->_oTemplate->pageStart();
        $sDateFormat = getParam ('me_tcol_date_format');
        $isShowUserTime = getParam('me_tcol_enable_js_date') ? true : false;
        $aVars = array (
            'server_time' => date($sDateFormat),
            'bx_if:show_user_time' => array(
                'condition' => $isShowUserTime,
                'content' => array(),
            ),
        );
        echo $this->_oTemplate->parseHtmlByName('main', $aVars);
        $this->_oTemplate->pageCode(_t('_me_tcol'), true);
    }

    function actionAdministration () {
        if (!$GLOBALS['logged']['admin']) { // check access to the page
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart(); // all the code below will be wrapped by the admin design
	    $iId = $this->_oDb->getSettingsCategory(); // get our setting category id
	    if(empty($iId)) { // if category is not found display page not found
            echo MsgBox(_t('_sys_request_page_not_found_cpt'));
            $this->_oTemplate->pageCodeAdmin (_t('_me_tcol'));
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
        echo DesignBoxAdmin (_t('_me_tcol'), $sResult); // dsiplay box
        $this->_oTemplate->pageCodeAdmin (_t('_me_tcol')); // output is completed, admin page will be displaed here
    }    
}
?>