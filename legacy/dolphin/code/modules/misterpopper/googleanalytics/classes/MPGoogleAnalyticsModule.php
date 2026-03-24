<?php

/**
* Copyright (c) 2012-2016 Andreas Pachler - http://www.paan-systems.com
* This is a commercial product made by Andreas Pachler and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Andreas Pachler.
* This notice may not be removed from the source code.
*/

bx_import('BxDolModule');
bx_import('BxDolPageView');

class MPGoogleAnalyticsModule extends BxDolModule
{
    /**
     * Variables
     */
    var $sHomeUrl;
    var $sHomePath;
    var $sModuleUrl;

    /**
     *
     *
     * @param unknown $aModule (reference)
     */
    function MPGoogleAnalyticsModule(&$aModule)
    {
        parent::BxDolModule($aModule);

        $this->sHomeUrl = $this->_oConfig->getHomeUrl();
        $this->sHomePath = $this->_oConfig->getHomePath();
        $this->sModuleUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();
    }

    /**
     *
     *
     * @param unknown $sActionMode (optional)
     * @param unknown $sExtraParam (optional)
     */
    function actionAdministration($sActionMode = '', $sExtraParam = '')
    {
        $GLOBALS['iAdminPage'] = 1;

        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();

            return;
        }

        $sSettings = $this->_getAdministrationSettings();
        $sCode = $this->_oTemplate->adminBlock($sSettings, _t('_mp_googleanalytics_settings'));

        $this->_oTemplate->pageStart();

        $aVars = array(
            'module_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri(),
        );
        $sContent = $this->_oTemplate->parseHtmlByName('admin_links.html', $aVars);

        echo $this->_oTemplate->adminBlock($sContent, _t('_mp_googleanalytics_admin_links'));
        echo $sCode;
        $this->_oTemplate->addCssAdmin(array('forms_adv.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCodeAdmin (_t('_mp_googleanalytics'));
    }

    //-- INJECTIONS --//

    /**
     *
     *
     * @return unknown
     */
    function getAnalyticsInjection()
    {
        $sCode = getParam('mp_googleanalytics_injection') != 'off' ? $this->_getAnalyticsContent() : '';

        return $sCode;
    }

    //-- PRIVATE METHODS --//

    /*
    * Draw administration settings
    */

    /**
     *
     *
     * @return unknown
     */
    function _getAdministrationSettings()
    {
        $iId = $this->_oDb->getSettingsCategory(); // get our setting category id
        if (empty($iId)) // if category is not found display page not found
            return MsgBox(_t('_sys_request_page_not_found_cpt'));

        bx_import('BxDolAdminSettings'); // import class

        $mixedResult = '';
        if (isset($_POST['save']) && isset($_POST['cat'])) { // save settings
            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId); // get display form code
        $sResult = $oSettings->getForm();

        if ($mixedResult !== true && !empty($mixedResult)) // attach any resulted messages at the form beginning
            $sResult = $mixedResult . $sResult;

        return $sResult;
    }

    /**
     * get admin filter field
     *
     * @return text
     */
    function _getAdmFilterList()
    {
        $this->_oTemplate->addCssAdmin('admin.css');
        $this->_oTemplate->addCssAdmin('unit.css');
        $this->_oTemplate->addCssAdmin('main.css');
        $this->_oTemplate->addCssAdmin('forms_extra.css');
        $this->_oTemplate->addCssAdmin('forms_adv.css');

        $sFilter = false != bx_get('filter') ? bx_get('filter') : '';

        return $this->_oTemplate->getAdminFilterList($sFilter);
    }

    /**
     * get the google analytics code
     */
    function _getAnalyticsContent()
    {
        $sCode = '';
        
        $sTrackingID = getParam('mp_googleanalytics_tracking_id');
        if (is_string($sTrackingID)) {
            // check if we want to add the opt-out code
            $sOptOutCode = false;
            if (getParam('mp_googleanalytics_opt_out') == 'on') {
                $aVars = array (
                    'tracking_id' => $sTrackingID,
                );
                $sOptOutCode = $this->_oTemplate->parseHtmlByName('out_out.html', $aVars);
            }

            $aVars = array (
                'tracking_id' => $sTrackingID,
                'bx_if:is_anonymize_ip' => array(
                    'condition' => getParam('mp_googleanalytics_anonymize_ip'),
                    'content' => array('anonymize_ip' => "_gaq.push(['_gat._anonymizeIp']);"),
                ),
                'bx_if:is_opt_out' => array(
                    'condition' => is_string($sOptOutCode),
                    'content' => array('opt_out_code' => $sOptOutCode),
                ),
            );

            $sCode = $this->_oTemplate->parseHtmlByName('tracking_code.html', $aVars);
        }

        return $sCode;
    }
}
