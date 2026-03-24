<?php

/**
* Copyright (c) 2012-2016 Andreas Pachler - http://www.paan-systems.com
* This is a commercial product made by Andreas Pachler and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Andreas Pachler.
* This notice may not be removed from the source code.
*/

bx_import(BxDolModuleDb);

class MPEmailTmplEditorDb extends BxDolModuleDb
{
    var $sTablePrefix;

    /**
     *
     *
     * @param unknown $oConfig (reference)
     */
    function MPEmailTmplEditorDb(&$oConfig)
    {
        parent::BxDolModuleDb();

        $this->sTablePrefix = $oConfig->getDbPrefix();
    }

    /**
     *
     *
     * @return unknown
     */
    function getTemplates()
    {
        return $this->getAll("SELECT * FROM `sys_email_templates` WHERE `LangID` = '0'");
    }

    function getFirstTemplateID()
    {
        return $this->getOne("SELECT `ID` FROM `sys_email_templates` WHERE `LangID` = '0' LIMIT 1");
    }

    /**
     *
     *
     * @param  unknown $iTmplID
     * @return unknown
     */
    function getTmplByID($iTmplID)
    {
        return $this->getRow("SELECT * FROM `sys_email_templates` WHERE `ID`='$iTmplID' LIMIT 1");
    }

    /**
     *
     *
     * @param  unknown $sName
     * @param  unknown $iLangID
     * @return unknown
     */
    function getTmplByName($sName, $iLangID)
    {
        return $this->getRow("SELECT * FROM `sys_email_templates` WHERE `Name`='$sName' AND `LangID` = '$iLangID' LIMIT 1");
    }

    /**
     *
     *
     * @param unknown $sTmplID
     * @param unknown $sLangID
     * @param unknown $dbContent
     * @param unknown $sSubject
     * @param unknown $aTemplate
     */
    function updateTemplate($sTmplID, $sLangID, $dbContent, $sSubject, $aTemplate)
    {
        if ($this->query("UPDATE `sys_email_templates` SET `Body` = '" . process_db_input($dbContent) . "', `Subject` = '" . process_db_input($sSubject) . "' WHERE `ID` = '$sTmplID' AND `LangID` = '$sLangID'") == 0) { // doesn't exist
            $this->res("INSERT INTO `sys_email_templates` SET `Name` = '" . process_db_input($aTemplate['Name']) . "', `Subject` = '" . process_db_input($sSubject) . "', `Body` = '" . process_db_input($dbContent) . "', `Desc` = '" . process_db_input($aTemplate['Desc']) . "', `LangID` = '".$sLangID."'");
        }
    }


    /**
     *
     *
     * @param  unknown $isSave (optional)
     * @return unknown
     */
    function getPlaceholders($isSave = false)
    {
        $aPlaceholdersDB = $this->getAll("SELECT * FROM `{$this->sTablePrefix}_placeholders`");

        $aPlaceholders = array();
        foreach ($aPlaceholdersDB as $iID => $sData) {
            $sPlaceholder = $sData['placeholder'];

            if ($isSave) {
                $aPlaceholders['&lt;+'.$sPlaceholder.'+&gt;'] = '<'.$sPlaceholder.'>';
                $aPlaceholders['<+'.$sPlaceholder.'+>'] = '<'.$sPlaceholder.'>';
                $aPlaceholders['&lt;'.$sPlaceholder.'&gt;'] = '<'.$sPlaceholder.'>';
            } else {
                $aPlaceholders['<'.$sPlaceholder.'>'] = '<+'.$sPlaceholder.'+>';
            }
        }

        return $aPlaceholders;
    }


    /**
     *
     *
     * @param unknown $sPlaceholder
     */
    function addPlaceholder($sPlaceholder)
    {
        $this->res("INSERT INTO `{$this->sTablePrefix}_placeholders` SET `placeholder` = '" . process_db_input($sPlaceholder)."'");
    }


    /**
     *
     *
     * @return unknown
     */
    function getLangKeys()
    {
        return $this->getAll("SELECT * FROM `sys_localization_languages`");
    }





    /**
     *
     *
     * @return unknown
     */
    function getDefaultLang()
    {
        $l = $this->getOne("SELECT `VALUE` FROM `sys_options` where `Name`='lang_default'");
        $id = $this->getOne("SELECT `ID` FROM `sys_localization_languages` where `Name`='$l'");

        return $id;
    }

}
