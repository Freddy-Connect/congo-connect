<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Notify
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

bx_import('BxDolTwigPageMain');
bx_import('BxTemplCategories');

class BxNotifyPageMain extends BxDolTwigPageMain {

    function BxNotifyPageMain(&$oMain) {

		$this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
        $this->oMain = $oMain;

        $this->sSearchResultClassName = 'BxNotifySearchResult';
        $this->sFilterName = 'modzzz_notify_filter';
 
		parent::BxDolTwigPageMain('modzzz_notify_main', $oMain);
		 
	}
   
    function getBlockCode_Settings() {
	 
		$sSetting = $this->_oDb->getNotificationSetting($this->oMain->_iProfileId);
 
		$aResult = array(
			'none_selected'=>'',
			'immediately_selected'=>'',
			'daily_selected'=>'',
			'weekly_selected'=>'',
			'monthly_selected'=>'',  
			'action_url'=> BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'settings', 
		);

		switch($sSetting){
			case '':
			case 'none':
  				$aResult['none_selected'] = 'checked="checked"';   
			break;
			case 'immediately':
  				$aResult['immediately_selected'] = 'checked="checked"';   
			break;
			case 'daily':
  				$aResult['daily_selected'] = 'checked="checked"'; 
			break;
			case 'weekly':
  				$aResult['weekly_selected'] = 'checked="checked"';
			break;
			case 'monthly':
  				$aResult['monthly_selected'] = 'checked="checked"';
			break;
		} 
  
	    return $this->oTemplate->parseHtmlByName('settings', $aResult);   
	}

    function getBlockCode_Desc() {
 
 		$sHeadSettingC = _t("_modzzz_notify_caption_existing_settings"); 
		$sHeadActionsC = _t("_modzzz_notify_actions"); 
		$sFriendC = _t("_modzzz_notify_friend");
		$sFavoritesC = _t("_modzzz_notify_favorites"); 
		$sBothC = _t("_modzzz_notify_both");  
		$sNoneC = _t("_modzzz_notify_none");

		if(isset($_REQUEST['submit_form'])) { 
			$this->oDb->saveMemberSettings($this->oMain->_iProfileId);
		}

 		$arrActions = $this->oDb->getNotifyActions(true, 'friend');
		$arrSettings = $this->oDb->getMemberNotifySettings($this->oMain->_iProfileId);
  
        $aForm = array(
            'form_attrs' => array(
                'action' => '',
                'method' => 'post',
                'name'     => 'submit_form', 
            ), 
		);
 	
		$iter=1;
		$sOldGroup = "";
		foreach($arrActions as $aEachAction)
		{   
			if($aEachAction['unit']!='profile'){ 
				list($sPrefix, $sUnit) = explode('_', $aEachAction['unit']);
				$oModuleDb = new BxDolModuleDb();
				if(!$oModuleDb->isModule($sUnit)) continue;
			}

			$iId = $aEachAction['id'];
			$sNewGroup = _t($aEachAction['group']);

			if(is_array($arrSettings[$iId]) && count($arrSettings[$iId]))
				$sAccess = $arrSettings[$iId]['Access'];
			else
				$sAccess = "none";

			$sFriendChecked = "";
			$sFavoritesChecked = "";
			$sBothChecked = "";
			$sNoneChecked = ""; 
			switch($sAccess){
				case "friends":
					$sFriendChecked = "checked='checked'";
				break;
				case "favorites":
					$sFavoritesChecked = "checked='checked'";
				break;
				case "all":
					$sBothChecked = "checked='checked'";
				break;
				case "none":
					$sNoneChecked = "checked='checked'";
				break;
			}

			if($sOldGroup != $sNewGroup) {
				
				if($sOldGroup != "") { 
					$aForm['inputs']["header{$iter}_end"] = array(
						'type' => 'block_end'
					);
				}

				$aForm['inputs']["header{$iter}"] = array(
					'type' => 'block_header',
					'caption' => "<b>{$sNewGroup}</b>",
					'collapsable' => true,
					'collapsed' => ($iter==1) ? false : true, 

				);

				 $aForm['inputs']["ItemHead{$iter}"] = array(
					'type' => 'custom',
					'name' => "ItemHead{$iter}",
					'content' =>  "<div style='width:100%'><div style='float:left;width:50%'>
						<!-- freddy commentaire <b>{$sHeadActionsC}</b> -->
					</div><div style='float:left;width:45%'>
					<!-- freddy commentaire<b>{$sHeadSettingC}</b>-->
					</div></div><div class='clear_both'></div>",  
					'colspan' => true
				);
 
			}
			
			$sActionC = _t($aEachAction['desc']);
  
			 $aForm['inputs']["Item{$iter}"] = array(
				'type' => 'custom',
				'name' => "Item{$iter}",
				'content' =>  "<div style='width:100%'>
								   <div style='float:left;width:50%'>{$sActionC}</div>
								   <div style='float:left;width:45%;'>
										
										<!-- Freddy commentaire
										<input type=radio name=notify[$iId] value='friends' {$sFriendChecked}> {$sFriendC} &nbsp;
										<input type=radio name=notify[$iId] value='favorites' {$sFavoritesChecked}> {$sFavoritesC} &nbsp;
										-->
										
										<input type=radio name=notify[$iId] value='all' {$sBothChecked}> {$sBothC} &nbsp;
										<input type=radio name=notify[$iId] value='none' {$sNoneChecked}> {$sNoneC} &nbsp;
								   </div>
							  </div>
							  <div class='clear_both'></div>",  
				'colspan' => true
			);

			$sOldGroup = $sNewGroup;
			$iter++;

		}//END

		$aForm['inputs']["header{$iter}_end"] = array(
			'type' => 'block_end'
		);
 
 		$aForm['inputs']["submit_button"] = array(
			'type' => 'submit',
			'name' => 'submit_form',
			'value' => _t('_Submit_Notify'),
			'colspan' => false,
		);
  
		$oForm = new BxTemplFormView($aForm);
		$sCode = '<div class="bx-def-bc-padding">' . $oForm->getCode() . '</div>'; 
  
		return $sCode;  
	}

 
	function getNotifyMain() {
        return BxDolModule::getInstance('BxNotifyModule');
    }
  

}
