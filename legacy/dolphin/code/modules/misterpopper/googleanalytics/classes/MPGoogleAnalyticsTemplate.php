<?php

/**
* Copyright (c) 2012-2016 Andreas Pachler - http://www.paan-systems.com
* This is a commercial product made by Andreas Pachler and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Andreas Pachler.
* This notice may not be removed from the source code.
*/

bx_import('BxDolModuleTemplate');

class MPGoogleAnalyticsTemplate extends BxDolModuleTemplate
{
    /**
     *
     *
     * @param unknown $oConfig (reference)
     * @param unknown $oDb     (reference)
     */
    function MPGoogleAnalyticsTemplate(&$oConfig, &$oDb)
    {
        parent::BxDolModuleTemplate($oConfig, $oDb);

        $this->_sPrefix = 'BxDolTemplate';
    }

    /**
     *
     *
     * @param  unknown $sContent
     * @param  unknown $sTitle
     * @param  unknown $aMenu    (optional)
     * @return unknown
     */
    function adminBlock($sContent, $sTitle, $aMenu = array())
    {
        $sContent = $this->parseHtmlByName('default_padding.html', array('content' => $sContent));

        return DesignBoxAdmin($sTitle, $sContent, $aMenu);
    }

    /**
     *
     *
     * @param unknown $sTitle
     */
    function pageCodeAdmin($sTitle)
    {
        global $_page;
        global $_page_cont;

        $_page['name_index'] = 9;

        $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
        $_page['header_text'] = $sTitle;

        $_page_cont[$_page['name_index']]['page_main_code'] = $this->pageEnd();

        PageCodeAdmin();
    }

    /**
     *
     */
    function pageStart()
    {
        if (0 == $this->_bObStarted) {
            ob_start();
            $this->_bObStarted = 1;
        }
    }

    /**
     *
     *
     * @param  unknown $isGetContent (optional)
     * @return unknown
     */
    function pageEnd($isGetContent = true)
    {
        if (1 == $this->_bObStarted) {
            $sRet = '';
            if ($isGetContent)
                $sRet = ob_get_clean();
            else
                ob_end_clean();
            $this->_bObStarted = 0;

            return $sRet;
        }
    }

    /**
     *
     *
     * @param unknown $sName
     */
    function addCssAdmin($sName)
    {
        $sClassPrefix = 'mp_googleanalytics_css';
        $GLOBALS['oAdmTemplate']->addLocation($sClassPrefix, $this->_oConfig->getHomePath(), $this->_oConfig->getHomeUrl());
        $GLOBALS['oAdmTemplate']->addCss($sName);
        $GLOBALS['oAdmTemplate']->removeLocation($sClassPrefix);
    }

}
