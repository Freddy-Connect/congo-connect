<?php
/***************************************************************************
* Date				: Monday October 4, 2010
* Copywrite			: (c) 2010 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Site Stat Manager
* Product Version	: 1.1
*
* IMPORTANT: This is a commercial product made by Dean Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean Bassett Jr.
*
***************************************************************************/

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolModule.php');

class BxSiteStatManagerModule
    extends BxDolModule
    {

	    function BxSiteStatManagerModule(&$aModule) {
			parent::BxDolModule($aModule);
		}

	    function actionAdministration ($sCommand = '',$iID = '') {
	        global $_page, $_page_cont;
	        require_once(BX_DIRECTORY_PATH_INC . 'admin_design.inc.php');
	        $logged['admin'] = member_auth(1, true, true);
	        $iNameIndex=9;
	        $_page=array
	            (
	            'name_index' => $iNameIndex,
	            'css_name' => array('forms_adv.css'),
				'js_name' => array(BX_DOL_URL_ROOT . 'plugins/jquery/jquery.ui.all.min.js'),
	            'header' => _t('_dbcs_SM_SiteStatManagerHeader'),
	            'header_text' => _t('_dbcs_SM_SiteStatManagerHeaderText')
	            );

			//echo $sCommand;
			//echo $iID;
			//exit;
			$sMsg = '';			
			
			if($sCommand == 'update') {
				$sName = $_POST['SiteStatManagerName'];
				$iBlockLocation = $this->_oDb->findBlock($iID,$sName);
				if ($iBlockLocation == 'active') {
				
					if(isset($_POST['B1'])) {
						// save the post data.
						$sName = $this->_oDb->escape($_POST['SiteStatManagerName']);
						$sTitle = $this->_oDb->escape($_POST['SiteStatManagerTitle']);
						$sUserLink = $this->_oDb->escape($_POST['SiteStatManagerUserLink']);
						$sUserQuery = $this->_oDb->escape($_POST['SiteStatManagerUserQuery']);
						$sAdminLink = $this->_oDb->escape($_POST['SiteStatManagerAdminLink']);
						$sAdminQuery = $this->_oDb->escape($_POST['SiteStatManagerAdminQuery']);
						$sIconName = $this->_oDb->escape($_POST['SiteStatManagerIconName']);
						$sQuery = "UPDATE `sys_stat_site` SET `Name`='$sName',`Title`='$sTitle',`UserLink`='$sUserLink',`UserQuery`='$sUserQuery',`AdminLink`='$sAdminLink',`AdminQuery`='$sAdminQuery',`IconName`='$sIconName' WHERE `ID`='$iID'";
						$this->_oDb->doQuery($sQuery);
						// now clear the cache. 
						$this->clearDbCache();
						$sMsg = _t('_dbcs_SM_SiteStatManagerSaved');
					}
					if(isset($_POST['B2'])) {
						// delete the item.
						$this->_oDb->deleteStatBlock($iID);
						// now clear the cache. 
						$this->clearDbCache();
						$sMsg = _t('_dbcs_SM_SiteStatManagerDeleted');
					}
				} else {
					$sMsg = _t('_dbcs_SM_SiteStatManagerNotActive');
				}
			}
			if($sCommand == 'savenew') {
				if(isset($_POST['B1'])) {
					// save the post data.
					$sName = $this->_oDb->escape($_POST['SiteStatManagerName']);
					$sTitle = $this->_oDb->escape($_POST['SiteStatManagerTitle']);
					$sUserLink = $this->_oDb->escape($_POST['SiteStatManagerUserLink']);
					$sUserQuery = $this->_oDb->escape($_POST['SiteStatManagerUserQuery']);
					$sAdminLink = $this->_oDb->escape($_POST['SiteStatManagerAdminLink']);
					$sAdminQuery = $this->_oDb->escape($_POST['SiteStatManagerAdminQuery']);
					$sIconName = $this->_oDb->escape($_POST['SiteStatManagerIconName']);
					$iStatOrder = $this->_oDb->getLastBlockOrder()+1;
					$sQuery = "INSERT INTO `sys_stat_site` SET `Name`='$sName',`Title`='$sTitle',`UserLink`='$sUserLink',`UserQuery`='$sUserQuery',`AdminLink`='$sAdminLink',`AdminQuery`='$sAdminQuery',`IconName`='$sIconName',`StatOrder`='$iStatOrder'";
					$this->_oDb->doQuery($sQuery);
					// now clear the cache. 
					$this->clearDbCache();
					$sMsg = _t('_dbcs_SM_SiteStatManagerSaved');
				}
			}
	        $_page_cont[$iNameIndex]['page_main_code'] .= $this->DeanoMainCode($sMsg);
	        PageCodeAdmin();
		}  
		
	    function actionSaveOrder() {
	        $sAction = BX_DOL_URL_ROOT . 'modules/?r=site_stat_manager/administration/';
			// First we need to see if a item has been dragged from active to inactive or vice versa, and if so, move it
			// from sys_stat_site to dbcs_sys_stat_site
			$sActive = $_POST['ActiveList'];
			$sActive = str_replace("ID[]=","",$sActive);
			$aActive = explode("&", $sActive);

			$sInActive = $_POST['InactiveList'];
			$sInActive = str_replace("ID[]=","",$sInActive);
			$aInActive = explode("&", $sInActive);

			$aActiveStats = $this->_oDb->getStats();
			$aInactiveStats = $this->_oDb->getInActive();

			// first loop through the active stats.
			foreach ($aActiveStats as $iID => $aData) {
				$iBlockID = (int)$aData['ID'];
				// see if this block id is found in the passed inactive data.
				foreach ($aInActive as $iID) {
					if ($iBlockID == $iID) {
						// a match has been found. Move active stat to the inactive table.
						$this->_oDb->moveActive($iBlockID);
					}
				}
			}
			// now loop through the inactive stats.
			foreach ($aInactiveStats as $iID => $aData) {
				$iBlockID = (int)$aData['ID'];
				// see if this block id is found in the passed active data.
				foreach ($aActive as $iID) {
					if ($iBlockID == $iID) {
						// a match has been found. Move inactive stat to the active table.
						$this->_oDb->moveInActive($iBlockID);
					}
				}
			}

			// Now we apply the order to both tables as passed.
			$iCount = 1;
			foreach ($aActive as $iID) {
				$this->_oDb->orderActive($iID, $iCount);
				$iCount++;
			}
			$iCount = 1;
			foreach ($aInActive as $iID) {
				$this->_oDb->orderInActive($iID, $iCount);
				$iCount++;
			}

			// now clear the cache. 
			$this->clearDbCache();
		}

	
		function DeanoMainCode($sMsg)
		    {
			$sExistedC = _t('_dbcs_SM_SiteStatManagerBoxHeader');
	        $sCss = $this->_oTemplate->addCss('unit.css', true);
			//$sJs = $this->_oTemplate->addJs('dbcsfunctions.js', true);
	        $sAction = BX_DOL_URL_ROOT . 'modules/?r=site_stat_manager/administration/';
	        $sNew = BX_DOL_URL_ROOT . 'modules/?r=site_stat_manager/new/';
	        $sEdit = BX_DOL_URL_ROOT . 'modules/?r=site_stat_manager/edit/';


			$sCode = '
	<script type="text/javascript">
	$(function() {
		$("#sortable, #sortable2").sortable({
			connectWith: \'.container\',
			stop: function() { 
				var result1 = $("#sortable").sortable(\'serialize\');
				var result2 = $("#sortable2").sortable(\'serialize\');
				$.post("?r=site_stat_manager/saveorder/", { 
					ActiveList: result1,
					InactiveList: result2
				})
			}
		});
		$("#sortable, #sortable2").disableSelection();
	});
	</script>

				';
			if($sMsg != '') {
				$sCode .= '	<div id="quotes_box">' . MsgBox($sMsg,4) . '</div>';
				$sCode .= '	<div class="clear_both"></div>';
			}

			$sCode .= '<div class="dbContent bx-def-bc-margin">';

			$sCode .= '<div class="dbcsheading1">' . _t('_dbcs_SM_SiteStatManagerHead1') . '</div>';
			$sCode .= '<table class="dbcstable" cellspacing="0" cellpadding="0"><tr><td>';
			$sCode .= '<div id="sortable" class="container">';
			$aStats = $this->_oDb->getStats();
			foreach ($aStats as $iID => $aData) {
				$iBlockID = (int)$aData['ID'];
				$sTitle = $aData['Title'];
				$sCode .= '<div id="ID_' . $iBlockID . '" class="ui-state-default dbcsblock" style="background-image: url(\'' . BX_DOL_URL_ADMIN . 'templates/base/images/block_header.gif\')">';
				$sCode .= '<div class="dbcsblocktext"><a href="' . $sEdit . $iBlockID . '">' . _t('_' . $sTitle) . '</a></div>';
				$sCode .= '</div>';
			}
			$sCode .= '</div>';
			$sCode .= '<div class="clear_both"></div>';
			$sCode .= '</td></tr></table>';
			$sCode .= '<div class="dbcsheading2">' . _t('_dbcs_SM_SiteStatManagerHead2') . '</div>';
			$sCode .= '<table class="dbcstable" cellspacing="0" cellpadding="0"><tr><td>';
			$sCode .= '<div id="sortable2" class="container">';

			$aStats = $this->_oDb->getInActive();
			foreach ($aStats as $iID => $aData) {
				$iBlockID = (int)$aData['ID'];
				$sTitle = $aData['Title'];
				$sCode .= '<div id="ID_' . $iBlockID . '" class="ui-state-default dbcsblock" style="background-image: url(\'' . BX_DOL_URL_ADMIN . 'templates/base/images/block_header.gif\')">';
				$sCode .= '<div class="dbcsblocktext"><a href="' . $sEdit . $iBlockID . '">' . _t('_' . $sTitle) . '</a></div>';
				$sCode .= '</div>';
			}


			$sCode .= '</div>';
			$sCode .= '<div class="clear_both"></div>';
			$sCode .= '</td></tr></table>';

			$sCode .= '</div>';



			bx_import('BxDolPageView');
			if($sDoAction != '') {
		        $sActions = BxDolPageView::getBlockCaptionMenu(mktime(), array(
		            'add_unit' => array('href' => $sAction, 'title' => _t('_dbcs_SM_SiteStatManagerGoBack'), 'onclick' => '', 'active' => 0),
		        ));
			} else {
		        $sActions = BxDolPageView::getBlockCaptionMenu(mktime(), array(
		            'add_unit' => array('href' => $sNew, 'title' => _t('_dbcs_SM_SiteStatManagerNew'), 'onclick' => '', 'active' => 0),
		        ));

			}
            return DesignBoxContent($sExistedC, $sCss . $sJs . $sCode, 1, $sActions);

        }

		function clearDbCache() {
			// Clear for dolphin 7.0.3
			$files = glob(BX_DIRECTORY_PATH_CACHE . 'db_*.php');
			array_map('unlink', $files);
			// Clear for dolphin versions below 7.0.3
			$sFileName = BX_DIRECTORY_PATH_CACHE . 'sys_stat_site.inc';
			if (file_exists($sFileName)) unlink($sFileName);
			}

	    function actionEdit($iID) {
	        global $_page, $_page_cont;
	        require_once(BX_DIRECTORY_PATH_INC . 'admin_design.inc.php');
	        $logged['admin'] = member_auth(1, true, true);
	        $iNameIndex=9;
	        $_page=array
	            (
	            'name_index' => $iNameIndex,
	            'css_name' => array('forms_adv.css'),
				'js_name' => array(BX_DOL_URL_ROOT . 'plugins/jquery/ui.core.js',BX_DOL_URL_ROOT . 'plugins/jquery/ui.sortable.js'),
	            'header' => _t('_dbcs_SM_SiteStatManagerHeader'),
	            'header_text' => _t('_dbcs_SM_SiteStatManagerHeaderText')
	            );


			$sExistedC = _t('_dbcs_SM_SiteStatManagerBoxHeader');
	        $sCss = $this->_oTemplate->addCss('unit.css', true);
	        $sAction = BX_DOL_URL_ROOT . 'modules/?r=site_stat_manager/administration/';
	        $sNew = BX_DOL_URL_ROOT . 'modules/?r=site_stat_manager/new/';
	        $sEdit = BX_DOL_URL_ROOT . 'modules/?r=site_stat_manager/edit/';
			$sCode = '';
			$aStatData = $this->_oDb->getStatBlock($iID);

			if(!$aStatData) {
				$sLoc = $sAction . 'update/' . $iID;
				header('Location: ' . $sLoc);
			}
/* ----------------------------------------------------------------------------------------------------------------- */
			$sCode .= '<div class="dbContent bx-def-bc-margin">



<form method="POST" action="?r=site_stat_manager/administration/update/' . $iID . '">
  <table cellspacing="0" cellpadding="0" class="form_advanced_table">
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerName') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
          <input type="text" name="SiteStatManagerName" value="' . $aStatData['Name'] . '" class="form_input_text bx-def-font">
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerTitle') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
          <input type="text" name="SiteStatManagerTitle" value="' . $aStatData['Title'] . '" class="form_input_text bx-def-font">
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerUserLink') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
          <input type="text" name="SiteStatManagerUserLink" value="' . $aStatData['UserLink'] . '" class="form_input_text bx-def-font">
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerUserQuery') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_textarea bx-def-round-corners-with-border" style="width: 100%">
          <textarea name="SiteStatManagerUserQuery" class="form_input_textarea bx-def-font">' . $aStatData['UserQuery'] . '</textarea>
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerAdminLink') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
          <input type="text" name="SiteStatManagerAdminLink" value="' . $aStatData['AdminLink'] . '" class="form_input_text bx-def-font">
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerAdminQuery') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_textarea bx-def-round-corners-with-border" style="width: 100%">
          <textarea name="SiteStatManagerAdminQuery" class="form_input_textarea bx-def-font">' . $aStatData['AdminQuery'] . '</textarea>
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerIconName') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
          <input type="text" name="SiteStatManagerIconName" value="' . $aStatData['IconName'] . '" class="form_input_text bx-def-font">
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption"></td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_submit ">
          <div class="button_wrapper">
            <input type="submit" value="' . _t('_dbcs_SM_SiteStatManagerSave') . '" name="B1" class="form_input_submit bx-btn">
            <input type="submit" value="' . _t('_dbcs_SM_SiteStatManagerDelete') . '" name="B2" class="form_input_submit bx-btn" style="padding-left: 10px;">
          </div>
        </div>
        <div class="clear_both"></div></td>
    </tr>
  </table>
</form>





</div>
			';
/* ----------------------------------------------------------------------------------------------------------------- */







			bx_import('BxDolPageView');
	        $sActions = BxDolPageView::getBlockCaptionMenu(mktime(), array(
	            'add_unit' => array('href' => $sAction, 'title' => _t('_dbcs_SM_SiteStatManagerGoBack'), 'onclick' => '', 'active' => 0),
	        ));
			$_page_cont[$iNameIndex]['page_main_code'] .= DesignBoxContent($sExistedC, $sCss . $sCode, 1, $sActions);
	        PageCodeAdmin();
		}
	    function actionNew() {
	        global $_page, $_page_cont;
	        require_once(BX_DIRECTORY_PATH_INC . 'admin_design.inc.php');
	        $logged['admin'] = member_auth(1, true, true);
	        $iNameIndex=9;
	        $_page=array
	            (
	            'name_index' => $iNameIndex,
	            'css_name' => array('forms_adv.css'),
				'js_name' => array(BX_DOL_URL_ROOT . 'plugins/jquery/ui.core.js',BX_DOL_URL_ROOT . 'plugins/jquery/ui.sortable.js'),
	            'header' => _t('_dbcs_SM_SiteStatManagerHeader'),
	            'header_text' => _t('_dbcs_SM_SiteStatManagerHeaderText')
	            );


			$sExistedC = _t('_dbcs_SM_SiteStatManagerBoxHeader');
	        $sCss = $this->_oTemplate->addCss('unit.css', true);
	        $sAction = BX_DOL_URL_ROOT . 'modules/?r=site_stat_manager/administration/';
	        $sNew = BX_DOL_URL_ROOT . 'modules/?r=site_stat_manager/new/';
	        $sEdit = BX_DOL_URL_ROOT . 'modules/?r=site_stat_manager/edit/';
			$sCode = '';


/* ----------------------------------------------------------------------------------------------------------------- */
			$sCode .= '<div class="dbContent bx-def-bc-margin">


<form method="POST" action="?r=site_stat_manager/administration/savenew/">
  <table cellspacing="0" cellpadding="0" class="form_advanced_table">
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerName') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
          <input type="text" name="SiteStatManagerName" class="form_input_text bx-def-font">
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerTitle') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
          <input type="text" name="SiteStatManagerTitle" class="form_input_text bx-def-font">
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerUserLink') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
          <input type="text" name="SiteStatManagerUserLink" class="form_input_text bx-def-font">
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerUserQuery') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_textarea bx-def-round-corners-with-border" style="width: 100%">
          <textarea name="SiteStatManagerUserQuery" class="form_input_textarea bx-def-font"></textarea>
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerAdminLink') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
          <input type="text" name="SiteStatManagerAdminLink" class="form_input_text bx-def-font">
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerAdminQuery') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_textarea bx-def-round-corners-with-border" style="width: 100%">
          <textarea name="SiteStatManagerAdminQuery" class="form_input_textarea bx-def-font"></textarea>
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption">' . _t('_dbcs_SM_SiteStatManagerIconName') . '</td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border">
          <input type="text" name="SiteStatManagerIconName" class="form_input_text bx-def-font">
        </div>
        <div class="clear_both"></div></td>
    </tr>
    <tr>
      <td class="caption"></td>
      <td class="value"><div class="clear_both"></div>
        <div class="input_wrapper input_wrapper_submit ">
          <div class="button_wrapper">
            <input type="submit" value="' . _t('_dbcs_SM_SiteStatManagerSave') . '" name="B1" class="form_input_submit bx-btn">
          </div>
        </div>
        <div class="clear_both"></div></td>
    </tr>
  </table>
</form>




</div>
			';
/* ----------------------------------------------------------------------------------------------------------------- */




			bx_import('BxDolPageView');
	        $sActions = BxDolPageView::getBlockCaptionMenu(mktime(), array(
	            'add_unit' => array('href' => $sAction, 'title' => _t('_dbcs_SM_SiteStatManagerGoBack'), 'onclick' => '', 'active' => 0),
	        ));
			$_page_cont[$iNameIndex]['page_main_code'] .= DesignBoxContent($sExistedC, $sCss . $sCode, 1, $sActions);
	        PageCodeAdmin();

		}


    }
?>