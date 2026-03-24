<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolProfileFields');

class BxJobsFormApply extends BxTemplFormView {

    function BxJobsFormApply ($oMain, $aDataEntry, $iProfileId) {
 
		/*
		if(getParam("modzzz_resume_job_connect")=='on'){ 
			$aResume = $oMain->_oDb->getResumeList($iProfileId);   
		}else{
			$aResume = array();
		}
		*/

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_apply',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(
            
               /*  Freddy commentaire
			    'message' => array(
                    'type' => 'textarea',
                    'name' => 'message',
                    'caption' => _t('_modzzz_jobs_form_caption_cover_letter'), 
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(10,64000),
                        'error' => _t('_modzzz_jobs_form_err_cover_letter'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),                
 
             
			    'resume_id' => array(
                    'type' => 'select',
                    'name' => 'resume_id',
                    'caption' => _t('_modzzz_jobs_form_caption_select_resume'),
                    'info' => _t('_modzzz_jobs_form_info_select_resume'),
                    'required' => false, 
                    'values' => $aResume,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),  

				'resume' => array(
					'type' => 'custom',
					'name' => "resume",
					'caption' => _t('_modzzz_jobs_form_caption_resume'), 
					'content' =>  "<input type=file  name='resume'>",  
				), 
				*/

                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_Submit'),
                    'colspan' => false,
                ),
            ),            
        );

		/*
		if(count($aResume)<=1)
            unset ($aCustomForm['inputs']['resume_id']);
			*/


        parent::BxTemplFormView ($aCustomForm);
    }
}
