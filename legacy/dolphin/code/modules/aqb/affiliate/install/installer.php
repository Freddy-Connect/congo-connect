<?php
/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
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

require_once(BX_DIRECTORY_PATH_CLASSES . "BxDolInstaller.php");

class AqbAffInstaller extends BxDolInstaller {
    function AqbAffInstaller($aConfig) {
        parent::BxDolInstaller($aConfig);
    }
	
	function install($aParams){
	    $aResult = parent::install($aParams);
	    if($aResult['result'] && BxDolRequest::serviceExists('aqb_affiliate', 'integrate_with_points')) BxDolService::call('aqb_affiliate', 'integrate_with_points');
	    return $aResult;
	}
	
	function uninstall($aParams) {
		BxDolService::call('aqb_affiliate', 'uninstall_integration');
		return parent::uninstall($aParams);
	}
	
	function actionExecuteSql($bInstall = true) {
        if($bInstall)
            $this->actionExecuteSql(false);
        
    	$sPath = $this->_sHomePath . 'install/sql/' . ($bInstall ? 'install' : 'uninstall') . '.sql';	    
	    if(!file_exists($sPath) || !($rHandler = fopen($sPath, "r")))
            return BX_DOL_INSTALLER_FAILED;	

	    $sQuery = "";
	    $sDelimiter = ';';    	
    	$aResult = array();
    	while(!feof($rHandler)) {
    		$sStr = trim(fgets($rHandler));
    		
    		if(empty($sStr) || $sStr[0] == "" || $sStr[0] == "#" || ($sStr[0] == "-" && $sStr[1] == "-")) 
                continue;

    		//--- Change delimiter ---//
    		if(strpos($sStr, "DELIMITER //") !== false || strpos($sStr, "DELIMITER ;") !== false) {
                $sDelimiter = trim(str_replace('DELIMITER', '', $sStr));
                continue;                
    		}

    		$sQuery .= $sStr;

    		//--- Check for multiline query ---//
    		if(substr($sStr, -strlen($sDelimiter)) != $sDelimiter)
                continue;

    		//--- Execute query ---//
    		$sQuery = str_replace("[db_prefix]", $this->_aConfig['db_prefix'], $sQuery);
			$sQuery = str_replace("[db_index_link]", BX_DOL_URL_ROOT, $sQuery);
    		
			if($sDelimiter != ';')
                $sQuery = str_replace($sDelimiter, "", $sQuery);
    		$rResult = db_res(trim($sQuery), false);
    		if(!$rResult)
    		    $aResult[] = array('query' => $sQuery, 'error' => mysql_error());
    		    
    		$sQuery = "";
    	}
    	fclose($rHandler);
    	
    	return empty($aResult) ? BX_DOL_INSTALLER_SUCCESS : array('code' => BX_DOL_INSTALLER_FAILED, 'content' => $aResult);
    }
}
?>