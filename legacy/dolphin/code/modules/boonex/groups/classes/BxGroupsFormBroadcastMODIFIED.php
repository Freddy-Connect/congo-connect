<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

bx_import('BxDolProfileFields');
 
class BxGroupsFormBroadcast extends BxTemplFormView
{
    function BxGroupsFormBroadcast ()
    {

		$sCaptionMsgTitle = _t('_bx_groups_form_caption_broadcast_title');
		$sErrMsgTitle = _t('_bx_groups_form_err_broadcast_title');
		$sCaptionMsgBody = _t('_bx_groups_form_caption_broadcast_message');
		$sErrMsgBory = _t('_bx_groups_form_err_broadcast_message');

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_broadcast',
                'action'   => '',
                'method'   => 'post',
            ),

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
            ),

            'inputs' => array(
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => $sCaptionMsgTitle,
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => $sErrMsgTitle,
                    ),
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ), 
                'message' => array(
                    'type' => 'textarea',
                    'name' => 'message',
                    'caption' => $sCaptionMsgBody,
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(10,64000),
                        'error' => $sErrMsgBory,
                    ),
					'html' => 2,
                    'db' => array (
                        'pass' => 'XssHtml',
                    ),
                ),

                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                ),
            ),
        );

        parent::BxTemplFormView ($aCustomForm);
    }
}
