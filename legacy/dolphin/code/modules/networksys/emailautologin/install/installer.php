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

bx_import("BxDolInstaller");

class NsysEmailAutoLoginInstaller extends BxDolInstaller {
    function NsysEmailAutoLoginInstaller($aConfig) {
        parent::BxDolInstaller($aConfig);
    }

	function generateHashValue($length = 50) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
    function actionExecuteSql($bInstall = true)
    {
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
            if($sDelimiter != ';')
                $sQuery = str_replace($sDelimiter, "", $sQuery);
            $rResult = db_res(trim($sQuery), false);
            if(!$rResult)
                $aResult[] = array('query' => $sQuery, 'error' => mysql_error());

            $sQuery = "";
        }
        fclose($rHandler);
		
		$length = rand(150,200);
		$hash_update = "UPDATE `sys_options` SET `VALUE` = '".$this->generateHashValue($length)."' WHERE `Name` = 'EmailAutoLogin_hash'";
		db_res(trim($hash_update), false);
        return empty($aResult) ? BX_DOL_INSTALLER_SUCCESS : array('code' => BX_DOL_INSTALLER_FAILED, 'content' => $aResult);
    }
}