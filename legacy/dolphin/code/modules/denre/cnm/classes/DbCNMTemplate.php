<?php
bx_import ('BxDolTwigTemplate');

class DbCNMTemplate extends BxDolTwigTemplate
{    
    function DbCNMTemplate(&$oConfig, &$oDb)
    {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }

    function getMessageBlock($aOptions)
    {
        $sHeaderTag = $this->_wrapInTagJs('modules/denre/cnm/templates/base/js/jquery.cookiebar.js');
        $this->addInjection('injection_header', 'text', $sHeaderTag);

        foreach($aOptions as $sKey => $sValue)
            $sOptions .= $sKey . ': ' . $sValue . ', ';

        $CCSJs = $this->_wrapInTagJsCode('$(document).ready(function(){$.cookieBar({' . $sOptions . '});});');
        $this->addInjection('injection_footer', 'text', $CCSJs);

        $this->addCss('jquery.cookiebar.css');
    }

}

?>