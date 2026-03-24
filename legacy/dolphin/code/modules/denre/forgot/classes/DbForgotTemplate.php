<?php

bx_import ('BxDolTwigTemplate');

class DbForgotTemplate extends BxDolTwigTemplate
{
    function DbForgotTemplate(&$oConfig, &$oDb)
    {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }
}

?>