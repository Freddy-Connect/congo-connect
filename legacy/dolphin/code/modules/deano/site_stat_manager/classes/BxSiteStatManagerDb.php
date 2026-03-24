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

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php' );


/*
* Quotes module Data
*/
class BxSiteStatManagerDb extends BxDolModuleDb {	
	var $_oConfig;
	/*
	* Constructor.
	*/
	function BxSiteStatManagerDb(&$oConfig) {
		parent::BxDolModuleDb();

		$this->_oConfig = $oConfig;
	}

/*	function getDefaultLang() {
		$l = $this->getOne("SELECT `VALUE` FROM `sys_options` where `Name`='lang_default'");
		$id = $this->getOne("SELECT `ID` FROM `sys_localization_languages` where `Name`='$l'");
		return $id;
	}

	function getLangKeys() {
		return $this->getAll("SELECT * FROM `sys_localization_languages`");
	}

	function getPageData($sPage) {
		return $this->getRow("SELECT * FROM `bx_SiteStatManager_data` WHERE `pagename`='$sPage'");
	} */


/* xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx */

	function getStats() {
		return $this->getAll("SELECT * FROM `sys_stat_site` ORDER BY `StatOrder`");
	}

	function getInActive() {
		return $this->getAll("SELECT * FROM `dbcs_sys_stat_site` ORDER BY `StatOrder`");
	}

	function moveActive($iID) {
		// moved stat matching passed active id to the inactive table
		$sQuery = "INSERT INTO `dbcs_sys_stat_site` (`Name`,`Title`,`UserLink`,`UserQuery`,`AdminLink`,`AdminQuery`,`IconName`,`StatOrder`) SELECT `Name`,`Title`,`UserLink`,`UserQuery`,`AdminLink`,`AdminQuery`,`IconName`,`StatOrder` FROM `sys_stat_site` WHERE `ID`='$iID'";
		$this->query($sQuery);
		$sQuery = "DELETE FROM `sys_stat_site` WHERE `ID`='$iID'";
		$this->query($sQuery);
	}
	function moveInActive($iID) {
		// moved stat matching passed inactive id to the active table
		$sQuery = "INSERT INTO `sys_stat_site` (`Name`,`Title`,`UserLink`,`UserQuery`,`AdminLink`,`AdminQuery`,`IconName`,`StatOrder`) SELECT `Name`,`Title`,`UserLink`,`UserQuery`,`AdminLink`,`AdminQuery`,`IconName`,`StatOrder` FROM `dbcs_sys_stat_site` WHERE `ID`='$iID'";
		$this->query($sQuery);
		$sQuery = "DELETE FROM `dbcs_sys_stat_site` WHERE `ID`='$iID'";
		$this->query($sQuery);
	}

	function orderActive($iID,$iOrder) {
		$sQuery = "UPDATE `sys_stat_site` SET `StatOrder`= '$iOrder' WHERE `ID`='$iID'";
		$this->query($sQuery);
	}

	function orderInActive($iID,$iOrder) {
		$sQuery = "UPDATE `dbcs_sys_stat_site` SET `StatOrder`= '$iOrder' WHERE `ID`='$iID'";
		$this->query($sQuery);
	}

	function getStatBlock($iBlockID) {
		return $this->getRow("SELECT * FROM `sys_stat_site` WHERE `ID`='$iBlockID'");
	}

	function deleteStatBlock($iBlockID) {
		$sQuery = "DELETE FROM `sys_stat_site` WHERE `ID`='$iBlockID'";
		$this->query($sQuery);
	}

	function doQuery($sQuery) {
		$this->query($sQuery);
	}

    function escape ($s) {
		if (get_magic_quotes_gpc()) {
		     return $s;
		} else {
	        return mysql_real_escape_string($s);
		}
    }        

	function getLastBlockOrder() {
		return $this->getOne("SELECT MAX(`StatOrder`) FROM `sys_stat_site`");
	}

	function findBlock($iBlockID,$sBlockName) {
		// locates the block by id and name to see if it is in the active or inactive table.
		// return active for active, NULL for inactive.
		$a1 = $this->getOne("SELECT `ID` FROM `sys_stat_site` where `ID`='$iBlockID' AND `Name`='$sBlockName'");
		//$a2 = $this->getOne("SELECT `ID` FROM `dbcs_sys_stat_site` where `ID`='$iBlockID' AND `Name`='$sBlockName'");
		$r = '';
		if ($a1 == $iBlockID) $r = 'active';
		return $r;
	}

/*
	function getPHPBlockData($iBlockID) {
		return $this->getRow("SELECT * FROM `sys_page_compose` WHERE `ID`='$iBlockID'");
	}

	

	function getLastID() {
		return $this->lastId();
	}



	function getNumCols($sPage) {
		//get columns
		$sQuery = "SELECT `Column`,	`ColWidth` FROM `sys_page_compose` WHERE `Page` = '$sPage' AND `Column` > 0 GROUP BY `Column` ORDER BY `Column`";
		return $this->query($sQuery);
	}
	function insertPHPBlock($sPage,$sKey,$sText,$sCode) {
		$query = "INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES ('$sPage', '998px', '$sText', '$sKey', 0, 0, 'PHP', '$sCode', 1, 66, 'non,memb', 0)";
		$this->query($query);
	}

	function updatePHPBlock($iBlockID,$sLkey,$sPHPCode) {
		$query = "UPDATE `sys_page_compose` SET `Caption`='$sLkey', `Content`='$sPHPCode' WHERE `ID`='$iBlockID'";
		$this->query($query);
	}

	function updateHTMLBlock($iBlockID,$sLkey,$sHTMLCode) {
		$query = "UPDATE `sys_page_compose` SET `Caption`='$sLkey', `Content`='$sHTMLCode' WHERE `ID`='$iBlockID'";
		$this->query($query);
	}

	function keyExists($sLKey) {
		return $this->getOne("SELECT `ID` FROM `sys_localization_keys` where `Key`='$sLKey'");
	}
*/


}

?>