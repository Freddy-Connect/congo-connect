<?php

/**
* Copyright (c) 2012-2016 Andreas Pachler - http://www.paan-systems.com
* This is a commercial product made by Andreas Pachler and cannot be modified for other than personal usage.
* This product cannot be redistributed for free or a fee without written permission from Andreas Pachler.
* This notice may not be removed from the source code.
*/

bx_import('BxDolModule');

class MPEmailTmplEditorModule extends BxDolModule
{
    /**
     *
     *
     * @param unknown $aModule (reference)
     */
    function MPEmailTmplEditorModule(&$aModule)
    {
        parent::BxDolModule($aModule);

        $this->sHomeUrl = $this->_oConfig->getHomeUrl();
        $this->sHomePath = $this->_oConfig->getHomePath();
        $this->sModuleUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri();
    }

    /**
     *
     */
    function actionAdministration()
    {
        $GLOBALS['iAdminPage'] = 1;

        if (!$GLOBALS['logged']['admin']) {
            $this->_oTemplate->displayAccessDenied ();

            return;
        }

        $aPlaceholder = array();

        bx_import('BxTemplFormView');

        $aFormPlaceholder = array(
            'form_attrs' => array(
                'name' => 'placeholder',
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
                'csrf' => array(
                    'disable' => true,
                ),
            ),
            'inputs' => array(
                'placeholder' => array(
                    'type' => 'text',
                    'name' => 'Placeholder',
                    'caption' => 'Placeholder (without <>)',
                    'value' => isset($aPlaceholder['placeholder']) ? $aPlaceholder['placeholder'] : '',
                    'required' => true,
                ),
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => 'Add',
                    'colspan' => false,
                ),
            ),
        );

        $oFormPlaceholder = new BxTemplFormView($aFormPlaceholder);
        $oFormPlaceholder->initChecker();

        if ($oFormPlaceholder->isSubmittedAndValid()) {
            $sFormPlaceholder = $oFormPlaceholder->getCode();

            $sPlaceholder = $oFormPlaceholder->getCleanValue('placeholder');
            $this->_oDb->addPlaceholder($sPlaceholder);
        } else {
            $sFormPlaceholder = $oFormPlaceholder->getCode();
        }

        $sAdminUrl = $this->sModuleUrl . 'administration/';

        $sTmplID = isset($_GET['TmplID']) ? intval($_GET['TmplID']) : $this->_oDb->getFirstTemplateID();
        $sLangID = isset($_GET['LangID']) ? intval($_GET['LangID']) : 0;
        $sEditorID = isset($_GET['EditorID']) ? intval($_GET['EditorID']) : 0;

        if (isset($_POST['B1']) && $_POST['B1'] == 'Save') {
            if ($sEditorID == 0)
                $sContent = $_POST['textarea_mce'];
            else if ($sEditorID == 1)
                $sContent = $_POST['textarea_html'];

            $sSubject = $_POST['subject'];

            if (!empty($sContent)) {
                $aTmpl = $this->_oDb->getTmplByID($sTmplID);
                $aTemplate = $this->_oDb->getTmplByName($aTmpl['Name'], $sLangID);

                $aTagReplace = $this->_oDb->getPlaceholders(true);
                $sContent = "<bx_include_auto:_email_header.html />\r\n\r\n" . strtr($sContent, $aTagReplace) . "\r\n\r\n<bx_include_auto:_email_footer.html />";

                if (count($aTemplate) > 0) {
                    $this->_oDb->updateTemplate($aTemplate['ID'], $sLangID, $sContent, $sSubject, $aTemplate);
                } else {
                    $this->_oDb->updateTemplate($sTmplID, $sLangID, $sContent, $sSubject, $aTmpl);
                }
            }
        }

        $sSelector = $this->getSelector($sTmplID, $sLangID, $sEditorID);
        $sEditor = $this->getPageEditor($sTmplID, $sLangID, $sEditorID);

        $this->_oTemplate->pageStart();
        echo $this->_oTemplate->adminBlock($sFormPlaceholder, _t('_mp_emailtmpleditor_placeholder'));
        echo $this->_oTemplate->adminBlock($sSelector, _t('_mp_emailtmpleditor_selector'));
        echo $this->_oTemplate->adminBlock($sEditor, _t('_mp_emailtmpleditor_page_content'));
        $this->_oTemplate->addCssAdmin(array('forms_adv.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCodeAdmin (_t('_mp_emailtmpleditor'));
    }

    /**
     *
     *
     * @param  unknown $sTmplID
     * @param  unknown $sLangID
     * @param  unknown $sEditorID
     * @return unknown
     */
    function getSelector($sTmplID, $sLangID, $sEditorID)
    {
        $aTemplates = $this->_oDb->getTemplates();
        foreach ($aTemplates as $iID => $sData) {
            $iTmplID = (int)$sData['ID'];
            $sTmplName = $sData['Desc'];

            if ($iTmplID == $sTmplID) {
                $sTmplSelector .= '<option value="' . $iTmplID . '" selected>' . $sTmplName . '</option>';
            } else {
                $sTmplSelector .= '<option value="' . $iTmplID . '">' . $sTmplName . '</option>';
            }
        }

        if ($sLangID == '0') {
            $sLangSelector .= '<option value="0" selected>default</option>';
        } else {
            $sLangSelector .= '<option value="0">default</option>';
        }
        $lKeys = $this->_oDb->getLangKeys();
        foreach ($lKeys as $iID => $sData) {
            $iLID = (int)$sData['ID'];
            $sLName = $sData['Name'];
            $sLTitle = $sData['Title'];
            $sLFlag = $sData['Flag'];

            if ($iLID == $sLangID) {
                $sLangSelector .= '<option value="' . $iLID . '" selected>' . $sLTitle . '</option>';
            } else {
                $sLangSelector .= '<option value="' . $iLID . '">' . $sLTitle . '</option>';
            }
        }

        if ($sEditorID == '0') {
            $sEditorSelector = '<option value="0" selected>'._t('_mp_emailtmpleditor_tinymce_editor').'</option>';
            $sEditorSelector .= '<option value="1">'._t('_mp_emailtmpleditor_html_editor').'</option>';
        } else {
            $sEditorSelector = '<option value="0">'._t('_mp_emailtmpleditor_tinymce_editor').'</option>';
            $sEditorSelector .= '<option value="1" selected>'._t('_mp_emailtmpleditor_html_editor').'</option>';
        }

        $aVars = array(
            'tmpl_selector' => $sTmplSelector,
            'lang_selector' => $sLangSelector,
            'editor_selector' => $sEditorSelector,
            'admin_url' => $this->sModuleUrl . 'administration/',
        );
        $sCode = $this->_oTemplate->parseHtmlByName('selector.html', $aVars);

        return $sCode;
    }

    /**
     *
     *
     * @param  unknown $sAction
     * @param  unknown $sTmplID
     * @param  unknown $sLangID
     * @param  unknown $sEditorID
     * @return unknown
     */
    function getPageEditor($sTmplID, $sLangID, $sEditorID)
    {
        $aTmpl = $this->_oDb->getTmplByID($sTmplID, $sLangID);
        $aTemplate = $this->_oDb->getTmplByName($aTmpl['Name'], $sLangID);

            $aTagReplace = $this->_oDb->getPlaceholders(false);
            $sContent = $aTemplate['Body'] ? $aTemplate['Body'] : $aTmpl['Body'];

            $sContent = str_replace("<bx_include_auto:_email_header.html />", "", $sContent);
            $sContent = str_replace("<bx_include_auto:_email_footer.html />", "", $sContent);
            $sContent = strtr($sContent, $aTagReplace);

            bx_import('BxDolEditor');
            $oEditor = BxDolEditor::getObjectInstance();

            $aVars = array(
                'form_action' => $this->sModuleUrl . 'administration/?TmplID=' . $sTmplID . '&LangID=' . $sLangID . '&EditorID=' . $sEditorID,
                'subject' => $aTemplate['Subject'] ? $aTemplate['Subject'] : $aTmpl['Subject'],
                'bx_if:is_tinymce' => array(
                    'condition' => $sEditorID == 0,
                    'content' => array(
                        'text' => $sContent,
                        'tinymce_js' => isset($oEditor) ? $oEditor->attachEditor('#editor_form [name="textarea_mce"]', BX_EDITOR_FULL) : '',
                    )
                ),
                'bx_if:is_html' => array(
                    'condition' => $sEditorID == 1,
                    'content' => array('text' => $sContent)
                ),
            );
            $sCode = $this->_oTemplate->parseHtmlByName('editor.html', $aVars);

            return $sCode;
    }

}
