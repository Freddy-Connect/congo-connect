<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

bx_import('BxDolTwigPageView');

class BxGroupsPageViewPrivate extends BxDolTwigPageView
{
    function BxGroupsPageViewPrivate(&$oMain, &$aDataEntry)
    {   
        parent::BxDolTwigPageView('bx_groups_view', $oMain, $aDataEntry); 
    }

    function getBlockCode_Echo( $iBlockID, $sContent )
    {
		return '';
	}

    function getBlockCode_XML( $iBlockID, $sContent )
    {
		return '';
	}

    function getBlockCode_PHP( $iBlockID, $sContent )
    {
		return '';
	}

    function getBlockCode_RSS( $iBlockID, $sContent )
    {
		return '';
	}
 
	function getBlockCode_ForumFeed()
    {
		return '';
	}

    function getBlockCode_SocialSharing()
    {
		return '';
	}

    function getBlockCode_Desc()
    {
		$isFanPending = $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 0);
		$sMsg = ($isFanPending) ? '_bx_groups_msg_private_join_pending' : '_bx_groups_msg_private_join';
        
		return array( MsgBox(_t($sMsg)) );
    }
  
    function getBlockCode_Actions()
    {
		if(!$this->_oMain->_iProfileId)
			return array( MsgBox(_t('_bx_groups_login', BX_DOL_URL_ROOT . 'member.php')) );

		$isFan = $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 0) || $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 1);

		$aInfo = array (
			'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
			'iViewer' => $this->_oMain->_iProfileId,
			'ownerID' => (int)$this->aDataEntry['author_id'],
			'ID' => (int)$this->aDataEntry['id'],
			'URI' => $this->aDataEntry['uri'], 
			'TitleJoin' => $this->_oMain->isAllowedJoin($this->aDataEntry) ? ($isFan ? _t('_bx_groups_action_title_leave') : _t('_bx_groups_action_title_join')) : '',
			'IconJoin' => $isFan ? 'signout' : 'signin',

		);

		if (!$aInfo['TitleJoin'])
			return '';

		return $this->genObjectsActions($aInfo, 'bx_groups');
	 
    }
  

    /**
     * @description : function will generate object's action lists;
     * @param : $aKeys        (array)  - array with all nedded keys;
     * @param : $sActionsType (string) - type of actions;
     * @param : $iDivider     (integer) - number of column;
     * @return:  HTML presentation data;
    */
    function genObjectsActions( &$aKeys,  $sActionsType, $bSubMenuMode = false, $sTemplateIndex = 'actions', $sTemplateIndexActionLink = 'action' )
    {

		global $oFunctions;
 
        // ** init some needed variables ;
        $sActionsList 	= null;
        $sResponceBlock = null;

        $aUsedTemplate	= array (
            'actions_submenu' => 'member_actions_list_submenu.html',
            'actions' => 'member_actions_list.html',
            'ajaxy_popup' => 'ajaxy_popup_result.html',
        );
  
		$sQuery  = 	"
			SELECT
				`Caption`, `Icon`, `Url`, `Script`, `Eval`, `bDisplayInSubMenuHeader`
			FROM
				`sys_objects_actions`
			WHERE
				`Type` = '{$sActionsType}'
				AND `Caption` = '{TitleJoin}'
			ORDER BY
				`Order`
		";

		$rResult = db_res($sQuery);
		while ( $aRow = mysql_fetch_assoc($rResult) ) {
			$aActions[$sActionsType][] = $aRow;
		}
 

        // ** generate actions block ;

        // contain all systems actions that will procces by self function ;
        $aCustomActions = array();
        if ( is_array($aActions[$sActionsType]) and !empty($aActions[$sActionsType]) ) {

            // need for table's divider ;
            $iDivider = $iIndex = 0;
            foreach( $aActions[$sActionsType] as  $aRow ) {
                if ($bSubMenuMode && $aRow['bDisplayInSubMenuHeader']==0) continue;

                $sOpenTag = $sCloseTag = null;

                // generate action's link ;
                $sActionLink = $oFunctions -> genActionLink( $aKeys, $aRow, 'menuLink', $sTemplateIndexActionLink );

                if ( $sActionLink ) {
                    $iDivider = $iIndex % 2;

                    if ( !$iDivider ) {
                        $sOpenTag = '<tr>';
                    }

                    if ( $iDivider ) {
                        $sCloseTag = '</tr>';
                    }

                    $aActionsItem[] = array (
                        'open_tag'    => $sOpenTag,
                        'action_link' => $sActionLink,
                        'close_tag'   => $sCloseTag,
                    );

                    $iIndex++;
                }

                // it's system action ;
                if ( !$aRow['Url'] && !$aRow['Script'] ) {
                    $aCustomActions[] =  array (
                        'caption'   => $aRow['Caption'],
                        'code'      => $aRow['Eval'],
                    );
                }
            }
        }

        if ($iIndex % 2 == 1) { //fix for ODD menu elements count
            $aActionsItem[] = array (
                'open_tag'    => '',
                'action_link' => '',
                'close_tag'   => ''
            );
        }

        if ( !empty($aActionsItem) ) {

            // check what response window use ;
            // is there any value to having this template even if the ID is empty?
            if (!empty($aKeys['ID'])) {
                $sResponceBlock = $GLOBALS['oSysTemplate'] -> parseHtmlByName( $aUsedTemplate['ajaxy_popup'], array('object_id' => $aKeys['ID']) );
            }

            $aTemplateKeys = array (
                'bx_repeat:actions' => $aActionsItem,
                'responce_block'    => $sResponceBlock,
            );

            $sActionsList = $GLOBALS['oSysTemplate'] -> parseHtmlByName( $aUsedTemplate[$sTemplateIndex], $aTemplateKeys );
        }

        //procces all the custom actions ;
        if ($aCustomActions) {
            foreach($aCustomActions as $iIndex => $sValue ) {
                $sActionsList .= eval( $oFunctions -> markerReplace($aKeys, $aCustomActions[$iIndex]['code']) );
            }
        }

        return $sActionsList;
    }


    function getCode()
    { 
        return parent::getCode();
    }

}
