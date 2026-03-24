<?php
/***************************************************************************
*
*     copyright            : (C) 2017 AQB Soft
*     website              : http://www.aqbsoft.com
*
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
*
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
*
* This notice may not be removed from the source code.
*
***************************************************************************/

bx_import('BxDolModuleTemplate');
bx_import('BxTemplFormView');
bx_import('BxDolProfileFields');


class AqbPYMLTemplate extends BxDolModuleTemplate {
	/**
	 * Constructor
	 */
	function __construct(&$oConfig, &$oDb) {
	    parent::__construct($oConfig, $oDb);
	}

    function fieldsManager() {
        $aFields = $this->_oDb->getFields();
        $aOptions = $this->_oDb->getPossibleFields();
        $aOptionsCopy = $aOptions;


        $aFieldsTmp = array();
        if ($aFields)
        foreach ($aFields as $aField) {
            foreach ($aOptions as $i => $aOption) {
                $aOptions[$i]['selected'] = $aOption['ID'] == $aField['ID'] ? 'selected="selected"' : '';
            }

            $aFieldsTmp[] = array(
                'field' => $aField['ID'],
                'bx_repeat:fieldsoptions' => $aOptions,
                'match_checked' => $aField['Match'] == 'MATCH' ? 'checked="checked"' : '',
                'dontmatch_checked' => $aField['Match'] != 'MATCH' ? 'checked="checked"' : '',
            );
        }

        return $this->parseHtmlByName('fields_manager.html', array(
            'bx_repeat:fieldsoptions_template' => $aOptionsCopy,
            'bx_repeat:fields' => $aFieldsTmp,
        ));
    }

    function showMatches($iProfile, $sPageUrl) {
       
	     //--- Freddy AQB Soft: Befriend
		$iUserId = getLoggedId();
		$aFriends = getMyFriendsEx($iUserId);
		$aFriendsIds = array_keys($aFriends);
	   
	    $iTotal = $this->_oDb->getProfilesCount($iProfile);
        if (!$iTotal) return '';

        $this->addCss('main.css');

        $iPage = intval($_GET['aqbpage']);
        if ($iPage < 1) $iPage = 1;
        $iPerPage = $this->_oDb->getParam('aqb_pyml_perpage');
        $iStartFrom = ($iPage - 1) * $iPerPage;

        $iSeed = $_GET['seed'] ? intval($_GET['seed']) : rand();
        $aProfiles = $this->_oDb->getProfiles($iProfile, $iStartFrom, $iPerPage, $iSeed);

        $aFields = $this->_oDb->getFields();

        $oProfileFields = new BxDolProfileFields(0);

        $aProfilesTmpl = array();
        foreach ($aProfiles as $iProfile) {
            $aProfile = getProfileInfo($iProfile);
            $aFieldsValues = array();
            foreach ($aFields as $aField) {
                if (!$aProfile[$aField['Name']]) continue;
                $aFieldsValues[] = array(
                    'field_name' => _t('_FieldCaption_'.$aField['Name'].'_View'),
                    'field_value' => $oProfileFields->getViewableValue($aField, $aProfile[$aField['Name']]),
                );
            }

             $aProfilesTmpl[] = array(
               /*freddy modif
			    'thumb' => get_member_thumbnail($iProfile, 'none', true),
                'bx_repeat:fields' => $aFieldsValues,
				*/
				
				///Freddt AQB
				'bx_if:befriend' => array(
					'condition' => !in_array($iProfile, $aFriendsIds) && !isFriendRequest($iUserId, $iProfile),
					'content' => array(
						'id' => $iProfile,
						
						////
				 'thumb' => get_member_thumbnail($iProfile, 'none', true),
                 'bx_repeat:fields' => $aFieldsValues,

					)
				)
				////////////////////////////////////
            );

        }
		//--- AQB Soft: Befriend
        $sPagination = '';
        if ($iTotal > $iPerPage) {
            $oPaginate = new BxDolPaginate(array(
                'count' => $iTotal,
                'per_page' => $iPerPage,
                'page' => $iPage,
                'per_page_changer' => false,
                'page_reloader' => false,
                'on_change_page' => 'return !loadDynamicBlock({id}, \''.$sPageUrl."?aqbpage={page}&seed={$iSeed}');",
            ));
            $sPagination = $oPaginate->getPaginate();
        }

        $sCont = $this->parseHtmlByName('profiles.html', array(
            'bx_repeat:profiles' => $aProfilesTmpl,
        ));

        return array($sCont, NULL, $sPagination);
    }
}
